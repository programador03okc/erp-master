<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_insumo extends Model
{
    protected $table = 'proy_insumo';

    protected $primaryKey ='id_insumo';
    
    public $timestamps=false;

    protected $fillable = [
        'id_insumo',
        'codigo',
        'descripcion',
        'tp_insumo',
        'unid_medida',
        'precio',
        'peso_unitario',
        'iu',
        'fecha_registro',
        'estado'
    ];
    protected $guarded = ['id_insumo'];
}