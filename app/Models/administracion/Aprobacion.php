<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

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
                'adm_vobo.descripcion',
                'adm_aprobacion.id_usuario',
                'sis_usua.nombre_corto',
                'adm_aprobacion.detalle_observacion',
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
                ->leftJoin('administracion.adm_operacion', 'adm_flujo.id_operacion', '=', 'adm_operacion.id_operacion')
                ->where([
                    ['id_doc_aprob', '=', $id_doc_aprobacion],
                    ['adm_flujo.estado', '=', 1]
                ])
                ->orderBy('adm_flujo.orden', 'asc')
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

    public static function getObservaciones($id_doc_aprob)
    {
        $obs =  Aprobacion::where([['id_doc_aprob', $id_doc_aprob], ['id_vobo', 3]])
            ->get();
        return $obs;
    }



    
}
