<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    //
    protected $table = 'administracion.adm_estado_doc';

    protected $primaryKey = 'id_estado_doc';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_estado_doc'];

}
