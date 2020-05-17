<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Contribuyente extends Model
{
    // table name
    protected $table = 'contabilidad.adm_contri';
    //primary key
    protected $primaryKey = 'id_contribuyente';
    //  public $incrementing = false;
    //Timesptamps
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_contribuyente',
        'id_doc_identidad',
        'razon_social',
        'telefono',
        'celular',
        'direccion_fiscal',
        'ubigeo',
        'id_pais',
        'estado',
        'fecha_registro'

    ];

    public function empresa()
    {
        return $this->hasOne('App\Models\Tesoreria\Empresa', 'id_empresa');
    }

    public function proveedor()
    {
        return $this->hasOne('App\Models\Tesoreria\Proveedor', 'id_proveedor');
    }


    //    public function tipocontribuyente()
    //    {
    //        return $this->hasOne('App\Models\administracion\tipo_contribuyente','id_tipo_contribuyente','id_tipo_contribuyente');

    //    }
    //    public function contribuyentecontacto()
    //    {
    //        return $this->hasMany('App\Models\administracion\contribuyente_contacto','id_contribuyente','id_contribuyente');

    //    }
    //    public function rubro()
    //    {
    //        return $this->hasOne('App\Models\administracion\contribuyente_rubro','id_contribuyente','id_contribuyente');

    //    }

    //    public function cuenta()
    //    {
    //        return $this->hasOne('App\Models\administracion\cuenta_contribuyente','id_contribuyente','id_contribuyente');

    //    }
}
