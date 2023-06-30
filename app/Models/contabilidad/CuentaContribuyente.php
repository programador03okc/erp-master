<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CuentaContribuyente extends Model
{
    protected $table = 'contabilidad.adm_cta_contri';
    protected $primaryKey = 'id_cuenta_contribuyente';
    public $timestamps = false;

    public static function mostrarCuentasProveedor($idContribuyente)
    {
        $data = CuentaContribuyente::with('banco','banco.contribuyente','tipoCuenta','moneda')->where('adm_cta_contri.id_contribuyente', '=', $idContribuyente);
        return $data;
    }


    public function banco(){
        return $this->hasOne('App\Models\Contabilidad\Banco','id_banco','id_banco');
    }
    public function tipoCuenta(){
        return $this->hasOne('App\Models\Contabilidad\TipoCuenta','id_tipo_cuenta','id_tipo_cuenta');
    }
    public function moneda(){
        return $this->hasOne('App\Models\Configuracion\Moneda','id_moneda','id_moneda');
    }
    public function usuario(){
        return $this->hasOne('App\Models\Configuracion\Usuario','id_usuario','id_usuario');
    }
}

