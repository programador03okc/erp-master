<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_cu extends Model
{
    protected $table = 'proy_cu';

    protected $primaryKey ='id_cu';
    
    public $timestamps=false;

    protected $fillable = [
        'id_cu',
        'codigo',
        'descripcion',
        'unid_medida',
        'total',
        'rendimiento',
        'observacion',
        'estado',
        'fecha_registro'
    ];
    protected $guarded = ['id_cu'];
}