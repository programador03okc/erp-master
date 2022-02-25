<?php

namespace App\Models\Cas;

use App\Helpers\StringHelper;
use App\Models\Administracion\Empresa;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $table = 'almacen.incidencia';
    public $timestamps = false;
    protected $primaryKey = 'id_incidencia';

    public static function nuevoCodigoIncidencia($id_empresa)
    {
        $yyyy = date('Y', strtotime("now"));
        $yy = date('y', strtotime("now"));

        $empresa = Empresa::findById($id_empresa);

        $num = Incidencia::where([
            ['id_empresa', '=', $id_empresa],
            ['anio', '=', $yyyy]
        ])->count();

        $correlativo = StringHelper::leftZero(4, ($num + 1));

        return 'INC-' . $empresa->codigo . '-' . $yy . $correlativo;
    }
}
