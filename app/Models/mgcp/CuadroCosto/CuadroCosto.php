<?php

namespace App\Models\mgcp\CuadroCosto;

use Carbon\Carbon;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\CuadroCosto\Responsable;
use App\Models\mgcp\CuadroCosto\EstadoAprobacion;
use App\Helpers\mgcp\CuadroCosto\CuadroCostoHelper;
use App\Models\Presupuestos\CentroCostoNivelView;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CuadroCosto extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc';
    protected $appends = ['fecha_creacion_format']; //['margen_ganancia', 'monto_ganancia', 'fecha_creacion_format', 'tiene_transformacion'];
    public $timestamps = false;

    /*public static function listar($request)
    {
        $data = CuadroCosto::with('oportunidad')->join('mgcp_oportunidades.oportunidades', 'id_oportunidad', '=', 'oportunidades.id')
            ->join('mgcp_usuarios.users', 'oportunidades.id_responsable', '=', 'users.id')
            ->join('mgcp_acuerdo_marco.entidades', 'oportunidades.id_entidad', '=', 'entidades.id')
            ->join('mgcp_cuadro_costos.estados_aprobacion', 'estado_aprobacion', '=', 'estados_aprobacion.id');

        $filtros['eliminado'] = false;
        if ($request->session()->has('cc_estado')) {
            $filtros['estado_aprobacion'] = session('cc_estado');
        }
        if ($request->session()->has('cc_tipo')) {
            $filtros['tipo_cuadro'] = session('cc_tipo');
        }
        if ($request->session()->has('cc_responsable')) {
            $filtros['id_responsable'] = session('cc_responsable');
        }
        return $data->where($filtros)->select([
            'cc.id', 'cc.moneda AS moneda', 'tipo_cambio', 'id_oportunidad', 'cc.fecha_creacion',
            'codigo_oportunidad', 'oportunidad AS descripcion_oportunidad', 'fecha_limite', 'nombre AS nombre_entidad', 'name', 'tipo_cuadro',
            'estados_aprobacion.estado',DB::raw('10')
        ]);
    }*/

    public function getFechaEntregaAttribute()
    {
        return date_format(date_create($this->attributes['fecha_entrega']), 'd-m-Y');
    }

    public function getFechaCreacionFormatAttribute()
    {
        return $this->attributes['fecha_creacion'] == null ? '' : (new Carbon($this->attributes['fecha_creacion']))->format('d-m-Y');
    }

    public function setFechaEntregaAttribute($valor)
    {
        $this->attributes['fecha_entrega'] = Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }

    public function setMejorFechaEntregaAttribute($valor)
    {
        $this->attributes['mejor_fecha_entrega'] = Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }

    public function oportunidad()
    {
        return $this->belongsTo(Oportunidad::class, 'id_oportunidad');
    }

    /*public function detalleProceso()
    {
        return $this->belongsTo(Detalleproceso::class, 'id_detalle_proceso');
    }*/

    public function getCondicionCreditoFormatAttribute()
    {
        switch ($this->attributes['id_condicion_credito']) {
            case 0:
                return 'No seleccionado';
                break;
            case 1:
                return $this->condicionCredito->descripcion;
                break;
            case 2:
                return $this->condicionCredito->descripcion . ' ' . $this->dato_credito . ' ' . 'días';
                break;
            case 3:
                return $this->dato_credito . ' ' . $this->condicionCredito->descripcion;
                break;
            default:
                return 'Opción desconocida';
                break;
        }
    }

    public function estado()
    {
        return $this->belongsTo(EstadoAprobacion::class, 'estado_aprobacion');
    }

    public function condicionCredito()
    {
        return $this->belongsTo(CondicionCredito::class, 'id_condicion_credito');
    }

    public function getMargenGananciaAttribute()
    {
        $data = CuadroCostoHelper::obtenerDetallesFilas($this->attributes['id']);
        return $data->margen_ganancia_format;
    }

    public function getFleteTotalAttribute()
    {
        $data = CuadroCostoHelper::obtenerDetallesFilas($this->attributes['id']);
        return $data->flete_total;
    }

    public function getTieneTransformacionAttribute()
    {
        /*$resultado = DB::selectOne("SELECT CASE WHEN cc.tipo_cuadro=0 THEN 0
        ELSE 
        (SELECT COUNT(*) FROM mgcp_cuadro_costos.cc_am_filas WHERE cc_am_filas.id_cc_am=cc.id AND part_no_producto_transformado IS NOT NULL)
        END AS cantidad
        FROM mgcp_cuadro_costos.cc where id=?", [$this->attributes['id']]);*/
        $tieneTransformacion = false;
        $filas = CcAmFila::where('id_cc_am', $this->attributes['id'])->get();
        foreach ($filas as $fila) {
            if ($fila->tieneTransformacion()) {
                $tieneTransformacion = true;
                break;
            }
        }
        return $tieneTransformacion;
    }

    public function getMontoGananciaAttribute()
    {
        $data = CuadroCostoHelper::obtenerDetallesFilas($this->attributes['id']);
        return $data->ganancia_real_format;
    }

    public static function tipoEdicion($cuadro, $usuario)
    {
        $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
        if (($cuadro->estado_aprobacion == 1 || $cuadro->estado_aprobacion == 5) && ($usuario->tieneRol(28) || $oportunidad->id_responsable == $usuario->id)) {
            return 'corporativo';
        }
        if ($cuadro->estado_aprobacion == 3 && $usuario->tieneRol(46)) {
            return 'compras';
        }
        return 'ninguno';
    }

    public function getCantidadSolicitudesAttribute()
    {
        return CcSolicitud::where('id_cc', $this->attributes['id'])->count();
    }

    public function getCantidadAprobacionesAttribute()
    {
        return CcSolicitud::where('id_cc', $this->attributes['id'])->where('id_tipo', 1)->where('aprobada', true)->count();
    }

    public function nivelCentroCosto()
    {
        return $this->belongsTo(CentroCostoNivelView::class, 'id_centro_costo', 'id_centro_costo');
    }
}
