<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table='configuracion.ubi_dpto';
    public $timestamps=false;
    protected $primaryKey='id_dpto';

    
    public static function getIdDepartamento($nombre){
        $data = Departamento::select('ubi_dpto.*')
        ->where([
            ['ubi_dpto.descripcion', '=', $nombre]
            ])
        ->first();
        return ($data!==null ? $data->id_dpto : 0);
    }
}
