<?php

namespace App\Models\Proyectos;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyectos.proy_proyecto';
    protected $primaryKey ='id_proyecto';    
    public $timestamps=false;


    public static function mostrar()
    {
        $data = Proyecto::select(
                'proy_proyecto.*'
            )
            ->where('estado','!=',7)
            ->orderBy('proy_proyecto.descripcion', 'asc')
            ->get();
        return $data;
    }
}