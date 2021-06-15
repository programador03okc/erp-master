<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class Operacion extends Model
{
    protected $table = 'administracion.adm_operacion';
    protected $primaryKey = 'id_operacion';
    public $timestamps = false;

    public static function getOperacion($tipo_documento, $grupo, $prioridad)
    {

        $data_tipo_documento = TipoDocumento::getIdTipoDocumentp($tipo_documento);
        if ($data_tipo_documento['status'] == 200) {

            $adm_operacion = Operacion::where([
                ['id_tp_documento', '=', $data_tipo_documento['data']],
                // ['id_prioridad', '=', $prioridad ], 
                ['id_grupo', '=', $grupo],
                ['estado', '=', 1]
            ])
                ->get();
            $status = 200;
        } else {
            $adm_operacion = [];
            $status = 400;
        }
        $output = ['data' => $adm_operacion, 'status' => $status];

        return $output;
    }
}
