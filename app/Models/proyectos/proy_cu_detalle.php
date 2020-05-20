<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_cu_detalle extends Model
{
    protected $table = 'proy_cu_detalle';

    protected $primaryKey ='id_cu_detalle';
    
    public $timestamps=false;

    protected $fillable = [
        'id_cu_detalle',
        'id_cu',
        'id_insumo',
        'unid_medida',
        'cantidad',
        'cuadrilla',
        'precio_unit',
        'precio_total',
        'estado',
        'fecha_registro'
    ];
    protected $guarded = ['id_cu_detalle'];
}