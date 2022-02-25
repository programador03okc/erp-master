<?php

namespace App\Models\almacen\Catalogo;

use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'almacen.alm_subcat';
    public $timestamps = false;
    protected $primaryKey = 'id_subcategoria';

    /*public static function nextId(){
        //$cantidad = Marca::where('estado',1)->get()->count();
        $nextId = StringHelper::leftZero(3,$cantidad);
        return $nextId;
    }*/

    // public static function mostrarSubcategorias(){
    //     $data = SubCategoria::select('alm_subcat.id_subcategoria','alm_subcat.descripcion')
    //         ->where([['alm_subcat.estado', '=', 1]])
    //             ->orderBy('descripcion')
    //             ->get();
    //     return $data;
    // }
}
