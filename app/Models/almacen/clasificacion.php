<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class clasificacion extends Model
{
    protected $table = 'alm_clasif';

    protected $primaryKey = 'id_clasificacion';

    public $timestamps = false;

    protected $fillable = [
        'id_clasificacion',
        'descripcion',
        'estado',
        'fecha_registro'
    ];
    protected $guarded = ['id_clasificacion'];
}