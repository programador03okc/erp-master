<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'logistica.log_prove';
    protected $primaryKey = 'id_proveedor';
    public $timestamps = false;
    protected $guarded = ['id_proveedor'];


    public static function listado(){
        $data = Proveedor::with('contribuyente.tipoContribuyente',
        'contribuyente.tipoDocumentoIdentidad',
        'cuentaContribuyente.banco',
        'cuentaContribuyente.banco.contribuyente',
        'cuentaContribuyente.tipoCuenta',
        'cuentaContribuyente.moneda',
        'contribuyente.pais',
        'contribuyente.distrito',
        // 'contribuyente.distrito.provincia',
        // 'contribuyente.distrito.provincia.departamento',
        'estadoProveedor');
        // ->where('log_prove.id_contribuyente','=',1912);
        return $data;

    }
    public static function mostrar($idProveedor){
        $data = Proveedor::with('contribuyente.tipoContribuyente',
        'contribuyente.tipoDocumentoIdentidad',
        'cuentaContribuyente.banco',
        'cuentaContribuyente.banco.contribuyente',
        'cuentaContribuyente.tipoCuenta',
        'cuentaContribuyente.moneda',
        'contribuyente.pais',
        'contribuyente.distrito',
        'contactoContribuyente',
        // 'contribuyente.distrito.provincia',
        // 'contribuyente.distrito.provincia.departamento',
        'estadoProveedor')->where('id_proveedor',$idProveedor)->first();
        // ->where('log_prove.id_contribuyente','=',1912);
        return $data;

    }

    public static function mostrarCuentasProveedor($idProveedor)
    {

        $data = Proveedor::with('contribuyente','cuentaContribuyente.banco','cuentaContribuyente.banco.contribuyente','cuentaContribuyente.tipoCuenta','cuentaContribuyente.moneda')
        ->where('log_prove.id_proveedor', '=', $idProveedor);
        return $data;
    }



    public function contribuyente(){
        return $this->hasOne('App\Models\Contabilidad\Contribuyente','id_contribuyente','id_contribuyente');
    }
    public function cuentaContribuyente(){
        return $this->hasMany('App\Models\Contabilidad\CuentaContribuyente','id_contribuyente','id_contribuyente');
    }
    public function contactoContribuyente(){
        return $this->hasMany('App\Models\Contabilidad\ContactoContribuyente','id_contribuyente','id_contribuyente');
    }
    public function estadoProveedor(){
        return $this->hasOne('App\Models\Logistica\EstadoProveedor','id_estado','estado')->withDefault([
            'id_estado' => null,
            'descripcion' => null,
            'estado' => null
        ]);
    }
}
