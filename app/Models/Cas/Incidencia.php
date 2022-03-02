<?php

namespace App\Models\Cas;

use App\Helpers\StringHelper;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Estado;
use App\Models\Configuracion\Usuario;
use App\Models\Contabilidad\ContactoContribuyente;
use App\Models\Contabilidad\Contribuyente;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $table = 'almacen.incidencia';
    public $timestamps = false;
    protected $primaryKey = 'id_incidencia';

    public function contribuyente()
    {
        return $this->hasOne(Contribuyente::class, 'id_contribuyente', 'id_contribuyente');
    }

    public function contacto()
    {
        return $this->hasOne(ContactoContribuyente::class, 'id_datos_contacto', 'id_contacto');
    }

    public function responsable()
    {
        return $this->hasOne(Usuario::class, 'id_usuario', 'id_responsable');
    }

    public function tipoFalla()
    {
        return $this->belongsTo(TipoFalla::class, 'id_tipo_falla');
    }

    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class, 'id_tipo_servicio');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado');
    }

    public static function nuevoCodigoIncidencia($id_empresa, $yyyy)
    {
        $yy = date('y', strtotime("now"));

        $empresa = Empresa::find($id_empresa);

        $num = Incidencia::where([
            ['id_empresa', '=', $id_empresa],
            ['anio', '=', $yyyy]
        ])->count();

        $correlativo = StringHelper::leftZero(4, ($num++));

        return 'INC-' . $empresa->codigo . '-' . $yy . $correlativo;
    }
}
