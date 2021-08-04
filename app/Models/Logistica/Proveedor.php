<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'logistica.log_prove';
    protected $primaryKey = 'id_proveedor';
    public $timestamps = false;
    protected $guarded = ['id_proveedor'];

    public static function mostrarCuentasProveedor($idProveedor)
    {

        $data = Proveedor::with('contribuyente','cuentaContribuyente.banco','cuentaContribuyente.banco.contribuyente','cuentaContribuyente.tipoCuenta','cuentaContribuyente.moneda')
        ->where('log_prove.id_proveedor', '=', $idProveedor);
        return $data;
    }


    public function contribuyente(){
        return $this->belongsTo('App\Models\Contabilidad\Contribuyente','id_contribuyente','id_contribuyente');
    }
    public function cuentaContribuyente(){
        return $this->belongsTo('App\Models\Contabilidad\CuentaContribuyente','id_contribuyente','id_contribuyente');
    }
}
