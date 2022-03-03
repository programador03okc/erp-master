<?php


namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model {

    protected $table = 'configuracion.sis_grupo';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;


    public static function mostrar()
    {
        $data = Grupo::select(
                'sis_grupo.*'
            )
            ->where('id_estado','!=',7)
            ->orderBy('sis_grupo.descripcion', 'asc')
            ->get();
        return $data;
    }
}
