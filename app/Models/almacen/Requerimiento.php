<?php

namespace App\Models\Almacen;

use App\Models\Administracion\Estado;
use App\Models\Configuracion\Usuario;
use App\Models\Logistica\OrdenCompraDetalle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Requerimiento extends Model
{
    protected $table = 'almacen.alm_req';
    protected $primaryKey = 'id_requerimiento';
    protected $appends = ['termometro','nombre_estado','nombre_completo_usuario','ordenes_compra'];
    public $timestamps = false;


    public function getFechaEntregaAttribute(){
        $fecha= new Carbon($this->attributes['fecha_entrega']);
        return $fecha->format('d-m-Y');
    }

    public function getFechaRegistroAttribute(){
        $fecha= new Carbon($this->attributes['fecha_registro']);
        return $fecha->format('d-m-Y H:i');
    }

    public function getTermometroAttribute(){

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

    public function getNombreEstadoAttribute(){
        $estado=Estado::join('almacen.alm_req', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
        ->where('alm_req.id_requerimiento',$this->attributes['id_requerimiento'])
        ->first()->estado_doc;
        return $estado;
    }
 
    public function getNombreCompletoUsuarioAttribute(){
        $nombreUsuario= Usuario::leftJoin('almacen.alm_req', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
        ->where('alm_req.id_requerimiento',$this->attributes['id_requerimiento'])
        ->select(DB::raw("concat(rrhh_perso.nombres, ' ', rrhh_perso.apellido_paterno, ' ', rrhh_perso.apellido_materno)  AS nombre_completo_usuario"))
        ->first()->nombre_completo_usuario;
        return $nombreUsuario;
    }

    public function getOrdenesCompraAttribute(){

        $ordenes=OrdenCompraDetalle::join('almacen.alm_det_req','log_det_ord_compra.id_detalle_requerimiento','alm_det_req.id_detalle_requerimiento')
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','log_det_ord_compra.id_orden_compra')
        ->where([['alm_det_req.id_requerimiento',$this->attributes['id_requerimiento']],['log_ord_compra.estado','!=',7]])
        ->select(['log_ord_compra.id_orden_compra','log_ord_compra.codigo'])->distinct()->get(); 

        return $ordenes;
    }
 

    public static function obtenerCantidadRegistros($tipoRequerimiento,$grupo){
        $yyyy = date('Y', strtotime("now"));
        $num = Requerimiento::when(($grupo >0), function($query) use ($grupo)  {
            return $query->Where('id_grupo','=',$grupo);
        })
        ->whereYear('fecha_registro', '=', $yyyy)
        ->count();
        return $num;
    }

    public static function crearCodigo($tipoRequerimiento,$idGrupo){
        $documento = 'R'; //Prefijo para el codigo de requerimiento
        switch ($tipoRequerimiento) {
            case 1: # tipo MGCP
                $documento.='M';
                $num = Requerimiento::obtenerCantidadRegistros(1,2);
                break;
            
            case 2: #tipo Ecommerce
                $documento.='E';
                $num = Requerimiento::obtenerCantidadRegistros(2,2);
                break;
            
            case 3: case 4: case 5: case 6: case 7: #tipo:Bienes y Servicios, Compra para stock,Compra para activos,Compra para garantías,Otros
                if($idGrupo==1){
                    $documento.='A';
                    $num = Requerimiento::obtenerCantidadRegistros(3,1); //tipo: BS, grupo: Administración
                }
                if($idGrupo==2){ 
                    $documento.='C';
                    $num = Requerimiento::obtenerCantidadRegistros(3,2); //tipo: BS, grupo: Comercial
                }
                if($idGrupo==3){
                    $documento.='P';
                    $num = Requerimiento::obtenerCantidadRegistros(3,3); //tipo: BS, grupo: Proyectos
                }
                break;
            
            default:
                $num = 0;
                break;
        }
        $yy = date('y', strtotime("now"));
        $correlativo= sprintf('%04d', ($num + 1));
        
        return "{$documento}-{$yy}{$correlativo}";

    }

    public function detalle(){
        return $this->hasMany('App\Models\Almacen\DetalleRequerimiento','id_requerimiento','id_requerimiento');
    }
    public function tipo(){
        return $this->belongsTo('App\Models\Almacen\TipoRequerimiento','id_tipo_requerimiento','id_tipo_requerimiento');
    }
    public function division(){
        return $this->belongsTo('App\Models\Administracion\DivisionArea','division_id','id_division');
    }
    public function creadoPor(){
        return $this->belongsTo('App\Models\Configuracion\Usuario','id_usuario','id_usuario');
    }
    public function moneda(){
        return $this->belongsTo('App\Models\Configuracion\Moneda','id_moneda','id_moneda');
    }
}
