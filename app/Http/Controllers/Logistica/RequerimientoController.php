<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProyectosController;
use App\Models\Administracion\Area;
use App\Models\Administracion\Division;
use App\Models\Administracion\Documento;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Almacen\CategoriaAdjunto;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Fuente;
use App\Models\Almacen\Requerimiento;
use App\Models\Almacen\TipoRequerimiento;
use App\Models\Almacen\Trazabilidad;
use App\Models\Almacen\UnidadMedida;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Usuario;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\Identidad;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\Logistica\Empresa;
use App\Models\Presupuestos\Presupuesto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class RequerimientoController extends Controller
{
    public function index()
    {

        $grupos = Auth::user()->getAllGrupo();
        $monedas = Moneda::mostrar();
        $prioridades = Prioridad::mostrar();
        $tipo_requerimiento = TipoRequerimiento::mostrar();
        $empresas = Empresa::all();
        $areas = Area::mostrar();
        $unidadesMedida = UnidadMedida::mostrar();
        $periodos = Periodo::mostrar();
        $roles = Auth::user()->getAllRol(); //Usuario::getAllRol(Auth::user()->id_usuario);
        //var_dump($roles);
        //die("FIN");
        $sis_identidad = Identidad::mostrar();
        $bancos = Banco::mostrar();
        $tipos_cuenta = TipoCuenta::mostrar();
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $unidades = (new AlmacenController)->mostrar_unidades_cbo();
        $proyectos_activos = (new ProyectosController)->listar_proyectos_activos();
        $fuentes = Fuente::mostrar();
        $aprobantes = Division::mostrarFlujoAprobacion();
        $categoria_adjunto = CategoriaAdjunto::mostrar();

        return view('logistica/requerimientos/gestionar_requerimiento', compact('categoria_adjunto', 'aprobantes', 'grupos', 'sis_identidad', 'tipo_requerimiento', 'monedas', 'prioridades', 'empresas', 'unidadesMedida', 'roles', 'periodos', 'bancos', 'tipos_cuenta', 'clasificaciones', 'subcategorias', 'categorias', 'unidades', 'proyectos_activos', 'fuentes'));
    }

    public function mostrarPartidas($idGrupo, $idProyecto = null)
    {
        return Presupuesto::mostrarPartidas($idGrupo, $idProyecto);
    }

    public function mostrarCategoriaAdjunto()
    {
        return CategoriaAdjunto::mostrar();
    }



    public function guardarRequerimiento(Request $request)
    {
        // dd($request->all());
        // exit();   
        DB::beginTransaction();
        try {

            $requerimiento = new Requerimiento();
            $requerimiento->codigo =  Requerimiento::crearCodigo($request->tipo_requerimiento, $request->id_grupo);
            $requerimiento->id_tipo_requerimiento = $request->tipo_requerimiento;
            $requerimiento->id_usuario = Auth::user()->id_usuario;
            $requerimiento->id_rol = $request->id_rol;
            $requerimiento->fecha_requerimiento = $request->fecha_requerimiento;
            $requerimiento->id_periodo = $request->id_periodo;
            $requerimiento->concepto = strtoupper($request->concepto);
            $requerimiento->id_moneda = $request->id_moneda;
            $requerimiento->id_proyecto = $request->id_proyecto;
            $requerimiento->observacion = $request->observacion;
            $requerimiento->id_grupo = $request->id_grupo;
            $requerimiento->id_area = $request->id_area;
            $requerimiento->id_area = $request->id_area;
            $requerimiento->id_prioridad = $request->id_prioridad;
            $requerimiento->fecha_registro = new Carbon();
            $requerimiento->estado = 1;
            $requerimiento->id_empresa = $request->id_empresa;
            $requerimiento->id_sede = $request->id_sede;
            $requerimiento->tipo_cliente = $request->tipo_cliente;
            $requerimiento->id_cliente = $request->id_cliente;
            $requerimiento->id_persona = $request->id_persona;
            $requerimiento->direccion_entrega = $request->direccion_entrega;
            $requerimiento->id_cuenta = $request->id_cuenta;
            $requerimiento->nro_cuenta = $request->nro_cuenta;
            $requerimiento->nro_cuenta_interbancaria = $request->nro_cuenta_interbancaria;
            $requerimiento->telefono = $request->telefono;
            $requerimiento->email = $request->email;
            $requerimiento->id_ubigeo_entrega = $request->id_ubigeo_entrega;
            $requerimiento->id_almacen = $request->id_almacen;
            $requerimiento->confirmacion_pago = ($request->tipo_requerimiento == 2 ? ($request->fuente == 2 ? true : false) : true);
            $requerimiento->monto = $request->monto;
            $requerimiento->fecha_entrega = $request->fecha_entrega;
            $requerimiento->id_cc = $request->id_cc;
            $requerimiento->tipo_cuadro = $request->tipo_cuadro;
            $requerimiento->tiene_transformacion = $request->tiene_transformacion ? $request->tiene_transformacion : false;
            $requerimiento->fuente_id = $request->fuente;
            $requerimiento->fuente_det_id = $request->fuente_det;
            $requerimiento->para_stock_almacen = $request->para_stock_almacen;
            $requerimiento->rol_aprobante_id = $request->rol_aprobante;
            $requerimiento->trabajador_id = $request->id_trabajador;
            $requerimiento->save();
            $requerimiento->adjuntoOtrosAdjuntos= $request->archivoAdjuntoRequerimiento1;
            $requerimiento->adjuntoOrdenes= $request->archivoAdjuntoRequerimiento2;
            $requerimiento->adjuntoComprobanteBancario= $request->archivoAdjuntoRequerimiento3;
            $requerimiento->adjuntoComprobanteContable= $request->archivoAdjuntoRequerimiento4;

            $count = count($request->descripcion);

            for ($i = 0; $i < $count; $i++) {
                $detalle = new DetalleRequerimiento();
                $detalle->id_requerimiento = $requerimiento->id_requerimiento;
                $detalle->partida = $request->idPartida[$i];
                $detalle->centro_costo_id = $request->idCentroCosto[$i];
                $detalle->part_number = $request->partNumber[$i];
                $detalle->descripcion = $request->descripcion[$i];
                $detalle->id_unidad_medida = $request->unidad[$i];
                $detalle->cantidad = $request->cantidad[$i];
                $detalle->precio_unitario = $request->precioUnitario[$i];
                $detalle->motivo = $request->motivo[$i];
                $detalle->tiene_transformacion = ($request->tiene_transformacion ? $request->tiene_transformacion : false);
                $detalle->fecha_registro = new Carbon();
                $detalle->estado = $requerimiento->id_tipo_requerimiento == 2 ? 19 : 1;
                $detalle->save();
            }

            $documento = new Documento();
            $documento->id_tp_documento = 1;
            $documento->codigo_doc = $requerimiento->codigo;
            $documento->id_doc = $requerimiento->id_requerimiento;
            $documento->save();

            $trazabilidad = new Trazabilidad();
            $trazabilidad->id_requerimiento = $requerimiento->id_requerimiento;
            $trazabilidad->id_usuario = Auth::user()->id_usuario;
            if ($requerimiento->id_tipo_requerimiento == 1) { // tipo mgcp
                $trazabilidad->accion = 'VINCULADO';
                $trazabilidad->descripcion = 'Fecha de creación de Cuadro de Costos: ' . (isset($request->fecha_creacion_cc) ? $request->fecha_creacion_cc : '');
                $trazabilidad->fecha_registro = $request->fecha_creacion_cc;
            } else {
                $trazabilidad->accion = 'ELABORADO';
                $trazabilidad->descripcion = 'Requerimiento elaborado.' . (isset($request->justificacion_generar_requerimiento) ? ('Con CC Pendiente Aprobación. ' . $request->justificacion_generar_requerimiento) : '');
                $trazabilidad->fecha_registro = new Carbon();
            }
            $trazabilidad->save();

            // Requerimiento::guardarAdjuntoNivelRequerimiento($requerimiento);


            DB::commit();
            // TODO: ENVIAR CORREO AL APROBADOR DE ACUERDO AL MONTO SELECCIONADO DEL REQUERIMIENTO
            return response()->json(['id_requerimiento' => $requerimiento->id_requerimiento, 'codigo' => $requerimiento->codigo]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento' => 0, 'codigo' => '', 'mensaje'=>'Hubo un problema al guardar el requerimiento. Por favor intentelo de nuevo. Mensaje de error: '.$e->getMessage()]);
 
        }
    }

    public static function guardarAdjuntoNivelRequerimiento($requerimiento) {


        $adjuntoOtrosAdjuntosLength = $requerimiento->adjuntoOtrosAdjuntos != null ? count($requerimiento->adjuntoOtrosAdjuntos) : 0;
        $adjuntoOrdenesLength = $requerimiento->adjuntoOrdenes!= null ? count($requerimiento->adjuntoOrdenes) : 0;
        $adjuntoComprobanteBancarioLength = $requerimiento->adjuntoComprobanteContable!= null ? count($requerimiento->adjuntoComprobanteContable):0;
        $adjuntoComprobanteContableLength = $requerimiento->adjuntoComprobanteBancario !=null ? count($requerimiento->adjuntoComprobanteBancario):0;

   
        
        if ($adjuntoOtrosAdjuntosLength >0) {
            foreach($requerimiento->adjuntoOtrosAdjuntos as $clave =>$valor){
                $alm_req_adjuntos = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                    [
                        'id_requerimiento'          => $requerimiento->id_requerimiento,
                        'archivo'                   => $valor->nameFile,
                        'estado'                    => 1,
                        'categoria_adjunto_id'      => $valor->c,
                        'fecha_registro'            => date('Y-m-d H:i:s')
                    ],
                    'id_adjunto'
                );
                Storage::disk('archivos')->put("logistica/requerimiento/" . $name_file, \File::get($file));
            }

        }


        return response()->json($alm_req_adjuntos);
    }



    public function listar_requerimientos_elaborados(Request $request){
        $id_empresa =$request->id_empresa;
        $id_sede=$request->id_sede;
        $id_grupo=$request->id_grupo;
        $id_prioridad=$request->id_prioridad;

        $hasWhere=[
            ['alm_req.estado', '!=', 7],
            ['adm_grupo.estado', '=', 1],
            ['sis_sede.estado', '=', 1]
        ];

        if($id_empresa >0){
            $hasWhere[]=['alm_req.id_empresa', '=', $id_empresa];
        }
        if($id_sede >0){
            $hasWhere[]=['alm_req.id_sede', '=', $id_sede];
        }
        if($id_grupo >0){
            $hasWhere[]=['adm_grupo.id_grupo', '=', $id_grupo];
        }
        if($id_prioridad >0){
            $hasWhere[]=['alm_req.id_prioridad', '=', $id_prioridad];
        }

        $req     = array();
        $det_req = array();

        $requerimientos = Requerimiento::leftJoin('administracion.adm_documentos_aprob', 'alm_req.id_requerimiento', '=', 'adm_documentos_aprob.id_doc')
        ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
        ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
        ->leftJoin('administracion.adm_grupo', 'alm_req.id_grupo', '=', 'adm_grupo.id_grupo')
        ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
        ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
        ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
        ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'alm_req.id_periodo')
        ->leftJoin('administracion.adm_empresa', 'alm_req.id_empresa', '=', 'adm_empresa.id_empresa')
        ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
        ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
        ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
        ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
        ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
        ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')

        ->select(
            'alm_req.id_requerimiento',
            'adm_documentos_aprob.id_doc_aprob',
            'alm_req.codigo',
            'alm_req.id_tipo_requerimiento',
            'alm_req.id_usuario',
            'alm_req.id_rol',
            'alm_req.fecha_requerimiento',
            'alm_req.fecha_entrega',
            'alm_req.id_periodo',
            'adm_periodo.descripcion as descripcion_periodo',
            'alm_req.concepto',
            'alm_req.id_grupo',
            'alm_req.id_empresa',
            'adm_contri.razon_social',
            'adm_contri.nro_documento',
            'adm_contri.id_doc_identidad',
            'sis_identi.descripcion as tipo_documento_identidad',
            // 'proy_op_com.codigo as codigo_op_com',
            // 'proy_op_com.descripcion as descripcion_op_com',
            'alm_req.concepto AS alm_req_concepto',
            'alm_req.estado',
            'alm_req.fecha_registro',
            'alm_req.id_area',
            'alm_req.id_prioridad',
            'alm_req.id_presupuesto',
            'alm_req.id_moneda',
            'adm_estado_doc.estado_doc',
            'alm_tp_req.descripcion AS tipo_requerimiento',
            'adm_prioridad.descripcion AS priori',
            'adm_grupo.descripcion AS grupo',
            'adm_area.descripcion AS area',
            'sis_moneda.simbolo AS simbolo_moneda',
            DB::raw("CONCAT(pers.nombres,' ',pers.apellido_paterno,' ',pers.apellido_materno) as nombre_usuario")

        )
        ->where($hasWhere);

        return datatables($requerimientos)->filterColumn('nombre_usuario', function ($query, $keyword) {
            $keywords = trim(strtoupper($keyword));
            $query->whereRaw("UPPER(CONCAT(pers.nombres,' ',pers.apellido_paterno,' ',pers.apellido_materno)) LIKE ?", ["%{$keywords}%"]);
        })->toJson();
        // ->orderBy('alm_req.id_requerimiento', 'DESC')
        // ->get();

        // $simbolo_moneda='';
        // foreach($sql_req as $data){
        //     $req[]=[
        //         'id_requerimiento' => $data->id_requerimiento,
        //         'id_doc_aprob' => $data->id_doc_aprob,
        //         'codigo' => $data->codigo,
        //         'id_tipo_requerimiento' => $data->id_tipo_requerimiento,
        //         'id_usuario' => $data->id_usuario,
        //         'id_rol' => $data->id_rol,
        //         'fecha_requerimiento' => $data->fecha_requerimiento,
        //         'id_periodo' => $data->id_periodo,
        //         'descripcion_periodo' => $data->descripcion_periodo,
        //         'concepto' => $data->concepto,
        //         'id_empresa' => $data->id_empresa,
        //         'razon_social' => $data->razon_social,
        //         'nro_documento' => $data->nro_documento,
        //         'tipo_documento_identidad' => $data->tipo_documento_identidad,
        //         'id_grupo' => $data->id_grupo,
        //         'estado' => $data->estado,
        //         'fecha_registro' => $data->fecha_registro,
        //         'id_area' => $data->id_area,
        //         'id_prioridad' => $data->id_prioridad,
        //         'id_presupuesto' => $data->id_presupuesto,
        //         'id_moneda' => $data->id_moneda,
        //         'simbolo_moneda' => $data->simbolo_moneda,
        //         'estado_doc' => $data->estado_doc,
        //         'tipo_requerimiento' => $data->tipo_requerimiento,
        //         'priori' => $data->priori,
        //         'grupo' => $data->grupo,
        //         'area' => $data->area,
        //         'usuario' => $data->nombre_usuario,

                
        //     ];

        //     $simbolo_moneda = $data->simbolo_moneda;

        // }

        // $size_req= count($req);

        // $sql_det_req = DB::table('almacen.alm_det_req')
        // ->select(
        //     'alm_det_req.id_detalle_requerimiento',
        //     'alm_det_req.id_requerimiento',
        //     'alm_det_req.id_item',
        //     'alm_det_req.precio_unitario',
        //     'alm_det_req.subtotal',
        //     'alm_det_req.cantidad',
        //     'alm_det_req.descripcion_adicional',
        //     'alm_det_req.partida',
        //     'alm_det_req.unidad_medida',
        //     'alm_det_req.estado',
        //     'alm_det_req.fecha_registro',
        //     'alm_det_req.lugar_entrega',
        //     'alm_det_req.id_unidad_medida',
        //     'alm_det_req.id_tipo_item'
        // )
        // ->where('alm_det_req.estado', '!=', $estado_anulado)
        // ->orderBy('alm_det_req.id_requerimiento', 'DESC')
        // ->get();


        // $aux_sum=0; // aux monto referencual head req

        // if(isset($sql_det_req) && sizeof($sql_det_req) > 0){
        //     foreach($sql_det_req as $data){
                
        //         $det_req[]=[
        //             'id_detalle_requerimiento'=> $data->id_detalle_requerimiento,
        //             'id_requerimiento'=> $data->id_requerimiento,
        //             'id_item'=> $data->id_item,
        //             'precio_unitario'=> $data->precio_unitario,
        //             'subtotal'=> $data->subtotal,
        //             'cantidad'=> $data->cantidad,
        //             'descripcion_adicional'=> $data->descripcion_adicional,
        //             'partida'=> $data->partida,
        //             'unidad_medida'=> $data->unidad_medida,
        //             'estado'=> $data->estado,
        //             'fecha_registro'=> $data->fecha_registro,
        //             'lugar_entrega'=> $data->lugar_entrega,
        //             'id_unidad_medida'=> $data->id_unidad_medida,
        //             'id_tipo_item'=> $data->id_tipo_item
        //         ];


        //     }
    
        //     $size_det_req= count($det_req);
            
        //     for($i = 0; $i < $size_req; $i++ ){
        //         for($j = 0; $j < $size_det_req; $j++ ){
        //             $req[$i]['detalle'] = [];
    
        //         }
        //     }
    
        //     for($i = 0; $i < $size_req; $i++ ){
        //         for($j = 0; $j < $size_det_req; $j++ ){
        //             if($det_req[$j]['id_requerimiento'] == $req[$i]['id_requerimiento']){
        //                 $req[$i]['detalle'][] = $det_req[$j];
        //             }
        //         }
        //     }
        // }else{ // si no existe datos en detalle_requerimiento
        //     for($i = 0; $i < $size_req; $i++ ){
        //         $req[$i]['detalle'] = [];
        //     }
        // }

        

    }

    function viewLista()
    {
        $grupos = Auth::user()->getAllGrupo();
        $roles = Auth::user()->getAllRol(); //$this->userSession()['roles'];
        $empresas = Empresa::all();
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();


        return view('logistica/requerimientos/lista_requerimientos', compact('periodos','grupos','roles','empresas','prioridades'));
    }


    public function viewAprobar(Request $request){
        $grupos = Auth::user()->getAllGrupo();
        $roles = Auth::user()->getAllRol();
        $empresas = Empresa::all();
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();


        return view('logistica/requerimientos/aprobar_requerimiento', compact('periodos','grupos','roles','empresas','prioridades'));
    }

}
