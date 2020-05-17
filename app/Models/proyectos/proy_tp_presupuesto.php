<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_tp_presupuesto extends Model
{
    protected $table = 'proy_tp_pres';

    protected $primaryKey ='id_tp_pres';
    
    public $timestamps=false;

    protected $fillable = [
        'id_tp_pres',
        'descripcion',
        'estado',
        'fecha_registro'
    ];

    protected $guarded = ['id_tp_pres'];
}