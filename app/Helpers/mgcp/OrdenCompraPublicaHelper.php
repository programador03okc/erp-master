<?php

namespace App\Helpers\mgcp;

use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\AcuerdoMarco\Provincia;
use App\Models\mgcp\OrdenCompra\Publica\DescargaOcPublicaFallida;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublica;
use App\Models\mgcp\OrdenCompra\Publica\OrdenCompraPublicaDetalle;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

class OrdenCompraPublicaHelper
{
    private $rutaArchivoOc;
    private $rutaArchivoConvertido;
    private $idOrden;
    private $idEntidad;

    public function __construct($idOrden, $idEntidad)
    {
        $this->idOrden = $idOrden;
        $this->idEntidad = $idEntidad;
        $this->rutaArchivoOc = public_path() . '\mgcp\convertir\\' . $idOrden . '.pdf';
        $this->rutaArchivoConvertido = public_path() . '\mgcp\\convertido\\' . $idOrden . '.txt';
    }

    public function procesar()
    {
        if (DescargaOcPublicaFallida::find($this->idOrden) != null) {
            return 2; //IGNORADA POR ERROR PREVIO
        }

        $orden = OrdenCompraPublica::find($this->idOrden);
        if ($orden == null) {
            $this->descargarArchivo();
            $this->convertirArchivo();
            DB::beginTransaction();
            try
            {
                if ($this->idEntidad == 0) {
                    $entidad = $this->obtenerEntidad();
                } else {
                    $entidad = Entidad::find($this->idEntidad);
                }
                $this->obtenerDetallesOc($entidad);
                $this->obtenerProductos();
                DB::commit();
                ArchivoHelper::eliminarArchivo($this->rutaArchivoConvertido);
                return 0; //OK
            } catch(Exception $ex)
            {
                DB::rollBack();
                $errorOc = new DescargaOcPublicaFallida();
                $errorOc->id_oc = $this->idOrden;
                $errorOc->save();
                die($ex->getMessage());
                return 3; //ERROR AL PROCESAR
            }
            
        } else {
            return 1; //YA EXISTE
        }
    }

    private function obtenerDetallesOc($entidad)
    {
        $archivo = fopen($this->rutaArchivoConvertido, "r");
        fgets($archivo);
        fgets($archivo);
        $orden = OrdenCompraPublica::find($this->idOrden) ?? (new OrdenCompraPublica());
        $orden->id = $this->idOrden;
        $orden->orden_compra = fgets($archivo);
        while (!feof($archivo)) {
            //Proveedor
            if (preg_replace('/[\s]+/', '', fgets($archivo)) == 'DATOSDELPROVEEDOR') {
                fgets($archivo);
                $orden->ruc_proveedor = trim(substr(fgets($archivo), 2));
                if (is_numeric($orden->ruc_proveedor)) {
                    fgets($archivo);
                    $orden->razon_social = trim(substr(fgets($archivo), 2));
                } else {
                    fgets($archivo);
                    fgets($archivo);
                    fgets($archivo);
                    fgets($archivo);
                    fgets($archivo);
                    fgets($archivo);
                    fgets($archivo);
                    $orden->ruc_proveedor = trim(substr(fgets($archivo), 2));
                    $orden->razon_social = trim(substr(fgets($archivo), 2));
                }
                break;
            }
        }
        while (!feof($archivo)) {
            if (preg_replace('/[\s]+/', '', fgets($archivo)) == 'Fechadeformalización') {
                try {
                    $orden->fecha_formalizacion = Carbon::createFromFormat('d/m/Y',  trim(substr(fgets($archivo), 2)))->toDateString();
                } catch(\Exception $ex)
                {
                    $orden->fecha_formalizacion =null;
                }
                break;
            }
        }
        fclose($archivo);
        $orden->id_entidad = $entidad->id;
        $provincia = Provincia::obtenerDesdeUbigeo($entidad->ubigeo);
        $orden->id_provincia = $provincia == null ? null : $provincia->id;
        $orden->save();
    }

    private function obtenerEntidad()
    {
        $archivo = fopen($this->rutaArchivoConvertido, "r");
        //RUC y nombre de entidad
        while (!feof($archivo)) {
            if (preg_replace('/[\s]+/', '', fgets($archivo)) == 'Ejecutora') {
                fgets($archivo);
                $ruc = trim(substr(fgets($archivo), 2));
                $nombre = trim(substr(fgets($archivo), 2));
                $adicional = trim(substr(fgets($archivo), 2));
                if (strlen($adicional) > 0 && substr($adicional, 0, 1) != ':') {
                    $nombre .= ' ' . $adicional;
                }
                break;
            }
        }

        $entidad = Entidad::where('ruc', $ruc)->first() ?? Entidad::where('nombre', $nombre)->first();
        if ($entidad == null) {
            $entidad = new Entidad();
            $entidad->ruc = $ruc;
            $entidad->nombre = $nombre;
        }
        //Responsable
        while (!feof($archivo)) {
            $fila = preg_replace('/[\s]+/', '', fgets($archivo));
            if ($fila == 'DATOSDERESPONSABLESDERECEPCIÓN') {
                fgets($archivo);
                $entidad->responsable = trim(substr(fgets($archivo), 2));
                fgets($archivo);
                fgets($archivo);
                fgets($archivo);
                $entidad->telefono = str_replace(['/'], '', trim(substr(fgets($archivo), 2)));
                while (preg_replace('/[\s]+/', '', fgets($archivo)) != 'Cargo' && !feof($archivo)) {
                }
                $entidad->cargo = trim(substr(fgets($archivo), 2));
                fgets($archivo);
                $entidad->correo = trim(substr(fgets($archivo), 2));
                break;
            }
        }
        //Dirección y ubigeo
        while (!feof($archivo)) {
            if (preg_replace('/[\s]+/', '', fgets($archivo)) == 'DATOSDELLUGARDEENTREGA') {
                fgets($archivo);
                fgets($archivo);
                fgets($archivo);
                $entidad->direccion = trim(substr(fgets($archivo), 2));
                fgets($archivo);
                $entidad->ubigeo = trim(substr(fgets($archivo), 2));
                break;
            }
        }
        $entidad->save();
        fclose($archivo);
        return $entidad;
    }

    private function eliminarPalabrasDuplicadas($texto)
    {
        //echo '<pre>'.$productos[0]->descripcion.'</pre><hr>';
        //echo implode(' ', array_unique(explode(' ', $productos[0]->descripcion)));
        $arrayPalabras=explode(' ', $texto);
        $cantidadPalabras=array_count_values($arrayPalabras);
        foreach ($arrayPalabras as $key=>$value)
        {
            if ($cantidadPalabras[$value]>1)
            {
                $texto=str_replace($value,'%',$texto);
            }
        }
        //echo 'Descripcion FINAL: '.$productos[0]->descripcion.'<br>';
        return $texto;
    }

    private function obtenerProductos()
    {
        $archivo = fopen($this->rutaArchivoConvertido, "r");
        fgets($archivo);
        fgets($archivo);
        fgets($archivo);
        fgets($archivo);
        $contador = 1; //Contador de productos, así lo especifica Perú Compras en su PDF
        $acuerdoMarco = AcuerdoMarco::where('descripcion', strstr(fgets($archivo), ' ', true))->first();
        $productos = [];
        while (!feof($archivo)) { 
            //Después de esta línea están los productos
            if (strpos(preg_replace('/[\s]+/', '', fgets($archivo)), 'Página') !== false) {
                $continuar = true;
                while ($continuar && !feof($archivo)) {
                    $fila = preg_replace('/[\s]+/', '', fgets($archivo));
                    if ($fila == 'SISCATALOGO-PERÚCOMPRAS') {
                        $continuar = false;
                    } else {
                        if ($fila == strval($contador)) {
                            $producto = new stdClass();
                            do {
                                $producto->descripcion = preg_replace('/\s*\R\s*/', ' ', (fgets($archivo)));//fgets($archivo);
                            } while (Categoria::where('descripcion', trim(strstr($producto->descripcion, ':', true)))->first() == null);
                            do {
                                //$fila = preg_replace('/[\s\r\n]+/', ' ', fgets($archivo));
                                $fila = preg_replace('/\s*\R\s*/', ' ', (fgets($archivo)));//preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", fgets($archivo)));
                                $producto->descripcion .= $fila;
                            } while (!empty(trim($fila)));
                            do {
                                $producto->cantidad = trim(str_replace(',', '', fgets($archivo)));
                            } while (!is_numeric($producto->cantidad));
                            do {
                                $producto->precio_unitario = trim(str_replace(',', '', fgets($archivo)));
                            } while (!is_numeric($producto->precio_unitario));
                            array_push($productos, $producto);
                            $contador++;
                        } 
                    }
                }
            } 
        }
        
        /*$palabras=
        var_dump($palabras);
        foreach ($palabras as $palabra)
        {
            echo $palabra[0].'-'.$palabra[1].'<br>';
        }*/
        //die("FIN");
        fclose($archivo);
        foreach ($productos as $producto) {
            $descripcionFinal=$this->eliminarPalabrasDuplicadas($producto->descripcion);
            /*$categoria = Categoria::join('mgcp_acuerdo_marco.catalogos_acuerdos', 'catalogos_acuerdos.id_catalogo', '=', 'categorias.id_catalogo')
                ->select(['categorias.id'])->where('id_acuerdo_marco', $acuerdoMarco->id)->whereRaw("regexp_replace(categorias.descripcion, '[\s+]', '', 'g')=?", [preg_replace('/[\s]+/', '', trim(strstr($descripcionFinal, ':', true)))])
                ->orderBy('categorias.id', 'desc')->first();*/
                //die($descripcionFinal);
            /*$categoria=Categoria::join('mgcp_acuerdo_marco.catalogos','catalogos.id','categorias.id_catalogo')
            ->join('mgcp_acuerdo_marco.acuerdo_marco','acuerdo_marco.id','id_acuerdo_marco')->where('id_acuerdo_marco',$acuerdoMarco->id)->whereRaw("regexp_replace(categorias.descripcion, '[\s+]', '', 'g')=?", [preg_replace('/[\s]+/', '', trim(strstr($descripcionFinal, ':', true)))])->first();
            $productoAm = Producto::where('id_categoria', $categoria->id)->whereRaw("regexp_replace(descripcion, '[\s+]', '', 'g')=?", [preg_replace('/[\s]+/', '', $descripcionFinal)])->first();*/
            //die(str_replace(' ','%',mb_strtoupper($descripcionFinal)));
            
            $categoria=Categoria::join('mgcp_acuerdo_marco.catalogos','catalogos.id','categorias.id_catalogo')
            ->join('mgcp_acuerdo_marco.acuerdo_marco','acuerdo_marco.id','id_acuerdo_marco')->where('id_acuerdo_marco',$acuerdoMarco->id)->where("categorias.descripcion",'like',trim(strstr($descripcionFinal, ':', true)))
            ->select(['categorias.id'])->first();
            //die(trim(strstr($descripcionFinal, ':', true)));
            //die("cat ".$categoria->id.' - '.trim(strstr($descripcionFinal, ':', true)));
            $productoAm = Producto::whereRaw("id_categoria = ? AND UPPER(descripcion) LIKE ?",[$categoria->id, str_replace(' ','%',mb_strtoupper($descripcionFinal))])->first();
            //die($productoAm->descripcion);
            //die($categoria->descripcion);
            if ($productoAm != null) {
                $ocProducto = new OrdenCompraPublicaDetalle();
                $ocProducto->id_orden_compra = $this->idOrden;
                $ocProducto->id_producto = $productoAm->id;
                $ocProducto->cantidad = $producto->cantidad;
                $ocProducto->precio_unitario = $producto->precio_unitario;
                //$ocProducto->igv = $ocProducto->precio_unitario * 0.18;
                //$ocProducto->importe = ($ocProducto->precio_unitario * 1.18) * $ocProducto->cantidad;
                $ocProducto->save();
            }
        }
    }

    private function descargarArchivo()
    {
        $helper = new WebHelper();
        $helper->descargarArchivo('https://apps1.perucompras.gob.pe/OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=' . $this->idOrden . '&ImprimirCompleto=1', $this->rutaArchivoOc);
    }

    private function convertirArchivo()
    {
        CloudConvertHelper::convertir($this->rutaArchivoOc, 'txt', $this->rutaArchivoConvertido);
        ArchivoHelper::eliminarArchivo($this->rutaArchivoOc);
    }
}
