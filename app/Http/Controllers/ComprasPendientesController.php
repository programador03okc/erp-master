<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Almacen\Movimiento\OrdenesPendientesController;
use App\Models\Almacen\Almacen;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\ProductoUbicacion;
use App\Models\Almacen\Requerimiento;
use App\Models\Almacen\Reserva;
use App\Models\Almacen\UnidadMedida;
use App\Models\Configuracion\Moneda;
use App\Models\Logistica\OrdenCompraDetalle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//date_default_timezone_set('America/Lima');

use Debugbar;


class ComprasPendientesController extends Controller
{

    function viewComprasPendientes()
    {
        $condiciones = $this->select_condiciones();
        // $tp_doc = $this->select_tp_doc();
        // $bancos = $this->select_bancos();
        // $cuentas = $this->select_tipos_cuenta();
        // $responsables = $this->select_responsables();
        // $contactos = $this->select_contacto();

        $tp_moneda = $this->select_moneda();
        $tp_documento = $this->select_documento();
        $sis_identidad = $this->select_sis_identidad();
        $sedes = $this->select_sedes();
        $empresas = $this->select_mostrar_empresas();
        $tp_doc = $this->select_tp_doc();
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $unidades = (new AlmacenController)->mostrar_unidades_cbo();

        $unidades_medida = UnidadMedida::mostrar();
        $monedas = Moneda::mostrar();
    // $sedes = Auth::user()->sedesAcceso();

        $tipos = AlmacenController::mostrar_tipos_cbo();
        $almacenes = Almacen::mostrar();

        return view(
            'logistica/gestion_logistica/compras/pendientes/vista_pendientes',
            compact(
                'sedes',
                'empresas',
                'sis_identidad',
                'tp_documento',
                'tp_moneda',
                'tp_doc',
                'condiciones',
                'clasificaciones',
                'subcategorias',
                'categorias',
                'unidades',
                'unidades_medida',
                'monedas',
                'tipos',
                'almacenes'
            )
        );
    }


    public function select_condiciones()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago', 'log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado', 1)
            ->orderBy('log_cdn_pago.descripcion')
            ->get();
        return $data;
    }

    public function select_moneda()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda', 'sis_moneda.descripcion', 'sis_moneda.simbolo')
            ->where([
                ['sis_moneda.estado', '=', 1]
            ])
            ->orderBy('sis_moneda.id_moneda', 'asc')
            ->get();
        return $data;
    }

    public function select_documento()
    {
        $data = DB::table('administracion.adm_tp_docum')
            ->select('adm_tp_docum.id_tp_documento', 'adm_tp_docum.descripcion', 'adm_tp_docum.abreviatura')
            ->where([
                ['adm_tp_docum.estado', '=', 1],
                ['adm_tp_docum.descripcion', 'like', '%Orden%']
            ])
            ->orderBy('adm_tp_docum.id_tp_documento', 'asc')
            ->get();
        return $data;
    }

    public function select_sis_identidad()
    {
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }

    public function select_sedes()
    {
        $data = DB::table('administracion.sis_sede')
            ->select(
                'sis_sede.*'
            )
            ->orderBy('sis_sede.id_empresa', 'asc')
            ->get();
        return $data;
    }

    public function select_mostrar_empresas()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_empresa.logo_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }

    public function select_tp_doc()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.id_tp_doc', 'cont_tp_doc.cod_sunat', 'cont_tp_doc.descripcion')
            ->where([['cont_tp_doc.estado', '=', 1]])
            ->orderBy('cont_tp_doc.id_tp_doc')
            ->get();
        return $data;
    }


    public function listarRequerimientosPendientes($empresa,$sede,$fechaRegistroDesde,$fechaRegistroHasta,$reserva,$orden)
    {
 


        $alm_req = Requerimiento::join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('almacen.tipo_cliente', 'tipo_cliente.id_tipo_cliente', '=', 'alm_req.tipo_cliente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri as contri_cliente', 'com_cliente.id_contribuyente', '=', 'contri_cliente.id_contribuyente')
            ->leftJoin('rrhh.rrhh_perso as perso_natural', 'alm_req.id_persona', '=', 'perso_natural.id_persona')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('rrhh.rrhh_trab as trab_solicitado_por', 'alm_req.trabajador_id', '=', 'trab_solicitado_por.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as postu_solicitado_por', 'postu_solicitado_por.id_postulante', '=', 'trab_solicitado_por.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as perso_solicitado_por', 'perso_solicitado_por.id_persona', '=', 'postu_solicitado_por.id_persona')
            ->leftJoin('mgcp_cuadro_costos.cc_view', 'cc_view.id', '=', 'alm_req.id_cc')


            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'alm_req.fecha_entrega',
                'sis_moneda.simbolo as simbolo_moneda',
                'sis_moneda.descripcion as moneda',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion AS tipo_req_desc',
                'alm_req.tipo_cliente',
                'tipo_cliente.descripcion AS tipo_cliente_desc',
                'sis_usua.usuario',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_usuario"),
                'rrhh_rol.id_area',
                'adm_area.descripcion AS area_desc',
                'rrhh_rol.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_grupo',
                'adm_grupo.descripcion AS adm_grupo_descripcion',
                // 'alm_req.id_op_com',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                // 'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
                'alm_req.id_cliente',
                'contri_cliente.nro_documento as cliente_ruc',
                'contri_cliente.razon_social as cliente_razon_social',
                'alm_req.id_persona',
                'perso_natural.nro_documento as dni_persona',
                DB::raw("CONCAT(perso_natural.nombres,' ', perso_natural.apellido_paterno,' ', perso_natural.apellido_materno)  AS nombre_persona"),
                'alm_req.id_prioridad',
                'alm_req.fecha_registro',
                'alm_req.trabajador_id',
                DB::raw("CONCAT(perso_solicitado_por.nombres,' ', perso_solicitado_por.apellido_paterno,' ', perso_solicitado_por.apellido_materno)  AS solicitado_por"),
                'cc_view.name as cc_solicitado_por',
                'alm_req.estado',
                'alm_req.id_empresa',
                'alm_req.id_sede',
                'alm_req.tiene_transformacion',
                'sis_sede.descripcion as empresa_sede',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc"),
                DB::raw("(SELECT  COUNT(alm_det_req.id_detalle_requerimiento) FROM almacen.alm_det_req
                WHERE alm_det_req.id_requerimiento = alm_req.id_requerimiento and alm_det_req.tiene_transformacion=false)::integer as cantidad_items_base"),
                DB::raw("(SELECT json_agg(DISTINCT nivel.unidad) FROM almacen.alm_det_req dr
                INNER JOIN finanzas.cc_niveles_view nivel ON dr.centro_costo_id = nivel.id_centro_costo
                WHERE dr.id_requerimiento = almacen.alm_req.id_requerimiento and dr.tiene_transformacion=false ) as division"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                WHERE det.id_requerimiento = alm_req.id_requerimiento AND det.id_tipo_item =1
                AND det.id_producto >0 and det.estado != 7 and det.tiene_transformacion =false) AS count_mapeados"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                WHERE det.id_requerimiento = alm_req.id_requerimiento AND det.id_tipo_item =1
                AND det.id_producto is null and det.estado !=7 and  det.tiene_transformacion =false) AS count_pendientes"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
                INNER JOIN almacen.alm_reserva ON det.id_detalle_requerimiento = alm_reserva.id_detalle_requerimiento
                WHERE det.id_requerimiento = alm_req.id_requerimiento AND alm_reserva.estado = 1
                AND det.estado != 7) AS count_stock_comprometido")
            )
            // ->when(($empresa >0), function ($query) use($empresa) {
            //     return $query->where('alm_req.id_empresa','=',$empresa); 
            // })
            // ->when(($sede >0), function ($query) use($sede) {
            //     return $query->where('alm_req.id_sede','=',$sede); 
            // })
            // ->when(($reserva == 'SIN_RESERVA'), function ($query) {
            //     $query->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
            //     return $query->whereRaw('alm_det_req.stock_comprometido isNULL'); 
            // })
            // ->when(($reserva == 'CON_RESERVA'), function ($query) {
            //     $query->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
            //     return $query->whereRaw('alm_det_req.stock_comprometido > 0'); 
            // })
            // ->when(($orden == 'CON_ORDEN'), function ($query) {
            //     $query->Join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
            //     $query->Join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
            //     return $query->whereRaw('log_det_ord_compra.id_detalle_requerimiento > 0'); 
            // })
            // ->when(($orden == 'SIN_ORDEN'), function ($query) {
            //     $query->Join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
            //     return $query->rightJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
            // })
            ->FiltroEmpresa($empresa)
            ->FiltroSede($sede)
            ->FiltroRangoFechas($fechaRegistroDesde, $fechaRegistroHasta)
            ->FiltroReserva($reserva)
            ->FiltroOrden($orden)
            ->where('alm_req.confirmacion_pago', true)
            ->whereIn('alm_req.estado', [2,15,27])
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();
            
            return response()->json(["data" => $alm_req]);
    }


    public function get_lista_items_cuadro_costos_por_id_requerimiento(Request $request)
    {
        $requerimientoList = $request->requerimientoList;
        $temp_data = [];
        $data = [];

        if (count($requerimientoList) > 0) {

            $alm_req = DB::table('almacen.alm_req')
                ->select('alm_req.id_cc')
                ->whereIn('alm_req.id_requerimiento', $requerimientoList)
                ->orderBy('alm_req.id_requerimiento', 'desc')
                ->get();


            foreach ($alm_req as $element) {
                $cuadroCostos = ((new RequerimientoController)->get_detalle_cuadro_costos($element->id_cc));
                $temp_data[] = $cuadroCostos['detalle'];
                $headCuadroCostos = $cuadroCostos['head'];
            }

            if (count($temp_data) > 0) {
                foreach ($temp_data as $arr) {
                    foreach ($arr as $value) {
                        $data[] = $value;
                    }
                }
                $status = 200;
            } else {
                $status = 204;
            }
        }

        $output = ['status' => $status, 'head' => $headCuadroCostos, 'detalle' => $data];

        return response()->json($output);
    }

    function getGrupoSelectItemParaCompra()
    {

        $output = [];
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $monedas = (new LogisticaController)->mostrar_moneda();
        $unidades_medida = (new AlmacenController)->mostrar_unidades_cbo();
        $output[] = [
            'categoria' => $categorias,
            'subcategoria' => $subcategorias,
            'clasificacion' => $clasificaciones,
            'moneda' => $monedas,
            'unidad_medida' => $unidades_medida
        ];
        return response()->json($output);
    }

    function guardarReservaAlmacen(Request $request)
    {

        try {
            DB::beginTransaction();

            $mensaje='';
            $crearNuevaReserva=true;
            $ReservasProductoActivas = Reserva::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],
            ['estado',1]])->get();
            $codigoOIdReservaAnulada= '';

            $hasStock=false;
            $stock=0;
            $saldo=0;
            $productoUbicacion=ProductoUbicacion::where([['id_producto',$request->idProducto],['id_almacen',$request->almacenReserva],['estado',1]])->first();
            if(!empty($productoUbicacion)){
                $stock= $productoUbicacion->stock;
                $hasStock=true;
            }

            foreach ($ReservasProductoActivas as $value) {
                if($value->id_almacen_reserva == $request->almacenReserva && $value->stock_comprometido == $request->cantidadReserva){
                    $crearNuevaReserva=false;
                    $mensaje.='No puede generar una reserva que actualmente existe con mismo almacén y misma cantidad a reservar';
                }
                // if($value->id_almacen_reserva == $request->almacenReserva && $value->stock_comprometido != $request->cantidadReserva){
                if($value->id_almacen_reserva == $request->almacenReserva){
                    $reservaMismoAlmacen = Reserva::where([['id_detalle_requerimiento',$request->idDetalleRequerimiento],['estado',1],
                    ['id_almacen_reserva',$request->almacenReserva]])->first();
                    $reservaMismoAlmacen->estado=7;
                    $reservaMismoAlmacen->save();
                    $codigoOIdReservaAnulada= $reservaMismoAlmacen->codigo?$reservaMismoAlmacen->codigo:$reservaMismoAlmacen->id_reserva;
                    $crearNuevaReserva= true;

                }
            }
            $reserva = new Reserva();
            if($crearNuevaReserva==true){
                $reserva->codigo = Reserva::crearCodigo();
                $reserva->id_detalle_requerimiento = $request->idDetalleRequerimiento;
                $reserva->id_producto = $request->idProducto;
                $reserva->id_almacen_reserva = $request->almacenReserva;
                $reserva->stock_comprometido = $request->cantidadReserva;
                $reserva->usuario_registro =  Auth::user()->id_usuario;
                $reserva->fecha_registro =  new Carbon();
                $reserva->estado = 1;
                $reserva->save();

                if($stock >0 ){
                    $saldo = floatval($stock) - floatval($request->cantidadReserva);
                }
    
            }

            
            if($reserva->id_reserva > 0){
                if($hasStock ==true){
                    $mensaje.=' Se creo nueva reserva '.$reserva->codigo.', con un saldo actual de '.$saldo.' unidades';
                }else{
                    $mensaje.=' Se creo nueva reserva '.$reserva->codigo.', sin saldo (el producto no se encontró en el almacén seleccionado)';
                }
                OrdenesPendientesController::validaProdUbi($request->idProducto, $request->almacenReserva);
                if(strlen($codigoOIdReservaAnulada)>0){
                    $mensaje.=' en remplazo por la reserva '.$codigoOIdReservaAnulada;
                }
            } 

            $ReservasProductoActualizadas = Reserva::with('almacen','usuario.trabajador.postulante.persona','estado')->where([['id_detalle_requerimiento',$request->idDetalleRequerimiento], ['estado',1]])->get();


            DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($request->idDetalleRequerimiento);
            // actualizar estado de requerimiento
            $Requerimiento = DetalleRequerimiento::where('id_detalle_requerimiento',$request->idDetalleRequerimiento)->first();
            $nuevoEstadoRequerimiento=  Requerimiento::actualizarEstadoRequerimientoAtendido([$Requerimiento->id_requerimiento]);
 
            DB::commit();

        return response()->json(['id_reserva'=>$reserva->id_reserva,'codigo'=>$reserva->codigo,'data'=>$ReservasProductoActualizadas,'estado_requerimiento'=>$nuevoEstadoRequerimiento ,'mensaje'=>$mensaje]);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    function anularReservaAlmacen(Request $request)
    {
        
        try {
            DB::beginTransaction();

            $status=0;
        
            $reserva = Reserva::where('id_reserva',$request->idReserva)->first();
            $reserva->estado=7;
            $reserva->save();

            if($reserva){
                $status=200;
            }
            $ReservasProductoActualizadas = Reserva::with('almacen','usuario.trabajador.postulante.persona','estado')->where([['id_detalle_requerimiento',$request->idDetalleRequerimiento], ['estado',1]])->get();
            DetalleRequerimiento::actualizarEstadoDetalleRequerimientoAtendido($request->idDetalleRequerimiento);
            // actualizar estado de requerimiento
            $Requerimiento = DetalleRequerimiento::where('id_detalle_requerimiento',$request->idDetalleRequerimiento)->first();
            $nuevoEstadoRequerimiento= Requerimiento::actualizarEstadoRequerimientoAtendido([$Requerimiento->id_requerimiento]);

        //     (new LogisticaController)->generarTransferenciaRequerimiento($id_requerimiento, $id_sede, $data);
            DB::commit();

        return response()->json(['id_reserva'=>$reserva->id_reserva,'data'=>$ReservasProductoActualizadas, 'status'=>$status, 'estado_requerimiento'=>$nuevoEstadoRequerimiento]);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    function buscarItemCatalogo(Request $request)
    {
        $part_number = $request->part_number;
        $descripcion = $request->descripcion;
        $where = [];
        if ($part_number !== null && $part_number !== '') {
            $where = [['alm_prod.part_number', '=', strtoupper($part_number)], ['alm_prod.estado', '=', 1]];
        } else if ($descripcion !== null && $descripcion !== '') {
            // $where=[['alm_prod.descripcion','like','%'.$descripcion.'%'],['alm_prod.estado','=',1]];
            $where = [['alm_prod.descripcion', '=', strtoupper($descripcion)], ['alm_prod.estado', '=', 1]];
        }

        $alm_prod = DB::table('almacen.alm_prod')
            ->select(
                'alm_item.id_item',
                'alm_item.codigo AS codigo_item',
                'alm_prod.id_producto',
                'alm_prod.codigo AS alm_prod_codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_prod.id_unidad_medida',
                'alm_prod.id_moneda',
                'sis_moneda.descripcion as moneda',
                'alm_prod.id_categoria',
                'alm_prod.id_subcategoria',
                'alm_prod.id_clasif',
                'alm_und_medida.descripcion as unidad_medida',
                'alm_cat_prod.descripcion as categoria',
                'alm_subcat.descripcion as subcategoria',
                'alm_clasif.descripcion as clasificacion'
            )
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_prod.id_moneda')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_item', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftJoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftJoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_prod.id_clasif')
            ->where($where)
            ->get();

        return response()->json($alm_prod);
    }

    function guardarItemsEnDetalleRequerimiento(Request $request)
    {
        $id_requerimiento_list = $request->id_requerimiento_list;
        $id_requerimiento = $id_requerimiento_list[0]; // solo toma el primero id_requerimieno
        $items = $request->items;
        $status = '';
        $newIdDetalleRequerimientoList = [];
        $cantidadItemSinCodigoProducto = 0;
        $idProductosGuardadosDetalleReq = [];
        $msj = [];
        $count_items = count($items);
        if ($count_items > 0) {


            $alm_det_req = DB::table('almacen.alm_det_req')
                ->select(
                    'alm_det_req.*'
                )
                ->where([['alm_det_req.id_requerimiento', '=', $id_requerimiento], ['alm_det_req.estado', '!=', 7]])
                ->get();

            foreach ($alm_det_req as $value) {
                $dProductosGuardadosDetalleReq[] = $value->id_producto;
            }


            for ($i = 0; $i < $count_items; $i++) {
                if ($items[$i]['id_producto'] > 0 && in_array(!$items[$i]['id_producto'], $idProductosGuardadosDetalleReq)) {

                    $alm_det_req = DB::table('almacen.alm_det_req')->insertGetId(

                        [
                            'id_requerimiento'      => $id_requerimiento,
                            'id_item'               => is_numeric($items[$i]['id_item']) == 1 && $items[$i]['id_item'] > 0 ? $items[$i]['id_item'] : null,
                            'id_cc_am_filas'        => is_numeric($items[$i]['id_cc_am_filas']) == 1 && $items[$i]['id_cc_am_filas'] > 0 ? $items[$i]['id_cc_am_filas'] : null,
                            'id_cc_venta_filas'     => is_numeric($items[$i]['id_cc_venta_filas']) == 1 && $items[$i]['id_cc_venta_filas'] > 0 ? $items[$i]['id_cc_venta_filas'] : null,
                            'id_producto'           => is_numeric($items[$i]['id_producto']) == 1 && $items[$i]['id_producto'] > 0 ? $items[$i]['id_producto'] : null,
                            'precio_unitario'       => is_numeric($items[$i]['precio_unitario']) == 1 ? $items[$i]['precio_unitario'] : null,
                            'cantidad'              => $items[$i]['cantidad'] ? $items[$i]['cantidad'] : null,
                            'id_moneda'             => $items[$i]['id_moneda'] ? $items[$i]['id_moneda'] : null,
                            'descripcion_adicional' => isset($items[$i]['descripcion']) ? $items[$i]['descripcion'] : null,
                            'id_unidad_medida'      => is_numeric($items[$i]['id_unidad_medida']) == 1 ? $items[$i]['id_unidad_medida'] : null,
                            'id_tipo_item'          => 1,
                            'fecha_registro'        => date('Y-m-d H:i:s'),
                            'estado'                => 1,
                            'tiene_transformacion'  => isset($items[$i]['tiene_transformacion']) ? $items[$i]['tiene_transformacion'] : false


                        ],
                        'id_detalle_requerimiento'
                    );

                    $newIdDetalleRequerimientoList[] = $alm_det_req;
                } else {
                    $cantidadItemSinCodigoProducto++;
                }
            }
        }

        if ($cantidadItemSinCodigoProducto > 0) {
            $msj[] = 'No se pudo agregar un item que no este guardado primero en el catálogo';
        }

        if (count($newIdDetalleRequerimientoList) > 0) {
            $status = 200;
            $msj[] = 'Items guardados!';
        } else {
            $status = 204;
            $msj[] = 'No se guardaron items';
        }

        $output = [
            'id_detalle_requerimiento_list' => $newIdDetalleRequerimientoList,
            'status' => $status,
            'mensaje' => $msj
        ];

        return response()->json($output);
    }
}
