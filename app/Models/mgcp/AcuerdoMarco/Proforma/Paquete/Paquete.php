<?php


namespace App\Models\mgcp\AcuerdoMarco\Proforma\Paquete;

use App\Models\mgcp\AcuerdoMarco\Empresa;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Paquete extends Model
{
    protected $table = 'mgcp_acuerdo_marco.proformas_paquete';
    //protected $primaryKey = 'nro_requerimiento';
    //public $incrementing = false;
    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id');
    }

    public function getFechaEmisionAttribute()
    {
        return $this->attributes['fecha_emision'] == null ? null : (new Carbon($this->attributes['fecha_emision']))->format('d-m-Y');
    }

    public function getFechaLimiteAttribute()
    {
        return $this->attributes['fecha_limite'] == null ? null : (new Carbon($this->attributes['fecha_limite']))->format('d-m-Y');
    }

    public static function generarConsultaRequerimientos(Request $filtros)
    {
        /*$requerimientosEnProformas = Paquete::generarConsultaProformas($filtros)->get();
        $arrayRequerimientos = [];
        foreach ($requerimientosEnProformas as $fila) {
            array_push($arrayRequerimientos, $fila->requerimiento);
        }*/
        /*Se obtiene la cabecera de los requerimientos. No se utiliza la query anterior porque no es posible paginar el resultado a nivel cabecera, 
        ya que esta trae los detalles*/
        $resultado = Paquete::join('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'id_entidad')
            ->where('tipo', $filtros->tipoProforma)
            ->whereBetween('fecha_emision', [Carbon::createFromFormat('d-m-Y', $filtros->fechaEmisionDesde)->toDateString(), Carbon::createFromFormat('d-m-Y', $filtros->fechaEmisionHasta)->toDateString()])

            //->join('mgcp_acuerdo_marco.departamentos', 'departamentos.id', '=', 'id_departamento')
            ->select([
                'requerimiento', 'fecha_emision', 'fecha_limite', 'id_entidad', 'entidades.ruc AS ruc_entidad', 'indicador_semaforo', 'entidades.nombre AS entidad',
                DB::raw('mgcp_acuerdo_marco.proforma_paquete_monto_requerimiento(requerimiento) AS monto_total')
                //DB::raw('mgcp_acuerdo_marco.proforma_co_monto_requerimiento(requerimiento) AS monto_total')
            ]);
        if (!empty($filtros->criterio)) {
            $criterio = '%' . str_replace(' ', '%', mb_strtoupper($filtros->criterio)) . '%';
            $resultado = $resultado->whereRaw('(entidades.ruc LIKE ? OR requerimiento LIKE ? OR entidades.nombre LIKE ?)', [$criterio, $criterio, $criterio]);
        }

        if ($filtros->chkEstado == 'on') {
            $resultado = $resultado->where('estado', $filtros->selectEstado);
        }

        if ($filtros->chkFechaLimite == 'on') {
            $resultado = $resultado->whereBetween('fecha_limite', [Carbon::createFromFormat('d-m-Y', $filtros->fechaLimiteDesde)->toDateString(), Carbon::createFromFormat('d-m-Y', $filtros->fechaLimiteHasta)->toDateString()]);
        }
        return $resultado->distinct();
    }

    public static function generarConsultaEnvioDetalle($proforma,$idEmpresa,$nroRequerimientoEntrega)
    {
        return Paquete::join('mgcp_acuerdo_marco.proforma_paquete_productos','proforma_paquete_productos.proforma_paquete_id','proformas_paquete.id')
        ->join('mgcp_acuerdo_marco.proforma_paquete_producto_detalles','proforma_paquete_producto_id','proforma_paquete_productos.id')
        ->join('mgcp_acuerdo_marco.proforma_paquete_destinos','proforma_paquete_destinos.proforma_paquete_id','proformas_paquete.id')
        ->join('mgcp_acuerdo_marco.proforma_paquete_envios','proforma_paquete_destino_id','proforma_paquete_destinos.id')
        ->join('mgcp_acuerdo_marco.proforma_paquete_envio_detalles',function($join){
            $join->on('proforma_paquete_envio_id','proforma_paquete_envios.id');
            $join->on('proforma_paquete_envio_detalles.nro_proforma','proforma_paquete_producto_detalles.nro_proforma');
        })->where('proforma',$proforma)->where('id_empresa',$idEmpresa)->where('nro_requerimiento_entrega',$nroRequerimientoEntrega)->where('seleccionado',true)->select(['proforma_paquete_envio_detalles.*'])->first();
    }

    public static function generarConsultaEnvios($requerimiento)
    {
        return Paquete::join('mgcp_acuerdo_marco.proforma_paquete_destinos','proforma_paquete_id','proformas_paquete.id')
        ->join('mgcp_acuerdo_marco.proforma_paquete_envios','proforma_paquete_destino_id','proforma_paquete_destinos.id')
        ->join('mgcp_acuerdo_marco.departamentos','departamentos.id','id_departamento')
        ->join('mgcp_acuerdo_marco.proforma_paquete_envio_detalles','proforma_paquete_envio_id','proforma_paquete_envios.id')
        ->join('mgcp_acuerdo_marco.proforma_paquete_producto_detalles','proforma_paquete_producto_detalles.nro_proforma','proforma_paquete_envio_detalles.nro_proforma')
        ->where('requerimiento',$requerimiento)->orderBy('lugar_entrega')->orderBy('proforma')
        ->select(['proforma','nro_requerimiento_entrega','cantidad','requiere_flete','lugar_entrega','inicio_entrega','fin_entrega', 'departamentos.nombre AS departamento','plazo_publicar',
        'editar_plazo','software_educativo'])->distinct()->get();
        /* 
        SELECT * FROM mgcp_acuerdo_marco.proformas_paquete
INNER JOIN mgcp_acuerdo_marco.proforma_paquete_destinos ON proformas_paquete.id=proforma_paquete_destinos.proforma_paquete_id
INNER JOIN mgcp_acuerdo_marco.proforma_paquete_envios ON proforma_paquete_destino_id=proforma_paquete_destinos.id
INNER JOIN mgcp_acuerdo_marco.proforma_paquete_envio_detalles ON proforma_paquete_envio_id=proforma_paquete_envios.id
WHERE requerimiento='REQ-2021-301838-467'
ORDER BY lugar_entrega, nro_proforma, id_empresa
        */
        //$resultado=Paquete::
    }

    public static function generarConsultaProformas($requerimiento)
    {
        return Paquete::join('mgcp_acuerdo_marco.proforma_paquete_productos', 'proforma_paquete_productos.proforma_paquete_id', 'proformas_paquete.id')
            ->join('mgcp_acuerdo_marco.proforma_paquete_producto_detalles', 'proforma_paquete_producto_detalles.proforma_paquete_producto_id', 'proforma_paquete_productos.id')

            ->join('mgcp_acuerdo_marco.productos_am', 'productos_am.id', 'id_producto')
            ->join('mgcp_acuerdo_marco.categorias', 'categorias.id', 'id_categoria')
            ->join('mgcp_acuerdo_marco.catalogos', 'catalogos.id', 'id_catalogo')
            ->join('mgcp_acuerdo_marco.entidades', 'entidades.id', 'id_entidad')
            ->leftJoin('mgcp_usuarios.users', 'id_ultimo_usuario', 'users.id')
            ->where('requerimiento', $requerimiento)
            ->orderBy('proforma')->orderBy('marca')->orderBy('modelo')->orderBy('part_no')->orderBy('id_producto')->orderBy('id_empresa');
    }
}
