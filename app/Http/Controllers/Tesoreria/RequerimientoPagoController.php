<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class RequerimientoPagoController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_main_tesoreria(){
        $pagos_pendientes = DB::table('almacen.alm_req')
        ->where('estado',8)->count();

        $confirmaciones_pendientes = DB::table('almacen.alm_req')
        ->where([['estado','=',19],['confirmacion_pago','=',false]])->count();

        return view('tesoreria/main', compact('pagos_pendientes','confirmaciones_pendientes'));
    }
    
    function view_requerimiento_pagos(){
        return view('tesoreria/Pagos/requerimientoPagos');
    }

    function listarRequerimientosPagos(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','sis_sede.descripcion as sede_descripcion',
            'sis_usua.nombre_corto as responsable',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'req_pagos.fecha_pago','req_pagos.observacion',
            'registrado_por.nombre_corto as usuario_pago',
            'sis_moneda.simbolo'
            )
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_req.id_sede')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','alm_req.estado')
            ->leftJoin('tesoreria.req_pagos','req_pagos.id_requerimiento','=','alm_req.id_requerimiento')
            ->leftJoin('configuracion.sis_usua as registrado_por','registrado_por.id_usuario','=','req_pagos.registrado_por')
            ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_req.id_moneda')
            ->where('alm_req.estado',8)
            ->orWhere('alm_req.estado',9)
            ->orderBy('alm_req.fecha_requerimiento','desc');
        return datatables($data)->toJson();
    }

    
    public function listarComprobantesPagos(){
        $data = DB::table('almacen.doc_com')
        ->select(
            'doc_com.*','adm_contri.razon_social',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
            'sis_moneda.simbolo','log_cdn_pago.descripcion AS condicion_pago',
            'cont_tp_doc.descripcion as tipo_documento',
            'req_pagos.fecha_pago','req_pagos.observacion',
            'registrado_por.nombre_corto as usuario_pago',
            )
        ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_com.estado')
        ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
        ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
        ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
        ->leftJoin('tesoreria.req_pagos','req_pagos.id_doc_com','=','doc_com.id_doc_com')
        ->leftJoin('configuracion.sis_usua as registrado_por','registrado_por.id_usuario','=','req_pagos.registrado_por')
        ->where('doc_com.estado','=',8)
        ->orWhere('doc_com.estado','=',9);

        return datatables($data)->toJson();
    }

    public function detalleComprobante($id_doc_com)
    {
        $detalles = DB::table('almacen.doc_com_det')
            ->select('doc_com_det.*','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
                    'alm_prod.descripcion as producto_descripcion','alm_prod.codigo as producto_codigo',
                    'alm_und_medida.abreviatura','alm_prod.part_number')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'doc_com_det.id_item')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'doc_com_det.id_unid_med')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'doc_com_det.estado')
            ->where([['doc_com_det.id_doc','=',$id_doc_com],
                     ['doc_com_det.estado','!=',7]])
            ->get();

        return response()->json($detalles);
    }

    function procesarPago(Request $request){
        
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $file = $request->file('adjunto');

            $id_pago = DB::table('tesoreria.req_pagos')
            ->insertGetId([ 'id_requerimiento'=> $request->id_requerimiento,
                            'id_doc_com'=>$request->id_doc_com,
                            'fecha_pago'=>$request->fecha_pago,
                            'observacion'=>$request->observacion,
                            'registrado_por'=>$id_usuario,
                            'estado'=>1,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                ],'id_pago');

            if (isset($file)){
                //obtenemos el nombre del archivo
                $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $nombre = $id_pago.'.'.$request->codigo.'.'.$extension;
                //indicamos que queremos guardar un nuevo archivo en el disco local
                \File::delete(public_path('tesoreria/requerimiento_pagos/'.$nombre));
                \Storage::disk('archivos')->put('tesoreria/requerimiento_pagos/'.$nombre,\File::get($file));
                
                DB::table('tesoreria.req_pagos')
                ->where('id_pago',$id_pago)
                ->update([ 'adjunto'=>$nombre ]);
            }
            
            if ($request->id_requerimiento!==null){
                DB::table('almacen.alm_req')
                ->where('id_requerimiento',$request->id_requerimiento)
                ->update(['estado'=>9]);//procesado
            }
            else if ($request->id_doc_com!==null){
                DB::table('almacen.doc_com')
                ->where('id_doc_com',$request->id_doc_com)
                ->update(['estado'=>9]);//procesado
            }

            DB::commit();
            return response()->json($id_pago);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}