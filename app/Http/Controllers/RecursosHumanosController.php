<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Mail;

use Swift_Transport;
use Swift_Message;
use Swift_Mailer;
use Swift_Attachment;
use Swift_IoException;
use Swift_Preferences;

ini_set('max_execution_time', 3600);
date_default_timezone_set('America/Lima');


class RecursosHumanosController extends Controller{

    /* VISTAS */
    function view_main(){
        // $main = $this->rrhh_dashboard('return');
        // $dash = json_decode($main);
        // $dsb_1 = $main['dsb'];
        return view('rrhh/main');
        
    }
    // function rrhh_dashboard($type){
    //     $hoy = date('Y-m-d');
    //     $array = [];
    //     $dsb1 = DB::table('rrhh.rrhh_contra')->where('fecha_fin', '>', $hoy)->get()->count();

    //     $array = ['dsb' => $dsb1];
        
    //     if ($type = 'echo'){
    //         echo $array;
    //     }elseif($type = 'return'){
    //         return $array;
    //     }
    // }
    function view_persona(){
        $doc_identi = $this->select_doc_idendidad();
        $est_civil = $this->select_est_civil();
        return view('rrhh/escalafon/persona', compact('doc_identi', 'est_civil'));
    }
    function view_postulante(){
        $pais = $this->select_pais();
        $pais_frm = $this->select_pais();
        $tipo_archivo = $this->select_tipo_archivo();
        $niv_est = $this->select_niv_est();
        $carrera = $this->select_carrera();
        return view('rrhh/escalafon/postulante', compact('pais', 'pais_frm', 'niv_est', 'carrera', 'tipo_archivo'));
    }
    function view_trabajador(){
        $empresa = $this->select_empresa();
        $cargo = $this->select_cargo();
        $plani = $this->select_tipo_planilla();
        $planil = $this->select_tipo_planilla();
        $tpemp = $this->select_tipo_empleado();
        $categ = $this->select_cate_ocup();
        $pensi = $this->select_pensiones();
        $contra = $this->select_tipo_contrato();
        $modali = $this->select_modalidad();
        $horar = $this->select_horario();
        $cc = $this->select_centro_costos();
        $banco = $this->select_banco();
        $tpcta = $this->select_tipo_cuenta();
        $moneda = $this->select_moneda();
        $rol_conc = $this->select_rol_concepto();
        return view('rrhh/escalafon/trabajador', compact('empresa', 'cargo', 'plani', 'planil', 'tpemp', 'categ', 'pensi', 'contra', 'modali', 'horar', 'cc', 'banco', 'tpcta', 'moneda', 'rol_conc'));
    }
    function view_cargo(){
        return view('rrhh/escalafon/cargo');
    }
    function view_derecho_hab(){
        $condi = $this->select_condicion();
        return view('rrhh/escalafon/derecho_hab', compact('condi'));
    }
    function view_tareo(){
        $empre = $this->select_empresa();
        $empresas = $this->select_empresa();
        $plani = $this->select_tipo_planilla();
        return view('rrhh/control/tareo', compact('empre', 'plani', 'empresas'));
    }
    function view_asistencia(){
        $empre = $this->select_empresa();
        $empresas = $this->select_empresa();
        $plani = $this->select_tipo_planilla();
        return view('rrhh/control/asistencia', compact('empre', 'plani', 'empresas'));
    }
    function view_planilla(){
        $plani = $this->select_tipo_planilla();
        $emp = $this->select_empresa();
        $trab = $this->select_personal();
        $peri = $this->select_periodo();
        return view('rrhh/remuneraciones/planilla', compact('emp', 'plani', 'trab', 'peri'));
    }
    function view_merito(){
        $meri = $this->select_tipo_merito();
        return view('rrhh/escalafon/merito', compact('meri'));
    }
    function view_sancion(){
        $sanci = $this->select_tipo_sancion();
        return view('rrhh/escalafon/demerito', compact('sanci'));
    }
    function view_salidas(){
        $salidas = $this->select_tipo_salida();
        $usuario = $this->select_personal_usuario();
        return view('rrhh/control/salidas', compact('salidas', 'usuario'));
    }
    function view_prestamo(){
        return view('rrhh/control/prestamo');
    }
    function view_vacaciones(){
        return view('rrhh/control/vacaciones');
    }
    function view_licencia(){
        return view('rrhh/control/licencia');
    }
    function view_horas_ext(){
        $usuario = $this->select_personal_usuario();
        return view('rrhh/control/horas_ext', compact('usuario'));
    }
    function view_cese(){
        $baja = $this->select_baja();
        return view('rrhh/control/cese', compact('baja'));
    }

    function view_bonificacion(){
        $bonif = $this->select_tipo_bonif();
        return view('rrhh/remuneraciones/bonificacion', compact('bonif'));
    }
    function view_descuento(){
        $dsct = $this->select_tipo_dscto();
        return view('rrhh/remuneraciones/descuento', compact('dsct'));
    }
    function view_retencion(){
        $reten = $this->select_tipo_retencion();
        return view('rrhh/remuneraciones/retencion', compact('reten'));
    }
    function view_aportacion(){
        $aport = $this->select_tipo_aportacion();
        return view('rrhh/remuneraciones/aportacion', compact('aport'));
    }
    function view_reintegro(){return view('rrhh/remuneraciones/reintegro');}
    
    function view_periodo(){return view('rrhh/control/periodo');}
    function view_horario(){return view('rrhh/variables/horario');}
    function view_tolerancia(){return view('rrhh/variables/tolerancia');}
    function view_est_civil(){return view('rrhh/variables/est_civil');}
    function view_cond_derecho_hab(){return view('rrhh/variables/cond_derecho_hab');}
    function view_niv_estudio(){return view('rrhh/variables/niv_estudios');}
    function view_carrera(){return view('rrhh/variables/carrera');}
    function view_tipo_trabajador(){return view('rrhh/variables/tipo_trabajador');}
    function view_tipo_contrato(){return view('rrhh/variables/tipo_contrato');}
    function view_modalidad(){return view('rrhh/variables/modalidad');}
    function view_concepto_rol(){return view('rrhh/variables/concepto_rol');}
    function view_cat_ocupacional(){return view('rrhh/variables/cat_ocupacional');}
    function view_tipo_planilla(){return view('rrhh/variables/tipo_planilla');}
    function view_tipo_merito(){return view('rrhh/variables/tipo_merito');}
    function view_tipo_demerito(){return view('rrhh/variables/tipo_demerito');}
    function view_tipo_bonificacion(){return view('rrhh/variables/tipo_bonificacion');}
    function view_tipo_descuento(){return view('rrhh/variables/tipo_descuento');}
    function view_tipo_retencion(){return view('rrhh/variables/tipo_retencion');}
    function view_tipo_aportes(){return view('rrhh/variables/tipo_aportes');}
    function view_pension(){return view('rrhh/variables/pension');}
    function view_cv(){return view('rrhh/reportes/datos_personal');}
    function view_busq_postu(){return view('rrhh/reportes/busqueda_postulante');}
    function view_cumple(){return view('rrhh/reportes/cumple');}
    function view_datos_generales(){return view('rrhh/reportes/datos_rrhh');}
    function view_reporte_afp(){
        return view('rrhh/reportes/reporte_afp');
    }
    function view_grupo_trab(){
        $empresa = $this->select_empresa();
        return view('rrhh/reportes/grupo_trabajador', compact('empresa'));
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
        $data = DB::table('administracion.adm_grupo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->select('adm_grupo.id_grupo', 'adm_grupo.descripcion AS nombre_grupo', 'sis_sede.descripcion AS nombre_sede')->where('adm_grupo.estado', '=', 1)
            ->orderBy('sis_sede.descripcion', 'asc')->get();
        return $data;
    }
    public function select_grupo($sede){
        $data = DB::table('administracion.adm_grupo')->select('id_grupo', 'descripcion')->where([['estado', '=', 1], ['id_sede', '=', $sede]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_area($grupo){
        $data = DB::table('administracion.adm_area')->select('id_area', 'descripcion')->where([['estado', '=', 1], ['id_grupo', '=', $grupo]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_est_civil(){
        $data = DB::table('rrhh.rrhh_est_civil')->select('id_estado_civil', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_niv_est(){
        $data = DB::table('rrhh.rrhh_niv_estud')->select('id_nivel_estudio', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_carrera(){
        $data = DB::table('rrhh.rrhh_carrera')->select('id_carrera', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_tipo_archivo(){
        $data = DB::table('administracion.adm_tipo_archivo')->select('id_tipo_archivo', 'descripcion')->where([['estado', '=', 1],['filtro', '=', 'rrhh']])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_periodo(){
        $data = DB::table('administracion.adm_periodo')->select('id_periodo', 'descripcion')->where('estado', '=', 1)
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
    public function select_condicion(){
        $data = DB::table('rrhh.rrhh_cdn_dhab')->select('id_condicion_dh', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_tipo_planilla(){
        $data = DB::table('rrhh.rrhh_tp_plani')->select('id_tipo_planilla', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_tipo_empleado(){
        $data = DB::table('rrhh.rrhh_tp_trab')->select('id_tipo_trabajador', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_cate_ocup(){
        $data = DB::table('rrhh.rrhh_cat_ocupac')->select('id_categoria_ocupacional', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_pensiones(){
        $data = DB::table('rrhh.rrhh_pensi')->select('id_pension', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_tipo_contrato(){
        $data = DB::table('rrhh.rrhh_tp_contra')->select('id_tipo_contrato', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_modalidad(){
        $data = DB::table('rrhh.rrhh_modali')->select('id_modalidad', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_horario(){
        $data = DB::table('rrhh.rrhh_horario')->select('id_horario', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_cargo(){
        $data = DB::table('rrhh.rrhh_cargo')->select('id_cargo', 'descripcion')
            ->where('estado', '=', 1) ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_rol_concepto(){
        $data = DB::table('rrhh.rrhh_rol_concepto')->select('id_rol_concepto', 'descripcion')
            ->where('estado', '=', 1)->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_banco(){
        $data = DB::table('contabilidad.cont_banco')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
                ->select('cont_banco.id_banco', 'adm_contri.razon_social AS descripcion')
                ->orderBy('adm_contri.razon_social', 'asc')->get();
        return $data;
    }
    public function select_tipo_cuenta(){
        $data = DB::table('contabilidad.adm_tp_cta')->select('id_tipo_cuenta', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_moneda(){
        $data = DB::table('configuracion.sis_moneda')->select('id_moneda', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_tipo_merito(){
        $data = DB::table('rrhh.rrhh_var_merito')->select('id_variable_merito', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_tipo_sancion(){
        $data = DB::table('rrhh.rrhh_var_sanci')->select('id_variable_sancion', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_tipo_salida(){
        $data = DB::table('rrhh.rrhh_tp_permi')->select('id_tipo_permiso', 'descripcion')->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_personal_usuario(){
        $data = DB::table('configuracion.sis_usua')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where('sis_usua.estado', '=', 1)
            ->select('sis_usua.id_usuario', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno')
            ->orderBy('rrhh_perso.apellido_paterno', 'asc')->get();
        return $data;
    }
    public function select_personal(){
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where('rrhh_trab.estado', '=', 1)
            ->select('rrhh_trab.id_trabajador', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno')
            ->orderBy('rrhh_perso.apellido_paterno', 'asc')->get();
        return $data;
    }
    public function select_baja(){
        $data = DB::table('rrhh.rrhh_baja')->select('id_baja', 'descripcion')->orderBy('descripcion', 'asc')->where('estado', '=', 1)->get();
        return $data;
    }
    public function select_tipo_bonif(){
        $data = DB::table('rrhh.rrhh_var_bonif')->select('id_variable_bonificacion', 'descripcion')->orderBy('descripcion', 'asc')->where('estado', '=', 1)->get();
        return $data;
    }
    public function select_tipo_dscto(){
        $data = DB::table('rrhh.rrhh_var_dscto')->select('id_variable_descuento', 'descripcion')->orderBy('descripcion', 'asc')->where('estado', '=', 1)->get();
        return $data;
    }

    public function select_tipo_retencion(){
        $data = DB::table('rrhh.rrhh_var_reten')->select('id_variable_retencion', 'descripcion')->orderBy('descripcion', 'asc')->where('estado', '=', 1)->get();
        return $data;
    }

    public function select_tipo_aportacion(){
        $data = DB::table('rrhh.rrhh_var_aport')->select('id_variable_aportacion', 'descripcion')->orderBy('descripcion', 'asc')->where('estado', '=', 1)->get();
        return $data;
    }

    /* PERSONA */
    public function mostrar_persona_table(){
        $data = DB::table('rrhh.rrhh_perso')->where('estado', '=', 1)->orderBy('id_persona', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_persona_id($id){
        $data = DB::table('rrhh.rrhh_perso')->where('id_persona', $id)->get();
        return response()->json($data);
    }
    public function mostrar_longitud_doc($id){
        $data = DB::table('contabilidad.sis_identi')->select('longitud')->where('id_doc_identidad', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_persona(Request $request){
        $sql = DB::table('rrhh.rrhh_perso')->where('nro_documento', '=', $request->nro_documento)->get();
        $fecha_registro = date('Y-m-d H:i:s');

        if ($sql->count() > 0){
            $id = 'exist';
        }else{
            $id = DB::table('rrhh.rrhh_perso')->insertGetId(
                [
                    'id_documento_identidad'    => $request->id_documento_identidad,
                    'nro_documento'             => $request->nro_documento,
                    'nombres'                   => strtoupper($request->nombres),
                    'apellido_paterno'          => strtoupper($request->apellido_paterno),
                    'apellido_materno'          => strtoupper($request->apellido_materno),
                    'fecha_nacimiento'          => $request->fecha_nacimiento,
                    'sexo'                      => $request->sexo,
                    'id_estado_civil'           => $request->id_estado_civil,
                    'estado'                    => 1,
                    'fecha_registro'            => $fecha_registro,
                ],
                'id_persona'
            );
        }
        return response()->json($id);
    }
    public function actualizar_persona(Request $request){
        $data = DB::table('rrhh.rrhh_perso')->where('id_persona', $request->id_persona)
        ->update([
            'id_documento_identidad'    => $request->id_documento_identidad,
            'nro_documento'             => $request->nro_documento,
            'nombres'                   => strtoupper($request->nombres),
            'apellido_paterno'          => strtoupper($request->apellido_paterno),
            'apellido_materno'          => strtoupper($request->apellido_materno),
            'fecha_nacimiento'          => $request->fecha_nacimiento,
            'sexo'                      => $request->sexo,
            'id_estado_civil'           => $request->id_estado_civil,
            'estado'                    => 1,
            'fecha_registro'            => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function anular_persona($id){
        $verify = DB::table('rrhh.rrhh_postu')->where([['rrhh_postu.id_persona', '=', $id], ['rrhh_postu.estado', '=', 1]])->get();

        if ($verify->count() > 0){
            $val = 0;
        }else{
            $data = DB::table('rrhh.rrhh_perso')->where('id_persona', $id)->update(['estado'     => 2]);
            $val = $data;
        }
        return response()->json($val);
    }

    /* POSTULANTE */
    public function mostrar_postulante_table(){
        $data = DB::table('rrhh.rrhh_postu')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_postu.id_postulante', 'rrhh_postu.direccion', 'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno', 'rrhh_perso.nro_documento')
            ->where('rrhh_perso.estado', '=', 1)->orderBy('id_postulante','asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_postulante_id($id){
        $data = DB::table('rrhh.rrhh_postu')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->select('rrhh_postu.*','rrhh_perso.id_persona', 'rrhh_perso.estado', 'rrhh_perso.nro_documento',
                        DB::raw("CONCAT(rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno, ' ',rrhh_perso.nombres) AS datos_persona"))
                ->where('rrhh_postu.id_postulante', $id)->get();
        return response()->json($data);
    }
    public function mostrar_postulante_dni($dni){
        $perso = DB::table('rrhh.rrhh_perso')->select('rrhh_perso.id_persona')->where('rrhh_perso.nro_documento', $dni)->get();
        if ($perso->count() > 0){
            foreach($perso as $value){
                $id_persona = $value->id_persona;
            }
            $postu = DB::table('rrhh.rrhh_postu')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->select('rrhh_postu.id_postulante')
                    ->where('rrhh_perso.nro_documento', $dni)->get();
            if ($postu->count() > 0) {
                $prevData = DB::table('rrhh.rrhh_postu')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->select('rrhh_postu.*','rrhh_perso.id_persona', 'rrhh_perso.estado', 'rrhh_perso.nro_documento',
                                DB::raw("CONCAT(rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno, ' ',rrhh_perso.nombres) AS datos_persona"))
                    ->where('rrhh_perso.nro_documento', $dni)->get();
                $data[0] = ['id_persona' => $id_persona, 'id_postulante' => 1, 'data' => $prevData];
                return response()->json($data);
            }else{
                $prevData = DB::table('rrhh.rrhh_perso')->select('rrhh_perso.id_persona', 'rrhh_perso.estado', 'rrhh_perso.nro_documento',
                                DB::raw("CONCAT(rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno, ' ',rrhh_perso.nombres) AS datos_persona"))
                                ->where('rrhh_perso.nro_documento', $dni)->get();
                $data[0] = ['id_persona' => $id_persona, 'id_postulante' => 0, 'data' => $prevData];
                return response()->json($data);
            }
        }else{
            $data[0] = ['id_persona' => 0];
            return response()->json($data);
        }
    }
    public function mostrar_formacion_acad($id){
        $html = '';
        $data = DB::table('rrhh.rrhh_postu')
            ->join('rrhh.rrhh_frm_acad', 'rrhh_frm_acad.id_postulante', '=', 'rrhh_postu.id_postulante')
            ->join('rrhh.rrhh_niv_estud', 'rrhh_niv_estud.id_nivel_estudio', '=', 'rrhh_frm_acad.id_nivel_estudio')
            ->join('rrhh.rrhh_carrera', 'rrhh_carrera.id_carrera', '=', 'rrhh_frm_acad.id_carrera')
            ->select('rrhh_frm_acad.*', 'rrhh_niv_estud.descripcion AS nivel_est', 'rrhh_carrera.descripcion AS carrera')
            ->where('rrhh_postu.id_postulante', $id)->get();
        
        if ($data->count() > 0){
            foreach ($data as $row){
                $fi = ($row->fecha_inicio != null) ? date('d/m/Y', strtotime($row->fecha_inicio)) : '';
                $ff = ($row->fecha_fin != null) ? date('d/m/Y', strtotime($row->fecha_fin)) : '';
                $html .=
                '<tr>
                    <td>'.$row->id_formacion.'</td>
                    <td>'.$row->nivel_est.'</td>
                    <td>'.$row->carrera.'</td>
                    <td>'.$row->nombre_institucion.'</td>
                    <td>'.$fi.'</td>
                    <td>'.$ff.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="5"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_experiencia_lab($id){
        $html = '';
        $data = DB::table('rrhh.rrhh_postu')
            ->join('rrhh.rrhh_exp_labo', 'rrhh_exp_labo.id_postulante', '=', 'rrhh_postu.id_postulante')
            ->where('rrhh_postu.id_postulante', $id)->get();
        
        if ($data->count() > 0){
            foreach ($data as $row){
                $fi = ($row->fecha_ingreso != null) ? date('d/m/Y', strtotime($row->fecha_ingreso)) : '';
                $ff = ($row->fecha_cese != null) ? date('d/m/Y', strtotime($row->fecha_cese)) : '';

                if ($row->datos_contacto != null){
                    if ($row->telefono_contacto != null){
                        $dts_ctt = $row->datos_contacto.' ('.$row->telefono_contacto.')';
                    }else{
                        $dts_ctt = $row->datos_contacto;
                    }
                }else{
                    $dts_ctt = '';
                }

                $html .=
                '<tr>
                    <td>'.$row->id_experiencia_laboral.'</td>
                    <td>'.$row->nombre_empresa.'</td>
                    <td>'.$row->cargo_ocupado.'</td>
                    <td>'.$dts_ctt.'</td>
                    <td>'.$fi.'</td>
                    <td>'.$ff.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="5"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_datos_extras($id){
        $html = '';
        $data = DB::table('rrhh.rrhh_postu')
            ->join('rrhh.rrhh_dts_extra', 'rrhh_dts_extra.id_postulante', '=', 'rrhh_postu.id_postulante')
            ->join('administracion.adm_tipo_archivo', 'adm_tipo_archivo.id_tipo_archivo', '=', 'rrhh_dts_extra.id_tipo_archivo')
            ->select('rrhh_dts_extra.*', 'adm_tipo_archivo.descripcion AS tipo_archivo')
            ->where('rrhh_postu.id_postulante', $id)->get();
        
        if ($data->count() > 0){
            foreach ($data as $row){
                if ($row->id_tipo_archivo == 1){
                    $ruta = '/rrhh/antec_policiales/'.$row->archivo;
                }elseif ($row->id_tipo_archivo == 2){
                    $ruta = '/rrhh/antec_penales/'.$row->archivo;
                }elseif ($row->id_tipo_archivo == 3){
                    $ruta = '/rrhh/cv/'.$row->archivo;
                }elseif ($row->id_tipo_archivo == 4){
                    $ruta = '/rrhh/fotos_postulantes/'.$row->archivo;
                }
                $nameFile = asset('files').$ruta;
                $html .=
                '<tr>
                    <td>'.$row->id_datos_extras.'</td>
                    <td>'.$row->tipo_archivo.'</td>
                    <td><a href="'.$nameFile.'" target="_blank">'.$row->archivo.'</a></td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="2"> No hay archivos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_observaciones($id){
        $html = '';
        $data = DB::table('rrhh.rrhh_postu')
            ->join('rrhh.rrhh_obs_postu', 'rrhh_obs_postu.id_postulante', '=', 'rrhh_postu.id_postulante')
            ->select('rrhh_obs_postu.*')->where('rrhh_postu.id_postulante', $id)->get();
        
        if ($data->count() > 0){
            foreach ($data as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_observacion.'</td>
                    <td>'.$row->observacion.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="1"> No hay archivos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function guardar_informacion_postulante(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $sql = DB::table('rrhh.rrhh_postu')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', 'rrhh_postu.id_persona')
                ->where('rrhh_perso.nro_documento', '=', $request->nro_documento)->get();

        if ($sql->count() > 0){
            $id = 'exist';
        }else{
            $id = DB::table('rrhh.rrhh_postu')->insertGetId(
                [
                    'id_persona'        => $request->id_persona,
                    'direccion'         => strtoupper($request->direccion),
                    'telefono'          => $request->telefono,
                    'correo'            => $request->correo,
                    'brevette'          => $request->brevette,
                    'id_pais'           => $request->id_pais,
                    'ubigeo'            => $request->ubigeo,
                    'fecha_registro'    => $fecha_registro
                ],
                'id_postulante'
            );
        }
        return response()->json($id);
    }
    public function actualizar_informacion_postulante(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_postu')->where('id_postulante', $request->id_postulante)
        ->update([
            'id_persona'        => $request->id_persona,
            'direccion'         => strtoupper($request->direccion),
            'telefono'          => $request->telefono,
            'correo'            => $request->correo,
            'brevette'          => $request->brevette,
            'id_pais'           => $request->id_pais,
            'ubigeo'            => $request->ubigeo,
            'fecha_registro'    => $fecha_registro
        ]);

        if ($data > 0){
            $val = $request->id_postulante;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function guardar_formacion_academica(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_frm_acad')->insertGetId(
            [
                'id_postulante'        => $request->id_postulante,
                'id_nivel_estudio'     => $request->id_nivel_estudio,
                'id_carrera'           => $request->id_carrera,
                'fecha_inicio'         => $request->fecha_inicio,
                'fecha_fin'            => $request->fecha_fin,
                'nombre_institucion'   => strtoupper($request->nombre_institucion),
                'id_pais'              => $request->id_pais,
                'ubigeo'               => $request->ubigeo,
                'estado'               => 1,
                'fecha_registro'       => $fecha_registro
            ],
            'id_formacion'
        );
        if ($id > 0){
            $val = $request->id_postulante;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_formacion_academica(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_frm_acad')->where('id_formacion', $request->id_formacion)
        ->update([
                'id_postulante'        => $request->id_postulante,
                'id_nivel_estudio'     => $request->id_nivel_estudio,
                'id_carrera'           => $request->id_carrera,
                'fecha_inicio'         => $request->fecha_inicio,
                'fecha_fin'            => $request->fecha_fin,
                'nombre_institucion'   => strtoupper($request->nombre_institucion),
                'id_pais'              => $request->id_pais,
                'ubigeo'               => $request->ubigeo,
                'estado'               => 1,
                'fecha_registro'       => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_postulante;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function guardar_experiencia_laboral(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $datos_con = ($request->datos_contacto != '') ? strtoupper($request->datos_contacto) : null;
        $rel_trab = ($request->relacion_trab_contacto != '') ? strtoupper($request->relacion_trab_contacto) : null;
        $funcion = ($request->funciones != '') ? strtoupper($request->funciones) : null;
        $id = DB::table('rrhh.rrhh_exp_labo')->insertGetId(
            [
                'id_postulante'             => $request->id_postulante,
                'nombre_empresa'            => strtoupper($request->nombre_empresa),
                'cargo_ocupado'             => strtoupper($request->cargo_ocupado),
                'datos_contacto'            => $datos_con,
                'telefono_contacto'         => $request->telefono_contacto,
                'relacion_trab_contacto'    => $rel_trab,
                'funciones'                 => $funcion,
                'fecha_ingreso'             => $request->fecha_ingreso,
                'fecha_cese'                => $request->fecha_cese,
                'estado'                    => 1,
                'fecha_registro'            => $fecha_registro
            ],
            'id_experiencia_laboral'
        );
        if ($id > 0){
            $val = $request->id_postulante;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_experiencia_laboral(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $datos_con = ($request->datos_contacto != '') ? strtoupper($request->datos_contacto) : null;
        $rel_trab = ($request->relacion_trab_contacto != '') ? strtoupper($request->relacion_trab_contacto) : null;
        $funcion = ($request->funciones != '') ? strtoupper($request->funciones) : null;
        $data = DB::table('rrhh.rrhh_exp_labo')->where('id_experiencia_laboral', $request->id_experiencia_laboral)
        ->update([
            'id_postulante'             => $request->id_postulante,
            'nombre_empresa'            => strtoupper($request->nombre_empresa),
            'cargo_ocupado'             => strtoupper($request->cargo_ocupado),
            'datos_contacto'            => $datos_con,
            'telefono_contacto'         => $request->telefono_contacto,
            'relacion_trab_contacto'    => $rel_trab,
            'funciones'                 => $funcion,
            'fecha_ingreso'             => $request->fecha_ingreso,
            'fecha_cese'                => $request->fecha_cese,
            'estado'                    => 1,
            'fecha_registro'            => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_postulante;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function guardar_dextra_postulante(Request $request){
        $archivo = $request->file('archivo');
        $fecha_registro = date('Y-m-d H:i:s');

        if(isset($archivo)){
            $file = time().$archivo->getClientOriginalName();
            if ($request->id_tipo_archivo == 1){
                $ruta = 'rrhh/antec_policiales/'.$file;
            }elseif ($request->id_tipo_archivo == 2){
                $ruta = 'rrhh/antec_penales/'.$file;
            }elseif ($request->id_tipo_archivo == 3){
                $ruta = 'rrhh/cv/'.$file;
            }elseif ($request->id_tipo_archivo == 4){
                $ruta = 'rrhh/fotos_postulantes/'.$file;
            }
            Storage::disk('archivos')->put($ruta, \File::get($archivo));
        }else{
            $file = null;
        }

        $id = DB::table('rrhh.rrhh_dts_extra')->insertGetId(
            [
                'id_postulante'     => $request->id_postulante,
                'archivo'           => $file,
                'id_tipo_archivo'   => $request->id_tipo_archivo,
                'fecha_registro'    => $fecha_registro
            ],
            'id_datos_extras'
        );
        if ($id > 0){
            $val = $request->id_postulante;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function guardar_observacion_postulante(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_obs_postu')->insertGetId(
            [
                'id_postulante'     => $request->id_postulante,
                'observacion'       => $request->observacion,
                'id_usuario'        => $request->id_usuario,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_observacion'
        );
        if ($id > 0){
            $val = $request->id_postulante;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_observacion_postulante(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_obs_postu')->where('id_observacion', $request->id_observacion)
        ->update([
            'id_postulante'             => $request->id_postulante,
            'observacion'               => $request->observacion,
            'id_usuario'                => $request->id_usuario,
            'estado'                    => 1,
            'fecha_registro'            => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_postulante;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }

    public function mostrar_formacion_click($id){
        $data = DB::table('rrhh.rrhh_frm_acad')->where('id_formacion', $id)->get();
        return response()->json($data);
    }
    public function mostrar_experiencia_click($id){
        $data = DB::table('rrhh.rrhh_exp_labo')->where('id_experiencia_laboral', $id)->get();
        return response()->json($data);
    }
    public function mostrar_contrato_click($id){
        $data = DB::table('rrhh.rrhh_contra')->where('id_contrato', $id)->get();
        return response()->json($data);
    }
    public function mostrar_rol_click($id){
        $data = DB::table('rrhh.rrhh_rol')
            ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
            ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->select('adm_empresa.id_empresa', 'adm_area.descripcion as nombre_area', 'rrhh_rol.*')
            ->where('rrhh_rol.id_rol', $id)->get();
        return response()->json($data);
    }
    public function mostrar_cuenta_click($id){
        $data = DB::table('rrhh.rrhh_cta_banc')->where('id_cuenta_bancaria', $id)->get();
        return response()->json($data);
    }
    public function mostrar_observacion_click($id){
        $data = DB::table('rrhh.rrhh_obs_postu')->where('id_observacion', $id)->get();
        return response()->json($data);
    }

    /* TRABAJADOR */
    public function mostrar_trabajador_table(){
        $trab = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_trab.id_trabajador', 'rrhh_postu.direccion', 'rrhh_postu.telefono',  'rrhh_perso.nro_documento',
                'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno')
            ->where('rrhh_trab.estado', 1)
            ->orderBy('id_trabajador','asc')->get();

        foreach ($trab as $row){
            $id_trab = $row->id_trabajador;
            $nro_doc = $row->nro_documento;
            $dt_trab = $row->apellido_paterno.' '.$row->apellido_materno.' '.$row->nombres;

            $sql = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
                ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->select('rrhh_cargo.descripcion AS rol', 'sis_sede.descripcion AS sede', 'adm_grupo.descripcion AS grupo', 'adm_contri.razon_social AS empresa')
                ->where('rrhh_trab.id_trabajador', $id_trab)->orderBy('rrhh_rol.id_rol', 'desc')->limit(1)->get();
            if ($sql->count() > 0) {
                $miRw = $sql->first();
                $rol = strtoupper($miRw->rol);
                $sede = strtoupper($miRw->sede);
                $grup = strtoupper($miRw->grupo);
                $empre = strtoupper($miRw->empresa);
            }else{
                $rol = '';
                $sede = '';
                $grup = '';
                $empre = '';
            }

            $output['data'][] = array('id_trabajador'=> $id_trab, 'nro_documento'=>$nro_doc, 'datos_trabajador'=>$dt_trab, 'rol'=>$rol, 'sede'=>$sede, 'grupo'=>$grup, 'empresa'=>$empre);
        }
        return response()->json($output);
    }
    public function mostrar_trabajador_id($id){
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_trab.id_trabajador', 'rrhh_trab.id_tipo_trabajador', 'rrhh_trab.condicion', 'rrhh_trab.hijos', 'rrhh_trab.id_pension', 'rrhh_trab.seguro', 'rrhh_trab.confianza', 'rrhh_postu.id_postulante',
                    'rrhh_perso.nro_documento', 'rrhh_trab.cuspp', 'rrhh_trab.id_categoria_ocupacional', 'rrhh_trab.id_tipo_planilla', 'rrhh_trab.marcaje',
                    DB::raw("CONCAT(rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno, ' ',rrhh_perso.nombres) AS datos_postulante"))
        ->where('rrhh_trab.id_trabajador', $id)->get();
        return response()->json($data);
    }
    public function mostrar_trabajador_dni($dni){
        $postu = DB::table('rrhh.rrhh_perso')
            ->join('rrhh.rrhh_postu', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_postu.id_postulante')
            ->where('rrhh_perso.nro_documento', $dni)->get();
        if ($postu->count() > 0){
            foreach ($postu as $value){
                $id_postulante = $value->id_postulante;
            }
            $trab = DB::table('rrhh.rrhh_trab')
                        ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                        ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                        ->select('rrhh_trab.id_trabajador')
                        ->where([['rrhh_perso.nro_documento', $dni], ['rrhh_trab.estado', 1]])->get();
            if ($trab->count() > 0) {
                $prevData = DB::table('rrhh.rrhh_trab')
                        ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                        ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                        ->select('rrhh_trab.*','rrhh_perso.id_persona', DB::raw("CONCAT(rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno, ' ',rrhh_perso.nombres) AS datos_postulante"), 'rrhh_perso.estado')
                        ->where('rrhh_perso.nro_documento', $dni)->get();
                $data[0] = ['id_postulante' => $id_postulante, 'id_trabajador' => 1, 'data' => $prevData];
                return response()->json($data);
            }else{
                $prevData = DB::table('rrhh.rrhh_perso')
                            ->join('rrhh.rrhh_postu', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                            ->select('rrhh_postu.id_postulante', DB::raw("CONCAT(rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno, ' ',rrhh_perso.nombres) AS datos_postulante"), 'rrhh_perso.estado')
                            ->where('rrhh_perso.nro_documento', $dni)->get();
                $data[0] = ['id_postulante' => $id_postulante, 'id_trabajador' => 0, 'data' => $prevData];
                return response()->json($data);
            }
        }else{
            $data[0] = ['id_trabajador' => 0];
            return response()->json($data);
        }
    }
    public function mostrar_contrato_trab($id){
        $html = '';
        $data = DB::table('rrhh.rrhh_contra')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_contra.id_trabajador')
            ->join('rrhh.rrhh_tp_contra', 'rrhh_tp_contra.id_tipo_contrato', '=', 'rrhh_contra.id_tipo_contrato')
            ->join('rrhh.rrhh_modali', 'rrhh_modali.id_modalidad', '=', 'rrhh_contra.id_modalidad')
            ->select('rrhh_contra.*', 'rrhh_tp_contra.descripcion AS tipo_contrato', 'rrhh_modali.descripcion AS modalidad')
            ->where([['rrhh_contra.id_trabajador', $id], ['rrhh_contra.estado', 1]])->get();

        if ($data->count() > 0){
            foreach ($data as $row){
                $fi = ($row->fecha_inicio != null) ? date('d/m/Y', strtotime($row->fecha_inicio)) : '';
                $ff = ($row->fecha_fin != null) ? date('d/m/Y', strtotime($row->fecha_fin)) : '';
                $html .=
                '<tr>
                    <td>'.$row->id_contrato.'</td>
                    <td>'.$row->motivo.'</td>
                    <td>'.$row->tipo_contrato.'</td>
                    <td>'.$row->modalidad.'</td>
                    <td>'.$fi.'</td>
                    <td>'.$ff.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="5"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_rol_trab($id){
        $html = '';
        $data = DB::table('rrhh.rrhh_rol')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_rol.id_trabajador')
            ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
            ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->select('rrhh_rol.*', 'sis_sede.descripcion AS sede', 'adm_area.descripcion AS area', 'rrhh_cargo.descripcion AS cargo', 'adm_contri.razon_social')
            ->where([['rrhh_rol.id_trabajador', $id], ['rrhh_rol.estado', '>', 0]])->orderBy('rrhh_rol.fecha_inicio')->get();

        if ($data->count() > 0){
            foreach ($data as $row){
                $fi = ($row->fecha_inicio != null) ? date('d/m/Y', strtotime($row->fecha_inicio)) : '';
                $ff = ($row->fecha_fin != null) ? date('d/m/Y', strtotime($row->fecha_fin)) : '';
                $status = ($row->estado > 1) ? '' : '<button type="button" class="btn-danger" onClick="closeRole('.$row->id_rol.', '.$id.');" data-toggle="tooltip" data-placement="bottom" data-original-title="Cerrar Rol"><i class="fas fa-power-off"></i></button>';
               
                $html .=
                '<tr>
                    <td>'.$row->id_rol.'</td>
                    <td>'.$row->razon_social.'</td>
                    <td>'.strtoupper($row->sede).'</td>
                    <td>'.$row->area.'</td>
                    <td>'.$row->cargo.'</td>
                    <td>'.$fi.'</td>
                    <td>'.$ff.'</td>
                    <td>'.number_format($row->salario, 2).'</td>
                    <td>'.$status.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="8"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_cuentas_trab($id){
        $html = '';
        $data = DB::table('rrhh.rrhh_cta_banc')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_cta_banc.id_trabajador')
            ->join('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'rrhh_cta_banc.id_tipo_cuenta')
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'rrhh_cta_banc.id_banco')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->select('rrhh_cta_banc.*', 'adm_tp_cta.descripcion AS tipo_cuenta', 'adm_contri.razon_social AS banco')
            ->where([['rrhh_cta_banc.id_trabajador', $id], ['rrhh_cta_banc.estado', 1]])->get();

        if ($data->count() > 0){
            foreach ($data as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_cuenta_bancaria.'</td>
                    <td>'.$row->banco.'</td>
                    <td>'.$row->nro_cci.'</td>
                    <td>'.$row->nro_cuenta.'</td>
                    <td>'.$row->tipo_cuenta.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="7"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function guardar_alta_trabajador(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $confianza = ($request->confianza == 1) ? true : false;
        $marcaje = ($request->marcaje == 1) ? 1 : 0;
        $cusp = (strlen($request->cuspp) > 0) ? strtoupper($request->cuspp) : null;
        $sql = DB::table('rrhh.rrhh_trab')->where('rrhh_trab.id_postulante', '=', $request->id_postulante)->get();

        if ($sql->count() > 0){
            $id = 'exist';
        }else{
            $id = DB::table('rrhh.rrhh_trab')->insertGetId(
                [
                    'id_postulante'             => $request->id_postulante,
                    'id_tipo_planilla'          => $request->id_tipo_planilla,
                    'id_tipo_trabajador'        => $request->id_tipo_trabajador,
                    'id_categoria_ocupacional'  => $request->id_categoria_ocupacional,
                    'condicion'                 => null,
                    'hijos'                     => $request->hijos,
                    'id_pension'                => $request->id_pension,
                    'cuspp'                     => $cusp,
                    'seguro'                    => $request->seguro,
                    'confianza'                 => $confianza,
                    // 'archivo_adjunto'        => $request->archivo_adjunto,
                    'estado'                    => 1,
                    'fecha_registro'            => $fecha_registro,
                    'marcaje'                   => $marcaje
                ],
                'id_trabajador'
            );
        }
        return response()->json($id);
    }
    public function actualizar_alta_trabajador(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $confianza = ($request->confianza == 1) ? true : false;
        $marcaje = ($request->marcaje == 1) ? 1 : 0;
        $data = DB::table('rrhh.rrhh_trab')->where('id_trabajador', $request->id_trabajador)
        ->update([
            'id_postulante'             => $request->id_postulante,
            'id_tipo_planilla'          => $request->id_tipo_planilla,
            'id_tipo_trabajador'        => $request->id_tipo_trabajador,
            'id_categoria_ocupacional'  => $request->id_categoria_ocupacional,
            'condicion'                 => null,
            'hijos'                     => $request->hijos,
            'id_pension'                => $request->id_pension,
            'cuspp'                     => strtoupper($request->cuspp),
            'seguro'                    => $request->seguro,
            'confianza'                 => $confianza,
            // 'archivo_adjunto'        => $request->archivo_adjunto,
            'estado'                    => 1,
            'fecha_registro'            => $fecha_registro,
            'marcaje'                   => $marcaje
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function guardar_contrato_trabajador(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_contra')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_tipo_contrato'      => $request->id_tipo_contrato,
                'id_modalidad'          => $request->id_modalidad,
                'id_horario'            => $request->id_horario,
                'id_centro_costo'       => $request->id_centro_costo,
                'tipo_centro_costo'     => $request->tipo_centro_costo,
                'fecha_inicio'          => $request->fecha_inicio,
                'fecha_fin'             => $request->fecha_fin,
                'motivo'                => strtoupper($request->motivo),
                // 'archivo_adjunto'    => $request->archivo_adjunto,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro
            ],
            'id_contrato'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_contrato_trabajador(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_contra')->where('id_contrato', $request->id_contrato)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'id_tipo_contrato'      => $request->id_tipo_contrato,
            'id_modalidad'          => $request->id_modalidad,
            'id_horario'            => $request->id_horario,
            'id_centro_costo'       => $request->id_centro_costo,
            'tipo_centro_costo'     => $request->tipo_centro_costo,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
            'motivo'                => $request->motivo,
            // 'archivo_adjunto'    => $request->archivo_adjunto,
            'estado'                => 1,
            'fecha_registro'        => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function guardar_rol_trabajador(Request $request){
        $responsabilidad = ($request->responsabilidad == 1) ? true : false;
        $sctr = ($request->sctr == 1) ? true : false;
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_rol')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_area'               => $request->id_area,
                'id_cargo'              => $request->id_cargo,
                'id_rol_concepto'       => $request->id_rol_concepto,
                'salario'               => $request->salario,
                'responsabilidad'       => $responsabilidad,
                'id_proyecto'           => NULL, /* VERIFICAR */
                'sctr'                  => $sctr,
                'fecha_inicio'          => $request->fecha_ingreso,
                'fecha_fin'             => $request->fecha_cese,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro,
                'id_tipo_planilla'        => $request->rol_id_tipo_planilla
            ],
            'id_rol'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_rol_trabajador(Request $request){
        $responsabilidad = ($request->responsabilidad == 1) ? true : false;
        $sctr = ($request->sctr == 1) ? true : false;
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_rol')->where('id_rol', $request->id_rol)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'id_area'               => $request->id_area,
            'id_cargo'              => $request->id_cargo,
            'id_rol_concepto'       => $request->id_rol_concepto,
            'salario'               => $request->salario,
            'responsabilidad'       => $responsabilidad,
            'id_proyecto'           => NULL, /* VERIFICAR */
            'sctr'                  => $sctr,
            'fecha_inicio'          => $request->fecha_ingreso,
            'fecha_fin'             => $request->fecha_cese,
            'estado'                => 1,
            'fecha_registro'        => $fecha_registro,
            'id_tipo_planilla'        => $request->rol_id_tipo_planilla
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_cierre_rol($id, $fecha){
        $data = DB::table('rrhh.rrhh_rol')->where('id_rol', $id)
        ->update([
            'fecha_fin'             => $fecha,
            'estado'                => 2
        ]);
        if ($data > 0){
            $val = 1;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function guardar_cuentas_trabajador(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_cta_banc')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_banco'              => $request->id_banco,
                'id_tipo_cuenta'        => $request->id_tipo_cuenta,
                'nro_cci'               => $request->nro_cci,
                'nro_cuenta'            => $request->nro_cuenta,
                'id_moneda'             => $request->id_moneda,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro
            ],
            'id_cuenta_bancaria'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_cuentas_trabajador(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_cta_banc')->where('id_cuenta_bancaria', $request->id_cuenta_bancaria)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'id_banco'              => $request->id_banco,
            'id_tipo_cuenta'        => $request->id_tipo_cuenta,
            'nro_cci'               => $request->nro_cci,
            'nro_cuenta'            => $request->nro_cuenta,
            'id_moneda'             => $request->id_moneda,
            'estado'                => 1,
            'fecha_registro'        => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }

    /* BUSQUEDA */
    public function buscar_sede($value){
        $dataSede = DB::table('administracion.sis_sede')->select('id_sede', 'descripcion')
            ->where([['id_empresa', '=', $value], ['estado', '=', 1]])
            ->orderBy('descripcion', 'asc')->get();
        $array = array('sedes' => $dataSede);
        return $array;
    }
    public function buscar_grupo($value){
        $data = DB::table('administracion.adm_grupo')->select('id_grupo', 'descripcion')
            ->where([['id_sede', '=', $value], ['estado', '=', 1]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function buscar_area($value){
        $data = DB::table('administracion.adm_area')->select('id_area', 'descripcion')
            ->where([['id_grupo', '=', $value], ['estado', '=', 1]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function buscar_trabajador_id($id){
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno', 'rrhh_perso.nombres', 'rrhh_trab.id_trabajador')
            ->where('rrhh_trab.id_trabajador', '=', $id)->get()->first();
        $name = $data->apellido_paterno.' '.$data->apellido_materno. ' '.$data->nombres;
        return $name;
    }
    public function buscar_dni($id){
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_perso.nro_documento')
            ->where('rrhh_trab.id_trabajador', '=', $id)->get()->first();
        $doc = $data->nro_documento;
        return $doc;
    }
    public function buscar_cargo($id){
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
            ->select('rrhh_cargo.descripcion AS cargo')
            ->where('rrhh_trab.id_trabajador', $id)->orderBy('rrhh_rol.id_rol', 'desc')->limit(1)->get()->first();
        $cargo = $data->cargo;
        return $cargo;
    }
    public function buscar_usuario_trab_id($id){
        $data = DB::table('configuracion.sis_usua')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno', 'rrhh_perso.nombres', 'rrhh_trab.id_trabajador')
            ->where('sis_usua.id_usuario', '=', $id)->get()->first();
        $name = $data->apellido_paterno.' '.$data->apellido_materno. ' '.$data->nombres;
        return $name;
    }
    public function buscar_trab_dni($dni){
        $perso = DB::table('rrhh.rrhh_trab')->select('rrhh_perso.*', 'rrhh_trab.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
        ->where('rrhh_perso.nro_documento', $dni)->get();
        if ($perso->count() > 0){
            $data = $perso;
        }else{
            $data[0] = ['id_trabajador' => 0];
        }
        return response()->json($data);
    }
    public function buscar_persona_dni($id){
        $data = DB::table('rrhh.rrhh_perso')->where([['nro_documento', '=', $id], ['estado', '=', 1]])->get();
        if($data->count() > 0){
            return response()->json($data);
        }else{
            $data[0] = ['id_persona' => 0];
            return response()->json($data);
        }
    }

    public function buscarUbigeo($code, $type){
        $sql = DB::table('configuracion.ubi_dis')
            ->join('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->join('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->select('ubi_dpto.descripcion AS dpto', 'ubi_prov.descripcion AS prov', 'ubi_dis.descripcion AS dist')
            ->where('ubi_dis.codigo', '=', $code)->get()->first();
            
        if ($type == 'dpto'){
            $lugar = $sql->dpto;
        }elseif($type == 'prov'){
            $lugar = $sql->prov;
        }elseif($type == 'dist'){
            $lugar = $sql->dist;
        }
        
        return $lugar;
    }

    /* CARGO */
    public function mostrar_cargo_table(){
        $cargo = DB::table('rrhh.rrhh_cargo')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $cargo;
        return response()->json($output);
    }
    public function mostrar_cargo_id($id){
        $data = DB::table('rrhh.rrhh_cargo')->where('id_cargo', $id)->get();
        return response()->json($data);
    }
    public function guardar_cargo(Request $request){
        $sql = DB::table('rrhh.rrhh_cargo')->get();
        $total = $sql->count() + 1;

        $fecha_registro = date('Y-m-d H:i:s');
        $code = $this->leftZero(5, $total);
        $codigo = 'C'.$code;
        
        $id = DB::table('rrhh.rrhh_cargo')->insertGetId(
            [
                'codigo'                => $codigo,
                'descripcion'           => strtoupper($request->descripcion),
                'sueldo_rango_minimo'   => $request->sueldo_rango_minimo,
                'sueldo_rango_maximo'   => $request->sueldo_rango_maximo,
                'sueldo_fijo'           => $request->sueldo_fijo,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro
            ],
            'id_cargo'
        );
        return response()->json($id);
    }
    public function actualizar_cargo(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_cargo')->where('id_cargo', $request->id_cargo)
        ->update([
            'descripcion'           => strtoupper($request->descripcion),
            'sueldo_rango_minimo'   => $request->sueldo_rango_minimo,
            'sueldo_rango_maximo'   => $request->sueldo_rango_maximo,
            'sueldo_fijo'           => $request->sueldo_fijo,                                           
            'estado'                => 1,
            'fecha_registro'        => $fecha_registro
        ]);
        return response()->json($data);
    }
    public function anular_cargo($id){
        $data = DB::table('rrhh.rrhh_cargo')->where('id_cargo', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* MERITOS */
    public function mostrar_merito_table($id){
        $html = '';
        $merito = DB::table('rrhh.rrhh_merito')
            ->join('rrhh.rrhh_var_merito', 'rrhh_var_merito.id_variable_merito', '=', 'rrhh_merito.id_variable_merito')
            ->select('rrhh_merito.*', 'rrhh_var_merito.descripcion AS tipo')
            ->where([['rrhh_merito.id_trabajador', '=', $id],['rrhh_merito.estado', '=', 1]])->orderBy('id_merito', 'asc')->get();

        if ($merito->count() > 0){
            foreach ($merito as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_merito.'</td>
                    <td>'.$row->tipo.'</td>
                    <td>'.$row->concepto.'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_merito)).'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="3"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_merito_id($id){
        $merito = DB::table('rrhh.rrhh_merito')->where('rrhh_merito.id_merito', '=', $id)->get();
        return response()->json($merito);
    }
    public function guardar_merito(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_merito')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_variable_merito'    => $request->id_variable_merito,
                'concepto'              => $request->concepto,
                'motivo'                => $request->motivo,
                'fecha_merito'          => $request->fecha_merito,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro
            ],
            'id_merito'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_merito(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_merito')->where('id_merito', $request->id_merito)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'id_variable_merito'    => $request->id_variable_merito,
            'concepto'              => $request->concepto,
            'motivo'                => $request->motivo,
            'fecha_merito'          => $request->fecha_merito,
            // 'archivo_adjunto'   => $request->archivo_adjunto,
            'estado'                => 1,
            'fecha_registro'        => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_merito($id){
        $data = DB::table('rrhh.rrhh_merito')->where('id_merito', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* DEMERITOS */
    public function mostrar_sancion_table($id){
        $html = '';
        $merito = DB::table('rrhh.rrhh_sanci')
            ->join('rrhh.rrhh_var_sanci', 'rrhh_var_sanci.id_variable_sancion', '=', 'rrhh_sanci.id_variable_sancion')
            ->select('rrhh_sanci.*', 'rrhh_var_sanci.descripcion AS tipo')
            ->where([['rrhh_sanci.id_trabajador', '=', $id],['rrhh_sanci.estado', '=', 1]])->orderBy('id_sancion', 'asc')->get();

        if ($merito->count() > 0){
            foreach ($merito as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_sancion.'</td>
                    <td>'.$row->tipo.'</td>
                    <td>'.$row->concepto.'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_sancion)).'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="3"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_sancion_id($id){
        $merito = DB::table('rrhh.rrhh_sanci')->where('rrhh_sanci.id_sancion', '=', $id)->get();
        return response()->json($merito);
    }
    public function guardar_sancion(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_sanci')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_variable_sancion'   => $request->id_variable_sancion,
                'concepto'              => $request->concepto,
                'motivo'                => $request->motivo,
                'fecha_sancion'         => $request->fecha_sancion,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro
            ],
            'id_sancion'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_sancion(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_sanci')->where('id_sancion', $request->id_sancion)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'id_variable_sancion'   => $request->id_variable_sancion,
            'concepto'              => $request->concepto,
            'motivo'                => $request->motivo,
            'fecha_sancion'         => $request->fecha_sancion,
            // 'archivo_adjunto'   => $request->archivo_adjunto,
            'estado'                => 1,
            'fecha_registro'        => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_sancion($id){
        $data = DB::table('rrhh.rrhh_sanci')->where('id_sancion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* DERECHO HABIENTES */
    public function mostrar_derechohabiente_table($id){
        $dhab = DB::table('rrhh.rrhh_der_hab')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_der_hab.id_persona')
            ->join('rrhh.rrhh_cdn_dhab', 'rrhh_cdn_dhab.id_condicion_dh', '=', 'rrhh_der_hab.id_condicion_dh')
            ->select('rrhh_der_hab.*', 'rrhh_cdn_dhab.descripcion AS condicion', 'rrhh_perso.nro_documento AS dni_persona', 'rrhh_perso.fecha_nacimiento',
                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_persona"))
            ->where([
                ['rrhh_der_hab.id_trabajador', '=', $id],
                ['rrhh_der_hab.estado', '=', 1]
            ])->get();
        $html = '';

        if ($dhab->count() > 0){
            foreach ($dhab as $row){
                $id_dha = $row->id_derecho_habiente;
                $id_tra = $row->id_trabajador;
                $id_per = $row->id_persona;
                $id_cdh = $row->id_condicion_dh;
                $estado = $row->estado;
                $fe_reg = $row->fecha_registro;
                $condic = $row->condicion;
                $dni_pe = $row->dni_persona;
                $nom_pe = $row->nombre_persona;
                $fe_nac = $row->fecha_nacimiento;
    
                $edad = $this->buscar_edad($fe_nac);
                $edad = ($edad > 1) ? $edad.' años' : $edad.' año';
                $html .=
                '<tr>
                    <td>'.$id_dha.'</td>
                    <td>'.$dni_pe.'</td>
                    <td>'.$nom_pe.'</td>
                    <td>'.$fe_nac.'</td>
                    <td>'.$edad.'</td>
                    <td>'.$condic.'</td>
                </tr>';
            }
        }else{
            $html .= '<tr><td></td><td colspan="5">No hay datos registrados</td></tr>';
        }

        return response()->json($html);
    }
    public function mostrar_derechohabiente_id($id){
        $sql = DB::table('rrhh.rrhh_der_hab')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_der_hab.id_persona')
            ->select('rrhh_der_hab.*', 'rrhh_perso.nro_documento AS dni_persona',  DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_persona"))
            ->where('rrhh_der_hab.id_derecho_habiente', '=', $id)->get();
        foreach ($sql as $row) {
            $id_dhb = $row->id_derecho_habiente;
            $id_cdh = $row->id_condicion_dh;
            $id_per = $row->id_persona;
            $dni_pe = $row->dni_persona;
            $nom_pe = $row->nombre_persona;

            $data = array(
                'id_derecho_habiente'   => $id_dhb,
                'id_condicion_dh'       => $id_cdh,
                'id_persona'            => $id_per,
                'dni_persona'           => $dni_pe,
                'nombre_persona'        => $nom_pe,
            );
        }
        return response()->json($data);
    }
    public function guardar_derecho_habiente(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_der_hab')->insertGetId(
            [
                'id_trabajador'     => $request->id_trabajador,
                'id_persona'        => $request->id_persona,
                'id_condicion_dh'   => $request->id_condicion_dh,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_derecho_habiente'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_derecho_habiente(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_der_hab')->where('id_derecho_habiente', $request->id_derecho_habiente)
        ->update([
            'id_trabajador'     => $request->id_trabajador,
            'id_persona'        => $request->id_persona,
            'id_condicion_dh'   => $request->id_condicion_dh,
            'estado'            => 1,
            'fecha_registro'    => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_derecho_habiente($id){
        $data = DB::table('rrhh.rrhh_der_hab')->where('id_derecho_habiente', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* SALIDAS Y PERMISOS */
    public function mostrar_salidas_table($id){
        $output = array();
        $salidas = DB::table('rrhh.rrhh_permi')
            ->join('rrhh.rrhh_tp_permi', 'rrhh_tp_permi.id_tipo_permiso', '=', 'rrhh_permi.id_tipo_permiso')
            ->select('rrhh_permi.*', 'rrhh_tp_permi.descripcion AS tipo_permi')
            ->where([['rrhh_permi.id_trabajador', '=', $id], ['rrhh_permi.estado', '=', 1]])->get();

        if ($salidas->count() > 0){
            foreach ($salidas as $row){
                if ($row->tipo == 1) {
                    $tpText = 'Permiso';
                }else{
                    $tpText = 'Comisión de Salida';
                }

                $mes = $this->convertMesEspañol($row->fecha_inicio_permiso);

                if ($row->fecha_fin_permiso != null) {
                    $txtFecha = date('d/m/Y', strtotime($row->fecha_inicio_permiso)).' - '.date('d/m/Y', strtotime($row->fecha_fin_permiso));
                }else{
                    $txtFecha = date('d/m/Y', strtotime($row->fecha_inicio_permiso));
                }
                if ($row->hora_fin != null) {
                    $txtHora = date('H:i', strtotime($row->hora_inicio)).' - '.date('H:i', strtotime($row->hora_fin));
                }else{
                    $txtHora = date('H:i', strtotime($row->hora_inicio));
                }

                $autoriza = $this->buscar_usuario_trab_id($row->id_trabajador_autoriza);

                $output[] = array(
                    'id_permiso'    => $row->id_permiso,
                    'inicio'        => $row->fecha_inicio_permiso,
                    'mes'           => $mes,
                    'tipo'          => $tpText,
                    'fecha'         => $txtFecha,
                    'hora'          => $txtHora,
                    'autoriza'      => $autoriza
                );
            }
        }
        return response()->json($output);
    }
    public function mostrar_salidas_id($id){
        $merito = DB::table('rrhh.rrhh_permi')->where('rrhh_permi.id_permiso', '=', $id)->get();
        return response()->json($merito);
    }
    public function guardar_salidas(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_permi')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_tipo_permiso'       => $request->id_tipo_permiso,
                'tipo'                  => $request->tipo,
                'motivo'                => strtoupper($request->motivo),
                'fecha_inicio_permiso'  => $request->fecha_inicio_permiso,
                'fecha_fin_permiso'     => $request->fecha_fin_permiso,
                'hora_inicio'           => $request->hora_inicio,
                'hora_fin'              => $request->hora_fin,
                'id_trabajador_autoriza'=> $request->id_trabajador_autoriza,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro
            ],
            'id_permiso'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_salidas(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_permi')->where('id_permiso', $request->id_permiso)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'id_tipo_permiso'       => $request->id_tipo_permiso,
            'tipo'                  => $request->tipo,
            'motivo'                => strtoupper($request->motivo),
            'fecha_inicio_permiso'  => $request->fecha_inicio_permiso,
            'fecha_fin_permiso'     => $request->fecha_fin_permiso,
            'hora_inicio'           => $request->hora_inicio,
            'hora_fin'              => $request->hora_fin,
            'id_trabajador_autoriza'=> $request->id_trabajador_autoriza,
            // 'archivo_adjunto'   => $request->archivo_adjunto,
            'estado'                => 1,
            'fecha_registro'        => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_salidas($id){
        $data = DB::table('rrhh.rrhh_permi')->where('id_permiso', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* PRESTAMOS */
    public function mostrar_prestamo_table($id){
        $html = '';
        $prestamo = DB::table('rrhh.rrhh_presta')
            ->where([['id_trabajador', '=', $id], ['estado', '=', 1]])->orderBy('fecha_prestamo', 'desc')->get();

        if ($prestamo->count() > 0){
            foreach ($prestamo as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_prestamo.'</td>
                    <td>'.$row->concepto.'</td>
                    <td>'.number_format($row->monto_prestamo, 2).'</td>
                    <td>'.$row->nro_cuotas.'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_prestamo)).'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="4"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_prestamo_id($id){
        $prestamo = DB::table('rrhh.rrhh_presta')->where('rrhh_presta.id_prestamo', '=', $id)->get();
        return response()->json($prestamo);
    }
    public function guardar_prestamo(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_presta')->insertGetId(
            [
                'id_trabajador'     => $request->id_trabajador,
                'concepto'          => strtoupper($request->concepto),
                'fecha_prestamo'    => $request->fecha_prestamo,
                'nro_cuotas'        => $request->nro_cuotas,
                'monto_prestamo'    => $request->monto_prestamo,
                'porcentaje'        => $request->porcentaje,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_prestamo'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_prestamo(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_presta')->where('id_prestamo', $request->id_prestamo)
        ->update([
            'id_trabajador'     => $request->id_trabajador,
            'concepto'          => strtoupper($request->concepto),
            'fecha_prestamo'    => $request->fecha_prestamo,
            'nro_cuotas'        => $request->nro_cuotas,
            'monto_prestamo'    => $request->monto_prestamo,
            'porcentaje'        => $request->porcentaje,
            // 'archivo_adjunto'   => $request->archivo_adjunto,
            'estado'            => 1,
            'fecha_registro'    => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_prestamo($id){
        $data = DB::table('rrhh.rrhh_presta')->where('id_prestamo', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* CONTROL DE VACACIONES */
    public function mostrar_vacaciones_table($id){
        $html = '';
        $horext = DB::table('rrhh.rrhh_vacac')
            ->where([['id_trabajador', '=', $id], ['estado', '=', 1]])->orderBy('concepto', 'desc')->get();

        if ($horext->count() > 0){
            foreach ($horext as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_vacaciones.'</td>
                    <td>PERIODO: '.$row->concepto.'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_inicio)).'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_fin)).'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_retorno)).'</td>
                    <td>'.$row->dias.'</td>
                    <td><button class="btn btn-xs btn-primary" onclick=" return imprimir('.$row->id_vacaciones.');"><i class="fa fa-print"></i></button></td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="6"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_vacaciones_id($id){
        $horext = DB::table('rrhh.rrhh_vacac')->where('rrhh_vacac.id_vacaciones', '=', $id)->get();
        return response()->json($horext);
    }
    public function guardar_vacaciones(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $result = array();
        $id = DB::table('rrhh.rrhh_vacac')->insertGetId(
            [
                'id_trabajador'     => $request->id_trabajador,
                'fecha_inicio'      => $request->fecha_inicio,
                'fecha_retorno'     => $request->fecha_retorno,
                'fecha_fin'         => $request->fecha_fin,
                'dias'              => $request->dias,
                'concepto'          => $request->concepto,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_vacaciones'
        );
        if ($id > 0){
            $result = array('id_trabajador' => $request->id_trabajador, 'id_vacaciones' => $id);
        }else{
            $result = array('id_trabajador' => $request->id_trabajador, 'id_vacaciones' => 0);
        }
        return response()->json($result);
    }
    public function actualizar_vacaciones(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_vacac')->where('id_vacaciones', $request->id_vacaciones)
        ->update([
            'id_trabajador'     => $request->id_trabajador,
            'fecha_inicio'      => $request->fecha_inicio,
            'fecha_fin'         => $request->fecha_fin,
            'fecha_retorno'     => $request->fecha_retorno,
            'dias'              => $request->dias,
            'concepto'          => $request->concepto,
            // 'archivo_adjunto'   => $request->archivo_adjunto,
            'estado'            => 1,
            'fecha_registro'    => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_vacaciones($id){
        $data = DB::table('rrhh.rrhh_vacac')->where('id_vacaciones', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* CONTROL DE LICENCIAS */
    public function mostrar_licencia_table($id){
        $html = '';
        $horext = DB::table('rrhh.rrhh_licenc')
            ->where([['id_trabajador', '=', $id], ['estado', '=', 1]])->orderBy('fecha_inicio', 'desc')->get();

        if ($horext->count() > 0){
            foreach ($horext as $row){
                $tipo = ($row->id_tipo_licencia == 1) ? 'LICENCIA CON GOCE' : 'TELETRABAJO';
                $html .=
                '<tr>
                    <td>'.$row->id_licencia.'</td>
                    <td>'.$tipo.'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_inicio)).'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_fin)).'</td>
                    <td>'.$row->dias.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="5"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_licencia_id($id){
        $horext = DB::table('rrhh.rrhh_licenc')->where('rrhh_licenc.id_licencia', '=', $id)->get();
        return response()->json($horext);
    }
    public function guardar_licencia(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $result = array();
        $id = DB::table('rrhh.rrhh_licenc')->insertGetId(
            [
                'id_trabajador'     => $request->id_trabajador,
                'id_tipo_licencia'  => $request->tipo_licencia,
                'fecha_inicio'      => $request->fecha_inicio,
                'fecha_fin'         => $request->fecha_fin,
                'dias'              => $request->dias,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_licencia'
        );
        if ($id > 0){
            $result = array('id_trabajador' => $request->id_trabajador, 'id_licencia' => $id);
        }else{
            $result = array('id_trabajador' => $request->id_trabajador, 'id_licencia' => 0);
        }
        return response()->json($result);
    }
    public function actualizar_licencia(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_licenc')->where('id_licencia', $request->id_licencia)
        ->update([
            'id_trabajador'     => $request->id_trabajador,
            'id_tipo_licencia'  => $request->tipo_licencia,
            'fecha_inicio'      => $request->fecha_inicio,
            'fecha_fin'         => $request->fecha_fin,
            'dias'              => $request->dias,
            'estado'            => 1,
            'fecha_registro'    => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_licencia($id){
        $data = DB::table('rrhh.rrhh_licenc')->where('id_licencia', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* CONTROL DE HORAS EXTRAS */
    public function mostrar_horas_ext_table($id){
        $html = '';
        $horext = DB::table('rrhh.rrhh_hrs_extra')
        ->where([['id_trabajador', '=', $id], ['estado', '=', 1]])->orderBy('fecha_hora_extra', 'desc')->get();
        
        if ($horext->count() > 0){
            foreach ($horext as $row){
                $dia = $this->filtrar_dia($row->fecha_hora_extra);
                $he = $row->total_horas;
                $auto = $row->id_trabajador_autoriza;
                $autoriza = $this->buscar_usuario_trab_id($auto);

                if ($dia != 0) {
                    if ($he > 2){
                        $he25 = 2;
                        $he35 = ($he - 2);
                        $he100 = 0;
                    }else{
                        $he25 = $he;
                        $he35 = 0;
                        $he100 = 0;
                    }
                }else{
                    $he25 = 0;
                    $he35 = 0;
                    $he100 = $he;
                }
                
                $html .=
                '<tr>
                    <td>'.$row->id_hora_extra.'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_hora_extra)).'</td>
                    <td>'.number_format($he25, 2).'</td>
                    <td>'.number_format($he35, 2).'</td>
                    <td>'.number_format($he100, 2).'</td>
                    <td>'.number_format($row->total_horas, 2).'</td>
                    <td>'.$autoriza.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="6"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_horas_ext_id($id){
        $horext = DB::table('rrhh.rrhh_hrs_extra')->where('rrhh_hrs_extra.id_hora_extra', '=', $id)->get();
        return response()->json($horext);
    }
    public function guardar_horas_ext(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_hrs_extra')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'total_horas'           => $request->horas,
                'motivo'                => $request->motivo,
                'fecha_hora_extra'      => $request->fecha,
                'id_trabajador_autoriza'=> $request->id_trabajador_autoriza,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro
            ],
            'id_hora_extra'
        );
        return response()->json($id);
    }
    public function actualizar_horas_ext(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_hrs_extra')->where('id_hora_extra', $request->id_hora_extra)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'total_horas'           => $request->horas,
            'motivo'                => $request->motivo,
            'fecha_hora_extra'      => $request->fecha,
            'id_trabajador_autoriza'=> $request->id_trabajador_autoriza,
            'estado'                => 1,
            'fecha_registro'        => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_horas_ext($id){
        $data = DB::table('rrhh.rrhh_hrs_extra')->where('id_hora_extra', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* PERIODO */
    public function mostrar_periodo_table(){
        $data = DB::table('rrhh.rrhh_asist')->orderBy('id_asistencia', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_periodo_id($id){
        $data = DB::table('rrhh.rrhh_asist')->where('id_asistencia', $id)->get();
        return response()->json($data);
    }
    public function mostrar_periodo_count($id){
        $periodo = DB::table('rrhh.rrhh_asist')->where('id_tipo_asistencia', '=', $id)->count();
        return $periodo + 1;
    }
    public function guardar_periodo(Request $request){
        if ($request->id_tipo_asistencia == 1) {
            $val = 'SEMANA ';
            $num = $this->mostrar_periodo_count($request->id_tipo_asistencia);
            $desc = $val.$num;
        }else{
            $desc = $request->descripcion;
        }
        $fecha_registro = date('Y-m-d H:i:s');
        
        $id = DB::table('rrhh.rrhh_asist')->insertGetId(
            [
                'id_tipo_asistencia'  => $request->id_tipo_asistencia,
                'descripcion'         => strtoupper($desc),
                'fecha_inicio'        => $request->fecha_inicio,
                'fecha_fin'           => $request->fecha_fin,
                'estado'              => 1,
                'fecha_registro'      => $fecha_registro
            ],
            'id_asistencia'
        );
        return response()->json($id);
    }
    public function actualizar_periodo(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_asist')->where('id_asistencia', $request->id_asistencia)
        ->update([
            'id_tipo_asistencia'  => $request->id_tipo_asistencia,
            'descripcion'         => strtoupper($request->descripcion),
            'fecha_inicio'        => $request->fecha_inicio,
            'fecha_fin'           => $request->fecha_fin,
            'estado'              => 1,
            'fecha_registro'      => $fecha_registro
        ]);
        return response()->json($data);
    }
    public function anular_periodo($id){
        $data = DB::table('rrhh.rrhh_asist')->where('id_asistencia', $id)
        ->update([
            'estado'     => 2
        ]);
        return response()->json($data);
    }

    /* CESE DEL PERSONAL */
    public function guardar_cese(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $insert = DB::table('rrhh.rrhh_cese')->insertGetId(
            [
                'id_trabajador'     => $request->id_trabajador,
                'id_baja'           => $request->id_baja,
                'fecha_cese'        => $request->fecha_cese,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'fecha_registro'    => $fecha_registro
            ],
            'id_cese'
        );

        if ($insert > 0) {
            $data = DB::table('rrhh.rrhh_trab')->where('id_trabajador', $request->id_trabajador)->update(['estado' => 0]);
            $value = 1;
        }else{
            $value = 0;
        }
        return response()->json($value);
    }

    /* HORARIOS */
    public function mostrar_horarios_table(){
        $horario = DB::table('rrhh.rrhh_horario')->where('rrhh_horario.estado', 1)->orderBy('id_horario', 'asc')->get();
            $output['data'] = $horario;
        return response()->json($output);
    }
    public function mostrar_horario_id($id){
        $data = DB::table('rrhh.rrhh_horario')->where('id_horario', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_horario(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_horario')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'hora_ent_reg_sem'  => $request->hora_ini_reg,
                'hora_sal_reg_sem'  => $request->hora_fin_reg,
                'hora_sal_alm_sem'  => $request->hora_ini_alm,
                'hora_ent_alm_sem'  => $request->hora_fin_alm,
                'hora_ent_reg_sab'  => $request->hora_ini_sab,
                'hora_sal_reg_sab'  => $request->hora_fin_sab,
                'dias_sem'          => $request->dias_sem,
                'hora_sem'          => $request->hora_sem,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_horario'
        );
        return response()->json($id);
    }
    public function actualizar_horario(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_horario')->where('id_horario', $request->id_horario)
        ->update([
            'descripcion'      => strtoupper($request->descripcion),
            'hora_ent_reg_sem' => $request->hora_ini_reg,
            'hora_sal_reg_sem' => $request->hora_fin_reg,
            'hora_sal_alm_sem' => $request->hora_ini_alm,
            'hora_ent_alm_sem' => $request->hora_fin_alm,
            'hora_ent_reg_sab' => $request->hora_ini_sab,
            'hora_sal_reg_sab' => $request->hora_fin_sab,
            'dias_sem'          => $request->dias_sem,
            'hora_sem'          => $request->hora_sem,
            'estado'           => 1,
            'fecha_registro'   => $fecha_registro
        ]);
        return response()->json($data);
    }
    public function anular_horario($id){
        $data = DB::table('rrhh.rrhh_horario')->where('id_horario', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function mostrar_trabajador_dni_reloj($id){
        $trab = 0;
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_trab.id_trabajador')
            ->where('rrhh_perso.nro_documento', $id)->first();
        if ($data){
            $trab = $data->id_trabajador;
        }
        return $trab;
    }
    public function cargar_horario_reloj(){
        if (!empty($_FILES['archivo']['name'])){
			$handle = fopen($_FILES['archivo']['tmp_name'], 'r');

			if ($handle){
				while ($data = fgetcsv($handle, 4096, ";")){
					$string[] = array('name' => $data[1], 'fecha' => $data[3], 'dni' => $data[6], 'tipo' => $data[4]);
				}
                fclose($handle);

				foreach ($string as $row) {
                    $fecha = $row['fecha'];
                    $dni = $row['dni'];
                    $tipo = $row['tipo'];
                    $txtIn = '';

                    if ($tipo == 'C/In'){
                        $txtIn = 1;
                    }elseif ($tipo == 'OverTime Out'){
                        $txtIn = 2;
                    }elseif ($tipo == 'OverTime In'){
                        $txtIn = 3;
                    }elseif ($tipo == 'C/Out'){
                        $txtIn = 4;
                    }

                    // buscar ID_TRABAJADOR
                    $id_trab = $this->mostrar_trabajador_dni_reloj($dni);
                    if ($id_trab > 0) {
                        $date = substr($fecha, 0, 10);
                        $hour = substr($fecha, 11, 19);
                        $hora = date('H:i:s', strtotime($hour));

                        DB::table('rrhh.rrhh_reloj')->insertGetId(
                            [
                                'id_trabajador' =>$id_trab,
                                'fecha'         =>$date,
                                'horario'       =>$hora,
                                'tipo'          =>$txtIn                                
                            ],
                            'id_horario'
                        );
                    }
                }
                $rpta = 'ok';
			}else{
				$rpta = 'error';
			}
		}else{
			$rpta = 'null';
        }
        $array = array('status' => $rpta);
        echo json_encode($array);
    }
    public function cargar_horario_diario($empre, $sede, $tipoPla, $fecha){
        $myfecha = date('Y-m-d', strtotime($fecha));
        $fech_perm = "'".$myfecha."'";
        $dia = $this->filtrar_dia($myfecha);
        $txt = '';
		$button = '';
        $hora = [];
		
        $verify = $this->verifyDataHorario($myfecha, $tipoPla, $empre, $sede);
        if ($verify > 0){
            $sql = $this->searchPersonalData($myfecha, $empre, $sede, $tipoPla);
            $cont = 0;
            $name = '';
			foreach ($sql as $row){
                $id_trab = $row->id_trabajador;
                $name = $this->buscar_trabajador_id($id_trab);

				$er = $row->hora_entrada;
				$sa = $row->hora_salida_almuerzo;
				$ea = $row->hora_entrada_almuerzo;
				$sr = $row->hora_salida;
				$ti = date('H:i', strtotime($row->minutos_tardanza));
                $ta = date('H:i', strtotime($row->minutos_tardanza_alm));
                
                $horaTrab = $this->searchTrabHour($id_trab);
                foreach ($horaTrab as $keyH){
                    $her = $keyH->hora_ent_reg_sem;
                    $hsr = $keyH->hora_sal_reg_sem;
                    ////////////
                    $hsa = $keyH->hora_sal_alm_sem;
                    $hea = $keyH->hora_ent_alm_sem;
                    /////////
                    $hes = $keyH->hora_ent_reg_sab;
                    $hss = $keyH->hora_sal_reg_sab;
                }

				$txt =
				'<tr>
                    <td colspan="6" width="290">
                        <input type="text" name="rrhh_persona[]" class="input-name" value="'.$name.'" readonly>
                        <input type="text" name="rrhh_id_trabajador[]" value="'.$id_trab.'" style="display:none;">
                        <input type="text" name="rrhh_id_tipo_planilla[]" value="'.$tipoPla.'" style="display:none;">
                    </td>
                    <td style="display:none;"><input type="hidden" name="rrhh_her[]" value="'.$her.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hsr[]" value="'.$hsr.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hsa[]" value="'.$hsa.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hea[]" value="'.$hea.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hes[]" value="'.$hes.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hss[]" value="'.$hss.'"></td>
					<td><input type="time" name="rrhh_ent_reg[]" value="'.$er.'" onchange="calcularDiario();" disabled></td>
					<td><input type="time" name="rrhh_sal_alm[]" value="'.$sa.'" onchange="calcularDiario();" disabled></td>
					<td><input type="time" name="rrhh_ent_alm[]" value="'.$ea.'" onchange="calcularDiario();" disabled></td>
					<td><input type="time" name="rrhh_sal_reg[]" value="'.$sr.'" onchange="calcularDiario();" disabled></td>
					<td><input type="text" name="rrhh_tar_ing[]" class="input-name" value="'.$er.'" readonly></td>
					<td><input type="text" name="rrhh_tar_alm[]" class="input-name" value="'.$ta.'" readonly></td>
                    <td></td>
                    <td></td>
                </tr>';
				$hora[$cont] = $txt;
				$cont++;
			}
        }else{
            $cont = 1;
			$sql = $this->mostrar_trabajador_horario($tipoPla, $empre, $sede, $myfecha);

			foreach ($sql as $row){
                $id_trab = $row->id_trabajador;
                $name = $this->buscar_trabajador_id($id_trab);
                $info = $this->searcHours($id_trab, $myfecha);

                $er = '00:00'; //entrada regular
                $sa = '00:00'; //salida al refrigerio
                $ea = '00:00'; //entrada del refrigerio
                $sr = '00:00'; //salida regular
                $her = '00:00';
                $hsa = '00:00';
                $hea = '00:00';
                $hsr = '00:00';
                $hes = '00:00';
                $hss = '00:00';

                $horaTrab = $this->searchTrabHour($id_trab);
                foreach ($horaTrab as $keyH){
                    $her = $keyH->hora_ent_reg_sem;
                    $hsr = $keyH->hora_sal_reg_sem;
                    ////////////
                    $hsa = $keyH->hora_sal_alm_sem;
                    $hea = $keyH->hora_ent_alm_sem;
                    /////////
                    $hes = $keyH->hora_ent_reg_sab;
                    $hss = $keyH->hora_sal_reg_sab;
                }

				foreach ($info as $value){
                    $tipo = $value->tipo;
					$hour = date('H:i', strtotime($value->horario));

                    if ($tipo == 1){
                        $er = $hour;
                    }elseif ($tipo == 2){
                        $sa = $hour;
                    }elseif ($tipo == 3){
                        $ea = $hour;
                    }elseif ($tipo == 4){
                        $sr = $hour;
                    }
                }

                $cons_perm = DB::table('rrhh.rrhh_permi')->where([['id_trabajador', '=', $id_trab], ['fecha_inicio_permiso', '=', $myfecha]])->get();
                if ($cons_perm->count() > 0){
                    $valPermi = '<button class="btn btn-xs btn-block btn-danger btn-log" onClick="verPermisos('.$id_trab.', '.$fech_perm.')";>Ver detalle</button>';
                }else{
                    $valPermi = '';
                }

				$txt =
				'<tr>
                    <td colspan="6" width="290">
                        <input type="text" name="rrhh_persona[]" class="input-name" value="'.$name.'" readonly>
                        <input type="text" name="rrhh_id_trabajador[]" value="'.$id_trab.'" style="display:none;">
                        <input type="text" name="rrhh_id_tipo_planilla[]" value="'.$tipoPla.'" style="display:none;">
                    </td>
                    <td style="display:none;"><input type="hidden" name="rrhh_her[]" value="'.$her.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hsr[]" value="'.$hsr.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hsa[]" value="'.$hsa.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hea[]" value="'.$hea.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hes[]" value="'.$hes.'"></td>
                    <td style="display:none;"><input type="hidden" name="rrhh_hss[]" value="'.$hss.'"></td>
					<td><input type="time" name="rrhh_ent_reg[]" value="'.$er.'" onchange="calcularDiario();"></td>
					<td><input type="time" name="rrhh_sal_alm[]" value="'.$sa.'" onchange="calcularDiario();"></td>
					<td><input type="time" name="rrhh_ent_alm[]" value="'.$ea.'" onchange="calcularDiario();"></td>
					<td><input type="time" name="rrhh_sal_reg[]" value="'.$sr.'" onchange="calcularDiario();"></td>
					<td><input type="text" name="rrhh_tar_ing[]" class="input-name" value="" readonly></td>
					<td><input type="text" name="rrhh_tar_alm[]" class="input-name" value="" readonly></td>
                    <td></td>
                    <td>'.$valPermi.'</td>
				</tr>';
				$hora[$cont] = $txt;
				$cont++;
			}
			$button = '<button class="btn btn-warning" onclick="Recargar();">Actualizar Horarios</button>';      
        }
        $myArray = array('hora' => $hora,
						 'dia' => $dia,
						 'button' => $button);
		
		echo json_encode($myArray);
    }

    public function permisos_asistencia($id, $fecha){
        $myfecha = date('Y-m-d', strtotime($fecha));
        $sql = DB::table('rrhh.rrhh_permi')->where([['id_trabajador', '=', $id], ['fecha_inicio_permiso', '=', $myfecha]])->get();
        $html = '';

        foreach ($sql as $key){
            $fini = date('d/m/Y', strtotime($key->fecha_inicio_permiso));
            $ffin = date('d/m/Y', strtotime($key->fecha_fin_permiso));
            $hini = date('H:i', strtotime($key->hora_inicio));
            $hfin = date('H:i', strtotime($key->hora_fin));
            $desc = $key->motivo;
            $tipo = $key->tipo;
            $auto = $key->id_trabajador_autoriza;
            $autoriza = $this->buscar_usuario_trab_id($auto);

            $txtTipo = ($tipo == 1) ? 'Permiso' : 'Comisión de Salida';

            $html .=
            '<div class="col-md-12">
                <div class="box box-default box-solid collapsed-box">
                    <div class="box-header with-border">
                        <h3 class="box-title">'.$txtTipo.'</h3>
                        <div class="box-tools pull-right"><button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
                    </div>
                    <div class="box-body" style="display: none; background-color: #fff; padding: 4px; color: #000;">
                        <h5><strong>Fecha: </strong>'.$fini.' al '.$ffin.'</h5>
                        <h5><strong>Hora: </strong>'.$hini.' al '.$hfin.'</h5>
                        <h5><strong>Autoriza: </strong>'.$autoriza.'</h5>
                        <p><h5><strong>Concepto: </strong></h5><h6>'.$desc.'</h6></p>
                    </div>
                </div>
            </div>
            <br>';
        }
        return response()->json($html);
    }

    public function verifyDataHorario($fecha, $tipo, $emp, $sede){
        $sql = DB::table('rrhh.rrhh_asi_diaria')
        ->where([
            ['fecha_asistencia', '=', $fecha], ['id_tipo_planilla', '=', $tipo], ['id_empresa', '=', $emp], ['id_sede', '=', $sede]
        ])->get();
		return $sql->count();
    }

    public function mostrar_trabajador_horario($tipo, $empresa, $sede, $fecha){
        $data = DB::table('rrhh.rrhh_reloj')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_reloj.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
            ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->select('rrhh_trab.id_trabajador', 'rrhh_postu.direccion', 'rrhh_postu.telefono', 'rrhh_perso.nombres','rrhh_perso.apellido_paterno','rrhh_perso.apellido_materno', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_perso.nro_documento')
            ->where([['rrhh_trab.id_tipo_planilla', '=', $tipo], ['rrhh_trab.marcaje', '=', 1], ['adm_empresa.id_empresa', '=', $empresa], ['sis_sede.id_sede', '=', $sede], ['rrhh_reloj.fecha', '=', $fecha], ['rrhh_rol.estado', 1], ['rrhh_trab.estado', 1]])
            
            ->orderBy('rrhh_perso.apellido_paterno','asc')->distinct()->get();
        return $data;
    }
    public function searcHours($id, $fecha){
        $sql = DB::table('rrhh.rrhh_reloj')->where([['id_trabajador', '=', $id],['fecha', '=', $fecha]])->orderBy('id_trabajador', 'asc')->get();
		return $sql;
    }
    public function searchTrabHour($id){
        $sql = DB::table('rrhh.rrhh_contra')
                ->join('rrhh.rrhh_horario', 'rrhh_horario.id_horario', '=', 'rrhh_contra.id_horario')
                ->where('id_trabajador', '=', $id)
                ->orderBy('id_trabajador', 'asc')->get();
		return $sql;
    }
    public function searchPersonalData($fecha, $emp, $sede, $plani){
        $sql = DB::table('rrhh.rrhh_asi_diaria')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_asi_diaria.id_trabajador')
            ->where([
                ['rrhh_asi_diaria.fecha_asistencia', '=', $fecha], ['rrhh_asi_diaria.id_empresa', '=', $emp], ['rrhh_asi_diaria.id_sede', '=', $sede], ['rrhh_asi_diaria.id_tipo_planilla', '=', $plani]
            ])->orderBy('rrhh_trab.id_trabajador', 'asc')->get();
		return $sql;
    }
    public function grabar_asistencia_diaria(Request $request){
        $per = explode(',', $request->personal);
        $tipo = explode(',', $request->tipo);
		$ent = explode(',', $request->entrada);
		$hsa = explode(',', $request->almuerzo_sal);
		$hea = explode(',', $request->almuerzo_ent);
		$sal = explode(',', $request->salida);
		$ting = explode(',', $request->ting);
		$talm = explode(',', $request->talm);

		$count = count($per);
        $fecha = date('Y-m-d', strtotime($request->fecha));
        $empresa = $request->empresa;
        $sede = $request->sede;

		for($i = 0; $i < $count; $i++){
			$val1 = $per[$i];
			$val2 = $ent[$i];
			$val3 = $hsa[$i];
			$val4 = $hea[$i];
			$val5 = $sal[$i];
			$val6 = $ting[$i];
            $val7 = $talm[$i];
            $val8 = $tipo[$i];
            $hoy = date('Y-m-d H:i:s');
            
            $insert = DB::table('rrhh.rrhh_asi_diaria')->insertGetId(
                [
                    'id_asistencia'         => 3,
                    'id_trabajador'         => $val1,
                    'fecha_asistencia'      => $fecha,
                    'hora_entrada'          => $val2,
                    'hora_salida_almuerzo'  => $val3,
                    'hora_entrada_almuerzo' => $val4,
                    'hora_salida'           => $val5,
                    'hora_trabajada'        => 8,
                    'minutos_tardanza'      => $val6,
                    'minutos_tardanza_alm'  => $val7,
                    'id_tipo_planilla'      => $val8,
                    'id_empresa'            => $empresa,
                    'id_sede'               => $sede,
                    'fecha_registro'        => $hoy,
                ],
                'id_asistencia_diaria'
            );
		}
		return response()->json($insert);
    }

    public function grabar_asistencia_final(Request $request){
        $per = explode(',', $request->personal);
        $dia1 = explode(',', $request->dia1);
		$dia2 = explode(',', $request->dia2);
		$dia3 = explode(',', $request->dia3);
		$dia4 = explode(',', $request->dia4);
		$dia5 = explode(',', $request->dia5);
		$dia6 = explode(',', $request->dia6);
        $dia7 = explode(',', $request->dia7);
        $dia8 = explode(',', $request->dia8);
		$dia9 = explode(',', $request->dia9);
		$dia10 = explode(',', $request->dia10);
		$dia11 = explode(',', $request->dia11);
		$dia12 = explode(',', $request->dia12);
		$dia13 = explode(',', $request->dia13);
        $dia14 = explode(',', $request->dia14);
        $dia15 = explode(',', $request->dia15);
		$dia16 = explode(',', $request->dia16);
		$dia17 = explode(',', $request->dia17);
		$dia18 = explode(',', $request->dia18);
		$dia19 = explode(',', $request->dia19);
		$dia20 = explode(',', $request->dia20);
        $dia21 = explode(',', $request->dia21);
        $dia22 = explode(',', $request->dia22);
		$dia23 = explode(',', $request->dia23);
		$dia24 = explode(',', $request->dia24);
		$dia25 = explode(',', $request->dia25);
		$dia26 = explode(',', $request->dia26);
		$dia27 = explode(',', $request->dia27);
        $dia28 = explode(',', $request->dia28);
        $dia29 = explode(',', $request->dia29);
		$dia30 = explode(',', $request->dia30);
        $dia31 = explode(',', $request->dia31);
        $tardanza = explode(',', $request->tardanza);
        $descuento = explode(',', $request->descuento);
        $inasistencia = explode(',', $request->inasistencia);

		$count = count($per);
        $empresa = $request->empresa;
        $sede = $request->sede;
        $tipo = $request->tipo;
        $fecha = date('n', strtotime($request->fecha));

		for($i = 0; $i < $count; $i++){
			$pers = $per[$i];
			$val1 = $dia1[$i];
			$val2 = $dia2[$i];
			$val3 = $dia3[$i];
			$val4 = $dia4[$i];
			$val5 = $dia5[$i];
            $val6 = $dia6[$i];
            $val7 = $dia7[$i];
            $val8 = $dia8[$i];
			$val9 = $dia9[$i];
			$val10 = $dia10[$i];
			$val11 = $dia11[$i];
			$val12 = $dia12[$i];
			$val13 = $dia13[$i];
            $val14 = $dia14[$i];
            $val15 = $dia15[$i];
            $val16 = $dia16[$i];
			$val17 = $dia17[$i];
			$val18 = $dia18[$i];
			$val19 = $dia19[$i];
			$val20 = $dia20[$i];
            $val21 = $dia21[$i];
            $val22 = $dia22[$i];
            $val23 = $dia23[$i];
			$val24 = $dia24[$i];
			$val25 = $dia25[$i];
			$val26 = $dia26[$i];
			$val27 = $dia27[$i];
			$val28 = $dia28[$i];
            $val29 = $dia29[$i];
            $val30 = $dia30[$i];
            $val31 = $dia31[$i];
            $tardz = $tardanza[$i];
            $dscto = $descuento[$i];
            $inasi = $inasistencia[$i];
            $hoy = date('Y-m-d H:i:s');
            $anio = date('Y');
            
            DB::table('rrhh.rrhh_tareo')->insert([
                'id_empresa'        => $empresa,
                'id_sede'           => $sede,
                'id_tipo_planilla'  => $tipo,
                'mes'               => $fecha,
                'id_trabajador'     => $pers,
                'dia_1'             => $val1,
                'dia_2'             => $val2,
                'dia_3'             => $val3,
                'dia_4'             => $val4,
                'dia_5'             => $val5,
                'dia_6'             => $val6,
                'dia_7'             => $val7,
                'dia_8'             => $val8,
                'dia_9'             => $val9,
                'dia_10'            => $val10,
                'dia_11'            => $val11,
                'dia_12'            => $val12,
                'dia_13'            => $val13,
                'dia_14'            => $val14,
                'dia_15'            => $val15,
                'dia_16'            => $val16,
                'dia_17'            => $val17,
                'dia_18'            => $val18,
                'dia_19'            => $val19,
                'dia_20'            => $val20,
                'dia_21'            => $val21,
                'dia_22'            => $val22,
                'dia_23'            => $val23,
                'dia_24'            => $val24,
                'dia_25'            => $val25,
                'dia_26'            => $val26,
                'dia_27'            => $val27,
                'dia_28'            => $val28,
                'dia_29'            => $val29,
                'dia_30'            => $val30,
                'dia_31'            => $val31,
                'tardanza'          => $tardz,
                'descuento'         => $dscto,
                'inasistencia'      => $inasi,
                'fecha_registro'    => $hoy,
                'anio'              => $anio
            ]);
		}
		return response()->json('ok');
    }

    public function tardanza_trabajador($from, $to, $empresa, $sede){
        $from = date('Y-m-d', strtotime($from));
		$to = date('Y-m-d', strtotime($to));
        $nDays = $this->restaFechasDias($to, $from);
         
		$html =
        '<table border="1" class="table table-condensed table-bordered table-hover sortable" width="100%">
            <thead>
                <tr>
                    <th rowspan="2" style="vertical-align:middle;">Datos del Personal</th>
                    <th colspan="'.($nDays + 1).'" style="text-align:center;">Reporte entre '.date('d/m/Y', strtotime($from)).' y '.date('d/m/Y', strtotime($to)).'</th>
                    <th rowspan="2">Acum.</th>
                    <th rowspan="2" width="50">Jornal Dscto.</th>
                    <th rowspan="2" width="60">Inasist</th>
                </tr>
                <tr>';
                for($i = $from; $i <= $to; $i = date("Y-m-d", strtotime($i ."+ 1 days"))){
                    $fexa = date('d/m', strtotime($i));
                    $html .= '<th>'.$fexa.'</th>';
                }

                $html .= '</tr></thead><tbody>';

                $nameSQL = DB::table('rrhh.rrhh_asi_diaria')
                            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_asi_diaria.id_trabajador')
                            ->select('rrhh_asi_diaria.id_trabajador')->where('rrhh_trab.estado', 1)->orderBy('rrhh_asi_diaria.id_trabajador')->distinct()->get();

                if ($nameSQL->count() > 0){
                    foreach ($nameSQL as $value){
                        $idPer = $value->id_trabajador;
                        $nombrePer = $this->buscar_trabajador_id($idPer);
                        $html .= '<tr><td>'.$nombrePer.'</td>';
                        $sum_acumu = '00:00';
                        $sum_acu = '00:00';
                        $inas = 0;
                        $desc = 0;
                        $acumul = 0;
                        $sum_total = 0;
                        $tTotal = 0;
    
                        for($i = $from; $i <= $to; $i = date("Y-m-d", strtotime($i ."+ 1 days"))){
                            $fecha = date('Y-m-d', strtotime($i));
                            $tTotal = '00:00';
    
                            $sql = DB::table('rrhh.rrhh_asi_diaria')->select('rrhh_asi_diaria.*')
                                ->where([['id_trabajador', '=', $idPer], ['fecha_asistencia', '=', $fecha]])->get();
    
                            $her = '00:00';
                            $hsa = '00:00';
                            $hea = '00:00';
                            $hsr = '00:00';
                            $ini = '00:00';
                            $fin = '00:00';
                            $compe = '00:00';
                            $taf = '00:00';
                            
                            $horaTrab = $this->searchTrabHour($idPer);
                            foreach ($horaTrab as $keyH){
                                $Hher = $keyH->hora_ent_reg_sem;
                                $Hhsr = $keyH->hora_sal_reg_sem;
                                ////////////
                                $Hhsa = $keyH->hora_sal_alm_sem;
                                $Hhea = $keyH->hora_ent_alm_sem;
                                /////////
                                $Hhes = $keyH->hora_ent_reg_sab;
                                $Hhss = $keyH->hora_sal_reg_sab;
                            }
    
                            $dia = $this->filtrar_dia($fecha);
    
                            foreach ($sql as $row){
                                $her = date('H:i', strtotime($row->hora_entrada));
                                $hsa = date('H:i', strtotime($row->hora_salida_almuerzo));
                                $hea = date('H:i', strtotime($row->hora_entrada_almuerzo));
                                $hsr = date('H:i', strtotime($row->hora_salida));
                                $thi = date('H:i', strtotime($row->minutos_tardanza));
                                $tha = date('H:i', strtotime($row->minutos_tardanza_alm));
    
                                if ($dia == 6) {
                                    $ini = $Hhes;
                                    $fin = $Hhss;
                                }else{
                                    $ini = $Hher;
                                    $fin = $Hhsr;
                                }
    
                                // Inasistencias
                                if (($her == '00:00') && ($hsa == '00:00') && ($hea == '00:00') && ($hsr == '00:00')){
                                    $inas += 1;
                                }
    
                                if ($tha > '00:00'){
                                    if ($hsr > $fin){
                                        $compe = $this->restar_horas($fin, $hsr);
    
                                        if ($compe > $tha){
                                            $taf = '00:00';
                                        }else{
                                            $taf = $this->restar_horas($compe, $tha);
                                        }
                                    }else{
                                        $taf = $tha;
                                    }
                                }else{
                                    $taf = $tha;
                                }
    
                                $tTotal = $this->sumar_horas($thi, $taf);
                                $sum_acu = $this->sumar_horas($tTotal, $sum_acu);
                                $sum_acumu = $sum_acu;
                            }
                            $totalDscto = $this->convertHtoM($tTotal);
                            $sum_total += $totalDscto;
                            $html .='<td class="okc-numero">'.number_format($totalDscto).'</td>';
                        }
                        $desc = (int) (($sum_total) / 60);
                        $html .= '<td class="okc-numero"><b>'.$sum_total.'</b></td><td class="okc-numero"><b>'.$desc.'</b></td><td class="okc-numero"><b>'.$inas.'</b></td>';
                    }
                }
                $html .=
                '</tr>
            </tbody>
        <table>';

        $data = $html;
        return $data;
    }

    public function tardanza_final_trabajador($from, $to, $empresa, $sede, $plani){
        $from = date('Y-m-d', strtotime($from));
		$to = date('Y-m-d', strtotime($to));
        $nDays = $this->restaFechasDias($to, $from);
        $html =
        '<table border="1" class="table table-condensed table-bordered table-hover sortable" width="100%">
        <thead>
            <tr>
                <th colspan="4" style="text-align:center;">Reporte entre '.date('d/m/Y', strtotime($from)).' y '.date('d/m/Y', strtotime($to)).'</th>
            </tr>
            <tr>
                <th rowspan="2" style="vertical-align:middle;">Datos del Personal</th>
                <th rowspan="2" width="60">Acum.</th>
                <th rowspan="2" width="60">Jornal Dscto.</th>
                <th rowspan="2" width="60">Inasist</th>
            </tr>
        </thead>
        <tbody>';

        $lista = DB::table('rrhh.rrhh_asi_diaria')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_asi_diaria.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select(DB::raw('DISTINCT rrhh_asi_diaria.id_trabajador'), 'rrhh_perso.apellido_paterno')
            ->where('rrhh_asi_diaria.id_tipo_planilla', '=', $plani)
            ->where('rrhh_asi_diaria.id_empresa', '=', $empresa)
            ->whereBetween('rrhh_asi_diaria.fecha_asistencia', [$from, $to])
            ->orderBy('rrhh_perso.apellido_paterno', 'asc')->get();
        
        foreach ($lista as $row){
            $id_trabajador = $row->id_trabajador;
            $nombre = $this->buscar_trabajador_id($id_trabajador);
            $html .= '<tr><td><input type="hidden" name="id_trabajador[]" value="'.$id_trabajador.'">'.$nombre.'</td>';
            $sum_acumu = '00:00';
            $sum_acu = '00:00';
            $inas = 0;
            $sum_total = 0;
            $html_dias = '';
            $contador = 0;

            for($i = $from; $i <= $to; $i = date("Y-m-d", strtotime($i ."+ 1 days"))){
                $fecha = date('Y-m-d', strtotime($i));
                $contDay = date('d', strtotime($i));
                $tTotal = '00:00';

                $sql = DB::table('rrhh.rrhh_asi_diaria')->select('rrhh_asi_diaria.*')
                    ->where([['id_trabajador', '=', $id_trabajador], ['fecha_asistencia', '=', $fecha]])->get();

                $her = '00:00';
                $hsa = '00:00';
                $hea = '00:00';
                $hsr = '00:00';
                $ini = '00:00';
                $fin = '00:00';
                $compe = '00:00';
                $taf = '00:00';
                
                $horaTrab = $this->searchTrabHour($id_trabajador);
                foreach ($horaTrab as $keyH){
                    $Hher = $keyH->hora_ent_reg_sem;
                    $Hhsr = $keyH->hora_sal_reg_sem;
                    ////////////
                    $Hhsa = $keyH->hora_sal_alm_sem;
                    $Hhea = $keyH->hora_ent_alm_sem;
                    /////////
                    $Hhes = $keyH->hora_ent_reg_sab;
                    $Hhss = $keyH->hora_sal_reg_sab;
                }

                $dia = $this->filtrar_dia($fecha);
                $class = '';

                foreach ($sql as $row){
                    $her = date('H:i', strtotime($row->hora_entrada));
                    $hsa = date('H:i', strtotime($row->hora_salida_almuerzo));
                    $hea = date('H:i', strtotime($row->hora_entrada_almuerzo));
                    $hsr = date('H:i', strtotime($row->hora_salida));
                    $thi = date('H:i', strtotime($row->minutos_tardanza));
                    $tha = date('H:i', strtotime($row->minutos_tardanza_alm));

                    if ($dia == 6) {
                        $ini = $Hhes;
                        $fin = $Hhss;
                    }else{
                        $ini = $Hher;
                        $fin = $Hhsr;
                    }

                    // Inasistencias
                    if (($her == '00:00') && ($hsa == '00:00') && ($hea == '00:00') && ($hsr == '00:00')){
                        if ($this->filtrar_dia($i) != 0){
                            $inas += 1;
                        }
                        $class = 'text-danger';
                    }else{
                        $class = 'text-primary';
                    }

                    if ($tha > '00:00'){
                        if ($hsr > $fin){
                            $compe = $this->restar_horas($fin, $hsr);

                            if ($compe > $tha){
                                $taf = '00:00';
                            }else{
                                $taf = $this->restar_horas($compe, $tha);
                            }
                        }else{
                            $taf = $tha;
                        }
                    }else{
                        $taf = $tha;
                    }

                    $tTotal = $this->sumar_horas($thi, $taf);
                    $sum_acu = $this->sumar_horas($tTotal, $sum_acu);
                    $sum_acumu = $sum_acu;
                }
                if ($this->filtrar_dia($i) != 0){
                    $totalDscto = $this->convertHtoM($tTotal);
                    $sum_total += $totalDscto;
                    $html_dias .='<input type="hidden" class="mihora '.$class.'" value="'.number_format($totalDscto).'" name="dia'.$contDay.'[]" />';
                }else{
                    $html_dias .='<input type="hidden" class="mihora deshabilitado" readonly="true" value="0" name="dia'.$contDay.'[]" />';
                }
                $contador+=1;
            }
            $desc = (int) ($sum_total / 60);
            if ($contador != 31) {
                $falta = (31 - $contador);
                $adic = '';
                for ($w = 1; $w <= $falta; $w++){
                    $compl = $contador + $w;
                    $adic .='<input type="hidden" class="mihora deshabilitado" readonly="true" value="0" name="dia'.$compl.'[]" />';
                }
            }else{
                $adic = '';
            }
            $html .=
            '<td class="hidden">'.$html_dias.$adic.'</td>
            <td><input type="text" class="mihorafinal" name="minutos[]" value="'.$sum_total.'" /></td>
            <td><input type="text" class="mihorafinal" name="descuentos[]" value="'.$desc.'" /></td>
            <td><input type="text" class="mihorafinal" name="inasistencia[]" value="'.$inas.'" /></td>';
        }
        $html .= '</tr></tbody><table>';
        return $html;
    }

    /* TOLERANCIA */
    public function mostrar_tolerancia_table(){
        $toler = DB::table('rrhh.rrhh_tolerancia')->where('estado', 1)->orderBy('id_tolerancia', 'asc')->get();
        $output['data'] = $toler;
        return response()->json($output);
    }
    public function mostrar_tolerancia_id($id){
        $data = DB::table('rrhh.rrhh_tolerancia')->where('id_tolerancia', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_tolerancia(Request $request){
        $id = DB::table('rrhh.rrhh_tolerancia')->insertGetId(
            [
                'tiempo'    => $request->tiempo,
                'periodo'   => $request->periodo,
                'estado'    =>1
            ],
            'id_tolerancia'
        );
        return response()->json($id);
    }
    public function actualizar_tolerancia(Request $request){
        $data = DB::table('rrhh.rrhh_tolerancia')->where('id_tolerancia', $request->id_tolerancia)
        ->update([
            'tiempo'  => $request->tiempo,
            'periodo' => $request->periodo,
            'estado'  => 1
        ]);
        return response()->json($data);
    }
    public function anular_tolerancia($id){
        $data = DB::table('rrhh.rrhh_tolerancia')->where('id_tolerancia', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* ESTADO CIVIL */
    public function mostrar_estado_civil_table(){
        $civil = DB::table('rrhh.rrhh_est_civil')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $civil;
        return response()->json($output);
    }
    public function mostrar_est_civil_id($id){
        $data = DB::table('rrhh.rrhh_est_civil')->where('id_estado_civil', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_estado_civil(Request $request){
        $id = DB::table('rrhh.rrhh_est_civil')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_estado_civil'
        );
        return response()->json($id);
    }
    public function actualizar_estado_civil(Request $request){
        $data = DB::table('rrhh.rrhh_est_civil')->where('id_estado_civil', $request->id_estado_civil)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function anular_estado_civil($id){
        $data = DB::table('rrhh.rrhh_est_civil')->where('id_estado_civil', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* CONDICION DERECHO HABIENTE */
    public function mostrar_condiciondh_table(){
        $condh = DB::table('rrhh.rrhh_cdn_dhab')->where('rrhh_cdn_dhab.estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $condh;
        return response()->json($output);
    }
    public function mostrar_condiciondh_id($id){
        $data = DB::table('rrhh.rrhh_cdn_dhab')->where('id_condicion_dh', $id)->get();
        return response()->json($data);
    }
    public function guardar_condicion_dh(Request $request){
        $id = DB::table('rrhh.rrhh_cdn_dhab')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_condicion_dh'
        );
        return response()->json($id);
    }
    public function actualizar_condicion_dh(Request $request){
        $data = DB::table('rrhh.rrhh_cdn_dhab')->where('id_condicion_dh', $request->id_condicion_dh)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function anular_condicion_dh($id){
        $data = DB::table('rrhh.rrhh_cdn_dhab')->where('id_condicion_dh', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* NIVELES DE ESTUDIOS */
    public function mostrar_nivel_estudio_table(){
        $nivel = DB::table('rrhh.rrhh_niv_estud')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $nivel;
        return response()->json($output);
    }
    public function mostrar_nivel_estudios_id($id){
        $data = DB::table('rrhh.rrhh_niv_estud')->where('id_nivel_estudio', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_nivel_estudio(Request $request){
        $id = DB::table('rrhh.rrhh_niv_estud')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_nivel_estudio'
        );
    }
    public function actualizar_nivel_estudio(Request $request){
        $data = DB::table('rrhh.rrhh_niv_estud')->where('id_nivel_estudio', $request->id_nivel_estudio)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function anular_nivel_estudio($id){
        $data = DB::table('rrhh.rrhh_niv_estud')->where('id_nivel_estudio', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* CARRERA */
    public function mostrar_carrera_table(){
        $carre = DB::table('rrhh.rrhh_carrera')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $carre;
        return response()->json($output);
    }
    public function mostrar_carrera_id($id){
        $data = DB::table('rrhh.rrhh_carrera')->where('id_carrera', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_carrera(Request $request){
        $id = DB::table('rrhh.rrhh_carrera')->insertGetId(
            [
                'descripcion'   => strtoupper($request->descripcion),
                'estado'        => 1
            ],
            'id_carrera'
        );
        return response()->json($id);
    }
    public function actualizar_carrera(Request $request){
        $data = DB::table('rrhh.rrhh_carrera')->where('id_carrera', $request->id_carrera)
        ->update([
            'descripcion'   => strtoupper($request->descripcion),
            'estado'        => 1
        ]);
        return response()->json($data);
    }
    public function anular_carrera($id){
        $data = DB::table('rrhh.rrhh_carrera')->where('id_carrera', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* TIPO TRABAJADOR */
    public function mostrar_tipo_trabajador_table(){
        $tptrab = DB::table('rrhh.rrhh_tp_trab')->where('rrhh_tp_trab.estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data']= $tptrab;
        return response()->json($output);
    }
    public function mostrar_tipo_trabajador_id($id){
        $data = DB::table('rrhh.rrhh_tp_trab')->where('id_tipo_trabajador', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_tipo_trabajador(Request $request){
        $id = DB::table('rrhh.rrhh_tp_trab')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_tipo_trabajador'
        );
        return response()->json($id);
    }
    public function actualizar_tipo_trabajador(Request $request){
        $data = DB::table('rrhh.rrhh_tp_trab')->where('id_tipo_trabajador', $request->id_tipo_trabajador)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function anular_tipo_trabajador($id){
        $data = DB::table('rrhh.rrhh_tp_trab')->where('id_tipo_trabajador', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* TIPO CONTRATO */
    public function mostrar_tipo_contrato_table(){
        $tpcon = DB::table('rrhh.rrhh_tp_contra')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $tpcon;
        return response()->json($output);
    }
    public function mostrar_tipo_contrato_id($id){
        $data = DB::table('rrhh.rrhh_tp_contra')->where('id_tipo_contrato', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_tipo_contrato(Request $request){
        $id = DB::table('rrhh.rrhh_tp_contra')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_tipo_contrato'
        );
        return response()->json($id);
    }
    public function actualizar_tipo_contrato(Request $request){
        $data = DB::table('rrhh.rrhh_tp_contra')->where('id_tipo_contrato', $request->id_tipo_contrato)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function anular_tipo_contrato(Request $request, $id){
        $data = DB::table('rrhh.rrhh_tp_contra')->where('id_tipo_contrato', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* MODALIDAD */
    public function mostrar_modalidad_table(){
        $modali = DB::table('rrhh.rrhh_modali')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $modali;
        return response()->json($output);
    }
    public function mostrar_modalidad_id($id){
        $data = DB::table('rrhh.rrhh_modali')->where('id_modalidad', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_modalidad(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_modali')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'dias_trabajo'      => $request->dias_trabajo,
                'dias_descanso'     => $request->dias_descanso,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_modalidad'
        );
        return response()->json($id);
    }
    public function actualizar_modalidad(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_modali')->where('id_modalidad', $request->id_modalidad)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'dias_trabajo'      => $request->dias_trabajo,
            'dias_descanso'     => $request->dias_descanso,
            'estado'            => 1,
            'fecha_registro'    => $fecha_registro
        ]);
        return response()->json($data);
    }
    public function anular_modalidad($id){
        $data = DB::table('rrhh.rrhh_modali')->where('id_modalidad', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* CONCEPTO DE ROLES */
    public function mostrar_concepto_rol_table(){
        $concepto = DB::table('rrhh.rrhh_rol_concepto')->where('estado', 1)->orderBy('id_rol_concepto', 'asc')->get();
        $output['data'] = $concepto;
        return response()->json($output);
    }
    public function mostrar_concepto_rol_id($id){
        $data = DB::table('rrhh.rrhh_rol_concepto')->where('id_rol_concepto', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_concepto_rol(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_rol_concepto')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_rol_concepto'
        );
        return response()->json($id);
    }
    public function actualizar_concepto_rol(Request $request){
        $data = DB::table('rrhh.rrhh_rol_concepto')->where('id_rol_concepto', $request->id_rol_concepto)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function anular_concepto_rol(Request $request, $id){
        $data = DB::table('rrhh.rrhh_rol_concepto')->where('id_rol_concepto', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* CATEGORIA OCUPACIONAL */
    public function mostrar_categoria_ocupacional(){
        $concepto = DB::table('rrhh.rrhh_cat_ocupac')->where('estado', 1)->orderBy('id_categoria_ocupacional', 'asc')->get();
        $output['data'] = $concepto;
        return response()->json($output);
    }
    public function mostrar_categoria_ocupacional_id($id){
        $data = DB::table('rrhh.rrhh_cat_ocupac')->where('id_categoria_ocupacional', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_categoria_ocupacional(Request $request){
        $id = DB::table('rrhh.rrhh_cat_ocupac')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_categoria_ocupacional'
        );
        return response()->json($id);
    }
    public function actualizar_categoria_ocupacional(Request $request){
        $data = DB::table('rrhh.rrhh_cat_ocupac')->where('id_categoria_ocupacional', $request->id_categoria_ocupacional)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function anular_categoria_ocupacional(Request $request, $id){
        $data = DB::table('rrhh.rrhh_cat_ocupac')->where('id_categoria_ocupacional', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* TIPO PLANILLA */
    public function mostrar_tipo_planilla_table(){
        $tplani = DB::table('rrhh.rrhh_tp_plani')->where('rrhh_tp_plani.estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $tplani;
        return response()->json($output);
    }
    public function mostrar_tipo_planilla_id($id){
        $data = DB::table('rrhh.rrhh_tp_plani')->where('id_tipo_planilla', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_tipo_planilla(Request $request){
        $id = DB::table('rrhh.rrhh_tp_plani')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_tipo_planilla'
        );
        return response()->json($id);
    }
    public function actualizar_tipo_planilla(Request $request, $id){
        $data = DB::table('rrhh.rrhh_tp_plani')->where('id_tipo_planilla', $id)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function anular_tipo_planilla(Request $request, $id){
        $data = DB::table('rrhh.rrhh_tp_plani')->where('id_tipo_planilla', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* MERITOS */
    public function mostrar_tipo_merito_table(){
        $merito = DB::table('rrhh.rrhh_var_merito')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $merito;
        return response()->json($output);
    }
    public function mostrar_tipo_merito_id($id){
        $data = DB::table('rrhh.rrhh_var_merito')->where('id_variable_merito', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_tipo_merito(Request $request){
        $id = DB::table('rrhh.rrhh_var_merito')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_variable_merito'
        );
        return response()->json($id);
    }
    public function actualizar_tipo_merito(Request $request){
        $data = DB::table('rrhh.rrhh_var_merito')->where('id_variable_merito', $request->id_variable_merito)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function anular_tipo_merito($id){
        $data = DB::table('rrhh.rrhh_var_merito')->where('id_variable_merito', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* DEMERITOS */
    public function mostrar_tipo_demerito_table(){
        $sanci = DB::table('rrhh.rrhh_var_sanci')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $sanci;
        return response()->json($output);
    }
    public function mostrar_tipo_demerito_id($id){
        $data = DB::table('rrhh.rrhh_var_sanci')->where('id_variable_sancion', '=', $id)->get();
        return response()->json($data);
    }
    public function guardar_tipo_demerito(Request $request){
        $id = DB::table('rrhh.rrhh_var_sanci')->insertGetId(
            [
                'descripcion'   => strtoupper($request->descripcion),
                'estado'        => 1
            ],
            'id_variable_sancion'
        );
        return response()->json($id);
    }
    public function actualizar_tipo_demerito(Request $request){
        $data = DB::table('rrhh.rrhh_var_sanci')->where('id_variable_sancion', $request->id_variable_sancion)
        ->update([
            'descripcion'   => strtoupper($request->descripcion),
            'estado'        => 1
        ]);
        return response()->json($data);
    }
    public function anular_tipo_demerito($id){
        $data = DB::table('rrhh.rrhh_var_sanci')->where('id_variable_sancion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* TIPO BONIFICACION */
    public function mostrar_tipo_bonificacion_table(){
        $bonif = DB::table('rrhh.rrhh_var_bonif')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $bonif;
        return response()->json($output);
    }
    public function mostrar_tipo_bonificacion_id($id){
        $data = DB::table('rrhh.rrhh_var_bonif')->where('id_variable_bonificacion', $id)->get();
        return response()->json($data);
    }
    public function guardar_tipo_bonificacion(Request $request){
        $id = DB::table('rrhh.rrhh_var_bonif')->insertGetId(
            [
                'descripcion'   => strtoupper($request->descripcion),
                'estado'        => 1
            ],
            'id_variable_bonificacion'
        );
        return response()->json($id);
    }
    public function actualizar_tipo_bonificacion(Request $request){
        $data = DB::table('rrhh.rrhh_var_bonif')->where('id_variable_bonificacion', $request->id_variable_bonificacion)
        ->update([
            'descripcion'   => strtoupper($request->descripcion),
            'estado'        => 1
        ]);
        return response()->json($data);
    }
    public function anular_tipo_bonificacion($id){
        $data = DB::table('rrhh.rrhh_var_bonif')->where('id_variable_bonificacion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* TIPO DESCUENTOS */
    public function mostrar_tipo_descuento_table(){
        $dscto = DB::table('rrhh.rrhh_var_dscto')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $dscto;
        return response()->json($output);
    }
    public function mostrar_tipo_descuento_id($id){
        $data = DB::table('rrhh.rrhh_var_dscto')->where('id_variable_descuento', $id)->get();
        return response()->json($data);
    }
    public function guardar_tipo_descuento(Request $request){
        $id = DB::table('rrhh.rrhh_var_dscto')->insertGetId(
            [
                'descripcion'   => strtoupper($request->descripcion),
                'estado'        => 1
            ],
            'id_variable_descuento'
        );
        return response()->json($id);
    }
    public function actualizar_tipo_descuento(Request $request){
        $data = DB::table('rrhh.rrhh_var_dscto')->where('id_variable_descuento', $request->id_variable_descuento)
        ->update([
            'descripcion'   => strtoupper($request->descripcion),
            'estado'        => 1
        ]);
        return response()->json($data);
    }
    public function anular_tipo_descuento($id){
        $data = DB::table('rrhh.rrhh_var_dscto')->where('id_variable_descuento', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* TIPO RETENCION */
    public function mostrar_tipo_retencion_table(){
        $reten = DB::table('rrhh.rrhh_var_reten')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $reten;
        return response()->json($output);
    }
    public function mostrar_tipo_retencion_id($id){
        $data = DB::table('rrhh.rrhh_var_reten')->where('id_variable_retencion', $id)->get();
        return response()->json($data);
    }
    public function guardar_tipo_retencion(Request $request){
        $id = DB::table('rrhh.rrhh_var_reten')->insertGetId(
            [
                'descripcion'   => strtoupper($request->descripcion),
                'estado'        => 1
            ],
            'id_variable_retencion'
        );
        return response()->json($id);
    }
    public function actualizar_tipo_retencion(Request $request){
        $data = DB::table('rrhh.rrhh_var_reten')->where('id_variable_retencion', $request->id_variable_retencion)
        ->update([
            'descripcion'   => strtoupper($request->descripcion),
            'estado'        => 1
        ]);
        return response()->json($data);
    }
    public function anular_tipo_retencion($id){
        $data = DB::table('rrhh.rrhh_var_reten')->where('id_variable_retencion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* TIPO APORTACIONES DEL EMPLEADOR */
    public function mostrar_tipo_aporte_table(){
        $aport = DB::table('rrhh.rrhh_var_aport')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $aport;
        return response()->json($output);
    }
    public function mostrar_tipo_aporte_id($id){
        $data = DB::table('rrhh.rrhh_var_aport')->where('id_variable_aportacion', $id)->get();
        return response()->json($data);
    }
    public function guardar_tipo_aporte(Request $request){
        $id = DB::table('rrhh.rrhh_var_aport')->insertGetId(
            [
                'descripcion'   => strtoupper($request->descripcion),
                'estado'        => 1
            ],
            'id_variable_aportacion'
        );
        return response()->json($id);
    }
    public function actualizar_tipo_aporte(Request $request){
        $data = DB::table('rrhh.rrhh_var_aport')->where('id_variable_aportacion', $request->id_variable_aportacion)
        ->update([
            'descripcion'   => strtoupper($request->descripcion),
            'estado'        => 1
        ]);
        return response()->json($data);
    }
    public function anular_tipo_aporte($id){
        $data = DB::table('rrhh.rrhh_var_aport')->where('id_variable_aportacion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* FONDO PENSION */
    public function mostrar_pension_table(){
        $aport = DB::table('rrhh.rrhh_pensi')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $aport;
        return response()->json($output);
    }
    public function mostrar_pension_id($id){
        $data = DB::table('rrhh.rrhh_pensi')->where('id_pension', $id)->get();
        return response()->json($data);
    }
    public function guardar_pension(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_pensi')->insertGetId(
            [
                'descripcion'           => strtoupper($request->descripcion),
                'porcentaje_general'    => $request->porcentaje_general,
                'aporte'                => $request->aporte,
                'prima_seguro'          => $request->prima_seguro,
                'comision'              => $request->comision,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro
            ],
            'id_pension'
        );
        return response()->json($id);
    }
    public function actualizar_pension(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_pensi')->where('id_pension', $request->id_pension)
        ->update([
            'descripcion'           => strtoupper($request->descripcion),
            'porcentaje_general'    => $request->porcentaje_general,
            'aporte'                => $request->aporte,
            'prima_seguro'          => $request->prima_seguro,
            'comision'              => $request->comision,
            'estado'                => 1,
            'fecha_registro'        => $fecha_registro
        ]);
        return response()->json($data);
    }
    public function anular_pension($id){
        $data = DB::table('rrhh.rrhh_pensi')->where('id_pension', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    public function cargar_regimen($id){
        $html = '';
        $bonif = DB::table('rrhh.rrhh_rol')->where([['id_trabajador', '=', $id], ['estado', '=', 1]])->get();

        foreach ($bonif as $key){
            $id_tp = $key->id_tipo_planilla;
            $sqlTP = DB::table('rrhh.rrhh_tp_plani')->where('id_tipo_planilla', $id_tp)->get()->first();
            $name_tp = $sqlTP->descripcion;
            $html .= '<option value="'.$id_tp.'">'.$name_tp.'</option>';
        }
        return response()->json($html);
    }

    /* BONIFICACIONES */
    public function mostrar_bonificacion_table($id){
        $html = '';
        $bonificacion = DB::table('rrhh.rrhh_bonif')
            ->join('rrhh.rrhh_var_bonif', 'rrhh_var_bonif.id_variable_bonificacion', '=', 'rrhh_bonif.id_variable_bonificacion')
            ->select('rrhh_bonif.*', 'rrhh_var_bonif.descripcion AS tipo')
            ->where([['rrhh_bonif.id_trabajador', '=', $id], ['rrhh_bonif.estado', '=', 1]])->orderBy('rrhh_bonif.id_bonificacion', 'asc')->get();

        if ($bonificacion->count() > 0){
            foreach ($bonificacion as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_bonificacion.'</td>
                    <td>'.$row->tipo.'</td>
                    <td>'.$row->afecto.'</td>
                    <td>'.number_format($row->importe, 2).'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_bonificacion)).'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="4"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_bonificacion_id($id){
        $bonif = DB::table('rrhh.rrhh_bonif')->where('rrhh_bonif.id_bonificacion', '=', $id)->get();
        return response()->json($bonif);
    }
    public function guardar_bonificacion(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_bonif')->insertGetId(
            [
                'id_trabajador'             => $request->id_trabajador,
                'id_variable_bonificacion'  => $request->id_variable_bonificacion,
                'afecto'                    => $request->afecto,
                'concepto'                  => strtoupper($request->motivo),
                'importe'                   => $request->importe,
                'fecha_bonificacion'        => $request->fecha,
                'id_tipo_planilla'          => $request->id_tipo_pla,
                'estado'                    => 1,
                'fecha_registro'            => $fecha_registro
            ],
            'id_bonificacion'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_bonificacion(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_bonif')->where('id_bonificacion', $request->id_bonificacion)
        ->update([
            'id_trabajador'             => $request->id_trabajador,
            'id_variable_bonificacion'  => $request->id_variable_bonificacion,
            'afecto'                    => $request->afecto,
            'concepto'                  => strtoupper($request->motivo),
            'importe'                   => $request->importe,
            'fecha_bonificacion'        => $request->fecha,
            'id_tipo_planilla'          => $request->id_tipo_pla,
            'estado'                    => 1,
            'fecha_registro'            => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_bonificacion($id){
        $data = DB::table('rrhh.rrhh_bonif')->where('id_bonificacion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* DESCUENTOS */
    public function mostrar_descuento_table($id){
        $html = '';
        $descuento = DB::table('rrhh.rrhh_dscto')
            ->join('rrhh.rrhh_var_dscto', 'rrhh_var_dscto.id_variable_descuento', '=', 'rrhh_dscto.id_variable_descuento')
            ->select('rrhh_dscto.*', 'rrhh_var_dscto.descripcion AS tipo')
            ->where([['rrhh_dscto.id_trabajador', '=', $id], ['rrhh_dscto.estado', '=', 1]])->orderBy('rrhh_dscto.id_descuento', 'asc')->get();

        if ($descuento->count() > 0){
            foreach ($descuento as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_descuento.'</td>
                    <td>'.$row->tipo.'</td>
                    <td>'.$row->afecto.'</td>
                    <td>'.number_format($row->importe, 2).'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_descuento)).'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="4"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_descuento_id($id){
        $dscto = DB::table('rrhh.rrhh_dscto')->where('rrhh_dscto.id_descuento', '=', $id)->get();
        return response()->json($dscto);
    }
    public function guardar_descuento(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_dscto')->insertGetId(
            [
                'id_trabajador'             => $request->id_trabajador,
                'id_variable_descuento'     => $request->id_variable_descuento,
                'afecto'                    => $request->afecto,
                'concepto'                  => strtoupper($request->motivo),
                'importe'                   => $request->importe,
                'fecha_descuento'           => $request->fecha,
                'id_tipo_planilla'          => $request->id_tipo_pla,
                'estado'                    => 1,
                'fecha_registro'            => $fecha_registro
            ],
            'id_descuento'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_descuento(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_dscto')->where('id_descuento', $request->id_descuento)
        ->update([
            'id_trabajador'             => $request->id_trabajador,
            'id_variable_descuento'     => $request->id_variable_descuento,
            'afecto'                    => $request->afecto,
            'concepto'                  => strtoupper($request->motivo),
            'importe'                   => $request->importe,
            'fecha_descuento'           => $request->fecha,
            'id_tipo_planilla'          => $request->id_tipo_pla,
            'estado'                    => 1,
            'fecha_registro'            => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_descuento($id){
        $data = DB::table('rrhh.rrhh_dscto')->where('id_descuento', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* RETENCIONES */
    public function mostrar_retencion_table($id){
        $html = '';
        $retencion = DB::table('rrhh.rrhh_retencion')
            ->join('rrhh.rrhh_var_reten', 'rrhh_var_reten.id_variable_retencion', '=', 'rrhh_retencion.id_variable_retencion')
            ->select('rrhh_retencion.*', 'rrhh_var_reten.descripcion AS tipo')
            ->where([['rrhh_retencion.id_trabajador', '=', $id], ['rrhh_retencion.estado', '=', 1]])->orderBy('rrhh_retencion.id_retencion', 'asc')->get();

        if ($retencion->count() > 0){
            foreach ($retencion as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_retencion.'</td>
                    <td>'.$row->tipo.'</td>
                    <td>'.$row->afecto.'</td>
                    <td>'.number_format($row->importe, 2).'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha_retencion)).'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="4"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_retencion_id($id){
        $bonif = DB::table('rrhh.rrhh_retencion')->where('rrhh_retencion.id_retencion', '=', $id)->get();
        return response()->json($bonif);
    }
    public function guardar_retencion(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_retencion')->insertGetId(
            [
                'id_trabajador'             => $request->id_trabajador,
                'id_variable_retencion'     => $request->id_variable_retencion,
                'afecto'                    => $request->afecto,
                'concepto'                  => strtoupper($request->motivo),
                'importe'                   => $request->importe,
                'fecha_retencion'           => $request->fecha,
                'estado'                    => 1,
                'fecha_registro'            => $fecha_registro
            ],
            'id_retencion'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_retencion(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_retencion')->where('id_retencion', $request->id_retencion)
        ->update([
            'id_trabajador'             => $request->id_trabajador,
            'id_variable_retencion'  => $request->id_variable_retencion,
            'afecto'                    => $request->afecto,
            'concepto'                  => strtoupper($request->motivo),
            'importe'                   => $request->importe,
            'fecha_retencion'        => $request->fecha,
            'estado'                    => 1,
            'fecha_registro'            => $fecha_registro
        ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_retencion($id){
        $data = DB::table('rrhh.rrhh_retencion')->where('id_retencion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* APORTACIONES */
    public function mostrar_aportacion_table(){
        $data = DB::table('rrhh.rrhh_aport')
                ->join('rrhh.rrhh_var_aport', 'rrhh_var_aport.id_variable_aportacion', '=', 'rrhh_aport.id_variable_aportacion')
                ->select('rrhh_aport.*', 'rrhh_var_aport.descripcion AS tipo')->where('rrhh_aport.estado', '=', 1)->orderBy('rrhh_aport.id_aportacion', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_aportacion_id($id){
        $data = DB::table('rrhh.rrhh_aport')->where('id_aportacion', $id)->get();
        return response()->json($data);
    }
    public function guardar_aportacion(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_aport')->insertGetId(
            [
                'id_variable_aportacion'    => $request->id_variable_aportacion,
                'concepto'                  => strtoupper($request->concepto),
                'valor'                     => $request->valor,
                'estado'                    => 1,
                'fecha_registro'            => $fecha_registro
            ],
            'id_aportacion'
        );
        return response()->json($id);
    }
    public function actualizar_aportacion(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_aport')->where('id_aportacion', $request->id_aportacion)
        ->update([
            'id_variable_aportacion'    => $request->id_variable_aportacion,
            'concepto'                  => strtoupper($request->concepto),
            'valor'                     => $request->valor
        ]);
        return response()->json($data);
    }
    public function anular_aportacion($id){
        $data = DB::table('rrhh.rrhh_aport')->where('id_aportacion', $id)
        ->update([
            'estado'     => 2
        ]);
        return response()->json($data);
    }

    /* REINTEGROS */
    public function mostrar_reintegro_table($id){
        $html = '';
        $reintegro = DB::table('rrhh.rrhh_reintegro')
            ->where([['rrhh_reintegro.id_trabajador', '=', $id], ['rrhh_reintegro.estado', '=', 1]])->orderBy('rrhh_reintegro.id_reintegro', 'asc')->get();

        if ($reintegro->count() > 0){
            foreach ($reintegro as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_reintegro.'</td>
                    <td>'.$row->concepto.'</td>
                    <td>'.number_format($row->importe, 2).'</td>
                    <td>'.date('d/m/Y', strtotime($row->fecha)).'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="3"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_reintegro_id($id){
        $bonif = DB::table('rrhh.rrhh_reintegro')->where('rrhh_reintegro.id_reintegro', '=', $id)->get();
        return response()->json($bonif);
    }
    public function guardar_reintegro(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_reintegro')->insertGetId(
            [
                'id_trabajador'     => $request->id_trabajador,
                'fecha'             => $request->fecha,
                'importe'           => $request->importe,
                'concepto'          => strtoupper($request->motivo),
                'afecto'             => $request->afecto,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_reintegro'
        );
        if ($id > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function actualizar_reintegro(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_reintegro')->where('id_reintegro', $request->id_reintegro)
            ->update([
                'id_trabajador'     => $request->id_trabajador,
                'fecha'             => $request->fecha,
                'importe'           => $request->importe,
                'concepto'          => strtoupper($request->motivo),
                'afecto'             => $request->afecto,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ]);
        if ($data > 0){
            $val = $request->id_trabajador;
        }else{
            $val = 0;
        }
        return response()->json($val);
    }
    public function anular_reintegro($id){
        $data = DB::table('rrhh.rrhh_reintegro')->where('id_reintegro', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* PLANILLAS */
    public function tardanza_planilla_trabajador($from, $to, $id){
        $from = date('Y-m-d', strtotime($from));
		$to = date('Y-m-d', strtotime($to));
		$nDays = $this->restaFechasDias($to, $from);

        $nameSQL = DB::table('rrhh.rrhh_asi_diaria')->select('id_trabajador')
            ->where('id_trabajador', '=', $id)->get();

        foreach ($nameSQL as $value){
            $idPer = $value->id_trabajador;
            $sum_acumu = '00:00';
            $sum_acu = '00:00';
            $inas = 0;

            for($i = $from; $i <= $to; $i = date("Y-m-d", strtotime($i ."+ 1 days"))){
                $fecha = date('Y-m-d', strtotime($i));

                $sql = DB::table('rrhh.rrhh_asi_diaria')->select('rrhh_asi_diaria.*')
                    ->where([['id_trabajador', '=', $idPer], ['fecha_asistencia', '=', $fecha]])->get();
                $tTotal = '00:00';

                $her = '00:00';
                $hsa = '00:00';
                $hea = '00:00';
                $hsr = '00:00';
                $ini = '00:00';
                $fin = '00:00';
                $compe = '00:00';
                $taf = '00:00';
                
                $horaTrab = $this->searchTrabHour($idPer);
                foreach ($horaTrab as $keyH){
                    $Hher = $keyH->hora_ent_reg_sem;
                    $Hhsr = $keyH->hora_sal_reg_sem;
                    ////////////
                    $Hhsa = $keyH->hora_sal_alm_sem;
                    $Hhea = $keyH->hora_ent_alm_sem;
                    /////////
                    $Hhes = $keyH->hora_ent_reg_sab;
                    $Hhss = $keyH->hora_sal_reg_sab;
                }

                $dia = $this->filtrar_dia($fecha);

                foreach ($sql as $row){
                    $her = date('H:i', strtotime($row->hora_entrada));
                    $hsa = date('H:i', strtotime($row->hora_salida_almuerzo));
                    $hea = date('H:i', strtotime($row->hora_entrada_almuerzo));
                    $hsr = date('H:i', strtotime($row->hora_salida));
                    $thi = date('H:i', strtotime($row->minutos_tardanza));
                    $tha = date('H:i', strtotime($row->minutos_tardanza_alm));

                    if ($dia == 6) {
                        $ini = $Hhes;
                        $fin = $Hhss;
                    }else{
                        $ini = $Hher;
                        $fin = $Hhsr;
                    }

                    // Inasistencias
                    if (($her == '00:00') && ($hsa == '00:00') && ($hea == '00:00') && ($hsr == '00:00')){
                        $inas += 1;
                    }

                    if ($tha > '00:00'){
                        if ($hsr > $fin){
                            $compe = $this->restar_horas($fin, $hsr);

                            if ($compe > $tha){
                                $taf = '00:00';
                            }else{
                                $taf = $this->restar_horas($compe, $tha);
                            }
                        }else{
                            $taf = $tha;
                        }
                    }else{
                        $taf = $tha;
                    }

                    $tTotal = $this->sumar_horas($thi, $taf);
                    $sum_acu = $this->sumar_horas($tTotal, $sum_acu);
                    $sum_acumu = $sum_acu;
                }
                $totalDscto = $this->convertHtoM($tTotal);
            }
            $desc = $this->convertDescuents($sum_acumu);
            $acumul = $this->convertHtoM($sum_acumu);
        }

        $data = array('descuento' => $desc, 'tardanza' => $acumul);
        return $data;
    }

    function dias_efectivos($ini, $fin, $type){
        $from = date('Y-m-d', strtotime($ini));
        $to = date('Y-m-d', strtotime($fin));
        $cont = 0;
        $feriado = array('08/10', '01/11', '25/12', '01/01');

        for($i = $from; $i <= $to; $i = date("Y-m-d", strtotime($i ."+ 1 days"))){
            $fecha = date('Y-m-d', strtotime($i));
            $fex = date('d/m', strtotime($i));
            $dia = $this->filtrar_dia($fecha);

            if (in_array($fex, $feriado) == false){
                if ($type == 13) {
                    if ($dia == 1 || $dia == 2 || $dia == 3 || $dia == 4 || $dia == 5 || $dia == 6){
                        $cont += 1;
                    }
                }else{
                    if ($dia == 1 || $dia == 2 || $dia == 3 || $dia == 4 || $dia == 5){
                        $cont += 1;
                    }
                }
            }
        }
        return $cont;
    }

    function dias_total($mes, $anio){
        if( is_callable("cal_days_in_month")){
            return cal_days_in_month(1, $mes, $anio);
        }
    }

    public function remuneracion_spcc($emp, $plani, $mes, $anio){
        $data_prev = array();
        $data = array();
        $dias_compu = 30;
        $total_dias = cal_days_in_month(1, $mes, $anio);
        $hora_trab = 8;

        $list_trab = DB::table('rrhh.rrhh_extra_spcc')
                    ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_extra_spcc.id_trabajador')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                    ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->select(DB::raw('DISTINCT rrhh_trab.id_trabajador'), 'rrhh_perso.apellido_paterno', 'rrhh_extra_spcc.dias_trabajados',
                            'rrhh_extra_spcc.dias_feriados', 'rrhh_extra_spcc.extra_retencion')
                    ->where([
                        ['rrhh_extra_spcc.mes', '=', $mes], ['rrhh_extra_spcc.periodo', '=', $anio], ['rrhh_extra_spcc.estado', '=', 1]
                    ])->orderBy('rrhh_perso.apellido_paterno', 'asc')->get();
        
        foreach ($list_trab as $row){
            $ids_trab = $row->id_trabajador;
            $dias_total = $row->dias_trabajados;
            $dias_fer = $row->dias_feriados;
            $q_retenc = $row->extra_retencion;

            // DATOS TRABAJADOR
            $sqlDatos = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', 'rrhh_trab.id_tipo_trabajador')
                ->join('rrhh.rrhh_cat_ocupac', 'rrhh_cat_ocupac.id_categoria_ocupacional', 'rrhh_trab.id_categoria_ocupacional')
                ->join('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'rrhh_perso.id_documento_identidad')
                ->join('rrhh.rrhh_pensi', 'rrhh_pensi.id_pension', 'rrhh_trab.id_pension')
                ->select('rrhh_perso.nro_documento AS nro_doc_persona', 'rrhh_perso.sexo', 'sis_identi.descripcion AS doc_identidad', 'rrhh_trab.cuspp', 'rrhh_trab.hijos', 'rrhh_trab.marcaje',
                        'rrhh_pensi.descripcion AS pension', 'rrhh_pensi.id_pension', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_cat_ocupac.descripcion AS categ_trabajador',
                        DB::raw("CONCAT(rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno,' ',rrhh_perso.nombres) AS datos_trabajador"))
                ->where('rrhh_trab.id_trabajador', '=', $ids_trab)->first();

            // ROL DEL TRABAJADOR
            $sqlRol = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
                ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->select('rrhh_cargo.descripcion AS cargo', 'adm_area.descripcion AS area', 'sis_sede.descripcion AS sede',
                'adm_grupo.id_grupo', 'adm_grupo.descripcion AS grupo', 'rrhh_rol.salario', 'adm_contri.nro_documento AS ruc', 'adm_contri.razon_social AS empresa',
                'adm_empresa.id_empresa', 'sis_sede.id_sede', 'rrhh_rol.id_area', 'rrhh_rol.id_cargo', 'rrhh_rol.fecha_inicio', 'rrhh_rol.fecha_fin', 'rrhh_rol.sctr')
                ->where([['rrhh_trab.id_trabajador', '=', $ids_trab], ['rrhh_rol.salario', '>', 0], ['rrhh_rol.id_tipo_planilla', '=', $plani]])
                ->orderBy('rrhh_rol.id_rol', 'desc')->limit(1)->get();

            if ($sqlRol->count() > 0){
                $data = $sqlRol->first();
                $ruc = $data->ruc;
                $empre = $data->empresa;
                $id_empre = $data->id_empresa;
                $cargo = strtoupper($data->cargo);
                $id_cargo = $data->id_cargo;
                $sede = strtoupper($data->sede);
                $id_sede = $data->id_sede;
                $area = $data->area;
                $id_area = $data->id_area;
                $grup = strtoupper($data->grupo);
                $id_grup = $data->id_grupo;
                $salario = $data->salario;
                $ini_contrato = $data->fecha_inicio;
                $fin_contrato = ($data->fecha_fin != null) ? $data->fecha_fin : null;
                $sctr = ($data->sctr == true) ? 1 : 0;
            }else{
                $ruc = '';
                $empre = '';
                $id_empre = 0;
                $sede = '';
                $id_sede = 0;
                $grup = '';
                $id_grup = 0;
                $area = '';
                $id_area = 0;
                $cargo = '';
                $id_cargo = 0;
                $salario = 0;
                $ini_contrato = '';
                $fin_contrato = '';
                $sctr = 0;
            }

            // CONTRATO DEL TRABAJADOR
            $sqlContrato = DB::table('rrhh.rrhh_contra')
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_contra.id_trabajador')
                ->join('rrhh.rrhh_tp_contra', 'rrhh_tp_contra.id_tipo_contrato', '=', 'rrhh_contra.id_tipo_contrato')
                ->join('rrhh.rrhh_modali', 'rrhh_modali.id_modalidad', '=', 'rrhh_contra.id_modalidad')
                ->join('rrhh.rrhh_horario', 'rrhh_horario.id_horario', '=', 'rrhh_contra.id_horario')
                ->select('rrhh_modali.descripcion AS modalidad', 'rrhh_tp_contra.descripcion AS tipo_contrato', 'rrhh_contra.tipo_centro_costo',
                        'rrhh_contra.fecha_inicio AS contra_inicio', 'rrhh_contra.fecha_fin AS contra_fin' , 'rrhh_contra.motivo', 
                        'rrhh_horario.id_horario', 'rrhh_horario.hora_sem')
                ->where([['rrhh_trab.id_trabajador', '=', $ids_trab], ['rrhh_contra.estado', '>', 0]])
                ->limit(1)->orderBy('rrhh_contra.id_contrato', 'desc')->get();
            
            if ($sqlContrato->count() > 0){
                $ctts = $sqlContrato->first();
                $modalid = $ctts->modalidad;
                $tpcontra = $ctts->tipo_contrato;
                $tp_cc = $ctts->tipo_centro_costo;
                $horas = $ctts->hora_sem;
                $tp_horario = $ctts->id_horario;
            }

            $dts_trab = $sqlDatos->datos_trabajador;
            $doc_iden = $sqlDatos->doc_identidad;
            $nro_docu = $sqlDatos->nro_doc_persona;
            $tip_trab = $sqlDatos->tipo_trabajador;
            $cat_trab = $sqlDatos->categ_trabajador;
            $nro_cusp = $sqlDatos->cuspp;
            $ids_pnsi = $sqlDatos->id_pension;
            if ($ids_trab == 101) {
                $fnd_pnsi = 'SPP';
            }else{
                $fnd_pnsi = $sqlDatos->pension;
            }
            
            $cnt_hijo = $sqlDatos->hijos;
            $sexo_per = $sqlDatos->sexo;
            $tare_per = $sqlDatos->marcaje;

            $rol_desc = $cargo;
            $sede_emp = $sede;
            $sld_trab = (float) $salario;
            $dsc_carg = $cargo;
            $ruc_empr = $ruc;
            $dts_empr = $empre;

            $ini_ctts = $ini_contrato;
            $fin_ctts = $fin_contrato;
            $mod_ctts = $modalid;
            $tip_ctts = $tpcontra;
            $tipo_cc = ($tp_cc == 1) ? 'FIJO': 'VARIABLE';
            $hora_semana = (float) $horas;

            //DATOS PENSIONES
            $sqlPension = DB::table('rrhh.rrhh_pensi')->where('id_pension', $ids_pnsi)->first();
            $psn_gral = $sqlPension->porcentaje_general;
            $psn_apor = $sqlPension->aporte;
            $psn_prim = $sqlPension->prima_seguro;
            $psn_comi = $sqlPension->comision;
            $psn_desc = ($sqlPension->descripcion == 'ONP') ? 1 : 2;

            // ASIGNACION FAMILIAR
            if ($plani == 1){
                if ($cnt_hijo == 1){
                    $sqlAsig = DB::table('rrhh.rrhh_asig_familiar')->where('estado', 1)->limit(1)->orderBy('rrhh_asig_familiar.id_asignacion', 'desc')->first();
                        $asg_fami = (float) $sqlAsig->valor;
                }else{
                    $asg_fami = 0;
                }
            }else{
                $asg_fami = 0;
            }

            //REINTEGROS
            $con_reint = 0;
            $sin_reint = 0;
            $dts_reint = ['reint_deduc' => $con_reint, 'reint_no_deduc' => $sin_reint];
            $reint_con = 0;
            $reint_sin = 0;
            $tot_reint = $reint_con + $reint_sin;

            // LICENCIA
            $dias_licenc = 0;
            $dias_telet = 0;

            //BONIFICACIONES
            $dts_bonif = [];
            $sld_dias = (float) ($sld_trab / 30);
            $ext_spcc = $dias_fer * $sld_dias;
            
            if ($ext_spcc > 0) {
                $con_bonif = $ext_spcc;
                $sin_bonif = 0;
                $dts_bonif[] = ['afecto'=> 'SI', 'concepto' => "HORAS EXTRAS", 'importe'=> $con_bonif];
            }else{
                $con_bonif = 0;
                $sin_bonif = 0;
                $dts_bonif = [];
            }
            $bonif_con = $con_bonif;
            $bonif_sin = $sin_bonif;
            $tot_bonif = $con_bonif + $sin_bonif;

            //DESCUENTOS
            $dts_dsct = [];
            $con_dsct = 0;
            $sin_dsct = 0;
            $dscto_con = $con_dsct;
            $dscto_sin = $sin_dsct;
            $tot_dsct = $dscto_con + $dscto_sin;

            //RETENCIONES
            $dts_reten = [];
            if ($q_retenc > 0) {
                $con_reten = $q_retenc;
                $sin_reten = 0;
                $dts_reten[] = ['concepto'=> '5TA CATEGORIA', 'importe'=> $con_reten];
            }else{
                $con_reten = 0;
                $sin_reten = 0;
                $dts_reten = [];
            }
            $reten_con = $con_reten;
            $reten_sin = $sin_reten;
            $tot_reten = $con_reten + $sin_reten;

            /* TARDANZAS */
            $hor_perm = 0;
            $min_tard = 0;
            $min_tard_time = 0;
            $min_tard_dsct = 0;
            $min_inas = 0;

            /* VACACIONES - TOTAL DIAS */
            $dias_vac = 0;
            $dias_max_cont = 12;
            $dias_contables = 12;

            $dias_trab = (int) ($dias_max_cont - $min_inas - $dias_vac - $dias_licenc - $dias_telet); //se agrego licencias
            $dias_lab = (int) $dias_total - $min_inas - $dias_vac - $dias_licenc - $dias_telet;
            $dias_laborados = (int) ($dias_max_cont - $min_inas - $dias_vac - $dias_licenc - $dias_telet);

            if ($dias_lab < 0){
                $dias_lab = 0;
                $min_fin_efe =  (float) 0;
                $horas_pre_efec = (float) 0;
                $horas_efectivas = (float) 0;
                $hor_efectivos = round(0);
                $min_efectivos = round(0);
            }else{
                $min_fin_efe =  (float) ($min_tard_time / 60);
                $horas_pre_efec = (float) ($dias_laborados * $hora_semana);
                $horas_efectivas = (float) ($horas_pre_efec - $min_fin_efe);
                $hor_efectivos = round(intval($horas_efectivas));
                $min_efectivos = round(floatval(($horas_efectivas - $hor_efectivos) * 60));
            }

            $sld_dias = (float) ($sld_trab / 30);
            $sld_hora = $sld_dias / 8;
            $sld_minu = $sld_hora / 60;

            /// TOTALES
            if ($plani == 1){
                $sueldo_vacac = ($dias_vac > 0) ? (($sld_trab / 30) * $dias_vac) : 0;
            }else{
                $sueldo_vacac = 0;
            }
            $sueldo_lice = ($dias_licenc > 0) ? (($sld_trab / 30) * $dias_licenc) : 0;
            $sueldo_tltb = ($dias_telet > 0) ? (($sld_trab / 30) * $dias_telet) : 0;
            $sueldo_basi = (($sld_trab / 30) * $dias_trab);
            $sueldo_asig = (($sld_trab / 30) * $dias_trab) + $asg_fami;
            $total_tndza = $sld_dias * $min_tard_dsct;
            $total_dscto = $dscto_con + $dscto_sin;
            $total_bonif = $bonif_con + $bonif_sin;
            $total_reint = $reint_con + $reint_sin;
            $tot_sueldo = ($sueldo_asig + ($bonif_con + $reint_con) - ($dscto_con + $total_tndza) + $sueldo_vacac + $sueldo_lice + $sueldo_tltb); // se agrego licencia

            if ($plani == 1){
                if ($psn_desc == 1){ //ONP
                    $tot_pensi = $tot_sueldo * ($psn_gral / 100);
                    $dts_pensi = array('tipo' => 'onp', 'snp' => $tot_pensi, 'spp' => 0, 'prima' => 0, 'comision' => 0, 'total_aportes' => $tot_pensi);
                }else{ //AFP
                    $limit_afp = 9788.95;

                    if ($mes == 4 and $anio == 2020) {
                        $total_obli = 0;
                        $total_prim = ($tot_sueldo >= $limit_afp) ? round($limit_afp * ($psn_prim / 100), 2) : round($tot_sueldo * ($psn_prim / 100), 2);
                        $total_comi = 0;
                    }else{
                        $total_obli = round($tot_sueldo * ($psn_apor / 100), 2);
                        $total_prim = ($tot_sueldo >= $limit_afp) ? round($limit_afp * ($psn_prim / 100), 2) : round($tot_sueldo * ($psn_prim / 100), 2);
                        $total_comi = round($tot_sueldo * ($psn_comi / 100), 2);
                    }

                    $tot_pensi = $total_obli + $total_prim + $total_comi;
                    $dts_pensi = array('tipo' => 'afp', 'snp' => 0, 'spp' => $total_obli, 'prima' => $total_prim, 'comision' => $total_comi, 'total_aportes' => $tot_pensi);
                }
            }elseif ($plani == 2){
                $tot_pensi = 0;
                $dts_pensi = array('tipo' => 'na', 'snp' => 0, 'spp' => 0, 'prima' => 0, 'comision' => 0, 'total_aportes' => 0);
            }

            //APORTES DEL EMPLEADOR
            $dts_apor = [];
            $tot_aport = 0;
            if ($plani == 1){
                $sqlAporta = DB::table('rrhh.rrhh_aport')->where('estado', '=', 1)->get();

                foreach ($sqlAporta as $keyAporta){
                    $apt_valor = $keyAporta->valor;
                    $apt_desc = $keyAporta->concepto;
                    if ($apt_desc == 'ESSALUD (REGULAR CBSSP AGRAR/AC) TRAB') {
                        if ($tot_sueldo <= 930){
                            $tot_aport = 83.70;
                        }else{
                            $tot_aport = $tot_sueldo * ($apt_valor / 100);
                        }
                    }else{
                        $tot_aport = $tot_sueldo * ($apt_valor / 100);
                    }
                    $dts_apor[] = ['concepto'=> $apt_desc, 'importe'=> $tot_aport];
                }

            }elseif ($plani == 2){
                $tot_aport = 0;
                $dts_apor[] = ['concepto'=> 'na', 'importe'=> 0];
            }

            $total_ingresos = $sueldo_asig + $total_reint + $total_bonif + $sueldo_vacac + $sueldo_lice + $sueldo_tltb;
            $pago_final = $sueldo_asig + $total_reint + $total_bonif + $sueldo_vacac + $sueldo_lice + $sueldo_tltb - $total_dscto - $tot_pensi - $total_tndza - $tot_reten;

            $data_prev[] = array(
                'id_empresa'            => $id_empre,
                'empresa'               => $dts_empr,
                'ruc'                   => $ruc_empr,
                'sede'                  => $sede_emp,
                'id_sede'               => $id_sede,
                'grupo'                 => $grup,
                'id_grupo'              => $id_grup,
                'area'                  => $area,
                'id_area'               => $id_area,
                'id_trabajador'         => $ids_trab,
                'datos_trabajador'      => $dts_trab,
                'tipo_documento'        => $doc_iden,
                'dni_trabajador'        => $nro_docu,
                'sexo_trabajador'       => $sexo_per,
                'fecha_contrato_ini'    => $ini_ctts,
                'fecha_contrato_fin'    => $fin_ctts,
                'modalidad_contrato'    => $mod_ctts,
                'tipo_contrato'         => $tip_ctts,
                'tipo_trabajador'       => $tip_trab,
                'categoria_trabajador'  => $cat_trab,
                'tipo_planilla'         => $plani,
                'rol_trabajador'        => $rol_desc,
                'id_cargo'              => $id_cargo,
                'numero_cussp'          => $nro_cusp,
                'tipo_centro_costo'     => $tipo_cc,
                'fondo_pension'         => $fnd_pnsi,
                'sctr'                  => $sctr,
                'salario'               => $sld_trab,
                'sueldo_basico'         => $sueldo_basi,
                'sueldo_dia'            => $sld_dias,
                'sueldo_hora'           => $sld_hora,
                'sueldo_minuto'         => $sld_minu,
                'sueldo_asigna'         => $sueldo_asig,
                'minutos_tardanza'      => $min_tard_time,
                'descuento_tardanza'    => $min_tard_dsct,
                'monto_tardanza'        => $total_tndza,
                'asignacion_familiar'   => $asg_fami,
                'horas_permisos'        => $hor_perm,
                'bonificaciones'        => $dts_bonif,
                'monto_bonif_con'       => $con_bonif,
                'monto_bonif_sin'       => $sin_bonif,
                'total_bonificacion'    => $tot_bonif,
                'descuentos'            => $dts_dsct,
                'monto_dscto_con'       => $con_dsct,
                'monto_dscto_sin'       => $sin_dsct,
                'total_dscto'           => $tot_dsct,
                'retenciones'           => $dts_reten,
                'monto_reten_con'       => $con_reten,
                'monto_reten_sin'       => $sin_reten,
                'total_reten'           => $tot_reten,
                'reintegros'            => $dts_reint,
                'monto_reintegro'       => $tot_reint,
                'dias_vacaciones'       => $dias_vac,
                'sueldo_vacaciones'     => $sueldo_vacac,
                'dias_licencia'         => $dias_licenc,
                'sueldo_licencia'       => $sueldo_lice,
                'dias_teletrabajo'      => $dias_telet,
                'sueldo_teletrabajo'    => $sueldo_tltb,
                'aporte_pension'        => $dts_pensi,
                'aporte_empleador'      => $dts_apor,
                'total_remun'           => $tot_sueldo,
                'total_ingreso'         => $total_ingresos,
                'total_pago'            => $pago_final,
                'dias_mes'              => $total_dias,
                'dias_compu'            => $dias_compu,
                'regimen'               => $hora_trab,
                'hora_regimen'          => $hora_semana,
                'dias_total'            => $dias_lab,
                'dias_laborados'        => $dias_laborados,
                'total_regimen'         => $horas_pre_efec,
                'total_efectivas'       => $horas_efectivas,
                'horas_efectivas'       => $hor_efectivos,
                'minutos_efectivas'     => $min_efectivos,
                'valor_final'           => $dias_contables
            );
        }
        $data = ['data' =>$data_prev];
        return $data;            
    }
    
    public function cargar_remuneraciones($emp, $plani, $mes, $anio, $type, $empleado, $areaGRupal){
        $dmY_primer = $this->primerDia($mes, $anio);
        $dmY_ultimo = $this->ultimoDia($mes, $anio);
        $data_prev = array();
        $data = array();
        $mes_ant = $mes - 1;
        $dia_ant = '26-'.$mes_ant.'-'.$anio;
        $dia_fin = '25-'.$mes.'-'.$anio;

        $dias_compu = 30;
        $total_dias = cal_days_in_month(1, $mes, $anio);
        $hora_trab = 8;
        $dias_total = $this->dias_total($mes, $anio);

        if ($empleado > 0) {
            $list_trab = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                    ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->select(DB::raw('DISTINCT rrhh_trab.id_trabajador'), 'rrhh_perso.apellido_paterno')
                    ->where([['rrhh_trab.id_trabajador', '=' ,$empleado], ['adm_empresa.id_empresa', '=', $emp], ['rrhh_rol.id_tipo_planilla', '=', $plani],
                            ['rrhh_rol.salario', '>', 0], ['rrhh_rol.estado', '=', 1]])
                    ->get();
        }else{
            if ($type == 1){
                $list_trab = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                    ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->select(DB::raw('DISTINCT rrhh_trab.id_trabajador'), 'rrhh_perso.apellido_paterno')
                    ->where([['rrhh_trab.estado', '=', 1], ['adm_empresa.id_empresa', '=', $emp], ['rrhh_rol.id_tipo_planilla', '=', $plani],
                            ['rrhh_rol.salario', '>', 0], ['rrhh_rol.estado', '=', 1]])
                    ->orderBy('rrhh_perso.apellido_paterno', 'asc')->get();
            }elseif($type == 2){
                $list_trab = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                    ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->select(DB::raw('DISTINCT rrhh_trab.id_trabajador'), 'rrhh_perso.apellido_paterno')
                    ->where([['rrhh_trab.estado', '=', 1], ['rrhh_rol.salario', '>', 0], ['rrhh_rol.id_tipo_planilla', '=', $plani], ['rrhh_rol.estado', '=', 1]])
                    ->orderBy('rrhh_trab.id_trabajador', 'asc')->get();
            }elseif($type == 3){
                $list_trab = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                    ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->select(DB::raw('DISTINCT rrhh_trab.id_trabajador'), 'rrhh_perso.apellido_paterno')
                    ->where([
                        ['rrhh_trab.estado', '=', 1], ['adm_empresa.id_empresa', '=', $emp], ['rrhh_rol.id_tipo_planilla', '=', $plani],
                        ['rrhh_rol.salario', '>', 0], ['rrhh_rol.estado', '=', 1], [DB::raw('upper(adm_grupo.descripcion)'), '=', $areaGRupal]
                    ])->orderBy('rrhh_perso.apellido_paterno', 'asc')->get();
            }
        }

        foreach ($list_trab as $row){
            $ids_trab = $row->id_trabajador;

            // DATOS TRABAJADOR
            $sqlDatos = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', 'rrhh_trab.id_tipo_trabajador')
                ->join('rrhh.rrhh_cat_ocupac', 'rrhh_cat_ocupac.id_categoria_ocupacional', 'rrhh_trab.id_categoria_ocupacional')
                ->join('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'rrhh_perso.id_documento_identidad')
                ->join('rrhh.rrhh_pensi', 'rrhh_pensi.id_pension', 'rrhh_trab.id_pension')
                ->select('rrhh_perso.nro_documento AS nro_doc_persona', 'rrhh_perso.sexo', 'sis_identi.descripcion AS doc_identidad', 'rrhh_trab.cuspp', 'rrhh_trab.hijos', 'rrhh_trab.marcaje',
                        'rrhh_pensi.descripcion AS pension', 'rrhh_pensi.id_pension', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_cat_ocupac.descripcion AS categ_trabajador',
                        DB::raw("CONCAT(rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno,' ',rrhh_perso.nombres) AS datos_trabajador"))
                ->where('rrhh_trab.id_trabajador', '=', $ids_trab)->first();

            // ROL DEL TRABAJADOR
            $sqlRol = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
                ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->select('rrhh_cargo.descripcion AS cargo', 'adm_area.descripcion AS area', 'sis_sede.descripcion AS sede',
                'adm_grupo.id_grupo', 'adm_grupo.descripcion AS grupo', 'rrhh_rol.salario', 'adm_contri.nro_documento AS ruc', 'adm_contri.razon_social AS empresa',
                'adm_empresa.id_empresa', 'sis_sede.id_sede', 'rrhh_rol.id_area', 'rrhh_rol.id_cargo', 'rrhh_rol.fecha_inicio', 'rrhh_rol.fecha_fin', 'rrhh_rol.sctr')
                ->where([['rrhh_trab.id_trabajador', '=', $ids_trab], ['rrhh_rol.salario', '>', 0], ['rrhh_rol.id_tipo_planilla', '=', $plani]])
                ->orderBy('rrhh_rol.id_rol', 'desc')->limit(1)->get();

            if ($sqlRol->count() > 0){
                $data = $sqlRol->first();
                $ruc = $data->ruc;
                $empre = $data->empresa;
                $id_empre = $data->id_empresa;
                $cargo = strtoupper($data->cargo);
                $id_cargo = $data->id_cargo;
                $sede = strtoupper($data->sede);
                $id_sede = $data->id_sede;
                $area = $data->area;
                $id_area = $data->id_area;
                $grup = strtoupper($data->grupo);
                $id_grup = $data->id_grupo;
                $salario = $data->salario;
                $ini_contrato = $data->fecha_inicio;
                $fin_contrato = ($data->fecha_fin != null) ? $data->fecha_fin : null;
                $sctr = ($data->sctr == true) ? 1 : 0;
            }else{
                $ruc = '';
                $empre = '';
                $id_empre = 0;
                $sede = '';
                $id_sede = 0;
                $grup = '';
                $id_grup = 0;
                $area = '';
                $id_area = 0;
                $cargo = '';
                $id_cargo = 0;
                $salario = 0;
                $ini_contrato = '';
                $fin_contrato = '';
                $sctr = 0;
            }

            // CONTRATO DEL TRABAJADOR
            $sqlContrato = DB::table('rrhh.rrhh_contra')
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_contra.id_trabajador')
                ->join('rrhh.rrhh_tp_contra', 'rrhh_tp_contra.id_tipo_contrato', '=', 'rrhh_contra.id_tipo_contrato')
                ->join('rrhh.rrhh_modali', 'rrhh_modali.id_modalidad', '=', 'rrhh_contra.id_modalidad')
                ->join('rrhh.rrhh_horario', 'rrhh_horario.id_horario', '=', 'rrhh_contra.id_horario')
                ->select('rrhh_modali.descripcion AS modalidad', 'rrhh_tp_contra.descripcion AS tipo_contrato', 'rrhh_contra.tipo_centro_costo',
                        'rrhh_contra.fecha_inicio AS contra_inicio', 'rrhh_contra.fecha_fin AS contra_fin' , 'rrhh_contra.motivo', 
                        'rrhh_horario.id_horario', 'rrhh_horario.hora_sem')
                ->where([['rrhh_trab.id_trabajador', '=', $ids_trab], ['rrhh_contra.estado', '>', 0]])
                ->limit(1)->orderBy('rrhh_contra.id_contrato', 'desc')->get();
            
            if ($sqlContrato->count() > 0){
                $ctts = $sqlContrato->first();
                // $ini_contrato = date('d/m/Y', strtotime($ctts->contra_inicio));
                // $fin_contrato = date('d/m/Y', strtotime($ctts->contra_fin));
                $modalid = $ctts->modalidad;
                $tpcontra = $ctts->tipo_contrato;
                $tp_cc = $ctts->tipo_centro_costo;
                $horas = $ctts->hora_sem;
                $tp_horario = $ctts->id_horario;
            }

            $dts_trab = $sqlDatos->datos_trabajador;
            $doc_iden = $sqlDatos->doc_identidad;
            $nro_docu = $sqlDatos->nro_doc_persona;
            $tip_trab = $sqlDatos->tipo_trabajador;
            $cat_trab = $sqlDatos->categ_trabajador;
            $nro_cusp = $sqlDatos->cuspp;
            $ids_pnsi = $sqlDatos->id_pension;
            if ($ids_trab == 101) {
                $fnd_pnsi = 'SPP';
            }else{
                $fnd_pnsi = $sqlDatos->pension;
            }
            
            $cnt_hijo = $sqlDatos->hijos;
            $sexo_per = $sqlDatos->sexo;
            $tare_per = $sqlDatos->marcaje;

            $rol_desc = $cargo;
            $sede_emp = $sede;
            $sld_trab = (float) $salario;
            $dsc_carg = $cargo;
            $ruc_empr = $ruc;
            $dts_empr = $empre;

            $ini_ctts = $ini_contrato;
            $fin_ctts = $fin_contrato;
            $mod_ctts = $modalid;
            $tip_ctts = $tpcontra;
            $tipo_cc = ($tp_cc == 1) ? 'FIJO': 'VARIABLE';
            $hora_semana = (float) $horas;

            //DATOS PENSIONES
            $sqlPension = DB::table('rrhh.rrhh_pensi')->where('id_pension', $ids_pnsi)->first();
            $psn_gral = $sqlPension->porcentaje_general;
            $psn_apor = $sqlPension->aporte;
            $psn_prim = $sqlPension->prima_seguro;
            $psn_comi = $sqlPension->comision;
            $psn_desc = ($sqlPension->descripcion == 'ONP') ? 1 : 2;

            // ASIGNACION FAMILIAR // HORAS EXTRAS
            if ($plani == 1){
                if ($cnt_hijo == 1){
                    $sqlAsig = DB::table('rrhh.rrhh_asig_familiar')->where('estado', 1)->limit(1)->orderBy('rrhh_asig_familiar.id_asignacion', 'desc')->first();
                        $asg_fami = (float) $sqlAsig->valor;
                }else{
                    $asg_fami = 0;
                }

                $sqlHExt = DB::table('rrhh.rrhh_hrs_extra')->select('total_horas', 'fecha_hora_extra')
                ->where([['id_trabajador', $ids_trab], ['estado', 1]])
                ->whereBetween('fecha_hora_extra', [$dmY_primer, $dmY_ultimo])->get();
                if ($sqlHExt->count() > 0){
                    foreach ($sqlHExt as $keyhe) {
                        $total_he = $keyhe->total_horas;
                        $fecha_he = $keyhe->fecha_hora_extra;
                        $diaHE = $this->filtrar_dia($fecha_he);

                        if ($diaHE != 0) {
                            if ($total_he > 2){
                                $h25 = 2;
                                $h35 = ($total_he - 2);
                                $h100 = 0;
                            }else{
                                $h25 = $total_he;
                                $h35 = 0;
                                $h100 = 0;
                            }
                        }else{
                            $h25 = 0;
                            $h35 = 0;
                            $h100 = $total_he;
                        }
                    }
                }else{
                    $h25 = 0;
                    $h35 = 0;
                    $h100 = 0;
                }
            }else{
                $asg_fami = 0;
                $h25 = 0;
                $h35 = 0;
                $h100 = 0;
            }

            // VACACIONES
            if ($plani == 1) {
                $sqlVacac = DB::table('rrhh.rrhh_vacac')->where([['id_trabajador', $ids_trab], ['estado', 1]])
                ->whereBetween('fecha_inicio', [$dmY_primer, $dmY_ultimo])->get();
    
                if ($sqlVacac->count() > 0){
                    foreach ($sqlVacac as $keyVac){
                        $dias_vac = $keyVac->dias;
                    }
                }else{
                    $dias_vac = 0;
                }
            }else{
                $dias_vac = 0;
            }

            // LICENCIA
            $sqlLicenc = DB::table('rrhh.rrhh_licenc')->where([['id_trabajador', $ids_trab], ['estado', 1]])
            ->whereBetween('fecha_inicio', [$dmY_primer, $dmY_ultimo])->get();

            if ($sqlLicenc->count() > 0){
                $dias_licenc = 0;
                $dias_telet = 0;
                foreach ($sqlLicenc as $keyLicen){
                    if ($keyLicen->id_tipo_licencia == 1){
                        $dias_licenc += $keyLicen->dias;
                    }elseif ($keyLicen->id_tipo_licencia == 2){
                        $dias_telet += $keyLicen->dias;
                    }
                }
            }else{
                $dias_licenc = 0;
                $dias_telet = 0;
            }

            //REINTEGROS
            $con_reint = 0;
            $sin_reint = 0;

            $sqlReint = DB::table('rrhh.rrhh_reintegro')->select(DB::raw("SUM(importe) AS importe"), 'afecto')
            ->where([['id_trabajador', $ids_trab], ['estado', 1]])
            ->whereBetween('fecha', [$dmY_primer, $dmY_ultimo])
            ->groupBy('afecto')->get();
            
            if ($sqlReint->count() > 0){
                foreach ($sqlReint as $keyreint){
                    $imp_reint = $keyreint->importe;
                    $afc_reint = $keyreint->afecto;

                    if ($afc_reint == 'SI'){
                        $con_reint += $imp_reint;
                    }else{
                        $sin_reint += $imp_reint;
                    }
                }
                $reint_con = $con_reint;
                $reint_sin = $sin_reint;
                $dts_reint = ['reint_deduc' => $con_reint, 'reint_no_deduc' => $sin_reint];
                $tot_reint = $reint_con + $reint_sin;
            }else{
                $reint_con = 0;
                $reint_sin = 0;
                $dts_reint = ['reint_deduc' => $con_reint, 'reint_no_deduc' => $sin_reint];
                $tot_reint = $reint_con + $reint_sin;
            }

            //BONIFICACIONES
            $con_bonif = 0;
            $sin_bonif = 0;
            $dts_bonif = [];

            $sqlBonif = DB::table('rrhh.rrhh_bonif')->select(DB::raw("SUM(rrhh_bonif.importe) AS importe"), 'rrhh_bonif.afecto', 'rrhh_var_bonif.descripcion AS concepto')
                ->join('rrhh.rrhh_var_bonif', 'rrhh_var_bonif.id_variable_bonificacion', 'rrhh_bonif.id_variable_bonificacion')
                ->where([['rrhh_bonif.id_trabajador', $ids_trab], ['rrhh_bonif.id_tipo_planilla', $plani], ['rrhh_bonif.estado', 1]])
                ->whereBetween('rrhh_bonif.fecha_bonificacion', [$dmY_primer, $dmY_ultimo])
                ->groupBy('rrhh_bonif.id_variable_bonificacion', 'rrhh_bonif.afecto', 'rrhh_var_bonif.descripcion')->get();
            
            if ($sqlBonif->count() > 0){
                foreach ($sqlBonif as $keybonif){
                    $imp_bonif = $keybonif->importe;
                    $afc_bonif = $keybonif->afecto;
                    $cnc_bonif = $keybonif->concepto;

                    $dts_bonif[] = ['afecto'=>$afc_bonif, 'concepto'=> $cnc_bonif, 'importe'=> $imp_bonif];
                    
                    if ($afc_bonif == 'SI'){
                        $con_bonif += $imp_bonif;
                    }else{
                        $sin_bonif += $imp_bonif;
                    }
                }
                $bonif_con = $con_bonif;
                $bonif_sin = $sin_bonif;
                $tot_bonif = $con_bonif + $sin_bonif;
            }else{
                $bonif_con = 0;
                $bonif_sin = 0;
                $tot_bonif = $bonif_con + $bonif_sin;
            }

            //DESCUENTOS
            $con_dsct = 0;
            $sin_dsct = 0;
            $dts_dsct = [];

            $sqlDescu = DB::table('rrhh.rrhh_dscto')->select(DB::raw("SUM(rrhh_dscto.importe) AS importe"), 'rrhh_dscto.afecto', 'rrhh_var_dscto.descripcion AS concepto')
                ->join('rrhh.rrhh_var_dscto', 'rrhh_var_dscto.id_variable_descuento', 'rrhh_dscto.id_variable_descuento')
                ->where([['rrhh_dscto.id_trabajador', $ids_trab], ['rrhh_dscto.id_tipo_planilla', $plani], ['rrhh_dscto.estado', 1]])
                ->whereBetween('rrhh_dscto.fecha_descuento', [$dmY_primer, $dmY_ultimo])
                ->groupBy('rrhh_dscto.id_variable_descuento', 'rrhh_dscto.afecto', 'rrhh_var_dscto.descripcion')->get();
            
            if ($sqlDescu->count() > 0){
                foreach ($sqlDescu as $keydscto){
                    $imp_dsct = (float) $keydscto->importe;
                    $afc_dscto = $keydscto->afecto;
                    $cnc_dsct = $keydscto->concepto;

                    $dts_dsct[] = ['afecto'=>$afc_dscto, 'concepto'=> $cnc_dsct, 'importe'=> $imp_dsct];
                    
                    if ($afc_dscto == 'SI'){
                        $con_dsct += $imp_dsct;
                    }else{
                        $sin_dsct += $imp_dsct;
                    }
                }
                $dscto_con = $con_dsct;
                $dscto_sin = $sin_dsct;
                $tot_dsct = $dscto_con + $dscto_sin;
            }else{
                $dscto_con = 0;
                $dscto_sin = 0;
                $tot_dsct = $dscto_con + $dscto_sin;
            }

            //RETENCIONES
            $con_reten = 0;
            $sin_reten = 0;
            $dts_reten = [];

            $sqlReten = DB::table('rrhh.rrhh_retencion')->select(DB::raw("SUM(rrhh_retencion.importe) AS importe"), 'rrhh_retencion.afecto', 'rrhh_var_reten.descripcion AS concepto')
                ->join('rrhh.rrhh_var_reten', 'rrhh_var_reten.id_variable_retencion', 'rrhh_retencion.id_variable_retencion')
                ->where([['rrhh_retencion.id_trabajador', $ids_trab], ['rrhh_retencion.estado', 1]])
                ->whereBetween('rrhh_retencion.fecha_retencion', [$dmY_primer, $dmY_ultimo])
                ->groupBy('rrhh_retencion.id_variable_retencion', 'rrhh_retencion.afecto', 'rrhh_var_reten.descripcion')->get();
            
            if ($sqlReten->count() > 0){
                foreach ($sqlReten as $keyreten){
                    $imp_reten = $keyreten->importe;
                    $afc_reten = $keyreten->afecto;
                    $cnc_reten = $keyreten->concepto;

                    if ($plani == 1) {
                        if ($cnc_reten != '4TA CATEGORIA') {
                            $dts_reten[] = ['concepto'=> $cnc_reten, 'importe'=> $imp_reten];
                            if ($afc_reten == 'SI'){
                                $con_reten += $imp_reten;
                            }else{
                                $sin_reten += $imp_reten;
                            }
                        }
                    }else{
                        if ($cnc_reten != '5TA CATEGORIA') {
                            $dts_reten[] = ['concepto'=> $cnc_reten, 'importe'=> $imp_reten];
                            if ($afc_reten == 'SI'){
                                $con_reten += $imp_reten;
                            }else{
                                $sin_reten += $imp_reten;
                            }
                        }
                    }
                }
                $reten_con = $con_reten;
                $reten_sin = $sin_reten;
                $tot_reten = $con_reten + $sin_reten;
            }else{
                $reten_con = 0;
                $reten_sin = 0;
                $tot_reten = $reten_con + $reten_sin;
            }

            $hor_perm = 0;
            $min_tard = 0;

            /* TARDANZAS */
            $tard_hour = DB::table('rrhh.rrhh_tareo')
                ->where([['id_empresa', $emp], ['id_tipo_planilla', $plani], ['mes', $mes], ['anio', $anio], ['id_trabajador', $ids_trab]])
                ->get();

            if ($tare_per > 0){
                if ($tard_hour->count() > 0){
                    $hr_control = $tard_hour->first();
                    $min_tard_time = (float) $hr_control->tardanza;
                    $min_tard_dsct = (int) $hr_control->descuento;
                    $min_inas = (int) $hr_control->inasistencia;
                }else{
                    $min_tard_time = 0;
                    $min_tard_dsct = 0;
                    $min_inas = 0;
                }
            }else{
                $min_tard_time = 0;
                $min_tard_dsct = 0;
                $min_inas = 0;
            }

            // $dias_licenc = 0;
            // $dias_telet = 0;

            $fi_cont = date('Y-m-d', strtotime($ini_ctts));
            $ff_cont = ($fin_ctts != null) ? date('Y-m-d', strtotime($fin_ctts)) : null;
            $dias_contables = $this->calcularDiasEfectivos($mes, $anio, $fi_cont, $ff_cont);
            if ($dias_contables < $dias_total) {
                $dias_max_cont = (30 - ($dias_total - $dias_contables));
            }else{
                $dias_max_cont = 30;
            }
            // $dias_max_cont = ($dias_contables >= $dias_total) ? 30 : $dias_contables;
            $dias_trab = (int) ($dias_max_cont - $min_inas - $dias_vac - $dias_licenc - $dias_telet); //se agrego licencias
            $dias_lab = (int) $dias_total - $min_inas - $dias_vac - $dias_licenc - $dias_telet;
            $dias_laborados = (int) ($dias_max_cont - $min_inas - $dias_vac - $dias_licenc - $dias_telet);

            if ($dias_lab < 0){
                $dias_lab = 0;
                $min_fin_efe =  (float) 0;
                $horas_pre_efec = (float) 0;
                $horas_efectivas = (float) 0;
                $hor_efectivos = round(0);
                $min_efectivos = round(0);
            }else{
                $min_fin_efe =  (float) ($min_tard_time / 60);
                $horas_pre_efec = (float) ($dias_laborados * $hora_semana);
                $horas_efectivas = (float) ($horas_pre_efec - $min_fin_efe);
                $hor_efectivos = round(intval($horas_efectivas));
                $min_efectivos = round(floatval(($horas_efectivas - $hor_efectivos) * 60));
            }

            $sld_dias = (float) ($sld_trab / 30);
            $sld_hora = $sld_dias / 8;
            $sld_minu = $sld_hora / 60;

            /// TOTALES
            if ($plani == 1){
                $sueldo_vacac = ($dias_vac > 0) ? (($sld_trab / 30) * $dias_vac) : 0;
            }else{
                $sueldo_vacac = 0;
            }
            $sueldo_lice = ($dias_licenc > 0) ? (($sld_trab / 30) * $dias_licenc) : 0;
            $sueldo_tltb = ($dias_telet > 0) ? (($sld_trab / 30) * $dias_telet) : 0;
            $sueldo_basi = (($sld_trab / 30) * $dias_trab);
            $sueldo_asig = (($sld_trab / 30) * $dias_trab) + $asg_fami;
            $total_tndza = $sld_dias * $min_tard_dsct;
            $total_dscto = $dscto_con + $dscto_sin;
            $total_bonif = $bonif_con + $bonif_sin;
            $total_reint = $reint_con + $reint_sin;
            $tot_sueldo = ($sueldo_asig + ($bonif_con + $reint_con) - ($dscto_con + $total_tndza) + $sueldo_vacac + $sueldo_lice + $sueldo_tltb); // se agrego licencia

            if ($plani == 1){
                if ($psn_desc == 1){ //ONP
                    $tot_pensi = $tot_sueldo * ($psn_gral / 100);
                    $dts_pensi = array('tipo' => 'onp', 'snp' => $tot_pensi, 'spp' => 0, 'prima' => 0, 'comision' => 0, 'total_aportes' => $tot_pensi);
                }else{ //AFP
                    $limit_afp = 9788.95;

                    if ($mes == 4 and $anio == 2020) {
                        $total_obli = 0;
                        $total_prim = ($tot_sueldo >= $limit_afp) ? round($limit_afp * ($psn_prim / 100), 2) : round($tot_sueldo * ($psn_prim / 100), 2);
                        $total_comi = 0;
                    }else{
                        $total_obli = round($tot_sueldo * ($psn_apor / 100), 2);
                        $total_prim = ($tot_sueldo >= $limit_afp) ? round($limit_afp * ($psn_prim / 100), 2) : round($tot_sueldo * ($psn_prim / 100), 2);
                        $total_comi = round($tot_sueldo * ($psn_comi / 100), 2);
                    }

                    $tot_pensi = $total_obli + $total_prim + $total_comi;
                    $dts_pensi = array('tipo' => 'afp', 'snp' => 0, 'spp' => $total_obli, 'prima' => $total_prim, 'comision' => $total_comi, 'total_aportes' => $tot_pensi);
                }
            }elseif ($plani == 2){
                $tot_pensi = 0;
                $dts_pensi = array('tipo' => 'na', 'snp' => 0, 'spp' => 0, 'prima' => 0, 'comision' => 0, 'total_aportes' => 0);
            }

            //APORTES DEL EMPLEADOR
            $dts_apor = [];
            $tot_aport = 0;
            if ($plani == 1){
                $sqlAporta = DB::table('rrhh.rrhh_aport')->where('estado', '=', 1)->get();

                foreach ($sqlAporta as $keyAporta){
                    $apt_valor = $keyAporta->valor;
                    $apt_desc = $keyAporta->concepto;
                    if ($apt_desc == 'ESSALUD (REGULAR CBSSP AGRAR/AC) TRAB') {
                        if ($tot_sueldo <= 930){
                            $tot_aport = 83.70;
                        }else{
                            $tot_aport = $tot_sueldo * ($apt_valor / 100);
                        }
                    }else{
                        $tot_aport = $tot_sueldo * ($apt_valor / 100);
                    }
                    $dts_apor[] = ['concepto'=> $apt_desc, 'importe'=> $tot_aport];
                }

            }elseif ($plani == 2){
                $tot_aport = 0;
                $dts_apor[] = ['concepto'=> 'na', 'importe'=> 0];
            }

            $total_ingresos = $sueldo_asig + $total_reint + $total_bonif + $sueldo_vacac + $sueldo_lice + $sueldo_tltb;
            $pago_final = $sueldo_asig + $total_reint + $total_bonif + $sueldo_vacac + $sueldo_lice + $sueldo_tltb - $total_dscto - $tot_pensi - $total_tndza - $tot_reten;

            $data_prev[] = array(
                'id_empresa'            => $id_empre,
                'empresa'               => $dts_empr,
                'ruc'                   => $ruc_empr,
                'sede'                  => $sede_emp,
                'id_sede'               => $id_sede,
                'grupo'                 => $grup,
                'id_grupo'              => $id_grup,
                'area'                  => $area,
                'id_area'               => $id_area,
                'id_trabajador'         => $ids_trab,
                'datos_trabajador'      => $dts_trab,
                'tipo_documento'        => $doc_iden,
                'dni_trabajador'        => $nro_docu,
                'sexo_trabajador'       => $sexo_per,
                'fecha_contrato_ini'    => $ini_ctts,
                'fecha_contrato_fin'    => $fin_ctts,
                'modalidad_contrato'    => $mod_ctts,
                'tipo_contrato'         => $tip_ctts,
                'tipo_trabajador'       => $tip_trab,
                'categoria_trabajador'  => $cat_trab,
                'tipo_planilla'         => $plani,
                'rol_trabajador'        => $rol_desc,
                'id_cargo'              => $id_cargo,
                'numero_cussp'          => $nro_cusp,
                'tipo_centro_costo'     => $tipo_cc,
                'fondo_pension'         => $fnd_pnsi,
                'sctr'                  => $sctr,
                'salario'               => $sld_trab,
                'sueldo_basico'         => $sueldo_basi,
                'sueldo_dia'            => $sld_dias,
                'sueldo_hora'           => $sld_hora,
                'sueldo_minuto'         => $sld_minu,
                'sueldo_asigna'         => $sueldo_asig,
                'minutos_tardanza'      => $min_tard_time,
                'descuento_tardanza'    => $min_tard_dsct,
                'monto_tardanza'        => $total_tndza,
                'asignacion_familiar'   => $asg_fami,
                'horas_permisos'        => $hor_perm,
                'bonificaciones'        => $dts_bonif,
                'monto_bonif_con'       => $con_bonif,
                'monto_bonif_sin'       => $sin_bonif,
                'total_bonificacion'    => $tot_bonif,
                'descuentos'            => $dts_dsct,
                'monto_dscto_con'       => $con_dsct,
                'monto_dscto_sin'       => $sin_dsct,
                'total_dscto'           => $tot_dsct,
                'retenciones'           => $dts_reten,
                'monto_reten_con'       => $con_reten,
                'monto_reten_sin'       => $sin_reten,
                'total_reten'           => $tot_reten,
                'reintegros'            => $dts_reint,
                'monto_reintegro'       => $tot_reint,
                'dias_vacaciones'       => $dias_vac,
                'sueldo_vacaciones'     => $sueldo_vacac,
                'dias_licencia'         => $dias_licenc,
                'sueldo_licencia'       => $sueldo_lice,
                'dias_teletrabajo'      => $dias_telet,
                'sueldo_teletrabajo'    => $sueldo_tltb,
                'aporte_pension'        => $dts_pensi,
                'aporte_empleador'      => $dts_apor,
                'total_remun'           => $tot_sueldo,
                'total_ingreso'         => $total_ingresos,
                'total_pago'            => $pago_final,
                'dias_mes'              => $total_dias,
                'dias_compu'            => $dias_compu,
                'regimen'               => $hora_trab,
                'hora_regimen'          => $hora_semana,
                'dias_total'            => $dias_lab,
                'dias_laborados'        => $dias_laborados,
                'total_regimen'         => $horas_pre_efec,
                'total_efectivas'       => $horas_efectivas,
                'horas_efectivas'       => $hor_efectivos,
                'minutos_efectivas'     => $min_efectivos,
                'valor_final'           => $dias_contables
            );
        }
        $data = ['data' =>$data_prev];
        return $data;
    }

    public function generar_planilla_pdf($emp, $plani, $mes, $anio){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->exportPlanilla($emp, $plani, $mes, $anio))->setPaper('a4', 'portrait');
        return $pdf->stream('planilla.pdf');
    }

    public function generar_planilla_spcc_pdf($emp, $plani, $mes, $anio){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->exportPlanillaSPCC($emp, $plani, $mes, $anio))->setPaper('a4', 'portrait');
        return $pdf->stream('planilla_spcc.pdf');
    }

    public function procesar_planilla($emp, $plani, $mes){
        $anio = date('Y');
        
        $insert = DB::table('rrhh.rrhh_planilla')->insertGetId(['id_empresa' => $emp, 'id_tipo_planilla' => $plani, 'mes' => $mes, 'año' => $anio, 'estado' => 1],'id_planilla_pago');
        $user = $id_usuario = Auth::user()->id_usuario;

        if ($insert > 0){
            $data = $this->cargar_remuneraciones($emp, $plani, $mes, $anio, 1, 0, 0);
            $myData = $data['data'];
            $cont = sizeof($myData);
    
            for ($i = 0; $i < $cont ; $i++){
                $empr_id = $myData[$i]['id_empresa'];
                $sede_id = $myData[$i]['id_sede'];
                $area_id = $myData[$i]['id_area'];
                $trab_id = $myData[$i]['id_trabajador'];
                $dias_tot = $myData[$i]['dias_total'];
                $dias_lab = $myData[$i]['dias_laborados'];
                $efec_hor = $myData[$i]['horas_efectivas'];
                $efec_min = $myData[$i]['minutos_efectivas'];
                $asig_fam = $myData[$i]['asignacion_familiar'];
                $remu_bas = $myData[$i]['sueldo_basico'];
                $remu_asg = $myData[$i]['sueldo_asigna'];
                $mont_tard = $myData[$i]['monto_tardanza'];
                $mont_bonc = $myData[$i]['monto_bonif_con'];
                $mont_bons = $myData[$i]['monto_bonif_sin'];
                $mont_dctc = $myData[$i]['monto_dscto_con'];
                $mont_dcts = $myData[$i]['monto_dscto_sin'];
                $mont_retc = $myData[$i]['monto_reten_con'];
                $mont_rets = $myData[$i]['monto_reten_sin'];
                $mont_reic = $myData[$i]['reintegros']['reint_deduc'];
                $mont_reis = $myData[$i]['reintegros']['reint_no_deduc'];
                $myPensi = $myData[$i]['aporte_pension'];
                $tpPensi = $myPensi['tipo'];
                if ($tpPensi == 'onp') {
                    $pnsi_prct = $myPensi['snp'];
                    $pnsi_prim = 0;
                    $pnsi_comi = 0;
                    $pnsi_tota = $myPensi['total_aportes'];
                }else{
                    $pnsi_prct = $myPensi['spp'];
                    $pnsi_prim = $myPensi['prima'];
                    $pnsi_comi = $myPensi['comision'];
                    $pnsi_tota = $myPensi['total_aportes'];
                }
                $myAport = $myData[$i]['aporte_empleador'];
                $countAport = sizeof($myAport);
                $essalud = 0;
                for ($apt = 0; $apt < $countAport; $apt++){ 
                    if ($myAport[$apt]['concepto'] == 'ESSALUD (REGULAR CBSSP AGRAR/AC) TRAB'){
                        $essalud = $myAport[$apt]['importe'];
                    }
                }
                $total_ing = $myData[$i]['total_ingreso'];
                $total_rem = $myData[$i]['total_remun'];
                $total_pag = $myData[$i]['total_pago'];

                $detail = DB::table('rrhh.rrhh_planilla_det')->insertGetId(
                    [
                        'id_planilla_pago'		=> $insert,
                        'id_trabajador'			=> $trab_id,
                        'id_empresa'			=> $empr_id,
                        'id_sede'				=> $sede_id,
                        'id_area'				=> $area_id,
                        'dias_total'			=> $dias_tot,
                        'dias_lab'				=> $dias_lab,
                        'dias_cts'				=> null,
                        'dias_gratif'			=> null,
                        'dias_subsi'			=> null,
                        'horas_efect'			=> $efec_hor,
                        'minutos_efect'			=> $efec_min,
                        'monto_he25'			=> null,
                        'monto_he35'			=> null,
                        'monto_he100'			=> null,
                        'asig_familiar'			=> $asig_fam,
                        'sueldo_basico'			=> $remu_bas,
                        'sueldo_asigna'			=> $remu_asg,
                        'monto_tardanza'		=> $mont_tard,
                        'monto_bonif_con'		=> $mont_bonc,
                        'monto_bonif_sin'		=> $mont_bons,
                        'monto_dscto_con'		=> $mont_dctc,
                        'monto_dscto_sin'		=> $mont_dcts,
                        'monto_reten_con'		=> $mont_retc,
                        'monto_reten_sin'		=> $mont_rets,
                        'monto_reint_con'		=> $mont_reic,
                        'monto_reint_sin'		=> $mont_reis,
                        'pensi_porcent'			=> $pnsi_prct,
                        'pensi_comision'		=> $pnsi_comi,
                        'pensi_prima'			=> $pnsi_prim,
                        'pensi_total'			=> $pnsi_tota,
                        'monto_seguro'			=> $essalud,
                        'monto_subsi_mater'		=> null,
                        'monto_subsi_incap'		=> null,
                        'total_ingreso'			=> $total_ing,
                        'total_rem_aseg'		=> $total_rem,
                        'total_pago'			=> $total_pag,
                        'gratif_sexto'			=> null,
                        'monto_gratif'			=> null,
                        'monto_gratif_ley'		=> null,
                        'monto_gratif_adela'	=> null,
                        'total_gratif'			=> null,
                        'cts_periodo'			=> null,
                        'monto_cts'				=> null,
                        'monto_devol_quinta'	=> null,
                        'estado'				=> 1,
                        'id_usuario'			=> $user
                    ],
                    'id_planilla_detalle'
                );
            }
            $value = 1;
        }else{
            $value = 0;
        }
        return response()->json($value);
    }

    public function exportPlanilla($emp, $planiPla, $mesPla, $aniPla){
        $data = $this->cargar_remuneraciones($emp, $planiPla, $mesPla, $aniPla, 1, 0, 0);
        $myData = $data['data'];
        $cont = sizeof($myData);
        $mes = $this->hallarMes($mesPla);
        $html = 
        '<html>
            <head>
                <style type="text/css">
                    @page{
                        margin: 30px;
                    }
                    body{
                        background-color: #fff;
                        font-family: "Helvetica";
                        font-size: 13px;
                        box-sizing: border-box;
                    }
                    table{
                        border-spacing: 0;
                        border-collapse: collapse;
                        font-size: 14.5px;
                    }
                    table tr th,
                    table tr td{
                        border: 1px solid #ccc;
                        padding: 3px;
                    }
                    h3{
                        margin: 0;
                    }
                    .header-planilla{
                        width: 100%;
                        padding: 3px;
                        border: 1px solid #ccc;
                        margin-bottom: 5px;
                        box-sizing: border-box;
                    }
                    .header-tr{
                        text-align: left;
                    }
                </style>
            </head>
            <body>';

        for ($i = 0; $i < $cont; $i++) { 
            $html .=
            '<div class="header-planilla">
                <h2 style="text-align:center; margin: 0;">BOLETA DE PAGO DE REMUNERACIONES</h2>
                <h3 style="text-align:center; margin-bottom: 10px;">Periodo: '.$mes.' del '.$aniPla.'</h3>
                <h3>'.$myData[$i]['empresa'].'</h3>
                <h3>RUC: '.$myData[$i]['ruc'].'</h3>
            </div>
            <table width="100%">
                <tr>
                    <th colspan="3">Doc. de Identidad</th>
                    <th colspan="6">Nombres y Apellidos</th>
                    <th colspan="2">Situación</th>
                </tr>
                <tr>
                    <td>'.$myData[$i]['tipo_documento'].'</td>
                    <td colspan="2">'.$myData[$i]['dni_trabajador'].'</td>
                    <td colspan="6">'.$myData[$i]['datos_trabajador'].'</td>
                    <td colspan="2">ACTIVO O SUBSIDIADO</td>
                </tr>
                <tr>
                    <th colspan="2">Fecha Ingreso</th>
                    <th colspan="4">Cargo</th>
                    <th colspan="3">Régimen Pensionario</th>
                    <th colspan="2">CUSPP</th>
                </tr>
                <tr>
                    <td colspan="2" align="center">'.date('d/m/Y', strtotime($myData[$i]['fecha_contrato_ini'])).'</td>
                    <td colspan="4">'.$myData[$i]['rol_trabajador'].'</td>
                    <td colspan="3">'.$myData[$i]['fondo_pension'].'</td>
                    <td colspan="2">'.$myData[$i]['numero_cussp'].'</td>
                </tr>
                <tr>
                    <th colspan="2" rowspan="2" align="center">Remuneración Pactada</th>
                    <th rowspan="2" align="center">Días Efect.</th>
                    <th rowspan="2" align="center">Días Lic.</th>
                    <th rowspan="2" align="center">Días Tel.</th>
                    <th colspan="2" align="center">Jornada Ordinaria</th>
                    <th colspan="2" align="center">Sobretiempo</th>
                    <th rowspan="2" align="center">Días Subs.</th>
                    <th rowspan="2" align="center">Días Vacac.</th>
                </tr>
                <tr>
                    <th align="center">Hrs.</th>
                    <th align="center">Min.</th>
                    <th align="center">Hrs.</th>
                    <th align="center">Min.</th>
                </tr>
                <tr>
                    <td align="center" colspan="2" width="18%">'.number_format($myData[$i]['salario'], 2).'</td>
                    <td align="center" width="10%">'.$myData[$i]['dias_total'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['dias_licencia'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['dias_teletrabajo'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['horas_efectivas'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['minutos_efectivas'].'</td>
                    <td align="center" width="9%">0.0</td>
                    <td align="center" width="9%">0.0</td>
                    <td align="center" width="9%">0</td>
                    <td align="center" width="9%">'.$myData[$i]['dias_vacaciones'].'</td>
                </tr>
            </table>
            <br>
            <table width="100%">
                <thead>
                    <tr>
                        <th colspan="2">Conceptos</th>
                        <th>Ingresos</th>
                        <th>Descuentos</th>
                        <th>Neto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="5" class="header-tr">Ingresos</th>
                    </tr>
                    <tr>
                        <td width="10"></td>
                        <td>REMUNERACION BASICA</td>
                        <td align="right">'.number_format($myData[$i]['sueldo_basico'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="10"></td>
                        <td>REMUNERACION VACACIONAL</td>
                        <td align="right">'.number_format($myData[$i]['sueldo_vacaciones'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>';

                    if ($myData[$i]['dias_licencia'] > 0) {
                        $html.=
                        '<tr>
                            <td width="10"></td>
                            <td>LICENCIA CON GOCE</td>
                            <td align="right">'.number_format($myData[$i]['sueldo_licencia'], 2).'</td>
                            <td></td>
                            <td></td>
                        </tr>';
                    }

                    if ($myData[$i]['dias_teletrabajo'] > 0) {
                        $html.=
                        '<tr>
                            <td width="10"></td>
                            <td>REMUNERACION TELETRABAJO</td>
                            <td align="right">'.number_format($myData[$i]['sueldo_teletrabajo'], 2).'</td>
                            <td></td>
                            <td></td>
                        </tr>';
                    }

                    $html.=
                    '<tr>
                        <td width="10"></td>
                        <td>ASIGNACION FAMILIAR</td>
                        <td align="right">'.number_format($myData[$i]['asignacion_familiar'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>';

            $myBoni = $data['data'][$i]['bonificaciones'];
            $contBoni = sizeof($myBoni);

            if ($contBoni > 0){
                for ($j = 0; $j < $contBoni; $j++) { 
                    $html .=
                        '<tr>
                            <td width="10%"></td>
                            <td>'.$myBoni[$j]['concepto'].'</td>
                            <td width="15%" align="right">'.number_format($myBoni[$j]['importe'], 2).'</td>
                            <td width="15%"></td>
                            <td width="15%"></td>
                        </tr>';
                }
            }

            $reint_deduc = $data['data'][$i]['reintegros']['reint_deduc'];
            $reint_no_deduc = $data['data'][$i]['reintegros']['reint_no_deduc'];
            if ($reint_deduc > 0){
                $html.= 
                '<tr>
                    <td width="10"></td>
                    <td>REINTEGRO DEDUCIBLE</td>
                    <td align="right">'.number_format($reint_deduc, 2).'</td>
                    <td></td>
                    <td></td>
                </tr>';
            }
            if ($reint_no_deduc > 0) {
                $html.=
                '<tr>
                    <td width="10"></td>
                    <td>REINTEGRO NO DEDUCIBLE</td>
                    <td align="right">'.number_format($reint_no_deduc, 2).'</td>
                    <td></td>
                    <td></td>
                </tr>';
            }   

            $html .= '<tr><th colspan="5" class="header-tr">Descuentos</th></tr>';
            
            $myDscto = $data['data'][$i]['descuentos'];
            $contDescu = sizeof($myDscto);

            $html .=
                    '<tr>
                        <td width="10%"></td>
                        <td>TARDANZA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myData[$i]['monto_tardanza'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';

            for ($k = 0; $k < $contDescu; $k++) { 
                $html .=
                    '<tr>
                        <td width="10%"></td>
                        <td>'.$myDscto[$k]['concepto'].'</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myDscto[$k]['importe'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }

            $myPensi = $data['data'][$i]['aporte_pension'];
            $tpPensi = $data['data'][$i]['aporte_pension']['tipo'];
            $total_pago = $data['data'][$i]['total_pago'];

            if ($tpPensi == 'onp'){
                $html .=
                    '<tr><th colspan="5" class="header-tr">Aportes del Trabajador</th></tr>
                    <tr>
                        <td width="10%"></td>
                        <td>ONP - APORTACION OBLIGATORIA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['snp'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }else{
                $html .=
                    '<tr><th colspan="5" class="header-tr">Aportes del Trabajador</th></tr>
                    <tr>
                        <td width="10%"></td>
                        <td>COMISION AFP PORCENTUAL</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['comision'], 2).'</td>
                        <td width="15%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td>PRIMA SEGURO AFP</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['prima'], 2).'</td>
                        <td width="15%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td>SPP - APORTACION OBLIGATORIA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['spp'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }
            $html .=
                '<tr>
                    <td width="10%"></td>
                    <td>RENTA DE QUINTA CATEGORIA</td>
                    <td width="15%"></td>';
                $myRet = $data['data'][$i]['retenciones'];
                $contRet = sizeof($myRet);

                if ($contRet > 0){
                    for ($r = 0; $r < $contRet; $r++) {
                        if ($myRet[$r]['concepto'] == '5TA CATEGORIA'){
                            $html .= '<td width="15%" align="right">'.number_format($myRet[$r]['importe'], 2).'</td>';
                        }else{
                            $html .= '<td width="15%" align="right">0.00</td>';
                        }
                    }
                }else{
                    $html .= '<td width="15%" align="right">0.00</td>';
                }
            $html .= '<td width="15%"></td>
                </tr>
                <tr>
                    <th colspan="4" class="header-tr">NETO A PGAR</th>
                    <th align="right">'.number_format($total_pago, 2).'</th>
                </tr>';

            $html .=
                '</tbody>
            </table>';
            if ($planiPla == 1){
                $html .=
                '<table width="100%">
                    <tr>
                        <th colspan="5" class="header-tr">Aportes del Empleador</th>
                    </tr>';
                
                $myAport = $data['data'][$i]['aporte_empleador'];
                $contAport = sizeof($myAport);
    
                for ($m = 0; $m < $contAport; $m++) { 
                    $html .=    
                    '<tr>
                        <td colspan="4">'.$myAport[$m]['concepto'].'</td>
                        <td width="15%" align="right">'.number_format($myAport[$m]['importe'], 2).'</td>
                    </tr>';
                }
    
                $html .=
                '</table>
                <div style="page-break-after:always;"></div>';
            }else{
                $html .= '<div style="page-break-after:always;"></div>';
            }
        }
        $html .=
        '</body>
        </html>';
        return $html;
    }

    public function exportPlanillaSPCC($emp, $planiPla, $mesPla, $aniPla){
        $data = $this->remuneracion_spcc($emp, $planiPla, $mesPla, $aniPla);
        $myData = $data['data'];
        $cont = sizeof($myData);
        $mes = $this->hallarMes($mesPla);
        $html = 
        '<html>
            <head>
                <style type="text/css">
                    @page{
                        margin: 30px;
                    }
                    body{
                        background-color: #fff;
                        font-family: "Helvetica";
                        font-size: 13px;
                        box-sizing: border-box;
                    }
                    table{
                        border-spacing: 0;
                        border-collapse: collapse;
                        font-size: 14px;
                    }
                    table tr th,
                    table tr td{
                        border: 1px solid #ccc;
                        padding: 3px;
                    }
                    h3{
                        margin: 0;
                    }
                    .header-planilla{
                        width: 100%;
                        padding: 3px;
                        border: 1px solid #ccc;
                        margin-bottom: 5px;
                        box-sizing: border-box;
                    }
                    .header-tr{
                        text-align: left;
                    }
                </style>
            </head>
            <body>';

        for ($i = 0; $i < $cont; $i++) { 
            $html .=
            '<div class="header-planilla">
                <h2 style="text-align:center; margin: 0;">BOLETA DE PAGO DE REMUNERACIONES</h2>
                <h3 style="text-align:center; margin-bottom: 10px;">Periodo: '.$mes.' del '.$aniPla.'</h3>
                <h3>'.$myData[$i]['empresa'].'</h3>
                <h3>RUC: '.$myData[$i]['ruc'].'</h3>
            </div>
            <table width="100%">
                <tr>
                    <th colspan="3">Doc. de Identidad</th>
                    <th colspan="6">Nombres y Apellidos</th>
                    <th colspan="2">Situación</th>
                </tr>
                <tr>
                    <td>'.$myData[$i]['tipo_documento'].'</td>
                    <td colspan="2">'.$myData[$i]['dni_trabajador'].'</td>
                    <td colspan="6">'.$myData[$i]['datos_trabajador'].'</td>
                    <td colspan="2">ACTIVO O SUBSIDIADO</td>
                </tr>
                <tr>
                    <th colspan="2">Fecha Ingreso</th>
                    <th colspan="4">Cargo</th>
                    <th colspan="3">Régimen Pensionario</th>
                    <th colspan="2">CUSPP</th>
                </tr>
                <tr>
                    <td colspan="2" align="center">'.date('d/m/Y', strtotime($myData[$i]['fecha_contrato_ini'])).'</td>
                    <td colspan="4">'.$myData[$i]['rol_trabajador'].'</td>
                    <td colspan="3">'.$myData[$i]['fondo_pension'].'</td>
                    <td colspan="2">'.$myData[$i]['numero_cussp'].'</td>
                </tr>
                <tr>
                    <th colspan="2" rowspan="2" align="center">Remuneración Pactada</th>
                    <th rowspan="2" align="center">Días Efect.</th>
                    <th rowspan="2" align="center">Días Lic.</th>
                    <th rowspan="2" align="center">Días Tel.</th>
                    <th colspan="2" align="center">Jornada Ordinaria</th>
                    <th colspan="2" align="center">Sobretiempo</th>
                    <th rowspan="2" align="center">Días Subs.</th>
                    <th rowspan="2" align="center">Días Vacac.</th>
                </tr>
                <tr>
                    <th align="center">Hrs.</th>
                    <th align="center">Min.</th>
                    <th align="center">Hrs.</th>
                    <th align="center">Min.</th>
                </tr>
                <tr>
                    <td align="center" colspan="2" width="18%">'.number_format($myData[$i]['salario'], 2).'</td>
                    <td align="center" width="10%">'.$myData[$i]['dias_total'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['dias_licencia'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['dias_teletrabajo'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['horas_efectivas'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['minutos_efectivas'].'</td>
                    <td align="center" width="9%">0.0</td>
                    <td align="center" width="9%">0.0</td>
                    <td align="center" width="9%">0</td>
                    <td align="center" width="9%">'.$myData[$i]['dias_vacaciones'].'</td>
                </tr>
            </table>
            <br>
            <table width="100%">
                <thead>
                    <tr>
                        <th colspan="2">Conceptos</th>
                        <th>Ingresos</th>
                        <th>Descuentos</th>
                        <th>Neto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="5" class="header-tr">Ingresos</th>
                    </tr>
                    <tr>
                        <td width="10"></td>
                        <td>REMUNERACION BASICA</td>
                        <td align="right">'.number_format($myData[$i]['sueldo_basico'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="10"></td>
                        <td>REMUNERACION VACACIONAL</td>
                        <td align="right">'.number_format($myData[$i]['sueldo_vacaciones'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>';

                    if ($myData[$i]['dias_licencia'] > 0) {
                        $html.=
                        '<tr>
                            <td width="10"></td>
                            <td>LICENCIA CON GOCE</td>
                            <td align="right">'.number_format($myData[$i]['sueldo_licencia'], 2).'</td>
                            <td></td>
                            <td></td>
                        </tr>';
                    }

                    if ($myData[$i]['dias_teletrabajo'] > 0) {
                        $html.=
                        '<tr>
                            <td width="10"></td>
                            <td>REMUNERACION TELETRABAJO</td>
                            <td align="right">'.number_format($myData[$i]['sueldo_teletrabajo'], 2).'</td>
                            <td></td>
                            <td></td>
                        </tr>';
                    }

                    $html.=
                    '<tr>
                        <td width="10"></td>
                        <td>ASIGNACION FAMILIAR</td>
                        <td align="right">'.number_format($myData[$i]['asignacion_familiar'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>';

            $myBoni = $data['data'][$i]['bonificaciones'];
            $contBoni = sizeof($myBoni);

            if ($contBoni > 0){
                for ($j = 0; $j < $contBoni; $j++) { 
                    $html .=
                        '<tr>
                            <td width="10%"></td>
                            <td>'.$myBoni[$j]['concepto'].'</td>
                            <td width="15%" align="right">'.number_format($myBoni[$j]['importe'], 2).'</td>
                            <td width="15%"></td>
                            <td width="15%"></td>
                        </tr>';
                }
            }

            $reint_deduc = $data['data'][$i]['reintegros']['reint_deduc'];
            $reint_no_deduc = $data['data'][$i]['reintegros']['reint_no_deduc'];
            if ($reint_deduc > 0){
                $html.= 
                '<tr>
                    <td width="10"></td>
                    <td>REINTEGRO DEDUCIBLE</td>
                    <td align="right">'.number_format($reint_deduc, 2).'</td>
                    <td></td>
                    <td></td>
                </tr>';
            }
            if ($reint_no_deduc > 0) {
                $html.=
                '<tr>
                    <td width="10"></td>
                    <td>REINTEGRO NO DEDUCIBLE</td>
                    <td align="right">'.number_format($reint_no_deduc, 2).'</td>
                    <td></td>
                    <td></td>
                </tr>';
            }   

            $html .= '<tr><th colspan="5" class="header-tr">Descuentos</th></tr>';
            
            $myDscto = $data['data'][$i]['descuentos'];
            $contDescu = sizeof($myDscto);

            $html .=
                    '<tr>
                        <td width="10%"></td>
                        <td>TARDANZA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myData[$i]['monto_tardanza'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';

            for ($k = 0; $k < $contDescu; $k++) { 
                $html .=
                    '<tr>
                        <td width="10%"></td>
                        <td>'.$myDscto[$k]['concepto'].'</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myDscto[$k]['importe'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }

            $myPensi = $data['data'][$i]['aporte_pension'];
            $tpPensi = $data['data'][$i]['aporte_pension']['tipo'];
            $total_pago = $data['data'][$i]['total_pago'];

            if ($tpPensi == 'onp'){
                $html .=
                    '<tr><th colspan="5" class="header-tr">Aportes del Trabajador</th></tr>
                    <tr>
                        <td width="10%"></td>
                        <td>ONP - APORTACION OBLIGATORIA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['snp'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }else{
                $html .=
                    '<tr><th colspan="5" class="header-tr">Aportes del Trabajador</th></tr>
                    <tr>
                        <td width="10%"></td>
                        <td>COMISION AFP PORCENTUAL</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['comision'], 2).'</td>
                        <td width="15%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td>PRIMA SEGURO AFP</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['prima'], 2).'</td>
                        <td width="15%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td>SPP - APORTACION OBLIGATORIA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['spp'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }
            $html .=
                '<tr>
                    <td width="10%"></td>
                    <td>RENTA DE QUINTA CATEGORIA</td>
                    <td width="15%"></td>';
                $myRet = $data['data'][$i]['retenciones'];
                $contRet = sizeof($myRet);

                if ($contRet > 0){
                    for ($r = 0; $r < $contRet; $r++) {
                        if ($myRet[$r]['concepto'] == '5TA CATEGORIA'){
                            $html .= '<td width="15%" align="right">'.number_format($myRet[$r]['importe'], 2).'</td>';
                        }else{
                            $html .= '<td width="15%" align="right">0.00</td>';
                        }
                    }
                }else{
                    $html .= '<td width="15%" align="right">0.00</td>';
                }
            $html .= '<td width="15%"></td>
                </tr>
                <tr>
                    <th colspan="4" class="header-tr">NETO A PGAR</th>
                    <th align="right">'.number_format($total_pago, 2).'</th>
                </tr>';

            $html .=
                '</tbody>
            </table>';
            if ($planiPla == 1){
                $html .=
                '<table width="100%">
                    <tr>
                        <th colspan="5" class="header-tr">Aportes del Empleador</th>
                    </tr>';
                
                $myAport = $data['data'][$i]['aporte_empleador'];
                $contAport = sizeof($myAport);
    
                for ($m = 0; $m < $contAport; $m++) { 
                    $html .=    
                    '<tr>
                        <td colspan="4">'.$myAport[$m]['concepto'].'</td>
                        <td width="15%" align="right">'.number_format($myAport[$m]['importe'], 2).'</td>
                    </tr>';
                }
    
                $html .=
                '</table>
                <div style="page-break-after:always;"></div>';
            }else{
                $html .= '<div style="page-break-after:always;"></div>';
            }
        }
        $html .=
        '</body>
        </html>';
        return $html;
    }

    public function exportPlanillaIndividual($emp, $planiPla, $mesPla, $aniPla, $empleado, $design){
        $img = '';
        if ($emp == 1){
            $img = '<img src="./images/okc.jpg" alt="firma" height="100px">';
        }else if ($emp == 2){
            $img = '<img src="./images/pyc.jpg" alt="firma" height="100px">';
        }else if ($emp == 3){
            $img = '<img src="./images/svs.jpg" alt="firma" height="100px">';
        }
        $data = $this->cargar_remuneraciones($emp, $planiPla, $mesPla, $aniPla, 1, $empleado, 0);
        $myData = $data['data'];
        $cont = sizeof($myData);
        $mes = $this->hallarMes($mesPla);
        $html = 
        '<html>
            <head>
                <style type="text/css">
                    @page{
                        margin: 30px;
                    }
                    body{
                        background-color: #fff;
                        font-family: "Helvetica";
                        font-size: 13px;
                        box-sizing: border-box;
                    }
                    table{
                        border-spacing: 0;
                        border-collapse: collapse;
                        font-size: 14.5px;
                    }
                    table tr th,
                    table tr td{
                        border: 1px solid #ccc;
                        padding: 3px;
                    }
                    h3{
                        margin: 0;
                    }
                    .header-planilla{
                        width: 100%;
                        padding: 3px;
                        border: 1px solid #ccc;
                        margin-bottom: 5px;
                        box-sizing: border-box;
                    }
                    .header-tr{
                        text-align: left;
                    }
                    .firma{
                        margin-top: 50px;
                        margin-left: 40px;
                    }
                </style>
            </head>
            <body>';

        for ($i = 0; $i < $cont; $i++) {
            $html .=
            '<div class="header-planilla">
                <h2 style="text-align:center; margin: 0;">BOLETA DE PAGO DE REMUNERACIONES</h2>
                <h3 style="text-align:center; margin-bottom: 10px;">Periodo: '.$mes.' del '.$aniPla.'</h3>
                <h3>'.$myData[$i]['empresa'].'</h3>
                <h3>RUC: '.$myData[$i]['ruc'].'</h3>
            </div>
            <table width="100%">
                <tr>
                    <th colspan="3">Doc. de Identidad</th>
                    <th colspan="6">Nombres y Apellidos</th>
                    <th colspan="2">Situación</th>
                </tr>
                <tr>
                    <td>'.$myData[$i]['tipo_documento'].'</td>
                    <td colspan="2">'.$myData[$i]['dni_trabajador'].'</td>
                    <td colspan="6">'.$myData[$i]['datos_trabajador'].'</td>
                    <td colspan="2">ACTIVO O SUBSIDIADO</td>
                </tr>
                <tr>
                    <th colspan="2">Fecha Ingreso</th>
                    <th colspan="4">Cargo</th>
                    <th colspan="3">Régimen Pensionario</th>
                    <th colspan="2">CUSPP</th>
                </tr>
                <tr>
                    <td colspan="2" align="center">'.date('d/m/Y', strtotime($myData[$i]['fecha_contrato_ini'])).'</td>
                    <td colspan="4">'.$myData[$i]['rol_trabajador'].'</td>
                    <td colspan="3">'.$myData[$i]['fondo_pension'].'</td>
                    <td colspan="2">'.$myData[$i]['numero_cussp'].'</td>
                </tr>
                <tr>
                    <th colspan="2" rowspan="2" align="center">Remuneración Pactada</th>
                    <th rowspan="2" align="center">Días Efect.</th>
                    <th rowspan="2" align="center">Días Lic.</th>
                    <th rowspan="2" align="center">Días Tel.</th>
                    <th colspan="2" align="center">Jornada Ord.</th>
                    <th colspan="2" align="center">Sobretiempo</th>
                    <th rowspan="2" align="center">Días Subs.</th>
                    <th rowspan="2" align="center">Días Vacac.</th>
                </tr>
                <tr>
                    <th align="center">Hrs.</th>
                    <th align="center">Min.</th>
                    <th align="center">Hrs.</th>
                    <th align="center">Min.</th>
                </tr>
                <tr>
                    <td align="center" colspan="2" width="18%">'.number_format($myData[$i]['salario'], 2).'</td>
                    <td align="center" width="10%">'.$myData[$i]['dias_total'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['dias_licencia'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['dias_teletrabajo'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['horas_efectivas'].'</td>
                    <td align="center" width="9%">'.$myData[$i]['minutos_efectivas'].'</td>
                    <td align="center" width="9%">0.0</td>
                    <td align="center" width="9%">0.0</td>
                    <td align="center" width="9%">0</td>
                    <td align="center" width="9%">'.$myData[$i]['dias_vacaciones'].'</td>
                </tr>
            </table>
            <br>
            <table width="100%">
                <thead>
                    <tr>
                        <th colspan="2">Conceptos</th>
                        <th>Ingresos</th>
                        <th>Descuentos</th>
                        <th>Neto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="5" class="header-tr">Ingresos</th>
                    </tr>
                    <tr>
                        <td width="10"></td>
                        <td>REMUNERACION BASICA</td>
                        <td align="right">'.number_format($myData[$i]['sueldo_basico'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="10"></td>
                        <td>REMUNERACION VACACIONAL</td>
                        <td align="right">'.number_format($myData[$i]['sueldo_vacaciones'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>';

                    if ($myData[$i]['dias_licencia'] > 0) {
                        $html.=
                        '<tr>
                            <td width="10"></td>
                            <td>LICENCIA CON GOCE</td>
                            <td align="right">'.number_format($myData[$i]['sueldo_licencia'], 2).'</td>
                            <td></td>
                            <td></td>
                        </tr>';
                    }

                    if ($myData[$i]['dias_teletrabajo'] > 0) {
                        $html.=
                        '<tr>
                            <td width="10"></td>
                            <td>REMUNERACION TELETRABAJO</td>
                            <td align="right">'.number_format($myData[$i]['sueldo_teletrabajo'], 2).'</td>
                            <td></td>
                            <td></td>
                        </tr>';
                    }
                    
                   $html.= 
                   '<tr>
                        <td width="10"></td>
                        <td>ASIGNACION FAMILIAR</td>
                        <td align="right">'.number_format($myData[$i]['asignacion_familiar'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>';

            $myBoni = $data['data'][$i]['bonificaciones'];
            $contBoni = sizeof($myBoni);

            if ($contBoni > 0){
                for ($j = 0; $j < $contBoni; $j++) { 
                    $html .=
                        '<tr>
                            <td width="10%"></td>
                            <td>'.$myBoni[$j]['concepto'].'</td>
                            <td width="15%" align="right">'.number_format($myBoni[$j]['importe'], 2).'</td>
                            <td width="15%"></td>
                            <td width="15%"></td>
                        </tr>';
                }
            }

            $reint_deduc = $data['data'][$i]['reintegros']['reint_deduc'];
            $reint_no_deduc = $data['data'][$i]['reintegros']['reint_no_deduc'];
            if ($reint_deduc > 0){
                $html.= 
                '<tr>
                    <td width="10"></td>
                    <td>REINTEGRO DEDUCIBLE</td>
                    <td align="right">'.number_format($reint_deduc, 2).'</td>
                    <td></td>
                    <td></td>
                </tr>';
            }
            if ($reint_no_deduc > 0) {
                $html.=
                '<tr>
                    <td width="10"></td>
                    <td>REINTEGRO NO DEDUCIBLE</td>
                    <td align="right">'.number_format($reint_no_deduc, 2).'</td>
                    <td></td>
                    <td></td>
                </tr>';
            }

            $html .= '<tr><th colspan="5" class="header-tr">Descuentos</th></tr>';
            
            $myDscto = $data['data'][$i]['descuentos'];
            $contDescu = sizeof($myDscto);

            $html .=
                    '<tr>
                        <td width="10%"></td>
                        <td>TARDANZA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myData[$i]['monto_tardanza'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';

            for ($k = 0; $k < $contDescu; $k++) { 
                $html .=
                    '<tr>
                        <td width="10%"></td>
                        <td>'.$myDscto[$k]['concepto'].'</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myDscto[$k]['importe'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }

            $myPensi = $data['data'][$i]['aporte_pension'];
            $tpPensi = $data['data'][$i]['aporte_pension']['tipo'];
            $total_pago = $data['data'][$i]['total_pago'];

            if ($tpPensi == 'onp'){
                $html .=
                    '<tr><th colspan="5" class="header-tr">Aportes del Trabajador</th></tr>
                    <tr>
                        <td width="10%"></td>
                        <td>ONP - APORTACION OBLIGATORIA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['snp'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }else{
                $html .=
                    '<tr><th colspan="5" class="header-tr">Aportes del Trabajador</th></tr>
                    <tr>
                        <td width="10%"></td>
                        <td>COMISION AFP PORCENTUAL</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['comision'], 2).'</td>
                        <td width="15%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td>PRIMA SEGURO AFP</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['prima'], 2).'</td>
                        <td width="15%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td>SPP - APORTACION OBLIGATORIA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myPensi['spp'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }
            $html .=
                '<tr>
                    <td width="10%"></td>
                    <td>RENTA DE QUINTA CATEGORIA</td>
                    <td width="15%"></td>';
                $myRet = $data['data'][$i]['retenciones'];
                $contRet = sizeof($myRet);

                if ($contRet > 0){
                    for ($r = 0; $r < $contRet; $r++) {
                        if ($myRet[$r]['concepto'] == '5TA CATEGORIA'){
                            $html .= '<td width="15%" align="right">'.number_format($myRet[$r]['importe'], 2).'</td>';
                        }else{
                            $html .= '<td width="15%" align="right">0.00</td>';
                        }
                    }
                }else{
                    $html .= '<td width="15%" align="right">0.00</td>';
                }
            $html .= '<td width="15%"></td>
                </tr>
                <tr>
                    <th colspan="4" class="header-tr">NETO A PGAR</th>
                    <th align="right">'.number_format($total_pago, 2).'</th>
                </tr>';

            $html .=
                '</tbody>
            </table>';
            if ($planiPla == 1){
                $html .=
                '<table width="100%">
                    <tr>
                        <th colspan="5" class="header-tr">Aportes del Empleador</th>
                    </tr>';
                
                $myAport = $data['data'][$i]['aporte_empleador'];
                $contAport = sizeof($myAport);
    
                for ($m = 0; $m < $contAport; $m++) { 
                    $html .=    
                    '<tr>
                        <td colspan="4">'.$myAport[$m]['concepto'].'</td>
                        <td width="15%" align="right">'.number_format($myAport[$m]['importe'], 2).'</td>
                    </tr>';
                }
    
                $html .= '</table>';
            }else{
                $html .= '<div style="page-break-after:always;"></div>';
            }
        }
        if ($design > 0){
            $html .=
            '<div class="firma">'.$img.'</div>
            </body>
            </html>';
        }else{
            $html .=
            '</body>
            </html>';
        }
        return $html;
    }

    public function reporte_planilla_trabajador_xls($emp, $plani, $mes, $anio, $empleado){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->exportPlanillaIndividual($emp, $plani, $mes, $anio, $empleado, 1))->setPaper('a4', 'portrait');
        return $pdf->stream('reporte_ind.pdf');
    }

    //nuevo
    public function generar_pdf_trabajdor($emp, $plani, $mes, $anio){
        $sql = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->select(DB::raw('DISTINCT rrhh_trab.id_trabajador'), 'rrhh_perso.nro_documento', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno', 'rrhh_postu.correo')
            ->where([['rrhh_trab.estado', '=', 1], ['adm_empresa.id_empresa', '=', $emp], ['rrhh_rol.id_tipo_planilla', '=', $plani],
                    ['rrhh_rol.salario', '>', 0], ['rrhh_rol.estado', '=', 1], ['rrhh_postu.correo', '!=', null]])
            ->orderBy('rrhh_perso.apellido_paterno', 'asc')->get();

        foreach ($sql as $key){
            $empleado = $key->id_trabajador;
            $correo = $key->correo;
            $dni = $key->nro_documento;
            $name = $key->apellido_paterno.' '.$key->apellido_materno;
            $nameFile = $dni.'_'.$mes.'_'.$anio;
            $this->enviar_email($emp, $plani, $mes, $anio, $empleado, $correo, $nameFile, $name);
        }
    }

    public function enviar_email($emp, $plani, $mes, $anio, $empleado, $correo, $nameFile, $datos){
        $data = $this->exportPlanillaIndividual($emp, $plani, $mes, $anio, $empleado, 1);
        $imgs = $this->exportPlanillaIndividual($emp, $plani, $mes, $anio, $empleado, 0);
        $myHtml_si = '';
        $myHtml_no = '';
        $ruta = '/planillas/'.$nameFile.'.pdf';
        $atach = asset('files').$ruta;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('a4', 'portrait');
        $pdf->loadHTML($data);

	    $output = $pdf->stream();
        Storage::disk('archivos')->put($ruta, $output);

        $smtpAddress = 'smtp.gmail.com';
        $port = 587;
        $encryption = 'tls';
        $yourEmail = 'rrhh@okcomputer.com.pe';
        $yourPassword = '12345678';

        Swift_Preferences::getInstance()->setCacheType('null');

        $transport = (new \Swift_SmtpTransport($smtpAddress, $port, $encryption))
            ->setUsername($yourEmail)
            ->setPassword($yourPassword);
        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message('Boleta de Pagos'))
            ->setFrom([$yourEmail => 'Recursos Humanos'])
            ->setTo([$correo])
            ->setBody('Boleta de pagos')
            ->addPart($imgs, 'text/html');

        $message->attach(new Swift_Attachment($pdf->output(), 'boleta.pdf', 'application/pdf'));

        if($mailer->send($message)){
            $myHtml_si.= '<li>'.$datos.' - Recibió el mensaje</li>';
        }else{
            $myHtml_no.= '<li>'.$datos.' - No recibió el mensaje</li>';
        }
        $array = array('si' => $myHtml_si, 'no' =>$myHtml_no);
        echo json_encode($array);
    }

    public function reporte_gastos($planiRem, $mesRem, $anio){
        $dataEmp = DB::table('administracion.adm_empresa')->select('id_empresa')->where('estado', '=', 1)->orderBy('id_empresa', 'ASC')->get();
        $mes = $this->hallarMes($mesRem);
        $html = '';

        foreach ($dataEmp as $key){
            $data = $this->cargar_remuneraciones($key->id_empresa, $planiRem, $mesRem, $anio, 1, 0, 0);
            $myData = $data['data'];
            $cont = sizeof($myData);
            
            $name_empresa = DB::table('administracion.adm_empresa')->select('codigo')->where('id_empresa', '=', $key->id_empresa)->get();
            $codeEmp = $name_empresa->first()->codigo;

            $html.=
            '<h2>Gastos de Planilla '.$codeEmp.'</h2>
            <h4>PERIODO '.$mes.'-'.$anio.'</h4>
            <table border="1">
                <thead>
                    <tr>
                        <th>Apellidos y Nombre</th>
                        <th>Cargo</th>
                        <th>Centro Costo</th>
                        <th>Tipo Costo</th>
                        <th>DC. Ley / MyPE</th>
                        <th>Remun Aseg.</th>
                        <th>Total Ingresos</th>
                        <th>Vacac. 1/12</th>
                        <th>Gratif. 1/6</th>
                        <th>CTS. 1/2</th>
                        <th>EsSalud</th>
                        <th>SCTR</th>
                        <th>Costo Total</th>
                        <th>Sobre Costo</th>
                    </tr>
                </thead>
            <tbody>';
            
            $vacac = 0;
            $grati = 0;
            $micts = 0;
            $salud = 0;
            $total = 0;
            $costo = 0;

            if ($codeEmp == 'OKC' || $codeEmp == 'PYC') {
                $dcley = 1;
            }else{
                $dcley = 2;
            }

            for ($i = 0; $i < $cont; $i++){
                $traba = $myData[$i]['datos_trabajador'];
                $cargo = $myData[$i]['rol_trabajador'];
                $ccost = $myData[$i]['sede'].' - '.$myData[$i]['area'];
                $tpcos = $myData[$i]['tipo_centro_costo'];
                $tsctr = ($myData[$i]['sctr'] == 1) ? 15.13 : 0; 
                $remun = $myData[$i]['total_remun'];
                $ingre = $myData[$i]['total_ingreso'];

                $vacac = ($remun/12/$dcley);
                $grati = ($remun/6*1.09/$dcley);
                $micts = ($remun/12/$dcley + $remun/12/6/$dcley);
                $salud = ($remun*0.09);
                $total = ($ingre + $vacac + $grati + $micts + $salud + $tsctr);
                $costo = ($total / $ingre);
                
                $html.=
                '<tr>
                    <td>'.$traba.'</td>
                    <td>'.$cargo.'</td>
                    <td>'.$ccost.'</td>
                    <td>'.$tpcos.'</td>
                    <td class="okc-order">'.$dcley.'</td>
                    <td class="okc-numero">'.number_format($remun, 2).'</td>
                    <td class="okc-numero">'.number_format($ingre, 2).'</td>
                    <td class="okc-numero">'.number_format($vacac, 2).'</td>
                    <td class="okc-numero">'.number_format($grati, 2).'</td>
                    <td class="okc-numero">'.number_format($micts, 2).'</td>
                    <td class="okc-numero">'.number_format($salud, 2).'</td>
                    <td class="okc-numero">'.number_format($tsctr, 2).'</td>
                    <td class="okc-numero">'.number_format($total, 2).'</td>
                    <td class="okc-numero">'.number_format($costo, 2).'</td>
                </tr>';
            }
            $html .= '</tbody></table>';
        }
        $export = $html;
        return view('rrhh/reportes/Planilla_Gastos', compact('export'));
    }

    public function reporte_planilla_grupal_xls($planiRem, $mesRem, $anio, $grupo){
        $dataEmp = DB::table('administracion.adm_empresa')->select('id_empresa')->where('estado', '=', 1)->get();
        $mes = $this->hallarMes($mesRem);
        $html = '';
        foreach ($dataEmp as $key){
            $data = $this->cargar_remuneraciones($key->id_empresa, $planiRem, $mesRem, $anio, 3, 0, $grupo);
            $myData = $data['data'];
            $cont = sizeof($myData);
            $name_empresa = DB::table('administracion.adm_empresa')->select('codigo')->where('id_empresa', '=', $key->id_empresa)->get();
            $codeEmp = $name_empresa->first()->codigo;

            $html.=
            '<h2>Planilla de Remuneraciones '.$codeEmp.'</h2>
            <h4>PERIODO '.$mes.'-'.$anio.'</h4>
            <table border="1">
            <thead>';
            if ($planiRem == 1){
                $html .=
                '<tr>
                    <th rowspan="3">N°</th>
                    <th rowspan="3">Apellidos y Nombres</th>
                    <th rowspan="3">DNI</th>
                    <th rowspan="3">SEXO</th>
                    <th rowspan="3">CUSPP</th>
                    <th rowspan="3">SEDE</th>
                    <th rowspan="3">AREA</th>
                    <th rowspan="3">CARGO</th>
                    <th rowspan="3">Centro Costos</th>
                    <th rowspan="3">Tipo Costo</th>
                    <th rowspan="3">Rég. Laboral</th>
                    <th rowspan="3">Fondo Pensión</th>
                    <th rowspan="3" width="80">Días No Comp.</th>
                    <th rowspan="3" width="80">N° Horas Ordinarias</th>
                    <th rowspan="2" colspan="3">Horas Extras</th>
                    <th rowspan="3" width="80">Minutos Tardanza</th>
                    <th rowspan="3" width="80">Acum. Tardanza</th>
                    <th rowspan="3">Remun. Básica</th>
                    <th colspan="8">CONCEPTOS BASICOS NO REMUNERATIVOS</th>
                    <th colspan="10">REMUNERACION ASEGURABLE</th>
                    <th rowspan="3">Total Ingresos</th>
                    <th rowspan="3">Total Remun. Asegurable</th>
                    <th colspan="5">APORTES</th>
                    <th rowspan="3">Total Aportes</th>
                    <th colspan="5">DESCUENTOS DEDUCIBLES</th>
                    <th rowspan="3">Total Descto. Deduc.</th>
                    <th colspan="6">OTROS DESCUENTOS NO DEDUCIBLES</th>
                    <th rowspan="3">Total Descto. No Deduc.</th>
                    <th rowspan="3">Neto a Pagar</th>
                    <th rowspan="3">EsSalud 9%</th>
                    <th colspan="2">Horas Efectivas</th>
                </tr>
                <tr>
                    <th colspan="3">LIQUIDACION</th>
                    <th rowspan="2" width="80">Reint. No Deduc.</th>
                    <th rowspan="2" width="80">Bonif. x Ventas</th>
                    <th rowspan="2" width="80">Bono x Cumplim.</th>
                    <th rowspan="2" width="80">Vivienda Condicion de Trab.</th>
                    <th rowspan="2" width="80">Devol. 5ta Cat.</th>
                    <th rowspan="2">Remun. Principal</th>
                    <th rowspan="2">Asig. Familiar</th>
                    <th colspan="3">Horas Extras</th>
                    <th rowspan="2">Reintegro</th>
                    <th rowspan="2">Otros Ingresos</th>
                    <th colspan="3">Vacaciones</th>
                    <th rowspan="2">SNP 13%</th>
                    <th colspan="3">SPP</th>
                    <th rowspan="2">I.R. 5ta Categ.</th>
                    <th rowspan="2" width="80">Tardanzas</th>
                    <th rowspan="2" width="80">Permiso Sin Goce</th>
                    <th rowspan="2" width="80">Inasistencia</th>
                    <th rowspan="2" width="80">Falta Injustif.</th>
                    <th rowspan="2" width="80">Otros Deducibles</th>
                    <th rowspan="2" width="80">Préstamos</th>
                    <th rowspan="2" width="80">Adelantos</th>
                    <th rowspan="2" width="80">Retenc. Judicial</th>
                    <th rowspan="2" width="80">Vacac. Pago x Adelant.</th>
                    <th rowspan="2" width="80">Descuento Autor</th>
                    <th rowspan="2" width="80">Otros</th>
                    <th rowspan="2">Horas</th>
                    <th rowspan="2">Minutos</th>
                </tr>
                <tr>
                    <th>N° 25%</th>
                    <th>N° 35%</th>
                    <th>N° 100%</th>
                    <th>CTS</th>
                    <th>Gratif.</th>
                    <th>Bonif. Extra</th>
                    <th>25%</th>
                    <th>35%</th>
                    <th>100%</th>
                    <th>Truncas</th>
                    <th>Remun. Vac.</th>
                    <th>Compen. Vac.</th>
                    <th>Aporte</th>
                    <th>Prima</th>
                    <th>Comisión</th>
                    
                </tr>';
            }else if ($planiRem == 2){
                $html .=
                '<tr>
                    <th>N°</th>
                    <th>Apellidos y Nombres</th>
                    <th>DNI</th>
                    <th>Sexo</th>
                    <th>Sede</th>
                    <th>Area</th>
                    <th>Cargo</th>
                    <th>Rég. Laboral</th>
                    <th>Días Comp.</th>
                    <th>Sueldo Básico</th>
                    <th>Remun. Principal</th>
                    <th>Otros Ingresos</th>
                    <th>Reintegro</th>
                    <th>Minutos Tardanza</th>
                    <th>Monto Tardanza</th>
                    <th>Inasistencia</th>
                    <th>Monto Inasistencia</th>
                    <th>Otros Descuentos</th>
                    <th>Sueldo Neto</th>
                    <th>Horas Efect.</th>
                    <th>Minutos Efect.</th>
                </tr>';
            }
            $html.=
            '</thead>
            <tbody>';
            if ($planiRem == 1){
                for ($i = 0; $i < $cont; $i++){
                    $a = 0;
                    $nro = $i + 1;
                    $datos = $myData[$i]['datos_trabajador'];
                    $dni = $myData[$i]['dni_trabajador'];
                    $sexo = $myData[$i]['sexo_trabajador'];
                    $sede = $myData[$i]['sede'];
                    $area = $myData[$i]['area'];
                    $cargo = $myData[$i]['rol_trabajador'];
                    $pension = $myData[$i]['fondo_pension'];
                    $ini_cont = $myData[$i]['fecha_contrato_ini'];
                    $cussp = $myData[$i]['numero_cussp'];
                    $asig = $myData[$i]['asignacion_familiar'];
                    $horas = $myData[$i]['horas_efectivas'];
                    $mints = $myData[$i]['minutos_efectivas'];
    
                    $reglab = $myData[$i]['regimen'];
                    $dias = $myData[$i]['dias_total'];
                    $cc = $sede.' - '.$area;
                    $tipo_cc = $myData[$i]['tipo_centro_costo'];
                    $salario = $myData[$i]['salario'];
                    $sueldo = $myData[$i]['sueldo_basico'];
                    $total_remun = $myData[$i]['total_remun'];
    
                    $myPensi = $data['data'][$i]['aporte_pension'];
                    $tpPensi = $myPensi['tipo'];
                    if ($tpPensi == 'onp') {
                        $snp = $myPensi['snp'];
                        $spp = 0;
                        $prima = 0;
                        $comision = 0;
                    }else{
                        $snp = 0;
                        $spp = $myPensi['spp'];
                        $prima = $myPensi['prima'];
                        $comision = $myPensi['comision'];
                    }
    
                    $reint_deduc = $data['data'][$i]['reintegros']['reint_deduc'];
                    $reint_no_deduc = $data['data'][$i]['reintegros']['reint_no_deduc'];
    
                    $myRetenc = $data['data'][$i]['retenciones'];
                    $countRetenc = sizeof($myRetenc);
                    $quinta = 0;
                    for ($ret = 0; $ret < $countRetenc; $ret++){ 
                        if ($myRetenc[$ret]['concepto'] == '5TA CATEGORIA'){
                            $quinta = $myRetenc[$ret]['importe'];
                        }
                    }
    
                    $myAport = $data['data'][$i]['aporte_empleador'];
                    $countAport = sizeof($myAport);
                    $essalud = 0;
                    for ($apt = 0; $apt < $countAport; $apt++){ 
                        if ($myAport[$apt]['concepto'] == 'ESSALUD (REGULAR CBSSP AGRAR/AC) TRAB'){
                            $essalud = $myAport[$apt]['importe'];
                        }
                    }
    
                    $myPrest = $data['data'][$i]['descuentos'];
                    $countPrest = sizeof($myPrest);
                    $prest = 0;
                    $otrod = 0;
                    $adela = 0;
                    for ($ptm = 0; $ptm < $countPrest; $ptm++) { 
                        if ($myPrest[$ptm]['concepto'] == 'PRESTAMOS'){
                            $prest += $myPrest[$ptm]['importe'];
                        }elseif($myPrest[$ptm]['concepto'] == 'OTROS DESCUENTOS NO DEDUC. DE BASE IMPONIBLE'){
                            $otrod += $myPrest[$ptm]['importe'];
                        }elseif($myPrest[$ptm]['concepto'] == 'ADELANTOS'){
                            $adela += $myPrest[$ptm]['importe'];
                        }
                    }
    
                    $myIna = $data['data'][$i]['descuentos'];
                    $countIna = sizeof($myIna);
                    $inas = 0;
                    for ($ist = 0; $ist < $countIna; $ist++) { 
                        if ($myIna[$ist]['concepto'] == 'INASISTENCIAS'){
                            $inas += $myIna[$ist]['importe'];
                        }
                    }
                    
                    $myBonif = $data['data'][$i]['bonificaciones'];
                    $countBonif = sizeof($myBonif);
                    $bnfcmp = 0;
                    $comis = 0;
                    $viv = 0;
                    $cmps = 0;
                    $otroBon = 0;
                    for ($bnf = 0; $bnf < $countBonif; $bnf++) { 
                        if ($myBonif[$bnf]['concepto'] == 'COMISION'){
                            $comis += $myBonif[$bnf]['importe'];
                        }elseif ($myBonif[$bnf]['concepto'] == 'VIVIENDA CONDICION DE TRABAJO'){
                            $viv += $myBonif[$bnf]['importe'];
                        }elseif($myBonif[$bnf]['concepto'] == 'COMPENSACION VACACIONAL'){
                            $cmps += $myBonif[$bnf]['importe'];
                        }elseif ($myBonif[$bnf]['concepto'] == 'BONO POR CUMPLIMIENTO'){
                            $bnfcmp += $myBonif[$bnf]['importe'];
                        }else{
                            $otroBon += $myBonif[$bnf]['importe'];
                        }
                    }
    
                    $minTa = $myData[$i]['minutos_tardanza'];
                    $dscTa = $myData[$i]['descuento_tardanza'];
                    $monTa = $myData[$i]['monto_tardanza'];
                    $ingreso = $myData[$i]['total_ingreso'];
                    $aportes = $snp + $spp + $prima + $comision + $quinta;
                    $descuents = $monTa + $inas;
                    $dsctos_no = $prest + $otrod + $adela;
                    $neto = $myData[$i]['total_pago'];
                    $vacac = $myData[$i]['sueldo_vacaciones'];
    
                    $html .=
                    '<tr>
                        <td class="okc-order">'.$nro.'</td>
                        <td>'.$datos.'</td>
                        <td>'.$dni.'</td>
                        <td>'.$sexo.'</td>
                        <td>'.$cussp.'</td>
                        <td>'.$sede.'</td>
                        <td>'.$area.'</td>
                        <td>'.$cargo.'</td>
                        <td>'.$cc.'</td>
                        <td>'.$tipo_cc.'</td>
                        <td>'.$reglab.'</td>
                        <td>'.$pension.'</td>
                        <td>'.$dias.'</td>
                        <td class="okc-numero">'.$horas.'</td>
                        <td class="okc-numero">0</td>
                        <td class="okc-numero">0</td>
                        <td class="okc-numero">0</td>
                        <td class="okc-numero">'.$minTa.'</td>
                        <td class="okc-numero">'.$dscTa.'</td>
                        <td class="okc-numero">'.number_format($salario, 2).'</td>
                        <td class="okc-numero">'.number_format(0, 2).'</td>
                        <td class="okc-numero">'.number_format(0, 2).'</td>
                        <td class="okc-numero">'.number_format(0, 2).'</td>
                        <td class="okc-numero">'.number_format($reint_no_deduc, 2).'</td>
                        <td class="okc-numero">'.number_format($comis, 2).'</td>
                        <td class="okc-numero">'.number_format($bnfcmp, 2).'</td>
                        <td class="okc-numero">'.number_format($viv, 2).'</td>
                        <td class="okc-numero">'.number_format(0, 2).'</td>
                        <td class="okc-numero">'.number_format($sueldo, 2).'</td>
                        <td class="okc-numero">'.number_format($asig, 2).'</td>
                        <td class="okc-numero">0</td>
                        <td class="okc-numero">0</td>
                        <td class="okc-numero">0</td>
                        <td class="okc-numero">'.number_format($reint_deduc, 2).'</td>
                        <td class="okc-numero">'.number_format($otroBon, 2).'</td>
                        <td class="okc-numero">'.number_format(0, 2).'</td>
                        <td class="okc-numero">'.number_format($vacac, 2).'</td>
                        <td class="okc-numero">'.number_format($cmps, 2).'</td>
                        <td class="okc-numero">'.number_format($ingreso, 2).'</td>
                        <td class="okc-numero">'.number_format($total_remun, 2).'</td>
                        <td class="okc-numero">'.number_format($snp, 2).'</td>
                        <td class="okc-numero">'.number_format($spp, 2).'</td>
                        <td class="okc-numero">'.number_format($prima, 2).'</td>
                        <td class="okc-numero">'.number_format($comision, 2).'</td>
                        <td class="okc-numero">'.number_format($quinta, 2).'</td>
                        <td class="okc-numero">'.number_format($aportes, 2).'</td>
                        <td class="okc-numero">'.number_format($monTa, 2).'</td>
                        <td class="okc-numero">0</td>
                        <td class="okc-numero">'.number_format($inas, 2).'</td>
                        <td class="okc-numero">0</td>
                        <td class="okc-numero">0</td>
                        <td class="okc-numero">'.number_format($descuents, 2).'</td>
                        <td class="okc-numero">'.number_format($prest, 2).'</td>
                        <td class="okc-numero">'.number_format($adela, 2).'</td>
                        <td class="okc-numero">'.number_format(0, 2).'</td>
                        <td class="okc-numero">'.number_format(0, 2).'</td>
                        <td class="okc-numero">'.number_format(0, 2).'</td>
                        <td class="okc-numero">'.number_format($otrod, 2).'</td>
                        <td class="okc-numero">'.number_format($dsctos_no, 2).'</td>
                        <td class="okc-numero">'.number_format($neto, 2).'</td>
                        <td class="okc-numero">'.number_format($essalud, 2).'</td>
                        <td class="okc-numero">'.$horas.'</td>
                        <td class="okc-numero">'.$mints.'</td>
                    </tr>';
                }
            }else if ($planiRem == 2){
                for ($i = 0; $i < $cont; $i++){
                    $a = 0;
                    
                    $nro = $i + 1;
                    $datos = $myData[$i]['datos_trabajador'];
                    $dni = $myData[$i]['dni_trabajador'];
                    $sede = $myData[$i]['sede'];
                    $area = $myData[$i]['area'];
                    $cargo = $myData[$i]['rol_trabajador'];
                    $reglab = $myData[$i]['regimen'];
                    $dias = $myData[$i]['dias_total'];
                    $sueldo = $myData[$i]['sueldo_basico'];
                    $salario = $myData[$i]['salario'];
                    $sexo = $myData[$i]['sexo_trabajador'];
                    $minTa = $myData[$i]['minutos_tardanza'];
                    $monTa = $myData[$i]['monto_tardanza'];
                    $neto = $myData[$i]['total_pago'];
                    $asig = $myData[$i]['asignacion_familiar'];
                    $horas = $myData[$i]['horas_efectivas'];
                    $mints = $myData[$i]['minutos_efectivas'];
    
                    $myIngres = $data['data'][$i]['bonificaciones'];
                    $countIngres = sizeof($myIngres);
                    $bonif = 0;
                    for ($bon = 0; $bon < $countIngres; $bon++){
                        $bonif += $myIngres[$bon]['importe'];
                    }
    
                    $myDscto = $data['data'][$i]['descuentos'];
                    $countDscto = sizeof($myDscto);
                    $dscto = 0;
                    for ($des = 0; $des < $countDscto; $des++){
                        $dscto += $myDscto[$des]['importe'];
                    }
    
                    $myIna = $data['data'][$i]['descuentos'];
                    $countIna = sizeof($myIna);
                    $inas = 0;
                    for ($ist = 0; $ist < $countIna; $ist++) { 
                        if ($myIna[$ist]['concepto'] == 'INASISTENCIAS'){
                            $inas += $myIna[$ist]['importe'];
                        }
                    }
    
                    $reint_no_deduc = $data['data'][$i]['reintegros']['reint_no_deduc'];
    
                    $html .=
                    '<tr>
                        <td>'.$nro.'</td>
                        <td>'.$datos.'</td>
                        <td>'.$dni.'</td>
                        <td>'.$sexo.'</td>
                        <td>'.$sede.'</td>
                        <td>'.$area.'</td>
                        <td>'.$cargo.'</td>
                        <td class="okc-numero">'.number_format($reglab).'</td>
                        <td class="okc-numero">'.number_format($dias).'</td>
                        <td class="okc-numero">'.number_format($sueldo, 2).'</td>
                        <td class="okc-numero">'.number_format($salario, 2).'</td>
                        <td class="okc-numero">'.number_format($bonif, 2).'</td>
                        <td class="okc-numero">'.number_format($reint_no_deduc, 2).'</td>
                        <td class="okc-numero">'.number_format($minTa, 2).'</td>
                        <td class="okc-numero">'.number_format(round($monTa), 2).'</td>
                        <td class="okc-numero">'.number_format(0).'</td>
                        <td class="okc-numero">'.number_format(round($inas), 2).'</td>
                        <td class="okc-numero">'.number_format($dscto, 2).'</td>
                        <td class="okc-numero">'.number_format(round($neto), 2).'</td>
                        <td class="okc-numero">'.$horas.'</td>
                        <td class="okc-numero">'.$mints.'</td>
                    </tr>';
                }
            }
            $html.='</tbody></table>';
        }
        $export = $html;
        return view('rrhh/reportes/Planilla_Grupal', compact('export'));
    }

    public function reporte_planilla_xls($emp, $planiRem, $mesRem, $anio, $filter, $valueGroup){
        $data = $this->cargar_remuneraciones($emp, $planiRem, $mesRem, $anio, 1, 0, 0);    
        $myData = $data['data'];
        $cont = sizeof($myData);
        $mes = $this->hallarMes($mesRem);
        $thead = '';
        $tbody = '';

        $dtaEmp = DB::table('administracion.adm_empresa')->select('codigo')->where('id_empresa', '=', $emp)->get();
        $codeEmp = $dtaEmp->first()->codigo;
        
        if ($planiRem == 1){
            $thead .=
            '<tr>
                <th rowspan="3">N°</th>
                <th rowspan="3">Apellidos y Nombres</th>
                <th rowspan="3">DNI</th>
                <th rowspan="3">SEXO</th>
                <th rowspan="3">CUSPP</th>
                <th rowspan="3">SEDE</th>
                <th rowspan="3">AREA</th>
                <th rowspan="3">CARGO</th>
                <th rowspan="3">Centro Costos</th>
                <th rowspan="3">Tipo Costo</th>
                <th rowspan="3">Rég. Laboral</th>
                <th rowspan="3">Fondo Pensión</th>
                <th rowspan="3" width="80">Días No Comp.</th>
                <th rowspan="3" width="80">Días Tele Trab.</th>
                <th rowspan="3" width="80">Días Licen.</th>
                <th rowspan="3" width="80">Días Trab.</th>
                <th rowspan="3" width="80">N° Horas Ordinarias</th>
                <th rowspan="2" colspan="3">Horas Extras</th>
                <th rowspan="3" width="80">Minutos Tardanza</th>
                <th rowspan="3" width="80">Acum. Tardanza</th>
                <th rowspan="3">Remun. Básica</th>
                <th colspan="8">CONCEPTOS BASICOS NO REMUNERATIVOS</th>
                <th colspan="10">REMUNERACION ASEGURABLE</th>
                <th rowspan="3">Total Ingresos</th>
                <th rowspan="3">Total Remun. Asegurable</th>
                <th colspan="5">APORTES</th>
                <th rowspan="3">Total Aportes</th>
                <th colspan="5">DESCUENTOS DEDUCIBLES</th>
                <th rowspan="3">Total Descto. Deduc.</th>
                <th colspan="6">OTROS DESCUENTOS NO DEDUCIBLES</th>
                <th rowspan="3">Total Descto. No Deduc.</th>
                <th rowspan="3">Neto a Pagar</th>
                <th rowspan="3">EsSalud 9%</th>
                <th colspan="2">Horas Efectivas</th>
            </tr>
            <tr>
                <th colspan="3">LIQUIDACION</th>
                <th rowspan="2" width="80">Reint. No Deduc.</th>
                <th rowspan="2" width="80">Bonif. x Ventas</th>
                <th rowspan="2" width="80">Bono x Cumplim.</th>
                <th rowspan="2" width="80">Vivienda Condicion de Trab.</th>
                <th rowspan="2" width="80">Devol. 5ta Cat.</th>
                <th rowspan="2">Remun. Principal</th>
                <th rowspan="2">Asig. Familiar</th>
                <th colspan="3">Horas Extras</th>
                <th rowspan="2">Reintegro</th>
                <th rowspan="2">Otros Ingresos</th>
                <th colspan="3">Vacaciones</th>
                <th rowspan="2">SNP 13%</th>
                <th colspan="3">SPP</th>
                <th rowspan="2">I.R. 5ta Categ.</th>
                <th rowspan="2" width="80">Tardanzas</th>
                <th rowspan="2" width="80">Permiso Sin Goce</th>
                <th rowspan="2" width="80">Inasistencia</th>
                <th rowspan="2" width="80">Falta Injustif.</th>
                <th rowspan="2" width="80">Otros Deducibles</th>
                <th rowspan="2" width="80">Préstamos</th>
                <th rowspan="2" width="80">Adelantos</th>
                <th rowspan="2" width="80">Retenc. Judicial</th>
                <th rowspan="2" width="80">Vacac. Pago x Adelant.</th>
                <th rowspan="2" width="80">Descuento Autor</th>
                <th rowspan="2" width="80">Otros</th>
                <th rowspan="2">Horas</th>
                <th rowspan="2">Minutos</th>
            </tr>
            <tr>
                <th>N° 25%</th>
                <th>N° 35%</th>
                <th>N° 100%</th>
                <th>CTS</th>
                <th>Gratif.</th>
                <th>Bonif. Extra</th>
                <th>25%</th>
                <th>35%</th>
                <th>100%</th>
                <th>Truncas</th>
                <th>Remun. Vac.</th>
                <th>Compen. Vac.</th>
                <th>Aporte</th>
                <th>Prima</th>
                <th>Comisión</th>
                
            </tr>';

            for ($i = 0; $i < $cont; $i++){
                $a = 0;
                $nro = $i + 1;
                $datos = $myData[$i]['datos_trabajador'];
                $dni = $myData[$i]['dni_trabajador'];
                $sexo = $myData[$i]['sexo_trabajador'];
                $sede = $myData[$i]['sede'];
                $area = $myData[$i]['area'];
                $cargo = $myData[$i]['rol_trabajador'];
                $pension = $myData[$i]['fondo_pension'];
                $ini_cont = $myData[$i]['fecha_contrato_ini'];
                $cussp = $myData[$i]['numero_cussp'];
                $asig = $myData[$i]['asignacion_familiar'];
                $horas = $myData[$i]['horas_efectivas'];
                $mints = $myData[$i]['minutos_efectivas'];

                $reglab = $myData[$i]['regimen'];
                $dias = $myData[$i]['dias_total'];
                $diat = $myData[$i]['dias_teletrabajo'];
                $dial = $myData[$i]['dias_licencia'];
                $dia_lab = $myData[$i]['dias_laborados'];
                $cc = $sede.' - '.$area;
                $tipo_cc = $myData[$i]['tipo_centro_costo'];
                $salario = $myData[$i]['salario'];
                $sueldo = $myData[$i]['sueldo_basico'];
                $total_remun = $myData[$i]['total_remun'];

                $myPensi = $data['data'][$i]['aporte_pension'];
                $tpPensi = $myPensi['tipo'];
                if ($tpPensi == 'onp') {
                    $snp = $myPensi['snp'];
                    $spp = 0;
                    $prima = 0;
                    $comision = 0;
                }else{
                    $snp = 0;
                    $spp = $myPensi['spp'];
                    $prima = $myPensi['prima'];
                    $comision = $myPensi['comision'];
                }

                $reint_deduc = $data['data'][$i]['reintegros']['reint_deduc'];
                $reint_no_deduc = $data['data'][$i]['reintegros']['reint_no_deduc'];

                $myRetenc = $data['data'][$i]['retenciones'];
                $countRetenc = sizeof($myRetenc);
                $quinta = 0;
                for ($ret = 0; $ret < $countRetenc; $ret++){ 
                    if ($myRetenc[$ret]['concepto'] == '5TA CATEGORIA'){
                        $quinta = $myRetenc[$ret]['importe'];
                    }
                }

                $myAport = $data['data'][$i]['aporte_empleador'];
                $countAport = sizeof($myAport);
                $essalud = 0;
                for ($apt = 0; $apt < $countAport; $apt++){ 
                    if ($myAport[$apt]['concepto'] == 'ESSALUD (REGULAR CBSSP AGRAR/AC) TRAB'){
                        $essalud = $myAport[$apt]['importe'];
                    }
                }

                $myPrest = $data['data'][$i]['descuentos'];
                $countPrest = sizeof($myPrest);
                $prest = 0;
                $otrod = 0;
                $adela = 0;
                for ($ptm = 0; $ptm < $countPrest; $ptm++) { 
                    if ($myPrest[$ptm]['concepto'] == 'PRESTAMOS'){
                        $prest += $myPrest[$ptm]['importe'];
                    }elseif($myPrest[$ptm]['concepto'] == 'OTROS DESCUENTOS NO DEDUC. DE BASE IMPONIBLE'){
                        $otrod += $myPrest[$ptm]['importe'];
                    }elseif($myPrest[$ptm]['concepto'] == 'ADELANTOS'){
                        $adela += $myPrest[$ptm]['importe'];
                    }
                }

                $myIna = $data['data'][$i]['descuentos'];
                $countIna = sizeof($myIna);
                $inas = 0;
                for ($ist = 0; $ist < $countIna; $ist++) { 
                    if ($myIna[$ist]['concepto'] == 'INASISTENCIAS'){
                        $inas += $myIna[$ist]['importe'];
                    }
                }
                
                $myBonif = $data['data'][$i]['bonificaciones'];
                $countBonif = sizeof($myBonif);
                $bnfcmp = 0;
                $comis = 0;
                $viv = 0;
                $cmps = 0;
                $otroBon = 0;
                for ($bnf = 0; $bnf < $countBonif; $bnf++) { 
                    if ($myBonif[$bnf]['concepto'] == 'COMISION'){
                        $comis += $myBonif[$bnf]['importe'];
                    }elseif ($myBonif[$bnf]['concepto'] == 'VIVIENDA CONDICION DE TRABAJO'){
                        $viv += $myBonif[$bnf]['importe'];
                    }elseif($myBonif[$bnf]['concepto'] == 'COMPENSACION VACACIONAL'){
                        $cmps += $myBonif[$bnf]['importe'];
                    }elseif ($myBonif[$bnf]['concepto'] == 'BONO POR CUMPLIMIENTO'){
                        $bnfcmp += $myBonif[$bnf]['importe'];
                    }else{
                        $otroBon += $myBonif[$bnf]['importe'];
                    }
                }

                $minTa = $myData[$i]['minutos_tardanza'];
                $dscTa = $myData[$i]['descuento_tardanza'];
                $monTa = $myData[$i]['monto_tardanza'];
                $ingreso = $myData[$i]['total_ingreso'];
                $aportes = $snp + $spp + $prima + $comision + $quinta;
                $descuents = $monTa + $inas;
                $dsctos_no = $prest + $otrod + $adela;
                $neto = $myData[$i]['total_pago'];
                $vacac = $myData[$i]['sueldo_vacaciones'];

                $tbody .=
                '<tr>
                    <td class="okc-order">'.$nro.'</td>
                    <td>'.$datos.'</td>
                    <td>'.$dni.'</td>
                    <td>'.$sexo.'</td>
                    <td>'.$cussp.'</td>
                    <td>'.$sede.'</td>
                    <td>'.$area.'</td>
                    <td>'.$cargo.'</td>
                    <td>'.$cc.'</td>
                    <td>'.$tipo_cc.'</td>
                    <td>'.$reglab.'</td>
                    <td>'.$pension.'</td>
                    <td class="okc-numero">'.number_format($dias).'</td>
                    <td class="okc-numero">'.number_format($diat).'</td>
                    <td class="okc-numero">'.number_format($dial).'</td>
                    <td class="okc-numero">'.number_format($dia_lab).'</td>
                    <td class="okc-numero">'.$horas.'</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">'.$minTa.'</td>
                    <td class="okc-numero">'.$dscTa.'</td>
                    <td class="okc-numero">'.number_format($salario, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format($reint_no_deduc, 2).'</td>
                    <td class="okc-numero">'.number_format($comis, 2).'</td>
                    <td class="okc-numero">'.number_format($bnfcmp, 2).'</td>
                    <td class="okc-numero">'.number_format($viv, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format($sueldo, 2).'</td>
                    <td class="okc-numero">'.number_format($asig, 2).'</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">'.number_format($reint_deduc, 2).'</td>
                    <td class="okc-numero">'.number_format($otroBon, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format($vacac, 2).'</td>
                    <td class="okc-numero">'.number_format($cmps, 2).'</td>
                    <td class="okc-numero">'.number_format($ingreso, 2).'</td>
                    <td class="okc-numero">'.number_format($total_remun, 2).'</td>
                    <td class="okc-numero">'.number_format($snp, 2).'</td>
                    <td class="okc-numero">'.number_format($spp, 2).'</td>
                    <td class="okc-numero">'.number_format($prima, 2).'</td>
                    <td class="okc-numero">'.number_format($comision, 2).'</td>
                    <td class="okc-numero">'.number_format($quinta, 2).'</td>
                    <td class="okc-numero">'.number_format($aportes, 2).'</td>
                    <td class="okc-numero">'.number_format($monTa, 2).'</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">'.number_format($inas, 2).'</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">'.number_format($descuents, 2).'</td>
                    <td class="okc-numero">'.number_format($prest, 2).'</td>
                    <td class="okc-numero">'.number_format($adela, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format($otrod, 2).'</td>
                    <td class="okc-numero">'.number_format($dsctos_no, 2).'</td>
                    <td class="okc-numero">'.number_format($neto, 2).'</td>
                    <td class="okc-numero">'.number_format($essalud, 2).'</td>
                    <td class="okc-numero">'.$horas.'</td>
                    <td class="okc-numero">'.$mints.'</td>
                </tr>';
            }
        }else if ($planiRem == 2){
            $thead .=
            '<tr>
                <th>N°</th>
                <th>Apellidos y Nombres</th>
                <th>DNI</th>
                <th>Sexo</th>
                <th>Sede</th>
                <th>Area</th>
                <th>Cargo</th>
                <th>Rég. Laboral</th>
                <th>Días Comp.</th>
                <th>Días Tele Trab.</th>
                <th>Días Licen.</th>
                <th>Días Trab.</th>
                <th>Sueldo Básico</th>
                <th>Remun. Principal</th>
                <th>Otros Ingresos</th>
                <th>Reintegro</th>
                <th>Minutos Tardanza</th>
                <th>Monto Tardanza</th>
                <th>Inasistencia</th>
                <th>Monto Inasistencia</th>
                <th>Otros Descuentos</th>
                <th>Sueldo Neto</th>
                <th>Horas Efect.</th>
                <th>Minutos Efect.</th>
            </tr>';
            for ($i = 0; $i < $cont; $i++){
                $a = 0;
                
                $nro = $i + 1;
                $datos = $myData[$i]['datos_trabajador'];
                $dni = $myData[$i]['dni_trabajador'];
                $sede = $myData[$i]['sede'];
                $area = $myData[$i]['area'];
                $cargo = $myData[$i]['rol_trabajador'];
                $reglab = $myData[$i]['regimen'];
                $dias = $myData[$i]['dias_total'];
                $diat = $myData[$i]['dias_teletrabajo'];
                $dial = $myData[$i]['dias_licencia'];
                $dia_lab = $myData[$i]['dias_laborados'];
                $sueldo = $myData[$i]['sueldo_basico'];
                $salario = $myData[$i]['salario'];
                $sexo = $myData[$i]['sexo_trabajador'];
                $minTa = $myData[$i]['minutos_tardanza'];
                $monTa = $myData[$i]['monto_tardanza'];
                $neto = $myData[$i]['total_pago'];
                $asig = $myData[$i]['asignacion_familiar'];
                $horas = $myData[$i]['horas_efectivas'];
                $mints = $myData[$i]['minutos_efectivas'];

                $myIngres = $data['data'][$i]['bonificaciones'];
                $countIngres = sizeof($myIngres);
                $bonif = 0;
                for ($bon = 0; $bon < $countIngres; $bon++){
                    $bonif += $myIngres[$bon]['importe'];
                }

                $myDscto = $data['data'][$i]['descuentos'];
                $countDscto = sizeof($myDscto);
                $dscto = 0;
                for ($des = 0; $des < $countDscto; $des++){
                    $dscto += $myDscto[$des]['importe'];
                }

                $myIna = $data['data'][$i]['descuentos'];
                $countIna = sizeof($myIna);
                $inas = 0;
                for ($ist = 0; $ist < $countIna; $ist++) { 
                    if ($myIna[$ist]['concepto'] == 'INASISTENCIAS'){
                        $inas += $myIna[$ist]['importe'];
                    }
                }

                $reint_no_deduc = $data['data'][$i]['reintegros']['reint_no_deduc'];

                $tbody .=
                '<tr>
                    <td>'.$nro.'</td>
                    <td>'.$datos.'</td>
                    <td>'.$dni.'</td>
                    <td>'.$sexo.'</td>
                    <td>'.$sede.'</td>
                    <td>'.$area.'</td>
                    <td>'.$cargo.'</td>
                    <td class="okc-numero">'.number_format($reglab).'</td>
                    <td class="okc-numero">'.number_format($dias).'</td>
                    <td class="okc-numero">'.number_format($diat).'</td>
                    <td class="okc-numero">'.number_format($dial).'</td>
                    <td class="okc-numero">'.number_format($dia_lab).'</td>
                    <td class="okc-numero">'.number_format($sueldo, 2).'</td>
                    <td class="okc-numero">'.number_format($salario, 2).'</td>
                    <td class="okc-numero">'.number_format($bonif, 2).'</td>
                    <td class="okc-numero">'.number_format($reint_no_deduc, 2).'</td>
                    <td class="okc-numero">'.number_format($minTa, 2).'</td>
                    <td class="okc-numero">'.number_format(round($monTa), 2).'</td>
                    <td class="okc-numero">'.number_format(0).'</td>
                    <td class="okc-numero">'.number_format(round($inas), 2).'</td>
                    <td class="okc-numero">'.number_format($dscto, 2).'</td>
                    <td class="okc-numero">'.number_format(round($neto), 2).'</td>
                    <td class="okc-numero">'.$horas.'</td>
                    <td class="okc-numero">'.$mints.'</td>
                </tr>';
            }
        }

        $headExport = $thead;
        $dataExport = $tbody;
        $perExport = 'PERIODO '.$mes.'-'.$anio;
        $companyExport = $codeEmp;
        return view('rrhh/reportes/Planilla_Remuneracion', compact('headExport', 'dataExport', 'perExport', 'companyExport'));
    }

    public function reporte_planilla_spcc_xls($emp, $planiRem, $mesRem, $anio){
        $data = $this->remuneracion_spcc($emp, $planiRem, $mesRem, $anio);    
        $myData = $data['data'];
        $cont = sizeof($myData);
        $mes = $this->hallarMes($mesRem);
        $thead = '';
        $tbody = '';

        $dtaEmp = DB::table('administracion.adm_empresa')->select('codigo')->where('id_empresa', '=', $emp)->get();
        $codeEmp = $dtaEmp->first()->codigo;
        
        if ($planiRem == 1){
            $thead .=
            '<tr>
                <th rowspan="3">N°</th>
                <th rowspan="3">Apellidos y Nombres</th>
                <th rowspan="3">DNI</th>
                <th rowspan="3">SEXO</th>
                <th rowspan="3">CUSPP</th>
                <th rowspan="3">SEDE</th>
                <th rowspan="3">AREA</th>
                <th rowspan="3">CARGO</th>
                <th rowspan="3">Centro Costos</th>
                <th rowspan="3">Tipo Costo</th>
                <th rowspan="3">Rég. Laboral</th>
                <th rowspan="3">Fondo Pensión</th>
                <th rowspan="3" width="80">Días No Comp.</th>
                <th rowspan="3" width="80">N° Horas Ordinarias</th>
                <th rowspan="2" colspan="3">Horas Extras</th>
                <th rowspan="3" width="80">Minutos Tardanza</th>
                <th rowspan="3" width="80">Acum. Tardanza</th>
                <th rowspan="3">Remun. Básica</th>
                <th colspan="8">CONCEPTOS BASICOS NO REMUNERATIVOS</th>
                <th colspan="10">REMUNERACION ASEGURABLE</th>
                <th rowspan="3">Total Ingresos</th>
                <th rowspan="3">Total Remun. Asegurable</th>
                <th colspan="5">APORTES</th>
                <th rowspan="3">Total Aportes</th>
                <th colspan="5">DESCUENTOS DEDUCIBLES</th>
                <th rowspan="3">Total Descto. Deduc.</th>
                <th colspan="6">OTROS DESCUENTOS NO DEDUCIBLES</th>
                <th rowspan="3">Total Descto. No Deduc.</th>
                <th rowspan="3">Neto a Pagar</th>
                <th rowspan="3">EsSalud 9%</th>
                <th colspan="2">Horas Efectivas</th>
            </tr>
            <tr>
                <th colspan="3">LIQUIDACION</th>
                <th rowspan="2" width="80">Reint. No Deduc.</th>
                <th rowspan="2" width="80">Bonif. x Ventas</th>
                <th rowspan="2" width="80">Bono x Cumplim.</th>
                <th rowspan="2" width="80">Vivienda Condicion de Trab.</th>
                <th rowspan="2" width="80">Devol. 5ta Cat.</th>
                <th rowspan="2">Remun. Principal</th>
                <th rowspan="2">Asig. Familiar</th>
                <th colspan="3">Horas Extras</th>
                <th rowspan="2">Reintegro</th>
                <th rowspan="2">Otros Ingresos</th>
                <th colspan="3">Vacaciones</th>
                <th rowspan="2">SNP 13%</th>
                <th colspan="3">SPP</th>
                <th rowspan="2">I.R. 5ta Categ.</th>
                <th rowspan="2" width="80">Tardanzas</th>
                <th rowspan="2" width="80">Permiso Sin Goce</th>
                <th rowspan="2" width="80">Inasistencia</th>
                <th rowspan="2" width="80">Falta Injustif.</th>
                <th rowspan="2" width="80">Otros Deducibles</th>
                <th rowspan="2" width="80">Préstamos</th>
                <th rowspan="2" width="80">Adelantos</th>
                <th rowspan="2" width="80">Retenc. Judicial</th>
                <th rowspan="2" width="80">Vacac. Pago x Adelant.</th>
                <th rowspan="2" width="80">Descuento Autor</th>
                <th rowspan="2" width="80">Otros</th>
                <th rowspan="2">Horas</th>
                <th rowspan="2">Minutos</th>
            </tr>
            <tr>
                <th>N° 25%</th>
                <th>N° 35%</th>
                <th>N° 100%</th>
                <th>CTS</th>
                <th>Gratif.</th>
                <th>Bonif. Extra</th>
                <th>25%</th>
                <th>35%</th>
                <th>100%</th>
                <th>Truncas</th>
                <th>Remun. Vac.</th>
                <th>Compen. Vac.</th>
                <th>Aporte</th>
                <th>Prima</th>
                <th>Comisión</th>
                
            </tr>';

            for ($i = 0; $i < $cont; $i++){
                $a = 0;
                $nro = $i + 1;
                $datos = $myData[$i]['datos_trabajador'];
                $dni = $myData[$i]['dni_trabajador'];
                $sexo = $myData[$i]['sexo_trabajador'];
                $sede = $myData[$i]['sede'];
                $area = $myData[$i]['area'];
                $cargo = $myData[$i]['rol_trabajador'];
                $pension = $myData[$i]['fondo_pension'];
                $ini_cont = $myData[$i]['fecha_contrato_ini'];
                $cussp = $myData[$i]['numero_cussp'];
                $asig = $myData[$i]['asignacion_familiar'];
                $horas = $myData[$i]['horas_efectivas'];
                $mints = $myData[$i]['minutos_efectivas'];

                $reglab = $myData[$i]['regimen'];
                $dias = $myData[$i]['dias_total'];
                $cc = $sede.' - '.$area;
                $tipo_cc = $myData[$i]['tipo_centro_costo'];
                $salario = $myData[$i]['salario'];
                $sueldo = $myData[$i]['sueldo_basico'];
                $total_remun = $myData[$i]['total_remun'];

                $myPensi = $data['data'][$i]['aporte_pension'];
                $tpPensi = $myPensi['tipo'];
                if ($tpPensi == 'onp') {
                    $snp = $myPensi['snp'];
                    $spp = 0;
                    $prima = 0;
                    $comision = 0;
                }else{
                    $snp = 0;
                    $spp = $myPensi['spp'];
                    $prima = $myPensi['prima'];
                    $comision = $myPensi['comision'];
                }

                $reint_deduc = $data['data'][$i]['reintegros']['reint_deduc'];
                $reint_no_deduc = $data['data'][$i]['reintegros']['reint_no_deduc'];

                $myRetenc = $data['data'][$i]['retenciones'];
                $countRetenc = sizeof($myRetenc);
                $quinta = 0;
                for ($ret = 0; $ret < $countRetenc; $ret++){ 
                    if ($myRetenc[$ret]['concepto'] == '5TA CATEGORIA'){
                        $quinta = $myRetenc[$ret]['importe'];
                    }
                }

                $myAport = $data['data'][$i]['aporte_empleador'];
                $countAport = sizeof($myAport);
                $essalud = 0;
                for ($apt = 0; $apt < $countAport; $apt++){ 
                    if ($myAport[$apt]['concepto'] == 'ESSALUD (REGULAR CBSSP AGRAR/AC) TRAB'){
                        $essalud = $myAport[$apt]['importe'];
                    }
                }

                $myPrest = $data['data'][$i]['descuentos'];
                $countPrest = sizeof($myPrest);
                $prest = 0;
                $otrod = 0;
                $adela = 0;
                for ($ptm = 0; $ptm < $countPrest; $ptm++) { 
                    if ($myPrest[$ptm]['concepto'] == 'PRESTAMOS'){
                        $prest += $myPrest[$ptm]['importe'];
                    }elseif($myPrest[$ptm]['concepto'] == 'OTROS DESCUENTOS NO DEDUC. DE BASE IMPONIBLE'){
                        $otrod += $myPrest[$ptm]['importe'];
                    }elseif($myPrest[$ptm]['concepto'] == 'ADELANTOS'){
                        $adela += $myPrest[$ptm]['importe'];
                    }
                }

                $myIna = $data['data'][$i]['descuentos'];
                $countIna = sizeof($myIna);
                $inas = 0;
                for ($ist = 0; $ist < $countIna; $ist++) { 
                    if ($myIna[$ist]['concepto'] == 'INASISTENCIAS'){
                        $inas += $myIna[$ist]['importe'];
                    }
                }
                
                $myBonif = $data['data'][$i]['bonificaciones'];
                $countBonif = sizeof($myBonif);
                $bnfcmp = 0;
                $comis = 0;
                $viv = 0;
                $cmps = 0;
                $otroBon = 0;
                for ($bnf = 0; $bnf < $countBonif; $bnf++) { 
                    if ($myBonif[$bnf]['concepto'] == 'COMISION'){
                        $comis += $myBonif[$bnf]['importe'];
                    }elseif ($myBonif[$bnf]['concepto'] == 'VIVIENDA CONDICION DE TRABAJO'){
                        $viv += $myBonif[$bnf]['importe'];
                    }elseif($myBonif[$bnf]['concepto'] == 'COMPENSACION VACACIONAL'){
                        $cmps += $myBonif[$bnf]['importe'];
                    }elseif ($myBonif[$bnf]['concepto'] == 'BONO POR CUMPLIMIENTO'){
                        $bnfcmp += $myBonif[$bnf]['importe'];
                    }else{
                        $otroBon += $myBonif[$bnf]['importe'];
                    }
                }

                $minTa = $myData[$i]['minutos_tardanza'];
                $dscTa = $myData[$i]['descuento_tardanza'];
                $monTa = $myData[$i]['monto_tardanza'];
                $ingreso = $myData[$i]['total_ingreso'];
                $aportes = $snp + $spp + $prima + $comision + $quinta;
                $descuents = $monTa + $inas;
                $dsctos_no = $prest + $otrod + $adela;
                $neto = $myData[$i]['total_pago'];
                $vacac = $myData[$i]['sueldo_vacaciones'];

                $tbody .=
                '<tr>
                    <td class="okc-order">'.$nro.'</td>
                    <td>'.$datos.'</td>
                    <td>'.$dni.'</td>
                    <td>'.$sexo.'</td>
                    <td>'.$cussp.'</td>
                    <td>'.$sede.'</td>
                    <td>'.$area.'</td>
                    <td>'.$cargo.'</td>
                    <td>'.$cc.'</td>
                    <td>'.$tipo_cc.'</td>
                    <td>'.$reglab.'</td>
                    <td>'.$pension.'</td>
                    <td>'.$dias.'</td>
                    <td class="okc-numero">'.$horas.'</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">'.$minTa.'</td>
                    <td class="okc-numero">'.$dscTa.'</td>
                    <td class="okc-numero">'.number_format($salario, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format($reint_no_deduc, 2).'</td>
                    <td class="okc-numero">'.number_format($comis, 2).'</td>
                    <td class="okc-numero">'.number_format($bnfcmp, 2).'</td>
                    <td class="okc-numero">'.number_format($viv, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format($sueldo, 2).'</td>
                    <td class="okc-numero">'.number_format($asig, 2).'</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">'.number_format($reint_deduc, 2).'</td>
                    <td class="okc-numero">'.number_format($otroBon, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format($vacac, 2).'</td>
                    <td class="okc-numero">'.number_format($cmps, 2).'</td>
                    <td class="okc-numero">'.number_format($ingreso, 2).'</td>
                    <td class="okc-numero">'.number_format($total_remun, 2).'</td>
                    <td class="okc-numero">'.number_format($snp, 2).'</td>
                    <td class="okc-numero">'.number_format($spp, 2).'</td>
                    <td class="okc-numero">'.number_format($prima, 2).'</td>
                    <td class="okc-numero">'.number_format($comision, 2).'</td>
                    <td class="okc-numero">'.number_format($quinta, 2).'</td>
                    <td class="okc-numero">'.number_format($aportes, 2).'</td>
                    <td class="okc-numero">'.number_format($monTa, 2).'</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">'.number_format($inas, 2).'</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">0</td>
                    <td class="okc-numero">'.number_format($descuents, 2).'</td>
                    <td class="okc-numero">'.number_format($prest, 2).'</td>
                    <td class="okc-numero">'.number_format($adela, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format(0, 2).'</td>
                    <td class="okc-numero">'.number_format($otrod, 2).'</td>
                    <td class="okc-numero">'.number_format($dsctos_no, 2).'</td>
                    <td class="okc-numero">'.number_format($neto, 2).'</td>
                    <td class="okc-numero">'.number_format($essalud, 2).'</td>
                    <td class="okc-numero">'.$horas.'</td>
                    <td class="okc-numero">'.$mints.'</td>
                </tr>';
            }
        }else if ($planiRem == 2){
            $thead .=
            '<tr>
                <th>N°</th>
                <th>Apellidos y Nombres</th>
                <th>DNI</th>
                <th>Sexo</th>
                <th>Sede</th>
                <th>Area</th>
                <th>Cargo</th>
                <th>Rég. Laboral</th>
                <th>Días Comp.</th>
                <th>Sueldo Básico</th>
                <th>Remun. Principal</th>
                <th>Otros Ingresos</th>
                <th>Reintegro</th>
                <th>Minutos Tardanza</th>
                <th>Monto Tardanza</th>
                <th>Inasistencia</th>
                <th>Monto Inasistencia</th>
                <th>Otros Descuentos</th>
                <th>Sueldo Neto</th>
                <th>Horas Efect.</th>
                <th>Minutos Efect.</th>
            </tr>';
            for ($i = 0; $i < $cont; $i++){
                $a = 0;
                
                $nro = $i + 1;
                $datos = $myData[$i]['datos_trabajador'];
                $dni = $myData[$i]['dni_trabajador'];
                $sede = $myData[$i]['sede'];
                $area = $myData[$i]['area'];
                $cargo = $myData[$i]['rol_trabajador'];
                $reglab = $myData[$i]['regimen'];
                $dias = $myData[$i]['dias_total'];
                $sueldo = $myData[$i]['sueldo_basico'];
                $salario = $myData[$i]['salario'];
                $sexo = $myData[$i]['sexo_trabajador'];
                $minTa = $myData[$i]['minutos_tardanza'];
                $monTa = $myData[$i]['monto_tardanza'];
                $neto = $myData[$i]['total_pago'];
                $asig = $myData[$i]['asignacion_familiar'];
                $horas = $myData[$i]['horas_efectivas'];
                $mints = $myData[$i]['minutos_efectivas'];

                $myIngres = $data['data'][$i]['bonificaciones'];
                $countIngres = sizeof($myIngres);
                $bonif = 0;
                for ($bon = 0; $bon < $countIngres; $bon++){
                    $bonif += $myIngres[$bon]['importe'];
                }

                $myDscto = $data['data'][$i]['descuentos'];
                $countDscto = sizeof($myDscto);
                $dscto = 0;
                for ($des = 0; $des < $countDscto; $des++){
                    $dscto += $myDscto[$des]['importe'];
                }

                $myIna = $data['data'][$i]['descuentos'];
                $countIna = sizeof($myIna);
                $inas = 0;
                for ($ist = 0; $ist < $countIna; $ist++) { 
                    if ($myIna[$ist]['concepto'] == 'INASISTENCIAS'){
                        $inas += $myIna[$ist]['importe'];
                    }
                }

                $reint_no_deduc = $data['data'][$i]['reintegros']['reint_no_deduc'];

                $tbody .=
                '<tr>
                    <td>'.$nro.'</td>
                    <td>'.$datos.'</td>
                    <td>'.$dni.'</td>
                    <td>'.$sexo.'</td>
                    <td>'.$sede.'</td>
                    <td>'.$area.'</td>
                    <td>'.$cargo.'</td>
                    <td class="okc-numero">'.number_format($reglab).'</td>
                    <td class="okc-numero">'.number_format($dias).'</td>
                    <td class="okc-numero">'.number_format($sueldo, 2).'</td>
                    <td class="okc-numero">'.number_format($salario, 2).'</td>
                    <td class="okc-numero">'.number_format($bonif, 2).'</td>
                    <td class="okc-numero">'.number_format($reint_no_deduc, 2).'</td>
                    <td class="okc-numero">'.number_format($minTa, 2).'</td>
                    <td class="okc-numero">'.number_format(round($monTa), 2).'</td>
                    <td class="okc-numero">'.number_format(0).'</td>
                    <td class="okc-numero">'.number_format(round($inas), 2).'</td>
                    <td class="okc-numero">'.number_format($dscto, 2).'</td>
                    <td class="okc-numero">'.number_format(round($neto), 2).'</td>
                    <td class="okc-numero">'.$horas.'</td>
                    <td class="okc-numero">'.$mints.'</td>
                </tr>';
            }
        }

        $headExport = $thead;
        $dataExport = $tbody;
        $perExport = 'PERIODO '.$mes.'-'.$anio;
        $companyExport = $codeEmp;
        return view('rrhh/reportes/Planilla_Remuneracion', compact('headExport', 'dataExport', 'perExport', 'companyExport'));
    }

    /* REPORTE */
    public function reporte_tardanza($from, $to, $empresa, $sede){
        $data = $this->tardanza_trabajador($from, $to, $empresa, $sede, 'tareo', 0);
        return view('rrhh/reportes/downloadExcelAsis', compact('data'));
    }

    public function cargar_asistencia($empresa, $sede, $tipo, $fecha_ini, $fecha_fin){
        $data = $this->tardanza_final_trabajador($fecha_ini, $fecha_fin, $empresa, $sede, $tipo);
        echo json_encode($data);
    }

    public function generar_vacaciones_pdf($id){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->exportVacaciones($id))->setPaper('A5', 'portrait');
        return $pdf->stream('reporte.pdf');
    }

    public function exportVacaciones($id){
        $sql = DB::table('rrhh.rrhh_vacac')->where('id_vacaciones', $id)->get();

        $html = 
        '<html>
            <head>
                <style type="text/css">
                    @page{
                        margin: 10px;
                    }
                    *{
                        font-size: 11px;
                    }
                    body{
                        background-color: #fff;
                        font-family: "Helvetica";
                        box-sizing: border-box;
                    }
                    table{
                        width: 100%;
                        border-spacing: 0;
                        border-collapse: collapse;
                        font-size: 10px;
                    }
                    table tr th,
                    table tr td{
                        padding: 3px;
                    }
                    h5{
                        margin-bottom: 2px;
                    }
                    .tabla tr th,
                    .tabla tr td{
                        border: 1px solid #ccc;
                    }
                    .title{
                        font-weight: bold;
                        margin: 0;
                    }
                    .center{
                        text-align: center;
                    }
                    .firmas tr td{
                        width: 33.33%;
                    }
                    .firmas tr td div{
                        width: 100%;
                        height: 50px;
                    }
                </style>
            </head>
            <body>
            <div style="border: 1px solid #ccc; padding: 6px;">';
        
        $cargo = '';
        $dni = '';
        foreach ($sql as $row){
            $id_trabajador = $row->id_trabajador;
            $fecha_inicio = date('d/m/Y', strtotime($row->fecha_inicio));;
            $fecha_fin = date('d/m/Y', strtotime($row->fecha_fin));
            $fecha_retorno = date('d/m/Y', strtotime($row->fecha_retorno));
            $dias = $row->dias;
            $periodo = $row->concepto;
            $fecha = date('d/m/Y', strtotime($row->fecha_registro));

            $concepto = explode('/', $periodo);
            $pe_ini = $concepto[0];
            $pe_fin = $concepto[1];

            $trabajador = $this->buscar_trabajador_id($id_trabajador);
            $dni = $this->buscar_dni($id_trabajador);
            $cargo = $this->buscar_cargo($id_trabajador);
        }

        $html .=
        '<table>
            <tbody>
                <tr>
                    <td width="70">
                        <p class="title">OK COMPUTER EIRL</p>
                        <p class="title">RUC: 20519865476</p>
                    </td>
                    <td class="title center">PAPELETA DE VACACIONES</td>
                    <td width="70" align="center">
                        <img src="./images/LogoSlogan-80.png" alt="Logo" height="30px">
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table class="tabla">
           <tbody>
                <tr>
                    <th colspan="4">Apellidos y Nombres</th>
                </tr>
                <tr>
                    <td colspan="4">'.$trabajador.'</td>
                </tr>
                <tr>
                    <th colspan="2" width="100">DNI</th>
                    <th colspan="2">Cargo</th>
                </tr>
                <tr>
                    <td colspan="2">'.$dni.'</td>
                    <td rowspan="3" colspan="2">'.$cargo.'</td>
                </tr>
                <tr>
                    <th colspan="2">Fecha</th>
                </tr>
                <tr>
                    <td colspan="2">'.$fecha.'</td>
                </tr>
           </tbody>
        </table>
        <h5>DESCANSO VACACIONAL</h5>
        <table class="tabla">
            <tbody>
                <tr>
                    <th align="center">Fecha de inicio</th>
                    <th align="center">Fecha de termino</th>
                    <th align="center">Fecha de retorno</th>
                    <th align="center">Días de goce vacacional</th>
                </tr>
                <tr>
                    <td align="center">'.$fecha_inicio.'</td>
                    <td align="center">'.$fecha_fin.'</td>
                    <td align="center">'.$fecha_retorno.'</td>
                    <td align="center">'.$dias.'</td>
                </tr>
            </tbody>
        </table>
        <h5>CORRESPONDIENTE AL PERIODO LABORADO</h5>
        <table class="tabla">
            <tbody>
                <tr>
                    <th align="center">Desde</th>
                    <th align="center">Hasta</th>
                </tr>
                <tr>
                    <td align="center">'.$pe_ini.'</td>
                    <td align="center">'.$pe_fin.'</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Importante:</strong> La salida de vacaciones sin la presente constancia autorizada será considerada como inasitencia injustificada.
                    </td>
                </tr>
            </tbody>
        </table>
        <h5>FIRMAS</h5>
        <table class="tabla firmas">
            <tbody>
                <tr>
                    <td><div></div></td>
                    <td><div>Jefe de Area</div></td>
                    <td><div>Solicitante</div></td>
                </tr>
            </tbody>
        </table>';

        $html .=
        '</div>
        </body>
        </html>';
        return $html;
    }

    public function buscar_postulantes_reporte($filtro, $desc){
        ////////////////////////////
        // reporte excel que muestre empresa/funciones/cargo ocupado
        ////////////////////////////
        
        $output = array();
        if ($filtro == 1) {
            $sql = DB::table('rrhh.rrhh_frm_acad')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_frm_acad.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('rrhh.rrhh_carrera', 'rrhh_carrera.id_carrera', '=', 'rrhh_frm_acad.id_carrera')
                ->join('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'rrhh_postu.id_pais')
                ->select('rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno',
                        'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_postu.ubigeo', 'rrhh_postu.id_postulante', 'sis_pais.descripcion AS pais',
                        'rrhh_carrera.descripcion AS carrera')
                ->where('rrhh_carrera.descripcion', 'ilike', '%'.$desc.'%')->distinct()->get();

            foreach ($sql as $row){
                $ubigeo = $this->buscarUbigeo($row->ubigeo, 'dpto');
                $lugar = $row->pais.' - '.$ubigeo;
                $output[] = array(
                    'id_postulante'     => $row->id_postulante,
                    'nro_documento'     => $row->nro_documento,
                    'nombres'           => $row->nombres,
                    'apellido_paterno'  => $row->apellido_paterno,
                    'apellido_materno'  => $row->apellido_materno,
                    'telefono'          => $row->telefono,
                    'correo'            => $row->correo,
                    'carrera'       => $row->carrera,
                    'ubigeo'            => $lugar
                );
            }
        }else if($filtro == 2){
            $sql = DB::table('rrhh.rrhh_frm_acad')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_frm_acad.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('rrhh.rrhh_niv_estud', 'rrhh_niv_estud.id_nivel_estudio', '=', 'rrhh_frm_acad.id_nivel_estudio')
                ->join('rrhh.rrhh_carrera', 'rrhh_carrera.id_carrera', '=', 'rrhh_frm_acad.id_carrera')
                ->join('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'rrhh_postu.id_pais')
                ->select('rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno',
                        'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_postu.ubigeo', 'rrhh_postu.id_postulante', 'sis_pais.descripcion AS pais',
                        'rrhh_niv_estud.descripcion AS nivel_estudio')
                ->where('rrhh_niv_estud.descripcion', 'ilike', '%'.$desc.'%')->distinct()->get();
            
            foreach ($sql as $row){
                $ubigeo = $this->buscarUbigeo($row->ubigeo, 'dpto');
                $lugar = $row->pais.' - '.$ubigeo;
                $output[] = array(
                    'id_postulante'     => $row->id_postulante,
                    'nro_documento'     => $row->nro_documento,
                    'nombres'           => $row->nombres,
                    'apellido_paterno'  => $row->apellido_paterno,
                    'apellido_materno'  => $row->apellido_materno,
                    'telefono'          => $row->telefono,
                    'correo'            => $row->correo,
                    'nivel_estudio'     => $row->nivel_estudio,
                    'ubigeo'            => $lugar
                );
            }
        }else if($filtro == 3){
            $sql = DB::table('rrhh.rrhh_frm_acad')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_frm_acad.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('rrhh.rrhh_niv_estud', 'rrhh_niv_estud.id_nivel_estudio', '=', 'rrhh_frm_acad.id_nivel_estudio')
                ->join('rrhh.rrhh_carrera', 'rrhh_carrera.id_carrera', '=', 'rrhh_frm_acad.id_carrera')
                ->join('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'rrhh_postu.id_pais')
                ->select('rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno',
                        'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_postu.ubigeo', 'rrhh_postu.id_postulante', 'sis_pais.descripcion AS pais',
                        'rrhh_frm_acad.nombre_institucion AS institucion')
                ->where('rrhh_frm_acad.nombre_institucion', 'ilike', '%'.$desc.'%')->distinct()->get();
            
            foreach ($sql as $row){
                $ubigeo = $this->buscarUbigeo($row->ubigeo, 'dpto');
                $lugar = $row->pais.' - '.$ubigeo;
                $output[] = array(
                    'id_postulante'     => $row->id_postulante,
                    'nro_documento'     => $row->nro_documento,
                    'nombres'           => $row->nombres,
                    'apellido_paterno'  => $row->apellido_paterno,
                    'apellido_materno'  => $row->apellido_materno,
                    'telefono'          => $row->telefono,
                    'correo'            => $row->correo,
                    'institucion'       => $row->institucion,
                    'ubigeo'            => $lugar
                );
            }
        }else if($filtro == 4){
            $provi = DB::table('configuracion.ubi_prov')
                ->select('ubi_prov.codigo AS code', 'ubi_prov.descripcion AS provs')
                ->where('ubi_prov.descripcion', 'ilike', '%'.$desc.'%')->get();

            if ($provi->count() > 0){
                $codeUbi = $provi->first()->code;
                $provUbi = $provi->first()->provs;
                $provincia = $provUbi.' ('.$codeUbi.')';
                
                $sql = DB::table('rrhh.rrhh_frm_acad')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_frm_acad.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->join('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'rrhh_postu.id_pais')
                    ->select('rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno',
                            'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_postu.ubigeo', 'rrhh_postu.id_postulante', 'sis_pais.descripcion AS pais')
                    ->where('rrhh_postu.ubigeo', 'ilike', '%'.$codeUbi.'%')->distinct()->get();
                
                foreach ($sql as $row){
                    $ubigeo = $this->buscarUbigeo($row->ubigeo, 'dpto');
                    $lugar = $row->pais.' - '.$ubigeo;
                    $output[] = array(
                        'id_postulante'     => $row->id_postulante,
                        'nro_documento'     => $row->nro_documento,
                        'nombres'           => $row->nombres,
                        'apellido_paterno'  => $row->apellido_paterno,
                        'apellido_materno'  => $row->apellido_materno,
                        'telefono'          => $row->telefono,
                        'correo'            => $row->correo,
                        'provincia'         => $provincia,
                        'ubigeo'            => $lugar
                    );
                }
            }
        }else if($filtro == 5){
            $ubis = DB::table('configuracion.ubi_dis')
                ->select('ubi_dis.codigo AS code', 'ubi_dis.descripcion AS dists')
                ->where('ubi_dis.descripcion', 'ilike', '%'.$desc.'%')->get();

            if ($ubis->count() > 0){
                $codeUbi = $ubis->first()->code;
                $distUbi = $ubis->first()->dists;
                $distrito = $distUbi.' ('.$codeUbi.')';

                $sql = DB::table('rrhh.rrhh_frm_acad')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_frm_acad.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->join('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'rrhh_postu.id_pais')
                    ->select('rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno',
                            'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_postu.ubigeo', 'rrhh_postu.id_postulante', 'sis_pais.descripcion AS pais')
                    ->where('rrhh_postu.ubigeo', '=', $codeUbi)->distinct()->get();
                
                foreach ($sql as $row){
                    $ubigeo = $this->buscarUbigeo($row->ubigeo, 'dpto');
                    $lugar = $row->pais.' - '.$ubigeo;
                    $output[] = array(
                        'id_postulante'     => $row->id_postulante,
                        'nro_documento'     => $row->nro_documento,
                        'nombres'           => $row->nombres,
                        'apellido_paterno'  => $row->apellido_paterno,
                        'apellido_materno'  => $row->apellido_materno,
                        'telefono'          => $row->telefono,
                        'correo'            => $row->correo,
                        'distrito'          => $distrito,
                        'ubigeo'            => $lugar
                    );
                }
            }
        }else if($filtro == 6){
            $sql = DB::table('rrhh.rrhh_exp_labo')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_exp_labo.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'rrhh_postu.id_pais')
                ->select('rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno',
                        'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_postu.ubigeo', 'rrhh_postu.id_postulante', 'sis_pais.descripcion AS pais', 'rrhh_exp_labo.cargo_ocupado')
                ->where('rrhh_exp_labo.cargo_ocupado', 'ilike', '%'.$desc.'%')->distinct()->get();
            
            foreach ($sql as $row){
                $ubigeo = $this->buscarUbigeo($row->ubigeo, 'dpto');
                $lugar = $row->pais.' - '.$ubigeo;
                $output[] = array(
                    'id_postulante'     => $row->id_postulante,
                    'nro_documento'     => $row->nro_documento,
                    'nombres'           => $row->nombres,
                    'apellido_paterno'  => $row->apellido_paterno,
                    'apellido_materno'  => $row->apellido_materno,
                    'telefono'          => $row->telefono,
                    'correo'            => $row->correo,
                    'cargo_ocupado'     => $row->cargo_ocupado,
                    'ubigeo'            => $lugar
                );
            }
        }else if($filtro == 7){
            $sql = DB::table('rrhh.rrhh_exp_labo')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_exp_labo.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'rrhh_postu.id_pais')
                ->select('rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno',
                        'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_postu.ubigeo', 'rrhh_postu.id_postulante', 'sis_pais.descripcion AS pais', 'rrhh_exp_labo.funciones')
                ->where('rrhh_exp_labo.funciones', 'ilike', '%'.$desc.'%')->distinct()->get();

            foreach ($sql as $row){
                $ubigeo = $this->buscarUbigeo($row->ubigeo, 'dpto');
                $lugar = $row->pais.' - '.$ubigeo;
                $output[] = array(
                    'id_postulante'     => $row->id_postulante,
                    'nro_documento'     => $row->nro_documento,
                    'nombres'           => $row->nombres,
                    'apellido_paterno'  => $row->apellido_paterno,
                    'apellido_materno'  => $row->apellido_materno,
                    'telefono'          => $row->telefono,
                    'correo'            => $row->correo,
                    'funciones'         => $row->funciones,
                    'ubigeo'            => $lugar
                );
            }
        }
        return response()->json($output);
    }

    public function onomastico_reporte($filtro){
        $anio = date('Y');
        $output = array();
        
        if ($filtro > 12){
            $sql = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->select('rrhh_trab.id_trabajador', 'rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno',
                    'rrhh_perso.apellido_materno', 'rrhh_perso.fecha_nacimiento')
                ->whereYear('rrhh_perso.fecha_nacimiento', '>', 0)->orderBy('rrhh_perso.fecha_nacimiento', 'ASC')->get();
        }else{
            $sql = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->select('rrhh_trab.id_trabajador', 'rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno',
                    'rrhh_perso.apellido_materno', 'rrhh_perso.fecha_nacimiento')
                ->whereMonth('rrhh_perso.fecha_nacimiento', $filtro)->orderBy('rrhh_perso.fecha_nacimiento', 'ASC')->get();
        }

        $count = $sql->count();

        if ($count > 0){
            foreach ($sql as $row){
                $id_trab = $row->id_trabajador;
        
                $roles = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                    ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
                    ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                    ->select('rrhh_cargo.descripcion AS cargo', 'sis_sede.descripcion AS sede', 'adm_grupo.descripcion AS grupo', 'adm_contri.razon_social AS empresa')
                    ->where('rrhh_trab.id_trabajador', $id_trab)->orderBy('rrhh_rol.id_rol', 'desc')->limit(1)->get()->first();
                
                $cargo = $roles->cargo;
                $empresa = $roles->empresa;
                $sede = $roles->sede;
                $grupo = $roles->grupo;
                $fecha = ($row->fecha_nacimiento != '') ? date('d/m/Y', strtotime($row->fecha_nacimiento)) : '';
                $txtFcha = '<label class="text-danger">'.$fecha.'</label>';
                $output[] = array(
                    'nro_documento'     => $row->nro_documento,
                    'nombres'           => $row->nombres,
                    'apellido_paterno'  => $row->apellido_paterno,
                    'apellido_materno'  => $row->apellido_materno,
                    'cargo'             => $cargo,
                    'empresa'           => $empresa,
                    'sede'              => $sede,
                    'grupo'             => $grupo,
                    'onomastico'        => $txtFcha
                );
            }
        }
        $dataFinal = array('result' => $output, 'count' => $count);
        return response()->json($dataFinal);
    }

    public function datos_generales_reporte($type){
        $sql = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('rrhh.rrhh_est_civil', 'rrhh_est_civil.id_estado_civil', '=', 'rrhh_perso.id_estado_civil')
                ->select('rrhh_trab.id_trabajador', 'rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno',
                    'rrhh_perso.apellido_materno', 'rrhh_est_civil.descripcion AS estado_civil', 'rrhh_trab.hijos', 'rrhh_postu.correo')
                ->where('rrhh_trab.estado', 1)->orderBy('rrhh_perso.apellido_paterno', 'ASC')->get();

        $count = $sql->count();
        if ($count > 0){
            $id_trab = 0;
            foreach ($sql as $row){
                $id_trab = $row->id_trabajador;
                $civil = $row->estado_civil;
                $hijos = $row->hijos;
                $correo = $row->correo;
        
                $roles = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                    ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
                    ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                    ->select('rrhh_cargo.descripcion AS cargo', 'sis_sede.descripcion AS sede', 'adm_grupo.descripcion AS grupo',
                            'adm_contri.razon_social AS empresa', 'adm_area.descripcion AS area', 'adm_empresa.codigo AS abrev_empre',
                            'rrhh_rol.salario', 'rrhh_rol.fecha_inicio')
                    ->where('rrhh_trab.id_trabajador', $id_trab)->orderBy('rrhh_rol.id_rol', 'desc')->limit(1)->get();

                foreach ($roles as $key){
                    $cargo = $key->cargo;
                    $empresa = $key->empresa;
                    $plani = $key->abrev_empre;
                    $sede = $key->sede;
                    $grupo = $key->grupo;
                    $area = $key->area;
                    $salario = $key->salario;
                    $inicio = $key->fecha_inicio;
                }

                $asig = ($hijos == 1) ? 93 : 0;
                $ingreso = ($inicio != null) ? date('d/m/Y', strtotime($inicio)) : '';

                $output[] = array(
                    'id_trabajador'     => $id_trab,
                    'nro_documento'     => $row->nro_documento,
                    'nombres'           => $row->nombres,
                    'apellido_paterno'  => $row->apellido_paterno,
                    'apellido_materno'  => $row->apellido_materno,
                    'estado_civil'      => $civil,
                    'fecha_ingreso'     => $ingreso,
                    'cargo'             => $cargo,
                    'empresa'           => $plani,
                    'sede'              => $sede,
                    'grupo'             => $grupo,
                    'area'              => $area,
                    'remuneracion'      => number_format($salario, 2),
                    'asignacion'        => number_format($asig, 2)
                );
            }
        }
        if ($type == 1) {
            $dataFinal = array('result' => $output, 'count' => $count);
            return response()->json($dataFinal);
        }
    }

    public function cargar_detalle_postulante($id){
        $html = '';
        $exp_lab = DB::table('rrhh.rrhh_exp_labo')->where('id_postulante', '=', $id)->get();

        $frm_post = DB::table('rrhh.rrhh_frm_acad')
            ->join('rrhh.rrhh_carrera', 'rrhh_carrera.id_carrera', '=', 'rrhh_frm_acad.id_carrera')
            ->select('rrhh_frm_acad.*', 'rrhh_carrera.descripcion AS carrera')
            ->where('rrhh_frm_acad.id_postulante', '=', $id)->get();

        $html.=
        '<h5>1° Formación Académica</h5>
        <table class="table table-striped table-bordered table-hover table-okc-view">
        <thead>
            <tr>
                <th>Carrera</th>
                <th>Institución</th>
                <th>F. Inicio</th>
                <th>F. Fin</th>
            </tr>
        </thead>';
        foreach ($frm_post as $row){
            $fini = ($row->fecha_inicio != null) ? date('d/m/Y', strtotime($row->fecha_inicio)) : '';
            $ffin = ($row->fecha_fin != null) ? date('d/m/Y', strtotime($row->fecha_fin)) : '';
            $html.=
            '<tr>
                <td>'.$row->carrera.'</td>
                <td>'.$row->nombre_institucion.'</td>
                <td>'.$fini.'</td>
                <td>'.$ffin.'</td>
            </tr>';
        }
        $html.=
        '</table><br><br>
        <h5>2° Experiencia Laboral</h5>
        <table class="table table-striped table-bordered table-hover table-okc-view">
        <thead>
            <tr>
                <th width="250">Cargo</th>
                <th width="250">Empresa</th>
                <th width="150">Datos de Contacto</th>
                <th>Funciones</th>
            </tr>
        </thead>';
        foreach ($exp_lab as $key){
            $html.=
            '<tr>
                <td>'.$key->cargo_ocupado.'</td>
                <td>'.$key->nombre_empresa.'</td>
                <td>'.$key->datos_contacto.'</td>
                <td>'.$key->funciones.'</td>
            </tr>';
        }
        $html.= '</table>';

        return response()->json($html);
    }

    public function grupo_trabajador_reporte($emp, $grupo){
        $thead =
        '<tr>
            <th>Empresa</th>
            <th>Sede</th>
            <th>DNI</th>
            <th>Trabajador</th>
            <th>Cargo</th>
        </tr>';
        $tbody = '';

        $sql = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
            ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->select(DB::raw('DISTINCT rrhh_trab.id_trabajador'), 'rrhh_cargo.descripcion AS rol', 'sis_sede.descripcion AS sede', 'adm_grupo.descripcion AS grupo', 'adm_contri.razon_social AS empresa')
            ->where([['adm_empresa.id_empresa', $emp], ['adm_grupo.id_grupo', $grupo]])->get();

        foreach ($sql as $key) {
            $empre = $key->empresa;
            $grupo = $key->grupo;
            $sede = $key->sede;
            $id_trabajador = $key->id_trabajador;
            $rol = $key->rol;

            $trabajador = $this->buscar_trabajador_id($id_trabajador);
            $nro_doc = $this->buscar_dni($id_trabajador);
            $tbody.=
            '<tr>
                <td>'.$empre.'</td>
                <td>'.$sede.'</td>
                <td>'.$nro_doc.'</td>
                <td>'.$trabajador.'</td>
                <td>'.$rol.'</td>
            </tr>';
        }
        
        $headExport = $thead;
        $dataExport = $tbody;
        return view('rrhh/reportes/trabajador_grupo', compact('headExport', 'dataExport'));
    }

    public function reporte_afp(){
        $anio = date('Y');

        $data = $this->cargar_remuneraciones(0, 1, 9, $anio, 2, 0, 0);
        $myData = $data['data'];
        $cont = sizeof($myData);

        $html =
        '<table border="1">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>CUSPP</th>
                    <th width="70">Tipo Doc. Identidad</th>
                    <th>Documento de Identidad</th>
                    <th>Apellidos y Nombres</th>
                    <th width="70">Relación Laboral</th>
                    <th width="70">Inicio de RL</th>
                    <th width="70">Cese de RL</th>
                    <th width="70">Excepcion de Aportar</th>
                    <th>Remuneración Asegurable</th>
                    <th width="70">Aporte voluntario del afiliado con fin previsional</th>
                    <th width="70">Aporte voluntario del afiliado sin fin previsional</th>
                    <th width="70">Aporte voluntario del empleador</th>
                    <th width="70">Tipo de trabajo o  Rubro</th>
                    <th>AFP</th>
                </tr>
            </thead>
        <tbody>';
        $correla = 0;

        for ($i = 0; $i < $cont; $i++){
            $correla = $i + 1;

            $datos = $myData[$i]['datos_trabajador'];
            $dni = $myData[$i]['dni_trabajador'];
            $tp_doc = $myData[$i]['tipo_documento'];
            $pension = $myData[$i]['fondo_pension'];
            $ini_cont = $myData[$i]['fecha_contrato_ini'];
            $cussp = $myData[$i]['numero_cussp'];
            $sueldo = $myData[$i]['total_remun'];

            if ($tp_doc == 'DNI'){
                $tdoc = 0;
            }elseif ($tp_doc == 'CARNET DE EXTRANJERIA'){
                $tdoc = 1;
            }elseif ($tp_doc == 'PASAPORTE'){
                $tdoc = 4;
            }

            $txt_irl = ($ini_cont == 9) ? 'S' : 'N';

            $html .=
            '<tr>
                <td class="okc-numero">'.$correla.'</td>
                <td>'.$cussp.'</td>
                <td class="okc-numero">'.$tdoc.'</td>
                <td>'.$dni.'</td>
                <td>'.$datos.'</td>
                <td align="center">S</td>
                <td align="center">'.$txt_irl.'</td>
                <td align="center">N</td>
                <td align="center"></td>
                <td class="okc-moneda">'.$sueldo.'</td>
                <td align="center" class="okc-numero">0</td>
                <td align="center" class="okc-numero">0</td>
                <td align="center" class="okc-numero">0</td>
                <td align="center">N</td>
                <td></td>
            </tr>';
        }

        $html .= '</tbody></table>';
        $data = $html;
        return view('rrhh/reportes/downloadExcelAfp', compact('data'));
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

    function ultimoDia($mes, $anio){ 
        $month = $mes;
        $year = $anio;
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));
        return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    }

    function primerDia($mes, $anio){
        $month = $mes;
        $year = $anio;
        return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    }

    function filtrar_dia($fecha){
        $dia = date("w", strtotime($fecha));
        return $dia;
    }

    function restaFechasDias($fechaVen, $fechaHoy){
        $segundos = strtotime($fechaVen) - strtotime($fechaHoy);
        $diferencia_dias = intval($segundos/60/60/24);
        return $diferencia_dias; 
    }

    function calcularDiasEfectivos($mes, $anio, $fechaIni, $fechaFin){
        $diaIni = $this->primerDia($mes, $anio);
        $diaFin = $this->ultimoDia($mes, $anio);

        if ($fechaIni <= $diaIni){
            if ($fechaFin == null){//Si la fecha es nula (no tiene fecha fin)
                $calFecIni = date('Y-m-d', strtotime($diaIni.'- 1 days')); 
                $final = $this->restaFechasDias($diaFin, $calFecIni);
            }else{
                if ($fechaFin >= $diaIni){
                    if ($fechaFin <= $diaFin){ //fecha de cese dentro del rango
                        $calFecIni = date('Y-m-d', strtotime($diaIni.'- 1 days')); 
                        $final = $this->restaFechasDias($fechaFin, $calFecIni);
                    }else{
                        $calFecIni = date('Y-m-d', strtotime($diaIni.'- 1 days')); 
                        $final = $this->restaFechasDias($diaFin, $calFecIni); //fecha esta ingresada adelantando 
                    }
                }else{ // cesado antes
                    $final = 0;
                }
            }
        }else{
            if ($fechaIni < $diaFin){ //Si ingresó, esta dentro del rango
                $calFecIni = date('Y-m-d', strtotime($fechaIni.'- 1 days')); 
                $final = $this->restaFechasDias($diaFin, $calFecIni);
            }else{
                $final = 0;
            }
        }

        return $final;
    }

    function convertDescuents($hora){
		$time = date('H:i', strtotime($hora));
		$ini = explode(":", $hora);

		$hora = $ini[0] * 60;
		$min = $hora + $ini[1];

		$total = (int) ($min / 60);
		return $total;
	}

	function sumar_horas($hini, $hfin) {          
        $ini = explode(":", $hini);
		$fin = explode(":", $hfin);

		$horas = (int) ($ini[0] + $fin[0]);
		$mints = (int) ($ini[1] + $fin[1]);

		$horas+= (int) ($mints / 60); 
        $mints = $mints % 60;

        if($horas < 10){
        	$horas = "0".$horas;
        }
        if($mints < 10){
        	$mints = "0".$mints;
        }
        return $horas.":".$mints;
    }

    function restar_horas($horaini, $horafin){
        $horai = substr($horaini, 0, 2);
        $mini = substr($horaini, 3, 2);
        $segi = substr($horaini, 6, 2);
        
        $horaf = substr($horafin, 0, 2);
        $minf = substr($horafin, 3, 2);
        $segf = substr($horafin, 6, 2);
        
        $ini = ((($horai * 60) * 60) + ($mini * 60) + $segi);
        $fin = ((($horaf * 60) * 60) + ($minf * 60) + $segf);
        
        $dif = $fin - $ini;
        
        $difh = floor($dif / 3600);
        $difm = floor(($dif - ($difh * 3600)) / 60);
        $difs = $dif - ($difm * 60) - ($difh * 3600);
        $hora = date("H:i", mktime($difh, $difm, $difs));
        return $hora;
    }

    function convertHtoM($hora){
    	$time = date('H:i', strtotime($hora));
		$ini = explode(":", $hora);
		$min  = (int) (($ini[0] * 60) + $ini[1]);
		return $min;
    }

    function convertTime($in){
        $h = intval($in);
        $m = round((((($in - $h) / 100.0) * 60.0) * 100), 0);
        if ($m == 60){
            $h++;
            $m = 0;
        }
        $retval = sprintf("%02d:%02d", $h, $m);
        return $retval;
    }
    
    function hallarMes($val){
        $meses = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre");
        $fin = $meses[$val];
        return $fin;
    }

    function buscar_edad($fechanacimiento){
        list($ano,$mes,$dia) = explode("-",$fechanacimiento);
        $ano_diferencia = date("Y") - $ano;
        $mes_diferencia = date("m") - $mes;
        $dia_diferencia = date("d") - $dia;
        if ($dia_diferencia < 0 && $mes_diferencia <= 0)
        $ano_diferencia--;
        return $ano_diferencia;
    }

    function convertMesEspañol($fecha){
        $mes = date('F', strtotime($fecha));
        $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
        return $nombreMes;
    }
}