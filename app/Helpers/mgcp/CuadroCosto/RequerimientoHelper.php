<?php


namespace App\Helpers\mgcp\CuadroCosto;

use App\Mail\mgcp\CuadroCosto\AprobacionCuadro;
use App\Mail\mgcp\CuadroCosto\ErrorReplicarRequerimiento;
use App\Mail\mgcp\CuadroCosto\RespuestaSolicitud;
use App\Mail\mgcp\CuadroCosto\SolicitudAprobacion;
use App\Models\Administracion\Periodo;
use App\Models\Almacen\DetalleOrdenCompra;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\OrdenCompra;
use App\Models\Almacen\Producto;
use App\Models\Almacen\Requerimiento;
use App\Models\Almacen\RequerimientoObservacion;
use App\Models\Almacen\Subcategoria;
use App\Models\Comercial\Cliente;
use App\Models\Configuracion\Usuario;
use App\Models\Configuracion\UsuarioSede;
use App\Models\Contabilidad\Contribuyente;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\CuadroCosto\AprobadorUno;
use App\Models\mgcp\CuadroCosto\AprobadorDos;
use App\Models\mgcp\CuadroCosto\AprobadorTres;
use App\Models\mgcp\CuadroCosto\CcAmFila;
use App\Models\mgcp\CuadroCosto\CcFilaMovimientoTransformacion;
use App\Models\mgcp\CuadroCosto\CcSolicitud;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\Proveedor;
use App\Models\mgcp\Usuario\Notificacion;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\RRHH\Persona;
use App\Models\RRHH\Postulante;
use App\Models\RRHH\Trabajador;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

class RequerimientoHelper
{
    const ID_USUARIO_MGCP = 79;
    const ID_ANULADO_AGIL = 7;
    const ID_DIVISION_UCORP = 1;

    /**
     * Devuelve el requerimiento creado, el reemplazado y el estado (sin_cambios, nuevo, reemplazo)
     */
    public function replicarPorCuadroCosto($idOportunidad)
    {
        try {
            $respuesta = new stdClass();

            $cuadro = CuadroCosto::where('id_oportunidad', $idOportunidad)->first();
            $oportunidad = $cuadro->oportunidad;
            $ordenCompra = OrdenCompraPropiaView::where('id_oportunidad', $idOportunidad)->first();
            $respuesta->requerimiento = Requerimiento::where('id_cc', $cuadro->id)->orderBy('id_requerimiento', 'DESC')->first();
            $crearRequerimiento = false;
            if ($respuesta->requerimiento != null) { //Si requerimiento existe
                if ($respuesta->requerimiento->estado == self::ID_ANULADO_AGIL) { //Y está anulado
                    $crearRequerimiento = true; //Crear nuevo requerimiento
                    $respuesta->reemplazado = $respuesta->requerimiento;
                } else //Caso contrario, que devuelva el requerimiento
                {
                    $respuesta->reemplazado = null;
                    $respuesta->estado = 'sin_cambios';
                    return $respuesta;
                }
            } else {
                $crearRequerimiento = true; //Requerimiento no existe y se debe crear uno
                $respuesta->reemplazado = null;
            }
            if ($crearRequerimiento) {
                DB::beginTransaction();
                $idUsuario = $this->obtenerIdUsuario($oportunidad->id_responsable);
                $respuesta->requerimiento = new Requerimiento();
                $respuesta->requerimiento->id_tipo_requerimiento = 1;
                $respuesta->requerimiento->id_usuario = $idUsuario;
                $respuesta->requerimiento->trabajador_id = $idUsuario;
                $respuesta->requerimiento->fecha_requerimiento = new Carbon();
                $concepto = ($ordenCompra == null ? '' : 'O/C: ' . $ordenCompra->nro_orden . ' / ');
                $respuesta->requerimiento->concepto = trim($concepto . ' CDP: ' . $oportunidad->codigo_oportunidad . ' / CLIENTE: ' . $oportunidad->entidad->nombre);
                if ($respuesta->reemplazado == null) {
                    $respuesta->estado = 'nuevo';
                } else {
                    $respuesta->requerimiento->concepto .= ' (REEMPLAZA A ' . $respuesta->reemplazado->codigo . ')';
                    $respuesta->estado = 'reemplazado';
                }
                $respuesta->requerimiento->id_grupo = 2; //2 es Comercial
                $respuesta->requerimiento->estado = 2;
                $respuesta->requerimiento->occ_softlink = ($ordenCompra == null ? null : $ordenCompra->occ);
                $respuesta->requerimiento->fecha_registro = new Carbon();
                $respuesta->requerimiento->id_prioridad = 1;
                $respuesta->requerimiento->observacion = "CREADO DE FORMA AUTOMÁTICA DESDE EL MGC";
                $respuesta->requerimiento->id_moneda = 2; //2 es dólares

                $respuesta->requerimiento->id_empresa = $ordenCompra == null ? 1 : $ordenCompra->id_empresa;
                $respuesta->requerimiento->id_periodo = $this->obtenerPeriodo($respuesta->requerimiento->fecha_requerimiento->year)->id_periodo;
                $respuesta->requerimiento->id_sede = $ordenCompra == null ? 4 : $this->obtenerIdSede($ordenCompra->id_empresa); //sede de la empresa de donde viene el requerimiento
                $respuesta->requerimiento->id_cliente = $this->obtenerCliente($oportunidad->id_entidad)->id_cliente;
                $respuesta->requerimiento->tipo_cliente = 2; //Cliente persona jurídica
                $respuesta->requerimiento->direccion_entrega = $ordenCompra == null ? 'CONSULTAR CON EL CORPORATIVO' : $ordenCompra->lugar_entrega;

                $respuesta->requerimiento->id_almacen = $ordenCompra == null ? 2 : $this->obtenerIdAlmacen($ordenCompra->id_empresa); //id del almacen que va a atender
                $respuesta->requerimiento->confirmacion_pago = true;
                $respuesta->requerimiento->fecha_entrega = ($ordenCompra == null ? (new Carbon()) : $ordenCompra->fecha_entrega);
                $respuesta->requerimiento->id_cc = $cuadro->id;
                $respuesta->requerimiento->tiene_transformacion = $cuadro->tiene_transformacion; //Rocío lo usa por conveniencia, para no revisar las filas
                $respuesta->requerimiento->division_id = self::ID_DIVISION_UCORP;
                //$respuesta->requerimiento->save();
                $respuesta->requerimiento->codigo = Requerimiento::crearCodigo(1, 0);
                $respuesta->requerimiento->save();
                $this->crearHistorialAprobacion($respuesta->requerimiento,$cuadro);
                $this->crearDetalles($respuesta->requerimiento, $cuadro);
                DB::commit();
            }
            return $respuesta;
        } catch (Exception $ex) {
            DB::rollBack();
            $cuadro = CuadroCosto::where('id_oportunidad', $idOportunidad)->first();
            //Envía por correo el error generado para poder corregirlo
            Mail::to(config('global.adminEmail'))->send(new ErrorReplicarRequerimiento($cuadro, $ex->getMessage()));
            return null;
        }
    }

    private function crearHistorialAprobacion($requerimiento,$cuadro)
    {
        $ultimaSolicitudCc=CcSolicitud::where('id_cc',$cuadro->id)->orderBy('id','DESC')->first();
        $observacion = new RequerimientoObservacion();
        $observacion->id_requerimiento=$requerimiento->id_requerimiento;
        $observacion->accion='APROBADO';
        $observacion->descripcion=$ultimaSolicitudCc->comentario_aprobador;
        $observacion->id_usuario=$this->obtenerIdUsuario($ultimaSolicitudCc->enviada_a);
        $observacion->fecha_registro=new Carbon();
        $observacion->save();
    }

    private function crearHistorialAnulacion(Requerimiento $requerimiento)
    {
        $observacion = new RequerimientoObservacion();
        $observacion->id_requerimiento=$requerimiento->id_requerimiento;
        $observacion->accion='ANULADO';
        $observacion->descripcion='Anulado de forma automática por reaprobación de cuadro de presupuesto (MGC)';
        $observacion->id_usuario=$requerimiento->id_usuario;
        $observacion->fecha_registro=new Carbon();
        $observacion->save();
    }

    /**
     * Devuelve el ID del usuario en Agile usando el ID de usuario del MGC. En caso el usuario del MGC no esté registrado en Agile, se utiliza el ID del usuario de Agile asignado para el MGC
     * @param int $idUsuario ID de usuario en MGC
     */
    private function obtenerIdUsuario($idUsuario) : int
    { //Usuario por defecto en el sistema Agile en caso que no exista el usuario buscado
        $usuarioMgcp = User::find($idUsuario);
        $persona = Persona::where('email', $usuarioMgcp->email)->first();
        if ($persona == null) {
            return self::ID_USUARIO_MGCP; //Usuario MGCP
        } else {
            $postulante = Postulante::where('id_persona', $persona->id_persona)->first();
            if ($postulante == null) {
                return self::ID_USUARIO_MGCP;
            } else {
                $trabajador = Trabajador::where('id_postulante', $postulante->id_postulante)->first();
                if ($trabajador == null) {
                    return self::ID_USUARIO_MGCP;
                } else {
                    $usuario = Usuario::where('id_trabajador', $trabajador->id_trabajador)->first();
                    if ($usuario == null) {
                        return self::ID_USUARIO_MGCP;
                    } else {
                        return $usuario->id_usuario;
                    }
                }
            }
        }
    }

    /**
     * Obtiene la ID de la sede en Lima de la empresa especificada
     */
    private function obtenerIdSede($idEmpresa)
    {
        $id = null;
        switch ($idEmpresa) {
            case 1:
                $id = 4;
                break;
            case 2:
                $id = 10;
                break;
            case 3:
                $id = 11;
                break;
            case 4:
                $id = 12;
                break;
            case 5:
                $id = 13;
                break;
            case 6:
                $id = 14;
                break;
        }
        return $id;
    }

    /**
     * Obtiene el ID del almacén en Lima de la empresa especificada
     */
    private function obtenerIdAlmacen($idEmpresa)
    {
        $id = null;
        switch ($idEmpresa) {
            case 1:
                $id = 2;
                break;
            case 2:
                $id = 8;
                break;
            case 3:
                $id = 9;
                break;
            case 4:
                $id = 10;
                break;
            case 5:
                $id = 11;
                break;
            case 6:
                $id = 12;
                break;
        }
        return $id;
    }

    private function crearDetalles($cabecera, $cuadro)
    {
        $filasCuadro = CcAmFila::where('id_cc_am', $cabecera->id_cc)->orderBy('id', 'asc')->get();
        $tipoCambio = (CuadroCosto::find($cabecera->id_cc))->tipo_cambio;
        foreach ($filasCuadro as $fila) {
            $proveedorFila = $fila->amProveedor;
            //$fondoProveedor = ($proveedorFila == null ? null : $proveedorFila->fondoProveedor);
            $detalle = new DetalleRequerimiento();
            $detalle->id_requerimiento = $cabecera->id_requerimiento;
            $detalle->cantidad = $fila->cantidad ?? 0;
            $detalle->estado = 1; //1 es elaborado
            $detalle->fecha_registro = new Carbon();
            $detalle->id_tipo_item = 1; //1 es producto
            $detalle->id_unidad_medida = 1; //Unidad (UND)
            $detalle->id_cc_am_filas = $fila->id;
            $detalle->id_moneda = 2; //siempre en dólares 
            $detalle->tiene_transformacion = false; //False son los productos base
            $detalle->centro_costo_id = $cuadro->id_centro_costo;
            $objProducto = $this->obtenerProducto($fila->marca, $fila->part_no);
            $detalle->id_producto = $objProducto == null ? null : $objProducto->id_producto; //Mapeo de producto si es que existe en el catálogo de Agile. El MGC no lo crea de forma automática
            $detalle->proveedor_id = $proveedorFila == null ? null : $this->obtenerProveedor($proveedorFila->id_proveedor)->id_proveedor;
            $detalle->precio_unitario = $proveedorFila == null ? 0 : ($proveedorFila->precio / ($proveedorFila->moneda == 'd' ? 1 : $tipoCambio));
            $detalle->part_number = $fila->part_no;
            $detalle->descripcion = $fila->descripcion;
           
            $detalle->entrega_cliente = ($fila->tieneTransformacion() == false && (CcFilaMovimientoTransformacion::where('id_fila_ingresa', $fila->id)->first() == null));
            /*if ($fila->id==5374) {
                die("Capturado, entrega es ".($detalle->entrega_cliente ? 'TRUE' : 'FALSE'));
            }*/
            /*if ($fondoProveedor != null) {
                $detalle->descripcion_adicional = "Fondo de proveedor: $fondoProveedor->descripcion (" . ($fondoProveedor->moneda == 's' ? 'S/' : '$') . number_format($fondoProveedor->valor_unitario, 2) . ")";
            }*/
            $detalle->save();
            $this->crearDetallePorTransformacion($fila, $cabecera); //Creará una fila del producto transformado sólo si el producto tiene transformación
        }
    }

    private function crearDetallePorTransformacion($fila, $cabecera)
    {
        if ($fila->tieneTransformacion()) {
            $detalle = new DetalleRequerimiento();
            $detalle->id_requerimiento = $cabecera->id_requerimiento;
            $detalle->cantidad = $fila->cantidad ?? 0;
            $detalle->estado = 1; //1 es elaborado
            $detalle->fecha_registro = new Carbon();
            $detalle->id_tipo_item = 1; //1 es producto
            $detalle->id_unidad_medida = 1; //Unidad (UND)
            $detalle->id_cc_am_filas = $fila->id;
            $detalle->id_moneda = 2; //siempre en dólares 
            $detalle->tiene_transformacion = true; //$fila->tieneTransformacion();
            $detalle->part_number = $fila->part_no_producto_transformado;
            $detalle->descripcion = $fila->descripcion_producto_transformado;
            //$detalle->id_producto = $this->obtenerProducto($fila->marca_producto_transformado, $fila->descripcion_producto_transformado, $fila->part_no_producto_transformado)->id_producto;
            $detalle->proveedor_id = null;
            $detalle->precio_unitario = 0;
            $detalle->entrega_cliente = true;
            //$detalle->descripcion_adicional="PRODUCTO TRANSFORMADO";
            $detalle->save();
        }
    }

    private function obtenerProveedor($idProveedor)
    {
        $proveedorMgcp = \App\Models\mgcp\CuadroCosto\Proveedor::find($idProveedor);
        $contribuyente = Contribuyente::where('razon_social', $proveedorMgcp->razon_social)->first();
        if ($contribuyente == null) {
            $contribuyente = new Contribuyente();
            $contribuyente->nro_documento = $proveedorMgcp->ruc;
            $contribuyente->razon_social = $proveedorMgcp->razon_social;
            $contribuyente->fecha_registro = new Carbon();
            $contribuyente->transportista = false;
            $contribuyente->save();
        }
        $proveedorAgile = \App\Models\Logistica\Proveedor::where('id_contribuyente', $contribuyente->id_contribuyente)->first();
        if ($proveedorAgile == null) {
            $proveedorAgile = new \App\Models\Logistica\Proveedor();
            $proveedorAgile->id_contribuyente = $contribuyente->id_contribuyente;
            $proveedorAgile->estado = 1;
            $proveedorAgile->fecha_registro = new Carbon();
            $proveedorAgile->save();
        }
        return $proveedorAgile;
    }

    /**
     * Devuelve un objeto del tipo Producto si se encuentra o NULL si no se encuentra en la BD
     * @param string $marca Marca (subcategoría) del producto
     * @param string $nroParte Número de parte del producto
     */
    private function obtenerProducto($marca, $nroParte)
    {
        //No busca productos si no tienen Nro. de parte
        if (empty($nroParte)) {
            return null;
        }
        $objMarca = $this->obtenerMarca(mb_strtoupper($marca));
        //Si la marca no está registrada en la BD, se asume que el producto no está registrado
        if ($objMarca == null) {
            return null;
        } else {
            return \App\Models\Almacen\Producto::where('estado', 1)->where('part_number', mb_strtoupper($nroParte))->where('id_subcategoria', $objMarca->id_subcategoria)->first();;
        }
    }

    /**
     * Devuelve un objeto del tipo Subcategoría si se encuentra la marca o NULL si no se encuentra en la BD
     * @param string $nombre Nombre de la marca a buscar
     */
    private function obtenerMarca($nombre)
    {
        return Subcategoria::where('descripcion', (empty($nombre) ? 'SIN MARCA' : $nombre))->first();
    }

    /**
     * Devuelve TRUE si el requerimiento fue anulado, FALSE si no se pudo anular
     * @param Requerimiento $requerimiento Requerimiento a anular
     */
    public function anular(Requerimiento $requerimiento): bool
    {
        $orden = OrdenCompra::whereRaw('id_orden_compra IN (
            SELECT log_det_ord_compra.id_orden_compra FROM  logistica.log_det_ord_compra WHERE id_detalle_requerimiento IN 
            (SELECT alm_det_req.id_detalle_requerimiento FROM almacen.alm_det_req WHERE id_requerimiento=?)
            )', [$requerimiento->id_requerimiento])->first();
        if ($orden != null) {
            if ($orden->en_almacen) {
                return false;
            } else {
                $orden->estado = self::ID_ANULADO_AGIL;
                $orden->save();
                DetalleOrdenCompra::where('id_orden_compra', $orden->id_orden_compra)->update(['estado' => self::ID_ANULADO_AGIL]);
            }
        }
        DetalleRequerimiento::where('id_requerimiento', $requerimiento->id_requerimiento)->update(['estado' => self::ID_ANULADO_AGIL]);
        $requerimiento->estado = self::ID_ANULADO_AGIL;
        $requerimiento->concepto = $requerimiento->concepto . ' (ANULADO POR REAPROBACIÓN DE CUADRO)';
        $requerimiento->save();
        $this->crearHistorialAnulacion($requerimiento);
        return true;
    }

    private function obtenerCliente($idEntidad)
    {
        $entidad = Entidad::find($idEntidad);
        $contribuyente = Contribuyente::where('razon_social', $entidad->nombre)->first();
        if ($contribuyente == null) {
            $contribuyente = new Contribuyente();
            $contribuyente->nro_documento = $entidad->ruc;
            $contribuyente->razon_social = $entidad->nombre;
            $contribuyente->telefono = $entidad->telefono;
            $contribuyente->direccion_fiscal = $entidad->direccion;
            $contribuyente->ubigeo = null; //Ubigeo es string en MGCP, id (int) en Agile
            $contribuyente->fecha_registro = new Carbon();
            $contribuyente->email = $entidad->correo;
            $contribuyente->transportista = false;
            $contribuyente->save();
        }
        $cliente = Cliente::where('id_contribuyente', $contribuyente->id_contribuyente)->first();
        if ($cliente == null) {
            $cliente = new Cliente();
            $cliente->id_contribuyente = $contribuyente->id_contribuyente;
            $cliente->save();
        }
        return $cliente;
    }

    private function obtenerPeriodo($anio)
    {
        $periodo = Periodo::where('descripcion', $anio)->first();
        if ($periodo == null) {
            $periodo->descripcion = $anio;
            $periodo->estado = 1;
            $periodo->save();
        }
        return $periodo;
    }
}
