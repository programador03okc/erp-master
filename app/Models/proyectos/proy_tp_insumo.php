<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_tp_insumo extends Model
{
    protected $table = 'proy_tp_insumo';

    protected $primaryKey ='id_tp_insumo';
    
    public $timestamps=false;

    protected $fillable = [
        'id_tp_insumo',
        'codigo',
        'descripcion',
        'fecha_registro'
    ];

    protected $guarded = ['id_tp_insumo'];
}