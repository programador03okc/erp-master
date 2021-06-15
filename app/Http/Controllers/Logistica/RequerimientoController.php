<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProyectosController;
use App\Models\Administracion\Aprobacion;
use App\Models\Administracion\Area;
use App\Models\Administracion\Division;
use App\Models\Administracion\Documento;
use App\Models\Administracion\Flujo;
use App\Models\Administracion\Operacion;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Administracion\VoBo;
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
use App\Models\Administracion\Empresa;
use App\Models\administracion\Sede;
use App\Models\Configuracion\Grupo;
use App\Models\Presupuestos\Presupuesto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Debugbar;


class RequerimientoController extends Controller
{
    public function index()
    {

        $grupos = Auth::user()->getAllGrupo();
        $monedas = Moneda::mostrar();
        $prioridades = Prioridad::mostrar();
        $tipo_requerimiento = TipoRequerimiento::mostrar();
        $empresas = Empresa::mostrar();
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

    public function requerimientoAPago($id)
    {
        $req = DB::table('almacen.alm_req')
            ->where('id_requerimiento', $id)
            ->update(['estado' => 8]);

        return response()->json($req);
    }
    public function detalleRequerimiento($id_requerimiento)
    {
        $detalles = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.*',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'alm_prod.descripcion as producto_descripcion',
                'alm_prod.codigo as producto_codigo',
                'alm_und_medida.abreviatura',
                'alm_prod.part_number'
            )
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
            // ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            // ->leftJoin('almacen.alm_almacen as almacen_reserva','almacen_reserva.id_almacen','=','alm_det_req.id_almacen_reserva')
            ->where([
                ['alm_det_req.id_requerimiento', '=', $id_requerimiento],
                ['alm_det_req.estado', '!=', 7]
            ])
            ->get();

        return response()->json($detalles);
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
            $requerimiento->id_periodo = $request->periodo;
            $requerimiento->concepto = strtoupper($request->concepto);
            $requerimiento->id_moneda = $request->moneda;
            $requerimiento->id_proyecto = $request->id_proyecto;
            $requerimiento->observacion = $request->observacion;
            $requerimiento->id_grupo = $request->id_grupo;
            $requerimiento->id_area = $request->id_area;
            $requerimiento->id_prioridad = $request->prioridad;
            $requerimiento->fecha_registro = new Carbon();
            $requerimiento->estado = 1;
            $requerimiento->id_empresa = $request->empresa;
            $requerimiento->id_sede = $request->sede;
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
            $requerimiento->adjuntoOtrosAdjuntos = $request->archivoAdjuntoRequerimiento1;
            $requerimiento->adjuntoOrdenes = $request->archivoAdjuntoRequerimiento2;
            $requerimiento->adjuntoComprobanteBancario = $request->archivoAdjuntoRequerimiento3;
            $requerimiento->adjuntoComprobanteContable = $request->archivoAdjuntoRequerimiento4;

            $count = count($request->descripcion);
            
            for ($i = 0; $i < $count; $i++) {
                $detalle = new DetalleRequerimiento();
                $detalle->id_requerimiento = $requerimiento->id_requerimiento;
                $detalle->id_tipo_item = $request->tipoItem[$i];
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
                $detalle->idRegister = $request->idRegister[$i];
                $detalleArray[] = $detalle;
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

            $adjuntosRequerimiento = $this->guardarAdjuntoNivelRequerimiento($requerimiento);

            $adjuntoDetelleRequerimiento=[];
            for ($i = 0; $i < count($detalleArray); $i++) {
                $archivos=$request->{"archivoAdjuntoItem".$detalleArray[$i]['idRegister']};  
                if(isset($archivos)){
                    foreach ($archivos as $archivo) {
                        $adjuntoDetelleRequerimiento[]=[
                            'id_detalle_requerimiento'=>$detalleArray[$i]['id_detalle_requerimiento'],
                            'nombre_archivo'=>$archivo->getClientOriginalName(),
                            'archivo'=>$archivo
                        ];
                        // $adjuntoDetelleRequerimiento =DB::table('almacen.alm_det_req_adjuntos')->insertGetId(
                        //     [
                        //         'id_detalle_requerimiento'  => $detalleArray[$i]['id_detalle_requerimiento'],
                        //         'archivo'                   => $archivo->getClientOriginalName(),
                        //         'estado'                    => 1,
                        //         'fecha_registro'            => date('Y-m-d H:i:s')
                        //     ],
                        //     'id_adjunto'
                        // );
                        // Storage::disk('archivos')->put("logistica/requerimiento/" . $archivo->getClientOriginalName(), \File::get($archivo));
                    }
                }
            }
            if(count($adjuntoDetelleRequerimiento)>0){
                $this->guardarAdjuntoNivelDetalleItem($adjuntoDetelleRequerimiento);
            }


            DB::commit();
            // TODO: ENVIAR CORREO AL APROBADOR DE ACUERDO AL MONTO SELECCIONADO DEL REQUERIMIENTO
            return response()->json(['id_requerimiento' => $requerimiento->id_requerimiento, 'codigo' => $requerimiento->codigo, 'adjuntos'=>$adjuntosRequerimiento]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento' => 0, 'codigo' => '', 'mensaje' => 'Hubo un problema al guardar el requerimiento. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    public static function guardarAdjuntoNivelRequerimiento($requerimiento)
    {


        $adjuntoOtrosAdjuntosLength = $requerimiento->adjuntoOtrosAdjuntos != null ? count($requerimiento->adjuntoOtrosAdjuntos) : 0;
        $adjuntoOrdenesLength = $requerimiento->adjuntoOrdenes != null ? count($requerimiento->adjuntoOrdenes) : 0;
        $adjuntoComprobanteContableLength = $requerimiento->adjuntoComprobanteContable != null ? count($requerimiento->adjuntoComprobanteContable) : 0;
        $adjuntoComprobanteBancarioLength = $requerimiento->adjuntoComprobanteBancario != null ? count($requerimiento->adjuntoComprobanteBancario) : 0;


        if ($adjuntoOtrosAdjuntosLength > 0) {
            foreach ($requerimiento->adjuntoOtrosAdjuntos as $archivo) {

                $otrosAdjuntos = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                    [
                        'id_requerimiento'          => $requerimiento->id_requerimiento,
                        'archivo'                   => $archivo->getClientOriginalName(),
                        'estado'                    => 1,
                        'categoria_adjunto_id'      => 1,
                        'fecha_registro'            => date('Y-m-d H:i:s')
                    ],
                    'id_adjunto'
                );
                Storage::disk('archivos')->put("logistica/requerimiento/" . $archivo->getClientOriginalName(), \File::get($archivo));
            }
        }
        $ordenesAdjuntos=0;
        $comprobanteContableAdjuntos=0;
        $comprobanteBancarioAdjunto=0;
        $comprobanteBancarioAdjunto=0;

        if ($adjuntoOrdenesLength > 0) {
            foreach ($requerimiento->adjuntoOrdenes as $archivo) {

                $ordenesAdjuntos = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                    [
                        'id_requerimiento'          => $requerimiento->id_requerimiento,
                        'archivo'                   => $archivo->getClientOriginalName(),
                        'estado'                    => 1,
                        'categoria_adjunto_id'      => 2,
                        'fecha_registro'            => date('Y-m-d H:i:s')
                    ],
                    'id_adjunto'
                );
                Storage::disk('archivos')->put("logistica/requerimiento/" . $archivo->getClientOriginalName(), \File::get($archivo));
            }
        }
        if ($adjuntoComprobanteContableLength > 0) {
            foreach ($requerimiento->adjuntoComprobanteContable as $archivo) {

                $comprobanteContableAdjuntos = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                    [
                        'id_requerimiento'          => $requerimiento->id_requerimiento,
                        'archivo'                   => $archivo->getClientOriginalName(),
                        'estado'                    => 1,
                        'categoria_adjunto_id'      => 3,
                        'fecha_registro'            => date('Y-m-d H:i:s')
                    ],
                    'id_adjunto'
                );
                Storage::disk('archivos')->put("logistica/requerimiento/" . $archivo->getClientOriginalName(), \File::get($archivo));
            }
        }
        if ($adjuntoComprobanteBancarioLength > 0) {
            foreach ($requerimiento->adjuntoComprobanteBancario as $archivo) {

                $comprobanteBancarioAdjunto = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                    [
                        'id_requerimiento'          => $requerimiento->id_requerimiento,
                        'archivo'                   => $archivo->getClientOriginalName(),
                        'estado'                    => 1,
                        'categoria_adjunto_id'      => 4,
                        'fecha_registro'            => date('Y-m-d H:i:s')
                    ],
                    'id_adjunto'
                );
                Storage::disk('archivos')->put("logistica/requerimiento/" . $archivo->getClientOriginalName(), \File::get($archivo));
            }
        }


        return response()->json($ordenesAdjuntos);
    }

    public static function guardarAdjuntoNivelDetalleItem($adjuntoDetelleRequerimiento){
        $detalleAdjuntos=0;
        if(count($adjuntoDetelleRequerimiento) >0){
            foreach ($adjuntoDetelleRequerimiento as $adjunto) {
                $detalleAdjuntos =DB::table('almacen.alm_det_req_adjuntos')->insertGetId(
                    [
                        'id_detalle_requerimiento'  => $adjunto['id_detalle_requerimiento'],
                        'archivo'                   => $adjunto['nombre_archivo'],
                        'estado'                    => 1,
                        'fecha_registro'            => date('Y-m-d H:i:s')
                    ],
                    'id_adjunto'
                );
                Storage::disk('archivos')->put("logistica/detalle_requerimiento/" . $adjunto['nombre_archivo'], \File::get($adjunto['archivo']));
            }
        }
        return response()->json($detalleAdjuntos);
    }


    public function listarRequerimientosElaborados(Request $request)
    {
        $idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
        $idGrupo = $request->idGrupo;
        $idPrioridad = $request->idPrioridad;

        // $req     = array();
        // $det_req = array();

        $requerimientos = Requerimiento::leftJoin('administracion.adm_documentos_aprob', 'alm_req.id_requerimiento', '=', 'adm_documentos_aprob.id_doc')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'alm_req.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
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
                'sis_grupo.descripcion AS grupo',
                'adm_area.descripcion AS area',
                'sis_moneda.simbolo AS simbolo_moneda',
                DB::raw("CONCAT(pers.nombres,' ',pers.apellido_paterno,' ',pers.apellido_materno) as nombre_usuario")

            )
            ->where([['alm_req.estado', '!=', 7],['sis_sede.estado', '=', 1]])

            ->when((intval($idEmpresa)> 0), function($query)  use ($idEmpresa) {
                return $query->whereRaw('alm_req.id_empresa = '.$idEmpresa);
            })
            ->when((intval($idSede)> 0), function($query)  use ($idSede) {
                return $query->whereRaw('alm_req.id_sede = '.$idSede);
            })
            ->when((intval($idGrupo)> 0), function($query)  use ($idGrupo) {
                return $query->whereRaw('sis_grupo.id_grupo = '.$idGrupo);
            })
            ->when((intval($idPrioridad)> 0), function($query)  use ($idPrioridad) {
                return $query->whereRaw('alm_req.id_prioridad = '.$idPrioridad);
            });

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
        $gruposUsuario = Auth::user()->getAllGrupo();
        $grupos = Grupo::mostrar();
        $roles = Auth::user()->getAllRol(); //$this->userSession()['roles'];
        $empresas = Empresa::mostrar();
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();


        return view('logistica/requerimientos/lista_requerimientos', compact('periodos', 'gruposUsuario', 'grupos', 'roles', 'empresas', 'prioridades'));
    }


    public function viewAprobar(Request $request)
    {
        $gruposUsuario = Auth::user()->getAllGrupo();
        $grupos = Grupo::mostrar();
        $roles = Auth::user()->getAllRol();
        $empresas = Empresa::mostrar();
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();


        return view('logistica/requerimientos/aprobar_requerimiento', compact('periodos', 'gruposUsuario', 'grupos', 'roles', 'empresas', 'prioridades'));
    }


    public function listadoAprobacion(Request $request)
    {

        $idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
        $idGrupo = $request->idGrupo;
        $idPrioridad = $request->idPrioridad;
        $usuarioSoloSiCorrespondeAprobacion = false;
        // $compra =(new LogisticaController)->get_tipo_requerimiento('Compra');
        $tipo_requerimiento = 3; // Bienes y Servicios
        $tipo_documento = 1; // Requerimientos

        $requerimientos = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('almacen.tipo_cliente', 'alm_req.tipo_cliente', '=', 'tipo_cliente.id_tipo_cliente')
            ->leftJoin('almacen.alm_almacen', 'alm_req.id_almacen', '=', 'alm_almacen.id_almacen')
            ->leftJoin('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            // ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            // ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            // ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            // ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            // ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('configuracion.ubi_dis', 'alm_req.id_ubigeo_entrega', '=', 'ubi_dis.id_dis')
            ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('administracion.adm_periodo', 'alm_req.id_periodo', '=', 'adm_periodo.id_periodo')
            ->leftJoin('configuracion.sis_rol', 'alm_req.id_rol', '=', 'sis_rol.id_rol')
            ->leftJoin('administracion.adm_documentos_aprob', 'alm_req.id_requerimiento', '=', 'adm_documentos_aprob.id_doc')

            ->select(
                'alm_req.id_requerimiento',
                'adm_documentos_aprob.id_doc_aprob',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'sis_moneda.descripcion as desrcipcion_moneda',
                'alm_req.id_periodo',
                'adm_periodo.descripcion as descripcion_periodo',
                'alm_req.id_prioridad',
                'adm_prioridad.descripcion as descripcion_prioridad',
                'alm_req.estado',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.id_empresa',
                'alm_req.id_grupo',
                'sis_grupo.descripcion as descripcion_grupo',
                'contrib.razon_social as razon_social_empresa',
                'sis_sede.codigo as codigo_sede_empresa',
                'adm_empresa.logo_empresa',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_req.observacion',
                'alm_tp_req.descripcion AS tipo_requerimiento',
                'alm_req.id_usuario',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno)  AS persona"),
                'sis_usua.usuario',
                'alm_req.id_rol',
                'sis_rol.descripcion as descripcion_rol',
                // 'rrhh_rol.id_rol_concepto',
                // 'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                // 'adm_area.descripcion AS area_descripcion',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.fecha_registro',
                'alm_req.id_sede',
                'alm_req.tipo_cliente as id_tipo_cliente',
                'tipo_cliente.descripcion as descripcion_tipo_cliente',
                'alm_req.id_ubigeo_entrega',
                DB::raw("(ubi_dis.descripcion) || ' ' || (ubi_prov.descripcion) || ' ' || (ubi_dpto.descripcion)  AS name_ubigeo"),
                'alm_req.id_almacen',
                'alm_almacen.descripcion as descripcion_almacen',
                'alm_req.monto',
                'alm_req.fecha_entrega',
                'alm_req.rol_aprobante_id'
            )
            ->where([
                ['alm_req.id_tipo_requerimiento', '=', $tipo_requerimiento],
                // ['alm_req.codigo', '=','RC-210007'],
                // ['alm_req.tipo_cliente','=',$uso_administracion] // uso administracion
                ['alm_req.estado','!=',7] // elaborado
            ])
            ->when((intval($idEmpresa)> 0), function($query)  use ($idEmpresa) {
                return $query->whereRaw('alm_req.id_empresa = '.$idEmpresa);
            })
            ->when((intval($idSede)> 0), function($query)  use ($idSede) {
                return $query->whereRaw('alm_req.id_sede = '.$idSede);
            })
            ->when((intval($idGrupo)> 0), function($query)  use ($idGrupo) {
                return $query->whereRaw('sis_grupo.id_grupo = '.$idGrupo);
            })
            ->when((intval($idPrioridad)> 0), function($query)  use ($idPrioridad) {
                return $query->whereRaw('alm_req.id_prioridad = '.$idPrioridad);
            })
            ->orderBy('alm_req.fecha_registro', 'desc')
            ->get();

        // return $requerimientos;
        $payload = [];
        $operacion_selected = 0;
        $flujo_list_selected = [];

        $pendiente_aprobacion = [];

        $allGrupo = Auth::user()->getAllGrupo();

        foreach ($allGrupo as $grupo) {
            $id_grupo_list[] = $grupo->id_grupo; // lista de id_rol del usuario en sesion
        }
        $list_req = [];
        foreach ($requerimientos as $element) {
            if (in_array($element->id_grupo, $id_grupo_list) == true) {

                $id_doc_aprobacion_req = $element->id_doc_aprob;
                $id_grupo_req = $element->id_grupo;
                $id_tipo_requerimiento_req = $element->id_tipo_requerimiento;
                $id_prioridad_req = $element->id_prioridad;
                $estado_req = $element->estado;
                $rol_aprobante_id = $element->rol_aprobante_id;

                // $id_doc_aprobacion_req_list[]=$id_doc_aprobacion_req;
                $voboList = Aprobacion::getVoBo($id_doc_aprobacion_req); // todas las vobo
                $aprobaciones = [];
                if ($voboList['status'] == 200) {
                    foreach ($voboList['data'] as $vobo) {
                        $aprobaciones[] = $vobo; //lista de aprobaciones
                    }
                }

                // ##### obteniendo un array de id_flujos de aprobacion ###
                $id_flujo_array = [];
                $flujo_list_id_rol = [];
                $OrdenFlujoAprobacionesHechasList=[];
                foreach ($aprobaciones as $aprobacion) {
                    $id_flujo_array[] = $aprobacion->id_flujo;
                    $OrdenFlujoAprobacionesHechasList[] = $aprobacion->orden;

                }
                // #####
                // return $aprobaciones;

                // ### seleccionar la operacion que corrresponde el req segun grupo, tipo documento , prioridad
                // $prioridadList=['data'=>[],'status'=>400];
                $operaciones = Operacion::getOperacion('Requerimiento', $id_grupo_req, $id_prioridad_req);

                foreach ($operaciones['data'] as $operacion) {
                    if ($operacion->id_grupo == $id_grupo_req && $operacion->id_tp_documento == $tipo_documento) {
                        $operacion_selected = $operacion->id_operacion;
                        // ### si tiene agun criterio 
                        if ($operacion->id_grupo_criterios != null) { // accion si existe algun criterio
                            // $prioridadArrayList =(new AprobacionController)->getCriterioPrioridad($operacion->id_grupo_criterios);
                            // if($prioridadList['status']==200){
                            // if(count($prioridadList['data'] > 0)){
                            //  tiene criterio prioridad

                            // }
                            // return $prioridadArrayList;
                            // }
                            // $rangoMonto = $this->getCriterioMonto(); // only declared
                        }
                        // ##### seleccion de flujos    
                        $flujo_list = Flujo::getIdFlujo($operacion_selected);
                        // return $id_flujo_array;

                        $pendiente_aprobacion = [];
                        // $flujo_list_id_rol= [];
                        // return $pendiente_aprobacion;
                        //eliminando flujo ya aprobados
                        foreach ($flujo_list['data'] as $key => $object) {
                            $flujo_list_id_rol[] = $object->id_rol;
                            if (!in_array($object->id_flujo, $id_flujo_array) && !in_array($object->orden,$OrdenFlujoAprobacionesHechasList)) {
                                $pendiente_aprobacion[] = $object;
                            }
                        }

                        if ($rol_aprobante_id > 0) {
                            // Debugbar::info('rol_aprobante_id'.$rol_aprobante_id);

                            $numOrdenAprobante = 0;
                            foreach ($flujo_list['data'] as $key => $value) {
                                if ($value->id_rol == $rol_aprobante_id) {
                                    $numOrdenAprobante = $value->orden;
                                }
                            }
                            // Debugbar::info('numOrdenAprobante'.$numOrdenAprobante);

                            foreach ($flujo_list['data'] as $key => $value) {
                                if (($value->id_rol != $rol_aprobante_id) && ($value->orden == $numOrdenAprobante)) {
                                    // unset($flujo_list['data'][$key]);
                                    array_splice($flujo_list['data'], $key, 1);
                                }
                            }
                            foreach ($pendiente_aprobacion as $key => $value) {
                                if (($value->id_rol != $rol_aprobante_id) && ($value->orden == $numOrdenAprobante)) {
                                    // unset($pendiente_aprobacion[$key]);
                                    array_splice($pendiente_aprobacion, $key, 1);
                                }
                            }
                        }
                        // return $flujo_list_id_rol;
                        // $list_req[]=$flujo_list;
                        $observacion_list = [];
                        $observacion_list = Aprobacion::getObservaciones($element->id_doc_aprob);
                    }
                }

                $ordenFlujoReal=[];
                foreach ($flujo_list['data'] as $key => $value) {
                    if(!in_array($value->orden,$ordenFlujoReal)){
                        $ordenFlujoReal[]=$value->orden;
                    }
                }
                $TamañoFlujoReal= count($ordenFlujoReal);

                // filtar requerimientos para usuario en sesion 
                $allRol = Auth::user()->getAllRol();
                $id_rol_list = [];
                foreach ($allRol as $rol) {
                    $id_rol_list[] = $rol->id_rol; // lista de id_rol del usuario en sesion
                }
                // return $flujo_list;
                // if(count($pendiente_aprobacion)>0){
                // if(in_array($flujo_list['data']['id_rol'], $id_rol_list) == true){
                if (count($flujo_list_id_rol) > 0) { // si tiene flujo el id_rol

                    if($usuarioSoloSiCorrespondeAprobacion == true){ // solo donde le toca aprobacion
                        if (count(array_intersect($flujo_list_id_rol, $id_rol_list)) > 0 && in_array($pendiente_aprobacion[0]->id_rol,$id_rol_list) ) {
                            $payload[] = [
                                'id_requerimiento' => $element->id_requerimiento,
                                'id_doc_aprob' => $id_doc_aprobacion_req,
                                'id_tipo_requerimiento' => $element->id_tipo_requerimiento,
                                'tipo_requerimiento' => $element->tipo_requerimiento,
                                'id_tipo_cliente' => $element->id_tipo_cliente,
                                'descripcion_tipo_cliente' => $element->descripcion_tipo_cliente,
                                'id_prioridad' => $element->id_prioridad,
                                'descripcion_prioridad' => $element->descripcion_prioridad,
                                'id_periodo' => $element->id_periodo,
                                'descripcion_periodo' => $element->descripcion_periodo,
                                'codigo' => $element->codigo,
                                'concepto' => $element->concepto,
                                'id_empresa' => $element->id_empresa,
                                'razon_social_empresa' => $element->razon_social_empresa,
                                'codigo_sede_empresa' => $element->codigo_sede_empresa,
                                'logo_empresa' => $element->logo_empresa,
                                'id_grupo' => $element->id_grupo,
                                'descripcion_grupo' => $element->descripcion_grupo,
                                'fecha_requerimiento' => $element->fecha_requerimiento,
                                'observacion' => $element->observacion,
                                'name_ubigeo' => $element->name_ubigeo,
                                'id_moneda' => $element->id_moneda,
                                'desrcipcion_moneda' => $element->desrcipcion_moneda,
                                'monto' => $element->monto,
                                'fecha_entrega' => $element->fecha_entrega,
                                'id_usuario' => $element->id_usuario,
                                'id_rol' => $element->id_rol,
                                'descripcion_rol' => $element->descripcion_rol,
                                'usuario' => $element->usuario,
                                'persona' => $element->persona,
                                'id_almacen' => $element->id_almacen,
                                'descripcion_almacen' => $element->descripcion_almacen,
                                'cantidad_aprobados_total_flujo' => count($aprobaciones) . '/' . ($TamañoFlujoReal),
                                'aprobaciones' => $aprobaciones,
                                'pendiente_aprobacion' => $pendiente_aprobacion,
                                'id_rol_aprobacion_actual' => count($pendiente_aprobacion)>0?$pendiente_aprobacion[0]->id_rol:null,
                                'observaciones' => $observacion_list,
                                'estado' => $element->estado,
                                'estado_doc' => $element->estado_doc,
                                'rol_aprobante_id' => $element->rol_aprobante_id
                            ];
                        }
                        
                    }else{ // todo donde el usuario esta involuctado en el flujo
                        if (count(array_intersect($flujo_list_id_rol, $id_rol_list)) > 0 ) {
                            $payload[] = [
                                'id_requerimiento' => $element->id_requerimiento,
                                'id_doc_aprob' => $id_doc_aprobacion_req,
                                'id_tipo_requerimiento' => $element->id_tipo_requerimiento,
                                'tipo_requerimiento' => $element->tipo_requerimiento,
                                'id_tipo_cliente' => $element->id_tipo_cliente,
                                'descripcion_tipo_cliente' => $element->descripcion_tipo_cliente,
                                'id_prioridad' => $element->id_prioridad,
                                'descripcion_prioridad' => $element->descripcion_prioridad,
                                'id_periodo' => $element->id_periodo,
                                'descripcion_periodo' => $element->descripcion_periodo,
                                'codigo' => $element->codigo,
                                'concepto' => $element->concepto,
                                'id_empresa' => $element->id_empresa,
                                'razon_social_empresa' => $element->razon_social_empresa,
                                'codigo_sede_empresa' => $element->codigo_sede_empresa,
                                'logo_empresa' => $element->logo_empresa,
                                'id_grupo' => $element->id_grupo,
                                'descripcion_grupo' => $element->descripcion_grupo,
                                'fecha_requerimiento' => $element->fecha_requerimiento,
                                'observacion' => $element->observacion,
                                'name_ubigeo' => $element->name_ubigeo,
                                'id_moneda' => $element->id_moneda,
                                'desrcipcion_moneda' => $element->desrcipcion_moneda,
                                'monto' => $element->monto,
                                'fecha_entrega' => $element->fecha_entrega,
                                'id_usuario' => $element->id_usuario,
                                'id_rol' => $element->id_rol,
                                'descripcion_rol' => $element->descripcion_rol,
                                'usuario' => $element->usuario,
                                'persona' => $element->persona,
                                'id_almacen' => $element->id_almacen,
                                'descripcion_almacen' => $element->descripcion_almacen,
                                'cantidad_aprobados_total_flujo' => count($aprobaciones) . '/' . ($TamañoFlujoReal),
                                'aprobaciones' => $aprobaciones,
                                'pendiente_aprobacion' => $pendiente_aprobacion,
                                'id_rol_aprobacion_actual' => count($pendiente_aprobacion)>0?$pendiente_aprobacion[0]->id_rol:null,
                                'observaciones' => $observacion_list,
                                'estado' => $element->estado,
                                'estado_doc' => $element->estado_doc,
                                'rol_aprobante_id' => $element->rol_aprobante_id
                            ];
                        }
                    }
                }
                // }
            }
        }


        $output = ['data' => $payload];
        return $output;
    }

    public function listarSedesPorEmpresa($idEmpresa)
    {
        return Sede::listarSedesPorEmpresa($idEmpresa);
    }

 


    // botonera aprobar  
    function flujoAprobacion($req, $doc)
    {

        $cont = 1;
        $footer = '';

        $dataFinal = array();
        $alert = '<ul style="list-style: none; padding: 0;">';

        $dataFinal = $this->get_historial_aprobacion($req, $doc);

        foreach ($dataFinal as $value => $val) {
            $usu = $val['usuario'];
            $est = $val['estado'];
            $day = $val['fecha'];
            $obs = $val['obs'];
            $name_user = $val['nombre_usuario'];

            if (strtoupper($est) == 'ELABORADO') {
                $claseObs = 'alert-okc alert-okc-primary';
            } elseif (strtoupper($est) == 'OBSERVADO') {
                $claseObs = 'alert-okc alert-okc-warning';
            } elseif (strtoupper($est) == 'DENEGADO') {
                $claseObs = 'alert-okc alert-okc-danger';
            } elseif (strtoupper($est) == 'SUSTENTO') {
                $claseObs = 'alert-okc alert-okc-info';
            } elseif (strtoupper($est) == 'APROBADO') {
                $claseObs = 'alert-okc alert-okc-success';
            }


            $alert .=
                '<li class="' . $claseObs . '" style="padding: 5px; margin-bottom: 8px;">
            <strong>' . strtoupper($est) . ' - ' . $name_user . '</strong>
            <small>(' . date('d/m/Y H:i:s', strtotime($day)) . ')</small>
            <br>' . $obs . '
            </li>';

            $cont++;

            foreach ($val['detalle'] as $key => $value) {

                if (count($val['detalle'][$key]) > 0) {

                    $usu_sus = $value['usuario'];
                    $est_sus = $value['estado'];
                    $day_sus = $value['fecha'];
                    $obs_sus = $value['obs'];
                    $name_user_sus = $value['nombre_usuario'];

                    if (strtoupper($est_sus) == 'ELABORADO') {
                        $claseObs = 'alert-okc alert-okc-primary';
                    } elseif (strtoupper($est_sus) == 'OBSERVADO') {
                        $claseObs = 'alert-okc alert-okc-warning';
                    } elseif (strtoupper($est_sus) == 'DENEGADO') {
                        $claseObs = 'alert-okc alert-okc-danger';
                    } elseif (strtoupper($est_sus) == 'SUSTENTO') {
                        $claseObs = 'alert-okc alert-okc-info';
                    } elseif (strtoupper($est_sus) == 'APROBADO') {
                        $claseObs = 'alert-okc alert-okc-success';
                    }

                    $alert .=
                        '<li class="' . $claseObs . '" style="padding: 5px; margin-bottom: 8px;">
                    <strong>' . strtoupper($est_sus) . ' - ' . $name_user_sus . '</strong>
                    <small>(' . date('d/m/Y H:i:s', strtotime($day_sus)) . ')</small>
                    <br>' . $obs_sus . '
                </li>';
                }
            }
        }
        $estado_req = $this->consult_estado($req); // get id_estado_doc
        // $totalFlujo = $this->totalAprobOp(1);
        // $totalAprob = $this->consult_aprob($doc); // cantidad aprobaciones


        $id_grupo = $this->get_id_grupo($req);

        $num_doc = $this->consult_doc_aprob($req, 1);
        $total_aprob = $this->consult_aprob($num_doc);
        $total_flujo = $this->consult_tamaño_flujo($req);
        $areaOfRolAprob = $this->getAreaOfRolAprob($num_doc, 1); //{num doc},{tp doc} 

        $tp_doc = 1; // tipo de documento = requerimiento 
        $id_operacion = $this->get_id_operacion($id_grupo, $areaOfRolAprob['id'], $tp_doc);

        // $sgt_aprob='-';
        // $sgt_per='-';

        if ($estado_req == 12) {
            if ($total_aprob > 0) {
                if ($total_flujo > $total_aprob) {
                    $sgt_aprob = ($total_aprob + 1);
                    $sgt_per = $this->consult_sgt_aprob($sgt_aprob, $id_operacion);
                    $footer .= '<strong>Próximo en aprobar: </strong>' . $sgt_per;
                }
            }
        } elseif ($estado_req == 3) {
            $usuario_crea = $this->consult_usuario_elab($req);
            $usu_elab = Usuario::find($usuario_crea)->trabajador->postulante->persona->nombre_completo;
            $footer .= '<strong>Por sustentar </strong>' . $usu_elab;
        } elseif ($estado_req == 13) {
            if ($total_flujo > $total_aprob) {
                $sgt_aprob = ($total_aprob + 1);
                $sgt_per = $this->consult_sgt_aprob($sgt_aprob, $id_operacion);
                $footer .= '<strong>Próximo en aprobar: </strong>' . $sgt_per;
            }
        } elseif ($estado_req == 1) {
            $PrimeraApro = $this->consulta_req_primera_aprob($req);
            $usuPrimeraApro = $PrimeraApro['nombre'];
            $rolPrimeraApro = $PrimeraApro['id_rol'];
            $nameUserPrimeraApro = $this->consulta_nombre_usuario($rolPrimeraApro);
            $json = json_decode($nameUserPrimeraApro);
            $allnameUserPrimeraApro = implode(", ", array_map(function ($obj) {
                foreach ($obj as $p => $v) {
                    return $v;
                }
            }, $json));

            $footer = '<strong>Pendiente </strong><abbr title="' . $allnameUserPrimeraApro . '">' . $usuPrimeraApro . '</abbr>';
        }

        // if($sql3->first()->observacion != null){
        //     $footer .= ' <strong> Por Aceptar Sustento:</strong> Logistica' ;
        // }

        $reqs = $this->mostrar_requerimiento_id($req, 2);

        $data = ['flujo' => $alert, 'siguiente' => $footer, 'requerimiento' => $reqs, 'cont' => $cont];
        return response()->json($data);
    }

    public function get_historial_aprobacion($req)
    {

        $doc = $this->consult_doc_aprob($req, 1);

        $new_data = array();
        $dataFinal = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();

        $req_elaborado = DB::table('almacen.alm_req')
            ->where('id_requerimiento', '=', $req)
            ->get();
        $cant_req_elaborado = $req_elaborado->count();

        if ($cant_req_elaborado > 0) {
            foreach ($req_elaborado as $row) {
                $id_us = $row->id_usuario;
                $fechae = $row->fecha_registro;
                $data1[] = array(
                    'estado' => 'ELABORADO', 'usuario' => $id_us, 'fecha' => $fechae, 'obs' => '',
                    'nombre_usuario' => Usuario::find($row->id_usuario)->trabajador->postulante->persona->nombre_completo,
                    'detalle' => []
                );
            }
        }



        $req_aprobado = DB::table('administracion.adm_aprobacion')
            ->join('administracion.adm_vobo', 'adm_vobo.id_vobo', '=', 'adm_aprobacion.id_vobo')
            ->select('adm_aprobacion.*', 'adm_vobo.descripcion AS vobo')
            ->where('adm_aprobacion.id_doc_aprob', '=', $doc)->get();

        $cant_req_aprob = $req_aprobado->count();

        if ($cant_req_aprob > 0) {
            foreach ($req_aprobado as $key) {
                $id_usua = $key->id_usuario;
                $my_vobo = $key->vobo;
                $fechavb = $key->fecha_vobo;
                $det_obs = $key->detalle_observacion;
                $data2[] = array(
                    'estado' => $my_vobo, 'usuario' => $id_usua, 'fecha' => $fechavb,
                    'nombre_usuario' => Usuario::find($key->id_usuario)->trabajador->postulante->persona->nombre_completo,
                    'obs' => $det_obs, 'detalle' => []
                );
            }
        }

        $dataFinal = array_merge($data1, $data2, $new_data);
        $date = array();
        foreach ($dataFinal as $row) {
            $date[] = $row['fecha'];
        }
        array_multisort($date, SORT_ASC, $dataFinal);

        return $dataFinal;
    }

    function consult_estado($req)
    {
        $sql = DB::table('almacen.alm_req')->select('estado')->where('id_requerimiento', $req)->first();
        return $sql->estado;
    }

    function get_id_grupo($req)
    {
        $sql = DB::table('almacen.alm_req')
            ->where([['id_requerimiento', '=', $req]])
            ->get();
        if ($sql->count() > 0) {
            $id_grupo = $sql->first()->id_grupo;
        } else {
            $id_grupo = 0;
        }
        return $id_grupo;
    }

    function consult_doc_aprob($id_doc, $tp_doc)
    {
        $sql = DB::table('administracion.adm_documentos_aprob')->where([['id_tp_documento', '=', $tp_doc], ['id_doc', '=', $id_doc]])->get();

        if ($sql->count() > 0) {
            $val = $sql->first()->id_doc_aprob;
        } else {
            $val = 0;
        }

        return $val;
    }

    function consult_aprob($doc)
    {
        $sql = DB::table('administracion.adm_aprobacion')->where([['id_vobo', '=', 1], ['id_doc_aprob', '=', $doc]])->get();
        return $sql->count();
    }
    public function get_id_tipo_documento($descripcion)
    {
        $adm_tp_docum = DB::table('administracion.adm_tp_docum')
            ->select('adm_tp_docum.*')
            ->where('descripcion', 'like', '%' . $descripcion)
            ->get()->first()->id_tp_documento;

        return $adm_tp_docum;
    }
    function consult_tamaño_flujo($id_req)
    {
        $id_tipo_doc = $this->get_id_tipo_documento('Requerimiento');

        $req = DB::table('almacen.alm_req')
            ->where([
                ['id_requerimiento', '=', $id_req]
            ])
            ->first();
        // $id_prioridad = $req->id_prioridad;
        $id_prioridad = 1;
        $id_grupo = isset($req->id_grupo) ? $req->id_grupo : 0;
        $id_area = isset($req->id_area) ? $req->id_area : 0;

        $sql_operacion = DB::table('administracion.adm_operacion')
            ->where([
                ['id_grupo', '=', $id_grupo],
                ['id_tp_documento', '=', 1],
                ['estado', '=', 1]
            ])
            ->get();
        if ($sql_operacion->count() > 0) {
            $id_operacion = $sql_operacion->first()->id_operacion;
        } else {
            $id_operacion = 0;
        }

        $flujo = DB::table('administracion.adm_flujo')->where([
            ['id_operacion', '=', $id_operacion],
            ['estado', '=', 1]
        ])
            ->get();

        return $flujo->count();
    }
    public function get_id_doc($id_doc_aprob, $tp_doc)
    {
        $sql = DB::table('administracion.adm_documentos_aprob')
            ->where([
                ['id_tp_documento', '=', $tp_doc],
                ['id_doc_aprob', '=', $id_doc_aprob]
            ])
            ->get();

        if ($sql->count() > 0) {
            $val = $sql->first()->id_doc;
        } else {
            $val = 0;
        }
        return $val;
    }
    public function get_id_rol_req($id_req)
    {
        $sql = DB::table('almacen.alm_req')
            ->where([['id_requerimiento', '=', $id_req]])
            ->get();
        if ($sql->count() > 0) {
            $val = $sql->first()->id_rol;
        } else {
            $val = 0;
        }
        return $val;
    }

    public function get_id_area_rol($id_rol_req)
    {
        $sql = DB::table('administracion.rol_aprobacion')
            ->where([['id_rol_aprobacion', '=', $id_rol_req]])
            ->get();
        if ($sql->count() > 0) {
            $val = $sql->first()->id_area;
        } else {
            $val = 0;
        }
        return $val;
    }
    public function getAreaOfRolAprob($id_doc_aprob, $tp_doc)
    {

        $id_area_rol = 0;
        $msg = 'OK';

        switch ($tp_doc) {
            case '1': //requerimiento
                # code...
                $id_req = $this->get_id_doc($id_doc_aprob, 1);
                if ($id_req == 0) {
                    $msg = 'error id_req';
                } else {
                    $id_rol_req = $this->get_id_rol_req($id_req);
                    if ($id_rol_req == 0) {
                        $msg = 'error id_rol_req';
                    } else {
                        $id_area_rol = $this->get_id_area_rol($id_rol_req);
                        if ($id_area_rol == 0) {
                            $msg = 'error id_area_rol';
                        }
                    }
                }

                break;

            case '2': //orden
                # code...
                $id_orden = $this->get_id_doc($id_doc_aprob, 2);

                if ($id_orden == 0) {
                    $msg = 'error id_orden';
                } else {
                    $dataReq = $this->get_data_req_by_id_orden($id_orden);
                    if (count($dataReq) == 0) {
                        $msg = 'error dataReq';
                    } else {
                        $id_area_rol = array_unique($dataReq['data']['rol']);
                        if (count($id_area_rol) == 0) {
                            $msg = 'error id_area_rol';
                        }
                    }
                }
                break;

            default:
                # code...
                break;
        }

        $array = array('id' => $id_area_rol, 'msg' => $msg);
        return $array;
    }

    function get_id_operacion($id_grp, $id_area, $tp_doc)
    {
        $filterBy = [];
        if ($id_area == 0 && $id_grp > 0) {
            $filterBy = [['adm_operacion.id_grupo', '=', $id_grp]];
        } else if ($id_area > 0 && $id_grp > 0) {
            $filterBy = [['id_area', '=', $id_area], ['id_grupo', '=', $id_grp]];
        }

        $sql = DB::table('administracion.adm_operacion')
            ->where([
                $filterBy[0],
                ['id_tp_documento', '=', $tp_doc],
                ['estado', '=', 1]
            ])
            ->get();
        if ($sql->count() > 0) {
            $operacion = $sql->first()->id_operacion;
        } else {
            $operacion = 0;
        }
        return $operacion;
    }

    function consult_sgt_aprob($orden, $operacion)
    {
        $sql = DB::table('administracion.adm_flujo')
            ->select('adm_flujo.id_rol')
            ->where([['id_operacion', '=', $operacion], ['orden', '=', $orden], ['estado', '=', 1]])
            ->get();


        if (count($sql) > 0) {
            $trab = DB::table('configuracion.sis_usua')
                ->select('rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno', 'sis_rol.descripcion AS rol')
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('configuracion.sis_acceso', 'sis_acceso.id_usuario', '=', 'sis_usua.id_usuario')
                ->join('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'sis_acceso.id_rol')
                ->where('sis_acceso.id_rol', $sql->first()->id_rol)->first();
            $nombre = $trab->nombres . ' ' . $trab->apellido_paterno . ' - ' . $trab->rol;
        } else {
            $nombre = '';
        }
        return $nombre;
    }

    function consult_usuario_elab($req)
    {
        $sql = DB::table('almacen.alm_req')->select('id_usuario')->where('id_requerimiento', $req)->first();
        return $sql->id_usuario;
    }

    function consulta_req_primera_aprob($req)
    {
        $id_tipo_doc = $this->get_id_tipo_documento('Requerimiento');
        $message = '';
        $statusOption = ['success', 'fail'];
        $status = '';
        $output = [];

        $sql1 = DB::table('almacen.alm_req')->select('id_grupo', 'id_area', 'rol_aprobante_id')->where('id_requerimiento', $req)->get();
        if (sizeof($sql1) > 0) {
            $sql11 = DB::table('administracion.adm_operacion')->where([['id_grupo', $sql1->first()->id_grupo], ['id_tp_documento', $id_tipo_doc], ['estado', 1]])->get();
            if (sizeof($sql11) > 0) {
                if ($sql1->first()->rol_aprobante_id > 0) {

                    $sql2 = DB::table('administracion.adm_flujo')->where([['id_operacion', $sql11->first()->id_operacion], ['id_rol', $sql1->first()->rol_aprobante_id], ['estado', 1]])
                        ->orderby('orden', 'asc')
                        ->get();
                } else {

                    $sql2 = DB::table('administracion.adm_flujo')->where([['id_operacion', $sql11->first()->id_operacion], ['estado', 1]])
                        ->orderby('orden', 'asc')
                        ->get();
                }

                $nombre = ($sql2->count() > 0) ? $sql2->first()->nombre : '';
                $id_rol = ($sql2->count() > 0) ? $sql2->first()->id_rol : '';
                $status = $statusOption[0];
                $array = array('nombre' => $nombre, 'id_rol' => $id_rol, 'status' => $status, 'message' => 'Flujo Encontrado');

                return $array;
            } else {
                $message = 'No existe id operacion con id_area=' . $sql1->first()->id_area . ',id_grupo=' . $sql1->first()->id_grupo;
                $status = $statusOption[1];
                $output = ['message' => $message, 'status' => $status];

                $sql111 = DB::table('administracion.adm_operacion')->where([['id_grupo', $sql1->first()->id_grupo], ['id_area', null], ['id_tp_documento', 1], ['estado', 1]])->get();
                if (sizeof($sql111) > 0) {
                    $sql2 = DB::table('administracion.adm_flujo')->where([['id_operacion', $sql111->first()->id_operacion], ['estado', 1]])
                        ->orderby('orden', 'asc')
                        ->get();

                    $nombre = ($sql2->count() > 0) ? $sql2->first()->nombre : '';
                    $id_rol = ($sql2->count() > 0) ? $sql2->first()->id_rol : '';
                    $status = $statusOption[0];
                    $array = array('nombre' => $nombre, 'id_rol' => $id_rol, 'status' => $status, 'message' => 'Flujo Encontrado');

                    return $array;
                } else {
                    $message = 'No existe id operacion con id_area= null, id_grupo=' . $sql1->first()->id_grupo;
                    $status = $statusOption[1];
                    $output = ['message' => $message, 'status' => $status];
                }


                return $output;
            }
        } else {
            $message = 'No existe id requerimiento';
            $status = $statusOption[1];
            $output = ['message' => $message, 'status' => $status];
            return $output;
        }
    }


    function consulta_nombre_usuario($id_rol)
    {
        $query = DB::table('administracion.rol_aprobacion')
            ->select(
                DB::raw("concat(rrhh_perso.nombres,' ', rrhh_perso.apellido_paterno, ' ',rrhh_perso.apellido_materno)  AS nombre_completo")
            )


            ->when(($id_rol > 0), function ($query) use ($id_rol) {
                return $query->Where('rol_aprobacion.id_rol_concepto', '=', $id_rol);
            })
            ->where([
                ['rol_aprobacion.estado', '=', 1]
            ])
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rol_aprobacion.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')

            ->orderby('rol_aprobacion.id_rol_aprobacion', 'desc')
            ->get();
        return $query;
    }

    public function mostrar_nombre_grupo($id_grupo)
    {
        $sql = DB::table('administracion.adm_grupo')
            ->select('adm_grupo.id_grupo', 'adm_grupo.descripcion')
            ->where('adm_grupo.id_grupo', $id_grupo)
            ->get();


        if ($sql->count() > 0) {
            $id_grupo = $sql->first()->id_grupo;
            $descripcion = $sql->first()->descripcion;
        } else {
            $id_grupo = 0;
            $descripcion = '';
        }
        $array = array('id_grupo' => $id_grupo, 'descripcion' => $descripcion);
        return $array;
    }

    function consult_moneda($id)
    {
        $sql = DB::table('configuracion.sis_moneda')
            ->select('descripcion', 'simbolo')
            ->where('id_moneda', '=', $id)->first();

        return $sql;
    }
    function mostrar_requerimiento_id($id, $type)
    {
        $sql = DB::table('almacen.alm_req')
            ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'alm_req.id_periodo')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'alm_req.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'alm_req.id_sede', '=', 'sis_sede.id_sede')
            ->leftJoin('administracion.adm_empresa', 'sis_sede.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->select(
                'alm_req.*',
                'adm_periodo.descripcion as descripcion_periodo',
                'adm_estado_doc.estado_doc',
                'alm_tp_req.descripcion AS tipo_requerimiento',
                'adm_prioridad.descripcion AS priori',
                'sis_grupo.descripcion AS grupo',
                'adm_area.descripcion AS area',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                'alm_req.estado',
                'adm_contri.razon_social',
                'sis_sede.codigo as codigo_sede'
            )
            ->where('alm_req.id_requerimiento', '=', $id)
            ->orderBy('alm_req.fecha_registro', 'desc')
            ->get();
        $html = '';

        foreach ($sql as $row) {
            $code = $row->codigo;
            $motivo = $row->concepto;
            $empresa_sede = $row->razon_social . ' - ' . $row->codigo_sede;
            $id_usu = $row->id_usuario;
            $grupo = $row->id_grupo;
            $area_id = $row->id_area;
            $id_op_com = null;
            $date = date('d/m/Y', strtotime($row->fecha_requerimiento));
            $id_periodo = $row->id_periodo;
            $descripcion_periodo = $row->descripcion_periodo;
            $moneda = $row->id_moneda ? $row->id_moneda : 1;
            $estado_doc = $row->estado_doc;

            $infoGrupo = $this->mostrar_nombre_grupo($grupo);

            if ($infoGrupo['descripcion'] == 'Proyectos') {
                if ($id_op_com != null) {
                    $destino = null;
                } else {
                    $destino = $row->area . ' - GASTOS ADMINISTRATIVOS';
                }
            } else {
                if ($area_id != 6) {
                    $destino = $row->area;
                } else {
                    $destino = $row->area . ' - ' . $row->occ;
                }
            }

            $responsable = Usuario::find($id_usu)->trabajador->postulante->persona->nombre_completo;
            $simbolMoneda = $this->consult_moneda($moneda)->simbolo;
            $descripcionMoneda = $this->consult_moneda($moneda)->descripcion;
        }

        $html =
            '<table width="100%">
            <thead>
                <tr>
                    <th width="140">Código:</th>
                    <td>' . $code . '</td>
                </tr>
                <tr>
                    <th width="140">Motivo:</th>
                    <td>' . $motivo . '</td>
                </tr>
                <tr>
                    <th width="140">Empresa:</th>
                    <td>' . $empresa_sede . '</td>
                </tr>
                <tr>
                    <th width="140">Responsable:</th>
                    <td>' . $responsable . '</td>
                </tr>
                <tr>
                    <th>Area o Servicio:</th>
                    <td>' . $destino . '</td>
                </tr>';
        if ($destino == 'COMERCIAL') {
            $html .= '<tr>
                                <th>OCC:</th>
                                <td></td>
                            </tr>';
        }

        $html .= '<tr>
                    <th>Fecha:</th>
                    <td colspan="2">' . $date . '</td>
                </tr>
                <tr>';
        if ($type == 1) {
            $html .=
                '<th>Moneda:</th>
                    <td>' . $descripcionMoneda . '</td>';
        } elseif ($type == 2) {
            $html .=
                '<th>Moneda:</th>
                    <td>' . $descripcionMoneda . '</td>
                    <td width="100" align="right"><button class="btn btn-primary" onClick="imprimirReq(' . $id . ');"><i class="fas fa-print"></i> Imprimir formato</button></td>
                    <td>&nbsp;</td>
                    <td width="100" align="right"><button class="btn btn-info" onClick="verArchivosAdjuntosRequerimiento(' . $id . ');"><i class="fas fa-folder"></i> Archivos Adjuntos</button></td>';
        }
        $html .=
            '</tr>
            <tr>
                <th>Periodo:</th>
                <td colspan="2">' . $descripcion_periodo . '</td>
            </tr>
            <tr>
                <th>Estado:</th>
                <td colspan="2">' . $estado_doc . '</td>
            </tr>
            </thead>
        </table>
        <br>
        <table class="table table-bordered table-striped table-view-okc" width="100%">';
        if ($type == 1) {
            $html .=
                '<thead style="background-color:#5c5c5c; color:#fff;">
                    <th>Código</th>
                    <th>Part.No</th>
                    <th>Descripción del Bien o Servicio</th>
                    <th width="150">Partida</th>
                    <th width="90">Fecha Entrega</th>
                    <th width="90">Unidad</th>
                    <th width="100">Cantidad</th>
                    <th width="100">Precio Unit.</th>
                    <th width="110">Subtotal</th>
                </thead>
                <tbody>';
        } elseif ($type == 2) {
            $html .=
                '<thead style="background-color:#5c5c5c; color:#fff;">
                    <th>Código</th>
                    <th>Part.No</th>
                    <th>Descripción del Bien o Servicio</th>
                    <th width="150">Partida</th>
                    <th width="90">Fecha Entrega</th>
                    <th width="90">Unidad</th>
                    <th width="100">Cantidad</th>
                    <th width="100">Precio Unit.</th>
                    <th width="110">Subtotal</th>
                </thead>
                <tbody>';
        }

        $cont = 1;
        $total = 0;

        $detail = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*', 'alm_prod.codigo', 'alm_prod.part_number', 'alm_prod.descripcion as descripcion_producto', 'alm_und_medida.descripcion as unidad_medida_descripcion')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')

            ->where('id_requerimiento', $id)
            ->get();

        foreach ($detail as $clave => $det) {
            $id_det = $det->id_detalle_requerimiento;
            $codigo_producto = $det->codigo;
            $part_number = $det->part_number;
            $id_item = $det->id_item;
            $id_producto = $det->id_producto;
            $precio = $det->precio_unitario;
            $cant = $det->cantidad;
            $id_part = $det->partida;
            $tiene_transformacion = $det->tiene_transformacion;
            $unit = $det->unidad_medida_descripcion;
            // $simbMoneda = $this->consult_moneda($det->id_moneda)->simbolo;
            $descripcion_adicional = $det->descripcion_adicional;

            $active = '';

            if (is_numeric($id_part)) {
                $name_part = DB::table('finanzas.presup_par')->select('codigo')->where('id_partida', $id_part)->first();
                $partida = $name_part->codigo;
            } else {
                $partida = ''/*$id_part*/;
            }

            $subtotal = $precio * $cant;
            $total += $subtotal;
            $unidad = 'S/N';
            if ($id_producto == null) {
                $name = $descripcion_adicional;
                $unidad = 'Servicio';
            } else {
                $name = $det->descripcion_producto;
            }

            // if ($obs == 't' or $obs == '1' or $obs == 'true') {
            //     $active = 'checked="checked" disabled';
            // }

            if ($type == 1) {
                $html .=
                    '<tr>
                    <td> ' . ($codigo_producto ? $codigo_producto : '') . '</td>
                    <td> ' . ($part_number ? $part_number : '') . ($tiene_transformacion > 0 ? '<br><span style="display: inline-block; font-size: 8px; background:#ddd; color: #666; border-radius:8px; padding:2px 10px;">Transformado</span>' : '') . '</td>
                    <td>' . $name . '</td>
                    <td>' . $partida . '</td>
                    <td></td>
                    <td>' . $unit . '</td>
                    <td class="text-right">' . number_format($cant, 3) . '</td>
                    <td class="text-right">' . $simbolMoneda . number_format($precio, 2) . '</td>
                    <td class="text-right">' . $simbolMoneda . number_format($subtotal, 2) . '</td>
                </tr>';
            } elseif ($type == 2) {
                $html .=
                    '<tr>
                    <td> ' . ($codigo_producto ? $codigo_producto : '') . '</td>
                    <td> ' . ($part_number ? $part_number : '') . ($tiene_transformacion > 0 ? '<br><span style="display: inline-block; font-size: 8px; background:#ddd; color: #666; border-radius:8px; padding:2px 10px;">Transformado</span>' : '') . '</td>
                    <td>' . $name . '</td>
                    <td>' . $partida . '</td>
                    <td></td>
                    <td>' . ($unit ? $unit : $unidad) . '</td>
                    <td class="text-right">' . number_format($cant, 3) . '</td>
                    <td class="text-right">' . $simbolMoneda . number_format($precio, 2) . '</td>
                    <td class="text-right">' . $simbolMoneda . number_format($subtotal, 2) . '</td>
                </tr>';
            }

            $cont++;
        }

        $html .=
            '<tr>
            <th colspan="7" class="text-right">Total:</th>
            <td class="text-right">' . $simbolMoneda . number_format($total, 2) . '</td>
        </tr>
        </tbody></table>';

        // if ($type == 1){
        //     return response()->json($html);
        // }elseif ($type == 2){
        //     return $html;
        // }
        return $html;
    }



    // tracking requerimiento 

    public function explorar_requerimiento($id_requerimiento)
    {
        $requerimiento = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('administracion.rol_aprobacion', 'alm_req.id_rol', '=', 'rol_aprobacion.id_rol_aprobacion')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rol_aprobacion.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')

            // ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
            // ->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            // ->leftJoin('almacen.guia_com_oc', 'guia_com_oc.id_oc', '=', 'log_ord_compra.id_orden_compra')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion AS tipo_req_desc',
                'sis_usua.usuario',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_responsable"),

                'alm_req.id_area',
                'adm_area.descripcion AS area_desc',
                'rol_aprobacion.id_rol_aprobacion as id_rol',
                'rol_aprobacion.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_grupo',
                'adm_grupo.descripcion AS adm_grupo_descripcion',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                'alm_req.fecha_registro',
                'alm_req.id_prioridad',
                'alm_req.estado',
                'adm_estado_doc.estado_doc',
                'alm_req.estado'
            )
            ->where([
                ['alm_req.id_requerimiento', '=', $id_requerimiento]
            ])
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();

        // $id_prioridad= $requerimiento->first()->id_prioridad;
        $id_prioridad = 1;
        $tipo_documento = 1;
        $id_grupo = $requerimiento->first()->id_grupo;
        // $id_area= $requerimiento->first()->id_area;

        $num_doc = $this->consult_doc_aprob($id_requerimiento, 1);
        // $id_operacion=$this->get_id_operacion($id_grupo,$id_area,$tipo_documento);
        $areaOfRolAprob = $this->getAreaOfRolAprob($num_doc, 1); //{num doc},{tp doc} 

        $id_operacion = $this->get_id_operacion($id_grupo, $areaOfRolAprob['id'], $tipo_documento);

        // get flujo aprobación
        $flujo_aprobacion = $this->get_flujo_aprobacion($id_operacion, $areaOfRolAprob['id']);
        // $flujo_aprobacion = $id_operacion;
        // Lista de historial aprobación
        $historial_aprobacion = $this->get_historial_aprobacion($id_requerimiento);
        // lista de Solicitud de Cotización
        // $solicitud_de_cotizaciones = $this->get_cotizacion_by_req($id_requerimiento);
        $solicitud_de_cotizaciones = [];
        // Lista de Cuadros Comparativo
        $cuadros_comparativos = [];
        // $cuadros_comparativos = $this->get_cuadro_comparativo_by_req($id_requerimiento);
        //lista de ordenes
        // $ordenes = $this->get_orden_by_req($id_requerimiento);
        $ordenes = [];

        // salida
        $output = [
            'requerimiento' => $requerimiento,
            'flujo_aprobacion' => $flujo_aprobacion,
            'historial_aprobacion' => $historial_aprobacion,
            'solicitud_cotizaciones' => $solicitud_de_cotizaciones,
            'cuadros_comparativos' => $cuadros_comparativos,
            'ordenes' => $ordenes
        ];

        return response()->json($output);
    }

    public function get_flujo_aprobacion($id_operacion, $id_area)
    {
        $adm_flujo_aprobacion = DB::table('administracion.adm_flujo')
            ->select(
                'adm_flujo.id_flujo',
                'adm_flujo.id_operacion',
                'adm_flujo.id_rol',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_responsable"),
                // 'rol_aprobacion.id_area',
                'sis_rol.descripcion as descripcion_rol',
                'adm_flujo.nombre as nombre_fase',
                'adm_flujo.orden',
                'adm_flujo.estado'
            )
            ->leftJoin('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'adm_flujo.id_rol')
            ->leftJoin('configuracion.sis_acceso', 'sis_acceso.id_rol', '=', 'sis_rol.id_rol')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'sis_acceso.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where([
                ['adm_flujo.estado', '=', 1],
                // ['rol_aprobacion.estado', '=', 1],
                // ['rol_aprobacion.id_area', '=', $id_area],
                ['adm_flujo.id_operacion', '=', $id_operacion]
            ])
            ->orderBy('adm_flujo.orden', 'asc')
            ->get();
        // return $adm_flujo_aprobacion;
        $flujo_aprobacion = [];
        $id_flujo_list = [];

        foreach ($adm_flujo_aprobacion as $data) {

            $id_flujo_list[] = $data->id_flujo;

            $flujo_aprobacion[] = [
                'id_flujo' => $data->id_flujo,
                'nombre_fase' => $data->nombre_fase,
                'id_operacion' => $data->id_operacion,
                'id_rol' => $data->id_rol,
                // 'id_area'=>$data->id_area,
                'nombre_responsable' => $data->nombre_responsable,
                'descripcion_rol' => $data->descripcion_rol,
                'orden' => $data->orden,
                'estado' => $data->estado,
                'criterio_monto' => [],
                'criterio_prioridad' => []
            ];
        }


        return $flujo_aprobacion;
    }



    function aprobarDocumento(Request $request)
    {
        $id_doc_aprob = $request->id_doc_aprob;
        $detalle_observacion = $request->detalle_observacion;
        $id_rol = $request->id_rol;
        $id_vobo = VoBo::getIdVoBo('Aprobado');
        $id_usuario = Auth::user()->id_usuario;

        $status = '';
        $message = '';

        // ### determinar flujo , tamaño de flujo
        $flujo = Documento::getFlujoByIdDocumento($id_doc_aprob);
        $id_req = Documento::getIdDocByIdDocAprob($id_doc_aprob);

        $sql_req = DB::table('almacen.alm_req')->select('rol_aprobante_id')->where('id_requerimiento', $id_req)->get();
        if (count($sql_req) > 0) {
            if ($sql_req->first()->rol_aprobante_id > 0) {
                foreach ($flujo as $value) {
                    if ($sql_req->first()->rol_aprobante_id == $value->id_rol) {
                        $numOrdenAprobante = $value->orden;
                    }
                }

                foreach ($flujo as $key => $value) {
                    if (($value->id_rol != $sql_req->first()->rol_aprobante_id) && ($value->orden == $numOrdenAprobante)) {

                        array_splice($flujo, $key, 1);
                    }
                }
            }
        }

        $tamaño_flujo = count($flujo);

        $aprobaciones = Aprobacion::getVoBo($id_doc_aprob);
        $aprobacionList = $aprobaciones['data'];
        $cantidad_aprobaciones = count($aprobacionList);
        // ### tiene aprobaciones? cantidad de aprobaciones realizadas?
        // ### si tiene aprobaciones determinar si es la ultima aprobacion
        // return $aprobacionList;




        // obtener todo los roles del usuario
        $idRolUsuarioList=[];
        $allRol = Auth::user()->getAllRol();
        foreach ($allRol as  $rol) {
            $idRolUsuarioList[]=$rol->id_rol;
            # code...
        }

        // examinar el el flujo el rol que coiciden con el usuario
        $idFlujoUsuarioApruebaList[] = Documento::searchIdFlujoPorIdRol($flujo, $allRol);

        // aprobaciones pendientes
        $idFlujoAprobacionesHechasList=[];
        $OrdenFlujoAprobacionesHechasList=[];
        foreach ($aprobacionList as $aprobacion) {
            $idFlujoAprobacionesHechasList[] = $aprobacion->id_flujo;
            $OrdenFlujoAprobacionesHechasList[] = $aprobacion->orden;
        }

        //eliminando flujo ya aprobados
        $aprobacionPendienteList=[];
        foreach ($flujo as $value) {
            if (!in_array($value->id_flujo,$idFlujoAprobacionesHechasList) && !in_array($value->orden,$OrdenFlujoAprobacionesHechasList) ) {
                $aprobacionPendienteList[] = $value;
            }
        }

        // eliminar flujo con numero de orden aprobado 
        // Debugbar::info($aprobacionPendienteList);


        // si el id_rol usuario le corresponde aprobar la primera aprobacion pendiente y evaluar si le toca la siguiente
        $i=0;
        $FlujoAGrabarList=[];
        foreach ($aprobacionPendienteList as $ap) {
            if(in_array($ap->id_rol,$idRolUsuarioList)){
                $FlujoAGrabarList[]= $ap;
            }
            if(++$i > 2) break; //limite 2
        }
        // guardar 
        foreach ($FlujoAGrabarList as $value) {
            $nuevaAprobacion = $this->guardar_aprobacion_documento($value->id_flujo, $id_doc_aprob, $id_vobo, $detalle_observacion, $id_usuario, $value->id_rol);
        }


        // verificar aprobacionesPendientes == aprobaciones
        $newAprobaciones = Aprobacion::getVoBo($id_doc_aprob);
        $newAprobacionList = $newAprobaciones['data'];
        
        // aprobaciones pendientes
        $idFlujoAprobacionesHechasList=[];
        $OrdenFlujoAprobacionesHechasList=[];
        foreach ($newAprobacionList as $aprobacion) {
            $idFlujoAprobacionesHechasList[] = $aprobacion->id_flujo;
            $OrdenFlujoAprobacionesHechasList[] = $aprobacion->orden;
        }

        //eliminando flujo ya aprobados
        $newAprobacionPendienteList=[];
        foreach ($flujo as $value) {
            if (!in_array($value->id_flujo,$idFlujoAprobacionesHechasList) && !in_array($value->orden,$OrdenFlujoAprobacionesHechasList) ) {
                $newAprobacionPendienteList[] = $value;
            }
        }

        $idRequerimiento = Documento::getIdDocByIdDocAprob($id_doc_aprob);
        if(count($newAprobacionPendienteList)== 0){
            DB::table('almacen.alm_req')->where('id_requerimiento', $idRequerimiento)->update(['estado' => 2]); // estado aprobado
        }else{
            DB::table('almacen.alm_req')->where('id_requerimiento', $idRequerimiento)->update(['estado' => 12]); //Pendiente de Aprobación
        }

        if($nuevaAprobacion>0){
            $status = 200; 
            $message = 'Ok';
        }else{
            $status = 204;
            $message = 'No Content, data vacia';
        }




        // $id_flujo = Documento::searchIdFlujoByOrden($flujo, $cantidad_aprobaciones + 1);


        // if ($cantidad_aprobaciones < $tamaño_flujo) {
        //     $nuevaAprobacion = $this->guardar_aprobacion_documento($id_flujo, $id_doc_aprob, $id_vobo, $detalle_observacion, $id_usuario, $id_rol);
        //     if ($nuevaAprobacion > 0) {
        //         $status = 200; // No Content
        //         $message = 'Ok';
        //         $aprobaciones = Aprobacion::getVoBo($id_doc_aprob);
        //         // Debugbar::info($aprobaciones);
        //         $aprobacionList = $aprobaciones['data'];
        //         $cantidad_aprobaciones = count($aprobacionList);

        //         if ($cantidad_aprobaciones == $tamaño_flujo) {
        //             $id_req = Documento::getIdDocByIdDocAprob($id_doc_aprob);
        //             $estado_aprobado = 2; // estado aprobado
        //             $update_requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['estado' => $estado_aprobado]);
        //         } else {
        //             $id_req = Documento::getIdDocByIdDocAprob($id_doc_aprob);
        //             $estado_pendiente_aprobacion = 12; //Pendiente de Aprobación
        //             $update_requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['estado' => $estado_pendiente_aprobacion]);
        //         }
        //     } else {
        //         $status = 204; // No Content
        //         $message = 'No Content, data vacia';
        //     }
        // }

        $output = ['status' => $status, 'message' => $message];
        return $output;
    }


    public static function guardar_aprobacion_documento($id_flujo, $id_doc_aprob, $id_vobo, $detalle_observacion, $id_usuario, $id_rol)
    {
        $hoy = date('Y-m-d H:i:s');
        $nuevaAprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo'              => $id_flujo,
                'id_doc_aprob'          => $id_doc_aprob,
                'id_vobo'               => $id_vobo,
                'id_usuario'            => $id_usuario,
                'fecha_vobo'            => $hoy,
                'detalle_observacion'   => $detalle_observacion,
                'id_rol'                => $id_rol
            ],
            'id_aprobacion'
        );

        return $nuevaAprobacion;
    }


    function observarDocumento(Request $request)
    {
        $id_doc_aprob = $request->id_doc_aprob;
        $detalle_observacion = $request->detalle_observacion;
        $id_rol = $request->id_rol;
        $id_usuario = Auth::user()->id_usuario;
        $id_requerimiento = $this->get_id_doc($id_doc_aprob, 1);

        $status = '';
        $message = '';
        $hoy = date('Y-m-d H:i:s');
        $estado_observado = 3;

        $requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_requerimiento)
            ->update([
                'estado' => $estado_observado
            ]);
        $detalle_req = DB::table('almacen.alm_det_req')
            ->where('id_requerimiento', '=', $id_requerimiento)
            ->update([
                'estado' => $estado_observado
            ]);
        if ($requerimiento && $detalle_req > 0) {
            $nuevaAprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
                [
                    'id_flujo'              => null,
                    'id_doc_aprob'          => $id_doc_aprob,
                    'id_vobo'               => 3,
                    'id_usuario'            => $id_usuario,
                    'fecha_vobo'            => $hoy,
                    'detalle_observacion'   => $detalle_observacion,
                    'id_rol'                => $id_rol,
                    'id_sustentacion'       => null
                ],
                'id_aprobacion'
            );

            if ($nuevaAprobacion > 0) {
                $status = 200;
                $message = 'OK';
            }
        }



        $output = ['status' => $status, 'message' => $message];
        return $output;
    }



    function anularDocumento(Request $request)
    {
        $id_doc_aprob = $request->id_doc_aprob;
        $id_requerimiento = $this->get_id_doc($id_doc_aprob, 1);
        $motivo = $request->motivo;
        $id_rol = $request->id_rol;
        $id_usuario = Auth::user()->id_usuario;
        // $estado_anulado = $this->get_estado_doc('Anulado');
        $estado_anulado = 7;
        $hoy = date('Y-m-d H:i:s');
        $status = '';
        $message = '';

        $requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_requerimiento)
            ->update([
                'estado' => $estado_anulado
            ]);
        $detalle_req = DB::table('almacen.alm_det_req')
            ->where('id_requerimiento', '=', $id_requerimiento)
            ->update([
                'estado' => $estado_anulado
            ]);

        if ($requerimiento && $detalle_req > 0) {
            $AnularReq = DB::table('administracion.adm_aprobacion')->insertGetId(
                [
                    'id_flujo'              => null,
                    'id_doc_aprob'          => $id_doc_aprob,
                    'id_vobo'               => 2,
                    'id_usuario'            => $id_usuario,
                    'fecha_vobo'            => $hoy,
                    'detalle_observacion'   => $motivo,
                    'id_rol'                => $id_rol,
                    'id_sustentacion'       => null
                ],
                'id_aprobacion'
            );

            if ($AnularReq > 0) {
                $status = 200;
                $message = 'OK';
            }
        }

        $output = ['status' => $status, 'message' => $message];

        return response()->json($output);
    }
}
