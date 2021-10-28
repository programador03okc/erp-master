<?php

namespace App\Http\Controllers\Almacen\Reporte;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Sede;
use App\Models\Almacen\Movimiento;
use App\Models\Almacen\MovimientoDetalle;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


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

        $movimiento = Movimiento::join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
        ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
        ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
        ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'mov_alm.id_doc_com')
        // ->leftjoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
        ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
        // ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
        ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
        ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
        ->select(
            'mov_alm.*',
            // 'sis_moneda.simbolo',
            'doc_com.total',
            'doc_com.fecha_vcmto',
            'doc_com.total_igv',
            'doc_com.total_a_pagar',
            'cont_tp_doc.abreviatura',
            'doc_com.credito_dias',
            // 'log_cdn_pago.descripcion as des_condicion',
            'doc_com.fecha_emision as fecha_doc',
            'alm_almacen.descripcion as des_almacen',
            'doc_com.tipo_cambio',
            'doc_com.moneda',
            'doc_com.id_sede',
            DB::raw("CONCAT(doc_com.serie, '-' , doc_com.numero) as doc"),
            DB::raw("CONCAT(guia_com.serie,'-' ,guia_com.numero) as guia"),
            'guia_com.fecha_emision as fecha_guia',
            'adm_contri.nro_documento',
            'adm_contri.razon_social',
            'tp_ope.descripcion as des_operacion',
            'sis_usua.nombre_corto as nombre_trabajador'
        )

        ->when(($idEmpresa > 0), function ($query) use($idEmpresa) {
            $sedes= Sede::where('id_empresa',$idEmpresa)->get();
            $idSedeList=[];
            foreach($sedes as $sede){
                $idSedeList[]=$sede->id_sede;
            }
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
  
        ->where([['mov_alm.estado', '!=', 7]]);
        
        // foreach ($movimiento as $mov) {
        //     $ordenes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
        //         ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', 'guia_com_det.id_oc_det')
        //         ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', 'log_det_ord_compra.id_orden_compra')
        //         ->where('mov_alm_det.id_mov_alm', $mov->id_mov_alm)
        //         ->select(['log_ord_compra.codigo'])->distinct()->get();

        //         $ordenes_array = [];
        //         foreach ($ordenes as $oc) {
        //             array_push($ordenes_array, $oc->codigo);
        //         }

        //         $mov->setAttribute('ordenes',$ordenes_array);

        //         $comprobantes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
        //         ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
        //         ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
        //         ->join('logistica.log_prove', 'log_prove.id_proveedor', 'doc_com.id_proveedor')
        //         ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', 'log_prove.id_contribuyente')
        //         ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
        //         ->join('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
        //         ->where([
        //             ['mov_alm_det.id_mov_alm', '=', $mov->id_mov_alm],
        //             ['mov_alm_det.estado', '!=', 7],
        //             ['guia_com_det.estado', '!=', 7],
        //             ['doc_com_det.estado', '!=', 7]
        //         ])
        //         ->select([
        //             'doc_com.serie', 'doc_com.numero', 'doc_com.fecha_emision', 'sis_moneda.simbolo', 'doc_com.moneda',
        //             'adm_contri.nro_documento', 'adm_contri.razon_social', 'log_cdn_pago.descripcion as des_condicion',
        //             'doc_com.credito_dias', 'doc_com.sub_total', 'doc_com.total_igv', 'doc_com.total_a_pagar'
        //         ])
        //         ->distinct()->get();

        //         $comprobantes_array = [];
        //         $doc_fecha_emision_array = [];
        //         $ruc = '';
        //         $razon_social = '';
        //         $simbolo = '';
        //         $moneda = '';
        //         $total = '';
        //         $total_igv = '';
        //         $total_a_pagar = '';
        //         $condicion = '';
        //         $credito_dias = '';

        //         foreach ($comprobantes as $doc) {
        //             array_push($comprobantes_array, $doc->serie . '-' . $doc->numero);
        //             array_push($doc_fecha_emision_array, $doc->fecha_emision);
        //             $ruc = ($doc->nro_documento !== null ? $doc->nro_documento : '');
        //             $razon_social = ($doc->razon_social !== null ? $doc->razon_social : '');
        //             $simbolo = ($doc->simbolo !== null ? $doc->simbolo : '');
        //             $moneda = ($doc->moneda !== null ? $doc->moneda : '');
        //             $total = ($doc->sub_total !== null ? $doc->sub_total : '');
        //             $total_igv = ($doc->total_igv !== null ? $doc->total_igv : '');
        //             $total_a_pagar = ($doc->total_a_pagar !== null ? $doc->total_a_pagar : '');
        //             $condicion = ($doc->des_condicion !== null ? $doc->des_condicion : '');
        //             $credito_dias = ($doc->credito_dias !== null ? $doc->credito_dias : '');
        //         }
        //         $mov->setAttribute('documentos', $comprobantes_array);
        //         $mov->setAttribute('fecha_doc', $doc_fecha_emision_array);
        //         $mov->setAttribute('nro_documento',$ruc);
        //         $mov->setAttribute('razon_social',$razon_social);
        //         $mov->setAttribute('simbolo',$simbolo);
        //         $mov->setAttribute('moneda',$moneda);
        //         $mov->setAttribute('total',$total);
        //         $mov->setAttribute('total_igv',$total_igv);
        //         $mov->setAttribute('total_a_pagar',$total_a_pagar);
        //         $mov->setAttribute('des_condicion',$condicion. ($credito_dias !== '' ? ' ' . $credito_dias . ' días' : ''));
        //         $mov->setAttribute('credito_dias',$credito_dias);

        // }

        return datatables($movimiento)   
        ->editColumn('fecha_guia', function($movimiento) {
            $fecha =Carbon::parse($movimiento->fecha_guia)->format('d-m-Y');
            return $fecha;
        })
        ->editColumn('fecha_registro', function($movimiento) {
            $fecha =Carbon::parse($movimiento->fecha_registro)->format('d-m-Y H:i');
            return $fecha;
        })

        ->filterColumn('nombre_trabajador', function ($query, $keyword) {
            $keywords = trim(strtoupper($keyword));
            $query->whereRaw("UPPER(sis_usua.nombre_corto) LIKE ?", ["%{$keywords}%"]);
        })     
        ->filterColumn('guia_com.fecha_emision', function ($query, $keyword) {
            try {
                $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
                $query->where('guia_com.fecha_emision', $keywords);
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
                $query->whereRaw("UPPER(CONCAT(guia_com.serie,'-',guia_com.numero)) LIKE ?", ["%{$keywords}%"]);
            } catch (\Throwable $th) {
            }
        })
        ->filterColumn('doc', function ($query, $keyword) {
            try {
                $keywords = trim(strtoupper($keyword));
                $query->whereRaw("UPPER(CONCAT(doc_com.serie,'-',doc_com.numero)) LIKE ?", ["%{$keywords}%"]);
            } catch (\Throwable $th) {
            }
        })
        // ->filterColumn('log_cdn_pago.descripcion', function ($query, $keyword) {
        //     try {
        //         $keywords = trim(strtoupper($keyword));
        //         $query->whereRaw("log_cdn_pago.descripcion LIKE ?", ["%{$keywords}%"]);
        //     } catch (\Throwable $th) {
        //     }
        // })
        ->filterColumn('tp_ope.descripcion', function ($query, $keyword) {
            try {
                $keywords = trim(strtoupper($keyword));
                $query->whereRaw("tp_ope.descripcion LIKE ?", ["%{$keywords}%"]);
            } catch (\Throwable $th) {
            }
        })
        ->toJson();


    }

    // public function listarIngresos(Request $request){
    //     $idEmpresa= $request->idEmpresa;
    //     $idSede= $request->idSede;
    //     $idAlmacenes= $request->idAlmacenList;
    //     $idCondicioneList= $request->idCondicionList;
    //     $fechaInicio= $request->fechaInicio;
    //     $fechaFin= $request->fechaFin;
    //     $idProveedor= $request->idProveedor;
    //     $idUsuario= $request->idUsuario;
    //     $idMoneda= $request->idMoneda;

    //     $data = Movimiento::with(
    //     [
    //     'movimiento_detalle',
    //     'movimiento_detalle.guia_compra_detalle.documento_compra_detalle.documento_compra.proveedor.contribuyente',
    //     'movimiento_detalle.guia_compra_detalle.documento_compra_detalle.documento_compra.moneda',
    //     'movimiento_detalle.guia_compra_detalle.documento_compra_detalle.documento_compra.condicion_pago',
    //     'movimiento_detalle.guia_compra_detalle.orden_detalle.orden',
    //     'estado',
    //     'almacen',
    //     'almacen.tipo_almacen',
    //     'almacen.sede',
    //     'almacen.estado',
    //     'documento_compra',
    //     'documento_compra.tipo_documento.estado',
    //     'documento_compra.moneda',
    //     'documento_compra.condicion_pago',
    //     'guia_compra' => function ($q) {
    //         $q->where('guia_com.estado', '!=', 7);
    //     },
    //     'guia_compra.estado',
    //     'guia_compra.tipo_documento_almacen.estado',
    //     'guia_compra.proveedor.contribuyente',
    //     'guia_compra.proveedor.estadoProveedor',
    //     'operacion',
    //     'operacion.estado',
    //     'usuario'
    //     ])
    //     ->when(($idEmpresa > 0), function ($query) use($idEmpresa) {
    //         $sedes= Sede::where('id_empresa',$idEmpresa)->get();
    //         $idSedeList=[];
    //         foreach($sedes as $sede){
    //             $idSedeList[]=$sede->id_sede;
    //         }
    //         $query->join('almacen.alm_almacen', 'alm_almacen.id_almacen', 'mov_alm.id_almacen');
    //         return $query->whereIn('alm_almacen.id_sede', $idSedeList);
    //     })
    //     ->when(($idSede > 0), function ($query) use($idSede) {
    //         return $query->where('alm_almacen.id_sede',$idSede);
    //     })

    //     ->when((($fechaInicio != 'SIN_FILTRO') and ($fechaFin == 'SIN_FILTRO')), function ($query) use($fechaInicio) {
    //         return $query->where('mov_alm.fecha_emision' ,'>=',$fechaInicio); 
    //     })
    //     ->when((($fechaInicio == 'SIN_FILTRO') and ($fechaFin != 'SIN_FILTRO')), function ($query) use($fechaFin) {
    //         return $query->where('mov_alm.fecha_emision' ,'<=',$fechaFin); 
    //     })
    //     ->when((($fechaInicio != 'SIN_FILTRO') and ($fechaFin != 'SIN_FILTRO')), function ($query) use($fechaInicio,$fechaFin) {
    //         return $query->whereBetween('mov_alm.fecha_emision' ,[$fechaInicio,$fechaFin]); 
    //     })
    //     ->when((count($idAlmacenes) > 0), function ($query) use($idAlmacenes) {
    //         return $query->whereIn('mov_alm.id_almacen',$idAlmacenes);
    //     })
    //     ->when((count($idCondicioneList) > 0), function ($query) use($idCondicioneList) {
    //         return $query->whereIn('mov_alm.id_operacion',$idCondicioneList);
    //     })
    //     ->when(($idProveedor !=null && $idProveedor > 0), function ($query) use($idProveedor) {
    //         return $query->where('guia_com.id_proveedor',$idProveedor);
    //     })
    //     ->when(($idUsuario !=null && $idUsuario > 0), function ($query) use($idUsuario) {
    //         return $query->where('guia_com.usuario',$idUsuario);
    //     })
    //     ->when(($idMoneda == 1 || $idMoneda == 2), function ($query) use($idMoneda) {
    //         return $query->where('doc_com.moneda',$idMoneda);
    //     })
    //     ->where([['mov_alm.estado','!=',7]]);

        
    //     // ])->whereIn('mov_alm.id_mov_alm',[112,114]);

    //     return DataTables::eloquent($data)
    //     ->filterColumn('comprobantes', function ($query, $keyword) {
    //         // $keywords = trim(strtoupper($keyword));
    //         // $query
    //         // ->join('almacen.mov_alm_det', 'mov_alm.id_mov_alm', 'mov_alm_det.id_mov_alm')
    //         // ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
    //         // ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
    //         // ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
    //         // ->whereRaw("(CONCAT(doc_com.serie,'-',doc_com.numero)) LIKE ?", ["%{$keywords}%"]);
    //         $sql = "CONCAT(doc_com.serie,'-',doc_com.numero)  like ?";
    //         $query->whereRaw($sql, ["%{$keyword}%"]);
    //     })
    //     ->filterColumn('mov_alm.fecha_emision', function ($query, $keyword) {
    //         try {
    //             $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
    //             $query->where('mov_alm.fecha_emision', $keywords);
    //         } catch (\Throwable $th) {
    //         }
    //     })
    //     // ->filterColumn('guia_com.fecha_emision', function ($query, $keyword) {
    //     //     try {
    //     //         $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
    //     //         $query->where('guia_com.fecha_emision', $keywords);
    //     //     } catch (\Throwable $th) {
    //     //     }
    //     // })
    //     ->toJson();
    

    // }

}
