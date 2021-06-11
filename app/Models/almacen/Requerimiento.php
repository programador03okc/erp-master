<?php

namespace App\Models\Almacen;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    protected $table = 'almacen.alm_req';
    protected $primaryKey = 'id_requerimiento';
    public $timestamps = false;

    public function getFechaEntregaAttribute(){
        $fecha= new Carbon($this->attributes['fecha_entrega']);
        return $fecha->format('d-m-Y');
    }


    public static function obtenerCantidadRegistros($tipoRequerimiento,$grupo){
        $yyyy = date('Y', strtotime("now"));
        $num = Requerimiento::where('id_tipo_requerimiento',$tipoRequerimiento)
        ->when(($grupo >0), function($query) use ($grupo)  {
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
                $num = Requerimiento::obtenerCantidadRegistros(1,null);
                break;
            
            case 2: #tipo Ecommerce
                $documento.='E';
                $num = Requerimiento::obtenerCantidadRegistros(2,null);
                break;
            
            case 3: #tipo Bienes y servicios
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

}
