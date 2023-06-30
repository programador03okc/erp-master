<?php

namespace App\Models\Logistica;

use App\Models\Configuracion\Distrito;
use App\Models\Configuracion\Pais;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\ContactoContribuyente;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Contabilidad\CuentaContribuyente;
use App\Models\Contabilidad\TipoContribuyente;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\Contabilidad\TipoDocumentoIdentidad;
use App\Models\sistema\moneda;
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
        'establecimientoProveedor',
        // 'contribuyente.distrito.provincia',
        // 'contribuyente.distrito.provincia.departamento',
        'estadoProveedor')->whereHas('contribuyente', function($q){
            $q->where('estado', '=', 1);
        })
        ->where('log_prove.estado','=',1);
        return $data;

    }
    public static function mostrar($idProveedor){
        // $data = Proveedor::with(['contribuyente.tipoContribuyente',
        // 'contribuyente.tipoDocumentoIdentidad',
        // 'cuentaContribuyente'=> function($q){
        //     $q->where('adm_cta_contri.estado', '=', 1);
        // },
        // 'cuentaContribuyente.banco',
        // 'cuentaContribuyente.banco.contribuyente',
        // 'cuentaContribuyente.tipoCuenta',
        // 'cuentaContribuyente.moneda',
        // 'contribuyente.pais',
        // 'contribuyente.distrito',
        // 'contactoContribuyente' => function($q){
        //     $q->where('adm_ctb_contac.estado', '=', 1);
        // },
        // // 'contribuyente.distrito.provincia',
        // // 'contribuyente.distrito.provincia.departamento',
        // 'establecimientoProveedor' => function($q){
        //     $q->where('establecimiento_proveedor.estado', '=', 1);
        // },
        // 'establecimientoProveedor.estadoEstablecimiento',
        // 'estadoProveedor'
        // ])->whereHas('contribuyente', function($q){
        //     $q->where('estado', '=', 1);
        // })->where('id_proveedor',$idProveedor)->first();
        // // ->where('log_prove.id_contribuyente','=',1912);
        // return $data;

        $data = Contribuyente::with(['tipoContribuyente',
        'proveedor',
        'tipoDocumentoIdentidad',
        'cuentaContribuyente'=> function($q){
            $q->where('adm_cta_contri.estado', '=', 1);
        },
        'cuentaContribuyente.banco',
        'cuentaContribuyente.banco.contribuyente',
        'cuentaContribuyente.tipoCuenta',
        'cuentaContribuyente.moneda',
        'cuentaContribuyente.usuario',
        'pais',
        'distrito',
        'contactoContribuyente' => function($q){
            $q->where('adm_ctb_contac.estado', '=', 1);
        },
        'proveedor.establecimientoProveedor' => function($q){
            $q->where('establecimiento_proveedor.estado', '=', 1);
        },
        'proveedor.establecimientoProveedor.estadoEstablecimiento',
        'proveedor.estadoProveedor'
        ])->whereHas('proveedor', function($q) use($idProveedor){
            $q->where('id_proveedor', '=', $idProveedor);
        })->first();
        // ->where('log_prove.id_contribuyente','=',1912);
        return $data;

    }

    public static function mostrarCuentasProveedor($idProveedor)
    {

 
        // $data = Proveedor::with(['contribuyente','cuentaContribuyente.banco.contribuyente','cuentaContribuyente.tipoCuenta','cuentaContribuyente.moneda','cuentaContribuyente' => function($q){
        $data = Proveedor::with(['contribuyente','cuentaContribuyente' => function($q){
            $q->where('estado', '!=', 7);
        },'cuentaContribuyente.banco.contribuyente','cuentaContribuyente.tipoCuenta','cuentaContribuyente.moneda'])
        // ->whereHas('cuentaContribuyente', function ($q) {
        //     $q->where('estado', '!=',7);
        // })
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
    public function establecimientoProveedor(){
        return $this->hasMany('App\Models\Logistica\EstablecimientoProveedor','id_proveedor','id_proveedor');
    }
    public function estadoProveedor(){
        return $this->hasOne('App\Models\Logistica\EstadoProveedor','id_estado','estado')->withDefault([
            'id_estado' => null,
            'descripcion' => null,
            'estado' => null
        ]);
    }
}
