<?php

namespace App\Http\Controllers\Tesoreria;

use App\Exports\ListadoItemsRequerimientoPagoExport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Exports\ListadoRequerimientoPagoExport;
use App\Helpers\Finanzas\PresupuestoInternoHistorialHelper;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Finanzas\Presupuesto\PresupuestoInternoController;
use App\Http\Controllers\ProyectosController;
use App\Models\Administracion\Aprobacion;
use App\Models\Administracion\Division;
use App\Models\Administracion\Documento;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Administracion\Estado;
use App\Models\Administracion\Sede;
use App\Models\almacen\DocumentoCompra;
use App\Models\almacen\DocumentoCompraDetalle;
use App\Models\Almacen\Trazabilidad;
use App\Models\Almacen\UnidadMedida;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Configuracion\Grupo;
use App\Models\Configuracion\Moneda;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Contabilidad\CuentaContribuyente;
use App\Models\Contabilidad\Identidad;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\Logistica\Proveedor;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\Rrhh\CuentaPersona;
use App\Models\Rrhh\Persona;
use App\Models\Tesoreria\OtrosAdjuntosTesoreria;
use App\Models\Tesoreria\RegistroPago;
use App\Models\Tesoreria\RequerimientoPagoDetalle;
use App\Models\Tesoreria\RequerimientoPago;
use App\Models\Tesoreria\RequerimientoPagoAdjunto;
use App\Models\Tesoreria\RequerimientoPagoAdjuntoDetalle;
use App\Models\Tesoreria\RequerimientoPagoCategoriaAdjunto;
use App\Models\Tesoreria\RequerimientoPagoTipo;
use App\Models\Tesoreria\RequerimientoPagoTipoDestinatario;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yajra\DataTables\Facades\DataTables;
use Debugbar;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class RequerimientoPagoController extends Controller
{
    public function __construct()
    {
        // session_start();
    }

    public function viewListaRequerimientoPago()
    {
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();
        $gruposUsuario = Auth::user()->getAllGrupo();

        $tiposDestinatario = RequerimientoPagoTipoDestinatario::mostrar();
        $empresas = Empresa::mostrar();
        $grupos = Grupo::mostrar();
        $divisiones = Division::mostrar();
        $monedas = Moneda::mostrar();
        $unidadesMedida = UnidadMedida::mostrar();
        $proyectosActivos = (new ProyectosController)->listar_proyectos_activos();
        $bancos = Banco::mostrar();
        $tipo_cuenta = TipoCuenta::mostrar();
        $tiposRequerimientoPago = RequerimientoPagoTipo::mostrar();
        $tipos_documentos = Identidad::mostrar();
        $idTrabajador = Auth::user()->id_trabajador;
        $idUsuario = Auth::user()->id_usuario;
        $nombreUsuario = Auth::user()->nombre_corto;

        $estados = Estado::mostrar();
        $array_accesos = [];
        $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        $tipoDocumentos = (new ContabilidadController)->listaTipoDocumentos();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos, $value->id_acceso);
        }

        $presupuestoInternoList = (new PresupuestoInternoController)->comboPresupuestoInterno(0, 0);

        return view(
            'tesoreria/requerimiento_pago/lista',
            compact(
                'prioridades',
                'empresas',
                'grupos',
                'tiposRequerimientoPago',
                'periodos',
                'monedas',
                'unidadesMedida',
                'divisiones',
                'gruposUsuario',
                'proyectosActivos',
                'bancos',
                'tipo_cuenta',
                'tipos_documentos',
                'tiposDestinatario',
                'idUsuario',
                'idTrabajador',
                'nombreUsuario',
                'estados',
                'array_accesos',
                'presupuestoInternoList',
                'tipoDocumentos'
            )
        );
    }
    // public function viewRevisarAprobarRequerimientoPago()
    // {
    //     $periodos = Periodo::mostrar();
    //     $prioridades = Prioridad::mostrar();
    //     $gruposUsuario = Auth::user()->getAllGrupo();

    //     $tipoDestinatario = RequerimientoPagoTipoDestinatario::mostrar();
    //     $empresas = Empresa::mostrar();
    //     $grupos = Grupo::mostrar();
    //     $divisiones = Division::mostrar();
    //     $monedas = Moneda::mostrar();
    //     $unidadesMedida = UnidadMedida::mostrar();
    //     $proyectosActivos = (new ProyectosController)->listar_proyectos_activos();



    //     return view('tesoreria/requerimiento_pago/revisar_aprobar', compact('prioridades', 'empresas', 'grupos', 'periodos', 'monedas', 'unidadesMedida', 'divisiones', 'gruposUsuario', 'proyectosActivos','tipoDestinatario'));
    // }
    function listarRequerimientoPago(Request $request)
    {
        $mostrar = $request->meOrAll;
        $idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
        $idGrupo = $request->idGrupo;
        $division = $request->idDivision;
        $fechaRegistroDesde = $request->fechaRegistroDesde;
        $fechaRegistroHasta = $request->fechaRegistroHasta;
        $idEstado = $request->idEstado;

        $GrupoDeUsuarioEnSesionList = Auth::user()->getAllGrupo();
        $idGrupoDeUsuarioEnSesionList = [];
        foreach ($GrupoDeUsuarioEnSesionList as $grupo) {
            $idGrupoDeUsuarioEnSesionList[] = $grupo->id_grupo; // lista de id_rol del usuario en sesion
        }

        $data = RequerimientoPago::with('detalle')
            ->leftJoin('tesoreria.requerimiento_pago_tipo', 'requerimiento_pago_tipo.id_requerimiento_pago_tipo', '=', 'requerimiento_pago.id_requerimiento_pago_tipo')
            ->leftJoin('administracion.adm_estado_doc', 'requerimiento_pago.id_estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('administracion.adm_prioridad', 'requerimiento_pago.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'requerimiento_pago.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'requerimiento_pago.id_sede')
            ->leftJoin('configuracion.sis_moneda', 'requerimiento_pago.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'requerimiento_pago.id_periodo')
            ->leftJoin('administracion.adm_empresa', 'requerimiento_pago.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'requerimiento_pago.id_usuario')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'requerimiento_pago.id_division')
            ->leftJoin('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'requerimiento_pago.id_proyecto')
            ->leftJoin('finanzas.presupuesto_interno', 'presupuesto_interno.id_presupuesto_interno', '=', 'requerimiento_pago.id_presupuesto_interno')


            ->select(
                'requerimiento_pago.*',
                'requerimiento_pago_tipo.descripcion as descripcion_requerimiento_pago_tipo',
                'sis_moneda.descripcion as descripcion_moneda',
                'sis_moneda.simbolo as simbolo_moneda',
                'adm_periodo.descripcion as periodo',
                'adm_prioridad.descripcion as prioridad',
                'sis_grupo.descripcion as grupo',
                'sis_sede.codigo as sede',
                'sis_sede.descripcion as descripcion_empresa_sede',
                'division.descripcion as division',
                'presupuesto_interno.codigo as codigo_presupuesto_interno',
                'presupuesto_interno.descripcion as descripcion_presupuesto_interno',
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'adm_contri.razon_social as empresa_razon_social',
                'adm_contri.nro_documento as empresa_nro_documento',
                'sis_identi.descripcion as empresa_tipo_documento',
                'sis_usua.nombre_corto as usuario_nombre_corto',
                DB::raw("(SELECT COUNT(registro_pago.id_pago)
                FROM tesoreria.registro_pago
                WHERE  registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago AND registro_pago.adjunto IS NOT NULL AND
                registro_pago.estado != 7) AS cantidad_adjuntos_pago"),

                // DB::raw("(SELECT  jsonb_agg(DISTINCT jsonb_build_object('vobo', adm_vobo.descripcion, 'usuario', sis_usua.nombre_corto, 'fecha_vobo', adm_aprobacion.fecha_vobo))
                // FROM administracion.adm_documentos_aprob
                //      INNER JOIN administracion.adm_aprobacion ON adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                //     LEFT JOIN administracion.adm_vobo ON adm_vobo.id_vobo = adm_aprobacion.id_vobo
                //     LEFT JOIN configuracion.sis_usua ON sis_usua.id_usuario = adm_aprobacion.id_usuario
                // WHERE requerimiento_pago.id_requerimiento_pago = adm_documentos_aprob.id_doc and adm_documentos_aprob.id_tp_documento =11 ) AS flujo_aprobacion")
                DB::raw("(SELECT sis_usua.nombre_corto
                FROM administracion.adm_documentos_aprob
                     INNER JOIN administracion.adm_aprobacion ON adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                    LEFT JOIN administracion.adm_vobo ON adm_vobo.id_vobo = adm_aprobacion.id_vobo
                    LEFT JOIN configuracion.sis_usua ON sis_usua.id_usuario = adm_aprobacion.id_usuario
                WHERE requerimiento_pago.id_requerimiento_pago = adm_documentos_aprob.id_doc and adm_documentos_aprob.id_tp_documento =11 and adm_aprobacion.id_vobo =1 order by adm_aprobacion.fecha_vobo desc limit 1 ) AS ultimo_aprobador")

            )
            ->when(($mostrar === 'ME'), function ($query) {
                $idUsuario = Auth::user()->id_usuario;
                return $query->whereRaw('requerimiento_pago.id_usuario = ' . $idUsuario);
            })
            ->when(($mostrar === 'ALL'), function ($query) {
                return $query->whereRaw('requerimiento_pago.id_usuario > 0');
            })
            ->when((intval($idEmpresa) > 0), function ($query)  use ($idEmpresa) {
                return $query->whereRaw('requerimiento_pago.id_empresa = ' . $idEmpresa);
            })
            ->when((intval($idSede) > 0), function ($query)  use ($idSede) {
                return $query->whereRaw('requerimiento_pago.id_sede = ' . $idSede);
            })
            ->when((intval($idGrupo) > 0), function ($query)  use ($idGrupo) {
                return $query->whereRaw('sis_grupo.id_grupo = ' . $idGrupo);
            })
            ->when((intval($division) > 0), function ($query)  use ($division) {
                return $query->whereRaw('requerimiento_pago.division_id = ' . $division);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde) {
                return $query->where('requerimiento_pago.fecha_registro', '>=', $fechaRegistroDesde);
            })
            ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroHasta) {
                return $query->where('requerimiento_pago.fecha_registro', '<=', $fechaRegistroHasta);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde, $fechaRegistroHasta) {
                return $query->whereBetween('requerimiento_pago.fecha_registro', [$fechaRegistroDesde, $fechaRegistroHasta]);
            })

            ->when((intval($idEstado) > 0), function ($query)  use ($idEstado) {
                return $query->whereRaw('requerimiento_pago.id_estado = ' . $idEstado);
            })

            ->whereIn('requerimiento_pago.id_grupo', $idGrupoDeUsuarioEnSesionList);


        return datatables($data)
            ->filterColumn('requerimiento_pago.fecha_registro', function ($query, $keyword) {
                try {
                    $desde = Carbon::createFromFormat('d-m-Y', trim($keyword))->hour(0)->minute(0)->second(0);
                    $hasta = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->whereBetween('requerimiento_pago.fecha_registro', [$desde, $hasta->addDay()->addSeconds(-1)]);
                } catch (\Throwable $th) {
                }
            })
            ->editColumn('flujo_aprobacion', function ($data) {
                return json_decode($data->flujo_aprobacion, true);
            })
            ->rawColumns(['termometro'])->toJson();
    }

    function listarAdjuntosPago($idRequerimientoPago)
    {

        $registrosPago = RegistroPago::where([["id_requerimiento_pago", $idRequerimientoPago], ["adjunto", '!=', null]])->get();
        return $registrosPago;
    }


    function guardarRequerimientoPago(Request $request)
    {
        DB::beginTransaction();
        try {

            // evaluar si el estado del cierre periodo

            $añoPeriodo = Periodo::find($request->periodo)->descripcion;
            $idEmpresa = Sede::find($request->sede)->id_empresa;
            // $fechaPeriodo = Carbon::createFromFormat('Y-m-d', ($periodo->descripcion.'-01-01'));
            $estadoOperativo = (new CierreAperturaController)->consultarPeriodoOperativo($añoPeriodo, $idEmpresa);
            if ($estadoOperativo != 1) { //1:abierto, 2:cerrado, 3:Declarado
                return response()->json(['id_requerimiento_pago' => 0, 'codigo' => '', 'mensaje' => 'No se puede generar el requerimiento cuando el periodo operativo está cerrado']);
            }


            $requerimientoPago = new RequerimientoPago();
            $requerimientoPago->id_usuario = Auth::user()->id_usuario;
            $requerimientoPago->concepto = strtoupper($request->concepto);
            $requerimientoPago->fecha_registro = new Carbon();
            $requerimientoPago->id_periodo = $request->periodo;
            $requerimientoPago->id_moneda = $request->moneda > 0 ? $request->moneda : null;
            $requerimientoPago->id_prioridad = $request->prioridad > 0 ? $request->prioridad : null;
            $requerimientoPago->id_requerimiento_pago_tipo = $request->tipo_requerimiento_pago > 0 ? $request->tipo_requerimiento_pago : null;
            $requerimientoPago->comentario = $request->comentario;
            $requerimientoPago->id_empresa = $request->empresa ? $request->empresa : null;
            $requerimientoPago->id_sede = $request->sede > 0 ? $request->sede : null;
            $requerimientoPago->id_grupo = $request->grupo > 0 ? $request->grupo : null;
            $requerimientoPago->id_division = $request->division;
            $requerimientoPago->id_tipo_destinatario = $request->id_tipo_destinatario;
            $requerimientoPago->id_cuenta_persona = $request->id_cuenta_persona > 0 ? $request->id_cuenta_persona : null;
            $requerimientoPago->id_persona = $request->id_persona > 0 ? $request->id_persona : null;
            $requerimientoPago->id_contribuyente = $request->id_contribuyente > 0 ? $request->id_contribuyente : null;
            $requerimientoPago->id_cuenta_contribuyente = $request->id_cuenta_contribuyente > 0 ? $request->id_cuenta_contribuyente : null;
            // $requerimientoPago->confirmacion_pago = ($request->tipo_requerimiento == 2 ? ($request->fuente == 2 ? true : false) : true);
            $requerimientoPago->monto_total = $request->monto_total;
            $requerimientoPago->id_proyecto = $request->proyecto > 0 ? $request->proyecto : null;
            $requerimientoPago->id_cc = $request->id_cc > 0 ? $request->id_cc : null;
            $requerimientoPago->id_estado = 1;
            $requerimientoPago->id_trabajador = $request->id_trabajador > 0 ? $request->id_trabajador : null;
            $requerimientoPago->id_presupuesto_interno = $request->id_presupuesto_interno > 0 ? $request->id_presupuesto_interno : null;

            $requerimientoPago->save();

            $requerimientoPago->archivo_adjunto = $request->archivo_adjunto;
            $requerimientoPago->fecha_emision_adjunto = $request->fecha_emision_adjunto;
            $requerimientoPago->categoria_adjunto = $request->categoria_adjunto;
            $requerimientoPago->accion_adjunto = $request->accion_adjunto;
            // $requerimientoPago->fecha_emision = $request->fecha_emision;//fecha de emision de comprobsante del adjunto nivel cabecera

            $count = count($request->descripcion);
            $montoTotal = 0;
            for ($i = 0; $i < $count; $i++) {
                if ($request->cantidad[$i] <= 0) {
                    return response()->json(['id_requerimiento_pago' => 0, 'codigo' => '', 'mensaje' => 'La cantidad solicitada debe ser mayor a 0']);
                }

                $detalle = new RequerimientoPagoDetalle();
                $detalle->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                $detalle->id_tipo_item = $request->tipoItem[$i];
                if(intval($request->id_presupuesto_interno) > 0){
                    $detalle->id_partida_pi = $request->idPartida[$i]??null;
                }else{
                    $detalle->id_partida = $request->idPartida[$i]??null;
                }
                $detalle->id_centro_costo = $request->idCentroCosto[$i];
                $detalle->descripcion = $request->descripcion[$i];
                $detalle->id_unidad_medida = $request->unidad[$i];
                $detalle->cantidad = $request->cantidad[$i];
                $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                $subtotalItem = floatval($request->cantidad[$i]) * floatval($request->precioUnitario[$i]);
                $detalle->subtotal = $subtotalItem;
                $detalle->fecha_registro = new Carbon();
                $detalle->id_estado = 1;
                $detalle->motivo = $request->motivo[$i];
                $detalle->save();
                $detalle->idRegister = $request->idRegister[$i];
                $detalle->importe_item_para_presupuesto = $subtotalItem;
                $detalleArray[] = $detalle;
                $montoTotal += $detalle->cantidad * $detalle->precioUnitario;
            }




            DB::commit();

            $codigo = RequerimientoPago::crearCodigo($request->grupo, $requerimientoPago->id_requerimiento_pago, $request->periodo);
            $rp = RequerimientoPago::find($requerimientoPago->id_requerimiento_pago);
            $rp->codigo = $codigo;
            $rp->save();

            $documento = new Documento();
            $documento->id_tp_documento = 11; // requerimiento pago
            $documento->codigo_doc = $requerimientoPago->codigo;
            $documento->id_doc = $requerimientoPago->id_requerimiento_pago;
            $documento->save();

            // guardar factura solo si existe vinculo
            // $documentoCompraArray=[];
            $documentoCompraArray = $this->VincularFacturaRequerimientoPago($request, $count, $detalleArray, $codigo);

            // Adjuntos Cabecera
            if (isset($request->archivo_adjunto_list)) {
                $ObjectoAdjunto = json_decode($request->archivoAdjuntoRequerimientoPagoObject);
                $adjuntoOtrosAdjuntosLength = $request->archivo_adjunto_list != null ? count($request->archivo_adjunto_list) : 0;
                $archivoAdjuntoList = $request->archivo_adjunto_list;

                if (count($request->archivo_adjunto_list) > 0) {
                    foreach ($ObjectoAdjunto as $keyObj => $value) {
                        $ObjectoAdjunto[$keyObj]->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                        $ObjectoAdjunto[$keyObj]->codigo = $codigo;


                        if (count($documentoCompraArray) > 0) {
                            foreach ($documentoCompraArray as $keyDoc => $docCom) {
                                if ($ObjectoAdjunto[$keyObj]->id == $docCom->id_adjunto) {
                                    $ObjectoAdjunto[$keyObj]->id_doc_com = $docCom->id_doc_com;
                                }
                            }
                        }

                        if ($adjuntoOtrosAdjuntosLength > 0) {
                            foreach ($archivoAdjuntoList as $keyA => $archivo) {
                                if (is_file($archivo)) {
                                    $nombreArchivoAdjunto = $archivo->getClientOriginalName();
                                    if ($nombreArchivoAdjunto == $value->nameFile) {
                                        $ObjectoAdjunto[$keyObj]->file = $archivo;
                                    }
                                }
                            }
                        }
                    }

                    $idAdjunto[] = $this->subirYRegistrarArchivoCabecera($ObjectoAdjunto);
                }
            }
            // Adjuntos Detalle
            if (isset($request->archivo_adjunto_detalle_list)) {
                $ObjectoAdjuntoDetalle = json_decode($request->archivoAdjuntoRequerimientoPagoDetalleObject);
                $adjuntoOtrosAdjuntosDetalleLength = $request->archivo_adjunto_detalle_list != null ? count($request->archivo_adjunto_detalle_list) : 0;
                $archivoAdjuntoDetalleList = $request->archivo_adjunto_detalle_list;
                if (count($request->archivo_adjunto_detalle_list) > 0) {

                    foreach ($ObjectoAdjuntoDetalle as $keyObj => $value) {
                        foreach ($detalleArray as $keyDetArr => $det) {
                            if ($det->idRegister == $value->id) {
                                $ObjectoAdjuntoDetalle[$keyObj]->id_requerimiento_pago_detalle = $det->id_requerimiento_pago_detalle;
                            }
                            $ObjectoAdjuntoDetalle[$keyObj]->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                            $ObjectoAdjuntoDetalle[$keyObj]->codigo = $codigo;
                            if ($adjuntoOtrosAdjuntosDetalleLength > 0) {
                                foreach ($archivoAdjuntoDetalleList as $keyA => $archivo) {
                                    if (is_file($archivo)) {
                                        $nombreArchivoAdjunto = $archivo->getClientOriginalName();
                                        if ($nombreArchivoAdjunto == $value->nameFile) {
                                            $ObjectoAdjuntoDetalle[$keyObj]->file = $archivo;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Debugbar::info($ObjectoAdjuntoDetalle);

                    $idAdjuntoDetalle[] = $this->guardarAdjuntoRequerimientoPagoDetalle($ObjectoAdjuntoDetalle);
                }
            }

            return response()->json(['id_requerimiento_pago' => $requerimientoPago->id_requerimiento_pago, 'mensaje' => 'Se guardó el requerimiento de pago ' . $codigo]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento_pago' => 0, 'mensaje' => 'Hubo un problema al guardar el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function vincularFacturaRequerimientoPago($request, $count, $detalleArray, $codigo)
    {
        $documentoCompraArray = [];
        $ObjectoFactura = json_decode($request->facturaObject);
        if (isset($ObjectoFactura)) {
            foreach ($ObjectoFactura as $keyObj => $value) {
                if (count($value->items) > 0) {
                    if ($value->id_doc_com == '' || $value->id_doc_com == null) { // es un id_doc_com con numeros y letras => es nuevo, insertar
                        $documentoCompra = new DocumentoCompra();
                        $documentoCompra->serie = $value->serie;
                        $documentoCompra->numero = $value->numero;
                        $documentoCompra->id_tp_doc = $value->id_tp_doc;
                        $documentoCompra->fecha_emision = $value->fecha_emision;
                        $documentoCompra->fecha_vcmto =  $value->fecha_emision;
                        $documentoCompra->id_condicion = $value->id_condicion;
                        $documentoCompra->moneda = $value->id_moneda;
                        $documentoCompra->sub_total = $value->subtotal;
                        $documentoCompra->total_igv = $value->total_igv;
                        $documentoCompra->total_a_pagar = $value->total_a_pagar;
                        $documentoCompra->usuario = Auth::user()->id_usuario;
                        $documentoCompra->estado = 1;
                        $documentoCompra->fecha_registro =  new Carbon();
                        $documentoCompra->registrado_por = Auth::user()->id_usuario;
                        $documentoCompra->id_sede = $value->id_sede;
                        $documentoCompra->save();
                        $documentoCompra->id_adjunto = $value->id_adjunto;
                        $documentoCompraArray[] = $documentoCompra;

                        $listaItemAtendido = [];
                        for ($i = 0; $i < $count; $i++) {
                            foreach ($detalleArray as $key => $det) {
                                for ($j = 0; $j < count($value->items); $j++) {
                                    if ($det->idRegister == $value->items[$j]) {
                                        if (!in_array($value->items[$j], $listaItemAtendido)) {
                                            $documentoCompraDetalle = new DocumentoCompraDetalle();
                                            $documentoCompraDetalle->id_doc = $documentoCompra->id_doc_com;
                                            $documentoCompraDetalle->cantidad = $det->cantidad;
                                            $documentoCompraDetalle->id_unid_med = $det->id_unidad_medida;
                                            $documentoCompraDetalle->precio_unitario = $det->precio_unitario;
                                            $documentoCompraDetalle->servicio_descripcion = $det->descripcion;
                                            $documentoCompraDetalle->sub_total =  floatval($det->cantidad) * floatval($det->precio_unitario);
                                            $documentoCompraDetalle->precio_total = floatval($det->cantidad) * floatval($det->precio_unitario);
                                            $documentoCompraDetalle->estado = 1;
                                            $documentoCompraDetalle->fecha_registro = new Carbon();
                                            $documentoCompraDetalle->obs = "Creado a partir del requerimiento de pago " . $codigo;
                                            $documentoCompraDetalle->id_requerimiento_pago_detalle = $det->id_requerimiento_pago_detalle;
                                            $documentoCompraDetalle->save();

                                            $listaItemAtendido[] = $value->items[$j];
                                        }
                                    }
                                }
                            }
                        }
                    } else { // actualizar


                        $documentoCompra = DocumentoCompra::find($value->id_doc_com);
                        $documentoCompra->id_tp_doc = $value->id_tp_doc;
                        $documentoCompra->fecha_emision = $value->fecha_emision;
                        $documentoCompra->fecha_vcmto =  $value->fecha_emision;
                        $documentoCompra->id_condicion = $value->id_condicion;
                        $documentoCompra->moneda = $value->id_moneda;
                        $documentoCompra->sub_total = $value->subtotal;
                        $documentoCompra->total_igv = $value->total_igv;
                        $documentoCompra->total_a_pagar = $value->total_a_pagar;
                        $documentoCompra->usuario = Auth::user()->id_usuario;
                        $documentoCompra->estado = 1;
                        $documentoCompra->id_sede = $value->id_sede;
                        $documentoCompra->save();
                        $documentoCompra->id_adjunto = $value->id_adjunto;
                        $documentoCompraArray[] = $documentoCompra;

                        // anular todo los existente detalles para luego insertar el detalle actualizado
                        $documentoCompraDetalleOld = DocumentoCompraDetalle::where('id_doc', $value->id_doc_com)->get();
                        foreach ($documentoCompraDetalleOld as $detFac) {
                            $eliminarDetFact = DocumentoCompraDetalle::find($detFac->id_doc_det);
                            $eliminarDetFact->estado = 7;
                            $eliminarDetFact->save();
                        }

                        for ($i = 0; $i < $count; $i++) {
                            foreach ($detalleArray as $key => $det) {
                                for ($j = 0; $j < count($value->items); $j++) {
                                    if ($det->idRegister == $value->items[$j]) {
                                        $documentoCompraDetalle = new DocumentoCompraDetalle();
                                        $documentoCompraDetalle->id_doc = $documentoCompra->id_doc_com;
                                        $documentoCompraDetalle->cantidad = $det->cantidad;
                                        $documentoCompraDetalle->id_unid_med = $det->id_unidad_medida;
                                        $documentoCompraDetalle->precio_unitario = $det->precio_unitario;
                                        $documentoCompraDetalle->servicio_descripcion = $det->descripcion;
                                        $documentoCompraDetalle->sub_total =  floatval($det->cantidad) * floatval($det->precio_unitario);
                                        $documentoCompraDetalle->precio_total = floatval($det->cantidad) * floatval($det->precio_unitario);
                                        $documentoCompraDetalle->estado = 1;
                                        $documentoCompraDetalle->fecha_registro = new Carbon();
                                        $documentoCompraDetalle->obs = "Creado a partir del requerimiento de pago " . $codigo;
                                        $documentoCompraDetalle->id_requerimiento_pago_detalle = $det->id_requerimiento_pago_detalle;
                                        $documentoCompraDetalle->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $documentoCompraArray;
    }

    function guardarAdjuntosAdicionales(Request $request)
    {
        DB::beginTransaction();
        try {
            // Debugbar::info($ObjectoAdjuntoDetalle);
            $estado_accion = '';
            $idAdjunto = [];

            $ObjectoAdjunto = json_decode($request->archivoAdjuntoRequerimientoPagoObject);
            $adjuntoOtrosAdjuntosLength = $request->archivo_adjunto_list != null ? count($request->archivo_adjunto_list) : 0;
            $archivoAdjuntoList = $request->archivo_adjunto_list;
            $requerimientoPago = RequerimientoPago::where("id_requerimiento_pago", $request->id_requerimiento_pago)->first();

            if (count($request->archivo_adjunto_list) > 0) {


                foreach ($ObjectoAdjunto as $keyObj => $value) {
                    $ObjectoAdjunto[$keyObj]->id_requerimiento_pago = $request->id_requerimiento_pago;
                    $ObjectoAdjunto[$keyObj]->codigo = $requerimientoPago->codigo;
                    if ($adjuntoOtrosAdjuntosLength > 0) {
                        foreach ($archivoAdjuntoList as $keyA => $archivo) {
                            if (is_file($archivo)) {
                                $nombreArchivoAdjunto = $archivo->getClientOriginalName();
                                if ($nombreArchivoAdjunto == $value->nameFile) {
                                    $ObjectoAdjunto[$keyObj]->file = $archivo;
                                }
                            }
                        }
                    }
                }
                $idAdjunto[] = $this->subirYRegistrarArchivoCabecera($ObjectoAdjunto);
            }



            if (count($idAdjunto) > 0) {
                $mensaje = 'Adjuntos guardos';
                $estado_accion = 'success';
            } else {
                $mensaje = 'Hubo un problema y no se pudo guardo los adjuntos';
                $estado_accion = 'warning';
            }
            DB::commit();

            return response()->json(['status' => $estado_accion, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'mensaje' => 'Hubo un problema al guardar los adjuntos. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function subirYRegistrarArchivoCabecera($ObjectoAdjunto)
    {
        $idList = [];

        foreach ($ObjectoAdjunto as $key => $archivo) {

            if ($archivo->action == "GUARDAR") {
                $fechaHoy = new Carbon();
                $sufijo = $fechaHoy->format('YmdHis');
                $file = ($archivo->file)->getClientOriginalName();
                $codigo = $archivo->codigo;
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $newNameFile = $codigo . '_' . $key . $archivo->category . $sufijo . '.' . $extension;
                Storage::disk('archivos')->put("necesidades/requerimientos/pago/cabecera/" . $newNameFile, File::get($archivo->file));

                $idAdjunto = DB::table('tesoreria.requerimiento_pago_adjunto')->insertGetId(
                    [
                        'id_requerimiento_pago'     => $archivo->id_requerimiento_pago,
                        'archivo'                   => $newNameFile,
                        'id_estado'                 => 1,
                        'serie'                     => $archivo->serie,
                        'numero'                    => $archivo->numero,
                        'id_moneda'                 => $archivo->id_moneda,
                        'monto_total'               => $archivo->monto_total,
                        'fecha_emision'             => $archivo->fecha_emision,
                        'id_tp_doc'                 => $archivo->category,
                        'id_usuario'                => Auth::user()->id_usuario,
                        'fecha_registro'            => $fechaHoy,
                        'id_doc_com'                => isset($archivo->id_doc_com) ? $archivo->id_doc_com : null
                    ],
                    'id_requerimiento_pago_adjunto'
                );
                $idList[] = $idAdjunto;
            } elseif ($archivo->action == "ACTUALIZAR") {
                $idAdjunto = DB::table('tesoreria.requerimiento_pago_adjunto')
                    ->where('id_requerimiento_pago_adjunto', $archivo->id)
                    ->update(
                        [
                            'estado'                => 1,
                            'id_tp_doc'             => $archivo->category,
                            'fecha_emision'         => $archivo->fecha_emision
                        ]
                    );

                $idList[] = $idAdjunto;
            } elseif ($archivo->action == "ANULAR") {

                $idAdjunto = DB::table('tesoreria.requerimiento_pago_adjunto')
                    ->where('id_requerimiento_pago_adjunto', $archivo->id)
                    ->update(
                        [
                            'estado' => 7
                        ]
                    );
                $idList[] = $idAdjunto;
            } elseif ($archivo->action == "ANULAR") {

                $idAdjunto = DB::table('tesoreria.requerimiento_pago_adjunto')
                    ->where('id_requerimiento_pago_adjunto', $archivo->id)
                    ->update(
                        [
                            'estado' => 7
                        ]
                    );
                $idList[] = $idAdjunto;
            }
        }
        return $idList;
    }

    function guardarAdjuntoRequerimientoPagoDetalle($ObjectoAdjuntoDetalle)
    {
        // Debugbar::info($ObjectoAdjuntoDetalle);

        $idList = [];

        foreach ($ObjectoAdjuntoDetalle as $key => $archivo) {

            if ($archivo->action == "GUARDAR") {
                $fechaHoy = new Carbon();
                $sufijo = $fechaHoy->format('YmdHis');
                $file = ($archivo->file)->getClientOriginalName();
                $codigo = $archivo->codigo;
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $newNameFile = $codigo . '_' . $key  . $sufijo . '.' . $extension;
                Storage::disk('archivos')->put("necesidades/requerimientos/pago/detalle/" . $newNameFile, File::get($archivo->file));

                $idAdjunto = DB::table('tesoreria.requerimiento_pago_detalle_adjunto')->insertGetId(
                    [
                        'id_requerimiento_pago_detalle' => $archivo->id_requerimiento_pago_detalle,
                        'archivo'                   => $newNameFile,
                        'id_estado'                 => 1,
                        'fecha_registro'            => $fechaHoy
                    ],
                    'id_requerimiento_pago_detalle_adjunto'
                );
                $idList[] = $idAdjunto;
            } elseif ($archivo->action == "ACTUALIZAR") {
                $idAdjunto = DB::table('tesoreria.requerimiento_pago_detalle_adjunto')
                    ->where('id_requerimiento_pago_detalle_adjunto', $archivo->id)
                    ->update(
                        [
                            'estado'                => 1,
                        ]
                    );

                $idList[] = $idAdjunto;
            } elseif ($archivo->action == "ANULAR") {

                $idAdjunto = DB::table('tesoreria.requerimiento_pago_detalle_adjunto')
                    ->where('id_requerimiento_pago_detalle_adjunto', $archivo->id)
                    ->update(
                        [
                            'estado' => 7
                        ]
                    );
                $idList[] = $idAdjunto;
            } elseif ($archivo->action == "ANULAR") {

                $idAdjunto = DB::table('tesoreria.requerimiento_pago_detalle_adjunto')
                    ->where('id_requerimiento_pago_detalle_adjunto', $archivo->id)
                    ->update(
                        [
                            'estado' => 7
                        ]
                    );
                $idList[] = $idAdjunto;
            }
        }
        return $idList;
    }
    public function obtenerIdDocumento($tipoDocumento, $idReq)
    {
        $result = [];

        $documento = Documento::where([['id_doc', $idReq], ['id_tp_documento', $tipoDocumento]])->first();

        if (!empty($documento)) {
            $result = [
                'id' => $documento->id_doc_aprob,
                'mensaje' => 'Id encontrado',
                'estado' => 'success'
            ];
        } else {
            $result = [
                'id' => 0,
                'mensaje' => 'No se encontro el id',
                'estado' => 'error'
            ];
        }

        return $result;
    }


    function actualizarRequerimientoPago(Request $request)
    {
        DB::beginTransaction();
        try {

            // evaluar si el estado del cierre periodo
            $añoPeriodo = Periodo::find($request->periodo)->descripcion;
            $idEmpresa = Sede::find($request->sede)->id_empresa;
            // $fechaPeriodo = Carbon::createFromFormat('Y-m-d', ($periodo->descripcion.'-01-01'));
            $estadoOperativo = (new CierreAperturaController)->consultarPeriodoOperativo($añoPeriodo, $idEmpresa);
            if ($estadoOperativo != 1) { //1:abierto, 2:cerrado, 3:Declarado
                return response()->json(['id_requerimiento_pago' => 0, 'mensaje' => 'No se puede actualizar el requerimiento cuando el periodo operativo está cerrado']);
            }

            $requerimientoPago = RequerimientoPago::where("id_requerimiento_pago", $request->id_requerimiento_pago)->first();
            $requerimientoPago->id_usuario = Auth::user()->id_usuario;
            $requerimientoPago->concepto = strtoupper($request->concepto);
            $requerimientoPago->id_periodo = $request->periodo;
            $requerimientoPago->id_moneda = $request->moneda > 0 ? $request->moneda : null;
            $requerimientoPago->id_prioridad = $request->prioridad > 0 ? $request->prioridad : null;
            $requerimientoPago->id_requerimiento_pago_tipo = $request->tipo_requerimiento_pago > 0 ? $request->tipo_requerimiento_pago : null;
            $requerimientoPago->comentario = $request->comentario;
            $requerimientoPago->id_empresa = $request->empresa ? $request->empresa : null;
            $requerimientoPago->id_sede = $request->sede > 0 ? $request->sede : null;
            $requerimientoPago->id_grupo = $request->grupo > 0 ? $request->grupo : null;
            $requerimientoPago->id_division = $request->division;
            $requerimientoPago->id_tipo_destinatario = $request->id_tipo_destinatario;
            $requerimientoPago->id_cuenta_persona = $request->id_cuenta_persona > 0 ? $request->id_cuenta_persona : null;
            $requerimientoPago->id_persona = $request->id_persona > 0 ? $request->id_persona : null;
            $requerimientoPago->id_contribuyente = $request->id_contribuyente > 0 ? $request->id_contribuyente : null;
            $requerimientoPago->id_cuenta_contribuyente = $request->id_cuenta_contribuyente > 0 ? $request->id_cuenta_contribuyente : null;
            if ($request->id_estado == 3) { // levantar observación
                $requerimientoPago->id_estado = 1;
                // $trazabilidad = new Trazabilidad();
                // $trazabilidad->id_requerimiento = $request->id_requerimiento;
                // $trazabilidad->id_usuario = Auth::user()->id_usuario;
                // $trazabilidad->accion = 'SUSTENTADO';
                // $trazabilidad->descripcion = 'Sustentado por ' . $nombreCompletoUsuario ? $nombreCompletoUsuario : '';
                // $trazabilidad->fecha_registro = new Carbon();
                // $trazabilidad->save();
                if (intval($request->id_requerimiento_pago) > 0) {
                    $documento = $this->obtenerIdDocumento(11, $request->id_requerimiento_pago);
                }
                $aprobacion = new Aprobacion();
                $aprobacion->id_flujo = null;
                $aprobacion->id_doc_aprob = (isset($documento) == true) ? $documento['id'] : null;
                $aprobacion->id_usuario = Auth::user()->id_usuario;
                $aprobacion->id_vobo = 8; // sustentado
                $aprobacion->fecha_vobo = new Carbon();
                $aprobacion->detalle_observacion = null; // comentario
                $aprobacion->id_rol = null;
                $aprobacion->tiene_sustento = false;
                $aprobacion->save();


                $idDocumento = Documento::getIdDocAprob($requerimientoPago->id_requerimiento_pago, 11);
                $ultimoVoBo = Aprobacion::getUltimoVoBo($idDocumento);
                $aprobacion = Aprobacion::where("id_aprobacion", $ultimoVoBo->id_aprobacion)->first();
                $aprobacion->tiene_sustento = true;
                $aprobacion->save();

                // TODO:  enviaar notificación al usuario aprobante, asunto => se levanto la observación
            }
            $requerimientoPago->monto_total = $request->monto_total;
            $requerimientoPago->id_proyecto = $request->proyecto > 0 ? $request->proyecto : null;
            $requerimientoPago->id_cc = $request->id_cc > 0 ? $request->id_cc : null;
            $requerimientoPago->id_trabajador = $request->id_trabajador > 0 ? $request->id_trabajador : null;
            $requerimientoPago->id_presupuesto_interno = $request->id_presupuesto_interno > 0 ? $request->id_presupuesto_interno : null;
            $requerimientoPago->save();

            $count = count($request->descripcion);

            $detalleArray = [];

            $afectaPresupuestoInternoSuma = [];
            $afectaPresupuestoInternoResta = [];

            for ($i = 0; $i < $count; $i++) {
                $id = $request->idRegister[$i];

                if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id)) // es un id con numeros y letras => es nuevo, insertar
                {
                    $detalle = new RequerimientoPagoDetalle();

                    $detalle->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                    $detalle->id_tipo_item = $request->tipoItem[$i];
                    if(intval($request->id_presupuesto_interno) > 0){
                        $detalle->id_partida_pi = $request->idPartida[$i]??null;
                    }else{
                        $detalle->id_partida = $request->idPartida[$i]??null;
                    }
                    $detalle->id_centro_costo = $request->idCentroCosto[$i];
                    $detalle->descripcion = $request->descripcion[$i] != null ? trim(strtoupper($request->descripcion[$i])) : null;
                    $detalle->id_unidad_medida = $request->unidad[$i];
                    $detalle->cantidad = $request->cantidad[$i];
                    $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                    $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                    $detalle->fecha_registro = new Carbon();
                    $detalle->id_estado = 1;
                    $detalle->motivo = $request->motivo[$i];
                    $detalle->save();
                    $detalle->idRegister = $request->idRegister[$i];
                    $detalleArray[] = $detalle;
                } else { // es un id solo de numerico => actualiza
                    if ($request->idEstado[$i] == 7) {
                        if (is_numeric($id)) { // si es un numero
                            $detalle = RequerimientoPagoDetalle::where("id_requerimiento_pago_detalle", $id)->first();
                            $detalle->id_estado = 7;
                            $detalle->save();
                            $detalle->idRegister = $request->idRegister[$i];
                            $detalleArray[] = $detalle;
                            // $detalleAnuladoArray[]= $detalle;
                            $detalle->importe_item_para_presupuesto = (floatval($detalle->precio_unitario) * floatval($detalle->cantidad));
                            $detalle->operacion_item_para_presupuesto = 'suma';
                        }
                    } else {

                        $detalle = RequerimientoPagoDetalle::where("id_requerimiento_pago_detalle", $id)->first();
                        $detalle->id_tipo_item = $request->tipoItem[$i];
                        if(intval($request->id_presupuesto_interno) > 0){
                            $detalle->id_partida_pi = $request->idPartida[$i]??null;
                        }else{
                            $detalle->id_partida = $request->idPartida[$i]??null;
                        }
                        $detalle->id_centro_costo = $request->idCentroCosto[$i];
                        $detalle->descripcion = $request->descripcion[$i] != null ? trim(strtoupper($request->descripcion[$i])) : null;
                        $detalle->id_unidad_medida = $request->unidad[$i];
                        $detalle->cantidad = $request->cantidad[$i];

                        $subtotalOrigen = floatval($detalle->precio_unitario) * floatval($detalle->cantidad);
                        $subtotalNuevo =  floatval($request->precioUnitario[$i]) * floatval($request->cantidad[$i]);

                        if ($subtotalOrigen != $subtotalNuevo) {
                            if ($subtotalOrigen < $subtotalNuevo) {
                                $importeItemParaPresupuesto = $subtotalNuevo - $subtotalOrigen;
                                $tipoOperacionItemParaPresupuesto = 'resta';
                            } elseif ($subtotalOrigen > $subtotalNuevo) {
                                $importeItemParaPresupuesto = $subtotalOrigen - $subtotalNuevo;
                                $tipoOperacionItemParaPresupuesto = 'suma';
                                // Debugbar::info($importeItemParaPresupuesto);
                            }
                        }
                        $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                        $detalle->subtotal = $subtotalNuevo;
                        $detalle->motivo = $request->motivo[$i];
                        $detalle->save();
                        $detalle->importe_item_para_presupuesto = $importeItemParaPresupuesto ?? 0;
                        $detalle->operacion_item_para_presupuesto = $tipoOperacionItemParaPresupuesto ?? '';
                        $detalle->idRegister = $request->idRegister[$i];
                        $detalleArray[] = $detalle;

                        $tipoOperacionItemParaPresupuesto = '';
                        $importeItemParaPresupuesto = 0;
                    }
                }
            }

            $documentoCompraArray = $this->VincularFacturaRequerimientoPago($request, $count, $detalleArray, $requerimientoPago->codigo);

            //adjuntos cabecera
            if (isset($request->archivo_adjunto_list)) {

                $ObjectoAdjunto = json_decode($request->archivoAdjuntoRequerimientoPagoObject);
                $adjuntoOtrosAdjuntosLength = $request->archivo_adjunto_list != null ? count($request->archivo_adjunto_list) : 0;
                $archivoAdjuntoList = $request->archivo_adjunto_list;


                if (count($request->archivo_adjunto_list) > 0) {


                    foreach ($ObjectoAdjunto as $keyObj => $value) {
                        $ObjectoAdjunto[$keyObj]->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                        $ObjectoAdjunto[$keyObj]->codigo = $requerimientoPago->codigo;
                        if ($adjuntoOtrosAdjuntosLength > 0) {
                            foreach ($archivoAdjuntoList as $keyA => $archivo) {
                                if (is_file($archivo)) {
                                    $nombreArchivoAdjunto = $archivo->getClientOriginalName();
                                    if ($nombreArchivoAdjunto == $value->nameFile) {
                                        $ObjectoAdjunto[$keyObj]->file = $archivo;
                                    }
                                }
                            }
                        }
                    }
                    $idAdjunto[] = $this->subirYRegistrarArchivoCabecera($ObjectoAdjunto);
                }
            }

            //adjuntos detalle
            if (isset($request->archivo_adjunto_detalle_list)) {

                // Debugbar::info(count($request->archivo_adjunto_detalle_list) );
                $ObjectoAdjuntoDetalle = json_decode($request->archivoAdjuntoRequerimientoPagoDetalleObject);
                $adjuntoOtrosAdjuntosDetalleLength = $request->archivo_adjunto_detalle_list != null ? count($request->archivo_adjunto_detalle_list) : 0;
                $archivoAdjuntoDetalleList = $request->archivo_adjunto_detalle_list;
                if (count($request->archivo_adjunto_detalle_list) > 0) {


                    foreach ($ObjectoAdjuntoDetalle as $keyObj => $value) {
                        $ObjectoAdjuntoDetalle[$keyObj]->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                        $ObjectoAdjuntoDetalle[$keyObj]->codigo = $requerimientoPago->codigo;
                        if ($adjuntoOtrosAdjuntosDetalleLength > 0) {
                            foreach ($archivoAdjuntoDetalleList as $keyA => $archivo) {
                                if (is_file($archivo)) {
                                    $nombreArchivoAdjunto = $archivo->getClientOriginalName();
                                    if ($nombreArchivoAdjunto == $value->nameFile) {
                                        $ObjectoAdjuntoDetalle[$keyObj]->file = $archivo;
                                    }
                                }
                            }
                        }
                    }


                    $idAdjuntoDetalle[] = $this->guardarAdjuntoRequerimientoPagoDetalle($ObjectoAdjuntoDetalle);
                }
            }
            DB::commit();

            return response()->json([
                'id_requerimiento_pago' => $requerimientoPago->id_requerimiento_pago,
                'mensaje' => 'Se actualizó el requerimiento de pago ' . $requerimientoPago->codigo,
                'afecta_presupuesto_interno_suma' => $afectaPresupuestoInternoSuma ?? [],
                'afecta_presupuesto_interno_resta' => $afectaPresupuestoInternoResta ?? []
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento_pago' => 0, 'mensaje' => 'Hubo un problema al actualizar el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function anularRequerimientoPago(Request $request)
    {
        try {
            DB::beginTransaction();

            $idRequerimientoPago = $request->idRequerimientoPago;
            $output = [];

            if ($idRequerimientoPago > 0) {
                $requerimientoPago = RequerimientoPago::find($idRequerimientoPago);

                // evaluar si el estado del cierre periodo
                $añoPeriodo = Periodo::find($requerimientoPago->id_periodo)->descripcion;
                $idEmpresa = Sede::find($requerimientoPago->id_sede)->id_empresa;
                // $fechaPeriodo = Carbon::createFromFormat('Y-m-d', ($periodo->descripcion.'-01-01'));
                $estadoOperativo = (new CierreAperturaController)->consultarPeriodoOperativo($añoPeriodo, $idEmpresa);
                if ($estadoOperativo != 1) { //1:abierto, 2:cerrado, 3:Declarado
                    return response()->json(['id_requerimiento_pago' => 0, 'tipo_estado' => 'warning',  'mensaje' => 'No se puede anular el requerimiento cuando el periodo operativo está cerrado']);
                }


                $todoDetalleRequerimientoPago = RequerimientoPagoDetalle::where("id_requerimiento_pago", $idRequerimientoPago)->get();

                if (in_array($requerimientoPago->id_estado, [1, 3])) { // estado elaborado, estado observado
                    $requerimientoPago->id_estado = 7;
                    $requerimientoPago->save();

                    // anular detalle requerimiento pago
                    $detalleArray = [];

                    foreach ($todoDetalleRequerimientoPago as $detalleRequerimientoPago) {
                        $detalle = RequerimientoPagoDetalle::where("id_requerimiento_pago_detalle", $detalleRequerimientoPago->id_requerimiento_pago_detalle)->first();
                        $detalle->id_estado = 7;
                        $detalle->save();
                        $detalle->importe_item_para_presupuesto = $detalle->subtotal;
                        $detalleArray[] = $detalle;
                    }

                    // anular adjunto cabecera
                    RequerimientoPagoAdjunto::where('id_requerimiento_pago', '=', $idRequerimientoPago)
                        ->update(['id_estado' => 7]);

                    if (intval($idRequerimientoPago) > 0) {
                        $documento = $this->obtenerIdDocumento(11, $idRequerimientoPago);
                    }
                    $aprobacion = new Aprobacion();
                    $aprobacion->id_flujo = null;
                    $aprobacion->id_doc_aprob = (isset($documento) == true) ? $documento['id'] : null;
                    $aprobacion->id_usuario = Auth::user()->id_usuario;
                    $aprobacion->id_vobo = 7; // Anulado
                    $aprobacion->fecha_vobo = new Carbon();
                    $aprobacion->detalle_observacion = null; // comentario
                    $aprobacion->id_rol = null;
                    $aprobacion->tiene_sustento = false;
                    $aprobacion->save();

                    $output = [
                        'id_requerimiento_pago' => $idRequerimientoPago,
                        'status' => 200,
                        'tipo_estado' => 'success',
                        'mensaje' => 'El requerimiento de pago ' . $requerimientoPago->codigo . ' fue anulado',
                    ];
                } else {
                    $output = [
                        'id_requerimiento_pago' => 0,
                        'status' => 204,
                        'tipo_estado' => 'warning',
                        'mensaje' => 'No se pudo anular el requerimiento de pago, únicamente se puede anular requerimientos en estado elaborado o observado',
                    ];
                }
            } else {
                $output = [
                    'id_requerimiento_pago' => 0,
                    'status' => 204,
                    'tipo_estado' => 'warning',
                    'mensaje' => 'No se pudo anular el requerimiento de pago, el id no es valido',
                ];
            }

            DB::commit();

            return response()->json($output);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento_pago' => 0, 'tipo_estado' => 'error',  'mensaje' => 'Hubo un problema en el método para anular el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }


    function listaCuadroPresupuesto(Request $request)
    {
        $data = CuadroCostoView::where('eliminado', false);

        return datatables($data)->toJson();
    }


    function mostrarRequerimientoPago($idRequerimientoPago)
    {

        $detalleRequerimientoPagoList = RequerimientoPagoDetalle::with('unidadMedida', 'producto', 'partida.presupuesto', 'presupuestoInternoDetalle', 'centroCosto', 'adjunto', 'estado')
            ->where([['id_requerimiento_pago', $idRequerimientoPago], ['id_estado', '!=', 7]])
            ->get();

        $requerimientoPago = RequerimientoPago::where('id_requerimiento_pago', $idRequerimientoPago)
            ->with(
                'tipoRequerimientoPago',
                'periodo',
                'prioridad',
                'moneda',
                'creadoPor',
                'empresa',
                'sede',
                'grupo',
                'division',
                'cuadroPresupuesto',
                'tipoDestinatario',
                'persona.tipoDocumentoIdentidad',
                'cuentaPersona.banco.contribuyente',
                'cuentaPersona.tipoCuenta',
                'cuentaPersona.moneda',
                'contribuyente.tipoDocumentoIdentidad',
                'contribuyente.tipoContribuyente',
                'cuentaContribuyente.banco.contribuyente',
                'cuentaContribuyente.moneda',
                'cuentaContribuyente.tipoCuenta',
                'cuadroCostos',
                'proyecto',
                'adjunto.documentoCompra.DocumentoCompraDetalle',
                'presupuestoInterno'
            )
            ->first();

        $documento = Documento::where([['id_tp_documento', 11], ['id_doc', $idRequerimientoPago]])->first();
        if (!empty($documento)) {
            if ($documento->id_doc_aprob > 0) {
                $aprobacion = Aprobacion::where('id_doc_aprob', $documento->id_doc_aprob)->with('usuario', 'VoBo')->get();
            } else {
                $aprobacion = [];
            }
        } else {
            $aprobacion = [];
        }
        $requerimientoPago->setAttribute('aprobacion', $aprobacion);



        return $requerimientoPago->setAttribute('detalle', $detalleRequerimientoPagoList);
    }

    function listaAdjuntosRequerimientoPagoCabecera($idRequerimientoPago)
    {
        $data = RequerimientoPagoAdjunto::where([['id_requerimiento_pago', $idRequerimientoPago], ['id_estado', '!=', 7]])
            ->with('categoriaAdjunto', 'documentoCompra.DocumentoCompraDetalle.unidadMedida')->get();
        return response()->json($data);
    }
    function listaCategoriaAdjuntos()
    {
        $data = RequerimientoPagoCategoriaAdjunto::where("id_estado", '!=', 7)->get();
        return response()->json($data);
    }

    function listaAdjuntosRequerimientoPagoDetalle($idRequerimientoPagoDetalle)
    {
        $data = RequerimientoPagoAdjuntoDetalle::where([['id_requerimiento_pago_detalle', $idRequerimientoPagoDetalle], ['id_estado', '!=', 7]])->get();
        return response()->json($data);
    }

    function imprimirRequerimientoPagoPdf($idRequerimientoPago)
    {

        $requerimientoPago = $this->mostrarRequerimientoPago($idRequerimientoPago);

        $vista = View::make(
            'tesoreria/requerimiento_pago/export/RequerimientoPagoPdf',
            compact('requerimientoPago')
        )->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($vista);

        return $pdf->stream();
        return $pdf->download('requerimiento-pago.pdf');
    }

    function obtenerDestinatarioPorNumeroDeDocumento(Request $request)
    {
        $nroDocumento = $request->nroDocumento;
        $idTipoDestinatario = $request->idTipoDestinatario;
        $destinatario = [];
        $tipo_estado = '';
        $mensaje = '';

        if ($idTipoDestinatario == 1) { // tipo persona
            $destinatario = Persona::with("tipoDocumentoIdentidad", "cuentaPersona.banco.contribuyente", "cuentaPersona.tipoCuenta", "cuentaPersona.moneda")->where([["nro_documento", $nroDocumento], ["estado", "!=", 7]])->get();
        } elseif ($idTipoDestinatario == 2) { // tipo contribuyente
            $destinatario =  Contribuyente::with("tipoDocumentoIdentidad", "cuentaContribuyente.banco.contribuyente", "cuentaContribuyente.tipoCuenta")->where([["nro_documento", $nroDocumento], ["estado", "!=", 7]])->get();
        } else {
            $tipo_estado = "error";
            $mensaje = 'no se recibio un valor valido para tipo de destinatario';
        }

        if ($destinatario->count() == 1) {
            $tipo_estado = "success";
            $mensaje = 'Destinatario encontrado';
        } elseif ($destinatario->count() > 1) {
            $tipo_estado = "success";
            $mensaje = 'Se encontro más de un destinatario que coincide con el número de documento';
        } else {
            $tipo_estado = "warning";
            $mensaje = 'no se encontro un destinatario';
        }



        return ['data' => $destinatario, 'tipo_estado' => $tipo_estado, 'mensaje' => $mensaje];
    }

    function obtenerDestinatarioPorNombre(Request $request)
    {
        $nombreCompleto =   '%' . (strtoupper($request->nombreDestinatario)) . '%';
        $idTipoDestinatario = $request->idTipoDestinatario;
        $destinatario = [];
        $tipo_estado = '';
        $mensaje = '';


        if ($idTipoDestinatario == 1) { // tipo persona
            $destinatario = Persona::with("tipoDocumentoIdentidad", "cuentaPersona.banco.contribuyente", "cuentaPersona.tipoCuenta", "cuentaPersona.moneda")
                ->whereRaw("UPPER(nombres) LIKE '" . $nombreCompleto . "' AND estado != 7")
                ->orWhereRaw("UPPER(apellido_paterno) LIKE '" . $nombreCompleto . "' AND estado != 7")
                ->orWhereRaw("UPPER(apellido_materno) LIKE '" . $nombreCompleto . "' AND estado != 7")
                ->get();
        } elseif ($idTipoDestinatario == 2) { // tipo contribuyente
            $destinatario =  Contribuyente::with("tipoDocumentoIdentidad", "cuentaContribuyente.banco.contribuyente", "cuentaContribuyente.tipoCuenta", "tipoContribuyente")
                ->whereRaw("UPPER(razon_social) LIKE '" . $nombreCompleto . "'AND estado !=  7")->get();
        } else {
            $tipo_estado = "error";
            $mensaje = 'no se recibio un valor valido para tipo de destinatario';
        }

        if ($destinatario->count() == 1) {
            $tipo_estado = "success";
            $mensaje = 'Destinatario encontrado';
        } elseif ($destinatario->count() > 1) {
            $tipo_estado = "success";
            $mensaje = 'Se encontro más de un destinatario que coincide con el número de documento';
        } else {
            $tipo_estado = "warning";
            $mensaje = 'no se encontro un destinatario';
        }



        return ['data' => $destinatario, 'tipo_estado' => $tipo_estado, 'mensaje' => $mensaje];
    }

    function guardarContribuyente(Request $request)
    {
        try {
            DB::beginTransaction();
            $array = [];

            $contribuyente = DB::table('contabilidad.adm_contri')
                ->where('nro_documento', trim($request->nuevo_nro_documento))
                ->first();

            if ($contribuyente !== null) {
                $array = array(
                    'id_contribuyente' => 0,
                    'tipo_estado' => 'warning',
                    'mensaje' => 'Ya existe el número documento ingresado',
                );
            } else {

                $contribuyente = new Contribuyente();
                $contribuyente->nro_documento = trim($request->nuevo_nro_documento);
                $contribuyente->id_doc_identidad = $request->id_doc_identidad;
                $contribuyente->razon_social = strtoupper(trim($request->nuevo_razon_social));
                $contribuyente->telefono = trim($request->telefono);
                $contribuyente->direccion_fiscal = trim($request->direccion_fiscal);
                $contribuyente->fecha_registro = date('Y-m-d H:i:s');
                $contribuyente->estado = 1;
                $contribuyente->transportista = false;
                $contribuyente->save();

                $array = array(
                    'id_contribuyente' => $contribuyente->id_contribuyente,
                    'tipo_estado' => 'success',
                    'mensaje' => 'Se guardó el contribuyente',
                );
            }
            DB::commit();
            return response()->json($array);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(
                array(
                    'id_contribuyente' => '0',
                    'tipo_estado' => 'error',
                    'mensaje' => 'Hubo un problema. Por favor intente de nuevo',
                    'error' => $e->getMessage()
                )
            );
        }
    }
    function guardarPersona(Request $request)
    {
        try {
            DB::beginTransaction();
            $array = [];

            $persona = DB::table('rrhh.rrhh_perso')
                ->where('nro_documento', trim($request->nuevo_nro_documento))
                ->first();

            if ($persona !== null) {
                $array = array(
                    'id_persona' => 0,
                    'tipo_estado' => 'warning',
                    'mensaje' => 'Ya existe el número de documento ingresado',
                );
            } else {

                $persona = new Persona();
                $persona->nro_documento = trim($request->nuevo_nro_documento);
                $persona->id_documento_identidad = $request->id_doc_identidad;
                $persona->nombres = strtoupper(trim($request->nuevo_nombres));
                $persona->apellido_paterno = strtoupper(trim($request->nuevo_apellido_paterno));
                $persona->apellido_materno = strtoupper(trim($request->nuevo_apellido_materno));
                $persona->fecha_registro =  date('Y-m-d H:i:s');
                $persona->estado = 1;
                $persona->save();

                $array = array(
                    'id_persona' => $persona->id_persona,
                    'tipo_estado' => 'success',
                    'mensaje' => 'Se guardó la persona',
                );
            }
            DB::commit();
            return response()->json($array);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(
                array(
                    'id_persona' => '0',
                    'tipo_estado' => 'error',
                    'mensaje' => 'Hubo un problema. Por favor intente de nuevo',
                    'error' => $e->getMessage()
                )
            );
        }
    }
    function guardarCuentaDestinatario(Request $request)
    {
        try {
            DB::beginTransaction();
            $array = [];

            if ($request->id_tipo_destinatario == 1) { //tipo persona

                $cuentaPersona = new CuentaPersona();
                $cuentaPersona->id_persona = $request->id_persona;
                $cuentaPersona->id_banco = $request->banco;
                $cuentaPersona->id_tipo_cuenta = $request->tipo_cuenta_banco;
                $cuentaPersona->nro_cuenta = trim($request->nro_cuenta);
                $cuentaPersona->nro_cci = trim($request->nro_cuenta_interbancaria);
                $cuentaPersona->id_moneda = $request->moneda;
                $cuentaPersona->fecha_registro = date('Y-m-d H:i:s');
                $cuentaPersona->estado = 1;
                $cuentaPersona->save();

                $idCuenta = $cuentaPersona->id_cuenta_bancaria;
            } elseif ($request->id_tipo_destinatario == 2) { //tipo contribuyente

                $cuentaContribuyente = new CuentaContribuyente();
                $cuentaContribuyente->id_contribuyente = $request->id_contribuyente;
                $cuentaContribuyente->id_banco = $request->banco;
                $cuentaContribuyente->id_tipo_cuenta = $request->tipo_cuenta_banco;
                $cuentaContribuyente->nro_cuenta = trim($request->nro_cuenta);
                $cuentaContribuyente->nro_cuenta_interbancaria = trim($request->nro_cuenta_interbancaria);
                $cuentaContribuyente->id_moneda = $request->moneda;
                $cuentaContribuyente->fecha_registro = date('Y-m-d H:i:s');
                $cuentaContribuyente->estado = 1;
                $cuentaContribuyente->save();

                $idCuenta = $cuentaContribuyente->id_cuenta_contribuyente;
            }

            if ($idCuenta > 0) {
                $array = array(
                    'id_cuenta' => $idCuenta,
                    'id_tipo_destinatario' => $request->id_tipo_destinatario,
                    'tipo_estado' => 'success',
                    'mensaje' => 'Se guardó la cuenta',
                );
            } else {
                $array = array(
                    'id_cuenta' => 0,
                    'id_tipo_destinatario' => $request->id_tipo_destinatario,
                    'tipo_estado' => 'warning',
                    'mensaje' => 'Hubo un problema al intentar guardar la cuenta',
                );
            }
            DB::commit();
            return response()->json($array);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(
                array(
                    'id_cuenta' => 0,
                    'id_tipo_destinatario' => 0,
                    'tipo_estado' => 'error',
                    'mensaje' => 'Hubo un problema. Por favor intente de nuevo',
                    'error' => $e->getMessage()
                )
            );
        }
    }

    function obtenerCuentaPersona($idPersona)
    {
        // $data =DB::connection('pgsql_rrhh')->table('rrhh.rrhh_perso')->where('id_persona', $idPersona)->get();
        // return $data;
        $data = CuentaPersona::with("banco.contribuyente", "moneda", "tipoCuenta")->where([["id_persona", $idPersona], ["estado", "!=", 7]])->get();
        if (!empty($data) && $data->count() > 0) {
            $array = [
                'tipo_estado' => 'success',
                'data' => $data,
                'mensaje' => 'Ok',
            ];
        } else {
            $array = [
                'tipo_estado' => 'warning',
                'data' => [],
                'mensaje' => 'Sin cuentas bancarias para mostrar',
            ];
        }
        return $array;
    }
    function obtenerCuentaContribuyente($idContribuyente)
    {

        $data = CuentaContribuyente::with("banco.contribuyente", "moneda", "tipoCuenta")->where([["id_contribuyente", $idContribuyente], ["estado", "!=", 7]])->get();
        if (!empty($data) && $data->count() > 0) {
            $array = [
                'tipo_estado' => 'success',
                'data' => $data,
                'mensaje' => 'Ok',
            ];
        } else {
            $array = [
                'tipo_estado' => 'warning',
                'data' => [],
                'mensaje' => 'Sin cuentas bancarias para mostrar',
            ];
        }
        return $array;
    }

    public function listarTodoArchivoAdjuntoRequerimientoPago($idRequerimientoPago)
    {

        $requerimientoPago = RequerimientoPago::find($idRequerimientoPago);
        $idUsuarioPropietarioRequerimiento = $requerimientoPago->id_usuario ?? '';

        $detalleRequerimientoPagoList = RequerimientoPagoDetalle::where([["id_requerimiento_pago", $idRequerimientoPago], ["id_estado", "!=", 7]])->get();
        $idDetalleRequerimientoPagoList = [];
        foreach ($detalleRequerimientoPagoList as $dr) {
            $idDetalleRequerimientoPagoList[] = $dr->id_requerimiento_pago_detalle;
        }
        $ajuntosCabeceraList = RequerimientoPagoAdjunto::with("tipoDocumento", "moneda")->where([["id_requerimiento_pago", $idRequerimientoPago], ["id_estado", "!=", 7]])->get();
        if (count($idDetalleRequerimientoPagoList) > 0) {
            $adjuntoDetalleList = RequerimientoPagoAdjuntoDetalle::whereIn("id_requerimiento_pago_detalle", $idDetalleRequerimientoPagoList)->where("id_estado", "!=", 7)->get();
        }

        return ["adjuntos_cabecera" => $ajuntosCabeceraList ?? [], "adjuntos_detalle" => $adjuntoDetalleList ?? [], 'id_usuario_propietario_requerimiento' => $idUsuarioPropietarioRequerimiento];
    }


    function anularAdjuntoRequerimientoPagoCabecera(Request $request)
    {
        DB::beginTransaction();
        try {

            $estado_accion = '';
            $adjunto = RequerimientoPagoAdjunto::find($request->id_adjunto);
            if (isset($adjunto)) {
                $adjunto->id_estado = 7;
                $adjunto->save();
                $estado_accion = 'success';
                $mensaje = 'Adjuntos anulado';
            } else {
                $estado_accion = 'warning';
                $mensaje = 'Hubo un problema y no se pudo anular el adjuntos';
            }
            DB::commit();

            return response()->json(['status' => $estado_accion, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'mensaje' => 'Hubo un problema al anular el adjuntos. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function anularAdjuntoRequerimientoPagoDetalle(Request $request)
    {
        DB::beginTransaction();
        try {

            $estado_accion = '';
            $adjunto = RequerimientoPagoAdjuntoDetalle::find($request->id_adjunto);
            if (isset($adjunto)) {
                $adjunto->id_estado = 7;
                $adjunto->save();
                $estado_accion = 'success';
                $mensaje = 'Adjuntos anulado';
            } else {
                $estado_accion = 'warning';
                $mensaje = 'Hubo un problema y no se pudo anular el adjuntos';
            }
            DB::commit();

            return response()->json(['status' => $estado_accion, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'mensaje' => 'Hubo un problema al anular el adjuntos. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    public function duplicarRequerimientoPagoYActualizarCodigo($id, $idEstado)
    {
        DB::beginTransaction();
        try {

            $data = [];
            $status = '';
            $msj = '';

            if ($id > 0) {
                $requerimientoPago = RequerimientoPago::find($id);
                $nuevoRequerimientoPago = $requerimientoPago->replicate();
                $nuevoRequerimientoPago->fecha_registro = Carbon::now();
                $nuevoRequerimientoPago->id_estado = $idEstado;
                $nuevoRequerimientoPago->save();

                $RequerimientoPagoDetalle = RequerimientoPagoDetalle::where('id_requerimiento_pago', $id)->get();
                foreach ($RequerimientoPagoDetalle as $d) {
                    $detReqPago = RequerimientoPagoDetalle::find($d->id_requerimiento_pago_detalle);
                    $nuevoRequerimientoPagoDetalle = $detReqPago->replicate();
                    $nuevoRequerimientoPagoDetalle->id_requerimiento_pago = $nuevoRequerimientoPago->id_requerimiento_pago;
                    $nuevoRequerimientoPagoDetalle->fecha_registro = Carbon::now();
                    $nuevoRequerimientoPagoDetalle->id_estado = $idEstado;
                    $nuevoRequerimientoPagoDetalle->save();
                }

                $status = 'success';
                $msj = 'requerimiento de pago duplicado';
                $data = ['id_requerimiento_pago' => $nuevoRequerimientoPago->id_requerimiento_pago];



                DB::commit();

                $nuevoCodigo = RequerimientoPago::crearCodigo($requerimientoPago->id_grupo, $requerimientoPago->id_requerimiento_pago, $nuevoRequerimientoPago->periodo);
                $rp = RequerimientoPago::find($nuevoRequerimientoPago->id_requerimiento_pago);
                $rp->codigo = $requerimientoPago->codigo;
                $rp->save();

                $documento = new Documento();
                $documento->id_tp_documento = 11; // requerimiento pago
                $documento->codigo_doc = $requerimientoPago->codigo;
                $documento->id_doc = $nuevoRequerimientoPago->id_requerimiento_pago;
                $documento->save();

                $requerimientoPago = RequerimientoPago::find($id);
                $requerimientoPago->codigo = $nuevoCodigo;
                $requerimientoPago->save();
            } else {
                $status = 'warning';
                $msj = 'Id enviado no es valido';
            }

            return ['data' => $data, 'status' => $status, 'mensaje' => $msj];
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['data' => [], 'status' => $status, 'mensaje' => 'Hubo un problema al intentar duplicar el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }
    public function listadoRequerimientoPagoExportExcel($meOrAll, $idEmpresa, $idSede, $idGrupo, $idDivision, $fechaRegistroDesde, $fechaRegistroHasta, $idEstado)
    {
        # code...
        return Excel::download(new ListadoRequerimientoPagoExport($meOrAll, $idEmpresa, $idSede, $idGrupo, $idDivision, $fechaRegistroDesde, $fechaRegistroHasta, $idEstado), 'listado_requerimiento_pago.xlsx');;
    }
    public function listadoItemsRequerimientoPagoExportExcel($meOrAll, $idEmpresa, $idSede, $idGrupo, $idDivision, $fechaRegistroDesde, $fechaRegistroHasta, $idEstado)
    {
        return Excel::download(new ListadoItemsRequerimientoPagoExport($meOrAll, $idEmpresa, $idSede, $idGrupo, $idDivision, $fechaRegistroDesde, $fechaRegistroHasta, $idEstado), 'listado_items_requerimiento_pago.xlsx');;
    }
    public function obtenerRequerimientosElaborados($meOrAll, $idEmpresa, $idSede, $idGrupo, $idDivision, $fechaRegistroDesde, $fechaRegistroHasta, $idEstado)
    {
        # code...
        $GrupoDeUsuarioEnSesionList = Auth::user()->getAllGrupo();
        $idGrupoDeUsuarioEnSesionList = [];
        foreach ($GrupoDeUsuarioEnSesionList as $grupo) {
            $idGrupoDeUsuarioEnSesionList[] = $grupo->id_grupo; // lista de id_rol del usuario en sesion
        }
        $data = RequerimientoPago::with('detalle')
            ->leftJoin('tesoreria.requerimiento_pago_tipo', 'requerimiento_pago_tipo.id_requerimiento_pago_tipo', '=', 'requerimiento_pago.id_requerimiento_pago_tipo')
            ->leftJoin('administracion.adm_estado_doc', 'requerimiento_pago.id_estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('administracion.adm_prioridad', 'requerimiento_pago.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'requerimiento_pago.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'requerimiento_pago.id_sede')
            ->leftJoin('configuracion.sis_moneda', 'requerimiento_pago.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'requerimiento_pago.id_periodo')
            ->leftJoin('administracion.adm_empresa', 'requerimiento_pago.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'requerimiento_pago.id_usuario')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'requerimiento_pago.id_division')
            ->leftJoin('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'requerimiento_pago.id_proyecto')

            ->select(
                'adm_estado_doc.estado_doc',
                'requerimiento_pago.*',
                'requerimiento_pago_tipo.descripcion as descripcion_requerimiento_pago_tipo',
                'sis_moneda.descripcion as descripcion_moneda',
                'sis_moneda.simbolo as simbolo_moneda',
                'adm_periodo.descripcion as periodo',
                'adm_prioridad.descripcion as prioridad',
                'sis_grupo.descripcion as grupo',
                'sis_sede.codigo as sede',
                'sis_sede.descripcion as descripcion_empresa_sede',
                'division.descripcion as division',
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'adm_contri.razon_social as empresa_razon_social',
                'adm_contri.nro_documento as empresa_nro_documento',
                'sis_identi.descripcion as empresa_tipo_documento',
                'sis_usua.nombre_corto as usuario_nombre_corto',
                DB::raw("(SELECT COUNT(registro_pago.id_pago)
                FROM tesoreria.registro_pago
                WHERE  registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago AND registro_pago.adjunto IS NOT NULL AND
                registro_pago.estado != 7) AS cantidad_adjuntos_pago"),
                // DB::raw("(SELECT  jsonb_agg(DISTINCT jsonb_build_object('vobo', adm_vobo.descripcion, 'usuario', sis_usua.nombre_corto, 'fecha_vobo', adm_aprobacion.fecha_vobo))
                // FROM administracion.adm_documentos_aprob
                //      INNER JOIN administracion.adm_aprobacion ON adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                //     LEFT JOIN administracion.adm_vobo ON adm_vobo.id_vobo = adm_aprobacion.id_vobo
                //     LEFT JOIN configuracion.sis_usua ON sis_usua.id_usuario = adm_aprobacion.id_usuario
                // WHERE requerimiento_pago.id_requerimiento_pago = adm_documentos_aprob.id_doc and adm_documentos_aprob.id_tp_documento =11 ) AS flujo_aprobacion")

                DB::raw("(SELECT sis_usua.nombre_corto
                FROM administracion.adm_documentos_aprob
                     INNER JOIN administracion.adm_aprobacion ON adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob
                    LEFT JOIN administracion.adm_vobo ON adm_vobo.id_vobo = adm_aprobacion.id_vobo
                    LEFT JOIN configuracion.sis_usua ON sis_usua.id_usuario = adm_aprobacion.id_usuario
                WHERE requerimiento_pago.id_requerimiento_pago = adm_documentos_aprob.id_doc and adm_documentos_aprob.id_tp_documento =11 and adm_aprobacion.id_vobo =1 order by adm_aprobacion.fecha_vobo desc limit 1 ) AS ultimo_aprobador")

            )
            ->when(($meOrAll === 'ME'), function ($query) {
                $idUsuario = Auth::user()->id_usuario;
                return $query->whereRaw('requerimiento_pago.id_usuario = ' . $idUsuario);
            })
            ->when(($meOrAll === 'ALL'), function ($query) {
                return $query->whereRaw('requerimiento_pago.id_usuario > 0');
            })
            ->when((intval($idEmpresa) > 0), function ($query)  use ($idEmpresa) {
                return $query->whereRaw('requerimiento_pago.id_empresa = ' . $idEmpresa);
            })
            ->when((intval($idSede) > 0), function ($query)  use ($idSede) {
                return $query->whereRaw('requerimiento_pago.id_sede = ' . $idSede);
            })
            ->when((intval($idGrupo) > 0), function ($query)  use ($idGrupo) {
                return $query->whereRaw('sis_grupo.id_grupo = ' . $idGrupo);
            })
            ->when((intval($idDivision) > 0), function ($query)  use ($idDivision) {
                return $query->whereRaw('requerimiento_pago.division_id = ' . $idDivision);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde) {
                return $query->where('requerimiento_pago.fecha_registro', '>=', $fechaRegistroDesde);
            })
            ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroHasta) {
                return $query->where('requerimiento_pago.fecha_registro', '<=', $fechaRegistroHasta);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde, $fechaRegistroHasta) {
                return $query->whereBetween('requerimiento_pago.fecha_registro', [$fechaRegistroDesde, $fechaRegistroHasta]);
            })

            ->when((intval($idEstado) > 0), function ($query)  use ($idEstado) {
                return $query->whereRaw('requerimiento_pago.id_estado = ' . $idEstado);
            })

            ->whereIn('requerimiento_pago.id_grupo', $idGrupoDeUsuarioEnSesionList);

        return $data;
    }
    public function obtenerRequerimientosPagoElaboradosConDetalle($id_requerimiento_pago)
    {
        // $id_requerimiento_pago=50;
        // $detalleRequerimientoPagoList = RequerimientoPagoDetalle::with('unidadMedida', 'producto', 'partida.presupuesto', 'centroCosto', 'adjunto', 'estado')
        // ->where([['id_requerimiento_pago', $id_requerimiento_pago],['id_estado','!=',7]])
        // ->get();

        $detalleRequerimientoPagoList_2 = DB::table('tesoreria.requerimiento_pago_detalle')
            ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'requerimiento_pago_detalle.id_partida')
            ->leftJoin('finanzas.centro_costo', 'centro_costo.id_centro_costo', '=', 'requerimiento_pago_detalle.id_centro_costo')
            ->select(
                'requerimiento_pago_detalle.*',
                'presup_par.codigo as partida',
                'presup_par.descripcion as descripcion_partida',
                'presup_par.id_partida',
                'centro_costo.codigo as c_costo',
                'centro_costo.descripcion as descripcion_c_costo',
                'centro_costo.id_centro_costo'
            )
            ->where('requerimiento_pago_detalle.id_requerimiento_pago', $id_requerimiento_pago)
            ->get();

        $requerimientoPago = DB::table('tesoreria.requerimiento_pago')
            ->select('requerimiento_pago.id_requerimiento_pago', 'requerimiento_pago.fecha_autorizacion')
            ->where('id_requerimiento_pago', $id_requerimiento_pago)
            ->first();

        $requerimientoPago->detalle = $detalleRequerimientoPagoList_2;
        // $detalleRequerimientoPagoList=(object)$detalleRequerimientoPagoList;
        // var_dump($detalleRequerimientoPagoList->detalle);exit;
        // $requerimientoPago = RequerimientoPago::where('id_requerimiento_pago', $id_requerimiento_pago)
        //     ->with(
        //         'tipoRequerimientoPago',
        //         'periodo',
        //         'prioridad',
        //         'moneda',
        //         'creadoPor',
        //         'empresa',
        //         'sede',
        //         'grupo',
        //         'division',
        //         'cuadroPresupuesto',
        //         'tipoDestinatario',
        //         'persona.tipoDocumentoIdentidad',
        //         'cuentaPersona.banco.contribuyente',
        //         'cuentaPersona.tipoCuenta',
        //         'cuentaPersona.moneda',
        //         'contribuyente.tipoDocumentoIdentidad',
        //         'contribuyente.tipoContribuyente',
        //         'cuentaContribuyente.banco.contribuyente',
        //         'cuentaContribuyente.moneda',
        //         'cuentaContribuyente.tipoCuenta',
        //         'cuadroCostos',
        //         'proyecto',
        //         'adjunto'
        //     )
        //     ->first();

        // $documento = Documento::where([['id_tp_documento', 11], ['id_doc', $id_requerimiento_pago]])->first();
        // if (!empty($documento)) {
        //     if ($documento->id_doc_aprob > 0) {
        //         $aprobacion = Aprobacion::where('id_doc_aprob', $documento->id_doc_aprob)->with('usuario', 'VoBo')->get();
        //     } else {
        //         $aprobacion = [];
        //     }
        // } else {
        //     $aprobacion = [];
        // }
        // $requerimientoPago->setAttribute('aprobacion', $aprobacion);



        // return $requerimientoPago->detalle = $detalleRequerimientoPagoList;
        return $requerimientoPago;
    }
    public function obtenerItemsRequerimientoPagoElaborados($meOrAll, $idEmpresa, $idSede, $idGrupo, $idDivision, $fechaRegistroDesde, $fechaRegistroHasta, $idEstado)
    {

        $detalleRequerimientoPagoList_2 = DB::table('tesoreria.requerimiento_pago_detalle')
            ->leftJoin('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'requerimiento_pago_detalle.id_requerimiento_pago')
            ->leftJoin('finanzas.presupuesto_interno', 'presupuesto_interno.id_presupuesto_interno', '=', 'requerimiento_pago.id_presupuesto_interno')
            ->leftJoin('configuracion.sis_moneda', 'requerimiento_pago.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_prioridad', 'requerimiento_pago.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'requerimiento_pago.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'requerimiento_pago.id_sede')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'requerimiento_pago.id_division')
            ->leftJoin('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'requerimiento_pago.id_proyecto')
            ->leftJoin('administracion.adm_empresa', 'requerimiento_pago.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')

            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'requerimiento_pago.id_cc')
            ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('tesoreria.requerimiento_pago_tipo', 'requerimiento_pago_tipo.id_requerimiento_pago_tipo', '=', 'requerimiento_pago.id_requerimiento_pago_tipo')

            ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'requerimiento_pago_detalle.id_partida')
            ->leftJoin('finanzas.presup', 'presup.id_presup', '=', 'presup_par.id_presup')
            ->leftJoin('finanzas.centro_costo', 'centro_costo.id_centro_costo', '=', 'requerimiento_pago_detalle.id_centro_costo')
            ->leftJoin('finanzas.centro_costo as padre_centro_costo', 'padre_centro_costo.id_centro_costo', '=', 'centro_costo.id_padre')

            ->leftJoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago.id_estado', '=', 'requerimiento_pago_estado.id_requerimiento_pago_estado')

            ->select(
                'requerimiento_pago_detalle.descripcion',
                'requerimiento_pago_detalle.motivo',
                'requerimiento_pago_detalle.cantidad',
                'requerimiento_pago_detalle.precio_unitario',
                'requerimiento_pago_detalle.subtotal',
                'requerimiento_pago_detalle.fecha_registro',
                'adm_prioridad.descripcion as prioridad',
                'requerimiento_pago_tipo.descripcion AS tipo_requerimiento',

                'requerimiento_pago.codigo',
                'oportunidades.codigo_oportunidad',
                'requerimiento_pago.concepto',
                'requerimiento_pago.comentario',
                'sis_moneda.simbolo as simbolo_moneda',
                'sis_sede.codigo as sede',
                'sis_sede.descripcion as descripcion_empresa_sede',
                'adm_contri.razon_social as empresa_razon_social',
                'sis_identi.descripcion as empresa_tipo_documento',
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'sis_grupo.descripcion as grupo',
                'division.descripcion as division',
                'requerimiento_pago.monto_total',
                'presup_par.codigo as partida',
                'presup_par.descripcion as descripcion_partida',
                'presup_par.id_partida',
                'padre_centro_costo.codigo as padre_centro_costo',
                'padre_centro_costo.descripcion as padre_descripcion_centro_costo',
                'centro_costo.codigo as centro_costo',
                'centro_costo.descripcion as descripcion_centro_costo',
                'centro_costo.id_centro_costo',
                'requerimiento_pago_estado.descripcion as estado_requerimiento',
                'presup.codigo as codigo_presupuesto_old',
                'presup.descripcion as descripcion_presupuesto_old',
                'presupuesto_interno.codigo as codigo_presupuesto_interno',
                'presupuesto_interno.descripcion as descripcion_presupuesto_interno',
                DB::raw("(SELECT presup_titu.descripcion
                FROM finanzas.presup_titu
                WHERE presup_titu.codigo = presup_par.cod_padre and presup_titu.id_presup=presup_par.id_presup limit 1) AS descripcion_partida_padre"),
                DB::raw("(SELECT presupuesto_interno_detalle.partida
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = requerimiento_pago_detalle.id_partida_pi and requerimiento_pago.id_presupuesto_interno > 0 limit 1) AS codigo_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_detalle.descripcion
                FROM finanzas.presupuesto_interno_detalle
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = requerimiento_pago_detalle.id_partida_pi and requerimiento_pago.id_presupuesto_interno > 0 limit 1) AS descripcion_sub_partida_presupuesto_interno"),
                DB::raw("(SELECT presupuesto_interno_modelo.descripcion
                FROM finanzas.presupuesto_interno_detalle
                inner join finanzas.presupuesto_interno_modelo on presupuesto_interno_modelo.id_modelo_presupuesto_interno = presupuesto_interno_detalle.id_padre
                WHERE presupuesto_interno_detalle.id_presupuesto_interno_detalle = requerimiento_pago_detalle.id_partida_pi and requerimiento_pago.id_presupuesto_interno > 0 limit 1) AS descripcion_partida_presupuesto_interno")

            )
            ->when(($meOrAll === 'ME'), function ($query) {
                $idUsuario = Auth::user()->id_usuario;
                return $query->whereRaw('requerimiento_pago.id_usuario = ' . $idUsuario);
            })
            ->when(($meOrAll === 'ALL'), function ($query) {
                return $query->whereRaw('requerimiento_pago.id_usuario > 0');
            })
            ->when((intval($idEmpresa) > 0), function ($query)  use ($idEmpresa) {
                return $query->whereRaw('requerimiento_pago.id_empresa = ' . $idEmpresa);
            })
            ->when((intval($idSede) > 0), function ($query)  use ($idSede) {
                return $query->whereRaw('requerimiento_pago.id_sede = ' . $idSede);
            })
            ->when((intval($idGrupo) > 0), function ($query)  use ($idGrupo) {
                return $query->whereRaw('sis_grupo.id_grupo = ' . $idGrupo);
            })
            ->when((intval($idDivision) > 0), function ($query)  use ($idDivision) {
                return $query->whereRaw('requerimiento_pago.division_id = ' . $idDivision);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde) {
                return $query->where('requerimiento_pago.fecha_registro', '>=', $fechaRegistroDesde);
            })
            ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroHasta) {
                return $query->where('requerimiento_pago.fecha_registro', '<=', $fechaRegistroHasta);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use ($fechaRegistroDesde, $fechaRegistroHasta) {
                return $query->whereBetween('requerimiento_pago.fecha_registro', [$fechaRegistroDesde, $fechaRegistroHasta]);
            })

            ->when((intval($idEstado) > 0), function ($query)  use ($idEstado) {
                return $query->whereRaw('requerimiento_pago.id_estado = ' . $idEstado);
            })
            ->where([['requerimiento_pago_detalle.id_estado', '!=', 7], ['requerimiento_pago.id_estado', '!=', 7]])
            ->orderBy('requerimiento_pago_detalle.fecha_registro', 'desc')
            ->get();

        // $requerimientoPago = DB::table('tesoreria.requerimiento_pago')
        // ->select('requerimiento_pago.id_requerimiento_pago', 'requerimiento_pago.fecha_autorizacion')
        // ->where('id_requerimiento_pago', $id_requerimiento_pago)
        // ->first();

        return $detalleRequerimientoPagoList_2;
    }

    public function ordenesPago($id)
    {
        // $id=50;
        $detalles = DB::table('tesoreria.registro_pago')
            ->select(
                'registro_pago.*',
                // 'sis_usua.nombre_corto',
                // 'sis_moneda.simbolo',
                // 'adm_contri.razon_social as razon_social_empresa',
                // 'adm_cta_contri.nro_cuenta',
                DB::raw("(SELECT count(adjunto) FROM tesoreria.registro_pago_adjuntos
                      WHERE registro_pago_adjuntos.id_pago = registro_pago.id_pago
                        and registro_pago_adjuntos.estado != 7) AS count_adjuntos")
            )
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'registro_pago.registrado_por')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'registro_pago.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'registro_pago.id_cuenta_origen');

        $query = $detalles->join('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'registro_pago.id_requerimiento_pago')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'requerimiento_pago.id_moneda')
            ->where([['registro_pago.id_requerimiento_pago', '=', $id], ['registro_pago.estado', '!=', 7]])
            ->get();

        return $query;
    }
    public function obtenerOtrosAdjuntosTesoreria($id_requerimiento_pago)
    {
        // return $id_requerimiento_pago;exit;

        // return $adjuntos_pagos_complementarios;exit;
        $adjuntos_pagos_complementarios = OtrosAdjuntosTesoreria::select(
            'otros_adjuntos.*',
            'requerimiento_pago_categoria_adjunto.descripcion'
        )
            ->where('id_requerimiento_pago', $id_requerimiento_pago)
            ->join('tesoreria.requerimiento_pago_categoria_adjunto', 'requerimiento_pago_categoria_adjunto.id_requerimiento_pago_categoria_adjunto', '=', 'otros_adjuntos.id_categoria_adjunto')
            ->where('otros_adjuntos.id_estado', '!=', 7)
            ->get();

        $adjuntos_pagos = RegistroPago::select('registro_pago_adjuntos.adjunto', 'registro_pago_adjuntos.id_adjunto')
            ->where('id_requerimiento_pago', $id_requerimiento_pago)
            ->join('tesoreria.registro_pago_adjuntos', 'registro_pago_adjuntos.id_pago', '=', 'registro_pago.id_pago')
            ->get();


        if (sizeof($adjuntos_pagos_complementarios) > 0) {
            foreach ($adjuntos_pagos_complementarios as $key => $value) {
                $value->fecha_registro = date("d/m/Y", strtotime($value->fecha_registro));
            }
        }
        return response()->json([
            "success" => true,
            "status" => 200,
            "data" => $adjuntos_pagos_complementarios,
            "data_pagos" => $adjuntos_pagos
        ]);
    }
}
