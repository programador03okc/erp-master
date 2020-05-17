<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    //
    protected $table = 'logistica.log_prove';
    //primary key
    protected $primaryKey = 'id_proveedor';
    //  public $incrementing = false;
    //Timesptamps
    public $timestamps = false;

    protected $fillable = [
        'id_contribuyente',
        'codigo',
        'estado',
        'fecha_registro'

    ];

    protected $guarded = ['id_proveedor'];

    public function contribuyente(){
        return $this->belongsTo('App\Models\Tesoreria\Contribuyente','id_contribuyente','id_contribuyente');
    }
}
