<?php

namespace App\Models\Logistica;

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
        'id_contribuyente',
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
        return $this->hasOne('App\Models\Logistica\Empresa', 'id_empresa');
    }

    public function proveedor()
    {
        return $this->hasOne('App\Models\Logistica\Proveedor', 'id_proveedor');
    }


}
