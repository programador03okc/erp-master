<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class tipo_producto extends Model
{
    protected $table = 'alm_tp_prod';

    protected $primaryKey = 'id_tipo_producto';

    public $timestamps = false;

    protected $fillable = [
        'id_tipo_producto',
        'descripcion',
        'estado'
    ];
    protected $guarded = ['id_tipo_producto'];
}