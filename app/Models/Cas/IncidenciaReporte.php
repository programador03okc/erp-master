<?php

namespace App\Models\Cas;

use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Model;

class IncidenciaReporte extends Model
{
    protected $table = 'cas.incidencia_reporte';
    public $timestamps = false;
    protected $primaryKey = 'id_incidencia_reporte';

    public function incidencia()
    {
        return $this->hasOne(Incidencia::class, 'id_incidencia');
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_usuario');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado');
    }

    public static function nuevoCodigoFicha($id_incidencia)
    {
        $yy = date('y', strtotime("now"));
        $num = IncidenciaReporte::where('id_incidencia', $id_incidencia)->count();
        $correlativo = StringHelper::leftZero(4, (intval($num) + 1));

        return 'R' . $yy . $correlativo;
    }
}
