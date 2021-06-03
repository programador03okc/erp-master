<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CategoriaAdjunto extends Model
{
    protected $table = 'almacen.categoria_adjunto';
    protected $primaryKey = 'id_categoria_adjunto';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = DB::table('almacen.categoria_adjunto')
        ->select('categoria_adjunto.*')
        ->where('categoria_adjunto.estado', 1) // el usuario pertenece a un solo grupo
        ->get();

        return $data;
    }
}
