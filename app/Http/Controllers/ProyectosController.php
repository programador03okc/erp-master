<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use DateTime;
use Dompdf\Dompdf;
use PDF;

date_default_timezone_set('America/Lima');

class ProyectosController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_sis_contrato(){
        return view('proyectos/variables/sis_contrato');
    }
    function view_tipo_insumo(){
        return view('proyectos/variables/tipo_insumo');
    }
    function view_cat_insumo(){
        return view('proyectos/variables/cat_insumo');
    }
    function view_iu(){
        return view('proyectos/variables/iu');
    }
    function view_insumo(){
        $tipos = $this->mostrar_tipos_insumos_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        $ius = $this->mostrar_ius_cbo();
        $categorias = $this->select_categorias_insumos();
        return view('proyectos/insumo/insumo', compact('tipos','unidades','ius','categorias'));
    }
    function view_cat_acu(){
        return view('proyectos/variables/cat_acu');
    }
    function view_acu(){
        $unidades = $this->mostrar_unidades_cbo();
        $tipos = $this->mostrar_tipos_insumos_cbo();
        $ius = $this->mostrar_ius_cbo();
        $categorias = $this->select_categorias_acus();
        return view('proyectos/acu/acu', compact('unidades','tipos','ius','categorias'));
    }
    function view_cu(){
        $categorias = $this->select_categorias_acus();
        return view('proyectos/acu/cu', compact('categorias'));
    }
    function view_opcion(){
        $clientes = $this->mostrar_clientes_cbo();
        $unid_program = $this->mostrar_unid_program_cbo();
        $tipos = $this->mostrar_tipos_cbo();
        $empresas = $this->mostrar_empresas_cbo();
        $modalidades = $this->mostrar_modalidad_cbo();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = $this->sis_identidad_cbo();
        return view('proyectos/opcion/opcion', compact('clientes','unid_program',
        'tipos','empresas','modalidades','tp_contribuyente','sis_identidad'));
    }
    function view_opciones_todo(){
        return view('proyectos/reportes/opciones_todo');
    }
    function view_saldos_pres(){
        return view('proyectos/reportes/saldos_pres');
    }
    function view_residentes(){
        $cargos = $this->select_cargos();
        return view('proyectos/residentes/residentes', compact('cargos'));
    }
    function view_presint(){
        $monedas = $this->mostrar_monedas_cbo();
        $sistemas = $this->mostrar_sis_contrato_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        $tipos = $this->mostrar_tipos_insumos_cbo();
        $ius = $this->mostrar_ius_cbo();
        $categorias = $this->select_categorias_acus();
        return view('proyectos/presupuesto/presint', compact('monedas','sistemas','unidades','tipos','ius','categorias'));
    }
    function view_cronovalint(){
        $unid_program = $this->mostrar_unid_program_cbo();
        return view('proyectos/cronograma/cronovalint', compact('unid_program'));
    }
    function view_propuesta(){
        $monedas = $this->mostrar_monedas_cbo();
        $sistemas = $this->mostrar_sis_contrato_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        $unid_program = $this->mostrar_unid_program_cbo();
        $usuarios = $this->select_usuarios();
        return view('proyectos/presupuesto/propuesta', compact('monedas','sistemas','unidades','unid_program','usuarios'));
    }
    function view_preseje(){
        $monedas = $this->mostrar_monedas_cbo();
        $sistemas = $this->mostrar_sis_contrato_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        $tipos = $this->mostrar_tipos_insumos_cbo();
        $ius = $this->mostrar_ius_cbo();
        $categorias = $this->select_categorias_acus();
        return view('proyectos/presupuesto/preseje', compact('monedas','sistemas','unidades','tipos','ius','categorias'));
    }
    function view_cronoeje(){
        $unid_program = $this->mostrar_unid_program_cbo();
        return view('proyectos/cronograma/cronoeje', compact('unid_program'));
    }
    function view_cronopro(){
        $unid_program = $this->mostrar_unid_program_cbo();
        return view('proyectos/cronograma/cronopro', compact('unid_program'));
    }
    function view_cronovaleje(){
        $unid_program = $this->mostrar_unid_program_cbo();
        return view('proyectos/cronograma/cronovaleje', compact('unid_program'));
    }
    function view_cronovalpro(){
        $unid_program = $this->mostrar_unid_program_cbo();
        return view('proyectos/cronograma/cronovalpro', compact('unid_program'));
    }
    function view_valorizacion(){
        return view('proyectos/valorizacion/valorizacion');
    }
    function view_curvas(){
        return view('proyectos/reportes/curvas');
    }
    function view_proyecto(){
        $clientes = $this->mostrar_clientes_cbo();
        $monedas = $this->mostrar_monedas_cbo();
        $tipos = $this->mostrar_tipos_cbo();
        $sistemas = $this->mostrar_sis_contrato_cbo();
        $modalidades = $this->mostrar_modalidad_cbo();
        $unid_program = $this->mostrar_unid_program_cbo();
        $tipo_contrato = $this->mostrar_tipo_contrato_cbo();
        $empresas = $this->mostrar_empresas_cbo();
        return view('proyectos/proyecto/proyecto', compact('clientes','monedas','tipos','sistemas','modalidades','unid_program','tipo_contrato','empresas'));
    }
    function view_cronoint(){
        $unid_program = $this->mostrar_unid_program_cbo();
        return view('proyectos/cronograma/cronoint', compact('unid_program'));
    }
    function view_presEstructura(){
        $sedes = $this->mostrar_sedes_cbo();
        return view('proyectos/presEstructura/presEstructura', compact('sedes'));
    }
    public function select_cargos(){
        $data = DB::table('proyectos.proy_res_cargo')
            ->select('proy_res_cargo.id_cargo','proy_res_cargo.descripcion')
            ->where('proy_res_cargo.estado', 1)
            ->get();
        return $data;
    }
    public function sis_identidad_cbo(){
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }
    public function tp_contribuyente_cbo(){
        $data = DB::table('contabilidad.adm_tp_contri')
            ->select('adm_tp_contri.id_tipo_contribuyente', 'adm_tp_contri.descripcion')
            ->where('adm_tp_contri.estado', '=', 1)
            ->orderBy('adm_tp_contri.descripcion', 'asc')->get();
        return $data;
    }
    public function select_usuarios(){
        $data = DB::table('configuracion.sis_usua')
            ->select('sis_usua.id_usuario','sis_usua.nombre_corto')
            ->where([['sis_usua.estado', '=', 1],['sis_usua.nombre_corto', '<>', null]])
            ->get();
        return $data;
    }
    public function mostrar_sedes_cbo(){
        $data = DB::table('administracion.sis_sede')
            ->select('sis_sede.*','adm_contri.razon_social','adm_contri.nro_documento')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->where([['sis_sede.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function cargar_grupos($id_sede){
        $data = DB::table('administracion.adm_grupo')
            ->select('adm_grupo.*','sis_sede.id_empresa')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','adm_grupo.id_sede')
            ->where([['adm_grupo.id_sede', '=', $id_sede],
                     ['adm_grupo.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }

    public function tipos_insumos_cbo(){
        $data = DB::table('proyectos.proy_tp_insumo')
        ->select('proy_tp_insumo.id_tp_insumo','proy_tp_insumo.descripcion')
        ->where('estado',1)->get();
        return $data;
    }
    public function mostrar_unidades_cbo(){
        $data = DB::table('almacen.alm_und_medida')
            ->select('alm_und_medida.id_unidad_medida','alm_und_medida.abreviatura','alm_und_medida.descripcion')
            ->where([['alm_und_medida.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function mostrar_ius_cbo(){
        $data = DB::table('proyectos.proy_iu')
            ->select('proy_iu.id_iu','proy_iu.descripcion')
            ->where('estado', 1)
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    public function mostrar_tipos_insumos_cbo(){
        $data = DB::table('proyectos.proy_tp_insumo')
            ->select('proy_tp_insumo.id_tp_insumo','proy_tp_insumo.descripcion')
            ->where('estado',1)
            ->get();
        return $data;
    }
    public function select_categorias_insumos(){
        $data = DB::table('proyectos.proy_insumo_cat')
            ->select('proy_insumo_cat.id_categoria','proy_insumo_cat.descripcion')
            ->where('estado',1)
            ->get();
        return $data;
    }
    public function select_categorias_acus(){
        $data = DB::table('proyectos.proy_cu_cat')
            ->select('proy_cu_cat.id_categoria','proy_cu_cat.descripcion')
            ->where('estado',1)
            ->get();
        return $data;
    }
    public function mostrar_clientes_cbo(){
        $data = DB::table('comercial.com_cliente')
            ->select('com_cliente.id_cliente','adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('com_cliente.estado',1)
            ->get();
        return $data;
    }
    public function mostrar_monedas_cbo()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda','sis_moneda.simbolo','sis_moneda.descripcion')
            ->where([['sis_moneda.estado', '=', 1]])
            ->orderBy('sis_moneda.id_moneda')
            ->get();
        return $data;
    }
    public function mostrar_tipos_cbo()
    {
        $data = DB::table('proyectos.proy_tp_proyecto')
            ->select('proy_tp_proyecto.id_tp_proyecto','proy_tp_proyecto.descripcion')
            ->where([['proy_tp_proyecto.estado', '=', 1]])
            ->get();
            return $data;
    }
    public function mostrar_sis_contrato_cbo(){
        $data = DB::table('proyectos.proy_sis_contrato')
            ->select('proy_sis_contrato.id_sis_contrato','proy_sis_contrato.descripcion')
            ->where([['proy_sis_contrato.estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_modalidad_cbo(){
        $data = DB::table('proyectos.proy_modalidad')
            ->select('proy_modalidad.id_modalidad','proy_modalidad.descripcion')
            ->where([['proy_modalidad.estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_unid_program_cbo(){
        $data = DB::table('proyectos.proy_unid_program')
            ->select('proy_unid_program.id_unid_program','proy_unid_program.descripcion')
            ->where([['proy_unid_program.estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_tipo_contrato_cbo(){
        $data = DB::table('proyectos.proy_tp_contrato')
        ->select('proy_tp_contrato.id_tp_contrato','proy_tp_contrato.descripcion')
        ->where([['proy_tp_contrato.estado','=',1]])
            ->get();
        return $data;
    }
    public function mostrar_empresas_cbo(){
        $data = DB::table('administracion.adm_empresa')
        ->select('adm_empresa.id_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')
        ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_empresa.id_empresa', 'asc')->get();
        return $data;
    }
    //modalidad
    public function mostrar_modalidad(){
        $data = DB::table('proyectos.proy_modalidad')
        ->select('proy_modalidad.*')
            ->get();
        // $data = proy_modalidad::all();
        return response()->json($data);
    }
    //tipos de contrato
    public function mostrar_tipos_contrato(){
        $data = DB::table('proyectos.proy_tp_contrato')
        ->select('proy_tp_contrato.*')
            ->get();
        // $data = proy_tp_contrato::all();
        return response()->json($data);
    }
    //tipos de proyecto
    public function mostrar_tipos_proyecto(){
        $data = DB::table('proyectos.proy_tp_proyecto')
        ->select('proy_tp_proyecto.*')
            ->get();
        // $data = proy_tp_proyecto::all();
        return response()->json($data);
    }
    //clientes
    public function mostrar_clientes(){
        $data = DB::table('comercial.com_cliente')
        ->select('com_cliente.*','adm_contri.razon_social','adm_contri.nro_documento')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where([['com_cliente.estado','=',1]])
            ->orderBy('com_cliente.id_cliente')
            ->get();
        return response()->json($data);
    }
    public function mostrar_cliente($id){
        $data = DB::table('comercial.com_cliente')
        ->select('com_cliente.*','adm_contri.razon_social')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('id_cliente','=',$id)
            ->get();
        return response()->json($data);
    }
    //IGV
    public function mostrar_impuesto($cod,$fecha){
        $data = DB::table('contabilidad.cont_impuesto')
        ->select('cont_impuesto.*')
            ->where([['codigo','=',$cod],['fecha_inicio','<',$fecha]])
            ->orderBy('fecha_inicio','desc')
            ->first();
            // ->get();
        return response()->json($data);
    }
    //moneda
    public function mostrar_moneda(){
        $data = DB::table('configuracion.sis_moneda')
        ->select('sis_moneda.*')
            ->get();
        // $data = moneda::all();
        return response()->json($data);
    }
    //tipos de presupuesto
    public function mostrar_tp_presupuesto(){
        $data = DB::table('proyectos.proy_tp_pres')
        ->select('proy_tp_pres.*')
            ->get();
        // $data = proy_tp_presupuesto::all();
        return response()->json($data);
    }
    //unidad de programacion
    public function mostrar_unid_program(){
        $data = DB::table('proyectos.proy_unid_program')
        ->select('proy_unid_program.*')
            ->get();
        return response()->json($data);
    }
    public function mostrar_unid_programById($id){
        $data = DB::table('proyectos.proy_unid_program')
        ->select('proy_unid_program.*')
        ->where([['id_unid_program', '=', $id]])
            ->get();
        return response()->json($data);
    }
    //iu
    public function mostrar_ius(){
        $data = DB::table('proyectos.proy_iu')
            ->select('proy_iu.*')
            ->where('estado',1)
            ->orderBy('codigo')
            ->get();        
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_iu($iu)
    {
        $data = DB::table('proyectos.proy_iu')
            ->select('proy_iu.*')
            ->where([['id_iu', '=', $iu]])
            ->get();        
        // $data = proy_iu::where('id_iu', $iu)->first();
        return response()->json($data);
    }
    public function guardar_iu(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_iu = DB::table('proyectos.proy_iu')->insertGetId(
            [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_iu'
            );

        return response()->json($id_iu);
    }
    public function update_iu(Request $request)
    {
        $iu = DB::table('proyectos.proy_iu')
            ->where('id_iu',$request->id_iu)
            ->update([
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado
            ]);
        return response()->json($iu);
    }
    public function anular_iu(Request $request,$id_iu)
    {
        $iu = DB::table('proyectos.proy_iu')
            ->where('id_iu',$id_iu)
            ->update([ 'estado' => 7 ]);
        return response()->json($iu);
    }
    public function delete_iu($id)
    {
        DB::table('proyectos.proy_iu')
            ->where('id_iu', '=', $id)
            ->delete();
        // $data = proy_iu::where('id_iu', $id)->delete();
        return response()->json($data);
    }

    //sistemas de contrato
    public function mostrar_sis_contratos()
    {
        $data = DB::table('proyectos.proy_sis_contrato')
        ->select('proy_sis_contrato.*')
        ->where('estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_sis_contrato($id)
    {
        $data = DB::table('proyectos.proy_sis_contrato')
        ->select('proy_sis_contrato.*')
            ->where([['id_sis_contrato', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_sis_contrato(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('proyectos.proy_sis_contrato')->insertGetId(
            [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_sis_contrato'
            );
            
        return response()->json($data);
    }
    public function update_sis_contrato(Request $request)
    {
        $data = DB::table('proyectos.proy_sis_contrato')
            ->where('id_sis_contrato', $request->id_sis_contrato)
            ->update([
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion
            ]);

        return response()->json($data);
    }
    public function anular_sis_contrato(Request $request, $id)
    {
        $data = DB::table('proyectos.proy_sis_contrato')
            ->where('id_sis_contrato', $id)
            ->update([ 'estado' => 7 ]);

        return response()->json($data);
    }

    //tipos de insumos
    public function mostrar_tipos_insumos()
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->select('proy_tp_insumo.*')
            ->where('estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_tp_insumo($id)
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->select('proy_tp_insumo.*')
            ->where([['proy_tp_insumo.id_tp_insumo', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_tp_insumo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_tp_insumo = DB::table('proyectos.proy_tp_insumo')->insertGetId(
            [
                'codigo' => strtoupper($request->codigo),
                'descripcion' => strtoupper($request->descripcion),
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_tp_insumo'
            );
        return response()->json($id_tp_insumo);
    }
    public function update_tp_insumo(Request $request)
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->where('id_tp_insumo', $request->id_tp_insumo)
            ->update([
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion)
            ]);

        return response()->json($data);
    }
    public function anular_tp_insumo(Request $request, $id)
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->where('id_tp_insumo', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    //Categoria de Insumos
    public function listar_cat_insumos()
    {
        $data = DB::table('proyectos.proy_insumo_cat')
            ->select('proy_insumo_cat.*')
            ->where('estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_cat_insumo($id)
    {
        $data = DB::table('proyectos.proy_insumo_cat')
            ->select('proy_insumo_cat.*')
            ->where([['proy_insumo_cat.id_categoria', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_cat_insumo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_categoria = DB::table('proyectos.proy_insumo_cat')->insertGetId(
            [
                'descripcion' => strtoupper($request->descripcion),
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_categoria'
            );
        return response()->json($id_categoria);
    }
    public function update_cat_insumo(Request $request)
    {
        $data = DB::table('proyectos.proy_insumo_cat')
            ->where('id_categoria', $request->id_categoria)
            ->update([
                'descripcion' => strtoupper($request->descripcion)
            ]);

        return response()->json($data);
    }
    public function anular_cat_insumo(Request $request, $id)
    {
        $insumos = DB::table('proyectos.proy_insumo')
        ->where([['id_categoria','=',$id],['estado','=',1]])
        ->count();
        if ($insumos > 0){
            $data = 0;
        } else {
            $data = DB::table('proyectos.proy_insumo_cat')
                ->where('id_categoria', $id)
                ->update([ 'estado' => 7 ]);
        }
        return response()->json($data);
    }
    //Insumos
    public function listar_insumos()
    {
        $data = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.*','alm_und_medida.abreviatura',
        'proy_tp_insumo.codigo as cod_tp_insumo','proy_insumo_cat.descripcion as cat_descripcion',
        'proy_iu.descripcion as iu_descripcion',
        DB::raw("(SELECT proy_cu_detalle.precio_unit FROM proyectos.proy_cu_detalle 
        WHERE proy_cu_detalle.id_insumo=proy_insumo.id_insumo AND proy_cu_detalle.estado!=7
        ORDER BY fecha_registro desc limit 1) as precio_insumo"))
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
        ->leftjoin('proyectos.proy_insumo_cat','proy_insumo_cat.id_categoria','=','proy_insumo.id_categoria')
        ->join('proyectos.proy_iu','proy_iu.id_iu','=','proy_insumo.iu')
        ->where([['proy_insumo.estado', '=', 1]])
            ->orderBy('codigo')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_insumo_precios($id){
        $lista = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.precio_unit','proy_presup.codigo','proy_presup.fecha_emision','proy_op_com.descripcion')
            ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cu_partida','=','proy_cu_detalle.id_cu_partida')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->where([['proy_cu_detalle.id_insumo', '=', $id],
                     ['proy_cu_detalle.estado','!=',7],
                     ['proy_cd_partida.estado','!=',7],
                     ['proy_presup.estado','!=',7]])
                     ->groupBy('proy_cu_detalle.precio_unit','proy_presup.codigo','proy_presup.fecha_emision','proy_op_com.descripcion')
                     ->get();
        $output['data'] = $lista;
        return response()->json($output);
    }
    public function mostrar_insumo($id)
    {
        $data = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.*', 'alm_und_medida.abreviatura',
        'proy_tp_insumo.codigo as cod_tp_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->where([['proy_insumo.id_insumo', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function next_cod_insumo(){
        $data = DB::table('proyectos.proy_insumo')
        ->orderBy('codigo','desc')
        ->where('estado',1)
        ->first();
        $codigo = ((int)$data->codigo)+1;
        return ((string)$codigo);
    }
    public function guardar_insumo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_cod_insumo();
        $id_insumo = 0;

        $count = DB::table('proyectos.proy_insumo')
        ->where([['descripcion','=',strtoupper($request->descripcion)],
                 ['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_insumo = DB::table('proyectos.proy_insumo')->insertGetId(
            [
                'codigo' => $codigo,
                'descripcion' => strtoupper($request->descripcion),
                'tp_insumo' => $request->tp_insumo,
                'id_categoria' => $request->id_categoria,
                'unid_medida' => $request->unid_medida,
                'precio' => $request->precio,
                'flete' => $request->flete,
                'peso_unitario' => $request->peso_unitario,
                'iu' => $request->iu,
                'fecha_registro' => $fecha,
                'estado' => 1,
            ],
                'id_insumo'
            );
        }
        return response()->json($id_insumo);
    }
    public function update_insumo(Request $request)
    {
        $id_insumo = DB::table('proyectos.proy_insumo')
        ->where('id_insumo',$request->id_insumo)
        ->update([
            'descripcion' => strtoupper($request->descripcion),
            'tp_insumo' => $request->tp_insumo,
            'id_categoria' => $request->id_categoria,
            'unid_medida' => $request->unid_medida,
            'precio' => $request->precio,
            'flete' => $request->flete,
            'peso_unitario' => $request->peso_unitario,
            'iu' => $request->iu,
        ]);
        return response()->json($id_insumo);
    }
    public function anular_insumo(Request $request, $id)
    {
        DB::table('proyectos.proy_insumo')
            ->where('id_insumo', $id)
            ->update([ 'estado' => 7 ]);
        // $insumo = proy_insumo::where('id_insumo', $id_insumo)->first();
        // $insumo->estado = 2;
        // $insumo->save();
        return response()->json($id);
    }
    public function delete_insumo($id)
    {
        DB::table('proyectos.proy_insumo')
                ->where('id_insumo', '=', $id)
                ->delete();
        // $insumo = proy_insumo::find($id);
        // $insumo->delete();
        return response()->json('El insumo ha sido eliminado.');
    }
    public function buscar_iu(Request $request,$id_iu)
    {
        $insumos = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.id_insumo')
            ->where([['proy_insumo.iu', '=', $id_iu]])
            ->get()->count();
        return response()->json($insumos);
    }
    public function buscar_tp_insumo(Request $request,$id)
    {
        $insumos = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.id_insumo')
            ->where([['proy_insumo.tp_insumo', '=', $id]])
            ->get()->count();
        return response()->json($insumos);
    }
    //Categoria de Acus
    public function listar_cat_acus()
    {
        $data = DB::table('proyectos.proy_cu_cat')
            ->select('proy_cu_cat.*')
            ->where('estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_cat_acu($id)
    {
        $data = DB::table('proyectos.proy_cu_cat')
            ->select('proy_cu_cat.*')
            ->where([['proy_cu_cat.id_categoria', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_cat_acu(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_categoria = DB::table('proyectos.proy_cu_cat')->insertGetId(
            [
                'descripcion' => strtoupper($request->descripcion),
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_categoria'
            );
        return response()->json($id_categoria);
    }
    public function update_cat_acu(Request $request)
    {
        $data = DB::table('proyectos.proy_cu_cat')
            ->where('id_categoria', $request->id_categoria)
            ->update([
                'descripcion' => strtoupper($request->descripcion)
            ]);

        return response()->json($data);
    }
    public function anular_cat_acu(Request $request, $id)
    {
        $cus = DB::table('proyectos.proy_cu')
        ->where([['id_categoria','=',$id],['estado','=',1]])
        ->count();
        if ($cus > 0){
            $data = 0;
        } else {
            $data = DB::table('proyectos.proy_cu_cat')
                ->where('id_categoria', $id)
                ->update([ 'estado' => 7 ]);
        }
        return response()->json($data);
    }

    //Nombre de Analisis de Costos Unitarios
    public function listar_cus()
    {
        $data = DB::table('proyectos.proy_cu')
            ->select('proy_cu.*','proy_cu_cat.descripcion as cat_descripcion',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color','sis_usua.nombre_corto')
            ->leftjoin('proyectos.proy_cu_cat','proy_cu_cat.id_categoria','=','proy_cu.id_categoria')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_cu.estado')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_cu.usuario_registro')
            ->where([['proy_cu.estado', '!=', 7]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function guardar_cu(Request $request)
    {
        $codigo = $this->next_cod_acu();
        $count = DB::table('proyectos.proy_cu')
        ->where([['descripcion','=',strtoupper($request->cu_descripcion)],
                 ['estado','=',1]])
        ->count();
        $id_usuario = Auth::user()->id_usuario;
        $id_cu = 0;
        $cu = null;

        if ($count == 0){
            $id_cu = DB::table('proyectos.proy_cu')->insertGetId(
            [
                'codigo' => $codigo,
                'descripcion' => strtoupper($request->cu_descripcion),
                'id_categoria' => $request->id_categoria,
                'observacion' => $request->observacion,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'usuario_registro' => $id_usuario,
            ],
                'id_cu'
            );
            $cu = DB::table('proyectos.proy_cu')->where('id_cu',$id_cu)->first();
        }
        return response()->json(['id_cu'=>$id_cu,'cu'=>$cu]);
    }
    public function update_cu(Request $request)
    {
        $count = DB::table('proyectos.proy_cu')
        ->where([['descripcion','=',strtoupper($request->cu_descripcion)],
                 ['estado','!=',7],
                 ['id_cu','!=',$request->id_cu]])
        ->count();
        $id_cu = 0;

        if ($count <= 0){
            $id_cu = DB::table('proyectos.proy_cu')->where('id_cu',$request->id_cu)
            ->update([
                'descripcion' => strtoupper($request->cu_descripcion),
                'id_categoria' => $request->id_categoria,
                'observacion' => $request->observacion,
            ]);
        }
        return response()->json(['id_cu'=>$id_cu]);
    }
    public function anular_cu($id_cu)
    {
        $count = DB::table('proyectos.proy_cu_partida')
        ->where([['id_cu','=',$id_cu],['estado','!=',7]])
        ->count();
        $data = 0;
        if ($count == 0){
            $data = DB::table('proyectos.proy_cu')->where('id_cu',$id_cu)
            ->update(['estado'=>7]);
        }
        return response()->json($data);
    }
    public function listar_partidas_cu($id_cu)
    {
        $data = DB::table('proyectos.proy_cu_partida')
        ->select('proy_cu_partida.*','proy_cd_partida.cantidad','proy_cd_partida.importe_parcial',
        'alm_und_medida.abreviatura','proy_presup.id_presupuesto','proy_presup.codigo','proy_op_com.descripcion')
        ->leftJoin('proyectos.proy_cd_partida','proy_cd_partida.id_cu_partida','=','proy_cu_partida.id_cu_partida')
        ->leftJoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
        ->leftJoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
        ->leftJoin('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
        ->where([['proy_cu_partida.id_cu','=',$id_cu],['proy_cu_partida.estado','=',1]])
        ->get();
        return response()->json($data);
    }
    //Partidas cu_partida
    public function listar_acus_sin_presup()
    {
        $data = DB::table('proyectos.proy_cu_partida')
            ->select('proy_cu_partida.*','proy_cu.codigo','proy_cu.descripcion','proy_cu.observacion',
            'alm_und_medida.abreviatura','proy_cu_cat.descripcion as cat_descripcion','proy_cu.id_categoria',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->leftjoin('proyectos.proy_cu_cat','proy_cu_cat.id_categoria','=','proy_cu.id_categoria')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_cu_partida.estado')
            ->where([['proy_cu_partida.estado', '=', 1]])
            ->get();
        
        /*$lista = [];
        foreach($data as $d){
            // $nro_pres = $this->valida_acu_editar($d->id_cu_partida);
            $nro_pres = DB::table('proyectos.proy_cu_partida')
            ->select('proy_presup.codigo')
            ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cu_partida','=','proy_cu_partida.id_cu_partida')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->where([['proy_cu_partida.id_cu_partida','=',$d->id_cu_partida],
                     ['proy_cd_partida.estado','!=',7],
                    //  ['proy_presup.estado','!=',1],
                     ['proy_presup.estado','!=',7]])
                    ->distinct()
                    ->count();

            if ($nro_pres === 0){
                $nuevo = [
                    'id_cu_partida'=>$d->id_cu_partida,
                    'id_cu'=>$d->id_cu,
                    'id_categoria'=>$d->id_categoria,
                    'cat_descripcion'=>$d->cat_descripcion,
                    'codigo'=>$d->codigo,
                    'descripcion'=>$d->descripcion,
                    'rendimiento'=>$d->rendimiento,
                    'unid_medida'=>$d->unid_medida,
                    'abreviatura'=>$d->abreviatura,
                    'total'=>$d->total,
                    'observacion'=>$d->observacion,
                    'estado_doc'=>$d->estado_doc,
                    'bootstrap_color'=>$d->bootstrap_color,
                    'nro_pres'=>$nro_pres
                ];
                array_push($lista,$nuevo);
            }
        }*/

        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_acus()
    {
        $data = DB::table('proyectos.proy_cu_partida')
            ->select('proy_cu_partida.*','proy_cu.codigo','proy_cu.descripcion','proy_cu.observacion',
            'alm_und_medida.abreviatura','proy_cu_cat.descripcion as cat_descripcion','proy_cu.id_categoria',
            'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->leftjoin('proyectos.proy_cu_cat','proy_cu_cat.id_categoria','=','proy_cu.id_categoria')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_cu_partida.estado')
            ->where([['proy_cu_partida.estado', '=', 1]])
            ->orderBy('proy_cu_partida.id_cu')
            ->get();

        $lista = [];
        foreach($data as $d){
            $presupuestos = DB::table('proyectos.proy_cu_partida')
            ->select('proy_presup.codigo')
            ->leftjoin('proyectos.proy_cd_partida','proy_cd_partida.id_cu_partida','=','proy_cu_partida.id_cu_partida')
            ->leftjoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->where([['proy_cu_partida.id_cu_partida', '=', $d->id_cu_partida],
                     ['proy_cd_partida.estado','!=',7],
                     ['proy_presup.estado','!=',7]])
                    ->distinct()
                    ->get();
            
            $nro_pres = $this->valida_acu_editar($d->id_cu_partida);
            $cod_pres = '';
            if (isset($presupuestos)){
                foreach($presupuestos as $p){
                    if ($cod_pres !== ''){
                        $cod_pres .= ', '.$p->codigo;
                    } else {
                        $cod_pres .= $p->codigo;
                    }
                }
            }
            $nuevo = [
                'id_cu_partida'=>$d->id_cu_partida,
                'id_cu'=>$d->id_cu,
                'id_categoria'=>$d->id_categoria,
                'cat_descripcion'=>$d->cat_descripcion,
                'codigo'=>$d->codigo,
                'descripcion'=>$d->descripcion,
                'rendimiento'=>$d->rendimiento,
                'unid_medida'=>$d->unid_medida,
                'abreviatura'=>$d->abreviatura,
                'total'=>$d->total,
                'observacion'=>$d->observacion,
                'estado_doc'=>$d->estado_doc,
                'bootstrap_color'=>$d->bootstrap_color,
                'presupuestos'=>$cod_pres,
                'nro_pres'=>$nro_pres
            ];
            array_push($lista,$nuevo);
        }
        $output['data'] = $lista;
        return response()->json($output);
    }
    public function valida_acu_editar($id_cu_partida){
        $nro_pres = DB::table('proyectos.proy_cu_partida')
            ->select('proy_presup.codigo')
            ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cu_partida','=','proy_cu_partida.id_cu_partida')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->where([['proy_cu_partida.id_cu_partida','=',$id_cu_partida],
                     ['proy_cd_partida.estado','!=',7],
                     ['proy_presup.estado','!=',1],
                     ['proy_presup.estado','!=',7]])
                    ->distinct()
                    ->count();
        return $nro_pres;
    }
    public function mostrar_acu($id)
    {
        $nro_pres = $this->valida_acu_editar($id);
        $acu = '';
        $detalle = '';

        $acu = DB::table('proyectos.proy_cu_partida')
        ->select('proy_cu_partida.*', 'alm_und_medida.abreviatura','proy_cu.codigo','proy_cu.descripcion')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
        ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cu_partida.id_cu_partida', '=', $id]])
            ->get();

        $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu_partida', '=', $id],
                     ['proy_cu_detalle.estado','!=',7]])
            ->get();
            
        return response()->json(['acu'=>$acu,'detalle'=>$detalle,'nro_pres'=>$nro_pres]);
    }
    /*public function mostrar_acu_todo($id)
    {
        $acu = DB::table('proyectos.proy_cu')
        ->select('proy_cu.*', 'alm_und_medida.abreviatura')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu.unid_medida')
            ->where([['proy_cu.id_cu', '=', $id]])
            ->get();

        $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_tp_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu', '=', $id]])
            ->orderBy('codigo')
            ->get();

        $presupuestos = $this->mostrar_presupuestos_acu($id);
        $obs = $this->mostrar_lecciones_acu($id);

        $data = ["acu"=>$acu,"acu_detalle"=>$detalle,"presupuestos"=>$presupuestos,"obs"=>$obs];
        $output['data'] = $data;

        return response()->json($output);
    }
    public function mostrar_acu_completo()
    {
        $acu = DB::table('proyectos.proy_cu')
        ->select('proy_cu.*', 'alm_und_medida.abreviatura')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu.unid_medida')
            ->get();
            
        $new_acus = [];
        foreach($acu as $cu){
            $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_tp_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
                ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
                ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
                ->where([['proy_cu_detalle.id_cu', '=', $cu->id_cu]])
                ->get();
            $nuevo = array( 'id_cu'=>$cu->id_cu,
                            'codigo'=>$cu->codigo,
                            'descripcion'=>$cu->descripcion,
                            'rendimiento'=>$cu->rendimiento,
                            'abreviatura'=>$cu->abreviatura,
                            'total'=>$cu->total,
                            'estado'=>$cu->estado,
                            'insumos'=>$detalle);
            $new_acus[] = $nuevo;
        }

        return response()->json($new_acus);
    }
    //mostrar acu detalle
    public function mostrar_acu_detalle(Request $request,$id)
    {
        $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_tp_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu', '=', $id]])
            ->get();
        return response()->json($detalle);
    }*/
    public function listar_acu_detalle($id){
        $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu_partida', '=', $id],
                     ['proy_cu_detalle.estado','=',1]])
            ->get();
        return response()->json($detalle);
    }
    public function next_cod_acu(){
        $data = DB::table('proyectos.proy_cu')
        ->orderBy('codigo','desc')
        ->where('estado',1)
        ->first();
        $codigo = 1;
        if (isset($data)){
            $codigo = ((int)$data->codigo)+1;
        }
        return $this->leftZero(4,$codigo);
    }
    public function guardar_acu(Request $request)
    {
        try{
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $id_cu_partida = 0;
            $partida = null;
    
            $id_cu_partida = DB::table('proyectos.proy_cu_partida')->insertGetId(
            [
                'id_cu' => $request->id_cu,
                // 'codigo' => $codigo,
                // 'descripcion' => strtoupper($request->descripcion),
                // 'id_categoria' => $request->id_categoria,
                // 'observacion' => $request->observacion,
                'unid_medida' => $request->unid_medida,
                'total' => $request->total_acu,
                'rendimiento' => $request->rendimiento,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'usuario_registro' => $id_usuario,
            ],
                'id_cu_partida'
            );
    
            $insumos = json_decode($request->insumos);

            foreach ($insumos as $ins) {
                DB::table('proyectos.proy_cu_detalle')->insert(
                    [
                        'id_cu_partida' => $id_cu_partida,
                        'id_insumo' => $ins->id_insumo,
                        // 'id_precio' => $id_precio,
                        'cantidad' => $ins->cantidad,
                        'cuadrilla' => $ins->cuadrilla,
                        'precio_unit' => $ins->unitario,
                        'precio_total' => $ins->total,
                        'fecha_registro' => date('Y-m-d H:i:s'),
                        'estado' => 1
                    ]
                );
            }
            // $ids = explode(',',$request->id_det);
            // $ins = explode(',',$request->id_insumo);
            // $can = explode(',',$request->cantidad);
            // $cua = explode(',',$request->cuadrilla);
            // $uni = explode(',',$request->unitario);
            // $tot = explode(',',$request->total);
            // $count = count($ins);
    
            // for ($i=0; $i<$count; $i++){
            //     $id_det     = $ids[$i];
            //     $id_ins     = $ins[$i];
            //     $cant       = $can[$i];
            //     $cuad       = $cua[$i];
            //     $precio_u   = $uni[$i];
            //     $precio_t   = $tot[$i];
            // }
            $partida = DB::table('proyectos.proy_cu_partida')
            ->select('proy_cu_partida.*','proy_cu.codigo','proy_cu.descripcion','alm_und_medida.abreviatura')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->where('id_cu_partida',$id_cu_partida)->first();

            DB::commit();
            return response()->json(['id_cu_partida'=>$id_cu_partida,'partida'=>$partida]);

        } catch (\PDOException $e) {
            DB::rollBack();
        }
        
    }

    //update_acu
    public function update_acu(Request $request)
    {
        try{
            DB::beginTransaction();

            DB::table('proyectos.proy_cu_partida')->where('id_cu_partida', $request->id_cu_partida)
                ->update([
                    // 'descripcion' => strtoupper($request->descripcion),
                    // 'id_categoria' => $request->id_categoria,
                    'id_cu' => $request->id_cu,
                    'unid_medida' => $request->unid_medida,
                    'total' => $request->total_acu,
                    'rendimiento' => $request->rendimiento,
                ]);
    
            $insumos = json_decode($request->insumos);

            foreach ($insumos as $ins) {
                if ($ins->id_cu_detalle == '0'){
                    $update = DB::table('proyectos.proy_cu_detalle')->insert(
                        [
                            'id_cu_partida' => $request->id_cu_partida,
                            'id_insumo' => $ins->id_insumo,
                            'cantidad' => $ins->cantidad,
                            'cuadrilla' => $ins->cuadrilla,
                            'precio_unit' => $ins->unitario,
                            'precio_total' => $ins->total,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => 1
                        ]
                    );
                }
                else {
                    $update = DB::table('proyectos.proy_cu_detalle')
                    ->where('id_cu_detalle',$ins->id_cu_detalle)
                    ->update([
                            'cantidad' => $ins->cantidad,
                            'cuadrilla' => $ins->cuadrilla,
                            'precio_unit' => $ins->unitario,
                            'precio_total' => $ins->total,
                        ]
                    );
                }
            }
            $elim = explode(',',$request->det_eliminados);
            $count1 = count($elim);
    
            if (!empty($request->det_eliminados)){
    
                for ($i=0; $i<$count1; $i++){
                    $id_eli = $elim[$i];
                    $update = DB::table('proyectos.proy_cu_detalle')
                    ->where('id_cu_detalle',$id_eli)
                    ->update([ 'estado' => 7 ]);
                }
            }

            $partida = DB::table('proyectos.proy_cu_partida')
            ->select('proy_cu_partida.*','proy_cu.codigo','proy_cu.descripcion','alm_und_medida.abreviatura')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->where('id_cu_partida',$request->id_cu_partida)->first();

            DB::commit();
            return response()->json(['id_cu_partida'=>$request->id_cu_partida,'partida'=>$partida]);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function anular_acu($id){
        $data = DB::table('proyectos.proy_cu_partida')->where('id_cu_partida', $id)
            ->update([ 'estado' => 7 ]);
        DB::table('proyectos.proy_cu_detalle')->where('id_cu_partida', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

   //OPCION COMERCIAL
    public function listar_opciones()
    {
        $data = DB::table('proyectos.proy_op_com')
            ->select('proy_op_com.*', 'proy_tp_proyecto.descripcion as des_tp_proyecto',
            'proy_unid_program.descripcion as des_program','adm_contri.razon_social',
            'adm_contri.id_contribuyente','sis_usua.nombre_corto','proy_modalidad.descripcion as des_modalidad',
            'adm_estado_doc.estado_doc')
            ->join('proyectos.proy_tp_proyecto','proy_tp_proyecto.id_tp_proyecto','=','proy_op_com.tp_proyecto')
            ->leftjoin('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_op_com.unid_program')
            ->leftjoin('proyectos.proy_modalidad','proy_modalidad.id_modalidad','=','proy_op_com.modalidad')
            ->join('comercial.com_cliente','com_cliente.id_cliente','=','proy_op_com.cliente')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_op_com.elaborado_por')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_op_com.estado')
                ->where([['proy_op_com.estado', '!=', 7]])
                ->orderBy('proy_op_com.codigo','desc')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_opciones_sin_presint()
    {
        $opciones = DB::table('proyectos.proy_op_com')
            ->select('proy_op_com.*', 'proy_tp_proyecto.descripcion as des_tp_proyecto',
            'proy_unid_program.descripcion as des_program','adm_contri.razon_social',
            'adm_contri.id_contribuyente','sis_usua.nombre_corto','proy_modalidad.descripcion as des_modalidad')
            ->join('proyectos.proy_tp_proyecto','proy_tp_proyecto.id_tp_proyecto','=','proy_op_com.tp_proyecto')
            ->leftjoin('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_op_com.unid_program')
            ->leftjoin('proyectos.proy_modalidad','proy_modalidad.id_modalidad','=','proy_op_com.modalidad')
            ->join('comercial.com_cliente','com_cliente.id_cliente','=','proy_op_com.cliente')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_op_com.elaborado_por')
                ->where([['proy_op_com.estado', '!=', 7]])
                ->orderBy('proy_op_com.codigo','desc')
                ->get();
        $output['data'] = $opciones;
        return response()->json($output);
    }

    public function listar_opciones_sin_preseje()
    {
        $opciones = DB::table('proyectos.proy_op_com')
            ->select('proy_op_com.*', 'proy_tp_proyecto.descripcion as des_tp_proyecto',
            'proy_unid_program.descripcion as des_program','adm_contri.razon_social',
            'adm_contri.id_contribuyente','sis_usua.nombre_corto','proy_modalidad.descripcion as des_modalidad')
            ->join('proyectos.proy_tp_proyecto','proy_tp_proyecto.id_tp_proyecto','=','proy_op_com.tp_proyecto')
            ->leftjoin('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_op_com.unid_program')
            ->leftjoin('proyectos.proy_modalidad','proy_modalidad.id_modalidad','=','proy_op_com.modalidad')
            ->join('comercial.com_cliente','com_cliente.id_cliente','=','proy_op_com.cliente')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_op_com.elaborado_por')
                ->where([['proy_op_com.estado', '!=', 7]])
                ->orderBy('proy_op_com.codigo','desc')
                ->get();
        
        $lista = [];
        foreach($opciones as $d){
            $preseje = DB::table('proyectos.proy_presup')
            ->where([['id_op_com','=',$d->id_op_com],['estado','!=',7],
                     ['id_tp_presupuesto','=',2]])//Presupuesto Ejecucion
                     ->first();
            $presint = DB::table('proyectos.proy_presup')
            ->where([['id_op_com','=',$d->id_op_com],['estado','!=',7],
                     ['id_tp_presupuesto','=',1]])//Presupuesto Interno
                    ->first();
            if (!isset($preseje) && isset($presint)){
                array_push($lista, $d);
            }
        }
        $output['data'] = $lista;
        return response()->json($output);
    }

    public function mostrar_opcion($id)
    {
        $data = DB::table('proyectos.proy_op_com')
            ->select('proy_op_com.*', 'adm_contri.razon_social',
            DB::raw('(SELECT presup_totales.sub_total FROM finanzas.presup 
            INNER JOIN finanzas.presup_totales ON(
                presup.id_presup = presup_totales.id_presup
            )
            WHERE presup.id_op_com = proy_op_com.id_op_com 
              AND presup.tp_presup = 3) AS sub_total_propuesta'))
            // DB::raw('(SELECT proy_presup_importe.sub_total FROM proyectos.proy_presup 
            // INNER JOIN proyectos.proy_presup_importe ON(
            //     proy_presup.id_presupuesto = proy_presup_importe.id_presupuesto
            // )
            // WHERE proy_presup.id_op_com = proy_op_com.id_op_com 
            //   AND proy_presup.id_tp_presupuesto = 1) AS sub_total_presint'))
            ->leftjoin('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->leftjoin('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_op_com.id_op_com', '=', $id]])
                ->first();
        return response()->json($data);
    }

    public function nextOpcion($id_emp,$fecha)
    {
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $code_emp = '';
        $result = '';

        $emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', '=', $id_emp)
        ->get();
        foreach ($emp as $rowEmp) {
            $code_emp = $rowEmp->codigo;
        }
        $data = DB::table('proyectos.proy_op_com')
                ->where('id_empresa', '=', $id_emp)
                // ->whereMonth('fecha_emision', '=', $mes)
                ->whereYear('fecha_emision', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $result = "OP-".$code_emp."-".$anio."".$number;

        return $result;
    }
    public function guardar_opcion(Request $request)
    {
        $id_usuario = Auth::user()->id_usuario;
        $codigo = $this->nextOpcion($request->id_empresa, $request->fecha_emision);
        $id_op_com = DB::table('proyectos.proy_op_com')->insertGetId(
            [
                'tp_proyecto' => $request->tp_proyecto,
                'id_empresa' => $request->id_empresa,
                'descripcion' => strtoupper(trim($request->descripcion)),
                'cliente' => $request->cliente,
                'unid_program' => ($request->unid_program > 0 ? $request->unid_program : null),
                'cantidad' => $request->cantidad,
                'modalidad' => ($request->modalidad > 0 ? $request->modalidad : null),
                'fecha_emision' => $request->fecha_emision,
                'codigo' => $codigo,
                'elaborado_por' => $id_usuario,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_op_com'
            );

            // $lec = $request->lecciones;
            // $lecArray = json_decode($lec, true);
            // $count_lec = count($lecArray);

            // if ($count_lec > 0){
            //     for ($i=0; $i<$count_lec; $i++){
                    
            //         if ($lecArray[$i]['id_leccion']===0){
            //             DB::table('proyectos.proy_op_com_lec')->insert(
            //                 [
            //                     'id_proy_op_com' => $id_op_com,
            //                     'descripcion'    => $lecArray[$i]['descripcion'],
            //                     'usuario'        => $lecArray[$i]['usuario'],
            //                     'estado'         => $lecArray[$i]['estado'],
            //                     'fecha_registro' => $lecArray[$i]['fecha_registro']
            //                 ]
            //             );
            //         }
            //     }
            // }

        return response()->json($id_op_com);
    }

    public function update_opcion(Request $request)
    {
        // $codigo = $this->nextOpcion($request->empresa,$request->fecha_emision);
        $data = DB::table('proyectos.proy_op_com')->where('id_op_com', $request->id_op_com)
            ->update([
                'tp_proyecto' => $request->tp_proyecto,
                'id_empresa' => $request->id_empresa,
                'descripcion' => strtoupper(trim($request->descripcion)),
                'cliente' => $request->cliente,
                'unid_program' => ($request->unid_program > 0 ? $request->unid_program : null),
                'cantidad' => $request->cantidad,
                'modalidad' => ($request->modalidad > 0 ? $request->modalidad : null),
                'fecha_emision' => $request->fecha_emision,
            ]);

            // $lec = $request->lecciones;
            // $lecArray = json_decode($lec, true);
            // $count_lec = count($lecArray);

            // if ($count_lec > 0){
            //     for ($i=0; $i<$count_lec; $i++){
                    
            //         if ($lecArray[$i]['id_leccion']===0){
            //             DB::table('proyectos.proy_op_com_lec')->insert(
            //                 [
            //                     'id_proy_op_com' => $id,
            //                     'descripcion'    => $lecArray[$i]['descripcion'],
            //                     'usuario'        => $lecArray[$i]['usuario'],
            //                     'estado'         => $lecArray[$i]['estado'],
            //                     'fecha_registro' => $lecArray[$i]['fecha_registro']
            //                 ]
            //             );
            //         }
            //     }
            // }

        return response()->json($data);
    }
    public function anular_opcion(Request $request, $id)
    {
        $data = DB::table('proyectos.proy_op_com')
                ->where('id_op_com',$id)
                ->update([ 'estado' => 7 ]);

        // $detalle = DB::table('proyectos.proy_op_com_lec')
        //         ->where('id_proy_op_com',$id)
        //         ->update([ 'estado' => 7 ]);

        return response()->json($data);
    }
    //LECCIONES APRENDIDAS
    public function mostrar_lecciones(Request $request,$id)
    {
        $detalle = DB::table('proyectos.proy_op_com_lec')
                   ->select('proy_op_com_lec.*', 'sis_usua.usuario as nombre_usuario')
                   ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_op_com_lec.id_proy_op_com')
                   ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_op_com_lec.usuario')
                   ->where([['proy_op_com_lec.id_proy_op_com', '=', $id]])
                   ->get();

       return response()->json($detalle);

    }
    public function guardar_leccion(Request $request)
    {
        $data = DB::table('proyectos.proy_op_com_lec')->insertGetId(
            [
                'id_proy_op_com' => $request->id_proy_op_com,
                'descripcion' => $request->descripcion,
                'usuario' => $request->usuario,
                'estado' => $request->estado,
                'fecha_registro' => $request->fecha_registro
            ],
                'id_leccion'
            );
        return response()->json($data);
    }
    public function update_leccion(Request $request, $id)
    {
        $data = DB::table('proyectos.proy_op_com_lec')->where('id_leccion', $id)
            ->update([
                'id_proy_op_com' => $request->id_proy_op_com,
                'descripcion' => $request->descripcion,
                'usuario' => $request->usuario,
                'estado' => $request->estado,
                'fecha_registro' => date('Y-m-d H:i:s')
            ]);
        return response()->json($data);
    }
    //PROYECTO
    public function listar_proyectos()
    {
        $data = DB::table('proyectos.proy_proyecto')
                ->select('proy_proyecto.*', 'adm_contri.razon_social','proy_modalidad.descripcion as nombre_modalidad',
                'proy_tp_proyecto.descripcion as nombre_tp_proyecto','proy_sis_contrato.descripcion as nombre_sis_contrato',
                'sis_moneda.simbolo','sis_usua.usuario','proy_unid_program.descripcion as des_unid_prog',
                'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
                ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('proyectos.proy_modalidad','proy_modalidad.id_modalidad','=','proy_proyecto.modalidad')
                ->join('proyectos.proy_tp_proyecto','proy_tp_proyecto.id_tp_proyecto','=','proy_proyecto.tp_proyecto')
                ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_proyecto.sis_contrato')
                ->join('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_proyecto.unid_program')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_proyecto.moneda')
                ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_proyecto.elaborado_por')
                ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_proyecto.estado')
                // ->leftjoin('proyectos.proy_presup','proy_presup.id_proyecto','=','proy_proyecto.id_proyecto')
                ->where([['proy_proyecto.estado', '!=', 7]])
                ->orderBy('id_proyecto')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_proyectos_pendientes($emp,$rol)
    {
        //Lista de flujos con el rol en sesion para proyecto
        $flujos = DB::table('administracion.adm_flujo')
            ->select('adm_flujo.*')
            ->where([['adm_flujo.id_rol','=',$rol],
                    ['adm_flujo.estado','=',1],
                    ['adm_flujo.id_operacion','=',6] //Operacion= 6->Proyecto
                    ])
            ->orderBy('orden')
            ->get();

        //Lista de proyectos pendientes
        $pendientes = DB::table('proyectos.proy_proyecto')
            ->select('proy_proyecto.*','adm_documentos_aprob.id_doc_aprob','adm_contri.razon_social',
            'sis_moneda.simbolo')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_proyecto.moneda')
            ->leftjoin('administracion.adm_documentos_aprob','adm_documentos_aprob.codigo_doc','=','proy_proyecto.codigo')
            ->where([['proy_proyecto.estado','=',1],//elaborado
                    ['proy_proyecto.empresa','=',$emp]])
            ->get();
        
        $lista = [];

        //Nro de flujos que necesita para aprobar el proyecto
        $nro_flujo = DB::table('administracion.adm_flujo')
            ->where([['adm_flujo.estado','=',1],//activo->1
                    ['adm_flujo.id_operacion','=',6]])//proyecto->6
            ->count();

        foreach($pendientes as $proy){
            //Nro de aprobacion que necesita
            $nro_ap = DB::table('administracion.adm_aprobacion')
                ->where([['adm_aprobacion.id_doc_aprob','=',$proy->id_doc_aprob],
                        ['adm_aprobacion.id_vobo','=',1]])
                ->count() + 1;
            //Si el nro total de flujos es >= que el nro de aprobaciones
            if ($nro_flujo >= $nro_ap){
                //Recorre los flujos con mi rol
                foreach($flujos as $flujo){
                    //Si el nro de orden de mi flujo es = nro de aprobacion q necesita
                    if ($flujo->orden === $nro_ap){
                        $nuevo_proy = [
                            "id_proyecto"=>$proy->id_proyecto,
                            "empresa"=>$proy->empresa,
                            "descripcion"=>$proy->descripcion,
                            "cliente"=>$proy->cliente,
                            "razon_social"=>$proy->razon_social,
                            "id_doc_aprob"=>$proy->id_doc_aprob,
                            "simbolo"=>$proy->simbolo,
                            "importe"=>$proy->importe,
                            "fecha_inicio"=>$proy->fecha_inicio,
                            "fecha_fin"=>$proy->fecha_fin,
                            "codigo"=>$proy->codigo,
                            "orden"=>$nro_ap,
                            "id_flujo"=>$flujo->id_flujo
                        ];
                        //agrega el proyecto a la lista
                        array_push($lista,$nuevo_proy);
                    }
                }
            }
        }
        // return response()->json(["lista"=>$lista,"flujos"=>$flujos]);
        return response()->json($lista);
    }
    public function aprobacion_completa($id_doc_aprob)
    {
        $rspta = 0;
        //Nro de flujos que necesita para aprobar el proyecto
        $nro_flujo = DB::table('administracion.adm_flujo')
        ->where([['adm_flujo.estado','=',1],//activo->1
                ['adm_flujo.id_operacion','=',6]])//proyecto->6
        ->count();
        //Nro de aprobacion que necesita
        $nro_ap = DB::table('administracion.adm_aprobacion')
        ->where([['adm_aprobacion.id_doc_aprob','=',$id_doc_aprob],
                ['adm_aprobacion.id_vobo','=',1]])
        ->count();
        //Si el nro de aprobaciones es < que el nro total de flujos
        if ($nro_ap >= $nro_flujo){
            $rspta = 1;
        }
        return $rspta;
    }
    public function guardar_aprobacion(Request $request)
    {
        $id_aprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo'=>$request->id_flujo, 
                'id_doc_aprob'=>$request->id_doc_aprob, 
                'id_vobo'=>$request->id_vobo, 
                'id_usuario'=>$request->id_usuario, 
                'id_area'=>$request->id_area, 
                'fecha_vobo'=>$request->fecha_vobo, 
                'detalle_observacion'=>$request->detalle_observacion, 
                'id_rol'=>$request->id_rol
            ],
                'id_aprobacion'
            );
        return response()->json($id_aprobacion);
    }
    public function estado_proyecto($id,$estado)
    {
        $data = DB::table('proyectos.proy_proyecto')
        ->where('id_proyecto', $id)
        ->update([ 'estado' => $estado ]);
        return response()->json($data);
    }
    public function mostrar_proyecto($id)
    {
        $data = DB::table('proyectos.proy_proyecto')
            ->select('proy_proyecto.*','adm_contri.razon_social','sis_moneda.simbolo')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_proyecto.moneda')
            ->where([['proy_proyecto.id_proyecto', '=', $id]])
            ->first();

        $primer_contrato = DB::table('proyectos.proy_contrato')
            ->select('proy_contrato.*','sis_moneda.simbolo','proy_tp_contrato.descripcion as tipo_contrato')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
            ->join('proyectos.proy_tp_contrato','proy_tp_contrato.id_tp_contrato','=','proy_contrato.id_tp_contrato')
            ->where([['proy_contrato.id_proyecto', '=', $id],['proy_contrato.estado', '!=', 7]])
            ->orderBy('fecha_registro','asc')
            ->first();

        return response()->json(["proyecto"=>$data,"primer_contrato"=>$primer_contrato]);
    }
    public function mostrar_proy_contratos()
    {
        $data = DB::table('proyectos.proy_contrato')
                ->select('proy_contrato.*',
                'proy_proyecto.descripcion','adm_contri.razon_social',//'proy_contrato.fecha_contrato',
                'sis_moneda.simbolo','proy_proyecto.id_op_com','proy_proyecto.empresa')
                ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
                ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
                ->where([['proy_contrato.estado', '=', 1]])
                // ->orderBy('nro_contrato')
                ->get();
        return response()->json($data);
    }
    public function listar_proyectos_contratos()
    {
        $data = DB::table('proyectos.proy_contrato')
                ->select('proy_contrato.*','proy_contrato.nro_contrato',
                'proy_contrato.importe','proy_proyecto.descripcion','adm_contri.razon_social',
                'sis_moneda.simbolo','proy_proyecto.id_op_com','proy_proyecto.empresa',
                DB::raw("(SELECT proy_presup.codigo FROM proyectos.proy_presup WHERE 
                proy_presup.id_proyecto=proy_proyecto.id_proyecto AND proy_presup.id_tp_presupuesto=2 AND proy_presup.estado!=7) as cod_preseje"))
                ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
                ->leftjoin('proyectos.proy_presup','proy_presup.id_proyecto','=','proy_proyecto.id_proyecto')
                ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
                ->where([['proy_contrato.estado', '=', 1]])
                ->get();
        return response()->json(['data'=>$data]);
    }
    public function mostrar_contrato($id)
    {
        $data = DB::table('proyectos.proy_contrato')
                ->select('proy_contrato.id_contrato','proy_contrato.nro_contrato','proy_contrato.moneda',
                'proy_proyecto.descripcion','adm_contri.razon_social','proy_contrato.fecha_contrato',
                'sis_moneda.simbolo','proy_contrato.importe','proy_proyecto.id_op_com','proy_proyecto.empresa')
                ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
                ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
                ->where([['proy_contrato.id_contrato', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function nextProyecto($id_emp,$fecha)
    {
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $code_emp = '';
        $result = '';

        $emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', '=', $id_emp)
        ->get();
        foreach ($emp as $rowEmp) {
            $code_emp = $rowEmp->codigo;
        }
        $data = DB::table('proyectos.proy_proyecto')
                ->where([['empresa','=',$id_emp]])
                // ->whereMonth('fecha_inicio', '=', $mes)
                ->whereYear('fecha_inicio', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $result = "PY-".$code_emp."-".$anio."-".$number;

        return $result;
    }

    public function guardar_proyecto(Request $request)
    {
        $codigo = $this->nextProyecto($request->id_empresa, $request->fecha_inicio);
        $id_usuario = Auth::user()->id_usuario;

        $id_proyecto = DB::table('proyectos.proy_proyecto')->insertGetId(
            [
                'tp_proyecto' => $request->tp_proyecto,
                'empresa' => $request->id_empresa,
                'descripcion' => $request->nombre_opcion,
                'cliente' => $request->id_cliente,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'elaborado_por' => $id_usuario,
                'codigo' => $codigo,
                'modalidad' => $request->modalidad,
                'sis_contrato' => $request->sis_contrato,
                'moneda' => $request->moneda,
                'plazo_ejecucion' => $request->plazo_ejecucion,
                'unid_program' => $request->unid_program,
                'id_op_com' => $request->id_op_com,
                'importe' => $request->importe,
                'jornal' => $request->jornal,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_proyecto'
            );

        $id_contrato = DB::table('proyectos.proy_contrato')->insertGetId(
            [
                'nro_contrato' => $request->nro_contrato_proy,
                'fecha_contrato' => $request->fecha_contrato_proy,
                'descripcion' => $request->descripcion_proy,
                'moneda' => $request->moneda_contrato,
                'importe' => $request->importe_contrato_proy,
                // 'archivo_adjunto' => $nombre,
                'id_proyecto' => $id_proyecto,
                'id_tp_contrato' => $request->id_tp_contrato_proy,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_contrato'
            );
        //obtenemos el campo file definido en el formulario
        $file = $request->file('primer_adjunto');
        if (isset($file)){
            //obtenemos el nombre del archivo 
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $nombre = $id_contrato.'.'.$request->nro_contrato_proy.'.'.$extension;
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \File::delete(public_path('proyectos/contratos/'.$nombre));
            \Storage::disk('archivos')->put('proyectos/contratos/'.$nombre,\File::get($file));
            
            $update = DB::table('proyectos.proy_contrato')
                ->where('id_contrato', $id_contrato)
                ->update(['archivo_adjunto' => $nombre]); 
        } else {
            $nombre = null;
        }
        return response()->json($id_proyecto);
    }
    public function actualizar_proyecto(Request $request)
    {
        $data = DB::table('proyectos.proy_proyecto')->where('id_proyecto', $request->id_proyecto)
        ->update([
            'tp_proyecto' => $request->tp_proyecto,
            'descripcion' => $request->nombre_opcion,
            'cliente' => $request->id_cliente,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'modalidad' => $request->modalidad,
            'sis_contrato' => $request->sis_contrato,
            'moneda' => $request->moneda,
            'plazo_ejecucion' => $request->plazo_ejecucion,
            'unid_program' => $request->unid_program,
            'id_op_com' => $request->id_op_com,
            'jornal' => $request->jornal,
            'importe' => $request->importe
        ]);
        return response()->json($data);
    }
    
    public function anular_proyecto(Request $request,$id)
    {
        $data = DB::table('proyectos.proy_proyecto')->where('id_proyecto', $id)
        ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }


    
    public function listar_proyectos_activos()
    {
        $data = DB::table('proyectos.proy_proyecto')
                ->select('proy_proyecto.*')
                ->where('proy_proyecto.estado', 1)
                ->orderBy('id_proyecto')
                ->get();
        return $data;
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


    //  PRESUPUESTO INTERNO
    public function mostrar_presupuestos_cabecera()
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_presup.codigo','proy_op_com.descripcion', 
            'adm_contri.razon_social')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_presup.estado', '=', 1]])
                ->orderBy('id_presupuesto')
                ->get();
        return response()->json($data);
    }
    public function mostrar_presupuesto_cabecera($id)
    {
        $data = DB::table('proyectos.proy_presup')
        ->select('proy_presup.codigo','proy_presup.fecha_emision','proy_proyecto.descripcion',
        'sis_moneda.simbolo as moneda','adm_contri.razon_social','proy_presup_importe.*',
        'proy_unid_program.descripcion as des_unid_program','proy_proyecto.plazo_ejecucion')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')

            ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_presup.id_contrato')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            ->join('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_proyecto.unid_program')
            
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->get();

        return response()->json($data);
    }
    public function mostrar_presupuesto_cabecera2($id)
    {
        $data = DB::table('proyectos.proy_presup')
        ->select('proy_presup.codigo','proy_presup.fecha_emision',
        'sis_moneda.simbolo as moneda','adm_contri.razon_social','proy_presup_importe.*',
        'proy_proyecto.plazo_ejecucion','proy_op_com.descripcion as nombre_opcion')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')

            // ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_presup.id_contrato')
            // ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            // ->join('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_proyecto.unid_program')
            
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->get();

        return response()->json($data);
    }
    public function mostrar_presup_ejecucion()
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_contrato.nro_contrato','proy_proyecto.id_proyecto',
            'proy_proyecto.descripcion','proy_presup.codigo','adm_contri.razon_social')
            ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_presup.id_contrato')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_presup.estado', '=', 1],['proy_presup.id_tp_presupuesto', '=', 3]])
                ->orderBy('id_presupuesto')
                ->get();
        return response()->json($data);
    }
    public function mostrar_presup_ejecucion_contrato($id_proyecto)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_contrato.nro_contrato','proy_proyecto.id_proyecto',
            'proy_proyecto.descripcion','proy_presup.codigo','adm_contri.razon_social')
            ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_presup.id_contrato')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_presup.estado', '=', 1],
                        ['proy_presup.id_tp_presupuesto', '=', 3],
                        ['proy_proyecto.id_proyecto','=',$id_proyecto]])
                ->get();
        return response()->json($data);
    }
    public function mostrar_presupuesto($id)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_presup.codigo','proy_presup.fecha_emision',
            'proy_presup.id_tp_presupuesto','proy_op_com.descripcion as nombre_opcion', 'proy_presup.moneda',
            'proy_presup.id_op_com','sis_moneda.simbolo','adm_contri.razon_social','proy_presup.id_empresa',
            'proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg','proy_presup_importe.*')
                ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
                ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
                ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->leftjoin('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->leftjoin('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->leftjoin('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->leftjoin('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.id_presupuesto', '=', $id]])//Tipo-> Pres.Interno
                ->get();
        return response()->json($data);
    }
 
    public function mostrar_presupuestos_acu($id_cu)
    {
        $proy_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_presup.id_presupuesto', 'proy_presup.codigo', 'proy_presup.id_tp_presupuesto',
                     'proy_op_com.descripcion','adm_contri.nro_documento','adm_contri.razon_social',
                     'adm_estado_doc.estado_doc','proy_presup.fecha_emision','proy_tp_pres.descripcion as des_tipo',
                     'proy_presup_importe.sub_total','sis_moneda.simbolo')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_presup.estado')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('proyectos.proy_tp_pres','proy_tp_pres.id_tp_pres','=','proy_presup.id_tp_presupuesto')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_cd_partida.id_cu_partida', '=', $id_cu],
                         ['proy_cd_partida.estado', '=', 1]])
                ->distinct();

        $proy_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_presup.id_presupuesto', 'proy_presup.codigo', 'proy_presup.id_tp_presupuesto',
                     'proy_op_com.descripcion','adm_contri.nro_documento','adm_contri.razon_social',
                     'adm_estado_doc.estado_doc','proy_presup.fecha_emision','proy_tp_pres.descripcion as des_tipo',
                     'proy_presup_importe.sub_total','sis_moneda.simbolo')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_ci_detalle.id_ci')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_presup.estado')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('proyectos.proy_tp_pres','proy_tp_pres.id_tp_pres','=','proy_presup.id_tp_presupuesto')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_ci_detalle.id_cu_partida', '=', $id_cu],
                         ['proy_ci_detalle.estado', '=', 1]])
                ->distinct()
                ->unionAll($proy_cd);

        $proy_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_presup.id_presupuesto', 'proy_presup.codigo', 'proy_presup.id_tp_presupuesto',
                     'proy_op_com.descripcion','adm_contri.nro_documento','adm_contri.razon_social',
                     'adm_estado_doc.estado_doc','proy_presup.fecha_emision','proy_tp_pres.descripcion as des_tipo',
                     'proy_presup_importe.sub_total','sis_moneda.simbolo')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_gg_detalle.id_gg')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_presup.estado')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('proyectos.proy_tp_pres','proy_tp_pres.id_tp_pres','=','proy_presup.id_tp_presupuesto')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_gg_detalle.id_cu_partida', '=', $id_cu],
                        ['proy_gg_detalle.estado', '=', 1]])
                ->distinct()
                ->unionAll($proy_ci)
                ->get()
                ->toArray();

        // $resultado = array_map("unserialize", array_unique(array_map("serialize", $proy_gg)));
 
        return $proy_gg;
    }
    public function html_presupuestos_acu($id_cu){
        $data = $this->mostrar_presupuestos_acu($id_cu);
        $html = '';
        $class = '';
        foreach($data as $d){
            if ($d->id_tp_presupuesto == 1){
                $class = 'label label-primary';
            } else if ($d->id_tp_presupuesto == 2){
                $class = 'label label-success';
            }
            $html.='
            <tr id="'.$d->id_presupuesto.'">
                <td><span class="'.$class.'">'.$d->codigo.'</span></td>
                <td>'.$d->descripcion.'</td>
                <td>'.$d->razon_social.'</td>
                <td>'.$d->estado_doc.'</td>
            </tr>';
        }
        return json_encode($html);
    }
    public function mostrar_lecciones_acu($id_cu_partida)
    {
        $proy_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_obs.*','proy_cd_partida.id_cu_partida','proy_presup.codigo',
            'sis_usua.usuario as nombre_usuario')
            ->join('proyectos.proy_obs','proy_obs.id_cd_partida','=','proy_cd_partida.id_partida')
            ->join('proyectos.proy_cd','proy_cd.id_cd','=','proy_cd_partida.id_cd')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd.id_presupuesto')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
                ->where([['proy_cd_partida.id_cu_partida', '=', $id_cu_partida]]);

        $proy_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_obs.*','proy_ci_detalle.id_cu_partida','proy_presup.codigo',
            'sis_usua.usuario as nombre_usuario')
            ->join('proyectos.proy_obs','proy_obs.id_ci_detalle','=','proy_ci_detalle.id_ci_detalle')
            ->join('proyectos.proy_ci','proy_ci.id_ci','=','proy_ci_detalle.id_ci')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_ci.id_presupuesto')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
                ->where([['proy_ci_detalle.id_cu_partida', '=', $id_cu_partida]])
                ->unionAll($proy_cd);

        $proy_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_obs.*','proy_gg_detalle.id_cu_partida','proy_presup.codigo',
            'sis_usua.usuario as nombre_usuario')
            ->join('proyectos.proy_obs','proy_obs.id_gg_detalle','=','proy_gg_detalle.id_gg_detalle')
            ->join('proyectos.proy_gg','proy_gg.id_gg','=','proy_gg_detalle.id_gg')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_gg.id_presupuesto')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
                ->where([['proy_gg_detalle.id_cu_partida', '=', $id_cu_partida]])
                ->unionAll($proy_ci)
                ->get()
                ->toArray();

        // $resultado = array_map("unserialize", array_unique(array_map("serialize", $proy_gg)));
        // return response()->json($proy_gg);
        return $proy_gg;
    }

    public function obsPartida($id, $origen){

        $data = DB::table('proyectos.proy_obs')
        ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
        ->where(
            function($query) use ($origen, $id)
            {
                if ($origen === "cd"){
                    $query->where('proy_obs.id_cd_partida', $id);
                }
                else if ($origen === "ci"){
                    $query->where('proy_obs.id_ci_detalle', $id);         
                }
                else if ($origen === "gg"){
                    $query->where('proy_obs.id_gg_detalle', $id);           
                }
            })
        ->orderBy('proy_obs.fecha_registro')
        ->get();

        return response()->json($data);
    }
    public function nextPresupuesto($tipo,$id_emp,$fecha)
    {
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));//yyyy
        $anio = date('y',strtotime($fecha));//yy
        $result = '';

        $tp = DB::table('proyectos.proy_tp_pres')
        ->select('codigo')
        ->where('id_tp_pres', $tipo)
        ->first();

        $emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', $id_emp)
        ->first();

        $data = DB::table('proyectos.proy_presup')
                ->where([['id_tp_presupuesto','=',$tipo],
                        ['id_empresa','=',$id_emp],
                        ['estado','!=',7]])
                // ->whereMonth('fecha_emision', '=', $mes)
                ->whereYear('fecha_emision', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $result = $tp->codigo."-".$emp->codigo."-".$anio."-".$number;

        return $result;
    }

    public function listar_trabajadores()
    {
        $data = DB::table('rrhh.rrhh_trab')
                ->select('rrhh_trab.*', 'rrhh_perso.nro_documento', 
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->where([['rrhh_trab.estado', '=', 1]])
                ->orderBy('nombre_trabajador')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_residentes()
    {
        $data = DB::table('proyectos.proy_residente')
                ->select('proy_residente.*','rrhh_perso.nro_documento','adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'proy_residente.id_trabajador')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'proy_residente.estado')
                ->where([['proy_residente.estado', '!=', 7]])
                ->orderBy('nombre_trabajador')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_proyectos_residente($id)
    {
        $contratos = DB::table('proyectos.proy_res_proy')
            ->select('proy_res_proy.*','adm_contri.razon_social','proy_proyecto.descripcion',
            'proy_res_cargo.descripcion as cargo_descripcion','proy_proyecto.codigo','proy_proyecto.importe',
            'sis_moneda.simbolo')
            ->join('proyectos.proy_res_cargo','proy_res_cargo.id_cargo','=','proy_res_proy.id_cargo')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_res_proy.id_proyecto')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_proyecto.moneda')
            ->join('comercial.com_cliente','com_cliente.id_cliente','=','proy_proyecto.cliente')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            // ->leftjoin('proyectos.proy_presup','proy_presup.id_proyecto','=','proy_proyecto.id_proyecto')
            ->where([['proy_res_proy.id_residente', '=', $id],
                     ['proy_res_proy.estado','!=',7]])
            ->get();

        return response()->json($contratos);
    }
    public function anular_proyecto_residente($id)
    {
        $data = DB::table('proyectos.proy_res_proy')
        ->where('id_res_con',$id)
        ->update(['estado'=>7]);

        return response()->json($data);
    }

    public function guardar_residente(Request $request)
    {
        $res = DB::table('proyectos.proy_residente')
        ->where([['id_trabajador','=',$request->id_trabajador],
                 ['estado','!=',7]])
        ->first();
        $id_residente = 0;

        if (!isset($res)){
            $id_usuario = Auth::user()->id_usuario;
            $id_residente = DB::table('proyectos.proy_residente')->insertGetId(
                [
                    'id_trabajador' => $request->id_trabajador,
                    'colegiatura' => $request->colegiatura,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d')
                ],
                    'id_residente'
                );
    
            $ids = explode(',',$request->id_res_con);
            $proy = explode(',',$request->id_proyecto);
            $carg = explode(',',$request->id_cargo);
            $fini = explode(',',$request->fecha_inicio);
            $ffin = explode(',',$request->fecha_fin);
            $part = explode(',',$request->participacion);
            $count = count($ids);
    
            for ($i=0; $i<$count; $i++){
                $id_proy     = $proy[$i];
                $id_cargo    = $carg[$i];
                $fec_inicio  = $fini[$i];
                $fec_fin     = $ffin[$i];
                $parti       = $part[$i];
                
                DB::table('proyectos.proy_res_proy')->insert(
                    [
                        'id_residente'     => $id_residente,
                        'id_proyecto'      => $id_proy,
                        'id_cargo'         => $id_cargo,
                        'fecha_inicio'     => $fec_inicio,
                        'fecha_fin'        => ($fec_fin!=='' ? $fec_fin : null),
                        'participacion'    => $parti,
                        // 'observacion'      => $observacion,
                        'estado'           => 1,
                        'fecha_registro'   => date('Y-m-d'),
                        'usuario_registro' => $id_usuario
                    ]
                );
            }    
        }

        return response()->json($id_residente);
    }
    public function update_residente(Request $request)
    {
        $id_usuario = Auth::user()->id_usuario;
        $id_residente = DB::table('proyectos.proy_residente')
        ->where('id_residente', $request->id_residente)
        ->update([
                'id_trabajador' => $request->id_trabajador,
                'colegiatura' => $request->colegiatura,
                // 'id_cargo' => $request->id_cargo
            ]);

        $ids  = explode(',',$request->id_res_con);
        $proy = explode(',',$request->id_proyecto);
        $carg = explode(',',$request->id_cargo);
        $fini = explode(',',$request->fecha_inicio);
        $ffin = explode(',',$request->fecha_fin);
        $part = explode(',',$request->participacion);
        $count = count($ids);

        for ($i=0; $i<$count; $i++){
            $id_res_con  = $ids[$i];
            $id_proy     = $proy[$i];
            $id_carg     = $carg[$i];
            $fec_inicio  = $fini[$i];
            $fec_fin     = $ffin[$i];
            $parti       = $part[$i];

            if ($id_res_con == '0'){
                DB::table('proyectos.proy_res_proy')->insert(
                    [
                        'id_residente'     => $request->id_residente,
                        'id_proyecto'      => $id_proy,
                        'id_cargo'         => $id_carg,
                        'fecha_inicio'     => $fec_inicio,
                        'fecha_fin'        => ($fec_fin!=='' ? $fec_fin : null),
                        'participacion'    => $parti,
                        // 'observacion'      => $observacion,
                        'estado'           => 1,
                        'fecha_registro'   => date('Y-m-d'),
                        'usuario_registro' => $id_usuario
                    ]
                );
            } else {
                DB::table('proyectos.proy_res_proy')
                ->where('id_res_con',$id_res_con)
                ->update([
                        'id_proyecto'   => $id_proy,
                        'id_cargo'      => $id_carg,
                        'fecha_inicio'  => $fec_inicio,
                        'fecha_fin'     => ($fec_fin!=='' ? $fec_fin : null),
                        'participacion' => $parti
                        // 'observacion'      => $observacion
                    ]);
            }
        }

        $elim = explode(',',$request->anulados);
        $count1 = count($elim);

        if (!empty($request->anulados)){
            for ($i=0; $i<$count1; $i++){
                $id_eli = $elim[$i];
                DB::table('proyectos.proy_res_proy')
                ->where('id_res_con',$id_eli)
                ->update([ 'estado' => 7 ]);
            }
        }

        return response()->json($id_residente);
    }
    public function anular_residente($id)
    {
        $id_residente = DB::table('proyectos.proy_residente')
                ->where('id_residente',$id)
                ->update([ 'estado' => 7 ]);

        $detalle = DB::table('proyectos.proy_res_proy')
                ->where('id_residente',$id)
                ->update([ 'estado' => 7 ]);

        return response()->json($id_residente);
    }
    public function mostrar_portafolios()
    {
        $data = DB::table('proyectos.proy_portafolio')
                ->select('proy_portafolio.*', DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'proy_portafolio.responsable')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->where([['proy_portafolio.estado', '=', 1]])
                ->orderBy('id_portafolio')
                ->get();
        return response()->json($data);
    }
    public function mostrar_portafolio($id)
    {
        $data = DB::table('proyectos.proy_portafolio')
            ->select('proy_portafolio.*',DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_trabajador"))
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'proy_portafolio.responsable')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where([['proy_portafolio.id_portafolio', '=', $id]])
            ->get();

        $detalle = DB::table('proyectos.proy_porta_detalle')
            ->select('proy_porta_detalle.*','adm_contri.razon_social',
            'proy_proyecto.descripcion','proy_proyecto.codigo')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_porta_detalle.id_proyecto')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->where([['proy_porta_detalle.id_portafolio', '=', $id]])
            ->get();

        return response()->json(["portafolio"=>$data,"detalle"=>$detalle]);
    }

    public function nextPortafolio($id_emp,$fecha)
    {
        $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $code_emp = '';
        $result = '';

        $emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', '=', $id_emp)
        ->get();
        foreach ($emp as $rowEmp) {
            $code_emp = $rowEmp->codigo;
        }
        $data = DB::table('proyectos.proy_portafolio')
                ->where('id_empresa', '=', $id_emp)
                ->whereMonth('fecha_emision', '=', $mes)
                ->whereYear('fecha_emision', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $result = "GP/".$code_emp."-".$anio."".$mes."".$number;

        return $result;
    }

    public function guardar_portafolio(Request $request)
    {
        $codigo = $this->nextPortafolio($request->id_empresa,$request->fecha_emision);

        $id_portafolio = DB::table('proyectos.proy_portafolio')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'fecha_emision' => $request->fecha_emision,
                'responsable' => $request->responsable,
                'fecha_registro' => $request->fecha_registro,
                'usuario_registro' => $request->usuario_registro,
                'estado' => $request->estado,
                'codigo' => $codigo,
                'id_empresa' => $request->id_empresa
            ],
                'id_portafolio'
            );

        $ids = $request->c_id_detalle;
        $count = count($ids);

        for ($i=0; $i<$count; $i++){
            // $id_portafolio  = $request->c_id_portafolio[$i];
            $id_proyecto    = $request->c_id_proyecto[$i];
            $fecha_registro = $request->c_fecha_registro[$i];
            $estado         = $request->c_estado[$i];
            
            DB::table('proyectos.proy_porta_detalle')->insert(
                [
                    // 'id_detalle'     => $id_detalle,
                    'id_portafolio'  => $id_portafolio,
                    'id_proyecto'    => $id_proyecto,
                    'fecha_registro' => $fecha_registro,
                    'estado'         => $estado,
                ]
            );
        }

        return response()->json($id_portafolio);
    }
    public function update_portafolio(Request $request, $id)
    {
        $id_portafolio = DB::table('proyectos.proy_portafolio')
        ->where('id_portafolio',$id)
        ->update([
                'descripcion' => $request->descripcion,
                'fecha_emision' => $request->fecha_emision,
                'responsable' => $request->responsable,
                'fecha_registro' => $request->fecha_registro,
                'usuario_registro' => $request->usuario_registro,
                'estado' => $request->estado
                // 'codigo' => $codigo,
                // 'id_empresa' => $request->id_empresa
            ]);

        $ids = $request->c_id_detalle;
        $count = count($ids);

        for ($i=0; $i<$count; $i++){
            $id_detalle     = $request->c_id_detalle[$i];
            $id_proyecto    = $request->c_id_proyecto[$i];
            $fecha_registro = $request->c_fecha_registro[$i];
            $estado         = $request->c_estado[$i];
            
            if ($id_detalle === 0){
                DB::table('proyectos.proy_porta_detalle')->insert(
                    [
                        'id_portafolio'  => $id,
                        'id_proyecto'    => $id_proyecto,
                        'fecha_registro' => $fecha_registro,
                        'estado'         => $estado,
                    ]
                );
            }
            else {
                DB::table('proyectos.proy_porta_detalle')
                ->where('id_detalle',$id_detalle)
                ->update([
                        // 'id_portafolio'  => $id_portafolio,
                        'id_proyecto'    => $id_proyecto,
                        'fecha_registro' => $fecha_registro,
                        'estado'         => $estado,
                    ]);
            }
        }

        return response()->json($id_portafolio);
    }
    public function anular_portafolio(Request $request, $id)
    {
        $id_portafolio = DB::table('proyectos.proy_portafolio')
                ->where('id_portafolio',$id)
                ->update([ 'estado' => 7 ]);

        $detalle = DB::table('proyectos.proy_porta_detalle')
                ->where('id_portafolio',$id)
                ->update([ 'estado' => 7 ]);

        return response()->json($id_portafolio);
    }
    /*
    //construye la valorizacion
    public function mostrar_pres_valorizacion($id_presupuesto)
    {
        $presupuesto = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_presup.codigo','proy_presup.fecha_emision',
            'proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg','proy_op_com.descripcion as nombre_opcion',
            'sis_moneda.simbolo as moneda','adm_contri.razon_social','proy_presup_importe.*')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')                
                ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_presup.id_presupuesto', '=', $id_presupuesto]])
                ->first();
                
        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.id_partida','proy_cd_partida.codigo','proy_cd_partida.descripcion',
            'proy_cd_partida.cantidad','proy_cd_partida.importe_unitario','proy_cd_partida.importe_parcial',
            'proy_cd_partida.cod_compo','alm_und_medida.abreviatura','proy_cu_partida.rendimiento',
            'proy_cd_pcronog.dias','proy_cd_pcronog.fecha_inicio','proy_cd_pcronog.fecha_fin',
                DB::raw('(SELECT SUM(metrado_actual) FROM proyectos.proy_cd_pvalori 
                    WHERE id_partida = proy_cd_partida.id_partida) AS metrado_anterior'),
                DB::raw('(SELECT SUM(porcen_actual) FROM proyectos.proy_cd_pvalori 
                    WHERE id_partida = proy_cd_partida.id_partida) AS porcen_anterior'),
                DB::raw('(SELECT SUM(costo_actual) FROM proyectos.proy_cd_pvalori 
                    WHERE id_partida = proy_cd_partida.id_partida) AS costo_anterior'),
                DB::raw('0 as metrado_actual'), DB::raw('0 as porcen_actual'), DB::raw('0 as costo_actual'),
                DB::raw('0 as metrado_saldo'), DB::raw('0 as porcen_saldo'), DB::raw('0 as costo_saldo'))
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
                ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
                ->join('proyectos.proy_cd_pcronog','proy_cd_pcronog.id_partida','=','proy_cd_partida.id_partida')
                ->where([['proy_cd_partida.id_cd','=',$presupuesto->id_cd],
                            ['proy_cd_pcronog.estado','=',1]])
                ->get()
                ->toArray();

        $part_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_ci_detalle.id_ci_detalle as id_partida','proy_ci_detalle.codigo',
            'proy_ci_detalle.descripcion','proy_ci_detalle.cantidad','proy_ci_detalle.importe_unitario',
            'proy_ci_detalle.importe_parcial','proy_ci_detalle.cod_compo','alm_und_medida.abreviatura',
            'proy_cu_partida.rendimiento','proy_ci_pcronog.dias','proy_ci_pcronog.fecha_inicio',
            'proy_ci_pcronog.fecha_fin',
                DB::raw('(SELECT SUM(metrado_actual) FROM proyectos.proy_ci_pvalori 
                    WHERE id_partida = proy_ci_detalle.id_ci_detalle) AS metrado_anterior'),
                DB::raw('(SELECT SUM(porcen_actual) FROM proyectos.proy_ci_pvalori 
                    WHERE id_partida = proy_ci_detalle.id_ci_detalle) AS porcen_anterior'),
                DB::raw('(SELECT SUM(costo_actual) FROM proyectos.proy_ci_pvalori 
                    WHERE id_partida = proy_ci_detalle.id_ci_detalle) AS costo_anterior'),
                DB::raw('0 as metrado_actual'), DB::raw('0 as porcen_actual'), DB::raw('0 as costo_actual'),
                DB::raw('0 as metrado_saldo'), DB::raw('0 as porcen_saldo'), DB::raw('0 as costo_saldo'))
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
                ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_ci_detalle.id_cu_partida')
                ->join('proyectos.proy_ci_pcronog','proy_ci_pcronog.id_partida','=','proy_ci_detalle.id_ci_detalle')
                ->where([['proy_ci_detalle.id_ci','=',$presupuesto->id_ci],
                        ['proy_ci_pcronog.estado','=',1]])
                    ->get()
                    ->toArray();

        $part_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_gg_detalle.id_gg_detalle as id_partida','proy_gg_detalle.codigo',
            'proy_gg_detalle.descripcion','proy_gg_detalle.cantidad','proy_gg_detalle.importe_unitario',
            'proy_gg_detalle.importe_parcial','proy_gg_detalle.cod_compo','alm_und_medida.abreviatura',
            'proy_cu_partida.rendimiento','proy_gg_pcronog.dias','proy_gg_pcronog.fecha_inicio',
            'proy_gg_pcronog.fecha_fin',
                DB::raw('(SELECT SUM(metrado_actual) FROM proyectos.proy_gg_pvalori 
                    WHERE id_partida = proy_gg_detalle.id_gg_detalle) AS metrado_anterior'),
                DB::raw('(SELECT SUM(porcen_actual) FROM proyectos.proy_gg_pvalori 
                    WHERE id_partida = proy_gg_detalle.id_gg_detalle) AS porcen_anterior'),
                DB::raw('(SELECT SUM(costo_actual) FROM proyectos.proy_gg_pvalori 
                    WHERE id_partida = proy_gg_detalle.id_gg_detalle) AS costo_anterior'),
                DB::raw('0 as metrado_actual'), DB::raw('0 as porcen_actual'), DB::raw('0 as costo_actual'),
                DB::raw('0 as metrado_saldo'), DB::raw('0 as porcen_saldo'), DB::raw('0 as costo_saldo'))
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
                ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_gg_detalle.id_cu_partida')
                ->join('proyectos.proy_gg_pcronog','proy_gg_pcronog.id_partida','=','proy_gg_detalle.id_gg_detalle')
                ->where([['proy_gg_detalle.id_gg', '=', $presupuesto->id_gg],
                         ['proy_gg_pcronog.estado','=',1]])
                    ->get()
                    ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
                ->where([['proy_cd_compo.id_cd', '=', $presupuesto->id_cd]])
                ->get()->toArray();

        $componentes_cd = [];
        $array = [];

        foreach ($compo_cd as $comp){
            $total = 0;
            foreach($part_cd as $partidax){
                if ($comp->codigo == $partidax->cod_compo){
                    array_push($array, $partidax);
                    $total += $partidax->importe_parcial;
                }
            }

            $nuevo_comp = [
                "id_cd_compo"=>$comp->id_cd_compo,
                "id_cd"=>$comp->id_cd,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];

            $array = [];
            array_push($componentes_cd,$nuevo_comp);
        }
        
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
                ->where([['proy_ci_compo.id_ci', '=', $presupuesto->id_ci]])
                ->get();

        $componentes_ci = [];
        $array = [];

        foreach ($compo_ci as $comp){
            $total = 0;
            foreach($part_ci as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_ci_compo"=>$comp->id_ci_compo,
                "id_ci"=>$comp->id_ci,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_ci,$nuevo_comp);
        }

        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
                ->where([['proy_gg_compo.id_gg', '=', $presupuesto->id_gg]])
                ->get();

        $componentes_gg = [];
        $array = [];

        foreach ($compo_gg as $comp){
            $total = 0;
            foreach($part_gg as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_gg_compo"=>$comp->id_gg_compo,
                "id_gg"=>$comp->id_gg,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_gg,$nuevo_comp);
        }

        $cd = ["id_cd"=>$presupuesto->id_cd,"componentes_cd"=>$componentes_cd,"partidas_cd"=>$part_cd];
        $ci = ["id_ci"=>$presupuesto->id_ci,"componentes_ci"=>$componentes_ci,"partidas_ci"=>$part_ci];
        $gg = ["id_gg"=>$presupuesto->id_gg,"componentes_gg"=>$componentes_gg,"partidas_gg"=>$part_gg];

        return response()->json(["presupuesto"=>$presupuesto,"cd"=>$cd,"ci"=>$ci,"gg"=>$gg]);
    }*/
    
    //NUEVO ERP
    public function listar_contratos_proy($id){
        $data = DB::table('proyectos.proy_contrato')
            ->select('proy_contrato.*','sis_moneda.simbolo','proy_tp_contrato.descripcion as tipo_contrato')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
            ->join('proyectos.proy_tp_contrato','proy_tp_contrato.id_tp_contrato','=','proy_contrato.id_tp_contrato')
            ->where([['proy_contrato.id_proyecto', '=', $id],
                    ['proy_contrato.estado', '=', 1]])
            ->get();

        $html = '';
        foreach($data as $d){
            $ruta = '/proyectos/contratos/'.$d->archivo_adjunto;
            $file = asset('files').$ruta;

            $html .= '
                <tr id="con-'.$d->id_contrato.'">
                    <td>'.$d->tipo_contrato.'</td>
                    <td>'.$d->nro_contrato.'</td>
                    <td>'.$d->descripcion.'</td>
                    <td>'.$d->fecha_contrato.'</td>
                    <td>'.$d->simbolo.'</td>
                    <td>'.$d->importe.'</td>
                    <td><a href="'.$file.'" target="_blank">'.$d->archivo_adjunto.'</a></td>
                    <td style="display:flex;">
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Item" onClick="anular_contrato('.$d->id_contrato.');"></i>
                    </td>
                </tr>';
        }
        return json_encode($html);
    }

    public function guardar_contrato(Request $request){
        $id_contrato = DB::table('proyectos.proy_contrato')->insertGetId(
            [
                'nro_contrato' => $request->nro_contrato,
                'fecha_contrato' => $request->fecha_contrato,
                'descripcion' => $request->descripcion,
                'moneda' => $request->moneda_con,
                'importe' => $request->importe_contrato,
                // 'archivo_adjunto' => $nombre,
                'id_proyecto' => $request->id_proyecto,
                'id_tp_contrato' => $request->id_tp_contrato,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_contrato'
            );
        //obtenemos el campo file definido en el formulario
        $file = $request->file('adjunto');
        if (isset($file)){
            //obtenemos el nombre del archivo   .'.'.$file->getClientOriginalName()
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $nombre = $id_contrato.'.'.$request->nro_contrato.'.'.$extension;
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \File::delete(public_path('proyectos/contratos/'.$nombre));
            \Storage::disk('archivos')->put('proyectos/contratos/'.$nombre,\File::get($file));
            
            $update = DB::table('proyectos.proy_contrato')
                ->where('id_contrato', $id_contrato)
                ->update(['archivo_adjunto' => $nombre]); 
        } else {
            $nombre = null;
        }
        return response()->json($id_contrato);
    }
    
    public function abrir_adjunto($file_name){
        $file_path = public_path('files/proyectos/contratos/'.$file_name);
        // $result = File::exists('files/proyectos/contratos/'.$file_name);
        if (file_exists($file_path)){
            return response()->download($file_path);
        } else {
            return response()->json("No existe dicho archivo!");
        }
    }
    
    public function anular_contrato($id_contrato){
        $data = DB::table('proyectos.proy_contrato')
            ->where('proy_contrato.id_contrato', $id_contrato)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function mostrar_presupuestos($tp)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_op_com.descripcion', 'proy_presup_importe.sub_total',
                     'proy_presup_importe.total_presupuestado','sis_moneda.simbolo','adm_contri.razon_social')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.estado', '!=', 7],['proy_presup.id_tp_presupuesto', '=', $tp]])
                ->orderBy('proy_presup.id_presupuesto')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_presupuestos_copia($tp,$menos_id)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_op_com.descripcion', 'proy_presup_importe.total_presupuestado',
                     'sis_moneda.simbolo','adm_contri.razon_social')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.estado', '!=', 7],
                        ['proy_presup.id_tp_presupuesto', '=', $tp],
                        ['proy_presup.id_presupuesto','!=',$menos_id]])
                ->orderBy('proy_presup.id_presupuesto')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_pres_crono($tiene_crono, $tp_presup)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_op_com.descripcion', 'proy_presup_importe.sub_total',
                     'sis_moneda.simbolo','adm_contri.razon_social')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.estado', '!=', 7],
                ['proy_presup.id_tp_presupuesto', '=', $tp_presup],
                ['proy_presup.cronograma', '=', ($tiene_crono == 0 ? false : true)]])
                ->orderBy('proy_presup.fecha_emision','desc')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_pres_cronoval($tiene_crono, $tp_presup)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_op_com.descripcion', 'proy_presup_importe.sub_total',
                     'sis_moneda.simbolo')//,'adm_contri.razon_social'
            // ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            // ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            // ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->where([['proy_presup.estado', '!=', 7],
                ['proy_presup.id_tp_presupuesto', '=', $tp_presup],
                ['proy_presup.cronograma', '=', true],
                ['proy_presup.cronoval', '=', ($tiene_crono == 0 ? false : true)]])
                ->orderBy('proy_presup.fecha_emision','desc')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_propuesta_crono($tiene_crono)
    {
        $tp_propuesta = 3;
        $data = DB::table('finanzas.presup')
        ->select('presup.*')
        ->where([['presup.tp_presup','=',$tp_propuesta],['presup.estado','!=',7],
                ['presup.cronograma', '=', ($tiene_crono == 0 ? false : true)]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_propuesta_cronoval($tiene_crono)
    {
        $tp_propuesta = 3;
        $data = DB::table('finanzas.presup')
        ->select('presup.*')
        ->where([['presup.tp_presup','=',$tp_propuesta],['presup.estado','!=',7],
                ['presup.cronoval', '=', ($tiene_crono == 0 ? false : true)]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_presint($id)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_proyecto.descripcion as descripcion_proy',
                     'proy_op_com.descripcion', 'proy_presup_importe.total_costo_directo', 
                     'proy_presup_importe.total_ci', 'proy_presup_importe.porcentaje_ci', 
                     'proy_presup_importe.total_gg', 'proy_presup_importe.porcentaje_gg', 
                     'proy_presup_importe.sub_total', 'proy_presup_importe.porcentaje_utilidad', 
                     'proy_presup_importe.total_utilidad', 'proy_presup_importe.porcentaje_igv', 
                     'proy_presup_importe.total_igv', 'proy_presup_importe.total_presupuestado',
                     'sis_moneda.simbolo','adm_contri.razon_social','adm_estado_doc.estado_doc as des_estado')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->leftjoin('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_presup.id_proyecto')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_presup.estado')
                ->where([['proy_presup.id_presupuesto', '=', $id]])
                ->first();
        
        return response()->json($data);
    }

    public function listar_acus_cd($id)
    {
        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*','proy_sis_contrato.descripcion as nombre_sistema',
            'proy_cu_partida.unid_medida as cu_unid_medida',
            'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_cd_partida.id_sistema')
            ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_partida.id_cd', '=', $id],
                     ['proy_cd_partida.estado', '!=', 7]])
            ->orderBy('proy_cd_partida.codigo')
            ->get()
            ->toArray();
        
        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id],
                    ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();
        
        $html = '';
        $total = 0;
        $sistemas = $this->mostrar_sis_contrato_cbo();

        foreach ($compo_cd as $comp){
            $codigo = "'".$comp->codigo."'";
            $desc = "'".$comp->codigo." - ".$comp->descripcion."'";
            $html .= '
            <tr id="com-'.$comp->id_cd_compo.'">
                <td></td>
                <td>'.$comp->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$comp->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right">'.number_format($comp->total_comp,2,".",",").'</td>
                <td></td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Título" onClick="agregar_compo_cd('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agrega_partida_cd('.$codigo.','.$desc.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Título" onClick="editar_compo_cd('.$comp->id_cd_compo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Título" onClick="update_compo_cd('.$comp->id_cd_compo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Título" onClick="anular_compo_cd('.$comp->id_cd_compo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$comp->cod_padre.'</td>
            </tr>';
            
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $total += $partida->importe_parcial;
                    $id_sistema = (isset($partida->id_sistema) ? $partida->id_sistema : 0);
                    $html .= '
                    <tr id="par-'.$partida->id_partida.'">
                        <td>
                            <i class="fas fa-arrow-alt-circle-down" data-toggle="tooltip" data-placement="bottom" title="Bajar Partida" onClick="bajar_partida_cd('.$partida->id_partida.');"></i>
                            <i class="fas fa-arrow-alt-circle-up" data-toggle="tooltip" data-placement="bottom" title="Subir Partida" onClick="subir_partida_cd('.$partida->id_partida.');"></i>
                        </td>
                        <td id="cu-'.$partida->id_cu_partida.'">'.$partida->codigo.'</td>
                        <td id="ccu-'.$partida->cod_acu.'">'.$partida->descripcion.'</td>
                        <td id="abr-'.$partida->cu_unid_medida.'">'.$partida->abreviatura.'</td>
                        <td><input type="number" class="input-data right" name="cantidad" value="'.$partida->cantidad.'" onChange="calcula_total_cd('.$partida->id_partida.');" disabled="true"/></td>
                        <td><input type="number" class="input-data right" style="width:130px;" name="importe_unitario" value="'.round($partida->importe_unitario,6,PHP_ROUND_HALF_UP).'" onChange="calcula_total_cd('.$partida->id_partida.');" disabled="true"/></td>
                        <td><input type="number" class="input-data right" style="width:130px;" name="importe_parcial" value="'.round($partida->importe_parcial,6,PHP_ROUND_HALF_UP).'" onChange="calcula_total_cd('.$partida->id_partida.');" disabled="true"/></td>
                        <td>
                            <select class="input-data" name="id_sistema" disabled="true">
                                <option value="0">Elija una opción</option>';
                                foreach ($sistemas as $row) {
                                    if ($id_sistema == $row->id_sis_contrato){
                                        $html.='<option value="'.$row->id_sis_contrato.'" selected>'.$row->descripcion.'</option>';
                                    } else {
                                        $html.='<option value="'.$row->id_sis_contrato.'">'.$row->descripcion.'</option>';
                                    }
                                }
                            $html.='</select>
                        </td>
                        <td style="display:flex;">
                            <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida_cd('.$partida->id_partida.');"></i>
                            <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_partida_cd('.$partida->id_partida.');"></i>
                            <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida_cd('.$partida->id_partida.');"></i>
                            <i class="fas fa-pen-square icon-tabla green visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar A.C.U." onClick="edit_acu('.$partida->id_cu_partida.','.$partida->id_partida.');"></i>
                            <i class="fas fa-bars icon-tabla purple boton" data-toggle="tooltip" data-placement="bottom" title="Ver A.C.U." onClick="ver_acu_detalle('.$partida->id_cu_partida.','.$partida->cantidad.');"></i>
                            <i class="fas fa-file-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Lecciones Aprendidas" onClick="open_presLeccion('."'"."cd"."'".','.$partida->id_partida.');"></i>
                        </td>
                        <td hidden>'.$partida->cod_compo.'</td>
                    </tr>';
                }
            }
        }
        
        return json_encode(['html'=>$html,'total'=>$total]);
    }

    public function nuevo_cronograma($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*','proy_presup.fecha_emision',
            'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_partida.id_cd', '=', $id_presupuesto],
                     ['proy_cd_partida.estado', '!=', 7]])
            ->orderBy('proy_cd_partida.codigo')
            ->get()
            ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                    ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();

        $tp_pred = DB::table('proyectos.proy_tp_predecesora')->where('estado',1)->get();
        $lista = [];
        $partidas = [];
        $fecha_emision = null;
        $i = 1;

        foreach($compo_cd as $comp){
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $fecha_emision = $partida->fecha_emision;
                    $duracion = round(($partida->cantidad / $partida->rendimiento),2,PHP_ROUND_HALF_UP);
                    $fecha_fin = date("Y-m-d",strtotime($partida->fecha_emision."+ ".round($duracion,0,PHP_ROUND_HALF_UP)." days"));
                    $nuevo = [
                        'id_partida' => $partida->id_partida,
                        'id_cu_partida' => $partida->id_cu_partida,
                        'id_presupuesto' => $partida->id_cd,
                        'tipo' => 'cd',
                        'nro_orden' => $i,
                        'dias' => $duracion,
                        'fecha_inicio' => $partida->fecha_emision,
                        'fecha_fin' => $fecha_fin,
                        'tp_predecesora' => 1,
                        'predecesora' => "",
                        'dias_pos' => 0,
                        'codigo' => $partida->codigo,
                        'descripcion' => $partida->descripcion,
                        'cod_compo' => $partida->cod_compo,
                        'cantidad' => $partida->cantidad,
                        'abreviatura' => $partida->abreviatura,
                        'rendimiento' => $partida->rendimiento
                    ];
                    array_push($partidas, $nuevo);
                    $i++;
                }
            }
            $nuevo_comp = [
                'id_cd_compo' => $comp->id_cd_compo,
                'codigo' => $comp->codigo,
                'descripcion' => $comp->descripcion,
                'cod_padre' => $comp->cod_padre,
                'partidas' => $partidas
            ];
            $partidas = [];
            array_push($lista, $nuevo_comp);
        }

        $ci = [
            'id_partida' => null,
            'id_cu_partida' => null,
            'id_presupuesto' => $id_presupuesto,
            'tipo' => 'ci',
            'nro_orden' => $i,
            'dias' => 0,
            'fecha_inicio' => $fecha_emision,
            'fecha_fin' => $fecha_emision,
            'tp_predecesora' => 1,
            'predecesora' => "",
            'dias_pos' => 0,
            'codigo' => null,
            'descripcion' => null,
            'cod_compo' => null,
            'cantidad' => null,
            'abreviatura' => null,
            'rendimiento' => null
        ];
        array_push($lista, $ci);
        $i++;

        $gg = [
            'id_partida' => null,
            'id_cu_partida' => null,
            'id_presupuesto' => $id_presupuesto,
            'tipo' => 'gg',
            'nro_orden' => $i,
            'dias' => 0,
            'fecha_inicio' => $fecha_emision,
            'fecha_fin' => $fecha_emision,
            'tp_predecesora' => 1,
            'predecesora' => "",
            'dias_pos' => 0,
            'codigo' => null,
            'descripcion' => null,
            'cod_compo' => null,
            'cantidad' => null,
            'abreviatura' => null,
            'rendimiento' => null
        ];
        array_push($lista, $gg);

        $presup = DB::table('proyectos.proy_presup')->where('id_presupuesto',$id_presupuesto)->first();

        return response()->json(['lista'=>$lista,'unid_program'=>$presup->unid_program,
        'fecha_inicio_crono'=>$presup->fecha_emision,'tp_pred'=>$tp_pred]);
    }

    public function listar_cronograma($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.proy_cd_pcronog')
            ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cu.id_cu',
            'proy_cd_partida.id_cu_partida','proy_cd_partida.cod_compo',
            'proy_cd_partida.codigo','proy_cd_partida.descripcion','proy_cd_partida.cantidad',
            'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->leftjoin('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
            ->leftjoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_pcronog.id_presupuesto', '=', $id_presupuesto],
                    ['proy_cd_pcronog.estado', '!=', 7]])
            ->orderBy('proy_cd_pcronog.nro_orden')
            ->get()
            ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                    ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();

        $tp_pred = DB::table('proyectos.proy_tp_predecesora')->where('estado',1)->get();
        $lista = [];
        $partidas = [];
        $fini = null;
        $ffin = null;

        foreach($compo_cd as $comp){
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    if ($ffin == null){
                        $ffin = $partida->fecha_fin;
                    } else {
                        if ($ffin < $partida->fecha_fin){
                            $ffin = $partida->fecha_fin;
                        }
                    }
                    if ($fini == null){
                        $fini = $partida->fecha_inicio;
                    } else {
                        if ($fini > $partida->fecha_inicio){
                            $fini = $partida->fecha_inicio;
                        }
                    }
                    array_push($partidas, $partida);
                }
            }
            $nuevo_comp = [
                'id_cd_compo' => $comp->id_cd_compo,
                'codigo' => $comp->codigo,
                'descripcion' => $comp->descripcion,
                'cod_padre' => $comp->cod_padre,
                'fecha_inicio' => $fini,
                'fecha_fin' => $ffin,
                'partidas' => $partidas
            ];
            $partidas = [];
            $fini = null;
            $ffin = null;
            array_push($lista, $nuevo_comp);
        }

        foreach($part_cd as $partida){
            if ($partida->tipo !== 'cd'){
                array_push($lista, $partida);
                // array_push($no_part, $partida);
            }
        }
        // array_push($lista, $no_part);
        $presup = DB::table('proyectos.proy_presup')->where('id_presupuesto',$id_presupuesto)->first();

        return response()->json(['lista'=>$lista,'unid_program'=>$presup->unid_program,
        'fecha_inicio_crono'=>$presup->fecha_inicio_crono,'tp_pred'=>$tp_pred]);
    }

    public function ver_gant($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.proy_cd_pcronog')
        ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cu.id_cu',
        'proy_cd_partida.id_cu_partida','proy_cd_partida.cod_compo',
        'proy_cd_partida.codigo','proy_cd_partida.descripcion','proy_cd_partida.cantidad',
        'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
        ->leftjoin('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
        ->leftjoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
        ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
        ->where([['proy_cd_pcronog.id_presupuesto', '=', $id_presupuesto],
                 ['proy_cd_pcronog.estado', '=', 1]])
        ->orderBy('proy_cd_pcronog.nro_orden')
        ->get()
        ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
        ->select('proy_cd_compo.*')
        ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                ['proy_cd_compo.estado', '!=', 7]])
        ->orderBy('proy_cd_compo.codigo')
        ->get();
        
        return response()->json(['partidas'=>$part_cd,'titulos'=>$compo_cd]);
    }

    public function guardar_crono(Request $request){
        $ids = explode(',',$request->id_partida);
        $nro = explode(',',$request->nro_orden);
        $dias = explode(',',$request->dias);
        $fini = explode(',',$request->fini);
        $ffin = explode(',',$request->ffin);
        $tp_pred = explode(',',$request->tp_pred);
        $dias_pos = explode(',',$request->dias_pos);
        $pred = explode(',',$request->predecesora);
        $count = count($ids);
        $id_crono = 0;
        $fecha_inicio_crono = null;

        for ($i=0; $i<$count; $i++){
            if (is_numeric($ids[$i])){
                $id = $ids[$i];
                $tipo = 'cd';
            } else {
                $id = null;
                $tipo = $ids[$i];
            }
            $no = $nro[$i];
            $dia = $dias[$i];
            $ini = $fini[$i];
            $fin = $ffin[$i];
            $tp_pre = $tp_pred[$i];
            $dpos = $dias_pos[$i];
            $pre = $pred[$i];

            if ($fecha_inicio_crono == null){
                $fecha_inicio_crono = $ini;
            } else if ($ini < $fecha_inicio_crono){
                $fecha_inicio_crono = $ini;
            }

            if ($request->modo === 'new'){
                $id_crono = DB::table('proyectos.proy_cd_pcronog')
                ->insert([
                    'id_partida'=>$id,
                    'id_presupuesto'=>$request->id_presupuesto,
                    'tipo'=>$tipo,
                    'nro_orden'=>$no,
                    'fecha_inicio'=>$ini,
                    'fecha_fin'=>$fin,
                    'dias'=> ($dia!=='' ? $dia : 0),
                    'tp_predecesora'=>$tp_pre,
                    'dias_pos'=> ($dpos!=='' ? $dpos : 0),
                    'predecesora'=> ($pre!=='' ? $pre : 0),
                    'fecha_registro'=>date('Y-m-d'),
                    'estado'=>1
                ]);
            } 
            else {
                $crono = DB::table('proyectos.proy_cd_pcronog')
                ->where([['id_partida','=',$id],['tipo','=',$tipo]])
                ->first();

                $id_crono = DB::table('proyectos.proy_cd_pcronog')
                ->where('id_pcronog',$crono->id_pcronog)
                ->update([
                    'fecha_inicio'=>$ini,
                    'fecha_fin'=>$fin,
                    'tp_predecesora'=>$tp_pre,
                    'dias_pos'=>$dpos,
                    'predecesora'=>$pre,
                    'dias'=>$dia
                ]);
            }
        }
        DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$request->id_presupuesto)
        ->update([  'cronograma'=>true,
                    'unid_program'=>$request->unid_program,
                    'fecha_inicio_crono'=>$fecha_inicio_crono
                    ]);

        return response()->json($id_crono);
    }

    public function actualizar_partidas_cronograma($id_presupuesto)
    {
        try {
            DB::beginTransaction();

            $part_cd = DB::table('proyectos.proy_cd_partida')
                ->select('proy_cd_partida.*','proy_presup.fecha_emision','proy_cu_partida.rendimiento')
                ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
                // ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
                ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
                // ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
                ->where([['proy_cd_partida.id_cd', '=', $id_presupuesto],
                        ['proy_cd_partida.estado', '!=', 7]])
                ->orderBy('proy_cd_partida.codigo')
                ->get()
                ->toArray();

            $i = 1;

            foreach($part_cd as $partida)
            {
                $par_crono = DB::table('proyectos.proy_cd_pcronog')
                    ->select('proy_cd_pcronog.*','proy_cd_partida.cantidad','proy_cu_partida.rendimiento')
                    ->join('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
                    ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
                    ->where([['proy_cd_pcronog.id_partida','=',$partida->id_partida],['proy_cd_pcronog.tipo','=','cd']])
                    ->first();

                if ($par_crono !== null){
                    
                    $fecha_inicio = $par_crono->fecha_inicio;
                    $duracion = round(($par_crono->cantidad / $par_crono->rendimiento),2,PHP_ROUND_HALF_UP);
                    $fecha_fin = date("Y-m-d",strtotime($fecha_inicio."+ ".round($duracion,0,PHP_ROUND_HALF_UP)." days"));

                    DB::table('proyectos.proy_cd_pcronog')
                    ->where('id_pcronog',$par_crono->id_pcronog)
                    ->update([
                        'nro_orden'=>$i,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'tp_predecesora'=>1,
                        'dias_pos'=>0,
                        'predecesora'=>"",
                        'dias'=>$duracion
                    ]);
                }
                else {
                    $fecha_inicio = $partida->fecha_emision;
                    $duracion = round(($partida->cantidad / $partida->rendimiento),2,PHP_ROUND_HALF_UP);
                    $fecha_fin = date("Y-m-d",strtotime($fecha_inicio."+ ".round($duracion,0,PHP_ROUND_HALF_UP)." days"));

                    DB::table('proyectos.proy_cd_pcronog')
                    ->insert([
                        'id_partida'=>$partida->id_partida,
                        'id_presupuesto'=>$id_presupuesto,
                        'tipo'=>'cd',
                        'nro_orden'=>$i,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'dias'=> $duracion,
                        'tp_predecesora'=>1,
                        'dias_pos'=>0,
                        'predecesora'=>"",
                        'fecha_registro'=>date('Y-m-d'),
                        'estado'=>1
                    ]);
                }
                $i++;
            }
            DB::commit();
            return response()->json($id_presupuesto);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function anular_crono($id_presupuesto)
    {
        $presup = DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$id_presupuesto)
        ->first();
        $anula = 0;

        if ($presup->cronoval == false)
        {
            $anula = DB::table('proyectos.proy_presup')
            ->where('id_presupuesto',$id_presupuesto)
            ->update([  'cronograma'=>false,
                        'unid_program'=>null,
                        'fecha_inicio_crono'=>null
            ]);
            $anula = DB::table('proyectos.proy_cd_pcronog')
            ->where('id_presupuesto',$id_presupuesto)
            ->update([ 'estado' => 7 ]);
        }

        return response()->json($anula);
    }

    /**Cronograma Propuesta */
    public function listar_crono_propuesta($id_presupuesto)
    {
        $partidas = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup.fecha_emision',
            'alm_und_medida.abreviatura')
            ->join('finanzas.presup','presup.id_presup','=','presup_par.id_presup')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','presup_par.unidad_medida')
            ->where([['presup_par.id_presup', '=', $id_presupuesto],
                     ['presup_par.estado', '!=', 7]])
            ->orderBy('presup_par.codigo')
            ->get();

        $titulos = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id_presupuesto],
                    ['presup_titu.estado', '!=', 7]])
            ->orderBy('presup_titu.codigo')
            ->get();

        $tp_pred = DB::table('proyectos.proy_tp_predecesora')->where('estado',1)->get();
        $lista = [];
        $lista_partidas = [];
        $fecha_emision = null;
        $i = 1;

        foreach($titulos as $comp){
            foreach($partidas as $partida){
                if ($comp->codigo == $partida->cod_padre){
                    $fecha_emision = $partida->fecha_emision;
                    $fecha_fin = date("Y-m-d",strtotime($partida->fecha_emision."+ ".round(1,0,PHP_ROUND_HALF_UP)." days"));
                    
                    $nuevo = [
                        'id_partida' => $partida->id_partida,
                        'id_presupuesto' => $partida->id_presup,
                        'nro_orden' => $i,
                        'dias' => 1,
                        'fecha_inicio' => $partida->fecha_emision,
                        'fecha_fin' => $fecha_fin,
                        'tp_predecesora' => 1,
                        'predecesora' => "",
                        'dias_pos' => 0,
                        'codigo' => $partida->codigo,
                        'descripcion' => $partida->descripcion,
                        'cod_padre' => $partida->cod_padre,
                        'metrado' => $partida->metrado,
                        'abreviatura' => $partida->abreviatura,
                        // 'rendimiento' => $partida->rendimiento
                    ];
                    array_push($lista_partidas, $nuevo);
                    $i++;
                }
            }
            $nuevo_comp = [
                'id_titulo' => $comp->id_titulo,
                'codigo' => $comp->codigo,
                'descripcion' => $comp->descripcion,
                'cod_padre' => $comp->cod_padre,
                'partidas' => $lista_partidas
            ];
            array_push($lista, $nuevo_comp);
            $lista_partidas = [];
        }

        foreach($partidas as $partida){
            if ($partida->cod_padre == null){
                $fecha_emision = $partida->fecha_emision;
                $fecha_fin = date("Y-m-d",strtotime($partida->fecha_emision."+ ".round(1,0,PHP_ROUND_HALF_UP)." days"));
                
                $nuevo = [
                    'id_partida' => $partida->id_partida,
                    'id_presupuesto' => $partida->id_presup,
                    'nro_orden' => $i,
                    'dias' => 1,
                    'fecha_inicio' => $partida->fecha_emision,
                    'fecha_fin' => $fecha_fin,
                    'tp_predecesora' => 1,
                    'predecesora' => "",
                    'dias_pos' => 0,
                    'codigo' => $partida->codigo,
                    'descripcion' => $partida->descripcion,
                    'cod_padre' => $partida->cod_padre,
                    'metrado' => $partida->metrado,
                    'abreviatura' => $partida->abreviatura,
                    // 'rendimiento' => $partida->rendimiento
                ];
                array_push($lista, $nuevo);
                $i++;
            }
        }
        
        $presup = DB::table('finanzas.presup')->where('id_presup',$id_presupuesto)->first();

        return response()->json(['lista'=>$lista,'unid_program'=>$presup->unid_program_crono,
        'fecha_inicio_crono'=>$presup->fecha_emision,'tp_pred'=>$tp_pred]);
    }

    public function listar_cronograma_propuesta($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.presup_par_crono')
            ->select('presup_par_crono.*','presup.fecha_emision',
            'presup_par.cod_padre','presup_par.codigo','presup_par.descripcion',
            'presup_par.metrado','alm_und_medida.abreviatura')
            ->leftjoin('finanzas.presup_par','presup_par.id_partida','=','presup_par_crono.id_partida')
            ->leftjoin('finanzas.presup','presup.id_presup','=','presup_par_crono.id_presup')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','presup_par.unidad_medida')
            ->where([['presup_par_crono.id_presup', '=', $id_presupuesto],
                    ['presup_par_crono.estado', '=', 1]])
            ->orderBy('presup_par_crono.nro_orden')
            ->get();

        $compo_cd = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id_presupuesto],
                    ['presup_titu.estado', '!=', 7]])
            ->orderBy('presup_titu.codigo')
            ->get();

        $tp_pred = DB::table('proyectos.proy_tp_predecesora')->where('estado',1)->get();
        $lista = [];
        $partidas = [];
        $fini = null;
        $ffin = null;

        foreach($compo_cd as $comp){
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_padre){
                    if ($ffin == null){
                        $ffin = $partida->fecha_fin;
                    } else {
                        if ($ffin < $partida->fecha_fin){
                            $ffin = $partida->fecha_fin;
                        }
                    }
                    if ($fini == null){
                        $fini = $partida->fecha_inicio;
                    } else {
                        if ($fini > $partida->fecha_inicio){
                            $fini = $partida->fecha_inicio;
                        }
                    }
                    array_push($partidas, $partida);
                }
            }
            $nuevo_comp = [
                'id_titulo' => $comp->id_titulo,
                'codigo' => $comp->codigo,
                'descripcion' => $comp->descripcion,
                'cod_padre' => $comp->cod_padre,
                'fecha_inicio' => $fini,
                'fecha_fin' => $ffin,
                'partidas' => $partidas
            ];
            array_push($lista, $nuevo_comp);
            $partidas = [];
            $fini = null;
            $ffin = null;
        }

        foreach($part_cd as $partida){
            if ($partida->cod_padre == null){
                array_push($lista, $partida);
            }
        }
        $presup = DB::table('finanzas.presup')->where('id_presup',$id_presupuesto)->first();

        return response()->json(['lista'=>$lista,'unid_program'=>$presup->unid_program_crono,
        'fecha_inicio_crono'=>$presup->fecha_inicio_crono,'tp_pred'=>$tp_pred]);
    }

    public function ver_gant_propuesta($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.presup_par_crono')
        ->select('presup_par_crono.*','presup.fecha_emision',
        'presup_par.cod_padre','presup_par.codigo','presup_par.descripcion',
        'presup_par.metrado','alm_und_medida.abreviatura')
        ->leftjoin('finanzas.presup_par','presup_par.id_partida','=','presup_par_crono.id_partida')
        ->leftjoin('finanzas.presup','presup.id_presup','=','presup_par_crono.id_presup')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','presup_par.unidad_medida')
        ->where([['presup_par_crono.id_presup', '=', $id_presupuesto],
                 ['presup_par_crono.estado', '=', 1]])
        ->orderBy('presup_par_crono.nro_orden')
        ->get();

        $compo_cd = DB::table('finanzas.presup_titu')
        ->select('presup_titu.*')
        ->where([['presup_titu.id_presup', '=', $id_presupuesto],
                ['presup_titu.estado', '!=', 7]])
        ->orderBy('presup_titu.codigo')
        ->get();
        
        return response()->json(['partidas'=>$part_cd,'titulos'=>$compo_cd]);
    }

    public function guardar_crono_propuesta(Request $request){
        $ids = explode(',',$request->id_partida);
        $nro = explode(',',$request->nro_orden);
        $dias = explode(',',$request->dias);
        $fini = explode(',',$request->fini);
        $ffin = explode(',',$request->ffin);
        $tp_pred = explode(',',$request->tp_pred);
        $dias_pos = explode(',',$request->dias_pos);
        $pred = explode(',',$request->predecesora);
        $count = count($ids);
        $id_crono = 0;
        $fecha_inicio_crono = null;

        for ($i=0; $i<$count; $i++){
            $id = $ids[$i];
            $no = $nro[$i];
            $dia = $dias[$i];
            $ini = $fini[$i];
            $fin = $ffin[$i];
            $tp_pre = $tp_pred[$i];
            $dpos = $dias_pos[$i];
            $pre = $pred[$i];

            if ($fecha_inicio_crono == null){
                $fecha_inicio_crono = $ini;
            } else if ($ini < $fecha_inicio_crono){
                $fecha_inicio_crono = $ini;
            }

            if ($request->modo === 'new'){
                $id_crono = DB::table('proyectos.presup_par_crono')
                ->insert([
                    'id_partida'=>$id,
                    'id_presup'=>$request->id_presupuesto,
                    'nro_orden'=>$no,
                    'fecha_inicio'=>$ini,
                    'fecha_fin'=>$fin,
                    'dias'=> ($dia!=='' ? $dia : 0),
                    'predecesora'=> ($pre!=='' ? $pre : 0),
                    'tp_predecesora'=>$tp_pre,
                    'dias_pos'=> ($dpos!=='' ? $dpos : 0),
                    'fecha_registro'=>date('Y-m-d'),
                    'estado'=>1
                ]);
            } 
            else {
                $crono = DB::table('proyectos.presup_par_crono')
                ->where([['id_partida','=',$id]])
                ->first();

                $id_crono = DB::table('proyectos.presup_par_crono')
                ->where('id_pcrono',$crono->id_pcrono)
                ->update([
                    'fecha_inicio'=>$ini,
                    'fecha_fin'=>$fin,
                    'dias'=>$dia,
                    'predecesora'=>$pre,
                    'tp_predecesora'=>$tp_pre,
                    'dias_pos'=>$dpos,
                ]);
            }
        }
        DB::table('finanzas.presup')
        ->where('id_presup',$request->id_presupuesto)
        ->update([  'cronograma'=>true,
                    'unid_program_crono'=>$request->unid_program,
                    'fecha_inicio_crono'=>$fecha_inicio_crono
                    ]);

        return response()->json($id_crono);
    }   

    public function nuevo_crono_valorizado($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.proy_cd_pcronog')
            ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cu.id_cu',
            'proy_cd_partida.id_cu_partida','proy_cd_partida.cod_compo','proy_cd_partida.codigo',
            'proy_cd_partida.descripcion','proy_cd_partida.cantidad','proy_cd_partida.importe_parcial',
            'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->leftjoin('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
            ->leftjoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_pcronog.id_presupuesto', '=', $id_presupuesto],
                     ['proy_cd_pcronog.estado', '=', 1]])
            ->orderBy('proy_cd_pcronog.nro_orden')
            ->get()
            ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                    ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();

        $lista = [];
        $partidas = [];
        $fini = null;
        $ffin = null;

        foreach($compo_cd as $comp){
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    if ($ffin == null){
                        $ffin = $partida->fecha_fin;
                    } else {
                        if ($ffin < $partida->fecha_fin){
                            $ffin = $partida->fecha_fin;
                        }
                    }
                    if ($fini == null){
                        $fini = $partida->fecha_inicio;
                    } else {
                        if ($fini > $partida->fecha_inicio){
                            $fini = $partida->fecha_inicio;
                        }
                    }
                    array_push($partidas, $partida);
                }
            }
            $nuevo_comp = [
                'id_cd_compo' => $comp->id_cd_compo,
                'codigo' => $comp->codigo,
                'descripcion' => $comp->descripcion,
                'cod_padre' => $comp->cod_padre,
                'partidas' => $partidas
            ];
            array_push($lista, $nuevo_comp);
            $partidas = [];
        }

        foreach($part_cd as $partida){
            if ($partida->tipo !== 'cd'){
                if ($ffin == null){
                    $ffin = $partida->fecha_fin;
                } else {
                    if ($ffin < $partida->fecha_fin){
                        $ffin = $partida->fecha_fin;
                    }
                }
                if ($fini == null){
                    $fini = $partida->fecha_inicio;
                } else {
                    if ($fini > $partida->fecha_inicio){
                        $fini = $partida->fecha_inicio;
                    }
                }
                array_push($lista, $partida);
            } 
        }
        $total = DB::table('proyectos.proy_presup_importe')
        ->select('proy_presup_importe.*','sis_moneda.simbolo')
        ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_presup_importe.id_presupuesto')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
        ->where('proy_presup_importe.id_presupuesto',$id_presupuesto)->first();

        return response()->json([ 'lista'=>$lista, 'fecha_inicio'=>$fini, 'fecha_fin'=>$ffin, 'moneda'=>$total->simbolo,
        'total_ci'=>$total->total_ci, 'total_gg'=>$total->total_gg,'sub_total'=>$total->sub_total ]);
    }

    public function mostrar_crono_valorizado($id_presupuesto)
    {
        $partidas = DB::table('proyectos.proy_cd_pcronog')
            ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cd_partida.cod_compo',
            'proy_cd_partida.codigo','proy_cd_partida.descripcion','proy_cd_partida.cantidad',
            'proy_cd_partida.importe_parcial','alm_und_medida.abreviatura')
            ->leftjoin('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
            ->leftjoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_pcronog.id_presupuesto', '=', $id_presupuesto],
                     ['proy_cd_pcronog.estado', '=', 1]])
            ->orderBy('proy_cd_pcronog.nro_orden')
            ->get()
            ->toArray();

        $titulos = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                     ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();
        
        $lista = [];
        $list_par = [];
        $fini = null;
        $ffin = null;
        
        foreach($titulos as $ti){
            foreach($partidas as $par){
                if ($ti->codigo == $par->cod_compo){
                    if ($ffin == null){
                        $ffin = $par->fecha_fin;
                    } else {
                        if ($ffin < $par->fecha_fin){
                            $ffin = $par->fecha_fin;
                        }
                    }
                    if ($fini == null){
                        $fini = $par->fecha_inicio;
                    } else {
                        if ($fini > $par->fecha_inicio){
                            $fini = $par->fecha_inicio;
                        }
                    }
                    $periodos = DB::table('proyectos.proy_cd_pcronoval')
                    ->where([['id_pcronog','=',$par->id_pcronog],['estado','=',1]])
                    ->get();

                    $nuevo_par = [
                        // 'id_pcronoval' => $par->id_pcronoval,
                        'id_pcronog' => $par->id_pcronog,
                        'codigo' => $par->codigo,
                        'descripcion' => $par->descripcion,
                        'dias' => $par->dias,
                        'importe_parcial' => $par->importe_parcial,
                        'periodos' => (isset($periodos) ? $periodos : [])
                    ];

                    array_push($list_par, $nuevo_par);
                }
            }
            $nuevo_comp = [
                'id_cd_compo' => $ti->id_cd_compo,
                'codigo' => $ti->codigo,
                'descripcion' => $ti->descripcion,
                'cod_padre' => $ti->cod_padre,
                'partidas' => $list_par
            ];
            array_push($lista, $nuevo_comp);
            $list_par = [];
        }
    
        $total = DB::table('proyectos.proy_presup_importe')
        ->select('proy_presup_importe.*','proy_presup.crono_cantidad','proy_presup.crono_unid_program','sis_moneda.simbolo')
        ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_presup_importe.id_presupuesto')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
        ->where('proy_presup_importe.id_presupuesto',$id_presupuesto)->first();

        foreach($partidas as $par){
            if ($par->id_partida == null){
                if ($ffin == null){
                    $ffin = $par->fecha_fin;
                } else {
                    if ($ffin < $par->fecha_fin){
                        $ffin = $par->fecha_fin;
                    }
                }
                if ($fini == null){
                    $fini = $par->fecha_inicio;
                } else {
                    if ($fini > $par->fecha_inicio){
                        $fini = $par->fecha_inicio;
                    }
                }
                $periodos = DB::table('proyectos.proy_cd_pcronoval')
                ->where([['id_pcronog','=',$par->id_pcronog],['estado','=',1]])
                ->get();

                $nuevo_par = [
                    // 'id_pcronoval' => $par->id_pcronoval,
                    'id_pcronog' => $par->id_pcronog,
                    'codigo' => ($par->tipo == 'ci' ? 'CI' : 'GG'),
                    'descripcion' => ($par->tipo == 'ci' ? 'COSTOS INDIRECTOS' : 'GASTOS GENERALES'),
                    'dias' => $par->dias,
                    'importe_parcial' => ($par->tipo == 'ci' ? $total->total_ci : $total->total_gg),
                    'periodos' => (isset($periodos) ? $periodos : [])
                ];
                array_push($lista, $nuevo_par);
            } 
        }

        return response()->json([ 'lista'=>$lista, 'fecha_inicio'=>$fini, 'fecha_fin'=>$ffin, 
        'total'=>$total->sub_total, 'moneda'=>$total->simbolo, 'cantidad'=>$total->crono_cantidad, 
        'unid_program'=>$total->crono_unid_program ]);
    }

    public function guardar_cronoval_presupuesto(Request $request)
    {
        $ids = explode(',',$request->id_pcronoval);
        $par = explode(',',$request->id_pcronog);
        $per = explode(',',$request->periodo);
        $por = explode(',',$request->porcentaje);
        $imp = explode(',',$request->importe);
        $count = count($ids);
        $data = 0;

        for ($i=0; $i<$count; $i++){
            $id = $ids[$i];
            $pa = $par[$i];
            $pe = $per[$i];
            $po = $por[$i];
            $im = $imp[$i];

            if ($request->modo === 'new'){
                $data = DB::table('proyectos.proy_cd_pcronoval')
                ->insert([
                    'id_pcronog'=>$pa,
                    'id_presupuesto'=>$request->id_presupuesto,
                    'periodo'=>$pe,
                    'porcentaje'=>$po,
                    'importe'=>$im,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1
                ]);
            } 
            else {
                $data = DB::table('proyectos.proy_cd_pcronoval')
                ->where('id_pcronoval',$id)
                ->update([
                    'periodo'=>$pe,
                    'porcentaje'=>$po,
                    'importe'=>$im,
                ]);
            }
        }

        if ($request->modo === 'new'){
            $nro = explode(',',$request->pnro);
            $ndias = explode(',',$request->pnro_dias);
            $dias = explode(',',$request->pdias);
            $ini = explode(',',$request->pfini);
            $fin = explode(',',$request->pffin);
            $tot = explode(',',$request->ptotal);
            $cnt = count($nro);
    
            for ($j=0; $j<$cnt; $j++){
                $nr = $nro[$j];
                $nd = $ndias[$j];
                $di = $dias[$j];
                $in = $ini[$j];
                $fi = $fin[$j];
                $to = $tot[$j];

                DB::table('proyectos.proy_presup_periodos')
                ->insert([
                    'id_presupuesto'=>$request->id_presupuesto,
                    'numero'=>$nr,
                    'nro_dias'=>$nd,
                    'dias_acum'=>$di,
                    'fecha_inicio'=>$in,
                    'fecha_fin'=>$fi,
                    'total'=>$to,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1
                ]);
            }
        }

        DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$request->id_presupuesto)
        ->update([ 'cronoval'=>true,
                   'crono_cantidad'=>$request->cantidad,
                   'crono_unid_program'=>$request->unid_program ]);

        return response()->json($data);
    }

    public function anular_cronoval($id_presupuesto)
    {
        $anula = DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$id_presupuesto)
        ->update([  'cronoval'=>false,
                    'crono_cantidad'=>null,
                    'crono_unid_program'=>null
        ]);
        $anula = DB::table('proyectos.proy_presup_periodos')
        ->where('id_presupuesto',$id_presupuesto)
        ->update([ 'estado' => 7 ]);

        $anula = DB::table('proyectos.proy_cd_pcronoval')
        ->where('id_presupuesto',$id_presupuesto)
        ->update([ 'estado' => 7 ]);

        return response()->json($anula);
    }

    public function mostrar_cronoval_propuesta($id_propuesta)
    {
        $partidas = DB::table('proyectos.presup_par_crono')
            ->select('presup_par_crono.*','presup.fecha_emision',
            'presup_par.cod_padre','presup_par.codigo','presup_par.descripcion',
            'presup_par.metrado','presup_par.importe_total','alm_und_medida.abreviatura')
            ->leftjoin('finanzas.presup_par','presup_par.id_partida','=','presup_par_crono.id_partida')
            ->leftjoin('finanzas.presup','presup.id_presup','=','presup_par.id_presup')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','presup_par.unidad_medida')
            ->where([['presup_par_crono.id_presup', '=', $id_propuesta],
                     ['presup_par_crono.estado', '=', 1]])
            ->orderBy('presup_par_crono.nro_orden')
            ->get();
            
        $titulos = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id_propuesta],
                     ['presup_titu.estado', '!=', 7]])
            ->orderBy('presup_titu.codigo')
            ->get();
    
        $lista = [];
        $list_par = [];
        $fini = null;
        $ffin = null;

        foreach($titulos as $ti){
            foreach($partidas as $par){
                if ($ti->codigo == $par->cod_padre){
                    if ($ffin == null){
                        $ffin = $par->fecha_fin;
                    } else {
                        if ($ffin < $par->fecha_fin){
                            $ffin = $par->fecha_fin;
                        }
                    }
                    if ($fini == null){
                        $fini = $par->fecha_inicio;
                    } else {
                        if ($fini > $par->fecha_inicio){
                            $fini = $par->fecha_inicio;
                        }
                    }
                    array_push($list_par, $par);
                }
            }
            $nuevo_comp = [
                'id_titulo' => $ti->id_titulo,
                'codigo' => $ti->codigo,
                'descripcion' => $ti->descripcion,
                'cod_padre' => $ti->cod_padre,
                'partidas' => $list_par
            ];
            array_push($lista, $nuevo_comp);
            $list_par = [];
        }
    
        foreach($partidas as $par){
            if ($par->cod_padre == null){
                if ($ffin == null){
                    $ffin = $par->fecha_fin;
                } else {
                    if ($ffin < $par->fecha_fin){
                        $ffin = $par->fecha_fin;
                    }
                }
                if ($fini == null){
                    $fini = $par->fecha_inicio;
                } else {
                    if ($fini > $par->fecha_inicio){
                        $fini = $par->fecha_inicio;
                    }
                }
                array_push($lista, $par);
            } 
        }
        $total = DB::table('finanzas.presup_totales')
        ->select('presup_totales.*','sis_moneda.simbolo')
        ->join('finanzas.presup','presup.id_presup','=','presup_totales.id_presup')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','presup.moneda')
        ->where('presup_totales.id_presup',$id_propuesta)->first();

        return response()->json([ 'lista'=>$lista, 'fecha_inicio'=>$fini, 'fecha_fin'=>$ffin, 
        'total'=>$total->sub_total, 'moneda'=>$total->simbolo ]);
    }

    public function listar_cronoval_propuesta($id_propuesta)
    {
        $partidas = DB::table('proyectos.presup_par_crono')
            ->select('presup_par_crono.*','presup.fecha_emision',
            'presup_par.cod_padre','presup_par.codigo','presup_par.descripcion',
            'presup_par.metrado','presup_par.importe_total','alm_und_medida.abreviatura')
            ->leftjoin('finanzas.presup_par','presup_par.id_partida','=','presup_par_crono.id_partida')
            ->leftjoin('finanzas.presup','presup.id_presup','=','presup_par.id_presup')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','presup_par.unidad_medida')
            ->where([['presup_par_crono.id_presup', '=', $id_propuesta],
                     ['presup_par_crono.estado', '=', 1]])
            ->orderBy('presup_par_crono.nro_orden')
            ->get();
            
        $titulos = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id_propuesta],
                     ['presup_titu.estado', '!=', 7]])
            ->orderBy('presup_titu.codigo')
            ->get();
    
        $lista = [];
        $list_par = [];
        $fini = null;
        $ffin = null;
        
        foreach($titulos as $ti){
            foreach($partidas as $par){
                if ($ti->codigo == $par->cod_padre){
                    if ($ffin == null){
                        $ffin = $par->fecha_fin;
                    } else {
                        if ($ffin < $par->fecha_fin){
                            $ffin = $par->fecha_fin;
                        }
                    }
                    if ($fini == null){
                        $fini = $par->fecha_inicio;
                    } else {
                        if ($fini > $par->fecha_inicio){
                            $fini = $par->fecha_inicio;
                        }
                    }
                    $periodos = DB::table('proyectos.presup_par_cronoval')
                    ->where([['id_partida','=',$par->id_partida],['estado','=',1]])
                    ->get();

                    $nuevo_par = [
                        'id_pcrono' => $par->id_pcrono,
                        'id_partida' => $par->id_partida,
                        'codigo' => $par->codigo,
                        'descripcion' => $par->descripcion,
                        'dias' => $par->dias,
                        'importe_total' => $par->importe_total,
                        'periodos' => (isset($periodos) ? $periodos : [])
                    ];

                    array_push($list_par, $nuevo_par);
                }
            }
            $nuevo_comp = [
                'id_titulo' => $ti->id_titulo,
                'codigo' => $ti->codigo,
                'descripcion' => $ti->descripcion,
                'cod_padre' => $ti->cod_padre,
                'partidas' => $list_par
            ];
            array_push($lista, $nuevo_comp);
            $list_par = [];
        }
    
        foreach($partidas as $par){
            if ($par->cod_padre == null){
                if ($ffin == null){
                    $ffin = $par->fecha_fin;
                } else {
                    if ($ffin < $par->fecha_fin){
                        $ffin = $par->fecha_fin;
                    }
                }
                if ($fini == null){
                    $fini = $par->fecha_inicio;
                } else {
                    if ($fini > $par->fecha_inicio){
                        $fini = $par->fecha_inicio;
                    }
                }
                $periodos = DB::table('proyectos.presup_par_cronoval')
                ->where([['id_partida','=',$par->id_partida],['estado','=',1]])
                ->get();

                $nuevo_par = [
                    'id_pcrono' => $par->id_pcrono,
                    'id_partida' => $par->id_partida,
                    'codigo' => $par->codigo,
                    'descripcion' => $par->descripcion,
                    'dias' => $par->dias,
                    'importe_total' => $par->importe_total,
                    'periodos' => (isset($periodos) ? $periodos : [])
                ];
                array_push($lista, $nuevo_par);
            } 
        }
        $total = DB::table('finanzas.presup_totales')
        ->select('presup_totales.*','presup.cantidad_cronoval','presup.unid_program_cronoval','sis_moneda.simbolo')
        ->join('finanzas.presup','presup.id_presup','=','presup_totales.id_presup')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','presup.moneda')
        ->where('presup_totales.id_presup',$id_propuesta)->first();

        return response()->json([ 'lista'=>$lista, 'fecha_inicio'=>$fini, 'fecha_fin'=>$ffin, 
        'total'=>$total->sub_total, 'moneda'=>$total->simbolo, 'cantidad'=>$total->cantidad_cronoval, 
        'unid_program'=>$total->unid_program_cronoval ]);
    }

    public function guardar_cronoval_propuesta(Request $request){
        $ids = explode(',',$request->id_pcronoval);
        $par = explode(',',$request->id_partida);
        $per = explode(',',$request->periodo);
        $por = explode(',',$request->porcentaje);
        $imp = explode(',',$request->importe);
        $count = count($ids);
        $data = 0;

        for ($i=0; $i<$count; $i++){
            $id = $ids[$i];
            $pa = $par[$i];
            $pe = $per[$i];
            $po = $por[$i];
            $im = $imp[$i];

            if ($request->modo === 'new'){
                $data = DB::table('proyectos.presup_par_cronoval')
                ->insert([
                    'id_partida'=>$pa,
                    'id_presup'=>$request->id_presupuesto,
                    'periodo'=>$pe,
                    'porcentaje'=>$po,
                    'importe'=>$im,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1
                ]);
            } 
            else {
                $data = DB::table('proyectos.presup_par_cronoval')
                ->where('id_pcronoval',$id)
                ->update([
                    'periodo'=>$pe,
                    'porcentaje'=>$po,
                    'importe'=>$im,
                ]);
            }
        }

        if ($request->modo === 'new'){
            $nro = explode(',',$request->pnro);
            $ndias = explode(',',$request->pnro_dias);
            $dias = explode(',',$request->pdias);
            $ini = explode(',',$request->pfini);
            $fin = explode(',',$request->pffin);
            $tot = explode(',',$request->ptotal);
            $cnt = count($nro);
    
            for ($j=0; $j<$cnt; $j++){
                $nr = $nro[$j];
                $nd = $ndias[$j];
                $di = $dias[$j];
                $in = $ini[$j];
                $fi = $fin[$j];
                $to = $tot[$j];

                DB::table('proyectos.presup_periodos')
                ->insert([
                    'id_presup'=>$request->id_presupuesto,
                    'numero'=>$nr,
                    'nro_dias'=>$nd,
                    'dias_acum'=>$di,
                    'fecha_inicio'=>$in,
                    'fecha_fin'=>$fi,
                    'total'=>$to,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1
                ]);
            }
        }

        DB::table('finanzas.presup')
        ->where('id_presup',$request->id_presupuesto)
        ->update([ 'cronoval'=>true,
                   'cantidad_cronoval'=>$request->cantidad,
                   'unid_program_cronoval'=>$request->unid_program ]);

        return response()->json($data);
    }   

    public function listar_cd($id)
    {
        $data = $this->cd($id);
        return response()->json(['data'=>$data['array'],'total'=>$data['total']]);
    }

    public function cd($id)
    {
        $cd_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.id_insumo','proy_insumo.tp_insumo','proy_insumo.codigo',
        'proy_insumo.descripcion','alm_und_medida.abreviatura','proy_insumo.id_categoria',
        DB::raw('SUM(proy_cd_partida.cantidad * proy_cu_detalle.cantidad) as cantidad'),
        'proy_cu_detalle.precio_unit as precio_unitario',
        DB::raw('SUM(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as importe_parcial'), 
        DB::raw('count(proy_cu_detalle.precio_unit) as count_precio'))
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_presup.id_presupuesto')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->groupBy('proy_insumo.id_insumo','proy_insumo.tp_insumo','proy_insumo.codigo',
            'proy_insumo.descripcion','alm_und_medida.abreviatura','proy_insumo.id_categoria',
            'proy_cu_detalle.precio_unit')
            ->where([['proy_presup.id_presupuesto','=',$id],
                    ['proy_cd_partida.estado','=',1],
                    ['proy_cu_detalle.estado','=',1]])
            ->get();

        $insumos_aproximados = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.id_insumo','proy_insumo.tp_insumo','proy_insumo.codigo',
        'proy_insumo.descripcion','alm_und_medida.abreviatura','proy_insumo.id_categoria',
        // DB::raw('SUM(proy_cd_partida.cantidad * proy_cu_detalle.cantidad) as cantidad'),
        // 'proy_cu_detalle.precio_unit as precio_unitario',
        DB::raw('SUM(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as importe_parcial'), 
        DB::raw('count(proy_cu_detalle.precio_unit) as count_precio'))
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_presup.id_presupuesto')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->groupBy('proy_insumo.id_insumo','proy_insumo.tp_insumo','proy_insumo.codigo',
            'proy_insumo.descripcion','alm_und_medida.abreviatura','proy_insumo.id_categoria')
            ->where([['proy_presup.id_presupuesto','=',$id],
                    ['proy_cd_partida.estado','=',1],
                    ['proy_cu_detalle.estado','=',1],
                    ['proy_insumo.id_categoria','=',1]])//categoria=aproximados
            ->get();
    
        $tipos = DB::table('proyectos.proy_tp_insumo')
        ->select('proy_tp_insumo.id_tp_insumo','proy_tp_insumo.codigo','proy_tp_insumo.descripcion')
        ->where('estado',1)
            ->get();
            
        $sum = 0;
        $array = [];
        $total = 0;

        foreach($tipos as $tipo){
            $insumos_tipo = [];
            foreach ($cd_insumos as $row){
                if ($tipo->id_tp_insumo == $row->tp_insumo && $row->id_categoria !== 1){//categoria=aproximados
                    $sum += $row->importe_parcial;
                    $insumos_tipo[] = $row;
                }
            }
            foreach ($insumos_aproximados as $row){
                if ($tipo->id_tp_insumo == $row->tp_insumo){
                    $sum += $row->importe_parcial;
                    $insumos_tipo[] = $row;
                }
            }
            if ($sum > 0){
                $nuevo = array( 'id_tp_insumo'=>$tipo->id_tp_insumo, 
                                'codigo'=>$tipo->codigo,
                                'descripcion'=>$tipo->descripcion,
                                'suma'=>round($sum,6,PHP_ROUND_HALF_UP),
                                'insumos'=>$insumos_tipo);
                $array[] = $nuevo;
                $total +=$sum;
                $sum = 0;
            }
        }
        return ['array'=>$array,'total'=>$total];
    }
    //actualiza unitario de la partida
    public function update_unitario_partida_cd(Request $request)
    {
        $cd_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_cu_detalle.*')
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_presup.id_presupuesto')
        ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu_partida','=','proy_cu_partida.id_cu_partida')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_presup.id_presupuesto','=',$request->id_presupuesto],
                    ['proy_cd_partida.estado','=',1],
                    ['proy_cu_detalle.id_insumo','=',$request->id_insumo],
                    ['proy_cu_detalle.estado','=',1]])
            ->get();

        $lista_cu_partida = [];
        $data = 0;

        foreach($cd_insumos as $ins)
        {
            $total = $ins->cantidad * $request->unitario;
            $data = DB::table('proyectos.proy_cu_detalle')
            ->where('proy_cu_detalle.id_cu_detalle',$ins->id_cu_detalle)
            ->update([
                'precio_unit'=>$request->unitario,
                'precio_total'=>$total,
            ]);
            if (!in_array($ins->id_cu_partida, $lista_cu_partida)){
                array_push($lista_cu_partida, $ins->id_cu_partida);
            }
        }

        $tp_mo = 1;//Tipo de Insumo = Mano de Obra
        $cat_aproximados = 1;//Categoria = Aproximados

        for ($i=0; $i<count($lista_cu_partida); $i++)
        {
            //suma totales de mano de obra
            $mo = DB::table('proyectos.proy_cu_detalle')
            ->select(DB::raw('sum(proy_cu_detalle.precio_total) as suma_mo'))
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->where([['proy_cu_detalle.id_cu_partida','=',$lista_cu_partida[$i]],
                    ['proy_insumo.tp_insumo','=',$tp_mo],
                    ['proy_cu_detalle.estado','!=',7]])
            ->first();

            //detalles tipo mo
            $dets = DB::table('proyectos.proy_cu_detalle')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_insumo_cat','proy_insumo_cat.id_categoria','=','proy_insumo.id_categoria')
            ->where([['proy_cu_detalle.id_cu_partida','=',$lista_cu_partida[$i]],
                    ['proy_insumo_cat.id_categoria','=',$cat_aproximados],
                    ['proy_cu_detalle.estado','!=',7]])
            ->get();

            foreach($dets as $d)
            {
                //actualiza precios de insumos de mo
                $total = ($d->cantidad * $mo->suma_mo)/100;
                DB::table('proyectos.proy_cu_detalle')
                ->where('id_cu_detalle',$d->id_cu_detalle)
                ->update(['precio_unit'=>$mo->suma_mo,
                          'precio_total'=>$total
                ]);
            }

            //suma total del acu
            $cu = DB::table('proyectos.proy_cu_detalle')
            ->select(DB::raw('sum(proy_cu_detalle.precio_total) as suma_total'))
            ->where([['proy_cu_detalle.id_cu_partida','=',$lista_cu_partida[$i]],
                    ['proy_cu_detalle.estado','!=',7]])
            ->first();

            $data = DB::table('proyectos.proy_cu_partida')
            ->where('id_cu_partida',$lista_cu_partida[$i])
            ->update(['total'=>$cu->suma_total]);

            $partida = DB::table('proyectos.proy_cd_partida')
            ->where([['id_cu_partida','=',$lista_cu_partida[$i]],
                    ['id_cd','=',$request->id_presupuesto],
                    ['estado','!=',7]])
                    ->first();

            if (isset($partida)){
                $parcial = $cu->suma_total * $partida->cantidad;

                DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$partida->id_partida)
                ->update(['importe_unitario'=>$cu->suma_total,
                          'importe_parcial'=> $parcial
                ]);
            }
        }
        $this->actualiza_totales($request->id_presupuesto);

        $totales = DB::table('proyectos.proy_presup_importe')
        ->where('id_presupuesto',$request->id_presupuesto)
        ->first();

        return response()->json(['data'=>$data,'totales'=>$totales]);
    }

    public function partida_insumos_precio($id,$id_insumo)
    {
        $cd_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_cu_detalle.cantidad as cantidad_cu','proy_cu_detalle.precio_unit',
        'proy_cu.codigo','proy_cu.descripcion','alm_und_medida.abreviatura',
        'proy_cd_partida.cantidad as cantidad_partida','proy_insumo.id_insumo',
        DB::raw('(proy_cu_detalle.cantidad * proy_cd_partida.cantidad) as cantidad'),
        DB::raw('(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as importe_parcial'))
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_presup.id_presupuesto')
        ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu_partida','=','proy_cu_partida.id_cu_partida')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_presup.id_presupuesto','=',$id],
                    ['proy_cd_partida.estado','=',1],
                    ['proy_cu_detalle.id_insumo','=',$id_insumo],
                    ['proy_cu_detalle.estado','=',1]])
            ->get();

        $insumo = DB::table('proyectos.proy_insumo')
        ->where('id_insumo',$id_insumo)->first();
        return response()->json(['cd_insumos'=>$cd_insumos,'descripcion_insumo'=>$insumo->codigo.' - '.$insumo->descripcion]);
    }

    public function solo_cd($id_pres){
        $data = $this->cd($id_pres);
        return $data['array'];
    }

    public function listar_ci($id)
    {
        $part_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_ci_detalle.*','alm_und_medida.abreviatura',
            'proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
            ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_ci_detalle.id_cu_partida')
            ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_ci_detalle.id_ci', '=', $id],
                     ['proy_ci_detalle.estado', '=', 1]])
            ->orderBy('proy_ci_detalle.codigo')
            ->get()
            ->toArray();
            
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
            ->where([['proy_ci_compo.id_ci', '=', $id],
                     ['proy_ci_compo.estado', '=', 1]])
            ->orderBy('proy_ci_compo.codigo')
            ->get();
    
        $componentes_ci = [];
        $array = [];
        $html = '';
        $tipo = "'ci'";

        foreach ($compo_ci as $comp){
            $total = 0;
            $codigo = "'".$comp->codigo."'";
            $desc = "'".$comp->codigo." - ".$comp->descripcion."'";
            $html .= '
            <tr id="comci-'.$comp->id_ci_compo.'">
                <td></td>
                <td>'.$comp->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$comp->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right">'.number_format($comp->total_comp,2,".",",").'</td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Componente" onClick="agregar_compo_ci('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agrega_partida_ci('.$codigo.','.$desc.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Componente" onClick="editar_compo_ci('.$comp->id_ci_compo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Componente" onClick="update_compo_ci('.$comp->id_ci_compo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Componente" onClick="anular_compo_ci('.$comp->id_ci_compo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$comp->cod_padre.'</td>
            </tr>';

            foreach($part_ci as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $total += $partida->importe_parcial;
                    $html .= '
                    <tr id="parci-'.$partida->id_ci_detalle.'">
                        <td>
                            <i class="fas fa-arrow-alt-circle-down" data-toggle="tooltip" data-placement="bottom" title="Bajar Partida" onClick="bajar_partida_ci('.$partida->id_ci_detalle.');"></i>
                            <i class="fas fa-arrow-alt-circle-up" data-toggle="tooltip" data-placement="bottom" title="Subir Partida" onClick="subir_partida_ci('.$partida->id_ci_detalle.');"></i>
                        </td>
                        <td id="cu-'.(isset($partida->id_cu_partida) ? $partida->id_cu_partida : '').'">'.$partida->codigo.'</td>
                        <td id="ccu-'.(isset($partida->cod_acu) ? $partida->cod_acu : '').'">'.$partida->descripcion.'</td>
                        <td id="abr-'.(isset($partida->unid_medida) ? $partida->unid_medida : '').'">'.($partida->abreviatura !== null ? $partida->abreviatura : '').'</td>
                        <td class="right">'.$partida->cantidad.'</td>
                        <td class="right">'.number_format($partida->importe_unitario,2,".",",").'</td>
                        <td class="right">'.(isset($partida->participacion) ? number_format($partida->participacion,2,".",",") : '').'</td>
                        <td class="right">'.(isset($partida->tiempo) ? number_format($partida->tiempo,2,".",",") : '').'</td>
                        <td class="right">'.(isset($partida->veces) ? number_format($partida->veces,2,".",",") : '').'</td>
                        <td class="right">'.number_format($partida->importe_parcial,2,".",",").'</td>
                        <td></td>
                        <td style="display:flex;">
                            <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida_ci('.$partida->id_ci_detalle.');"></i>
                            <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida_ci('.$partida->id_ci_detalle.');"></i>
                            <i class="fas fa-file-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Lecciones Aprendidas" onClick="open_presLeccion('."'".$tipo."'".','.$partida->id_ci_detalle.');"></i>
                        </td>
                        <td hidden>'.$partida->cod_compo.'</td>
                    </tr>';
                }
            }
        }
        return json_encode($html);
    }
    public function listar_gg($id)
    {
        // $ci = DB::table('proyectos.proy_presup')
        //     ->select('proy_gg.id_gg')
        //     ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
        //     ->where([['proy_presup.id_presupuesto', '=', $id]])
        //     ->first();

        $part_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_gg_detalle.*',
            'alm_und_medida.abreviatura','proy_cu.rendimiento','proy_cu.codigo as cod_acu')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
            ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_gg_detalle.id_cu_partida')
            ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_gg_detalle.id_gg', '=', $id],
                    ['proy_gg_detalle.estado', '=', 1]])
            ->orderBy('proy_gg_detalle.codigo')
            ->get()
            ->toArray();
            
        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
            ->where([['proy_gg_compo.id_gg', '=', $id],
                    ['proy_gg_compo.estado', '=', 1]])
            ->orderBy('proy_gg_compo.codigo')
            ->get();
    
        $componentes_gg = [];
        $array = [];
        $html = '';
        $tipo = "'gg'";

        foreach ($compo_gg as $comp){
            $total = 0;
            $codigo = "'".$comp->codigo."'";
            $desc = "'".$comp->codigo." - ".$comp->descripcion."'";
            $html .= '
            <tr id="comgg-'.$comp->id_gg_compo.'">
                <td></td>
                <td>'.$comp->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$comp->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right">'.number_format($comp->total_comp,2,".",",").'</td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Componente" onClick="agregar_compo_gg('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agrega_partida_gg('.$codigo.','.$desc.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Componente" onClick="editar_compo_gg('.$comp->id_gg_compo.');"></i>
                    <i class="fas fa-save icon-tabla green boton oculto" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Componente" onClick="update_compo_gg('.$comp->id_gg_compo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Componente" onClick="anular_compo_gg('.$comp->id_gg_compo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$comp->cod_padre.'</td>
            </tr>';
            foreach($part_gg as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $total += $partida->importe_parcial;
                    $html .= '
                    <tr id="pargg-'.$partida->id_gg_detalle.'">
                        <td>
                            <i class="fas fa-arrow-alt-circle-down" data-toggle="tooltip" data-placement="bottom" title="Bajar Partida" onClick="bajar_partida_gg('.$partida->id_gg_detalle.');"></i>
                            <i class="fas fa-arrow-alt-circle-up" data-toggle="tooltip" data-placement="bottom" title="Subir Partida" onClick="subir_partida_gg('.$partida->id_gg_detalle.');"></i>
                        </td>
                        <td id="cu-'.(isset($partida->id_cu_partida) ? $partida->id_cu_partida : '').'">'.$partida->codigo.'</td>
                        <td id="ccu-'.(isset($partida->cod_acu) ? $partida->cod_acu : '').'">'.$partida->descripcion.'</td>
                        <td id="abr-'.(isset($partida->unid_medida) ? $partida->unid_medida : '').'">'.$partida->abreviatura.'</td>
                        <td class="right">'.$partida->cantidad.'</td>
                        <td class="right">'.number_format($partida->importe_unitario,2,".",",").'</td>
                        <td class="right">'.(isset($partida->participacion) ? number_format($partida->participacion,2,".",",") : '').'</td>
                        <td class="right">'.(isset($partida->tiempo) ? number_format($partida->tiempo,2,".",",") : '').'</td>
                        <td class="right">'.(isset($partida->veces) ? number_format($partida->veces,2,".",",") : '').'</td>
                        <td class="right">'.number_format($partida->importe_parcial,2,".",",").'</td>
                        <td></td>
                        <td style="display:flex;">
                        <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida_gg('.$partida->id_gg_detalle.');"></i>
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida_gg('.$partida->id_gg_detalle.');"></i>
                        <i class="fas fa-file-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Lecciones Aprendidas" onClick="open_presLeccion('."'".$tipo."'".','.$partida->id_gg_detalle.');"></i>
                        </td>
                        <td hidden>'.$partida->cod_compo.'</td>
                    </tr>';
                }
            }
        }
        return json_encode($html);
    }

    public function guardar_componente_cd(Request $request)
    {
        $data = DB::table('proyectos.proy_cd_compo')
            ->insertGetId([
                'id_cd' => $request->id_pres,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'cod_padre' => $request->cod_compo,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_cd_compo'
            );
        return response()->json($data);
    }

    public function guardar_componente_ci(Request $request)
    {
        $data = DB::table('proyectos.proy_ci_compo')
            ->insertGetId([
                'id_ci' => $request->id_pres,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'cod_padre' => $request->cod_compo,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_ci_compo'
            );
        return response()->json($data);
    }
    public function guardar_componente_gg(Request $request)
    {
        $data = DB::table('proyectos.proy_gg_compo')
            ->insertGetId([
                'id_gg' => $request->id_pres,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'cod_padre' => $request->cod_compo,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_gg_compo'
            );
        return response()->json($data);
    }
    public function update_componente_cd(Request $request){
        
        $data = DB::table('proyectos.proy_cd_compo')
            ->where('id_cd_compo', $request->id_cd_compo)
            ->update(['descripcion' => strtoupper($request->descripcion)]);

        return response()->json($data);
    }
    public function update_componente_ci(Request $request){
        
        $data = DB::table('proyectos.proy_ci_compo')
            ->where('id_ci_compo', $request->id_ci_compo)
            ->update(['descripcion' => strtoupper($request->descripcion)]);

        return response()->json($data);
    }
    public function update_componente_gg(Request $request){
        
        $data = DB::table('proyectos.proy_gg_compo')
            ->where('id_gg_compo', $request->id_gg_compo)
            ->update(['descripcion' => strtoupper($request->descripcion)]);

        return response()->json($data);
    }
    public function anular_compo_cd(Request $request){

        $data = DB::table('proyectos.proy_cd_compo')
            ->where('proy_cd_compo.id_cd_compo', $request->id_cd_compo)
            ->update(['estado' => 7]);

        $hijos_com = explode(',',$request->hijos_com);
        $count1 = count($hijos_com);

        if (!empty($request->hijos_com)){
            for ($i=0; $i<$count1; $i++){
                DB::table('proyectos.proy_cd_compo')
                ->where('proy_cd_compo.id_cd_compo', $hijos_com[$i])
                ->update(['estado' => 7]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par)){
            for ($i=0; $i<$count2; $i++){
                DB::table('proyectos.proy_cd_partida')
                ->where('proy_cd_partida.id_partida', $hijos_par[$i])
                ->update(['estado' => 7]);
            }
        }

        $this->suma_partidas_cd($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_compo_ci(Request $request){

        $data = DB::table('proyectos.proy_ci_compo')
            ->where('proy_ci_compo.id_ci_compo', $request->id_ci_compo)
            ->update(['estado' => 7]);

        $hijos_com = explode(',',$request->hijos_com);
        $count1 = count($hijos_com);

        if (!empty($request->hijos_com)){
            for ($i=0; $i<$count1; $i++){
                DB::table('proyectos.proy_ci_compo')
                ->where('proy_ci_compo.id_ci_compo', $hijos_com[$i])
                ->update(['estado' => 7]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par)){
            for ($i=0; $i<$count2; $i++){
                DB::table('proyectos.proy_ci_detalle')
                ->where('proy_ci_detalle.id_ci_detalle', $hijos_par[$i])
                ->update(['estado' => 7]);
            }
        }

        $this->suma_partidas_ci($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_compo_gg(Request $request){

        $data = DB::table('proyectos.proy_gg_compo')
            ->where('proy_gg_compo.id_gg_compo', $request->id_gg_compo)
            ->update(['estado' => 7]);

        $hijos_com = explode(',',$request->hijos_com);
        $count1 = count($hijos_com);

        if (!empty($request->hijos_com)){
            for ($i=0; $i<$count1; $i++){
                DB::table('proyectos.proy_gg_compo')
                ->where('proy_gg_compo.id_gg_compo', $hijos_com[$i])
                ->update(['estado' => 7]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par)){
            for ($i=0; $i<$count2; $i++){
                DB::table('proyectos.proy_gg_detalle')
                ->where('proy_gg_detalle.id_gg_detalle', $hijos_par[$i])
                ->update(['estado' => 7]);
            }
        }

        $this->suma_partidas_gg($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }

    public function guardar_partida_cd(Request $request)
    {
        $rspta = DB::table('proyectos.proy_cd_partida')
            ->insertGetId([
                'id_cd' => $request->id_cd,
                'id_cu_partida' => $request->id_cu_partida,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'id_sistema' => $request->sis,
                'cod_compo' => $request->comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_partida'
            );
        $this->suma_partidas_cd($request->comp, $request->id_cd);
        return response()->json($rspta);
    }
    public function guardar_partida_ci(Request $request)
    {
        $data = DB::table('proyectos.proy_ci_detalle')
            ->insertGetId([
                'id_ci' => $request->id_ci,
                'id_cu_partida' => $request->id_cu_partida,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'unid_medida' => ($request->unid_medida !== null ? $request->unid_medida : 0),
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_ci_detalle'
            );
        $this->suma_partidas_ci($request->comp, $request->id_ci);

        return response()->json($data);
    }
    public function guardar_partida_gg(Request $request)
    {
        $data = DB::table('proyectos.proy_gg_detalle')
            ->insertGetId([
                'id_gg' => $request->id_gg,
                'id_cu_partida' => $request->id_cu_partida,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_gg_detalle'
            );
        $this->suma_partidas_gg($request->comp, $request->id_gg);

        return response()->json($data);
    }
    public function update_partida_cd(Request $request){

        $data = DB::table('proyectos.proy_cd_partida')
            ->where('id_partida', $request->id_partida)
            ->update([
                // 'id_cu' => $request->id_cu,
                // 'descripcion' => $request->descripcion,
                // 'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->importe_unitario,
                'importe_parcial' => $request->importe_parcial,
                'id_sistema' => $request->id_sistema
                ]);
        $this->suma_partidas_cd($request->comp, $request->id_cd);
        return response()->json($data);
    }
    public function update_partida_ci(Request $request){

        $data = DB::table('proyectos.proy_ci_detalle')
            ->where('id_ci_detalle', $request->id_ci_detalle)
            ->update([
                // 'id_cu_partida' => $request->id_cu_partida,
                'descripcion' => strtoupper($request->descripcion),
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp
                ]);

        $this->suma_partidas_ci($request->comp, $request->id_ci);

        return response()->json($data);
    }
    public function update_partida_gg(Request $request){

        $data = DB::table('proyectos.proy_gg_detalle')
            ->where('id_gg_detalle', $request->id_gg_detalle)
            ->update([
                // 'id_cu_detalle' => $request->id_cu_detalle,
                'descripcion' => strtoupper($request->descripcion),
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp
                ]);

        $this->suma_partidas_gg($request->comp, $request->id_gg);

        return response()->json($data);
    }
    public function anular_partida_cd(Request $request){

        $data = DB::table('proyectos.proy_cd_partida')
            ->where('proy_cd_partida.id_partida', $request->id_partida)
            ->update(['estado' => 7]);

        $this->suma_partidas_cd($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_partida_ci(Request $request){

        $data = DB::table('proyectos.proy_ci_detalle')
            ->where('proy_ci_detalle.id_ci_detalle', $request->id_ci_detalle)
            ->update(['estado' => 7]);

        $this->suma_partidas_ci($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_partida_gg(Request $request){

        $data = DB::table('proyectos.proy_gg_detalle')
            ->where('proy_gg_detalle.id_gg_detalle', $request->id_gg_detalle)
            ->update(['estado' => 7]);

        $this->suma_partidas_gg($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }

    public function actualiza_moneda($id_pres)
    {
        $pres = DB::table('proyectos.proy_presup')
            ->where('id_presupuesto',$id_pres)
            ->first();

        $partidas = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*','proy_cu_partida.total as precio_cu')
            ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->where([['proy_cd_partida.id_cd', '=', $id_pres],
                    ['proy_cd_partida.estado', '=', 1]])
                    ->get();

        $ci_detalle = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_ci_detalle.*')
            ->where([['proy_ci_detalle.id_ci', '=', $id_pres],
                    ['proy_ci_detalle.estado', '=', 1]])
                    ->get();

        $gg_detalle = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_gg_detalle.*')
            ->where([['proy_gg_detalle.id_gg', '=', $id_pres],
                    ['proy_gg_detalle.estado', '=', 1]])
                    ->get();

        if (isset($partidas)){
            foreach($partidas as $p){
                if ($pres->moneda == 1){
                    $unitario = $p->precio_cu;
                } else {
                    $unitario = $p->precio_cu * $pres->tipo_cambio;
                }
                $parcial = $unitario * $p->cantidad;
    
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$p->id_partida)
                ->update(['importe_unitario'=>$unitario,
                        'importe_parcial'=>$parcial]);
            }
        }

        if (isset($ci_detalle)){
            foreach($ci_detalle as $det){
                if ($pres->moneda == 1){
                    $parcial = $det->importe_unitario * $det->cantidad;
                } else {
                    $parcial = $det->importe_unitario * $det->cantidad * $pres->tipo_cambio;
                }
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$det->id_ci_detalle)
                ->update(['importe_parcial'=>$parcial]);

                if (isset($det->cod_compo) && $det->cod_compo !== null){
                    $this->suma_padres_ci($det->cod_compo, $id_pres);
                }
            }
        }

        if (isset($gg_detalle)){
            foreach($gg_detalle as $det){
                if ($pres->moneda == 1){
                    $parcial = $det->importe_unitario * $det->cantidad;
                } else {
                    $parcial = $det->importe_unitario * $det->cantidad * $pres->tipo_cambio;
                }
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$det->id_gg_detalle)
                ->update(['importe_parcial'=>$parcial]);

                if (isset($det->cod_compo) && $det->cod_compo !== null){
                    $this->suma_padres_ci($det->cod_compo, $id_pres);
                }
            }        
        }
        $this->actualiza_totales($id_pres);

        return response()->json($update);
    }
/*
    public function suma_partidas_cd($padre, $id_cd)
    {
        $part = DB::table('proyectos.proy_cd_partida')
            ->select(DB::raw('SUM(proy_cd_partida.importe_parcial) as suma_partidas'))
            ->where([['proy_cd_partida.cod_compo', '=', $padre],
                    ['proy_cd_partida.id_cd', '=', $id_cd],
                    ['proy_cd_partida.estado', '!=', 7]])
            ->first();
        
        $update = DB::table('proyectos.proy_cd_compo')
            ->where([['proy_cd_compo.codigo','=',$padre],
                    ['proy_cd_compo.id_cd', '=', $id_cd]])
            ->update(['total_comp'=>$part->suma_partidas]);

        $this->actualiza_totales($id_cd);

        return response()->json($update);
    }
*/
    public function suma_padres_cd($cod_padre, $id_cd)
    {
        $part = DB::table('proyectos.proy_cd_partida')
        ->select(DB::raw('SUM(proy_cd_partida.importe_parcial) as suma_partidas'))
        ->where([['proy_cd_partida.cod_compo', '=', $cod_padre],
                ['proy_cd_partida.id_cd', '=', $id_cd],
                ['proy_cd_partida.estado', '!=', 7]])
        ->first();

        //Actualiza totales de los padres
        $update = DB::table('proyectos.proy_cd_compo')
            ->where([['proy_cd_compo.codigo','=',$cod_padre],
                    ['proy_cd_compo.id_cd', '=', $id_cd]])
            ->update(['total_comp'=>$part->suma_partidas]);

        //Obtiene el abuelo
        $abuelo = DB::table('proyectos.proy_cd_compo')
            ->select('cod_padre')//<-abuelo
            ->where([['codigo','=',$cod_padre],
                    ['id_cd','=',$id_cd],
                    ['estado','=',1]])
            ->first();

        //copia el padre
        $actualizar_padre = (isset($abuelo) ? $abuelo->cod_padre : null);
        
        while ($actualizar_padre !== null){
            //Suma los totales del abuelo
            $sum = DB::table('proyectos.proy_cd_compo')
            ->select(DB::raw('SUM(proy_cd_compo.total_comp) as suma'))
            ->where([['cod_padre',$actualizar_padre],
                    ['id_cd','=',$id_cd],
                    ['estado','=',1]])
            ->first();

            $data = DB::table('proyectos.proy_cd_compo')
            ->where([['codigo',$actualizar_padre],
                    ['id_cd','=',$id_cd],
                    ['estado','=',1]])
            ->update(['total_comp'=>$sum->suma]);

            //busca bisabuelo
            $bisabuelo = DB::table('proyectos.proy_cd_compo')
            ->select('cod_padre')//<-bisabuelo
            ->where([['codigo','=',$actualizar_padre],
                    ['id_cd','=',$id_cd],
                    ['estado','=',1]])
            ->first();
            //copia el bisabuelo
            $actualizar_padre = (isset($bisabuelo) ? $bisabuelo->cod_padre : null);
        }
    }

    public function suma_partidas_cd($cod_padre, $id_cd)
    {
        $this->suma_padres_cd($cod_padre, $id_cd);
        $update = $this->actualiza_totales($id_cd);
        return $update;
    }

    public function suma_padres_ci($cod_padre, $id_ci)
    {
        $part = DB::table('proyectos.proy_ci_detalle')
        ->select(DB::raw('SUM(proy_ci_detalle.importe_parcial) as suma_partidas'))
        ->where([['proy_ci_detalle.cod_compo', '=', $cod_padre],
                ['proy_ci_detalle.id_ci', '=', $id_ci],
                ['proy_ci_detalle.estado', '!=', 7]])
        ->first();
        //Actualiza totales de los padres
        $update = DB::table('proyectos.proy_ci_compo')
            ->where([['proy_ci_compo.codigo','=',$cod_padre],
                    ['proy_ci_compo.id_ci', '=', $id_ci]])
            ->update(['total_comp'=>$part->suma_partidas]);
        //Obtiene el abuelo
        $abuelo = DB::table('proyectos.proy_ci_compo')
            ->select('cod_padre')//<-abuelo
            ->where([['codigo','=',$cod_padre],
                    ['id_ci','=',$id_ci],
                    ['estado','=',1]])
            ->first();

        //copia el padre
        $actualizar_padre = (isset($abuelo) ? $abuelo->cod_padre : null);

        while ($actualizar_padre !== null){
            //Suma los totales del abuelo
            $sum = DB::table('proyectos.proy_ci_compo')
            ->select(DB::raw('SUM(proy_ci_compo.total_comp) as suma'))
            ->where([['cod_padre',$actualizar_padre],
                    ['id_ci','=',$id_ci],
                    ['estado','=',1]])
            ->first();
    
            $data = DB::table('proyectos.proy_ci_compo')
            ->where([['codigo',$actualizar_padre],
                    ['id_ci','=',$id_ci],
                    ['estado','=',1]])
            ->update(['total_comp'=>$sum->suma]);
            
            //busca bisabuelo
            $bisabuelo = DB::table('proyectos.proy_ci_compo')
            ->select('cod_padre')//<-bisabuelo
            ->where([['codigo','=',$actualizar_padre],
                    ['id_ci','=',$id_ci],
                    ['estado','=',1]])
            ->first();
            //copia el bisabuelo
            $actualizar_padre = (isset($bisabuelo) ? $bisabuelo->cod_padre : null);
        }
    }

    public function suma_partidas_ci($cod_padre, $id_ci)
    {
        $this->suma_padres_ci($cod_padre, $id_ci);
        $update = $this->actualiza_totales($id_ci);
        return $update;
    }

    public function suma_padres_gg($padre, $id_gg)
    {
        $part = DB::table('proyectos.proy_gg_detalle')
            ->select(DB::raw('SUM(proy_gg_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_gg_detalle.cod_compo', '=', $padre],
                    ['proy_gg_detalle.id_gg', '=', $id_gg],
                    ['proy_gg_detalle.estado', '!=', 7]])
            ->first();
        
        $update = DB::table('proyectos.proy_gg_compo')
            ->where([['proy_gg_compo.codigo','=',$padre],
                    ['proy_gg_compo.id_gg', '=', $id_gg]])
            ->update(['total_comp'=>$part->suma_partidas]);

        //Obtiene el abuelo
        $abuelo = DB::table('proyectos.proy_gg_compo')
        ->select('cod_padre')//<-abuelo
        ->where([['codigo','=',$padre],
                ['id_gg','=',$id_gg],
                ['estado','=',1]])
        ->first();

        //copia el padre
        $actualizar_padre = (isset($abuelo) ? $abuelo->cod_padre : null);

        while ($actualizar_padre !== null){
            //Suma los totales del abuelo
            $sum = DB::table('proyectos.proy_gg_compo')
            ->select(DB::raw('SUM(proy_gg_compo.total_comp) as suma'))
            ->where([['cod_padre',$actualizar_padre],
                    ['id_gg','=',$id_gg],
                    ['estado','=',1]])
            ->first();
    
            $data = DB::table('proyectos.proy_gg_compo')
            ->where([['codigo',$actualizar_padre],
                    ['id_gg','=',$id_gg],
                    ['estado','=',1]])
            ->update(['total_comp'=>$sum->suma]);
            
            //busca bisabuelo
            $bisabuelo = DB::table('proyectos.proy_gg_compo')
            ->select('cod_padre')//<-bisabuelo
            ->where([['codigo','=',$actualizar_padre],
                    ['id_gg','=',$id_gg],
                    ['estado','=',1]])
            ->first();
            //copia el bisabuelo
            $actualizar_padre = (isset($bisabuelo) ? $bisabuelo->cod_padre : null);
        }
    }

    public function suma_partidas_gg($padre, $id_gg)
    {
        $this->suma_padres_gg($padre, $id_gg);
        $update = $this->actualiza_totales($id_gg);
        return $update;
    }
    public function actualiza_totales($id_pres)
    {
        $part_cd_todo = DB::table('proyectos.proy_cd_partida')
            ->select(DB::raw('SUM(proy_cd_partida.importe_parcial) as suma_partidas'))
            ->where([['proy_cd_partida.id_cd', '=', $id_pres],
                    ['proy_cd_partida.estado', '=', 1]])
            ->first();

        $part_ci_todo = DB::table('proyectos.proy_ci_detalle')
            ->select(DB::raw('SUM(proy_ci_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_ci_detalle.id_ci', '=', $id_pres],
                    ['proy_ci_detalle.estado', '=', 1]])
            ->first();

        $part_gg_todo = DB::table('proyectos.proy_gg_detalle')
            ->select(DB::raw('SUM(proy_gg_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_gg_detalle.id_gg', '=', $id_pres],
                    ['proy_gg_detalle.estado', '=', 1]])
            ->first();

        $total_cd = $part_cd_todo->suma_partidas;
        $total_ci = $part_ci_todo->suma_partidas;
        $total_gg = $part_gg_todo->suma_partidas;
            
        $imp = DB::table('proyectos.proy_presup_importe')
            ->where([['id_presupuesto','=',$id_pres]])
            ->first();
                   
        if (isset($imp)){
            if ($total_ci == 0){
                $total_ci = $total_cd * $imp->porcentaje_ci;
            }
            if ($total_gg == 0){
                $total_gg = $total_cd * $imp->porcentaje_gg;
            }

            if ($imp->porcentaje_igv > 0){
                $porcentaje_igv = $imp->porcentaje_igv;
            } 
            else {
                $igv = DB::table('contabilidad.cont_impuesto')
                ->where('codigo','IGV')
                ->orderBy('fecha_inicio','desc')
                ->first();
                $porcentaje_igv = $igv->porcentaje;
            }
        
            $subtotal = $total_cd + $total_ci + $total_gg;
            $total_uti = (($subtotal / (1 - ($imp->porcentaje_utilidad / 100))) - $subtotal);
            $total_igv = ($porcentaje_igv / 100) * ($subtotal + $total_uti);
            $total_pres = $subtotal + $total_uti + $total_igv;

            $pres = DB::table('proyectos.proy_presup_importe')
            ->where([['id_presupuesto','=',$id_pres]])
            ->update([
                'total_costo_directo' => $total_cd, 
                'total_ci' => $total_ci,
                'total_gg' => $total_gg,
                'sub_total' => $subtotal, 
                'total_utilidad' => $total_uti, 
                'porcentaje_igv' => $porcentaje_igv, 
                'total_igv' => $total_igv, 
                'total_presupuestado' => $total_pres,
            ]);
        }
        return response()->json($pres);
    }

    public function totales($id_pres){
        $data = DB::table('proyectos.proy_presup_importe')
        ->select('proy_presup_importe.total_costo_directo',
        'proy_presup_importe.total_ci','proy_presup_importe.porcentaje_ci',
        'proy_presup_importe.total_gg','proy_presup_importe.porcentaje_gg',
        'proy_presup_importe.sub_total','proy_presup_importe.porcentaje_utilidad',
        'proy_presup_importe.total_utilidad','proy_presup_importe.porcentaje_igv',
        'proy_presup_importe.total_igv','proy_presup_importe.total_presupuestado')
        ->where('id_presupuesto',$id_pres)
        ->first();
        return response()->json($data);
    }

    public function guardar_presint(Request $request){
        $op_com = DB::table('proyectos.proy_op_com')
        ->where('id_op_com',$request->id_op_com)
        ->first();
        $msj = '';
        $id_pres = 0;

        if (isset($op_com)){
            $cod = $this->nextPresupuesto(
                $request->id_tp_presupuesto,
                $op_com->id_empresa,
                $request->fecha_emision
            );
            $fecha = date('Y-m-d H:i:s');
            $id_usuario = Auth::user()->id_usuario;

            $version = DB::table('proyectos.proy_presup')
            ->where([['id_tp_presupuesto','=',1],['id_op_com','=',$request->id_op_com],
                    ['estado','!=',7]])->count();

            $id_pres = DB::table('proyectos.proy_presup')->insertGetId(
                [
                    // 'id_proyecto' => $request->id_proyecto,
                    'fecha_emision' => $request->fecha_emision,
                    'moneda' => $request->moneda,
                    'id_tp_presupuesto' => $request->id_tp_presupuesto,
                    'elaborado_por' => $id_usuario,
                    'cronograma' => false,
                    'cronoval' => false,
                    'tipo_cambio' => $request->tipo_cambio,
                    'id_op_com' => $request->id_op_com,
                    'observacion' => $request->observacion,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                    'codigo' => $cod,
                    'id_empresa' => $op_com->id_empresa,
                    'version' => ($version + 1)
                ],
                    'id_presupuesto'
            );
    
            $pres_imp = DB::table('proyectos.proy_presup_importe')->insert(
                [
                    'id_presupuesto' => $id_pres,
                    'total_costo_directo' => 0,
                    'total_ci' => 0,
                    'porcentaje_ci' => 0,
                    'total_gg' => 0,
                    'porcentaje_gg' => 0,
                    'sub_total' => 0,
                    'porcentaje_utilidad' => 0,
                    'total_utilidad' => 0,
                    'porcentaje_igv' => 0,//jalar igv actual
                    'total_igv' => 0,
                    'total_presupuestado' => 0
                ]
            );
            if ($id_pres > 0 && $pres_imp > 0){
                $msj = 'Se guardo exitosamente.';
            }
        } else {
            $msj = 'No existe la Opción Comercial relacionada!.';
        }
        return response()->json(['msj'=>$msj,'id_pres'=>$id_pres]);
    }

    public function update_presint(Request $request){

        $version = DB::table('proyectos.proy_presup')
        ->where([['id_tp_presupuesto','=',1],['id_op_com','=',$request->id_op_com],
                ['estado','!=',7],['id_presupuesto','!=',$request->id_presupuesto]])
                ->count();

        $data = DB::table('proyectos.proy_presup')
            ->where('id_presupuesto',$request->id_presupuesto)
            ->update([
                'fecha_emision' => $request->fecha_emision,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'id_op_com' => $request->id_op_com,
                'observacion' => $request->observacion,
                'version' => ($version + 1)
            ]);
            
        $imp = DB::table('proyectos.proy_presup_importe')
            ->where('id_presupuesto',$request->id_presupuesto)
            ->update([
                    'total_costo_directo' => $request->total_costo_directo,
                    'total_ci' => $request->total_ci,
                    'porcentaje_ci' => $request->porcentaje_ci,
                    'total_gg' => $request->total_gg,
                    'porcentaje_gg' => $request->porcentaje_gg,
                    'sub_total' => $request->sub_total,
                    'porcentaje_utilidad' => $request->porcentaje_utilidad,
                    'total_utilidad' => $request->total_utilidad,
                    'porcentaje_igv' => $request->porcentaje_igv,
                    'total_igv' => $request->total_igv,
                    'total_presupuestado' => $request->total_presupuestado,
                ]
            );
        $msj = ($data !== null ? 'Se actualizó exitosamente.' : '');
        return response()->json(['msj'=>$msj,'id_pres'=>$request->id_presupuesto]);
    }

    public function anular_presint($id){
        $presup = DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$id)
        ->first();
        $msj = '';
        $update = 0;
        $anula = false;

        if ($presup->cronograma == false && $presup->cronoval == false && isset($presup)){
            if ($presup->id_presup !== null){
                $partidas = DB::table('finanzas.presup_par')
                ->where('id_presup',$presup->id_presup)
                ->get();
                $tiene_req = false;
                foreach($partidas as $par){
                    $req = DB::table('almacen.alm_det_req')
                    ->where([['partida','=',$par->id_partida],
                            ['estado','!=',7]])
                    ->count();
                    if ($req > 0){
                        $tiene_req = true;
                        break;
                    }
                }
                if ($tiene_req){
                    $msj = 'No se pudo anular!. El presupuesto esta relacionado con Requerimientos.';
                } else {
                    $anula = true;
                }
            } else {
                $anula = true;
            }

            if ($anula){
                $update = DB::table('proyectos.proy_presup')
                ->where('id_presupuesto',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_cd_compo')
                ->where('id_cd',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_cd_partida')
                ->where('id_cd',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_ci_compo')
                ->where('id_ci',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_gg_compo')
                ->where('id_gg',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg',$id)
                ->update(['estado'=>7]);

                DB::table('finanzas.presup_par')
                ->where('id_presup',$presup->id_presup)
                ->update(['estado'=>7]);

                $msj = 'Se anuló con éxito!';
            }
        }
        return response()->json(['msj'=>$msj,'update'=>$update]);
    }
/*
    public function guardar_preseje(Request $request){
        $proy = DB::table('proyectos.proy_proyecto')
        ->where('id_proyecto',$request->id_proyecto)
        ->first();
        $msj = '';
        $id_pres = 0;

        if (isset($proy)){
            $cod = $this->nextPresupuesto(
                $request->id_tp_presupuesto,
                $proy->empresa,
                $request->fecha_emision
            );
            $fecha = date('Y-m-d H:i:s');
            $id_usuario = Auth::user()->id_usuario;
    
            $id_pres = DB::table('proyectos.proy_presup')->insertGetId(
                [
                    'codigo' => $cod,
                    'id_empresa' => $proy->empresa,
                    'id_proyecto' => $request->id_proyecto,
                    'fecha_emision' => $request->fecha_emision,
                    'moneda' => $request->moneda,
                    'id_tp_presupuesto' => $request->id_tp_presupuesto,
                    'elaborado_por' => $id_usuario,
                    'cronograma' => false,
                    'tipo_cambio' => $request->tipo_cambio,
                    'id_op_com' => $proy->id_op_com,
                    // 'observacion' => $request->observacion,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_presupuesto'
            );
    
            $pres_imp = DB::table('proyectos.proy_presup_importe')->insert(
                [
                    'id_presupuesto' => $id_pres,
                    'total_costo_directo' => 0,
                    'total_ci' => 0,
                    'porcentaje_ci' => 0,
                    'total_gg' => 0,
                    'porcentaje_gg' => 0,
                    'sub_total' => 0,
                    'porcentaje_utilidad' => 0,
                    'total_utilidad' => 0,
                    'porcentaje_igv' => 0,//jalar igv actual
                    'total_igv' => 0,
                    'total_presupuestado' => 0
                ]
            );
            if ($id_pres > 0 && $pres_imp > 0){
                $msj = 'Se guardo exitosamente.';
            }
        } else {
            $msj = 'No existe el Proyecto relacionado!.';
        }
        return response()->json(['msj'=>$msj,'id_pres'=>$id_pres]);
    }
*/
    public function update_preseje(Request $request){
        $proy = DB::table('proyectos.proy_proyecto')
        ->where('id_proyecto',$request->id_proyecto)
        ->first();

        if (isset($proy)){
            $version = DB::table('proyectos.proy_presup')
            ->where([['id_tp_presupuesto','=',2],['id_proyecto','=',$request->id_proyecto],
                    ['estado','!=',7],['id_presupuesto','!=',$request->id_presupuesto]])
                    ->count();
    
            $data = DB::table('proyectos.proy_presup')
                ->where('id_presupuesto',$request->id_presupuesto)
                ->update([
                    'fecha_emision' => $request->fecha_emision,
                    'moneda' => $request->moneda,
                    'tipo_cambio' => $request->tipo_cambio,
                    'id_proyecto' => $request->id_proyecto,
                    'id_empresa' => $proy->empresa,
                    'id_op_com' => $proy->id_op_com,
                    'version' => ($version + 1),
                    // 'observacion' => $request->observacion
                ]);
            $imp = DB::table('proyectos.proy_presup_importe')
                ->where('id_presupuesto',$request->id_presupuesto)
                ->update([
                        'total_costo_directo' => $request->total_costo_directo,
                        'total_ci' => $request->total_ci,
                        'porcentaje_ci' => $request->porcentaje_ci,
                        'total_gg' => $request->total_gg,
                        'porcentaje_gg' => $request->porcentaje_gg,
                        'sub_total' => $request->sub_total,
                        'porcentaje_utilidad' => $request->porcentaje_utilidad,
                        'total_utilidad' => $request->total_utilidad,
                        'porcentaje_igv' => $request->porcentaje_igv,
                        'total_igv' => $request->total_igv,
                        'total_presupuestado' => $request->total_presupuestado,
                    ]
                );
            $msj = ($data !== null ? 'Se actualizó exitosamente.' : '');
        } else {
            $msj = 'No existe el Proyecto relacionado!';
        }
        return response()->json(['msj'=>$msj,'id_pres'=>$request->id_presupuesto]);
    }

    public function generar_preseje($id_proyecto){
        $proy = DB::table('proyectos.proy_proyecto')
        ->where('id_proyecto',$id_proyecto)
        ->first();
        $msj = '';
        $id_pres = 0;
        $tp_pres = 1;// 1 Presupuesto Interno
        $estado = 8;

        if (isset($proy)){
            $fecha_emision = date('Y-m-d');
            $fecha_hora = date('Y-m-d H:i:s');
            $id_usuario = Auth::user()->id_usuario;
            $id_presupuesto = 0;

            $presint = DB::table('proyectos.proy_presup')
                ->select('proy_presup.*','proy_presup_importe.*')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.id_op_com','=',$proy->id_op_com],
                        ['proy_presup.id_tp_presupuesto','=',$tp_pres],// 1 Presupuesto Interno
                        ['proy_presup.estado','=',$estado]])
                ->orderBy('proy_presup.id_presupuesto','desc')
                ->first();

            if (isset($presint)){
                $cod = $this->nextPresupuesto(
                    2,//Presupuesto Ejecucion
                    $presint->id_empresa,
                    $fecha_emision
                );
                $version = DB::table('proyectos.proy_presup')
                ->where([['id_tp_presupuesto','=',2],['id_proyecto','=',$proy->id_proyecto],
                        ['estado','!=',7]])->count();

                $id_presupuesto = DB::table('proyectos.proy_presup')->insertGetId(
                    [
                        'fecha_emision' => $fecha_emision,
                        'moneda' => $presint->moneda,
                        'id_tp_presupuesto' => 2,//Presupuesto Ejecución
                        'elaborado_por' => $id_usuario,
                        'cronograma' => false,
                        'cronoval' => false,
                        'tipo_cambio' => $presint->tipo_cambio,
                        'id_proyecto' => $id_proyecto,
                        'id_op_com' => $proy->id_op_com,
                        'estado' => 1,
                        'fecha_registro' => $fecha_hora,
                        'codigo' => $cod,
                        'id_empresa' => $presint->id_empresa,
                        'version' => ($version + 1)
                    ],
                        'id_presupuesto'
                );
        
                DB::table('proyectos.proy_presup_importe')->insert(
                    [
                        'id_presupuesto' => $id_presupuesto,
                        'total_costo_directo' => $presint->total_costo_directo,
                        'total_ci'  => $presint->total_ci,
                        'porcentaje_ci' => $presint->porcentaje_ci,
                        'total_gg' => $presint->total_gg,
                        'porcentaje_gg' => $presint->porcentaje_gg,
                        'sub_total' => $presint->sub_total,
                        'porcentaje_utilidad' => $presint->porcentaje_utilidad,
                        'total_utilidad' => $presint->total_utilidad,
                        'porcentaje_igv' => $presint->porcentaje_igv,
                        'total_igv' => $presint->total_igv,
                        'total_presupuestado' => $presint->total_presupuestado
                    ]
                );
        
                $presint_cd_com = DB::table('proyectos.proy_cd_compo')
                    ->where([['id_cd','=',$presint->id_presupuesto],
                            ['estado','!=',7]])
                    ->get();
                $presint_ci_com = DB::table('proyectos.proy_ci_compo')
                    ->where([['id_ci','=',$presint->id_presupuesto],
                            ['estado','!=',7]])
                    ->get();
                $presint_gg_com = DB::table('proyectos.proy_gg_compo')
                    ->where([['id_gg','=',$presint->id_presupuesto],
                            ['estado','!=',7]])
                    ->get();
        
                foreach($presint_cd_com as $com)
                {
                    DB::table('proyectos.proy_cd_compo')->insertGetId([
                        'id_cd' => $id_presupuesto,
                        'codigo' => $com->codigo,
                        'descripcion' => $com->descripcion,
                        'cod_padre' => $com->cod_padre,
                        'total_comp' => $com->total_comp,
                        'porcen_utilidad' => $com->porcen_utilidad,
                        'importe_utilidad' => $com->importe_utilidad,
                        'fecha_registro' => $fecha_hora,
                        'estado' => 1
                    ],
                        'id_cd_compo'
                    );
                }
                foreach($presint_ci_com as $com)
                {
                    DB::table('proyectos.proy_ci_compo')->insertGetId([
                        'id_ci' => $id_presupuesto,
                        'codigo' => $com->codigo,
                        'descripcion' => $com->descripcion,
                        'cod_padre' => $com->cod_padre,
                        'total_comp' => $com->total_comp,
                        'porcen_utilidad' => $com->porcen_utilidad,
                        'importe_utilidad' => $com->importe_utilidad,
                        'fecha_registro' => $fecha_hora,
                        'estado' => 1
                    ],
                        'id_ci_compo'
                    );
                }
                foreach($presint_gg_com as $com)
                {
                    DB::table('proyectos.proy_gg_compo')->insertGetId([
                        'id_gg' => $id_presupuesto,
                        'codigo' => $com->codigo,
                        'descripcion' => $com->descripcion,
                        'cod_padre' => $com->cod_padre,
                        'total_comp' => $com->total_comp,
                        'porcen_utilidad' => $com->porcen_utilidad,
                        'importe_utilidad' => $com->importe_utilidad,
                        'fecha_registro' => $fecha_hora,
                        'estado' => 1
                    ],
                        'id_gg_compo'
                    );
                }
                $presint_cd_par = DB::table('proyectos.proy_cd_partida')
                    ->where([['id_cd','=',$presint->id_presupuesto],
                            ['estado','!=',7]])
                    ->get();
                $presint_ci_par = DB::table('proyectos.proy_ci_detalle')
                    ->where([['id_ci','=',$presint->id_presupuesto],
                            ['estado','!=',7]])
                    ->get();
                $presint_gg_par = DB::table('proyectos.proy_gg_detalle')
                    ->where([['id_gg','=',$presint->id_presupuesto],
                            ['estado','!=',7]])
                    ->get();
        
                foreach($presint_cd_par as $par)
                {
                    DB::table('proyectos.proy_cd_partida')->insertGetId([
                            'id_cd' => $id_presupuesto,
                            'id_cu_partida' => $par->id_cu_partida,
                            'codigo' => $par->codigo,
                            'descripcion' => $par->descripcion,
                            'unid_medida' => $par->unid_medida,
                            'cantidad' => $par->cantidad,
                            'importe_unitario' => $par->importe_unitario,
                            'importe_parcial' => $par->importe_parcial,
                            'id_sistema' => $par->id_sistema,
                            'cod_compo' => $par->cod_compo,
                            'fecha_registro' => $fecha_hora,
                            'estado' => 1
                        ],
                            'id_partida'
                        );
                }
                foreach($presint_ci_par as $par)
                {
                    DB::table('proyectos.proy_ci_detalle')->insertGetId([
                        'id_ci' => $id_presupuesto,
                        'id_cu_partida' => $par->id_cu_partida,
                        'codigo' => $par->codigo,
                        'descripcion' => $par->descripcion,
                        'unid_medida' => $par->unid_medida,
                        'cantidad' => $par->cantidad,
                        'importe_unitario' => $par->importe_unitario,
                        'importe_parcial' => $par->importe_parcial,
                        'participacion' => $par->participacion,
                        'tiempo' => $par->tiempo,
                        'veces' => $par->veces,
                        'cod_compo' => $par->cod_compo,
                        'fecha_registro' => $fecha_hora,
                        'estado' => 1
                    ],
                        'id_ci_detalle'
                    );
                }
                foreach($presint_gg_par as $par)
                {
                    DB::table('proyectos.proy_gg_detalle')->insertGetId([
                        'id_gg' => $id_presupuesto,
                        'id_cu_partida' => $par->id_cu_partida,
                        'codigo' => $par->codigo,
                        'descripcion' => $par->descripcion,
                        'unid_medida' => $par->unid_medida,
                        'cantidad' => $par->cantidad,
                        'importe_unitario' => $par->importe_unitario,
                        'importe_parcial' => $par->importe_parcial,
                        'participacion' => $par->participacion,
                        'tiempo' => $par->tiempo,
                        'veces' => $par->veces,
                        'cod_compo' => $par->cod_compo,
                        'fecha_registro' => $fecha_hora,
                        'estado' => 1
                    ],
                        'id_gg_detalle'
                    );
                }
                $propuesta = DB::table('finanzas.presup')
                ->where([['id_presint','=',$presint->id_presupuesto],['estado','!=',7]])
                ->first();

                if (isset($propuesta)){
                    DB::table('finanzas.presup')->where('id_presup',$propuesta->id_presup)
                    ->update(['id_proyecto'=>$id_proyecto]);
                }
            }
        }
        return response()->json($id_presupuesto);
    }

    //Generar un presupuesto en base a otro presupuesto 
    //(ID del presupuesto que voy a copiar, # del tipo de presupuesto que voy a generar, ID del presupuesto actual)
    public function generar_partidas_presupuesto($id_presupuesto, $id_presupuesto_actual)
    {
        $fecha_emision = date('Y-m-d');
        $fecha_hora = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id_usuario;
        // $id_presupuesto = 0;

        $presint_cd_com = DB::table('proyectos.proy_cd_compo')
            ->where([['id_cd','=',$id_presupuesto],
                    ['estado','!=',7]])
                    ->get();
        $presint_ci_com = DB::table('proyectos.proy_ci_compo')
            ->where([['id_ci','=',$id_presupuesto],
                    ['estado','!=',7]])
                    ->get();
        $presint_gg_com = DB::table('proyectos.proy_gg_compo')
            ->where([['id_gg','=',$id_presupuesto],
                    ['estado','!=',7]])
                    ->get();

        if (isset($presint_cd_com)){
            foreach($presint_cd_com as $com)
            {
                DB::table('proyectos.proy_cd_compo')->insertGetId([
                    'id_cd' => $id_presupuesto_actual,
                    'codigo' => $com->codigo,
                    'descripcion' => $com->descripcion,
                    'cod_padre' => $com->cod_padre,
                    'total_comp' => $com->total_comp,
                    'porcen_utilidad' => $com->porcen_utilidad,
                    'importe_utilidad' => $com->importe_utilidad,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_cd_compo'
                );
            }
        }
        if (isset($presint_ci_com)){
            foreach($presint_ci_com as $com)
            {
                DB::table('proyectos.proy_ci_compo')->insertGetId([
                    'id_ci' => $id_presupuesto_actual,
                    'codigo' => $com->codigo,
                    'descripcion' => $com->descripcion,
                    'cod_padre' => $com->cod_padre,
                    'total_comp' => $com->total_comp,
                    'porcen_utilidad' => $com->porcen_utilidad,
                    'importe_utilidad' => $com->importe_utilidad,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_ci_compo'
                );
            }
        }
        if (isset($presint_gg_com)){
            foreach($presint_gg_com as $com)
            {
                DB::table('proyectos.proy_gg_compo')->insertGetId([
                    'id_gg' => $id_presupuesto_actual,
                    'codigo' => $com->codigo,
                    'descripcion' => $com->descripcion,
                    'cod_padre' => $com->cod_padre,
                    'total_comp' => $com->total_comp,
                    'porcen_utilidad' => $com->porcen_utilidad,
                    'importe_utilidad' => $com->importe_utilidad,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_gg_compo'
                );
            }
        }
        $presint_cd_par = DB::table('proyectos.proy_cd_partida')
            ->where([['id_cd','=',$id_presupuesto],
                    ['estado','!=',7]])
            ->get();
        $presint_ci_par = DB::table('proyectos.proy_ci_detalle')
            ->where([['id_ci','=',$id_presupuesto],
                    ['estado','!=',7]])
            ->get();
        $presint_gg_par = DB::table('proyectos.proy_gg_detalle')
            ->where([['id_gg','=',$id_presupuesto],
                    ['estado','!=',7]])
            ->get();

        if (isset($presint_cd_par)){
            foreach($presint_cd_par as $par)
            {
                $cu = DB::table('proyectos.proy_cu_partida')
                ->where('id_cu_partida',$par->id_cu_partida)
                ->first();
                //Crear cu_partida
                $id_cu_partida = DB::table('proyectos.proy_cu_partida')->insertGetId([
                        'id_cu' => $cu->id_cu,
                        'rendimiento' => $cu->rendimiento,
                        'unid_medida' => $cu->unid_medida,
                        'total' => $cu->total,
                        'estado' => 1,
                        'fecha_registro' => $fecha_hora,
                        'usuario_registro' => $id_usuario
                    ],
                        'id_cu_partida'
                );

                $cu_det = DB::table('proyectos.proy_cu_detalle')
                ->where([['id_cu_partida','=',$par->id_cu_partida],['estado','!=',7]])
                ->get();
                //Crea los cu detalles
                foreach($cu_det as $det)
                {
                    DB::table('proyectos.proy_cu_detalle')->insertGetId([
                            'id_cu_partida' => $id_cu_partida,
                            'id_insumo' => $det->id_insumo,
                            'unid_medida' => $det->unid_medida,
                            'cantidad' => $det->cantidad,
                            'cuadrilla' => $det->cuadrilla,
                            'precio_unit' => $det->precio_unit,
                            'precio_total' => $det->precio_total,
                            // 'id_precio' => $det->id_precio,
                            'estado' => 1,
                            'fecha_registro' => $fecha_hora
                        ],
                            'id_cu_detalle'
                    );
                }
                //Crea la partida
                DB::table('proyectos.proy_cd_partida')->insertGetId([
                        'id_cd' => $id_presupuesto_actual,
                        'id_cu_partida' => $id_cu_partida,
                        'codigo' => $par->codigo,
                        'descripcion' => $par->descripcion,
                        'unid_medida' => $par->unid_medida,
                        'cantidad' => $par->cantidad,
                        'importe_unitario' => $par->importe_unitario,
                        'importe_parcial' => $par->importe_parcial,
                        'id_sistema' => $par->id_sistema,
                        'cod_compo' => $par->cod_compo,
                        'fecha_registro' => $fecha_hora,
                        'estado' => 1
                    ],
                        'id_partida'
                    );
            }
        }
        if (isset($presint_ci_par)){
            foreach($presint_ci_par as $par)
            {
                DB::table('proyectos.proy_ci_detalle')->insertGetId([
                    'id_ci' => $id_presupuesto_actual,
                    'id_cu_partida' => $par->id_cu_partida,
                    'codigo' => $par->codigo,
                    'descripcion' => $par->descripcion,
                    'unid_medida' => $par->unid_medida,
                    'cantidad' => $par->cantidad,
                    'importe_unitario' => $par->importe_unitario,
                    'importe_parcial' => $par->importe_parcial,
                    'participacion' => $par->participacion,
                    'tiempo' => $par->tiempo,
                    'veces' => $par->veces,
                    'cod_compo' => $par->cod_compo,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_ci_detalle'
                );
            }
        }
        if (isset($presint_gg_par)){
            foreach($presint_gg_par as $par)
            {
                DB::table('proyectos.proy_gg_detalle')->insertGetId([
                    'id_gg' => $id_presupuesto_actual,
                    'id_cu_partida' => $par->id_cu_partida,
                    'codigo' => $par->codigo,
                    'descripcion' => $par->descripcion,
                    'unid_medida' => $par->unid_medida,
                    'cantidad' => $par->cantidad,
                    'importe_unitario' => $par->importe_unitario,
                    'importe_parcial' => $par->importe_parcial,
                    'participacion' => $par->participacion,
                    'tiempo' => $par->tiempo,
                    'veces' => $par->veces,
                    'cod_compo' => $par->cod_compo,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_gg_detalle'
                );
            }
        }

        $this->actualiza_totales($id_presupuesto_actual);
        
        return response()->json($id_presupuesto_actual);
    }
/*
    public function guardar_adjunto(Request $request)
    {
        $update = false;
        $namefile = "";
        if ($request->id_contrato !== "" && $request->id_contrato !== null){
            $nfile = $request->file('adjunto');
            if (isset($nfile)){
                $namefile = $request->id_contrato.'.'.$nfile->getClientOriginalExtension();
                \File::delete(public_path('proyectos/contratos/'.$namefile));
                Storage::disk('archivos')->put('proyectos/contratos/'.$namefile, \File::get($nfile));
            } else {
                $namefile = null;
            }
            $update = DB::table('proyectos.proy_contrato')
            ->where('id_contrato', $request->id_contrato)
            ->update(['archivo_adjunto' => $namefile]);    
        }

        if ($update){
            $status = 1;
        } else {
            $status = 0;
        }
        $array = array("status"=>$status, "adjunto"=>$namefile);
        return response()->json($array);
    }
    */
    public function listar_obs_cd($id_partida){
        $obs = DB::table('proyectos.proy_obs')
            ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
            ->where([['proy_obs.id_cd_partida','=', $id_partida],
                     ['proy_obs.estado','=',1]])
            ->orderBy('proy_obs.fecha_registro')
            ->get();
        $html = '';
        $i = 1;
        // <td><a href="abrir_adjunto_partida/'.$o->archivo_adjunto.'">'.$o->archivo_adjunto.'</a></td>
        foreach($obs as $o){
            $ruta = '/proyectos/presupuestos/partidas_adjunto/'.$o->archivo_adjunto;
            $file = asset('files').$ruta;
            $html .= '
            <tr id="obs-'.$o->id_obs.'">
                <td>'.$i.'</td>
                <td>'.$o->descripcion.'</td>
                <td>'.$o->nombre_usuario.'</td>
                <td>'.$o->fecha_registro.'</td>
                <td><a href="'.$file.'" target="_blank">'.$o->archivo_adjunto.'</a></td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular" onClick="anular_obs('.$o->id_obs.');"></i>
                </td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }
    public function listar_obs_ci($id_partida){
        $obs = DB::table('proyectos.proy_obs')
            ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
            ->where([['proy_obs.id_ci_detalle','=', $id_partida],
                     ['proy_obs.estado','=',1]])
            ->orderBy('proy_obs.fecha_registro')
            ->get();
        $html = '';
        $i = 1;
        foreach($obs as $o){
            $html .= '
            <tr id="obs-'.$o->id_obs.'">
                <td>'.$i.'</td>
                <td>'.$o->descripcion.'</td>
                <td>'.$o->nombre_usuario.'</td>
                <td>'.$o->fecha_registro.'</td>
                <td><a href="abrir_adjunto_partida/'.$o->archivo_adjunto.'">'.$o->archivo_adjunto.'</a></td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular" onClick="anular_obs('.$o->id_obs.');"></i>
                </td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }
    public function listar_obs_gg($id_partida){
        $obs = DB::table('proyectos.proy_obs')
            ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
            ->where([['proy_obs.id_gg_detalle','=', $id_partida],
                     ['proy_obs.estado','=',1]])
            ->orderBy('proy_obs.fecha_registro')
            ->get();
        $html = '';
        $i = 1;
        foreach($obs as $o){
            $html .= '
            <tr id="obs-'.$o->id_obs.'">
                <td>'.$i.'</td>
                <td>'.$o->descripcion.'</td>
                <td>'.$o->nombre_usuario.'</td>
                <td>'.$o->fecha_registro.'</td>
                <td><a href="abrir_adjunto_partida/'.$o->archivo_adjunto.'">'.$o->archivo_adjunto.'</a></td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular" onClick="anular_obs('.$o->id_obs.');"></i>
                </td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }

    public function guardar_obs_partida(Request $request){
        $id_usuario = Auth::user()->id_usuario;
        $id_obs = DB::table('proyectos.proy_obs')->insertGetId(
                [
                'id_cd_partida'=>$request->id_cd_partida,
                'id_ci_detalle'=>$request->id_ci_detalle,
                'id_gg_detalle'=>$request->id_gg_detalle,
                'descripcion'=>$request->observacion,
                'usuario'=>$id_usuario,
                'estado'=>1,
                'fecha_registro'=>date('Y-m-d H:i:s'),
                ],
                'id_obs'
            );
        //obtenemos el campo file definido en el formulario
        $file = $request->file('adjunto');
        if (isset($file)){
            //obtenemos el nombre del archivo
            // $file = Input::file('upfile')->getClientOriginalName();
            // $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

            $nombre = $id_obs.'.'.$extension;
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \File::delete(public_path('proyectos/presupuestos/partidas_adjunto/'.$nombre));
            \Storage::disk('archivos')->put('proyectos/presupuestos/partidas_adjunto/'.$nombre,\File::get($file));
            
            $update = DB::table('proyectos.proy_obs')
                ->where('id_obs', $id_obs)
                ->update(['archivo_adjunto' => $nombre]); 
        } else {
            $nombre = null;
        }
        return response()->json($id_obs);
    }
    /*
    public function abrir_adjunto_partida($file_name){
        $file_path = public_path('files/proyectos/presupuestos/partidas_adjunto/'.$file_name);
        // $result = File::exists('files/proyectos/contratos/'.$file_name);
        if (file_exists($file_path)){
            return response()->download($file_path);
        } else {
            return response()->json("No existe dicho archivo!");
        }
    }*/
    public function anular_obs_partida($id_obs){
        $data = DB::table('proyectos.proy_obs')->where('id_obs',$id_obs)
            ->update([ 'estado'=> 2 ]);
        return response()->json($data);
    }

//////////////////////////////////////////
/////////Finanzas - Presupuesto

    public function listar_pres_estructura(){
        $data = DB::table('finanzas.presup')
        ->where([['tp_presup','=',1],['estado','!=',7]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_pres_estructura($id_presup){
        $data = DB::table('finanzas.presup')
        ->select('presup.*','sis_sede.id_sede','sis_usua.nombre_corto')
        ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','presup.id_grupo')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','adm_grupo.id_sede')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','presup.responsable')
        ->where('id_presup',$id_presup)
        ->first();
        return response()->json($data);
    }

    public function nextCodigoPresupuesto($id_grupo,$fecha,$tp_presup)
    {
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));//yyyy
        $anio = date('y',strtotime($fecha));//yy
        $result = '';

        $grupo = DB::table('administracion.adm_grupo')
        ->select('descripcion')
        ->where('id_grupo', $id_grupo)
        ->first();

        $data = DB::table('finanzas.presup')
                ->where([['id_grupo','=',$id_grupo],
                        ['estado','=',1]])
                ->whereYear('fecha_emision', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $gru = strtoupper(substr($grupo->descripcion, 0, 2));
        $tp = '';

        if ($tp_presup == 1){
            $tp = 'EB';
        } 
        else if ($tp_presup == 2){
            $tp = 'PI';
        }
        else if ($tp_presup == 3){
            $tp = 'PC';
        }
        else if ($tp_presup == 4){
            $tp = 'PE';
        }
        else if ($tp_presup == 5){
            $tp = 'P'.$gru;
        }
        
        $result = $tp."-".$anio."-".$number;
        return $result;
    }

    public function guardar_pres_estructura(Request $request)
    {
        $id_usuario = Auth::user()->id_usuario;
        $codigo = $this->nextCodigoPresupuesto(5,$request->fecha_emision,1);// 1 Estructura Base

        $data = DB::table('finanzas.presup')
            ->insertGetId([
                'id_empresa' => 1,
                'id_grupo' => 5,
                'fecha_emision' => $request->fecha_emision,
                'codigo' => $codigo,
                'descripcion' => $request->descripcion,
                // 'moneda' => $request->moneda,
                'responsable' => $id_usuario,
                // 'unid_program' => $request->unid_program,
                // 'cantidad' => $request->cantidad,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'tp_presup' => 1,//Presup. Base
            ],
                'id_presup'
            );
        return response()->json($data);
    }

    public function update_pres_estructura(Request $request)
    {
        $data = DB::table('finanzas.presup')->where('id_presup',$request->id_presup)
            ->update([
                // 'id_empresa' => $request->id_empresa,
                // 'id_grupo' => $request->id_grupo,
                'fecha_emision' => $request->fecha_emision,
                'descripcion' => $request->descripcion,
                // 'moneda' => $request->moneda,
                // 'unid_program' => $request->unid_program,
                // 'cantidad' => $request->cantidad,
            ]);
        return response()->json($data);
    }

    public function listar_presupuesto($id)
    {
        $partidas = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup_pardet.descripcion')
            ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->where([['presup_par.id_presup', '=', $id],
                     ['presup_par.estado', '=', 1]])
            ->orderBy('presup_par.codigo')
            ->get()
            ->toArray();
            
        $titulos = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id],
                     ['presup_titu.estado', '=', 1]])
            ->orderBy('presup_titu.codigo')
            ->get();
    
        $nuevos_titulos = [];
        $array = [];
        $html = '';

        foreach ($titulos as $titu){
            $total = 0;
            $codigo = "'".$titu->codigo."'";
            $html .= '
            <tr id="ti-'.$titu->id_titulo.'">
                <td></td>
                <td>'.$titu->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$titu->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Título" onClick="agregar_titulo('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="pardetModal('.$codigo.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Título" onClick="editar_titulo('.$titu->id_titulo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Título" onClick="update_titulo('.$titu->id_titulo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Título" onClick="anular_titulo('.$titu->id_titulo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$titu->cod_padre.'</td>
            </tr>';

            foreach($partidas as $par){
                if ($titu->codigo == $par->cod_padre){
                    $total += $par->importe_total;
                    $html .= '
                    <tr id="par-'.$par->id_partida.'">
                        <td>
                            <i class="fas fa-arrow-alt-circle-down" data-toggle="tooltip" data-placement="bottom" title="Bajar Partida" onClick="bajar_partida_ci('.$par->id_partida.');"></i>
                            <i class="fas fa-arrow-alt-circle-up" data-toggle="tooltip" data-placement="bottom" title="Subir Partida" onClick="subir_partida_ci('.$par->id_partida.');"></i>
                        </td>
                        <td id="pd-'.(isset($par->id_pardet) ? $par->id_pardet : '').'">'.$par->codigo.'</td>
                        <td>'.$par->descripcion.'</td>
                        <td><input type="text" class="input-data" style="width:50px;" name="relacionado" 
                            value="'.$par->relacionado.'" disabled="true"/></td>
                        <td style="display:flex;">
                        <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida('.$par->id_partida.');"></i>
                        <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_partida('.$par->id_partida.');"></i>
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida('.$par->id_partida.');"></i>
                        </td>
                        <td hidden>'.$par->cod_padre.'</td>
                    </tr>';
                }
            }
        }
        return json_encode($html);
    }

    public function guardar_titulo(Request $request)
    {
        $data = DB::table('finanzas.presup_titu')
            ->insertGetId([
                'id_presup' => $request->id_presup,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'cod_padre' => $request->cod_padre,
                'total' => 0,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_titulo'
            );
        return response()->json($data);
    }

    public function update_titulo(Request $request)
    {
        $data = DB::table('finanzas.presup_titu')->where('id_titulo',$request->id_titulo)
            ->update([
                'descripcion' => strtoupper($request->descripcion),
            ]);
        return response()->json($data);
    }

    public function anular_titulo(Request $request){

        $data = DB::table('finanzas.presup_titu')
            ->where('presup_titu.id_titulo', $request->id_titulo)
            ->update(['estado' => 7]);

        $hijos_titu = explode(',',$request->hijos_titu);
        $count1 = count($hijos_titu);

        if (!empty($request->hijos_titu) && $count1 > 0){
            for ($i=0; $i<$count1; $i++){
                DB::table('finanzas.presup_titu')
                ->where('presup_titu.id_titulo', $hijos_titu[$i])
                ->update(['estado' => 7]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par) && $count2 > 0){
            for ($i=0; $i<$count2; $i++){
                DB::table('finanzas.presup_par')
                ->where('presup_par.id_partida', $hijos_par[$i])
                ->update(['estado' => 7]);
            }
        }

        // $this->suma_partidas_ci($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }

    public function listar_par_det(){
        $data = DB::table('finanzas.presup_pardet')
        ->where('estado',1)
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function guardar_partida(Request $request)
    {
        $data = DB::table('finanzas.presup_par')
            ->insertGetId([
                'id_presup' => $request->id_presup,
                'codigo' => $request->codigo,
                'id_pardet' => $request->id_pardet,
                'cod_padre' => $request->cod_padre,
                'relacionado' => '0',
                'importe_base' => 0,
                'importe_total' => 0,
                'porcentaje_utilidad' => 0,
                'importe_utilidad' => 0,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_partida'
            );
        return response()->json($data);
    }

    public function update_partida(Request $request)
    {
        $data = DB::table('finanzas.presup_par')
        ->where('id_partida',$request->id_partida)
        ->update([ 'relacionado' => strtoupper($request->relacionado) ]);
        return response()->json($data);
    }

    public function update_partida_propuesta(Request $request)
    {
        $data = DB::table('finanzas.presup_par')
        ->where('id_partida',$request->id_partida)
        ->update([
            'descripcion' => strtoupper($request->descripcion),
            'metrado' => $request->metrado,
            'importe_unitario' => $request->importe_unitario,
            'unidad_medida' => $request->unidad_medida,
            'importe_total' => $request->importe_total,
            'porcentaje_utilidad' => $request->porcentaje_utilidad,
            'importe_utilidad' => $request->importe_utilidad,
        ]);
        
        $this->actualiza_padres($request->id_presup, $request->cod_padre);
        $this->totales_propuesta($request->id_presup);

        $totales = DB::table('finanzas.presup_totales')
        ->where('id_presup',$request->id_presup)
        ->first();

        return response()->json(['data'=>$data,'totales'=>$totales]);
    }

    public function actualiza_padres($id_presup, $cod_padre){//48,01
        $padre = null;
        //obtiene el padre
        $padre = DB::table('finanzas.presup_titu')
        ->where([['id_presup','=',$id_presup],
                 ['estado','=',1],
                 ['codigo','=',$cod_padre]])
        ->first();
        // array_push($padres,$padre);
        $numero = 0;

        while(isset($padre->id_titulo)){
            $numero++;
            //suma importe de las partidas segun el padre
            $totales = DB::table('finanzas.presup_par')
            ->select(DB::raw('SUM(presup_par.importe_total) as suma_partidas'))
            ->where([['id_presup','=',$id_presup],
                    ['estado','=',1],
                    ['cod_padre','like',$padre->codigo.'%']])
                    ->first();
            //actualiza el total en el padre
            $update = DB::table('finanzas.presup_titu')
            ->where('id_titulo',$padre->id_titulo)
            ->update(['total'=>$totales->suma_partidas]);
            //si existe un padre
            if (isset($padre->cod_padre)){
                //obtiene el abuelo
                $abuelo = DB::table('finanzas.presup_titu')
                ->where([['id_presup','=',$id_presup],
                        ['estado','=',1],
                        ['codigo','=',$padre->cod_padre]])
                ->first();
                //asigan el valor al padre
                $padre = $abuelo;
            } else {
                $padre = null;
            }
        }
        return response()->json(['padre'=>$padre,'numero'=>$numero]);
    }
    public function anular_partida($id_partida){
        $data = DB::table('finanzas.presup_par')
        ->where('id_partida', $id_partida)
        ->update(['estado' => 7]);
        return response()->json($data);
    }

    public function generar_estructura($id_presupuesto, $tipo){

        try {
            DB::beginTransaction();

            $presup = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*','proy_op_com.descripcion',
            'proy_op_com.cantidad','proy_op_com.unid_program')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->where('id_presupuesto',$id_presupuesto)
            ->first();

            $id_grupo = 5;
            $codigo = $this->nextCodigoPresupuesto($id_grupo, $presup->fecha_emision, $tipo);

            //Inserta Nuevo Presupuesto
            $id_presup = DB::table('finanzas.presup')
            ->insertGetId([
                'id_empresa' => $presup->id_empresa,
                'id_grupo' => $id_grupo,//Grupo: Proyectos
                'fecha_emision' => $presup->fecha_emision,
                'codigo' => $codigo,
                'descripcion' => $presup->descripcion,
                'moneda' => $presup->moneda,
                'responsable' => $presup->elaborado_por,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'tp_presup' => $tipo,
            ],
                'id_presup'
            );
        
            $base = DB::table('finanzas.presup')
            ->where([['tp_presup','=',1],['estado','=',1]])
            ->orderBy('fecha_emision','desc')
            ->first();
        
            $titulos = DB::table('finanzas.presup_titu')
            ->where([['id_presup','=',$base->id_presup],['estado','=',1]])
            ->get();

            //Inserta los titulos
            foreach($titulos as $titu){
                $data = DB::table('finanzas.presup_titu')
                ->insertGetId([
                    'id_presup' => $id_presup,
                    'codigo' => $titu->codigo,
                    'descripcion' => strtoupper($titu->descripcion),
                    'cod_padre' => $titu->cod_padre,
                    'total' => 0,
                    'fecha_registro' => date('Y-m-d H:i:s'),
                    'estado' => 1
                ],
                    'id_titulo'
                );
            }

            $partidas = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup_pardet.descripcion')
            ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->where([['presup_par.id_presup','=',$base->id_presup],['presup_par.estado','=',1]])
            ->get();

            // $nuevas_partidas = [];
            $cd = $this->solo_cd($id_presupuesto);
            $ci = DB::table('proyectos.proy_ci_compo')
                ->where([['id_ci', '=', $id_presupuesto],
                        ['estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
            $gg = DB::table('proyectos.proy_gg_compo')
                ->where([['id_gg', '=', $id_presupuesto],
                        ['estado', '=', 1]])
                ->orderBy('codigo')
                ->get();

            foreach($partidas as $par){
                $rel_tipo = substr($par->relacionado, 0, 2);
                $relacionado = substr($par->relacionado, 2, (strlen($par->relacionado)-2));
                $agrega = false;

                if ($rel_tipo == 'CD'){
                    foreach($cd as $c){
                        if ($relacionado === $c["codigo"]){
                            $agrega = true;
                            DB::table('finanzas.presup_par')
                            ->insertGetId([
                                'id_presup' => $id_presup,
                                'codigo' => $par->codigo,
                                'id_pardet' => $par->id_pardet,
                                'cod_padre' => $par->cod_padre,
                                'relacionado' => '',
                                'importe_base' => 0,
                                'importe_total' => $c["suma"],
                                'fecha_registro' => date('Y-m-d H:i:s'),
                                'estado' => 1
                            ],
                                'id_partida'
                            );
                        }
                    }
                }
                else if ($rel_tipo == 'CI'){
                    foreach($ci as $i){
                        if ($relacionado === $i->codigo){
                            $agrega = true;
                            DB::table('finanzas.presup_par')
                            ->insertGetId([
                                'id_presup' => $id_presup,
                                'codigo' => $par->codigo,
                                'id_pardet' => $par->id_pardet,
                                'cod_padre' => $par->cod_padre,
                                'relacionado' => '',
                                'importe_base' => 0,
                                'importe_total' => $i->total_comp,
                                'fecha_registro' => date('Y-m-d H:i:s'),
                                'estado' => 1
                            ],
                                'id_partida'
                            );
                        }
                    }
                }
                else if ($rel_tipo == 'GG'){
                    foreach($gg as $g){
                        if ($relacionado === $g->codigo){
                            $agrega = true;
                            DB::table('finanzas.presup_par')
                            ->insertGetId([
                                'id_presup' => $id_presup,
                                'codigo' => $par->codigo,
                                'id_pardet' => $par->id_pardet,
                                'cod_padre' => $par->cod_padre,
                                'relacionado' => '',
                                'importe_base' => 0,
                                'importe_total' => $g->total_comp,
                                'fecha_registro' => date('Y-m-d H:i:s'),
                                'estado' => 1
                            ],
                                'id_partida'
                            );
                        }
                    }
                }

                if ($agrega == false){
                    DB::table('finanzas.presup_par')
                        ->insertGetId([
                            'id_presup' => $id_presup,
                            'codigo' => $par->codigo,
                            'id_pardet' => $par->id_pardet,
                            'cod_padre' => $par->cod_padre,
                            'relacionado' => '',
                            'importe_base' => 0,
                            'importe_total' => 0,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => 1
                        ],
                            'id_partida'
                        );
                }
            }
            DB::table('proyectos.proy_presup')
            ->where('id_presupuesto',$id_presupuesto)
            ->update(['estado'=>8, 'id_presup'=>$id_presup]);//Emitido

            $this->suma_titulos($id_presup);
            // $html = $this->html_presupuesto_proyecto($id_presup,'imprimir_padres');

            // return json_encode(['id_presup'=>$id_presup,'html'=>$html]);
            DB::commit();
            return json_encode($id_presup);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function listar_presupuesto_proyecto($id)
    {
        $html = $this->html_presupuesto_proyecto($id,'imprimir_padres');
        return json_encode($html);
    }

    public function listar_saldos_presupuesto($id)
    {
        $partidas = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup_pardet.descripcion')
            ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->where([['presup_par.id_presup', '=', $id],
                    ['presup_par.estado', '=', 1]])
            ->orderBy('presup_par.codigo')
            ->get()
            ->toArray();
            
        $titulos = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id],
                    ['presup_titu.estado', '=', 1]])
            ->orderBy('presup_titu.codigo')
            ->get();

        $html = '';
        $total = 0;
        $total_oc = 0;
        $total_req = 0;

        foreach ($titulos as $titu){
            $codigo = "'".$titu->codigo."'";
            $html .= '
            <tr id="ti-'.$titu->id_titulo.'" class=" success" >
                <td class="green"><strong>'.$titu->codigo.'</strong></td>
                <td class="green"><strong>'.$titu->descripcion.'</strong></td>
                <td class="right blue"><strong>'.number_format($titu->total,3,'.',',').'</strong></td>
                <td></td><td></td><td></td></tr>';

            foreach($partidas as $par){
                if ($titu->codigo == $par->cod_padre){

                    $total += $par->importe_total;
                    $html .= '
                    <tr id="par-'.$par->id_partida.'">
                        <td id="pd-'.(isset($par->id_pardet) ? $par->id_pardet : '').'">'.$par->codigo.'</td>
                        <td>'.$par->descripcion.'</td>
                        <td class="right blue"><strong>'.number_format($par->importe_total,3,'.',',').'</strong></td>';
                    //suma las relaciones con oc
                    $det_oc = DB::table('almacen.alm_det_req')
                        ->select(DB::raw('SUM(alm_det_req.cantidad * alm_det_req.precio_referencial) as suma_req'))
                        // DB::raw('SUM(log_valorizacion_cotizacion.precio_sin_igv) as suma_sin_igv'))
                        // ->leftjoin('logistica.valoriza_coti_detalle','valoriza_coti_detalle.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
                        // ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','valoriza_coti_detalle.id_valorizacion_cotizacion')
                        ->where([['alm_det_req.partida','=',$par->id_partida],
                                // ['valoriza_coti_detalle.estado','=',1],
                                // ['log_valorizacion_cotizacion.estado','!=',7],
                                ['alm_det_req.estado','!=',7]])
                        ->first();
                    //si existe oc suma total_oc
                    if (isset($det_oc)){
                        // $total_oc += $det_oc->suma_sin_igv;
                        $total_oc += $det_oc->suma_req;
                        $html .= '
                        <td class="right red"><strong>'.number_format($det_oc->suma_req,3,'.',',').'</strong></td>
                        <td class="right green"><strong>'.number_format(($par->importe_total - $det_oc->suma_req),3,'.',',').'</strong></td>';

                        if ($det_oc->suma_req > 0){
                            $html .='<td>
                            <i class="fas fa-list-alt btn-info visible boton" data-toggle="tooltip" data-placement="bottom" 
                            title="Ver Detalle Consumido" onClick="ver_detalle_partida('.$par->id_partida.','."'".$par->codigo.' '.$par->descripcion."'".','.$par->importe_total.');"></i>
                            </td>';
                        } else {
                            $html .='<td></td>';
                        }
                    } else {
                        $html .= '<td></td><td></td><td></td>';
                    }
                    
                    $html .='</tr>';
                }
            }
        }

        return json_encode(['html'=>$html,'total_oc'=>$total_oc,'total'=>$total]);
    }

    public function html_presupuesto_proyecto($id, $var)
    {
        $partidas = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup_pardet.descripcion')
            ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->where([['presup_par.id_presup', '=', $id],
                     ['presup_par.estado', '=', 1]])
            ->orderBy('presup_par.codigo')
            ->get()
            ->toArray();
            
        $titulos = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id],
                     ['presup_titu.estado', '=', 1]])
            ->orderBy('presup_titu.codigo')
            ->get();
    
        $html = '';

        foreach ($titulos as $titu){
            // $total = 0;
            $codigo = "'".$titu->codigo."'";
            $html .= '
            <tr id="ti-'.$titu->id_titulo.'" class="green success" >
                <td><strong>'.$titu->codigo.'</strong></td>
                <td><strong>'.$titu->descripcion.'</strong></td>
                <td class="right"><strong>'.number_format($titu->total,3,'.',',').'</strong></td>';
                if ($var == 'imprimir_padres'){
                    $html .='<td hidden>'.$titu->cod_padre.'</td>';
                }
                if ($var == 'boton_saldos'){
                    $html .='<td></td>';
                }
            $html .='</tr>';

            foreach($partidas as $par){
                if ($titu->codigo == $par->cod_padre){
                    // $total += $par->importe_total;
                    $html .= '
                    <tr id="par-'.$par->id_partida.'">
                        <td id="pd-'.(isset($par->id_pardet) ? $par->id_pardet : '').'">'.$par->codigo.'</td>
                        <td>'.$par->descripcion.'</td>
                        <td class="right">'.number_format($par->importe_total,3,'.',',').'</td>';

                        if ($var == 'imprimir_padres'){
                            $html.='<td hidden>'.$par->cod_padre.'</td>';
                        }

                        if ($var == 'boton_saldos'){
                            //cuenta las relaciones con requerimiento
                            $count_req = DB::table('almacen.alm_det_req')
                            ->where([['alm_det_req.partida','=',$par->id_partida],['alm_det_req.estado','!=',7]])
                            ->count();
                            //si count es mayor a 0 color warning
                            if ($count_req > 0){
                                $html .= '<td>
                                <i class="fas fa-list-alt btn-warning visible boton" data-toggle="tooltip" data-placement="bottom" 
                                title="Ver Detalle Consumido" onClick="ver_detalle_partida('.$par->id_partida.','."'".$par->codigo.' '.$par->descripcion."'".','.$par->importe_total.');"></i>
                                </td>';
                            } else {
                                $html .= '<td></td>';
                            }
                            
                        }
                    $html .='</tr>';
                }
            }
        }
        return $html;
    }

    public function download_presupuesto($id){
        $detalle = $this->html_presupuesto_proyecto($id,'');
        $data = '
        <html>
            <head>
            <style type="text/css">
                *{ 
                    font-family: Calibri;
                }
                body{
                    background-color: #fff;
                    font-family: "DejaVu Sans";
                    font-size: 12px;
                    box-sizing: border-box;
                }
            </style>
            </head>
            <body>
                <table border="0" width="100%">
                    <thead>
                        <tr><th colSpan="3" style="alignment:center;">PRESUPUESTO INTERNO</th></tr>
                    </thead>
                </table>
                </br>
                <table id="detalle" border="0" width="100%">
                    <thead>
                        <tr style="background: silver;">
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>'.$detalle.'</tbody>
                </table>
            </body>
        </html>
        ';
        // return $data;  //class="table table-condensed table-bordered table-hover sortable" 
        return view('proyectos/reportes/presupuesto_excel', compact('data'));
    }

    public function download_propuesta($id){
        
        $partidas = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup_pardet.descripcion as pardet_descripcion','alm_und_medida.abreviatura',
            'presup_parobs.descripcion as obs')
            ->leftjoin('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','presup_par.unidad_medida')
            ->leftjoin('finanzas.presup_parobs','presup_parobs.id_partida','=','presup_par.id_partida')
            ->where([['presup_par.id_presup', '=', $id],
                     ['presup_par.estado', '=', 1]])
            ->orderBy('presup_par.codigo')
            ->get()
            ->toArray();
            
        $titulos = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id],
                     ['presup_titu.estado', '=', 1]])
            ->orderBy('presup_titu.codigo')
            ->get();
    
        $detalle = '';
        $total = 0;
        $utilidad = 0;

        foreach ($titulos as $titu){
            $codigo = "'".$titu->codigo."'";
            $detalle .= '
            <tr>
                <td><strong>'.$titu->codigo.'</strong></td>
                <td><strong>'.$titu->descripcion.'</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right"><strong>'.number_format($titu->total,2,'.',',').'</strong></td>
                <td></td>
                <td></td>
            </tr>';

            foreach($partidas as $par){
                if ($titu->codigo == $par->cod_padre){
                    $total += $par->importe_total;
                    $utilidad += $par->importe_utilidad;
                    $detalle .= '
                    <tr>
                        <td style="vertical-align: top;">'.$par->codigo.'</td>
                        <td style="vertical-align: top;">'.$par->descripcion.'</td>
                        <td style="vertical-align: top;">'.$par->abreviatura.'</td>
                        <td style="vertical-align: top;">'.number_format($par->metrado,2,'.',',').'</td>
                        <td style="vertical-align: top;">'.number_format($par->importe_unitario,2,'.',',').'</td>
                        <td style="vertical-align: top;">'.number_format($par->importe_total,2,'.',',').'</td>
                        <td style="vertical-align: top;">'.number_format($par->porcentaje_utilidad,2,'.',',').'</td>
                        <td style="vertical-align: top;">'.number_format($par->importe_utilidad,2,'.',',').'</td>
                    </tr>';
                    if ($par->obs !== null && $par->obs !== ''){
                        $detalle .='
                        <tr>
                            <td></td>
                            <td>'.$par->obs.'</td>
                            <td colSpan="6"></td>
                        </tr>';
                    }
                }
            }
        }
        $totales = DB::table('finanzas.presup_totales')->where('id_presup',$id)->first();
        $data = '
        <html>
            <head>
            <style type="text/css">
                *{ 
                    font-family: Calibri;
                }
                body{
                    background-color: #fff;
                    font-family: "DejaVu Sans";
                    font-size: 12px;
                    box-sizing: border-box;
                }
                #detalle thead tr th,
                #detalle tbody tr td{
                    border: 0px;
                }
                #detalle tfoot tr th{
                    border-top: 1px solid #605f5f;
                }
            </style>
            </head>
            <body>
                <table border="0" width="100%">
                    <thead>
                        <tr><th colSpan="8" style="alignment:center;font-size: 16px;">PROPUESTA CLIENTE</th></tr>
                        <tr><td colSpan="8"></td></tr>
                    </thead>
                </table>
                <table id="detalle" width="100%">
                    <thead>
                        <tr style="background: silver;">
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Unidad</th>
                            <th>Metrado</th>
                            <th>Unitario</th>
                            <th>Total</th>
                            <th>% Uti.</th>
                            <th>Utilidad</th>
                        </tr>
                    </thead>
                    <tbody>'.$detalle.'</tbody>
                    <tfoot>
                        <tr>
                            <th colSpan="5" style="text-align: right;">Sub Total</th>
                            <th>'.number_format($total,2,'.',',').'</th>
                            <th></th>
                            <th>'.number_format($utilidad,2,'.',',').'</th>
                        </tr>';
                    if (isset($totales)){
                        $data.='
                        <tr>
                            <td colSpan="5" style="text-align: right;">Utilidad</td>
                            <td>'.number_format($totales->importe_utilidad,2,'.',',').'</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colSpan="5" style="text-align: right;">Total</th>
                            <th>'.number_format(($totales->importe_utilidad + $totales->sub_total),2,'.',',').'</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <td colSpan="5" style="text-align: right;">IGV</td>
                            <td>'.number_format($totales->importe_igv,2,'.',',').'</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colSpan="5" style="text-align: right;">Total Propuesta</th>
                            <th><strong>'.number_format($totales->total_propuesta,2,'.',',').'</strong></th>
                            <th></th>
                            <th></th>
                        </tr>';
                    }
                    $data.='</tfoot>
                </table>
            </body>
        </html>
        ';
        return view('proyectos/reportes/propuesta_excel', compact('data'));
    }

    public function download_cronoval($id_presupuesto, $nro_dias){

        $part_cd = DB::table('proyectos.proy_cd_pcronog')
            ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cu.id_cu',
            'proy_cd_partida.id_cu_partida','proy_cd_partida.cod_compo','proy_cd_partida.codigo',
            'proy_cd_partida.descripcion','proy_cd_partida.cantidad','proy_cd_partida.importe_parcial',
            'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->leftjoin('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
            ->leftjoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_pcronog.id_presupuesto', '=', $id_presupuesto],
                     ['proy_cd_pcronog.estado', '=', 1]])
            ->orderBy('proy_cd_pcronog.nro_orden')
            ->get()
            ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                    ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();

        $lista = [];
        $partidas = [];
        $fini_crono = null;
        $ffin_crono = null;

        foreach($compo_cd as $comp){
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    if ($ffin_crono == null){
                        $ffin_crono = $partida->fecha_fin;
                    } else {
                        if ($ffin_crono < $partida->fecha_fin){
                            $ffin_crono = $partida->fecha_fin;
                        }
                    }
                    if ($fini_crono == null){
                        $fini_crono = $partida->fecha_inicio;
                    } else {
                        if ($fini_crono > $partida->fecha_inicio){
                            $fini_crono = $partida->fecha_inicio;
                        }
                    }
                    array_push($partidas, $partida);
                }
            }
            $nuevo_comp = [
                'id_cd_compo' => $comp->id_cd_compo,
                'codigo' => $comp->codigo,
                'descripcion' => $comp->descripcion,
                'cod_padre' => $comp->cod_padre,
                'partidas' => $partidas
            ];
            $partidas = [];
            array_push($lista, $nuevo_comp);
        }

        foreach($part_cd as $partida){
            if ($partida->tipo !== 'cd'){
                array_push($lista, $partida);
            } 
        }
        $total = DB::table('proyectos.proy_presup_importe')->where('id_presupuesto',$id_presupuesto)->first();

        $fecha1 = new DateTime($fini_crono);
        $fecha2 = new DateTime($ffin_crono);
        $duracion_total = $fecha1->diff($fecha2);
        $array_periodo = [];
        
        if ($duracion_total->days > $nro_dias){
            $length = 0;
            if ($nro_dias > 0){
                $length = ($duracion_total->days / $nro_dias);
            }
            $periodo;
            $suma_rango = 0;
            $i;
            $fini = $fini_crono;
            $ffin;

            for ($i=1;$i<=$length;$i++) {
                $suma_rango += $nro_dias;
                // $duracion = round(($partida->cantidad / $partida->rendimiento),2,PHP_ROUND_HALF_UP);
                $ffin = date("Y-m-d",strtotime($fini."+ ".round($nro_dias,0,PHP_ROUND_HALF_UP)." days"));

                // $ffin = strtotime( '+'+$nro_dias+' day' , strtotime( $fini ) );
                // $ffin = date ( 'Y-m-d' , $ffin );
                $periodo = [
                    'nro' => $i,
                    'nro_dias'=> $nro_dias,
                    'dias'=> $suma_rango,
                    'fecha_inicio'=> $fini,
                    'fecha_fin'=> $ffin
                ];
                array_push($array_periodo, $periodo);
                $fini = $ffin;
            }
            $dif = $duracion_total->days - $suma_rango;

            if ($dif > 0){
                $suma_rango += $dif;
                $ffin = date("Y-m-d",strtotime($fini."+ ".round($nro_dias,0,PHP_ROUND_HALF_UP)." days"));
                // $ffin = strtotime( '+'+$dif+' day' , strtotime( $fini ) );
                // $ffin = date ( 'Y-m-d' , $ffin );
                $periodo = [
                    'nro'=> $i,
                    'nro_dias'=> $dif,
                    'dias'=> $suma_rango,
                    'fecha_inicio'=> $fini,
                    'fecha_fin'=> $ffin
                ];
                array_push($array_periodo, $periodo);
            }
        }

        return ['array_periodo'=>$array_periodo];

/*
        $data = '
        <html>
            <head>
            <style type="text/css">
                *{ 
                    font-family: Calibri;
                }
                body{
                    background-color: #fff;
                    font-family: "DejaVu Sans";
                    font-size: 12px;
                    box-sizing: border-box;
                }
            </style>
            </head>
            <body>
                <table border="0" width="100%">
                    <thead>
                        <tr><th colSpan="3" style="alignment:center;">PRESUPUESTO INTERNO</th></tr>
                    </thead>
                </table>
                </br>
                <table id="detalle" border="0" width="100%">
                    <thead>
                        <tr style="background: silver;">
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>'.$detalle.'</tbody>
                </table>
            </body>
        </html>
        ';*/
        // return view('proyectos/reportes/presupuesto_excel', compact('data'));
    }

    public function suma_titulos($id_presup)
    {
        //Listar titulos
        $titulos = DB::table('finanzas.presup_titu')
            ->where([['presup_titu.id_presup','=',$id_presup],['estado','=',1]])
            ->get();
        $update = 0;

        foreach($titulos as $ti){
            //Sumar partidas
            $part = DB::table('finanzas.presup_par')
            ->select(DB::raw('SUM(presup_par.importe_total) as suma_partidas'))
            ->where([['presup_par.cod_padre', '=', $ti->codigo],
                    ['presup_par.id_presup', '=', $id_presup],
                    ['presup_par.estado', '=', 1]])
            ->first();    
            
            if (isset($part->suma_partidas)){
                //Actualiza totales de los padres
                $update = DB::table('finanzas.presup_titu')
                ->where('presup_titu.id_titulo',$ti->id_titulo)
                ->update(['total'=>$part->suma_partidas]);
            }
        }

        foreach($titulos as $ti){
            //Suma de titulos
            $sum = DB::table('finanzas.presup_titu')
            ->select(DB::raw('SUM(presup_titu.total) as suma_total'))
            ->where([['presup_titu.cod_padre', '=', $ti->codigo],
                    ['presup_titu.id_presup', '=', $id_presup],
                    ['presup_titu.estado', '=', 1]])
            ->first();

            if (isset($sum->suma_total)){
                //Actualiza totales de los padres
                $update = DB::table('finanzas.presup_titu')
                ->where('presup_titu.id_titulo',$ti->id_titulo)
                ->update(['total'=>$sum->suma_total]);
            }
        }

        return response()->json($update);
    }

    public function anular_estructura($id_pres)
    {
        $pres = DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$id_pres)
        ->first();
        $update = 0;

        if (isset($pres->id_presup)){
            $partidas = DB::table('finanzas.presup_par')
            ->where('id_presup',$pres->id_presup)
            ->get();
            $tiene_req = false;
            $r = 0;
            
            foreach($partidas as $par){
                $req = DB::table('almacen.alm_det_req')
                ->where([['partida','=',strval($par->id_partida)],
                         ['estado','!=',7]])
                ->count();
                
                if ($req > 0){
                    $tiene_req = true;
                    $r++;
                    break;
                }
            }
            //el presupuesto tiene partidas?
            if ($tiene_req == false){
                //Anula presup
                $update = DB::table('finanzas.presup')
                ->where('id_presup',$pres->id_presup)
                ->update(['estado' => 7]);
                //Anula titulos 
                $update = DB::table('finanzas.presup_titu')
                ->where('id_presup',$pres->id_presup)
                ->update(['estado' => 7]);
                //Anula partidas
                $update = DB::table('finanzas.presup_par')
                ->where('id_presup',$pres->id_presup)
                ->update(['estado' => 7]);
                //Quita la relacion con el presupuesto
                $update = DB::table('proyectos.proy_presup')
                ->where('id_presupuesto',$id_pres)
                ->update(['id_presup' => null,'estado'=>1]);
            }
        }

        return response()->json($update);
    }

    public function crear_titulos_ci($id_presupuesto){
        $pres = DB::table('finanzas.presup')
        ->where([['tp_presup','=',1],['estado','=',1]])
        ->orderBy('presup.fecha_emision','desc')
        ->first();
        $data = '';

        if (isset($pres)){
            $titulos = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup_pardet.descripcion')
            ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->where([['id_presup','=',$pres->id_presup],['relacionado','like','CI%']])
            ->orderBy('relacionado','asc')
            ->get();
    
            // $data = DB::table('proyectos.proy_ci_compo')
            // ->insertGetId([
            //     'id_ci' => $id_presupuesto,
            //     'codigo' => '01',
            //     'descripcion' => 'Almacenes / Alojamiento / Alimentación',
            //     'cod_padre' => '',
            //     'total_comp' => 0,
            //     'fecha_registro' => date('Y-m-d H:i:s'),
            //     'estado' => 1
            // ],
            //     'id_ci_compo'
            // );
    
            foreach($titulos as $d){
                $codigo = substr($d->relacionado, 2, (strlen($d->relacionado)-2));
                $tiene = strstr($d->relacionado, '.', true);
                $padre = (strlen($tiene) > 0 ? substr($tiene, 2, strlen($tiene)) : '');
    
                $data = DB::table('proyectos.proy_ci_compo')
                ->insertGetId([
                    'id_ci' => $id_presupuesto,
                    'codigo' => $codigo,
                    'descripcion' => $d->descripcion,
                    'cod_padre' => $padre,
                    'total_comp' => 0,
                    'fecha_registro' => date('Y-m-d H:i:s'),
                    'estado' => 1
                ],
                    'id_ci_compo'
                );
            }
        }
        return response()->json($data);
    }

    public function crear_titulos_gg($id_presupuesto){
        $pres = DB::table('finanzas.presup')
        ->where([['tp_presup','=',1],['estado','=',1]])
        ->orderBy('presup.fecha_emision','desc')
        ->first();

        $titulos = DB::table('finanzas.presup_par')
        ->select('presup_par.*','presup_pardet.descripcion')
        ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
        ->where([['id_presup','=',$pres->id_presup],['relacionado','like','GG%']])
        ->orderBy('relacionado','asc')
        ->get();

        foreach($titulos as $d){
            $codigo = substr($d->relacionado, 2, (strlen($d->relacionado)-2));
            // $padre = substr($d->relacionado, 2, (strlen($d->relacionado)-5));

            $tiene = strstr($d->relacionado, '.', true);
            $padre = (strlen($tiene) > 0 ? substr($tiene, 2, strlen($tiene)) : '');

            $data = DB::table('proyectos.proy_gg_compo')
            ->insertGetId([
                'id_gg' => $id_presupuesto,
                'codigo' => $codigo,
                'descripcion' => $d->descripcion,
                'cod_padre' => $padre,
                'total_comp' => 0,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_gg_compo'
            );
        }
        return response()->json($data);
    }

    public function mostrar_total_presint($id_op_com){
        $data = DB::table('proyectos.proy_presup')
        ->select('proy_presup_importe.sub_total','proy_presup.moneda','proy_presup.id_presupuesto')
        ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
        ->where([['id_op_com','=',$id_op_com],['estado','=',8],['id_tp_presupuesto','=',1]])
        ->orderBy('id_op_com','desc')
        ->first();
        return response()->json($data);
    }
    
    public function copiar_partidas_presint($id_presupuesto, $id_presup)
    {
        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*','proy_presup.fecha_emision',
            'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_partida.id_cd', '=', $id_presupuesto],
                     ['proy_cd_partida.estado', '!=', 7]])
            ->orderBy('proy_cd_partida.codigo')
            ->get();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                    ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();


        foreach ($compo_cd as $titu) {
            $id_titulo = DB::table('finanzas.presup_titu')
            ->insertGetId([
                'id_presup' => $id_presup,
                'codigo' => $titu->codigo,
                'descripcion' => strtoupper($titu->descripcion),
                'cod_padre' => $titu->cod_padre,
                'total' => $titu->total_comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_titulo'
            );
        }

        foreach ($part_cd as $par) {
            $id_partida = DB::table('finanzas.presup_par')
            ->insertGetId([
                'id_presup' => $id_presup,
                'codigo' => $par->codigo,
                'descripcion' => $par->descripcion,
                'cod_padre' => $par->cod_compo,
                'unidad_medida' => $par->unid_medida,
                'metrado' => $par->cantidad,
                'importe_unitario' => $par->importe_unitario,
                'importe_total' => $par->importe_parcial,
                'porcentaje_utilidad' => 0,
                'importe_utilidad' => 0,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_partida'
            );
        }

        $presup = DB::table('proyectos.proy_presup_importe')->where('id_presupuesto',$id_presupuesto)->first();

        DB::table('finanzas.presup_par')->insertGetId([
                'id_presup' => $id_presup,
                'codigo' => 'CI',
                'descripcion' => 'COSTOS INDIRECTOS',
                'cod_padre' => null,
                'unidad_medida' => null,
                'metrado' => 1,
                'importe_unitario' => $presup->total_ci,
                'importe_total' => $presup->total_ci,
                'porcentaje_utilidad' => 0,
                'importe_utilidad' => 0,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_partida'
            );

        DB::table('finanzas.presup_par')->insertGetId([
                'id_presup' => $id_presup,
                'codigo' => 'GG',
                'descripcion' => 'GASTOS GENERALES',
                'cod_padre' => null,
                'unidad_medida' => null,
                'metrado' => 1,
                'importe_unitario' => $presup->total_gg,
                'importe_total' => $presup->total_gg,
                'porcentaje_utilidad' => 0,
                'importe_utilidad' => 0,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_partida'
            );

        DB::table('finanzas.presup_totales')->where('id_presup',$id_presup)
            ->update([
                'sub_total' => $presup->sub_total,
                'porcen_utilidad' => 0,
                'importe_utilidad' => 0,
                'porcen_igv' => $presup->porcentaje_igv,
                'importe_igv' => $presup->total_igv,
                'total_propuesta' => $presup->total_presupuestado
            ]);

        return response()->json($id_presup);
    }

    public function guardar_presup(Request $request)
    {
        $codigo = $this->nextCodigoPresupuesto($request->id_grupo,$request->fecha_emision,$request->tp_presup);
        $opcion = null;
        if ($request->id_op_com !== null){
            $opcion = DB::table('proyectos.proy_op_com')->where('id_op_com',$request->id_op_com)
            ->first();
        }

        $id_presup = DB::table('finanzas.presup')
            ->insertGetId([
                'id_empresa' => ($opcion !== null ? $opcion->id_empresa : 1),
                'id_grupo' => $request->id_grupo,
                'fecha_emision' => $request->fecha_emision,
                'codigo' => $codigo,
                'descripcion' => $request->nombre_opcion,
                'moneda' => $request->moneda,
                'responsable' => $request->responsable,
                'tp_presup' => $request->tp_presup,
                'id_op_com' => $request->id_op_com,
                'id_presint' => $request->id_presupuesto,
                'cronograma' => false,
                'cronoval' => false,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
            ],
                'id_presup'
            );

        if ($request->tp_presup == 3){
            DB::table('finanzas.presup_totales')
            ->insert([
                'id_presup' => $id_presup,
                'sub_total' => $request->sub_total,
                'porcen_utilidad' => $request->porcen_utilidad,
                'importe_utilidad' => $request->importe_utilidad,
                'porcen_igv' => $request->porcen_igv,
                'importe_igv' => $request->importe_igv,
                'total_propuesta' => $request->total_propuesta
            ]);
            // $totales = DB::table('finanzas.presup_totales')->where('id_presup',$id_presup)->first();
            
            // return response()->json(['data'=>$id_presup,'totales'=>$totales]);
        }
        return response()->json($id_presup);
    }

    public function update_presup(Request $request){
        $data = DB::table('finanzas.presup')->where('id_presup',$request->id_presup)
            ->update([
                'fecha_emision' => $request->fecha_emision,
                'descripcion' => $request->nombre_opcion,
                'moneda' => $request->moneda,
                'responsable' => $request->responsable,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
            ]);
        if ($request->tp_presup == 3){
            $data = DB::table('finanzas.presup_totales')->where('id_presup',$request->id_presup)
                    ->update([  'porcen_utilidad' => $request->porcen_utilidad,
                                'importe_utilidad' => $request->impor_utilidad ]);
            $this->totales_propuesta($request->id_presup);

            $totales = DB::table('finanzas.presup_totales')->where('id_presup',$request->id_presup)->first();
            
            return response()->json(['data'=>$data,'totales'=>$totales]);
        }
        return response()->json($data);
    }

    public function anular_presup($id_presup){
        $data = DB::table('finanzas.presup')->where('id_presup', $id_presup)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function anular_propuesta($id_presup){
        $presup = DB::table('finanzas.presup')->where('id_presup', $id_presup)->first();
        $data = 0;
        if (isset($presup)){
            if ($presup->estado !== 7 && $presup->cronograma == false && $presup->cronoval == false){
                $data = DB::table('finanzas.presup')->where('id_presup', $id_presup)
                    ->update([ 'estado' => 7 ]);
            }
        }
        return response()->json($data);
    }

    public function totales_propuesta($id_presup){
        $totales = DB::table('finanzas.presup_par')
            ->select(DB::raw('SUM(presup_par.importe_total) as suma_partidas'))
            ->where([['id_presup','=',$id_presup],['estado','=',1]])
            ->first();

        $imp = DB::table('finanzas.presup_totales')
            ->where([['id_presup','=',$id_presup]])
            ->first();

        if (isset($imp) && isset($totales)){
            //utilidad en partidas
            $utilidad = DB::table('finanzas.presup_par')
            ->select(DB::raw('SUM(presup_par.importe_utilidad) as suma_utilidad'))
            ->where([['id_presup','=',$id_presup],['estado','=',1]])
            ->first();
            //si existe utilidad en las partidas
            $importe_uti = 0;
            if (isset($utilidad) && $utilidad->suma_utilidad > 0){
                $importe_uti = $utilidad->suma_utilidad;
                $porcentaje_uti = 0;
            } else {
                $porcentaje_uti = $imp->porcen_utilidad;
                //calcula utilidad global
                if ($imp->porcen_utilidad > 0){//si se eligio como porcentaje
                    $importe_uti = $imp->porcen_utilidad * $totales->suma_partidas / 100;
                } else {//si se eligio como importe
                    $importe_uti = $imp->importe_utilidad;
                }
            }
            //actualiza total
            $total = $totales->suma_partidas + $importe_uti;
            //si no existe porcentaje igv lo actualiza segun el ultimo registrado
            $porcentaje_igv = 0;
            if ($imp->porcen_igv > 0){
                $porcentaje_igv = $imp->porcen_igv;
            } 
            else {
                $igv = DB::table('contabilidad.cont_impuesto')
                ->where([['codigo','=','IGV'],['estado','=',1]])
                ->orderBy('fecha_inicio','desc')
                ->first();
                $porcentaje_igv = $igv->porcentaje;
            }
            //actualiza total igv
            $total_igv = $total * $porcentaje_igv / 100;
            $total_propuesta = $total + $total_igv;
            
            DB::table('finanzas.presup_totales')->where('id_presup',$id_presup)
            ->update([  'sub_total'=>$totales->suma_partidas,
                        'importe_utilidad'=>$importe_uti,
                        'porcen_utilidad'=>$porcentaje_uti,
                        'porcen_igv'=>$porcentaje_igv,
                        'importe_igv'=>$total_igv,
                        'total_propuesta'=>$total_propuesta ]);
        }
        return response()->json($totales);
    }

    public function listar_propuestas(){
        $tp_propuesta = 3;
        $data = DB::table('finanzas.presup')
        ->select('presup.*')
        // ->leftjoin('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','presup.unid_program')
        ->where([['presup.tp_presup','=',$tp_propuesta],['presup.estado','!=',7]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_propuestas_preseje(){
        $tp_propuesta = 3;
        $data = DB::table('finanzas.presup')
        ->select('presup.*',DB::raw('(SELECT proy_presup.id_presupuesto FROM proyectos.proy_presup WHERE 
                proy_presup.id_op_com = presup.id_op_com and
                proy_presup.id_tp_presupuesto = 2 and 
                proy_presup.estado != 7
                order by proy_presup.version desc limit 1) AS id_presupuesto'))
        ->where([['presup.tp_presup','=',$tp_propuesta],
                 ['presup.estado','!=',7]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_propuestas_activas(){
        $tp_propuesta = 3;
        $data = DB::table('finanzas.presup')
        ->select('presup.*')
        ->where([['presup.tp_presup','=',$tp_propuesta],['presup.estado','=',1]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_propuesta($id_presup){
        $propuesta = DB::table('finanzas.presup')
        ->select('presup.*','presup_totales.sub_total','presup_totales.porcen_utilidad',
        'presup_totales.importe_utilidad','presup_totales.porcen_igv','adm_estado_doc.estado_doc as des_estado',
        'presup_totales.importe_igv','presup_totales.total_propuesta','sis_moneda.simbolo')
        ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','presup.moneda')
        ->leftjoin('finanzas.presup_totales','presup_totales.id_presup','=','presup.id_presup')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','presup.estado')
        ->where('presup.id_presup',$id_presup)
        ->first();

        $totales = DB::table('finanzas.presup_totales')
        ->where('id_presup',$id_presup)
        ->first();

        $presint = DB::table('proyectos.proy_presup')
        ->select('proy_presup_importe.*','proy_presup.codigo')
        ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
        ->where([['proy_presup.id_op_com','=',$propuesta->id_op_com],['proy_presup.estado','!=',7],
                ['id_tp_presupuesto','=',1]])
        ->orderBy('id_presup','desc')
        ->first();

        return response()->json(['propuesta'=>$propuesta,'totales'=>$totales,
                                 'presint'=>(isset($presint) ? $presint : '')]);
    }

    public function listar_partidas_propuesta($id)
    {
        $partidas = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup_pardet.descripcion as des_pardet')
            ->leftjoin('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->where([['presup_par.id_presup', '=', $id],
                     ['presup_par.estado', '=', 1]])
            ->orderBy('presup_par.codigo')
            ->get()
            ->toArray();
            
        $titulos = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id],
                     ['presup_titu.estado', '=', 1]])
            ->orderBy('presup_titu.codigo')
            ->get();
    
        $nuevos_titulos = [];
        $array = [];
        $html = '';
        $unidades = $this->mostrar_unidades_cbo();

        foreach ($titulos as $titu){
            // $total = 0;
            $codigo = "'".$titu->codigo."'";
            $html .= '
            <tr id="ti-'.$titu->id_titulo.'" class="green success" >
                <td></td> 
                <td><strong>'.$titu->codigo.'</strong></td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                        value="'.$titu->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right"><strong>'.number_format($titu->total,2,'.','').'</strong></td>
                <td></td>
                <td></td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Título" onClick="agregar_titulo('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agregar_partida('.$codigo.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Título" onClick="editar_titulo('.$titu->id_titulo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Título" onClick="update_titulo('.$titu->id_titulo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Título" onClick="anular_titulo('.$titu->id_titulo.','."'".$titu->codigo."'".');"></i>
                </td>
                <td hidden>'.$titu->cod_padre.'</td>
            </tr>';

            foreach($partidas as $par){
                if ($titu->codigo == $par->cod_padre){
                    // $total += $par->importe_total;
                    $html .= '
                    <tr id="par-'.$par->id_partida.'">
                        <td>
                            <i class="fas fa-arrow-alt-circle-down" data-toggle="tooltip" data-placement="bottom" title="Bajar Partida" onClick="bajar_partida('.$par->id_partida.');"></i>
                            <i class="fas fa-arrow-alt-circle-up" data-toggle="tooltip" data-placement="bottom" title="Subir Partida" onClick="subir_partida('.$par->id_partida.');"></i>
                        </td>
                        <td id="pd-'.(isset($par->id_pardet) ? $par->id_pardet : '').'">'.$par->codigo.'</td>
                        <td>
                            <input type="text" class="input-data" name="descripcion" 
                                value="'.($par->descripcion !== null ? $par->descripcion : $par->des_pardet).'" disabled="true"/>
                        </td>
                        <td>
                            <select class="input-data" name="unidad_medida" disabled="true">
                                <option value="0">Elija una opción</option>';
                                foreach ($unidades as $row) {
                                    if ($par->unidad_medida == $row->id_unidad_medida){
                                        $html.='<option value="'.$row->id_unidad_medida.'" selected>'.$row->descripcion.'</option>';
                                    } else {
                                        $html.='<option value="'.$row->id_unidad_medida.'">'.$row->descripcion.'</option>';
                                    }
                                }
                            $html.='</select>
                        </td>
                        <td>
                        <input type="number" class="input-data right" style="width:100px;" disabled="true"
                            name="metrado" onChange="calcular_total('.$par->id_partida.')" value="'.number_format($par->metrado,2,'.','').'" />
                        </td>
                        <td>
                        <input type="number" class="input-data right" style="width:100px;" disabled="true"
                            name="importe_unitario" onChange="calcular_total('.$par->id_partida.')" value="'.number_format($par->importe_unitario,2,'.','').'" />
                        </td>
                        <td>
                        <input type="number" class="input-data right" style="width:100px;" disabled="true"
                            name="importe_total" onChange="change_total('.$par->id_partida.')" value="'.number_format($par->importe_total,2,'.','').'" />
                        </td>
                        <td>
                        <input type="number" class="input-data right" style="width:100px;" disabled="true"
                            name="porcentaje_utilidad" onChange="change_utilidad_det('.$par->id_partida.')" value="'.number_format($par->porcentaje_utilidad,2,'.','').'" />
                        </td>
                        <td>
                        <input type="number" class="input-data right" style="width:100px;" disabled="true"
                            name="importe_utilidad" onChange="change_importe_utilidad_det('.$par->id_partida.')" value="'.number_format($par->importe_utilidad,2,'.','').'" />
                        </td>
                        <td style="display:flex;">
                        <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida('.$par->id_partida.');"></i>
                        <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_partida('.$par->id_partida.');"></i>
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida('.$par->id_partida.');"></i>
                        <i class="fas fa-list-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle Item" onClick="detalle_partida('.$par->id_partida.');"></i>
                        </td>
                        <td hidden>'.$par->cod_padre.'</td>
                    </tr>';
                }
            }
        }

        foreach($partidas as $par){
            if ($par->cod_padre == null){
                $html .= '
                <tr id="par-'.$par->id_partida.'" class="green success" >
                    <td></td>
                    <td id="pd-'.(isset($par->id_pardet) ? $par->id_pardet : '').'">'.$par->codigo.'</td>
                    <td>
                        <input type="text" class="input-data" name="descripcion" 
                            value="'.($par->descripcion !== null ? $par->descripcion : $par->des_pardet).'" disabled="true"/>
                    </td>
                    <td>
                        <select class="input-data" name="unidad_medida" disabled="true">
                            <option value="0">Elija una opción</option>';
                            foreach ($unidades as $row) {
                                if ($par->unidad_medida == $row->id_unidad_medida){
                                    $html.='<option value="'.$row->id_unidad_medida.'" selected>'.$row->descripcion.'</option>';
                                } else {
                                    $html.='<option value="'.$row->id_unidad_medida.'">'.$row->descripcion.'</option>';
                                }
                            }
                        $html.='</select>
                    </td>
                    <td>
                    <input type="number" class="input-data right" style="width:100px;" disabled="true"
                        name="metrado" onChange="calcular_total('.$par->id_partida.')" value="'.number_format($par->metrado,2,'.','').'" />
                    </td>
                    <td>
                    <input type="number" class="input-data right" style="width:100px;" disabled="true"
                        name="importe_unitario" onChange="calcular_total('.$par->id_partida.')" value="'.number_format($par->importe_unitario,2,'.','').'" />
                    </td>
                    <td>
                    <input type="number" class="input-data right" style="width:100px;" disabled="true"
                        name="importe_total" onChange="change_total('.$par->id_partida.')" value="'.number_format($par->importe_total,2,'.','').'" />
                    </td>
                    <td>
                    <input type="number" class="input-data right" style="width:100px;" disabled="true"
                        name="porcentaje_utilidad" onChange="change_utilidad_det('.$par->id_partida.')" value="'.number_format($par->porcentaje_utilidad,2,'.','').'" />
                    </td>
                    <td>
                    <input type="number" class="input-data right" style="width:100px;" disabled="true"
                        name="importe_utilidad" onChange="change_importe_utilidad_det('.$par->id_partida.')" value="'.number_format($par->importe_utilidad,2,'.','').'" />
                    </td>
                    <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida('.$par->id_partida.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_partida('.$par->id_partida.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida('.$par->id_partida.');"></i>
                    <i class="fas fa-list-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle Item" onClick="detalle_partida('.$par->id_partida.');"></i>
                    </td>
                    <td hidden>'.$par->cod_padre.'</td>
                </tr>';
            }
        }
        
        return json_encode($html);
    }

    public function guardar_detalle_partida(Request $request){
        $data = DB::table('finanzas.presup_parobs')
        ->insert([
            'id_partida'=>$request->id_partida_obs,
            'descripcion'=>$request->par_descripcion,
            'fecha_registro'=>date('Y-m-d H:i:s')
        ]);
        return response()->json($data);
    }

    public function update_detalle_partida(Request $request){
        $data = DB::table('finanzas.presup_parobs')
        ->where('id_partida',$request->id_partida_obs)
        ->update(['descripcion'=>$request->par_descripcion]);

        return response()->json($data);
    }

    public function mostrar_detalle_partida($id_partida){
        $data = DB::table('finanzas.presup_parobs')
        ->where('id_partida',$id_partida)
        ->first();
        return response()->json(isset($data) ? $data : 0);
    }

    public function guardar_cliente(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $contri = DB::table('contabilidad.adm_contri')
            ->where('nro_documento',$request->nro_documento)
            ->first();
        $id_cliente = '';
        $id_contribuyente = '';

        if ($contri !== null){
            $id_contribuyente = $contri->id_contribuyente;
        } 
        else {
            $id_contribuyente = DB::table('contabilidad.adm_contri')->insertGetId(
                [
                    'id_tipo_contribuyente' => $request->id_tipo_contribuyente,
                    'id_doc_identidad' => $request->id_doc_identidad,
                    'nro_documento' => $request->nro_documento,
                    'razon_social' => $request->razon_social,
                    'direccion_fiscal' => $request->direccion_fiscal,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_contribuyente'
            );
        }
        $cli = DB::table('comercial.com_cliente')
            ->select('com_cliente.*','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('com_cliente.id_contribuyente',$id_contribuyente)
            ->first();
        
        if ($cli !== null){
            $id_cliente = $cli->id_cliente;
        } 
        else {
            $id_cliente = DB::table('comercial.com_cliente')->insertGetId(
                [
                    'id_contribuyente' => $id_contribuyente,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_cliente'
            );
            $cli = DB::table('comercial.com_cliente')
            ->select('com_cliente.*','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('com_cliente.id_cliente',$id_cliente)
            ->first();
        }
        return response()->json($cli);
    }

    public function subir_partida_cd($id_partida){
        $cid = DB::table('proyectos.proy_cd_partida')
        ->where('id_partida',$id_partida)
        ->first();
        $codigo = $cid->codigo;
        //obtiene ultimo numero y resta -1
        $nuevo = intval(substr($cid->codigo,-2,2)) - 1;
        $update = 0;

        if ($nuevo > 0){
            //obtiene el codigo
            $padre = substr($cid->codigo,0,strlen($cid->codigo)-2);
            $nuevo_codigo = $padre.$this->leftZero(2,$nuevo);
            
            //obtener el anterior y restarle una posicion
            $ant = DB::table('proyectos.proy_cd_partida')
            ->where([['id_cd','=',$cid->id_cd],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();

            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$ant->id_partida)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            $anterior = intval(substr($cid->codigo,-5,2));
            //resta ultimo numero
            $nuevo = $anterior - 1;
            //obtiene el codigo
            $nuevo_codigo = substr($cid->codigo,0,strlen($cid->codigo)-5);
            $nue_padre = $nuevo_codigo.$this->leftZero(2,$nuevo);
            $padre_anterior = substr($cid->codigo,0,strlen($cid->codigo)-3);
            //obtiene el ultimo hijo del nuevo padre
            $titulo = DB::table('proyectos.proy_cd_compo')
            ->where([['codigo','like',$nue_padre.'%'],['estado','=',1],['id_cd','=',$cid->id_cd]])
            ->orderBy('codigo','desc')
            ->first();

            if (isset($titulo)){
                //obtener el anterior y sumarle una posicion
                $count = DB::table('proyectos.proy_cd_partida')
                ->where([['cod_compo','=',$titulo->codigo],['estado','=',1],['id_cd','=',$cid->id_cd]])
                ->count();
                //genera nuevo codigo
                $cod = $titulo->codigo.'.'.$this->leftZero(2,($count+1));
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $cod,
                          'cod_compo' => $titulo->codigo]);
                // actualiza hijos del padre anterior
                $hijos = DB::table('proyectos.proy_cd_partida')
                ->where([['cod_compo','=',$padre_anterior],['estado','=',1],['id_cd','=',$cid->id_cd]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 0;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.$this->leftZero(2,($i));
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_cd_partida')
                    ->where('id_partida',$h->id_partida)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
            }
        }
        return response()->json($update);
    }

    public function bajar_partida_cd($id_partida){
        $cid = DB::table('proyectos.proy_cd_partida')
        ->where('id_partida',$id_partida)
        ->first();
        //codigo actual
        $codigo = $cid->codigo;
        //obtiene ultimo numero 
        $ultimo = intval(substr($cid->codigo,-2,2));
        $update = 0;
        $padre = substr($cid->codigo,0,strlen($cid->codigo)-3);
        //cuenta los hijos
        $count = DB::table('proyectos.proy_cd_partida')
            ->where([['cod_compo','=',$padre],['estado','=',1],['id_cd','=',$cid->id_cd]])
            ->count();
        //si el codigo actual es menor que la cantidad de partidas
        if ($ultimo < $count){
            //suma uno al numero
            $nuevo = $ultimo + 1;
            //genera el nuevo codigo
            $nuevo_codigo = $padre.'.'.$this->leftZero(2,$nuevo);
            //obtener el anterior
            $ant = DB::table('proyectos.proy_cd_partida')
            ->where([['id_cd','=',$cid->id_cd],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();
            //verifica si existe el anterior
            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$ant->id_partida)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            //obtiene padre actual
            $padre_actual = intval(substr($cid->codigo,-5,2));
            //suma al padre
            $nue = $padre_actual + 1;
            //obtiene el codigo
            $nuevo_padre = substr($cid->codigo,0,strlen($cid->codigo)-5).$this->leftZero(2,$nue);

            $count_nuevo_padre = DB::table('proyectos.proy_cd_compo')
            ->where([['codigo','=',$nuevo_padre],['estado','=',1],['id_cd','=',$cid->id_cd]])
            ->count();

            if ($count_nuevo_padre > 0){
                //genera nuevo codigo hijo
                $nuevo_codigo = $nuevo_padre.'.01';
                //actualiza los hijos del nuevo padre
                $hijos = DB::table('proyectos.proy_cd_partida')
                ->where([['cod_compo','=',$nuevo_padre],['estado','=',1],['id_cd','=',$cid->id_cd]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 1;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.$this->leftZero(2,$i);
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_cd_partida')
                    ->where('id_partida',$h->id_partida)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $nuevo_codigo,
                          'cod_compo' => $nuevo_padre]);
            }
            else {
                //obtiene abuelo actual
                $abuelo_actual = intval(substr($cid->codigo,-8,2));
                //suma al abuelo
                $nue_abu = $abuelo_actual + 1;
                //obtiene el codigo
                $nuevo_abuelo = substr($cid->codigo,0,strlen($cid->codigo)-8).$this->leftZero(2,$nue_abu);

                $count_nuevo_abuelo = DB::table('proyectos.proy_cd_compo')
                ->where([['codigo','=',$nuevo_abuelo],['estado','=',1],['id_cd','=',$cid->id_cd]])
                ->count();

                if ($count_nuevo_abuelo > 0){
                    //genera nuevo codigo hijo
                    $nuevo_codigo = $nuevo_abuelo.'.'.$this->leftZero(2,1);
                    //actualiza los hijos del nuevo padre
                    $hijos = DB::table('proyectos.proy_cd_partida')
                    ->where([['cod_compo','=',$nuevo_abuelo],['estado','=',1],['id_cd','=',$cid->id_cd]])
                    ->orderBy('codigo','asc')
                    ->get();
                    
                    $i = 1;
                    foreach($hijos as $h){
                        $i++;
                        $c = substr($h->codigo,0,strlen($h->codigo)-3);
                        $nuevo_hijo = $c.'.'.$this->leftZero(2,$i);
                        //actualiza nuevo codigo
                        DB::table('proyectos.proy_cd_partida')
                        ->where('id_partida',$h->id_partida)
                        ->update(['codigo'=>$nuevo_hijo]);
                    }
                    // actualiza el codigo actual
                    $update = DB::table('proyectos.proy_cd_partida')
                    ->where('id_partida',$id_partida)
                    ->update(['codigo' => $nuevo_codigo,
                              'cod_compo' => $nuevo_abuelo]);
                }
            }
        }
        return response()->json($update);
    }

    public function subir_partida_ci($id_ci_detalle){
        $cid = DB::table('proyectos.proy_ci_detalle')
        ->where('id_ci_detalle',$id_ci_detalle)
        ->first();
        $codigo = $cid->codigo;
        //obtiene ultimo numero y resta -1
        $nuevo = intval(substr($cid->codigo,-2,2)) - 1;
        $update = 0;

        if ($nuevo > 0){
            //obtiene el codigo
            $padre = substr($cid->codigo,0,strlen($cid->codigo)-2);
            $nuevo_codigo = $padre.$this->leftZero(2,$nuevo);
            
            //obtener el anterior y restarle una posicion
            $ant = DB::table('proyectos.proy_ci_detalle')
            ->where([['id_ci','=',$cid->id_ci],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();

            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$ant->id_ci_detalle)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$id_ci_detalle)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            $anterior = intval(substr($cid->codigo,-5,2));
            //resta ultimo numero
            $nuevo = $anterior - 1;
            //obtiene el codigo
            $nuevo_codigo = substr($cid->codigo,0,strlen($cid->codigo)-5);
            $nue_padre = $nuevo_codigo.$this->leftZero(2,$nuevo);
            $padre_anterior = substr($cid->codigo,0,strlen($cid->codigo)-3);
            //obtiene el ultimo hijo del nuevo padre
            $titulo = DB::table('proyectos.proy_ci_compo')
            ->where([['codigo','like',$nue_padre.'%'],['estado','=',1],['id_ci','=',$cid->id_ci]])
            ->orderBy('codigo','desc')
            ->first();

            if (isset($titulo)){
                //obtener el anterior y sumarle una posicion
                $count = DB::table('proyectos.proy_ci_detalle')
                ->where([['cod_compo','=',$titulo->codigo],['estado','=',1],['id_ci','=',$cid->id_ci]])
                ->count();
                //genera nuevo codigo
                $cod = $titulo->codigo.'.'.$this->leftZero(2,($count+1));
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$id_ci_detalle)
                ->update(['codigo' => $cod,
                            'cod_compo' => $titulo->codigo]);
                // actualiza hijos del padre anterior
                $hijos = DB::table('proyectos.proy_ci_detalle')
                ->where([['cod_compo','=',$padre_anterior],['estado','=',1],['id_ci','=',$cid->id_ci]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 0;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.$this->leftZero(2,($i));
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_ci_detalle')
                    ->where('id_ci_detalle',$h->id_ci_detalle)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
            }
        }
        return response()->json($update);
    }

    public function bajar_partida_ci($id_ci_detalle){
        $cid = DB::table('proyectos.proy_ci_detalle')
        ->where('id_ci_detalle',$id_ci_detalle)
        ->first();
        //codigo actual
        $codigo = $cid->codigo;
        //obtiene ultimo numero 
        $ultimo = intval(substr($cid->codigo,-2,2));
        $update = 0;
        $padre = substr($cid->codigo,0,strlen($cid->codigo)-3);
        //cuenta los hijos
        $count = DB::table('proyectos.proy_ci_detalle')
            ->where([['cod_compo','=',$padre],['estado','=',1],['id_ci','=',$cid->id_ci]])
            ->count();
        //si el codigo actual es menor que la cantidad de partidas
        if ($ultimo < $count){
            //suma uno al numero
            $nuevo = $ultimo + 1;
            //genera el nuevo codigo
            $nuevo_codigo = $padre.'.'.$this->leftZero(2,$nuevo);
            //obtener el anterior
            $ant = DB::table('proyectos.proy_ci_detalle')
            ->where([['id_ci','=',$cid->id_ci],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();
            //verifica si existe el anterior
            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$ant->id_ci_detalle)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$id_ci_detalle)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            //obtiene padre actual
            $padre_actual = intval(substr($cid->codigo,-5,2));
            //suma al padre
            $nue = $padre_actual + 1;
            //obtiene el codigo
            $nuevo_padre = substr($cid->codigo,0,strlen($cid->codigo)-5).$this->leftZero(2,$nue);

            $count_nuevo_padre = DB::table('proyectos.proy_ci_compo')
            ->where([['codigo','=',$nuevo_padre],['estado','=',1],['id_ci','=',$cid->id_ci]])
            ->count();

            if ($count_nuevo_padre > 0){
                //genera nuevo codigo hijo
                $nuevo_codigo = $nuevo_padre.'.01';
                //actualiza los hijos del nuevo padre
                $hijos = DB::table('proyectos.proy_ci_detalle')
                ->where([['cod_compo','=',$nuevo_padre],['estado','=',1],['id_ci','=',$cid->id_ci]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 1;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.$this->leftZero(2,$i);
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_ci_detalle')
                    ->where('id_ci_detalle',$h->id_ci_detalle)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$id_ci_detalle)
                ->update(['codigo' => $nuevo_codigo,
                          'cod_compo' => $nuevo_padre]);
            }
            else {
                //obtiene abuelo actual
                $abuelo_actual = intval(substr($cid->codigo,-8,2));
                //suma al abuelo
                $nue_abu = $abuelo_actual + 1;
                //obtiene el codigo
                $nuevo_abuelo = substr($cid->codigo,0,strlen($cid->codigo)-8).$this->leftZero(2,$nue_abu);

                $count_nuevo_abuelo = DB::table('proyectos.proy_ci_compo')
                ->where([['codigo','=',$nuevo_abuelo],['estado','=',1],['id_ci','=',$cid->id_ci]])
                ->count();

                if ($count_nuevo_abuelo > 0){
                    //genera nuevo codigo hijo
                    $nuevo_codigo = $nuevo_abuelo.'.'.$this->leftZero(2,1);
                    //actualiza los hijos del nuevo padre
                    $hijos = DB::table('proyectos.proy_ci_detalle')
                    ->where([['cod_compo','=',$nuevo_abuelo],['estado','=',1],['id_ci','=',$cid->id_ci]])
                    ->orderBy('codigo','asc')
                    ->get();
                    
                    $i = 1;
                    foreach($hijos as $h){
                        $i++;
                        $c = substr($h->codigo,0,strlen($h->codigo)-3);
                        $nuevo_hijo = $c.'.'.$this->leftZero(2,$i);
                        //actualiza nuevo codigo
                        DB::table('proyectos.proy_ci_detalle')
                        ->where('id_ci_detalle',$h->id_ci_detalle)
                        ->update(['codigo'=>$nuevo_hijo]);
                    }
                    // actualiza el codigo actual
                    $update = DB::table('proyectos.proy_ci_detalle')
                    ->where('id_ci_detalle',$id_ci_detalle)
                    ->update(['codigo' => $nuevo_codigo,
                              'cod_compo' => $nuevo_abuelo]);
                }
            }
        }
        return response()->json($update);
    }

    public function subir_partida_gg($id_gg_detalle){
        $cid = DB::table('proyectos.proy_gg_detalle')
        ->where('id_gg_detalle',$id_gg_detalle)
        ->first();
        $codigo = $cid->codigo;
        //obtiene ultimo numero y resta -1
        $nuevo = intval(substr($cid->codigo,-2,2)) - 1;
        $update = 0;

        if ($nuevo > 0){
            //obtiene el codigo
            $padre = substr($cid->codigo,0,strlen($cid->codigo)-2);
            $nuevo_codigo = $padre.$this->leftZero(2,$nuevo);
            
            //obtener el anterior y sumarle una posicion
            $ant = DB::table('proyectos.proy_gg_detalle')
            ->where([['id_gg','=',$cid->id_gg],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();

            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$ant->id_gg_detalle)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$id_gg_detalle)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            $anterior = intval(substr($cid->codigo,-5,2));
            //resta ultimo numero
            $nuevo = $anterior - 1;
            //obtiene el codigo
            $nuevo_codigo = substr($cid->codigo,0,strlen($cid->codigo)-5);
            $nue_padre = $nuevo_codigo.$this->leftZero(2,$nuevo);
            $padre_anterior = substr($cid->codigo,0,strlen($cid->codigo)-3);
            //obtiene el ultimo hijo del nuevo padre
            $titulo = DB::table('proyectos.proy_gg_compo')
            ->where([['codigo','like',$nue_padre.'%'],['estado','=',1],['id_gg','=',$cid->id_gg]])
            ->orderBy('codigo','desc')
            ->first();
            
            if (isset($titulo)){
                //obtener el anterior y sumarle una posicion
                $count = DB::table('proyectos.proy_gg_detalle')
                ->where([['cod_compo','=',$titulo->codigo],['estado','=',1],['id_gg','=',$cid->id_gg]])
                ->count();
                
                $cod = $titulo->codigo.'.'.$this->leftZero(2,($count+1));
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$id_gg_detalle)
                ->update(['codigo' => $cod,
                          'cod_compo' => $titulo->codigo]);
                // actualiza hijos del padre anterior
                $hijos = DB::table('proyectos.proy_gg_detalle')
                ->where([['cod_compo','=',$padre_anterior],['estado','=',1],['id_gg','=',$cid->id_gg]])
                ->orderBy('codigo','asc')
                ->get();

                $i = 0;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.$this->leftZero(2,($i));
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_gg_detalle')
                    ->where('id_gg_detalle',$h->id_gg_detalle)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
            }
        }
        return response()->json($update);
    }

    public function bajar_partida_gg($id_gg_detalle){
        $cid = DB::table('proyectos.proy_gg_detalle')
        ->where('id_gg_detalle',$id_gg_detalle)
        ->first();
        //codigo actual
        $codigo = $cid->codigo;
        //obtiene ultimo numero 
        $ultimo = intval(substr($cid->codigo,-2,2));
        $update = 0;
        $padre = substr($cid->codigo,0,strlen($cid->codigo)-3);
        //cuenta los hijos
        $count = DB::table('proyectos.proy_gg_detalle')
            ->where([['cod_compo','=',$padre],['estado','=',1],['id_gg','=',$cid->id_gg]])
            ->count();
        //si el codigo actual es menor que la cantidad de partidas
        if ($ultimo < $count){
            //suma uno al numero
            $nuevo = $ultimo + 1;
            //genera el nuevo codigo
            $nuevo_codigo = $padre.'.'.$this->leftZero(2,$nuevo);
            //obtener el anterior
            $ant = DB::table('proyectos.proy_gg_detalle')
            ->where([['id_gg','=',$cid->id_gg],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();
            //verifica si existe el anterior
            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$ant->id_gg_detalle)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$id_gg_detalle)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            //obtiene padre actual
            $padre_actual = intval(substr($cid->codigo,-5,2));
            //suma al padre
            $nue = $padre_actual + 1;
            //obtiene el codigo
            $nuevo_padre = substr($cid->codigo,0,strlen($cid->codigo)-5).$this->leftZero(2,$nue);

            $count_nuevo_padre = DB::table('proyectos.proy_gg_compo')
            ->where([['codigo','=',$nuevo_padre],['estado','=',1],['id_gg','=',$cid->id_gg]])
            ->count();
            
            if ($count_nuevo_padre > 0){
                //genera nuevo codigo hijo
                $nuevo_codigo = $nuevo_padre.'.01';
                // actualiza los hijos del nuevo padre
                $hijos = DB::table('proyectos.proy_gg_detalle')
                ->where([['cod_compo','=',$nuevo_padre],['estado','=',1],['id_gg','=',$cid->id_gg]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 1;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.$this->leftZero(2,$i);
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_gg_detalle')
                    ->where('id_gg_detalle',$h->id_gg_detalle)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$id_gg_detalle)
                ->update(['codigo' => $nuevo_codigo,
                          'cod_compo' => $nuevo_padre]);
            }
            else {
                //obtiene abuelo actual
                $abuelo_actual = intval(substr($cid->codigo,-8,2));
                //suma al abuelo
                $nue_abu = $abuelo_actual + 1;
                //obtiene el codigo
                $nuevo_abuelo = substr($cid->codigo,0,strlen($cid->codigo)-8).$this->leftZero(2,$nue_abu);

                $count_nuevo_abuelo = DB::table('proyectos.proy_gg_compo')
                ->where([['codigo','=',$nuevo_abuelo],['estado','=',1],['id_gg','=',$cid->id_gg]])
                ->count();

                if ($count_nuevo_abuelo > 0){
                    //genera nuevo codigo hijo
                    $nuevo_codigo = $nuevo_abuelo.'.01';
                    //actualiza los hijos del nuevo padre
                    $hijos = DB::table('proyectos.proy_gg_detalle')
                    ->where([['cod_compo','=',$nuevo_abuelo],['estado','=',1],['id_gg','=',$cid->id_gg]])
                    ->orderBy('codigo','asc')
                    ->get();
                    
                    $i = 1;
                    foreach($hijos as $h){
                        $i++;
                        $c = substr($h->codigo,0,strlen($h->codigo)-3);
                        $nuevo_hijo = $c.'.'.$this->leftZero(2,$i);
                        //actualiza nuevo codigo
                        DB::table('proyectos.proy_gg_detalle')
                        ->where('id_gg_detalle',$h->id_gg_detalle)
                        ->update(['codigo'=>$nuevo_hijo]);
                    }
                    // actualiza el codigo actual
                    $update = DB::table('proyectos.proy_gg_detalle')
                    ->where('id_gg_detalle',$id_gg_detalle)
                    ->update(['codigo' => $nuevo_codigo,
                              'cod_compo' => $nuevo_abuelo]);
                }
            }
        }
        return response()->json($update);
    }

    public function subir_partida($id_partida){
        $cid = DB::table('finanzas.presup_par')
        ->where('id_partida',$id_partida)
        ->first();
        $codigo = $cid->codigo;
        //obtiene ultimo numero y resta -1
        $nuevo = intval(substr($cid->codigo,-2,2)) - 1;
        $update = 0;

        if ($nuevo > 0){
            //obtiene el codigo
            $padre = substr($cid->codigo,0,strlen($cid->codigo)-2);
            $nuevo_codigo = $padre.$this->leftZero(2,$nuevo);
            
            //obtener el anterior y sumarle una posicion
            $ant = DB::table('finanzas.presup_par')
            ->where([['id_presup','=',$cid->id_presup],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','!=',7]])
            ->first();

            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('finanzas.presup_par')
                ->where('id_partida',$ant->id_partida)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('finanzas.presup_par')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            $anterior = intval(substr($cid->codigo,-5,2));
            //resta ultimo numero
            $nuevo = $anterior - 1;
            //obtiene el codigo
            $nuevo_codigo = substr($cid->codigo,0,strlen($cid->codigo)-5);
            $nue_padre = $nuevo_codigo.$this->leftZero(2,$nuevo);
            $padre_anterior = substr($cid->codigo,0,strlen($cid->codigo)-3);

            //obtener el anterior y sumarle una posicion
            $count = DB::table('finanzas.presup_par')
            ->where([['cod_padre','=',$nue_padre],
                    ['estado','!=',7],
                    ['id_presup','=',$cid->id_presup]])
            ->count();
            
            $cod = $nue_padre.'.'.$this->leftZero(2,($count+1));

            if (isset($cod)){
                // actualiza el codigo actual
                $update = DB::table('finanzas.presup_par')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $cod,
                          'cod_padre' => $nue_padre]);
                // actualiza hijos del padre anterior
                $hijos = DB::table('finanzas.presup_par')
                ->where([['cod_padre','=',$padre_anterior],
                        ['estado','!=',7],
                        ['id_presup','=',$cid->id_presup]])
                ->orderBy('codigo','asc')
                ->get();

                $i = 0;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.$this->leftZero(2,($i));
                    //actualiza nuevo codigo
                    DB::table('finanzas.presup_par')
                    ->where('id_partida',$h->id_partida)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
            }
        }
        return response()->json($update);
    }

    public function bajar_partida($id_partida){
        $cid = DB::table('finanzas.presup_par')
        ->where('id_partida',$id_partida)
        ->first();
        //codigo actual
        $codigo = $cid->codigo;
        //obtiene ultimo numero 
        $ultimo = intval(substr($cid->codigo,-2,2));
        $update = 0;
        $padre = substr($cid->codigo,0,strlen($cid->codigo)-3);
        //cuenta los hijos
        $count = DB::table('finanzas.presup_par')
            ->where([['cod_padre','=',$padre],['estado','!=',7],['id_presup','=',$cid->id_presup]])
            ->count();
        //si el codigo actual es menor que la cantidad de partidas
        if ($ultimo < $count){
            //suma uno al numero
            $nuevo = $ultimo + 1;
            //genera el nuevo codigo
            $nuevo_codigo = $padre.'.'.$this->leftZero(2,$nuevo);
            //obtener el anterior
            $ant = DB::table('finanzas.presup_par')
            ->where([['id_presup','=',$cid->id_presup],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','!=',7]])
            ->first();
            //verifica si existe el anterior
            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('finanzas.presup_par')
                ->where('id_partida',$ant->id_partida)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('finanzas.presup_par')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            //obtiene padre actual
            $padre_actual = intval(substr($cid->codigo,-5,2));
            //suma al padre
            $nue = $padre_actual + 1;
            //obtiene el codigo
            $nuevo_padre = substr($cid->codigo,0,strlen($cid->codigo)-5).$this->leftZero(2,$nue);
            $nuevo_codigo = $nuevo_padre.'.'.$this->leftZero(2,1);
            
            if (isset($nuevo_codigo)){
                // actualiza los hijos del nuevo padre
                $hijos = DB::table('finanzas.presup_par')
                ->where([['cod_padre','=',$nuevo_padre],['estado','!=',7],['id_presup','=',$cid->id_presup]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 1;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.$this->leftZero(2,$i);
                    //actualiza nuevo codigo
                    DB::table('finanzas.presup_par')
                    ->where('id_partida',$h->id_partida)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
                // actualiza el codigo actual
                $update = DB::table('finanzas.presup_par')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $nuevo_codigo,
                          'cod_padre' => $nuevo_padre]);
            }
        }
        return response()->json($update);
    }

    public function listar_opciones_todo(){
        $opciones = DB::table('proyectos.proy_op_com')
            ->select('proy_op_com.*', 'proy_tp_proyecto.descripcion as des_tp_proyecto',
            'proy_unid_program.descripcion as des_program','adm_contri.razon_social',
            'adm_contri.id_contribuyente','sis_usua.nombre_corto','proy_modalidad.descripcion as des_modalidad',
            'adm_estado_doc.estado_doc')
            ->join('proyectos.proy_tp_proyecto','proy_tp_proyecto.id_tp_proyecto','=','proy_op_com.tp_proyecto')
            ->leftjoin('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_op_com.unid_program')
            ->leftjoin('proyectos.proy_modalidad','proy_modalidad.id_modalidad','=','proy_op_com.modalidad')
            ->join('comercial.com_cliente','com_cliente.id_cliente','=','proy_op_com.cliente')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_op_com.elaborado_por')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_op_com.estado')
                ->where([['proy_op_com.estado', '!=', 7]])
                ->orderBy('proy_op_com.fecha_emision')
                ->get();

        $lista = [];

        foreach($opciones as $op){
            $proyecto = DB::table('proyectos.proy_proyecto')
            ->where([['id_op_com','=',$op->id_op_com],
                     ['estado','!=',7]])//Distinto de Anulado
            ->first();

            $presint = DB::table('proyectos.proy_presup')
            ->where([['id_op_com','=',$op->id_op_com],
                     ['id_tp_presupuesto','=',1],//Pres. Interno
                     ['estado','=',8]])//Emitido
            ->first();

            $propuesta = DB::table('finanzas.presup')
            ->where([['id_op_com','=',$op->id_op_com],
                     ['tp_presup','=',3],//Propuesta
                     ['estado','!=',7]])
            ->first();

            $preseje = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*','proy_presup_importe.total_presupuestado',
            'proy_presup_importe.sub_total','proy_presup_importe.total_igv')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->where([['proy_presup.id_op_com','=',$op->id_op_com],
                     ['proy_presup.id_tp_presupuesto','=',2],//Pres. Ejecucion         
                     ['proy_presup.estado','=',8]])//Emitido
            ->first();

            $total_req = 0;
            $total_oc_sin_igv = 0;
            $total_oc_con_igv = 0;

            if (isset($preseje)){
                if ($preseje->id_presup !== null){

                    $partidas_eje = DB::table('finanzas.presup_par')
                    ->where([['id_presup','=',$preseje->id_presup],
                             ['estado','!=',7]])
                    ->get();

                    if (isset($partidas_eje)){
                        foreach($partidas_eje as $par){

                            $det_req = DB::table('almacen.alm_det_req')
                            ->select(DB::raw('SUM(alm_det_req.precio_referencial * alm_det_req.cantidad) as suma_req'))
                            ->where([['alm_det_req.partida','=',$par->id_partida],['alm_det_req.estado','!=',7]])
                            ->first();
                            if (isset($det_req)){
                                $total_req += $det_req->suma_req;
                            }

                            $det_oc = DB::table('almacen.alm_det_req')
                            ->select(DB::raw('SUM(log_valorizacion_cotizacion.precio_sin_igv) as suma_sin_igv'),
                            DB::raw('SUM(log_valorizacion_cotizacion.subtotal) as suma_con_igv'))
                            ->join('logistica.valoriza_coti_detalle','valoriza_coti_detalle.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
                            ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','valoriza_coti_detalle.id_valorizacion_cotizacion')
                            ->where([['alm_det_req.partida','=',$par->id_partida],
                                    ['valoriza_coti_detalle.estado','=',1],
                                    ['log_valorizacion_cotizacion.estado','!=',7],
                                    ['alm_det_req.estado','!=',7]])
                            ->first();
                            
                            if (isset($det_oc)){
                                $total_oc_sin_igv += $det_oc->suma_sin_igv;
                                $total_oc_con_igv += $det_oc->suma_con_igv;
                            }
                        }
                    }
                }
            }

            $nuevo = [
                'id_op_com'=>$op->id_op_com,
                'codigo'=>$op->codigo,
                'descripcion'=>$op->descripcion,
                'fecha_emision'=>$op->fecha_emision,
                'estado_doc'=>$op->estado_doc,
                'total_req'=>$total_req,
                'total_oc_sin_igv'=>$total_oc_sin_igv,
                'total_oc_con_igv'=>$total_oc_con_igv,
                'cod_presint'=>(isset($presint) ? $presint->codigo : ''),
                'cod_propuesta'=>(isset($propuesta) ? $propuesta->codigo : ''),
                'cod_preseje'=>(isset($preseje) ? $preseje->codigo : ''),
                'cod_proyecto'=>(isset($proyecto) ? $proyecto->codigo : ''),
                'id_presint'=>(isset($presint) ? $presint->id_presupuesto : ''),
                'id_propuesta'=>(isset($propuesta) ? $propuesta->id_presup : ''),
                'id_preseje'=>(isset($preseje) ? $preseje->id_presupuesto : ''),
                'id_proyecto'=>(isset($proyecto) ? $proyecto->id_proyecto : ''),
                'sub_total'=>(isset($preseje) ? $preseje->sub_total : ''),
                'total_igv'=>(isset($preseje) ? $preseje->total_igv : ''),
                'total_presupuestado'=>(isset($preseje) ? $preseje->total_presupuestado : ''),
                // 'id_proyecto'=>(isset($proyecto) ? $proyecto->id_proyecto : ''),
            ];
            array_push($lista, $nuevo);

            // $html.='
            // <tr id="'.$op->id_op_com.'">
            //     <td>'.$op->codigo.'</td>
            //     <td>'.$op->descripcion.'</td>
            //     <td>'.$op->fecha_emision.'</td>
            //     <td>'.(isset($presint) ? $presint->codigo : '').'</td>
            //     <td>'.(isset($propuesta) ? $propuesta->codigo : '').'</td>
            //     <td>'.(isset($preseje) ? $preseje->codigo : '').'</td>
            //     <td>'.(isset($proyecto) ? $proyecto->codigo : '').'</td>
            //     <td></td>
            // </tr>';
        }

        $output['data'] = $lista;
        return response()->json($output);
        // return json_encode($html);
    }

    public function listar_estructuras_preseje(){
        $presup = DB::table('finanzas.presup')
        ->select('presup.*','sis_moneda.simbolo','adm_contri.razon_social')
        ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'presup.moneda')
        ->join('proyectos.proy_presup', 'proy_presup.id_presup', '=', 'presup.id_presup')
        ->join('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'proy_presup.id_proyecto')
        ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'proy_proyecto.cliente')
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
        ->where([['presup.tp_presup','=',4],['presup.estado','!=',7]])//Presup Ejec
        ->get();
        $output['data'] = $presup;
        return response()->json($output);
    }

    public function ver_detalle_partida($id_partida)
    {
        $det_req = DB::table('almacen.alm_det_req')
        ->select('alm_det_req.*','moneda_req.simbolo as moneda_req','log_det_ord_compra.precio as precio_sin_igv',
        'log_det_ord_compra.cantidad as cantidad_cotizada','alm_req.codigo as cod_req','alm_req.concepto','alm_req.fecha_requerimiento',
        'log_ord_compra.id_orden_compra','log_ord_compra.codigo as cod_orden','log_ord_compra.fecha as fecha_orden',
        'adm_contri.nro_documento','adm_contri.razon_social','sis_moneda.simbolo as moneda_oc')
        ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->join('configuracion.sis_moneda as moneda_req','moneda_req.id_moneda','=','alm_req.id_moneda')
        // ->leftjoin('logistica.valoriza_coti_detalle','valoriza_coti_detalle.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
        // ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','valoriza_coti_detalle.id_valorizacion_cotizacion')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_valorizacion_cotizacion','=','alm_det_req.id_detalle_requerimiento')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
        ->where([['alm_det_req.partida','=',($id_partida)],
                 ['alm_det_req.estado','!=',7]])
                //  ['valoriza_coti_detalle.estado','=',1],
                //  ['log_valorizacion_cotizacion.estado','!=',7]])
        ->get();
        $output['data'] = $det_req;
        return response()->json($output);
    }

    public function nueva_valorizacion($id_presup){
        $presup = DB::table('finanzas.presup')
        ->select('presup.*','sis_moneda.simbolo','proy_res_proy.id_residente',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_residente"))
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','presup.moneda')
        ->leftjoin('proyectos.proy_res_proy','proy_res_proy.id_proyecto','=','presup.id_proyecto')
        ->leftjoin('proyectos.proy_residente','proy_residente.id_residente','=','proy_res_proy.id_residente')
        ->leftjoin('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','proy_residente.id_trabajador')
        ->leftjoin('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        ->where('id_presup',$id_presup)
        ->first();

        $periodo = DB::table('proyectos.presup_periodos')
        ->where([['id_presup','=',$id_presup],['estado','=',1]])
        ->orderBy('numero','asc')->first();

        $partidas = DB::table('finanzas.presup_par')
        ->select('presup_par.*','presup.fecha_emision','alm_und_medida.abreviatura')
        ->join('finanzas.presup','presup.id_presup','=','presup_par.id_presup')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','presup_par.unidad_medida')
        ->where([['presup_par.id_presup', '=', $id_presup],
                 ['presup_par.estado', '!=', 7]])
        ->orderBy('presup_par.codigo')
        ->get();

        $titulos = DB::table('finanzas.presup_titu')
        ->select('presup_titu.*')
        ->where([['presup_titu.id_presup', '=', $id_presup],
                 ['presup_titu.estado', '!=', 7]])
        ->orderBy('presup_titu.codigo')
        ->get();

        $lista = [];
        $list_par = [];
        
        foreach($titulos as $ti){
            foreach($partidas as $par){
                if ($ti->codigo == $par->cod_padre){
                    //obtiene la ultima valorizacion
                    $valori = DB::table('proyectos.proy_valori_par')
                    ->select(DB::raw('sum(proy_valori_par.avance_metrado) as avance_metrado'))
                    ->join('proyectos.proy_valori','proy_valori.id_valorizacion','=','proy_valori_par.id_valorizacion')
                    ->where([['proy_valori_par.id_partida','=',$par->id_partida],
                             ['proy_valori_par.estado','!=',7],
                             ['proy_valori.numero','<',$periodo->numero]])
                    ->first();

                    $nuevo_par = [
                        'id_partida' => $par->id_partida,
                        'codigo' => $par->codigo,
                        'descripcion' => $par->descripcion,
                        'metrado' => $par->metrado,
                        'abreviatura' => $par->abreviatura,
                        'importe_unitario' => $par->importe_unitario,
                        'importe_total' => $par->importe_total,
                        'avance_anterior' => ($valori->avance_metrado !== null ? $valori->avance_metrado : 0),
                        'avance_actual' => 0
                    ];
                    array_push($list_par, $nuevo_par);
                }
            }
            $nuevo_titulo = [
                'id_titulo' => $ti->id_titulo,
                'codigo' => $ti->codigo,
                'descripcion' => $ti->descripcion,
                'cod_padre' => $ti->cod_padre,
                'total' => $ti->total,
                'partidas' => $list_par
            ];
            array_push($lista, $nuevo_titulo);
            $list_par = [];
        }

        foreach($partidas as $par){
            if ($par->cod_padre == null){
                //obtiene la ultima valorizacion
                $valori = DB::table('proyectos.proy_valori_par')
                ->select(DB::raw('sum(proy_valori_par.avance_metrado) as avance_metrado'))
                ->join('proyectos.proy_valori','proy_valori.id_valorizacion','=','proy_valori_par.id_valorizacion')
                ->where([['proy_valori_par.id_partida','=',$par->id_partida],
                         ['proy_valori_par.estado','!=',7],
                         ['proy_valori.numero','<',$periodo->numero]])
                ->first();

                $nuevo_par = [
                    'id_partida' => $par->id_partida,
                    'codigo' => $par->codigo,
                    'descripcion' => $par->descripcion,
                    'metrado' => $par->metrado,
                    'abreviatura' => $par->abreviatura,
                    'importe_unitario' => $par->importe_unitario,
                    'importe_total' => $par->importe_total,
                    'avance_anterior' => ($valori->avance_metrado !== null ? $valori->avance_metrado : 0),
                    'avance_actual' => 0
                ];
                array_push($lista, $nuevo_par);
            } 
        }
        
        return response()->json(['periodo'=>$periodo,'presup'=>$presup,'lista'=>$lista]);
    }

    public function listar_valorizaciones()
    {
        $data = DB::table('proyectos.proy_valori')
        ->select('proy_valori.*','presup.codigo','presup.descripcion',
        'presup_totales.sub_total','sis_moneda.simbolo')
        ->join('finanzas.presup','presup.id_presup','=','proy_valori.id_presup')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','presup.moneda')
        ->join('finanzas.presup_totales','presup_totales.id_presup','=','proy_valori.id_presup')
        ->where([['proy_valori.estado','!=',7]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_valorizacion($id_valorizacion)
    {
        $presup = DB::table('proyectos.proy_valori')
        ->select('proy_valori.*','presup.codigo','presup.descripcion','sis_moneda.simbolo','proy_res_proy.id_residente',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_residente"))
        ->join('finanzas.presup','presup.id_presup','=','proy_valori.id_presup')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','presup.moneda')
        ->leftjoin('proyectos.proy_res_proy','proy_res_proy.id_proyecto','=','presup.id_proyecto')
        ->leftjoin('proyectos.proy_residente','proy_residente.id_residente','=','proy_res_proy.id_residente')
        ->leftjoin('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','proy_residente.id_trabajador')
        ->leftjoin('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        ->where('id_valorizacion',$id_valorizacion)
        ->first();

        $periodo = DB::table('proyectos.presup_periodos')
        ->where([['id_presup','=',$presup->id_presup],
                 ['numero','=',$presup->numero]])->first();

        $partidas = DB::table('finanzas.presup_par')
        ->select('presup_par.*','presup.fecha_emision','alm_und_medida.abreviatura')
        ->join('finanzas.presup','presup.id_presup','=','presup_par.id_presup')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','presup_par.unidad_medida')
        ->where([['presup_par.id_presup', '=', $presup->id_presup],
                 ['presup_par.estado', '!=', 7]])
        ->orderBy('presup_par.codigo')
        ->get();

        $titulos = DB::table('finanzas.presup_titu')
        ->select('presup_titu.*')
        ->where([['presup_titu.id_presup', '=', $presup->id_presup],
                 ['presup_titu.estado', '!=', 7]])
        ->orderBy('presup_titu.codigo')
        ->get();

        $lista = [];
        $list_par = [];
        
        foreach($titulos as $ti){
            foreach($partidas as $par){
                if ($ti->codigo == $par->cod_padre){
                    //obtiene la valorizacion
                    $valori = DB::table('proyectos.proy_valori_par')
                    ->where([['id_valorizacion','=',$id_valorizacion],
                             ['id_partida','=',$par->id_partida],
                             ['estado','=',1]])
                    ->orderBy('id_valori_par','desc')->first();

                    //obtiene la ultima valorizacion
                    $anterior = DB::table('proyectos.proy_valori_par')
                    ->select(DB::raw('sum(proy_valori_par.avance_metrado) as avance_metrado'))
                    ->join('proyectos.proy_valori','proy_valori.id_valorizacion','=','proy_valori_par.id_valorizacion')
                    ->where([['proy_valori_par.id_partida','=',$par->id_partida],
                             ['proy_valori_par.estado','!=',7],
                             ['proy_valori.numero','<',$presup->numero]])
                    ->first();

                    $nuevo_par = [
                        'id_valori_par' => (isset($valori) ? $valori->id_valori_par : 0),
                        'id_partida' => $par->id_partida,
                        'codigo' => $par->codigo,
                        'descripcion' => $par->descripcion,
                        'metrado' => $par->metrado,
                        'abreviatura' => $par->abreviatura,
                        'importe_unitario' => $par->importe_unitario,
                        'importe_total' => $par->importe_total,
                        'avance_anterior' => (isset($anterior) ? floatval($anterior->avance_metrado) : 0),
                        'avance_actual' => (isset($valori) ? floatval($valori->avance_metrado) : 0)
                    ];
                    array_push($list_par, $nuevo_par);
                }
            }
            $nuevo_titulo = [
                'id_titulo' => $ti->id_titulo,
                'codigo' => $ti->codigo,
                'descripcion' => $ti->descripcion,
                'cod_padre' => $ti->cod_padre,
                'total' => $ti->total,
                'partidas' => $list_par
            ];
            array_push($lista, $nuevo_titulo);
            $list_par = [];
        }

        foreach($partidas as $par){
            if ($par->cod_padre == null){
                //obtiene la valorizacion
                $valori = DB::table('proyectos.proy_valori_par')
                ->where([['id_valorizacion','=',$id_valorizacion],
                         ['id_partida','=',$par->id_partida]])
                ->orderBy('id_valori_par','desc')->first();

                //obtiene la ultima valorizacion
                $anterior = DB::table('proyectos.proy_valori_par')
                ->select(DB::raw('sum(proy_valori_par.avance_metrado) as avance_metrado'))
                ->join('proyectos.proy_valori','proy_valori.id_valorizacion','=','proy_valori_par.id_valorizacion')
                ->where([['proy_valori_par.id_partida','=',$par->id_partida],
                         ['proy_valori_par.estado','!=',7],
                         ['proy_valori.numero','<',$presup->numero]])
                ->first();

                $nuevo_par = [
                    'id_valori_par' => (isset($valori) ? $valori->id_valori_par : 0),
                    'id_partida' => $par->id_partida,
                    'codigo' => $par->codigo,
                    'descripcion' => $par->descripcion,
                    'metrado' => $par->metrado,
                    'abreviatura' => $par->abreviatura,
                    'importe_unitario' => $par->importe_unitario,
                    'importe_total' => $par->importe_total,
                    'avance_anterior' => (isset($anterior) ? floatval($anterior->avance_metrado) : 0),
                    'avance_actual' => (isset($valori) ? floatval($valori->avance_metrado) : 0)
                ];
                array_push($lista, $nuevo_par);
            } 
        }
        return response()->json(['total'=>(isset($periodo) ? $periodo->total : 0),'presup'=>$presup,'lista'=>$lista]);
    }

    public function guardar_valorizacion(Request $request)
    {
        $usuario = Auth::user()->id_usuario;
        $id_valorizacion = DB::table('proyectos.proy_valori')->insertGetId(
            [
                'id_presup' => $request->id_presup, 
                'fecha_valorizacion' => $request->fecha_valorizacion, 
                'id_residente' => $request->id_residente, 
                'numero' => $request->numero, 
                'fecha_inicio' => $request->fecha_inicio, 
                'fecha_fin' => $request->fecha_fin, 
                'id_periodo' => $request->id_periodo, 
                'total' => $request->total, 
                'usuario_registro' => $usuario, 
                'estado' => 1, 
                'fecha_registro' => date('Y-m-d H:i:s'), 
            ],
                'id_valorizacion'
            );

        $ids = explode(',',$request->id_valori_par);
        $par = explode(',',$request->id_partida);
        $ava = explode(',',$request->avance_actual);
        $count = count($ids);
        
        for ($i=0; $i<$count; $i++){
            $id = $ids[$i];
            $pa = $par[$i];
            $av = $ava[$i];
            
            DB::table('proyectos.proy_valori_par')
            ->insert([
                'id_valorizacion'=>$id_valorizacion,
                'id_partida'=>$pa,
                'avance_metrado'=>$av,
                'usuario_registro'=>$usuario,
                'fecha_registro'=>date('Y-m-d H:i:s'),
                'estado'=>1
            ]);
        }

        $periodo = DB::table('proyectos.presup_periodos')
        ->where('id_periodo',$request->id_periodo)
        ->update(['estado'=>19]);//Valorizado

        $count = DB::table('proyectos.presup_periodos')
        ->where([['id_presup','=',$request->id_presup],
                 ['estado','!=',7]])
        ->count();

        if ($count == intval($request->numero)){
            DB::table('finanzas.presup')
            ->where('id_presup',$request->id_presup)
            ->update(['estado'=>19]);//Valorizado
        }

        return response()->json($id_valorizacion);
    }
    
    public function update_valorizacion(Request $request)
    {
        $data = DB::table('proyectos.proy_valori')
        ->where('id_valorizacion',$request->id_valorizacion)
        ->update([
            'fecha_valorizacion' => $request->fecha_valorizacion, 
            // 'id_residente' => $request->id_residente, 
            // 'fecha_inicio' => $request->fecha_inicio, 
            // 'fecha_fin' => $request->fecha_fin, 
            // 'id_periodo' => $request->id_periodo, 
            'total' => $request->total, 
        ]);

        $ids = explode(',',$request->id_valori_par);
        $par = explode(',',$request->id_partida);
        $ava = explode(',',$request->avance_actual);
        $count = count($ids);
        $usuario = Auth::user()->id_usuario;

        for ($i=0; $i<$count; $i++){
            $id = $ids[$i];
            $pa = $par[$i];
            $av = $ava[$i];
            
            if ($id !== '0'){
                $data = DB::table('proyectos.proy_valori_par')
                ->where('id_valori_par',$id)
                ->update([ 'avance_metrado'=>$av ]);
            } 
            else {
                $data = DB::table('proyectos.proy_valori_par')
                ->insert([
                    'id_valorizacion'=>$request->id_valorizacion,
                    'id_partida'=>$pa,
                    'avance_metrado'=>$av,
                    'usuario_registro'=>$usuario,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1
                ]);    
            }
        }
        return response()->json($data);
    }

    public function anular_valorizacion($id_valorizacion)
    {
        $data = DB::table('proyectos.proy_valori')
        ->where('id_valorizacion',$id_valorizacion)
        ->update(['estado'=>7]);

        $par_valori = DB::table('proyectos.proy_valori_par')
        ->where([['id_valorizacion','=',$id_valorizacion],
                ['estado','!=',7]])
        ->get();

        foreach ($par_valori as $value) {
            $data = DB::table('proyectos.proy_valori_par')
            ->where('id_valori_par',$value->id_valori_par)
            ->update(['estado'=>7]);
        }

        $valori = DB::table('proyectos.proy_valori')
        ->where('id_valorizacion',$id_valorizacion)->first();

        if (isset($valori)){
            DB::table('proyectos.presup_periodos')
            ->where('id_periodo',$valori->id_periodo)
            ->update(['estado'=>1]);//Elaborado
    
            DB::table('finanzas.presup')
            ->where('id_presup',$valori->id_presup)
            ->update(['estado'=>1]);//Elaborado
        }

        return response()->json($data);
    }

    public function getProgramadoValorizado($id_presup, $id_presupuesto)
    {
        // Debugbar::info($id_presup);
        // Debugbar::info($id_presupuesto);
        $pro_programado = DB::table('proyectos.presup_periodos')
        ->where([['id_presup','=',$id_presup],['estado','!=',7]])
        ->orderBy('numero','asc')
        ->get();

        $pro_valorizado = DB::table('proyectos.proy_valori')
        ->where([['id_presup','=',$id_presup],['estado','!=',7]])
        ->orderBy('numero','asc')
        ->get();
        

        $pres_programado = DB::table('proyectos.proy_presup_periodos')
        ->where([['id_presupuesto','=',$id_presupuesto],['estado','!=',7]])
        ->orderBy('numero','asc')
        ->get();

        $partidas = DB::table('finanzas.presup_par')
        ->select('presup_par.id_partida')
        ->join('proyectos.proy_presup','proy_presup.id_presup','=','presup_par.id_presup')
        ->where([['proy_presup.id_presupuesto','=',$id_presupuesto],
                 ['presup_par.estado','!=',7]])
        ->orderBy('presup_par.codigo')
        ->get();

        $array_partidas = [];

        foreach($partidas as $par){
            array_push($array_partidas, $par->id_partida);
        }

        $pres_ejecutado = [];

        foreach($pres_programado as $pro){
            $req = DB::table('almacen.alm_det_req')
            ->select(DB::raw('sum(alm_det_req.cantidad * alm_det_req.precio_referencial) as total_req'))
            ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
            ->whereIn('alm_det_req.partida',$array_partidas)
            ->where([['alm_req.fecha_requerimiento','>=',$pro->fecha_inicio],
                     ['alm_req.fecha_requerimiento','<',$pro->fecha_fin]])
            // ->whereBetween('alm_req.fecha_requerimiento', [$pro->fecha_inicio, $pro->fecha_fin])
            ->first();

            if ($req->total_req !== null){
                $nuevo = [
                    'id_periodo'=>$pro->id_periodo,
                    'numero'=>$pro->numero,
                    'total'=>$req->total_req,
                ];
                array_push($pres_ejecutado, $nuevo);
            }
        }

        return response()->json(['pro_programado'=>$pro_programado, 'pro_valorizado'=>$pro_valorizado,
        'pres_programado'=>$pres_programado, 'pres_ejecutado'=>$pres_ejecutado]);
    }

    public function getProyectosActivos()
    {
        $fecha_actual = date('Y-m-d');
        $dias = 30;//dias en un mes
        $data = DB::table('proyectos.proy_proyecto')
        ->select('proy_proyecto.codigo','proy_proyecto.descripcion','proy_proyecto.importe',
        DB::raw('(CASE 
                    WHEN proy_proyecto.unid_program = 4 THEN proy_proyecto.plazo_ejecucion 
                    WHEN proy_proyecto.unid_program = 1 THEN (proy_proyecto.plazo_ejecucion / '.$dias.') 
                 ELSE ((proy_proyecto.plazo_ejecucion * proy_unid_program.dias) / '.$dias.') END) AS cant_mes'),
        DB::raw("(SELECT proy_valori.total 
                    FROM proyectos.proy_valori
                    WHERE proy_valori.id_presup = presup.id_presup
                    AND proy_valori.estado != 7 
                    ORDER BY numero desc LIMIT 1
                    ) AS actual_ejecutado"),
        DB::raw("(SELECT SUM(proy_valori.total) 
                    FROM proyectos.proy_valori
                    WHERE proy_valori.id_presup = presup.id_presup
                    AND proy_valori.estado != 7 
                    ) AS acumulado_ejecutado"),
        DB::raw("(SELECT SUM(presup_periodos.total)
                    FROM proyectos.presup_periodos
                    WHERE presup_periodos.id_presup = presup.id_presup
                    AND presup_periodos.estado != 7
                    ) AS total_programado")
        // DB::raw("(SELECT SUM(alm_det_req.cantidad * alm_det_req.precio_referencial)
        //             FROM proyectos.proy_presup
        //             INNER JOIN finanzas.presup_par ON(
        //                         presup_par.id_presup = proy_presup.id_presup AND
        //                             presup_par.estado = 1
        //                     )
        //             INNER JOIN almacen.alm_det_req ON(
        //                         alm_det_req.partida = presup_par.id_partida 
        //                     )
        //             WHERE proy_presup.id_proyecto = proy_proyecto.id_proyecto
        //             AND proy_presup.cronograma = true
        //             AND proy_presup.cronoval = true
        //             AND proy_presup.estado = 8) AS total_valorizado")
        )
        ->join('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_proyecto.unid_program')
        ->leftjoin('finanzas.presup','presup.id_proyecto','=','proy_proyecto.id_proyecto')
        ->where([['proy_proyecto.estado','=',1],
                 ['proy_proyecto.fecha_inicio','<=',$fecha_actual],
                 ['proy_proyecto.fecha_fin','>=',$fecha_actual],
                 ['presup.estado','!=',7],
                 ['presup.tp_presup','=',3]
                ])
        ->get();
        
        $nro_opciones = DB::table('proyectos.proy_op_com')
        ->where([['estado','!=',7]])
        ->count();

        return response()->json(['data'=>$data,'nro_opciones'=>$nro_opciones]);
    }

    public function prueba(){
        $data = DB::table('almacen.alm_req')
        ->get();
        return $data;
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

}
