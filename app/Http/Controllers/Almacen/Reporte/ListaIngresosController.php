<?php

namespace App\Http\Controllers\Almacen\Reporte;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Sede;
use App\Models\Almacen\Movimiento;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use DB;

class ListaIngresosController extends Controller
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

    // public function obtenerDataIngresos($idEmpresa,$idSede,$idAlmacenes,$idCondicioneList,$fechaInicio,$fechaFin,$idUsuario,$idProveedor,$idMoneda){

    // }

    public function listarIngresos(Request $request){
        $idEmpresa= $request->idEmpresa;
        $idSede= $request->idSede;
        $idAlmacenes= $request->idAlmacenList;
        $idCondicioneList= $request->idCondicionList;
        $fechaInicio= $request->fechaInicio;
        $fechaFin= $request->fechaFin;
        $idProveedor= $request->idProveedor;
        $idUsuario= $request->idUsuario;
        $idMoneda= $request->idMoneda;

        $data = Movimiento::with(
        [
        'movimiento_detalle',
        'movimiento_detalle.guia_compra_detalle.documento_compra_detalle.documento_compra.proveedor.contribuyente',
        'movimiento_detalle.guia_compra_detalle.documento_compra_detalle.documento_compra.moneda',
        'movimiento_detalle.guia_compra_detalle.documento_compra_detalle.documento_compra.condicion_pago',
        'movimiento_detalle.guia_compra_detalle.orden_detalle.orden',
        'estado',
        'almacen',
        'almacen.tipo_almacen',
        'almacen.sede',
        'almacen.estado',
        'documento_compra',
        'documento_compra.tipo_documento.estado',
        'documento_compra.moneda',
        'documento_compra.condicion_pago',
        'guia_compra' => function ($q) {
            $q->where('guia_com.estado', '!=', 7);
        },
        'guia_compra.estado',
        'guia_compra.tipo_documento_almacen.estado',
        'guia_compra.proveedor.contribuyente',
        'guia_compra.proveedor.estadoProveedor',
        'operacion',
        'operacion.estado',
        'usuario'
        ])
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
        ->when((count($idAlmacenes) > 0), function ($query) use($idAlmacenes) {
            return $query->whereIn('mov_alm.id_almacen',$idAlmacenes);
        })
        ->when((count($idCondicioneList) > 0), function ($query) use($idCondicioneList) {
            return $query->whereIn('mov_alm.id_operacion',$idCondicioneList);
        })
        ->when(($idProveedor !=null && $idProveedor > 0), function ($query) use($idProveedor) {
            return $query->where('guia_com.id_proveedor',$idProveedor);
        })
        ->when(($idUsuario !=null && $idUsuario > 0), function ($query) use($idUsuario) {
            return $query->where('guia_com.usuario',$idUsuario);
        })
        ->when(($idMoneda == 1 || $idMoneda == 2), function ($query) use($idMoneda) {
            return $query->where('doc_com.moneda',$idMoneda);
        })
        ->where([['mov_alm.estado','!=',7]]);

        
        // ])->whereIn('mov_alm.id_mov_alm',[112,114]);

        return DataTables::eloquent($data)
        ->filterColumn('comprobantes', function ($query, $keyword) {
            // $keywords = trim(strtoupper($keyword));
            // $query
            // ->join('almacen.mov_alm_det', 'mov_alm.id_mov_alm', 'mov_alm_det.id_mov_alm')
            // ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
            // ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
            // ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
            // ->whereRaw("(CONCAT(doc_com.serie,'-',doc_com.numero)) LIKE ?", ["%{$keywords}%"]);
            $sql = "CONCAT(doc_com.serie,'-',doc_com.numero)  like ?";
            $query->whereRaw($sql, ["%{$keyword}%"]);
        })
        ->filterColumn('mov_alm.fecha_emision', function ($query, $keyword) {
            try {
                $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
                $query->where('mov_alm.fecha_emision', $keywords);
            } catch (\Throwable $th) {
            }
        })
        // ->filterColumn('guia_com.fecha_emision', function ($query, $keyword) {
        //     try {
        //         $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
        //         $query->where('guia_com.fecha_emision', $keywords);
        //     } catch (\Throwable $th) {
        //     }
        // })
        ->toJson();
    

    }

}
