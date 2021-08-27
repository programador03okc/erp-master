<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'almacen.alm_reserva';
    protected $primaryKey = 'id_reserva';
    public $timestamps = false;

    public static function nextCodigo($id_almacen)
    {
        $yyyy = date('Y', strtotime(date('Y-m-d H:i:s')));
        $anio = date('y', strtotime(date('Y-m-d H:i:s')));

        $cantidad = Reserva::where('id_almacen_reserva', $id_almacen)
            ->whereYear('fecha_registro', '=', $yyyy)
            ->get()->count();
        // $val = GenericoAlmacenController::leftZero(4, ($cantidad + 1));
        $val = sprintf('%04d', ($cantidad + 1));

        return "RE-" . $id_almacen . "-" . $anio . $val;
    }
}
