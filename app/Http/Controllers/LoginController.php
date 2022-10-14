<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller{

    public function __construct(){
		$this->middleware('auth')->except(['mostrar_roles']);
    }

    public function notas_de_lanzamiento(){
		$data = DB::table('configuracion.nota_lanzamiento')
		->select('nota_lanzamiento.*', 'detalle_nota_lanzamiento.*')
		->join('configuracion.detalle_nota_lanzamiento', 'detalle_nota_lanzamiento.id_nota_lanzamiento', '=', 'nota_lanzamiento.id_nota_lanzamiento')
        ->where([
			['nota_lanzamiento.estado', '=', 1],
			['nota_lanzamiento.version_actual', '=', true]
			])
			->orderBy('detalle_nota_lanzamiento.fecha_detalle_nota_lanzamiento', 'desc')
			->get();
		return $data;
    }

    function index(){

        $mods = $this->select_modules();
        $notasLanzamiento = $this->notas_de_lanzamiento();
		return view('home',compact('notasLanzamiento'))->with('modulos', $mods);
    }

    function select_modules(){
        $sql = DB::table('configuracion.modulos')->where([['id_padre', '=', 0],['estado','!=',7]])->orderBy('descripcion', 'ASC')->get();
        $html = '';

        foreach ($sql as $row){
            $id = $row->id_modulo;
            $name = $row->descripcion;
            $link = $row->ruta;
            $rutas = '';

            if ($id===169) {
                if (in_array(Auth::user()->id_usuario, [1,31,6,129])) {
                    $html .=
                    '<div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">Módulo</div>
                            <div class="panel-body">
                                <h4><a class="panel-link" href="'.$link.'">'.$name.'</a></h4>
                            </div>
                        </div>
                    </div>';
                }
            }else{
                $html .=
                '<div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">Módulo</div>
                        <div class="panel-body ">
                            <h4><a class="panel-link" href="'.$link.'/index">'.$name.'</a></h4>
                        </div>
                    </div>
                </div>';
            }

        }
        return $html;
    }

    public function encode5t($str){
        for($i=0; $i<5;$i++){
            $str=strrev(base64_encode($str));
        }
        return $str;
    }

    public function decode5t($str){
        for($i=0; $i<5;$i++){
            $str=base64_decode(strrev($str));
        }
        return $str;
    }

    public function mostrar_empresas(){
        $sql = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', 'adm_contri.id_contribuyente')
            ->select('adm_empresa.id_empresa', 'adm_contri.razon_social')->get();
        return response()->json($sql);
    }

    public function select_empresa(){
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('adm_empresa.id_empresa', 'adm_contri.razon_social')->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')->get();
        return $data;
    }

    public function mostrar_roles($user){
        $prev = DB::table('configuracion.sis_usua')->where('usuario', '=', $user)->get();
        $sql = '';

        if ($prev->count() > 0){
            $trab = $prev->first()->id_trabajador;
            $sql = DB::table('rrhh.rrhh_rol')
                ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', 'rrhh_rol.id_rol_concepto')
                ->select('rrhh_rol.id_rol', 'rrhh_rol_concepto.descripcion')
                ->where([['rrhh_rol.id_trabajador', '=', $trab], ['rrhh_rol.estado', '=', 1]])
                ->orderBy('rrhh_rol.id_rol', 'DESC')->limit(1)->get();
            $roles = ($sql->count() > 0) ? $sql->first()->id_rol : 0;
        }
        $array = array('rol' => $roles);
        return response($array);
    }
}
