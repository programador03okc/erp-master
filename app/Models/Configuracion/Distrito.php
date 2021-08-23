<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table='configuracion.ubi_dis';
    public $timestamps=false;
    protected $primaryKey='id_dis';
    
    public static function getIdDistrito($nombre){
        $data = Distrito::select('ubi_dis.*')
        ->where([
            ['ubi_dis.descripcion', '=', $nombre]
            ])
        ->first();
        return ($data!==null ? $data->id_dis : 0);
    }

    public function provincia()
    {
        return $this->hasOne('App\Models\Configuracion\Provincia','id_prov','id_prov')->withDefault([
            'id_prov' => null,
            'descripcion' => null,
            'estado' => null
        ]);
    }
}
