<?php

namespace App\Http\Controllers;

use App\Models\Almacen\UnidadMedida;
use App\Models\Configuracion\Moneda;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set('America/Lima');

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
                'tipos'
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


    public function listarRequerimientosPendientes($idEmpresa = null, $idSede = null)
    {

        $requerimiento = array();
        $detalleRequerimiento = array();

        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('almacen.tipo_cliente', 'tipo_cliente.id_tipo_cliente', '=', 'alm_req.tipo_cliente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            // ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri as contri_cliente', 'com_cliente.id_contribuyente', '=', 'contri_cliente.id_contribuyente')
            ->leftJoin('rrhh.rrhh_perso as perso_natural', 'alm_req.id_persona', '=', 'perso_natural.id_persona')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')

            // ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
            // ->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            // ->leftJoin('almacen.guia_com_oc', 'guia_com_oc.id_oc', '=', 'log_ord_compra.id_orden_compra')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
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
                DB::raw("(perso_natural.nombres) || ' ' || (perso_natural.apellido_paterno) || ' ' || (perso_natural.apellido_materno)  AS nombre_persona"),
                'alm_req.id_prioridad',
                'alm_req.fecha_registro',
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
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req AS det
            WHERE det.id_requerimiento = alm_req.id_requerimiento AND det.id_tipo_item =1
              AND det.id_producto is null) AS count_pendientes")

                //         DB::raw("(SELECT  COUNT(log_ord_compra.id_orden_compra) FROM logistica.log_ord_compra
                // WHERE log_ord_compra.id_grupo_cotizacion = log_detalle_grupo_cotizacion.id_grupo_cotizacion)::integer as cantidad_orden"),
                //         DB::raw("(SELECT  COUNT(mov_alm.id_mov_alm) FROM almacen.mov_alm
                // WHERE mov_alm.id_guia_com = guia_com_oc.id_guia_com and 
                // guia_com_oc.id_oc = log_ord_compra.id_orden_compra)::integer as cantidad_entrada_almacen")

            )
            ->where([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 1], ['alm_req.estado', 2], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orWhere([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 4], ['alm_req.estado', 2], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orWhere([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 5], ['alm_req.estado', 2], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orWhere([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 6], ['alm_req.estado', 2], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orWhere([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 7], ['alm_req.estado', 2], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orWhere([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 3], ['alm_req.estado', 2], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orWhere([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 1], ['alm_req.estado', 15], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orWhere([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 3], ['alm_req.estado', 15], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orWhere([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 1], ['alm_req.estado', 27], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orWhere([['alm_req.confirmacion_pago', true], ['alm_req.id_tipo_requerimiento', 3], ['alm_req.estado', 27], $idEmpresa > 0 ? ['alm_req.id_empresa', $idEmpresa] : [null], $idSede > 0 ? ['alm_req.id_sede', $idSede] : [null]])
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();

        foreach ($alm_req as $data) {
            $requerimiento[] = [
                'id_requerimiento' => $data->id_requerimiento,
                'codigo' => $data->codigo,
                'concepto' => $data->concepto,
                'id_moneda' => $data->id_moneda,
                'moneda' => $data->moneda,
                'simbolo_moneda' => $data->simbolo_moneda,
                'fecha_requerimiento' => $data->fecha_requerimiento,
                'id_tipo_requerimiento' => $data->id_tipo_requerimiento,
                'tipo_req_desc' => $data->tipo_req_desc,
                'tipo_cliente' => $data->tipo_cliente,
                'tipo_cliente_desc' => $data->tipo_cliente_desc,
                'usuario' => $data->usuario,
                'nombre_usuario' => $data->nombre_usuario,
                'id_area' => $data->id_area,
                'area_desc' => $data->area_desc,
                'id_rol' => $data->id_rol,
                'id_rol_concepto' => $data->id_rol_concepto,
                'rrhh_rol_concepto' => $data->rrhh_rol_concepto,
                'id_grupo' => $data->id_grupo,
                'adm_grupo_descripcion' => $data->adm_grupo_descripcion,
                'alm_req_concepto' => $data->alm_req_concepto,
                'id_cliente' => $data->id_cliente,
                'cliente_ruc' => $data->cliente_ruc,
                'cliente_razon_social' => $data->cliente_razon_social,
                'id_persona' => $data->id_persona,
                'dni_persona' => $data->dni_persona,
                'nombre_persona' => $data->nombre_persona,
                'id_prioridad' => $data->id_prioridad,
                'fecha_registro' => $data->fecha_registro,
                'id_empresa' => $data->id_empresa,
                'id_sede' => $data->id_sede,
                'tiene_transformacion' => $data->tiene_transformacion,
                'empresa_sede' => $data->empresa_sede,
                'cantidad_items_base' => $data->cantidad_items_base,
                'estado' => $data->estado,
                'estado_doc' => $data->estado_doc,
                'bootstrap_color' => $data->bootstrap_color,
                'detalle' => [],
                'count_pendientes' => $data->count_pendientes,
            ];
        }

        $alm_det_req = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.*'
            )
            ->where('alm_det_req.estado', '=', 1)->orderBy('alm_det_req.id_requerimiento', 'DESC')
            ->get();

        if (isset($alm_det_req) && sizeof($alm_det_req) > 0) {
            foreach ($alm_det_req as $data) {
                $detalleRequerimiento[] = [
                    'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                    'id_requerimiento' => $data->id_requerimiento,
                    'id_tipo_item' => $data->id_tipo_item,
                    'descripcion_adicional' => $data->descripcion_adicional,
                    'id_item' => $data->id_item,
                    'id_unidad_medida' => $data->id_unidad_medida,
                    'unidad_medida' => $data->unidad_medida,
                    'cantidad' => $data->cantidad,
                    'precio_unitario' => $data->precio_unitario,
                    'subtotal' => $data->subtotal,
                    'partida' => $data->partida,
                    'lugar_entrega' => $data->lugar_entrega,
                    'fecha_registro' => $data->fecha_registro,
                    'estado' => $data->estado
                ];
            }
        }

        $size_det_req = count($detalleRequerimiento);
        $size_req = count($requerimiento);

        for ($i = 0; $i < $size_req; $i++) {
            for ($j = 0; $j < $size_det_req; $j++) {
                if ($detalleRequerimiento[$j]['id_requerimiento'] == $requerimiento[$i]['id_requerimiento']) {
                    $requerimiento[$i]['detalle'][] = $detalleRequerimiento[$j];
                }
            }
        }

        return response()->json(["data" => $requerimiento]);
    }

    public function get_lista_items_cuadro_costos_por_id_requerimiento_pendiente_compra(Request $request)
    {
        $requerimientoList = $request->requerimientoList;
        $temp_data = [];
        $data = [];
        $totalItemsAgregadosADetalleRequerimiento = null;

        if (count($requerimientoList) > 0) {

            $alm_req = DB::table('almacen.alm_req')
                ->select('alm_req.id_cc')
                ->whereIn('alm_req.id_requerimiento', $requerimientoList)
                ->orderBy('alm_req.id_requerimiento', 'desc')
                ->get();

            $alm_det_req = DB::table('almacen.alm_det_req')
                ->select('alm_det_req.stock_comprometido', 'alm_det_req.tiene_transformacion', 'alm_det_req.id_almacen_reserva', 'alm_det_req.id_cc_am_filas', 'alm_det_req.id_cc_venta_filas')
                // ->where('alm_det_req.tiene_transformacion', false)
                ->whereIn('alm_det_req.id_requerimiento', $requerimientoList)
                ->orderBy('alm_det_req.id_detalle_requerimiento', 'desc')
                ->get();


            $cantidadItemsRequerimiento = count($alm_det_req);

            foreach ($alm_req as $element) {
                $temp_data[] = ((new RequerimientoController)->get_detalle_cuadro_costos($element->id_cc)['detalle']);
            }
            $cantidadItemsDetalleCuadroCosto = count($temp_data[0]);
            // Debugbar::info($alm_det_req);
            // Debugbar::info($temp_data[0]);

            $idAgregadosList = [];
            if (count($temp_data) > 0) {
                foreach ($temp_data as $arr) {
                    foreach ($arr as $value) {
                        foreach ($alm_det_req as $det_req) {
                            if (($value->id == $det_req->id_cc_am_filas) && ($det_req->tiene_transformacion == false)) {
                                $idAgregadosList[] = $value->id;
                                if (($value->cantidad > ($det_req->stock_comprometido >= 0 ? $det_req->stock_comprometido : 0)) && ($det_req->id_almacen_reserva > 0)) {
                                    $data[] = [
                                        'id' => $value->id,
                                        'id_cc_am' => $value->id_cc_am,
                                        'id_cc_am_filas' => $value->id_cc_am_filas,
                                        'cantidad' => ($value->cantidad - ($det_req->stock_comprometido > 0 ? $det_req->stock_comprometido : 0)),
                                        'comentario_producto_transformado' => $value->comentario_producto_transformado,
                                        'descripcion' => $value->descripcion,
                                        'descripcion_producto_transformado' => $value->descripcion_producto_transformado,
                                        'fecha_creacion' => $value->fecha_creacion,
                                        'flete_oc' => $value->flete_oc,
                                        'garantia' => $value->garantia,
                                        'id_autor' => $value->id_autor,
                                        'nombre_autor' => $value->nombre_autor,
                                        'part_no' => $value->part_no,
                                        'part_no_producto_transformado' => $value->part_no_producto_transformado,
                                        'proveedor_seleccionado' => $value->proveedor_seleccionado,
                                        'pvu_oc' => $value->pvu_oc,
                                        'razon_social_proveedor' => $value->razon_social_proveedor,
                                        'ruc_proveedor' => $value->ruc_proveedor
                                    ];
                                }
                            }
                        }
                    }
                }

                // Debugbar::info($idAgregadosList);

                foreach ($temp_data as $arr) {
                    foreach ($arr as $value) {

                        foreach ($alm_det_req as $det_req) {
                            if (($value->id == $det_req->id_cc_am_filas)) {

                                if (in_array($value->id, $idAgregadosList, true) == false) {

                                    $data[] = [
                                        'id' => $value->id,
                                        'id_cc_am' => $value->id_cc_am,
                                        'id_cc_am_filas' => $value->id_cc_am_filas,
                                        'cantidad' => ($value->cantidad - ($det_req->stock_comprometido > 0 ? $det_req->stock_comprometido : 0)),
                                        'comentario_producto_transformado' => $value->comentario_producto_transformado,
                                        'descripcion' => $value->descripcion,
                                        'descripcion_producto_transformado' => $value->descripcion_producto_transformado,
                                        'fecha_creacion' => $value->fecha_creacion,
                                        'flete_oc' => $value->flete_oc,
                                        'garantia' => $value->garantia,
                                        'id_autor' => $value->id_autor,
                                        'nombre_autor' => $value->nombre_autor,
                                        'part_no' => $value->part_no,
                                        'part_no_producto_transformado' => $value->part_no_producto_transformado,
                                        'proveedor_seleccionado' => $value->proveedor_seleccionado,
                                        'pvu_oc' => $value->pvu_oc,
                                        'razon_social_proveedor' => $value->razon_social_proveedor,
                                        'ruc_proveedor' => $value->ruc_proveedor
                                    ];
                                }
                            }
                        }
                    }
                }
                $status = 200;
            } else {
                $status = 204;
            }
            // Debugbar::info($cantidadItemsRequerimiento);
            // Debugbar::info($cantidadItemsDetalleCuadroCosto);
            if ($cantidadItemsRequerimiento == $cantidadItemsDetalleCuadroCosto) {
                $totalItemsAgregadosADetalleRequerimiento = true;
            } else {
                $totalItemsAgregadosADetalleRequerimiento = false;
            }
        }

        $output = ['status' => $status, 'data' => $data, 'tiene_total_items_agregados' => $totalItemsAgregadosADetalleRequerimiento];

        return response()->json($output);
    }

    public function tieneItemsParaCompra(Request $request)
    {
        $requerimientoList = $request->requerimientoList;
        $tieneItems = false;
        $totalItemsAgregadosADetalleRequerimiento = null;

        $alm_det_req = DB::table('almacen.alm_det_req')
            ->leftJoin('almacen.alm_prod', 'alm_det_req.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftJoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftJoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_prod.id_clasif')
            ->leftJoin('almacen.alm_und_medida', 'alm_det_req.id_unidad_medida', '=', 'alm_und_medida.id_unidad_medida')
            ->select(
                'alm_det_req.*',
                DB::raw("(CASE 
        WHEN alm_det_req.id_cc_am_filas isNUll THEN alm_det_req.id_cc_venta_filas 
        WHEN alm_det_req.id_cc_venta_filas isNUll THEN alm_det_req.id_cc_am_filas 
        ELSE null END) AS id"),
                'alm_cat_prod.id_categoria',
                'alm_cat_prod.descripcion as categoria',
                'alm_subcat.id_subcategoria',
                'alm_subcat.descripcion as subcategoria',
                'alm_clasif.id_clasificacion as id_clasif',
                'alm_clasif.descripcion as clasificacion',
                'alm_prod.codigo AS alm_prod_codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion AS descripcion',
                'alm_und_medida.descripcion AS unidad_medida'

            )
            ->where([['alm_det_req.tiene_transformacion', false], ['alm_det_req.id_almacen_reserva', null]])
            ->whereIn('alm_det_req.id_requerimiento', $requerimientoList)
            ->get();


        $alm_req = DB::table('almacen.alm_req')
            ->select('alm_req.id_cc')
            ->whereIn('alm_req.id_requerimiento', $requerimientoList)
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();

        $alm_det_req_agregados = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.stock_comprometido', 'alm_det_req.id_almacen_reserva', 'alm_det_req.id_cc_am_filas', 'alm_det_req.id_cc_venta_filas')
            ->where('alm_det_req.tiene_transformacion', false)
            ->whereIn('alm_det_req.id_requerimiento', $requerimientoList)
            ->orderBy('alm_det_req.id_detalle_requerimiento', 'desc')
            ->get();

        $cantidadItemsRequerimiento = count($alm_det_req_agregados);

        foreach ($alm_req as $element) {
            $temp_data[] = ((new RequerimientoController)->get_detalle_cuadro_costos($element->id_cc)['detalle']);
        }
        $cantidadItemsDetalleCuadroCosto = count($temp_data[0]);

        if ($cantidadItemsRequerimiento == $cantidadItemsDetalleCuadroCosto) {
            $totalItemsAgregadosADetalleRequerimiento = true;
        } else {
            $totalItemsAgregadosADetalleRequerimiento = false;
        }

        // if(count($alm_det_req)>0){
        //     $tieneItems=true;
        // }
        $output = ['det_req' => $alm_det_req, 'tiene_total_items_agregados' => $totalItemsAgregadosADetalleRequerimiento];

        return response()->json($output);
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

    function guardarAtencionConAlmacen(Request $request)
    {

        try {
            DB::beginTransaction();
            $estado = 27;
            $lista_items_reservar = $request->lista_items_reservar;
            $total_lista_items_reservar = count($lista_items_reservar);
            $lista_items_base = $request->lista_items_base;
            $total_lista_items_base = count($lista_items_base);
            $id_requerimiento = $lista_items_reservar[0]['id_requerimiento'];
            $id_sede = $lista_items_reservar[0]['id_sede'];
            $updateDetReq = 0;

            if ($total_lista_items_reservar == $total_lista_items_base) {

                $estado = 28; // Almacén Total
            } else {
                $estado = 27; // Almacén Parcial
            }
            foreach ($lista_items_reservar as $det) {
                if ($det['cantidad_a_atender'] == $det['cantidad']) {
                    $estado = 28; // Almacén Total
                } elseif ($det['cantidad_a_atender'] < $det['cantidad']) {
                    $estado = 27; // Almacén Parcial

                }
                $updateDetReq += DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $det['id_detalle_requerimiento'])
                    ->update([
                        'stock_comprometido' => $det['cantidad_a_atender'] >0 ? $det['cantidad_a_atender']:null,
                        'id_almacen_reserva' => $det['id_almacen_reserva'] > 0 ? $det['id_almacen_reserva'] : null,
                        'estado' => $estado
                    ]);
            }

            (new LogisticaController)->actualizarEstadoRequerimientoAtendido([$id_requerimiento]);
            // (new LogisticaController)->generarTransferenciaRequerimiento($id_requerimiento, $id_sede, $data);

            $output = [
                'id_requerimiento' => $id_requerimiento,
                'update_det_req' => $updateDetReq
            ];

            DB::commit();

            return response()->json($output);
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
