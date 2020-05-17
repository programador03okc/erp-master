<?php

namespace App\Models\proyectos;

use Illuminate\Database\Eloquent\Model;

class proy_iu extends Model
{
    protected $table = 'proy_iu';

    protected $primaryKey ='id_iu';
    
    public $timestamps=false;

    protected $fillable = [
        'id_iu',
        'codigo',
        'descripcion',
        'estado',
        'fecha_registro'
    ];

    protected $guarded = ['id_iu'];
}