<?php


namespace App\Models\Logistica;

use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\mgcp\AcuerdoMarco\OrdenCompraPropias;
use App\Models\mgcp\CuadroCosto\CcSolicitud;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Debugbar;
use Illuminate\Validation\Rules\Unique;

class Orden extends Model
{

    protected $table = 'logistica.log_ord_compra';
    protected $primaryKey = 'id_orden_compra';
    protected $appends = ['cuadro_costo', 'monto', 'requerimientos', 'oportunidad', 'tiene_transformacion', 'cantidad_equipos', 'estado_orden', 'requerimientos_codigo'];

    public $timestamps = false;

    public function getFechaAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha']);
        return $fecha->format('d-m-Y h:m');
    }
    public function getFechaOrdenAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_orden']);
        return $fecha->format('d-m-Y h:m');
    }

    public function getFechaRegistroRequerimientoAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_registro_requerimiento']);
        return $fecha->format('d-m-Y');
    }
    public function getFechaIngresoAlmacenAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_ingreso_almacen']);
        return $fecha->format('d-m-Y');
    }
    public function getEstadoOrdenAttribute()
    {
        $estado = ($this->attributes['estado']);
        $estado_descripcion = EstadoCompra::find($estado)->first()->descripcion;
        return $estado_descripcion;
    }
    // public function getFechaVencimientoOcamAttribute(){
    //     $fecha= new Carbon($this->attributes['fecha_vencimiento_ocam']);
    //     return $fecha->format('d-m-Y');
    // }
    public function getFechaEntregaAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_entrega']);
        return $fecha->format('d-m-Y');
    }
    public function getCuadroCostoAttribute()
    {
        $idCuadroCostoList = [];
        $idReqList = [];
        $data = [];
        $detalleOrden = OrdenCompraDetalle::where([['id_orden_compra', $this->attributes['id_orden_compra']], ['estado', '!=', 7]])->get();
        foreach ($detalleOrden as $do) {

            if ($do->id_detalle_requerimiento > 0) {
                $detReq = DetalleRequerimiento::find($do->id_detalle_requerimiento);
                $idReqList[] = $detReq->id_requerimiento;
            }
        }

        $req = Requerimiento::whereIn('id_requerimiento', array_unique($idReqList))->get();
        foreach ($req as $r) {
            if ($r->id_cc > 0) {
                $idCuadroCostoList[] = $r->id_cc;
            }
        }

        $ccVista = CuadroCostoView::whereIn('id', $idCuadroCostoList)->get();

        foreach ($ccVista as $cc) {
            $ccSolicitud = CcSolicitud::where([['id_cc', $cc->id], ['aprobada', true], ['id_tipo', 1]])->orderBy("id", 'desc')->first();

            $data[] = [
                'id' => $cc->id,
                'codigo_oportunidad' => $cc->codigo_oportunidad,
                'fecha_creacion' => $cc->fecha_creacion,
                'fecha_limite' => $cc->fecha_limite,
                'estado_aprobacion_cuadro' => $cc->estado_aprobacion,
                'fecha_aprobacion' => $ccSolicitud->fecha_solicitud ?? null,
                'id_estado_aprobacion' => $cc->id_estado_aprobacion,
                'estado_aprobacion' => $cc->estado_aprobacion
            ];
        }

        return $data;
    }

    public function mostrar()
    {
        $data = Orden::select('log_ord_compra.*')
            ->where('log_ord_compra.estado', 1)
            ->get();
        return $data;
    }


    public static function leftZero($lenght, $number)
    {
        $nLen = strlen($number);
        $zeros = '';
        for ($i = 0; $i < ($lenght - $nLen); $i++) {
            $zeros = $zeros . '0';
        }
        return $zeros . $number;
    }

    public static function nextCodigoOrden($id_tp_docum)
    {
        $mes = date('m', strtotime("now"));
        $anio = date('y', strtotime("now"));

        $num = DB::table('logistica.log_ord_compra')
            ->where('id_tp_documento', $id_tp_docum)->count();

        $correlativo = Orden::leftZero(4, ($num + 1));

        if ($id_tp_docum == 2) {
            $codigoOrden = "OC-{$anio}{$mes}{$correlativo}";
        } else if ($id_tp_docum == 3) {
            $codigoOrden = "OS-{$anio}{$mes}{$correlativo}";
        } else {
            $codigoOrden = "-{$anio}{$mes}{$correlativo}";
        }
        return $codigoOrden;
    }

    public function getTieneTransformacionAttribute()
    {

        $requerimiento = OrdenCompraDetalle::where('log_det_ord_compra.id_orden_compra', $this->attributes['id_orden_compra'])
            ->leftJoin('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', 'alm_det_req.id_detalle_requerimiento')
            ->Join('almacen.alm_req', 'alm_req.id_requerimiento', 'alm_det_req.id_requerimiento')
            ->select('alm_req.tiene_transformacion')->get();
        if (!empty($requerimiento->first())) {
            return $requerimiento->first()->tiene_transformacion;
        } else {
            return 'NO APLICA';
        }
    }
    public function getMontoAttribute()
    {

        $Montototal = OrdenCompraDetalle::where('log_det_ord_compra.id_orden_compra', $this->attributes['id_orden_compra'])
            ->select(DB::raw('sum(log_det_ord_compra.cantidad * log_det_ord_compra.precio) as total'))->first();
        return $Montototal->total;
    }

    public function getCantidadEquiposAttribute()
    {

        $equipos = OrdenCompraDetalle::where('log_det_ord_compra.id_orden_compra', $this->attributes['id_orden_compra'])
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', 'log_det_ord_compra.id_producto')
            ->select('alm_prod.descripcion', 'log_det_ord_compra.cantidad', 'log_det_ord_compra.descripcion_adicional')->get();
        $cantidadEquipoList = [];
        foreach ($equipos as $equipo) {
            // $cantidadEquipoList[]= '('.(floatval($equipo->cantidad) <10?('0'.$equipo->cantidad):$equipo->cantidad).' Ud.) '.(utf8_decode($equipo->descripcion)); 
            $cantidadEquipoList[] = '(' . (floatval($equipo->cantidad) < 10 ? ('0' . $equipo->cantidad) : $equipo->cantidad) . ' Ud.) ' . $equipo->descripcion != '' ? (preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $equipo->descripcion)) : (preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $equipo->descripcion_adicional));
        }
        return implode(' + ', $cantidadEquipoList);
    }


    public function getRequerimientosAttribute()
    {

        $requerimientos = OrdenCompraDetalle::leftJoin('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', 'alm_det_req.id_detalle_requerimiento')
            ->Join('almacen.alm_req', 'alm_req.id_requerimiento', 'alm_det_req.id_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->where('log_det_ord_compra.id_orden_compra', $this->attributes['id_orden_compra'])
            ->select(['alm_req.id_requerimiento', 'alm_req.codigo', 'alm_req.estado', 'sis_usua.nombre_corto'])->distinct()->get();
        return $requerimientos;
    }

    public function getOportunidadAttribute()
    {

        $oportunidadList = [];

        $requerimientos = OrdenCompraDetalle::leftJoin('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', 'alm_det_req.id_detalle_requerimiento')
            ->Join('almacen.alm_req', 'alm_req.id_requerimiento', 'alm_det_req.id_requerimiento')
            ->where('log_det_ord_compra.id_orden_compra', $this->attributes['id_orden_compra'])
            ->select(['alm_req.id_requerimiento', 'alm_req.id_cc'])->distinct()->get();

        foreach ($requerimientos as $r) {
            $cc = CuadroCosto::with('oportunidad', 'oportunidad.responsable')->where('cc.id', $r->id_cc)->first();
            if ($cc) {
                $oportunidadList[] = [
                    'codigo_oportunidad' => $cc->oportunidad->codigo_oportunidad,
                    'responsable' => $cc->oportunidad->responsable->name,
                ];
            }
        }

        return $oportunidadList;
    }

    // public function getCuadroCostoAttribute(){

    //     $cc=OrdenCompraDetalle::leftJoin('almacen.alm_det_req','log_det_ord_compra.id_detalle_requerimiento','alm_det_req.id_detalle_requerimiento')
    //     ->Join('almacen.alm_req','alm_req.id_requerimiento','alm_det_req.id_requerimiento')
    //     ->leftJoin('mgcp_cuadro_costos.cc_view','alm_req.id_cc','cc_view.id')
    //     ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc_view.id_oportunidad')
    //     ->where('log_det_ord_compra.id_orden_compra',$this->attributes['id_orden_compra'])
    //     ->select(
    //         'cc_view.codigo_oportunidad',
    //         'cc_view.fecha_creacion',
    //         'cc_view.fecha_limite',
    //         'oc_propias_view.estado_aprobacion_cuadro',
    //         'oc_propias_view.fecha_estado'
    //         )
    //     ->first(); 
    //     return $cc;
    // }
    // public function getCuadroCostoAttribute(){

    //     if($this->attributes['id_occ'] != null){
    //         $cc=CuadroCostosView::
    //         leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc_view.id_oportunidad')
    //         ->where('cc_view.id',$this->attributes['id_occ'])
    //         ->select(
    //             'cc_view.codigo_oportunidad',
    //             'cc_view.fecha_creacion',
    //             'cc_view.fecha_limite',
    //             'oc_propias_view.estado_aprobacion_cuadro',
    //             'oc_propias_view.fecha_estado'
    //             )
    //         ->first(); 
    //         return $cc;

    //     }else{
    //         return '';
    //     }
    // }

    public static function reporteListaOrdenes()
    {
        $ord_compra = Orden::select(
            'log_ord_compra.*',
            'sis_sede.descripcion as descripcion_sede_empresa',
            DB::raw("(CASE 
            WHEN log_ord_compra.id_condicion = 1 THEN log_cdn_pago.descripcion 
            WHEN log_ord_compra.id_condicion = 2 THEN log_cdn_pago.descripcion || ' ' || log_ord_compra.plazo_dias  || ' Días'
            ELSE null END) AS condicion
            "),
            'sis_moneda.simbolo as moneda_simbolo',
            'sis_moneda.descripcion as moneda_descripcion',
            'adm_contri.id_contribuyente',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'estados_compra.descripcion as estado_doc',
            'log_ord_compra.estado',
            'log_ord_compra_pago.id_pago',
            'log_ord_compra_pago.detalle_pago',
            'log_ord_compra_pago.archivo_adjunto',


            DB::raw("(SELECT  coalesce(sum((log_det_ord_compra.cantidad * log_det_ord_compra.precio))*1.18 ,0) AS monto_total_orden
            FROM logistica.log_det_ord_compra 
            WHERE   log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra AND
                    log_det_ord_compra.estado != 7) AS monto_total_orden"),

            DB::raw("(SELECT  coalesce(oc_propias_view.monto_soles) AS monto_total_presup
            FROM logistica.log_det_ord_compra 
            INNER JOIN almacen.alm_det_req on alm_det_req.id_detalle_requerimiento = log_det_ord_compra.id_detalle_requerimiento
            INNER JOIN almacen.alm_req on alm_req.id_requerimiento = alm_det_req.id_requerimiento
            INNER JOIN mgcp_cuadro_costos.cc on cc.id = alm_req.id_cc
            INNER JOIN mgcp_ordenes_compra.oc_propias_view on oc_propias_view.id_oportunidad = cc.id_oportunidad

            WHERE log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra AND
            logistica.log_det_ord_compra.estado != 7 LIMIT 1) AS monto_total_presup")
        )
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'log_ord_compra.id_moneda')
            ->leftjoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->leftjoin('contabilidad.adm_cta_contri as cta_prin', 'cta_prin.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_principal')
            ->leftjoin('contabilidad.adm_cta_contri as cta_alter', 'cta_alter.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_alternativa')
            ->leftjoin('contabilidad.adm_cta_contri as cta_detra', 'cta_detra.id_cuenta_contribuyente', '=', 'log_ord_compra.id_cta_detraccion')
            ->leftjoin('logistica.estados_compra', 'estados_compra.id_estado', '=', 'log_ord_compra.estado')
            ->leftjoin('logistica.log_ord_compra_pago', 'log_ord_compra_pago.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
            ->where('log_ord_compra.estado', '!=', 7)
            ->orderBy('log_ord_compra.fecha', 'desc')
            ->get();

        $data_orden = [];
        if (count($ord_compra) > 0) {
            foreach ($ord_compra as $element) {



                $data_orden[] = [
                    'id_orden_compra' => $element->id_orden_compra,
                    'id_tp_documento' => $element->id_tp_documento,
                    'fecha' => date_format(date_create($element->fecha), 'Y-m-d'),
                    // 'fecha' => $element->fecha, 
                    'codigo' => $element->codigo,
                    'codigo_softlink' => $element->codigo_softlink,
                    'descripcion_sede_empresa' => $element->descripcion_sede_empresa,
                    'nro_documento' => $element->nro_documento,
                    'razon_social' => $element->razon_social,
                    'id_moneda' => $element->id_moneda,
                    'moneda_simbolo' => $element->moneda_simbolo,
                    'incluye_igv' => $element->incluye_igv,
                    'monto_igv' => $element->monto_igv,
                    'monto_total' => $element->monto_total,
                    'condicion' => $element->condicion,
                    'plazo_entrega' => $element->plazo_entrega,
                    'nro_cuenta_prin' => $element->nro_cuenta_prin,
                    'nro_cuenta_alter' => $element->nro_cuenta_alter,
                    'nro_cuenta_detra' => $element->nro_cuenta_detra,
                    'codigo_cuadro_comparativo' => '',
                    'estado' => $element->estado,
                    'estado_doc' => $element->estado_doc,
                    'detalle_pago' => $element->detalle_pago,
                    'archivo_adjunto' => $element->archivo_adjunto,
                    'monto_total_orden' => $element->monto_total_orden,
                    'monto_total_presup' => $element->monto_total_presup,
                    'tipo_cambio_compra' => $element->tipo_cambio_compra,
                    'facturas' => implode(',', Orden::obtenerFacturas($element->id_orden_compra)),
                    'codigo_requerimiento' => []

                ];
            }
        }

        $detalle_orden = Orden::select(
            'log_ord_compra.id_orden_compra',
            'log_det_ord_compra.id_detalle_orden',
            'alm_req.id_requerimiento',
            'alm_req.codigo as codigo_requerimiento',
            'alm_req.fecha_registro as fecha_registro_requerimiento',
            'cc_view.codigo_oportunidad',
            'oc_propias_view.fecha_entrega',
            'guia_com_det.fecha_registro as fecha_ingreso_almacen',
            'oc_propias_view.fecha_estado',
            'oc_propias_view.estado_aprobacion_cuadro'
        )
            ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_det_ord_compra.id_detalle_requerimiento')
            ->leftJoin('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_cuadro_costos.cc_view', 'cc_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->where([
                ['log_ord_compra.estado', '!=', 7],
                ['log_det_ord_compra.id_detalle_requerimiento', '>', 0]

            ])
            ->orderBy('log_ord_compra.fecha', 'desc')
            ->get();

        $data_detalle_orden = [];
        if (count($ord_compra) > 0) {
            foreach ($detalle_orden as $element) {

                $data_detalle_orden[] = [
                    'id_orden_compra' => $element->id_orden_compra,
                    'id_detalle_orden' => $element->id_detalle_orden,
                    'codigo_requerimiento' => $element->codigo_requerimiento,
                    'codigo_oportunidad' => $element->codigo_oportunidad,
                    'fecha_entrega' => $element->fecha_entrega,
                    'fecha_ingreso_almacen' => date_format(date_create($element->fecha_ingreso_almacen), 'Y-m-d'),
                    'estado_aprobacion' => $element->estado_aprobacion_cuadro,
                    'fecha_estado' => $element->fecha_estado,
                    'fecha_registro_requerimiento' => date_format(date_create($element->fecha_registro_requerimiento), 'Y-m-d')
                ];
            }
        }


        foreach ($data_orden as $ordenKey => $ordenValue) {
            foreach ($data_detalle_orden as $detalleOrdnKey => $detalleOrdenValue) {
                if ($ordenValue['id_orden_compra'] == $detalleOrdenValue['id_orden_compra']) {
                    if (in_array($detalleOrdenValue['codigo_requerimiento'], $data_orden[$ordenKey]['codigo_requerimiento']) == false) {
                        $data_orden[$ordenKey]['codigo_requerimiento'][] = $detalleOrdenValue['codigo_requerimiento'];
                        $data_orden[$ordenKey]['codigo_oportunidad'] = $detalleOrdenValue['codigo_oportunidad'];
                        $data_orden[$ordenKey]['fecha_vencimiento_ocam'] = $detalleOrdenValue['fecha_entrega'];
                        $data_orden[$ordenKey]['fecha_ingreso_almacen'] = $detalleOrdenValue['fecha_ingreso_almacen'];
                        $data_orden[$ordenKey]['estado_aprobacion_cc'] = $detalleOrdenValue['estado_aprobacion'];
                        $data_orden[$ordenKey]['fecha_estado'] = $detalleOrdenValue['fecha_estado'];
                        $data_orden[$ordenKey]['fecha_registro_requerimiento'] = $detalleOrdenValue['fecha_registro_requerimiento'];
                    }
                }
            }
        }

        $data = [];
        foreach ($data_orden as $d) {
            $fechaHoy = Carbon::now();
            $fechaOrden = Carbon::create($d['fecha']);
            $fechaLlegada = Carbon::create($d['fecha'])->addDays($d['plazo_entrega']);
            $diasRestantes = $fechaLlegada->diffInDays($fechaHoy);

            // $fechaRegistroRequerimiento = new Carbon($d['fecha_registro_requerimiento']);
            // $fechaRegistroAlmacen=  new Carbon($d['fecha_ingreso_almacen']);

            $fechaRegistroRequerimiento = $d['fecha_registro_requerimiento'] ?? '';
            $fechaRegistroAlmacen = $d['fecha_ingreso_almacen'] ?? '';


            $tiempo_atencion_logistica = $fechaOrden->diffInDays($fechaRegistroRequerimiento);
            $tiempo_atencion_almacen = $fechaOrden->diffInDays($fechaRegistroAlmacen);
            $data[] = [
                'codigo_cuadro_costos' => $d['codigo_oportunidad'] ?? '',
                'proveedor' => $d['razon_social'],
                'codigo_orden' => $d['codigo'],
                'codigo_softlink' => $d['codigo_softlink'],
                'codigo_requerimiento_o_codigo_cuadro_comparativo' => $d['codigo_requerimiento'] ? $d['codigo_requerimiento'] : $d['codigo_cuadro_comparativo'],
                'estado' => $d['estado_doc'],
                'fecha_vencimiento' => $d['fecha_vencimiento_ocam'] ?? '',
                'fecha_llegada' => $fechaRegistroAlmacen,
                'estado_aprobacion_cc' => $d['estado_aprobacion_cc'] ?? '',
                'fecha_estado' => $d['fecha_estado'] ?? '',
                'fecha_registro_requerimiento' => $fechaRegistroRequerimiento,
                'leadtime' => $fechaLlegada->toDateString() . ' (días restantes: ' . $diasRestantes . ')',
                'empresa_sede' => $d['descripcion_sede_empresa'],
                'moneda' => $d['moneda_simbolo'],
                'condicion' => $d['condicion'],
                'fecha_orden' => $d['fecha'],
                'tiempo_atencion_logistica' => isset($tiempo_atencion_logistica) ? $tiempo_atencion_logistica : '',
                'tiempo_atencion_almacen' => isset($tiempo_atencion_almacen) ? $tiempo_atencion_almacen : '',
                'facturas' => $fechaOrden,
                'monto_total_presup' => $d['monto_total_presup'] > 0 ? number_format($d['monto_total_presup'], 2) : '(No aplica)',
                'monto_total_orden' => ($d['id_moneda'] == 2 && $d['tipo_cambio_compra'] > 0) ? ('S/' . number_format(($d['monto_total_orden'] * $d['tipo_cambio_compra']), 2)) : ($d['moneda_simbolo'] . number_format($d['monto_total_orden'], 2)),
                'detalle_pago' => $d['detalle_pago'],
                'tipo_cambio_compra' => $d['tipo_cambio_compra']
            ];
        }

        return $data;
    }


    public static function obtenerFacturas($idOrden)
    {
        $facturas = [];
        $sql_facturas = DB::table('logistica.log_det_ord_compra')
            ->select(DB::raw("concat(doc_com.serie, '-', doc_com.numero) AS facturas"))
            ->join('almacen.guia_com_det', 'guia_com_det.id_oc_det', '=', 'log_det_ord_compra.id_detalle_orden')
            ->leftjoin('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', '=', 'guia_com_det.id_guia_com_det')
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
            ->where('log_det_ord_compra.id_orden_compra', $idOrden)
            ->get();
        if (count($sql_facturas) > 0) {
            foreach ($sql_facturas as $value) {
                $facturas[] = $value->facturas;
            }
        }
        return array_values(array_unique($facturas));
    }


    public function detalle()
    {
        return $this->hasMany('App\Models\Logistica\OrdenCompraDetalle', 'id_orden_compra', 'id_orden_compra');
    }
    public function sede()
    {
        return $this->hasOne('App\Models\Administracion\Sede', 'id_sede', 'id_sede');
    }
    public function proveedor()
    {
        return $this->hasOne('App\Models\Logistica\Proveedor', 'id_proveedor', 'id_proveedor');
    }
    public function moneda()
    {
        return $this->belongsTo('App\Models\Configuracion\Moneda', 'id_moneda', 'id_moneda');
    }
    public function estado()
    {
        return $this->hasOne('App\Models\Logistica\EstadoCompra', 'id_estado', 'estado');
    }
    public function estado_orden()
    {
        return $this->hasOne('App\Models\Logistica\EstadoCompra', 'id_estado', 'estado');
    }

    public function getRequerimientosCodigoAttribute()
    {

        $requerimientos = OrdenCompraDetalle::leftJoin('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', 'alm_det_req.id_detalle_requerimiento')
            ->Join('almacen.alm_req', 'alm_req.id_requerimiento', 'alm_det_req.id_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->where('log_det_ord_compra.id_orden_compra', $this->attributes['id_orden_compra'])
            ->select(['alm_req.id_requerimiento', 'alm_req.codigo', 'alm_req.estado', 'sis_usua.nombre_corto'])->distinct()->get();
        $resultado = [];
        foreach ($requerimientos as $req) {
            array_push($resultado, $req->codigo);
        }
        return $resultado;
    }
}
