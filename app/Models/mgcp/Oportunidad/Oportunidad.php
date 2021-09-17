<?php

namespace App\Models\mgcp\Oportunidad;

use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Oportunidad extends Model
{
    //use HasFactory;
    protected $table = 'mgcp_oportunidades.oportunidades';
    protected $appends = ['ultimo_status', 'monto', 'dias_diferencia'];

    public static function crearCodigo()
    {
        $oportunidades = Oportunidad::select();
        if ($oportunidades->count() > 0) {
            $inicioMes = Carbon::now('America/Lima')->startOfMonth();
            $finMes = Carbon::now('America/Lima')->endOfMonth();
            $cantNegMes = $oportunidades
                ->where('created_at', '>=', $inicioMes)
                ->where('created_at', '<=', $finMes)
                ->count();

            return "OKC" . (Carbon::now('America/Lima')->year - 2000) . str_pad((Carbon::now('America/Lima')->month), 2, "0", STR_PAD_LEFT) . str_pad(($cantNegMes + 1), 3, "0", STR_PAD_LEFT);
        } else {
            return "OKC" . (Carbon::now('America/Lima')->year - 2000) . str_pad((Carbon::now('America/Lima')->month), 2, "0", STR_PAD_LEFT) . str_pad((0 + 1), 3, "0", STR_PAD_LEFT);
        }
    }

    public function getUltimoStatusAttribute()
    {
        $oportunidad = Status::where('id_oportunidad', $this->id)->orderBy('created_at', 'desc')->first();
        if (!is_null($oportunidad)) {
            return $oportunidad->detalle;
        } else {
            return '';
        }
    }

    public function totalPorTipo($tipo, $idUsuario)
    {
        if ($idUsuario == 0) {
            return Oportunidad::where('eliminado', 0)->where('tipo_negocio', $tipo)->count();
        } else {
            return Oportunidad::where('eliminado', 0)->where('id_responsable', $idUsuario)->where('tipo_negocio', $tipo)->count();
        }
    }

    public function totalPorTipoEstado($tipo, $estado, $idUsuario)
    {
        if ($idUsuario == 0) {
            return Oportunidad::where('eliminado', 0)->where('tipo_negocio', $tipo)->where('cod_estado', $estado)->count();
        } else {
            return Oportunidad::where('eliminado', 0)->where('id_responsable', $idUsuario)->where('cod_estado', $estado)->where('tipo_negocio', $tipo)->count();
        }
    }

    public function getMontoAttribute()
    {
        return ($this->moneda == 's' ? 'S/' : '$') . number_format($this->importe, 2, ".", ",");
    }

    public function setFechaLimiteAttribute($valor)
    {
        $this->attributes['fecha_limite'] = Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }

    public function getDiasDiferenciaAttribute()
    {
        $fecha_limite = date_create($this->fecha_limite);
        $hoy = date_create(date('Y-m-d'));
        return date_diff($hoy, $fecha_limite)->format('%r%a');
    }

    public function getFechaLimiteAttribute()
    {
        return date_format(date_create($this->attributes['fecha_limite']), 'd-m-Y');
    }

    public function getCreatedAtAttribute()
    {
        return date_format(date_create($this->attributes['created_at']), 'd-m-Y');
    }

    public function tipoNegocio()
    {
        return $this->belongsTo(TipoNegocio::class, 'id_tipo_negocio');
    }

    public function ordenCompraPropia()
    {
        return $this->hasOne(OrdenCompraPropiaView::class, 'id_oportunidad');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'id_entidad');
    }

    public function status()
    {
        return $this->hasMany(Status::class, 'id_oportunidad');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'id_oportunidad');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_oportunidad');
    }

    public function notificar()
    {
        return $this->hasMany(Notificar::class, 'id_oportunidad');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'id_responsable');
    }

    public function cuadroCosto()
    {
        return $this->hasOne(CuadroCosto::class, 'id_oportunidad', 'id');
    }
}
