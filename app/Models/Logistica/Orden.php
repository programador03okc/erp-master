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
    protected $appends = ['cuadro_costo', 'monto','fecha_formato', 'requerimientos', 'oportunidad', 'tiene_transformacion', 'cantidad_equipos', 'estado_orden', 'requerimientos_codigo','cantidad_ingresos_almacen'];

    public $timestamps = false;

    public function getFechaFormatoAttribute()
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
    public function getCantidadIngresosAlmacenAttribute()
    {
        $cantidadIngresos = 0;
        $id_detalle_orden_list=[];
        $detalleOrdenCompra = OrdenCompraDetalle::where([['id_orden_compra',$this->attributes['id_orden_compra']],['estado','!=',7]])->get();
        if (count($detalleOrdenCompra) > 0) {
            foreach ($detalleOrdenCompra as $do) {
                $id_detalle_orden_list[] = $do->id_detalle_orden;
            }
        }
        if(count($id_detalle_orden_list)>0){
            $guia_com_det = DB::table('almacen.guia_com_det')
            ->select(
                'guia_com_det.*'
            )
            ->whereIn('guia_com_det.id_oc_det', $id_detalle_orden_list)
            ->where('guia_com_det.estado', 1)
            ->get();
        $cantidadIngresos =count($guia_com_det);
        }
        
        return $cantidadIngresos;

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
        } else if ($id_tp_docum == 12) {
            $codigoOrden = "OI-{$anio}{$mes}{$correlativo}";
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
            ->select(['alm_req.id_requerimiento', 'alm_req.codigo', 'alm_req.estado', 'sis_usua.nombre_corto','alm_req.observacion'])->distinct()->get();
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
        return implode(', ', $resultado);
    }
}
