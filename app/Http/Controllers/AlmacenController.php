<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use PDF;
use App\Models\almacen\mov_alm as Movimiento;
use App\Models\almacen\mov_alm_det as MovDetalle;
use App\Models\almacen\guia_com as GuiaCompra;
use Illuminate\Support\Collection as Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set('America/Lima');

class AlmacenController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_tipo(){
        return view('almacen/producto/tipo');
    }
    function view_categoria(){
        $tipos = $this->mostrar_tipos_cbo();
        return view('almacen/producto/categoria', compact('tipos'));
    }
    function view_subcategoria(){
        return view('almacen/producto/subcategoria');
    }
    function view_clasificacion(){
        return view('almacen/producto/clasificacion');
    }
    function view_prod_catalogo(){
        return view('almacen/producto/prod_catalogo');
    }
    function view_producto(){
        $clasificaciones = $this->mostrar_clasificaciones_cbo();
        $subcategorias = $this->mostrar_subcategorias_cbo();
        $categorias = $this->mostrar_categorias_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        $posiciones = $this->mostrar_posiciones_cbo();
        $ubicaciones = $this->mostrar_ubicaciones_cbo();
        $monedas = $this->mostrar_moneda_cbo();
        return view('almacen/producto/producto', compact('clasificaciones','subcategorias','categorias','unidades','posiciones','ubicaciones','monedas'));
    }
    function view_almacenes(){
        $sedes = $this->mostrar_sedes_cbo();
        $tipos = $this->mostrar_tp_almacen_cbo();
        return view('almacen/ubicacion/almacenes', compact('sedes','tipos'));
    }
    function view_ubicacion(){
        $almacenes = $this->mostrar_almacenes_cbo();
        $estantes = $this->mostrar_estantes_cbo();
        $niveles = $this->mostrar_niveles_cbo();
        return view('almacen/ubicacion/ubicacion', compact('almacenes','estantes','niveles'));
    }
    function view_tipo_almacen(){
        return view('almacen/variables/tipo_almacen');
    }
    function view_tipo_servicio(){
        return view('almacen/variables/tipoServ');
    }
    function view_servicio(){
        $tipos = $this->mostrar_tp_servicios_cbo();
        $detracciones = $this->mostrar_detracciones_cbo();
        return view('almacen/variables/servicio', compact('tipos','detracciones'));
    }
    function view_tipo_movimiento(){
        return view('almacen/variables/tipo_movimiento');
    }
    function view_unid_med(){
        return view('almacen/variables/unid_med');
    }
    
    function view_guia_compra(){
        $proveedores = $this->mostrar_proveedores_cbo();
        $almacenes = $this->mostrar_almacenes_cbo();
        $posiciones = $this->mostrar_posiciones_cbo();
        $motivos = $this->mostrar_motivos_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $monedas = $this->mostrar_moneda_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_ing();
        $tp_operacion = $this->tp_operacion_cbo_ing();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = $this->sis_identidad_cbo();
        $tp_prorrateo = $this->select_tp_prorrateo();
        $usuarios = $this->select_usuarios();
        $motivos_anu = $this->select_motivo_anu();
        return view('almacen/guias/guia_compra', compact('proveedores','almacenes','posiciones','motivos','clasificaciones','tp_doc','monedas','tp_doc_almacen','tp_operacion','tp_contribuyente','sis_identidad','tp_prorrateo','usuarios','motivos_anu'));
    }
    function view_guia_venta(){
        $almacenes = $this->mostrar_almacenes_cbo();
        $posiciones = $this->mostrar_posiciones_cbo();
        $motivos = $this->mostrar_motivos_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        // $empresas = $this->select_empresa();
        $sedes = $this->mostrar_sedes_cbo();
        $proveedores = $this->mostrar_proveedores_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_sal();
        $tp_operacion = $this->tp_operacion_cbo_sal();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = $this->sis_identidad_cbo();
        // $usuarios = $this->select_usuarios_almacen();
        $usuarios = $this->select_usuarios();
        $motivos_anu = $this->select_motivo_anu();
        return view('almacen/guias/guia_venta', compact('almacenes','posiciones','motivos','clasificaciones','sedes','proveedores','tp_doc_almacen','tp_operacion','tp_contribuyente','sis_identidad','usuarios','motivos_anu'));
    }
    // function view_doc_compra(){
    //     $proveedores = $this->mostrar_proveedores_cbo();
    //     $clasificaciones = $this->mostrar_guia_clas_cbo();
    //     $condiciones = $this->mostrar_condiciones_cbo();
    //     $tp_doc = $this->mostrar_tp_doc_cbo();
    //     $moneda = $this->mostrar_moneda_cbo();
    //     $detracciones = $this->mostrar_detracciones_cbo();
    //     $impuestos = $this->mostrar_impuestos_cbo();
    //     $usuarios = $this->select_usuarios();
    //     $tp_contribuyente = $this->tp_contribuyente_cbo();
    //     $sis_identidad = $this->sis_identidad_cbo();
    //     return view('almacen/documentos/doc_compra', compact('proveedores','clasificaciones','condiciones','tp_doc','moneda','detracciones','impuestos','usuarios','tp_contribuyente','sis_identidad'));
    // }
    function view_doc_venta(){
        // $empresas = $this->select_empresa();
        $sedes = $this->mostrar_sedes_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $condiciones = $this->mostrar_condiciones_cbo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $moneda = $this->mostrar_moneda_cbo();
        $usuarios = $this->select_usuarios();
        return view('almacen/documentos/doc_venta', compact('sedes','clasificaciones','condiciones','tp_doc','moneda','usuarios'));
    }
    function view_cola_atencion(){
        $motivos = $this->mostrar_motivos_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $almacenes = $this->mostrar_almacenes_cbo();
        return view('almacen/reportes/cola_atencion', compact('motivos','clasificaciones','almacenes'));
    }
    function view_kardex_general(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        return view('almacen/reportes/kardex_general', compact('almacenes','empresas'));
    }
    function view_kardex_detallado(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        return view('almacen/reportes/kardex_detallado', compact('almacenes','empresas'));
    }
    function view_saldos(){
        $almacenes = $this->mostrar_almacenes_cbo();
        return view('almacen/reportes/saldos', compact('almacenes'));
    }
    function view_tipo_doc_almacen(){
        $tp_doc = $this->mostrar_tp_doc_cbo();
        return view('almacen/variables/tipo_doc_almacen', compact('tp_doc'));
    }
    function view_ingresos(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_ing();
        $tp_operacion = $this->tp_operacion_cbo_ing();
        $usuarios = $this->select_almaceneros();
        return view('almacen/reportes/lista_ingresos', compact('almacenes','empresas','tp_doc_almacen','tp_operacion','usuarios'));
    }
    function view_salidas(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_sal();
        $tp_operacion = $this->tp_operacion_cbo_sal();
        $usuarios = $this->select_almaceneros();
        return view('almacen/reportes/lista_salidas', compact('almacenes','empresas','tp_doc_almacen','tp_operacion','usuarios'));
    }
    function view_busqueda_ingresos(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_ing();
        return view('almacen/reportes/busqueda_ingresos', compact('almacenes','empresas','tp_doc_almacen'));
    }
    function view_busqueda_salidas(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_sal();
        return view('almacen/reportes/busqueda_salidas', compact('almacenes','empresas','tp_doc_almacen'));
    }
    function view_listar_transferencias(){
        $motivos = $this->mostrar_motivos_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $almacenes = $this->mostrar_almacenes_cbo();
        // $usuarios = $this->select_usuarios_almacen();
        $usuarios = $this->select_usuarios();
        return view('almacen/transferencias/listar_transferencias', compact('motivos','clasificaciones','almacenes','usuarios'));
    }
    function view_transformacion(){
        $almacenes = $this->mostrar_almacenes_cbo();
        $empresas = $this->select_empresa();
        // $usuarios = $this->select_usuarios_almacen();
        $usuarios = $this->select_usuarios();
        return view('almacen/customizacion/transformacion', compact('almacenes','empresas','usuarios'));
    }
    function view_listar_transformaciones(){
        $almacenes = $this->mostrar_almacenes_cbo();
        // $usuarios = $this->select_usuarios_almacen();
        $usuarios = $this->select_usuarios();
        return view('almacen/customizacion/listar_transformaciones', compact('almacenes','usuarios'));
    }
    function view_serie_numero(){
        $tipos = $this->select_cont_tp_doc();
        $sedes = $this->mostrar_sedes_cbo();
        return view('almacen/variables/serie_numero', compact('tipos','sedes'));
    }
    function view_kardex_series(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        return view('almacen/reportes/kardex_series', compact('almacenes','empresas'));
    }
    function view_docs_prorrateo(){
        // $tp_doc = $this->mostrar_tp_doc_cbo();
        return view('almacen/reportes/docs_prorrateo');
    }

    /* Combos */
    public function select_cont_tp_doc(){
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.*')
            ->where([['estado', '=', 1],['abreviatura','!=',null]])
            ->orderBy('cod_sunat', 'asc')->get();
        return $data;
    }
    public function select_adm_tp_docum(){
        $data = DB::table('administracion.adm_tp_docum')
            ->select('adm_tp_docum.*')
            ->where('adm_tp_docum.estado', '=', 1)
            ->orderBy('adm_tp_docum.descripcion', 'asc')->get();
        return $data;
    }
    public function select_motivo_anu(){
        $data = DB::table('almacen.motivo_anu')
            ->select('motivo_anu.id_motivo', 'motivo_anu.descripcion')
            ->where('motivo_anu.estado', '=', 1)
            ->orderBy('motivo_anu.descripcion', 'asc')->get();
        return $data;
    }
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
    public function select_tp_prorrateo(){
        $data = DB::table('almacen.tp_prorrateo')
            ->select('tp_prorrateo.id_tp_prorrateo', 'tp_prorrateo.descripcion')
            ->where('tp_prorrateo.estado', '=', 1)
            ->orderBy('tp_prorrateo.id_tp_prorrateo', 'asc')->get();
        return $data;
    }
    public function guardar_tipo_prorrateo($nombre){
        $id_tipo = DB::table('almacen.tp_prorrateo')->insertGetId(
            [   'descripcion'=>$nombre, 
                'estado'=>1
            ],
                'id_tp_prorrateo'
            );

        $data = DB::table('almacen.tp_prorrateo')->where('estado',1)->get();
        $html = '';

        foreach($data as $d){
            if ($id_tipo == $d->id_tp_prorrateo){
                $html.='<option value="'.$d->id_tp_prorrateo.'" selected>'.$d->descripcion.'</option>';
            } else {
                $html.='<option value="'.$d->id_tp_prorrateo.'">'.$d->descripcion.'</option>';
            }
        }
        return json_encode($html);
    }
    public static function tp_operacion_cbo_ing(){
        $data = DB::table('almacen.tp_ope')
            ->select('tp_ope.id_operacion','tp_ope.cod_sunat','tp_ope.descripcion')
            ->where('tp_ope.estado', 1)
            ->whereIn('tp_ope.tipo',[1,3])
            ->get();
        return $data;
    }
    public function tp_operacion_cbo_sal(){
        $data = DB::table('almacen.tp_ope')
            ->select('tp_ope.id_operacion','tp_ope.cod_sunat','tp_ope.descripcion')
            ->where('tp_ope.estado', 1)
            ->whereIn('tp_ope.tipo',[2,3])
            ->orderBy('cod_sunat','asc')
            ->get();
        return $data;
    }
    public function tp_doc_almacen_cbo_ing(){
        $data = DB::table('almacen.tp_doc_almacen')
            ->select('tp_doc_almacen.id_tp_doc_almacen','tp_doc_almacen.descripcion')
            ->where([['tp_doc_almacen.estado', '=', 1],
                    ['tp_doc_almacen.tipo', '=', 1]])
            ->get();
        return $data;
    }
    public function tp_doc_almacen_cbo_sal(){
        $data = DB::table('almacen.tp_doc_almacen')
            ->select('tp_doc_almacen.id_tp_doc_almacen','tp_doc_almacen.descripcion')
            ->where([['tp_doc_almacen.estado', '=', 1],
                    ['tp_doc_almacen.tipo', '=', 2]])
            ->orderBy('descripcion','desc')
            ->get();
        return $data;
    }
    public function mostrar_impuestos_cbo(){
        $data = DB::table('contabilidad.cont_impuesto')
            ->select('cont_impuesto.id_impuesto','cont_impuesto.descripcion',
            'cont_impuesto.porcentaje')
            ->where('cont_impuesto.estado', '=', 1)
            ->get();
        return $data;
    }
    public function select_empresa(){
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('adm_empresa.id_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_empresa.id_empresa', 'asc')->get();
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
    public function mostrar_area_cbo(){
        $data = DB::table('administracion.adm_area')
            ->select('adm_area.id_area',DB::raw("CONCAT(adm_grupo.descripcion,' - ',adm_area.descripcion) as area_descripcion"))
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
            ->where('adm_area.estado', '=', 1)
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
            ->get();
        return $data;
    }
    public static function select_usuarios(){
        $data = DB::table('configuracion.sis_usua')
            ->select('sis_usua.id_usuario','sis_usua.nombre_corto')
            ->where([['sis_usua.estado', '=', 1],['sis_usua.nombre_corto', '<>', null]])
            ->get();
        return $data;
    }
    public function select_usuarios_almacen(){
        $data = DB::table('rrhh.rrhh_rol')
            ->select('sis_usua.id_usuario','sis_usua.nombre_corto')
            ->where([['rrhh_rol.id_area','=',47],['sis_usua.estado','=',1]])
            ->join('configuracion.sis_usua','sis_usua.id_trabajador','=','rrhh_rol.id_trabajador')
            ->get();
        return $data;
    }
    public function select_almaceneros(){
        $data = DB::table('rrhh.rrhh_rol')
            ->select('sis_usua.id_usuario','rrhh_rol.id_trabajador',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','rrhh_rol.id_trabajador')
            ->join('configuracion.sis_usua','sis_usua.id_trabajador','=','rrhh_rol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where([['sis_usua.estado', '=', 1],['rrhh_rol.id_area','=',8]])//Area = Almacen
            ->get();
        return $data;
    }
    public function mostrar_equipo_cbo(){
        $data = DB::table('logistica.equipo')
            ->select('equipo.id_equipo','equipo.codigo','equipo.descripcion')
            ->where('estado', '=', 1)
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
    public function mostrar_tipos_cbo(){
        $data = DB::table('almacen.alm_tp_prod')
            ->select('alm_tp_prod.id_tipo_producto','alm_tp_prod.descripcion')
            ->where('estado', '=', 1)
                ->orderBy('alm_tp_prod.id_tipo_producto','asc')->get();
        return $data;
    }
    public function mostrar_clasificaciones_cbo(){
        $data = DB::table('almacen.alm_clasif')
            ->select('alm_clasif.id_clasificacion','alm_clasif.descripcion')
            ->where([['alm_clasif.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function mostrar_subcategorias_cbo(){
        $data = DB::table('almacen.alm_subcat')
            ->select('alm_subcat.id_subcategoria','alm_subcat.descripcion')
            ->where([['alm_subcat.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function mostrar_categorias_cbo(){
        $data = DB::table('almacen.alm_cat_prod')
            ->select('alm_cat_prod.id_categoria','alm_cat_prod.descripcion')
            ->where([['alm_cat_prod.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function mostrar_unidades_cbo(){
        $data = DB::table('almacen.alm_und_medida')
            ->select('alm_und_medida.id_unidad_medida','alm_und_medida.descripcion',
                'alm_und_medida.abreviatura')
            ->where([['alm_und_medida.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function mostrar_unidades(){
        $data = DB::table('almacen.alm_und_medida')
            ->select('alm_und_medida.*')
            ->where([['alm_und_medida.estado', '=', 1]])
                ->orderBy('id_unidad_medida')
                ->get();
        return response()->json($data); 
    }
    public function mostrar_tp_servicios_cbo(){
        $data = DB::table('logistica.log_tp_servi')
            ->select('log_tp_servi.id_tipo_servicio','log_tp_servi.descripcion')
            ->where([['log_tp_servi.estado', '=', 1]])
                ->orderBy('id_tipo_servicio')
                ->get();
        return $data;
    }
    public function mostrar_tp_almacen_cbo(){
        $data = DB::table('almacen.alm_tp_almacen')
            ->select('alm_tp_almacen.id_tipo_almacen','alm_tp_almacen.descripcion')
            ->where([['alm_tp_almacen.estado', '=', 1]])
                ->orderBy('descripcion')
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
    public static function mostrar_almacenes_cbo(){
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen','alm_almacen.codigo','alm_almacen.descripcion')
            ->where([['alm_almacen.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function cargar_almacenes($id_sede){
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen','alm_almacen.codigo','alm_almacen.descripcion')
            ->where([['alm_almacen.estado', '=', 1],
                     ['alm_almacen.id_sede','=',$id_sede]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function cargar_almacenes_contrib($id_contribuyente){
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen','alm_almacen.codigo','alm_almacen.descripcion')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->where([['alm_almacen.estado', '=', 1],
                     ['adm_contri.id_contribuyente','=',$id_contribuyente]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function select_almacenes_empresa($id_empresa){
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen','alm_almacen.codigo','alm_almacen.descripcion')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->where([['alm_almacen.estado', '=', 1],
                     ['adm_empresa.id_empresa', '=', $id_empresa]])
                ->orderBy('alm_almacen.codigo')
                ->get();
        return $data;
    }
    public function mostrar_estantes_cbo(){
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.id_estante','alm_ubi_estante.codigo')
            ->where([['alm_ubi_estante.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function mostrar_niveles_cbo()
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.id_nivel','alm_ubi_nivel.codigo')
            ->where([['alm_ubi_nivel.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function mostrar_posiciones_cbo()
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
            ->where([['alm_ubi_posicion.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function select_posiciones_almacen($id_almacen){
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function mostrar_ubicaciones_cbo()
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.id_prod_ubi','alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.estado', '=', 1]])
                ->orderBy('cod_posicion')
                ->get();
        return $data;
    }
    public function mostrar_detracciones_cbo()
    {
        $data = DB::table('contabilidad.cont_detra_det')
            ->select('cont_detra_det.id_detra_det','cont_detra.cod_sunat','cont_detra_det.porcentaje','cont_detra.descripcion')
            ->join('contabilidad.cont_detra','cont_detra.id_cont_detra','=','cont_detra_det.id_detra')
            ->where([['cont_detra_det.estado', '=', 1]])
                ->orderBy('cont_detra.descripcion')
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
    public function mostrar_motivos_cbo()
    {
        $data = DB::table('almacen.guia_motivo')
            ->select('guia_motivo.id_motivo','guia_motivo.descripcion')
            ->where([['guia_motivo.estado', '=', 1]])
            ->orderBy('guia_motivo.id_motivo')
            ->get();
        return $data;
    }
    public static function mostrar_guia_clas_cbo()
    {
        $data = DB::table('almacen.guia_clas')
            ->select('guia_clas.id_clasificacion','guia_clas.descripcion')
            ->where([['guia_clas.estado', '=', 1]])
            ->orderBy('guia_clas.id_clasificacion')
            ->get();
        return $data;
    }
    public function mostrar_condiciones_cbo()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago','log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado',1)
            ->orderBy('log_cdn_pago.descripcion')
            ->get();
        return $data;
    }
    public static function mostrar_tp_doc_cbo()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.id_tp_doc','cont_tp_doc.cod_sunat','cont_tp_doc.descripcion')
            ->where([['cont_tp_doc.estado', '=', 1]])
            ->orderBy('cont_tp_doc.cod_sunat','asc')
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

    ///////////////////////////////////
    public function mostrar_impuesto($cod, $fecha){
        $data = DB::table('contabilidad.cont_impuesto')
        ->select('cont_impuesto.*')
            ->where([['codigo','=',$cod],['fecha_inicio','<',$fecha]])
            ->orderBy('fecha_inicio','desc')
            ->first();
        return $data;
    }

    public function mostrar_clientes()
    {
        $data = DB::table('comercial.com_cliente')
            ->select('com_cliente.id_cliente','com_cliente.id_contribuyente',
                'adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where([['com_cliente.estado', '=', 1]])
                ->orderBy('adm_contri.nro_documento')
                ->get();
        $output['data'] = $data;
        return $output;
    }
    public function mostrar_clientes_empresa()
    {
        $data = DB::table('comercial.com_cliente')
            ->select('com_cliente.id_cliente','com_cliente.id_contribuyente',
                'adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->join('administracion.adm_empresa','adm_empresa.id_contribuyente','=','adm_contri.id_contribuyente')
            ->where([['com_cliente.estado', '=', 1]])
                ->orderBy('adm_contri.nro_documento')
                ->get();
        $output['data'] = $data;
        return $output;
    }
    //Tipo de Producto
    public function mostrar_tp_productos(){
        $data = DB::table('almacen.alm_tp_prod')
            ->select('alm_tp_prod.*')
            ->where([['alm_tp_prod.estado', '=', 1]])
                ->orderBy('id_tipo_producto')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_tp_producto($id){
        $data = DB::table('almacen.alm_tp_prod')
            ->where([['alm_tp_prod.id_tipo_producto', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_tp_producto(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_tp_prod')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_tipo_producto = DB::table('almacen.alm_tp_prod')->insertGetId(
                [
                    'descripcion' => $des,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_tipo_producto'
                );
        } else {
            $msj = 'No es posible guardar. Ya existe '.$count.' tipo registrado con la misma descripción.';
        }
        return response()->json($msj);
    }
    public function update_tp_producto(Request $request)
    {
        $des = strtoupper($request->descripcion);
        $count = DB::table('almacen.alm_tp_prod')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();
        $msj = '';
        if ($count <= 1){
            $data = DB::table('almacen.alm_tp_prod')
            ->where('id_tipo_producto',$request->id_tipo_producto)
            ->update([
                'descripcion' => $des
            ]);
        } else {
            $msj = 'No es posible actualizar. Ya existe '.$count.' tipo registrado con la misma descripción.';
        }
        return response()->json($msj);
    }
    public function anular_tp_producto(Request $request,$id){
        $msj = '';
        $count = DB::table('almacen.alm_cat_prod')
        ->where('id_tipo_producto',$id)
        ->count();
        if ($count == 0){
            DB::table('almacen.alm_tp_prod')
            ->where('id_tipo_producto',$id)
            ->update([ 'estado' => 7 ]);
        } else {
            $msj = 'No puede anular. Tiene vinculado '.$count.' categoría.';
        }
        return response()->json($msj);
    }
    public function tipo_revisar_relacion($id){
        $data = DB::table('almacen.alm_cat_prod')
        ->where([['id_tipo_producto','=',$id],
                ['estado','=',1]])
        ->get()->count();
        return response()->json($data);
    }

    //Categorias
    public function mostrar_categorias(){
        $data = DB::table('almacen.alm_cat_prod')
            ->select('alm_cat_prod.*', 'alm_tp_prod.descripcion as tipo_descripcion')
            ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->where([['alm_cat_prod.estado', '=', 1]])
                ->orderBy('id_categoria')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_categoria($id){
        $data = DB::table('almacen.alm_cat_prod')
        ->select('alm_cat_prod.*', 'alm_tp_prod.descripcion as tipo_descripcion',
                 'alm_tp_prod.id_tipo_producto')
        ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->where([['alm_cat_prod.id_categoria', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function categoria_nextId($id_tipo_producto){
        $cantidad = DB::table('almacen.alm_cat_prod')
        ->where('id_tipo_producto',$id_tipo_producto)
        ->get()->count();
        $val = AlmacenController::leftZero(3,$cantidad);
        $nextId = "".$id_tipo_producto."".$val;
        return $nextId;
    }
    public function guardar_categoria(Request $request){
        $codigo = $this->categoria_nextId($request->id_tipo_producto);
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_cat_prod')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_categoria = DB::table('almacen.alm_cat_prod')->insertGetId(
                [
                    'codigo' => $codigo,
                    'id_tipo_producto' => $request->id_tipo_producto,
                    'descripcion' => $des,
                    'estado' => $request->estado,
                    'fecha_registro' => $fecha
                ],
                    'id_categoria'
                );
        } else {
            $msj = 'No puede guardar. Ya existe dicha descripción.';
        }
        return response()->json($msj);
    }
    public function update_categoria(Request $request){
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_cat_prod')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $id_categoria = DB::table('almacen.alm_cat_prod')
            ->where('id_categoria',$request->id_categoria)
            ->update([ 'descripcion' => $des ]);
        } else {
            $msj = 'No puede actualizar. Ya existe dicha descripción.';
        }
        return response()->json($msj);
    }
    public function anular_categoria(Request $request,$id){
        $id_categoria = DB::table('almacen.alm_cat_prod')
        ->where('id_categoria',$id)
        ->update([ 'estado' => 7 ]);
        return response()->json($id_categoria);
    }
    public function cat_revisar($id){
        $data = DB::table('almacen.alm_prod')
        ->where([['id_categoria','=',$id],
                ['estado','=',1]])
        ->get()->count();
        // $data = DB::table('almacen.alm_subcat')
        // ->where([['id_categoria','=',$id],
        //         ['estado','=',1]])
        // ->get()->count();
        return response()->json($data);
    }
    //SubCategorias
    public function mostrar_sub_categorias(){
        $data = DB::table('almacen.alm_subcat')
        // ->select('alm_subcat.*', 'alm_cat_prod.descripcion as cat_descripcion',
        //         'alm_tp_prod.descripcion as tipo_descripcion')
        // ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_subcat.id_categoria')
        // ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
        ->where('estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_sub_categoria($id){
        $data = DB::table('almacen.alm_subcat')
        ->select('alm_subcat.*','sis_usua.nombre_corto')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_subcat.registrado_por')
            ->where([['alm_subcat.id_subcategoria', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function subcategoria_nextId($id_categoria){
        $cantidad = DB::table('almacen.alm_subcat')
        ->where('estado',1)->get()->count();
        $nextId = AlmacenController::leftZero(3,$cantidad);
        return $nextId;
    }
    public function guardar_sub_categoria(Request $request){
        $codigo = $this->subcategoria_nextId($request->id_categoria);
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user()->id_usuario;
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_subcat')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $data = DB::table('almacen.alm_subcat')->insertGetId(
                [
                    'codigo' => $codigo,
                    // 'id_categoria' => $request->id_categoria,
                    'descripcion' => $des,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                    'registrado_por' => $usuario
                ],
                    'id_subcategoria'
                );
        } else {
            $msj = 'No es posible guardar. Ya existe una subcategoria con dicha descripción';
        }
        return response()->json($msj);
    }
    public function update_sub_categoria(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_subcat')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $id_sub_cat = DB::table('almacen.alm_subcat')
            ->where('id_subcategoria',$request->id_subcategoria)
            ->update([ 'descripcion' => $des ]);
        } else {
            $msj = 'No es posible actualizar. Ya existe una subcategoria con dicha descripción';
        }
        return response()->json($msj);
    }
    public function anular_sub_categoria(Request $request,$id){
        $id_sub_cat = DB::table('almacen.alm_subcat')
        ->where('id_subcategoria',$id)
        ->update([ 'estado' => 7 ]);
        return response()->json($id_sub_cat);
    }
    public function subcat_revisar($id){
        $data = DB::table('almacen.alm_prod')
        ->where([['id_subcategoria','=',$id],
                ['estado','=',1]])
        ->get()->count();
        return response()->json($data);
    }
    //Clasificaciones
    public function mostrar_clasificaciones(){
        $data = DB::table('almacen.alm_clasif')
            ->select('alm_clasif.*')
            ->where([['alm_clasif.estado', '=', 1]])
                ->orderBy('id_clasificacion')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_clasificacion($id){
        $data = DB::table('almacen.alm_clasif')
            ->where([['alm_clasif.id_clasificacion', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_clasificacion(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_clasif')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_clasificacion = DB::table('almacen.alm_clasif')->insertGetId(
                [
                    'descripcion' => $des,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_clasificacion'
                );
        } else {
            $msj = 'No es posible guardar. Ya existe una clasificación con dicha descripción.';
        }
        return response()->json($msj);
    }
    public function update_clasificacion(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_clasif')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $data = DB::table('almacen.alm_clasif')
                ->where('id_clasificacion',$request->id_clasificacion)
                ->update([ 'descripcion' => $des ]);
        } else {
            $msj = 'No es posible guardar. Ya existe una clasificación con dicha descripción.';
        }
        return response()->json($msj);
    }
    public function anular_clasificacion(Request $request,$id){
        $data = DB::table('almacen.alm_clasif')
            ->where('id_clasificacion',$id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function clas_revisar($id){
        $data = DB::table('almacen.alm_prod')
        ->where([['id_clasif','=',$id],
                ['estado','=',1]])
        ->get()->count();
        return response()->json($data);
    }
    //Productos
    public function mostrar_prods(){
        $prod = DB::table('almacen.alm_prod')
            ->select('alm_prod.id_producto', 'alm_prod.codigo', 'alm_prod.descripcion',
            'alm_prod.codigo_anexo','alm_prod.id_unidad_medida','alm_prod_antiguo.cod_antiguo')
            ->leftjoin('almacen.alm_prod_antiguo','alm_prod_antiguo.id_producto','=','alm_prod.id_producto')
            ->get();
        $output['data'] = $prod;
        return response()->json($output);
    }
    public function mostrar_prods_almacen($id_almacen){
        $prod = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_prod.codigo_anexo','alm_prod.id_unidad_medida',
            'alm_prod_antiguo.cod_antiguo','alm_prod_ubi.stock',
            'alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_ubi.id_producto')
            ->leftjoin('almacen.alm_prod_antiguo','alm_prod_antiguo.id_producto','=','alm_prod_ubi.id_producto')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_almacen.id_almacen','=',$id_almacen],
                     ['alm_prod_ubi.stock','>',0]])
            ->get();
        $size = $prod->count();
        $output['data'] = $prod;
        return response()->json($output);
    }
    public function mostrar_productos(){
        $data = DB::table('almacen.alm_prod')
            ->select('alm_prod.id_producto','alm_prod.codigo','alm_prod.descripcion',
            'alm_subcat.codigo as cod_sub_cat','alm_subcat.descripcion as subcat_descripcion',
            'alm_cat_prod.codigo as cod_cat','alm_cat_prod.descripcion as cat_descripcion',
            'alm_tp_prod.id_tipo_producto','alm_tp_prod.descripcion as tipo_descripcion',
            'alm_clasif.id_clasificacion','alm_clasif.descripcion as clasif_descripcion')
            ->join('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
            ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
            ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->join('almacen.alm_clasif','alm_clasif.id_clasificacion','=','alm_prod.id_clasif')
            ->get();
            $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_producto($id){
        $producto = DB::table('almacen.alm_prod')
        ->select('alm_prod.*', 'alm_subcat.descripcion as subcat_descripcion',
                'alm_cat_prod.descripcion as cat_descripcion',
                'alm_tp_prod.descripcion as tipo_descripcion')
        ->join('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
        ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
        ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
        ->where([['alm_prod.id_producto', '=', $id]])
            ->get();
        
        $antiguos = DB::table('almacen.alm_prod_antiguo')
        ->where([['alm_prod_antiguo.id_producto', '=', $id]])
        ->orderBy('cod_antiguo')->get();

        $data = ["producto"=>$producto,"antiguos"=>$antiguos];
        return response()->json($data);
    }

    public function next_correlativo_prod($id_categoria, $id_subcategoria, $id_clasif)
    {
        $cantidad = DB::table('almacen.alm_prod')
            ->where([['id_categoria', '=', $id_categoria],
                    ['id_subcategoria', '=', $id_subcategoria],
                    ['id_clasif','=',$id_clasif]])
            ->get()->count();

        $subcat = DB::table('almacen.alm_subcat')->select('codigo')
            ->where('id_subcategoria', $id_subcategoria)
            ->first();

        $cat = DB::table('almacen.alm_cat_prod')->select('codigo')
            ->where('id_categoria', $id_categoria)
            ->first();

        $clasif = AlmacenController::leftZero(2,$id_clasif);
        $prod = AlmacenController::leftZero(3,$cantidad+1);
        $nextId = $cat->codigo.$subcat->codigo.$clasif.$prod;
        return $nextId;
    }

    public function guardar_imagen(Request $request)
    {
        $update = false;
        $namefile = "";
        if ($request->codigo !== "" && $request->codigo !== null){
            $nfile = $request->file('imagen');
            if (isset($nfile)){
                $namefile = $request->codigo.'.'.$nfile->getClientOriginalExtension();
                \File::delete(public_path('productos/'.$namefile));
                // if (file_exists(public_path('productos/'+$namefile))){
                //     unlink(public_path('productos/'+$namefile));
                // }else{
                //     dd('El archivo no existe.');
                // }
                Storage::disk('archivos')->put('productos/'.$namefile, \File::get($nfile));
            } else {
                $namefile = null;
            }
            $update = DB::table('almacen.alm_prod')
            ->where('id_producto', $request->id_producto)
            ->update(['imagen' => $namefile]);    
        }

        if ($update){
            $status = 1;
        } else {
            $status = 0;
        }
        $array = array("status"=>$status, "imagen"=>$namefile);
        return response()->json($array);
    }
    
    public function guardar_producto(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_correlativo_prod($request->id_categoria, $request->id_subcategoria, $request->id_clasif);
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_prod')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $id_producto = DB::table('almacen.alm_prod')->insertGetId(
                [
                    'codigo' => $codigo,
                    'codigo_anexo' => $request->codigo_anexo,
                    'codigo_proveedor' => $request->codigo_proveedor,
                    'id_clasif' => $request->id_clasif,
                    'id_subcategoria' => $request->id_subcategoria,
                    'descripcion' => $des,
                    'id_unidad_medida' => $request->id_unidad_medida,
                    'id_unid_equi' => $request->id_unid_equi,
                    'cant_pres' => $request->cant_pres,
                    'series' => ($request->series == '1')?true:false,
                    'afecto_igv' => ($request->afecto_igv == '1')?true:false,
                    'id_moneda' => $request->id_moneda,
                    'notas' => $request->notas,
                    'id_categoria' => $request->id_categoria,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_producto'
                );
        
            $id_item = DB::table('almacen.alm_item')->insertGetId(
                [   'id_producto' => $id_producto,
                    'codigo' => $codigo,
                    'fecha_registro' => $fecha
                ],  'id_item');
        } else {
            $msj = 'No es posible guardar. Ya existe un producto con dicha descripción.';
        }
        return response()->json(['msj'=>$msj,'id_producto'=>$id_producto]);
    }

    public function update_producto(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_prod')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $data = DB::table('almacen.alm_prod')
                ->where('id_producto', $request->id_producto)
                ->update([
                    // 'codigo' => $request->codigo,
                    'codigo_anexo' => $request->codigo_anexo,
                    'codigo_proveedor' => $request->codigo_proveedor,
                    'id_subcategoria' => $request->id_subcategoria,
                    'id_categoria' => $request->id_categoria,
                    'id_clasif' => $request->id_clasif,
                    'descripcion' => $des,
                    'id_unidad_medida' => $request->id_unidad_medida,
                    'id_unid_equi' => $request->id_unid_equi,
                    'cant_pres' => $request->cant_pres,
                    'series' => ($request->series == '1'?true:false),
                    'afecto_igv' => ($request->afecto_igv == '1'?true:false),
                    'id_moneda' => $request->id_moneda,
                    'notas' => $request->notas,
                ]);
        } else {
            $msj = 'No es posible actualizar. Ya existe un producto con la misma descripción.';
        }
        return response()->json(['msj'=>$msj,'id_producto'=>$request->id_producto]);
    }

    public function anular_producto(Request $request,$id){
        $data = DB::table('almacen.alm_prod')
            ->where('id_producto',$id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }


    //Tipo de Servicio
    public function mostrar_tp_servicios(){
        $data = DB::table('logistica.log_tp_servi')
            ->select('log_tp_servi.*')
            ->where([['log_tp_servi.estado', '=', 1]])
                ->orderBy('id_tipo_servicio')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_tp_servicio($id){
        $data = DB::table('logistica.log_tp_servi')
            ->select('log_tp_servi.*')
            ->where([['log_tp_servi.id_tipo_servicio', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_tp_servicio(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('logistica.log_tp_servi')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $data = DB::table('logistica.log_tp_servi')->insertGetId(
                [
                    'descripcion' => $des,
                    'estado' => 1
                ],
                    'id_tipo_servicio'
                );
        } else {
            $msj = 'No es posible guardar. Ya existe un tipo con la misma descripción.';
        }
        return response()->json($msj);
    }
    public function update_tp_servicio(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('logistica.log_tp_servi')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $data = DB::table('logistica.log_tp_servi')
                ->where('id_tipo_servicio', $request->id_tipo_servicio)
                ->update([ 'descripcion' => $des ]);
        } else {
            $msj = 'No es posible actualizar. Ya existe un tipo con la misma descripción.';
        }
        return response()->json($msj);
    }
    public function anular_tp_servicio(Request $request, $id)
    {
        $data = DB::table('logistica.log_tp_servi')
            ->where('id_tipo_servicio', $id)
            ->update([
                'descripcion' => $request->descripcion,
                'estado' => 7
            ]);
        return response()->json($data);
    }

    //Categoria Servicios
    public function mostrar_cat_servicios(){
        $data = DB::table('logistica.log_cat_serv')
            ->select('log_cat_serv.*','log_tp_servi.descripcion as tipo_descripcion')
            ->join('logistica.log_tp_servi','log_tp_servi.id_tipo_servicio','=','log_cat_serv.id_tipo_servicio')
                ->where([['log_cat_serv.estado', '=', 1]])
                ->orderBy('id_categoria')
                ->get();
        return response()->json($data);
    }
    public function mostrar_cat_servicio($id){
        $data = DB::table('logistica.log_cat_serv')
            ->select('log_cat_serv.*','log_tp_servi.descripcion as tipo_descripcion',
                     'log_tp_servi.id_tipo_servicio')
            ->join('logistica.log_tp_servi','log_tp_servi.id_tipo_servicio','=','log_cat_serv.id_tipo_servicio')
                ->where([['log_cat_serv.id_categoria', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_cat_servicio(Request $request){
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('logistica.log_cat_serv')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count == 0){
            $data = DB::table('logistica.log_cat_serv')->insertGetId(
                [
                    'descripcion' => $des,
                    'id_tipo_servicio' => $request->id_tipo_servicio,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                    'id_categoria'
                );
        } else {
            $msj = 'No es posible guardar. Ya existe una categoria con la misma descripción.';
        }
        return response()->json($msj);
    }
    public function update_cat_servicio(Request $request, $id){
        $des = strtoupper($request->descripcion);
        $msj = '';

        $count = DB::table('logistica.log_cat_serv')
        ->where([['descripcion','=',$des],['estado','=',1]])
        ->count();

        if ($count <= 1){
            $data = DB::table('logistica.log_cat_serv')->where('id_categoria', $id)
                ->update([ 'descripcion' => $des ]);
        } else {
            $msj = 'No es posible actualizar. Ya existe una categoria con la misma descripción.';
        }
        return response()->json($msj);
    }
    public function update_cat_servicio_anular(Request $request, $id){
        $data = DB::table('logistica.log_cat_serv')->where('id_categoria', $id)
            ->update([
                'estado' => 7
            ]);
        return response()->json($data);
    }

    //Catalogo de Servicios
    public function mostrar_servicios(){
        $data = DB::table('logistica.log_servi')
            ->orderBy('codigo')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_servicio($id){
        $data = DB::table('logistica.log_servi')
        ->select('log_servi.*','log_tp_servi.id_tipo_servicio',
                 'log_tp_servi.descripcion as tipo_descripcion')
        // ->join('logistica.log_cat_serv','log_cat_serv.id_categoria','=','log_servi.id_cat_servicio')
        ->join('logistica.log_tp_servi','log_servi.id_tipo_servicio','=','log_tp_servi.id_tipo_servicio')
            ->where([['log_servi.id_servicio', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function next_correlativo_ser($id_tipo_servicio){
        $cantidad = DB::table('logistica.log_servi')
            ->where([['log_servi.id_tipo_servicio', '=', $id_tipo_servicio]])
            ->get()->count();
        $tipo = AlmacenController::leftZero(2,$id_tipo_servicio);
        $serv = AlmacenController::leftZero(3,$cantidad+1);
        $nextId = $tipo."".$serv;
        return $nextId;
    }

    public function guardar_servicio(Request $request){
        $codigo = $this->next_correlativo_ser($request->id_tipo_servicio);
        $fecha = date('Y-m-d H:i:s');

        $id_servicio = DB::table('logistica.log_servi')->insertGetId(
            [
                'codigo' => $codigo,
                'descripcion' => $request->descripcion,
                'id_tipo_servicio' => $request->id_tipo_servicio,
                'estado' => $request->estado,
                'fecha_registro' => $fecha
            ],
                'id_servicio'
            );

        $id_item = DB::table('almacen.alm_item')->insertGetId(
            [
                'id_servicio' => $id_servicio,
                'codigo' => $codigo,
                'fecha_registro' => $fecha
            ],
                'id_item'
            );
            
        return response()->json($id_servicio);
    }
    
    public function update_servicio(Request $request){
        $data = DB::table('logistica.log_servi')
            ->where('id_servicio', $request->id_servicio)
            ->update([
                // 'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'id_tipo_servicio' => $request->id_tipo_servicio,
                'estado' => $request->estado
            ]);
        return response()->json($data);
    }

    public function anular_servicio(Request $request, $id){
        $data = DB::table('logistica.log_servi')
            ->where('id_servicio', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    /*Almacen*/
    public function mostrar_almacenes()
    {
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.*', 'sis_sede.id_empresa', 'sis_sede.descripcion as sede_descripcion',
            'alm_tp_almacen.descripcion as tp_almacen')
            ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('almacen.alm_tp_almacen','alm_tp_almacen.id_tipo_almacen','=','alm_almacen.id_tipo_almacen')
            ->where([['alm_almacen.estado', '=', 1]])
                ->orderBy('id_almacen')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_almacen($id)
    {
        $data = DB::table('almacen.alm_almacen')
        ->select('alm_almacen.*', 'sis_sede.descripcion as sede_descripcion',
        DB::raw("CONCAT(ubi_dis.descripcion,' - ',ubi_prov.descripcion,' - ',ubi_dpto.descripcion) as name_ubigeo"))
        ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->leftjoin('configuracion.ubi_dis','ubi_dis.id_dis','=','alm_almacen.ubigeo')
        ->leftjoin('configuracion.ubi_prov','ubi_prov.id_prov','=','ubi_dis.id_prov')
        ->leftjoin('configuracion.ubi_dpto','ubi_dpto.id_dpto','=','ubi_prov.id_dpto')
        ->where([['alm_almacen.id_almacen', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_almacen(Request $request)
    {
        $id_usuario = Auth::user()->id_usuario;
        $fecha = date('Y-m-d H:i:s');
        $id_almacen = DB::table('almacen.alm_almacen')->insertGetId(
            [
                'id_sede' => $request->id_sede,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
                'id_tipo_almacen' => $request->id_tipo_almacen,
                'codigo' => $request->codigo,
                'ubigeo' => $request->ubigeo,
                'estado' => 1,
                'registrado_por' => $id_usuario,
                'fecha_registro' => $fecha
            ],
                'id_almacen'
            );
        return response()->json($id_almacen);
    }

    public function update_almacen(Request $request)
    {
        $id_usuario = Auth::user()->id_usuario;
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.alm_almacen')->where('id_almacen', $request->id_almacen)
            ->update([
                'codigo' => $request->codigo,
                'id_sede' => $request->id_sede,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
                'id_tipo_almacen' => $request->id_tipo_almacen,
                'ubigeo' => $request->ubigeo,
                'registrado_por' => $id_usuario,
                'fecha_registro' => $fecha
            ]);
        return response()->json($data);
    }

    public function anular_almacen(Request $request, $id)
    {
        $data = DB::table('almacen.alm_almacen')->where('id_almacen', $id)
            ->update([
                'estado' => 7
            ]);
        return response()->json($data);
    }
    /* Estante */
    public function mostrar_estantes()
    {
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.*','alm_almacen.id_almacen',
            'alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_estantes_almacen($id)
    {
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.*', 'alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_estante.id_almacen', '=', $id]])
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_estante($id)
    {
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.*')
            ->where([['alm_ubi_estante.id_estante', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_estante(Request $request){
        $id_almacen = DB::table('almacen.alm_ubi_estante')->insertGetId(
            [
                'id_almacen' => $request->id_almacen,
                'codigo' => $request->codigo,
                'estado' => 1
            ],
                'id_estante'
            );
        return response()->json($id_almacen);
    }
    public function guardar_estantes(Request $request){
        $id_almacen = $request->id_almacen;
        $desde = $request->desde;
        $hasta = $request->hasta;

        $almacen = DB::table('almacen.alm_almacen')
        ->where('id_almacen',$request->id_almacen)
        ->first();

        for ($i=$desde; $i<=$hasta; $i++) { 
            $codigo = $almacen->codigo."-".AlmacenController::leftZero(2,$i);

            $exist = DB::table('almacen.alm_ubi_estante')
                ->where('codigo',$codigo)->get()->count();
            
            if ($exist === 0){
                $data = DB::table('almacen.alm_ubi_estante')->insertGetId([
                    'id_almacen' => $id_almacen,
                    'codigo' => $codigo,
                    'estado' => 1
                ],
                    'id_estante'
                );
            }
        }
        return response()->json($data);
    }
    public function update_estante(Request $request){
        $data = DB::table('almacen.alm_ubi_estante')
            ->where([['alm_ubi_estante.id_estante','=',$request->id_estante]])
            ->update([
                'id_almacen' => $request->id_almacen,
                'codigo' => $request->codigo
            ]);
        return response()->json($data);
    }
    public function anular_estante(Request $request, $id){
        $data = DB::table('almacen.alm_ubi_estante')
            ->where([['alm_ubi_estante.id_estante','=',$id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function revisar_estante($id){
        $data = DB::table('almacen.alm_ubi_nivel')
            ->where([['alm_ubi_nivel.id_estante','=',$id],
                    ['estado','=', 1]])
            ->get()->count();
        return response()->json($data);
    }
/* Nivel */
    public function mostrar_niveles()
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.*','alm_almacen.id_almacen',
            'alm_almacen.descripcion as alm_descripcion',
            'alm_ubi_estante.id_estante','alm_ubi_estante.codigo as cod_estante')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_niveles_estante($id)
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_estante.codigo as cod_estante')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_nivel.id_estante', '=', $id]])
            ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_nivel($id)
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.*','alm_almacen.id_almacen',
            'alm_ubi_estante.id_estante')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_nivel.id_nivel', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_nivel(Request $request){
        $id_almacen = DB::table('almacen.alm_ubi_nivel')->insertGetId(
            [
                'id_estante' => $request->id_estante,
                'codigo' => $request->codigo,
                'estado' => 1
            ],
                'id_nivel'
            );
        return response()->json($id_almacen);
    }
    public function guardar_niveles(Request $request){
        // ini_set('max_execution_time', 180);
        // set_time_limit(0);
        $abc = [0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z'];
        
        $desde = array_search(strtoupper($request->desde),$abc);
        $hasta = array_search(strtoupper($request->hasta),$abc);
        $i = 0;

        for ($i=$desde; $i<=$hasta; $i++) {
            $codigo = $request->cod_estante."-".$abc[$i];

            // $exist = DB::table('almacen.alm_ubi_nivel')
            //     ->where('codigo',$codigo)->first();
            
            // if (!isset($exist)){
                $data = DB::table('almacen.alm_ubi_nivel')->insertGetId([
                    'id_estante' => $request->id_estante,
                    'codigo' => $codigo,
                    'estado' => 1
                ],
                    'id_nivel'
                );
            // }
        }
        return response()->json($data);
    }
    public function update_nivel(Request $request){
        $data = DB::table('almacen.alm_ubi_nivel')
            ->where([['alm_ubi_nivel.id_nivel','=',$request->id_nivel]])
            ->update([
                'id_estante' => $request->id_estante,
                'codigo' => $request->codigo
            ]);
        return response()->json($data);
    }
    public function anular_nivel(Request $request, $id){
        $data = DB::table('almacen.alm_ubi_nivel')
            ->where([['alm_ubi_nivel.id_nivel','=',$id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function revisar_nivel($id){
        $data = DB::table('almacen.alm_ubi_posicion')
            ->where([['alm_ubi_posicion.id_nivel','=',$id],
                    ['estado','=', 1]])
            ->get()->count();
        return response()->json($data);
    }
    /* Posicion */
    public function mostrar_posiciones()
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_estante.codigo as cod_estante','alm_ubi_nivel.codigo as cod_nivel')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_posiciones_nivel($id)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_estante.codigo as cod_estante','alm_ubi_nivel.codigo as cod_nivel')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.id_nivel', '=', $id]])
            ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_posicion($id)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.*','alm_almacen.id_almacen',
            'alm_ubi_estante.id_estante','alm_ubi_nivel.id_nivel')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.id_posicion', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_posicion(Request $request){
        if ($request->id_nivel !== null){
            $id_posicion = DB::table('almacen.alm_ubi_posicion')->insertGetId(
                [
                    'id_nivel' => $request->id_nivel,
                    'codigo' => $request->codigo,
                    'estado' => 1
                ],
                    'id_posicion'
                );
        }
        return response()->json($id_posicion);
    }
    public function guardar_posiciones(Request $request){
        $cod_nivel = $request->cod_nivel;
        $desde = $request->desde;
        $hasta = $request->hasta;
        $i = 0;
        for ($i=$desde; $i<=$hasta; $i++) {
            $codigo = $cod_nivel."-".AlmacenController::leftZero(2,$i);

            $exist = DB::table('almacen.alm_ubi_posicion')
                ->where('codigo',$codigo)->get()->count();
            
            if ($exist === 0){
                $data = DB::table('almacen.alm_ubi_posicion')->insertGetId([
                    'id_nivel' => $request->id_nivel,
                    'codigo' => $codigo,
                    'estado' => 1
                ],
                    'id_posicion'
                );
            }
        }
        return response()->json($data);
    }

    public function anular_posicion(Request $request, $id){
        $data = DB::table('almacen.alm_ubi_posicion')
            ->where([['alm_ubi_posicion.id_posicion','=',$id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    
    public function almacen_posicion($id)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.id_posicion', '=', $id]])
                ->get();
        return response()->json($data);
    }
    /** Producto Ubicacion */
    public function mostrar_ubicaciones_producto($id)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.*','alm_almacen.codigo',
                'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.id_producto', '=', $id]])
            ->orderBy('cod_posicion')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_ubicacion($id)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.*','alm_almacen.descripcion as alm_descripcion')
                // 'alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.id_prod_ubi', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_ubicacion(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_almacen = DB::table('almacen.alm_prod_ubi')->insertGetId(
            [
                'id_producto' => $request->id_producto,
                'id_posicion' => $request->id_posicion,
                'stock' => $request->stock,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_prod_ubi'
            );
        return response()->json($id_almacen);
    }
    public function update_ubicacion(Request $request){
        $data = DB::table('almacen.alm_prod_ubi')
            ->where('id_prod_ubi', $request->id_prod_ubi)
            ->update([
                'id_posicion' => $request->id_posicion,
                'stock' => $request->stock
            ]);
        return response()->json($data);
    }
    public function anular_ubicacion(Request $request, $id){
        $data = DB::table('almacen.alm_prod_ubi')
            ->where([['alm_prod_ubi.id_prod_ubi','=',$id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    /**ProductoUbicacion Series */
    public function listar_series_producto($id)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->select('alm_prod_serie.*', 'alm_almacen.descripcion as alm_descripcion',
            DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia_com"),
            DB::raw("CONCAT('GR-',guia_ven.serie,'-',guia_ven.numero) as guia_ven"))
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_prod_serie.id_almacen')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','alm_prod_serie.id_guia_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','alm_prod_serie.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->where([['alm_prod_serie.id_prod', '=', $id]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_serie($id)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->select('alm_prod_serie.*')
            ->where([['alm_prod_serie.id_prod_serie', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_serie(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_almacen = DB::table('almacen.alm_prod_serie')->insertGetId(
            [
                'id_prod' => $request->id_prod,
                'id_almacen' => $request->id_almacen,
                'serie' => $request->serie,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_prod_serie'
            );
        return response()->json($id_almacen);
    }
    public function update_serie(Request $request){
        $data = DB::table('almacen.alm_prod_serie')
            ->where('id_prod_serie', $request->id_prod_serie)
            ->update([
                'id_prod' => $request->id_prod,
                'serie' => $request->serie
            ]);
        return response()->json($data);
    }
    public function anular_serie(Request $request, $id){
        $data = DB::table('almacen.alm_prod_serie')
            ->where([['alm_prod_serie.id_prod_serie','=',$id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    /* Tipo Almacen */
    public function mostrar_tipo_almacen(){
        $data = DB::table('almacen.alm_tp_almacen')->orderBy('id_tipo_almacen')->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tipo_almacenes($id){
        $data = DB::table('almacen.alm_tp_almacen')->orderBy('id_tipo_almacen')
            ->where([['alm_tp_almacen.id_tipo_almacen', '=', $id]])->get();
        return response()->json($data);
    }

    public function guardar_tipo_almacen(Request $request){
        $id_almacen = DB::table('almacen.alm_tp_almacen')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'estado' => 1
            ],
                'id_tipo_almacen'
            );
        return response()->json($id_almacen);
    }

    public function update_tipo_almacen(Request $request){
        $data = DB::table('almacen.alm_tp_almacen')->where('id_tipo_almacen', $request->id_tipo_almacen)
            ->update([
                'descripcion' => $request->descripcion,
                'estado' => 1
            ]);
        return response()->json($data);
    }
    public function anular_tipo_almacen($id){
        $data = DB::table('almacen.alm_tp_almacen')->where('id_tipo_almacen', $id)
            ->update([
                'estado' => 7
            ]);
        return response()->json($data);
    }

    /* Tipo de Movimiento */
    public function mostrar_tipos_mov()
    {
        $data = DB::table('almacen.tp_ope')
            ->where([['tp_ope.estado', '=', 1]])
                ->orderBy('id_operacion')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tipo_mov($id)
    {
        $data = DB::table('almacen.tp_ope')
        ->where([['tp_ope.id_operacion', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_tipo_mov(Request $request)
    {
        $id_operacion = DB::table('almacen.tp_ope')->insertGetId(
            [
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'cod_sunat' => $request->cod_sunat,
                'estado' => $request->estado,
            ],
                'id_operacion'
            );
        return response()->json($id_operacion);
    }

    public function update_tipo_mov(Request $request)
    {
        $data = DB::table('almacen.tp_ope')
            ->where('id_operacion', $request->id_operacion)
            ->update([
                'tipo' => $request->tipo,
                'cod_sunat' => $request->cod_sunat,
                'descripcion' => $request->descripcion
            ]);
        return response()->json($data);
    }

    public function anular_tipo_mov(Request $request, $id)
    {
        $data = DB::table('almacen.tp_ope')->where('id_operacion', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    /* Unidades de Medida */
    public function mostrar_unidades_med()
    {
        $data = DB::table('almacen.alm_und_medida')
            ->where([['alm_und_medida.estado', '=', 1]])
                ->orderBy('id_unidad_medida')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_unid_med($id)
    {
        $data = DB::table('almacen.alm_und_medida')
        ->where([['alm_und_medida.id_unidad_medida', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_unid_med(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_unidad_medida = DB::table('almacen.alm_und_medida')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'abreviatura' => $request->abreviatura,
                'estado' => 1,
                // 'fecha_registro' => $fecha,
            ],
                'id_unidad_medida'
            );
        return response()->json($id_unidad_medida);
    }

    public function update_unid_med(Request $request)
    {
        $data = DB::table('almacen.alm_und_medida')
            ->where('id_unidad_medida', $request->id_unidad_medida)
            ->update([
                'abreviatura' => $request->abreviatura,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
            ]);
        return response()->json($data);
    }

    public function anular_unid_med(Request $request, $id)
    {
        $data = DB::table('almacen.alm_und_medida')->where('id_unidad_medida', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    
    public function add_unid_med(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_unidad_medida = DB::table('almacen.alm_und_medida')->insertGetId(
            [
                'descripcion' => $request->descripcion_unidad,
                'abreviatura' => $request->abreviatura_unidad,
                'estado' => 1
            ],
                'id_unidad_medida'
            );
        $unid = DB::table('almacen.alm_und_medida')
            ->where('estado',1)->orderBy('descripcion','asc')->get();

        $html = '';
        foreach($unid as $unid){
            if ($id_unidad_medida == $unid->id_unidad_medida){
                $html .= '<option value="'.$unid->id_unidad_medida.'" selected>'.$unid->descripcion.'</option>';
            } else {
                $html .= '<option value="'.$unid->id_unidad_medida.'">'.$unid->descripcion.'</option>';
            }
        }
        return json_encode($html);
    }
    /**Guia Compra */
    public function listar_guias_compra()
    {
        $data = DB::table('almacen.guia_com')
        ->select('guia_com.*','adm_contri.razon_social','adm_estado_doc.estado_doc as des_estado')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
            ->where([['guia_com.estado','!=',7]])
            ->orderBy('fecha_emision','desc')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_guias_proveedor($id_proveedor)
    {
        $data = DB::table('almacen.guia_com')
        ->select('guia_com.*','adm_contri.razon_social','adm_estado_doc.estado_doc as des_estado')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
            ->where([['guia_com.id_proveedor','=',$id_proveedor],['guia_com.estado','!=',7]])
            ->orderBy('fecha_emision','desc')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_guia_compra($id){
        $guia = DB::table('almacen.guia_com')
        ->select('guia_com.*','adm_estado_doc.estado_doc AS des_estado',
        'sis_usua.nombre_corto','adm_contri.nro_documento','adm_contri.razon_social','doc_com.serie as doc_serie',
        'doc_com.numero as doc_numero','cont_tp_doc.abreviatura as tp_doc','doc_com.id_doc_com',
        'tp_doc_almacen.abreviatura as tp_doc_abreviatura')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
        ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','guia_com.registrado_por')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftjoin('almacen.doc_com_guia','doc_com_guia.id_guia_com','=','guia_com.id_guia')
        ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','doc_com_guia.id_doc_com')
        ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
        ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
        ->where([['guia_com.id_guia', '=', $id]])
            ->get();
        return response()->json($guia);
    }
    public function guardar_guia_compra(Request $request)
    {
        $usuario = Auth::user();
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_com')->insertGetId(
            [
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                'id_guia_clas' => $request->id_guia_clas,
                'id_operacion' => $request->id_operacion,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'transportista' => $request->transportista,
                'fecha_traslado' => $request->fecha_traslado,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'placa' => $request->placa,
                'usuario' => $request->usuario,
                'registrado_por' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_guia'
            );
        // $output['data'] = 'id_guia'
        return response()->json(["id_guia"=>$id_guia,"id_proveedor"=>$request->id_proveedor]);
    }

    public function update_guia_compra(Request $request)
    {
        $data = DB::table('almacen.guia_com')
            ->where('id_guia', $request->id_guia)
            ->update([
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_operacion' => $request->id_operacion,
                'id_guia_clas' => $request->id_guia_clas,
                'id_motivo' => $request->id_motivo,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'transportista' => $request->transportista,
                'fecha_traslado' => $request->fecha_traslado,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'placa' => $request->placa,
                'usuario' => $request->usuario
            ]);
        // return response()->json($data);
        return response()->json(["id_guia"=>$request->id_guia,"id_proveedor"=>$request->id_proveedor]);
    }

    public function anular_guia_compra(Request $request)
    {
        $rspta = '';
        $ing = DB::table('almacen.mov_alm')
            ->where([['id_guia_com','=',$request->id_guia_com],['estado','=',1]])
            ->first();
        
        if (isset($ing)){
            //si el ingreso no esta revisado
            if ($ing->revisado == 0){
                //Anula ingreso
                DB::table('almacen.mov_alm')
                    ->where('id_mov_alm', $ing->id_mov_alm)
                    ->update([ 'estado' => 7 ]);
                //Anula ingreso detalle
                $detalle = DB::table('almacen.guia_com_det')
                ->where('id_guia_com', $request->id_guia_com)->get();
    
                foreach($detalle as $det){
                    DB::table('almacen.mov_alm_det')
                    ->where('id_guia_com_det', $det->id_guia_com_det)
                    ->update([ 'estado' => 7 ]);
                }

                //motivo de la anulacion
                $mot = DB::table('almacen.motivo_anu')
                ->where('id_motivo',$request->id_motivo_obs)
                ->first();

                $id_usuario = Auth::user()->id_usuario;
                $obs = $mot->descripcion.'. '.$request->observacion;
                //Agrega observacion a la guia
                $id_obs = DB::table('almacen.guia_com_obs')->insertGetId(
                    [
                        'id_guia_com'=>$request->id_guia_com,
                        'observacion'=>$obs,
                        'registrado_por'=>$id_usuario,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                    ],
                        'id_obs'
                );
                //Anula la Guia
                $data = DB::table('almacen.guia_com')
                    ->where('id_guia', $request->id_guia_com)
                    ->update([ 'estado' => 7 ]);
                //Anula la Guia Detalle
                $detalle = DB::table('almacen.guia_com_det')
                    ->where('id_guia_com', $request->id_guia_com)
                    ->update([ 'estado' => 7 ]);
                //Anula la Guia OC
                $ordenes = DB::table('almacen.guia_com_oc')
                    ->where([['id_guia_com','=',$request->id_guia_com],
                             ['estado','!=',7]])
                    ->get();

                foreach($ordenes as $oc){
                    DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra',$oc->id_oc)
                    ->update(['en_almacen' => false]);
                }

                $ocs = DB::table('almacen.guia_com_oc')
                    ->where('id_guia_com',$request->id_guia_com)
                    ->update([ 'estado' => 7 ]);
                //Anula la Guia Doc
                $ocs = DB::table('almacen.doc_com_guia')
                    ->where('id_guia_com',$request->id_guia_com)
                    ->update([ 'estado' => 7 ]);
                //Anula detalle 
                $detalle = DB::table('almacen.guia_com_det')
                    ->where('id_guia_com', $request->id_guia_com)->get();

                foreach($detalle as $det){
                    //cambiar estado OC detalle
                    if ($det->id_oc_det !== null){
                        DB::table('logistica.log_det_ord_compra')
                        ->where('id_detalle_orden',$det->id_oc_det)
                        ->update([ 'estado' => 1 ]);//Elaborado

                        //cambiar estado OC en_almacen = false
                        DB::table('logistica.log_det_ord_compra')
                        ->where('id_detalle_orden',$det->id_oc_det)
                        ->update([ 'estado' => 1 ]); 
                    }
                }
                
                $rspta = 'Se anuló la Guía y el Ingreso generado';
                $trans = DB::table('almacen.trans')
                ->where('id_guia_com',$request->id_guia_com)
                ->first();

                if (isset($trans)){
                    DB::table('almacen.trans')
                    ->update(['id_guia_com'=>null,'estado'=>1]);
                }
            } 
            //si el ingreso está revisado u observado
            else {
                $des = ($ing->revisado == 1 ? 'Revisado' : 'Observado');
                $rspta = 'No es posible anular!. El ingreso fue '.$des.' por el Jefe de Almacén';
            }
        } 
        else {
            //motivo de la anulacion
            $mot = DB::table('almacen.motivo_anu')
            ->where('id_motivo',$request->id_motivo_obs)
            ->first();

            $id_usuario = Auth::user()->id_usuario;
            $obs = $mot->descripcion.'. '.$request->observacion;
            //Agrega observacion a la guia
            $id_obs = DB::table('almacen.guia_com_obs')->insertGetId(
                [
                    'id_guia_com'=>$request->id_guia_com,
                    'observacion'=>$obs,
                    'registrado_por'=>$id_usuario,
                    'fecha_registro'=>date('Y-m-d H:i:s')
                ],
                    'id_obs'
            );
            //Anula la Guia
            $data = DB::table('almacen.guia_com')
                ->where('id_guia', $request->id_guia_com)
                ->update([ 'estado' => 7 ]);
            //Anula la Guia Detalle
            $detalle = DB::table('almacen.guia_com_det')
                ->where('id_guia_com', $request->id_guia_com)
                ->update([ 'estado' => 7 ]);
            //Anula la Guia OC
            $ordenes = DB::table('almacen.guia_com_oc')
                    ->where([['id_guia_com','=',$request->id_guia_com],
                             ['estado','!=',7]])
                    ->get();

            foreach($ordenes as $oc){
                DB::table('logistica.log_ord_compra')
                ->where('id_orden_compra',$oc->id_oc)
                ->update(['en_almacen' => false]);
            }
            
            $ocs = DB::table('almacen.guia_com_oc')
                ->where('id_guia_com',$request->id_guia_com)
                ->update([ 'estado' => 7 ]);
            //Anula la Guia Doc
            $ocs = DB::table('almacen.doc_com_guia')
                ->where('id_guia_com',$request->id_guia_com)
                ->update([ 'estado' => 7 ]);
            //Anula detalle 
            $detalle = DB::table('almacen.guia_com_det')
                ->where('id_guia_com', $request->id_guia_com)->get();

            foreach($detalle as $det){
                //cambiar estado OC detalle
                if ($det->id_oc_det !== null){
                    //cambiar estado OC en_almacen = false
                    DB::table('logistica.log_det_ord_compra')
                    ->where('id_detalle_orden',$det->id_oc_det)
                    ->update([ 'estado' => 1 ]); 
                }
            }
            $rspta = 'La Guía fue anulada correctamente';

        }
        return response()->json($rspta);
    }
    public static function nextMovimiento($tipo, $fecha, $id_alm){
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $tp = '';
        switch($tipo){
            case 0: $tp = 'Ini';break;
            case 1: $tp = 'Ing';break;
            case 2: $tp = 'Sal';break;
            default:break;
        }

        $data = DB::table('almacen.mov_alm')
        ->where([['id_tp_mov','=',$tipo],
                ['id_almacen','=',$id_alm],
                ['estado','=',1]])
        ->whereYear('fecha_emision','=',$yyyy)
        // ->whereMonth('fecha_emision','=',$mes)
        ->count();
        
        $alm = DB::table('almacen.alm_almacen')
        ->where('id_almacen',$id_alm)->first();

        $correlativo = AlmacenController::leftZero(3, $data+1);
        
        $codigo = $tp.'-'.$alm->codigo.'-'.$anio.'-'.$correlativo;

        return $codigo;
    }
    /**Generar Ingreso */
    public function generar_ingreso($id_guia){
        
        $fecha = date('Y-m-d H:i:s');
        $fecha_emision = date('Y-m-d');
        $id_usuario = Auth::user()->id_usuario;
        $id_ingreso = 0;

        //verifica si existe un ingreso ya generado
        $ingreso = DB::table('almacen.mov_alm')
        ->where([['id_guia_com','=',$id_guia],['estado','=',1]])
        ->first();

        if (!isset($ingreso)){
            //obtiene la guia
            $guia = DB::table('almacen.guia_com')->where('id_guia',$id_guia)->first();
            //obtiene el detalle
            $detalle = DB::table('almacen.guia_com_det')
                ->select('guia_com_det.*','log_valorizacion_cotizacion.precio_sin_igv')//cambiar a precio_sin_igv
                ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
                ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
                ->where([['guia_com_det.id_guia_com','=',$id_guia],
                        ['guia_com_det.estado','=',1]])->get()->toArray();
    
            $codigo = AlmacenController::nextMovimiento(1,
                            $guia->fecha_almacen,
                            $guia->id_almacen);
            
            $doc = DB::table('almacen.doc_com_guia')
            ->where('id_guia_com',$id_guia)
            ->first();
    
            $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $guia->id_almacen,
                    'id_tp_mov' => 1,//Ingresos
                    'codigo' => $codigo,
                    'fecha_emision' => $guia->fecha_almacen,
                    'id_guia_com' => $guia->id_guia,
                    'id_doc_com' => (isset($doc) ? $doc->id_doc_com : null),
                    'id_operacion' => $guia->id_operacion,
                    'revisado' => 0,
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm'
                );
            // $nuevo_detalle = [];
            $cant = 0;
    
            // foreach ($detalle as $det){
            //     $exist = false;
            //     foreach ($nuevo_detalle as $nue => $value){
            //         if ($det->id_producto == $value['id_producto']){
            //             $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
            //             $nuevo_detalle[$nue]['valorizacion'] = floatval($value['valorizacion']) + floatval($det->total);
            //             $exist = true;
            //         }
            //     }
            //     if ($exist === false){
            //         $nuevo = [
            //             'id_producto' => $det->id_producto,
            //             'id_posicion' => $det->id_posicion,
            //             'id_oc_det' => (isset($det->id_oc_det)) ? $det->id_oc_det : 0,
            //             'cantidad' => floatval($det->cantidad),
            //             'valorizacion' => floatval($det->total)
            //             ];
            //         array_push($nuevo_detalle, $nuevo);
            //     }
            // }
    
            foreach ($detalle as $det){
                $prec = ($det->precio_sin_igv !== null ? $det->precio_sin_igv : $det->unitario);
                $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                    [
                        'id_mov_alm' => $id_ingreso,
                        'id_producto' => $det->id_producto,
                        'id_posicion' => $det->id_posicion,
                        'cantidad' => $det->cantidad,
                        'valorizacion' => (floatval($det->cantidad) * floatval($prec)),
                        'usuario' => $id_usuario,
                        'id_guia_com_det' => $det->id_guia_com_det,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                        'id_mov_alm_det'
                    );
                    
                if ($det->id_posicion !== null){
                    
                    $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([['id_producto','=',$det->id_producto],
                                ['id_posicion','=',$det->id_posicion]])
                        ->first();
                    //traer stockActual
                    $saldo = $this->saldo_actual($det->id_producto, $det->id_posicion);
                    $costo = $this->costo_promedio($det->id_producto, $det->id_posicion);
    
                    if (!isset($ubi->id_posicion)){//si no existe -> creo la ubicacion
                        DB::table('almacen.alm_prod_ubi')->insert([
                            'id_producto' => $det->id_producto,
                            'id_posicion' => $det->id_posicion,
                            'stock' => $saldo,
                            'costo_promedio' => $costo,
                            'estado' => 1,
                            'fecha_registro' => $fecha
                            ]);
                    } else {
                        DB::table('almacen.alm_prod_ubi')
                        ->where('id_prod_ubi',$ubi->id_prod_ubi)
                        ->update([  'stock' => $saldo,
                                    'costo_promedio' => $costo
                                ]);
                    }
                }
                if ($det->id_oc_det !== null && $det->id_oc_det > 0){
                    //cambiar estado orden
                    DB::table('logistica.log_det_ord_compra')
                    ->where('id_detalle_orden',$det->id_oc_det)
                    ->update(['estado'=>6]);//En Almacen
                    
                    // //cambiar estado requerimiento
                    // DB::table('almacen.alm_det_req')
                    // ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_valorizacion_cotizacion','=','log_valorizacion_cotizacion.id_valorizacion_cotizacion')
                    // ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
                    // ->where('log_det_ord_compra.id_detalle_orden',$det->id_oc_det)
                    // ->update(['estado'=>6]);//En Almacen
                }
            }
    
            $ocs = DB::table('almacen.guia_com_oc')
            ->where([['id_guia_com','=',$id_guia],['estado','=',1]])
            ->get();
    
            foreach($ocs as $oc){
                $ingresadas = DB::table('logistica.log_det_ord_compra')
                ->where([['id_orden_compra','=',$oc->id_oc],
                         ['estado','=',6]])
                ->count();
    
                $todas = DB::table('logistica.log_det_ord_compra')
                ->where([['id_orden_compra','=',$oc->id_oc],
                         ['estado','!=',7]])
                ->count();
                
                if ($todas == $ingresadas){
                    DB::table('logistica.log_ord_compra')
                    ->where('id_orden_compra',$oc->id_oc)
                    ->update(['en_almacen'=>true]);
                }
            }
            //cambiar estado guiacom
            DB::table('almacen.guia_com')
                ->where('id_guia',$id_guia)->update(['estado'=>9]);//Procesado    
        }

        return response()->json($id_ingreso);
    }
    public function req_almacen($id_oc_det){
        $data = DB::table('almacen.alm_det_req')
        ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
        ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_valorizacion_cotizacion','=','log_valorizacion_cotizacion.id_valorizacion_cotizacion')
        ->where('log_det_ord_compra.id_detalle_orden','=',$id_oc_det)
        // ->update(['estado'=>6]);//En Almacen
        ->get();
        // ->update(['alm_det_req.estado' => 6]);//En almacen
        return $data;
    }
    public function id_item($id_producto){
        $item = DB::table('almacen.alm_item')
        ->where('alm_item.id_producto',$id_producto)
        ->first();
        return $item->id_item;
    }
    public function id_ingreso($id_guia){
        $ing = DB::table('almacen.mov_alm')
        ->where('mov_alm.id_guia_com',$id_guia)
        ->first();
        return response()->json((isset($ing) ? $ing->id_mov_alm : 0));
    }
    public function id_ingreso_transformacion($id_transformacion){
        $ing = DB::table('almacen.mov_alm')
        ->where([['mov_alm.id_transformacion','=',$id_transformacion],
                ['id_tp_mov','=',1],//ingreso
                ['estado','=',1]])
        ->first();
        return response()->json($ing->id_mov_alm);
    }
    public function id_salida_transformacion($id_transformacion){
        $ing = DB::table('almacen.mov_alm')
        ->where([['mov_alm.id_transformacion','=',$id_transformacion],
                ['id_tp_mov','=',2],//salida
                ['estado','=',1]])
        ->first();
        return response()->json($ing->id_mov_alm);
    }
    public function get_ingreso($id){
        $ingreso = DB::table('almacen.mov_alm')
            ->select('mov_alm.*','alm_almacen.descripcion as des_almacen',
            DB::raw("CONCAT(tp_doc_almacen.abreviatura,'-',guia_com.serie,'-',guia_com.numero) as guia"),
            DB::raw("CONCAT(cont_tp_doc.abreviatura,'-',doc_com.serie,'-',doc_com.numero) as doc"),
            'doc_com.fecha_emision as doc_fecha_emision','tp_doc_almacen.descripcion as tp_doc_descripcion',
            'guia_com.fecha_emision as fecha_guia','sis_usua.usuario as nom_usuario',
            'adm_contri.razon_social','adm_contri.direccion_fiscal','adm_contri.nro_documento','tp_ope.cod_sunat',
            'tp_ope.descripcion as ope_descripcion','empresa.razon_social as empresa_razon_social',
            'empresa.nro_documento as ruc_empresa','doc_com.tipo_cambio','sis_moneda.descripcion as des_moneda',
            'sis_usua.nombre_corto as persona','transformacion.codigo as cod_transformacion',
            'transformacion.fecha_transformacion',//'transformacion.serie','transformacion.numero',
            'trans.codigo as trans_codigo','alm_origen.descripcion as trans_almacen_origen')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('contabilidad.adm_contri as empresa','empresa.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->leftjoin('almacen.transformacion','transformacion.id_transformacion','=','mov_alm.id_transformacion')
            ->leftjoin('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','mov_alm.id_operacion')
            ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
            ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
            ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->leftjoin('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
            ->leftjoin('almacen.trans','trans.id_guia_com','=','mov_alm.id_guia_com')
            ->leftjoin('almacen.alm_almacen as alm_origen','alm_origen.id_almacen','=','trans.id_almacen_origen')
            ->where('mov_alm.id_mov_alm',$id)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.codigo_anexo','alm_prod.descripcion',
            'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura',
            'sis_moneda.simbolo','log_valorizacion_cotizacion.subtotal','guia_com_det.unitario',
            'guia_com_det.unitario_adicional','alm_prod.series')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_prod.id_moneda')
            ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([['mov_alm_det.id_mov_alm','=',$id],['mov_alm_det.estado','=',1]])
            ->get();
        $ocs = [];
        if ($ingreso !== null){
            $ocs = DB::table('almacen.guia_com_oc')
                ->select('log_ord_compra.codigo')
                ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
                ->where([['guia_com_oc.id_guia_com','=',$ingreso->id_guia_com],
                        ['guia_com_oc.estado','=',1]])
                ->get();
        }

        return ['ingreso'=>$ingreso,'detalle'=>$detalle,'ocs'=>$ocs];
    }
    public function imprimir($id_ing){
        $result = $this->get_ingreso($id_ing);
        $ingreso = $result['ingreso'];
        // $detalle = $result->detalle;
        // $ocs = $result->ocs;
        return $ingreso->codigo;
    }
    public function imprimir_ingreso($id_ing){

        $id = $this->decode5t($id_ing);
        $result = $this->get_ingreso($id);
        $ingreso = $result['ingreso'];
        $detalle = $result['detalle'];
        $ocs = $result['ocs'];

        $cod_ocs = '';
        foreach($ocs as $oc){
            if ($cod_ocs == ''){
                $cod_ocs .= $oc->codigo;
            } else {
                $cod_ocs .= ', '.$oc->codigo;
            }
        }
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');

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
                <table width="100%">
                    <tr>
                        <td>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$ingreso->ruc_empresa.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$ingreso->empresa_razon_social.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">.::Sistema ERP v1.0::.</p>
                        </td>
                        <td>
                            <p style="text-align:right;font-size:10px;margin:0px;">Fecha: '.$fecha_actual.'</p>
                            <p style="text-align:right;font-size:10px;margin:0px;">Hora : '.$hora_actual.'</p>
                        </td>
                    </tr>
                </table>
                <h3 style="margin:0px; padding:0px;"><center>INGRESO A ALMACÉN</center></h3>
                <h5><center>'.$ingreso->id_almacen.' - '.$ingreso->des_almacen.'</center></h5>
                
                <table border="0">
                    <tr>
                        <td class="subtitle">Ingreso N°</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$ingreso->codigo.'</td>
                        <td>Fecha Ingreso</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->fecha_emision.'</td>
                    </tr>
                ';
                if ($ingreso->guia !== '--'){
                    $html.='
                    <tr>
                        <td class="subtitle">Guía N°</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$ingreso->guia.'</td>
                        <td>Fecha Guía</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->fecha_guia.'</td>
                    </tr>';
                }
                if ($ingreso->doc !== '--'){
                    $html.='<tr>
                        <td width=110px>Documento</td>
                        <td width=10px>:</td>
                        <td width=300px>'.$ingreso->doc.'</td>
                        <td width=120px>Fecha Documento</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->doc_fecha_emision.'</td>
                    </tr>';
                }
                if ($ingreso->cod_transformacion !== null){
                    $html.='<tr>
                        <td width=110px>Transformación</td>
                        <td width=10px>:</td>
                        <td width=300px>'.$ingreso->cod_transformacion.' ('.$ingreso->serie.'-'.$ingreso->numero.')</td>
                        <td width=150px>Fecha Transformación</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->fecha_transformacion.'</td>
                    </tr>';
                }
                if ($ingreso->trans_codigo !== null){
                    $html.='<tr>
                        <td width=110px>Transferencia</td>
                        <td width=10px>:</td>
                        <td width=300px>'.$ingreso->trans_codigo.'</td>
                        <td width=150px>Almacén Origen</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->trans_almacen_origen.'</td>
                    </tr>';
                }
                $html.='
                    <tr>
                        <td>Proveedor</td>
                        <td>:</td>
                    ';
                    if ($cod_ocs !== ''){
                        $html.='
                            <td>'.$ingreso->nro_documento.' - '.$ingreso->razon_social.'</td>
                            <td>Orden de Compra</td>
                            <td>:</td>
                            <td>'.$cod_ocs.'</td>
                        ';
                    } else {
                        $html.='<td colSpan="3">'.$ingreso->nro_documento.' - '.$ingreso->razon_social.'</td>
                        ';
                    }
                    $html.='
                    </tr>
                    <tr>
                        <td class="subtitle">Tipo Movim.</td>
                        <td>:</td>
                        <td colSpan="4">'.$ingreso->cod_sunat.' '.$ingreso->ope_descripcion.'</td>
                    </tr>
                    <tr>
                        <td>Responsable</td>
                        <td>:</td>
                        <td>'.$ingreso->persona.'</td>
                    </tr>
                </table>
                <br/>
                <table id="detalle">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Código</th>
                            <th width=40% >Descripción</th>
                            <th>Posición</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>Mnd.</th>
                            <th>Valor.</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $i = 1;

                    foreach($detalle as $det){
                        $series = '';
                        if ($det->series){
                            $det_series = DB::table('almacen.alm_prod_serie')
                            ->where([['alm_prod_serie.id_prod','=',$det->id_producto],
                                     ['alm_prod_serie.id_guia_det','=',$det->id_guia_com_det]])
                            ->get();
                
                            if (isset($det_series)){
                                foreach($det_series as $s){
                                    if ($series !== ''){
                                        $series.= ', '.$s->serie;
                                    } else {
                                        $series = 'Serie(s): '.$s->serie;
                                    }
                                }
                            }
                        }
                        $html.='
                        <tr>
                            <td class="right">'.$i.'</td>
                            <td>'.$det->codigo.'</td>
                            <td>'.$det->descripcion.' '.$series.'</td>
                            <td>'.$det->cod_posicion.'</td>
                            <td class="right">'.$det->cantidad.'</td>
                            <td>'.$det->abreviatura.'</td>
                            <td>'.$det->simbolo.'</td>
                            <td class="right">'.$det->valorizacion.'</td>
                        </tr>';
                        $i++;
                    }
                    $html.='</tbody>
                </table>
                <p style="text-align:right;font-size:11px;">Elaborado por: '.$ingreso->nom_usuario.' '.$ingreso->fecha_registro.'</p>

            </body>
        </html>';
        
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
        return $pdf->download('ingreso.pdf');
    }
    public function mostrar_ingreso($id){
        $ingreso = DB::table('almacen.mov_alm')
            ->select('mov_alm.*','alm_almacen.descripcion as des_almacen',
            DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
            'guia_com.fecha_emision as fecha_guia','sis_usua.usuario as nom_usuario')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
            ->where('mov_alm.id_mov_alm',$id)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->where('mov_alm_det.estado',1)
            ->get();

        return response()->json(['ingreso'=>$ingreso,'detalle'=>$detalle]);
    }
    /**Guia Compra Transportista */
    public function mostrar_transportistas($id){
        $data = DB::table('almacen.guia_com_tra')
        ->select('guia_com_tra.*','adm_contri.razon_social')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com_tra.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where([['guia_com_tra.id_guia', '=', $id]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_transportista($id){
        $data = DB::table('almacen.guia_com_tra')
        ->where([['guia_com_tra.id_guia_com_tra', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_transportista(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_com_tra')->insertGetId(
            [
                'id_guia' => $request->id_guia,
                'serie' => $request->serie_tra,
                'numero' => $request->numero_tra,
                'id_proveedor' => $request->id_proveedor_tra,
                'fecha_emision' => $request->fecha_emision_tra,
                'referencia' => $request->referencia,
                'placa' => $request->placa,
                'usuario' => 3,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_guia_com_tra'
            );
        return response()->json($id_guia);
    }

    public function update_transportista(Request $request)
    {
        $data = DB::table('almacen.guia_com_tra')
            ->where('id_guia_com_tra', $request->id_guia_com_tra)
            ->update([
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'referencia' => $request->referencia,
                'placa' => $request->placa,
                // 'usuario' => 3,
            ]);
        return response()->json($data);
    }

    public function anular_transportista(Request $request, $id)
    {
        $data = DB::table('almacen.guia_com_tra')->where('id_guia_com_tra', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function verifica_posiciones($id_guia){
        $detalle = DB::table('almacen.guia_com_det')
            ->where('id_guia_com',$id_guia)->get();
        $pos = false;
        foreach($detalle as $d){
            if ($d->id_posicion == null){
                $pos = true;
            }
        }
        return ($pos) ? 'Debe ingresar las posiciones de todos los items' : '';
    }
    /**Guia Detalle */
    public function listar_guia_detalle($id){
        $data = DB::table('almacen.guia_com_det')
        ->select('guia_com_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_und_medida.abreviatura','alm_prod.series','log_ord_compra.codigo AS cod_orden',
        DB::raw("CONCAT(guia_ven.serie,'-',guia_ven.numero) as guia_ven"))
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_com_det.id_posicion')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->leftjoin('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
        ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','guia_com_det.id_guia_ven_det')
        ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
        ->where([['guia_com_det.id_guia_com', '=', $id],
                 ['guia_com_det.estado','=',1]])
            ->get();

        $html = '';
        $suma = 0;
        $chk = '';

        $guia = DB::table('almacen.guia_com')
        ->where('id_guia',$id)->first();

        //listar posiciones que no estan enlazadas con ningun producto
        $posiciones = DB::table('almacen.alm_ubi_posicion')
        ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
        ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_posicion','=','alm_ubi_posicion.id_posicion')
        ->leftjoin('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->leftjoin('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['alm_prod_ubi.id_posicion','=',null],
                ['alm_ubi_posicion.estado','=',1],
                ['alm_almacen.id_almacen','=',$guia->id_almacen]])
        ->get();
        
        foreach($data as $det){
            $id_guia_det = $det->id_guia_com_det;
            $oc = $det->cod_orden;
            $codigo = $det->codigo;
            $descripcion = $det->descripcion;
            $cantidad = $det->cantidad;
            $abrev = $det->abreviatura;
            $id_posicion = $det->id_posicion;
            $unitario = (($det->unitario_adicional !== null && $det->unitario_adicional > 0)
                        ? ($det->unitario + $det->unitario_adicional)
                        : $det->unitario);
            $total = $unitario * $det->cantidad;
            $suma += $total;
            $tiene = strlen($oc);

            //jalar posicion relacionada con el producto
            $posicion = DB::table('almacen.alm_prod_ubi')
            ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            // ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.id_producto','=',$det->id_producto],
            ['alm_prod_ubi.estado','=',1],
            ['alm_ubi_estante.id_almacen','=',$guia->id_almacen]])
            ->get();
            $count = count($posicion);
            $o = false;
            if ($count > 0){
                $posiciones = $posicion;
                $o = true;
            }
            $chk = ($det->series ? 'true' : 'false');
            $series = '';
            $nro_series = 0;

            if ($chk == 'true'){
                $det_series = DB::table('almacen.alm_prod_serie')
                ->where([['alm_prod_serie.id_prod','=',$det->id_producto],
                         ['alm_prod_serie.id_guia_det','=',$id_guia_det],
                         ['alm_prod_serie.estado','=',1]])
                ->get();
    
                if (isset($det_series)){
                    foreach($det_series as $s){
                        if ($s->serie !== 'true'){
                            $nro_series++;
                            if ($series !== ''){
                                $series.= ', '.$s->serie;
                            } else {
                                $series = 'Serie(s): '.$s->serie;
                            }
                        }
                    }
                }
            }

            $html .= 
            '<tr id="reg-'.$id_guia_det.'">
                <td>'.$oc.'</td>
                <td>'.$det->guia_ven.'</td>
                <td><input type="text" class="oculto" name="series" value="'.$chk.'"/><input type="number" class="oculto" name="nro_series" value="'.$nro_series.'"/>'.$codigo.'</td>
                <td>'.$descripcion.' '.$series.'</td>
                <td>
                    <select class="input-data" name="id_posicion" disabled="true">
                        <option value="0">Elija una opción</option>';
                        foreach ($posiciones as $row) {
                            if ($o){
                                $html.='<option value="'.$row->id_posicion.'" selected>'.$row->codigo.'</option>';
                            } else {
                                $html.='<option value="'.$row->id_posicion.'">'.$row->codigo.'</option>';
                            }
                        }
                    $html.='</select>
                </td>
                <td><input type="number" class="input-data right" name="cantidad" value="'.$cantidad.'" onChange="calcula_total('.$id_guia_det.');" disabled="true"/></td>
                <td>'.$abrev.'</td>
                <td><input type="number" class="input-data right" name="unitario" value="'.$unitario.'" onChange="calcula_total('.$id_guia_det.');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="total" value="'.$total.'" disabled="true"/></td>
                <td style="display:flex;">';
                    if ($chk == "true") {
                        $html.='<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" onClick="agrega_series('.$id_guia_det.','.$codigo.');"></i>';
                    }
                    $html.='<i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$id_guia_det.','.$tiene.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle('.$id_guia_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$id_guia_det.');"></i>
                </td>
            </tr>';
        }
        return json_encode(['html'=>$html,'suma'=>$suma]);
        // return response()->json($chk);
    }
    public function mostrar_detalle($id){
        $data = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*',DB::raw("CONCAT(alm_prod.codigo,'-',
            alm_prod.descripcion) as producto"),'alm_und_medida.abreviatura')
            ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')
            ->where([['guia_com_det.id_guia_com_det', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_detalle_oc(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $oc = explode(',',$request->id_oc_det);
        $prod = explode(',',$request->id_producto);
        $pos = explode(',',$request->id_posicion);
        $cant = explode(',',$request->cantidad);
        $unid = explode(',',$request->id_unid_med);
        $unit = explode(',',$request->unitario);
        // $total = explode(',',$request->total);
        $id_usuario = Auth::user()->id_usuario;
        $count = count($oc);

        for ($i=0; $i<$count; $i++){
            $id_guia_com = $request->id_guia_com;
            $id_oc_det = $oc[$i];
            $id_producto = $prod[$i];
            $id_posicion = $pos[$i];
            $cantidad = $cant[$i];
            $id_unid_med = $unid[$i];
            $unitario = $unit[$i];
            // $total = $total[$i];

            $p = DB::table('almacen.guia_com_det')
            ->where([['guia_com_det.id_guia_com','=',$id_guia_com],
                    ['guia_com_det.id_producto','=',$id_producto],
                    ['guia_com_det.id_oc_det','=',$id_oc_det],
                    ['guia_com_det.estado','=',1]])
            ->first();
            
            if (isset($p)){//variable declarada que su valor NO es nulo
                $cant = floatval($p->cantidad) + floatval($cantidad);
                $data = DB::table('almacen.guia_com_det')
                ->where('id_guia_com_det', $p->id_guia_com_det)
                ->update(['cantidad' => $cant]);
            }
            else {
                $data = DB::table('almacen.guia_com_det')->insertGetId(
                [
                    'id_guia_com' => $id_guia_com,
                    'id_producto' => $id_producto,
                    'id_posicion' => $id_posicion,
                    'cantidad' => $cantidad,
                    'id_unid_med' => $id_unid_med,
                    'id_oc_det' => $id_oc_det,
                    'unitario' => $unitario,
                    'total' => ($cantidad * $unitario),
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_guia_com_det'
                );

                $ubi = DB::table('almacen.alm_prod_ubi')
                ->where([['id_producto','=',$id_producto],
                        ['id_posicion','=',$id_posicion]])
                ->first();

                if ($ubi == null){
                    DB::table('almacen.alm_prod_ubi')->insertGetId(
                        [
                            'id_producto' => $id_producto,
                            'id_posicion' => $id_posicion,
                            'stock' => $cantidad,
                            'estado' => 1,
                            'fecha_registro' => $fecha,
                            'costo_promedio' => $unitario,
                        ],
                            'id_prod_ubi'
                    );
                }
            }
        }

        $id_oc = DB::table('logistica.log_det_ord_compra')
            ->where('id_detalle_orden', $oc[0])->first();

        $exist = DB::table('almacen.guia_com_oc')
            ->where([['id_oc','=', $id_oc->id_orden_compra],
                    ['id_guia_com','=', $request->id_guia_com],
                    ['estado','=', 1]])->first();

        if (empty($exist)){
            AlmacenController::guardar_oc($request->id_guia_com, $id_oc->id_orden_compra);
        }

        return response()->json($data);
    }
    public function usuario(){
        $usu = Auth::user()->id_usuario;
        return response()->json($usu);
    }
    public function guardar_guia_detalle(Request $request)
    {
        $usu = Auth::user()->id_usuario;
        $data = DB::table('almacen.guia_com_det')->insertGetId([
                'id_guia_com' => $request->id_guia,
                'id_producto' => $request->id_producto,
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                'id_unid_med' => $request->id_unid_med,
                'unitario' => $request->unitario,
                'unitario_adicional' => 0,
                'total' => $request->total,
                'usuario' => $usu,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_guia_com_det'
            );
        return response()->json(['data'=>$data,'usuario'=>$usu]);
    }
    public function update_guia_detalle(Request $request)
    {
        $guia_det = DB::table('almacen.guia_com_det')
            ->where('id_guia_com_det',$request->id_guia_com_det)
            ->first();

        if ($guia_det->unitario_adicional !== null && $guia_det->unitario_adicional > 0){
            $data = DB::table('almacen.guia_com_det')
                ->where('id_guia_com_det', $request->id_guia_com_det)
                ->update([
                    'id_posicion' => $request->id_posicion,
                    'cantidad' => $request->cantidad,
                    // 'unitario' => $request->unitario,
                    'total' => $request->total,
                    // 'id_unid_med' => $request->id_unid_med
                ]);
        } else {
            $data = DB::table('almacen.guia_com_det')
            ->where('id_guia_com_det', $request->id_guia_com_det)
            ->update([
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                'unitario' => $request->unitario,
                'total' => $request->total,
                // 'id_unid_med' => $request->id_unid_med
            ]);
        }
            
        if (isset($guia_det)){
            if ($request->id_posicion !== null){
                //revisa si tiene enlazado una ubicacion
                $ubi = DB::table('almacen.alm_prod_ubi')
                ->where([['id_producto','=',$guia_det->id_producto],
                        ['id_posicion','=',$request->id_posicion]])
                ->first();
    
                //si no tiene enlazado lo agrega
                if ($ubi == null){
                    DB::table('almacen.alm_prod_ubi')->insertGetId(
                        [
                            'id_producto' => $guia_det->id_producto,
                            'id_posicion' => $request->id_posicion,
                            'stock' => $request->cantidad,
                            'estado' => 1,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'costo_promedio' => $request->unitario,
                        ],
                            'id_prod_ubi'
                    );
                }
            }
        }
        return response()->json($data);
    }
    public function anular_detalle(Request $request, $id)
    {
        $data = DB::table('almacen.guia_com_det')->where('id_guia_com_det', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    /**Guia Compra OC */
    public static function guardar_oc($id_guia,$id_oc){
        $fecha = date('Y-m-d H:i:s');

        $exist = DB::table('almacen.guia_com_oc')
        ->where([['id_guia_com',$id_guia],['id_oc',$id_oc]])
        ->first();
        
        if ($exist == null){
            $data = DB::table('almacen.guia_com_oc')->insertGetId(
                [
                    'id_guia_com' => $id_guia,
                    'id_oc' => $id_oc,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_guia_com_oc'
                );
            return response()->json($data);
        } else {
            return response()->json($exist);
        }
    }
    public function anular_oc($id,$guia)
    {
        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*')
            ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
            ->where([['log_ord_compra.id_orden_compra','=',$id],
                    ['guia_com_det.id_guia_com','=',$guia]])
            ->get()->toArray();

        foreach($detalle as $det){
            $dat = DB::table('almacen.guia_com_det')
            ->where('id_guia_com_det', $det->id_guia_com_det)
            ->update([ 'estado' => 7 ]);
        }

        $data = DB::table('almacen.guia_com_oc')
            ->where([['id_oc','=',$id],['id_guia_com','=',$guia]])
            ->update([ 'estado' => 7 ]);

        return response()->json($data);
    }
    public function guia_ocs($id_guia){
        $data = DB::table('almacen.guia_com_oc')
        ->select('guia_com_oc.id_oc','log_ord_compra.codigo','adm_contri.razon_social','log_ord_compra.fecha',
        //'log_cdn_pago.descripcion as condicion','log_esp_compra.forma_pago_credito','log_esp_compra.fecha_entrega','log_esp_compra.lugar_entrega',
        DB::raw("CONCAT(log_ord_compra.codigo,' - ',adm_contri.razon_social) as orden"),
        DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('logistica.log_cotizacion','log_cotizacion.id_cotizacion','=','log_ord_compra.id_cotizacion')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','log_ord_compra.id_usuario')
        ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
        ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        // ->leftjoin('logistica.log_esp_compra','log_esp_compra.id_especificacion_compra','=','log_cotizacion.id_especificacion_compra')
        // ->join('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_esp_compra.id_condicion_pago')
        ->where([['guia_com_oc.id_guia_com','=',$id_guia],['guia_com_oc.estado','=',1]])
        ->get();
        return response()->json($data);
    }
    public function listar_ordenes($id_proveedor){
        $data = DB::table('logistica.log_ord_compra')
            ->select('log_ord_compra.id_orden_compra',
            DB::raw("CONCAT(log_ord_compra.codigo,' - ',adm_contri.razon_social) AS orden"))
            ->join('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['log_ord_compra.id_proveedor','=',$id_proveedor],
                    ['log_ord_compra.id_tp_documento','=',2],
                    ['log_ord_compra.estado','=',1]])//Orden de Compra
            ->get();
        return response()->json($data);
    }
    public function listar_oc_det($id, $id_almacen){
        $data = DB::table('logistica.log_det_ord_compra')
            ->select('log_det_ord_compra.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_und_medida.abreviatura','alm_und_medida.id_unidad_medida','alm_item.id_producto',
            'log_ord_compra.codigo as cod_orden','log_valorizacion_cotizacion.precio_cotizado',
            'log_valorizacion_cotizacion.cantidad_cotizada')
            ->join('almacen.alm_item','alm_item.id_item','=','log_det_ord_compra.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
            ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([['log_det_ord_compra.id_orden_compra','=',$id],
                    ['log_det_ord_compra.estado','=',1]])
            ->get();
        
        $html = '';
        //listar posiciones que no estan enlazadas con ningun producto
        $posiciones = DB::table('almacen.alm_ubi_posicion')
        ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
        ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_posicion','=','alm_ubi_posicion.id_posicion')
        ->leftjoin('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->leftjoin('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['alm_prod_ubi.id_posicion','=',null],['alm_ubi_posicion.estado','=',1],
                 ['alm_almacen.id_almacen','=',$id_almacen]])
        ->get();

        foreach($data as $det){
            $guia = DB::table('almacen.guia_com_det')
            ->select(DB::raw('SUM(guia_com_det.cantidad) as sum_cantidad'))
            ->where([['id_oc_det','=',$det->id_detalle_orden],
                    ['estado','=',1]])
            ->first();
            $cantidad_nueva = $det->cantidad_cotizada - ($guia->sum_cantidad !== null ? $guia->sum_cantidad : 0);
            //Si hay cantidad por atender = cantidad > 0
            if ($cantidad_nueva > 0){
                $o = false;
                //jalar posicion relacionada con el producto
                $posicion = DB::table('almacen.alm_prod_ubi')
                ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
                ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
                ->where([['alm_prod_ubi.id_producto','=',$det->id_producto],
                         ['alm_prod_ubi.id_almacen','=',$id_almacen],
                         ['alm_prod_ubi.estado','=',1]])
                ->get();
                $count = count($posicion);
                if ($count > 0){
                    $posiciones = $posicion;
                    $o = true;
                }
                // $unitario = $det->subtotal / $det->cantidad_cotizada;
                // $cod_posicion = (isset($posicion->cod_posicion) ? $posicion->cod_posicion : '');

                $html .= 
                '<tr id="oc-'.$det->id_detalle_orden.'">
                    <td><input type="checkbox" checked></td>
                    <td><input type="text" name="id_oc_det" class="oculto" value="'.$det->id_detalle_orden.'"/>'.$det->cod_orden.'</td>
                    <td><input type="text" name="id_producto" class="oculto" value="'.$det->id_producto.'"/>'.$det->codigo.'</td>
                    <td>'.$det->descripcion.'</td>
                    <td>
                        <select class="input-data js-example-basic-single" name="id_posicion">
                            <option value="0">Elija una opción</option>';
                            // $pos = $this->mostrar_posiciones_cbo();
                            foreach ($posiciones as $row) {
                                if ($o){
                                    $html.='<option value="'.$row->id_posicion.'" selected>'.$row->codigo.'</option>';
                                } else {
                                    $html.='<option value="'.$row->id_posicion.'">'.$row->codigo.'</option>';
                                }
                            }
                        $html.='</select>
                    </td>
                    <td><input type="number" name="cantidad" class="input-data right" onChange="calcula_total_oc('.$det->id_detalle_orden.');"  value="'.$cantidad_nueva.'"/></td>
                    <td><input type="text" name="id_unid_med" class="oculto" value="'.$det->id_unidad_medida.'"/>'.$det->abreviatura.'</td>
                    <td><input type="number" name="unitario" class="input-data right" readOnly value="'.$det->precio_cotizado.'"/></td>
                    <td><input type="number" name="total" class="input-data right" readOnly value="'.($cantidad_nueva * $det->precio_cotizado).'"/></td>
                </tr>';    
            }
        }
        // return response()->json($nueva_data);
        return json_encode($html);
    }
    public function posiciones(){
        $posiciones = DB::table('almacen.alm_ubi_posicion')
        ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
        ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_posicion','=','alm_ubi_posicion.id_posicion')
        ->where([['alm_prod_ubi.id_posicion','=',null],['alm_ubi_posicion.estado','=',1]])
        ->get();
        $posicion = DB::table('almacen.alm_prod_ubi')
        ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
        ->where([['alm_prod_ubi.id_producto','=',2930],['alm_prod_ubi.estado','=',1]])
        ->get();
        $count = count($posicion);
        if ($count > 0){
            $posiciones = $posicion;
        }
        return response()->json($posiciones);
    }
    /**Guardar Series */
    public function guardar_series(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $se = explode(',',$request->series);
        $count = count($se);
        $data = 0;
        if (!empty($request->series)){
            $id = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','guia_com.id_almacen')
            ->where('id_guia_com_det',$request->id_guia_det)
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->first();
    
            for ($i=0; $i<$count; $i++){
                $serie = $se[$i];
                $data = DB::table('almacen.alm_prod_serie')->insertGetId(
                    [
                        'id_prod'=>$id->id_producto,
                        'id_almacen'=>$id->id_almacen,
                        'serie'=>$serie,
                        'estado'=>1,
                        'fecha_registro'=>$fecha,
                        'id_guia_det'=>$request->id_guia_det
                    ],
                    'id_prod_serie'
                );
            }
        }
        $an = explode(',',$request->anulados);
        $can = count($an);

        if (!empty($request->anulados)){
            for ($i=0;$i<$can;$i++){
                $data = DB::table('almacen.alm_prod_serie')
                ->where('id_prod_serie',$an[$i])
                ->update([ 'estado' => 7 ]);
            }
        }

        return response()->json($data);
    }
    public function listar_series($id_guia_det){
        $series = DB::table('almacen.alm_prod_serie')
        ->where([['id_guia_det','=',$id_guia_det],
                 ['estado','=',1]])
        ->get();
        return response()->json($series);
    }
    public function listar_series_guia_ven($id_guia_ven_det){
        $series = DB::table('almacen.alm_prod_serie')
        ->select('alm_prod_serie.*',
        DB::raw("CONCAT(tp_doc_almacen.abreviatura,'-',guia_com.serie,'-',guia_com.numero) as guia_com"))
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','alm_prod_serie.id_guia_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
        ->where([['alm_prod_serie.id_guia_ven_det','=',$id_guia_ven_det],
                 ['alm_prod_serie.estado','=',1]])
        ->get();
        return response()->json($series);
    }
    public function listar_series_almacen($id_prod, $id_almacen){
        $series = DB::table('almacen.alm_prod_serie')
        ->select('alm_prod_serie.*','guia_com.fecha_emision',
        DB::raw("CONCAT(tp_doc_almacen.abreviatura,'-',guia_com.serie,'-',guia_com.numero) as guia_com"))
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','alm_prod_serie.id_guia_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
        ->where([['alm_prod_serie.id_prod','=',$id_prod],
                ['alm_prod_serie.id_almacen','=',$id_almacen],
                ['alm_prod_serie.id_guia_ven_det','=',null],
                ['alm_prod_serie.estado','=',1]])
        ->get();
        $output['data'] = $series;
        return response()->json($output);
    }
    public function buscar_serie($serie){
        $data = DB::table('almacen.alm_prod_serie')
        ->select('alm_prod_serie.*',
        DB::raw("CONCAT(tp_doc_com.abreviatura,'-',guia_com.serie,'-',guia_com.numero) as guia_com"),
        DB::raw("CONCAT(tp_doc_ven.abreviatura,'-',guia_ven.serie,'-',guia_ven.numero) as guia_ven"))
        ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','alm_prod_serie.id_guia_ven_det')
        ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
        ->leftjoin('almacen.tp_doc_almacen as tp_doc_ven','tp_doc_ven.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
        ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','alm_prod_serie.id_guia_det')
        ->leftjoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->leftjoin('almacen.tp_doc_almacen as tp_doc_com','tp_doc_com.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
        ->where([['alm_prod_serie.serie','=',$serie],['alm_prod_serie.estado','=',1]])
        ->first();
        return response()->json($data);
    }

    /**Comprobante de Compra */
    /** */

    public function listar_docven_guias($id_doc){
        $guias = DB::table('almacen.doc_ven_guia')
        ->select('doc_ven_guia.*',DB::raw("CONCAT('GR-',guia_ven.serie,'-',guia_ven.numero) as guia"),
        'guia_ven.fecha_emision as fecha_guia','guia_motivo.descripcion as des_motivo',
        'adm_contri.razon_social')
        ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','doc_ven_guia.id_guia_ven')
        ->join('almacen.guia_motivo','guia_motivo.id_motivo','=','guia_ven.id_motivo')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','guia_ven.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->where([['doc_ven_guia.id_doc_ven','=',$id_doc],
                ['doc_ven_guia.estado','=',1]])
        ->get();
        $html ='';
        foreach($guias as $guia){
            $html .= '
            <tr id="doc-'.$guia->id_doc_ven_guia.'">
                <td>'.$guia->guia.'</td>
                <td>'.$guia->fecha_guia.'</td>
                <td>'.$guia->razon_social.'</td>
                <td>'.$guia->des_motivo.'</td>
                <td><i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular Guia" onClick="anular_guia('.$guia->id_guia_ven.','.$guia->id_doc_ven_guia.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }

    public function listar_guias_almacen($id_almacen){
        $data = DB::table('almacen.guia_com')
            ->select('guia_com.*','adm_contri.razon_social')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            // ->join('logistica.guia_motivo','guia_motivo.id_motivo','=','guia_com.id_motivo')
            ->where([['guia_com.id_almacen','=',$id_almacen],['guia_com.estado','!=',7]])
            ->get();
        return response()->json($data);
        // return json_encode($html);
    }
    public function listar_req($id_sede){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','adm_grupo.id_sede')
            ->where([['sis_sede.id_sede','=',$id_sede],
                    ['alm_req.stock_comprometido','=',true],//stock comprometido de almacen
                    ['alm_req.estado','!=',7]])
            ->get();
        return response()->json($data);
    }
    
    public function listar_docven_items($id_doc){
        $detalle = DB::table('almacen.doc_ven_det')
            ->select('doc_ven_det.*','alm_prod.codigo','alm_prod.descripcion',
            'tp_doc_almacen.abreviatura as guia_tp_doc_almacen',
            'guia_ven.serie as guia_serie','guia_ven.numero as guia_numero',
            'alm_und_medida.abreviatura')
            ->join('almacen.alm_item','alm_item.id_item','=','doc_ven_det.id_item')
            ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','doc_ven_det.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_ven_det.id_unid_med')
            ->where([['doc_ven_det.id_doc','=',$id_doc],
                    ['doc_ven_det.estado','=',1]])
            ->get();
        $html = '';
        foreach($detalle as $det){
            // <td>'.($det->guia_tp_doc_almacen !== null ? 
            // ($det->guia_tp_doc_almacen.'-'.$det->guia_serie.'-'.$det->guia_numero) : '').'</td>

            $html .= '
            <tr id="det-'.$det->id_doc_det.'">
                <td>'.$det->codigo.'</td>
                <td>'.$det->descripcion.'</td>
                <td><input type="number" class="input-data right" name="cantidad" 
                    value="'.$det->cantidad.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td>'.$det->abreviatura.'</td>
                <td><input type="number" class="input-data right" name="precio_unitario" 
                    value="'.$det->precio_unitario.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="porcen_dscto" 
                    value="'.$det->porcen_dscto.'" onChange="calcula_dscto('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="total_dscto" 
                    value="'.$det->total_dscto.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="precio_total" 
                    value="'.$det->precio_total.'" disabled="true"/>
                </td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$det->id_doc_det.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle('.$det->id_doc_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$det->id_doc_det.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }

    public function guardar_doc_guia(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user();

        $guia = DB::table('almacen.guia_com')
            ->select('guia_com.*')
            ->where('id_guia',$request->id_guia)
            ->first();

        $oc = DB::table('almacen.guia_com_oc')
            ->select('log_ord_compra.id_moneda','log_ord_compra.id_condicion','log_ord_compra.plazo_dias')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
            ->where('id_guia_com',$request->id_guia)
            ->first();

        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','log_valorizacion_cotizacion.precio_sin_igv')
            ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([['guia_com_det.id_guia_com','=',$request->id_guia],
                    ['guia_com_det.estado','=',1 ]])
            ->get();

        $id_doc = DB::table('almacen.doc_com')->insertGetId(
            [
                'serie'=>$request->serie,
                'numero'=>$request->numero,
                'id_tp_doc'=>$request->id_tp_doc,
                'id_proveedor'=>$guia->id_proveedor,
                'fecha_emision'=>$request->fecha_emision,
                'fecha_vcmto'=>$request->fecha_emision,
                'id_condicion'=>(($oc !== null && $oc->id_condicion !== null) ? $oc->id_condicion : null),
                'credito_dias'=>(($oc !== null && $oc->plazo_dias !== null) ? $oc->plazo_dias : null),
                'moneda'=>(($oc !== null && $oc->id_moneda !== null) ? $oc->id_moneda : null),
                'usuario'=>$usuario->id_usuario,
                'registrado_por'=>$usuario->id_usuario,
                'estado'=>1,
                'fecha_registro'=>$fecha
            ],
                'id_doc_com'
        );
        $sub_total = 0;

        foreach($detalle as $det){
            $total = ($det->precio_sin_igv !== null) ? $det->precio_sin_igv : $det->total;
            $unitario = $total / $det->cantidad;
            $sub_total += $total;

            $item = DB::table('almacen.alm_item')
                ->where('id_producto',$det->id_producto)
                ->first();

            $id_det = DB::table('almacen.doc_com_det')->insertGetId(
                [
                    'id_doc'=>$id_doc,
                    'id_item'=>$item->id_item,
                    'cantidad'=>$det->cantidad,
                    'id_unid_med'=>$det->id_unid_med,
                    'precio_unitario'=>$unitario,
                    'sub_total'=>$total,
                    'porcen_dscto'=>0,
                    'total_dscto'=>0,
                    'precio_total'=>$total,
                    'id_guia_com_det'=>$det->id_guia_com_det,
                    'estado'=>1,
                    'fecha_registro'=>$fecha
                ],
                    'id_doc_det'
            );
        }
        //obtiene IGV
        $impuesto = DB::table('contabilidad.cont_impuesto')
            ->where([['codigo','=','IGV'],['fecha_inicio','<',$request->fecha_emision]])
            ->orderBy('fecha_inicio','desc')
            ->first();
        $igv = $impuesto->porcentaje * $sub_total / 100;

        //actualiza totales
        DB::table('almacen.doc_com')->where('id_doc_com',$id_doc)
        ->update([
            'sub_total'=>$sub_total,
            'total_descuento'=>0,
            'porcen_descuento'=>0,
            'total'=>$sub_total,
            'total_igv'=>$igv,
            'total_ant_igv'=>0,
            'porcen_igv' => $request->porcen_igv,
            'porcen_anticipo' => $request->porcen_anticipo,
            'total_otros' => $request->total_otros,
            'total_a_pagar'=>($sub_total + $igv)
        ]);

        $guia = DB::table('almacen.doc_com_guia')->insertGetId(
            [
                'id_doc_com'=>$id_doc,
                'id_guia_com'=>$request->id_guia,
                'estado'=>1,
                'fecha_registro'=>$fecha
            ],
                'id_doc_com_guia'
        );
        $ingreso = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_com',$request->id_guia)
            ->first();

        if (isset($ingreso->id_mov_alm)){
            DB::table('almacen.mov_alm')
                ->where('id_mov_alm',$ingreso->id_mov_alm)
                ->update(['id_doc_com'=>$id_doc]);
        }
        $tp = DB::table('contabilidad.cont_tp_doc')->select('abreviatura')
            ->where('id_tp_doc',$request->id_tp_doc)->first();

        return response()->json(['id_doc'=>$id_doc,'tp_doc'=>$tp->abreviatura,'doc_serie'=>$request->serie,'doc_numero'=>$request->numero]);
    }
    public function actualizar_doc_guia(Request $request){
        $data = DB::table('almacen.doc_com')->where('id_doc_com',$request->id_doc_com)
        ->update([  'id_tp_doc'=>$request->id_tp_doc,
                    'serie'=>$request->serie,
                    'numero'=>$request->numero,
                    'fecha_emision'=>$request->fecha_emision,
                    'id_proveedor'=>$request->id_proveedor,
                ]);
        $tp = DB::table('contabilidad.cont_tp_doc')->select('abreviatura')
            ->where('id_tp_doc',$request->id_tp_doc)->first();

        return response()->json(['id_doc'=>$request->id_doc_com,'tp_doc'=>$tp->abreviatura,'doc_serie'=>$request->serie,'doc_numero'=>$request->numero]);
    }
    public function anular_guia($doc,$guia)
    {
        $detalle = DB::table('almacen.doc_com_det')
            ->select('doc_com_det.*')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->where([['doc_com_det.id_doc','=',$doc],
                     ['guia_com.id_guia','=',$guia]])
            ->get()->toArray();

        foreach($detalle as $det){
            DB::table('almacen.doc_com_det')
            ->where('id_doc_det', $det->id_doc_det)
            ->update([ 'estado' => 7 ]);
        }

        $data = DB::table('almacen.doc_com_guia')
            ->where([['id_doc_com','=',$doc],['id_guia_com','=',$guia]])
            ->update(['estado' => 7]);

        return response()->json($data);
    }
    public function anular_guiaven($doc,$guia)
    {
        $detalle = DB::table('almacen.doc_ven_det')
            ->select('doc_ven_det.*')
            ->join('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','doc_ven_det.id_guia_ven_det')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->where([['doc_ven_det.id_doc','=',$doc],
                     ['guia_ven.id_guia_ven','=',$guia]])
            ->get()->toArray();

        foreach($detalle as $det){
            DB::table('almacen.doc_ven_det')
            ->where('id_doc_det', $det->id_doc_det)
            ->update([ 'estado' => 7 ]);
        }

        $data = DB::table('almacen.doc_ven_guia')
            ->where([['id_doc_ven','=',$doc],['id_guia_ven','=',$guia]])
            ->update(['estado' => 7]);

        return response()->json($data);
    }
    public function listar_requerimientos(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','proy_proyecto.descripcion as proy_descripcion',
            'adm_area.descripcion as area_descripcion',
            'adm_prioridad.descripcion as des_prioridad','adm_grupo.descripcion as des_grupo',
            DB::raw("concat(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as responsable"))
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->join('administracion.adm_prioridad','adm_prioridad.id_prioridad','=','alm_req.id_prioridad')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->leftjoin('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','alm_req.id_proyecto')
            ->leftjoin('administracion.adm_area','adm_area.id_area','=','alm_req.id_area')
            ->where([['alm_req.estado','=',1],
                    ['alm_req.id_tipo_requerimiento','=',1]])
            ->get();
        // $i = 1;
        // $html = '';

        // foreach($data as $reg){
        //     $html .= '
        //     <tr id="req-'.$reg->id_requerimiento.'">
        //         <td>
        //             <input type="checkbox" class="flat-red">
        //         </td>
        //         <td>'.$i.'</td>
        //         <td>'.$reg->codigo.'</td>
        //         <td>'.$reg->fecha_requerimiento.'</td>
        //         <td>'.$reg->responsable.'</td>
        //         <td>'.$reg->concepto.'</td>';
        //         if ($reg->id_proyecto !== null){
        //             $html.='<td>'.$reg->proy_descripcion.'</td>';
        //         } else {
        //             $html.='<td>'.$reg->area_descripcion.'</td>';
        //         }
        //         $html.='<td><i class="fas fa-search-plus icon-tabla blue"></i></td>
        //     </tr>
        //     ';
        //     $i++;
        // }
        $output['data']=$data;
        return response()->json($output);
    }
    public function listar_items_req($id){
        $data = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_und_medida.abreviatura','alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_producto','=','alm_prod.id_producto')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->where('alm_det_req.id_requerimiento',$id)
            ->get();
        // $i = 1;
        // $html = '';

        // foreach($data as $reg){
        //     $html .= '
        //     <tr id="det-'.$reg->id_detalle_requerimiento.'">
        //         <td>
        //             <input type="checkbox" class="flat-red">
        //         </td>
        //         <td>'.$i.'</td>
        //         <td>'.$reg->codigo.'</td>
        //         <td>'.$reg->descripcion.'</td>
        //         <td>'.$reg->cod_posicion.'</td>
        //         <td>'.$reg->cantidad.'</td>
        //         <td>'.$reg->abreviatura.'</td>
        //         <td>'.$reg->partida.'</td>
        //     </tr>
        //     ';
        //     $i++;
        // }
        // return json_encode($html);
        $output['data']=$data;
        return response()->json($output);
    }
    public function id_producto($id_item){
        $item = DB::table('almacen.alm_item')
        ->where('id_item', $id_item)
        ->first();
        return $item->id_producto;
    }
    /*
    public function generar_salida(Request $request){
        
        $fecha = date('Y-m-d H:i:s');
        $fecha_emision = date('Y-m-d');
        $codigo = AlmacenController::nextMovimiento(2,$fecha_emision,1);
        $id_usuario = Auth::user()->id_usuario;
        
        $id_salida = DB::table('almacen.mov_alm')->insertGetId(
            [
                'id_almacen' => 1,
                'id_tp_mov' => 2,//Salidas
                'codigo' => $codigo,
                'fecha_emision' => $fecha_emision,
                'id_guia_ven' => $request->id_guia,
                'id_req' => $request->id_req,
                'id_operacion' => $guia->id_operacion,
                'revisado' => 0,
                'usuario' => $id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_mov_alm'
            );

        $det = $request->detalle;
        $array = json_decode($det, true);
        $count = count($array);

        if ($count > 0){
            for ($i=0; $i<$count; $i++){
                $id_prod = $this->id_producto($array[$i]['id_item']);

                $id_pos = DB::table('almacen.alm_prod_ubi')
                    ->where([['id_producto','=',$id_prod]])
                    ->first();

                //traer stockActual
                $saldo = $this->saldo_actual($id_prod, $id_pos->id_posicion);
                $costo = $this->costo_promedio($id_prod, $id_pos->id_posicion);

                $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                [
                    'id_mov_alm' => $id_salida,
                    'id_producto' => $id_prod,
                    'id_posicion' => $id_pos->id_posicion,
                    'cantidad' => $array[$i]['cantidad'],
                    'valorizacion' => ($costo * $array[$i]['cantidad']),
                    'usuario' => 3,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm_det'
                );

                if ($id_pos->id_posicion !== null){                
                    DB::table('almacen.alm_prod_ubi')
                    ->where('id_prod_ubi',$id_pos->id_prod_ubi)
                    ->update([  'stock' => $saldo,
                                'costo_promedio'=>$costo
                            ]);
                }
            }
        }
        DB::table('almacen.guia_ven')
            ->where('id_guia',$id_guia)->update(['estado'=>9]);//Procesado

        return response()->json($id_salida);
    }
*/
    // public function generar_salida(Request $request){
    //     try
    //     {
    //         DB::beginTransaction();
    //         $mov = new Movimiento;
    //         $mov->id_almacen = 1;
    //         $mov->codigo = AlmacenController::nextMovimiento(3,7,date('Y-m-d'),1);
    //         $mov->id_req = $request->id_req;
    //         $mov->id_guia = $request->id_guia;
    //         $mov->id_tp_mov = 7;
    //         $mov->fecha_emision = date('Y-m-d');
    //         $mov->usuario = 3;
    //         $mov->estado = 1;
    //         $mov->fecha_registro = date('Y-m-d H:i:s');
    //         $mov->save();
    //         $id = $mov['id_mov_alm'];

    //         $det = $request->detalle;
    //         $array = json_decode($det, true);
    //         $count = count($array);

    //         if ($count > 0){
    //             for ($i=0; $i<$count; $i++){
    //                 $id_prod = $this->id_producto($array[$i]['id_item']);
    //                 $d = new MovDetalle;

    //                 $d->id_mov_alm = $id;
    //                 $d->id_producto = $id_prod;
    //                 $d->cantidad = $array[$i]['cantidad'];
    //                 $d->valorizacion = $array[$i]['precio_referencial'];
    //                 $d->usuario = 3;
    //                 $d->estado = 1;
    //                 $d->fecha_registro = date('Y-m-d H:i:s');
    //                 $d->save();
    //             }
    //         }
    //         DB::commit();
    //         return response()->json($id);
    //     } catch (\Exception $e)
    //     {
    //         dd($e->getMessage());
    //         DB::rollback();
    //         return response()->json('Lo sentimos ha ocurrido un error');
    //     }
    // }
    public function id_salida($id_guia){
        $sal = DB::table('almacen.mov_alm')
        ->where([['mov_alm.id_guia_ven','=',$id_guia],
                ['mov_alm.estado','=',1]])
        ->first();
        if (isset($sal)){
            return response()->json($sal->id_mov_alm);
        } else {
            return response()->json(0);
        }
    }
    public function imprimir_salida($id_sal){
        $id = $this->decode5t($id_sal);
        $salida = DB::table('almacen.mov_alm')
            ->select('mov_alm.*','alm_almacen.descripcion as des_almacen',
            'sis_usua.usuario as nom_usuario','tp_ope.cod_sunat','tp_ope.descripcion as ope_descripcion',
            // 'proy_proyecto.descripcion as proy_descripcion','proy_proyecto.codigo as cod_proyecto',
            DB::raw("CONCAT(tp_doc_almacen.abreviatura,'-',guia_ven.serie,'-',guia_ven.numero) as guia"),
            'guia_motivo.descripcion as motivo_descripcion','trans.codigo as cod_trans',
            'alm_destino.descripcion as des_alm_destino','trans.fecha_transferencia',
            DB::raw("CONCAT(cont_tp_doc.abreviatura,'-',doc_ven.serie,'-',doc_ven.numero) as doc"),
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as persona"),
            'transformacion.codigo as cod_transformacion',//'transformacion.serie','transformacion.numero',
            'transformacion.fecha_transformacion','guia_ven.fecha_emision as fecha_guia',
            'adm_contri.nro_documento as ruc_empresa','adm_contri.razon_social as empresa_razon_social')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','mov_alm.id_operacion')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.guia_motivo','guia_motivo.id_motivo','=','guia_ven.id_motivo')
            ->leftjoin('almacen.trans','trans.id_guia_ven','=','guia_ven.id_guia_ven')
            ->leftjoin('almacen.alm_almacen as alm_destino','alm_destino.id_almacen','=','trans.id_almacen_destino')
            ->leftjoin('almacen.doc_ven','doc_ven.id_doc_ven','=','mov_alm.id_doc_ven')
            ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_ven.id_tp_doc')
            ->leftjoin('almacen.transformacion','transformacion.id_transformacion','=','mov_alm.id_transformacion')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where('mov_alm.id_mov_alm',$id)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura',
            'alm_prod.series','alm_req.id_requerimiento','alm_req.codigo as cod_req')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','mov_alm_det.id_guia_ven_det')
            ->leftjoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','guia_ven_det.id_req_det')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
            ->where([['mov_alm_det.id_mov_alm','=',$id],['mov_alm_det.estado','=',1]])
            ->get();

        $req = [];
        foreach($detalle as $d){
            if ($d->cod_req !== null){
                if (in_array($d->cod_req, $req) == false){
                    array_push($req, $d->cod_req);
                }
            }
        }
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');

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
                <table width="100%">
                    <tr>
                        <td>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$salida->ruc_empresa.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$salida->empresa_razon_social.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">.::Sistema ERP v1.0::.</p>
                        </td>
                        <td>
                            <p style="text-align:right;font-size:10px;margin:0px;">Fecha: '.$fecha_actual.'</p>
                            <p style="text-align:right;font-size:10px;margin:0px;">Hora : '.$hora_actual.'</p>
                        </td>
                    </tr>
                </table>
                <h3 style="margin:0px;"><center>SALIDA DE ALMACÉN</center></h3>
                <h5><center>'.$salida->id_almacen.' - '.$salida->des_almacen.'</center></h5>
                
                <table border="0">
                    <tr>
                        <td>Salida N°</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$salida->codigo.'</td>
                        <td>Fecha Salida</td>
                        <td width=10px>:</td>
                        <td>'.$salida->fecha_emision.'</td>
                    </tr>';
                if ($salida->guia !== '--'){
                    $html.='
                    <tr>
                        <td>Guía de Venta</td>
                        <td width=10px>:</td>
                        <td>'.$salida->guia.'</td>
                        <td>Fecha Guía</td>
                        <td width=10px>:</td>
                        <td>'.$salida->fecha_guia.'</td>
                    </tr>
                    ';
                }
                if ($salida->fecha_transformacion !== null){
                    $html.='
                    <tr>
                        <td>Transformación</td>
                        <td>:</td>
                        <td width=250px>'.$salida->cod_transformacion.' ('.$salida->serie.'-'.$salida->numero.')</td>
                        <td width=150px>Fecha Transformación</td>
                        <td width=10px>:</td>
                        <td>'.$salida->fecha_transformacion.'</td>
                    </tr>
                    ';
                }
                if (isset($salida->doc) && $salida->doc !== '--'){
                    $html.='
                    <tr>
                        <td>Documento de Venta</td>
                        <td>:</td>
                        <td>'.$salida->doc.'</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    ';
                }
                if ($req !== []){
                    $cods_req = '';
                    for ($i=0; $i<count($req); $i++){
                        if ($cods_req == ''){
                            $cods_req.= $req[$i];
                        } else {
                            $cods_req.= $req[$i].', ';
                        }
                    }
                    $html.='
                    <tr>
                        <td>Requerimiento</td>
                        <td>:</td>
                        <td colSpan="4">'.$cods_req.'</td>
                    </tr>
                    ';
                }
                if (isset($salida->cod_trans)){
                    $html.='
                    <tr>
                        <td width=130px>Transferencia</td>
                        <td>:</td>
                        <td>'.$salida->cod_trans.'</td>
                        <td>Fecha Transferencia</td>
                        <td>:</td>
                        <td>'.$salida->fecha_transferencia.'</td>
                    </tr>
                    <tr>
                        <td>Almacén Destino</td>
                        <td>:</td>
                        <td width=200px>'.$salida->des_alm_destino.'</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    ';
                }

                $html.='
                <tr>
                    <td>Tipo Movimiento</td>
                    <td>:</td>
                    <td colSpan="4">'.$salida->cod_sunat.' '.$salida->ope_descripcion.'</td>
                </tr>';

                $html.='
                    <tr>
                        <td>Generado por</td>
                        <td>:</td>
                        <td colSpan="4">'.$salida->persona.'</td>
                    </tr>
                </table>
                <br/>
                <table id="detalle">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Código</th>
                            <th width=45% >Descripción</th>
                            <th>Posición</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>Valor.</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $i = 1;
                    foreach($detalle as $det){
                        $chk = ($det->series ? 'true' : 'false');
                        $series = '';
                        if ($chk == 'true'){
                            $det_series = DB::table('almacen.alm_prod_serie')
                            ->where([['alm_prod_serie.id_prod','=',$det->id_producto],
                                    ['alm_prod_serie.id_guia_ven_det','=',$det->id_guia_ven_det]])
                            ->get();
                
                            if (isset($det_series)){
                                foreach($det_series as $s){
                                    if ($series !== ''){
                                        $series.= ', '.$s->serie;
                                    } else {
                                        $series = 'Serie(s): '.$s->serie;
                                    }
                                }
                            }
                        }
                        $html.='
                        <tr>
                            <td class="right">'.$i.'</td>
                            <td>'.$det->codigo.'</td>
                            <td>'.$det->descripcion.' '.$series.'</td>
                            <td>'.$det->cod_posicion.'</td>
                            <td class="right">'.$det->cantidad.'</td>
                            <td>'.$det->abreviatura.'</td>
                            <td class="right">'.round($det->valorizacion,2,PHP_ROUND_HALF_UP).'</td>
                        </tr>';
                        $i++;
                    }
                    $html.='</tbody>
                </table>
                <p style="text-align:right;font-size:11px;">Elaborado por: '.$salida->nom_usuario.' '.$salida->fecha_registro.'</p>

            </body>
        </html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->stream();
        return $pdf->download('salida.pdf');
        // return response()->json(['salida'=>$salida,'detalle'=>$detalle]);
    }
    /*public function guardar_guia_ven(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_ven')->insertGetId(
            [
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_empresa' => $request->id_empresa,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                // 'id_guia_clas' => $request->id_guia_clas,
                // 'id_guia_cond' => $request->id_guia_cond,
                'id_cliente' => $request->id_cliente,
                'usuario' => $request->usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_guia_ven'
            );
        
            $det = $request->detalle;
            $array = json_decode($det, true);
            $count = count($array);

            if ($count > 0){
                for ($i=0; $i<$count; $i++){
                    $id_prod = $this->id_producto($array[$i]['id_item']);
                    
                    $data = DB::table('almacen.guia_ven_det')->insertGetId(
                        [
                            'id_guia_ven' => $id_guia,
                            'id_producto' => $id_prod,
                            // 'id_posicion' => $array[$i]['id_posicion'],
                            'cantidad' => $array[$i]['cantidad'],
                            // 'id_unid_med' => $array[$i]['id_unid_med'],
                            // 'id_oc_det' => $array[$i]['id_unid_med'],
                            // 'unitario' => $array[$i]['unitario'],
                            // 'total' => $array[$i]['total'],
                            // 'usuario' => $request->usuario,
                            // 'estado' => 1,
                            // 'fecha_registro' => $fecha
                        ],
                            'id_guia_ven_det'
                        );
                }
            }
        // return response()->json(["id_guia"=>$id_guia,"id_proveedor"=>$request->id_proveedor]);
        return response()->json($id_guia);
    }*/
    public function kardex_general($almacenes, $finicio, $ffin){
        $alm_array = explode(',',$almacenes);
        
        $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision','mov_alm.id_tp_mov',
            'alm_prod.descripcion as prod_descripcion','alm_prod.codigo as prod_codigo',
            'alm_prod.codigo_anexo','alm_und_medida.abreviatura','alm_ubi_posicion.codigo as posicion',
            'tp_ope_com.cod_sunat as cod_sunat_com','tp_ope_com.descripcion as tp_com_descripcion',
            'tp_ope_ven.cod_sunat as cod_sunat_ven','tp_ope_ven.descripcion as tp_ven_descripcion',
            DB::raw("CONCAT(tp_guia_com.abreviatura,'-',guia_com.serie,'-',guia_com.numero) as guia_com"),
            DB::raw("CONCAT(tp_guia_ven.abreviatura,'-',guia_ven.serie,'-',guia_ven.numero) as guia_ven"),
            DB::raw("CONCAT(tp_doc_com.abreviatura,'-',doc_com.serie,'-',doc_com.numero) as doc_com"),
            DB::raw("CONCAT(tp_doc_ven.abreviatura,'-',doc_ven.serie,'-',doc_ven.numero) as doc_ven"),
            'guia_com.id_guia','guia_ven.id_guia_ven',
            'doc_com.id_doc_com','doc_ven.id_doc_ven','transformacion.codigo as cod_transformacion')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->leftjoin('almacen.transformacion','transformacion.id_transformacion','=','mov_alm.id_transformacion')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->leftjoin('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen as tp_guia_com','tp_guia_com.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_ope_com','tp_ope_com.id_operacion','=','mov_alm.id_operacion')
            ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_com','tp_doc_com.id_tp_doc','=','doc_com.id_tp_doc')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen as tp_guia_ven','tp_guia_ven.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_ope_ven','tp_ope_ven.id_operacion','=','mov_alm.id_operacion')
            ->leftjoin('almacen.doc_ven','doc_ven.id_doc_ven','=','mov_alm.id_doc_ven')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_ven','tp_doc_ven.id_tp_doc','=','doc_ven.id_tp_doc')
            // ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','mov_alm.id_req')
            ->where([['mov_alm.fecha_emision','>=',$finicio],
                    ['mov_alm.fecha_emision','<=',$ffin],
                    ['mov_alm_det.estado','=',1]])
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->orderBy('alm_prod.codigo','asc')
            ->orderBy('mov_alm.fecha_emision','asc')
            ->orderBy('mov_alm.id_tp_mov','asc')
            ->get();

        $saldo = 0;
        $saldo_valor = 0;
        $movimientos = [];
        $codigo = '';

        foreach($data as $d){

            if ($d->prod_codigo !== $codigo){
                $saldo = 0;
                $saldo_valor = 0;
            }
            $orden = '';
            $req = '';

            if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0){
                $saldo += $d->cantidad;
                $saldo_valor += $d->valorizacion;

                if ($d->id_guia_com_det !== null){
                    $ocs = DB::table('almacen.guia_com_det')
                    ->select('alm_req.codigo as cod_req','log_ord_compra.codigo as cod_orden')
                    ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
                    ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
                    ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
                    ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_valorizacion_cotizacion.id_detalle_requerimiento')
                    ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
                    ->where('guia_com_det.id_guia_com_det',$d->id_guia_com_det)
                    ->first();
                    if (isset($ocs)){
                        $orden = $ocs->cod_orden;
                        $req = $ocs->cod_req;
                    }
                }
            }
            else if ($d->id_tp_mov == 2){
                $saldo -= $d->cantidad;
                $saldo_valor -= $d->valorizacion;
            }
            $codigo = $d->prod_codigo;

            $nuevo = [
                "id_mov_alm_det"=>$d->id_mov_alm_det,
                "prod_codigo"=>$d->prod_codigo,
                "prod_descripcion"=>$d->prod_descripcion,
                "fecha_emision"=>$d->fecha_emision,
                "posicion"=>$d->posicion,
                "abreviatura"=>$d->abreviatura,
                "tipo"=>$d->id_tp_mov,
                "cantidad"=>$d->cantidad,
                "saldo"=>$saldo,
                "valorizacion"=>$d->valorizacion,
                "saldo_valor"=>$saldo_valor,
                "cod_sunat_com"=>$d->cod_sunat_com,
                "cod_sunat_ven"=>$d->cod_sunat_ven,
                "tp_com_descripcion"=>$d->tp_com_descripcion,
                "tp_ven_descripcion"=>$d->tp_ven_descripcion,
                "id_guia_com"=>$d->id_guia,
                "id_guia_ven"=>$d->id_guia_ven,
                "id_doc_com"=>$d->id_doc_com,
                "id_doc_ven"=>$d->id_doc_ven,
                "doc_com"=>$d->doc_com,
                "doc_ven"=>$d->doc_ven,
                "guia_com"=>$d->guia_com,
                "guia_ven"=>$d->guia_ven,
                "doc_com"=>$d->doc_com,
                "doc_ven"=>$d->doc_ven,
                "cod_transformacion"=>$d->cod_transformacion,
                "orden"=>$orden,
                "req"=>$req,
            ];
            array_push($movimientos, $nuevo);
        }
        return response()->json($movimientos);
    }
    public function listar_saldos($almacen){
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.*','alm_prod.codigo','alm_prod.descripcion','alm_ubi_posicion.codigo as cod_posicion',
            'alm_und_medida.abreviatura','alm_prod.codigo_anexo','sis_moneda.simbolo','alm_cat_prod.descripcion as des_categoria',
            'alm_subcat.descripcion as des_subcategoria','alm_clasif.descripcion as des_clasificacion',
            'alm_prod_antiguo.cod_antiguo','alm_prod.id_moneda',
            DB::raw("(SELECT SUM(alm_det_req.cantidad) FROM almacen.alm_det_req 
            WHERE alm_det_req.estado=19 
            AND alm_det_req.id_producto=alm_prod_ubi.id_producto 
            AND alm_det_req.id_almacen=alm_prod_ubi.id_almacen 
            GROUP BY alm_det_req.cantidad) as cantidad_reserva"))
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            // ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            // ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_prod_ubi.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_ubi.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_prod.id_moneda')
            ->leftjoin('almacen.alm_clasif','alm_clasif.id_clasificacion','=','alm_prod.id_clasif')
            ->leftjoin('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
            ->leftjoin('almacen.alm_prod_antiguo','alm_prod_antiguo.id_producto','=','alm_prod.id_producto')
            // ->leftjoin('almacen.alm_det_req','alm_det_req.id_producto','=','alm_prod.id_producto')
            ->where([['alm_prod_ubi.id_almacen','=',$almacen],
                    ['alm_prod_ubi.estado','=',1]])
            ->get();
        
        $nueva_data = [];
        $fecha = date('Y-m-d');
        $tipo_cambio_compra = $this->tipo_cambio_compra($fecha);

        foreach($data as $d){
            // $saldos = $this->saldo_producto($almacen, $d->id_producto, $fecha);
            // $costo = ($saldos['saldo'] !== 0 ? ($saldos['valorizacion'] / $saldos['saldo']) : 0);

            $soles = 0;
            $dolares = 0;

            if ($d->id_moneda == 1){
                $dolares = $d->valorizacion * $tipo_cambio_compra;
                $soles = $d->valorizacion;
            } 
            else if ($d->id_moneda == 2){
                $dolares = $d->valorizacion;
                $soles = $d->valorizacion / $tipo_cambio_compra;
            }
            else {
                $soles = $d->valorizacion;
                $dolares = $d->valorizacion * $tipo_cambio_compra;
            }
            $nuevo = [
                'id_prod_ubi'=> $d->id_prod_ubi,
                'codigo'=> $d->codigo,
                'codigo_anexo'=> $d->codigo_anexo,
                'cod_antiguo'=> $d->cod_antiguo,
                'descripcion'=> $d->descripcion,
                'abreviatura'=> $d->abreviatura,
                'stock'=> $d->stock,
                'simbolo'=> $d->simbolo,
                'id_moneda'=> $d->id_moneda,
                'soles'=> round($soles,4,PHP_ROUND_HALF_UP),
                'dolares'=> round($dolares,4,PHP_ROUND_HALF_UP),
                'costo_promedio'=> round($d->costo_promedio,4,PHP_ROUND_HALF_UP),
                'cantidad_reserva'=> $d->cantidad_reserva,
                // 'cod_posicion'=> $d->cod_posicion,
                'des_clasificacion'=> $d->des_clasificacion,
                'des_categoria'=> $d->des_categoria,
                'des_subcategoria'=> $d->des_subcategoria,
            ];
            array_push($nueva_data,$nuevo);
        }
        // return response()->json($nueva_data);
        $output['data'] = $data;
        return response()->json($output);
    }

    public function saldo_actual($id_producto, $id_posicion){
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as ingresos"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm_det.id_posicion','=',$id_posicion],
                     ['mov_alm.id_tp_mov','<=',1],//ingreso o carga inicial
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as salidas"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm_det.id_posicion','=',$id_posicion],
                     ['mov_alm.id_tp_mov','=',2],//salida
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $saldo = 0;
        if ($ing->ingresos !== null) $saldo += $ing->ingresos;
        if ($sal->salidas !== null) $saldo -= $sal->salidas;

        return $saldo;
    }

    public static function saldo_actual_almacen($id_producto, $id_almacen){
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as ingresos"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm.id_almacen','=',$id_almacen],
                     ['mov_alm.id_tp_mov','<=',1],//ingreso o carga inicial
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as salidas"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm.id_almacen','=',$id_almacen],
                     ['mov_alm.id_tp_mov','=',2],//salida
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $saldo = 0;
        if ($ing->ingresos !== null) $saldo += $ing->ingresos;
        if ($sal->salidas !== null) $saldo -= $sal->salidas;

        return $saldo;
    }

    public function costo_promedio($id_producto, $id_posicion){
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as ingresos"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm_det.id_posicion','=',$id_posicion],
                     ['id_tp_mov','<=',1],//ingreso o carga inicial
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as salidas"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm_det.id_posicion','=',$id_posicion],
                     ['id_tp_mov','=',2],//salida
                     ['mov_alm_det.estado','=',1]])
            ->first();
        
        $valorizacion = 0;
        if ($ing->ingresos !== null) $valorizacion += $ing->ingresos;
        if ($sal->salidas !== null) $valorizacion -= $sal->salidas;

        $saldo = $this->saldo_actual($id_producto, $id_posicion);

        return ($saldo > 0 ? $valorizacion/$saldo : 0);
    }

    public static function valorizacion_almacen($id_producto, $id_almacen){
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as ingresos"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm.id_almacen','=',$id_almacen],
                     ['mov_alm.id_tp_mov','<=',1],//ingreso o carga inicial
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as salidas"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm.id_almacen','=',$id_almacen],
                     ['mov_alm.id_tp_mov','=',2],//salida
                     ['mov_alm_det.estado','=',1]])
            ->first();
        
        $valorizacion = 0;
        if ($ing->ingresos !== null) $valorizacion += $ing->ingresos;
        if ($sal->salidas !== null) $valorizacion -= $sal->salidas;

        return $valorizacion;
    }

    /**Guia de Venta */
    public function listar_guias_venta(){
        $data = DB::table('almacen.guia_ven')
            ->select('guia_ven.*','adm_contri.razon_social','adm_estado_doc.estado_doc',
            'sis_usua.usuario as nombre_usuario','tp_ope.descripcion as ope_descripcion',
            'tp_doc_almacen.abreviatura as tp_doc_almacen')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','guia_ven.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->leftJoin('configuracion.sis_usua','sis_usua.id_usuario','=','guia_ven.usuario')
            ->join('almacen.tp_ope','tp_ope.id_operacion','=','guia_ven.id_operacion')
            ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_guia_venta($id){
        $data = DB::table('almacen.guia_ven')
            ->select('guia_ven.*','cliente.razon_social as cliente_razon_social','cliente.id_contribuyente',
            'adm_contri.razon_social','adm_estado_doc.estado_doc','sis_usua.nombre_corto',
            'trans.codigo as codigo_trans','trans.id_transferencia','tp_doc_almacen.abreviatura as tp_doc_almacen')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','guia_ven.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri as cliente','cliente.id_contribuyente','=','com_cliente.id_contribuyente')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','guia_ven.registrado_por')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.trans','trans.id_guia_ven','=','guia_ven.id_guia_ven')
            ->where('guia_ven.id_guia_ven',$id)
            ->get();
        return response()->json($data);
    }
    public function guardar_guia_venta(Request $request)
    {
        $usuario = Auth::user();
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_ven')->insertGetId(
            [
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_sede' => $request->id_sede,
                'id_cliente' => $request->id_cliente,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                'id_operacion' => $request->id_operacion,
                'transportista' => $request->transportista,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'fecha_traslado' => $request->fecha_traslado,
                'placa' => $request->placa,
                'usuario' => $request->usuario,
                'registrado_por' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_guia_ven'
            );

        DB::table('almacen.serie_numero')
        ->where('id_serie_numero',$request->id_serie_numero)
        ->update(['estado' => 8]);//emitido -> 8
        
        return response()->json(["id_guia_ven"=>$id_guia,"id_almacen"=>$request->id_almacen]);
    }
    public function update_guia_venta(Request $request)
    {
        $data = DB::table('almacen.guia_ven')
            ->where('id_guia_ven', $request->id_guia_ven)
            ->update([
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_sede' => $request->id_sede,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                'transportista' => $request->transportista,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'fecha_traslado' => $request->fecha_traslado,
                'id_cliente' => $request->id_cliente,
                'usuario' => $request->usuario,
                'placa' => $request->placa
            ]);
        return response()->json($data);
    }
    public function prueba($id){
        $trans = DB::table('almacen.trans')
        ->where([['id_guia_ven','=',$id],['estado','=',1]])
        ->first();
        $rspta = isset($trans);
        return response()->json($rspta);
    }
    public function anular_guia_venta(Request $request)
    {
        $rspta = '';
        //verifica si ya tiene guia de compra
        $tra = DB::table('almacen.trans')->where([
            ['id_guia_ven','=',$request->id_guia_ven],
            ['estado','=',1]
        ])->first();
        //si ya tiene guia compra -> no puede anular
        if ($tra !== null && $tra->id_guia_com !== null){
            $rspta = 'No puede anular. La Guia ya generó Ingreso en Almacén Destino \n Debe anular primero el Ingreso.';
        } 
        else {
            $sal = DB::table('almacen.mov_alm')->where([['id_guia_ven','=',$request->id_guia_ven],['estado','=',1]])->first();
            //verifica si ya existe una salida
            if (isset($sal)){
                //salida no revisada
                if ($sal->revisado == 0){
                    //motivo de la anulación
                    $mot = DB::table('almacen.motivo_anu')
                    ->where('id_motivo',$request->id_motivo_obs)
                    ->first();
            
                    $id_usuario = Auth::user()->id_usuario;
                    $obs = $mot->descripcion.'. '.$request->observacion;
                    //Agrega observacion a la guia
                    $id_obs = DB::table('almacen.guia_ven_obs')->insertGetId(
                        [
                            'id_guia_ven'=>$request->id_guia_ven,
                            'observacion'=>$obs,
                            'registrado_por'=>$id_usuario,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                        ],
                            'id_obs'
                    );
                    //Anula guia venta
                    $data = DB::table('almacen.guia_ven')->where('id_guia_ven', $request->id_guia_ven)
                    ->update([ 'estado' => 7 ]);
                    //Anula guia venta detalle
                    DB::table('almacen.guia_ven_det')->where('id_guia_ven',$request->id_guia_ven)
                        ->update([ 'estado' => 7 ]);

                    $guia = DB::table('almacen.guia_ven')
                    ->select('guia_ven.*','tp_doc_almacen.id_tp_doc')
                    ->where('guia_ven.id_guia_ven',$request->id_guia_ven)
                    ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
                    ->first();
    
                    if (isset($guia)){
                        DB::table('almacen.serie_numero')
                        ->where([['id_tp_documento','=',$guia->id_tp_doc],
                                ['serie','=',$guia->serie],
                                ['numero','=',$guia->numero]])
                        ->update([ 'estado' => 7 ]);
                    }
                    
                    //Anula salida
                    DB::table('almacen.mov_alm')->where('id_guia_ven',$request->id_guia_ven)
                        ->update([ 'estado' => 7 ]);
                    
                    $detalle = DB::table('almacen.guia_ven_det')
                        ->where('id_guia_ven',$request->id_guia_ven)
                        ->get();

                    foreach($detalle as $det){
                        //Anula salida detalle
                        DB::table('almacen.mov_alm_det')->where([
                                ['id_guia_ven_det','=',$det->id_guia_ven_det],
                                ['estado','=',1]])
                        ->update([ 'estado' => 7 ]);
                        //Desenlaza guia_ven_det
                        DB::table('almacen.alm_prod_serie')
                        ->where('id_guia_ven_det',$det->id_guia_ven_det)
                        ->update(['id_guia_ven_det' => null]);
                    }
                    if ($tra !== null){
                        //Anula transferencia
                        DB::table('almacen.trans')
                        ->where([['id_guia_ven','=',$request->id_guia_ven],['estado','=',1]])
                        ->update([ 'estado' => 7 ]);
                    }

                    $rspta = 'Se anuló correctamente.';
                } else {
                    $rspta = 'La salida ya fue revisada por el jefe de almacén. Debe solicitar que se quite el visto.';
                }
            } else {
                //motivo de la anulación
                $mot = DB::table('almacen.motivo_anu')
                ->where('id_motivo',$request->id_motivo_obs)
                ->first();
        
                $id_usuario = Auth::user()->id_usuario;
                $obs = $mot->descripcion.'. '.$request->observacion;
                //Agrega observacion a la guia
                $id_obs = DB::table('almacen.guia_ven_obs')->insertGetId(
                    [
                        'id_guia_ven'=>$request->id_guia_ven,
                        'observacion'=>$obs,
                        'registrado_por'=>$id_usuario,
                        'fecha_registro'=>date('Y-m-d H:i:s')
                    ],
                        'id_obs'
                );
                //Anula guia venta
                $data = DB::table('almacen.guia_ven')->where('id_guia_ven', $request->id_guia_ven)
                    ->update([ 'estado' => 7 ]);
                //Anula guia venta detalle
                DB::table('almacen.guia_ven_det')->where('id_guia_ven',$request->id_guia_ven)
                    ->update([ 'estado' => 7 ]);

                $guia = DB::table('almacen.guia_ven')
                ->select('guia_ven.*','tp_doc_almacen.id_tp_doc')
                ->where('guia_ven.id_guia_ven',$request->id_guia_ven)
                ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
                ->first();

                if (isset($guia)){
                    DB::table('almacen.serie_numero')
                    ->where([['id_tp_documento','=',$guia->id_tp_doc],
                            ['serie','=',$guia->serie],
                            ['numero','=',$guia->numero]])
                    ->update([ 'estado' => 7 ]);
                }
            }
        }
        return response()->json($rspta);
    }
    public function listar_detalle_doc($id_doc, $tipo, $id_almacen)
    {
        //Guia de Compra
        if ($tipo == 1){
            $detalle = DB::table('almacen.mov_alm_det')
            ->where([['mov_alm.id_guia_com','=',$id_doc],['mov_alm_det.estado','=',1]])
            ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura',
            'guia_com.serie','guia_com.numero','mov_alm.codigo as cod_mov')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->get();

        //Requerimiento
        } else if ($tipo == 2){
            $detalle = DB::table('almacen.alm_det_req')
            ->where([['alm_det_req.id_requerimiento','=',$id_doc],
                    ['alm_det_req.stock_comprometido','>',0],
                    ['alm_det_req.estado','=',1],
                    ['alm_req.stock_comprometido','=',true]
                    ])
            ->select('alm_det_req.*','alm_prod.id_producto','alm_prod.codigo',
                'alm_prod.descripcion','alm_und_medida.abreviatura')
            ->join('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
            ->get();
            
        //Comprobante de Pago Venta
        } else if ($tipo == 3){
            $detalle = DB::table('almacen.doc_ven_det')
            ->where([['doc_ven_det.id_doc','=',$id_doc],
                    ['doc_ven_det.estado','=',1]])
            ->select('doc_ven_det.id_doc_det','doc_ven_det.id_item','doc_ven_det.cantidad',
            'doc_ven_det.precio_total as valorizacion','alm_prod.id_producto',
            'alm_prod.codigo','alm_prod.descripcion','alm_und_medida.abreviatura')
            ->join('almacen.alm_item','alm_item.id_item','=','doc_ven_det.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            // ->leftjoin('almacen.doc_ven_guia','doc_ven_guia.id_doc_ven','=','doc_ven_det.id_doc')
            ->get();
        }
        $html = '';
        
        if (isset($detalle)){
            foreach($detalle as $d){
                // $data = DB::table('almacen.mov_alm_det')
                //     ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
                //     'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura')
                //     ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
                //     ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
                //     ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
                //     ->where([['mov_alm_det.id_mov_alm','=',$i->id_mov_alm],
                //             ['mov_alm_det.estado','=',1]])
                //     ->get();
        
                // foreach($data as $d){
                    $posicion = DB::table('almacen.alm_prod_ubi')
                    ->select('alm_prod_ubi.*','alm_ubi_posicion.codigo as cod_posicion')
                    ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
                    ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
                    ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
                    ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                    ->where([['alm_prod_ubi.id_producto','=',$d->id_producto],
                             ['alm_almacen.id_almacen','=',$id_almacen],
                             ['alm_prod_ubi.estado','=',1]])
                    ->first();
                    
                    $guia = (isset($d->serie) ? ('GR-'.$d->serie.'-'.$d->numero) : '');
                    $unit = (isset($d->valorizacion) ? (floatval($d->valorizacion) / floatval($d->cantidad)) : 
                            (isset($posicion) ? 
                                $this->costo_promedio($d->id_producto, $posicion->id_posicion) : '')
                            );
                    $total = (isset($d->valorizacion) ? $d->valorizacion : (floatval($d->cantidad) * floatval($unit)));
                    $html.='
                    <tr>
                        <td><input type="checkbox" checked></td>
                        <td hidden><input name="id" style="display:none;" 
                        value="'.(isset($d->id_mov_alm_det) ? ('ing-'.$d->id_mov_alm_det) 
                        : (isset($d->id_detalle_requerimiento) ? ('req-'.$d->id_detalle_requerimiento) 
                        : (isset($d->id_doc_det) ? ('doc-'.$d->id_doc_det) : ''))).'"/></td>
                        <td>'.$guia.'</td>
                        <td>'.(isset($d->cod_mov) ? $d->cod_mov : '').'</td>
                        <td>'.(isset($d->codigo) ? $d->codigo : '').'</td>
                        <td>'.(isset($d->descripcion) ? $d->descripcion : '').'</td>
                        <td>'.(isset($d->cod_posicion) ? $d->cod_posicion : (isset($posicion) ? $posicion->cod_posicion : '')).'</td>
                        <td>'.($tipo == 2 ? $d->stock_comprometido : (isset($d->cantidad) ? $d->cantidad : '')).'</td>
                        <td>'.(isset($d->abreviatura) ? $d->abreviatura : '').'</td>
                        <td>'.$unit.'</td>
                        <td>'.$total.'</td>
                    </tr>
                    ';
                // }
            }
        }
        return json_encode($html);
        // return response()->json($det_ing);
    }
    public function guardar_detalle_ing(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id = explode(',',$request->id);
        $tp = explode(',',$request->tipo);
        $count = count($id);
        $ing_det = '';

        $id_guia_ven = $request->id_guia_ven;
        $id_req = null;
        $id_doc = null;
        $data = 0;

        for ($i=0; $i<$count; $i++){
            if ($tp[$i] == "ing"){
                
                $id_ing = $id[$i];
                $ing_det = DB::table('almacen.mov_alm_det')
                ->select('mov_alm_det.*','alm_prod.id_unidad_medida')
                ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
                ->where([['mov_alm_det.id_mov_alm_det','=',$id_ing]])->first();
                
                $data = DB::table('almacen.guia_ven_det')->insertGetId(
                [
                    'id_guia_ven' => $id_guia_ven,
                    'id_producto' => $ing_det->id_producto,
                    'id_posicion' => $ing_det->id_posicion,
                    'cantidad'    => $ing_det->cantidad,
                    'id_unid_med' => $ing_det->id_unidad_medida,
                    'id_ing_det'  => $id_ing,
                    // 'usuario' => 3,
                    'estado'      => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_guia_ven_det'
                );
            }
            else if ($tp[$i] == "req"){
                $id_req = $id[$i];
                $req_det = DB::table('almacen.alm_det_req')
                ->select('alm_det_req.*','alm_prod.id_producto','alm_prod.id_unidad_medida')
                ->join('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
                ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
                ->where([['alm_det_req.id_detalle_requerimiento','=',$id_req]])->first();
                
                $id_posicion = null;
                if ($request->id_almacen !== null){
                    //jalar posicion relacionada con el producto
                    $posicion = DB::table('almacen.alm_prod_ubi')
                    ->select('alm_ubi_posicion.id_posicion')
                    ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
                    ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
                    ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
                    ->where([['alm_prod_ubi.id_producto','=',$req_det->id_producto],
                            ['alm_prod_ubi.estado','=',1],
                            ['alm_ubi_estante.id_almacen','=',$request->id_almacen]])
                    ->first();
                    if (isset($posicion)){
                        $id_posicion = $posicion->id_posicion;
                    }
                }

                $data = DB::table('almacen.guia_ven_det')->insertGetId(
                [
                    'id_guia_ven' => $id_guia_ven,
                    'id_producto' => $req_det->id_producto,
                    'id_posicion' => $id_posicion,
                    'cantidad'    => $req_det->stock_comprometido,
                    'id_unid_med' => $req_det->id_unidad_medida,
                    'id_req_det'  => $id_req,
                    // 'usuario' => 3,
                    'estado'      => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_guia_ven_det'
                );
                $id_req = $req_det->id_requerimiento;
            }
            else if ($tp[$i] == "doc"){
                $id_doc = $id[$i];
                $doc_det = DB::table('almacen.doc_ven_det')
                ->select('doc_ven_det.*','alm_prod.id_producto','alm_prod.id_unidad_medida')
                ->join('almacen.alm_item','alm_item.id_item','=','doc_ven_det.id_item')
                ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
                ->where([['doc_ven_det.id_doc_det','=',$id_doc]])->first();
                
                if (isset($doc_det)){
                    $id_posicion = null;
                    if ($request->id_almacen !== null){
                        //jalar posicion relacionada con el producto
                        $posicion = DB::table('almacen.alm_prod_ubi')
                        ->select('alm_ubi_posicion.id_posicion')
                        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
                        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
                        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
                        ->where([['alm_prod_ubi.id_producto','=',$doc_det->id_producto],
                                ['alm_prod_ubi.estado','=',1],
                                ['alm_ubi_estante.id_almacen','=',$request->id_almacen]])
                        ->first();
                        if (isset($posicion)){
                            $id_posicion = $posicion->id_posicion;
                        }
                    }
                    $data = DB::table('almacen.guia_ven_det')->insertGetId(
                    [
                        'id_guia_ven' => $id_guia_ven,
                        'id_producto' => $doc_det->id_producto,
                        'id_posicion' => $id_posicion,
                        'cantidad'    => $doc_det->cantidad,
                        'id_unid_med' => $doc_det->id_unidad_medida,
                        // 'id_req_det'  => $id_req,
                        'estado'      => 1,
                        'fecha_registro' => $fecha
                    ],
                        'id_guia_ven_det'
                    );
                    $id_doc = $doc_det->id_doc;
                }
            }
        }
        if ($id_req !== null){
            DB::table('almacen.alm_req')->where('id_requerimiento',$id_req)
            ->update(['stock_comprometido'=>false]);
        }
        if ($id_doc !== null){
            DB::table('almacen.doc_ven_guia')->insertGetId([
                'id_doc_ven'=>$id_doc,
                'id_guia_ven'=>$id_guia_ven,
                'estado'=>1,
                'fecha_registro'=>$fecha,
                ],
                'id_doc_ven_guia'
            );
        }
        return response()->json($data);
    }
    public function posiciones_sin_producto($id_almacen){
        //listar posiciones que no estan enlazadas con ningun producto
        $posiciones = DB::table('almacen.alm_ubi_posicion')
        ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
        ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_posicion','=','alm_ubi_posicion.id_posicion')
        ->leftjoin('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->leftjoin('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['alm_prod_ubi.id_posicion','=',null],
                ['alm_ubi_posicion.estado','=',1],
                ['alm_almacen.id_almacen','=',$id_almacen]])
        ->get();
        return response()->json($posiciones);
    }
    public function listar_guia_ven_det($id_guia){
        $guia = DB::table('almacen.guia_ven')->where('id_guia_ven',$id_guia)->first();
        $detalle = DB::table('almacen.guia_ven_det')
        ->select('guia_ven_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_ubi_posicion.codigo as cod_posicion','mov_alm.codigo as cod_mov',
        'alm_und_medida.abreviatura','mov_alm_det.id_guia_com_det','alm_prod.series',
        'guia_com.serie','guia_com.numero','tp_doc_almacen.abreviatura as tp_doc')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_ven_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_ven_det.id_posicion')
        ->leftjoin('almacen.mov_alm_det','mov_alm_det.id_mov_alm_det','=','guia_ven_det.id_ing_det')
        ->leftjoin('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
        ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
        ->leftjoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
        ->where([['guia_ven_det.id_guia_ven','=',$id_guia],
                ['guia_ven_det.estado','=',1]])
        ->get();

        $html = '';
        $chk = '';
        $posiciones = $this->posiciones_sin_producto($guia->id_almacen);

        foreach($detalle as $d){
            //jalar posicion relacionada con el producto
            $posicion = DB::table('almacen.alm_prod_ubi')
            ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            // ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.id_producto','=',$d->id_producto],
                    ['alm_prod_ubi.estado','=',1],
                    ['alm_ubi_estante.id_almacen','=',$guia->id_almacen]])
            ->get();

            $count = count($posicion);
            $o = false;

            if ($count > 0){
                $posiciones = $posicion;
                $o = true;
            }

            $chk = ($d->series ? 'true' : 'false');
            $series = '';
            $nro_series = 0;

            if ($chk == 'true'){
                $det_series = DB::table('almacen.alm_prod_serie')
                ->where([['alm_prod_serie.id_prod','=',$d->id_producto],
                         ['alm_prod_serie.id_guia_ven_det','=',$d->id_guia_ven_det],
                         ['alm_prod_serie.estado','=',1]])
                ->get();
    
                if (isset($det_series)){
                    foreach($det_series as $s){
                        if ($s->serie !== 'true'){
                            $nro_series++;
                            if ($series !== ''){
                                $series.= ', '.$s->serie;
                            } else {
                                $series = 'Serie(s): '.$s->serie;
                            }
                        }
                    }
                }
            }

            $html.='
            <tr id="reg-'.$d->id_guia_ven_det.'">
                <td>'.($d->tp_doc !== '' ? $d->tp_doc.'-'.$d->serie.'-'.$d->numero : $d->cod_mov).'</td>
                <td><input type="text" class="oculto" name="series" value="'.$chk.'"/><input type="number" class="oculto" name="nro_series" value="'.$nro_series.'"/>'.$d->codigo.'</td>
                <td>'.$d->descripcion.' '.$series.'</td>
                <td>
                    <select class="input-data" name="id_posicion" disabled="true">
                        <option value="0">Elija una opción</option>';
                        // $pos = $this->mostrar_posiciones_cbo();
                        if ($o){
                            foreach ($posiciones as $row) {
                                if ($o){
                                    $html.='<option value="'.$row->id_posicion.'" selected>'.$row->codigo.'</option>';
                                } else {
                                    $html.='<option value="'.$row->id_posicion.'">'.$row->codigo.'</option>';
                                }
                            }
                        }
                    $html.='</select>
                </td>
                <td><input type="number" name="cantidad" value="'.$d->cantidad.'" class="input-data right" disabled/></td>
                <td>'.$d->abreviatura.'</td>
                <td style="display:flex;">';
                    if ($chk == "true") {
                        $descripcion = "'".$d->descripcion."'";
                        $html.='<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" onClick="open_series('.$d->id_guia_ven_det.','.$descripcion.','.$d->cantidad.','.$d->id_producto.');"></i>';
                    }
                    $html.='
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$d->id_guia_ven_det.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle('.$d->id_guia_ven_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$d->id_guia_ven_det.');"></i>
                </td>
            </tr>
            ';
        }
        return json_encode($html);
    }
    public function guardar_guia_ven_detalle(Request $request)
    {
        $data = DB::table('almacen.guia_ven_det')->insertGetId([
                'id_guia_ven' => $request->id_guia_ven,
                'id_producto' => $request->id_producto,
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                'id_unid_med' => $request->id_unid_med,
                // 'unitario' => $request->unitario,
                // 'total' => $request->total,
                // 'usuario' => $request->usuario,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_guia_ven_det'
            );
        return response()->json($data);
    }
    public function update_guia_ven_detalle(Request $request)
    {
        $data = DB::table('almacen.guia_ven_det')
            ->where('id_guia_ven_det', $request->id_guia_ven_det)
            ->update([
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                // 'unitario' => $request->unitario,
                // 'total' => $request->total,
                // 'id_unid_med' => $request->id_unid_med
            ]);
        return response()->json($data);
    }
    public function anular_guia_ven_detalle(Request $request, $id)
    {
        $det = DB::table('almacen.guia_ven_det')
        ->select('guia_ven_det.*','alm_req.id_requerimiento')
        ->leftjoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','guia_ven_det.id_req_det')
        ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
        ->where('guia_ven_det.id_guia_ven_det', $id)
            ->first();

        $data = DB::table('almacen.guia_ven_det')->where('id_guia_ven_det', $id)
            ->update([ 'estado' => 7 ]);

        if (isset($det)){
            if ($det->id_requerimiento !== null){
                DB::table('almacen.alm_req')->where('id_requerimiento',$det->id_requerimiento)
                    ->update([ 'stock_comprometido' => true ]);
            }
            if ($det->id_guia_ven !== null){
                $count = DB::table('almacen.guia_ven_det')
                ->where([['id_guia_ven','=',$det->id_guia_ven],['estado','=',1]])
                ->count();
                if ($count == 0){
                    DB::table('almacen.doc_ven_guia')
                    ->where('id_guia_ven',$det->id_guia_ven)->delete();
                }
            }
        }

        return response()->json($data);
    }
    public function generar_salida_guia($id_guia){
        
        $fecha = date('Y-m-d H:i:s');
        $fecha_emision = date('Y-m-d');
        $id_usuario = Auth::user()->id_usuario;

        $guia = DB::table('almacen.guia_ven')
            ->where('id_guia_ven',$id_guia)->first();
        
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*','alm_prod.codigo','alm_prod.descripcion')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_ven_det.id_producto')
            ->where([['id_guia_ven','=',$id_guia],
                    ['guia_ven_det.estado','=',1]])->get()->toArray();
        
        $msj = 'No hay saldo en almacén de los siguiente(s) producto(s):';
        $sin_saldo = 0;
        $saldo = null;

        foreach($detalle as $det){
            $saldo = $this->saldo_producto($guia->id_almacen,$det->id_producto,$guia->fecha_almacen);
            if ($saldo['saldo'] < floatval($det->cantidad)){
                $msj .= "\n".$det->codigo.' '.$det->descripcion.' (saldo actual) = '.$saldo['saldo'];
                $sin_saldo++;
            }
        }

        if ($sin_saldo > 0){
            return response()->json(['msj'=>$msj,'id_salida'=>0,'saldo'=>$saldo,
            'id_alm'=>$guia->id_almacen,'id_prod'=>$det->id_producto,'fecha'=>$guia->fecha_almacen]);
        } 
        else {
            $codigo = AlmacenController::nextMovimiento(2,
                            $guia->fecha_almacen,
                            $guia->id_almacen);
            
            $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $guia->id_almacen,
                    'id_tp_mov' => 2,//salidas
                    'codigo' => $codigo,
                    'fecha_emision' => $guia->fecha_almacen,
                    'id_guia_ven' => $id_guia,
                    'id_operacion' => $guia->id_operacion,
                    'revisado' => 0,
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm'
                );
            $nuevo_detalle = [];
            $cant = 0;
    
            // foreach ($detalle as $det){
            //     $exist = false;
            //     foreach ($nuevo_detalle as $nue => $value){
            //         if ($det->id_producto == $value['id_producto']){
            //             $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
            //             // $nuevo_detalle[$nue]['valorizacion'] = floatval($value['valorizacion']) + floatval($det->total);
            //             $exist = true;
            //         }
            //     }
            //     if ($exist === false){
            //         $nuevo = [
            //             'id_producto' => $det->id_producto,
            //             'id_posicion' => $det->id_posicion,
            //             // 'id_oc_det' => (isset($det->id_oc_det)) ? $det->id_oc_det : 0,
            //             'cantidad' => floatval($det->cantidad)
            //             // 'valorizacion' => floatval($det->total)
            //             ];
            //         array_push($nuevo_detalle, $nuevo);
            //     }
            // }
    
            foreach ($detalle as $det){
                $costo = $this->costo_promedio($det->id_producto, $det->id_posicion);
                $valorizacion = $costo * $det->cantidad;
    
                $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                    [
                        'id_mov_alm' => $id_salida,
                        'id_producto' => $det->id_producto,
                        'id_posicion' => $det->id_posicion,
                        'cantidad' => $det->cantidad,
                        'valorizacion' => $valorizacion,
                        'id_guia_ven_det' => $det->id_guia_ven_det,
                        'usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                        'id_mov_alm_det'
                    );
                    
                if ($det->id_posicion !== null){
                    $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([['id_producto','=',$det->id_producto],
                                ['id_posicion','=',$det->id_posicion]])
                        ->first();
                    //traer stockActual
                    $saldo = $this->saldo_actual($det->id_producto, $det->id_posicion);
                    $costo = $this->costo_promedio($det->id_producto, $det->id_posicion);
    
                    if (!isset($ubi->id_posicion)){
                        DB::table('almacen.alm_prod_ubi')->insert([
                            'id_producto' => $det->id_producto,
                            'id_posicion' => $det->id_posicion,
                            'stock' => $saldo,
                            'costo_promedio' => $costo,
                            'estado' => 1,
                            'fecha_registro' => $fecha
                        ]);
                    } else {
                        DB::table('almacen.alm_prod_ubi')
                        ->where('id_prod_ubi',$ubi->id_prod_ubi)
                        ->update([  'stock' => $saldo,
                                    'costo_promedio' => $costo
                                ]);
                    }
                }
            }
            // cambiar estado guiaven
            DB::table('almacen.guia_ven')
                ->where('id_guia_ven',$id_guia)->update(['estado'=>9]);//Procesado

            return response()->json(['msj'=>'','id_salida'=>$id_salida]);
        }
    }

    /**Comprobante de Venta */
    public function listar_docs_venta(){
        $data = DB::table('almacen.doc_ven')
        ->select('doc_ven.*','adm_contri.razon_social','adm_estado_doc.estado_doc')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','doc_ven.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_ven.estado')
        // ->where('doc_ven.estado',1)
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_doc_venta($id){
        $doc = DB::table('almacen.doc_ven')
            ->select('doc_ven.*','adm_estado_doc.estado_doc',
            'sis_usua.nombre_corto','adm_contri.razon_social','adm_contri.id_contribuyente')
            // DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_usuario"))
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_ven.estado')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','doc_ven.usuario')
            ->join('comercial.com_cliente','com_cliente.id_cliente','=','doc_ven.id_cliente')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('doc_ven.id_doc_ven',$id)
            ->get();
        return response()->json(['doc'=>$doc]);
    }
    public function guardar_doc_venta(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id_usuario;
        $id_doc = DB::table('almacen.doc_ven')->insertGetId(
            [
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_sede' => $request->id_sede,
                'id_condicion' => $request->id_condicion,
                'credito_dias' => $request->credito_dias,
                'id_cliente' => $request->id_cliente,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => ($request->sub_total !== null ? $request->sub_total : 0),
                'total_descuento' => ($request->total_descuento !== null ? $request->total_descuento : 0),
                'porcen_descuento' => ($request->porcen_descuento !== null ? $request->porcen_descuento : 0),
                'total' => ($request->total !== null ? $request->total : 0),
                'total_igv' => ($request->total_igv !== null ? $request->total_igv : 0),
                'total_ant_igv' => ($request->total_ant_igv !== null ? $request->total_ant_igv : 0),
                'total_a_pagar' => ($request->total_a_pagar !== null ? $request->total_a_pagar : 0),
                'usuario' => $id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_doc_ven'
            );

        DB::table('almacen.serie_numero')
        ->where('id_serie_numero',$request->id_serie_numero)
        ->update(['estado'=>8]);//emitido -> 8

        return response()->json($id_doc);
    }
    public function update_doc_venta(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_ven')
            ->where('id_doc_ven',$request->id_doc_ven)
            ->update([
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_sede' => $request->id_sede,
                'id_condicion' => $request->id_condicion,
                'credito_dias' => $request->credito_dias,
                'id_cliente' => $request->id_cliente,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => ($request->sub_total !== null ? $request->sub_total : 0),
                'total_descuento' => ($request->total_descuento !== null ? $request->total_descuento : 0),
                'porcen_descuento' => ($request->porcen_descuento !== null ? $request->porcen_descuento : 0),
                'total' => ($request->total !== null ? $request->total : 0),
                'total_igv' => ($request->total_igv !== null ? $request->total_igv : 0),
                'total_ant_igv' => ($request->total_ant_igv !== null ? $request->total_ant_igv : 0),
                'total_a_pagar' => ($request->total_a_pagar !== null ? $request->total_a_pagar : 0)
            ]);
        return response()->json($data);
    }
    public function anular_doc_venta($id)
    {
        $data = DB::table('almacen.doc_ven')->where('id_doc_ven', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function guardar_docven_detalle(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id_usuario;
        $data = 0;

        $item = DB::table('almacen.alm_item')
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
        ->where('alm_item.id_producto',$request->id_producto)
        ->first();

        if (isset($item)){
            $data = DB::table('almacen.doc_ven_det')->insertGetId(
                [
                    'id_doc' => $request->id_doc,
                    'id_item' => $item->id_item,
                    'cantidad' => $request->cantidad,
                    'id_unid_med' => $request->id_unid_med,
                    'precio_unitario' => $request->precio_unitario,
                    'sub_total' => $request->sub_total,
                    'porcen_dscto' => 0,
                    'total_dscto' => 0,
                    'precio_total' => $request->sub_total,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                'id_doc_det'
            );
        }
        return response()->json($data);
    }
    public function update_docven_detalle(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_ven_det')
            ->where('id_doc_det', $request->id_doc_det)
            ->update([
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'porcen_dscto' => $request->porcen_dscto,
                'total_dscto' => $request->total_dscto,
                'precio_total' => $request->precio_total,
            ]);
        return response()->json($data);
    }
    public function anular_docven_detalle($id_doc_det)
    {
        $data = DB::table('almacen.doc_ven_det')
            ->where('id_doc_det', $id_doc_det)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function listar_guias_emp($id_empresa){
        $data = DB::table('almacen.guia_ven')
            ->select('guia_ven.*','adm_contri.razon_social','adm_estado_doc.estado_doc')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','guia_ven.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->where('adm_empresa.id_empresa',$id_empresa)
            ->get();
        return response()->json($data);
    }
    public function docven($id_guia, $id_doc){
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', DB::raw('(mov_alm_det.valorizacion / mov_alm_det.cantidad) as precio_unitario'))//jalar el precio unitario del ingreso
            ->leftjoin('almacen.mov_alm_det','mov_alm_det.id_mov_alm','=','guia_ven_det.id_ing_det')
            ->where([['guia_ven_det.id_guia_ven','=',$id_guia],
                    ['guia_ven_det.estado','=',1 ]])
            ->get();
        return $detalle;
    }
    public function listar_doc_ven($id_sede, $id_cliente){
        $data = DB::table('almacen.doc_ven')
        ->select('doc_ven.*','adm_contri.razon_social','cont_tp_doc.abreviatura')
        ->join('comercial.com_cliente','com_cliente.id_cliente','=','doc_ven.id_cliente')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->join('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_ven.id_tp_doc')
        ->leftjoin('almacen.doc_ven_guia','doc_ven_guia.id_doc_ven','=','doc_ven.id_doc_ven')
        ->where([['doc_ven.id_sede','=',$id_sede],
                ['doc_ven.id_cliente','=',$id_cliente],
                ['doc_ven_guia.id_doc_ven','=',null],
                ['doc_ven.estado','=',1]])
        ->get();
        return response()->json($data);
    }
    public function guardar_docven_items_guia($id_guia, $id_doc){
        $fecha = date('Y-m-d H:i:s');
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', DB::raw('(mov_alm_det.valorizacion / mov_alm_det.cantidad) as precio_unitario'))//jalar el precio unitario del ingreso
            ->leftjoin('almacen.mov_alm_det','mov_alm_det.id_mov_alm','=','guia_ven_det.id_ing_det')
            ->where([['guia_ven_det.id_guia_ven','=',$id_guia],
                    ['guia_ven_det.estado','=',1 ]])
            ->get();
        $nuevo_detalle = [];
        $cant = 0;
    
        foreach ($detalle as $det){
            $exist = false;
            foreach ($nuevo_detalle as $nue => $value){
                if ($det->id_producto == $value['id_producto'] && $det->id_guia_ven == $value['id_guia_ven']){
                    $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
                    $nuevo_detalle[$nue]['precio_unitario'] = floatval($value['precio_unitario']) + floatval($det->precio_unitario);
                    // $nuevo_detalle[$nue]['precio_total'] = floatval($value['precio_total']) + floatval($det->precio_total);
                    $exist = true;
                }
            }
            if ($exist === false){
                $nuevo = [
                    'id_guia_ven_det' => $det->id_guia_ven_det,
                    'id_guia_ven' => $det->id_guia_ven,
                    'id_producto' => $det->id_producto,
                    'id_unid_med' => $det->id_unid_med,
                    'cantidad' => floatval($det->cantidad),
                    'precio_unitario' => floatval($det->precio_unitario),
                    'precio_total' => floatval($det->cantidad * $det->precio_unitario)
                    ];
                array_push($nuevo_detalle, $nuevo);
            }
        }
        foreach($nuevo_detalle as $det){
            $item = DB::table('almacen.alm_item')
                ->where('id_producto',$det['id_producto'])
                ->first();

            $id_det = DB::table('almacen.doc_ven_det')->insert(
                [
                    'id_doc'=>$id_doc,
                    'id_item'=>$item->id_item,
                    'cantidad'=>$det['cantidad'],
                    'id_unid_med'=>$det['id_unid_med'],
                    'precio_unitario'=>$det['precio_unitario'],
                    'sub_total'=>$det['precio_total'],
                    'porcen_dscto'=>0,
                    'total_dscto'=>0,
                    'precio_total'=>$det['precio_total'],
                    'id_guia_ven_det'=>$det['id_guia_ven_det'],
                    'estado'=>1,
                    'fecha_registro'=>$fecha
                ]);
        }
        $guia = DB::table('almacen.doc_ven_guia')->insert(
            [
                'id_doc_ven'=>$id_doc,
                'id_guia_ven'=>$id_guia,
                'estado'=>1,
                'fecha_registro'=>$fecha
            ]);
        $salida = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_ven',$id_guia)
            ->first();

        if (isset($salida->id_mov_alm)){
            DB::table('almacen.mov_alm')
                ->where('id_mov_alm',$salida->id_mov_alm)
                ->update(['id_doc_ven'=>$id_doc]);
        }

        return response()->json($guia);
    }

    public function update_series(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $ids = explode(',',$request->ids);
        $anulados = explode(',',$request->anulados);
        $count = count($ids);
        $count_anu = count($anulados);

        if ($count_anu > 0){
            for ($i=0; $i<$count_anu; $i++){
                $id_anu = $anulados[$i];
                if ($id_anu !== ''){
                    $update = DB::table('almacen.alm_prod_serie')
                    ->where('id_prod_serie',$id_anu)
                    ->update(['id_guia_ven_det' => null]);
                }
            }
        }

        if ($count > 0){
            for ($i=0; $i<$count; $i++){
                $id = $ids[$i];
                if ($id !== null){
                    $update = DB::table('almacen.alm_prod_serie')
                    ->where('id_prod_serie',$id)
                    ->update(['id_guia_ven_det' => $request->id_guia_ven_det]);
                }
            }
        }
        return response()->json($count_anu);
    }

    public function saldo_producto($id_almacen,$id_producto,$fecha){
        $saldo = 0;

        $ingresos = DB::table('almacen.mov_alm_det')
        ->select(DB::raw('SUM(mov_alm_det.cantidad) as cant_ingresos'),
                 DB::raw('SUM(mov_alm_det.valorizacion) as val_ingresos'))
        ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['mov_alm_det.id_producto','=',$id_producto],
                ['mov_alm_det.estado','=',1],
                ['mov_alm.fecha_emision','<=',$fecha],
                ['alm_almacen.id_almacen','=',$id_almacen],
                ['mov_alm.id_tp_mov','=',1]])
        ->first();

        $salidas = DB::table('almacen.mov_alm_det')
        ->select(DB::raw('SUM(mov_alm_det.cantidad) as cant_salidas'),
                 DB::raw('SUM(mov_alm_det.valorizacion) as val_salidas'))
        ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['mov_alm_det.id_producto','=',$id_producto],
                ['mov_alm_det.estado','=',1],
                ['mov_alm.fecha_emision','<=',$fecha],
                ['alm_almacen.id_almacen','=',$id_almacen],
                ['mov_alm.id_tp_mov','=',2]])
        ->first();

        $saldo = $ingresos->cant_ingresos - $salidas->cant_salidas;
        $valorizacion = $ingresos->val_ingresos - $salidas->val_salidas;

        return ['saldo'=>$saldo,'valorizacion'=>$valorizacion];
    }
    public function movimientos_producto($id_almacen,$id_producto,$finicio,$ffin){
        $detalle = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision','mov_alm.id_tp_mov as tipo',
            // 'alm_prod.descripcion as prod_descripcion','alm_prod.codigo as prod_codigo',
            // 'alm_und_medida.abreviatura','alm_ubi_posicion.codigo as posicion',
            // 'tp_mov.tp_mov','tp_mov.tipo',
            'guia_com.fecha_emision as guia_com_fecha',
            'guia_com.serie as guia_com_serie','guia_com.numero as guia_com_numero',
            'tp_doc_com.cod_sunat as cod_sunat_com','doc_com.serie as doc_com_serie','doc_com.numero as doc_com_numero',
            'doc_com.fecha_emision as doc_com_fecha','guia_ven.fecha_emision as guia_ven_fecha',
            'guia_ven.serie as guia_ven_serie','guia_ven.numero as guia_ven_numero',
            'tp_doc_ven.cod_sunat as cod_sunat_ven','doc_ven.serie as doc_ven_serie','doc_ven.numero as doc_ven_numero',
            'doc_ven.fecha_emision as doc_ven_fecha','tp_op_com.cod_sunat as op_sunat_ing',
            'tp_op_ven.cod_sunat as op_sunat_sal','doc_com_sunat.cod_doc_sunat as doc_sunat_com',
            'doc_ven_sunat.cod_doc_sunat as doc_sunat_ven')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->leftjoin('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen as doc_com_sunat','doc_com_sunat.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_op_com','tp_op_com.id_operacion','=','guia_com.id_operacion')
            ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_com','tp_doc_com.id_tp_doc','=','doc_com.id_tp_doc')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen as doc_ven_sunat','doc_ven_sunat.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_op_ven','tp_op_ven.id_operacion','=','guia_ven.id_operacion')
            ->leftjoin('almacen.doc_ven','doc_ven.id_doc_ven','=','mov_alm.id_doc_ven')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_ven','tp_doc_ven.id_tp_doc','=','doc_ven.id_tp_doc')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','mov_alm.id_req')
            ->where([['mov_alm.id_almacen','=',$id_almacen],
                    ['mov_alm_det.id_producto','=',$id_producto],
                    ['mov_alm.fecha_emision','>=',$finicio],
                    ['mov_alm.fecha_emision','<=',$ffin],
                    ['mov_alm_det.estado','=',1]])
            // ->orderBy('alm_prod.descripcion','asc')
            ->orderBy('mov_alm.fecha_emision','asc')
            ->orderBy('mov_alm.id_tp_mov','asc')
            ->get();
        return $detalle;
    }
    public function kardex_sunat($almacenes, $finicio, $ffin){
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
                #detalle thead tr td{
                    padding: 4px;
                    background-color: #ddd;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                .left{
                    text-align: left;
                }
                .sup{
                    vertical-align:top;
                }
                </style>
            </head>
            <body>
                <h3 style="margin:0px;"><center>REGISTRO DE INVENTARIO PERMANENTE VALORIZADO - DETALLE DEL INVENTARIO VALORIZADO</center></h3>';
                
                $alm_array = explode(',',$almacenes);
                $count = count($alm_array);
                $mes = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SETIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                
                $mes_inicio = $mes[(date('m', strtotime($finicio))*1)-1];
                $yyyy_inicio = date('Y',strtotime($finicio));
                $mes_fin = $mes[(date('m', strtotime($ffin))*1)-1];
                $yyyy_fin = date('Y',strtotime($ffin));

                for ($i=0; $i<$count; $i++){
                    $id_almacen = $alm_array[$i];
                    $alm = DB::table('almacen.alm_almacen')
                    ->select('alm_almacen.*','adm_contri.razon_social','adm_contri.nro_documento')
                    ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
                    ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
                    ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
                    ->where('id_almacen',$id_almacen)->first();

                    $html.='
                    <table id="detalle" border="0" class="table table-condensed table-bordered table-hover sortable" width="100%">
                    <thead>
                        <tr>
                            <th class="left">Periodo:</th>
                            <th class="left">'.$mes_inicio.' '.$yyyy_inicio.' - '.$mes_fin.' '.$yyyy_fin.'</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">R.U.C.:</th>
                            <th class="left">'.$alm->nro_documento.'</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">Razon Social:</th>
                            <th class="left">'.$alm->razon_social.'</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">Establecimiento:</th>
                            <th class="left">'.$alm->ubicacion.'</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">Metodo Valuación:</th>
                            <th class="left">PROMEDIO PONDERADO</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <td rowspan="2"></td>
                            <td rowspan="2"></td>
                            <td rowspan="2">Fecha</td>
                            <td rowspan="2">Tipo</td>
                            <td rowspan="2">Serie</td>
                            <td rowspan="2">Numero</td>
                            <td rowspan="2">Fecha</td>
                            <td rowspan="2">Tipo</td>
                            <td rowspan="2">Serie</td>
                            <td rowspan="2">Numero</td>
                            <td rowspan="2">Tp.Ope</td>
                            <td colspan="3"><center>Entradas</center></td>
                            <td colspan="3"><center>Salidas</center></td>
                            <td colspan="3"><center>Saldo Final</center></td>
                        </tr>
                        <tr>
                            <td>Cantidad</td>
                            <td>Costo Unit.</td>
                            <td>Costo Total</td>
                            <td>Cantidad</td>
                            <td>Costo Unit.</td>
                            <td>Costo Total</td>
                            <td>Cantidad</td>
                            <td>Costo Unit.</td>
                            <td>Costo Total</td>
                        </tr>
                    </thead>
                    <tbody>';

                    $productos = DB::table('almacen.alm_prod_ubi')
                        ->select('alm_prod_ubi.*','alm_prod.codigo as prod_codigo',
                        'alm_prod.descripcion as prod_descripcion','alm_und_medida.abreviatura')
                        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_ubi.id_producto')
                        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
                        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
                        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
                        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
                        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                        ->where([['alm_prod_ubi.estado','=',1],
                                ['alm_almacen.id_almacen','=',$id_almacen]])
                        ->get();
            
            
                        foreach($productos as $prod){
                            $detalle = $this->movimientos_producto($id_almacen, $prod->id_producto, $finicio, $ffin);
                            $size = count($detalle);

                            if ($size > 0){

                                $html.='
                                <tr>
                                    <td>Código de Existencia:</td>
                                    <td>01 MERCADERIAS</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>
                                <tr>
                                    <td>Descripción:</td>
                                    <td>'.$prod->prod_codigo.' '.$prod->prod_descripcion.'</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>
                                <tr>
                                    <td>Codigo de Unidad:</td>
                                    <td>'.$prod->abreviatura.'</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>';
    
                                $saldo = 0;
                                $saldo_valor = 0;
                                $total_ing = 0;
                                $total_sal = 0;
                                $cant_ing = 0;
                                $cant_sal = 0;
                                $stock_inicial = false;
    
                                $html.='
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td>
                                    <td>Stock Inicial:</td>
                                    <td class="right" style="mso-number-format:"0.00";">'.$saldo.'</td>
                                    <td class="right" style="mso-number-format:"0.00";">'.($saldo !== 0 ? ($saldo_valor / $saldo) : 0).'</td>
                                    <td class="right" style="mso-number-format:"0.00";">'.$saldo_valor.'</td>
                                </tr>';
    
                                foreach($detalle as $det){
                                    if ($det->tipo == 1 || $det->tipo == 0){
                                        $saldo += $det->cantidad;
                                        $saldo_valor += $det->valorizacion;
                                    } 
                                    else if ($det->tipo == 2){
                                        $saldo -= $det->cantidad;
                                        $saldo_valor -= $det->valorizacion;
                                    }
                                    if ($det->tipo == 1){
                                        $total_ing += floatval($det->valorizacion);
                                        $cant_ing += floatval($det->cantidad);
                                        $unitario = floatval($det->valorizacion) / floatval($det->cantidad);
                                        $saldo_unitario = $saldo !== 0 ? ($saldo_valor / $saldo) : 0;
    
                                        $html.='
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>'.$det->doc_com_fecha.'</td>
                                            <td>'.$det->cod_sunat_com.'</td>
                                            <td>'.$det->doc_com_serie.'</td>
                                            <td>'.$det->doc_com_numero.'</td>
                                            <td>'.$det->guia_com_fecha.'</td>
                                            <td>'.$det->doc_sunat_com.'</td>
                                            <td>'.$det->guia_com_serie.'</td>
                                            <td>'.$det->guia_com_numero.'</td>
                                            <td>'.$det->op_sunat_ing.'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($det->cantidad,2).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($unitario,3).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($det->valorizacion,3).'</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo,2).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo_unitario,3).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo_valor,3).'</td>
                                        </tr>';
                                    }
                                    else if ($det->tipo == 2){
                                        $total_sal += floatval($det->valorizacion);
                                        $cant_sal += floatval($det->cantidad);
                                        $unitario = floatval($det->valorizacion) / floatval($det->cantidad);
                                        $saldo_unitario = $saldo !== 0 ? ($saldo_valor / $saldo) : 0;
    
                                        $html.='
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>'.$det->doc_ven_fecha.'</td>
                                            <td>'.$det->cod_sunat_ven.'</td>
                                            <td>'.$det->doc_ven_serie.'</td>
                                            <td>'.$det->doc_ven_numero.'</td>
                                            <td>'.$det->guia_ven_fecha.'</td>
                                            <td>'.$det->doc_sunat_ven.'</td>
                                            <td>'.$det->guia_ven_serie.'</td>
                                            <td>'.$det->guia_ven_numero.'</td>
                                            <td>'.$det->op_sunat_sal.'</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.floatval($det->cantidad).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.(floatval($det->valorizacion) / floatval($det->cantidad)).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.$det->valorizacion.'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo,2).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo_unitario,3).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo_valor,3).'</td>
                                        </tr>';
                                    }
                                    // $codigo = $det->prod_codigo;
                                }
                                $html.='
                                <tr>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td>
                                    <td><strong>Total:</strong></td>
                                    <td class="right"><strong>'.$cant_ing.'</strong></td><td></td>
                                    <td class="right"><strong>'.$total_ing.'</strong></td>
                                    <td class="right"><strong>'.$cant_sal.'</strong></td><td></td>
                                    <td class="right"><strong>'.$total_sal.'</strong></td><td></td><td></td><td></td>
                                </tr>'; 
                            }
                        }
                            $html.='
                        </tbody>
                    </table>';
                    }
                $html.='
            </body>
        </html>';
        
        return $html;
        // return $detalle;
    }
    public function download_kardex_sunat($almacenes, $finicio, $ffin){
        $data = $this->kardex_sunat($almacenes, $finicio, $ffin);
        return view('almacen/reportes/kardex_sunat_excel', compact('data'));
    }
    public function direccion_almacen($id_almacen){
        $alm = DB::table('almacen.alm_almacen')
        ->where('id_almacen',$id_almacen)
        ->first();
        $data = $alm->ubicacion; 
        return response()->json($data);
    }

    public function listar_tp_docs(){
        $data = DB::table('almacen.tp_doc_almacen')
        ->select('tp_doc_almacen.*','cont_tp_doc.cod_sunat')
        ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','tp_doc_almacen.id_tp_doc')
        ->where('tp_doc_almacen.estado',1)->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tp_doc($id){
        $data = DB::table('almacen.tp_doc_almacen')
        ->where('id_tp_doc_almacen',$id)
        ->get();
        return response()->json($data);
    }

    public function guardar_tp_doc(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_tp_doc = DB::table('almacen.tp_doc_almacen')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'id_tp_doc' => $request->id_tp_doc,
                'tipo' => $request->tipo,
                'abreviatura' => $request->abreviatura,
                'estado' => 1,
                'usuario' => $request->usuario,
                'fecha_registro' => $fecha
            ],
                'id_tp_doc_almacen'
            );
        return response()->json($id_tp_doc);
    }
    
    public function update_tp_doc(Request $request){
        $data = DB::table('almacen.tp_doc_almacen')
            ->where('id_tp_doc_almacen', $request->id_tp_doc_almacen)
            ->update([  'descripcion' => $request->descripcion,
                        'id_tp_doc' => $request->id_tp_doc,
                        'tipo' => $request->tipo,
                        'abreviatura' => $request->abreviatura ]);
        return response()->json($data);
    }

    public function anular_tp_doc(Request $request, $id){
        $data = DB::table('almacen.tp_doc_almacen')
            ->where('id_tp_doc_almacen', $id)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function listar_ocs(){
        $data = DB::table('logistica.log_ord_compra')
            ->select('log_ord_compra.*','adm_contri.razon_social')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['log_ord_compra.estado','!=',7],
                    ['log_ord_compra.en_almacen','=',false],
                    ['log_ord_compra.id_tp_documento','=',2]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function datos_producto($id_producto){
        $producto = DB::table('almacen.alm_prod')
        ->select('alm_prod.*','alm_und_medida.abreviatura','alm_subcat.descripcion as des_subcategoria',
        'alm_cat_prod.descripcion as des_categoria','alm_tp_prod.descripcion as des_tipo',
        'alm_tp_prod.id_tipo_producto','alm_cat_prod.codigo as cat_codigo','alm_ubi_posicion.codigo as cod_posicion',
        'alm_subcat.codigo as subcat_codigo','alm_clasif.descripcion as des_clasificacion')
        ->join('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
        ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
        ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('almacen.alm_clasif','alm_clasif.id_clasificacion','=','alm_prod.id_clasif')
        ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_producto','=','alm_prod.id_producto')
        ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
        ->where('alm_prod.id_producto',$id_producto)
        ->first();

        $html = '
            <tr>
                <th width="80px">Código</th>
                <td>'.$producto->codigo.'</td>
                <th width="80px">Descripción</th>
                <td>'.$producto->descripcion.'</td>
                <th width="80px">Unid.Med.</th>
                <td>'.$producto->abreviatura.'</td>
            </tr>
            <tr>
                <th>Tipo</th>
                <td width="23%">'.$producto->des_tipo.'</td>
                <th>Categoría</th>
                <td>'.$producto->des_categoria.'</td>
                <th>Sub-Categoría</th>
                <td>'.$producto->des_subcategoria.'</td>
            </tr>
            <tr>
                <th>Clasificación</th>
                <td>'.$producto->des_clasificacion.'</td>
                <th>Cod.Anexo</th>
                <td>'.$producto->codigo_anexo.'</td>
                <th>Ubicación</th>
                <td>'.$producto->cod_posicion.'</td>
            </tr>
            ';
        return json_encode($html);
    }
    public function listar_kardex_producto($id_producto,$almacen,$finicio,$ffin){
        $html = '';
        $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','alm_ubi_posicion.codigo as cod_posicion',
            'mov_alm.fecha_emision','mov_alm.id_tp_mov','mov_alm.codigo',
            DB::raw("CONCAT(tp_doc_com.abreviatura,'-',guia_com.serie,'-',guia_com.numero) as guia_com"),
            'tp_doc_com.cod_sunat as cod_sunat_doc_com',
            'tp_ope_com.cod_sunat as cod_sunat_ope_com',
            'tp_ope_com.descripcion as des_ope_com',
            DB::raw("CONCAT(tp_doc_ven.abreviatura,'-',guia_ven.serie,'-',guia_ven.numero) as guia_ven"),
            // 'tp_doc_ven.descripcion as des_doc_ven',
            'tp_doc_ven.cod_sunat as cod_sunat_doc_ven',
            'tp_ope_ven.cod_sunat as cod_sunat_ope_ven',
            'tp_ope_ven.descripcion as des_ope_ven','adm_contri.razon_social')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->leftjoin('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->leftjoin('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->leftjoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen as tp_doc_guia_com','tp_doc_guia_com.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_com','tp_doc_com.id_tp_doc','=','tp_doc_guia_com.id_tp_doc')
            ->leftjoin('almacen.tp_ope as tp_ope_com','tp_ope_com.id_operacion','=','mov_alm.id_operacion')
            ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','mov_alm_det.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen as tp_doc_guia_ven','tp_doc_guia_ven.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_ven','tp_doc_ven.id_tp_doc','=','tp_doc_guia_ven.id_tp_doc')
            ->leftjoin('almacen.tp_ope as tp_ope_ven','tp_ope_ven.id_operacion','=','guia_ven.id_operacion')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                    ['mov_alm.fecha_emision','>=',$finicio],
                    ['mov_alm.fecha_emision','<=',$ffin],
                    ['alm_almacen.id_almacen','=',$almacen],
                    ['mov_alm_det.estado','=',1]])
            ->orderBy('mov_alm.fecha_emision','asc')
            ->orderBy('mov_alm.id_tp_mov','asc')
            ->get();

        if (isset($data)){
            $saldo = 0;
            $saldo_valor = 0;
            $suma_ing_cant = 0;
            $suma_sal_cant = 0;
            $suma_ing_val = 0;
            $suma_sal_val = 0;

            foreach($data as $d){
                if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0){//ingreso o inicial
                    $saldo += $d->cantidad;
                    $saldo_valor += $d->valorizacion;
                    $suma_ing_cant += $d->cantidad;
                    $suma_ing_val += $d->valorizacion;
                } 
                else if ($d->id_tp_mov == 2){//salida
                    $saldo -= $d->cantidad;
                    $saldo_valor -= $d->valorizacion;
                    $suma_sal_cant += $d->cantidad;
                    $suma_sal_val += $d->valorizacion;
                }
                if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0){
                    $html.='
                    <tr id="'.$d->id_mov_alm_det.'">
                        <td>'.$d->fecha_emision.'</td>
                        <td>'.($d->guia_com == '--' ? $d->codigo : $d->guia_com).'</td>
                        <td></td>
                        <td>'.($d->razon_social !== null ? $d->razon_social : '').'</td>
                        <td class="right" style="background:#ffffb0;">'.$d->cantidad.'</td>
                        <td class="right" style="background:#ffffb0;">0</td>
                        <td class="right" style="background:#ffffb0;">'.$saldo.'</td>
                        <td class="right" style="background:#d8fcfc;">'.$d->valorizacion.'</td>
                        <td class="right" style="background:#d8fcfc;">0</td>
                        <td class="right" style="background:#d8fcfc;">'.$saldo_valor.'</td>
                        <td>'.$d->cod_posicion.'</td>
                        <td>'.($d->cod_sunat_ope_com !== null ? $d->cod_sunat_ope_com : '').'</td>
                        <td>'.$d->des_ope_com.'</td>
                    </tr>';
                }
                else if ($d->id_tp_mov == 2){
                    $html.='
                    <tr id="'.$d->id_mov_alm_det.'">
                        <td>'.$d->fecha_emision.'</td>
                        <td>'.$d->guia_ven.'</td>
                        <td></td>
                        <td></td>
                        <td class="right" style="background:#ffffb0;">0</td>
                        <td class="right" style="background:#ffffb0;">'.$d->cantidad.'</td>
                        <td class="right" style="background:#ffffb0;">'.$saldo.'</td>
                        <td class="right" style="background:#d8fcfc;">0</td>
                        <td class="right" style="background:#d8fcfc;">'.$d->valorizacion.'</td>
                        <td class="right" style="background:#d8fcfc;">'.$saldo_valor.'</td>
                        <td>'.$d->cod_posicion.'</td>
                        <td>'.$d->cod_sunat_ope_ven.'</td>
                        <td>'.$d->des_ope_ven.'</td>
                    </tr>';
                }
            }
            // $html.='</tbody></table>';
        }
        return ['html'=>$html,'suma_ing_cant'=>$suma_ing_cant,'suma_sal_cant'=>$suma_sal_cant,'suma_ing_val'=>$suma_ing_val,'suma_sal_val'=>$suma_sal_val];
    }
    public function kardex_producto($id_producto,$almacen,$finicio,$ffin){
        $html = $this->listar_kardex_producto($id_producto,$almacen,$finicio,$ffin);
        return json_encode($html);
    }
    public function download_kardex_producto($id_producto,$almacen,$finicio,$ffin){
        $data = $this->listar_kardex_producto($id_producto,$almacen,$finicio,$ffin);
        $html = $data['html'];
        return view('almacen/reportes/kardex_detallado_excel', compact('html'));
    }
    public function saldo_por_producto($id_producto){
        $data = DB::table('almacen.alm_prod_ubi')
        ->select('alm_prod_ubi.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_ubi_posicion.codigo as cod_posicion','alm_almacen.descripcion as des_almacen')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_ubi.id_producto')
        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['alm_prod_ubi.id_producto','=',$id_producto],
                ['alm_prod_ubi.stock','>',0],['alm_prod_ubi.estado','=',1]])
        ->get();
        return response()->json($data);
    }
    public function listar_ingresos($almacenes, $documentos, $condiciones, $fecha_inicio, $fecha_fin, $id_proveedor, $id_usuario, $moneda, /*$referenciado,*/ $transportista){
        $alm_array = explode(',',$almacenes);
        $doc_array = explode(',',$documentos);
        $con_array = explode(',',$condiciones);

        $hasWhere = [];
        if ($id_proveedor !== null && $id_proveedor > 0){
            $hasWhere[] = ['guia_com.id_proveedor','=',$id_proveedor];
        }
        if ($id_usuario !== null && $id_usuario > 0){
            $hasWhere[] = ['guia_com.usuario','=',$id_usuario];
        }
        if ($moneda == 1 || $moneda == 2){
            $hasWhere[] = ['doc_com.moneda','=',$moneda];
        }
        if ($transportista !== null && $transportista > 0){
            $hasWhere[] = ['guia_com.transportista','=',$transportista];
        }

        // $count = count($doc_array);
        // $docs = [];
        // $alm = [];
        // $oc = '';

        // for ($i=0; $i<$count; $i++){
        //     if ($doc_array[$i] > 100){ //Docs
        //         $docs[] = [$doc_array[$i] - 100];
        //     } 
        //     else if ($doc_array[$i] < 100){ //Alm
        //         $alm[] = [intval($doc_array[$i])];
        //     }
        //     else {
        //         $oc = intval($doc_array[$i]);
        //     }
        // }

        $data = DB::table('almacen.mov_alm')
        ->select('mov_alm.*','sis_moneda.simbolo','doc_com.total','doc_com.fecha_vcmto',
            'doc_com.total_igv','doc_com.total_a_pagar','cont_tp_doc.abreviatura',
            'doc_com.credito_dias','log_cdn_pago.descripcion as des_condicion',
            'doc_com.fecha_emision as fecha_doc','alm_almacen.descripcion as des_almacen',
            'doc_com.tipo_cambio','doc_com.moneda',
            DB::raw("CONCAT(doc_com.serie,'-',doc_com.numero) as doc"),
            DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia"),
            'guia_com.fecha_emision as fecha_guia','adm_contri.nro_documento',
            'adm_contri.razon_social','tp_ope.descripcion as des_operacion',
            'sis_usua.nombre_corto as nombre_trabajador')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
        ->leftjoin('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
        ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('almacen.tp_ope','tp_ope.id_operacion','=','mov_alm.id_operacion')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
        // ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
        // ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        // ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','mov_alm.id_doc_com')
        ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
        ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
        ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','doc_com.id_condicion')
        ->whereIn('mov_alm.id_almacen',$alm_array)
        // ->whereIn('guia_com.id_tp_doc_almacen',$doc_array)
        // ->whereIn('doc_com.id_tp_doc',$docs)
        ->whereIn('mov_alm.id_operacion',$con_array)
        ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
        ->where([['mov_alm.estado','!=',7]])
        ->where($hasWhere)
        ->get();

        $nueva_data = [];

        foreach($data as $d){
            $ocs = DB::table('almacen.guia_com_oc')
            ->select('log_ord_compra.codigo')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
            ->where('id_guia_com',$d->id_guia_com)
            ->get();
            $ordenes = '';
            foreach($ocs as $oc){
                if ($ordenes !== ''){
                    $ordenes.= ', '.$oc->codigo;
                } else {
                    $ordenes = $oc->codigo;
                }
            }
            $nuevo = [
                'id_mov_alm'=>$d->id_mov_alm,
                'revisado'=>$d->revisado,
                'fecha_emision'=>$d->fecha_emision,
                'codigo'=>$d->codigo,
                'fecha_guia'=>$d->fecha_guia,
                'guia'=>$d->guia,
                'fecha_doc'=>$d->fecha_doc,
                'abreviatura'=>$d->abreviatura,
                'doc'=>$d->doc,
                'nro_documento'=>$d->nro_documento,
                'razon_social'=>$d->razon_social,
                'simbolo'=>$d->simbolo,
                'moneda'=>$d->moneda,
                'total'=>$d->total,
                'total_igv'=>$d->total_igv,
                'total_a_pagar'=>$d->total_a_pagar,
                'des_condicion'=>$d->des_condicion,
                'credito_dias'=>$d->credito_dias,
                'des_operacion'=>$d->des_operacion,
                'fecha_vcmto'=>$d->fecha_vcmto,
                'nombre_trabajador'=>$d->nombre_trabajador,
                'tipo_cambio'=>$d->tipo_cambio,
                'des_almacen'=>$d->des_almacen,
                'fecha_registro'=>$d->fecha_registro,
                'ordenes'=>$ordenes
            ];
            array_push($nueva_data,$nuevo);
        }

        return response()->json($nueva_data);
        // return response()->json(['docs'=>$docs,'alm'=>$alm,'oc'=>$oc]);
    }
    public function listar_salidas($almacenes, $documentos, $condiciones, $fecha_inicio, $fecha_fin, $id_cliente, $id_usuario, $moneda, $referenciado){
        $alm_array = explode(',',$almacenes);
        $doc_array = explode(',',$documentos);
        $con_array = explode(',',$condiciones);

        $hasWhere = [];
        if ($id_cliente !== null && $id_cliente > 0){
            $hasWhere[] = ['guia_ven.id_cliente','=',$id_cliente];
        }
        if ($id_usuario !== null && $id_usuario > 0){
            $hasWhere[] = ['guia_ven.usuario','=',$id_usuario];
        }
        if ($moneda == 1 || $moneda == 2){
            $hasWhere[] = ['doc_ven.moneda','=',$moneda];
        }

        $data = DB::table('almacen.mov_alm')
        ->select('mov_alm.*','sis_moneda.simbolo','doc_ven.total','doc_ven.fecha_vcmto',
                'doc_ven.total_igv','doc_ven.total_a_pagar','cont_tp_doc.abreviatura',
                'doc_ven.credito_dias','log_cdn_pago.descripcion as des_condicion',
                'doc_ven.fecha_emision as fecha_doc','alm_almacen.descripcion as des_almacen',
                'doc_ven.tipo_cambio','doc_ven.moneda',
                DB::raw("CONCAT(doc_ven.serie,'-',doc_ven.numero) as doc"),
                DB::raw("CONCAT(guia_ven.serie,'-',guia_ven.numero) as guia"),
                'guia_ven.fecha_emision as fecha_guia','adm_contri.nro_documento',
                'adm_contri.razon_social','tp_ope.descripcion as des_operacion',
                'sis_usua.nombre_corto as nombre_trabajador')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
        ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
        ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->join('almacen.tp_ope','tp_ope.id_operacion','=','mov_alm.id_operacion')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
        // ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
        // ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        // ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        ->leftjoin('almacen.doc_ven','doc_ven.id_doc_ven','=','mov_alm.id_doc_ven')
        ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_ven.id_tp_doc')
        ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_ven.moneda')
        ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','doc_ven.id_condicion')
        ->whereIn('mov_alm.id_almacen',$alm_array)
        // ->whereIn('guia_ven.id_tp_doc_almacen',$doc_array)
        ->whereIn('mov_alm.id_operacion',$con_array)
        ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
        ->where([['mov_alm.estado','!=',7]])
        ->where($hasWhere)
        ->get();

        return response()->json($data);
    }
    public function update_revisado($id_mov_alm, $revisado, $obs){
        $data = DB::table('almacen.mov_alm')
        ->where('id_mov_alm',$id_mov_alm)
        ->update(['revisado' => $revisado,
                  'obs' => $obs ]);
        return response()->json($data);
    } 
    public function listar_busqueda_ingresos($almacenes, $tipo, $descripcion, $documentos, $fecha_inicio, $fecha_fin){
        $alm_array = explode(',',$almacenes);
        $doc_array = explode(',',$documentos);
        $des = strtoupper($descripcion);
        $hasWhere = '';

        if ($tipo == 1){
            $hasWhere = 'alm_prod.descripcion';
        } 
        else if ($tipo == 2){
            $hasWhere = 'alm_prod.codigo';
        } 
        else if ($tipo == 3){
            $hasWhere = 'alm_prod.codigo_anexo';
        }

        if ($descripcion !== '<vacio>'){
            $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision',
            'tp_doc_almacen.abreviatura as tp_doc','guia_com.fecha_emision as fecha_guia',
            DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia"),
            'adm_contri.razon_social','adm_contri.nro_documento','alm_almacen.descripcion as alm_descripcion',
            'alm_prod.codigo_anexo','alm_prod.codigo','alm_prod.descripcion',
            'tp_ope.descripcion as ope_descripcion','adm_estado_doc.estado_doc')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->leftjoin('almacen.guia_com','guia_com.id_guia','=','mov_alm_det.id_guia_com')
            ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->join('almacen.tp_ope','tp_ope.id_operacion','=','mov_alm.id_operacion')
            ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->whereIn('guia_com.id_tp_doc_almacen',$doc_array)
            ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
            ->where($hasWhere,'like','%'.$des.'%')
            // ->where( ( ($des !== '') ? [$hasWhere,'like','%'.$des.'%'] : '' ) )
            ->get();
        } else {
            $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision',
            'tp_doc_almacen.abreviatura as tp_doc','guia_com.fecha_emision as fecha_guia',
            DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia"),
            'adm_contri.razon_social','adm_contri.nro_documento','alm_almacen.descripcion as alm_descripcion',
            'alm_prod.codigo_anexo','alm_prod.codigo','alm_prod.descripcion',
            'tp_ope.descripcion as ope_descripcion','adm_estado_doc.estado_doc')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
            ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->whereIn('guia_com.id_tp_doc_almacen',$doc_array)
            ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
            ->get();
        }

        return response()->json($data);
    }
    public function imprimir_guia_ingreso($id_ing){
        $id = $this->decode5t($id_ing);
        $result = $this->get_ingreso($id);
        $ingreso = $result['ingreso'];
        $detalle = $result['detalle'];
        $ocs = $result['ocs'];

        $cod_ocs = '';
        foreach($ocs as $oc){
            if ($cod_ocs == ''){
                $cod_ocs .= $oc->codigo;
            } else {
                $cod_ocs .= ', '.$oc->codigo;
            }
        }
        $revisado = ($ingreso->revisado !== 0 ? 'No Revisado' : 
                    ($ingreso->revisado !== 1 ? 'Revisado' : 'Observado'));
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');

        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:11px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td,
                #detalle tfoot tr td{
                    font-size:11px;
                    padding: 4px;
                }
                #detalle tfoot{
                    border-top: 1px dashed #343a40;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                .guinda{
                    background-color: #8f1c1c;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tr>
                        <td>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$ingreso->ruc_empresa.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$ingreso->empresa_razon_social.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">.::Sistema ERP v1.0::.</p>
                        </td>
                        <td>
                            <p style="text-align:right;font-size:10px;margin:0px;">Fecha: '.$fecha_actual.'</p>
                            <p style="text-align:right;font-size:10px;margin:0px;">Hora : '.$hora_actual.'</p>
                        </td>
                    </tr>
                </table>
                <div style="border:1px #212121 solid;padding:2px;background-color:#e5e5e5;width:60%;margin:auto">
                    <h3 style="margin:0px;"><center>'.$ingreso->tp_doc_descripcion.'</center></h3>
                </div>
                <h5 style="margin:5px;"><center>'.$revisado.'</center></h5>
                
                <table border="0" style="border:1px #212121 dashed;padding:3px;">
                    <tr>
                        <td width=120px class="subtitle">Sucursal / Almacén</td>
                        <td width=10px>:</td>
                        <td colSpan="7" class="verticalTop">'.$ingreso->empresa_razon_social.' / '.$ingreso->des_almacen.'</td>
                    </tr>
                    <tr>
                        <td>TD.</td>
                        <td width=10px>:</td>
                        <td width=130px>'.$ingreso->guia.'</td>
                        <td width=50px>Fecha</td>
                        <td width=10px>:</td>
                        <td width=100px>'.$ingreso->fecha_guia.'</td>
                        <td width=50px>Moneda</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->des_moneda.'</td>
                        <td width=30px>T.C.</td>
                        <td width=10px>:</td>
                        <td width=40px>'.$ingreso->tipo_cambio.'</td>
                    </tr>
                    <tr>
                        <td>Señores</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">'.$ingreso->razon_social.'</td>
                        <td width=50px>Teléfono(s)</td>
                        <td width=10px>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Dirección</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">'.$ingreso->direccion_fiscal.'</td>
                        <td width=50px>RUC</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->nro_documento.'</td>
                    </tr>
                    <tr>
                        <td>Responsable</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">'.$ingreso->persona.'</td>
                        <td width=50px>Condición</td>
                        <td width=10px>:</td>
                        <td colSpan="4">'.$ingreso->ope_descripcion.'</td>
                    </tr>
                    <tr>
                        <td>Cod. Ingreso</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">'.$ingreso->codigo.'</td>
                        <td width=50px>Fecha Ing</td>
                        <td width=10px>:</td>
                        <td colSpan="4">'.$ingreso->fecha_emision.'</td>
                    </tr>
                </table>
                <br/>
                <table id="detalle">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Código</th>
                            <th>Cód.Anexo</th>
                            <th width=40% >Descripción</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>V.Compra</th>
                            <th>Agregado</th>
                            <th>P.Total</th>
                            <th>Unitario</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $i = 1;
                    $total = 0;
                    $unitarios = 0;
                    $agregado = 0;

                    foreach($detalle as $det){
                        $unitario = floatval($det->valorizacion)/floatval($det->cantidad);
                        $total += floatval($det->valorizacion);
                        $unitarios += floatval($unitario);
                        $agregado += floatval($det->unitario_adicional);
                        $html.='
                        <tr>
                            <td class="right">'.$i.'</td>
                            <td>'.$det->codigo.'</td>
                            <td>'.$det->codigo_anexo.'</td>
                            <td>'.$det->descripcion.'</td>
                            <td class="right">'.$det->cantidad.'</td>
                            <td>'.$det->abreviatura.'</td>
                            <td class="right">'.$det->unitario.'</td>
                            <td class="right">'.$det->unitario_adicional.'</td>
                            <td class="right">'.$det->valorizacion.'</td>
                            <td class="right">'.$unitario.'</td>
                        </tr>';
                        $i++;
                    }
                    $igv = $total * 0.18;
                    $html.='</tbody>
                    <tfoot>
                        <tr>
                            <td class="right" colSpan="6"><strong>Totales</strong></td>
                            <td class="right"></td>
                            <td class="right">'.$agregado.'</td>
                            <td class="right">'.$total.'</td>
                            <td class="right">'.$unitarios.'</td>
                        </tr>
                    </tfoot>
                </table>
                <br/>
                <div width=200px style="border:1px #212121 solid;padding:2px;background-color:#e5e5e5;">
                    <table>
                        <tr>
                            <td class="right"><strong>Monto Neto: </strong></td>
                            <td class="right">'.$total.'</td>
                            <td class="right"><strong>Impuesto: </strong></td>
                            <td class="right">'.$igv.'</td>
                            <td class="right"><strong>Total Doc: </strong></td>
                            <td class="right">'.($total + $igv).'</td>
                        </tr>
                    </table>
                </div>
                <p style="text-align:right;font-size:11px;">Elaborado por: '.$ingreso->nom_usuario.' '.$ingreso->fecha_registro.'</p>

            </body>
        </html>';
        
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
        return $pdf->download('ingreso.pdf');
    }
    public function listar_busqueda_salidas($almacenes, $tipo, $descripcion, $documentos, $fecha_inicio, $fecha_fin){
        $alm_array = explode(',',$almacenes);
        $doc_array = explode(',',$documentos);
        $des = strtoupper($descripcion);
        $hasWhere = '';

        if ($tipo == 1){
            $hasWhere = 'alm_prod.descripcion';
        } 
        else if ($tipo == 2){
            $hasWhere = 'alm_prod.codigo';
        } 
        else if ($tipo == 3){
            $hasWhere = 'alm_prod.codigo_anexo';
        }

        if ($descripcion !== '<vacio>'){
            $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision',
            'tp_doc_almacen.abreviatura as tp_doc','guia_ven.fecha_emision as fecha_guia',
            DB::raw("CONCAT(guia_ven.serie,'-',guia_ven.numero) as guia"),
            'adm_contri.razon_social','adm_contri.nro_documento','alm_almacen.descripcion as alm_descripcion',
            'alm_prod.codigo_anexo','alm_prod.codigo','alm_prod.descripcion',
            'tp_ope.descripcion as ope_descripcion','adm_estado_doc.estado_doc')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','mov_alm_det.id_guia_ven_det')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_ven.id_operacion')
            ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->whereIn('guia_ven.id_tp_doc_almacen',$doc_array)
            ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
            ->where($hasWhere,'like','%'.$des.'%')
            // ->where( ( ($des !== '') ? [$hasWhere,'like','%'.$des.'%'] : '' ) )
            ->get();
        } else {
            $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision',
            'tp_doc_almacen.abreviatura as tp_doc','guia_ven.fecha_emision as fecha_guia',
            DB::raw("CONCAT(guia_ven.serie,'-',guia_ven.numero) as guia"),
            'adm_contri.razon_social','adm_contri.nro_documento','alm_almacen.descripcion as alm_descripcion',
            'alm_prod.codigo_anexo','alm_prod.codigo','alm_prod.descripcion',
            'tp_ope.descripcion as ope_descripcion','adm_estado_doc.estado_doc')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','mov_alm_det.id_guia_ven_det')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_ven.id_operacion')
            ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->whereIn('guia_ven.id_tp_doc_almacen',$doc_array)
            ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
            ->get();
        }

        return response()->json($data);
    }
    public function listar_transportistas_com(){
        $data = DB::table('almacen.guia_com')->distinct()
        ->select('guia_com.transportista','adm_contri.id_contribuyente','adm_contri.razon_social','adm_contri.nro_documento')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.transportista')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where('guia_com.estado','<>',7)
        ->groupBy('guia_com.transportista','adm_contri.id_contribuyente','adm_contri.razon_social','adm_contri.nro_documento')
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_transportistas_ven(){
        $data = DB::table('almacen.guia_ven')->distinct()
        ->select('guia_ven.transportista','adm_contri.id_contribuyente','adm_contri.razon_social','adm_contri.nro_documento')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_ven.transportista')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where('guia_ven.estado','<>',7)
        ->groupBy('guia_ven.transportista','adm_contri.id_contribuyente','adm_contri.razon_social','adm_contri.nro_documento')
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function guardar_prorrateo(Request $request){
        $id_usuario = Auth::user()->id_usuario;
        $id_doc_com = DB::table('almacen.doc_com')->insertGetId(
            [
                'serie' => $request->pro_serie,
                'numero' => $request->pro_numero,
                'id_tp_doc' => $request->id_tp_documento,
                'id_proveedor' => $request->doc_id_proveedor,
                'moneda' => $request->id_moneda,
                'fecha_emision' => $request->doc_fecha_emision,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => $request->sub_total,
                'total_descuento' => 0,
                'total' => $request->sub_total,
                'total_igv' => 0,
                'total_a_pagar' => $request->sub_total,
                'usuario' => $id_usuario,
                'registrado_por' => $id_usuario,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_doc_com'
            );

        $data = DB::table('almacen.guia_com_prorrateo')->insertGetId(
            [
                'id_guia_com' => $request->id_guia,
                'id_tp_prorrateo' => $request->id_tp_prorrateo,
                'id_doc_com' => $id_doc_com,
                'tipo' => 1,//calculo global
                'importe' => $request->importe,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_prorrateo'
            );
            
        return response()->json($data);
    }
    public function listar_docs_prorrateo($id){
        $data = DB::table('almacen.guia_com_prorrateo')
            ->select('guia_com_prorrateo.*','doc_com.serie','doc_com.numero',
            'tp_prorrateo.descripcion as des_tp_prorrateo','sis_moneda.simbolo',
            'doc_com.sub_total','doc_com.fecha_emision','doc_com.tipo_cambio')
            ->join('almacen.doc_com','doc_com.id_doc_com','=','guia_com_prorrateo.id_doc_com')
            ->join('almacen.tp_prorrateo','tp_prorrateo.id_tp_prorrateo','=','guia_com_prorrateo.id_tp_prorrateo')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
            ->where('guia_com_prorrateo.id_guia_com',$id)
            ->get();
        $i = 1;
        $html = '';
        $total_comp = 0;
        $total_items = 0;
        $color = '';

        foreach($data as $d){
            if ($d->tipo == 1){
                $total_comp += floatval($d->importe);
                $color = 'orange';
            } else if ($d->tipo == 2){
                $total_items += floatval($d->importe);
                $color = 'purple';
            }
            $html .= '
            <tr id="det-'.$d->id_prorrateo.'">
                <td>'.$i.'</td>
                <td>'.$d->des_tp_prorrateo.'</td>
                <td>'.$d->serie.'-'.$d->numero.'</td>
                <td>'.$d->fecha_emision.'</td>
                <td>'.$d->simbolo.'</td>
                <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="subtotal" onChange="calcula_importe('.$d->id_prorrateo.');" value="'.$d->sub_total.'" disabled="true"/></td>
                <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="tipocambio" onChange="calcula_importe('.$d->id_prorrateo.');" value="'.$d->tipo_cambio.'" disabled="true"/></td>
                <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="importedet" value="'.$d->importe.'" disabled="true"/></td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar" onClick="editar_adicional('.$d->id_prorrateo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar" onClick="update_adicional('.$d->id_prorrateo.','.$d->id_doc_com.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular" onClick="anular_adicional('.$d->id_prorrateo.','.$d->id_doc_com.');"></i>
                    <i class="fas fa-list-alt icon-tabla '.$color.' boton" data-toggle="tooltip" data-placement="bottom" title="Aplicar Prorrateo por Items" onClick="prorrateo_items('.$d->id_prorrateo.','.$d->importe.');"></i>
                </td>
            </tr>
            ';
            $i++;
        }
        $moneda = DB::table('almacen.guia_com_oc')
        ->select('sis_moneda.simbolo','sis_moneda.descripcion')
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
        ->where('id_guia_com',$id)
        ->first();
        return json_encode(['html'=>$html,
                            'total_comp'=>round($total_comp,3,PHP_ROUND_HALF_UP),
                            'total_items'=>round($total_items,3,PHP_ROUND_HALF_UP),
                            'moneda'=>$moneda]);
    }

    public function listar_documentos_prorrateo(){
        $data = DB::table('almacen.guia_com_prorrateo')
            ->select('guia_com_prorrateo.id_prorrateo','tp_prorrateo.descripcion as des_tp_prorrateo',
            'doc_com.*','sis_moneda.simbolo','tp_doc_almacen.abreviatura as tp_doc_guia',
            'guia_com.serie as serie_guia','guia_com.numero as numero_guia',
            'adm_contri.nro_documento','adm_contri.razon_social','cont_tp_doc.descripcion as tp_doc_descripcion')
            ->join('almacen.doc_com','doc_com.id_doc_com','=','guia_com_prorrateo.id_doc_com')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->leftJoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
            ->join('almacen.tp_prorrateo','tp_prorrateo.id_tp_prorrateo','=','guia_com_prorrateo.id_tp_prorrateo')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_prorrateo.id_guia_com')
            ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            // ->where('guia_com_prorrateo.id_guia_com',$id)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_guia_detalle_prorrateo($id, $total_comp){
        $data = DB::table('almacen.guia_com_det')
        ->select('guia_com_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_und_medida.abreviatura','log_ord_compra.codigo AS cod_orden')
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_com_det.id_posicion')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->leftjoin('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
        ->where([['guia_com_det.id_guia_com', '=', $id],
                 ['guia_com_det.estado','=',1]])
            ->get();
        $html = '';
        $suma_total = 0;
        $suma_adicional = 0;
        $suma_costo = 0;

        foreach($data as $det){
            $suma_total += floatval($det->cantidad * $det->unitario);
        }

        $valor = $total_comp / ($suma_total !== 0 ? $suma_total : 1);

        foreach($data as $det){
            $id_guia_det = $det->id_guia_com_det;
            $oc = $det->cod_orden;
            $codigo = $det->codigo;
            $descripcion = $det->descripcion;
            $cantidad = $det->cantidad;
            $abrev = $det->abreviatura;
            $id_posicion = $det->id_posicion;
            $unitario = $det->unitario;
            $total = floatval($det->cantidad * $det->unitario);

            $adic = DB::table('almacen.guia_com_prorrateo_det')
            ->select(DB::raw('sum(guia_com_prorrateo_det.importe) as importe_adicional'))
            ->where('id_guia_com_det',$id_guia_det)
            ->first();

            $adicional = round(($valor * $total),4,PHP_ROUND_HALF_UP) + round($adic->importe_adicional,4,PHP_ROUND_HALF_UP);
            $costo_total = $total + $adicional;

            $suma_adicional += $adicional;
            $suma_costo += $costo_total;

            $unit = round(($costo_total/$cantidad),4,PHP_ROUND_HALF_UP);

            $html .= 
            '<tr id="det-'.$id_guia_det.'">
                <td>'.$oc.'</td>
                <td>'.$codigo.'</td>
                <td>'.$descripcion.'</td>
                <td style="text-align:right">'.$cantidad.'</td>
                <td>'.$abrev.'</td>
                <td style="text-align:right">'.$total.'</td>
                <td style="text-align:right">'.$adicional.'</td>
                <td style="text-align:right"><input type="text" class="oculto" name="unit" value="'.$unit.'"/>'.$costo_total.'</td>
            </tr>';
        }
        $sumas[] = [
            'suma_total'=>round($suma_total,2,PHP_ROUND_HALF_UP),
            'suma_adicional'=>round($suma_adicional,2,PHP_ROUND_HALF_UP),
            'suma_costo'=>round($suma_costo,2,PHP_ROUND_HALF_UP),
        ];
        return json_encode(['html'=>$html,'sumas'=>$sumas]);
    }
    public function update_doc_prorrateo(Request $request){
        $prorrateo = DB::table('almacen.guia_com_prorrateo')
        ->where('id_prorrateo',$request->id_prorrateo)
        ->update(['importe'=>$request->importe]);

        $doc = DB::table('almacen.doc_com')
        ->where('id_doc_com',$request->id_doc)
        ->update([ 'tipo_cambio'=>$request->tipo_cambio,
                   'sub_total'=>$request->sub_total ]);

        return response()->json($prorrateo);
    }
    public function eliminar_doc_prorrateo($id_prorrateo, $id_doc){
        $data = DB::table('almacen.guia_com_prorrateo')
        ->where('id_prorrateo',$id_prorrateo)
        ->delete();

        $detalle = DB::table('almacen.doc_com_det')->where('id_doc',$id_doc)->get();
        
        if (isset($detalle)){
            DB::table('almacen.doc_com')->where('id_doc_com',$id_doc)
            ->delete();
            DB::table('almacen.guia_com_prorrateo_det')->where('id_prorrateo',$id_prorrateo)
            ->delete();
        }
        
        return response()->json($data);
    }
    public function mostrar_guia_detalle($id,$id_prorrateo){
        $data = DB::table('almacen.guia_com_det')
        ->select('guia_com_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_und_medida.abreviatura','alm_prod.series','log_ord_compra.codigo AS cod_orden')
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_com_det.id_posicion')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->leftjoin('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
        ->where([['guia_com_det.id_guia_com', '=', $id],
                 ['guia_com_det.estado','=',1]])
        ->get();

        $html = '';
        foreach($data as $det){
            $pro = DB::table('almacen.guia_com_prorrateo_det')
            ->where([['id_guia_com_det','=',$det->id_guia_com_det],
                     ['id_prorrateo','=',$id_prorrateo]])
            ->first();
            
            $importe = 0;
            $chk = '';

            if (isset($pro)){
                $chk = 'checked';
                $importe = $pro->importe;
            }
            
            $html.='
            <tr id="'.$det->id_guia_com_det.'">
                <td><input type="checkbox" '.$chk.'/></td>
                <td>'.$det->codigo.'</td>
                <td>'.$det->descripcion.'</td>
                <td>'.$det->cantidad.'</td>
                <td>'.$det->abreviatura.'</td>
                <td>'.$det->unitario.'</td>
                <td>'.$det->total.'</td>
                <td>'.$importe.'</td>
            </tr>
            ';
        }
        return json_encode($html);
    }
    public function guardar_prorrateo_detalle(Request $request){
        $det = explode(',',$request->id_guia_com_det);
        $total_det = explode(',',$request->total_det);
        $count = count($det);

        $total_comp = floatval($request->importe_comp);
        $suma_total = floatval($request->suma_total);
        $valor = $total_comp / $suma_total;
        $result = [];

        for ($i=0; $i<$count; $i++){
            $id = $det[$i];
            $total = $total_det[$i];
            $adicional = round(($valor * $total),4,PHP_ROUND_HALF_UP);

            $pro_det = DB::table('almacen.guia_com_prorrateo_det')
            ->where([['id_guia_com_det','=',$id],['id_prorrateo','=',$request->id_prorrateo]])
            ->first();

            if (isset($pro_det)){//si no existe -> agrega
                DB::table('almacen.guia_com_prorrateo_det')
                ->where([['id_guia_com_det','=',$id],['id_prorrateo','=',$request->id_prorrateo]])
                ->update([  'importe'=>$adicional,
                            'fecha_registro'=>date('Y-m-d H:i:s')
                        ]);
            } else {//si existe -> actualiza
                DB::table('almacen.guia_com_prorrateo_det')->insertGetId(
                    [
                        'id_prorrateo'=>$request->id_prorrateo,
                        'id_guia_com_det'=>$id,
                        'importe'=>$adicional,
                        'fecha_registro'=>date('Y-m-d H:i:s')                       
                    ],
                        'id_prorrateo_det'
                    );
            }
        }
        $data = DB::table('almacen.guia_com_prorrateo')
        ->where('id_prorrateo',$request->id_prorrateo)
        ->update([ 'tipo' => 2 ]); //calculo por items

        return response()->json($data);
    }
    /**Update adicional guia detalle */
    public function update_guia_detalle_adic(Request $request){
        $id = explode(',',$request->id_guia_det);
        $unitario = explode(',',$request->unitario);
        $count = count($id);
        $update = '';

        for ($i=0; $i<$count; $i++){
            $id_guia_det = $id[$i];
            $unit = $unitario[$i];

            //Obtiene guia detalle
            $det = DB::table('almacen.guia_com_det')
            ->where('id_guia_com_det',$id_guia_det)
            ->first();

            //Calcula el nuevo unitario adicional 
            $nuevo = $unit - $det->unitario;
            $nuevo_adic = ($nuevo < 0 ? 0 : $nuevo);
            $total = ($det->unitario + $nuevo_adic) * $det->cantidad;

            //Actualiza el total OJO:no mueve el unitario
            $update = DB::table('almacen.guia_com_det')
            ->where('id_guia_com_det',$id_guia_det)
            ->update(['unitario_adicional'=>$nuevo_adic,
                      'total'=>$total]);

            //Obtiene ingreso detalle
            $ing = DB::table('almacen.mov_alm_det')
            ->where([['id_guia_com_det','=',$id_guia_det],['estado','!=',7]])
            ->first();

            //Actualiza valorizacion 
            if (isset($ing)){
                $valor = ($det->unitario + $nuevo_adic) * $ing->cantidad;
                $update = DB::table('almacen.mov_alm_det')
                ->where('id_guia_com_det',$id_guia_det)
                ->update([ 'valorizacion' => $valor ]);
            }
        }
        return response()->json($update);
    }
    public function tipo_cambio_compra($fecha){
        $data = DB::table('contabilidad.cont_tp_cambio')
        ->where('cont_tp_cambio.fecha','<=',$fecha)
        ->orderBy('fecha','desc')
        // ->take(1)->get();
        ->first();
        return $data->compra;
    }
    // public function actualiza_totales_doc($por_dscto, $id_doc, $fecha_emision){
    //     $detalle = DB::table('almacen.doc_com_det')
    //     ->select(DB::raw('sum(doc_com_det.precio_total) as sub_total'))
    //     ->where([['id_doc','=',$id_doc],['estado','=',1]])
    //     ->first();

    //     //obtiene IGV
    //     $impuesto = DB::table('contabilidad.cont_impuesto')
    //         ->where([['codigo','=','IGV'],['fecha_inicio','<',$fecha_emision]])
    //         ->orderBy('fecha_inicio','desc')
    //         ->first();

    //     $dscto = $por_dscto * $detalle->sub_total / 100;
    //     $total = $detalle->sub_total - $dscto;
    //     $igv = $impuesto->porcentaje * $total / 100;

    //     //actualiza totales
    //     $data = DB::table('almacen.doc_com')->where('id_doc_com',$id_doc)
    //     ->update([
    //         'sub_total'=>$detalle->sub_total,
    //         'total_descuento'=>$dscto,
    //         'porcen_descuento'=>$por_dscto,
    //         'total'=>$total,
    //         'total_igv'=>$igv,
    //         'total_ant_igv'=>0,
    //         'porcen_igv' => $impuesto->porcentaje,
    //         'porcen_anticipo' => 0,
    //         'total_otros' => 0,
    //         'total_a_pagar'=>($total + $igv)
    //     ]);
    //     return response()->json($data);
    // }
    public function transferencia_nextId($id_alm_origen){
        $cantidad = DB::table('almacen.trans')
        ->where([['id_almacen_origen','=',$id_alm_origen],
                ['estado','!=',7]])
        ->get()->count();
        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "Tr-".$id_alm_origen."-".$val;
        return $nextId;
    }
    public function guardar_transferencia(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->transferencia_nextId($request->id_almacen_origen);
        $id_usuario = Auth::user()->id_usuario;
        $guardar = false;

        if ($request->id_guia_ven !== null){
            $trans = DB::table('almacen.trans')
            ->where([['id_guia_ven','=',$request->id_guia_ven],['estado','!=',7]])
            ->first();
            if (isset($trans)){
                $id_trans = $trans->id_transferencia;
            } else {
                $guardar = true;
            }
        } else {
            $guardar = true;
        }
        if ($guardar){
            $id_trans = DB::table('almacen.trans')->insertGetId(
                [
                    'id_almacen_origen' => $request->id_almacen_origen,
                    'id_almacen_destino' => $request->id_almacen_destino,
                    'codigo' => $codigo,
                    'id_guia_ven' => $request->id_guia_ven,
                    'responsable_origen' => $request->responsable_origen,
                    'responsable_destino' => $request->responsable_destino,
                    'fecha_transferencia' => $request->fecha_transferencia,
                    'registrado_por' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_transferencia'
                );
        }
        return response()->json(['id_trans'=>$id_trans,'codigo'=>$codigo]);
    }
    public function anular_transferencia($id_transferencia){
        $data = DB::table('almacen.trans')
            ->where('id_transferencia',$id_transferencia)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function listar_transferencias_pendientes($ori){
        $data = DB::table('almacen.trans')
        ->select('trans.*','guia_ven.fecha_emision as fecha_guia',
        DB::raw("CONCAT(guia_ven.serie,'-',guia_ven.numero) as guia_ven"),
        DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia_com"),
        'alm_origen.descripcion as alm_origen_descripcion',
        'alm_destino.descripcion as alm_destino_descripcion',
        'usu_origen.nombre_corto as nombre_origen',
        'usu_destino.nombre_corto as nombre_destino',
        'usu_registro.nombre_corto as nombre_registro',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
        ->leftJoin('almacen.guia_ven','guia_ven.id_guia_ven','=','trans.id_guia_ven')
        ->leftJoin('almacen.guia_com','guia_com.id_guia','=','trans.id_guia_com')
        ->join('almacen.alm_almacen as alm_origen','alm_origen.id_almacen','=','trans.id_almacen_origen')
        ->leftJoin('almacen.alm_almacen as alm_destino','alm_destino.id_almacen','=','trans.id_almacen_destino')
        ->leftJoin('configuracion.sis_usua as usu_origen','usu_origen.id_usuario','=','trans.responsable_origen')
        ->leftJoin('configuracion.sis_usua as usu_destino','usu_destino.id_usuario','=','trans.responsable_destino')
        ->join('configuracion.sis_usua as usu_registro','usu_registro.id_usuario','=','trans.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','trans.estado')
        ->where([['trans.id_almacen_origen','=',$ori]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function prueba_($id_transferencia){
        $detalle = DB::table('almacen.guia_ven_det')
        ->select('guia_ven_det.*','alm_prod.codigo','alm_prod.descripcion','alm_und_medida.abreviatura')
        ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
        ->join('almacen.trans','trans.id_guia_ven','=','guia_ven.id_guia_ven')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_ven_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where('trans.id_transferencia',$id_transferencia)
        ->get();

        $array = [];

        foreach($detalle as $d){
            $guia_com_det = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*')
            ->where([['guia_com_det.id_guia_ven_det','=',$d->id_guia_ven_det], 
                    ['guia_com_det.estado','=',1]])
            ->first();

            $agrega = false;
            $nueva_cant = $d->cantidad;

            if ($guia_com_det !== null && $guia_com_det->cantidad !== null){
                if ($guia_com_det->cantidad < $d->cantidad){
                    $agrega = true;
                    $nueva_cant = $d->cantidad - $guia_com_det->cantidad;
                } else {
                    $agrega = false;
                }
            } else {
                $agrega = true;
            }
            array_push($array,$guia_com_det);

        }
        return response()->json(['array'=>$array,'detalle'=>$detalle]);
    }
    public function listar_transferencia_detalle($id_transferencia){
        $detalle = DB::table('almacen.guia_ven_det')
        ->select('guia_ven_det.*','alm_prod.codigo','alm_prod.descripcion','alm_und_medida.abreviatura')
        ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
        ->join('almacen.trans','trans.id_guia_ven','=','guia_ven.id_guia_ven')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_ven_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['trans.id_transferencia','=',$id_transferencia],
                ['guia_ven_det.estado','!=',7]])
        ->get();
        
        $trans = DB::table('almacen.trans')
        ->where('id_transferencia',$id_transferencia)
        ->first();

        //listar posiciones que no estan enlazadas con ningun producto
        $posiciones = DB::table('almacen.alm_ubi_posicion')
        ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
        ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_posicion','=','alm_ubi_posicion.id_posicion')
        ->leftjoin('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->leftjoin('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['alm_prod_ubi.id_posicion','=',null],
                ['alm_ubi_posicion.estado','=',1],
                ['alm_almacen.id_almacen','=',$trans->id_almacen_destino]])
        ->get();

        $html = '';
        foreach($detalle as $d){
            $o = false;
            //jalar posicion relacionada con el producto
            $posicion = DB::table('almacen.alm_prod_ubi')
            ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            // ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.id_producto','=',$d->id_producto],
                    ['alm_prod_ubi.estado','=',1],
                    ['alm_ubi_estante.id_almacen','=',$trans->id_almacen_destino]])
            ->get();
            $count = count($posicion);
            if ($count > 0){
                $posiciones = $posicion;
                $o = true;
            }
            $guia_com_det = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*')
            ->where([['guia_com_det.id_guia_ven_det','=',$d->id_guia_ven_det], 
                    ['guia_com_det.estado','=',1]])
            ->first();

            $nueva_cant = $d->cantidad;

            if ($guia_com_det !== null && $guia_com_det->cantidad !== null){
                $nueva_cant = $d->cantidad - $guia_com_det->cantidad;
            }

            // if ($agrega){

                $html.='
                <tr id="'.$d->id_guia_ven_det.'">
                    <td><input type="checkbox" checked change="onCheck('.$d->id_guia_ven_det.');"/></td>
                    <td>'.$d->codigo.'</td>
                    <td>'.$d->descripcion.'</td>
                    <td>'.$d->cantidad.'</td>
                    <td><input type="number" class="input-data right" style="width:80px;" name="cantidad_recibida" value="'.$nueva_cant.'" max="'.$nueva_cant.'"/></td>
                    <td>'.$d->abreviatura.'</td>
                    <td>
                        <select class="input-data" name="id_posicion">
                            <option value="0">Elija una opción</option>';
                            foreach ($posiciones as $row) {
                                if ($o){
                                    $html.='<option value="'.$row->id_posicion.'" selected>'.$row->codigo.'</option>';
                                } else {
                                    $html.='<option value="'.$row->id_posicion.'">'.$row->codigo.'</option>';
                                }
                            }
                        $html.='</select>
                    </td>
                    <td><input type="text" class="input-data" name="observacion"/></td>
                </tr>
                ';
            // }
        }
        return json_encode($html);
        // return response()->json($detalle);
    }
    public function proveedor($id_ing_det){
        $guia_com_det = DB::table('almacen.guia_ven_det')
        ->select('guia_com_det.*')
        ->leftjoin('almacen.mov_alm_det','mov_alm_det.id_mov_alm_det','=','guia_ven_det.id_ing_det')
        ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
        ->where('guia_ven_det.id_ing_det',$id_ing_det)
        ->first();
        $est = 'no agrega';
        if ($guia_com_det !== null){
            if ($guia_com_det->cantidad < 2){
                $est = 'agrega';
            }
        }
        return response()->json(['guia_com_det'=>$guia_com_det,'est'=>$est]);
    }
    public function guardar_ingreso_transferencia(Request $request){
        $usuario = Auth::user();
        $fecha = date('Y-m-d H:i:s');

        $guia_ven = DB::table('almacen.guia_ven')
        ->where('id_guia_ven',$request->id_guia_ven)
        ->first();

        $id_proveedor = null;

        if ($guia_ven->id_cliente !== null){
            //verifica si el cliente existe como proveedor
            $proveedor = DB::table('comercial.com_cliente')
            ->select('com_cliente.id_contribuyente','log_prove.id_proveedor')
            ->leftjoin('logistica.log_prove','log_prove.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('com_cliente.id_cliente',$guia_ven->id_cliente)
            ->first();

            //si existe, copia el id_proveedor
            if ($proveedor !== null && $proveedor->id_proveedor !== null){
                $id_proveedor = $proveedor->id_proveedor;
            }
            else { //si no existe, inserta uno
                if ($proveedor !== null && 
                    $proveedor->id_contribuyente !== null){
                    $id_proveedor = DB::table('logistica.log_prove')->insertGetId([
                        'id_contribuyente'=>$proveedor->id_contribuyente,
                        'estado'=>1,
                        'fecha_registro'=>$fecha,
                    ], 
                        'id_proveedor'
                    );
                } //si no existe proveedor, id_empresa
                else {
                    $empresa = DB::table('administracion.adm_empresa')
                    ->select('adm_empresa.id_contribuyente','log_prove.id_proveedor')
                    ->leftjoin('logistica.log_prove','log_prove.id_contribuyente','=','adm_empresa.id_contribuyente')
                    ->where('adm_empresa.id_empresa',$guia_ven->id_empresa)
                    ->first();
                
                    if ($empresa !== null && $empresa->id_proveedor !== null){
                        $id_proveedor = $empresa->id_proveedor;
                    } else {
                        $id_proveedor = DB::table('logistica.log_prove')->insertGetId([
                            'id_contribuyente'=>$empresa->id_contribuyente,
                            'estado'=>1,
                            'fecha_registro'=>$fecha,
                        ], 
                            'id_proveedor'
                        );
                    }    
                }
            }
        } 
        else {
            $empresa = DB::table('administracion.sis_sede')
            ->select('log_prove.id_proveedor')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('logistica.log_prove','log_prove.id_contribuyente','=','adm_empresa.id_contribuyente')
            // ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where('sis_sede.id_sede',$guia_ven->id_sede)
            ->first();

            if (isset($empresa)){
                $id_proveedor = $empresa->id_proveedor;
            }
        }

        $id_guia_com = DB::table('almacen.guia_com')->insertGetId(
            [
                'id_tp_doc_almacen' => 1,//Guia Compra
                'serie' => $guia_ven->serie,
                'numero' => $guia_ven->numero,
                'id_proveedor' => $id_proveedor,
                'fecha_emision' => $guia_ven->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen_destino,
                // 'id_motivo' => $guia_ven->id_motivo,
                'id_guia_clas' => 1,
                'id_operacion' => 21,//entrada por transferencia entre almacenes
                'punto_partida' => $guia_ven->punto_partida,
                'punto_llegada' => $guia_ven->punto_llegada,
                'transportista' => $guia_ven->transportista,
                'fecha_traslado' => $guia_ven->fecha_traslado,
                'tra_serie' => $guia_ven->tra_serie,
                'tra_numero' => $guia_ven->tra_numero,
                'placa' => $guia_ven->placa,
                'usuario' => $request->responsable_destino,
                'registrado_por' => $usuario->id_usuario,
                'estado' => 9,
                'fecha_registro' => $fecha
            ],
                'id_guia'
            );

        $codigo = AlmacenController::nextMovimiento(1,
            $request->fecha_almacen,
            $request->id_almacen_destino);

        $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
            [
                'id_almacen' => $request->id_almacen_destino,
                'id_tp_mov' => 1,//Ingresos
                'codigo' => $codigo,
                'fecha_emision' => $request->fecha_almacen,
                'id_guia_com' => $id_guia_com,
                'id_operacion' => 21,//entrada por transferencia entre almacenes
                'revisado' => 0,
                'usuario' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_mov_alm'
            );

        $guia_det = explode(',',$request->id_guia_ven_det);
        $cant_recibida = explode(',',$request->cantidad_recibida);
        $observacion = explode(',',$request->observacion);
        $ubicaciones = explode(',',$request->ubicaciones);
        $count = count($guia_det);

        if (!empty($request->id_guia_ven_det)){
            for($i=0; $i<$count; $i++){
                
                $det = DB::table('almacen.guia_ven_det')
                ->where('id_guia_ven_det',$guia_det[$i])
                ->first();
        
                $id_guia_com_det = DB::table('almacen.guia_com_det')->insertGetId([
                    'id_guia_com' => $id_guia_com,
                    'id_producto' => $det->id_producto,
                    'id_posicion' => $ubicaciones[$i],
                    'cantidad' => $cant_recibida[$i],
                    'id_unid_med' => $det->id_unid_med,
                    'id_guia_ven_det' => $guia_det[$i],
                    // 'unitario' => $det->unitario,
                    // 'unitario_adicional' => 0,
                    // 'total' => $det->total,
                    'usuario' => $usuario->id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_guia_com_det'
                );

                if ($ubicaciones[$i] !== null){
                    
                    $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([['id_producto','=',$det->id_producto],
                                ['id_posicion','=',$ubicaciones[$i]]])
                        ->first();
                    //traer stockActual
                    $saldo = $this->saldo_actual($det->id_producto, $ubicaciones[$i]);
                    $costo = $this->costo_promedio($det->id_producto, $ubicaciones[$i]);
    
                    if (!isset($ubi->id_posicion)){//si no existe -> creo la ubicacion
                        DB::table('almacen.alm_prod_ubi')->insert([
                            'id_producto' => $det->id_producto,
                            'id_posicion' => $ubicaciones[$i],
                            'stock' => $saldo,
                            'costo_promedio' => $costo,
                            'estado' => 1,
                            'fecha_registro' => $fecha
                            ]);
                    } else {
                        DB::table('almacen.alm_prod_ubi')
                        ->where('id_prod_ubi',$ubi->id_prod_ubi)
                        ->update([  'stock' => $saldo,
                                    'costo_promedio' => $costo ]);
                    }
                }

                if ($observacion[$i] !== ''){
                    DB::table('almacen.guia_com_det_obs')->insertGetId([
                        'id_guia_com_det' => $id_guia_com_det,
                        'observacion' => $observacion[$i],
                        'registrado_por' => $usuario->id_usuario,
                        'fecha_registro' => $fecha,
                    ],
                        'id_obs'
                    );
                }
                //guarda ingreso detalle
                DB::table('almacen.mov_alm_det')->insertGetId([
                    'id_mov_alm' => $id_ingreso,
                    'id_producto' => $det->id_producto,
                    'id_posicion' => $ubicaciones[$i],
                    'cantidad' => $cant_recibida[$i],
                    // 'valorizacion' => (floatval($det->cantidad) * floatval($prec)),
                    'usuario' => $usuario->id_usuario,
                    'id_guia_com_det' => $id_guia_com_det,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm_det'
                );
            }
        }

        // if ($request->estado == 'true'){
        //     $est = 5;//Atendido
        // } else {
        //     $est = 14;//Atención Parcial
        // }
        DB::table('almacen.trans')
        ->where('id_transferencia',$request->id_transferencia)
        ->update(['estado' => 14,//Recibido
                  'id_guia_com' => $id_guia_com
                ]);

        return response()->json($id_ingreso);
    }
    public function ingreso_transferencia($id_guia_com){
        $data = DB::table('almacen.mov_alm')
        ->where('id_guia_com',$id_guia_com)->get();
        return response()->json($data);
    }
    // public function listar_transformaciones(){
    //     $data = DB::table('almacen.transformacion')
    //     ->select('transformacion.*','adm_contri.razon_social','alm_almacen.descripcion')
    //     ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','transformacion.id_empresa')
    //     ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
    //     ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','transformacion.id_almacen')
    //     ->where([['transformacion.estado','!=',7]])
    //     ->get();
    //     $output['data'] = $data;
    //     return response()->json($output);
    // }
    public function mostrar_transformacion($id_transformacion){
        $data = DB::table('almacen.transformacion')
        ->select('transformacion.*','adm_estado_doc.estado_doc','sis_usua.nombre_corto')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','transformacion.estado')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','transformacion.registrado_por')
        ->where('id_transformacion',$id_transformacion)
        ->get();
        return response()->json($data);
    }
    public function transformacion_nextId($fecha, $id_empresa){
        $yyyy = date('Y',strtotime($fecha));
        
        $empresa = DB::table('administracion.adm_empresa')
        ->where('id_empresa',$id_empresa)
        ->first();

        $cantidad = DB::table('almacen.transformacion')
        ->where([['id_empresa','=',$id_empresa],['estado','!=',7]])
        ->whereYear('fecha_transformacion','=',$yyyy)
        ->get()->count();
        
        $val = AlmacenController::leftZero(3,($cantidad + 1));
        $nextId = "HT-".$empresa->codigo."-".$val;
        
        return $nextId;
    }
    public function guardar_transformacion(Request $request)
    {
        $usuario = Auth::user();
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->transformacion_nextId($request->fecha_transformacion, $request->id_empresa);
        $id_transformacion = DB::table('almacen.transformacion')->insertGetId(
            [
                'fecha_transformacion' => $request->fecha_transformacion,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'codigo' => $codigo,
                'responsable' => $request->responsable,
                'id_empresa' => $request->id_empresa,
                'id_almacen' => $request->id_almacen,
                'total_materias' => $request->total_materias,
                'total_directos' => $request->total_directos,
                'costo_primo' => $request->costo_primo,
                'total_indirectos' => $request->total_indirectos,
                'total_sobrantes' => $request->total_sobrantes,
                'costo_transformacion' => $request->costo_transformacion,
                'registrado_por' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_transformacion'
            );
        return response()->json($id_transformacion);
    }
    public function update_transformacion(Request $request)
    {
        $data = DB::table('almacen.transformacion')
            ->where('id_transformacion', $request->id_transformacion)
            ->update([
                'fecha_transformacion' => $request->fecha_transformacion,
                'serie' => $request->serie,
                'numero' => $request->numero,
                // 'codigo' => $request->codigo,
                'responsable' => $request->responsable,
                'id_empresa' => $request->id_empresa,
                'id_almacen' => $request->id_almacen,
                'total_materias' => $request->total_materias,
                'total_directos' => $request->total_directos,
                'costo_primo' => $request->costo_primo,
                'total_indirectos' => $request->total_indirectos,
                'total_sobrantes' => $request->total_sobrantes,
                'costo_transformacion' => $request->costo_transformacion
            ]);
        return response()->json($id_transformacion);
    }
    public function guardar_materia(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_materia = DB::table('almacen.transfor_materia')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'id_producto' => $request->id_producto,
                'cantidad' => $request->cantidad,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,2,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_materia'
            );
        return response()->json($id_materia);
    }
    public function update_materia(Request $request)
    {
        $data = DB::table('almacen.transfor_materia')
        ->where('id_materia',$request->id_materia)
        ->update([  'cantidad' => $request->cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }
    public function listar_materias($id_transformacion){
        $data = DB::table('almacen.transfor_materia')
        ->select('transfor_materia.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_und_medida.abreviatura','alm_prod.series')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','transfor_materia.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['transfor_materia.id_transformacion','=',$id_transformacion],
                 ['transfor_materia.estado','=',1]])
        ->get();

        $html = '';
        $i = 1;
        foreach ($data as $d){
            $html.='
            <tr id="mat-'.$d->id_materia.'">
                <td>'.$i.'</td>
                <td>'.$d->codigo.'</td>
                <td>'.$d->descripcion.'</td>
                <td><input type="number" class="input-data right" name="mat_cantidad" value="'.$d->cantidad.'" onChange="calcula_materia('.$d->id_materia.');" disabled="true"/></td>
                <td>'.$d->abreviatura.'</td>
                <td><input type="number" class="input-data right" name="mat_valor_unitario" value="'.$d->valor_unitario.'" onChange="calcula_materia('.$d->id_materia.');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="mat_valor_total" value="'.round($d->valor_total,2,PHP_ROUND_HALF_UP).'" onChange="calcula_materia('.$d->id_materia.');" disabled="true"/></td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_materia('.$d->id_materia.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_materia('.$d->id_materia.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_materia('.$d->id_materia.');"></i>
                </td>
            </tr>
            ';
            $i++;
        }
        return json_encode($html);
    }
    public function anular_materia(Request $request, $id)
    {
        $data = DB::table('almacen.transfor_materia')->where('id_materia', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function guardar_directo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_directo = DB::table('almacen.transfor_directo')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'id_servicio' => $request->id_servicio,
                'cantidad' => $request->cantidad,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,2,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_directo'
            );
        return response()->json($id_directo);
    }
    public function update_directo(Request $request)
    {
        $data = DB::table('almacen.transfor_directo')
        ->where('id_directo',$request->id_directo)
        ->update([  'cantidad' => $request->cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }
    public function listar_directos($id_transformacion){
        $data = DB::table('almacen.transfor_directo')
        ->select('transfor_directo.*','log_servi.codigo','log_servi.descripcion')
        ->join('logistica.log_servi','log_servi.id_servicio','=','transfor_directo.id_servicio')
        // ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['transfor_directo.id_transformacion','=',$id_transformacion],
                 ['transfor_directo.estado','=',1]])
        ->get();

        $html = '';
        $i = 1;
        foreach ($data as $d){
            $html.='
            <tr id="dir-'.$d->id_directo.'">
                <td>'.$i.'</td>
                <td>'.$d->codigo.'</td>
                <td>'.$d->descripcion.'</td>
                <td><input type="number" class="input-data right" name="dir_cantidad" value="'.$d->cantidad.'" onChange="calcula_directo('.$d->id_directo.');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="dir_valor_unitario" value="'.$d->valor_unitario.'" onChange="calcula_directo('.$d->id_directo.');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="dir_valor_total" value="'.round($d->valor_total,2,PHP_ROUND_HALF_UP).'" onChange="calcula_directo('.$d->id_directo.');" disabled="true"/></td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_directo('.$d->id_directo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_directo('.$d->id_directo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_directo('.$d->id_directo.');"></i>
                </td>
            </tr>
            ';
            $i++;
        }
        return json_encode($html);
    }
    public function anular_directo(Request $request, $id)
    {
        $data = DB::table('almacen.transfor_directo')->where('id_directo', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function guardar_indirecto(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_indirecto = DB::table('almacen.transfor_indirecto')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'cod_item' => $request->cod_item,
                'tasa' => $request->tasa,
                'parametro' => $request->parametro,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,2,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_indirecto'
            );
        return response()->json($id_indirecto);
    }
    public function update_indirecto(Request $request)
    {
        $data = DB::table('almacen.transfor_indirecto')
        ->where('id_indirecto',$request->id_indirecto)
        ->update([  'tasa' => $request->tasa,
                    'parametro' => $request->parametro,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }
    public function listar_indirectos($id_transformacion){
        $data = DB::table('almacen.transfor_indirecto')
        ->select('transfor_indirecto.*','log_servi.codigo','log_servi.descripcion')
        ->join('logistica.log_servi','log_servi.id_servicio','=','transfor_indirecto.cod_item')
        ->where([['transfor_indirecto.id_transformacion','=',$id_transformacion],
                 ['transfor_indirecto.estado','=',1]])
        ->get();

        $html = '';
        $i = 1;
        foreach ($data as $d){
            $html.='
            <tr id="ind-'.$d->id_indirecto.'">
                <td>'.$i.'</td>
                <td>'.$d->codigo.'</td>
                <td>'.$d->descripcion.'</td>
                <td><input type="number" class="input-data right" name="ind_tasa" value="'.$d->tasa.'" onChange="calcula_total('.$d->id_indirecto.');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="ind_parametro" value="'.$d->parametro.'" onChange="calcula_total('.$d->id_indirecto.');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="ind_valor_unitario" value="'.$d->valor_unitario.'" onChange="calcula_total('.$d->id_indirecto.');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="ind_valor_total" value="'.round($d->valor_total,2,PHP_ROUND_HALF_UP).'" onChange="calcula_total('.$d->id_indirecto.');" disabled="true"/></td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_indirecto('.$d->id_indirecto.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_indirecto('.$d->id_indirecto.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_indirecto('.$d->id_indirecto.');"></i>
                </td>
            </tr>
            ';
            $i++;
        }
        return json_encode($html);
    }
    public function anular_indirecto(Request $request, $id)
    {
        $data = DB::table('almacen.transfor_indirecto')->where('id_indirecto', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function guardar_sobrante(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_sobrante = DB::table('almacen.transfor_sobrante')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'id_producto' => $request->id_producto,
                'cantidad' => $request->cantidad,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,2,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_sobrante'
            );
        return response()->json($id_sobrante);
    }
    public function update_sobrante(Request $request)
    {
        $data = DB::table('almacen.transfor_sobrante')
        ->where('id_sobrante',$request->id_sobrante)
        ->update([  'cantidad' => $request->cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }
    public function listar_sobrantes($id_transformacion){
        $data = DB::table('almacen.transfor_sobrante')
        ->select('transfor_sobrante.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_und_medida.abreviatura','alm_prod.series')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','transfor_sobrante.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['transfor_sobrante.id_transformacion','=',$id_transformacion],
                 ['transfor_sobrante.estado','=',1]])
        ->get();

        $html = '';
        $i = 1;
        foreach ($data as $d){
            $html.='
            <tr id="sob-'.$d->id_sobrante.'">
                <td>'.$i.'</td>
                <td>'.$d->codigo.'</td>
                <td>'.$d->descripcion.'</td>
                <td><input type="number" class="input-data right" name="sob_cantidad" value="'.$d->cantidad.'" onChange="calcula_sobrante('.$d->id_sobrante.');" disabled="true"/></td>
                <td>'.$d->abreviatura.'</td>
                <td><input type="number" class="input-data right" name="sob_valor_unitario" value="'.$d->valor_unitario.'" onChange="calcula_sobrante('.$d->id_sobrante.');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="sob_valor_total" value="'.round($d->valor_total,2,PHP_ROUND_HALF_UP).'" onChange="calcula_sobrante('.$d->id_sobrante.');" disabled="true"/></td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_sobrante('.$d->id_sobrante.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_sobrante('.$d->id_sobrante.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_sobrante('.$d->id_sobrante.');"></i>
                </td>
            </tr>
            ';
            $i++;
        }
        return json_encode($html);
    }
    public function anular_sobrante(Request $request, $id)
    {
        $data = DB::table('almacen.transfor_sobrante')->where('id_sobrante', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function guardar_transformado(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_transformado = DB::table('almacen.transfor_transformado')->insertGetId(
            [
                'id_transformacion' => $request->id_transformacion,
                'id_producto' => $request->id_producto,
                'cantidad' => $request->cantidad,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => round($request->valor_total,2,PHP_ROUND_HALF_UP),
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_transformado'
            );
        return response()->json($id_transformado);
    }
    public function update_transformado(Request $request)
    {
        $data = DB::table('almacen.transfor_transformado')
        ->where('id_transformado',$request->id_transformado)
        ->update([  'cantidad' => $request->cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => $request->valor_total,
                ]);
        return response()->json($data);
    }
    public function listar_transformados($id_transformacion){
        $data = DB::table('almacen.transfor_transformado')
        ->select('transfor_transformado.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_und_medida.abreviatura','alm_prod.series')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','transfor_transformado.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['transfor_transformado.id_transformacion','=',$id_transformacion],
                 ['transfor_transformado.estado','=',1]])
        ->get();

        $html = '';
        $i = 1;
        foreach ($data as $d){
            $html.='
            <tr id="tra-'.$d->id_transformado.'">
                <td>'.$i.'</td>
                <td>'.$d->codigo.'</td>
                <td>'.$d->descripcion.'</td>
                <td><input type="number" class="input-data right" name="tra_cantidad" value="'.$d->cantidad.'" onChange="calcula_transformado('.$d->id_transformado.');" disabled="true"/></td>
                <td>'.$d->abreviatura.'</td>
                <td><input type="number" class="input-data right" name="tra_valor_unitario" value="'.$d->valor_unitario.'" onChange="calcula_transformado('.$d->id_transformado.');" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="tra_valor_total" value="'.round($d->valor_total,2,PHP_ROUND_HALF_UP).'" onChange="calcula_transformado('.$d->id_transformado.');" disabled="true"/></td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_transformado('.$d->id_transformado.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_transformado('.$d->id_transformado.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_transformado('.$d->id_transformado.');"></i>
                </td>
            </tr>
            ';
            $i++;
        }
        return json_encode($html);
    }
    public function anular_transformado(Request $request, $id)
    {
        $data = DB::table('almacen.transfor_transformado')->where('id_transformado', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function procesar_transformacion($id_transformacion){
        $id_usuario = Auth::user()->id_usuario;
        $fecha = date('Y-m-d H:i:s');
        
        $tra = DB::table('almacen.transformacion')
        ->where('id_transformacion',$id_transformacion)
        ->first();
        
        $salida = DB::table('almacen.transfor_materia')
        ->where([['id_transformacion','=',$id_transformacion],['estado','!=',7]])
        ->get();

        $id_salida = 0;
        if (count($salida) > 0){
            $codigo_sal = AlmacenController::nextMovimiento(2,$tra->fecha_transformacion,$tra->id_almacen);
            //guardar salida de almacén
            $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $tra->id_almacen,
                    'id_tp_mov' => 2,//Salidas
                    'codigo' => $codigo_sal,
                    'fecha_emision' => $tra->fecha_transformacion,
                    'id_transformacion' => $id_transformacion,
                    'id_operacion' => 27,//Salida por servicio de producción
                    'revisado' => 0,
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm'
                );
            //guardar detalle de salida de almacén
            foreach($salida as $sal){
                DB::table('almacen.mov_alm_det')->insertGetId(
                    [
                        'id_mov_alm' => $id_salida,
                        'id_producto' => $sal->id_producto,
                        // 'id_posicion' => $sal->id_posicion,
                        'cantidad' => $sal->cantidad,
                        'valorizacion' => $sal->valor_total,
                        'usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                        'id_mov_alm_det'
                    );
            }
        }

        $sob = DB::table('almacen.transfor_sobrante')
        ->select('transfor_sobrante.id_producto','transfor_sobrante.cantidad',
        'transfor_sobrante.valor_unitario','transfor_sobrante.valor_total')
        ->where([['id_transformacion','=',$id_transformacion],['estado','!=',7]]);
        
        $ingreso = DB::table('almacen.transfor_transformado')
        ->select('transfor_transformado.id_producto','transfor_transformado.cantidad',
        'transfor_transformado.valor_unitario','transfor_transformado.valor_total')
        ->where([['id_transformacion','=',$id_transformacion],['estado','!=',7]])
        ->unionAll($sob)
        ->get()
        ->toArray();

        $id_ingreso = 0;
        if (count($ingreso) > 0){
            $codigo_ing = AlmacenController::nextMovimiento(1,$tra->fecha_transformacion,$tra->id_almacen);

            $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $tra->id_almacen,
                    'id_tp_mov' => 1,//Ingresos
                    'codigo' => $codigo_ing,
                    'fecha_emision' => $tra->fecha_transformacion,
                    'id_transformacion' => $id_transformacion,
                    'id_operacion' => 26,//Entrada por servicio de producción
                    'revisado' => 0,
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm'
                );

            foreach($ingreso as $ing){
                DB::table('almacen.mov_alm_det')->insertGetId(
                    [
                        'id_mov_alm' => $id_ingreso,
                        'id_producto' => $ing->id_producto,
                        // 'id_posicion' => $ing->id_posicion,
                        'cantidad' => $ing->cantidad,
                        'valorizacion' => $ing->valor_total,
                        'usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                        'id_mov_alm_det'
                    );
            }
        }
        DB::table('almacen.transformacion')
        ->where('id_transformacion',$id_transformacion)
        ->update(['estado' => 9]);//Procesado

        return response()->json(['id_salida'=>$id_salida,'id_ingreso'=>$id_ingreso]);
    }
    public function anular_transformacion($id_transformacion){
        $rspta = '';
        $ing = DB::table('almacen.mov_alm')
        ->where([['id_transformacion','=',$id_transformacion],
                ['estado','=',1],['id_tp_mov','=',1]])//ingreso
        ->first();

        $sal = DB::table('almacen.mov_alm')
        ->where([['id_transformacion','=',$id_transformacion],
                ['estado','=',1],['id_tp_mov','=',2]])//salida
        ->first();

        $anula_trans = false;
        //Si existe ingreso y salida relacionado
        if (isset($ing) && isset($sal)){
            //Verifica que no esten revisado
            if ($ing->revisado == 0 && $sal->revisado == 0){
                DB::table('almacen.mov_alm')
                ->where('id_transformacion',$id_transformacion)
                ->whereIn('id_mov_alm',[ $ing->id_mov_alm, $sal->id_mov_alm ])
                ->update(['estado' => 7]);

                $det = DB::table('almacen.mov_alm_det')
                ->whereIn('mov_alm_det.id_mov_alm',[ $ing->id_mov_alm, $sal->id_mov_alm ])
                ->get();

                if (isset($det)){
                    foreach($det as $d){
                        DB::table('almacen.mov_alm_det')
                        ->where('id_mov_alm_det',$d->id_mov_alm_det)
                        ->update(['estado' => 7]);
                        $rspta = 'Se anuló correctamente....';
                    }
                }

                $anula_trans = true;
                if ($rspta == ''){
                    $rspta = 'Se anuló correctamente.';
                }
            } 
            else {
                $rspta = 'No es posible anular, su ingreso y/o salida ya fue revisada.';
            }
        }
        else {
            $anula_trans = true;
            $rspta = 'Se anuló correctamente.';
        }
        //anula la transformacion
        if ($anula_trans){
            DB::table('almacen.transformacion')
            ->where('id_transformacion',$id_transformacion)
            ->update(['estado' => 7]);
        }
        return response()->json($rspta);
    }
    public function listar_todas_transformaciones($id_almacen){
        $data = DB::table('almacen.transformacion')
        ->select('transformacion.*','adm_contri.razon_social','alm_almacen.descripcion',
        'respon.nombre_corto as nombre_responsable','regist.nombre_corto as nombre_registrado',
        'adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','transformacion.id_almacen')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('configuracion.sis_usua as respon','respon.id_usuario','=','transformacion.responsable')
        ->join('configuracion.sis_usua as regist','regist.id_usuario','=','transformacion.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','transformacion.estado')
        ->where([['transformacion.id_almacen','=',$id_almacen]])
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_series_numeros(){
        $data = DB::table('almacen.serie_numero')
        ->select('serie_numero.*','sis_usua.nombre_corto','adm_estado_doc.estado_doc',
        DB::raw("CONCAT(adm_contri.razon_social,'-',sis_sede.codigo) as empresa_sede"),
        'cont_tp_doc.descripcion as tipo_doc')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','serie_numero.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','serie_numero.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','serie_numero.estado')
        ->join('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','serie_numero.id_tp_documento')
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_serie_numero($id){
        $data = DB::table('almacen.serie_numero')
        ->select('serie_numero.*','sis_usua.nombre_corto',
        DB::raw("CONCAT(adm_contri.razon_social,'-',sis_sede.codigo)"))
        ->join('administracion.sis_sede','sis_sede.id_sede','=','serie_numero.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','serie_numero.registrado_por')
        ->where('serie_numero.id_serie_numero',$id)
        ->get();
        return response()->json($data);
    }
    public function guardar_serie_numero(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user()->id_usuario;
        $hasWhere = [];
        
        if ($request->numero_desde !== null && $request->numero_hasta !== null){
            $hasWhere[] = [$request->numero_desde, $request->numero_hasta];
        } else {
            $hasWhere[] = [$request->numero, $request->numero];
        }
        
        $count = DB::table('almacen.serie_numero')
        ->where([['id_tp_documento','=',$request->id_tp_documento],
                 ['id_sede','=',$request->id_sede],
                 ['serie','=',$request->serie]])
        ->whereBetween(DB::raw("CAST(numero AS INTEGER)"), $hasWhere)
        ->count();
        
        $rspta = '';
        if ($count == 0){
            for($i=$request->numero_desde; $i<=$request->numero_hasta; $i++){
                $num = AlmacenController::leftZero(7, $i);
                DB::table('almacen.serie_numero')->insertGetId(
                    [
                        'id_tp_documento' => $request->id_tp_documento,
                        'id_sede' => $request->id_sede,
                        'serie' => $request->serie,
                        'numero' => $num,
                        'estado' => 1,
                        'registrado_por' => $usuario,
                        'fecha_registro' => $fecha
                    ],
                        'id_serie_numero'
                    );
                $rspta = 'Se guardó las serie-numero con éxito.';
            }
        } else {
            $rspta = 'Ya existen dichas series!';
        }

        return response()->json($rspta);
    }
    public function update_serie_numero(Request $request)
    {
        $rspta = '';
        $count = DB::table('almacen.serie_numero')
        ->where([['id_tp_documento','=',$request->id_tp_documento],
                 ['id_sede','=',$request->id_sede],
                 ['serie','=',$request->serie],
                 ['numero','=',$request->numero]])
        ->count();

        if ($count == 0){
            $data = DB::table('almacen.serie_numero')
                ->where('id_serie_numero', $request->id_serie_numero)
                ->update([
                    'id_tp_documento' => $request->id_tp_documento,
                    'id_sede' => $request->id_sede,
                    'serie' => $request->serie,
                    'numero' => $request->numero
                ]);
            $rspta = 'Se actualizó con éxito.';
        } else {
            $rspta = 'Ya existe dicha serie-numero.';
        }
        return response()->json($rspta);
    }
    public function anular_serie_numero($id)
    {
        $data = DB::table('almacen.serie_numero')->where('id_serie_numero', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }

    public function next_serie_numero_guia($id_sede, $id_tp_doc){
        $tp_doc = DB::table('almacen.tp_doc_almacen')
        ->where('id_tp_doc_almacen',$id_tp_doc)
        ->first();

        $data = DB::table('almacen.serie_numero')
        ->select('serie_numero.*')
        ->where([['id_sede','=',$id_sede],
                 ['id_tp_documento','=',$tp_doc->id_tp_doc],
                 ['estado','=',1]])
        ->orderBy('numero','asc')
        ->first();

        if (isset($data)){
            return response()->json($data);
        } else {
            return response()->json('');
        }
    }

    public function next_serie_numero_doc($id_sede, $id_tp_doc){
        $data = DB::table('almacen.serie_numero')
        ->select('serie_numero.*')
        ->where([['id_sede','=',$id_sede],
                 ['id_tp_documento','=',$id_tp_doc],
                 ['estado','=',1]])
        ->orderBy('numero','asc')
        ->first();

        if (isset($data)){
            return response()->json($data);
        } else {
            return response()->json('');
        }
    }

    public function copiar_items_occ_doc($id, $id_doc){
        // $detalle = DB::connection('mgcp')->table('ordenes_compra')
        // ->select('orden_publica_detalles.*','ordenes_compra.id','productos_am.descripcion',
        // 'ordenes_compra.fecha_entrega','ordenes_compra.lugar_entrega')
        // ->leftjoin('orden_publica_detalles','orden_publica_detalles.id_oc','=','ordenes_compra.orden_compra')
        // ->leftjoin('productos_am','productos_am.id','=','orden_publica_detalles.id_producto')
        // ->where('ordenes_compra.id',$id)
        // ->get();

        $detalle = DB::table('logistica.log_det_ord_compra')
        ->select('log_det_ord_compra.*','log_valorizacion_cotizacion.precio_cotizado',
        'log_valorizacion_cotizacion.cantidad_cotizada','log_valorizacion_cotizacion.id_unidad_medida',
        'log_valorizacion_cotizacion.porcentaje_descuento','log_valorizacion_cotizacion.monto_descuento',
        'log_valorizacion_cotizacion.subtotal',
        DB::raw("(CASE 
        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
        ELSE 'nulo' END) AS descripcion
        "),
        DB::raw("(CASE 
        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
        ELSE 'nulo' END) AS codigo
        "),
        DB::raw("(CASE 
        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
        ELSE 'nulo' END) AS unidad_medida
        "))
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
        ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'log_det_ord_compra.id_item')
        ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
        ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
        ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
        ->leftjoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
        ->where([['log_ord_compra.id_occ','=',$id],['log_ord_compra.estado','!=',7]])
        ->get();

        $html = '';
        
        foreach($detalle as $det){
            $id_doc_det = DB::table('almacen.doc_ven_det')->insertGetId([
                'id_doc' => $id_doc,
                'id_item' => $det->id_item,
                'cantidad' => $det->cantidad_cotizada,
                'id_unid_med' => $det->id_unidad_medida,
                'precio_unitario' => floatval($det->precio_cotizado),
                'sub_total' => ($det->cantidad_cotizada * floatval($det->precio_cotizado)),
                'porcen_dscto' => $det->porcentaje_descuento,
                'total_dscto' => $det->monto_descuento,
                'precio_total' => $det->subtotal,
                'id_oc_det' => $det->id_detalle_orden,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_doc_det'
            );

            $html.='
            <tr id="'.$id_doc_det.'">
                <td>'.$det->codigo.'</td>
                <td>'.$det->descripcion.'</td>
                <td>'.$det->cantidad_cotizada.'</td>
                <td>'.$det->unidad_medida.'</td>
                <td>'.$det->precio_cotizado.'</td>
                <td>'.$det->porcentaje_descuento.'</td>
                <td>'.$det->monto_descuento.'</td>
                <td>'.$det->subtotal.'</td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$id_doc_det.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle('.$id_doc_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$id_doc_det.');"></i>
                </td>
            </tr>
            ';
        }
        return json_encode($html);
        // return response()->json($detalle);
    }

    public function actualiza_totales_doc_ven($id_doc){
        $doc_ven = DB::table('almacen.doc_ven_det')
        ->select(DB::raw('sum(doc_ven_det.precio_total) as suma'),
        'doc_ven.fecha_emision','doc_ven.total_descuento','doc_ven.porcen_descuento',
        'doc_ven.total','doc_ven.total_igv','doc_ven.total_ant_igv',
        'doc_ven.total_a_pagar')
        ->join('almacen.doc_ven','doc_ven.id_doc_ven','=','doc_ven_det.id_doc')
        ->where('id_doc',$id_doc)
        ->groupBy('doc_ven.fecha_emision','doc_ven.total_descuento','doc_ven.porcen_descuento',
        'doc_ven.total','doc_ven.total_igv','doc_ven.total_ant_igv',
        'doc_ven.total_a_pagar')
        ->first();

        $igv = $this->mostrar_impuesto('IGV',$doc_ven->fecha_emision);
        $total_igv = $igv->porcentaje / 100 * $doc_ven->suma;

        $data = DB::table('almacen.doc_ven')->where('id_doc_ven',$id_doc)
        ->update([  'sub_total'=> $doc_ven->suma,
                    'total_descuento' => ($doc_ven->total_descuento !== null ? $doc_ven->total_descuento : 0),
                    'porcen_descuento' => ($doc_ven->porcen_descuento !== null ? $doc_ven->porcen_descuento : 0),
                    'total_igv'=> $total_igv,
                    'total'=> ($doc_ven->suma + $total_igv),
                    'total_ant_igv' => ($doc_ven->total_ant_igv !== null ? $doc_ven->total_ant_igv : 0),
                    'total_a_pagar'=> ($doc_ven->suma + $total_igv)
            ]);
        return response()->json($data);
    }
    public function listar_occ(){
        $data = DB::connection('mgcp')->table('ordenes_compra')
        ->select('ordenes_compra.*','entidades.ruc','entidades.entidad')
        ->leftjoin('entidades','entidades.id','=','ordenes_compra.id_entidad')
        ->where('estado_am','ACEPTADA C/ENTREGA PENDIENTE')
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_kardex(){
        $data = DB::connection('soft')->table('movimien')
        ->where([['fec_docu','>=','2018-01-01'],
                 ['cod_docu','=','FA']])
        ->get();
        $output['data'] = $data;
        $size = $data->count();
        return response()->json(['size'=>$size,'output'=>$output]);
    }

    public function migrar_docs_compra(){
        
        $data = DB::table('almacen.doc_com')
        ->select('doc_com.*','cont_tp_doc.abreviatura','adm_contri.nro_documento')
        ->join('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where([['doc_com.fecha_emision','>=','2019-11-09'],['doc_com.estado','=',1]])
        ->get();


        foreach($data as $d){
            $guia = DB::table('almacen.doc_com_guia')
            ->select('guia_com.*','alm_almacen.codigo as cod_almacen')
            ->join('almacen.guia_com','guia_com.id_guia','=','doc_com_guia.id_guia_com')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_com.id_almacen')
            ->where('id_doc_com',$d->id_doc_com)
            ->first();

            $prove = DB::connection('soft')->table('auxiliar')
            ->where('ruc_auxi',$d->nro_documento)
            ->first();

            // if (isset($prove)){

            // }

            $guardar = DB::connection('soft')->table('movimien')->insertGetId(
            [
                'tipo' => 1, //decimal(1,0) NOT NULL DEFAULT '0' COMMENT 'Tipo de Aplicacion 1=Compras 2=Ventas',
                'cod_suc' => 1, //char(1) NOT NULL DEFAULT '' COMMENT 'ID de Sucursal',
                'cod_alma' => $guia->cod_almacen, //char(3) NOT NULL DEFAULT '' COMMENT 'ID de Almacen',
                'cod_docu' => $d->abreviatura, //char(2) NOT NULL DEFAULT '' COMMENT 'ID de Documento',
                'num_docu' => ($d->serie.$d->numero), // char(11) NOT NULL DEFAULT '' COMMENT 'Numero de Documento',
                'fec_docu' => $d->fecha_emision, //date NOT NULL COMMENT 'Fecha de Emision',
                'fec_entre' => $d->fecha_emision, //date NOT NULL COMMENT 'Fecha de Entrega',
                'fec_vcto' => $d->fecha_vcmto, //date NOT NULL COMMENT 'Fecha de Vencimiento',
                'flg_sitpedido' => 0, //???? bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que Indica si es de Seguimiento',
                'cod_pedi' => 'GR',//char(2) NOT NULL DEFAULT '' COMMENT 'ID de Referencia',
                'num_pedi' => ($guia->serie.$guia->numero), //char(11) NOT NULL DEFAULT '' COMMENT 'Numero de Referencia',
                'cod_auxi' => (isset($prove) ? $prove->cod_auxi : ''), //char(6) NOT NULL DEFAULT '' COMMENT 'ID de Cliente o Proveedor',
                'cod_trans' => '00000', //char(5) NOT NULL DEFAULT '00000' COMMENT 'ID de Transportista',
                'cod_vend' => '000001', //char(6) NOT NULL DEFAULT '' COMMENT 'ID de Vendedor',
                'tip_mone' => $d->moneda, //decimal(1,0) NOT NULL DEFAULT '0' COMMENT 'Moneda de Doc. 1=Soles 2=Dolares',
                'impto1' => $d->porcen_igv, //decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Porct. % Impuesto 1',
                'impto2' => 0, //decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Porct. % Impuesto 2',
                'mon_bruto' => $d->sub_total, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto Neto',
                'mon_impto1' => $d->total_igv, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Impuestos 1',
                'mon_impto2' => 0, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Impuestos 2',
                'mon_gravado' => $d->sub_total, //decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Gravado',
                'mon_inafec' => 0,//decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Inafectos',
                'mon_exonera' => 0,//decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Exonerado',
                'mon_gratis' => 0,//decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de Gratutito',
                'mon_total' => $d->total_a_pagar,//decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto Total',
                'sal_docu' => 0,//???decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Saldo de Documento',
                'tot_cargo' => 0,//???decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Cargos',
                'tot_percep' => 0,//???decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Percepcion',
                'tip_codicion' => '02',//?????char(2) NOT NULL DEFAULT '' COMMENT 'ID de Condicion',
                'txt_observa' => '',//varchar(250) NOT NULL DEFAULT ' ' COMMENT 'Notas de Documento',
                'flg_kardex' => 0,//bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica si actualizo Kardex',
                'flg_anulado' => ($d->estado == 7 ? 1 : 0),//bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica si esta Anulado',
                'flg_referen' => 0,//????bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica si esta Referenciado',
                'flg_percep' => 0,//bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica si hace percepcion',
                'cod_user' => '000001',//char(6) NOT NULL DEFAULT '' COMMENT 'ID de Usuario de sistema',
                'programa' => '',//char(1) NOT NULL DEFAULT '' COMMENT 'Valor sin Uso',
                'txt_nota' => '',//varchar(100) NOT NULL DEFAULT ' ' COMMENT 'Notas de Documento',
                'tip_cambio' => $d->tipo_cambio,//decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Tipo de Cambio',
                'tdflags' => 'NSSNNSSSSN',//?????char(12) NOT NULL DEFAULT '' COMMENT 'Flags de Configuraciones',
                'numlet' => '',//varchar(150) NOT NULL DEFAULT ' ' COMMENT 'Importe Total en Letras',
                'impdcto' => $d->total_descuento,//decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'Importe del Descuento',
                'impanticipos' => $d->total_ant_igv,//decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'Importe de Anticipo',
                'registro' => $d->fecha_registro,//timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha Hora de creacion',
                'tipo_canje' => 0, // decimal(1,0) NOT NULL DEFAULT '0' COMMENT 'Tipo de Canje Letras',
                'numcanje' => 0,// varchar(11) NOT NULL DEFAULT '' COMMENT 'Numero de Canje de Letras',
                'cobrobco'  => 0,//bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flags que Indica si la Letra esta en Banco',
                'ctabco'  => '',//char(3) NOT NULL DEFAULT '' COMMENT 'Cuenta de Banco donde se encuentra el Doc.',
                'flg_qcont' => 0,//bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que se ha Contabilizado / Anticipo cerrado',
                'fec_anul' => '0000-00-00',//date NOT NULL COMMENT 'Fecha de Anulado del Doc.',
                'audit' => 2, //decimal(1,0) NOT NULL DEFAULT '0' COMMENT 'Valor de Auditoria',
                'origen' => '',//char(1) NOT NULL DEFAULT '' COMMENT 'Valor en caso sea de Importacion',
                'tip_cont' => '',//char(2) NOT NULL DEFAULT '' COMMENT 'ID de Tipo de Contrato',
                'tip_fact' => '',//char(2) NOT NULL DEFAULT '' COMMENT 'ID de Tipo de Facturacion',
                'contrato' => '', //varchar(13) NOT NULL DEFAULT '' COMMENT 'ID y Numero de Contrato',
                'idcontrato' => '', //varchar(10) NOT NULL DEFAULT '' COMMENT 'ID Prinicpal del Contrato',
                'canje_fact' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica que esta canjeado x una Factura',
                'aceptado' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag de Aprobacion',
                'reg_conta' => 0, //decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'Nro de Registro Contable',
                'mov_pago' => '', //varchar(10) NOT NULL DEFAULT ' ' COMMENT 'ID Prog de Pago / Estado de Letra',
                'ndocu1' => '', //varchar(25) NOT NULL DEFAULT ' ' COMMENT 'Documento 1',
                'ndocu2' => '', //varchar(25) NOT NULL DEFAULT ' ' COMMENT 'Documento 2',
                'ndocu3' => '', //varchar(25) NOT NULL DEFAULT ' ' COMMENT 'Documento 3',
                'flg_logis' => 0, //???bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag de Stock Pedido y en Transito',
                'cod_recep' => '',//char(6) NOT NULL DEFAULT '' COMMENT 'ID de Receptor',
                'flg_aprueba' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag de Transf. Aprobada',
                'fec_aprueba' => '0000-00-00 00:00:00',// datetime NOT NULL COMMENT 'Fecha de Aprobacion de Transf.',
                'flg_limite' => 0,//bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag para saber si afecta el Limite de credito',
                'fecpago' => '0000-00-00', //date NOT NULL COMMENT 'Fecha de Cancelacion',
                'imp_comi' => 0,//decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Importe de Comision',
                'ptosbonus' => 0,//decimal(5,0) NOT NULL DEFAULT '0' COMMENT 'Ptos. ganados por documento',
                'canjepedtran' => 0,//bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag que indica que el documento canjeo el Stock Comprometido',
                'cod_clasi' => $guia->id_guia_clas,//char(1) NOT NULL DEFAULT '' COMMENT 'ID de Bien y/o Servicio',
                'doc_elec' => '',//varchar(2) NOT NULL DEFAULT ' ' COMMENT 'ID doc.Elect. SUNAT',
                'cod_nota' => '',//varchar(2) NOT NULL DEFAULT ' ' COMMENT 'ID de tipo de NC-ND',
                'hashcpe' => '',//varchar(50) NOT NULL DEFAULT ' ' COMMENT 'Hash Sunat CPE',
                'flg_sunat_acep' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Aceptado en Sunat',
                'flg_sunat_anul' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Anulado en Sunat',
                'flg_sunat_mail' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Email enviado',
                'flg_sunat_webs' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Publicado Web custodia',
                'mov_id_baja' => '', //varchar(10) NOT NULL DEFAULT ' ' COMMENT 'ID Comunicacion de baja',
                'mov_id_resu_bv' => '', //varchar(10) NOT NULL DEFAULT ' ' COMMENT 'ID Resumen diario BV',
                'mov_id_resu_ci' => '', //varchar(10) NOT NULL DEFAULT ' ' COMMENT 'ID Resumen comprobante impreso',
                'flg_guia_traslado' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Documento Guia Traslado',
                'flg_anticipo_doc' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Anticipo Recibido',
                'flg_anticipo_reg' => 0, //bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Anticipo Regularizacion',
                'doc_anticipo_id' => '',//varchar(10) NOT NULL DEFAULT ' ' COMMENT 'MovID de doc. de anticipo',
                'flg_emi_itinerante' => 0,//bit(1) NOT NULL DEFAULT b'0' COMMENT 'Flag Emisor Itinerante BOLETA',
                'placa' =>''

                ],
                    'mov_id'//???char(10) NOT NULL DEFAULT '' COMMENT 'ID Principal SYS(2015) VFP',
            );
        }

        return response()->json($guardar);
    }

    public function imprimir_guia_venta($id_guia_ven){
        $id_guia = $this->decode5t($id_guia_ven);
        $data = DB::table('almacen.guia_ven')
                ->select('guia_ven.*','adm_contri.razon_social as cli_razon_social',
                'contri.nro_documento as emp_ruc','adm_contri.nro_documento as cli_ruc',
                'adm_empresa.id_empresa',
                DB::raw("CONCAT(ubi_dis.descripcion,'-',ubi_prov.descripcion,'-',ubi_dpto.descripcion) as ubigeo_cliente"))
                ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
                ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
                ->leftjoin('configuracion.ubi_dis','ubi_dis.id_dis','=','adm_contri.ubigeo')
                ->leftjoin('configuracion.ubi_prov','ubi_prov.id_prov','=','ubi_dis.id_prov')
                ->leftjoin('configuracion.ubi_dpto','ubi_dpto.id_dpto','=','ubi_prov.id_dpto')
                ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','guia_ven.id_sede')
                ->leftjoin('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
                ->leftjoin('contabilidad.adm_contri as contri','contri.id_contribuyente','=','adm_empresa.id_contribuyente')
                ->where('id_guia_ven',$id_guia)
                ->first();
        
        $detalle = DB::table('almacen.guia_ven_det')
                ->select('guia_ven_det.*','alm_prod.codigo as cod_producto',
                'alm_prod.descripcion as des_producto','alm_ubi_posicion.codigo as cod_posicion',
                'alm_und_medida.abreviatura')
                ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_ven_det.id_producto')
                ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_ven_det.id_posicion')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_ven_det.id_unid_med')
                ->where([['guia_ven_det.id_guia_ven','=',$id_guia],
                        ['guia_ven_det.estado','=',1]])
                ->get();

        $nuevo_detalle = [];

        foreach($detalle as $det){
            $exist = false;
            foreach ($nuevo_detalle as $nue => $value){
                if ($det->id_producto == $value['id_producto']){
                    $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
                    // $nuevo_detalle[$nue]['valorizacion'] = floatval($value['valorizacion']) + floatval($det->total);
                    $series = DB::table('almacen.alm_prod_serie')
                    ->where([['id_guia_ven_det','=',$det->id_guia_ven_det],
                            ['estado','=',1]])
                    ->get();
                    $imp_series = '';
                    if (isset($series)){
                        foreach ($series as $se){
                            if ($imp_series == ''){
                                $imp_series .= $se->serie;
                            } else {
                                $imp_series .= ', '.$se->serie;
                            }
                        }
                    }
                    $nuevo_detalle[$nue]['series'] = $value['series'].', '.$imp_series;
                    $exist = true;
                }
            }
            if ($exist === false){
                $series = DB::table('almacen.alm_prod_serie')
                ->where([['id_guia_ven_det','=',$det->id_guia_ven_det],
                        ['estado','=',1]])
                ->get();
                $imp_series = '';
                if (isset($series)){
                    foreach ($series as $se){
                        if ($imp_series == ''){
                            $imp_series .= $se->serie;
                        } else {
                            $imp_series .= ', '.$se->serie;
                        }
                    }
                }
                $nuevo = [
                    'id_guia_ven_det' => $det->id_guia_ven_det,
                    'id_producto' => $det->id_producto,
                    'id_posicion' => $det->id_posicion,
                    'cod_producto' => $det->cod_producto,
                    'des_producto' => $det->des_producto,
                    'cod_posicion' => $det->cod_posicion,
                    'abreviatura' => $det->abreviatura,
                    'series' => $imp_series,
                    'cantidad' => floatval($det->cantidad)
                ];
                array_push($nuevo_detalle, $nuevo);
            }
        }

        $html = '';
        if ($data->id_empresa == 1){// 1 Ok Computer
            $html = $this->guia_ok_computer($data, $nuevo_detalle);
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
        return $pdf->download('guia_ven.pdf');
        // return $detalle;
    }

    public function guia_ok_computer($data, $nuevo_detalle){
        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:11px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td,
                #detalle tfoot tr td{
                    font-size:11px;
                    padding: 4px;
                }
                #detalle tfoot{
                    border-top: 1px dashed #343a40;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                .blanco{
                    color:#fff;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tbody>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr>
                            <td colSpan="3" class="blanco">.</td>
                            <td width="280px" colSpan="4">'.$data->fecha_emision.'</td>
                            <td>.</td>
                            <td>'.$data->serie.'-'.$data->numero.'</td>
                        </tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr>
                            <td colSpan="2" class="blanco">.</td>
                            <td colSpan="4">'.$data->punto_partida.'</td>
                            <td width="90px" class="blanco">.</td>
                            <td>'.$data->cli_razon_social.'</td>
                        </tr>
                        <tr>
                            <td width="80px" class="blanco">.</td>
                            <td colSpan="5">'.$data->emp_ruc.'</td>
                            <td class="blanco">.</td>
                            <td>'.$data->punto_llegada.'</td>
                        </tr>
                        <tr>
                            <td colSpan="4" class="blanco">.</td>
                            <td colSpan="2">'.$data->fecha_traslado.'</td>
                            <td class="blanco">.</td>
                            <td>'.$data->cli_ruc.'  '.$data->ubi_descripcion.'</td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <table id="detalle">
                    <tbody>
                        <tr><td colSpan="4" class="blanco">.</td></tr>
                        <tr><td colSpan="4" class="blanco">.</td></tr>
                        <tr><td colSpan="4" class="blanco">.</td></tr>';
                    
                    foreach($nuevo_detalle as $det){
                        $html.='
                        <tr>
                            <td class="sup">'.$det['cod_producto'].'</td>
                            <td class="sup">'.$det['cantidad'].'</td>
                            <td class="sup">'.trim($det['abreviatura']).'</td>
                            <td class="sup">'.$det['des_producto'].'. Series: '.$det['series'].'</td>
                        </tr>';
                    }   
                    $html.='</tbody>
                </table>
                <br/>
            </body>
        </html>';

        return $html;
    }

    public function guia_proyectec($data, $nuevo_detalle){
        $dia = date("d", strtotime($data->fecha_emision));
        $mes = date("m", strtotime($data->fecha_emision));
        $anio = date("Y", strtotime($data->fecha_emision));
        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:11px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td,
                #detalle tfoot tr td{
                    font-size:11px;
                    padding: 4px;
                }
                #detalle tfoot{
                    border-top: 1px dashed #343a40;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                .blanco{
                    color:#fff;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tbody>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr>
                            <td colSpan="3" class="blanco">.</td>
                            <td width="280px" colSpan="4">'.$dia.'   '.$mes.'   '.$anio.'</td>
                            <td>.</td>
                            <td>'.$data->serie.'-'.$data->numero.'</td>
                        </tr>
                        <tr><td colSpan="8" class="blanco">.</td></tr>
                        <tr>
                            <td colSpan="2" class="blanco">.</td>
                            <td colSpan="4">'.$data->punto_partida.'</td>
                            <td width="90px" class="blanco">.</td>
                            <td>'.$data->punto_llegada.'</td>
                        </tr>
                        <tr>
                            <td width="80px" class="blanco">.</td>
                            <td colSpan="5">'.$data->emp_ruc.'</td>
                            <td class="blanco">.</td>
                            <td>'.$data->punto_llegada.'</td>
                        </tr>
                        <tr>
                            <td colSpan="4" class="blanco">.</td>
                            <td colSpan="2">'.$data->fecha_traslado.'</td>
                            <td class="blanco">.</td>
                            <td>'.$data->cli_ruc.'  '.$data->ubi_descripcion.'</td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <table id="detalle">
                    <tbody>
                        <tr><td colSpan="4" class="blanco">.</td></tr>
                        <tr><td colSpan="4" class="blanco">.</td></tr>
                        <tr><td colSpan="4" class="blanco">.</td></tr>';

                    foreach($nuevo_detalle as $det){
                        $html.='
                        <tr>
                            <td class="sup">'.$det['cod_producto'].'</td>
                            <td class="sup">'.$det['cantidad'].'</td>
                            <td class="sup">'.trim($det['abreviatura']).'</td>
                            <td class="sup">'.$det['des_producto'].'. Series: '.$det['series'].'</td>
                        </tr>';
                    }   
                    $html.='</tbody>
                </table>
                <br/>
            </body>
        </html>';

        return $html;
    }

    public function listar_kardex_serie($serie, $descripcion){
        $hasWhere = [];
        if ($serie !== 'null'){
            $hasWhere = ['alm_prod_serie.serie','=',$serie];
        }
        else if ($descripcion !== 'null'){
            $hasWhere = ['alm_prod.descripcion','like','%'.strtoupper($descripcion).'%'];
        }
        $data = DB::table('almacen.alm_prod_serie')
        ->select('alm_prod_serie.*','alm_prod.descripcion',
        'guia_com.fecha_emision as fecha_guia_com',
        'guia_ven.fecha_emision as fecha_guia_ven',
        'contri_cliente.razon_social as razon_social_cliente',
        'contri_prove.razon_social as razon_social_prove',
        'alm_com.descripcion as almacen_compra','alm_ven.descripcion as almacen_venta',
        DB::raw("CONCAT(tp_doc_com.abreviatura,'-',guia_com.serie,'-',guia_com.numero) as guia_com"),
        DB::raw("CONCAT(tp_doc_ven.abreviatura,'-',guia_ven.serie,'-',guia_ven.numero) as guia_ven"))
        ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','alm_prod_serie.id_guia_ven_det')
        ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
        ->leftjoin('contabilidad.adm_contri as contri_cliente','contri_cliente.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('almacen.tp_doc_almacen as tp_doc_ven','tp_doc_ven.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
        ->leftjoin('almacen.alm_almacen as alm_ven','alm_ven.id_almacen','=','guia_ven.id_almacen')
        ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','alm_prod_serie.id_guia_det')
        ->leftjoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri as contri_prove','contri_prove.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftjoin('almacen.tp_doc_almacen as tp_doc_com','tp_doc_com.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
        ->leftjoin('almacen.alm_almacen as alm_com','alm_com.id_almacen','=','guia_com.id_almacen')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_serie.id_prod')
        ->where([['alm_prod_serie.estado','=',1],
                 ['alm_prod.estado','=',1],
                 $hasWhere])
        ->orderBy('alm_prod_serie.serie')
        ->get();
        $output['data'] = $data;
        return response()->json(($output));
    }

    public function listar_documentos_adicionales(){
        $data = DB::table('almacen.guia_com_prorrateo')
        ->select('guia_com_prorrateo.*','doc_com.serie','doc_com.numero')
        //terminar referencias
        ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','guia_com_prorrateo.id_doc_com')
        ->get();
        return response()->json($data);
    }
    public function listar_ubigeos(){
        $data = DB::table('configuracion.ubi_dis')
        ->select('ubi_dis.*','ubi_prov.descripcion as provincia','ubi_dpto.descripcion as departamento')
        ->join('configuracion.ubi_prov','ubi_prov.id_prov','=','ubi_dis.id_prov')
        ->join('configuracion.ubi_dpto','ubi_dpto.id_dpto','=','ubi_prov.id_dpto')
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }


    ////////////////////////////////////////
    public static function leftZero($lenght, $number){
        $nLen = strlen($number);
        $zeros = '';
        for($i=0; $i<($lenght-$nLen); $i++){
            $zeros = $zeros.'0';
        }
        return $zeros.$number;
    }
    // public function tipo_cambio(Request $request){
    //     $data = file_get_contents('https://api.sunat.cloud/cambio/'.$request->fecha);
        // $info = json_decode($data, true);
        // if ($data === '[]' || $info['fecha_inscripcion'] === '--'){
        //     $datos = array(0 => 'nada');
        // } else {
        //     $datos = array(
        //         0 => $info['compra'],
        //         1 => $info['venta']
        //     );
        // }
    //     return json_encode($data);
    // }
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

        // DB::transaction(function(){
        //     Prueba::whereId('20008')->update('ruta_2','Man-33')->all();
        // });
        // DB::beginTransaction();
        // try {
        //     $post->comments()->save($comment);
        //     $post->last_comment_at = now();
        //     $post->save();
        //     DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     throw $e;
        // } catch (\Throwable $e) {
        //     DB::rollback();
        //     throw $e;

