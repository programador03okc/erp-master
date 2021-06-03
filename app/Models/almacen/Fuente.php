<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fuente extends Model
{
    protected $table = 'almacen.fuente';
    protected $primaryKey = 'id_fuente';
    public $timestamps = false;

    public static function mostrar()
    {
        $data =Fuente::select('fuente.*')
        ->where('fuente.estado', 1)
        ->orderBy('fuente.id_fuente', 'asc')
        ->get();

        return $data;
    }
}
