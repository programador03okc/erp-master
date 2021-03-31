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

    function procesarPago(Request $request){
        
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $file = $request->file('adjunto');

            $id_pago = DB::table('tesoreria.req_pagos')
            ->insertGetId([ 'id_requerimiento'=> $request->id_requerimiento,
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
            
            DB::table('almacen.alm_req')
            ->where('id_requerimiento',$request->id_requerimiento)
            ->update(['estado'=>9]);//procesado

            DB::commit();
            return response()->json($id_pago);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}