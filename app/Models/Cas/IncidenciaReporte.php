<?php

namespace App\Models\Cas;

use App\Helpers\StringHelper;
use App\Models\Configuracion\Usuario;
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
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function adjuntos()
    {
        return $this->hasMany(IncidenciaReporteAdjunto::class, 'id_incidencia_reporte');
    }

    public static function nuevoCodigoFicha($id_incidencia)
    {
        $yy = date('y', strtotime("now"));
        $num = IncidenciaReporte::where('id_incidencia', $id_incidencia)->count();
        $correlativo = StringHelper::leftZero(4, (intval($num) + 1));

        return 'FR-' . $yy . $correlativo;
    }
}
