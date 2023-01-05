<?php

namespace App\Models\Almacen;

use App\Helpers\CuadroPresupuestoHelper;
use App\Http\Controllers\OrdenController;
use App\Models\Administracion\Aprobacion;
use App\Models\Administracion\Documento;
use App\Models\Administracion\Estado;
use App\Models\Administracion\Periodo;
use App\Models\Configuracion\Usuario;
use App\Models\Logistica\OrdenCompraDetalle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Debugbar;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class Requerimiento extends Model
{
    protected $table = 'almacen.alm_req';
    protected $primaryKey = 'id_requerimiento';
    protected $appends = ['termometro', 'nombre_estado', 'nombre_completo_usuario', 'ordenes_compra','reserva', 'cantidad_tipo_producto', 'cantidad_tipo_servicio','cantidad_adjuntos_activos','historial_aprobacion'];
    public $timestamps = false;

    // public function getMontoTotalAttribute(){
    //     $detalle= DetalleRequerimiento::where('id_requerimiento',$this->attributes['id_requerimiento'])->get();
    //     $total= 0;
    //     foreach ($detalle as $key => $value) {
    //         $total += $value['cantidad'] * $value['precio_unitario'];
    //     }
    //     return $total;
    // }

    // public function scopeFiltroEmpresa($query, $name)
    // {
    //     if ($name>0) {
    //         return $query->where('alm_req.id_empresa', '=', $name);
    //     }
    //     return $query;
    // }
    // public function scopeFiltroSede($query, $name)
    // {
    //     if ($name>0) {
    //         return $query->where('alm_req.id_sede', '=', $name);
    //     }
    //     return $query;
    // }
    // public function scopeFiltroRangoFechas($query, $desde, $hasta)
    // {
    //     if (($desde!='SIN_FILTRO' && $desde!='') && ($hasta!='SIN_FILTRO' && $hasta!='')) {
    //         return $query->whereBetween('alm_req.fecha_registro', [$desde, $hasta]);
    //     }
    //     if (($desde!='SIN_FILTRO') && ($desde!='')) {
    //         return $query->where('alm_req.fecha_registro','>', $desde);
    //     }
    //     if (($hasta !='SIN_FILTRO' && ($hasta!=''))) {
    //         return $query->where('alm_req.fecha_registro','<', $hasta);
    //     }
    //     return $query;
    // }
    // public function scopeFiltroReserva($query, $name)
    // {
    //         if($name=='SIN_RESERVA'){
    //             $query->leftJoin('almacen.alm_det_req', 'almacen.alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
    //             return $query->whereNull('almacen.alm_det_req.stock_comprometido');
    //         }elseif($name=='CON_RESERVA'){
    //             $query->leftJoin('almacen.alm_det_req', 'almacen.alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
    //             return $query->whereRaw('almacen.alm_det_req.stock_comprometido > 0'); 
    //         }

    //     return $query;
    // }
    // public function scopeFiltroOrden($query, $name)
    // {
    //     if($name=='CON_ORDEN'){
    //         $query->Join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
    //         $query->Join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');            
    //         return $query->whereRaw('log_det_ord_compra.id_detalle_requerimiento > 0'); 

    //     }elseif($name=='SIN_ORDEN'){
    //         $query->Join('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
    //         $query->Join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
    //         return $query->rightJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento');
    //     }
    //     return $query;
    // }

    public function getFechaEntregaAttribute()
    {
        if ($this->attributes['fecha_entrega'] == null) {
            return '';
        } else {
            $fecha = new Carbon($this->attributes['fecha_entrega']);
            return $fecha->format('d-m-Y');
        }
    }

    public function getFechaRegistroAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_registro']);
        return $fecha->format('d-m-Y H:i');
    }

    public function getTermometroAttribute()
    {

        switch ($this->attributes['id_prioridad']) {
            case '1':
                return '<div class="text-center"> <i class="fas fa-thermometer-empty green"  data-toggle="tooltip" data-placement="right" title="Normal"></i> </div>';
                break;

            case '2':
                return '<div class="text-center"> <i class="fas fa-thermometer-half orange"  data-toggle="tooltip" data-placement="right" title="Alta"></i> </div>';
                break;

            case '3':
                return '<div class="text-center"> <i class="fas fa-thermometer-full red"  data-toggle="tooltip" data-placement="right" title="Crítica"></i> </div>';
                break;

            default:
                return '';
                break;
        }
    }

    public function getCantidadAdjuntosActivosAttribute()
    {

        $cantidadAdjuntosCabecera = AdjuntoRequerimiento::where([['id_requerimiento',$this->attributes['id_requerimiento']],['estado','!=',7]])->count();
        $detalleRequerimiento = DetalleRequerimiento::where([['id_requerimiento',$this->attributes['id_requerimiento']],['estado','!=',7]])->get();
        $idDetalleRequerimientoList=[];
        $cantidadAdjuntosDetalle=0;
        foreach ($detalleRequerimiento as $value) {
            $idDetalleRequerimientoList[]=$value->id_detalle_requerimiento;
        }

        if(count($idDetalleRequerimientoList)>0){
            $cantidadAdjuntosDetalle= AdjuntoDetalleRequerimiento::whereIn('id_detalle_requerimiento',$idDetalleRequerimientoList)->where([['estado',1]])->count();
        }

        return ['cabecera'=>$cantidadAdjuntosCabecera, 'detalle'=>$cantidadAdjuntosDetalle];
    }

    // public function getDivisionAttribute(){


    //     $division = Requerimiento::with('detalle')
    //     ->where([
    //         ['alm_req.id_requerimiento',$this->attributes['id_requerimiento']]
    //         // ['alm_det_req.tiene_transformacion',false]
    //     ])
    //     ->first();
    //             Debugbar::info($division->detalle);
    //     return '';
    //     // return json_decode($division,true);
    // }

    // public function getCantidadStockComprometidoAttribute(){
    //     $cantidadStockComprometido = DetalleRequerimiento::join('almacen.alm_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
    //     ->where([['alm_req.id_requerimiento',$this->attributes['id_requerimiento']],
    //     ['alm_det_req.id_tipo_item',1],
    //     ['alm_det_req.stock_comprometido','>',0]
    //     ])->count();
    //     return $cantidadStockComprometido;
    // }


    public function getNombreEstadoAttribute()
    {
        $estado = Estado::join('almacen.alm_req', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->where('alm_req.id_requerimiento', $this->attributes['id_requerimiento'])
            ->first()->estado_doc??'';
        return $estado;
    }

    public function getNombreCompletoUsuarioAttribute()
    {
        $nombreUsuario = Usuario::leftJoin('almacen.alm_req', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where('alm_req.id_requerimiento', $this->attributes['id_requerimiento'])
            ->select(DB::raw("concat(rrhh_perso.nombres, ' ', rrhh_perso.apellido_paterno, ' ', rrhh_perso.apellido_materno)  AS nombre_completo_usuario"))
            ->first()->nombre_completo_usuario??'';
        return $nombreUsuario;
    }

    public function getReservaAttribute()
    {

        $reservas = Reserva::leftJoin('almacen.alm_det_req', 'alm_reserva.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
            ->where([['alm_det_req.id_requerimiento', $this->attributes['id_requerimiento']], ['alm_reserva.estado', '=', 1]])
            ->select(['alm_reserva.id_reserva', 'alm_reserva.codigo','alm_reserva.stock_comprometido'])
            ->get();

        return $reservas;
    }
    public function getOrdenesCompraAttribute()
    {

        $ordenes = OrdenCompraDetalle::join('almacen.alm_det_req', 'log_det_ord_compra.id_detalle_requerimiento', 'alm_det_req.id_detalle_requerimiento')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', 'log_det_ord_compra.id_orden_compra')
            ->where([['alm_det_req.id_requerimiento', $this->attributes['id_requerimiento']], ['log_ord_compra.estado', '!=', 7]])
            ->select(['log_ord_compra.id_orden_compra', 'log_ord_compra.codigo'])->distinct()->get();

        return $ordenes;
    }
    public function getCantidadTipoProductoAttribute()
    {

        $cantidadTipoProducto = DetalleRequerimiento::where([
            ['alm_det_req.id_requerimiento', $this->attributes['id_requerimiento']],
            ['alm_det_req.estado', '!=', 7],
            ['alm_det_req.id_tipo_item', '=', 1]
        ])
            ->count();
        return $cantidadTipoProducto;
    }
    public function getCantidadTipoServicioAttribute()
    {

        $cantidadTipoServicio = DetalleRequerimiento::where([
            ['alm_det_req.id_requerimiento', $this->attributes['id_requerimiento']],
            ['alm_det_req.estado', '!=', 7],
            ['alm_det_req.id_tipo_item', '=', 2]
        ])
            ->count();
        return $cantidadTipoServicio;
    }


    public static function obtenerCantidadRegistros($grupo, $idRequerimiento,$idPeriodo)
    {
        // $yyyy = date('Y', strtotime("now"));
        $num = Requerimiento::when(($grupo > 0), function ($query) use ($grupo, $idRequerimiento) {
            return $query->Where([['id_grupo', '=', $grupo], ['id_requerimiento', '<=', $idRequerimiento]]);
        })
            ->where('id_periodo', '=', $idPeriodo)
            // ->whereYear('fecha_registro', '=', $yyyy)
            ->count();
        return $num;
    }

    public static function crearCodigo($tipoRequerimiento, $idGrupo, $idRequerimiento, $idPeriodo)
    {

        $Periodo=Periodo::find($idPeriodo);
        $yyyy = $Periodo->descripcion;
        $yy = substr($Periodo->descripcion,2,2);

        $documento = 'R'; //Prefijo para el codigo de requerimiento
        switch ($tipoRequerimiento) {
            case 1: # tipo MGCP
                $documento .= 'M';
                $num = Requerimiento::obtenerCantidadRegistros(2, $idRequerimiento,$idPeriodo);
                break;

            case 2: #tipo Ecommerce
                $documento .= 'E';
                $num = Requerimiento::obtenerCantidadRegistros(2, $idRequerimiento,$idPeriodo);
                break;

            case 3:
            case 4:
            case 5:
            case 6:
            case 8:
            case 7: #tipo:Bienes y Servicios, Compra para stock,Compra para activos,Compra para garantías,Otros
                if ($idGrupo == 1) {
                    $documento .= 'A';
                    $num = Requerimiento::obtenerCantidadRegistros(1, $idRequerimiento,$idPeriodo); //tipo: BS, grupo: Administración
                }
                if ($idGrupo == 2) {
                    $documento .= 'C';
                    $num = Requerimiento::obtenerCantidadRegistros(2, $idRequerimiento,$idPeriodo); //tipo: BS, grupo: Comercial
                }
                if ($idGrupo == 3) {
                    $documento .= 'P';
                    $num = Requerimiento::obtenerCantidadRegistros(3, $idRequerimiento,$idPeriodo); //tipo: BS, grupo: Proyectos
                }
                if ($idGrupo == 4) {
                    $documento .= 'G';
                    $num = Requerimiento::obtenerCantidadRegistros(4, $idRequerimiento,$idPeriodo); //tipo: BS, grupo: Proyectos
                }
                if ($idGrupo == 5) {
                    $documento .= 'CI';
                    $num = Requerimiento::obtenerCantidadRegistros(5, $idRequerimiento,$idPeriodo); //tipo: BS, grupo: Proyectos
                }
                if ($idGrupo == 6) {
                    $documento .= 'MK';
                    $num = Requerimiento::obtenerCantidadRegistros(6, $idRequerimiento,$idPeriodo); //tipo: BS, grupo: Proyectos
                }
                break;

            default:
                $num = 0;
                break;
        }
        // $yy = date('y', strtotime("now"));
        $correlativo = sprintf('%04d', $num);

        return "{$documento}-{$yy}{$correlativo}";
    }

    public static function actualizarEstadoRequerimientoAtendido($tipoPeticion,$id_requerimiento_list)
    {

        $id_requerimiento_unique_list =  array_unique($id_requerimiento_list);

        $estadoActual = ['id' => 2, 'descripcion' => 'Aprobado'];

        if (count($id_requerimiento_unique_list) > 0) {
            foreach ($id_requerimiento_unique_list as  $idRequerimiento) {
                $total_items = 0;
                $total_estado_elaborado = 0;
                $total_estado_atentido_total = 0;
                $total_estado_atentido_parcial = 0;
                $total_estado_almacen_total = 0;
                $total_estado_almacen_parcial = 0;
                $alm_det_req = DB::table('almacen.alm_det_req')
                    ->select(
                        'alm_det_req.*'
                    )
                    ->where([['alm_det_req.tiene_transformacion', false], ['alm_det_req.estado', '!=', 7]])
                    ->where('alm_det_req.id_requerimiento', $idRequerimiento)
                    ->get();


                foreach ($alm_det_req as $data) {
                    $total_items += 1;
                }


                foreach ($alm_det_req as $det_req) {
                    if (intval($det_req->estado) == 1) {
                        $total_estado_elaborado += 1;
                    }
                    if (!in_array(intval($det_req->estado),array(1,15,28,27,7))) { // *atendidos total y posterior
                        $total_estado_atentido_total += 1;
                    }
                    if (intval($det_req->estado) == 15) {
                        $total_estado_atentido_parcial += 1;
                    }
                    if (intval($det_req->estado) == 28) {
                        $total_estado_almacen_total += 1;
                    }
                    if (intval($det_req->estado) == 27) {
                        $total_estado_almacen_parcial += 1;
                    }
                }
                if ($total_estado_elaborado > 0) {
                    $estadoActual = ['id' => 2, 'descripcion' => 'Aprobado'];
                } elseif ($total_estado_elaborado == 0 && $total_estado_atentido_parcial > 0) {
                    $estadoActual = ['id' => 15, 'descripcion' => 'Atendido parcial'];
                } elseif ($total_estado_elaborado == 0 && $total_estado_almacen_parcial > 0) {
                    $estadoActual = ['id' => 27, 'descripcion' => 'Almacen parcial'];
                } elseif ($total_estado_elaborado == 0 && $total_estado_atentido_parcial == 0 && $total_estado_atentido_total > 0) {
                    $estadoActual = ['id' => 5, 'descripcion' => 'Atendido total'];
                } elseif ($total_estado_elaborado == 0 && $total_estado_atentido_parcial == 0 && $total_estado_atentido_total == 0 && $total_estado_almacen_total > 0) {
                    $estadoActual = ['id' => 28, 'descripcion' => 'Almacén total'];
                }

                DB::table('almacen.alm_req')
                ->where('alm_req.id_requerimiento', $idRequerimiento)
                ->update(
                    [
                        'estado' => $estadoActual['id']
                    ]
                );

            }
            if(Auth::user()->id_usuario==17 || Auth::user()->id_usuario==3){ // si usuario es rhuancac, reconodir no finalizar CDP
                $finalizadosORestablecido['lista_finalizados']=[];
                $finalizadosORestablecido['lista_restablecidos']=[];
            }else{
                $finalizadosORestablecido = CuadroPresupuestoHelper::finalizar($tipoPeticion,$id_requerimiento_unique_list);
            }

        }
        return ['estado_actual'=>$estadoActual,'lista_finalizados'=>$finalizadosORestablecido['lista_finalizados'],'lista_restablecidos'=>$finalizadosORestablecido['lista_restablecidos']];

    }

    public function getHistorialAprobacionAttribute()
    {
        $historialAprobaciones=[];
        $documento= Documento::where([["id_doc",$this->attributes['id_requerimiento']], ['id_tp_documento',1]])->first();
        if(isset($documento->id_doc_aprob) && $documento->id_doc_aprob >0){
            $historialAprobaciones= Aprobacion::leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'adm_aprobacion.id_usuario')
            ->leftJoin('administracion.adm_vobo', 'adm_vobo.id_vobo', '=', 'adm_aprobacion.id_vobo')
            ->leftJoin('administracion.adm_flujo', 'adm_flujo.id_flujo', '=', 'adm_aprobacion.id_flujo')
            ->where("adm_aprobacion.id_doc_aprob",$documento->id_doc_aprob)
            ->select('adm_aprobacion.*',
                'adm_vobo.descripcion as descripcion_vobo',
                'adm_flujo.nombre as nombre_flujo',
                'sis_usua.nombre_corto as nombre_usuario_aprobador'
                )
            ->get();
        }
         if(isset($historialAprobaciones) && count($historialAprobaciones)>0){
            return $historialAprobaciones;

         }else{
            return [];
         }

    }


    public function detalle()
    {
        return $this->hasMany('App\Models\Almacen\DetalleRequerimiento', 'id_requerimiento', 'id_requerimiento');
    }
    public function adjuntoRequerimiento(){
        return $this->hasMany('App\Models\Almacen\AdjuntoRequerimiento','id_requerimiento','id_requerimiento');
    }
    public function tipo()
    {
        return $this->belongsTo('App\Models\Almacen\TipoRequerimiento', 'id_tipo_requerimiento', 'id_tipo_requerimiento');
    }
    public function division()
    {
        return $this->belongsTo('App\Models\Administracion\Division', 'division_id', 'id_division');
    }
    public function creadoPor()
    {
        return $this->belongsTo('App\Models\Configuracion\Usuario', 'id_usuario', 'id_usuario');
    }
    public function moneda()
    {
        return $this->belongsTo('App\Models\Configuracion\Moneda', 'id_moneda', 'id_moneda');
    }
    public function empresa()
    {
        return $this->hasOne('App\Models\Administracion\Empresa', 'id_empresa', 'id_empresa');
    }
    public function sede()
    {
        return $this->hasOne('App\Models\Administracion\Sede', 'id_sede', 'id_sede');
    }
    public function cuadroCostos()
    {
        return $this->hasOne('App\Models\Comercial\CuadroCosto\CuadroCostosView', 'id', 'id_cc');
    }
    // public function almacen()
    // {
    //     return $this->hasOne('App\Models\almacen\Almacen', 'id_almacen', 'id_almacen');
    // }
}
