<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Flujo extends Model
{
    protected $table = 'administracion.adm_flujo';
    protected $primaryKey = 'id_flujo';
    public $timestamps = false;

    public static function getIdFlujo($id_operacion)
    {
        if ($id_operacion > 0) {

            $adm_flujo = DB::table('administracion.adm_flujo')
                ->select(
                    'adm_flujo.*',
                    'adm_flujo.nombre as nombre_flujo',
                    'sis_rol.descripcion as descripcion_rol'
                )
                ->leftJoin('configuracion.sis_rol', 'adm_flujo.id_rol', '=', 'sis_rol.id_rol')
                ->where([
                    ['adm_flujo.id_operacion', '=', $id_operacion],
                    ['adm_flujo.estado', '=', 1]
                ])
                ->orderBy('adm_flujo.orden', 'asc')
                ->get();

            if (isset($adm_flujo) && count($adm_flujo) > 0) {
                $status = 200;
                $message = 'OK';
                foreach ($adm_flujo as $element) {
                    $flujo_list[] = $element;
                }
            } else {
                $flujo_list = [];
                $status = 204;
                $message = 'No Content, data vacia';
            }
        } else {
            $flujo_list = [];
            $status = 400;
            $message = 'Bad Request, necesita un parametro';
        }
        $output = ['data' => $flujo_list, 'status' => $status, 'message' => $message];

        return $output;
    }
}
