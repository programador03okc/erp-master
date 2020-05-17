<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    //
    protected $table = 'contabilidad.cont_banco';
    //primary key
    protected $primaryKey = 'id_banco';
    //  public $incrementing = false;
    //Timesptamps
    public $timestamps = false;

    protected $fillable = [
        'id_contribuyente',
        'codigo',
        'estado',
        'fecha_registro'

    ];

    protected $guarded = ['id_banco'];

    public function contribuyente(){
        return $this->belongsTo('App\Models\Tesoreria\Contribuyente','id_contribuyente','id_contribuyente');
    }
}
