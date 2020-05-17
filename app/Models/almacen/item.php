<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class item extends Model
{
    protected $table = 'alm_item';

    protected $primaryKey = 'id_item';

    public $timestamps = false;

    protected $fillable = [
        'id_item',
        'id_producto',
        'id_servicio',
        'codigo',
        'estado',
        'fecha_registro'
    ];
    protected $guarded = ['id_item'];
}