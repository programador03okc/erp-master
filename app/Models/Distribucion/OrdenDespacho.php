<?php

namespace App\Models\Distribucion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrdenDespacho extends Model
{
    protected $table = 'almacen.orden_despacho';
    protected $primaryKey = 'id_od';
    public $timestamps = false;

    // public function requerimiento()
    // {
    //     return $this->hasOne('App\Models\almacen\Requerimiento', 'id_requerimiento', 'id_requerimiento');
    // }

    public static function ODnextId($id_almacen, $aplica_cambios, $id, $fecha)
    {
        $yyyy = date('Y', strtotime($fecha));
        $yy = date('y', strtotime($fecha));

        $cantidad = OrdenDespacho::whereYear('fecha_despacho', '=', $yyyy)
            ->where([
                ['id_almacen', '=', $id_almacen],
                ['aplica_cambios', '=', $aplica_cambios],
                // ['estado', '!=', 7],
                // ['id_od', '<=', $id],
            ])
            ->get()->count();

        $almacen = DB::table('almacen.alm_almacen')
            ->select('codigo')
            ->where('id_almacen', $id_almacen)
            ->first();

        $val = sprintf('%04d', $cantidad + 1);
        $nextId = "OD" . ($aplica_cambios ? "I-" : "E-") . $almacen->codigo . '-' . $yy . $val;
        return $nextId;
    }
}
