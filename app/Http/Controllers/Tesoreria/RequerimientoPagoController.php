<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProyectosController;
use App\Models\Administracion\Aprobacion;
use App\Models\Administracion\Division;
use App\Models\Administracion\Documento;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Almacen\Trazabilidad;
use App\Models\Almacen\UnidadMedida;
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
        $nombreUsuario = Auth::user()->trabajador->postulante->persona->nombre_completo;

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
                'nombreUsuario'
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
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'adm_contri.razon_social as empresa_razon_social',
                'adm_contri.nro_documento as empresa_nro_documento',
                'sis_identi.descripcion as empresa_tipo_documento',
                'sis_usua.nombre_corto as usuario_nombre_corto',
                DB::raw("(SELECT COUNT(registro_pago.id_pago) 
                FROM tesoreria.registro_pago 
                WHERE  registro_pago.id_requerimiento_pago = requerimiento_pago.id_requerimiento_pago AND registro_pago.adjunto IS NOT NULL AND
                registro_pago.estado != 7) AS cantidad_adjuntos_pago")
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

            $requerimientoPago->save();
            $requerimientoPago->adjuntoOtrosAdjuntos = $request->archivoAdjuntoRequerimientoPagoCabeceraFile1;
            $requerimientoPago->adjuntoOrdenes = $request->archivoAdjuntoRequerimientoPagoCabeceraFile2;
            $requerimientoPago->adjuntoComprobanteBancario = $request->archivoAdjuntoRequerimientoPagoCabeceraFile3;
            $requerimientoPago->adjuntoComprobanteContable = $request->archivoAdjuntoRequerimientoPagoCabeceraFile4;

            $count = count($request->descripcion);
            $montoTotal = 0;
            for ($i = 0; $i < $count; $i++) {
                if ($request->cantidad[$i] <= 0) {
                    return response()->json(['id_requerimiento_pago' => 0, 'codigo' => '', 'mensaje' => 'La cantidad solicitada debe ser mayor a 0']);
                }

                $detalle = new RequerimientoPagoDetalle();
                $detalle->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                $detalle->id_tipo_item = $request->tipoItem[$i];
                $detalle->id_partida = $request->idPartida[$i];
                $detalle->id_centro_costo = $request->idCentroCosto[$i];
                $detalle->descripcion = $request->descripcion[$i];
                $detalle->id_unidad_medida = $request->unidad[$i];
                $detalle->cantidad = $request->cantidad[$i];
                $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                $detalle->fecha_registro = new Carbon();
                $detalle->id_estado = 1;
                $detalle->save();
                $detalle->idRegister = $request->idRegister[$i];
                $detalleArray[] = $detalle;
                $montoTotal += $detalle->cantidad * $detalle->precioUnitario;
            }




            DB::commit();

            $codigo = RequerimientoPago::crearCodigo($request->grupo, $requerimientoPago->id_requerimiento_pago);
            $rp = RequerimientoPago::find($requerimientoPago->id_requerimiento_pago);
            $rp->codigo = $codigo;
            $rp->save();

            $documento = new Documento();
            $documento->id_tp_documento = 11; // requerimiento pago
            $documento->codigo_doc = $requerimientoPago->codigo;
            $documento->id_doc = $requerimientoPago->id_requerimiento_pago;
            $documento->save();


            $this->guardarAdjuntoRequerimientoPagoCabecera($requerimientoPago, $codigo);

            // guardar adjuntos nivel detalle
            $adjuntoRequerimientoPagoDetalleArray = [];
            for ($i = 0; $i < count($detalleArray); $i++) {
                $archivos = $request->{"archivoAdjuntoRequerimientoPagoDetalle" . $detalleArray[$i]['idRegister']};
                if (isset($archivos)) {
                    foreach ($archivos as $archivo) {
                        $adjuntoRequerimientoPagoDetalleArray[] = [
                            'id_requerimiento_pago_detalle' => $detalleArray[$i]['id_requerimiento_pago_detalle'],
                            'nombre_archivo' => $archivo->getClientOriginalName(),
                            'archivo' => $archivo
                        ];
                    }
                }
            }

            if (count($adjuntoRequerimientoPagoDetalleArray) > 0) {
                $this->guardarAdjuntoRequerimientoPagoDetalle($adjuntoRequerimientoPagoDetalleArray, $codigo);
            }



            return response()->json(['id_requerimiento_pago' => $requerimientoPago->id_requerimiento_pago, 'mensaje' => 'Se guardó el requerimiento de pago ' . $codigo]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento_pago' => 0, 'mensaje' => 'Hubo un problema al guardar el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    function guardarAdjuntoRequerimientoPagoCabecera($requerimientoPago, $codigoRequerimientoPago)
    {
        $adjuntoOtrosAdjuntosLength = $requerimientoPago->adjuntoOtrosAdjuntos != null ? count($requerimientoPago->adjuntoOtrosAdjuntos) : 0;
        $adjuntoOrdenesLength = $requerimientoPago->adjuntoOrdenes != null ? count($requerimientoPago->adjuntoOrdenes) : 0;
        $adjuntoComprobanteContableLength = $requerimientoPago->adjuntoComprobanteContable != null ? count($requerimientoPago->adjuntoComprobanteContable) : 0;
        $adjuntoComprobanteBancarioLength = $requerimientoPago->adjuntoComprobanteBancario != null ? count($requerimientoPago->adjuntoComprobanteBancario) : 0;


        if ($adjuntoOtrosAdjuntosLength > 0) {
            $this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $requerimientoPago->adjuntoOtrosAdjuntos, $codigoRequerimientoPago, 1);
        }
        if ($adjuntoOrdenesLength > 0) {
            $this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $requerimientoPago->adjuntoOrdenes, $codigoRequerimientoPago, 2);
        }
        if ($adjuntoComprobanteContableLength > 0) {
            $this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $requerimientoPago->adjuntoComprobanteContable, $codigoRequerimientoPago, 3);
        }
        if ($adjuntoComprobanteBancarioLength > 0) {
            $this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $requerimientoPago->adjuntoComprobanteBancario, $codigoRequerimientoPago, 4);
        }
    }


    function subirYRegistrarArchivoCabecera($idRequerimientoPago, $adjunto, $codigoRequerimientoPago, $idCategoria)
    {
        $idAdjuntoRequerimientoPagoList=[];
        foreach ($adjunto as $key => $archivo) {
            if ($archivo != null) {
                $fechaHoy = new Carbon();
                $sufijo = $fechaHoy->format('YmdHis');
                $file = $archivo->getClientOriginalName();
                $codigo = $codigoRequerimientoPago;
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $newNameFile = $codigo . '_' . $key . $idCategoria . $sufijo . '.' . $extension;
                Storage::disk('archivos')->put("necesidades/requerimientos/pago/cabecera/" . $newNameFile, File::get($archivo));

                $idAdjuntoRequerimientoPago = DB::table('tesoreria.requerimiento_pago_adjunto')->insertGetId(
                    [
                        'id_requerimiento_pago'     => $idRequerimientoPago,
                        'archivo'                   => $newNameFile,
                        'id_estado'                 => 1,
                        'id_categoria_adjunto'      => $idCategoria,
                        'fecha_registro'            => $fechaHoy
                    ],
                    'id_requerimiento_pago_adjunto'
                );
                $idAdjuntoRequerimientoPagoList[]= $idAdjuntoRequerimientoPago;
            }
        }
        return $idAdjuntoRequerimientoPagoList;

    }

    function guardarAdjuntoRequerimientoPagoDetalle($adjuntoRequerimientoPagoDetalleArray, $codigoRequerimientoPago)
    {

        // $adjuntoRequerimientoPagoDetalle = 0;
        if ($adjuntoRequerimientoPagoDetalleArray != null && count($adjuntoRequerimientoPagoDetalleArray) > 0) {
            foreach ($adjuntoRequerimientoPagoDetalleArray as $key => $adjunto) {
                $fechaHoy = new Carbon();
                $sufijo = $fechaHoy->format('YmdHis');
                $file = $adjunto['archivo']->getClientOriginalName();
                $codigo = $codigoRequerimientoPago;
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $newNameFile = $codigo . '_' . $key . $sufijo . '.' . $extension;

                Storage::disk('archivos')->put("necesidades/requerimientos/pago/detalle/" . $newNameFile, File::get($adjunto['archivo']));


                $adjuntoRequerimientoPagoDetalle = DB::table('tesoreria.requerimiento_pago_detalle_adjunto')->insertGetId(
                    [
                        'id_requerimiento_pago_detalle' => $adjunto['id_requerimiento_pago_detalle'],
                        'archivo'                   => $newNameFile,
                        'id_estado'                 => 1,
                        'fecha_registro'            => $fechaHoy
                    ],
                    'id_requerimiento_pago_detalle_adjunto'
                );
            }
        }
    }


    function actualizarRequerimientoPago(Request $request)
    {
        DB::beginTransaction();
        try {
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
            $requerimientoPago->save();

            // guardar adjuntos
            $requerimientoPago->adjuntoOtrosAdjuntosGuardar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar1;
            $requerimientoPago->adjuntoOrdenesGuardar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar2;
            $requerimientoPago->adjuntoComprobanteBancarioGuardar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar3;
            $requerimientoPago->adjuntoComprobanteContableGuardar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar4;
            // //? actualizar adjuntos, actualmente no se actualizan archivos por otros
            // $requerimientoPago->adjuntoOtrosAdjuntosActualizar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileActualizar1;
            // $requerimientoPago->adjuntoOrdenesActualizar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileActualizar2;
            // $requerimientoPago->adjuntoComprobanteBancarioActualizar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileActualizar3;
            // $requerimientoPago->adjuntoComprobanteContableActualizar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileActualizar4;
            // //? eliminar adjuntos, actualmente no se elimina en disco 
            // $requerimientoPago->adjuntoOtrosAdjuntosEliminar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileEliminar1;
            // $requerimientoPago->adjuntoOrdenesEliminar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileEliminar2;
            // $requerimientoPago->adjuntoComprobanteBancarioEliminar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileEliminar3;
            // $requerimientoPago->adjuntoComprobanteContableEliminar = $request->archivoAdjuntoRequerimientoPagoCabeceraFileEliminar4;


            $count = count($request->descripcion);

            for ($i = 0; $i < $count; $i++) {
                $id = $request->idRegister[$i];

                if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id)) // es un id con numeros y letras => es nuevo, insertar
                {
                    $detalle = new RequerimientoPagoDetalle();

                    $detalle->id_requerimiento_pago = $requerimientoPago->id_requerimiento_pago;
                    $detalle->id_tipo_item = $request->tipoItem[$i];
                    $detalle->id_partida = $request->idPartida[$i];
                    $detalle->id_centro_costo = $request->idCentroCosto[$i];
                    $detalle->descripcion = $request->descripcion[$i] != null ? trim(strtoupper($request->descripcion[$i])) : null;
                    $detalle->id_unidad_medida = $request->unidad[$i];
                    $detalle->cantidad = $request->cantidad[$i];
                    $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                    $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                    $detalle->fecha_registro = new Carbon();
                    $detalle->id_estado = 1;
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
                        }
                    } else {

                        $detalle = RequerimientoPagoDetalle::where("id_requerimiento_pago_detalle", $id)->first();
                        $detalle->id_tipo_item = $request->tipoItem[$i];
                        $detalle->id_partida = $request->idPartida[$i];
                        $detalle->id_centro_costo = $request->idCentroCosto[$i];
                        $detalle->descripcion = $request->descripcion[$i] != null ? trim(strtoupper($request->descripcion[$i])) : null;
                        $detalle->id_unidad_medida = $request->unidad[$i];
                        $detalle->cantidad = $request->cantidad[$i];
                        $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                        $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                        $detalle->save();
                        $detalle->idRegister = $request->idRegister[$i];
                        $detalleArray[] = $detalle;
                    }
                }
            }


            // guardando adjuntos nuevos
            if (($requerimientoPago->adjuntoOtrosAdjuntosGuardar != null ? (count($requerimientoPago->adjuntoOtrosAdjuntosGuardar)) : 0) > 0) {
                $this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $requerimientoPago->adjuntoOtrosAdjuntosGuardar, $requerimientoPago->codigo, 1);
            }
            if (($requerimientoPago->adjuntoOrdenesGuardar != null ? (count($requerimientoPago->adjuntoOrdenesGuardar)) : 0) > 0) {
                $this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $requerimientoPago->adjuntoOrdenesGuardar, $requerimientoPago->codigo, 1);
            }
            if (($requerimientoPago->adjuntoComprobanteBancarioGuardar != null ? (count($requerimientoPago->adjuntoComprobanteBancarioGuardar)) : 0) > 0) {
                $this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $requerimientoPago->adjuntoComprobanteBancarioGuardar, $requerimientoPago->codigo, 1);
            }
            if (($requerimientoPago->adjuntoComprobanteContableGuardar != null ? (count($requerimientoPago->adjuntoComprobanteContableGuardar)) : 0) > 0) {
                $this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $requerimientoPago->adjuntoComprobanteContableGuardar, $requerimientoPago->codigo, 1);
            }


            // adjuntos cabecera - actualizar, anular adjuntos en tabla 
            $archivoAdjuntoRequerimientoPagoObject = json_decode($request->archivoAdjuntoRequerimientoPagoObject);
            if (count($archivoAdjuntoRequerimientoPagoObject) > 0) {
                foreach ($archivoAdjuntoRequerimientoPagoObject as $ar) {
                    if (preg_match('/^[0-9]+$/', $ar->id)) {
                        if ($ar->action == 'ACTUALIZAR') {
                            RequerimientoPagoAdjunto::where('id_requerimiento_pago_adjunto', '=', $ar->id)
                                ->update(['id_categoria_adjunto' => $ar->category]);
                        }
                        if ($ar->action == 'ELIMINAR') {
                            RequerimientoPagoAdjunto::where('id_requerimiento_pago_adjunto', '=', $ar->id)
                                ->update(['id_estado' => 7]);
                        }
                    }
                }
            }


            // adjunto detalle - guardar adjuntos
            $adjuntoRequerimientoPagoDetalleArray = [];

            if (count($detalleArray) > 0) {
                for ($i = 0; $i < count($detalleArray); $i++) {

                    $archivos = $request->{"archivoAdjuntoRequerimientoPagoDetalleGuardar" . $detalleArray[$i]['idRegister']};

                    if (isset($archivos)) {
                        foreach ($archivos as $archivo) {
                            $adjuntoRequerimientoPagoDetalleArray[] = [
                                'id_requerimiento_pago_detalle' => $detalleArray[$i]['id_requerimiento_pago_detalle'],
                                // 'nombre_archivo' => $archivo->getClientOriginalName(),
                                'archivo' => $archivo
                            ];
                        }
                    }
                }
            }



            // adjuntos detalle -anular adjuntos en tabla 
            $archivoAdjuntoRequerimientoPagoDetalleObject = json_decode($request->archivoAdjuntoRequerimientoPagoDetalleObject);
            if (count($archivoAdjuntoRequerimientoPagoDetalleObject) > 0) {
                foreach ($archivoAdjuntoRequerimientoPagoDetalleObject as $ar) {
                    if (preg_match('/^[0-9]+$/', $ar->id)) {
                        if ($ar->action == 'ELIMINAR') {
                            RequerimientoPagoAdjuntoDetalle::where('id_requerimiento_pago_detalle_adjunto', '=', $ar->id)
                                ->update(['id_estado' => 7]);
                        }
                    }
                }
            }

            if (count($adjuntoRequerimientoPagoDetalleArray) > 0) {
                $this->guardarAdjuntoRequerimientoPagoDetalle($adjuntoRequerimientoPagoDetalleArray, $requerimientoPago->codigo);
            }


            DB::commit();



            return response()->json(['id_requerimiento_pago' => $requerimientoPago->id_requerimiento_pago, 'mensaje' => 'Se actualizó el requerimiento de pago ' . $requerimientoPago->codigo]);
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
                $todoDetalleRequerimientoPago = RequerimientoPagoDetalle::where("id_requerimiento_pago", $idRequerimientoPago)->get();

                if (in_array($requerimientoPago->id_estado, [1, 3])) { // estado elaborado, estado observado
                    $requerimientoPago->id_estado = 7;
                    $requerimientoPago->save();

                    // anular detalle requerimiento pago 
                    foreach ($todoDetalleRequerimientoPago as $detalleRequerimientoPago) {
                        $detalle = RequerimientoPagoDetalle::where("id_requerimiento_pago_detalle", $detalleRequerimientoPago->id_requerimiento_pago_detalle)->first();
                        $detalle->id_estado = 7;
                        $detalle->save();
                    }
                    // anular adjunto cabecera
                    RequerimientoPagoAdjunto::where('id_requerimiento_pago', '=', $idRequerimientoPago)
                        ->update(['id_estado' => 7]);


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

        $detalleRequerimientoPagoList = RequerimientoPagoDetalle::with('unidadMedida', 'producto', 'partida.presupuesto', 'centroCosto', 'adjunto', 'estado')
            ->where([['id_requerimiento_pago', $idRequerimientoPago],['id_estado','!=',7]])
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
                'adjunto'
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
        $data = RequerimientoPagoAdjunto::where([['id_requerimiento_pago', $idRequerimientoPago], ['id_estado', '!=', 7]])->with('categoriaAdjunto')->get();
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
        $nombreCompleto =   '%'.(strtoupper($request->nombreDestinatario)).'%';
        $idTipoDestinatario = $request->idTipoDestinatario;
        $destinatario = [];
        $tipo_estado = '';
        $mensaje = '';
        

        if ($idTipoDestinatario == 1) { // tipo persona
            $destinatario = Persona::with("tipoDocumentoIdentidad", "cuentaPersona.banco.contribuyente", "cuentaPersona.tipoCuenta", "cuentaPersona.moneda")
            ->whereRaw("UPPER(nombres) LIKE '".$nombreCompleto."' AND estado != 7")
            ->orWhereRaw("UPPER(apellido_paterno) LIKE '".$nombreCompleto."' AND estado != 7")
            ->orWhereRaw("UPPER(apellido_materno) LIKE '".$nombreCompleto."' AND estado != 7")
                ->get();
        } elseif ($idTipoDestinatario == 2) { // tipo contribuyente
            $destinatario =  Contribuyente::with("tipoDocumentoIdentidad", "cuentaContribuyente.banco.contribuyente", "cuentaContribuyente.tipoCuenta", "tipoContribuyente")
                ->whereRaw("UPPER(razon_social) LIKE '". $nombreCompleto. "'AND estado !=  7")->get();
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

    public function listarTodoArchivoAdjuntoRequerimientoPago($idRequerimientoPago){

        $requerimientoPago = RequerimientoPago::find($idRequerimientoPago);
        $idUsuarioPropietarioRequerimiento = $requerimientoPago->id_usuario ?? '';

        $detalleRequerimientoPagoList=RequerimientoPagoDetalle::where([["id_requerimiento_pago",$idRequerimientoPago],["id_estado","!=",7]])->get();
        $idDetalleRequerimientoPagoList=[];
        foreach ($detalleRequerimientoPagoList as $dr) {
            $idDetalleRequerimientoPagoList[]=$dr->id_requerimiento_pago_detalle;
        }
        $ajuntosCabeceraList = RequerimientoPagoAdjunto::with("categoriaAdjunto")->where([["id_requerimiento_pago",$idRequerimientoPago],["id_estado","!=",7]])->get();
        if(count($idDetalleRequerimientoPagoList)>0){
            $adjuntoDetalleList = RequerimientoPagoAdjuntoDetalle::whereIn("id_requerimiento_pago_detalle",$idDetalleRequerimientoPagoList)->where("id_estado","!=",7)->get();
        }

        return ["adjuntos_cabecera"=>$ajuntosCabeceraList??[],"adjuntos_detalle"=>$adjuntoDetalleList??[],'id_usuario_propietario_requerimiento'=>$idUsuarioPropietarioRequerimiento];
    }

    function guardarAdjuntosAdicionales(Request $request){
        DB::beginTransaction();
        try {
        $requerimientoPago = RequerimientoPago::where("id_requerimiento_pago", $request->id_requerimiento_pago)->first();
 
        $adjuntoOtrosAdjuntosLength = $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar1 != null ? count($request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar1) : 0;
        $adjuntoOrdenesLength = $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar2 != null ? count($request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar2) : 0;
        $adjuntoComprobanteContableLength = $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar3 != null ? count($request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar3) : 0;
        $adjuntoComprobanteBancarioLength = $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar4 != null ? count($request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar4) : 0;

        $idAdjuntoList=[];
        if ($adjuntoOtrosAdjuntosLength > 0) {
            $idAdjuntoList=$this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar1, $requerimientoPago->codigo, 1);
        }
        if ($adjuntoOrdenesLength > 0) {
            $idAdjuntoList=$this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago, $request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar2, $requerimientoPago->codigo, 2);
        }
        if ($adjuntoComprobanteContableLength > 0) {
            $idAdjuntoList=$this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago,$request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar3, $requerimientoPago->codigo, 3);
        }
        if ($adjuntoComprobanteBancarioLength > 0) {
            $idAdjuntoList=$this->subirYRegistrarArchivoCabecera($requerimientoPago->id_requerimiento_pago,$request->archivoAdjuntoRequerimientoPagoCabeceraFileGuardar4, $requerimientoPago->codigo, 4);
        }
        $estado_accion='error';
        if(count($idAdjuntoList)>0){
            $mensaje = 'Adjuntos guardos';
            $estado_accion='success';
        }else{
            $mensaje = 'Hubo un problema y no se pudo guardo los adjuntos';
            $estado_accion='warning';
            
        }
            DB::commit();

        return response()->json(['status' => $estado_accion, 'mensaje' => $mensaje]);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 'error', 'mensaje' => 'Hubo un problema al guardar los adjuntos. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
    }

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

    public function duplicarRequerimientoPagoYActualizarCodigo($id,$idEstado){
        DB::beginTransaction();
        try {

        $data=[];
        $status='';
        $msj='';
        
        if($id>0){
            $requerimientoPago= RequerimientoPago::find($id);
            $nuevoRequerimientoPago= $requerimientoPago->replicate();
            $nuevoRequerimientoPago->fecha_registro = Carbon::now();
            $nuevoRequerimientoPago->id_estado=$idEstado;
            $nuevoRequerimientoPago->save();

            $RequerimientoPagoDetalle= RequerimientoPagoDetalle::where('id_requerimiento_pago',$id)->get();
            foreach ($RequerimientoPagoDetalle as $d) {
                $detReqPago= RequerimientoPagoDetalle::find($d->id_requerimiento_pago_detalle);
                $nuevoRequerimientoPagoDetalle= $detReqPago->replicate();
                $nuevoRequerimientoPagoDetalle->id_requerimiento_pago = $nuevoRequerimientoPago->id_requerimiento_pago;
                $nuevoRequerimientoPagoDetalle->fecha_registro = Carbon::now();
                $nuevoRequerimientoPagoDetalle->id_estado=$idEstado;
                $nuevoRequerimientoPagoDetalle->save();
            }

            $status='success';
            $msj='requerimiento de pago duplicado';
            $data=['id_requerimiento_pago'=>$nuevoRequerimientoPago->id_requerimiento_pago];



        DB::commit();

        $nuevoCodigo = RequerimientoPago::crearCodigo($requerimientoPago->id_grupo, $requerimientoPago->id_requerimiento_pago);
        $rp = RequerimientoPago::find($nuevoRequerimientoPago->id_requerimiento_pago);
        $rp->codigo = $requerimientoPago->codigo;
        $rp->save();

        $documento = new Documento();
        $documento->id_tp_documento = 11; // requerimiento pago
        $documento->codigo_doc = $requerimientoPago->codigo;
        $documento->id_doc = $nuevoRequerimientoPago->id_requerimiento_pago;
        $documento->save();

        $requerimientoPago= RequerimientoPago::find($id);
        $requerimientoPago->codigo=$nuevoCodigo;
        $requerimientoPago->save();


        }else{
            $status='warning';
            $msj='Id enviado no es valido';
        }

        return ['data'=>$data,'status'=>$status,'mensaje'=>$msj];

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['data' => [],'status'=>$status, 'mensaje' => 'Hubo un problema al intentar duplicar el requerimiento de pago. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    public function getCodigoRequerimientoPago($grupo,$idReq){
        return RequerimientoPago::crearCodigo($grupo, $idReq);

    }
}
