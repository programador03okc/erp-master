<?php

namespace App\Models\Distribucion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrdenDespacho extends Model
{
    protected $table = 'almacen.orden_despacho';
    protected $primaryKey = 'id_od';
    public $timestamps = false;

    // public function requerimiento()
    // {
    //     return $this->hasOne('App\Models\almacen\Requerimiento', 'id_requerimiento', 'id_requerimiento');
    // }

    public static function ODnextId($id_almacen, $aplica_cambios, $id)
    {
        //$yyyy = date('Y', strtotime(new Carbon()));
        //$yy = date('y', strtotime(new Carbon()));

        $cantidad = OrdenDespacho::whereYear('fecha_despacho', '=', Carbon::now()->format('Y'))
            ->where([
                // ['id_almacen', '=', $id_almacen],
                ['aplica_cambios', '=', $aplica_cambios],
                ['estado', '!=', 7],
                // ['id_od', '<=', $id],
            ])
            ->get()->count();

        $val = sprintf('%04d', $cantidad + 1);
        $nextId = "OD" . ($aplica_cambios ? "-I-" : "-E-") . Carbon::now()->format('y') . $val;
        return $nextId;
    }
}
