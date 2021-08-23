<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table='configuracion.ubi_prov';
    public $timestamps=false;
    protected $primaryKey='id_prov';

    
    public static function getIdProvincia($nombre){
        $data = Provincia::select('id_prov.*')
        ->where([
            ['id_prov.descripcion', '=', $nombre]
            ])
        ->first();
        return ($data!==null ? $data->id_prov : 0);
    }
    public function departamento()
    {
        return $this->hasOne('App\Models\Configuracion\Departamento','id_dpto','id_dpto')->withDefault([
            'id_dpto' => null,
            'descripcion' => null,
            'estado' => null
        ]);
    }
    
}
