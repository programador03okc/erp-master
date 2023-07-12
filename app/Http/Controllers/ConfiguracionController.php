<?php
namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Models\administracion\AdmGrupo;
use App\Models\Configuracion\Acceso;
use App\models\Configuracion\Accesos;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Configuracion\Grupo;
use App\Models\Configuracion\Modulo;
use App\Models\Configuracion\Pais as ConfiguracionPais;
use App\Models\Configuracion\Rol;
use App\Models\Configuracion\SisUsua;
use App\models\Configuracion\TableConfiguracionModulo;
use App\models\Configuracion\UsuarioGrupo;
use App\models\Configuracion\UsuarioRol;
use App\models\rrhh\rrhh_categoria_ocupacional;
use App\Models\rrhh\rrhh_est_civil;
use App\models\rrhh\rrhh_pension;
use App\Models\rrhh\rrhh_perso;
use App\Models\rrhh\rrhh_postu;
use App\models\rrhh\rrhh_tipo_planilla;
use App\Models\rrhh\rrhh_tp_trab;
use App\Models\rrhh\rrhh_trab;
use App\Models\sistema\pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DateTime;
ini_set('max_execution_time', 3600);
date_default_timezone_set('America/Lima');

class ConfiguracionController extends Controller{
    public $idEmpresa;
    public function __construct(){
        // session_start();
        $this->idEmpresa = session()->get('id_empresa'); /* Empresa en SESSION */
    }

    function view_main_configuracion(){
        return view('configuracion/main');

    }
/* VISTAS */
    function view_modulos(){ return view('configuracion/modulo');}
    function view_aplicaciones(){
        $modulos = $this->select_modulos();
        return view('configuracion/aplicaciones', compact('modulos'));
    }
    function view_usuario(){
        // return response()->json(Auth::);
        $modulos = $this->select_modulos();
        $roles=$this->lista_roles();

        $estado_civil = rrhh_est_civil::where("estado",1)->get();
        $pais = ConfiguracionPais::where('estado',1)->get();
        $tipo_trabajador =rrhh_tp_trab::where("estado",1)->get();
        $categoria_ocupacional = rrhh_categoria_ocupacional::where("estado",1)->get();
        $tipo_planilla = rrhh_tipo_planilla::where("estado",1)->get();
        $pension = rrhh_pension::where("estado",1)->get();
        $grupo = AdmGrupo::get();
        $rol = Rol::where("estado",1)->get();

        $modulos_padre =DB::table('configuracion.sis_modulo')->where('sis_modulo.estado',1)->where('sis_modulo.id_padre',0)->get();

        // return $rol[0]->id_rol;
        return view('configuracion.usuarios', compact('modulos','roles','estado_civil','pais','tipo_trabajador','categoria_ocupacional','tipo_planilla','pension','grupo','rol','modulos_padre'));
    }

    function view_notas_lanzamiento(){
        return view('configuracion/notas_lanzamiento');
    }
    function view_correo_coorporativo(){
        $empresas = $this->mostrarEmpresa();
        return view('configuracion/correo_coorporativo', compact('empresas'));
    }

    function view_configuracion_socket(){
        return view('configuracion/configuracion_socket');
    }



    function view_docuemtos(){ return view('configuracion/flujo_aprobacion/documentos');}
    function view_gestionar_flujos(){
        $grupoFlujo = $this->grupoFlujo();
        return view('configuracion/flujo_aprobacion/gestionar_flujos',compact('grupoFlujo'));}
    function view_historial_aprobaciones(){ return view('configuracion/flujo_aprobacion/historial_aprobaciones');}

    public function grupoFlujo(){
        $data = DB::table('administracion.grupo_flujo')->select('id_grupo_flujo', 'descripcion')->where('estado', '=', 1)
        ->orderBy('id_grupo_flujo', 'asc')->get();
        return $data;
    }

    public function rolesConcepto(){
        $data = DB::table('rrhh.rrhh_rol_concepto')
        ->select(
            'rrhh_rol_concepto.*'
        )
        ->where('rrhh_rol_concepto.estado', '=', 1)
        ->orderBy('rrhh_rol_concepto.id_rol_concepto', 'asc')->get();
        return $data;
    }

    public function operacion(){
        $data = DB::table('administracion.adm_operacion')
        ->select(
            'adm_operacion.*'

            )
        ->where('adm_operacion.estado', '=', 1)
        ->orderBy('adm_operacion.id_operacion', 'asc')->get();
        return $data;
    }
    public function operacionSelected($id){
        $data = DB::table('administracion.adm_operacion')
        ->select(
            'adm_operacion.*',
            'adm_tp_docum.descripcion as tipo_documento',
            'adm_grupo.descripcion as grupo_descripcion',
            'adm_operacion.id_area',
            'adm_area.descripcion as area_descripcion',
            'adm_empresa.id_empresa',
            'adm_contri.razon_social as razon_social_empresa',
            'sis_sede.id_sede',
            'sis_sede.codigo as codigo_sede'

            )
            ->join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'adm_operacion.id_tp_documento')
            ->leftJoin('administracion.adm_area', 'adm_area.id_area', '=', 'adm_operacion.id_area')
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_operacion.id_grupo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')

        ->where([
            ['adm_operacion.id_operacion', '=', $id],
            ['adm_operacion.estado', '=', 1]
        ])
        ->orderBy('adm_operacion.id_operacion', 'asc')->get();
        return $data;
    }

    function lista_roles_usuario($id){
        $rolesUsuario = Auth::user()->getAllRolUser($id);
		return $rolesUsuario;

    }
    function lista_roles(){
		$roles = DB::table('configuracion.sis_rol')
		->select('sis_rol.*')
		->where('sis_rol.estado',1)
		->get();
		return $roles;
    }

    public function mostrarTipoDocumento(){
        $data = DB::table('administracion.adm_tp_docum')
        ->select(
            'adm_tp_docum.*'
            )
        ->where([
            ['adm_tp_docum.estado', '=', 1]
        ])
        ->orderBy('adm_tp_docum.id_tp_documento', 'asc')->get();
        return $data;
    }
    public function mostrarEmpresa(){
        $data = DB::table('administracion.adm_empresa')
        ->select(
            'adm_empresa.*',
            'adm_contri.*'
            )
        ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
        ->where([
            ['adm_empresa.estado', '=', 1]
        ])
        ->orderBy('adm_empresa.id_empresa', 'asc')->get();
        return $data;
    }
    public function mostrarSede(){
        $data = DB::table('administracion.sis_sede')
        ->select(
            'sis_sede.*'
            )
        ->where([
            ['sis_sede.estado', '=', 1]
        ])
        ->orderBy('sis_sede.id_sede', 'asc')->get();
        return $data;
    }
    public function mostrarGrupo(){
        $data = DB::table('administracion.adm_grupo')
        ->select(
            'adm_grupo.*'
            )
        ->where([
            ['adm_grupo.estado', '=', 1]
        ])
        ->orderBy('adm_grupo.id_grupo', 'asc')->get();
        return $data;
    }
    public function mostrarArea(){
        $data = DB::table('administracion.adm_area')
        ->select(
            'adm_area.*'
            )
        ->where([
            ['adm_area.estado', '=', 1]
        ])
        ->orderBy('adm_area.id_area', 'asc')->get();
        return $data;
    }
    public function mostrarOperador(){
        $data = DB::table('administracion.operadores')
        ->select(
            'operadores.*'
            )
        ->where([
            ['operadores.estado', '=', 1]
        ])
        ->orderBy('operadores.id_operador', 'asc')->get();
        return $data;
    }

    public function mostrarGrupoCriterio($id_grupo_criterio =null){
        $option=[];
        if($id_grupo_criterio >0){
            $option=[['adm_grupo_criterios.id_grupo_criterios', '=', $id_grupo_criterio]];
        }

        $data = DB::table('administracion.adm_grupo_criterios')
        ->select(
            'adm_grupo_criterios.*'
            )
        ->where($option)
        ->orderBy('adm_grupo_criterios.id_grupo_criterios', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);    }

    public function revokeFlujo($id_flujo){
        $status='';
        $update = DB::table('administracion.adm_flujo')->where('id_flujo', $id_flujo)
        ->update([
            'estado' => 7
        ]);

        if($update >0){
            $status='ACTUALIZADO';
        }else{
            $status='NO_ACTUALIZADO';
        }

        return  response()->json($status);
    }

    public function revokeOperacion($id_operacion){
        $status='';
        $update = DB::table('administracion.adm_operacion')->where('id_operacion', $id_operacion)
        ->update([
            'estado' => 7
        ]);

        if($update >0){
            $status='ACTUALIZADO';
        }else{
            $status='NO_ACTUALIZADO';
        }

        return  response()->json($status);
    }


/* COMBOBOX - SELECT */
    public function select_doc_idendidad(){
        $data = DB::table('contabilidad.sis_identi')->select('id_doc_identidad', 'descripcion')->where('estado', '=', 1)
            ->orderBy('id_doc_identidad', 'asc')->get();
        return $data;
    }
    public function select_pais(){
        $data = DB::table('configuracion.sis_pais')->select('id_pais', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_centro_costos(){
        $data = DB::table('administracion.adm_grupo')->select('id_grupo', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_grupo($sede){
        $data = DB::table('administracion.adm_grupo')->select('id_grupo', 'descripcion')->where([['estado', '=', 1], ['id_sede', '=', $sede]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_sede(){
        $data = DB::table('administracion.sis_sede')->select('id_sede', 'descripcion')->where([['estado', '=', 1], ['id_empresa', '=', $this->idEmpresa]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_area($grupo){
        $data = DB::table('administracion.adm_area')->select('id_area', 'descripcion')->where([['estado', '=', 1], ['id_grupo', '=', $grupo]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_empresa(){
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('adm_empresa.id_empresa', 'adm_contri.razon_social')->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')->get();
        return $data;
    }
    public function select_modulos(){
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', 0], ['estado', '=', 1]])->get();
        return $data;
    }
    public function select_departamento(){
            $data = DB::table('configuracion.ubi_dpto')->select('id_dpto', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
            return $data;
    }
    public function select_prov_dep($id){
        $html = '';
        $data = DB::table('configuracion.ubi_prov')->where('id_dpto', '=', $id)->orderBy('descripcion', 'asc')->get();
        foreach ($data as $row){
        $id = $row->id_prov;
        $desc = $row->descripcion;
        $html .= '<option value="'.$id.'">'.$desc.'</option>';
        }
        return response()->json($html);
    }
    public function select_dist_prov($id){
        $html = '';
        $data = DB::table('configuracion.ubi_dis')->where('id_prov', '=', $id)->orderBy('descripcion', 'asc')->get();
        foreach ($data as $row){
        $id = $row->id_dis;
        $desc = $row->descripcion;
        $html .= '<option value="'.$id.'">'.$desc.'</option>';
    }
    return response()->json($html);
    }
    public function traer_ubigeo($id){
        $sql = DB::table('configuracion.ubi_dis')->where('id_dis', '=', $id)->first();
        $ubigeo = $sql->codigo;
    return response()->json($ubigeo);
    }
    public function cargar_estructura_org($id)
    {
        $html = '';
        $sql1 = DB::table('administracion.sis_sede')->where([['id_empresa', '=', $id], ['estado', '=', 1]])->get();
        foreach ($sql1 as $row) {
            $id_sede = $row->id_sede;
            $html .= '<ul>';
            $sql2 = DB::table('administracion.adm_grupo')->where([['id_sede', '=', $row->id_sede], ['estado', '=', 1]])->get();
            if ($sql2->count() > 0) {
                $html .=
                    '<li class="firstNode" onClick="showEfectOkc(' . $row->id_sede . ');">
                    <h5>+ <b> Sede - ' . $row->descripcion . '</b></h5>
                    <ul class="ul-nivel1" id="detalle-' . $row->id_sede . '">';
                foreach ($sql2 as $key) {
                    $id_grupo = $key->id_grupo;
                    $sql3 = DB::table('administracion.adm_area')->where([['id_grupo', '=', $key->id_grupo], ['estado', '=', 1]])->get();
                    if ($sql3->count() > 0) {
                        $html .= '<li><b>Grupo - ' . $key->descripcion . '</b><ul class="ul-nivel2">';
                        foreach ($sql3 as $value) {
                            $id_area = $value->id_area;
                            $area = $value->descripcion;
                            $txtArea = "'" . $area . "'";
                            $html .= '<li id="' . $id_area . '" onClick="areaSelectModal(' . $id_sede . ', ' . $id_grupo . ', ' . $id_area . ', ' . $txtArea . ');"> ' . $area . '</li>';
                        }
                    } else {
                        $html .= '<li> ' . $key->descripcion . '</li>';
                    }
                    $html .= '</li></ul>';
                }
                $html .= '</li></ul>';
            } else {
                $html .= '<li>' . $row->descripcion . '</li>';
            }
            $html .= '</ul>';
        }
        return response()->json($html);
    }
/* PASSWORDS */
    function cambiar_clave(Request $request){
        $p1 = StringHelper::encode5t(addslashes($request->pass_old));
        $p2 = StringHelper::encode5t(addslashes($request->pass_new));
        $user = Auth::user()->id_usuario;

        $sql = DB::table('configuracion.sis_usua')->where([['clave', '=', $p1], ['id_usuario', '=', $user], ['estado', '=', 1]])->first();

        if ($sql !== null) {
            $data = DB::table('configuracion.sis_usua')->where('id_usuario', $sql->id_usuario)->update(['clave'  => $p2, 'password' => Hash::make($request->pass_new)]);
            $rpta = $data;
        }else{
            $rpta = 0;
        }
        return response()->json($rpta);
    }
/* MODULO */
    public function mostrar_modulo_table(){
        $data = DB::table('configuracion.sis_modulo')->where('estado', '=', 1)->orderBy('tipo_modulo', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_modulo_id($id){
        $sql = DB::table('configuracion.sis_modulo')->where('id_modulo', $id)->get();
        $myId = $sql->first()->id_padre;
        $opt = $this->mostrar_modulos_edit($myId);
        $data = [0 => $sql, 1 => $opt];
        return response()->json($data);
    }
    public function mostrar_modulos_edit($value){
        $html = '';
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', 0], ['estado', '=', 1]])->orderBy('codigo', 'asc')->get();

        foreach ($data as $row){
            $id = $row->id_modulo;
            $desc = $row->descripcion;
            if ($id == $value) {
                $html .= '<option value="'.$id.'" selected>'.$desc.'</option>';
            }else{
                $html .= '<option value="'.$id.'">'.$desc.'</option>';
            }
        }
        return $html;
    }
    public function mostrar_modulos_combo(){
        $html = '';
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', 0], ['estado', '=', 1]])->orderBy('codigo', 'asc')->get();

        foreach ($data as $row){
            $id = $row->id_modulo;
            $desc = $row->descripcion;
            $html .= '<option value="'.$id.'">'.$desc.'</option>';
        }
        return response()->json($html);
    }
    public function countModules(){
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', 0],['estado', '=', 1]])->get();
        $num = $data->count();
        return $num;
    }
    public function countSubModules($id){
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', $id],['estado', '=', 1]])->get();
        $num = $data->count();
        return $num;
    }
    public function codeModules($id){
        $data = DB::table('configuracion.sis_modulo')->where('id_modulo', $id)->first();
        $code = $data->codigo;
        return $code;
    }
    public function guardar_modulo(Request $request){
        $tipo = $request->tipo_mod;
        $padre = (empty($request->padre_mod)) ? 0 : $request->padre_mod;
        $id = DB::table('configuracion.sis_modulo')->insertGetId(
            [
                'tipo_modulo'   => $tipo,
                'id_padre'      => $padre,
                'descripcion'   => $request->descripcion,
                'ruta'          => $request->ruta,
                'estado'        => 1
            ],
            'id_modulo'
        );
        if ($id > 0){
            if ($tipo == 1){
                $count = $this->countModules();
                $code = $this->leftZero(2, $count);
            }else{
                $count = $this->countSubModules($padre);
                $code1 = $this->codeModules($padre);
                $code2 = $this->leftZero(2, $count);
                $code = $code1.'.'.$code2;
            }

            $data = DB::table('configuracion.sis_modulo')->where('id_modulo', $id)
            ->update([
                'codigo'    => $code
                ]);
        }else{
            $id = 0;
        }
        return response()->json($id);
    }
    public function actualizar_modulo(Request $request){
        $tipo = $request->tipo_mod;
        $padre = (empty($request->padre_mod)) ? 0 : $request->padre_mod;

        $data = DB::table('configuracion.sis_modulo')->where('id_modulo', $request->id_modulo)
        ->update([
            'tipo_modulo'   => $tipo,
            'id_padre'      => $padre,
            'descripcion'   => $request->descripcion,
            'ruta'          => $request->ruta
        ]);
        return response()->json($data);
    }
    public function anular_modulo($id){
        $data = DB::table('configuracion.sis_modulo')->where('id_modulo', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
/* APLICACIONES */
    public function mostrar_aplicaciones_table(){
        $data = DB::table('configuracion.sis_aplicacion')
            ->join('configuracion.sis_modulo', 'sis_modulo.id_modulo', '=', 'sis_aplicacion.id_sub_modulo')
            ->select('sis_aplicacion.*', 'sis_modulo.descripcion AS modulo')
            ->where('sis_aplicacion.estado', '=', 1)->orderBy('sis_aplicacion.descripcion', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_aplicaciones_id($id){
        $sql = DB::table('configuracion.sis_aplicacion')
            ->join('configuracion.sis_modulo', 'sis_modulo.id_modulo', '=', 'sis_aplicacion.id_sub_modulo')
            ->select('sis_aplicacion.id_aplicacion', 'sis_aplicacion.id_sub_modulo AS submodulo', 'sis_aplicacion.descripcion', 'sis_aplicacion.ruta',
                'sis_modulo.id_padre', 'sis_aplicacion.estado')
            ->where('sis_aplicacion.id_aplicacion', $id)->get();
        $id_sub = $sql->first()->submodulo;
        $option = $this->mostrar_submodulo($id_sub);
        $data = [0 => $sql, 1 => $option];
        return response()->json($data);
    }
    public function mostrar_submodulo_id($id){
        $html = '';
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', $id], ['estado', '=', 1]])->orderBy('descripcion', 'asc')->get();
        foreach ($data as $row){
            $ids = $row->id_modulo;
            $desc = $row->descripcion;
            $html .= '<option value="'.$ids.'">'.$desc.'</option>';
        }
        return response()->json($html);
    }
    public function mostrar_submodulo($id){
        $html = '';
        $sql = DB::table('configuracion.sis_modulo')->where('id_modulo', '=', $id)->first();
        $myId = $sql->id_padre;
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', $myId], ['estado', '=', 1]])->orderBy('descripcion', 'asc')->get();
        foreach ($data as $row){
            $ids = $row->id_modulo;
            $desc = $row->descripcion;
            if ($id == $ids) {
                $html .= '<option value="'.$ids.'" selected>'.$desc.'</option>';
            }else{
                $html .= '<option value="'.$ids.'">'.$desc.'</option>';
            }
        }
        return $html;
    }
    public function guardar_aplicaciones(Request $request){
        $id = DB::table('configuracion.sis_aplicacion')->insertGetId(
            [
                'id_sub_modulo' => $request->sub_modulo,
                'descripcion'   => $request->descripcion,
                'ruta'          => $request->ruta,
                'estado'        => 1
            ],
            'id_aplicacion'
        );
        return response()->json($id);
    }
    public function actualizar_aplicaciones(Request $request){
        $data = DB::table('configuracion.sis_aplicacion')->where('id_aplicacion', $request->id_aplicacion)
        ->update([
            'id_sub_modulo' => $request->sub_modulo,
            'descripcion'   => $request->descripcion,
            'ruta'          => $request->ruta,
        ]);
        return response()->json($data);
    }
    public function anular_aplicaciones($id){
        $data = DB::table('configuracion.sis_aplicacion')->where('id_aplicacion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    public function decode5t($str)
    {
        for ($i = 0; $i < 5; $i++) {
            $str = base64_decode(strrev($str));
        }
        return $str;
    }

    public function getPasswordUserDecode($id){
        $data=null;
        $status=0;
        $sis_usu = DB::table('configuracion.sis_usua')
        ->select(
            'sis_usua.id_usuario',
            'sis_usua.usuario',
            'sis_usua.clave',
            )
        ->where('sis_usua.id_usuario', '=', $id)
        ->get();

        if(isset($sis_usu) && count($sis_usu) >0){
            $pass = $sis_usu->first()->clave;
            $status=200;
        }
        $thePass= $this->decode5t($pass);

        $output= ['data'=>$thePass, 'status'=>$status];

        return $output;

    }

    public function savePerfil(Request $request){
        $sis_usua                   = SisUsua::where('id_usuario',$request->id_usuario)->first();
        $sis_usua->usuario          = $request->usuario;
        if ($request->clave) {
            $sis_usua->clave        = StringHelper::encode5t($request->clave);
            $sis_usua->password     = Hash::make($request->clave);
        }
        $sis_usua->nombre_corto     = $request->nombre_corto;
        $sis_usua->codvend_softlink = $request->codvent_softlink;
        $sis_usua->email            = $request->email;
        $sis_usua->save();

        $rrhh_trab = rrhh_trab::where('id_trabajador',$sis_usua->id_trabajador)->first();
        // $rrhh_trab->id_postulante               = (int) $request->id_postulante;
        $rrhh_trab->id_tipo_trabajador          = (int) $request->id_tipo_trabajador;
        $rrhh_trab->id_categoria_ocupacional    = (int) $request->id_categoria_ocupacional;
        $rrhh_trab->id_tipo_planilla            = (int) $request->id_tipo_planilla;
        // $rrhh_trab->condicion                   = $request->condicion;
        $rrhh_trab->hijos                       = $request->hijos;
        // $rrhh_trab->id_pension                  = (int) $request->id_pension;
        // $rrhh_trab->cuspp                       = $request->cuspp;
        // $rrhh_trab->seguro                      = $request->seguro;
        // $rrhh_trab->confianza                   = $request->confianza;
        // $rrhh_trab->estado                      = 1;
        // $rrhh_trab->fecha_registro = date('Y-m-d H:i:s');
        $rrhh_trab->save();

        $rrhh_postu = rrhh_postu::where('id_postulante',$rrhh_trab->id_postulante)->first();
        // $rrhh_postu->id_persona     = (int) $rrhh_perso->id_persona;
        $rrhh_postu->direccion      = $request->direccion;
        $rrhh_postu->telefono       = (int) $request->telefono;
        $rrhh_postu->correo         = $request->email;
        // $rrhh_postu->brevette       = $request->brevette;
        $rrhh_postu->id_pais        = (int) $request->pais;
        $rrhh_postu->ubigeo         = $request->ubigeo;
        $rrhh_postu->fecha_registro = date('Y-m-d H:i:s');
        $rrhh_postu->save();

        $rrhh_perso = rrhh_perso::where('id_persona', $rrhh_postu->id_persona)->first();
        $rrhh_perso->id_documento_identidad = 1;
        $rrhh_perso->nro_documento          = (int) $request->nro_documento;
        $rrhh_perso->nombres                = $request->nombres;
        $rrhh_perso->apellido_paterno       = $request->apellido_paterno;
        $rrhh_perso->apellido_materno       = $request->apellido_materno;
        $rrhh_perso->fecha_nacimiento       = $request->fecha_nacimiento;
        $rrhh_perso->sexo                   = $request->sexo;
        $rrhh_perso->id_estado_civil        = (int) $request->id_estado_civil;
        // $rrhh_perso->estado                 = 1;
        // $rrhh_perso->fecha_registro         = date('Y-m-d H:i:s');
        $rrhh_perso->telefono               = (int) $request->telefono;
        $rrhh_perso->direccion              = $request->direccion;
        $rrhh_perso->email                  = $request->email;
        $rrhh_perso->save();

        UsuarioGrupo::where('estado', 1)->where('id_usuario',$request->id_usuario)
          ->update(['estado' => 7]);
        foreach ($request->id_grupo as $key => $value) {
            $usuario_grupo              = new UsuarioGrupo;
            $usuario_grupo->id_grupo    = $value;
            $usuario_grupo->id_usuario  = $sis_usua->id_usuario;
            $usuario_grupo->estado      = 1;
            $usuario_grupo->save();
        }
        UsuarioRol::where('estado', 1)->where('id_usuario',$request->id_usuario)
        ->update(['estado' => 7]);
        foreach ($request->id_rol as $key => $value) {
            $usuario_rol                = new UsuarioRol;
            $usuario_rol->id_rol        = $value;
            $usuario_rol->id_usuario    = $sis_usua->id_usuario;
            $usuario_rol->estado        = 1;
            $usuario_rol->save();
        }

        return response()->json([
            "success"=>true,
            "status"=>200
        ]);
        try {
            DB::beginTransaction();

        $status=0;
        $id_usuario = $request->id_usuario;
        $nombres = $request->nombres;
        $apellido_paterno = $request->apellido_paterno;
        $apellido_materno = $request->apellido_materno;
        $nombre_corto = $request->nombre_corto;
        $usuario = $request->usuario;
        $contraseña =  StringHelper::encode5t($request->contraseña);
        $email = $request->email;
        $rol = $request->rol;

        $sis_usua_update = DB::table('configuracion.sis_usua')
        ->where('id_usuario', $id_usuario)
        ->update([
            'usuario' => $usuario,
            'clave' => $contraseña,
            'password' => Hash::make($request->contraseña),
            'nombre_corto' => $nombre_corto
        ]);


        $sis_acceso_update = DB::table('configuracion.sis_acceso')
        ->where('id_usuario', $id_usuario)
        ->update([
            'id_rol' => $rol
        ]);

        $rrhh_perso = DB::table('configuracion.sis_usua')
        ->select(
            'rrhh_perso.id_persona'
        )
        ->join('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_trab.id_postulante', '=', 'rrhh_postu.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_postu.id_persona', '=', 'rrhh_perso.id_persona')

        ->where('sis_usua.id_usuario', '=', $id_usuario)
        ->get();

        $id_persona=0;
        if(count($rrhh_perso) > 0){
            $id_persona=$rrhh_perso->first()->id_persona;

            $rrhh_perso_update = DB::table('rrhh.rrhh_perso')
            ->where('id_persona', $id_persona)
            ->update([
                'nombres' => $nombres,
                'apellido_paterno' => $apellido_paterno,
                'apellido_materno' => $apellido_materno,
                'email' => $email,
            ]);
        }
        if($sis_usua_update > 0  && $sis_acceso_update > 0  && $rrhh_perso_update > 0  ){
            $status = 200;
        }


        $output= ['status'=>$status];
        DB::commit();
        return response()->json($output);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function getPerfil($id){

        $usuario=[];
        $status=0;
        $data = SisUsua::select(
            'sis_usua.*',
            'rrhh_trab.*',
            'rrhh_postu.*',
            'rrhh_perso.*'
        )
        ->join('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_trab.id_postulante', '=', 'rrhh_postu.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_postu.id_persona', '=', 'rrhh_perso.id_persona')
        ->where('id_usuario',$id)
        ->first();
        // $data->usuarioGrupo;
        if (sizeof($data->usuarioGrupo)>0) {
            $data->usuarioGrupo;
        }else{$data->usuarioGrupo=[];}
        $data->usuarioRol = (sizeof($data->usuarioRol)>0) ? $data->usuarioRol : [] ;
        // $grupo = Grupo::get();
        // $rol = Rol::where("estado",1)->get();
        return response()->json([
            "status"=>200,
            "data"=>$data
        ]);
    }


    public function mostrar_usuarios(){
        $response = SisUsua::where('estado',1)->where('deleted_at',null)
        ->select(
            'sis_usua.id_usuario',
            'sis_usua.nombre_corto',
            'sis_usua.usuario',
            'sis_usua.clave',
            'sis_usua.fecha_registro',
            'sis_usua.estado',
            'sis_usua.email',
            // 'usuario_rol.id_rol',
            // 'sis_rol.descripcion as rol',

        )
        ->get();

        $output=['data'=>$response];
        return $output;
    }

    public function guardar_usuarios(Request $request){

        // return $request;exit;
        $rrhh_perso = new rrhh_perso;
        $rrhh_perso->id_documento_identidad = 1;
        $rrhh_perso->nro_documento          = (int) $request->nro_documento;
        $rrhh_perso->nombres                = $request->nombres;
        $rrhh_perso->apellido_paterno       = $request->apellido_paterno;
        $rrhh_perso->apellido_materno       = $request->apellido_materno;
        $rrhh_perso->fecha_nacimiento       = $request->fecha_nacimiento;
        $rrhh_perso->sexo                   = $request->sexo;
        // $rrhh_perso->id_estado_civil        = (int) $request->id_estado_civil;
        $rrhh_perso->estado                 = 1;
        $rrhh_perso->fecha_registro         = date('Y-m-d H:i:s');
        $rrhh_perso->telefono               = (int) $request->telefono;
        $rrhh_perso->direccion              = $request->direccion;
        $rrhh_perso->email                  = $request->email;
        $rrhh_perso->save();

        $rrhh_postu = new rrhh_postu;
        $rrhh_postu->id_persona     = (int) $rrhh_perso->id_persona;
        $rrhh_postu->direccion      = $request->direccion;
        $rrhh_postu->telefono       = (int) $request->telefono;
        $rrhh_postu->correo         = $request->email;
        // $rrhh_postu->brevette       = $request->brevette;
        $rrhh_postu->id_pais        = (int) $request->pais;
        $rrhh_postu->ubigeo         = $request->ubigeo;
        $rrhh_postu->fecha_registro = date('Y-m-d H:i:s');
        $rrhh_postu->save();

        $rrhh_trab = new rrhh_trab;
        $rrhh_trab->id_postulante               = (int) $rrhh_postu->id_postulante;
        $rrhh_trab->id_tipo_trabajador          = (int) $request->id_tipo_trabajador;
        $rrhh_trab->id_categoria_ocupacional    = (int) $request->id_categoria_ocupacional;
        $rrhh_trab->id_tipo_planilla            = (int) $request->id_tipo_planilla;
        // $rrhh_trab->condicion                   = $request->condicion;
        $rrhh_trab->hijos                       = $request->hijos;
        // $rrhh_trab->id_pension                  = (int) $request->id_pension;
        // $rrhh_trab->cuspp                       = $request->cuspp;
        // $rrhh_trab->seguro                      = $request->seguro;
        // $rrhh_trab->confianza                   = $request->confianza;
        $rrhh_trab->estado                      = 1;
        $rrhh_trab->fecha_registro = date('Y-m-d H:i:s');
        $rrhh_trab->save();

        $sis_usua                   = new SisUsua;
        $sis_usua->id_trabajador    = $rrhh_trab->id_trabajador;
        $sis_usua->usuario          = $request->usuario;
        $sis_usua->clave            = StringHelper::encode5t('Inicio01');
        $sis_usua->estado           = 1;
        $sis_usua->fecha_registro   = date('Y-m-d H:i:s');
        $sis_usua->nombre_corto     = $request->nombre_corto;
        $sis_usua->codvend_softlink = $request->codvent_softlink;
        $sis_usua->email            = $request->email;
        $sis_usua->save();

        foreach ($request->id_grupo as $key => $value) {
            $usuario_grupo              = new UsuarioGrupo;
            $usuario_grupo->id_grupo    = $value;
            $usuario_grupo->id_usuario  = $sis_usua->id_usuario;
            $usuario_grupo->estado      = 1;
            $usuario_grupo->save();
        }

        foreach ($request->id_rol as $key => $value) {
            $usuario_rol                = new UsuarioRol;
            $usuario_rol->id_rol        = $value;
            $usuario_rol->id_usuario    = $sis_usua->id_usuario;
            $usuario_rol->estado        = 1;
            $usuario_rol->save();
        }


        return response()->json([
            "status"=>200,
            'success'=>true,

        ]);
    }

    public function anular_usuario($id){

        $usua = DB::table('configuracion.sis_usua')
        ->where('id_usuario',$id)
        ->update(['estado'=>7]);
        if ($usua) {
            return response()->json([
                "success"=>true,
                "status"=>200
            ]);
        } else {
            return response()->json([
                "success"=>false,
                "status"=>404
            ]);
        }


    }
/* NOTAS DE LANZAMIENTO */
public function mostrar_notas_lanzamiento_select(){
    $data = DB::table('configuracion.nota_lanzamiento')
        ->select('nota_lanzamiento.*')
        ->where('nota_lanzamiento.estado', '=', 1)
        ->orderBy('nota_lanzamiento.id_nota_lanzamiento', 'asc')
        ->get();
        return $data;
}
public function mostrar_nota_lanzamiento($id_nota){
    $data = DB::table('configuracion.nota_lanzamiento')
        ->select('nota_lanzamiento.*')
        ->where([['nota_lanzamiento.estado', '=', 1],
                ['nota_lanzamiento.id_nota_lanzamiento', '=', $id_nota],
                ])
        ->orderBy('nota_lanzamiento.id_nota_lanzamiento', 'asc')
        ->get();

        return response()->json($data->first());

}

public function mostrar_detalle_nota_lanzamiento($id){
    $data = DB::table('configuracion.detalle_nota_lanzamiento')
        ->select('detalle_nota_lanzamiento.*')
        ->where([
            ['detalle_nota_lanzamiento.estado', '=', 1],
            ['detalle_nota_lanzamiento.id_detalle_nota_lanzamiento', '=', $id]
        ])
        ->orderBy('detalle_nota_lanzamiento.id_detalle_nota_lanzamiento', 'asc')
        ->get();

    return response()->json($data->first());
}

public function mostrar_detalle_notas_lanzamiento_table($id){
    $data = DB::table('configuracion.detalle_nota_lanzamiento')
        ->select('detalle_nota_lanzamiento.*')
        ->where([
            ['detalle_nota_lanzamiento.estado', '=', 1],
            ['detalle_nota_lanzamiento.id_nota_lanzamiento', '=', $id]
        ])
        ->orderBy('detalle_nota_lanzamiento.id_detalle_nota_lanzamiento', 'asc')
        ->get();
    $output['data'] = $data;
    return response()->json($output);
}

public function updateNotaLanzamiento(Request $request){
    $nota= $request->all();
    $id_nota_lanzamiento = $nota['id_nota_lanzamiento'];
    $version = $nota['version'];
    $version_actual = $nota['version_actual'];
    $fecha_nota_lanzamiento = $nota['fecha_nota_lanzamiento'];
    $status='';
    $update = DB::table('configuracion.nota_lanzamiento')->where('id_nota_lanzamiento', $id_nota_lanzamiento)
    ->update([
        'version' => $version,
        'version_actual' => $version_actual,
        'fecha_nota_lanzamiento' => $fecha_nota_lanzamiento
    ]);

    if($update >0){
        $status='ACTUALIZADO';
    }else{
        $status='NO_ACTUALIZADO';
    }

    return  response()->json($status);
}
public function updateDetalleNotaLanzamiento(Request $request){
    $nota= $request->all();
    $id_detalle_nota_lanzamiento = $nota['id_detalle_nota_lanzamiento'];
    $titulo = $nota['titulo'];
    $descripcion = $nota['descripcion'];
    $fecha_detalle_nota_lanzamiento = $nota['fecha_detalle_nota_lanzamiento'];
    $status='';
    $update = DB::table('configuracion.detalle_nota_lanzamiento')->where('id_detalle_nota_lanzamiento', $id_detalle_nota_lanzamiento)
    ->update([
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'fecha_detalle_nota_lanzamiento' => $fecha_detalle_nota_lanzamiento
    ]);

    if($update >0){
        $status='ACTUALIZADO';
    }else{
        $status='NO_ACTUALIZADO';
    }

    return  response()->json($status);
}

public function guardarNotaLanzamiento(Request $request){
    $nota= $request->all();
    $id_nota_lanzamiento = $nota['id_nota_lanzamiento'];
    $version = $nota['version'];
    $version_actual = $nota['version_actual'];
    $fecha_nota_lanzamiento = $nota['fecha_nota_lanzamiento'];
    $status='';
    $guardar = DB::table('configuracion.nota_lanzamiento')->insertGetId([
        'version' => $version,
        'version_actual' => $version_actual,
        'fecha_nota_lanzamiento' => $fecha_nota_lanzamiento,
        'estado' => 1
    ],'id_nota_lanzamiento'
    );

    if($guardar >0){
        $status='GUARDADO';
    }else{
        $status='NO_GUARDADO';
    }
    return  response()->json($status);
}

public function eliminarNotaLanzamiento($id){
    $status='';
    $eliminar = DB::table('configuracion.nota_lanzamiento')
    ->where('id_nota_lanzamiento',$id)
    ->delete();

    if($eliminar >0){
        $status='ELIMINADO';
    }else{
        $status='NO_ELIMINADO';
    }
    return  response()->json($status);
}

public function eliminarDetalleNotaLanzamiento($id){
    $status='';
    $eliminar = DB::table('configuracion.detalle_nota_lanzamiento')
    ->where('id_detalle_nota_lanzamiento',$id)
    ->delete();

    if($eliminar >0){
        $status='ELIMINADO';
    }else{
        $status='NO_ELIMINADO';
    }
    return  response()->json($status);
}

public function mostrarVersionActual(){
    $nota_lanzamiento = DB::table('configuracion.nota_lanzamiento')
    ->select('nota_lanzamiento.*')
    ->where([['nota_lanzamiento.estado', '=', 1],['nota_lanzamiento.version_actual','=',true]])
    ->orderBy('nota_lanzamiento.id_nota_lanzamiento', 'asc')
    ->get();

    $id_nota_lanzamiento_list=[];
    foreach($nota_lanzamiento as $data){
        $id_nota_lanzamiento_list[] = $data->id_nota_lanzamiento;
    }
    $detalle_nota = DB::table('configuracion.detalle_nota_lanzamiento')
    ->select('detalle_nota_lanzamiento.*')
    ->where([['detalle_nota_lanzamiento.estado', '=', 1]])
    ->whereIn('detalle_nota_lanzamiento.id_nota_lanzamiento',$id_nota_lanzamiento_list)
    ->orderBy('detalle_nota_lanzamiento.id_detalle_nota_lanzamiento', 'asc')
    ->get();

    return $detalle_nota;
}

/* FLUJO APROBACION  - DOCUMENTO */
    public function mostrar_documento_table(){
        $data = DB::table('administracion.adm_estado_doc')
        ->orderBy('id_estado_doc', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_documento_id($id){
        $sql = DB::table('administracion.adm_estado_doc')
        ->where('id_estado_doc', $id)->get();
        $data = [0 => $sql];
        return response()->json($data);
    }
    public function guardar_documento(Request $request){
        $id = DB::table('administracion.adm_estado_doc')->insertGetId(
            [
                'estado_doc' => $request->estado_documento,
                'bootstrap_color' => $request->color
            ],
            'id_estado_doc'
        );
        return response()->json($id);
    }
    public function actualizar_documento(Request $request){
        $data = DB::table('administracion.adm_estado_doc')->where('id_estado_doc', $request->id_documento)
        ->update([
            'estado_doc'   => $request->estado_documento,
            'bootstrap_color' => $request->color,
        ]);
        return response()->json($data);
    }
    public function anular_documento($id){
        $idEstadoAnulado = $this->get_estado_doc('Anulado');
        $data = DB::table('administracion.adm_estado_doc')->where('id_estado_doc', $id)
        ->update([
            'estado'    => $idEstadoAnulado
        ]);
        return response()->json($data);
    }

/* FLUJO APROBACION  - HISTORIAL APROBACIÓN */
    public function mostrar_historial_aprobacion(){
        $data = DB::table('administracion.adm_aprobacion')
        ->select(
            'adm_aprobacion.id_aprobacion',
            'adm_aprobacion.id_flujo',
            'adm_aprobacion.id_doc_aprob',
            'adm_documentos_aprob.codigo_doc',
            'adm_aprobacion.id_vobo',
            'adm_aprobacion.id_usuario',
            'adm_aprobacion.id_area',
            'adm_aprobacion.fecha_vobo',
            'adm_aprobacion.detalle_observacion',
            'adm_aprobacion.id_rol',
            'adm_flujo.nombre as nombre_flujo',
            'adm_vobo.descripcion as descripcion_vobo',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_completo_usuario"),
            'adm_area.descripcion as descripcion_area',
            'rrhh_rol_concepto.descripcion as descripcion_rol_concepto'
        )
        ->join('administracion.adm_flujo', 'adm_aprobacion.id_flujo', '=', 'adm_flujo.id_flujo')
        ->join('administracion.adm_vobo', 'adm_aprobacion.id_vobo', '=', 'adm_vobo.id_vobo')
        ->join('configuracion.sis_usua', 'adm_aprobacion.id_usuario', '=', 'sis_usua.id_usuario')
        ->join('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_trab.id_postulante', '=', 'rrhh_postu.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_postu.id_persona', '=', 'rrhh_perso.id_persona')
        ->join('administracion.adm_area', 'adm_aprobacion.id_area', '=', 'adm_area.id_area')
        ->join('rrhh.rrhh_rol', 'adm_aprobacion.id_rol', '=', 'rrhh_rol.id_rol')
        ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol.id_rol_concepto', '=', 'rrhh_rol_concepto.id_rol_concepto')
        ->join('administracion.adm_documentos_aprob', 'adm_aprobacion.id_doc_aprob', '=', 'adm_documentos_aprob.id_doc_aprob')
        ->orderBy('id_aprobacion', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
/* FLUJO APROBACION  - GESTIONAR LFUJO */

public function mostrar_flujos($id_grupo_flujo=null,$id_flujo=null){
    $option=[ ['adm_flujo.estado', '=', 1],['adm_operacion.estado', '=', 1]];
    if($id_grupo_flujo >0){
        $option[]=['adm_flujo.id_grupo_flujo', '=', $id_grupo_flujo];
    }
    if($id_flujo >0){
        $option[]=['adm_flujo.id_flujo', '=', $id_flujo];
    }


    $data = DB::table('administracion.adm_flujo')
    ->select(
        'adm_flujo.id_flujo',
        'adm_flujo.id_grupo_flujo',
        'adm_flujo.id_operacion',
        'adm_flujo.id_rol',
        'adm_flujo.nombre',
        'adm_flujo.orden',
        'adm_flujo.estado as flujo_estado',
        'adm_operacion.descripcion as operacion_descripcion',
        'adm_operacion.id_grupo',
        'adm_grupo.descripcion as grupo_descripcion',
        'adm_operacion.id_tp_documento',
        'adm_tp_docum.descripcion as tp_documento_descripcion',
        // 'adm_operacion.id_prioridad', eliminar -> trasladado a detalle_criterio
        'adm_operacion.id_area',
        'adm_area.descripcion as area_descripcion',
        'adm_empresa.id_empresa',
        'adm_contri.razon_social as razon_social_empresa',
        'sis_sede.id_sede',
        'sis_sede.codigo as codigo_sede',

        'adm_operacion.fecha_registro',
        'adm_operacion.estado as operacion_estado',
        'rrhh_rol_concepto.descripcion as rol_concepto_descripcion',
        DB::raw("(SELECT COUNT(adm_detalle_grupo_criterios.id_criterio_monto) FROM administracion.adm_detalle_grupo_criterios
            WHERE adm_detalle_grupo_criterios.id_flujo = adm_flujo.id_flujo and adm_detalle_grupo_criterios.estado = 1)::integer as cantidad_criterio_monto"),
        DB::raw("(SELECT COUNT(adm_detalle_grupo_criterios.id_criterio_prioridad) FROM administracion.adm_detalle_grupo_criterios
            WHERE adm_detalle_grupo_criterios.id_flujo = adm_flujo.id_flujo and adm_detalle_grupo_criterios.estado = 1 )::integer as cantidad_criterio_prioridad")
    )
    ->join('administracion.adm_operacion', 'adm_operacion.id_operacion', '=', 'adm_flujo.id_operacion')
    ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'adm_flujo.id_rol')
    ->join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'adm_operacion.id_tp_documento')
    ->leftJoin('administracion.adm_area', 'adm_area.id_area', '=', 'adm_operacion.id_area')
    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_operacion.id_grupo')
    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
    ->where($option)
    ->orderBy('adm_flujo.id_flujo', 'asc')
    ->get();
    $output['data']=$data;
    return response()->json($output);
}

public function mostrar_operaciones($id_operacion=null){
    $option=[['adm_operacion.estado', '=', 1]];
    if($id_operacion >0){
        $option[]=['adm_flujo.id_operacion', '=', $id_operacion];
    }

    $data = DB::table('administracion.adm_operacion')
    ->select(
        'adm_operacion.id_operacion',
        'adm_operacion.descripcion as operacion_descripcion',
        'adm_operacion.id_grupo',
        'adm_grupo.descripcion as grupo_descripcion',
        'adm_operacion.id_tp_documento',
        'adm_tp_docum.descripcion as tp_documento_descripcion',
        'adm_operacion.id_area',
        'adm_area.descripcion as area_descripcion',
        'adm_empresa.id_empresa',
        'adm_contri.razon_social as razon_social_empresa',
        'sis_sede.id_sede',
        'sis_sede.codigo as codigo_sede',
        'adm_operacion.fecha_registro',
        'adm_operacion.estado as operacion_estado'
    )
    ->join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'adm_operacion.id_tp_documento')
    ->leftJoin('administracion.adm_area', 'adm_area.id_area', '=', 'adm_operacion.id_area')
    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_operacion.id_grupo')
    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
    ->where($option)
    ->orderBy('adm_operacion.id_operacion', 'asc')
    ->get();
    $output['data']=$data;
    return response()->json($output);
}

public function updateFlujo(Request $request){

    $flujo= $request->all();
    $id_flujo = $flujo['id_flujo'];
    $nombre_flujo = $flujo['nombre_flujo'];
    $grupo_flujo = $flujo['grupo_flujo'];
    $orden = $flujo['orden'];
    $rol = $flujo['rol'];
    $estado = $flujo['estado'];
    $operacion = $flujo['operacion'];

    $status='';
    $update = DB::table('administracion.adm_flujo')->where('id_flujo', $id_flujo)
    ->update([
        'id_operacion' => $operacion,
        'id_rol' => $rol,
        'nombre' => $nombre_flujo,
        'orden' => $orden,
        'estado' => $estado,
        'id_grupo_flujo' => $grupo_flujo
    ]);

    if($update >0){
        $status='ACTUALIZADO';
    }else{
        $status='NO_ACTUALIZADO';
    }

    return  response()->json($status);
}

public function updateOperacion(Request $request){

    $flujo= $request->all();
    $id_operacion = $flujo['id_operacion'];
    $operacion_descripcion = $flujo['operacion_descripcion'];
    $tipo_documento = $flujo['tipo_documento'];
    $empresa = $flujo['empresa'];
    $sede = $flujo['sede'];
    $grupo = $flujo['grupo'];
    $area = $flujo['area'];
    $estado = $flujo['estado'];

    $status='';
    $update = DB::table('administracion.adm_operacion')->where('id_operacion', $id_operacion)
    ->update([
        'descripcion' => $operacion_descripcion,
        'id_tp_documento' => $tipo_documento,
        'id_grupo' => $grupo,
        'id_area' => $area,
        'estado' => $estado
    ]);

    if($update >0){
        $status='ACTUALIZADO';
    }else{
        $status='NO_ACTUALIZADO';
    }

    return  response()->json($status);
}

public function mostrarCriterioMonto($id_criterio_monto =null){
    $option=[];
    if($id_criterio_monto >0){
        $option[]=['adm_criterio_monto.id_criterio_monto', '=', $id_criterio_monto];
    }

    $data = DB::table('administracion.adm_criterio_monto')
    ->select(
        'adm_criterio_monto.id_criterio_monto',
        'adm_criterio_monto.descripcion',
        'adm_criterio_monto.id_operador1',
        'op1.descripcion as descripcion_operador1',
        'op1.signo as signo_operador1',
        'adm_criterio_monto.monto1',
        'adm_criterio_monto.id_operador2',
        'op2.signo as signo_operador2',
        'op2.descripcion as descripcion_operador2',
        'adm_criterio_monto.monto2',
        'adm_criterio_monto.estado'
    )
    ->leftJoin('administracion.operadores as op1', 'op1.id_operador', '=', 'adm_criterio_monto.id_operador1')
    ->leftJoin('administracion.operadores as op2', 'op2.id_operador', '=', 'adm_criterio_monto.id_operador2')
    ->where($option)
    ->orderBy('adm_criterio_monto.id_criterio_monto', 'asc')
    ->get();
    $output['data']=$data;
    return response()->json($output);
}

public function mostrarGrupoCriterioByIdFlujo($id_flujo =null){
    $option=[];
    if($id_flujo >0){
        $option[]=['adm_detalle_grupo_criterios.id_flujo', '=', $id_flujo];
    }

    $sql_detalle_grupo = DB::table('administracion.adm_detalle_grupo_criterios')
    ->select(
        'adm_detalle_grupo_criterios.*'
    )
    ->where($option)
    ->orderBy('adm_detalle_grupo_criterios.id_detalle_grupo_criterios', 'asc')
    ->get();


    $id_grupo_criterio_list =[];
    foreach($sql_detalle_grupo as $data){
        $id_grupo_criterio_list[] = $data->id_grupo_criterios;
    }

    $sql_grupo = DB::table('administracion.adm_grupo_criterios')
    ->select(
        'adm_grupo_criterios.*'
    )
    ->whereIn('adm_grupo_criterios.id_grupo_criterios',$id_grupo_criterio_list)
    ->orderBy('adm_grupo_criterios.id_grupo_criterios', 'asc')
    ->get();


    return response()->json($sql_grupo);

}

public function mostrarCriterio($id_flujo,$id_grupo_criterio){
    $option=[];
    if($id_flujo >0){
        $option[]=['adm_detalle_grupo_criterios.id_flujo', '=', $id_flujo];
    }
    if($id_grupo_criterio >0){
        $option[]=['adm_detalle_grupo_criterios.id_grupo_criterios', '=', $id_grupo_criterio];
    }

    $sql_detalle_grupo = DB::table('administracion.adm_detalle_grupo_criterios')
    ->select(
        'adm_detalle_grupo_criterios.*'
    )
    ->where($option)
    ->orderBy('adm_detalle_grupo_criterios.id_detalle_grupo_criterios', 'asc')
    ->get();


    return response()->json($sql_detalle_grupo);

}


public function updateCriterioMonto(Request $request){

    $criterio_monto= $request->all();
    $id_criterio_monto = $criterio_monto['id_criterio_monto'];
    $descripcion_monto = $criterio_monto['descripcion_monto'];
    $id_operador1 = $criterio_monto['operador1'] >0?$criterio_monto['operador1']:null;
    $monto1 = $criterio_monto['monto1'];
    $id_operador2 = $criterio_monto['operador2'] >0?$criterio_monto['operador2']:null;
    $monto2 = $criterio_monto['monto2'];
    $estado =$criterio_monto['estado'];

    $status='';
    $update=0;

    if( $estado >0){
        $update = DB::table('administracion.adm_criterio_monto')->where('id_criterio_monto', $id_criterio_monto)
        ->update([
            'descripcion' => $descripcion_monto,
            'id_operador1' => $id_operador1,
            'monto1' => $monto1,
            'id_operador2' => $id_operador2,
            'monto2' => $monto2,
            'estado' => $estado
        ]);
    }else{
        $update = DB::table('administracion.adm_criterio_monto')->where('id_criterio_monto', $id_criterio_monto)
        ->update([
            'descripcion' => $descripcion_monto,
            'id_operador1' => $id_operador1,
            'monto1' => $monto1,
            'id_operador2' => $id_operador2,
            'monto2' => $monto2
        ]);
    }


    if($update >0){
        $status='ACTUALIZADO';
    }else{
        $status='NO_ACTUALIZADO';
    }

    return  response()->json($status);
}

public function saveCriterioMonto(Request $request){

    $criterio_monto= $request->all();
    $descripcion_monto = $criterio_monto['descripcion_monto'];
    $id_operador1 = $criterio_monto['operador1'] >0?$criterio_monto['operador1']:null;
    $monto1 = $criterio_monto['monto1'];
    $id_operador2 = $criterio_monto['operador2'] >0?$criterio_monto['operador2']:null;
    $monto2 = $criterio_monto['monto2'];
    $estado =$criterio_monto['estado'];

    $status='';

    $save = DB::table('administracion.adm_criterio_monto')->insertGetId(
        [
            'descripcion' => $descripcion_monto,
            'id_operador1' => $id_operador1,
            'monto1' => $monto1,
            'id_operador2' => $id_operador2,
            'monto2' => $monto2,
            'estado' => $estado
        ],
        'id_criterio_monto'
    );

    if($save >0){
        $status='GUARDADO';
    }else{
        $status='NO_GUARDADO';
    }

    return  response()->json($status);


}

public function mostrarCriterioPrioridad($id_criterio_prioridad){
    $option=[];
    if($id_criterio_prioridad >0){
        $option[]=['adm_prioridad.id_prioridad', '=', $id_criterio_prioridad];
    }

    $data = DB::table('administracion.adm_prioridad')
    ->select(
        'adm_prioridad.id_prioridad',
        'adm_prioridad.descripcion',
        'adm_prioridad.estado'
    )
    ->where($option)
    ->orderBy('adm_prioridad.id_prioridad', 'asc')
    ->get();
    $output['data']=$data;
    return response()->json($output);
}

public function updateCriterioPrioridad(Request $request){

    $criterio_prioridad= $request->all();
    $id_criterio_prioridad = $criterio_prioridad['id_criterio_prioridad'];
    $descripcion_prioridad = $criterio_prioridad['descripcion_prioridad'];
    $estado =$criterio_prioridad['estado'];

    $status='';
    $update=0;

    if( $estado >0){
        $update = DB::table('administracion.adm_prioridad')->where('id_prioridad', $id_criterio_prioridad)
        ->update([
            'descripcion' => $descripcion_prioridad,
            'estado' => $estado
        ]);
    }else{
        $update = DB::table('administracion.adm_prioridad')->where('id_prioridad', $id_criterio_prioridad)
        ->update([
            'descripcion' => $descripcion_prioridad
        ]);
    }


    if($update >0){
        $status='ACTUALIZADO';
    }else{
        $status='NO_ACTUALIZADO';
    }

    return  response()->json($status);
}


public function saveCriterioPrioridad(Request $request){

    $criterio_prioridad= $request->all();
    $descripcion_prioridad = $criterio_prioridad['descripcion_prioridad'];
    $estado =$criterio_prioridad['estado'];

    $status='';

    $save = DB::table('administracion.adm_prioridad')->insertGetId(
        [
            'descripcion' => $descripcion_prioridad,
            'estado' => $estado
        ],
        'id_prioridad'
    );

    if($save >0){
        $status='GUARDADO';
    }else{
        $status='NO_GUARDADO';
    }

    return  response()->json($status);
}


public function saveAignarCriterio(Request $request){
    $asignar_criterio= $request->all();
    $id_criterio_monto = $asignar_criterio['id_criterio_monto'];
    $id_criterio_prioridad =$asignar_criterio['id_criterio_prioridad'];
    $id_grupo_criterios =$asignar_criterio['id_grupo_criterio'];
    $estado_grupo_criterio =$asignar_criterio['estado_grupo_criterio'];
    $id_flujo =$asignar_criterio['id_flujo'];
    // $id_detalle_grupo_criterios =$asignar_criterio['id_detalle_grupo_criterios'];

    $status='';

    $save = DB::table('administracion.adm_detalle_grupo_criterios')->insertGetId(
        [
            'id_grupo_criterios' => $id_grupo_criterios,
            'id_criterio_monto' => $id_criterio_monto,
            'id_criterio_prioridad' => $id_criterio_prioridad,
            'id_flujo' => $id_flujo,
            'estado' => 1
        ],
        'id_detalle_grupo_criterios'
    );

    if($save >0){
        $status='GUARDADO';
    }else{
        $status='NO_GUARDADO';
    }

    return  response()->json($status);
}

public function updateAsignarCriterio(Request $request){
    $asignar_criterio= $request->all();
    $id_criterio_monto = $asignar_criterio['id_criterio_monto'];
    $id_criterio_prioridad =$asignar_criterio['id_criterio_prioridad'];
    $id_grupo_criterios =$asignar_criterio['id_grupo_criterio'];
    $estado_grupo_criterio =$asignar_criterio['estado_grupo_criterio'];
    $id_flujo =$asignar_criterio['id_flujo'];
    $id_detalle_grupo_criterios =$asignar_criterio['id_detalle_grupo_criterios'];
    $estado =$asignar_criterio['estado'];

    if($estado >0){
        $update = DB::table('administracion.adm_detalle_grupo_criterios')
        ->where('id_detalle_grupo_criterios', $id_detalle_grupo_criterios)
        ->update([
            'id_grupo_criterios' => $id_grupo_criterios,
            'id_criterio_monto' => $id_criterio_monto,
            'id_criterio_prioridad' => $id_criterio_prioridad,
            'id_flujo' => $id_flujo,
            'estado' => $estado
        ]);
    }else{
        $update = DB::table('administracion.adm_detalle_grupo_criterios')
        ->where('id_detalle_grupo_criterios', $id_detalle_grupo_criterios)
        ->update([
            'id_grupo_criterios' => $id_grupo_criterios,
            'id_criterio_monto' => $id_criterio_monto,
            'id_criterio_prioridad' => $id_criterio_prioridad,
            'id_flujo' => $id_flujo
        ]);
    }
    $status='';



    if($update >0){
        $status='ACTUALIZADO';
    }else{
        $status='NO_ACTUALIZADO';
    }

    return  response()->json($status);
}


public function saveGrupoCriterio(Request $request){
    $grupoCriterio= $request->all();
    $descripcion_grupo_criterio =$grupoCriterio['descripcion_grupo_criterio'];

    $status='';

    $save = DB::table('administracion.adm_grupo_criterios')->insertGetId(
        [
            'descripcion' => $descripcion_grupo_criterio,
            'fecha_registro' => date('Y-m-d'),
            'estado' => 1
        ],
        'id_grupo_criterios'
    );

    if($save >0){
        $status='GUARDADO';
    }else{
        $status='NO_GUARDADO';
    }

    return  response()->json($status);
}

public function updateGrupoCriterio(Request $request){
    $grupoCriterio= $request->all();
    $id_grupo_criterio = $grupoCriterio['id_grupo_criterio'];
    $descripcion_grupo_criterio =$grupoCriterio['descripcion_grupo_criterio'];
    $estado =$grupoCriterio['estado'];
    $status='';


    if($estado >0){
        $update = DB::table('administracion.adm_grupo_criterios')
        ->where('id_grupo_criterios', $id_grupo_criterio)
        ->update([
            'descripcion' => $descripcion_grupo_criterio,
            'estado' => $estado
        ]);
    }else{
        $update = DB::table('administracion.adm_grupo_criterios')
        ->where('id_grupo_criterios', $id_grupo_criterio)
        ->update([
            'descripcion' => $descripcion_grupo_criterio
        ]);
    }

    if($update >0){
        $status='ACTUALIZADO';
    }else{
        $status='NO_ACTUALIZADO';
    }

    return  response()->json($status);
}

// correo coorporativo

public function mostrar_correo_coorporativo($id_smtp_authentication =null){
    $option=[];
    if($id_smtp_authentication >0){
        $option[]=['smtp_authentication.id_smtp_authentication', '=', $id_smtp_authentication];
    }

    $data = DB::table('configuracion.smtp_authentication')
    ->select(
        'smtp_authentication.*',
        'adm_contri.razon_social'
    )
    ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'smtp_authentication.id_empresa')
    ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
    ->where($option)
    ->orderBy('smtp_authentication.id_smtp_authentication', 'asc')
    ->get();
    $output['data']=$data;
    return response()->json($output);
}

public function guardar_correo_coorporativo(Request $request){
    $save = DB::table('configuracion.smtp_authentication')
    ->insertGetId([
        'smtp_server'   => $request->smtp_server,
        'port'          => $request->port,
        'encryption'    => $request->encryption,
        'email'         => $request->email,
        'password'      => $request->password,
        'fecha_registro'=> date('Y-m-d H:i:s'),
        'estado'        => $request->estado
    ],
    'id_smtp_authentication'
    );

    return  response()->json($save);
}


public function actualizar_correo_coorporativo(Request $request){
    $data = DB::table('configuracion.smtp_authentication')
    ->where('id_smtp_authentication', $request->id_smtp_authentication)
    ->update([
        'id_empresa'   => $request->empresa,
        'smtp_server'   => $request->smtp_server,
        'port'          => $request->port,
        'encryption'    => $request->encryption,
        'email'         => $request->email,
        'password'      => $request->password,
        'estado'        => $request->estado
    ]);
    return response()->json($data);
}

public function anular_correo_coorporativo($id){
    $estado_anulado =(new LogisticaController)->get_estado_doc('Anulado');

    $data = DB::table('configuracion.smtp_authentication')->where('id_smtp_authentication', $id)
    ->update([
        'estado'    => $estado_anulado
    ]);
    return response()->json($data);
}

public function guardar_configuracion_socket(Request $request){
    $save = DB::table('configuracion.socket_setting')
    ->insertGetId([
        'modo'   => $request->modo,
        'host'   => $request->host,
        'activado' => $request->activado
    ],
    'id'
    );

    return  response()->json($save);
}

public function actualizar_configuracion_socket(Request $request){
    $data = DB::table('configuracion.socket_setting')
    ->where('id', $request->id)
    ->update([
        'modo'   => $request->modo,
        'host'   => $request->host,
        'activado'        => $request->activado
    ]);
    return response()->json($data);
}

public function anular_configuracion_socket($id){
    $data = DB::table('configuracion.socket_setting')->where('id', $id)
    ->update([
        'activado'    => false
    ]);
    return response()->json($data);
}


/* FUNCIONES */
    function leftZero($lenght, $number){
		$nLen = strlen($number);
		$zeros = '';
		for($i=0; $i<($lenght-$nLen); $i++){
			$zeros = $zeros.'0';
		}
		return $zeros.$number;
    }


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
    public function socket_setting($option){
        $data=[];
        $status=0;

        if($option == 'all'){
            $socket =  DB::table('configuracion.socket_setting')
            ->get();

            if($socket->count()>0){
                $data=  $socket;
                $status= 200;
            }else{
                $status= 500;
            }
        }
        elseif($option == 'activado'){
            $socket =  DB::table('configuracion.socket_setting')
            ->where('activado', true)
            ->get();

            if($socket->count()>0){
                $data=  $socket->first();
                $status= 200;
            }else{
                $status= 500;
            }
        }
        elseif($option > 0){
            $socket =  DB::table('configuracion.socket_setting')
            ->where('id', $option)
            ->get();

            if($socket->count()>0){
                $data=  $socket->first();
                $status= 200;
            }else{
                $status= 500;
            }
        }
        $output=['status'=>$status, 'data'=>$data];

        return response()->json($output);

    }

    public function arbol_modulos($id_rol){

        // $allRol = Auth::user()->getAllRol();
        // $idRolList=[];
        // foreach($allRol as $rol){
        //         $idRolList[]= $rol->id_rol;
        // }
        $idRolList[]=$id_rol;

        $sis_accion_rol = DB::table('configuracion.sis_accion_rol')
        ->select('sis_accion_rol.*')
        ->whereIn('id_rol', $idRolList)
        ->where([['estado', '=', 1]])
        ->orderBy('id_accion_rol', 'asc')
        ->get();

        $idAccionRol=[];
        foreach ($sis_accion_rol as $data){
            $idAccionRol[]=$data->id_accion;
        }


        $sis_modulo = DB::table('configuracion.sis_modulo')
        ->select('sis_modulo.*')
        ->where([['estado', '=', 1]])
        ->orderBy('descripcion', 'asc')
        ->get();

        $arbol_modulo=[];
        $arbol_sub_modulo=[];
        foreach($sis_modulo as $data){
            if($data->tipo_modulo ==1){
                $arbol_modulo[]=[
                    'id_modulo'=>$data->id_modulo,
                    'modulo'=>$data->descripcion,
                    'sub_modulo'=>[]
                ];
            }
            if($data->tipo_modulo ==2){
                $arbol_sub_modulo[]=[
                    'id_modulo'=>$data->id_modulo,
                    'id_padre'=>$data->id_padre,
                    'modulo'=>$data->descripcion
                ];
            }
        }

        foreach($arbol_modulo as $key_am => $am){
            foreach($sis_modulo as $key_sm => $sm){
                if($am['id_modulo'] == $sm->id_padre){
                    $arbol_modulo[$key_am]['sub_modulo'][]=[
                        'id_sub_modulo'=>$sm->id_modulo,
                        'id_padre'=>$sm->id_padre,
                        'descripcion'=>$sm->descripcion,
                        'sub_modulo_hijo'=>[]
                    ];
                }
            }
        }

        foreach($arbol_modulo as $key_am => $am){
            foreach($am['sub_modulo'] as $key_sm => $sm){
                foreach($arbol_sub_modulo as $key_asm => $asm){
                    if($sm['id_sub_modulo'] == $asm['id_padre']){
                        $arbol_modulo[$key_am]['sub_modulo'][$key_sm]['sub_modulo_hijo'][]= $asm ;
                    }

                }
            }
        }

        $sis_aplicacion = DB::table('configuracion.sis_aplicacion')
        ->select('sis_aplicacion.id_aplicacion','sis_aplicacion.id_sub_modulo','sis_aplicacion.descripcion')
        ->where([['estado', '=', 1]])
        ->orderBy('descripcion', 'asc')
        ->get();



        foreach($arbol_modulo as $key_am => $am){
            foreach($am['sub_modulo'] as $key_sm => $sm){
                if(isset($sm['sub_modulo_hijo'])){
                    foreach($sm['sub_modulo_hijo'] as $key_sh => $sh){
                        foreach($sis_aplicacion as $key_sa => $sa){
                            if($sa->id_sub_modulo == $sh['id_modulo']){
                                $arbol_modulo[$key_am]['sub_modulo'][$key_sm]['sub_modulo_hijo'][$key_sh]['aplicacion'][]= [
                                    'id_aplicacion'=>$sa->id_aplicacion,
                                    'id_sub_modulo'=>$sa->id_sub_modulo,
                                    'descripcion'=>$sa->descripcion,
                                    'accion'=>[]
                                    ] ;
                            }

                        }
                    }

                }
            }
        }

        $sis_accion = DB::table('configuracion.sis_accion')
        ->select('sis_accion.id_accion','sis_accion.id_aplicacion','sis_accion.descripcion')
        ->where([['estado', '=', 1]])
        ->orderBy('id_accion', 'asc')
        ->get();


        foreach($arbol_modulo as $key_am => $am){
            foreach($am['sub_modulo'] as $key_sm => $sm){
                if(isset($sm['sub_modulo_hijo'])){
                    foreach($sm['sub_modulo_hijo'] as $key_sh => $sh){
                        if(isset($sh['aplicacion'])){
                            foreach($sh['aplicacion'] as $key_a => $ap){
                                foreach($sis_accion as $key_ac => $ac){
                                    if($ac->id_aplicacion == $ap['id_aplicacion']){
                                        if(in_array($ac->id_accion,$idAccionRol)==true){
                                            $arbol_modulo[$key_am]['sub_modulo'][$key_sm]['sub_modulo_hijo'][$key_sh]['aplicacion'][$key_a]['accion'][]= [
                                                'id_accion'=> $ac->id_accion,
                                                'id_aplicacion'=> $ac->id_aplicacion,
                                                'descripcion'=> $ac->descripcion,
                                                'permiso'=> true
                                                ] ;
                                        }else{
                                            $arbol_modulo[$key_am]['sub_modulo'][$key_sm]['sub_modulo_hijo'][$key_sh]['aplicacion'][$key_a]['accion'][]= [
                                                'id_accion'=> $ac->id_accion,
                                                'id_aplicacion'=> $ac->id_aplicacion,
                                                'descripcion'=> $ac->descripcion,
                                                'permiso'=> false
                                                ] ;
                                        }
                                    }
                                }
                            }
                        }

                    }
                }
            }
        }

    return $arbol_modulo;

    }


   public function actualizar_accesos_usuario(Request $request){
        $id_rol= $request->id_rol;
        $accesos= $request->accesos;
        $status=0;

        $accion_rol_usuario_actual = DB::table('configuracion.sis_accion_rol')
        ->select('sis_accion_rol.*')
        ->where([['id_rol','=',$id_rol]])
        ->orderBy('id_accion_rol', 'asc')
        ->get();

        $id_accion_rol_usuario_actual_list=[];
        foreach($accion_rol_usuario_actual as $data){
            $id_accion_rol_usuario_actual_list[]=$data->id_accion;
        }

        $count_accesos= count($accesos);
        $id_accion_list=[];
        if ($count_accesos > 0) {
            for ($i = 0; $i < $count_accesos; $i++) {
                if(in_array($accesos[$i]['id_accion'],$id_accion_rol_usuario_actual_list)==true){
                    //actualizar
                    $update = DB::table('configuracion.sis_accion_rol')->where([['id_accion', $accesos[$i]['id_accion']],['id_rol',$id_rol]])
                    ->update([
                        'estado' => $accesos[$i]['valor']=='true'?1:0
                    ]);
                    $status=200;

                }else{
                    //crear nuevo id_accion_rol
                    $id_accion_rol = DB::table('configuracion.sis_accion_rol')->insertGetId(
                        [
                            'id_rol'    => $id_rol,
                            'id_accion' => $accesos[$i]['id_accion'],
                            'estado'    => $accesos[$i]['valor']=='true'?1:0

                        ],
                        'id_accion_rol'
                    );
                    if($id_accion_rol>0){
                        $status=200;

                    }else{
                        $status=204;
                    }

                }
            }
        }

        $output=['status'=>$status];
        return response()->json($output);
    }
    public function usuarioAsignar()
    {
        return view('configuracion/notas_lanzamiento');
    }
    public function usuarioAcceso($id)
    {
        # code...
        // return $id;
        return view('configuracion/usuario_accesos',compact('id'));
    }
    public function getUsuario($id)
    {
        $users = DB::table('configuracion.sis_usua')
        ->select('sis_usua.id_usuario','sis_usua.nombre_corto',DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_completo_usuario"))
        ->where('sis_usua.id_usuario',$id)
        ->join('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_trab.id_postulante', '=', 'rrhh_postu.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_postu.id_persona', '=', 'rrhh_perso.id_persona')
        ->first();
        return response()->json($users);
    }
    public function getModulos()
    {
        $modulos =DB::table('configuracion.sis_modulo')->where([['sis_modulo.estado', '=', 1],['sis_modulo.id_padre', '=', 0]])->get();
        if (sizeof($modulos)>0) {
            return response()->json([
                "status"=>200,
                "modulos"=>$modulos
            ]);
        }else{
            return response()->json([
                "status"=>404
            ]);
        }

    }
    public function asiganrModulos(Request $request)
    {
        $modulos_ids = $request->checkModulo;
        $id_usuario = $request->id_usuario;
        $modulos_padre =[];
        $guardar_acceso = [];
        foreach ($modulos_ids as $key => $value) {
            $modulo = DB::table('configuracion.sis_modulo')
                ->select('sis_modulo.*')
                ->where([['sis_modulo.id_modulo', '=', $value]])
                ->first();
            array_push($guardar_acceso,$modulo);
            while ($modulo->id_padre != 0) {
                $modulo = DB::table('configuracion.sis_modulo')
                    ->select('sis_modulo.*')
                    ->where([['sis_modulo.id_modulo', '=', $modulo->id_padre]])
                    ->first();
            };
            if (!in_array($modulo->id_modulo, $modulos_padre)) {
                array_push($modulos_padre,$modulo->id_modulo);
                array_push($guardar_acceso,$modulo);
            }
        }
        // $affected = DB::update('update configuracion.sis_acceso set estado = 7 where id_usuario = ?', [$id_usuario]);
        DB::table('configuracion.sis_acceso_atributo')
            ->where('sis_acceso_atributo.id_usuario', $request->id_usuario)
            ->update(['sis_acceso_atributo.estado' => 7]);
        foreach ($guardar_acceso as $key => $value) {
            $ver=0;$nuevo=0;$modificar=0;$eliminar=0;

            if ($request->ver) {
                foreach ($request->ver as $key_item => $value_item) {
                    if ($key_item==$value->id_modulo) {
                        $ver = (int) $value_item[0];
                    }
                    if ($value->id_padre===0) {
                        $ver = (int) 1;
                    }
                }
            }
            if ($request->nuevo) {
                foreach ($request->nuevo as $key_item => $value_item) {
                    if ($key_item==$value->id_modulo) {
                        $nuevo = (int) $value_item[0];
                    }
                }
            }
            if ($request->modificar) {
                foreach ($request->modificar as $key_item => $value_item) {
                    if ($key_item==$value->id_modulo) {
                        $modificar = (int) $value_item[0];
                    }
                }
            }

            if ($request->eliminar) {
                foreach ($request->eliminar as $key_item => $value_item) {
                    if ($key_item==$value->id_modulo) {
                        $eliminar = (int) $value_item[0];
                    }
                }
            }

            $value->accesos=array(
                "ver"=>$ver,
                "nuevo"=>$nuevo,
                "modificar"=>$modificar,
                "eliminar"=>$eliminar,
            );

            $id_acceso_atributo = DB::table('configuracion.sis_acceso_atributo')->insertGetId(
                [
                    'ver'           => $ver,
                    'nuevo'         => $nuevo,
                    'modificar'     => $modificar,
                    'eliminar'      => $eliminar,
                    'id_modulo'     => $value->id_modulo,
                    'id_usuario'    => $id_usuario,
                    'estado'        => 1
                ],
                'id_acceso_atributo'
            );
        }
        return response()->json($id_acceso_atributo);
        // return response()->json($request);
    }
    public function cambiarClave(Request $request)
    {
        $usuario = SisUsua::find($request->id_usuario);
            $usuario->clave = StringHelper::encode5t($request->nueva_clave);
            $usuario->password = Hash::make($request->nueva_clave);
        $usuario->save();
        return response()->json(["status" => 200, "success" => true, "modelo" => $usuario]);

    }
    public function viewAccesos($id)
    {
        $usuario = SisUsua::select(
            'sis_usua.*',
            'rrhh_trab.*',
            'rrhh_postu.*',
            'rrhh_perso.*'
        )
        ->join('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_trab.id_postulante', '=', 'rrhh_postu.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_postu.id_persona', '=', 'rrhh_perso.id_persona')
        ->where('id_usuario',$id)
        ->first();
        $modulos =DB::table('configuracion.modulos')->where('estado',1)->where('id_padre',0)->get();
        return view('configuracion.usuario_accesos', compact('modulos','id','usuario'));
    }
    public function getModulosAccion(Request $request)
    {
        $success=false;
        $status=400;
        $sub_modulos =[];

        $array__modulos=[];

        if ($request->data) {
            $success=true;
            $status = 200;
            $sub_modulos = DB::table('configuracion.modulos')
            ->select(
                'modulos.id_modulo',
                'modulos.descripcion as modulo',
                'accesos.id_acceso',
                'accesos.descripcion as acceso'
            )
            ->join('configuracion.accesos', 'accesos.id_modulo', '=', 'modulos.id_modulo','left')
            ->where('modulos.estado',1)
            // ->where('modulos.id_padre',18)
            ->where('modulos.id_padre',$request->data)
            ->get();

            foreach ($sub_modulos as $key => $value) {
                $value->modulos_hijos=[];
                if ($value->acceso ===null) {

                    $sub_modulos_hijos = DB::table('configuracion.modulos')
                    ->select(
                        'modulos.id_modulo',
                        'modulos.descripcion as modulo',
                        'accesos.id_acceso',
                        'accesos.descripcion as acceso'
                    )
                    ->join('configuracion.accesos', 'accesos.id_modulo', '=', 'modulos.id_modulo')
                    ->where('modulos.estado',1)
                    ->where('modulos.id_padre',$value->id_modulo)
                    // ->where('modulos.id_padre',89)
                    ->orderBy('modulo','ASC')
                    ->get();

                    if (sizeof($sub_modulos_hijos)>0) {
                        $value->modulos_hijos = $sub_modulos_hijos;

                        // foreach ($sub_modulos_hijos as $key_hijos => $value_hijos) {
                        //     $value_hijos->modulos_hijos_hijos=[];
                        //     if ($value_hijos->acceso ===null) {
                        //         $sub_modulos_hijos_tercer_nivel = DB::table('configuracion.modulos')
                        //         ->select(
                        //             'modulos.id_modulo',
                        //             'modulos.descripcion as modulo',
                        //             'accesos.id_acceso',
                        //             'accesos.descripcion as acceso'
                        //         )
                        //         ->join('configuracion.accesos', 'accesos.id_modulo', '=', 'modulos.id_modulo')
                        //         ->where('modulos.id_padre',$value_hijos->id_modulo)
                        //         ->where('modulos.estado',1)
                        //         ->orderBy('modulo','ASC')
                        //         ->get();

                        //         if (sizeof($sub_modulos_hijos_tercer_nivel)>0) {
                        //             $value_hijos->modulos_hijos_hijos = $sub_modulos_hijos_tercer_nivel;
                        //         }
                        //     }

                        // }


                    }

                }
            }
        }
        return response()->json([
            "success"=>$success,
            "status"=>$status,
            "sub_modulos"=>$sub_modulos
        ]);
    }
    public function guardarAccesos(Request $request)
    {
        // return $request;exit;
        if ($request->id_usuario) {
            // return $request;exit;
            AccesosUsuarios::where('id_usuario',$request->id_usuario)->where('estado',1)->update([
                "estado"=>0
            ]);
            if ($request->id_modulo_padre) {
                foreach ($request->id_modulo_padre as $key_modulo_padre => $value_modulo_hijos) {
                    foreach ($value_modulo_hijos as $key_modulo_hijo => $value_accesos) {
                        foreach ($value_accesos as $key_accesos => $value_acceso) {
                            $accesos_uduario = new AccesosUsuarios;
                            $accesos_uduario->id_acceso = $value_acceso;
                            $accesos_uduario->id_usuario = $request->id_usuario;
                            $accesos_uduario->id_modulo = $key_modulo_hijo;
                            $accesos_uduario->estado = 1;
                            $accesos_uduario->id_padre = $key_modulo_padre;
                            $accesos_uduario->save();

                        }

                    }

                }
            }

            return response()->json([
                "success"=>true,
                "status"=>200
            ]);
        }else{
            return response()->json([
                "success"=>false,
                "status"=>404
            ]);
        }


    }
    public function accesoUsuario($id)
    {
        $accesos_uduarios = AccesosUsuarios::where('id_usuario',$id)
            ->where('estado',1)
            ->orderBy('id_modulo','ASC')
            ->get();

        foreach ($accesos_uduarios as $key => $value) {
            $value->accesos;
            // if ($value->accesos) {
                $value->accesos->modulos;
            // }else{
            //     return $value;
            // }
            $value->moduloPadre;
            // return $value;
        }
        // return $accesos_uduarios;
        return response()->json([
            "success"=>true,
            "data"=>$accesos_uduarios
        ]);
    }
    public function prueba()
    {
        // data de usuarios de necesidades
        $data_usuarios = SisUsua::whereIn('id_usuario',[135,124,18,132,59,129,8,65,123,122,1,119,40,2,62,114,113,112,109,66,107,106,97,93,77,91,85,78,60,33,75,53,4,6,64,125,58,56,54,50,48,38,37,36,126,22,32,16,27,26,24,21,20,17,10,5,3,14,111,73,108,9,31,130,61,127,131,128,99])->get();
        // data de usuarios de logistica
        $modulo = TableConfiguracionModulo::where('estado',1)->where('id_padre',47)->get();
        foreach ($modulo as $key => $value) {
            $value->accesosAll ;
            if (sizeof($value->accesosAll)===0) {
                $value->modulo_nivel2 = TableConfiguracionModulo::where('estado',1)->where('id_padre',$value->id_modulo)->get();
                foreach ($value->modulo_nivel2 as $key_nivel2 => $value_nivel2) {
                    $value_nivel2->accesosAll ;
                }
            }
        }

        $array_accesos_usuarios = array();

        foreach ($modulo as $key_modulo => $value) {
            if (sizeof($value->accesosAll)>0) {
                foreach ($value->accesosAll as $key_accesos => $value_accesos) {
                    array_push($array_accesos_usuarios, (object)array(
                        "id_acceso" =>  $value_accesos->id_acceso,
                        "id_usuario"=>  111,
                        "estado"    =>  1,
                        "id_modulo" =>  $value_accesos->id_modulo,
                        "id_padre"  =>  0,
                    ));
                }

            }
            if ( isset($value->modulo_nivel2) &&sizeof($value->modulo_nivel2)>0) {
                foreach ($value->modulo_nivel2 as $key_nivel2 => $value_nivel2) {
                    foreach ($value_nivel2->accesosAll as $key_accesos => $value_accesos) {
                        array_push($array_accesos_usuarios,(object)array(
                            "id_acceso" =>  $value_accesos->id_acceso,
                            "id_usuario"=>  111,
                            "estado"    =>  1,
                            "id_modulo" =>  $value_accesos->id_modulo,
                            "id_padre"  =>  $value_nivel2->id_padre,
                        ));
                    }
                }

            }
        }

        foreach ($data_usuarios as $key_usuario => $value_usuario) {
            foreach ($array_accesos_usuarios as $key_accesos => $value_accesos) {
                // return $value_accesos->id_acceso;
                $accesos_usuarios = new AccesosUsuarios;
                $accesos_usuarios->id_acceso    =   $value_accesos->id_acceso;
                $accesos_usuarios->id_usuario   =   $value_usuario->id_usuario;
                $accesos_usuarios->estado       =   $value_accesos->estado;
                $accesos_usuarios->id_modulo    =   $value_accesos->id_modulo;
                $accesos_usuarios->id_padre     =   $value_accesos->id_padre;
                $accesos_usuarios->save();
            }
        }
        return response()->json([
            "success"=>true,
            "status"=>200
        ]);
    }
    public function scripts($variable = null)
    {

        if ($variable!==null) {
            $id_modulo=0;
            $data_usuarios=[];
            $modulo=[];
            $script=null;
            switch ($variable) {
                case '1':
                    // logistica
                    $id_modulo=18;
                    $data_usuarios = SisUsua::whereIn('id_usuario',[1,3,5,14,16,17,22,27,32,33,54,59,60,61,62,64,65,66,75,77,78,93,97,99,119,122,123,128,130,135])->get();
                    break;

                case '2':
                    // almacen
                    $data_usuarios = SisUsua::whereIn('id_usuario',[135,133,131,58,130,128,126,8,125,124,61,123,122,121,119,118,117,99,97,93,78,77,76,75,71,66,65,64,62,60,59,54,36,33,32,31,27,22,21,17,16,14,5,3,1])->get();
                    $id_modulo=3;
                    break;
            }
            $modulo = TableConfiguracionModulo::where('estado',1)->where('id_padre',$id_modulo)->get();
            foreach ($modulo as $key => $value) {
                $value->accesosAll ;
                if (sizeof($value->accesosAll)===0) {
                    $value->modulo_nivel2 = TableConfiguracionModulo::where('estado',1)->where('id_padre',$value->id_modulo)->get();
                    foreach ($value->modulo_nivel2 as $key_nivel2 => $value_nivel2) {
                        $value_nivel2->accesosAll ;
                    }
                }
            }


            $array_accesos_usuarios = array();

            foreach ($modulo as $key_modulo => $value) {
                if (sizeof($value->accesosAll)>0) {
                    foreach ($value->accesosAll as $key_accesos => $value_accesos) {
                        array_push($array_accesos_usuarios, (object)array(
                            "id_acceso" =>  $value_accesos->id_acceso,
                            "id_usuario"=>  111,
                            "estado"    =>  1,
                            "id_modulo" =>  $value_accesos->id_modulo,
                            "id_padre"  =>  0,
                        ));
                    }

                }
                if ( isset($value->modulo_nivel2) &&sizeof($value->modulo_nivel2)>0) {
                    foreach ($value->modulo_nivel2 as $key_nivel2 => $value_nivel2) {
                        foreach ($value_nivel2->accesosAll as $key_accesos => $value_accesos) {
                            array_push($array_accesos_usuarios,(object)array(
                                "id_acceso" =>  $value_accesos->id_acceso,
                                "id_usuario"=>  111,
                                "estado"    =>  1,
                                "id_modulo" =>  $value_accesos->id_modulo,
                                "id_padre"  =>  $value_nivel2->id_padre,
                            ));
                        }
                    }

                }
            }

            // return $array_accesos_usuarios;exit;

            foreach ($data_usuarios as $key_usuario => $value_usuario) {
                foreach ($array_accesos_usuarios as $key_accesos => $value_accesos) {
                    // return $value_accesos->id_acceso;
                    $accesos_usuarios = new AccesosUsuarios;
                    $accesos_usuarios->id_acceso    =   $value_accesos->id_acceso;
                    $accesos_usuarios->id_usuario   =   $value_usuario->id_usuario;
                    $accesos_usuarios->estado       =   $value_accesos->estado;
                    $accesos_usuarios->id_modulo    =   $value_accesos->id_modulo;
                    $accesos_usuarios->id_padre     =   $value_accesos->id_padre;
                    $accesos_usuarios->save();
                }
            }

            return response()->json([
                "succes"=>true,
                "status"=>200
            ]);
        }else{
            return response()->json([
                "succes"=>false,
                "status"=>404
            ]);
        }
    }
    public function validarDocumento(Request $request)
    {
        if ($request->documento!==null) {
            $documento = rrhh_perso::where('nro_documento',$request->documento)->first();
            if ($documento) {
                return response()->json([
                    "success"=>true,
                    "status"=>200
                ]);
            }else{
                return response()->json([
                    "success"=>false,
                    "status"=>404
                ]);
            }
        }
    }
    public function validarUsuario(Request $request)
    {
        if ($request->usuario!==null) {
            $documento = SisUsua::where('usuario',$request->usuario)->first();
            if ($documento) {
                return response()->json([
                    "success"=>true,
                    "status"=>200
                ]);
            }else{
                return response()->json([
                    "success"=>false,
                    "status"=>404
                ]);
            }
        }
    }
    public function scriptsAccesos()
    {
        // array de accesos
        $array_id_accesos=[78,83,77,79,82,80,81];
        $accesos=array();
        // array de usuarios
        $array_usuarios=[93,77,65,60,36,33,32,27,22,17,16,3,1];
        $json_usuarios=array();
        $usuarios_faltantes=array();

        foreach ($array_usuarios as $key => $value) {
            $accesos_table = SisUsua::where('id_usuario',$value)->first();
            if ($accesos_table) {
                array_push($json_usuarios,$accesos_table);
            } else {
                array_push($usuarios_faltantes,$value);
            }
        }

        $accesos_modulos=array();

        foreach ($array_id_accesos as $key => $value) {
            $accesos_table = Accesos::where('id_acceso',$value)->first();
            array_push($accesos,$accesos_table);

            $modulo = DB::table('configuracion.modulos')->where('id_modulo',$accesos_table->id_modulo)->first();
            array_push($accesos_modulos,array(
                "id_acceso"=>$value,
                "id_modulo"=>$accesos_table->id_modulo,
                "id_padre"=>$modulo->id_padre
            ));
        }

        $usuario_accessos=array();
        foreach ($json_usuarios as $key_usuario => $value_usuario) {
            foreach ($accesos_modulos as $key_accesos => $value_accesos) {
                array_push($usuario_accessos,array(
                    "id_acceso"=>$value_accesos['id_acceso'],
                    "id_modulo"=>$value_accesos['id_modulo'],
                    "id_padre"=>$value_accesos['id_padre'],
                    "id_usuario"=>$value_usuario['id_usuario']
                ));
            }
        }

        foreach ($usuario_accessos as $key => $value) {

            $busacar_accesos = AccesosUsuarios::where('estado',1)->where('id_usuario',$value['id_usuario'])->where('id_acceso',$value['id_acceso'])->first();
            if (!$busacar_accesos) {
                $accesos_usuarios_table= new AccesosUsuarios();
                $accesos_usuarios_table->id_acceso  = $value['id_acceso'];
                $accesos_usuarios_table->id_usuario = $value['id_usuario'];
                $accesos_usuarios_table->estado     = 1;
                $accesos_usuarios_table->id_modulo  = $value['id_modulo'];
                $accesos_usuarios_table->id_padre   = $value['id_padre'];
                $accesos_usuarios_table->save();
            }

        }

        return response()->json([
            "success"=>true,
            "status"=>200,
            "usuarios"=>$usuario_accessos,
            "usuarios_faltantes"=>$usuarios_faltantes,
            "accesos"=>$accesos_modulos
        ]);
    }
}

