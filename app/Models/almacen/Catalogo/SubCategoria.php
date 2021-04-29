<?php

namespace App\Models\Almacen\Catalogo;

use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Model;

class SubCategoria extends Model
{
    protected $table='almacen.alm_subcat';
    public $timestamps=false;
    protected $primaryKey='id_subcategoria';
    
    public static function nextId(){
        $cantidad = SubCategoria::where('estado',1)->get()->count();
        $nextId = StringHelper::leftZero(3,$cantidad);
        return $nextId;
    }

    public static function mostrarSubcategorias(){
        $data = SubCategoria::select('alm_subcat.id_subcategoria','alm_subcat.descripcion')
            ->where([['alm_subcat.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
}
