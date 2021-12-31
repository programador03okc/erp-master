<?php

namespace App\Models\Tesoreria;

use App\Models\Administracion\Documento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Administracion\Estado;
use App\Models\Configuracion\Usuario;
use Carbon\Carbon;
use Debugbar;

class RequerimientoPago extends Model
{
    protected $table = 'tesoreria.requerimiento_pago';
    protected $primaryKey = 'id_requerimiento_pago';
    protected $appends = ['id_documento','termometro', 'nombre_estado'];
    public $timestamps = false;

 

    public function getIdDocumentoAttribute()
    {
        $documento= Documento::where([["id_doc",$this->attributes['id_requerimiento_pago']],["id_tp_documento","3"]])->first();
        
        return $documento!=null ?$documento->id_doc_aprob:null;
    }
    public function getFechaEntregaAttribute()
    {
        if ($this->attributes['fecha_entrega'] == null) {
            return '';
        } else {
            $fecha = new Carbon($this->attributes['fecha_entrega']);
            return $fecha->format('d-m-Y');
        }
    }

    public function getFechaRegistroAttribute()
    {
        $fecha = new Carbon($this->attributes['fecha_registro']);
        return $fecha->format('d-m-Y H:i');
    }

    public function getNombreEstadoAttribute()
    {
        $estado = Estado::join('tesoreria.requerimiento_pago', 'adm_estado_doc.id_estado_doc', '=', 'requerimiento_pago.estado')
            ->where('requerimiento_pago.id_requerimiento_pago', $this->attributes['id_requerimiento_pago'])
            ->first()->estado_doc;
        return $estado;
    }

 
    public function getTermometroAttribute()
    {

        switch ($this->attributes['id_prioridad']) {
            case '1':
                return '<div class="text-center"> <i class="fas fa-thermometer-empty green"  data-toggle="tooltip" data-placement="right" title="Normal"></i> </div>';
                break;

            case '2':
                return '<div class="text-center"> <i class="fas fa-thermometer-half orange"  data-toggle="tooltip" data-placement="right" title="Alta"></i> </div>';
                break;

            case '3':
                return '<div class="text-center"> <i class="fas fa-thermometer-full red"  data-toggle="tooltip" data-placement="right" title="Crítica"></i> </div>';
                break;

            default:
                return '';
                break;
        }
    }

    public static function obtenerCantidadRegistros($grupo, $idRequerimientoPago)
    {
        $yyyy = date('Y', strtotime("now"));
        $num = RequerimientoPago::when(($grupo > 0), function ($query) use ($grupo, $idRequerimientoPago) {
            return $query->Where([['id_grupo', '=', $grupo], ['id_requerimiento_pago', '<=', $idRequerimientoPago]]);
        })
            ->whereYear('fecha_registro', '=', $yyyy)
            ->count();
        return $num;
    }

    public static function crearCodigo($idGrupo, $idRequerimientoPago)
    {
        $documento = 'RDP'; //Prefijo para el codigo de requerimiento
        if ($idGrupo == 1) {
            $documento .= 'A';
            $num = RequerimientoPago::obtenerCantidadRegistros(1, $idRequerimientoPago); //tipo: BS, grupo: Administración
        }
        if ($idGrupo == 2) {
            $documento .= 'C';
            $num = RequerimientoPago::obtenerCantidadRegistros(2, $idRequerimientoPago); //tipo: BS, grupo: Comercial
        }
        if ($idGrupo == 3) {
            $documento .= 'P';
            $num = RequerimientoPago::obtenerCantidadRegistros(3, $idRequerimientoPago); //tipo: BS, grupo: Proyectos
        }
        $yy = date('y', strtotime("now"));
        $correlativo = sprintf('%04d', $num);

        return "{$documento}-{$yy}{$correlativo}";
    }



    public function detalle()
    {
        return $this->hasMany('App\Models\Tesoreria\DetalleRequerimientoPago', 'id_requerimiento_pago', 'id_requerimiento_pago');
    }
    public function prioridad()
    {
        return $this->hasOne('App\Models\Administracion\prioridad', 'id_prioridad', 'id_prioridad');
    }
    public function periodo()
    {
        return $this->hasOne('App\Models\Administracion\periodo', 'id_periodo', 'id_periodo');
    }
    public function division()
    {
        return $this->belongsTo('App\Models\Administracion\DivisionArea', 'division_id', 'id_division');
    }
    public function creadoPor()
    {
        return $this->belongsTo('App\Models\Configuracion\Usuario', 'id_usuario', 'id_usuario');
    }
    public function moneda()
    {
        return $this->belongsTo('App\Models\Configuracion\Moneda', 'id_moneda', 'id_moneda');
    }
    public function empresa()
    {
        return $this->hasOne('App\Models\Administracion\Empresa', 'id_empresa', 'id_empresa');
    }
    public function sede()
    {
        return $this->hasOne('App\Models\Administracion\Sede', 'id_sede', 'id_sede');
    }

    public function grupo(){
        return $this->belongsTo('App\Models\Configuracion\Grupo','id_grupo','id_grupo');
    }

    public function cuadroCostos()
    {
        return $this->hasOne('App\Models\Comercial\CuadroCosto\CuadroCostosView', 'id', 'id_cc');
    }
    public function proyecto()
    {
        return $this->hasOne('App\Models\Proyectos\Proyecto', 'id_proyecto', 'id_proyecto');
    }
}
