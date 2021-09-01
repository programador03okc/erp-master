<?php

namespace App\Models\Logistica;

use App\Models\Configuracion\Distrito;
use Illuminate\Database\Eloquent\Model;

class EstablecimientoProveedor extends Model
{
    protected $table = 'logistica.establecimiento_proveedor';
    protected $primaryKey = 'id_establecimiento';
    public $timestamps = false;
    protected $appends = ['ubigeo_completo'];

    public function getUbigeoCompletoAttribute(){
        $dis= $this->attributes['ubigeo'];
        if($dis>0){
            $ubigeo=Distrito::with('provincia.departamento')->where('id_dis',$dis)->first();
            $dist= $ubigeo->descripcion;
            $prov= $ubigeo->provincia->descripcion;
            $dpto= $ubigeo->provincia->departamento->descripcion;
            return ($dist.' - '.$prov.' - '.$dpto);
        }else{
            return '';
        }

    }
    public function estadoEstablecimiento(){
        return $this->hasOne('App\Models\Logistica\EstadoProveedor','id_estado','estado')->withDefault([
            'id_estado' => null,
            'descripcion' => null,
            'estado' => null
        ]);
    }
}