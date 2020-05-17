<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sistema\sistema_doc_identidad;
use App\Models\sistema\pais;
use App\Models\sistema\moneda;
use App\Models\sistema\usuario;
use App\Models\sistema\sede;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SistemaController extends Controller
{
    public function mostrar_documentos()
    {
        $data = sistema_doc_identidad::all();
        return response()->json($data);

    }
    public function mostrar_doc_idendidad($id)
    {
        try {
        $data = sistema_doc_identidad::where('id_doc_identidad', $id)->first();
        return response()->json($data);
    } catch(QueryException $e) {
        // return Response::json(['error' => 'Error msg'], 404); // Status code here
        return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
    }
    }
    
    public function mostrar_archivos_adjuntos(Request $request, $id_detalle_requerimiento){
        $data = DB::table('almacen.alm_req_archivos')
        ->select(  
            'alm_req_archivos.id_archivo AS archivo_id_archivo',
            'alm_req_archivos.id_detalle_requerimiento AS archivo_id_detalle_requerimiento',
            'alm_req_archivos.archivo AS archivo_archivo',
            'alm_req_archivos.estado AS archivo_estado',
            'alm_req_archivos.fecha_registro AS archivo_fecha_registro'
            // DB::raw("(CASE WHEN alm_req_archivos.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
        ->where([
            ['alm_req_archivos.id_detalle_requerimiento', '=', $request->id_detalle_requerimiento],
            ['alm_req_archivos.estado', '=', 1]
        ])
        ->orderBy('alm_req_archivos.id_archivo', 'asc')
        ->get();
        return response()->json($data);
    }

        public function uploadfile(Request $request){
        $abreviatura = $request->abreviatura;
        $nombre_carpeta_destino = $request->nombre_carpeta_destino;
        $name_file='';
        $archivo_adjunto_length = count($request->archivo_adjunto);
        if($archivo_adjunto_length > 0){
            foreach($request->archivo_adjunto as $clave => $valor) {
                
                $file = $request->file('archivo_adjunto')[$clave];
                if(isset($file)){
                    $name_file = $abreviatura.time().$file->getClientOriginalName();
                    if($request->id_detalle_requerimiento >0 || $request->id_detalle_requerimiento !== NULL){
                        $alm_det_req = DB::table('almacen.alm_req_archivos')->insertGetId(
                                
                            [
                                'id_detalle_requerimiento'      => $request->id_detalle_requerimiento, 
                                'fecha_registro'        => date('Y-m-d H:i:s'),
                                'archivo'               => $name_file,
                                'estado'                => 1
                            ],
                            'id_archivo'
                        );
                            // Storage::disk('archivos')->put('tdr/'.$name_file, \File::get($file));
                            Storage::disk('archivos')->put("logistica/".$nombre_carpeta_destino.$name_file, \File::get($file));
                        }
                        
                }else{
                    $name_file = null;
                }
            } 
        }
        return response()->json($archivo_adjunto_length);
    }

    public function downloadfile(Request $request, $carpeta_destino, $archivo_storage){
         //$download_path = Storage_path('app/public/detalle_requerimiento/RQ1554388073Adaptive_Paths_Guide_to_Experience_Mapping.pdf');
        $download_path = Storage::disk('archivos')->path('logistica/'.$carpeta_destino.'/'.$archivo_storage);
        $headers = ['Content-Type: application/zip','Content-Disposition: attachment'];
        return response()->download($download_path, $archivo_storage,$headers);
    }

    public function actualizar_status_file(Request $request){
            if($request[0] >0){
            $data = DB::table('almacen.alm_req_archivos')->where('id_archivo', $request[0])
            ->update([
                'estado'                    => 0,
                'fecha_registro'            => date('Y-m-d H:i:s')
                ]);
            }else{
                $data=0;
            }
    return response()->json($data);
    }
//     public function mostrar_documentos()
//     {
//         $data = sistema_doc_identidad::all();
//          return response()->json($data);

    // }
    public function eliminar_pais($id){
        $data = pais::where('id_pais', $id)->delete();
        return response()->json($data);

    }
    public function actualizar_pais(Request $request, $id){
        $item = pais::where('id_pais', $id)->first();
        $item->descripcion = $request->descripcion;
        $item->abreviatura = $request->abreviatura;
        $item->estado = $request->estado;
        $item->save();
        return response()->json($item);
    }

    



    public function mostrar_monedas()
    {
        $data = moneda::all();
        return response()->json($data);
    }
    public function mostrar_moneda($id)
    {
        try {
        $data = moneda::where('id_moneda', $id)->first();
        return response()->json($data);
    } catch(QueryException $e) {
        // return Response::json(['error' => 'Error msg'], 404); // Status code here
        return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
    }

    }
    public function guardar_moneda(Request $request){
        $data = moneda::create($request->all());
        return response()->json($data);

    }
    public function eliminar_moneda($id){
        $data = moneda::where('id_moneda', $id)->delete();
        return response()->json($data);

    }
    public function actualizar_moneda(Request $request, $id){
        $item = moneda::where('id_moneda', $id)->first();
        $item->descripcion = $request->descripcion;
        $item->simbolo = $request->simbolo;
        $item->estado = $request->estado;
        $item->save();
        return response()->json($item);
    }



//

public function mostrar_usuarios()
{
    $data = usuario::all();
    return response()->json($data);
}
// public function mostrar_usuario($id)
// {
//     try {
//     $data = usuario::where('id_usuario', $id)->first();
//     return response()->json($data);
// } catch(QueryException $e) {
//     // return Response::json(['error' => 'Error msg'], 404); // Status code here
//     return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
// }
public function mostrar_usuario($id)
{
    $data = DB::table('sis_usua')
    ->select('sis_usua.*')
    ->where([['sis_usua.id_usuario', '=', $id]])
    ->get();
    return response()->json($data);
}
public function guardar_usuario(Request $request){
    $data = usuario::create($request->all());
      return response()->json($data);

}
public function eliminar_usuario($id){
    $data = usuario::where('id_usuario', $id)->delete();
    return response()->json($data);

}
public function actualizar_usuario(Request $request, $id){
    $item = usuario::where('id_usuario', $id)->first();
    $item->id_rol = $request->id_rol;
    $item->usuario = $request->usuario;
    $item->clave = $request->clave;
    $item->estado = $request->estado;
    $item->fecha_registro = $request->fecha_registro;
    $item->save();
    return response()->json($item);
}
 

//

public function mostrar_sedes()
{
    $data = sede::all();
     return response()->json($data);
}
public function mostrar_sede($id)
{
    try {
    $data = sede::where('id_sede', $id)->first();
    return response()->json($data);
} catch(QueryException $e) {
    // return Response::json(['error' => 'Error msg'], 404); // Status code here
    return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
}

}
public function guardar_sede(Request $request){
    $data = sede::create($request->all());
      return response()->json($data);

}
public function eliminar_sede($id){
    $data = sede::where('id_sede', $id)->delete();
    return response()->json($data);

}
public function actualizar_sede(Request $request, $id){
    $item = sede::where('id_sede', $id)->first();
    $item->id_empresa = $request->id_empresa;
    $item->codigo = $request->codigo;
    $item->descripcion = $request->descripcion;
    $item->direccion = $request->direccion;
    $item->direccion = $request->direccion;
    $item->estado = $request->estado;
    $item->fecha_registro = $request->fecha_registro;
    $item->save();
    return response()->json($item);
}
}
