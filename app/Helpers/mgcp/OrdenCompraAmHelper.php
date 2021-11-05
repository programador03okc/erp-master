<?php

namespace App\Helpers\mgcp;

use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Estado;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use Carbon\Carbon;

class OrdenCompraAmHelper
{
    //Si llega a conectar, devuelve false para que la funcion que invoca ya no insista
    public static function obtenerDetallesPortal(&$portal, &$orden)
    {
        $reintentar = false;
        try {
            $dataEnviar['N_OrdenCompra'] = $orden->id;
            $data = json_decode($portal->enviarData($dataEnviar, "https://www.catalogos.perucompras.gob.pe/OrdenCompra/consultaEntregas"));
            $orden->id_alternativo = $data->pLista[0]->N_OrdenCompra;
            $orden->lugar_entrega = substr($data->pLista[0]->C_LugarEntrega, 0, 200);
            $orden->fecha_entrega = $data->pLista[0]->C_FinEntrega;
            $orden->estado_entrega = $data->pLista[0]->C_EstadoOrden;
            $orden->inicio_entrega = $data->pLista[0]->C_InicioEntrega;
        } catch (\Exception $e) {
            $reintentar = true;
        }
        return $reintentar;
    }

    public static function registrarEstadosPortal(&$portal, &$orden)
    {
        $reintentar = false;
        try {
            $pagina = $portal->parseHtml($portal->visitarUrl('https://www.catalogos.perucompras.gob.pe/ConsultaOrdenesPub/_detalleEstadoOrden?ID_OrdenCompra=' . $orden->id_alternativo));
            foreach ($pagina->find('tbody tr') as $tr) {
                $estado = $tr->find('td', 0)->innertext;
                $estado = trim(substr($estado, strpos($estado, '/>') + 3));
                $fecha = Carbon::createFromFormat('d/m/Y H:i', trim($tr->find('td', 7)->innertext));
                $existe = Estado::where('id_oc', $orden->id)->where('estado', $estado)->first() != null;
                if (!$existe) {
                    $nuevo = new Estado();
                    $nuevo->id_oc = $orden->id;
                    $nuevo->estado = $estado;
                    $nuevo->fecha = $fecha->toDateTimeString();
                    $nuevo->save();
                }
                if ($estado == 'PUBLICADA' && $orden->fecha_publicacion == null) {
                    $orden->fecha_publicacion = $fecha->toDateString();
                }
            }
        } catch (\Exception $e) {
            //fwrite($myfile, "\nError ID $orden->id, estado: $estado".$e->getMessage());
            $reintentar = true;
        }
        //fwrite($myfile, "\nFin escritura");
        return $reintentar;
    }

    public static function obtenerFechaPublicacion(&$portal, &$orden)
    {
        $reintentar = false;
        try {
            $pagina = $portal->parseHtml($portal->visitarUrl('https://www.catalogos.perucompras.gob.pe/OrdenCompra/_detalleEstadoOrden?ID_OrdenCompra=' . $orden->id_alternativo));
            foreach ($pagina->find('tbody tr') as $tr) {
                foreach ($tr->find('td') as $td) {
                    if (strpos($td->innertext, 'PUBLICADA') !== false) {
                        //echo 'Convertir: '.$tr->find('td', 7)->innertext.'<br>';
                        $orden->fecha_publicacion = Carbon::createFromFormat('d/m/Y H:i', trim($tr->find('td', 7)->innertext))->toDateString();
                        break;
                        //return $reintentar;
                    }
                }
            }
        } catch (\Exception $e) {
            //echo 'Error: '.$e->getMessage().'<br>';
            $reintentar = true;
        }
        return $reintentar;
    }

    /**
     * Devuelve un array con las rutas de los archivos descargados
     */
    public static function descargarArchivos($idOrden)
    {
        $orden=OrdenCompraAm::find($idOrden);
        $carpeta=storage_path('app/mgcp/ordenes-compra/temporal/');
        $archivos=array($carpeta.$orden->orden_am . '-digital.pdf',$carpeta . $orden->orden_am . '-fisica.pdf');
        $helper = new WebHelper();
        //Descargar O/C digital
        $helper->descargarArchivo('https://apps1.perucompras.gob.pe/OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=' . $orden->id . '&ImprimirCompleto=1', $archivos[0]);
        //Descargar O/C fisica
        $helper->descargarArchivo($orden->url_oc_fisica, $archivos[1]);
        return $archivos;
    }
}
