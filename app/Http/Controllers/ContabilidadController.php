<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ContabilidadController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_main_contabilidad(){
        return view('contabilidad/main');
    }
    
    function view_listar_ventas(){
        return view('contabilidad/ventas/vista_listar');
    }
    
    function view_registro_ventas(){
        return view('contabilidad/ventas/vista_registro');
    }
    
    function view_cta_contable(){
        return view('contabilidad/cta_contable');
    }
    function mostrar_cuentas_contables(){
        $padres = DB::table('contabilidad.cont_cta_cble')->where('cod_padre', null)->get();
        $array = [];
        
        if (sizeof($padres) > 0){
            foreach ($padres as $row){
                $data = array();
                $data['text'] = $row->codigo." - ".$row->descripcion;
                $data['nodes'] = $this->traer_hijos($row->codigo);
                array_push($array, $data);
            }
        }

        return json_encode(array_values($array));
    }
    function traer_hijos($padre){
        $hijos = DB::table('contabilidad.cont_cta_cble')
        ->where('cod_padre', $padre)
        ->get()->toArray();
        $array = [];

        if (sizeof($hijos) > 0){
            foreach ($hijos as $row){
                $sub_array = array();
                $sub_array['text'] = $row->codigo." - ".$row->descripcion;
                // $sub_array['nodes'] = $this->traer_hijos($row->codigo);
                array_push($array,$sub_array);
            }
        }
        return $array;
    }








    public function mostrar_tipo_cuentas()
    {
      
         $adm_tp_cta = DB::table('contabilidad.adm_tp_cta')
          ->select(
             'adm_tp_cta.id_tipo_cuenta',
             'adm_tp_cta.descripcion',
             'adm_tp_cta.estado',
             DB::raw("(CASE WHEN adm_tp_cta.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS descripcion_estado")

             )
             ->where([
        
                ['adm_tp_cta.estado', '=', 1]
                ])
             ->orderBy('adm_tp_cta.id_tipo_cuenta', 'asc')
         ->get();
         return response()->json($adm_tp_cta);

    }
    public function mostrar_tipo_cuenta($id)
    {
        $adm_tp_cta = DB::table('contabilidad.adm_tp_cta')
        ->select(
            'adm_tp_cta.id_tipo_cuenta',
            'adm_tp_cta.descripcion',
            'adm_tp_cta.estado'
            )
            ->where([
                ['adm_tp_cta.id_tipo_cuenta', '=', $id]
               ])
            ->orderBy('adm_tp_cta.id_tipo_cuenta', 'asc')
        ->get();
        return response()->json(["adm_tp_cta"=>$adm_tp_cta]);
    }
 
    public function guardar_tipo_cuenta(Request $request){
        $data = DB::table('contabilidad.adm_tp_cta')->insertGetId(
            [
            'descripcion'=> $request->descripcion,
            'estado'     => $request->estado
            ],
            'id_tipo_cuenta'
        );
        return response()->json($data);

    }

    public function actualizar_tipo_cuenta(Request $request, $id){
        $data = DB::table('contabilidad.adm_tp_cta')->where('id_tipo_cuenta', $id)
        ->update([
            'descripcion'=> $request->descripcion,
            'estado'     => $request->estado
        ]);
        return response()->json($data);
    }

    public function eliminar_tipo_cuenta($id){
        // $data = DB::table('adm_tp_cta')->where('id_tipo_cuenta', '=', $id)->delete();
        // return response()->json($data);
        $data = DB::table('contabilidad.adm_tp_cta')->where('id_tipo_cuenta', $id)
        ->update([
            'estado'     => 0
        ]);
        return response()->json($data);

    }
    // public function mostrar_tipo_contribuyentes()
    // {
    //     $data = tipo_contribuyente::all();
    //      return response()->json($data);

    // }
    // public function mostrar_tipo_contribuyente($id)
    // {
    //     try {
    //     $data = tipo_contribuyente::where('id_tipo_contribuyente', $id)->first();
    //     return response()->json($data);
    // } catch(QueryException $e) {
    //     // return Response::json(['error' => 'Error msg'], 404); // Status code here
    //     return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
    // }

    // }
    // public function guardar_tipo_contribuyente(Request $request){
    //     $data = tipo_contribuyente::create($request->all());
    //       return response()->json($data);

    // }
    // public function eliminar_tipo_contribuyente($id){
    //     $data = tipo_contribuyente::where('id_tipo_contribuyente', $id)->delete();
    //     return response()->json($data);

    // }
    // public function actualizar_tipo_contribuyente(Request $request, $id){
    //     $item = tipo_contribuyente::where('id_tipo_contribuyente', $id)->first();
    //     // $item->id_tipo_contribuyente = $request->id_tipo_contribuyente;
    //     $item->descripcion = $request->descripcion;
    //     $item->estado = $request->estado;
    //     $item->save();
    //     return response()->json($item);
    // }
    //
    public function fill_input_contribuyentes(){

         $adm_tp_contri = DB::table('contabilidad.adm_tp_contri')
                    ->select(
                        'adm_tp_contri.id_tipo_contribuyente',
                        'adm_tp_contri.descripcion AS adm_tp_contri_descripcion'
                    )
                    ->where([
                        ['adm_tp_contri.estado', '=', 1]
                        ])
                    ->get();
         $sis_identi = DB::table('contabilidad.sis_identi')

                    ->select(
                        'sis_identi.id_doc_identidad',
                        'sis_identi.descripcion AS sis_identi_descripcion'
                    )
                    ->where([
                        ['sis_identi.estado', '=', 1]
                        ])
                    ->get();
         $sis_pais = DB::table('configuracion.sis_pais')
                    ->select(
                        'sis_pais.id_pais',
                        'sis_pais.descripcion as sis_pais_descripcion'
                    )
                    ->where([
                        ['sis_pais.estado', '=', 1]
                        ])  
                    ->get();
         $adm_rubro = DB::table('contabilidad.adm_rubro')
                    ->select(
                        'adm_rubro.id_rubro',
                        'adm_rubro.descripcion AS descripcion_rubro'
                     )
                    ->get();
        $data = ["adm_tp_contri"=>$adm_tp_contri,"sis_identi"=>$sis_identi,"sis_pais"=>$sis_pais,"adm_rubro"=>$adm_rubro];   
 
        return response()->json($data);

    }
//     public function fill_input_rubro(){

//         $adm_contri = DB::table('adm_contri')
//                    ->select(
//                        'adm_contri.id_contribuyente',
//                        'adm_contri.razon_social'
//                    )
//                    ->where([
//                        ['adm_contri.estado', '=', 1]
//                        ])
//                    ->get();
//         $adm_rubro = DB::table('adm_rubro')
//                 ->select(
//                     'adm_rubro.id_rubro',
//                     'adm_rubro.descripcion'
//                 )
//                 ->where([
//                     ['adm_rubro.estado', '=', 1]
//                     ])
//                 ->get();
 
//        $data = ["adm_contri"=>$adm_contri,"adm_rubro"=>$adm_rubro];   

//        return response()->json($data);

//    }
    public function fill_input_empresas(){

        $adm_contri = DB::table('contabilidad.adm_contri')
                   ->select(
                       'adm_contri.id_contribuyente',
                       'adm_contri.razon_social'
                   )
                   ->where([
                       ['adm_contri.estado', '=', 1]
                       ])
                   ->get();
 
 
       $data = ["adm_contri"=>$adm_contri];   

       return response()->json($data);

   }
   public function fill_input_cuenta(){
    $adm_contri = DB::table('contabilidad.adm_contri')
                ->select(
                    'adm_contri.id_contribuyente',
                    'adm_contri.razon_social'
                )
                ->where([
                    ['adm_contri.estado', '=', 1]
                    ])
                ->get();
    $adm_tp_cta = DB::table('contabilidad.adm_tp_cta')
               ->select(
                   'adm_tp_cta.id_tipo_cuenta',
                   'adm_tp_cta.descripcion'
               )
               ->get();
            $data = ["adm_tp_cta"=>$adm_tp_cta];   

    $cont_banco = DB::table('contabilidad.cont_banco')
                    ->leftJoin(DB::raw("(SELECT 
                    adm_contri.id_contribuyente,
                    adm_contri.razon_social
                    FROM adm_contri ) as banco"),function($join){
                    $join->on("banco.id_contribuyente","=","cont_banco.id_contribuyente");
                    })
               ->select(
                   'cont_banco.id_banco',
                   'cont_banco.id_contribuyente',
                   'banco.razon_social AS banco_razon_social',
                   'cont_banco.codigo',
                   'cont_banco.estado'
               )
               ->get();
            $data = ["adm_contri"=>$adm_contri,"adm_tp_cta"=>$adm_tp_cta,"cont_banco"=>$cont_banco];   

         return response()->json($data);

}
    public function mostrar_contribuyentes()
        {
         $result = DB::table('contabilidad.adm_contri')
                    ->join('contabilidad.adm_tp_contri', 'adm_contri.id_tipo_contribuyente', '=', 'adm_tp_contri.id_tipo_contribuyente')
                    ->leftJoin('contabilidad.adm_ctb_rubro', 'adm_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
                    ->leftJoin('contabilidad.adm_ctb_contac', 'adm_contri.id_contribuyente', '=', 'adm_ctb_contac.id_contribuyente')
                    ->leftJoin('contabilidad.sis_identi', 'adm_contri.id_doc_identidad', '=', 'sis_identi.id_doc_identidad')
                    ->leftJoin(DB::raw("(SELECT 
                    adm_rubro.id_rubro,
                    adm_rubro.descripcion
                    FROM contabilidad.adm_rubro ) as rubro"),function($join){
                    $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
                    })
                     ->select(
                     'adm_contri.id_contribuyente', 
                     'adm_contri.id_tipo_contribuyente', 
                     'sis_identi.descripcion as sis_identi_descripcion', 
                     'adm_contri.razon_social', 
                     'adm_contri.nro_documento', 
                     'adm_contri.telefono', 
                     'adm_contri.celular', 
                     'adm_contri.direccion_fiscal', 
                     'adm_contri.ubigeo', 
                     'adm_contri.id_pais', 
                     'adm_contri.estado', 
                     'adm_contri.fecha_registro', 
                     'rubro.id_rubro AS id_rubro',
                     'rubro.descripcion AS rubro_descripcion',
                     'adm_ctb_contac.id_contribuyente as adm_ctb_contacto_id_contribuyente',
                     'adm_ctb_contac.nombre', 
                     'adm_ctb_contac.cargo',
                     'adm_ctb_contac.telefono AS adm_ctb_contac_telefono', 
                     'adm_ctb_contac.email', 
                     'adm_ctb_contac.estado AS estado_adm_ctb_contac',
                     'adm_ctb_contac.fecha_registro AS adm_ctb_contac_fecha_registro'
                     )
                     ->where('adm_contri.estado', '=', 1)
                    ->orderBy('adm_contri.id_contribuyente', 'asc')
                     ->get();


                 foreach($result as $data){
                    $contacto[]=[
                        'id_contribuyente'=> $data->adm_ctb_contacto_id_contribuyente,
                        'nombre'=> $data->nombre,
                        'cargo'=> $data->cargo,
                        'telefono'=> $data->adm_ctb_contac_telefono,
                        'email'=> $data->email,
                        'estado'=> $data->estado_adm_ctb_contac,
                        'fecha_registro'=> $data->adm_ctb_contac_fecha_registro              
                    ];      
            };
                
            
            $lastId = "";
                foreach($result as $data){
                    if ($data->id_contribuyente !== $lastId) {
                        $contribuyente[] = [
                            'id_contribuyente'=> $data->id_contribuyente,
                            'id_tipo_contribuyente'=> $data->id_tipo_contribuyente,
                            'razon_social'=> $data->razon_social,
                            'doc_identi'=>$data->sis_identi_descripcion,
                            'nro_documento'=>$data->nro_documento,
                            'id_rubro'=>$data->id_rubro,
                            'rubro_descripcion'=>$data->rubro_descripcion,
                            'telefono'=> $data->telefono,
                            'celular'=> $data->celular,
                            'direccion_fiscal'=> $data->direccion_fiscal,
                            'ubigeo'=> $data->ubigeo,
                            'id_pais'=> $data->id_pais,
                            'estado'=> $data->estado,
                            'fecha_registro'=> $data->fecha_registro,
                        ];  
                        $lastId = $data->id_contribuyente;
                      } 
            };
            
             for($j=0; $j< sizeof($contacto);$j++){
                for($i=0; $i< sizeof($contribuyente);$i++){
                    if($contacto[$j]['id_contribuyente'] === $contribuyente[$i]['id_contribuyente']){
                        $contribuyente[$i]['contacto'][]=$contacto[$j];
                    }

                }

            }
 
        return response()->json($contribuyente);
 
 }

public function mostrar_contribuyente($id)
    {
        try {
            $result = DB::table('contabilidad.adm_contri')
             ->select(
             'adm_contri.id_contribuyente', 
             'adm_contri.razon_social', 
             'adm_contri.estado', 
             'adm_contri.fecha_registro'
             )
            
             ->where([
                 ['adm_contri.id_contribuyente', '=', $id],
                ['adm_contri.estado', '=', 1]
                ])
             ->get();
    return response()->json($result);
    } catch(QueryException $e) {
        // return Response::json(['error' => 'Error msg'], 404); // Status code here
        return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
    }
}
public function mostrar_contribuyente_data_contribuyente($id){
        try {
            $result = DB::table('contabilidad.adm_contri')
            ->leftJoin('adm_tp_contri', 'adm_contri.id_tipo_contribuyente', '=', 'adm_tp_contri.id_tipo_contribuyente')
            ->leftJoin('sis_pais', 'adm_contri.id_pais', '=', 'sis_pais.id_pais')
            ->leftJoin('adm_ctb_rubro', 'adm_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
            ->leftJoin('sis_identi', 'adm_contri.id_doc_identidad', '=', 'sis_identi.id_doc_identidad')
            ->leftJoin(DB::raw("(SELECT 
            adm_rubro.id_rubro,
            adm_rubro.descripcion
            FROM adm_rubro ) as rubro"),function($join){
            $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
            })
             ->select(
             'adm_contri.id_contribuyente', 
             'adm_contri.id_tipo_contribuyente', 
             'adm_tp_contri.descripcion AS adm_tip_contri_descripcion',
             'adm_contri.razon_social', 
             'rubro.id_rubro',
             'rubro.descripcion AS descripcion_rubro',
             'adm_contri.nro_documento', 
             'adm_contri.telefono', 
             'adm_contri.celular', 
             'adm_contri.direccion_fiscal', 
             'adm_contri.ubigeo', 
             'adm_contri.id_pais', 
             'sis_pais.descripcion AS sis_pais_descripcion', 
             'adm_contri.estado', 
             'adm_contri.fecha_registro', 
             'sis_identi.id_doc_identidad', 
             'sis_identi.descripcion as sis_identi_descripcion'

 
             )
             ->where([
                ['adm_contri.id_contribuyente', '=', $id],
               ['adm_contri.estado', '=', 1]
               ])
             ->get();

 
    $lastId = "";
        foreach($result as $data){
            if ($data->id_contribuyente !== $lastId) {
                $contribuyente[] = [
                    'id_contribuyente'=> $data->id_contribuyente,
                    'id_tipo_contribuyente'=> $data->id_tipo_contribuyente,
                    'adm_tip_contri_descripcion'=>$data->adm_tip_contri_descripcion,
                    'razon_social'=> $data->razon_social,
                    'id_rubro'=> $data->id_rubro,
                    'descripcion_rubro'=> $data->descripcion_rubro,
                    
                    'id_doc_identidad'=>$data->id_doc_identidad,
                    'doc_identi'=>$data->sis_identi_descripcion,
                    'nro_documento'=>$data->nro_documento,
                    'telefono'=> $data->telefono,
                    'celular'=> $data->celular,
                    'direccion_fiscal'=> $data->direccion_fiscal,
                    'ubigeo'=> $data->ubigeo,
                    'id_pais'=> $data->id_pais,
                    'sis_pais_descripcion'=> $data->sis_pais_descripcion,
                    'fecha_registro'=> $data->fecha_registro,
                    'estado'=> $data->estado
                ];  
                $lastId = $data->id_contribuyente;
              } 
    };
    return response()->json($contribuyente);

    } catch(QueryException $e) {
        // return Response::json(['error' => 'Error msg'], 404); // Status code here
        return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
    }
}
// public function mostrar_contribuyente_data_contribuyente_rubro($id){
//         try {
//             $result = DB::table('adm_contri')
//             ->leftJoin('sis_pais', 'adm_contri.id_pais', '=', 'sis_pais.id_pais')
//             ->leftJoin('adm_ctb_rubro', 'adm_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
//                  ->leftJoin(DB::raw("(SELECT 
//                 adm_rubro.id_rubro,
//                 adm_rubro.descripcion
//                 FROM adm_rubro ) as rubro"),function($join){
//                 $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
//                 })
//              ->select(
//              'adm_contri.id_contribuyente', 
//              'adm_contri.razon_social', 
//              'rubro.id_rubro AS id_rubro',
//              'rubro.descripcion',
//              'adm_contri.estado'
//              )
//              ->where([
//                 ['adm_contri.id_contribuyente', '=', $id],
//                ['adm_contri.estado', '=', 1]
//                ])
//              ->get();

 
//     $lastId = "";
//         foreach($result as $data){
//             if ($data->id_contribuyente !== $lastId) {
//                 $contribuyente[] = [
//                     'id_contribuyente'=> $data->id_contribuyente,
//                     'razon_social'=> $data->razon_social,
//                     'id_rubro'=>$data->id_rubro,
//                     'descripcion'=>$data->descripcion,
//                     'estado'=> $data->estado
//                 ];  
//                 $lastId = $data->id_contribuyente;
//               } 
//     };
//     return response()->json($contribuyente);

//     } catch(QueryException $e) {
//         // return Response::json(['error' => 'Error msg'], 404); // Status code here
//         return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
//     }
// }

// public function mostrar_contribuyente_data_contribuyente_contacto($id){
//     try {
//         $result = DB::table('adm_contri')
//         ->leftJoin('sis_pais', 'adm_contri.id_pais', '=', 'sis_pais.id_pais')
//          ->leftJoin('adm_ctb_contac', 'adm_contri.id_contribuyente', '=', 'adm_ctb_contac.id_contribuyente')
//          ->select(
//           'adm_ctb_contac.id_contribuyente',
//          'adm_contri.razon_social', 
//         'adm_ctb_contac.id_datos_contacto', 
//         'adm_ctb_contac.nombre', 
//         'adm_ctb_contac.cargo',
//         'adm_ctb_contac.telefono', 
//         'adm_ctb_contac.email', 
//         'adm_ctb_contac.estado',
//         'adm_ctb_contac.fecha_registro'
//          )
//          ->where([
//             ['adm_ctb_contac.id_contribuyente', '=', $id],
//            ['adm_ctb_contac.estado', '=', 1]
//            ])
//          ->get();


//     $lastId = "";
//     foreach($result as $data){
//         if ($data->id_contribuyente !== $lastId) {
//             $contribuyente[] = [
//                 'id_contribuyente'=> $data->id_contribuyente,
//                 'razon_social'=> $data->razon_social,
//                 'id_datos_contacto'=>$data->id_datos_contacto,
//                 'nombre'=>$data->nombre,
//                 'cargo'=>$data->cargo,
//                 'telefono'=> $data->telefono,
//                 'email'=> $data->email,
//                 'estado'=> $data->estado,
//                 'fecha_registro'=> $data->fecha_registro
//             ];  
//             $lastId = $data->id_contribuyente;
//           } 
//     };
//     return response()->json($contribuyente);

//     } catch(QueryException $e) {
//         // return Response::json(['error' => 'Error msg'], 404); // Status code here
//         return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
//     }
// }

// public function mostrar_contribuyente($id)
//     {
//         try {
//             $result = DB::table('adm_contri')
//             ->leftJoin('adm_tp_contri', 'adm_contri.id_tipo_contribuyente', '=', 'adm_tp_contri.id_tipo_contribuyente')
//             ->leftJoin('sis_pais', 'adm_contri.id_pais', '=', 'sis_pais.id_pais')
//             ->leftJoin('adm_ctb_rubro', 'adm_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
//             ->leftJoin('adm_ctb_contac', 'adm_contri.id_contribuyente', '=', 'adm_ctb_contac.id_contribuyente')
//             ->leftJoin('sis_identi', 'adm_contri.id_doc_identidad', '=', 'sis_identi.id_doc_identidad')
//             ->leftJoin('adm_cta_contri', 'adm_contri.id_contribuyente', '=', 'adm_cta_contri.id_contribuyente')
//             ->leftJoin('adm_tp_cta', 'adm_cta_contri.id_tipo_cuenta', '=', 'adm_tp_cta.id_tipo_cuenta')
//             ->leftJoin('cont_banco', 'adm_cta_contri.id_banco', '=', 'cont_banco.id_banco')
//                  ->leftJoin(DB::raw("(SELECT 
//                 adm_contri.id_contribuyente,
//                 adm_contri.razon_social
//                 FROM adm_contri ) as banco"),function($join){
//                 $join->on("banco.id_contribuyente","=","cont_banco.id_contribuyente");
//                 })
//                  ->leftJoin(DB::raw("(SELECT 
//                 adm_rubro.id_rubro,
//                 adm_rubro.descripcion
//                 FROM adm_rubro ) as rubro"),function($join){
//                 $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
//                 })
//                  ->leftJoin(DB::raw("(SELECT 
//                 adm_tp_cta.id_tipo_cuenta,
//                 adm_tp_cta.descripcion
//                 FROM adm_tp_cta ) as tp_cuenta"),function($join){
//                 $join->on("tp_cuenta.id_tipo_cuenta","=","adm_cta_contri.id_tipo_cuenta");
//                 })
//              ->select(
//              'adm_contri.id_contribuyente', 
//              'adm_contri.id_tipo_contribuyente', 
//              'adm_tp_contri.descripcion AS adm_tip_contri_descripcion',
//              'adm_contri.razon_social', 
//              'adm_contri.nro_documento', 
//              'adm_contri.telefono', 
//              'adm_contri.celular', 
//              'adm_contri.direccion_fiscal', 
//              'adm_contri.ubigeo', 
//              'adm_contri.id_pais', 
//              'sis_pais.descripcion AS sis_pais_descripcion', 
//              'adm_contri.estado', 
//              'adm_contri.fecha_registro', 
//              'sis_identi.id_doc_identidad as sis_identi_id_doc_identidad', 
//              'sis_identi.descripcion as sis_identi_descripcion', 
//             'rubro.id_rubro AS id_rubro',
//              'rubro.descripcion AS rubro_descripcion',
//              'adm_ctb_contac.id_contribuyente as adm_ctb_contacto_id_contribuyente',
//              'adm_ctb_contac.nombre', 
//              'adm_ctb_contac.cargo',
//              'adm_ctb_contac.telefono AS adm_ctb_contac_telefono', 
//              'adm_ctb_contac.email', 
//              'adm_ctb_contac.estado AS estado_adm_ctb_contac',
//              'adm_ctb_contac.fecha_registro AS adm_ctb_contac_fecha_registro',
//              'adm_cta_contri.id_cuenta_contribuyente',
//              'adm_cta_contri.id_contribuyente AS adm_cta_contri_id_contribuyente',
//              'adm_cta_contri.id_banco',
//              'banco.razon_social AS banco_razon_social',
//              'cont_banco.codigo AS cont_banco_codigo',
//              'adm_cta_contri.id_tipo_cuenta',
//              'tp_cuenta.descripcion AS tp_cuenta_descripcion',
//              'adm_tp_cta.descripcion AS adm_tp_cta_descripcion',
//              'adm_cta_contri.nro_cuenta',
//              'adm_cta_contri.nro_cuenta_interbancaria',
//              'adm_cta_contri.fecha_registro AS adm_cta_contri_fecha_registro'
//              )
//              ->where('adm_contri.id_contribuyente', '=', $id)
//              ->get();


//          foreach($result as $data){
//             $contacto[]=[
//                 'id_contribuyente'=> $data->adm_ctb_contacto_id_contribuyente,
//                 'nombre'=> $data->nombre,
//                 'cargo'=> $data->cargo,
//                 'telefono'=> $data->adm_ctb_contac_telefono,
//                 'email'=> $data->email,
//                 'fecha_registro_contacto'=> $data->adm_ctb_contac_fecha_registro,              
//                 'estado'=> $data->estado_adm_ctb_contac
//             ];      
//         };

//         $lastId = "";
//          foreach($result as $data){
//             if ($data->adm_cta_contri_id_contribuyente !== $lastId) {
//             $cuenta[]=[
//                 'id_cuenta_contribuyente'=> $data->id_cuenta_contribuyente,
//                 'id_contribuyente'=> $data->adm_cta_contri_id_contribuyente,
//                 'id_banco'=> $data->id_banco,
//                 'banco_razon_social'=> $data->banco_razon_social,
//                 'cont_banco_codigo'=> $data->cont_banco_codigo,
//                  'id_tipo_cuenta'=> $data->id_tipo_cuenta,
//                  'tp_cuenta_descripcion'=> $data->tp_cuenta_descripcion,
//                 'nro_cuenta'=> $data->nro_cuenta,
//                 'nro_cuenta_interbancaria'=> $data->nro_cuenta_interbancaria,
//                 'adm_cta_contri_fecha_registro'=> $data->adm_cta_contri_fecha_registro             
//              ]; 
//              $lastId = $data->adm_cta_contri_id_contribuyente;

//             }      
//         };
        
    
//     $lastId = "";
//         foreach($result as $data){
//             if ($data->id_contribuyente !== $lastId) {
//                 $contribuyente[] = [
//                     'id_contribuyente'=> $data->id_contribuyente,
//                     'id_tipo_contribuyente'=> $data->id_tipo_contribuyente,
//                     'adm_tip_contri_descripcion'=>$data->adm_tip_contri_descripcion,
//                     'razon_social'=> $data->razon_social,
//                     'sis_identi_id_doc_identidad'=>$data->sis_identi_id_doc_identidad,
//                     'doc_identi'=>$data->sis_identi_descripcion,
//                     'nro_documento'=>$data->nro_documento,
//                     'id_rubro'=>$data->id_rubro,
//                     'rubro_descripcion'=>$data->rubro_descripcion,
//                     'telefono'=> $data->telefono,
//                     'celular'=> $data->celular,
//                     'direccion_fiscal'=> $data->direccion_fiscal,
//                     'ubigeo'=> $data->ubigeo,
//                     'id_pais'=> $data->id_pais,
//                     'sis_pais_descripcion'=> $data->sis_pais_descripcion,
//                     'fecha_registro'=> $data->fecha_registro,
//                     'estado'=> $data->estado
//                 ];  
//                 $lastId = $data->id_contribuyente;
//               } 
//     };
    
//      for($j=0; $j< sizeof($contacto);$j++){
//         for($i=0; $i< sizeof($contribuyente);$i++){
//             if($contacto[$j]['id_contribuyente'] === $contribuyente[$i]['id_contribuyente']){
//                 $contribuyente[$i]['contacto'][]=$contacto[$j];
//             }
//         }
//     }

//     for($j=0; $j< sizeof($cuenta);$j++){
//         for($i=0; $i< sizeof($contribuyente);$i++){
//             if($cuenta[$j]['id_contribuyente'] === $contribuyente[$i]['id_contribuyente']){
//                 $contribuyente[$i]['cuenta'][]=$cuenta[$j];
//             }
//         }
//     }

//     return response()->json($contribuyente);

//     } catch(QueryException $e) {
//         // return Response::json(['error' => 'Error msg'], 404); // Status code here
//         return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
//     }

// }
    public function guardar_contribuyente(Request $request){
        $data = DB::table('contabilidad.adm_contri')->insertGetId(
            [
            'id_tipo_contribuyente' => $request->id_tipo_contribuyente,
            'id_doc_identidad'      => $request->id_doc_identidad,
            'nro_documento'         => $request->nro_documento,
            'razon_social'          => $request->razon_social,
            'telefono'              => $request->telefono,
            'celular'               => $request->celular,
            'direccion_fiscal'      => $request->direccion_fiscal,
            'ubigeo'                => $request->ubigeo,
            'id_pais'               => $request->id_pais,
            'estado'                => $request->estado,
            'fecha_registro'        => $request->fecha_registro
            ],
            'id_contribuyente'
        );
        if($data > 0){
            $data2 = DB::table('contabilidad.adm_ctb_rubro')->insertGetId(
                [
                'id_contribuyente'      => $data,
                'id_rubro'              => $request->id_rubro,
                'fecha_registro'        => $request->fecha_registro
                ],
                'id_rubro_contribuyente'
            );
            return response()->json($data2);
        }
    }
    public function eliminar_contribuyente($id){
        //  $data = DB::table('adm_ctb_rubro')->where('id_contribuyente', '=', $id)->delete();
        // if($data >0 || $data ==""){
        //     $data2 = DB::table('adm_contri')->where('id_contribuyente', '=', $id)->delete();
        //     return response()->json($data2);
        // }
        $data = DB::table('contabilidad.adm_contri')->where('id_contribuyente', $id)
        ->update([
             'estado' => 0
        ]);
        return response()->json($data);

    }
    public function actualizar_contribuyente(Request $request, $id){
        $data = DB::table('contabilidad.adm_contri')->where('id_contribuyente', $id)
        ->update([
            'id_tipo_contribuyente' => $request->id_tipo_contribuyente,
            'id_doc_identidad'      => $request->id_doc_identidad,
            'nro_documento'         => $request->nro_documento,
            'razon_social'          => $request->razon_social,
            'telefono'              => $request->telefono,
            'celular'               => $request->celular,
            'direccion_fiscal'      => $request->direccion_fiscal,
            'ubigeo'                => $request->ubigeo,
            'id_pais'               => $request->id_pais,
            'estado'                => $request->estado,
            'fecha_registro'        => $request->fecha_registro
        ]);
        
        if($data > 0){
            $data2 = DB::table('contabilidad.adm_ctb_rubro')->where('id_contribuyente', $id)
            ->update([
                'id_rubro'              => $request->id_rubro,
                'fecha_registro'        => $request->fecha_registro
            ]); 
        }
        return response()->json($data);
    }
 
 
        public function mostrar_contribuyente_contactos_contribuyente($id)
        {
             $adm_ctb_contac = DB::table('contabilidad.adm_ctb_contac')
            ->join('adm_contri', 'adm_ctb_contac.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select(
                'adm_ctb_contac.id_datos_contacto',
                'adm_ctb_contac.id_contribuyente',
                'adm_contri.razon_social',
                'adm_ctb_contac.nombre',
                'adm_ctb_contac.cargo',
                'adm_ctb_contac.telefono',
                'adm_ctb_contac.email',
                'adm_ctb_contac.estado',
                // 'adm_ctb_contac.estado', 
                DB::raw("(CASE WHEN adm_ctb_contac.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS descripcion_estado")
                
                )
                ->where([
                    ['adm_contri.id_contribuyente', '=', $id],
                   ['adm_ctb_contac.estado', '=', 1]
                   ])
                ->orderBy('adm_ctb_contac.id_datos_contacto', 'asc')
            ->get();


            $data = ["adm_ctb_contac"=>$adm_ctb_contac];   

            return response()->json($data);
        }

 
        public function guardar_contribuyente_contacto(Request $request){
            $data = DB::table('contabilidad.adm_ctb_contac')->insertGetId(
                [
                'id_contribuyente' => $request->id_contribuyente,
                'nombre'      => $request->nombre,
                'telefono'         => $request->telefono,
                'email'          => $request->email,
                'estado'              => $request->estado,
                'fecha_registro'        => $request->fecha_registro,
                'cargo'              => $request->cargo
                
                ],
                'id_datos_contacto'
            );
            return response()->json($data);
        }

        public function eliminar_contribuyente_contacto($id){
            // $data = DB::table('adm_ctb_contac')->where('id_datos_contacto', '=', $id)->delete();
            // return response()->json($data);
            $data = DB::table('adm_ctb_contac')->where('id_datos_contacto', $id)
            ->update([
                'estado' => 0
            ]);
            return response()->json($data);
    
        }
        public function actualizar_contribuyente_contacto(Request $request, $id){
            $data = DB::table('contabilidad.adm_ctb_contac')->where('id_datos_contacto', $id)
            ->update([
                'id_contribuyente' => $request->id_contribuyente,
                'nombre'      => $request->nombre,
                'telefono'         => $request->telefono,
                'email'          => $request->email,
                'estado'                => $request->estado,
                'fecha_registro'        => $request->fecha_registro,
                'cargo'        => $request->cargo
            ]);
            return response()->json($data);
        }

 //
//  public function mostrar_cuenta_contribuyentes()
//  {
//      $data = cuenta_contribuyente::all();
//       return response()->json($data);

//  }
//  public function mostrar_cuenta_contribuyente($id)
//  {
//      try {
//      $data = cuenta_contribuyente::where('id_cuenta_contribuyente', $id)->first();
//      return response()->json($data);
//  } catch(QueryException $e) {
//      // return Response::json(['error' => 'Error msg'], 404); // Status code here
//      return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
//  }

//  }
    public function mostrar_contribuyente_data_contribuyente_cuenta($id){
        try {
            $result = DB::table('contabilidad.adm_contri')
            ->join('adm_cta_contri', 'adm_contri.id_contribuyente', '=', 'adm_cta_contri.id_contribuyente')
            ->leftJoin('adm_tp_cta', 'adm_cta_contri.id_tipo_cuenta', '=', 'adm_tp_cta.id_tipo_cuenta')
            ->leftJoin('cont_banco', 'adm_cta_contri.id_banco', '=', 'cont_banco.id_banco')
            ->leftJoin('adm_ctb_rubro', 'adm_cta_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
                    ->leftJoin(DB::raw("(SELECT 
                    adm_contri.id_contribuyente,
                    adm_contri.razon_social
                    FROM adm_contri ) as banco"),function($join){
                    $join->on("banco.id_contribuyente","=","cont_banco.id_contribuyente");
                    })
                    ->leftJoin(DB::raw("(SELECT 
                    adm_rubro.id_rubro,
                    adm_rubro.descripcion
                    FROM adm_rubro ) as rubro"),function($join){
                    $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
                    })
            
            ->select(
            //   'adm_contri.id_contribuyente',
            'adm_contri.razon_social', 
            'adm_cta_contri.id_contribuyente',
            'rubro.id_rubro',
            'rubro.descripcion AS descripcion_rubro',
                //  'adm_cta_contri.id_contribuyente AS id_contribuyente_cuenta',
                'adm_cta_contri.id_cuenta_contribuyente',
                'adm_cta_contri.id_banco',
                'banco.razon_social AS banco_razon_social',
                'cont_banco.codigo AS cont_banco_codigo',
                'adm_cta_contri.id_tipo_cuenta',
                'adm_tp_cta.descripcion',
                'adm_cta_contri.nro_cuenta',
                'adm_cta_contri.nro_cuenta_interbancaria',
                'adm_cta_contri.fecha_registro'
            )
            ->where([
                ['adm_contri.id_contribuyente', '=', $id],
            ['adm_cta_contri.estado', '=', 1]
            ])
            ->get();

        foreach($result as $data){
                $contribuyente[] = [
                    'id_contribuyente'=> $data->id_contribuyente,
                    // 'razon_social'=> $data->razon_social,
                    'id_rubro'=>$data->id_rubro,
                    'descripcion_rubro'=>$data->descripcion_rubro,
                    'id_cuenta_contribuyente'=> $data->id_cuenta_contribuyente,
                    'id_banco'=>$data->id_banco,
                    'banco_razon_social'=>$data->banco_razon_social,
                    'cont_banco_codigo'=>$data->cont_banco_codigo,
                    'id_tipo_cuenta'=> $data->id_tipo_cuenta,
                    'descripcion'=> $data->descripcion,
                    'nro_cuenta'=> $data->nro_cuenta,
                    'nro_cuenta_interbancaria'=> $data->nro_cuenta_interbancaria,
                    'fecha_registro'=> $data->fecha_registro
                ];  
            
        };
    
        return response()->json(["adm_contri"=>$result]);

        } catch(QueryException $e) {
            // return Response::json(['error' => 'Error msg'], 404); // Status code here
            return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
        }
    }

    public function guardar_cuenta_contribuyente(Request $request){
        $data = DB::table('contabilidad.adm_cta_contri')->insertGetId(
            [
            'id_contribuyente'  => $request->id_contribuyente,
            'id_banco'          => $request->id_banco,
            'id_tipo_cuenta'    => $request->id_tipo_cuenta,
            'nro_cuenta'        => $request->nro_cuenta,
            'nro_cuenta_interbancaria'  => $request->nro_cuenta_interbancaria,
            'fecha_registro'            => $request->fecha_registro

            ],
            'id_cuenta_contribuyente'
        );
        return response()->json($data);

    }
    public function actualizar_cuenta_contribuyente(Request $request, $id){
        $data = DB::table('contabilidad.adm_cta_contri')->where('id_cuenta_contribuyente', $id)
        ->update([
            'id_contribuyente'  => $request->id_contribuyente,
            'id_banco'          => $request->id_banco,
            'id_tipo_cuenta'    => $request->id_tipo_cuenta,
            'nro_cuenta'        => $request->nro_cuenta,
            'nro_cuenta_interbancaria'  => $request->nro_cuenta_interbancaria,
            'fecha_registro'            => $request->fecha_registro
        ]);
        return response()->json($data);
    }

    public function eliminar_cuenta_contribuyente($id){
        //  $data = DB::table('adm_ctb_contac')->where('id_cuenta_contribuyente', '=', $id)->delete();
        //  return response()->json($data);
        $data = DB::table('contabilidad.adm_cta_contri')->where('id_cuenta_contribuyente', $id)
        ->update([
            'estado'  => 0
        ]);
        return response()->json($data);
    }


 

    public function mostrar_documentos()
    {
        $sis_identi = DB::table('contabilidad.sis_identi')
        ->select(
        'sis_identi.id_doc_identidad',
        'sis_identi.descripcion',
        'sis_identi.longitud',
        'sis_identi.estado',
        DB::raw("(CASE WHEN sis_identi.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS descripcion_estado")

        )
        ->where([
    
            ['sis_identi.estado', '=', 1]
            ])
        ->orderBy('sis_identi.id_doc_identidad', 'asc')
    ->get();
    return response()->json($sis_identi);

    }
    public function mostrar_doc_idendidad($id)
    {
        $sis_identi = DB::table('contabilidad.sis_identi')
        ->select(
            'sis_identi.id_doc_identidad',
            'sis_identi.descripcion',
            'sis_identi.longitud',
            'sis_identi.estado'
            )
            ->where([
                ['sis_identi.id_doc_identidad', '=', $id],
                ['sis_identi.estado', '=', 1]
            ])
            ->orderBy('sis_identi.id_doc_identidad', 'asc')
        ->get();
        return response()->json(["sis_identi"=>$sis_identi]);

    }
    public function guardar_documento(Request $request){
        $data = DB::table('contabilidad.sis_identi')->insertGetId(
            [
            'descripcion'=> $request->descripcion,
            'longitud'   => $request->longitud,
            'estado'     => $request->estado
            ],
            'id_doc_identidad'
        );
        return response()->json($data);

    }
    public function eliminar_documento($id){
        // $data = DB::table('contabilidad.sis_identi')->where('id_doc_identidad', '=', $id)->delete();
        // return response()->json($data);
        $data = DB::table('contabilidad.sis_identi')->where('id_doc_identidad', $id)
        ->update([
    
            'estado'     => 0
        ]);

    }
    public function actualizar_documento(Request $request, $id){
        $data = DB::table('contabilidad.sis_identi')->where('id_doc_identidad', $id)
        ->update([
            'descripcion' => $request->descripcion,
            'longitud'    => $request->longitud,
            'estado'     => $request->estado
        ]);
        return response()->json($data);
    }

    //  facturación

    public function get_estado_doc($nombreEstadoDoc){
        $estado_doc =  DB::table('administracion.adm_estado_doc')
        ->where('estado_doc', $nombreEstadoDoc)
        ->get();
        if($estado_doc->count()>0){
            $id_estado_doc=  $estado_doc->first()->id_estado_doc;
        }else{
            $id_estado_doc =0;
        }

        return $id_estado_doc;
    }


    function view_comprobante_compra(){
        $empresas = $this->select_mostrar_empresas();
        $monedas = $this->mostrar_moneda();

        return view('contabilidad/comprobante_compra', compact('empresas', 'monedas'));
    }

    function mostrar_moneda()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select(
                'sis_moneda.id_moneda',
                'sis_moneda.descripcion',
                'sis_moneda.simbolo',
                'sis_moneda.estado',
                DB::raw("(CASE WHEN configuracion.sis_moneda.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['sis_moneda.estado', '=', 1]
            ])
            ->orderBy('sis_moneda.id_moneda', 'asc')
            ->get();
        return $data;
    }

    public function select_mostrar_empresas()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_empresa.logo_empresa','adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }

    public function ordenes_sin_facturar($id_empresa,$all_o_id){
        $id_estado_elaborado= $this->get_estado_doc('Elaborado');

        $condicion = [  
            ['log_ord_compra.estado', '=', $id_estado_elaborado],
            ['comprobante_orden.id_comprobante_orden', '=', null]
        ];

        if($id_empresa > 0){
            $condicion[] = ['log_cotizacion.id_empresa', '=', $id_empresa];
        }

        if($all_o_id > 0){
            $condicion[] =  ['log_ord_compra.id_orden_compra', '=', $all_o_id];
        }

        $orden = DB::table('logistica.log_ord_compra')
        ->select(
            'log_ord_compra.id_orden_compra',
            'log_ord_compra.fecha',
            'log_ord_compra.codigo',
            'log_ord_compra.id_proveedor',
            DB::raw("CONCAT(adm_contri_prov.razon_social,' ',sis_identi_prov.descripcion,':',adm_contri_prov.nro_documento) as proveedor"),
            'log_ord_compra.id_moneda',
            'sis_moneda.descripcion as moneda',
            'log_ord_compra.monto_subtotal',
            'log_ord_compra.monto_igv',
            'log_ord_compra.monto_total',
            'log_ord_compra.id_condicion',
            'log_ord_compra.plazo_dias',
            DB::raw("CONCAT(log_cdn_pago.descripcion,' ',log_ord_compra.plazo_dias) as condicion"),
            'log_ord_compra.plazo_entrega',
            'log_cotizacion.id_empresa',
            'log_ord_compra.id_sede',
            DB::raw("CONCAT(adm_contri_empr.razon_social,' ',sis_identi_empr.descripcion,':',adm_contri_empr.nro_documento) as empresa")
        )
        ->join('logistica.log_cotizacion', 'log_ord_compra.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
        ->join('administracion.adm_empresa', 'log_cotizacion.id_empresa', '=', 'adm_empresa.id_empresa')
        ->join('logistica.log_prove', 'log_ord_compra.id_proveedor', '=', 'log_prove.id_proveedor')
        ->join('contabilidad.adm_contri as adm_contri_prov', 'log_prove.id_contribuyente', '=', 'adm_contri_prov.id_contribuyente')
        ->join('contabilidad.adm_contri as adm_contri_empr', 'adm_empresa.id_contribuyente', '=', 'adm_contri_empr.id_contribuyente')
        ->join('contabilidad.sis_identi as sis_identi_prov', 'adm_contri_prov.id_doc_identidad', '=', 'sis_identi_prov.id_doc_identidad')
        ->join('contabilidad.sis_identi as sis_identi_empr', 'adm_contri_empr.id_doc_identidad', '=', 'sis_identi_empr.id_doc_identidad')
        ->join('configuracion.sis_moneda', 'log_ord_compra.id_moneda', '=', 'sis_moneda.id_moneda')
        ->join('logistica.log_cdn_pago', 'log_ord_compra.id_condicion', '=', 'log_cdn_pago.id_condicion_pago')
        ->leftJoin('logistica.comprobante_orden', 'log_ord_compra.id_orden_compra', '=', 'comprobante_orden.id_orden')
        ->where($condicion)
        ->orderBy('log_ord_compra.id_orden_compra', 'asc')
        ->get();
        // return $orden;
        $detalle_orden = DB::table('logistica.log_det_ord_compra')
        ->select(
            'log_det_ord_compra.*',
            'alm_item.codigo',
            DB::raw("(CASE 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
            ELSE 'nulo' END) AS descripcion_item
            "),
            'log_valorizacion_cotizacion.precio_cotizado',
            'log_valorizacion_cotizacion.cantidad_cotizada',
            'log_valorizacion_cotizacion.subtotal',
            'log_valorizacion_cotizacion.flete',
            'log_valorizacion_cotizacion.porcentaje_descuento',
            'log_valorizacion_cotizacion.monto_descuento',
            'log_valorizacion_cotizacion.incluye_igv',
            'log_valorizacion_cotizacion.id_unidad_medida',
            'alm_und_medida.descripcion as unidad_medida',
            'log_valorizacion_cotizacion.precio_sin_igv',
            'log_valorizacion_cotizacion.igv'
        )
        ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_det_ord_compra.id_valorizacion_cotizacion', '=', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion')
        ->leftJoin('almacen.alm_und_medida', 'log_valorizacion_cotizacion.id_unidad_medida', '=', 'alm_und_medida.id_unidad_medida')
        ->leftJoin('almacen.alm_item', 'log_det_ord_compra.id_item', '=', 'alm_item.id_item')
        ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
        ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')

        ->where([
            ['log_det_ord_compra.estado', '=', $id_estado_elaborado]
        ])
        ->orderBy('log_det_ord_compra.id_detalle_orden', 'asc')
        ->get();

        $ordenList=[];
        foreach($orden as $data){
            $ordenList[]=[
                'id_orden_compra'=>$data->id_orden_compra,
                'fecha'=>$data->fecha,
                'codigo'=>$data->codigo,
                'id_proveedor'=>$data->id_proveedor,
                'proveedor'=>$data->proveedor,
                'id_moneda'=>$data->id_moneda,
                'moneda'=>$data->moneda,
                'monto_subtotal'=>$data->monto_subtotal,
                'monto_igv'=>$data->monto_igv,
                'monto_total'=>$data->monto_total,
                'id_condicion'=>$data->id_condicion,
                'plazo_dias'=>$data->plazo_dias,
                'condicion'=>$data->condicion,
                'plazo_entrega'=>$data->plazo_entrega,
                'id_empresa'=>$data->id_empresa,
                'id_sede'=>$data->id_sede,
                'empresa'=>$data->empresa,
                'detalle_orden'=>[]
            ];
        }
        $detalleOrdenList=[];
        foreach($detalle_orden as $data){
            $detalleOrdenList[]=[
                'id_detalle_orden'=>$data->id_detalle_orden,
                'id_orden_compra'=>$data->id_orden_compra,
                'codigo'=>$data->codigo,
                'id_item'=>$data->id_item,
                'descripcion_item'=>$data->descripcion_item,
                'precio_cotizado'=>$data->precio_cotizado,
                'cantidad_cotizada'=>$data->cantidad_cotizada,
                'subtotal'=>$data->subtotal,
                'flete'=>$data->flete,
                'porcentaje_descuento'=>$data->porcentaje_descuento?$data->porcentaje_descuento:0,
                'monto_descuento'=>$data->monto_descuento?$data->monto_descuento:0,
                'incluye_igv'=>$data->incluye_igv,
                'id_unidad_medida'=>$data->id_unidad_medida,
                'unidad_medida'=>$data->unidad_medida,
                'precio_sin_igv'=>$data->precio_sin_igv,
                'igv'=>$data->igv,
                'garantia'=>$data->garantia,
                'id_valorizacion_cotizacion'=>$data->id_valorizacion_cotizacion,
                'estado'=>$data->estado,
                'personal_autorizado'=>$data->personal_autorizado,
                'lugar_despacho'=>$data->lugar_despacho,
                'descripcion_adicional'=>$data->descripcion_adicional
            ];
        }

        foreach($ordenList as $keyOrden => $dataOrden){
            foreach($detalleOrdenList as $keyDetOrden => $dataDetOrden){
                if($dataOrden['id_orden_compra'] == $dataDetOrden['id_orden_compra']){
                    $ordenList[$keyOrden]['detalle_orden'][] = $dataDetOrden;
                }
            }
        }

        return response()->json($ordenList);
    }

    function get_tipo_doc($nombreTipoDoc){
        $tp_doc =  DB::table('administracion.adm_tp_docum')
        ->where('descripcion', $nombreTipoDoc)
        ->get();
        if($tp_doc->count()>0){
            $id_tp_documento=  $tp_doc->first()->id_tp_documento;
        }else{
            $id_tp_documento =0;
        }

        return $id_tp_documento;
    }

    function guardar_comprobante_compra(Request $request){
    
        $detalle_orden = $request->detalle_orden;
        $id_usuario = $request->id_usuario;
        $id_orden = $request->id_orden;
        $id_sede = $request->id_sede;
        $proveedor = $request-> proveedor; 
        $id_proveedor = $request->  id_proveedor; 
        $serie = $request->serie;
        $fecha_emision = $request->  fecha_emision; 
        $fecha_vencimiento = $request->  fecha_vencimiento; 
        $id_condicion = $request->  id_condicion; 
        $plazo_dias = $request->  plazo_dias; 
        $id_moneda = $request->  id_moneda; 
        $monto_subtotal = $request->  monto_subtotal; 
        $monto_igv = $request->  monto_igv; 
        $monto_total = $request->  monto_total;
        $monto_descuento = $request->  monto_descuento;
        $porcentaje_descuento = $request->  porcentaje_descuento;
        $id_estado_elaborado= $this->get_estado_doc('Elaborado');
        $numero = $this->nextNumeroFactura($fecha_emision);
        $hoy = date('Y-m-d H:i:s');
        $id_tp_doc= $this->get_tipo_doc('Comprobante de Compra');

        $ouput=[];
        $status=0;
        $insertarDocCom = DB::table('almacen.doc_com')->insertGetId(
            [
                'serie' => $serie,
                'numero' => $numero,
                'id_tp_doc' => $id_tp_doc,
                'id_proveedor' => $id_proveedor,
                'fecha_emision' => $fecha_emision,
                'fecha_vcmto' => $fecha_vencimiento,
                'id_condicion' => $id_condicion,
                'credito_dias' => $plazo_dias,
                'moneda' => $id_moneda,
                // 'tipo_cambio' => $tipo_cambio,
                'sub_total' => $monto_subtotal,
                'total' => $monto_total,
                'total_igv' => $monto_igv,
                // 'total_ant_igv' => $total_ant_igv,
                // 'total_a_pagar' => $total_a_pagar,
                'total_descuento' => $monto_descuento,
                'porcen_descuento' => $porcentaje_descuento,
                'usuario' => $id_usuario,
                'estado' => $id_estado_elaborado,
                'fecha_registro' => $hoy,
                // 'porcen_igv' => $porcen_igv,
                // 'porcen_anticipo' => $porcen_anticipo,
                // 'total_otros' => $total_otros,
                // 'registrado_por' => $registrado_por,
                'id_sede' => $id_sede
            ],
            'id_doc_com'
        );

        if($insertarDocCom>0){
            $status =200;

            foreach($detalle_orden as $data){
                $insertarDocComDet = DB::table('almacen.doc_com_det')->insertGetId(
                    [
                        'id_doc'=> $insertarDocCom,
                        'id_item'=> $data['id_item'],
                        'cantidad'=> $data['cantidad_cotizada'],
                        'id_unid_med'=> $data['id_unidad_medida'],
                        'precio_unitario'=> $data['precio_cotizado'],
                        'sub_total'=> $data['precio_sin_igv'],
                        'porcen_dscto'=> $data['porcentaje_descuento'],
                        'total_dscto'=> $data['monto_descuento'],
                        'precio_total'=> $data['subtotal'],
                        // 'id_guia_com_det'=> $data['id_guia_com_det'],
                        'estado'=> $id_estado_elaborado,
                        'fecha_registro'=> $hoy
                        // 'obs'=> $obs
                    ],
                    'id_doc_det'
                );
            }

            $insertarComprobanteOrden = DB::table('logistica.comprobante_orden')->insertGetId(
                [
                    'id_doc_com'=> $insertarDocCom,
                    'id_orden'=> $id_orden,
                    'estado'=> $id_estado_elaborado,
                    'fecha_registro'=> $hoy
                ],
                'id_comprobante_orden'
            );

            if($insertarComprobanteOrden > 0){
                $status = 200;
            }else{
                $status = 500;
            }


        }else{
            $status = 500;
        }
        $ouput=['status'=>$status];
        return response()->json($ouput);
    }


    function nextNumeroFactura($fecha_emision){
        $yyyy = date('Y',strtotime($fecha_emision));

        $data = DB::table('almacen.doc_com')
        ->where([['estado','=',1]])
        ->whereYear('fecha_emision','=',$yyyy)
        ->count();

        $correlativo = $this->leftZero(4, $data+1);
        
        $numero = $correlativo;

        return $numero;
    }

    public function leftZero($lenght, $number){
        $nLen = strlen($number);
        $zeros = '';
        for($i=0; $i<($lenght-$nLen); $i++){
            $zeros = $zeros.'0';
        }
        return $zeros.$number;
    }


    public function lista_comprobante_compra($id_sede, $all_o_id){

        $id_estado_elaborado= $this->get_estado_doc('Elaborado');
        $id_tipo_doc= $this->get_tipo_doc('Comprobante de Compra');

        $condicion = [  
            ['doc_com.estado', '=', $id_estado_elaborado],
            ['doc_com.id_tp_doc', '=', $id_tipo_doc]
        ];

        if($id_sede > 0){
            $condicion[] = ['doc_com.id_sede', '=', $id_sede];
        }

        if($all_o_id > 0){
            $condicion[] =  ['doc_com.id_doc_com', '=', $all_o_id];
        }

        $doc_com = DB::table('almacen.doc_com')
        ->select(
            'doc_com.id_doc_com',
            'doc_com.serie',
            'doc_com.numero',
            DB::raw("CONCAT(doc_com.serie,'-',doc_com.numero) as codigo"),
            'doc_com.id_tp_doc',
            'adm_tp_docum.descripcion as tipo_documento',
            'doc_com.id_proveedor',
            DB::raw("CONCAT(adm_contri_prov.razon_social,' ',sis_identi_prov.descripcion,':',adm_contri_prov.nro_documento) as proveedor"),
            'doc_com.fecha_emision',
            'doc_com.fecha_vcmto',
            'doc_com.id_condicion',
            'doc_com.credito_dias',
            DB::raw("CONCAT(log_cdn_pago.descripcion,' ',doc_com.credito_dias) as condicion"),
            'doc_com.moneda as id_moneda',
            'sis_moneda.descripcion as moneda',
            'doc_com.tipo_cambio',
            'doc_com.sub_total',
            'doc_com.porcen_descuento',
            'doc_com.total_descuento',
            'doc_com.total_igv',
            'doc_com.total',
            'doc_com.total_a_pagar',
            'doc_com.usuario',
            'doc_com.fecha_registro',
            'doc_com.registrado_por',
            'doc_com.id_sede',
            'sis_sede.codigo as sede',
            'sis_sede.id_empresa',
            DB::raw("CONCAT(adm_contri_empr.razon_social,' ',sis_identi_empr.descripcion,':',adm_contri_empr.nro_documento) as empresa")

        )
        ->leftJoin('logistica.log_prove', 'doc_com.id_proveedor', '=', 'log_prove.id_proveedor')
        ->leftJoin('contabilidad.adm_contri as adm_contri_prov', 'log_prove.id_contribuyente', '=', 'adm_contri_prov.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi as sis_identi_prov', 'adm_contri_prov.id_doc_identidad', '=', 'sis_identi_prov.id_doc_identidad')
        ->leftJoin('logistica.log_cdn_pago', 'doc_com.id_condicion', '=', 'log_cdn_pago.id_condicion_pago')
        ->leftJoin('configuracion.sis_moneda', 'doc_com.moneda', '=', 'sis_moneda.id_moneda')
        ->leftJoin('administracion.adm_tp_docum', 'doc_com.id_tp_doc', '=', 'adm_tp_docum.id_tp_documento')
        ->leftJoin('administracion.sis_sede', 'doc_com.id_sede', '=', 'sis_sede.id_sede')
        ->leftJoin('administracion.adm_empresa', 'sis_sede.id_empresa', '=', 'adm_empresa.id_empresa')
        ->leftJoin('contabilidad.adm_contri as adm_contri_empr', 'adm_empresa.id_contribuyente', '=', 'adm_contri_empr.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi as sis_identi_empr', 'adm_contri_empr.id_doc_identidad', '=', 'sis_identi_empr.id_doc_identidad')
        ->where($condicion)
        ->orderBy('doc_com.id_doc_com', 'asc')
        ->get();

        // return response()->json(['data'=>$doc_com]);
        return response()->json($doc_com);

    }


}



