<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;


class Empresa extends Model
{
    protected $table = 'administracion.adm_empresa';
    //primary key
    protected $primaryKey = 'id_empresa';
    //  public $incrementing = false;
    //Timesptamps
    public $timestamps = false;

    protected $fillable = [
        'id_contribuyente',
        'codigo',
        'estado',
        'fecha_registro'

    ];

    protected $guarded = ['id_sede'];

    public function contribuyente(){
        return $this->belongsTo('App\Models\Tesoreria\Contribuyente','id_contribuyente','id_contribuyente');
    }

    public function sedes(){
        return $this->hasMany(Sede::class, 'id_empresa');
	}

}
