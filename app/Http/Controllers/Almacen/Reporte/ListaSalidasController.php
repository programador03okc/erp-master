<?php

namespace App\Http\Controllers\Almacen\Reporte;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Sede;
use App\Models\Almacen\Movimiento;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ListaSalidasController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }


    public function obtenerDataSalidas($idEmpresa,$idSede,$idAlmacenes,$idCondicioneList,$fechaInicio,$fechaFin,$idUsuario,$idCliente,$idMoneda){
        $data = Movimiento::select(
            'mov_alm.*',
            'sis_moneda.simbolo',
            'doc_ven.total',
            'doc_ven.fecha_vcmto',
            'doc_ven.total_igv',
            'doc_ven.total_a_pagar',
            'cont_tp_doc.abreviatura',
            'doc_ven.credito_dias',
            'log_cdn_pago.descripcion as des_condicion',
            'doc_ven.fecha_emision as fecha_doc',
            'alm_almacen.descripcion as des_almacen',
            'doc_ven.tipo_cambio',
            'doc_ven.moneda',
            DB::raw("(doc_ven.serie) || '-' || (doc_ven.numero) as doc"),
            DB::raw("(guia_ven.serie) || '-' || (guia_ven.numero) as guia"),
            'guia_ven.fecha_emision as fecha_guia',
            'adm_contri.nro_documento',
            'adm_contri.razon_social',
            'tp_ope.descripcion as des_operacion',
            'sis_usua.nombre_corto as nombre_trabajador'
        )
        ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
        ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'mov_alm.id_guia_ven')
        ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
        ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
        ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
        ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
        ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
        ->leftjoin('almacen.doc_ven', 'doc_ven.id_doc_ven', '=', 'mov_alm.id_doc_ven')
        ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_ven.id_tp_doc')
        ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_ven.moneda')
        ->leftjoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_ven.id_condicion')

            ->when(($idEmpresa > 0), function ($query) use($idEmpresa) {
                $sedes= Sede::where('id_empresa',$idEmpresa)->get();
                $idSedeList=[];
                foreach($sedes as $sede){
                    $idSedeList[]=$sede->id_sede;
                }
                $query->join('almacen.alm_almacen', 'alm_almacen.id_almacen', 'mov_alm.id_almacen');
                return $query->whereIn('alm_almacen.id_sede', $idSedeList);
            })
            ->when(($idSede > 0), function ($query) use($idSede) {
                return $query->where('alm_almacen.id_sede',$idSede);
            })
    
            ->when((($fechaInicio != 'SIN_FILTRO') and ($fechaFin == 'SIN_FILTRO')), function ($query) use($fechaInicio) {
                return $query->where('mov_alm.fecha_emision' ,'>=',$fechaInicio); 
            })
            ->when((($fechaInicio == 'SIN_FILTRO') and ($fechaFin != 'SIN_FILTRO')), function ($query) use($fechaFin) {
                return $query->where('mov_alm.fecha_emision' ,'<=',$fechaFin); 
            })
            ->when((($fechaInicio != 'SIN_FILTRO') and ($fechaFin != 'SIN_FILTRO')), function ($query) use($fechaInicio,$fechaFin) {
                return $query->whereBetween('mov_alm.fecha_emision' ,[$fechaInicio,$fechaFin]); 
            })
            ->when(($idAlmacenes!=null && count($idAlmacenes) > 0), function ($query) use($idAlmacenes) {
                return $query->whereIn('mov_alm.id_almacen',$idAlmacenes);
            })
            ->when(($idCondicioneList!=null && count($idCondicioneList) > 0), function ($query) use($idCondicioneList) {
                return $query->whereIn('mov_alm.id_operacion',$idCondicioneList);
            })
            ->when(($idCliente !=null && $idCliente > 0), function ($query) use($idCliente) {
                return $query->where('guia_com.id_proveedor',$idCliente);
            })
            ->when(($idUsuario !=null && $idUsuario > 0), function ($query) use($idUsuario) {
                return $query->where('guia_com.usuario',$idUsuario);
            })
            ->when(($idMoneda == 1 || $idMoneda == 2), function ($query) use($idMoneda) {
                return $query->where('doc_com.moneda',$idMoneda);
            })        
            ->where([['mov_alm.estado','!=',7]]);
            return $data;
    }  


    public function listarSalidas(Request $request){
        $idEmpresa= $request->idEmpresa;
        $idSede= $request->idSede;
        $idAlmacenes= $request->idAlmacenList;
        $idCondicioneList= $request->idCondicionList;
        $fechaInicio= $request->fechaInicio;
        $fechaFin= $request->fechaFin;
        $idCliente= $request->idCliente;
        $idUsuario= $request->idUsuario;
        $idMoneda= $request->idMoneda;

        $data = $this->obtenerDataSalidas($idEmpresa,$idSede,$idAlmacenes,$idCondicioneList,$fechaInicio,$fechaFin,$idUsuario,$idCliente,$idMoneda);

		return datatables($data)
        ->editColumn('fecha_guia', function($data) {
            $fecha =Carbon::parse($data->fecha_guia)->format('d-m-Y');
            return $fecha;
        })
        ->editColumn('fecha_registro', function($data) {
            $fecha =Carbon::parse($data->fecha_registro)->format('d-m-Y H:i');
            return $fecha;
        })
        ->filterColumn('nombre_trabajador', function ($query, $keyword) {
            $keywords = trim(strtoupper($keyword));
            $query->whereRaw("UPPER(sis_usua.nombre_corto) LIKE ?", ["%{$keywords}%"]);
        })    

        ->filterColumn('guia_ven.fecha_emision', function ($query, $keyword) {
            try {
                $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
                $query->where('guia_ven.fecha_emision', $keywords);
            } catch (\Throwable $th) {
            }
        })
        ->filterColumn('fecha_doc', function ($query, $keyword) {
            try {
                $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
                $query->where('doc_ven.fecha_emision', $keywords);
            } catch (\Throwable $th) {
            }
        })
        ->filterColumn('mov_alm.fecha_registro', function ($query, $keyword) {
            try {
                $desde = Carbon::createFromFormat('d-m-Y', trim($keyword))->hour(0)->minute(0)->second(0);
                $hasta = Carbon::createFromFormat('d-m-Y', trim($keyword));
                $query->whereBetween('mov_alm.fecha_registro', [$desde, $hasta->addDay()->addSeconds(-1)]);
            } catch (\Throwable $th) {
            }
        })
        ->filterColumn('des_almacen', function ($query, $keyword) {
            try {
                $query->where('alm_almacen.descripcion', trim($keyword));
            } catch (\Throwable $th) {
            }
        })
        ->filterColumn('guia', function ($query, $keyword) {
            try {
                $keywords = trim(strtoupper($keyword));
                $query->whereRaw("UPPER(CONCAT(guia_ven.serie,'-',guia_ven.numero)) LIKE ?", ["%{$keywords}%"]);
            } catch (\Throwable $th) {
            }
        })
        ->filterColumn('doc', function ($query, $keyword) {
            try {
                $keywords = trim(strtoupper($keyword));
                $query->whereRaw("UPPER(CONCAT(doc_ven.serie,'-',doc_ven.numero)) LIKE ?", ["%{$keywords}%"]);
            } catch (\Throwable $th) {
            }
        })
        ->filterColumn('tp_ope.descripcion', function ($query, $keyword) {
            try {
                $keywords = trim(strtoupper($keyword));
                $query->whereRaw("tp_ope.descripcion LIKE ?", ["%{$keywords}%"]);
            } catch (\Throwable $th) {
            }
        })
        ->toJson();

    }
}
