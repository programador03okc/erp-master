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
    protected $table = 'cas.incidencia';
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

    public function modo()
    {
        return $this->belongsTo(ModoIncidencia::class, 'id_modo');
    }

    public function atiende()
    {
        return $this->belongsTo(AtiendeIncidencia::class, 'id_atiende');
    }

    public function tipoGarantia()
    {
        return $this->belongsTo(TipoGarantia::class, 'id_tipo_garantia');
    }

    public function estado()
    {
        return $this->belongsTo(IncidenciaEstado::class, 'estado', 'id_estado');
    }

    public static function nuevoCodigoIncidencia($id_empresa, $fecha)
    {
        $yy = date('y', strtotime($fecha));
        $yyyy = date('Y',  strtotime($fecha));

        $empresa = Empresa::find($id_empresa);

        $num = Incidencia::where([
            ['id_empresa', '=', $id_empresa],
            ['anio', '=', $yyyy]
        ])->count();

        $correlativo = StringHelper::leftZero(4, ($num + 1));

        return 'INC-' . $empresa->codigo . '-' . $yy . $correlativo;
    }
}
