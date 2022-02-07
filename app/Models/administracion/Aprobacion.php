<?php

namespace App\Models\Administracion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Aprobacion extends Model
{
    protected $table = 'administracion.adm_aprobacion';
    protected $primaryKey = 'id_aprobacion';
    public $timestamps = false;


    public static function getVoBo($id_doc_aprobacion)
    {
        if ($id_doc_aprobacion > 0) {
            $adm_aprobacion = Aprobacion::select(
                'adm_aprobacion.id_aprobacion',
                'adm_aprobacion.id_flujo',
                'adm_aprobacion.id_vobo',
                'adm_aprobacion.fecha_vobo',
                'adm_vobo.descripcion as accion',
                'adm_aprobacion.id_usuario',
                DB::raw("CONCAT(pers.nombres,' ',pers.apellido_paterno,' ',pers.apellido_materno) as nombre_usuario"),
                'sis_usua.nombre_corto',
                'adm_aprobacion.detalle_observacion',
                'adm_aprobacion.tiene_sustento',
                'adm_flujo.id_operacion',
                'adm_flujo.id_rol',
                'sis_rol.descripcion as descripcion_rol',
                'adm_flujo.nombre as nombre_flujo',
                'adm_flujo.orden'
            )
                ->leftJoin('administracion.adm_flujo', 'adm_aprobacion.id_flujo', '=', 'adm_flujo.id_flujo')
                ->leftJoin('administracion.adm_vobo', 'adm_aprobacion.id_vobo', '=', 'adm_vobo.id_vobo')
                ->leftJoin('configuracion.sis_usua', 'adm_aprobacion.id_usuario', '=', 'sis_usua.id_usuario')
                ->leftJoin('configuracion.sis_rol', 'adm_flujo.id_rol', '=', 'sis_rol.id_rol')
                ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
                ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
                ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')
                ->leftJoin('administracion.adm_operacion', 'adm_flujo.id_operacion', '=', 'adm_operacion.id_operacion')
                ->where([
                    ['id_doc_aprob', '=', $id_doc_aprobacion]
                    // ['adm_flujo.estado', '=', 1]
                ])
                ->orderBy('adm_aprobacion.fecha_vobo', 'asc')
                ->get();

            if (isset($adm_aprobacion) && (count($adm_aprobacion) > 0)) {
                $status = 200;
                $message = 'OK';
                foreach ($adm_aprobacion as $element) {
                    $aprobacion_list[] = $element;
                }
            } else {
                $aprobacion_list = [];
                $status = 204; // No Content
                $message = 'No Content, data vacia';
            }
        } else {
            $aprobacion_list = [];
            $status = 400; //Bad Request
            $message = 'Bad Request, necesita un parametro';
        }

        $output = ['data' => $aprobacion_list, 'status' => $status, 'message' => $message];

        return $output;
    }
    public static function getCantidadAprobacionesRealizadas($id_doc_aprobacion)
    {
        $ultimaObservacion = Aprobacion::select(
                'adm_aprobacion.*')
                ->where([
                    ['id_doc_aprob', '=', $id_doc_aprobacion],
                    ['id_vobo', '=', 3]
                ])
                ->orderBy('adm_aprobacion.fecha_vobo','desc')
                ->first();
        
        $fechaUltimaObservacion='';
        if($ultimaObservacion){
            $fechaUltimaObservacion = $ultimaObservacion->fecha_vobo;
        }

        $cantidadAprobaciones = Aprobacion::select(
                'adm_aprobacion.*')
                ->where('id_doc_aprob', '=', $id_doc_aprobacion)
                ->whereIn('id_vobo',[1,5])
                ->when((strlen($fechaUltimaObservacion) > 0), function ($query)  use ($fechaUltimaObservacion) {
                    return $query->whereRaw('adm_aprobacion.fecha_vobo >=  TIMESTAMP \'' . $fechaUltimaObservacion.'\'');
                })
                ->count();
        return $cantidadAprobaciones;

    }
    public static function getUltimoVoBo($id_doc_aprobacion)
    {
        $ultimaAprobacion = Aprobacion::select(
                'adm_aprobacion.*')
                ->where([
                    ['id_doc_aprob', '=', $id_doc_aprobacion]
                ])
                ->orderBy('adm_aprobacion.fecha_vobo','desc')
                ->first();
    
        return $ultimaAprobacion;
    }

    public static function getObservaciones($id_doc_aprob)
    {
        $obs =  Aprobacion::where([['id_doc_aprob', $id_doc_aprob], ['id_vobo', 3]])
            ->get();
        return $obs;
    }
    public static function cantidadAprobaciones($doc)
    {
        $sql = DB::table('administracion.adm_aprobacion')->where([['id_vobo', '=', 1], ['id_doc_aprob', '=', $doc]])->get();
        return $sql->count();
    }

    public function getFechaVoboAttribute(){
        $fecha= new Carbon($this->attributes['fecha_vobo']);
        return $fecha->format('d-m-Y H:i');
    }

    public static function getHeaderObservacion($id_doc_aprob){
        
        $data=[];
        $obs =  Aprobacion::select('adm_aprobacion.*',
        DB::raw("concat(rrhh_perso.nombres, ' ' ,rrhh_perso.apellido_paterno,' ' ,rrhh_perso.apellido_materno)  AS nombre_completo")
        )
        ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'adm_aprobacion.id_usuario')
        ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
        ->where([['id_doc_aprob', $id_doc_aprob],['id_vobo', 3]])
        ->get();

        // if(isset($obs) && count($obs)>0){
        //     foreach ($obs as $key => $value) {                
        //         $data[]=[
        //             'id_aprobacion'=> $value->id_aprobacion, 
        //             'id_vobo'=> $value->id_vobo, 
        //             'id_usuario'=> $value->id_usuario, 
        //             'nombre_completo'=> $value->nombre_completo, 
        //             'descripcion'=>$value->detalle_observacion,
        //             'id_rol'=>$value->id_rol,
        //             'id_sustentacion'=>$value->id_sustentacion
        //         ];
        //         }
        // }

        return $obs;

    }

    public static function getHistorialAprobacion($idRequerimiento){
     
        $idDocumento = Documento::getIdDocAprob($idRequerimiento,1);

        $aprobaciones= Aprobacion::select(
            'adm_aprobacion.id_aprobacion',
            'adm_aprobacion.id_flujo',
            'adm_aprobacion.id_vobo',
            'adm_aprobacion.fecha_vobo',
            'adm_vobo.descripcion as accion',
            'adm_aprobacion.id_usuario',
            DB::raw("CONCAT(pers.nombres,' ',pers.apellido_paterno,' ',pers.apellido_materno) as nombre_usuario"),
            'sis_usua.nombre_corto',
            'adm_aprobacion.detalle_observacion',
            'adm_aprobacion.tiene_sustento',
            'adm_flujo.id_operacion',
            'adm_flujo.id_rol',
            'sis_rol.descripcion as descripcion_rol',
            'adm_flujo.nombre as nombre_flujo',
            'adm_flujo.orden'
        )
            ->leftJoin('administracion.adm_flujo', 'adm_aprobacion.id_flujo', '=', 'adm_flujo.id_flujo')
            ->leftJoin('administracion.adm_vobo', 'adm_aprobacion.id_vobo', '=', 'adm_vobo.id_vobo')
            ->leftJoin('configuracion.sis_usua', 'adm_aprobacion.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('configuracion.sis_rol', 'adm_flujo.id_rol', '=', 'sis_rol.id_rol')
            ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')
            ->leftJoin('administracion.adm_operacion', 'adm_flujo.id_operacion', '=', 'adm_operacion.id_operacion')
            ->where([
                ['id_doc_aprob', '=', $idDocumento]
            ])
            ->orderBy('adm_aprobacion.fecha_vobo', 'asc')
            ->get();

            return $aprobaciones;
        
    }
    public function usuario()
    {
        return $this->belongsTo('App\Models\Configuracion\Usuario', 'id_usuario', 'id_usuario');
    }
    public function VoBo()
    {
        return $this->belongsTo('App\Models\Administracion\VoBo', 'id_vobo', 'id_vobo');
    }
}
