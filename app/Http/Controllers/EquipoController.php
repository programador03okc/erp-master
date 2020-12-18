<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set('America/Lima');

class EquipoController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_equi_tipo(){
        return view('equipo/equi_tipo');
    }
    function view_equi_cat(){
        $tipos = $this->mostrar_equi_tipos_cbo();
        return view('equipo/equi_cat', compact('tipos'));
    }
    function view_equipo(){
        $categorias = $this->mostrar_equi_cats_cbo();
        $propietarios = $this->mostrar_propietarios_cbo();
        return view('equipo/equipo', compact('categorias','propietarios'));
    }
    function view_equi_catalogo(){
        $categorias = $this->mostrar_equi_cats_cbo();
        $propietarios = $this->mostrar_propietarios_cbo();
        $tp_combustible = $this->mostrar_tp_combustible_cbo();
        $tp_seguro = $this->mostrar_tp_seguro_cbo();
        $proveedores = $this->mostrar_proveedores_cbo();
        $unid_program = $this->select_unidades_prog();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = $this->sis_identidad_cbo();
        return view('equipo/equi_catalogo', compact('categorias','propietarios','tp_combustible','tp_seguro','proveedores','unid_program','tp_contribuyente','sis_identidad'));
    }
    function view_tp_combustible(){
        return view('equipo/tp_combustible');
    }
    function view_mtto_pendientes(){
        return view('equipo/mtto_pendientes');
    }
    function view_docs(){
        return view('equipo/docs');
    }
    function view_mtto(){
        $proveedores = $this->mostrar_proveedores_cbo();
        $equipos = $this->mostrar_equipo_cbo();
        $empresa = $this->select_empresa();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = $this->sis_identidad_cbo();
        return view('equipo/mtto', compact('proveedores','equipos','empresa','tp_contribuyente','sis_identidad'));
    }
    function view_equi_sol(){
        $trabajadores = $this->mostrar_trabajadores_cbo();
        $categorias = $this->mostrar_equi_cats_cbo();
        $proyectos = $this->mostrar_proyectos_cbo();
        $empresa = $this->select_empresa();
        return view('equipo/equi_sol', compact('trabajadores','empresa','proyectos','categorias'));
    }
    function view_sol_todas(){
        return view('equipo/sol_todas');
    }
    function view_aprob_sol(){
        return view('equipo/aprob_sol');
    }
    function view_asignacion(){
        return view('equipo/asignacion');
    }
    function view_control(){
        $trabajadores = $this->mostrar_trabajadores_cbo();
        return view('equipo/control', compact('trabajadores'));
    }
    function view_mtto_realizados(){
        $equipos = $this->mostrar_equipo_cbo();
        return view('equipo/mtto_realizados', compact('equipos'));
    }
    /* Combos */
    public function tp_contribuyente_cbo(){
        $data = DB::table('contabilidad.adm_tp_contri')
            ->select('adm_tp_contri.id_tipo_contribuyente', 'adm_tp_contri.descripcion')
            ->where('adm_tp_contri.estado', '=', 1)
            ->orderBy('adm_tp_contri.descripcion', 'asc')->get();
        return $data;
    }
    public function sis_identidad_cbo(){
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }
    public function select_empresa(){
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('adm_empresa.id_empresa', 'adm_contri.razon_social')->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')->get();
        return $data;
    }
    public function mostrar_proyectos_cbo(){
        $data = DB::table('proyectos.proy_proyecto')
            ->select('proy_proyecto.id_proyecto','proy_proyecto.descripcion')
            ->where('proy_proyecto.estado', '=', 1)//Revisar si tiene que estar aprobado
            ->get();
        return $data;
    }
    public function mostrar_proyecto_cbo(){
        $data = DB::table('proyectos.proy_contrato')
            ->select('proy_proyecto.id_proyecto','proy_proyecto.descripcion')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            ->where('proy_contrato.estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_trabajadores_cbo(){
        $data = DB::table('rrhh.rrhh_trab')
            ->select('rrhh_trab.id_trabajador',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where('rrhh_trab.estado', '=', 1)
            ->orderBy('nombre_trabajador')
            ->get();
        return $data;
    }
    public function mostrar_equipo_cbo(){
        $data = DB::table('logistica.equipo')
            ->select('equipo.id_equipo','equipo.codigo','equipo.descripcion')
            ->where('equipo.estado', '=', 1)
            ->orderBy('equipo.codigo','asc')
            ->get();
        return $data;
    }
    public function mostrar_unid_program_cbo(){
        $data = DB::table('proyectos.proy_unid_program')
            ->select('proy_unid_program.id_unid_program','proy_unid_program.descripcion')
            ->where('estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_tp_combustible_cbo(){
        $data = DB::table('logistica.tp_combustible')
            ->select('tp_combustible.id_tp_combustible','tp_combustible.descripcion')
            ->where('estado', '=', 1)
                ->orderBy('tp_combustible.codigo','asc')->get();
        return $data;
    }
    public function mostrar_tp_seguro_cbo(){
        $data = DB::table('logistica.equi_tp_seguro')
            ->select('equi_tp_seguro.id_tp_seguro','equi_tp_seguro.descripcion')
            ->where('estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_sedes_cbo(){
        $data = DB::table('administracion.sis_sede')
            ->select('sis_sede.*')
            ->where([['sis_sede.estado', '=', 1]])
                ->orderBy('id_sede')
                ->get();
        return $data;
    }
    public function mostrar_proveedores_cbo()
    {
        $data = DB::table('logistica.log_prove')
            ->select('log_prove.id_proveedor','adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['log_prove.estado', '=', 1]])
                ->orderBy('adm_contri.nro_documento')
                ->get();
        return $data;
    }
    public function mostrar_moneda_cbo()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda','sis_moneda.simbolo','sis_moneda.descripcion')
            ->where([['sis_moneda.estado', '=', 1]])
            ->orderBy('sis_moneda.id_moneda')
            ->get();
        return $data;
    }
    public function mostrar_equi_tipos_cbo(){
        $data = DB::table('logistica.equi_tipo')
            ->select('equi_tipo.id_tipo','equi_tipo.codigo','equi_tipo.descripcion')
            ->where([['estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_equi_cats_cbo(){
        $data = DB::table('logistica.equi_cat')
            ->select('equi_cat.id_categoria','equi_cat.codigo','equi_cat.descripcion')
            ->where([['estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_propietarios_cbo(){
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa','adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->where([['adm_empresa.estado', '=', 1]])
            ->get();
        return $data;
    }
    ////////////////////////////////
    public function select_unidades_prog(){
        $data = DB::table('proyectos.proy_unid_program')
        ->select('proy_unid_program.id_unid_program','proy_unid_program.descripcion')
        ->where([['estado', '=', 1],
                ['id_unid_program', '>=', 4]])
        ->orderBy('id_unid_program', 'asc')->get();
        return $data;
    }
    public function select_programaciones($id_equipo){
        $data = DB::table('logistica.mtto_programacion')
        ->select('mtto_programacion.id_programacion','mtto_programacion.descripcion')
        ->where([['mtto_programacion.estado', '=', 1],
                ['mtto_programacion.id_equipo','=',$id_equipo]])
            ->orderBy('mtto_programacion.descripcion', 'asc')->get();
        $html = '<option value="0" disabled>Elija una opción</option>';
        foreach($data as $d){
            $html.='<option value="'.$d->id_programacion.'">'.$d->descripcion.'</option>';
        }
        return json_encode($html);
    }
    public function mostrar_equipos(){
        $data = DB::table('logistica.equipo')
            ->select('equipo.*','equi_cat.descripcion as cat_descripcion',
            'equi_tipo.descripcion as tipo_descripcion','adm_contri.razon_social',
            'tp_combustible.descripcion as des_tp_combustible')
            ->join('logistica.equi_cat','equi_cat.id_categoria','=','equipo.id_categoria')
            ->join('logistica.equi_tipo','equi_tipo.id_tipo','=','equi_cat.id_tipo')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','equipo.propietario')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->leftjoin('logistica.tp_combustible','tp_combustible.id_tp_combustible','=','equipo.tp_combustible')
            ->where([['equipo.estado','=',1]])
            ->get();
        
        $hoy = date('Y-m-d');
        $masunmes = date("Y-m-d",strtotime($hoy."+ 1 month"));
        $nueva_data = [];

        foreach($data as $d){
            $warning_docs = "false";
            $warning_mtto = "false";

            $tipos = DB::table('logistica.equi_seguro')
            ->where([['id_equipo', '=', $d->id_equipo],
                    ['estado', '=', 1]])
                    ->distinct('id_tp_seguro')->count('id_tp_seguro');
            // ->count(DB::raw('DISTINCT id_tp_seguro'));

            $activos = DB::table('logistica.equi_seguro')
            ->where([['id_equipo', '=', $d->id_equipo],
                    ['estado', '=', 1],
                    ['fecha_fin','>',$masunmes]])
                    ->count();
            if ($tipos > $activos){
                $warning_docs = "true";
            }

            $prog = DB::table('logistica.mtto_programacion')
            ->where([['id_equipo','=',$d->id_equipo],['estado','=',1]])
            ->get();
            $kactual = $this->kilometraje_actual($d->id_equipo);

            foreach($prog as $p){
                if ($warning_mtto == "false"){
                    $mtto_ult = DB::table('logistica.mtto_det')
                    ->select('mtto.fecha_mtto','mtto.kilometraje')
                    ->join('logistica.mtto','mtto.id_mtto','=','mtto_det.id_mtto')
                    ->where([['mtto_det.id_programacion','=',$p->id_programacion],
                            ['mtto_det.estado','=',1]])
                    ->orderBy('mtto.fecha_mtto','desc')
                    ->first();
    
                    if ($mtto_ult !== null){
                        if ($p->kilometraje_rango !== null){
                            $kil = $mtto_ult->kilometraje + $p->kilometraje_rango;
                            $warning_mtto = ($kil > $kactual ? "true" : "false");
                        } 
                        else if ($p->tiempo !== null){
                            $unid = ($p->unid_program == 4 ? "month" : "year");
                            $fecha = date("Y-m-d",strtotime($mtto_ult->fecha_mtto."+ ".$p->tiempo." ".$unid));
                            $warning_mtto = ($fecha < $hoy ? "true" : "false");
                        }
                    }
                }
            }
            
            $nueva = [
                'id_equipo'=>$d->id_equipo,
                'tipo_descripcion'=>$d->tipo_descripcion,
                'cat_descripcion'=>$d->cat_descripcion,
                'codigo'=>$d->codigo,
                'descripcion'=>$d->descripcion,
                'razon_social'=>$d->razon_social,
                'placa'=>$d->placa,
                'modelo'=>$d->modelo,
                'des_tp_combustible'=>$d->des_tp_combustible,
                'warning_docs'=>$warning_docs,
                'warning_mtto'=>$warning_mtto,
                'tp'=>$tipos,
                'activos'=>$activos,
                'id_categoria'=>$d->id_categoria,
                'propietario'=>$d->propietario,
                'marca'=>$d->marca,
                'cod_tarj_propiedad'=>$d->cod_tarj_propiedad,
                'serie'=>$d->serie,
                'anio_fabricacion'=>$d->anio_fabricacion,
                'tp_combustible'=>$d->tp_combustible,
                'caracteristicas_adic'=>$d->caracteristicas_adic,
                'kilometraje_inicial'=>$d->kilometraje_inicial,
            ];
            array_push($nueva_data, $nueva);
        }
        $output['data'] = $nueva_data;
        return response()->json($output);
    }
    public function mostrar_equipo($id){
        $data = DB::table('logistica.equipo')
            ->select('equipo.*','equi_cat.codigo as cod_cat',
            'equi_cat.descripcion as cat_descripcion',
            'equi_tipo.codigo as cod_tipo',
            'equi_tipo.descripcion as tipo_descripcion')
            ->join('logistica.equi_cat','equi_cat.id_categoria','=','equipo.id_categoria')
            ->join('logistica.equi_tipo','equi_tipo.id_tipo','=','equi_cat.id_tipo')
            ->where([['equipo.id_equipo', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function next_correlativo_equipo($id_categoria)
    {
        $cantidad = DB::table('logistica.equipo')
            ->where([['id_categoria', '=', $id_categoria],
                    ['estado','=',1]])
            ->get()->count();
        $cat = DB::table('logistica.equi_cat')
            ->where('id_categoria', $id_categoria)->first();
        // $tipo = DB::table('logistica.equi_tipo')->select('codigo')
        //     ->where('id_tipo', $cat->id_tipo)->first();
        $equipo = $this->leftZero(4,$cantidad+1);
        $nextId = $cat->codigo."".$equipo;
        return $nextId;
    }
    public function guardar_equipo(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_correlativo_equipo($request->id_categoria);
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('logistica.equipo')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_equipo = DB::table('logistica.equipo')->insertGetId(
                [
                    'id_categoria' => $request->id_categoria,
                    'codigo' => $codigo,
                    'descripcion' => $des,
                    'propietario' => $request->propietario,
                    'tp_combustible' => $request->tp_combustible,
                    'cod_tarj_propiedad' => $request->cod_tarj_propiedad,
                    'placa' => $request->placa,
                    'serie' => $request->serie,
                    'marca' => $request->marca,
                    'modelo' => $request->modelo,
                    'motor' => $request->motor,
                    'kilometraje_inicial' => $request->kilometraje_inicial,
                    'anio_fabricacion' => $request->anio_fabricacion,
                    'caracteristicas_adic' => $request->caracteristicas_adic,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_equipo'
                );
    
            $id_item = DB::table('almacen.alm_item')->insertGetId(
                [   'codigo' => $codigo,
                    'fecha_registro' => $fecha,
                    'id_equipo' => $id_equipo,
                ],  
                    'id_item'
            );
        } else {
            $msj = 'No puede guardar. Ya existe un equipo con dicha descripción.';
        }
    
        return response()->json($msj);
    }
    public function update_equipo(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);
        $count = DB::table('logistica.equipo')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $data = DB::table('logistica.equipo')
            ->where('id_equipo',$request->id_equipo)
            ->update([
                'id_categoria' => $request->id_categoria,
                'descripcion' => $des,
                'propietario' => $request->propietario,
                'tp_combustible' => $request->tp_combustible,
                'cod_tarj_propiedad' => $request->cod_tarj_propiedad,
                'placa' => $request->placa,
                'serie' => $request->serie,
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'motor' => $request->motor,
                'kilometraje_inicial' => $request->kilometraje_inicial,
                'anio_fabricacion' => $request->anio_fabricacion,
                'caracteristicas_adic' => $request->caracteristicas_adic
            ]);
        } else {
            $msj = 'No puede guardar. Ya existe un equipo con dicha descripción.';
        }
        return response()->json($msj);
    }
    public function anular_equipo($id){
        $data = DB::table('logistica.equipo')
        ->where('id_equipo',$id)
        ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function mostrar_equi_tipos(){
        $data = DB::table('logistica.equi_tipo')
            ->select('equi_tipo.*')
            ->where('estado',1)
            ->get();
            $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_equi_tipo($id){
        $data = DB::table('logistica.equi_tipo')
            ->select('equi_tipo.*')
            ->where([['equi_tipo.id_tipo', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function next_equi_tipo()
    {
        $cantidad = DB::table('logistica.equi_tipo')
            ->where([['estado','=',1]])
            ->get()->count();
        $tipo = $this->leftZero(2,$cantidad+1);
        return $tipo;
    }
    public function guardar_equi_tipo(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_equi_tipo();
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('logistica.equi_tipo')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_tipo = DB::table('logistica.equi_tipo')->insertGetId(
                [
                    'codigo' => $codigo,
                    'descripcion' => $des,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_tipo'
                );
        } else {
            $msj = 'No es posible guardar. Ya existe '.$count.' tipo registrado con la misma descripción.';
        }
        return response()->json($msj);
    }
    public function update_equi_tipo(Request $request)
    {
        $des = strtoupper($request->descripcion);
        $msj = '';

        $count = DB::table('logistica.equi_tipo')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $data = DB::table('logistica.equi_tipo')
            ->where('id_tipo',$request->id_tipo)
            ->update([ 'descripcion' => $request->descripcion ]);
        } else {
            $msj = 'No es posible actualizar. Ya existe '.$count.' tipo registrado con la misma descripción.';
        }
        return response()->json($msj);
    }
    public function anular_equi_tipo($id){
        $msj = '';
        $count = DB::table('logistica.equi_cat')
        ->where('id_tipo',$id)
        ->count();
        if ($count == 0){
            DB::table('logistica.equi_tipo')
            ->where('id_tipo',$id)
            ->update([ 'estado' => 7 ]);
        } else {
            $msj = 'No puede anular. Tiene vinculado '.$count.' categoría.';
        }
        return response()->json($msj);
    }
    public function mostrar_equi_cats(){
        $data = DB::table('logistica.equi_cat')
            ->select('equi_cat.*','equi_tipo.codigo as cod_tipo',
            'equi_tipo.descripcion as tipo_descripcion')
            ->join('logistica.equi_tipo','equi_tipo.id_tipo','=','equi_cat.id_tipo')
            ->where('equi_cat.estado',1)
            ->get();
            $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_equi_cat($id){
        $data = DB::table('logistica.equi_cat')
        ->select('equi_cat.*','equi_tipo.codigo as cod_tipo',
        'equi_tipo.descripcion as tipo_descripcion')
        ->join('logistica.equi_tipo','equi_tipo.id_tipo','=','equi_cat.id_tipo')
        ->where([['equi_cat.id_categoria', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function next_equi_cat($tipo)
    {
        $cantidad = DB::table('logistica.equi_cat')
            ->where([['id_tipo','=',$tipo],['estado','=',1]])
            ->get()->count();
        $tipo = DB::table('logistica.equi_tipo')->select('codigo')
            ->where('id_tipo', $tipo)->first();
        $cat = $this->leftZero(2,$cantidad+1);
        $nextId = $tipo->codigo."".$cat;
        return $nextId;
    }
    public function guardar_equi_cat(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_equi_cat($request->id_tipo);
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('logistica.equi_cat')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_categoria = DB::table('logistica.equi_cat')->insertGetId(
            [
                'id_tipo' => $request->id_tipo,
                'codigo' => $codigo,
                'descripcion' => $des,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_tipo'
            );
        } else {
            $msj = 'No puede guardar. Ya existe dicha descripción.';
        }
        return response()->json($msj);
    }
    public function update_equi_cat(Request $request)
    {
        $des = strtoupper($request->descripcion);
        $msj = '';

        $count = DB::table('logistica.equi_cat')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $data = DB::table('logistica.equi_cat')
            ->where('id_categoria',$request->id_categoria)
            ->update([ 'descripcion' => $des ]);
        } else {
            $msj = 'No puede actualizar. Ya existe dicha descripción.';
        }
        return response()->json($msj);
    }
    public function anular_equi_cat($id){
        $msj = '';
        $count = DB::table('logistica.equipo')
        ->where('id_categoria',$id)
        ->count();
        if ($count == 0){
            DB::table('logistica.equi_cat')
            ->where('id_categoria',$id)
            ->update([ 'estado' => 7 ]);
        } else {
            $msj = 'No puede anular. Tiene vinculado '.$count.' equipo(s).';
        }
        return response()->json($msj);
    }
    public function listar_docs(){
        $data = DB::table('logistica.equi_seguro')
            ->select('equi_seguro.*','equi_tp_seguro.descripcion as tipo_seguro',
            'adm_contri.razon_social','equipo.codigo as cod_equipo',
            'equipo.descripcion as des_equipo')
            ->join('logistica.equipo','equipo.id_equipo','=','equi_seguro.id_equipo')
            ->join('logistica.equi_tp_seguro','equi_tp_seguro.id_tp_seguro','=','equi_seguro.id_tp_seguro')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','equi_seguro.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['equi_seguro.estado', '=', 1]])
            ->orderBy('equi_seguro.fecha_fin','asc')
            ->get();

        $lista = [];

        $fecha_actual = Carbon::now();
        $actual = Carbon::now();
        $xx = $actual->addMonth(1);
        $xx = $xx->format('Y-m-d');

        foreach($data as $d){

            $warning = ($d->fecha_fin <= $fecha_actual ? 'red' 
            : ( ($d->fecha_fin <= $xx && $d->fecha_fin > $fecha_actual) 
                ? 'yellow' : 'blue' ) );

            $ruta = '/logistica/equipo_seguros/'.$d->archivo_adjunto;
            $file = asset('files').$ruta;
    
            $nuevo = [
                'id_seguro'=>$d->id_seguro,
                'cod_equipo'=>$d->cod_equipo,
                'des_equipo'=>$d->des_equipo,
                'tipo_seguro'=>$d->tipo_seguro,
                'nro_poliza'=>$d->nro_poliza,
                'razon_social'=>$d->razon_social,
                'fecha_inicio'=>$d->fecha_inicio,
                'fecha_fin'=>$d->fecha_fin,
                'importe'=>$d->importe,
                'archivo_adjunto'=>$d->archivo_adjunto,
                'file'=>$file,
                'warning'=>$warning,
                'xx'=>$xx,
                'actual'=>$fecha_actual
            ];
            array_push($lista,$nuevo);
        }

        return response()->json($lista);
    }
    public function listar_seguros($id){
        $data = DB::table('logistica.equi_seguro')
            ->select('equi_seguro.*','equi_tp_seguro.descripcion as tipo_seguro',
            'adm_contri.razon_social')
            ->join('logistica.equi_tp_seguro','equi_tp_seguro.id_tp_seguro','=','equi_seguro.id_tp_seguro')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','equi_seguro.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['equi_seguro.id_equipo', '=', $id],
                    ['equi_seguro.estado', '=', 1]])
            ->orderBy('id_tp_seguro','asc')
            ->orderBy('fecha_fin','desc')
            ->get();

        $html = '';
        $i = 1;
        $hoy = date('Y-m-d');
        $rspta = '';
        
        foreach($data as $d){
            $ffin = $d->fecha_fin;
            $menosunmes = date("Y-m-d",strtotime($ffin."- 1 month"));
            $estado = 'Activo';
            $color = 'success';
            if (($menosunmes < $hoy) && ($ffin > $hoy)){
                $estado = 'Por Expirar';
                $color = 'warning';
            } else if ($ffin < $hoy){
                $estado = 'Expirado';
                $color = 'danger';
            }
            $ruta = '/logistica/equipo_seguros/'.$d->archivo_adjunto;
            $file = asset('files').$ruta;
            $html .= '  
                <tr id="seg-'.$d->id_seguro.'">
                    <td>'.$i.'</td>
                    <td>'.$d->tipo_seguro.'</td>
                    <td><span class="label label-'.$color.'">'.$estado.'</span></td>
                    <td>'.$d->nro_poliza.'</td>
                    <td>'.$d->razon_social.'</td>
                    <td>'.$d->fecha_inicio.'</td>
                    <td>'.$d->fecha_fin.'</td>
                    <td>'.$d->importe.'</td>
                    <td><a href="'.$file.'" target="_blank">'.$d->archivo_adjunto.'</a></td>
                    <td style="display:flex;">
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Seguro" onClick="anular_seguro('.$d->id_seguro.');"></i>
                    </td>
                </tr>';
            $i++;
            // <td><a href="abrir_adjunto_seguro/'.$d->archivo_adjunto.'">'.$d->archivo_adjunto.'</a></td>
        }
        return json_encode($html);
    }
    public function guardar_seguro(Request $request){
        $id_seguro = DB::table('logistica.equi_seguro')->insertGetId(
                [
                    'id_equipo' => $request->id_equipo,
                    'id_tp_seguro' => $request->id_tp_seguro,
                    'nro_poliza' => $request->nro_poliza,
                    'fecha_inicio' => $request->fecha_inicio,
                    'fecha_fin' => $request->fecha_fin,
                    'id_proveedor' => $request->id_proveedor,
                    'importe' => $request->importe,
                    'usuario' => $request->usuario,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                    'id_seguro'
            );
        //obtenemos el campo file definido en el formulario
        $file = $request->file('adjunto');
        if (isset($file)){
            //obtenemos el nombre del archivo
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $nombre = $id_seguro.'.'.$request->nro_poliza.'.'.$extension;
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \File::delete(public_path('logistica/equipo_seguros/'.$nombre));
            \Storage::disk('archivos')->put('logistica/equipo_seguros/'.$nombre,\File::get($file));
            
            $update = DB::table('logistica.equi_seguro')
                ->where('id_seguro', $id_seguro)
                ->update(['archivo_adjunto' => $nombre]); 
        } else {
            $nombre = null;
        }
        return response()->json($id_seguro);
    }
    public function abrir_adjunto_seguro($file_name){
        $file_path = asset('files/logistica/equipo_seguros/'.$file_name);
        if (file_exists($file_path)){
            return response()->download($file_path);
        } else {
            return response()->json("No existe dicho archivo!");
        }
    }
    public function anular_seguro($id_seguro){
        $data = DB::table('logistica.equi_seguro')
            ->where('equi_seguro.id_seguro', $id_seguro)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function mostrar_tp_combustibles(){
        $data = DB::table('logistica.tp_combustible')
            ->select('tp_combustible.*')
            ->get();
            $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_tp_combustible($id){
        $data = DB::table('logistica.tp_combustible')
            ->select('tp_combustible.*')
            ->where([['tp_combustible.id_tp_combustible', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_tp_combustible(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $usuario = Auth::user()->id_usuario;

        $count = DB::table('logistica.tp_combustible')
        ->where('descripcion',$request->tp_descripcion)
        ->count();

        if ($count == 0){
            $id_tp_combustible = DB::table('logistica.tp_combustible')->insertGetId(
                [
                    'codigo' => $request->tp_codigo,
                    'descripcion' => $request->tp_descripcion,
                    'usuario' => $usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_tp_combustible'
                );

            $tipos = DB::table('logistica.tp_combustible')
                ->where('estado',1)->orderBy('descripcion','asc')->get();

            $html = '';
            foreach($tipos as $tp){
                if ($id_tp_combustible == $tp->id_tp_combustible){
                    $html .= '<option value="'.$tp->id_tp_combustible.'" selected>'.$tp->descripcion.'</option>';
                } else {
                    $html .= '<option value="'.$tp->id_tp_combustible.'">'.$tp->descripcion.'</option>';
                }
            }
        } else {
            $msj = 'No es posible guardar. Ya existe '.$count.' tipo de combustible registrado con la misma descripción.';
        }

        if ($msj == ''){
            return json_encode(['msj'=>$msj,'html'=>$html]);
        } else {
            return response()->json(['msj'=>$msj]);
        }
    }
    public function update_tp_combustible(Request $request)
    {
        $data = DB::table('logistica.tp_combustible')
        ->where('id_tp_combustible',$request->id_tp_combustible)
        ->update([ 
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion 
            ]);
        return response()->json($data);
    }
    public function anular_tp_combustible($id){
        $msj = '';
        $count = DB::table('logistica.equipo')
        ->where('tp_combustible',$id)
        ->count();
        if ($count == 0){
            $data = DB::table('logistica.tp_combustible')
            ->where('id_tp_combustible',$id)
            ->update([ 'estado' => 7 ]);
        } else {
            $msj = 'No puede anular. Tiene vinculado '.$count.' equipo(s).';
        }
        return response()->json($msj);
    }
    public function listar_programaciones($id){
        $data = DB::table('logistica.mtto_programacion')
            ->select('mtto_programacion.*','proy_unid_program.descripcion as des_unid_program',
            'equipo.kilometraje_inicial')
            ->leftjoin('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','mtto_programacion.unid_program')
            ->join('logistica.equipo','equipo.id_equipo','=','mtto_programacion.id_equipo')
            ->where([['mtto_programacion.id_equipo', '=', $id],
                    ['mtto_programacion.estado', '=', 1]])
            ->orderBy('descripcion','asc')
            ->get();

        $html = '';
        $i = 1;
        $hoy = date('Y-m-d');
        $kactual = $this->kilometraje_actual($id);

        foreach($data as $d){
            $warning = "";
            $color = "";

            $mtto_ult = DB::table('logistica.mtto_det')
            ->select('mtto.fecha_mtto','mtto.kilometraje')
            ->join('logistica.mtto','mtto.id_mtto','=','mtto_det.id_mtto')
            ->where([['mtto_det.id_programacion','=',$d->id_programacion],
                    ['mtto_det.estado','=',1]])
            ->orderBy('mtto.fecha_mtto','desc')
            ->first();

            if ($mtto_ult !== null){
                if ($d->kilometraje_rango !== null){
                    $kil = $mtto_ult->kilometraje + $d->kilometraje_rango;
                    $warning = ($kactual < $kil ? "Vencido" : "Activo");
                    $color = ($kactual < $kil ? "danger" : "success");
                } 
                else if ($d->tiempo !== null){
                    $unid = ($d->unid_program == 4 ? "month" : "year");
                    $fecha = date("Y-m-d",strtotime($mtto_ult->fecha_mtto."+ ".$d->tiempo." ".$unid));
                    $warning = ($fecha < $hoy ? "Vencido" : "Activo");
                    $color = ($fecha < $hoy ? "danger" : "success");
                }
            }
            $html .= '
                <tr id="seg-'.$d->id_programacion.'">
                    <td>'.$i.'</td>
                    <td>'.$d->descripcion.'</td>
                    <td>'.$d->kilometraje_rango.'</td>
                    <td>'.$d->tiempo.' '.$d->des_unid_program.'</td>
                    <td>'.($mtto_ult !== null ? $mtto_ult->fecha_mtto : '').'</td>
                    <td>'.($mtto_ult !== null ? $mtto_ult->kilometraje : '').'</td>
                    <td><span class="label label-'.$color.'">'.$warning.'</span></td>
                    <td style="display:flex;">
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Programacion" onClick="anular_programacion('.$d->id_programacion.');"></i>
                    </td>
                </tr>';
            $i++;
        }
        return json_encode(["html"=>$html,"kactual"=>$kactual]);
    }
    public function guardar_programacion(Request $request){
        $id_programacion = DB::table('logistica.mtto_programacion')->insertGetId(
                [
                    'id_equipo' => $request->id_equipo,
                    'descripcion' => $request->descripcion,
                    'kilometraje_inicial' => $request->kilometraje_inicial,
                    'kilometraje_rango' => $request->kilometraje_rango,
                    'fecha_inicial' => $request->fecha_inicial,
                    'tiempo' => $request->tiempo,
                    'unid_program' => $request->unid_program,
                    'usuario' => $request->usuario,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                    'id_programacion'
            );
        return response()->json($id_programacion);
    }
    public function anular_programacion($id_programacion){
        $data = DB::table('logistica.mtto_programacion')
            ->where('mtto_programacion.id_programacion', $id_programacion)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function listar_todas_programaciones(){
        $fecha_actual = Carbon::now();
        $actual = Carbon::now();
        $xx = $actual->addMonth(1);
        $xx = $xx->format('Y-m-d');

        $data = DB::table('logistica.mtto_programacion')
            ->select('mtto_programacion.*','proy_unid_program.descripcion as des_unid_program',
            'equipo.descripcion as des_equipo','equipo.codigo as cod_equipo',
            'adm_estado_doc.estado_doc')
            // DB::raw($date.addDay('proy_unid_program.dias')))
            ->join('logistica.equipo','equipo.id_equipo','=','mtto_programacion.id_equipo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','mtto_programacion.estado')
            ->leftjoin('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','mtto_programacion.unid_program')
            // ->where([['mtto_programacion.estado', '=', 1]])
            // ['mtto_programacion.fecha_inicial','<',$date],
            ->get();

        $pendientes = [];

        foreach($data as $d){
            $fecha_vcmto='';
            $kilometraje_vencimiento = '';
            $warning = '';

            if ($d->fecha_inicial !== null){
                $yyyy = date('Y',strtotime($d->fecha_inicial));
                $mes = date('m',strtotime($d->fecha_inicial));
                $dia = date('d',strtotime($d->fecha_inicial));
    
                $fecha_vcmto = Carbon::create($yyyy,$mes,$dia);
                
                if ($d->unid_program == 1){//Dias
                    $fecha_vcmto->addDay(floatval($d->tiempo));
                } else if ($d->unid_program == 2){//Semanas
                    $fecha_vcmto->addWeek(floatval($d->tiempo));
                } else if ($d->unid_program == 3){//Quincenas
                    $fecha_vcmto->addDay(floatval($d->tiempo) * 15);
                } else if ($d->unid_program == 4){//Meses
                    $fecha_vcmto->addMonth(floatval($d->tiempo));
                } else if ($d->unid_program == 5){//Años
                    $fecha_vcmto->addYear(floatval($d->tiempo));
                }
                $warning = ($fecha_vcmto <= $fecha_actual ? 'red' 
                            : ( ($fecha_vcmto <= $xx && $fecha_vcmto > $fecha_actual) 
                                ? 'yellow' : 'blue' ) );
                $fecha_vcmto = $fecha_vcmto->format('Y-m-d');
            }
            
            if ($d->kilometraje_inicial !== null){
                $kilometraje_vencimiento = $d->kilometraje_inicial + $d->kilometraje_rango;
            }
            $nuevo = [
                'id_programacion'=>$d->id_programacion,
                'cod_equipo'=>$d->cod_equipo,
                'des_equipo'=>$d->des_equipo,
                'descripcion'=>$d->descripcion,
                'kilometraje_inicial'=>$d->kilometraje_inicial,
                'kilometraje_rango'=>$d->kilometraje_rango,
                'kilometraje_vencimiento'=>$kilometraje_vencimiento,
                'fecha_inicial'=>$d->fecha_inicial,
                'fecha_vencimiento'=>$fecha_vcmto,
                'tiempo'=>$d->tiempo,
                'des_unid_program'=>$d->des_unid_program,
                'warning'=>$warning,
                'estado_doc'=>$d->estado_doc
            ];
            array_push($pendientes,$nuevo);
        }
        
        return response()->json($pendientes);
    }
    public function listar_programaciones_pendientes(){
        $date = Carbon::now();
        $data = DB::table('logistica.mtto_programacion')
            ->select('mtto_programacion.*','proy_unid_program.descripcion as des_unid_program',
            'equipo.descripcion as des_equipo')
            // DB::raw($date.addDay('proy_unid_program.dias')))
            ->join('logistica.equipo','equipo.id_equipo','=','mtto_programacion.id_equipo')
            ->leftjoin('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','mtto_programacion.unid_program')
            ->where([['mtto_programacion.estado', '=', 1]])
            // ['mtto_programacion.fecha_inicial','<',$date],
            ->get();
        $pendientes = [];

        foreach($data as $d){
            $yyyy = date('Y',strtotime($d->fecha_inicial));
            $mes = date('m',strtotime($d->fecha_inicial));
            $dia = date('d',strtotime($d->fecha_inicial));

            $fecha = Carbon::create($yyyy,$mes,$dia);
            
            if ($d->unid_program == 1){//Dias
                $fecha->addDay(floatval($d->tiempo));
            } else if ($d->unid_program == 2){//Semanas
                $fecha->addWeek(floatval($d->tiempo));
            } else if ($d->unid_program == 3){//Quincenas
                $fecha->addDay(floatval($d->tiempo) * 15);
            } else if ($d->unid_program == 4){//Meses
                $fecha->addMonth(floatval($d->tiempo));
            } else if ($d->unid_program == 5){//Años
                $fecha->addYear(floatval($d->tiempo));
            }
            $nuevo = [
                'id_programacion'=>$d->id_programacion,
                'des_equipo'=>$d->des_equipo,
                'descripcion'=>$d->descripcion,
                'kilometraje_inicial'=>$d->kilometraje_inicial,
                'kilometraje_rango'=>$d->kilometraje_rango,
                'fecha_inicial'=>$d->fecha_inicial,
                'fecha_vencimiento'=>$fecha->format('Y-m-d'),
                'tiempo'=>$d->tiempo,
                'des_unid_program'=>$d->des_unid_program
            ];
            array_push($pendientes,$nuevo);
        }
        return response()->json($pendientes);
    }
    public function listar_mtto_pendientes($id_equipo){
        $date = Carbon::now();
        $data = DB::table('logistica.mtto_programacion')
            ->select('mtto_programacion.*','proy_unid_program.descripcion as des_unid_program',
            'equipo.descripcion as des_equipo')
            ->join('logistica.equipo','equipo.id_equipo','=','mtto_programacion.id_equipo')
            ->leftjoin('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','mtto_programacion.unid_program')
            ->where([['mtto_programacion.id_equipo', '=', $id_equipo],
                     ['mtto_programacion.estado', '=', 1]])
            ->get();
        $pendientes = [];

        foreach($data as $d){
            $yyyy = date('Y',strtotime($d->fecha_inicial));
            $mes = date('m',strtotime($d->fecha_inicial));
            $dia = date('d',strtotime($d->fecha_inicial));

            $fecha = Carbon::create($yyyy,$mes,$dia);
            
            if ($d->unid_program == 1){//Dias
                $fecha->addDay(floatval($d->tiempo));
            } else if ($d->unid_program == 2){//Semanas
                $fecha->addWeek(floatval($d->tiempo));
            } else if ($d->unid_program == 3){//Quincenas
                $fecha->addDay(floatval($d->tiempo) * 15);
            } else if ($d->unid_program == 4){//Meses
                $fecha->addMonth(floatval($d->tiempo));
            } else if ($d->unid_program == 5){//Años
                $fecha->addYear(floatval($d->tiempo));
            }
            $nuevo = [
                'id_programacion'=>$d->id_programacion,
                'des_equipo'=>$d->des_equipo,
                'descripcion'=>$d->descripcion,
                'kilometraje_inicial'=>$d->kilometraje_inicial,
                'kilometraje_rango'=>$d->kilometraje_rango,
                'fecha_inicial'=>$d->fecha_inicial,
                'fecha_vencimiento'=>$fecha->format('Y-m-d'),
                'tiempo'=>$d->tiempo,
                'des_unid_program'=>$d->des_unid_program
            ];
            array_push($pendientes,$nuevo);
        }
        return response()->json($pendientes);
    }
    public function listar_mttos(){
        $data = DB::table('logistica.mtto')
            ->select('mtto.*','equipo.descripcion as des_equipo')
            ->join('logistica.equipo','equipo.id_equipo','=','mtto.id_equipo')
            // ->where([['mtto.estado', '=', 1]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_mttos_detalle($id_equipo,$fini,$ffin){
        $hasWhere = [];
        if ($id_equipo !== null && $id_equipo > 0){
            $hasWhere[] = ['mtto.id_equipo','=',$id_equipo];
        }
        $data = DB::table('logistica.mtto_det')
            ->select('mtto_det.*','equipo.codigo as cod_equipo',
            'equipo.descripcion as des_equipo','mtto.fecha_mtto',
            'adm_contri.razon_social','adm_estado_doc.estado_doc')
            ->join('logistica.mtto','mtto.id_mtto','=','mtto_det.id_mtto')
            ->join('logistica.equipo','equipo.id_equipo','=','mtto.id_equipo')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','mtto.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','mtto_det.estado')
            ->where([['mtto_det.estado', '=', 1],
                    // ['mtto.id_equipo', '=', $id_equipo],
                    ['mtto.fecha_mtto', '>=', $fini],
                    ['mtto.fecha_mtto', '<=', $ffin]])
            ->where($hasWhere)
            ->get();
        return response()->json($data);
    }
    public function mostrar_mtto($id){
        $mtto = DB::table('logistica.mtto')
            ->select('mtto.*','equipo.descripcion as des_equipo',
            'adm_grupo.id_grupo','sis_sede.id_sede','adm_empresa.id_empresa',
            'adm_area.descripcion as nombre_area','adm_contri.razon_social')
            ->join('logistica.equipo','equipo.id_equipo','=','mtto.id_equipo')
            ->join('administracion.adm_area','adm_area.id_area','=','mtto.id_area')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','adm_grupo.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','mtto.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['mtto.id_mtto', '=', $id]])
            ->first();

        // $sedes = DB::table('administracion.sis_sede')
        //     ->select('sis_sede.*')
        //     ->where('id_empresa',$mtto->id_empresa)
        //     ->get();
        // $grupos = DB::table('administracion.adm_grupo')
        //     ->select('adm_grupo.*')
        //     ->where('id_sede',$mtto->id_sede)
        //     ->get();
        // $areas = DB::table('administracion.adm_area')
        //     ->select('adm_area.*')
        //     ->where('id_grupo',$mtto->id_grupo)
        //     ->get();

        return response()->json($mtto);
    }
    public function next_cod_mtto($fecha,$id_equipo){
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        
        $equipo = DB::table('logistica.equipo')
            ->where('id_equipo',$id_equipo)
            ->first();

        $data = DB::table('logistica.mtto')
            ->where([['id_equipo', '=', $id_equipo]])
            ->whereYear('fecha_mtto', '=', $yyyy)
            ->count();

        $number = $this->leftZero(3,$data+1);
        $result = "MTO-".$equipo->codigo."-".$anio."".$number;
        return $result;
    }
    public function guardar_mtto(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_cod_mtto( $request->fecha_mtto, 
                                        $request->id_equipo );
        $id_mtto = DB::table('logistica.mtto')->insertGetId(
            [
                'codigo' => $codigo,
                'fecha_mtto' => $request->fecha_mtto,
                'id_proveedor' => $request->id_proveedor,
                'id_equipo' => $request->id_equipo,
                'kilometraje' => $request->kilometraje,
                'costo_total' => $request->costo_total,
                'observaciones' => $request->observaciones,
                'id_area' => $request->id_area,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_mtto'
            );
        return response()->json($id_mtto);
    }
    public function update_mtto(Request $request)
    {
        $data = DB::table('logistica.mtto')
        ->where('id_mtto',$request->id_mtto)
        ->update([ 
            'fecha_mtto' => $request->fecha_mtto,
            'id_proveedor' => $request->id_proveedor,
            'id_equipo' => $request->id_equipo,
            'kilometraje' => $request->kilometraje,
            'costo_total' => $request->costo_total,
            'observaciones' => $request->observaciones,
            'id_area' => $request->id_area
        ]);
        return response()->json($data);
    }
    public function anular_mtto($id){
        $data = DB::table('logistica.mtto')
        ->where('id_mtto',$id)
        ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function listar_mtto_detalle($id_mtto){
        $data = DB::table('logistica.mtto_det')
            ->select('mtto_det.*','mtto_programacion.descripcion as des_programacion')
            ->leftjoin('logistica.mtto_programacion','mtto_programacion.id_programacion','=','mtto_det.id_programacion')
            // ->leftjoin('logistica.equipo','equipo.id_equipo','=','mtto_programacion.id_equipo')
            ->where('mtto_det.id_mtto',$id_mtto)
            ->get();
        $html = '';
        foreach($data as $d){
            $html .='
            <tr id="det-'.$d->id_mtto_det.'">
                <td>'.($d->tp_mantenimiento === 1?'Preventivo':'Correctivo').'</td>
                <td>'.$d->des_programacion.'. '.$d->descripcion.'</td>
                <td>'.$d->cantidad.'</td>
                <td>'.$d->precio_unitario.'</td>
                <td>'.$d->precio_total.'</td>
                <td>'.$d->resultado.'</td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$d->id_mtto_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$d->id_mtto_det.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }
    public function mostrar_mtto_detalle($id_mtto_det){
        $data = DB::table('logistica.mtto_det')
            ->select('mtto_det.*','presup_par.codigo as cod_partida',
            'presup_pardet.descripcion as des_partida')
            ->join('finanzas.presup_par','presup_par.id_partida','=','mtto_det.id_partida')
            ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->where('mtto_det.id_mtto_det',$id_mtto_det)
            ->get();
        return response()->json($data);
    }
    public function guardar_mtto_detalle(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_mtto = DB::table('logistica.mtto_det')->insertGetId(
            [
                'id_mtto' => $request->id_mtto_padre,
                'id_programacion' => $request->id_programacion,
                'descripcion' => $request->descripcion,
                'resultado' => $request->resultado,
                'tp_mantenimiento' => $request->tp_mantenimiento,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'precio_total' => $request->precio_total,
                'id_partida' => $request->id_partida,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_mtto_det'
            );
        return response()->json($id_mtto);
    }
    public function update_mtto_detalle(Request $request)
    {
        $data = DB::table('logistica.mtto_det')
        ->where('id_mtto_det',$request->id_mtto_det)
        ->update([ 
            'descripcion' => $request->descripcion,
            'resultado' => $request->resultado,
            'tp_mantenimiento' => $request->tp_mantenimiento,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'precio_total' => $request->precio_total,
            'id_partida' => $request->id_partida
        ]);
        return response()->json($data);
    }
    public function anular_mtto_detalle($id){
        $data = DB::table('logistica.mtto_det')
        ->where('id_mtto_det',$id)
        ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function listar_todas_solicitudes(){
        $data = DB::table('logistica.equi_sol')
            ->select('equi_sol.*','adm_area.descripcion as des_area',
            'equi_cat.descripcion as des_categoria','adm_estado_doc.estado_doc',
            'adm_documentos_aprob.id_doc_aprob','equipo.descripcion as equi_asignado',
            'equi_asig.fecha_asignacion',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->join('logistica.equi_cat','equi_cat.id_categoria','=','equi_sol.id_categoria')
            ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','equi_sol.estado')
            ->join('administracion.adm_documentos_aprob','adm_documentos_aprob.codigo_doc','=','equi_sol.codigo')
            ->leftjoin('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','equi_sol.id_proyecto')
            ->leftjoin('logistica.equi_asig','equi_asig.id_solicitud','=','equi_sol.id_solicitud')
            ->leftjoin('logistica.equipo','equipo.id_equipo','=','equi_asig.id_equipo')
            ->get();
        return response()->json($data);
    }
    public function imprimir_solicitud($id_asignacion){
        $id = $this->decode5t($id_asignacion);
        $sol = DB::table('logistica.equi_asig')
        ->select('equi_asig.*','equipo.descripcion as equi_asignado',
        DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"),
        'adm_grupo.descripcion as des_grupo','sis_usua.nombre_corto','equi_sol.observaciones',
        'adm_area.descripcion as des_area','equi_sol.codigo','equi_sol.fecha_solicitud',
        'sis_sede.descripcion as des_sede','adm_contri.razon_social as des_empresa',
        'equi_cat.descripcion as des_categoria','proy_proyecto.descripcion as des_proyecto')
        ->leftjoin('logistica.equipo','equipo.id_equipo','=','equi_asig.id_equipo')
        ->leftjoin('logistica.equi_sol','equi_sol.id_solicitud','=','equi_asig.id_solicitud')
        ->leftjoin('logistica.equi_cat','equi_cat.id_categoria','=','equi_sol.id_categoria')
        ->leftjoin('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','equi_sol.id_proyecto')
        ->leftjoin('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
        ->leftjoin('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        ->leftjoin('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
        ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
        ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','adm_grupo.id_sede')
        ->leftjoin('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','equi_asig.usuario')
        ->where('equi_asig.id_asignacion',$id)
        ->first();

        $detalle ='';

        // if (isset($sol->id_asignacion)){
            $controles = DB::table('logistica.equi_asig_control')
                ->select('equi_asig_control.*',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
                ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_asig_control.chofer')
                ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
                ->where([['equi_asig_control.id_asignacion','=',$id],
                         ['equi_asig_control.estado','=',1]])
                ->orderBy('equi_asig_control.fecha_recorrido','asc')
                ->get();
            $i = 1;
            foreach($controles as $d){
                $gal = ($d->importe !== null ? ('S/'.$d->importe.' : '.$d->galones.' gal.') : '');
                $detalle .='
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$d->fecha_recorrido.'</td>
                    <td>'.($d->kilometraje_fin - $d->kilometraje_inicio).' km.</td>
                    <td>'.$d->nombre_trabajador.'</td>
                    <td>'.$d->descripcion_recorrido.'</td>
                    <td>'.$gal.'</td>
                </tr>
                ';
                $i++;
            }
        // }

        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:12px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                </style>
            </head>
            <body>
                <br/>
                <br/>
                <h3 style="margin:0px;"><center>SOLICITUD DE EQUIPO</center></h3>
                <br/>
                <p style="text-align:center;font-size:14px;margin:0px;"><strong>N° '.$sol->codigo.'</strong></p>
                <p style="text-align:center;font-size:12px;margin:0px;">Fecha de Solicitud: '.$sol->fecha_solicitud.'</p>
                <br/>
                <table border="0">
                    <tr>
                        <td class="subtitle">Fecha Inicio</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$sol->fecha_inicio.'</td>
                        <td width=100px>Empresa</td>
                        <td width=10px>:</td>
                        <td>'.$sol->des_empresa.'</td>
                    </tr>
                    <tr>
                        <td>Fecha Fin</td>
                        <td width=10px>:</td>
                        <td>'.$sol->fecha_fin.'</td>
                        <td>Sede</td>
                        <td>:</td>
                        <td>'.$sol->des_sede.'</td>
                    </tr>
                    <tr>
                        <td width=110px>Solicitado por</td>
                        <td width=10px>:</td>
                        <td width=300px>'.$sol->nombre_trabajador.'</td>
                        <td>Grupo</td>
                        <td>:</td>
                        <td>'.$sol->des_grupo.'</td>
                    </tr>
                    <tr>
                        <td>Categoría Solic.</td>
                        <td>:</td>
                        <td>'.$sol->des_categoria.'</td>
                        <td>Area</td>
                        <td>:</td>
                        <td>'.$sol->des_area.'</td>
                    </tr>
                    <tr>
                        <td>Proyecto</td>
                        <td>:</td>
                        <td colSpan="4">'.$sol->des_proyecto.'</td>
                    </tr>
                    <tr>
                        <td>Observación</td>
                        <td>:</td>
                        <td colSpan="4">'.$sol->observaciones.'</td>
                    </tr>
                </table>
                <p style="text-align:right;font-size:11px;">Fecha de Registro: '.$sol->fecha_registro.'</p>

                <br/>
                <h3 style="margin:0px;"><center>ASIGNACIÓN DE EQUIPO</center></h3>
                <br/>
                <p style="text-align:center;font-size:12px;margin:0px;">Fecha de Asignación: '.$sol->fecha_asignacion.'</p>
                <br/>
                <table border="0">
                    <tr>
                        <td width=110px>Equipo Asignado</td>
                        <td>:</td>
                        <td colSpan="4">'.$sol->equi_asignado.'</td>
                    </tr>
                    <tr>
                        <td>Fecha Inicio</td>
                        <td width=10px>:</td>
                        <td width=300px>'.$sol->fecha_inicio.'</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Fecha Fin</td>
                        <td width=10px>:</td>
                        <td>'.$sol->fecha_fin.'</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width=100px>Asignado por</td>
                        <td width=10px>:</td>
                        <td>'.$sol->nombre_corto.'</td>
                    </tr>
                    <tr>
                        <td>Observación</td>
                        <td>:</td>
                        <td colSpan="4">'.$sol->detalle_asignacion.'</td>
                    </tr>
                </table>

                <p style="text-align:right;font-size:11px;">Fecha de Registro: '.$sol->fecha_registro.'</p>
                <br/>';

        if ($detalle !== ''){
            $html .='
            <h3 style="margin:0px;"><center>CONTROL DEL RECORRIDO</center></h3>
            <br/>
            <table border="0" id="detalle">
                <thead>
                    <tr>
                        <th width="10px">N°</th>
                        <th width="70px">Fecha</th>
                        <th width="70px">Recorrido</th>
                        <th width="90px">Chofer</th>
                        <th width="350px">Descripción</th>
                        <th>Combustible</th>
                    </tr>
                </thead>
                <tbody>
                '.$detalle.'
                </tbody>
            </table>
            <br/>';
        }
                $html .='
            </body>
        </html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
        return $pdf->download('solicitud.pdf');

    }
    public function listar_solicitudes_aprobadas(){
        $data = DB::table('logistica.equi_sol')
            ->select('equi_sol.*','adm_area.descripcion as area_descripcion',
            'equi_cat.descripcion as des_categoria',
            DB::raw("(SELECT equi_sol.cantidad - COUNT(equi_asig.id_asignacion) FROM logistica.equi_asig 
            WHERE equi_asig.id_solicitud = equi_sol.id_solicitud and equi_asig.estado = 1) as asignaciones_pendientes"),
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
            ->join('logistica.equi_cat','equi_cat.id_categoria','=','equi_sol.id_categoria')
            ->where([['equi_sol.estado','=',2]])//Aprobado
            ->get();
        return response()->json($data);
    }
    // public function listar_solicitudes_asignadas(){
    //     $data = DB::table('logistica.equi_sol')
    //         ->select('equi_sol.*','adm_area.descripcion as area_descripcion',
    //         'equi_cat.descripcion as des_categoria',
    //         DB::raw("(SELECT equi_sol.cantidad - COUNT(equi_asig.id_asignacion) FROM logistica.equi_asig 
    //         WHERE equi_asig.id_solicitud = equi_sol.id_solicitud and equi_asig.estado = 1) as asignaciones_pendientes"),
    //         DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
    //         ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
    //         ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
    //         ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
    //         ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
    //         ->join('logistica.equi_cat','equi_cat.id_categoria','=','equi_sol.id_categoria')
    //         ->where([['equi_sol.estado','=',5]])//Asignadas
    //         ->get();
    //     return response()->json($data);
    // }
    public function mostrar_solicitudes($id_trabajador,$id_usuario){
        $data = DB::table('logistica.equi_sol')
            ->select('equi_sol.*','adm_area.descripcion as area_descripcion',
            'adm_grupo.descripcion as grupo_descripcion',
            'adm_estado_doc.estado_doc','adm_documentos_aprob.id_doc_aprob',
            'equi_cat.descripcion as des_categoria',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','equi_sol.estado')
            ->join('administracion.adm_documentos_aprob','adm_documentos_aprob.codigo_doc','=','equi_sol.codigo')
            ->join('logistica.equi_cat','equi_cat.id_categoria','=','equi_sol.id_categoria')
            ->where('equi_sol.id_trabajador',$id_trabajador)
            ->orWhere('equi_sol.usuario',$id_usuario)
            ->get();
        return response()->json($data);
    }
    public function mostrar_solicitudes_grupo(){
        $roles = Auth::user()->trabajador->roles;
        $grupos = [];
        foreach($roles as $rol){
            $grupo = DB::table('administracion.adm_area')
            ->where('adm_area.id_area',$rol->pivot->id_area)
            ->first();
            if (!in_array($grupo->id_grupo,$grupos)){
                array_push($grupos, $grupo->id_grupo);                
            }
        }
        $data = DB::table('logistica.equi_sol')
            ->select('equi_sol.*','adm_area.descripcion as area_descripcion',
            'adm_grupo.descripcion as grupo_descripcion',
            'adm_estado_doc.estado_doc','adm_documentos_aprob.id_doc_aprob',
            'equi_cat.descripcion as des_categoria',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','equi_sol.estado')
            ->join('administracion.adm_documentos_aprob','adm_documentos_aprob.codigo_doc','=','equi_sol.codigo')
            ->join('logistica.equi_cat','equi_cat.id_categoria','=','equi_sol.id_categoria')
            ->whereIn('adm_grupo.id_grupo',$grupos)
            ->get();
        return response()->json($data);
    }
    public function mostrar_solicitud($id){
        $sol = DB::table('logistica.equi_sol')
            ->select('equi_sol.*','adm_estado_doc.estado_doc','adm_documentos_aprob.id_doc_aprob',
            'adm_area.descripcion as nombre_area','adm_empresa.id_empresa','sis_usua.usuario as nombre_usuario')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','equi_sol.estado')
            ->join('administracion.adm_documentos_aprob','adm_documentos_aprob.codigo_doc','=','equi_sol.codigo')
            ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','adm_grupo.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','equi_sol.usuario')
            ->where([['equi_sol.id_solicitud','=',$id]])
            ->first();

        return response()->json($sol);
    }
    public function next_correlativo_sol($fecha,$area){
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));

        $data = DB::table('logistica.equi_sol')
        ->whereYear('fecha_solicitud','=',$yyyy)
        ->count();

        $area = DB::table('administracion.adm_area')
        ->where('id_area',$area)
        ->first();

        $correlativo = $this->leftZero(3,$data+1);
        $codigo = 'SE-'.$area->codigo.'-'.$anio.''.$correlativo;

        return $codigo;
    }
    public function guardar_equi_sol(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_correlativo_sol($request->fecha_solicitud, $request->id_area);
        $id_usuario = Auth::user()->id_usuario;
        $id_solicitud = DB::table('logistica.equi_sol')->insertGetId(
            [
                'id_trabajador' => $request->id_trabajador,
                'fecha_solicitud' => $request->fecha_solicitud,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'id_area' => $request->id_area,
                'codigo' => $codigo,
                'id_proyecto' => $request->id_proyecto,
                'id_categoria' => $request->id_categoria,
                'observaciones' => $request->observaciones,
                'cantidad' => $request->cantidad,
                'usuario' => $id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_solicitud'
            );

        DB::table('administracion.adm_documentos_aprob')->insertGetId(
            [
                'id_tp_documento' => 6,//Solicitud de Equipos
                'codigo_doc' => $codigo,
                'id_doc' => $id_solicitud,
            ],
            'id_doc_aprob'
        );

        return response()->json($id_solicitud);
    }
    public function update_equi_sol(Request $request)
    {
        $data = DB::table('logistica.equi_sol')
        ->where('id_solicitud',$request->id_solicitud)
        ->update([ 
            'id_trabajador' => $request->id_trabajador,
            'fecha_solicitud' => $request->fecha_solicitud,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'id_area' => $request->id_area,
            'id_proyecto' => $request->id_proyecto,
            // 'id_presupuesto' => $request->id_presupuesto,
            'id_categoria' => $request->id_categoria,
            'observaciones' => $request->observaciones,
            'cantidad' => $request->cantidad
        ]);
        return response()->json($request->id_solicitud);
    }
    public function anular_equi_sol($id){
        $data = DB::table('logistica.equi_sol')
        ->where('id_solicitud',$id)
        ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function equipos_disponibles($id_categoria){
        $data = DB::table('logistica.equipo')
            ->select('equipo.*')
            ->where([['equipo.estado','=',1],
                    ['equipo.id_categoria','=',$id_categoria]])
            ->get();

        $equipos = [];
        $fecha_actual = date('Y-m-d');

        foreach($data as $d){
            $asig = DB::table('logistica.equi_asig')
            ->where([['id_equipo','=',$d->id_equipo],
                     ['estado','=',1]])
            ->get();
            $fechas_uso = '';
            $agregar = true;

            foreach($asig as $a){
                if ($fecha_actual >= $a->fecha_inicio && $fecha_actual <= $a->fecha_fin) {
                    $agregar = false;
                } else {
                    if ($a->fecha_fin >= $fecha_actual){                    
                        $fechas_uso .= $a->fecha_inicio.' hasta '.$a->fecha_fin.'.  ';
                    }
                }
            }
            if ($agregar){
                $nuevo = [
                    'id_equipo'=>$d->id_equipo,
                    'codigo'=>$d->codigo,
                    'descripcion'=>$d->descripcion,
                    'fechas_uso'=>$fechas_uso,
                ];
                array_push($equipos,$nuevo);
            }
        }

        $output['data'] = $equipos;
        return response()->json($output);
    }
    public function guardar_asignacion(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_asignacion = DB::table('logistica.equi_asig')->insertGetId(
            [
                'id_solicitud' => $request->id_solicitud,
                'id_equipo' => $request->id_equipo,
                'fecha_asignacion' => $request->fecha_asignacion,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'detalle_asignacion' => $request->detalle_asignacion,
                'usuario' => $request->usuario,
                'kilometraje' => $request->kilometraje,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_asignacion'
            );
        
        $equipo = DB::table('logistica.equipo')
        ->where('id_equipo',$request->id_equipo)
        ->first();

        //obtenemos el campo file definido en el formulario
        $file = $request->file('adjunto');
        if (isset($file)){
            //obtenemos el nombre del archivo
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $nombre = $id_asignacion.'.'.$equipo->codigo.'.'.$extension;
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \File::delete(public_path('logistica/equipo_checklist/'.$nombre));
            \Storage::disk('archivos')->put('logistica/equipo_checklist/'.$nombre,\File::get($file));
            
            $update = DB::table('logistica.equi_asig')
                ->where('id_asignacion', $id_asignacion)
                ->update(['archivo_adjunto' => $nombre]); 
        } else {
            $nombre = null;
        }
        $asignacion = DB::table('logistica.equi_asig')
        ->where('id_solicitud',$request->id_solicitud)
        ->count();

        $solicitud = DB::table('logistica.equi_sol')
        ->where('id_solicitud',$request->id_solicitud)
        ->first();

        if ($asignacion >= $solicitud->cantidad){
            DB::table('logistica.equi_sol')
            ->where('id_solicitud',$request->id_solicitud)
            ->update(['estado'=>5]);//Atendido
        }
        return response()->json($id_asignacion);
    }
    public function update_equi_asig(Request $request)
    {
        $data = DB::table('logistica.equi_asig')
        ->where('id_asignacion',$request->id_asignacion)
        ->update([ 
            'id_solicitud' => $request->id_solicitud,
            'id_equipo' => $request->id_equipo,
            'fecha_asignacion' => $request->fecha_asignacion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'detalle_asignacion' => $request->detalle_asignacion,
            'kilometraje' => $request->kilometraje,
            ]);
        return response()->json($data);
    }
    public function anular_equi_asig($id){
        $data = DB::table('logistica.equi_asig')
        ->where('id_asignacion',$id)
        ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function listar_asignaciones(){
        $roles = Auth::user()->trabajador->roles;
        $areas = [];
        foreach($roles as $rol){
            array_push($areas, $rol->pivot->id_area);
        }
        $data = DB::table('logistica.equi_asig')
            ->select('equi_asig.*','adm_area.descripcion as area_descripcion',
            'equipo.descripcion as equipo_descripcion','equipo.codigo as cod_equipo',
            'equi_sol.codigo','equi_sol.observaciones',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('logistica.equipo','equipo.id_equipo','=','equi_asig.id_equipo')
            ->join('logistica.equi_sol','equi_sol.id_solicitud','=','equi_asig.id_solicitud')
            ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where([['equi_sol.estado','=',5]])//Atendido
            ->whereIn('equi_sol.id_area',$areas)
            ->get();
        return response()->json($data);
    }
    public function verUsuario(){
        $roles = Auth::user()->trabajador->roles;
        $areas = [];
        foreach($roles as $rol){
            array_push($areas, $rol->pivot->id_area);                
        }
        return $areas;
    }
    public function mostrar_asignacion($id){
        $data = DB::table('logistica.equi_asig')
            ->select('equi_asig.*','adm_area.descripcion as area_descripcion',
            'equipo.codigo','equipo.descripcion as equipo_descripcion','equi_sol.id_trabajador',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('logistica.equipo','equipo.id_equipo','=','equi_asig.id_equipo')
            ->join('logistica.equi_sol','equi_sol.id_solicitud','=','equi_asig.id_solicitud')
            ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where([['equi_asig.id_asignacion','=',$id],
                    ['equi_asig.estado','=',1]])
            ->get();
        return response()->json($data);
    }
    public function mostrar_control($id_control){
        $data = DB::table('logistica.equi_asig_control')
            ->select('equi_asig_control.*',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_asig_control.chofer')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where([['equi_asig_control.id_control','=',$id_control],
                    ['equi_asig_control.estado','=',1]])
            ->get();
        return response()->json($data);
    }
    public function listar_controles($id_asig){
        $data = DB::table('logistica.equi_asig_control')
            ->select('equi_asig_control.*',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_asig_control.chofer')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where([['equi_asig_control.id_asignacion','=',$id_asig],
                    ['equi_asig_control.estado','=',1]])
            ->get();
        $html = '';
        $i = 1;
        foreach($data as $d){
            $html .='
            <tr id="det-'.$d->id_control.'">
                <td>'.$i.'</td>
                <td>'.$d->fecha_recorrido.'</td>
                <td>'.$d->kilometraje_inicio.'</td>
                <td>'.$d->kilometraje_fin.'</td>
                <td>'.($d->kilometraje_fin - $d->kilometraje_inicio).'</td>
                <td>'.$d->hora_inicio.'</td>
                <td>'.$d->hora_fin.'</td>
                <td>'.$d->nombre_trabajador.'</td>
                <td>'.$d->descripcion_recorrido.'</td>
                <td>'.$d->importe.'</td>
                <td>'.$d->galones.'</td>
                <td>'.$d->observaciones.'</td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$d->id_control.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle('.$d->id_control.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$d->id_control.');"></i>
                </td>
            </tr>
            ';
            $i++;
        }
        return json_encode($html);
    }
    public function guardar_control(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_control = DB::table('logistica.equi_asig_control')->insertGetId(
            [
                'id_asignacion' => $request->id_asignacion,
                'chofer' => $request->chofer,
                'fecha_recorrido' => $request->fecha_recorrido,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'kilometraje_inicio' => $request->kilometraje_inicio,
                'kilometraje_fin' => $request->kilometraje_fin,
                'descripcion_recorrido' => $request->descripcion_recorrido,
                'importe' => $request->importe,
                'precio_unitario' => $request->precio_unitario,
                'galones' => $request->galones,
                'grifo' => $request->grifo,
                'observaciones' => $request->observaciones,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_control'
            );
        return response()->json($id_control);
    }
    public function update_control(Request $request)
    {
        $data = DB::table('logistica.equi_asig_control')
        ->where('id_control',$request->id_control)
        ->update([ 
            // 'id_asignacion' => $request->id_asignacion,
            'chofer' => $request->chofer,
            'fecha_recorrido' => $request->fecha_recorrido,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'kilometraje_inicio' => $request->kilometraje_inicio,
            'kilometraje_fin' => $request->kilometraje_fin,
            'descripcion_recorrido' => $request->descripcion_recorrido,
            'importe' => $request->importe,
            'precio_unitario' => $request->precio_unitario,
            'galones' => $request->galones,
            'grifo' => $request->grifo,
            'observaciones' => $request->observaciones,
        ]);
        return response()->json($data);
    }
    public function anular_control($id){
        $data = DB::table('logistica.equi_asig_control')
        ->where('id_control',$id)
        ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function getTrabajador($id_usuario){
        $data = DB::table('configuracion.sis_usua')
        ->where('id_usuario',$id_usuario)
        ->get();
        return response()->json($data);
    }
    public function listar_aprob_sol(){
        $usuario = Auth::user();
        $roles = $usuario->trabajador->roles;
        $id_roles = [];
        $id_grupos = [];
        $areas = [];

        foreach($roles as $rol){
            $grupo = DB::table('rrhh.rrhh_rol')
            ->select('adm_grupo.id_grupo','adm_area.descripcion')
            ->join('administracion.adm_area','adm_area.id_area','=','rrhh_rol.id_area')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
            ->where('id_rol',$rol->pivot->id_rol)
            ->first();

            if ($grupo !== null){
                if (!in_array($grupo->id_grupo, $id_grupos)){
                    array_push($id_grupos, $grupo->id_grupo);
                }
                if (!in_array($grupo->descripcion, $areas)){
                    array_push($areas, $grupo->descripcion);
                }
            }
            array_push($id_roles, $rol->pivot->id_rol);
        }

            $ope = DB::table('administracion.adm_operacion')
                ->select('adm_operacion.*')
                ->where([['estado','=',1],['id_tp_documento','=',6]])//Solicitud de Equipo
                ->whereIn('id_grupo',$id_grupos)
                ->first();
            $id_ope = ($ope !== null ? $ope->id_operacion : null);
            $lista = [];

        // if (isset($ope)){
            //Lista de flujos con el rol en sesion
            $flujos = DB::table('administracion.adm_flujo')
                ->select('adm_flujo.*')
                ->where([['adm_flujo.id_operacion','=',$id_ope],
                        ['adm_flujo.estado','=',1]])
                ->whereIn('adm_flujo.id_rol',$id_roles)
                ->orderBy('orden')
                ->get();
        
            //Lista de solicitudes pendientes 
            $pendientes = DB::table('logistica.equi_sol')
                ->select('equi_sol.*','adm_area.descripcion as area_descripcion',
                'equi_cat.descripcion as des_categoria','adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color','sis_sede.descripcion as nombre_empresa',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"),
                DB::raw("(SELECT equi_sol.cantidad - COUNT(equi_asig.id_asignacion) FROM logistica.equi_asig 
                WHERE equi_asig.id_solicitud = equi_sol.id_solicitud and equi_asig.estado = 1) as asignaciones_pendientes"))
                ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
                ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
                ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
                ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
                ->join('administracion.sis_sede','sis_sede.id_sede','=','adm_grupo.id_sede')
                // ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
                ->join('logistica.equi_cat','equi_cat.id_categoria','=','equi_sol.id_categoria')
                ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','equi_sol.estado')
                // ->where([['equi_sol.estado','=',1]])
                // ->whereIn('adm_grupo.id_grupo',$id_grupos)
                // ->orderBy('equi_sol.fecha_solicitud','desc')
                ->get();
    
            //Nro de flujos en total que necesita para aprobar la solicitud
            $nro_flujo = DB::table('administracion.adm_flujo')
            ->where([['adm_flujo.id_operacion','=',$id_ope],
                    ['adm_flujo.estado','=',1]])
            ->count();
            
            foreach($pendientes as $p){
                //Obtiene id_doc_aprob
                $id_doc = DB::table('administracion.adm_documentos_aprob')
                ->where([['id_doc','=',$p->id_solicitud],
                        ['id_tp_documento','=',6]])//6->Solicitud de Equipos
                ->first();
        
                $aprueba = "false";
                $asigna = "false";
                $id_flujo = 0;
                $nro_ap = 0;

                if ($p->estado == 1){
                    if (isset($id_doc->id_doc_aprob)){
                        //Nro de aprobacion que necesita
                        $nro_ap = DB::table('administracion.adm_aprobacion')
                            ->where([['adm_aprobacion.id_doc_aprob','=',$id_doc->id_doc_aprob],
                                    ['adm_aprobacion.id_vobo','=',1]])
                            ->count() + 1;
        
                        //Si el nro total de flujos es >= que el nro de aprobaciones
                        if ($nro_flujo >= $nro_ap){
                            //Recorre los flujos con mi rol
                            foreach($flujos as $flujo){
                                //Si el nro de orden de mi flujo es = nro de aprobacion q necesita
                                if ($flujo->orden === $nro_ap){
                                    $aprueba = "true";
                                    $id_flujo = $flujo->id_flujo;
                                }
                            }
                        }
                    }
                }
                else if ($p->estado == 2){
                    if (in_array("LOGISTICA",$areas)){
                        $asigna = "true";
                    }
                }

                $nuevo = [
                    "id_solicitud"=>$p->id_solicitud,
                    "fecha_solicitud"=>$p->fecha_solicitud,
                    "nombre_trabajador"=>$p->nombre_trabajador,
                    "nombre_empresa"=>$p->nombre_empresa,
                    "area_descripcion"=>$p->area_descripcion,
                    "des_categoria"=>$p->des_categoria,
                    "cantidad"=>$p->cantidad,
                    "asignaciones_pendientes"=>$p->asignaciones_pendientes,
                    "fecha_inicio"=>$p->fecha_inicio,
                    "fecha_fin"=>$p->fecha_fin,
                    "codigo"=>$p->codigo,
                    "observaciones"=>$p->observaciones,
                    "orden"=>$nro_ap,
                    "id_flujo"=>$id_flujo,
                    "id_doc_aprob"=>(isset($id_doc->id_doc_aprob) ? $id_doc->id_doc_aprob : ''),
                    "estado_doc"=>$p->estado_doc,
                    "bootstrap_color"=>$p->bootstrap_color,
                    "id_categoria"=>$p->id_categoria,
                    "aprueba"=>$aprueba,
                    "asigna"=>$asigna
                ];
                //agrega nuevo a la lista
                array_push($lista, $nuevo);
            }
        // }
        return response()->json($lista);
    }
    public function prueba($id_grupo){
        $pendientes = DB::table('logistica.equi_sol')
            ->select('equi_sol.*','adm_area.descripcion as area_descripcion',
            'equi_cat.descripcion as des_categoria',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
            ->join('logistica.equi_cat','equi_cat.id_categoria','=','equi_sol.id_categoria')
            ->where([['equi_sol.estado','=',1],
                    ['adm_grupo.id_grupo','=',$id_grupo]])
            ->get();
        return $pendientes;
    }
    public function guardar_aprobacion(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_aprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo'=>$request->id_flujo, 
                'id_doc_aprob'=>$request->id_doc_aprob, 
                'id_vobo'=>$request->id_vobo, 
                'id_usuario'=>$request->id_usuario, 
                'id_area'=>$request->id_area, 
                'fecha_vobo'=>$fecha, 
                'detalle_observacion'=>$request->detalle_observacion, 
                'id_rol'=>$request->id_rol
            ],
                'id_aprobacion'
            );

        if ($request->id_vobo == 2){//Visto Denegado
            $doc = DB::table('administracion.adm_documentos_aprob')
            ->select('adm_documentos_aprob.id_doc')
            ->where('id_doc_aprob',$request->id_doc_aprob)
            ->first();
            
            if ($doc->id_doc !== null){
                DB::table('logistica.equi_sol')
                ->where('id_solicitud',$doc->id_doc)
                ->update(['estado'=>4]);//Solicitud Denegada
            }            
        }
        return response()->json($id_aprobacion);
    }
    public function prueba_sol($id_doc_aprob){
        $doc = DB::table('administracion.adm_documentos_aprob')
        ->select('adm_documentos_aprob.id_doc')
        ->where('id_doc_aprob',$id_doc_aprob)
        ->first();
        $sol = null;
        if ($doc->id_doc !== null){
            $sol = DB::table('logistica.equi_sol')
            ->where('id_solicitud',$doc->id_doc)
            ->first();
        }
        return response()->json($sol);
    }
    public function guardar_sustento(Request $request){
        $fecha = date('Y-m-d H:i:s');
        //Obtiene id_doc_aprob
        $id_doc = DB::table('administracion.adm_documentos_aprob')
            ->where('codigo_doc',$request->codigo)//solicitud de equipo
            ->first();
        $id_aprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                // 'id_flujo'=>$request->id_flujo, 
                'id_doc_aprob'=>$id_doc->id_doc_aprob, 
                'id_vobo'=>$request->id_vobo, 
                'id_usuario'=>$request->id_usuario, 
                'id_area'=>$request->id_area, 
                'fecha_vobo'=>$fecha, 
                'detalle_observacion'=>$request->detalle_observacion, 
                'id_rol'=>$request->id_rol
            ],
                'id_aprobacion'
            );
        $this->solicitud_cambia_estado($request->id_solicitud,1);
        return response()->json($id_aprobacion);
    }
    public function solicitud_cambia_estado($id, $estado)
    {
        $data = DB::table('logistica.equi_sol')
            ->where('id_solicitud',$id)
            ->update([ 'estado' => $estado ]);
        return response()->json($data);
    }
    public function solicitud_flujos($id_doc_aprob,$id_solicitud){
        $data = DB::table('administracion.adm_aprobacion')
            ->select('adm_aprobacion.*','adm_vobo.descripcion as des_vobo',
            'rrhh_rol_concepto.descripcion as des_rol_concepto',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('administracion.adm_vobo','adm_vobo.id_vobo','=','adm_aprobacion.id_vobo')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','adm_aprobacion.id_usuario')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->join('rrhh.rrhh_rol','rrhh_rol.id_rol','=','adm_aprobacion.id_rol')
            ->join('rrhh.rrhh_rol_concepto','rrhh_rol_concepto.id_rol_concepto','=','rrhh_rol.id_rol_concepto')
            ->where('id_doc_aprob',$id_doc_aprob)
            ->orderBy('adm_aprobacion.fecha_vobo','asc')
            ->get();
        $elaborado = DB::table('logistica.equi_sol')
            ->select('equi_sol.*','adm_area.descripcion as des_area',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->join('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
            ->where('equi_sol.id_solicitud',$id_solicitud)
            ->first();
        $i = 1;
        $html = '
            <tr>
                <td>'.$i.'</td>
                <td>'.$elaborado->fecha_registro.'</td>
                <td>Elaborado</td>
                <td>'.$elaborado->des_area.'</td>
                <td>'.$elaborado->nombre_trabajador.'</td>
                <td></td>
            </tr>';
        foreach($data as $d){
            $i++;
            $html .='
            <tr>
                <td>'.$i.'</td>
                <td>'.$d->fecha_vobo.'</td>
                <td>'.$d->des_vobo.'</td>
                <td>'.$d->des_rol_concepto.'</td>
                <td>'.$d->nombre_trabajador.'</td>
                <td>'.$d->detalle_observacion.'</td>
            </tr>';
        }
        return json_encode($html);
    }
    public function userSession()
    {
        $id_rol = Auth::user()->login_rol;
        $id_usuario = Auth::user()->id_usuario;
        $id_trabajador = Auth::user()->id_trabajador;
        $usuario = Auth::user()->usuario;
        $estado = Auth::user()->estado;
        $nombre_corto = Auth::user()->nombre_corto;

        $dateNow= date('Y-m-d');

        $dataSession=[
            'id_rol'=>$id_rol,
            'id_usuario'=>$id_usuario,
            'id_trabajador'=>$id_trabajador,
            'usuario'=>$usuario,
            'estado'=>$estado,
            'nombre_corto'=>$nombre_corto,
            'roles'=>[]
        ];

        $rolConceptoUser = DB::table('administracion.rol_aprobacion')
        ->select(
            'rol_aprobacion.id_rol_aprobacion',
            'rol_aprobacion.id_area',
            'adm_area.descripcion as nombre_area',
            'rol_aprobacion.id_rol_concepto',
            'rrhh_rol_concepto.descripcion as rol_concepto',
            'rol_aprobacion.estado'
        )
        ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rol_aprobacion.id_rol_concepto')
        ->leftJoin('administracion.adm_area', 'adm_area.id_area', '=', 'rol_aprobacion.id_area')
        // ->where(function($q) use ($dateNow) {
        //     $q->where('rol_aprobacion.fecha_fin','>', $dateNow)
        //     ->orWhere('rol_aprobacion.fecha_fin', null);
        // })
        ->where([
            ['rol_aprobacion.id_trabajador', '=', $dataSession['id_trabajador']]
            ])
        ->whereNotIn( 'rol_aprobacion.estado', [2,7])
        ->get();

        $dataSession['roles']=$rolConceptoUser;

        return $dataSession;
    }
    public function mostrar_nombre_grupo($id_grupo){
        $sql = DB::table('administracion.adm_grupo')
        ->select('adm_grupo.id_grupo','adm_grupo.descripcion')
        ->where('adm_grupo.id_grupo', $id_grupo)
        ->get();
    

        if ($sql->count() > 0) {
            $id_grupo = $sql->first()->id_grupo;
            $descripcion = $sql->first()->descripcion;
        }else{
            $id_grupo=0;
            $descripcion='';
        }
        $array = array('id_grupo' => $id_grupo, 'descripcion' => $descripcion);
        return $array;
    }

    public function listar_partidas($id_grupo,$id_proyecto=null){
        
        if($id_proyecto != null || $id_proyecto != ''){ 
            
            $presup = DB::table('proyectos.proy_presup')
            ->select('presup.*')
            ->leftJoin('finanzas.presup', 'presup.id_presup', '=', 'proy_presup.id_presup')
            ->where([
                    ['proy_presup.id_proyecto','=',$id_proyecto],
                    ['proy_presup.estado','=',8],
                    ['tp_presup','=',4]

                    ])
            ->get();

        }else{

            $presup = DB::table('finanzas.presup')
            ->where([
                    ['id_grupo','=',$id_grupo],
                    ['estado','=',1],
                    ['tp_presup','=',2]
                    ])
            ->get();
        }

        $html = '';
        $userSession=$this->userSession()['roles'];
        $isVisible ='';

        foreach($presup as $p){
            $titulos = DB::table('finanzas.presup_titu')
                ->where([['id_presup','=',$p->id_presup],
                        ['estado','=',1]])
                ->orderBy('presup_titu.codigo')
                ->get();
            $partidas = DB::table('finanzas.presup_par')
                ->select('presup_par.*','presup_pardet.descripcion as des_pardet')
                ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
                ->where([['presup_par.id_presup','=',$p->id_presup],
                        ['presup_par.estado','=',1]])
                ->orderBy('presup_par.codigo')
                ->get();
            $html .='
            <div id='.$p->codigo.' class="panel panel-primary" style="width:100%;">
                <h5 onclick="apertura('.$p->id_presup.');" class="panel-heading" style="cursor: pointer; margin: 0;">
                '.$p->descripcion.' </h5>
                <div id="pres-'.$p->id_presup.'" class="oculto" style="width:100%;">
                    <table class="table table-bordered partidas" width="100%">
                        <tbody> 
                ';
                foreach($titulos as $ti){
                    $html .='
                    <tr id="com-'.$ti->id_titulo.'">
                        <td><strong>'.$ti->codigo.'</strong></td>
                        <td><strong>'.$ti->descripcion.'</strong></td>
                        <td class="right '.$isVisible.'"><strong>'.$ti->total.'</strong></td>
                    </tr>';
                    foreach($partidas as $par){
                        if ($ti->codigo == $par->cod_padre){
                            $html .='
                            <tr id="par-'.$par->id_partida.'" onclick="selectPartida('.$par->id_partida.');" style="cursor: pointer; margin: 0;">
                                <td name="codigo">'.$par->codigo.'</td>
                                <td name="descripcion">'.$par->des_pardet.'</td>
                                <td name="importe_total" class="right '.$isVisible.'">'.$par->importe_total.'</td>
                            </tr>';
                        }
                    }
                }
            $html .='
                    </tbody>
                </table>
            </div>
        </div>';
        }
        return json_encode($html);
    }
    
    public function guardar_tipo_doc($nombre){
        $id_tipo = DB::table('logistica.equi_tp_seguro')->insertGetId(
            [
                'descripcion'=>$nombre, 
                'estado'=>1
            ],
                'id_tp_seguro'
            );

        $data = DB::table('logistica.equi_tp_seguro')->where('estado',1)->get();
        $html = '';

        foreach($data as $d){
            if ($id_tipo == $d->id_tp_seguro){
                $html.='<option value="'.$d->id_tp_seguro.'" selected>'.$d->descripcion.'</option>';
            } else {
                $html.='<option value="'.$d->id_tp_seguro.'">'.$d->descripcion.'</option>';
            }
        }
        return json_encode($html);
    }
    public function kilometraje_actual($id_equipo){
        $kilometraje = 0;
        $kil = DB::table('logistica.equi_asig_control')
        ->join('logistica.equi_asig','equi_asig.id_asignacion','=','equi_asig_control.id_asignacion')
        ->where([['equi_asig.id_equipo','=',$id_equipo],['equi_asig_control.estado','=',1]])
        ->orderBy('kilometraje_fin','desc')
        ->first();
        if ($kil !== null){
            $kilometraje = $kil->kilometraje_fin;
        } else {
            $equipo = DB::table('logistica.equipo')
            ->where('id_equipo',$id_equipo)
            ->first();
            if ($equipo->kilometraje_inicial !== null){
                $kilometraje = $equipo->kilometraje_inicial;
            }
        }
        return $kilometraje;
    }
    public function usuario_aprobacion(){
        $roles = Auth::user()->trabajador->roles;
        $id_roles = [];
        foreach($roles as $rol){
            array_push($id_roles, $rol->pivot->id_rol);                
        }
        $flujos = DB::table('administracion.adm_flujo')
        ->join('administracion.adm_operacion','adm_operacion.id_operacion','=','adm_flujo.id_operacion')
        ->where('adm_operacion.id_tp_documento',6)//Solicitud de Equipos)
        ->whereIn('adm_flujo.id_rol',$id_roles)
        ->count();
        return response()->json($flujos);
    }

        
    public function imprimir_control_bitacora($id_asignacion,$fini,$ffin){
        $id = $this->decode5t($id_asignacion);
        $sol = DB::table('logistica.equi_asig')
        ->select('equi_asig.*','equipo.descripcion as equi_asignado',
        DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"),
        'adm_grupo.descripcion as des_grupo','sis_usua.nombre_corto',
        'adm_area.descripcion as des_area')
        ->leftjoin('logistica.equipo','equipo.id_equipo','=','equi_asig.id_equipo')
        ->leftjoin('logistica.equi_sol','equi_sol.id_solicitud','=','equi_asig.id_solicitud')
        ->leftjoin('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_sol.id_trabajador')
        ->leftjoin('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        ->leftjoin('administracion.adm_area','adm_area.id_area','=','equi_sol.id_area')
        ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
        ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','equi_asig.usuario')
        ->where('equi_asig.id_asignacion',$id)
        ->first();

        $detalle ='';
        
        $controles = DB::table('logistica.equi_asig_control')
            ->select('equi_asig_control.*',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','equi_asig_control.chofer')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where([['equi_asig_control.id_asignacion','=',$id],
                    ['equi_asig_control.estado','=',1],
                    ['equi_asig_control.fecha_recorrido','>=',$fini],
                    ['equi_asig_control.fecha_recorrido','<=',$ffin]])
            ->orderBy('equi_asig_control.fecha_recorrido','asc')
            ->get();
        $i = 1;
        foreach($controles as $d){
            $gal = ($d->importe !== null ? ('S/'.$d->importe.' : '.$d->galones.' gal.') : '');
            $detalle .='
            <tr>
                <td>'.$i.'</td>
                <td>'.$d->fecha_recorrido.'</td>
                <td>'.$d->kilometraje_inicio.'</td>
                <td>'.$d->kilometraje_fin.'</td>
                <td>'.($d->kilometraje_fin - $d->kilometraje_inicio).' km.</td>
                <td>'.$d->hora_inicio.'</td>
                <td>'.$d->hora_fin.'</td>
                <td>'.$d->nombre_trabajador.'</td>
                <td colSpan="2" >'.$d->descripcion_recorrido.'</td>
                <td>'.$gal.'</td>
                <td>'.$d->observaciones.'</td>
            </tr>
            ';
            $i++;
        }

        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:12px;
                }
                #detalle thead tr th{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                </style>
            </head>
            <body>
                <h3 style="margin:0px;"><center>REGISTRO DE BITÁCORA</center></h3>
                <br/>
                <table border="0">
                <tr>
                    <td colSpan="2" width=110px>Equipo Asignado</td>
                    <td>:</td>
                    <td colSpan="5">'.$sol->equi_asignado.'</td>
                </tr>
                <tr>
                    <td colSpan="2">Fecha Inicio / Fin</td>
                    <td width=10px>:</td>
                    <td colSpan="4" width=300px>'.$sol->fecha_inicio.' / '.$sol->fecha_fin.'</td>
                    <td></td>
                    <td></td>
                    <td width=130px>Fecha de Asignación</td>
                    <td>:</td>
                    <td>'.$sol->fecha_asignacion.'</td>
                </tr>
                <tr>
                    <td colSpan="2" width=110px>Solicitado por</td>
                    <td width=10px>:</td>
                    <td colSpan="4" width=300px>'.$sol->nombre_trabajador.'</td>
                    <td></td>
                    <td></td>
                    <td>Grupo</td>
                    <td>:</td>
                    <td>'.$sol->des_grupo.'</td>
                </tr>
                <tr>
                        <td colSpan="2" width=100px>Asignado por</td>
                        <td width=10px>:</td>
                        <td colSpan="4" >'.$sol->nombre_corto.'</td>
                        <td></td>
                        <td></td>
                        <td>Area</td>
                        <td>:</td>
                        <td>'.$sol->des_area.'</td>
                    </tr>
                    <tr>
                        <td colSpan="2" >Observación</td>
                        <td>:</td>
                        <td colSpan="5">'.$sol->detalle_asignacion.'</td>
                    </tr>
                </table>
            ';

        if ($detalle !== ''){
            $html .='
            <br/>
            <h5 style="margin:0px;"><center>CONTROL DEL RECORRIDO</center></h5>
            <table border="1" id="detalle">
                <thead>
                    <tr>
                        <th width="10px">N°</th>
                        <th width="70px">Fecha</th>
                        <th width="70px">Kil.Inicio</th>
                        <th width="70px">Kil.Fin</th>
                        <th width="70px">Recorrido</th>
                        <th width="70px">Hora Inicio</th>
                        <th width="70px">Hora Fin</th>
                        <th width="90px">Chofer</th>
                        <th colSpan="2" width="350px">Descripción</th>
                        <th>Combustible</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                '.$detalle.'
                </tbody>
            </table>
            <br/>';
        }
                $html .='
            </body>
        </html>';
        return $html;
    }
    public function download_control_bitacora($id_solicitud,$fini,$ffin){
        $data = $this->imprimir_control_bitacora($id_solicitud,$fini,$ffin);
        return view('almacen/reportes/control_bitacora_excel', compact('data'));
    }

    ////////////////////////////////////////
    public function leftZero($lenght, $number){
        $nLen = strlen($number);
        $zeros = '';
        for($i=0; $i<($lenght-$nLen); $i++){
            $zeros = $zeros.'0';
        }
        return $zeros.$number;
    }
    function encode5t($str){
        for($i=0; $i<5;$i++){
            $str=strrev(base64_encode($str));
        }
        return $str;
    }
   
    function decode5t($str){
        for($i=0; $i<5;$i++){
            $str=base64_decode(strrev($str));
        }
        return $str;
    }
}
