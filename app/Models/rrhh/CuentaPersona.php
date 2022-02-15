<?php


namespace App\Models\Rrhh;
use Illuminate\Database\Eloquent\Model;

class CuentaPersona extends Model {
    protected $connection = 'pgsql_rrhh'; // *conexión con okcomput_rrhh  
    protected $table = 'rrhh.rrhh_cta_banc';
    protected $primaryKey = 'id_cuenta_bancaria';
    public $timestamps = false;

    public function banco(){
        return $this->hasOne('App\Models\Contabilidad\Banco','id_banco','id_banco');
    }
    public function tipoCuenta(){
        return $this->hasOne('App\Models\Contabilidad\TipoCuenta','id_tipo_cuenta','id_tipo_cuenta');
    }
    public function moneda(){
        return $this->hasOne('App\Models\Configuracion\Moneda','id_moneda','id_moneda');
    }
}
