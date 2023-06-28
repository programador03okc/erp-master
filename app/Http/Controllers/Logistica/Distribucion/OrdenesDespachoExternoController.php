<?php

namespace App\Http\Controllers\Logistica\Distribucion;

use App\Exports\DespachosExternosExport;
use App\Helpers\mgcp\OrdenCompraAmHelper;
use App\Helpers\mgcp\OrdenCompraDirectaHelper;
use App\Helpers\NotificacionHelper;
use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\EmailContactoDespacho;
use App\Mail\EmailOrdenDespacho;
use App\Models\Administracion\Periodo;
use App\models\almacen\AdjuntosDespacho;
use App\Models\Almacen\Requerimiento;
use App\Models\Comercial\Cliente;
use App\models\Configuracion\AccesosUsuarios;
use App\models\Configuracion\AdjuntosNotificaciones;
use App\Models\Configuracion\Usuario;
use App\Models\Contabilidad\ContactoContribuyente;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Distribucion\OrdenDespacho;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Debugbar;

class OrdenesDespachoExternoController extends Controller
{

    const ID_DIVISION_UCORP = 1;
    const ID_USUARIO_MGCP = 79;

    function view_ordenes_despacho_externo()
    {
        $estados = DB::table('almacen.estado_envio')
            // ->where([
            //     ['id_estado', '>=', 3],
            //     ['id_estado', '<=', 8]
            // ])
            ->whereIn('id_estado', [3, 4, 5, 6, 7, 8, 11, 12, 13, 14, 15])->orderBy('descripcion', 'asc')
            ->get();
        $array_accesos = [];
        $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos, $value->id_acceso);
        }
        return view('almacen/distribucion/ordenesDespachoExterno', compact('estados', 'array_accesos'));
    }

    public function listarDespachosExternos(Request $request)
    {
        $data = DB::table('almacen.alm_req')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'oc_propias_view.fecha_entrega',
                'alm_req.tiene_transformacion',
                'alm_req.direccion_entrega',
                'alm_req.id_ubigeo_entrega',
                'alm_req.id_almacen',
                'alm_req.id_sede as sede_requerimiento',
                'alm_req.telefono',
                'alm_req.email',
                'alm_req.id_cliente',
                'alm_req.id_prioridad',
                'alm_req.id_contacto',
                'alm_req.enviar_contacto',
                'alm_req.estado',
                'alm_req.estado_despacho',
                'alm_tp_req.descripcion as tipo_requerimiento_descripcion',
                'sis_usua.nombre_corto as responsable',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'alm_almacen.descripcion as almacen_descripcion',
                'sede_req.descripcion as sede_descripcion_req',
                'adm_contri.id_contribuyente',
                'adm_contri.nro_documento as cliente_ruc',
                'adm_contri.razon_social as cliente_razon_social',
                'orden_despacho.id_od',
                'orden_despacho.fecha_despacho',
                'orden_despacho.nro_orden as numero_orden',
                'orden_despacho.persona_contacto',
                'orden_despacho.direccion_destino',
                'orden_despacho.correo_cliente',
                'orden_despacho.telefono as telefono_od',
                'orden_despacho.ubigeo_destino',
                'orden_despacho.codigo as codigo_od',
                'orden_despacho.estado as estado_od',
                'orden_despacho.serie as serie_tra',
                'orden_despacho.numero as numero_tra',
                'orden_despacho.serie_guia_venta as serie_guia',
                'orden_despacho.numero_guia_venta as numero_guia',
                'orden_despacho.fecha_transportista',
                'orden_despacho.codigo_envio',
                'orden_despacho.credito',
                'orden_despacho.importe_flete',
                'orden_despacho.id_transportista',
                'orden_despacho.plazo_excedido',
                'orden_despacho.fecha_entregada',
                'orden_despacho.fecha_despacho_real',
                'orden_despacho.fecha_registro_flete',
                'orden_despacho.fecha_actualizacion_od',
                'despachoInterno.id_od as id_despacho_interno',
                'despachoInterno.codigo as codigo_despacho_interno',
                'despachoInterno.estado as estado_di',
                'estado_envio.descripcion as estado_envio',
                'transportista.razon_social as transportista_razon_social',
                // 'guia_ven.serie',
                // 'guia_ven.numero',
                DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho_obs where
                            orden_despacho_obs.id_od = orden_despacho.id_od
                            and orden_despacho.estado != 7) AS count_estados_envios"),
                DB::raw("(SELECT SUM(orden_despacho_obs.gasto_extra) FROM almacen.orden_despacho_obs where
                            orden_despacho_obs.id_od = orden_despacho.id_od
                            and orden_despacho.estado != 7) AS gasto_extra"),
                DB::raw("(SELECT orden_despacho_obs.adjunto FROM almacen.orden_despacho_obs where
                            orden_despacho_obs.id_od = orden_despacho.id_od
                            and (orden_despacho_obs.accion = 8 or orden_despacho_obs.accion = 7 or orden_despacho_obs.accion = 6)
                            and orden_despacho.estado != 7
                            order by id_obs desc limit 1) AS adjunto"),
                'oc_propias_view.nro_orden',
                'oportunidades.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo',
                'oc_propias_view.id_oportunidad',
                'oc_propias_view.id_entidad',
                'oc_propias_view.estado_oc',
                'oc_propias_view.moneda_oc',
                'oc_propias_view.monto_total',
                'oc_propias_view.fecha_publicacion',
                'oc_propias_view.estado_aprobacion_cuadro',
                'oc_propias_view.siaf',
                'oc_propias_view.orden_compra',
                'oc_propias_view.occ',
                'oc_propias_view.tiene_comentarios',
                'oc_propias_view.nombre_entidad',
                'oc_propias_view.nombre_largo_responsable',
                // 'trazabilidad.adjunto',
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req where
                            alm_det_req.id_requerimiento = alm_req.id_requerimiento
                            and alm_det_req.estado != 7
                            and alm_det_req.id_tipo_item = 1
                            and alm_det_req.id_producto is null) AS productos_no_mapeados"),
                // DB::raw('count(*) as user_count, status')
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req where
                            alm_det_req.id_requerimiento = alm_req.id_requerimiento
                            and alm_det_req.estado != 7
                            and alm_det_req.id_tipo_item = 1
                            and alm_det_req.id_tipo_item = 1) AS count_productos"),
            )
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'oportunidades.id')
            ->leftJoin('almacen.alm_tp_req', 'alm_tp_req.id_tipo_requerimiento', '=', 'alm_req.id_tipo_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado_despacho')
            ->leftJoin('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function ($join) {
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.aplica_cambios', '=', false);
                $join->where('orden_despacho.estado', '!=', 7);
            })
            ->leftJoin('almacen.orden_despacho as despachoInterno', function ($join) {
                $join->on('despachoInterno.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('despachoInterno.aplica_cambios', '=', true);
                $join->where('despachoInterno.estado', '!=', 7);
            })
            ->leftJoin('contabilidad.adm_contri as transportista', 'transportista.id_contribuyente', '=', 'orden_despacho.id_transportista')
            ->leftJoin('administracion.adm_estado_doc as est_od', 'est_od.id_estado_doc', '=', 'orden_despacho.estado')
            ->leftJoin('almacen.estado_envio', 'estado_envio.id_estado', '=', 'orden_despacho.id_estado_envio')
            ->where([
                ['alm_req.estado', '!=', 7]
                // ['alm_req.observacion', '!=', 'Creado de forma automática por venta interna'],
                // ['nro_productos', '>', 0]
            ])
            ->whereIn('alm_req.id_tipo_detalle', [1, 3]);

        return $data;
    }

    public function listarRequerimientosPendientesDespachoExterno(Request $request)
    {
        $query = $this->listarDespachosExternos($request);
        return datatables($query)->toJson();
    }

    public function despachosExternosExcel(Request $request)
    {
        $data = $this->listarDespachosExternos($request);
        $data->orderBy('oc_propias_view.fecha_entrega', 'desc');
        return Excel::download(new DespachosExternosExport(
            $data,
            $request->select_mostrar
        ), 'despachosExternos.xlsx');
    }

    // public function guardarOrdenDespachoExterno(Request $request)
    // {

    //     try {
    //         DB::beginTransaction();

    //         $tiene_transformacion = ($request->tiene_transformacion == 'si' ? true : false);

    //         $usuario = Auth::user()->id_usuario;
    //         $fecha_registro = date('Y-m-d H:i:s');

    //         $id_od = DB::table('almacen.orden_despacho')
    //             ->insertGetId(
    //                 [
    //                     'id_sede' => $request->id_sede,
    //                     'id_requerimiento' => $request->id_requerimiento,
    //                     'id_cliente' => $request->id_cliente,
    //                     'id_persona' => ($request->id_persona > 0 ? $request->id_persona : null),
    //                     'id_almacen' => $request->id_almacen,
    //                     'codigo' => '-',
    //                     'telefono' => trim($request->telefono_cliente),
    //                     'persona_contacto' => trim($request->persona_contacto),
    //                     'ubigeo_destino' => $request->ubigeo,
    //                     'direccion_destino' => trim($request->direccion_destino),
    //                     'correo_cliente' => trim($request->correo_cliente),
    //                     // 'fecha_despacho' => $request->fecha_despacho,
    //                     // 'hora_despacho' => $request->hora_despacho,
    //                     'fecha_entrega' => $request->fecha_entrega,
    //                     'aplica_cambios' => false,
    //                     'registrado_por' => $usuario,
    //                     'tipo_entrega' => $request->tipo_entrega,
    //                     'fecha_registro' => $fecha_registro,
    //                     'documento' => $request->documento,
    //                     'estado' => 1,
    //                     'id_estado_envio' => 1,
    //                     // 'tipo_cliente' => $request->tipo_cliente
    //                 ],
    //                 'id_od'
    //             );

    //         // if ($request->id_requerimiento !== null) {
    //         //     DB::table('almacen.alm_req')
    //         //         ->where('id_requerimiento', $request->id_requerimiento)
    //         //         ->update([
    //         //             'enviar_facturacion' => true,
    //         //             'fecha_facturacion' => $request->fecha_facturacion,
    //         //             'obs_facturacion' => $request->obs_facturacion
    //         //         ]);
    //         // }

    //         //Si es Despacho Externo

    //         //Agrega accion en requerimiento
    //         DB::table('almacen.alm_req_obs')
    //             ->insert([
    //                 'id_requerimiento' => $request->id_requerimiento,
    //                 'accion' => 'DESPACHO EXTERNO',
    //                 'descripcion' => 'Se generó la Orden de Despacho Externa',
    //                 'id_usuario' => $usuario,
    //                 'fecha_registro' => $fecha_registro
    //             ]);

    //         // $data = json_decode($request->detalle_requerimiento);
    //         $detalle = DB::table('almacen.alm_det_req')
    //             ->where([
    //                 ['id_requerimiento', '=', $request->id_requerimiento],
    //                 ['tiene_transformacion', '=', $tiene_transformacion],
    //                 ['estado', '!=', 7]
    //             ])
    //             ->get();

    //         foreach ($detalle as $d) {
    //             DB::table('almacen.orden_despacho_det')
    //                 ->insert([
    //                     'id_od' => $id_od,
    //                     // 'id_producto' => $d->id_producto,
    //                     'id_detalle_requerimiento' => $d->id_detalle_requerimiento,
    //                     'cantidad' => $d->cantidad,
    //                     'transformado' => $d->tiene_transformacion,
    //                     'estado' => 1,
    //                     'fecha_registro' => $fecha_registro
    //                 ]);

    //             DB::table('almacen.alm_det_req')
    //                 ->where('id_detalle_requerimiento', $d->id_detalle_requerimiento)
    //                 ->update(['estado' => 23]); //despacho externo
    //         }

    //         DB::table('almacen.alm_req')
    //             ->where('id_requerimiento', $request->id_requerimiento)
    //             ->update(['estado' => 23]); //despacho externo


    //         /*
    //         $req = DB::table('almacen.alm_req')
    //             ->select(
    //                 'alm_req.*',
    //                 'oc_propias.id as id_oc_propia',
    //                 'oc_propias.url_oc_fisica',
    //                 'entidades.nombre',
    //                 'adm_contri.razon_social',
    //                 'oportunidades.codigo_oportunidad',
    //                 'adm_empresa.codigo as codigo_empresa',
    //                 'oc_propias.orden_am',
    //                 'adm_empresa.id_empresa'
    //             )
    //             ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
    //             ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
    //             ->leftjoin('mgcp_acuerdo_marco.oc_propias', 'oc_propias.id_oportunidad', '=', 'oportunidades.id')
    //             ->leftjoin('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'oportunidades.id_entidad')
    //             ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
    //             ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
    //             ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
    //             ->where('id_requerimiento', $request->id_requerimiento)
    //             ->first();

    //         if ($req->id_tipo_requerimiento == 1) {

    //             $asunto_facturacion = $req->orden_am . ' | ' . $req->nombre . ' | ' . $req->codigo_oportunidad . ' | ' . $req->codigo_empresa;
    //             $contenido_facturacion = '
    //                 Favor de generar documentación: <br>- ' . ($request->documento == 'Factura' ? $request->documento . '<br>- Guía<br>- Certificado de Garantía<br>- CCI<br>' : '<br>') . '
    //                 <br>Requerimiento ' . $req->codigo . '
    //                 <br>Entidad: ' . $req->nombre . '
    //                 <br>Empresa: ' . $req->razon_social . '
    //                 <br>' . $request->contenido . '<br>
    //         <br>' . ($req->id_oc_propia !== null
    //                 ? ('Ver Orden Física: ' . $req->url_oc_fisica . '
    //         <br>Ver Orden Electrónica: https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=' . $req->id_oc_propia . '&ImprimirCompleto=1') : '') . '
    //         <br><br>
    //         Saludos,<br>
    //         Módulo de Despachos<br>
    //         SYSTEM AGILE';

    //             $msj = '';
    //             $email_destinatario[] = 'programador01@okcomputer.com.pe';
    //             // $email_destinatario[] = 'administracionventas@okcomputer.com.pe';
    //             // $email_destinatario[] = 'asistente.contable.lima@okcomputer.com.pe';
    //             // $email_destinatario[] = 'asistente.contable@okcomputer.com.pe';
    //             // $email_destinatario[] = 'administracionventas@okcomputer.com.pe';
    //             // $email_destinatario[] = 'asistente.almacenlima1@okcomputer.com.pe';
    //             // $email_destinatario[] = 'asistente.almacenlima2@okcomputer.com.pe';
    //             // $email_destinatario[] = 'asistente.almacenlima@okcomputer.com.pe';
    //             // $email_destinatario[] = 'logistica.lima@okcomputer.com.pe';
    //             // $email_destinatario[] = 'soporte.lima@okcomputer.com.pe';
    //             // $email_destinatario[] = 'contadorgeneral@okcomputer.com.pe';
    //             // $email_destinatario[] = 'infraestructura@okcomputer.com.pe';
    //             // $email_destinatario[] = 'lenovo@okcomputer.com.pe';
    //             // $email_destinatario[] = 'logistica@okcomputer.com.pe';
    //             // $email_destinatario[] = 'dapaza@okcomputer.com.pe';
    //             // $email_destinatario[] = 'asistente.logistica@okcomputer.com.pe';
    //             $payload = [
    //                 'id_empresa' => $req->id_empresa,
    //                 'email_destinatario' => $email_destinatario,
    //                 'titulo' => $asunto_facturacion,
    //                 'mensaje' => $contenido_facturacion
    //             ];

    //             $smpt_setting = [
    //                 'smtp_server' => 'smtp.gmail.com',
    //                 // 'smtp_server'=>'outlook.office365.com',
    //                 'port' => 587,
    //                 'encryption' => 'tls',
    //                 'email' => 'webmaster@okcomputer.com.pe',
    //                 'password' => 'MgcpPeru2020*'
    //                 // 'email'=>'programador01@okcomputer.com.pe',
    //                 // 'password'=>'Dafne0988eli@'
    //                 // 'email'=>'administracionventas@okcomputer.com.pe',
    //                 // 'password'=>'Logistica1505'
    //             ];

    //             if (count($email_destinatario) > 0) {
    //                 $estado_envio = (new CorreoController)->enviar_correo_despacho($payload, $smpt_setting);
    //             }
    //         } else {
    //             $msj = 'Se guardó existosamente la Orden de Despacho';
    //         }*/
    //         // DB::commit();

    //         $codigo = OrdenesDespachoExternoController::ODnextId(date('Y-m-d'), $request->id_almacen, false, $id_od);

    //         if ($codigo !== null) {
    //             DB::table('almacen.orden_despacho')
    //                 ->where('id_od', $id_od)
    //                 ->update(['codigo' => $codigo]);
    //         }
    //         DB::commit();
    //         return response()->json('Se guardó existosamente la Orden de Despacho');
    //     } catch (\PDOException $e) {
    //         DB::rollBack();
    //         return response()->json('Algo salio mal');
    //     }
    // }
    private function enviarOrdenDespacho(Request $request, $oportunidad, $requerimiento, $ordenDespacho)
    {
        $archivosOc = [];
        /*
        if ($oportunidad !== null) {
            $ordenView = $oportunidad->ordenCompraPropia;
            //Obtencion de archivos en carpeta temporal
            if ($ordenView != null) {
                if ($ordenView->tipo == 'am') {
                    $archivosOc = OrdenCompraAmHelper::descargarArchivos($ordenView->id);
                } else {
                    $archivosOc = OrdenCompraDirectaHelper::copiarArchivos($ordenView->id);
                }
            }
        }
        //Guardar archivos subidos
      if ($request->hasFile('archivos')) {
            $archivos = $request->file('archivos');
            foreach ($archivos as $archivo) {
                Storage::putFileAs('mgcp/ordenes-compra/temporal/', $archivo, $archivo->getClientOriginalName());
                $archivosOc[] = storage_path('app/mgcp/ordenes-compra/temporal/') . $archivo->getClientOriginalName();
            }
        }*/
        $correos = [];
        $idUsuarios = [];
        if (config('app.debug')) {
            $correos[] = config('global.correoDebug1');
            $idUsuarios[] = Auth::user()->id_usuario;
        } else {
            $idUsuarios = Usuario::getAllIdUsuariosPorRol(26);

            if ($oportunidad !== null) {
                $usuario = DB::table('mgcp_usuarios.users')
                    ->where('id', $oportunidad->id_responsable)->first();

                $correos[] = $usuario->email;
            } else if ($requerimiento !== null) {
                $usuario = Usuario::withTrashed()->find($requerimiento->id_usuario)->email;

                $correos[] = $usuario->email;
            }
            // $correos[] = Usuario::find($requerimiento->id_usuario)->email;
            foreach ($idUsuarios as $id) {
                $correos[] = Usuario::withTrashed()->find($id)->email;
            }
        }
        $orden_despacho = OrdenDespacho::where('id_requerimiento', $request->id_requerimiento)->first();

        // Mail::to($correos)->send(new EmailOrdenDespacho($oportunidad, $request->mensaje, $archivosOc, $requerimiento));
        NotificacionHelper::notificacionOrdenDespacho($idUsuarios, $request->mensaje, $oportunidad, $requerimiento, $ordenDespacho);

        // foreach ($archivosOc as $archivo) {
        //   unlink($archivo);
        //}
    }

    public function usuariosDespacho()
    {
        $usuarios = Usuario::getAllIdUsuariosPorRol(26);
        return response()->json($usuarios);
    }


    public function guardarOrdenDespachoExterno(Request $request)
    {
        try {
            DB::beginTransaction();
            $ordenDespacho = null;
            $cuadro = null;
            $oportunidad = null;

            if ($request->id_requerimiento !== null) {

                $requerimiento = Requerimiento::where('id_requerimiento', $request->id_requerimiento)->first();
                $ordenDespacho = OrdenDespacho::where([
                    ['id_requerimiento', '=', $requerimiento->id_requerimiento],
                    ['aplica_cambios', '=', false],
                    ['estado', '!=', 7]
                ])->first();

                if ($ordenDespacho == null) {

                    $usuario = Auth::user()->id_usuario;
                    $fechaRegistro = new Carbon(); //date('Y-m-d H:i:s');
                    $id_estado_envio = 1; //despacho elaborado
                    $nro_orden = OrdenDespacho::where([['fecha_despacho', '=', $fechaRegistro]])->count();

                    $ordenDespacho = new OrdenDespacho();
                    $ordenDespacho->id_sede = $requerimiento->id_sede;
                    $ordenDespacho->id_requerimiento = $requerimiento->id_requerimiento;
                    $ordenDespacho->id_cliente = $requerimiento->id_cliente;
                    $ordenDespacho->id_persona = $requerimiento->id_persona;
                    $ordenDespacho->id_almacen = $requerimiento->id_almacen;
                    $ordenDespacho->aplica_cambios = false;
                    $ordenDespacho->registrado_por = $usuario;
                    $ordenDespacho->fecha_despacho = $fechaRegistro;
                    $ordenDespacho->fecha_registro = $fechaRegistro;
                    $ordenDespacho->nro_orden = ($nro_orden + 1);
                    $ordenDespacho->estado = 1;
                    $ordenDespacho->id_estado_envio = $id_estado_envio;
                    $ordenDespacho->save();
                    //Agrega accion en requerimiento
                    DB::table('almacen.alm_req_obs')
                        ->insert([
                            'id_requerimiento' => $requerimiento->id_requerimiento,
                            'accion' => 'DESPACHO EXTERNO',
                            'descripcion' => 'Se generó la Orden de Despacho Externa',
                            'id_usuario' => $usuario,
                            'fecha_registro' => $fechaRegistro
                        ]);

                    if ($requerimiento->id_tipo_requerimiento == 1) {

                        $detalle = DB::table('almacen.alm_det_req')
                            ->where([
                                ['id_requerimiento', '=', $requerimiento->id_requerimiento],
                                ['id_tipo_item', '=', 1],
                                ['entrega_cliente', '=', true],
                                ['estado', '!=', 7]
                            ])
                            ->get();
                    } else {
                        $detalle = DB::table('almacen.alm_det_req')
                            ->where([
                                ['id_requerimiento', '=', $requerimiento->id_requerimiento],
                                ['id_tipo_item', '=', 1],
                                ['estado', '!=', 7]
                            ])
                            ->get();
                    }

                    foreach ($detalle as $d) {
                        DB::table('almacen.orden_despacho_det')
                            ->insert([
                                'id_od' => $ordenDespacho->id_od,
                                // 'id_producto' => $d->id_producto,
                                'id_detalle_requerimiento' => $d->id_detalle_requerimiento,
                                'cantidad' => $d->cantidad,
                                'transformado' => $d->tiene_transformacion,
                                'estado' => 1,
                                'fecha_registro' => $fechaRegistro
                            ]);

                        DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento', $d->id_detalle_requerimiento)
                            ->update(['estado' => 23]); //despacho externo
                    }

                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $requerimiento->id_requerimiento)
                        ->update(['estado_despacho' => 23]); //despacho externo

                    $ordenDespacho->codigo = OrdenDespacho::ODnextId($requerimiento->id_almacen, false, $ordenDespacho->id_od, $request->fecha_documento_ode);
                    $ordenDespacho->fecha_documento = $request->fecha_documento_ode;
                    $ordenDespacho->save();

                    //Agrega primera trazabilidad de envio (la generacion de la Orden de despacho)
                    $obs = DB::table('almacen.orden_despacho_obs')
                        ->where([
                            ['id_od', '=', $ordenDespacho->id_od],
                            ['accion', '=', $id_estado_envio]
                        ])
                        ->first();

                    $name_usuario = Auth::user()->nombre_corto;
                    //si ya existe, actualiza
                    if ($obs !== null) {
                        DB::table('almacen.orden_despacho_obs')
                            ->where('id_obs', $obs->id_obs)
                            ->update([
                                'observacion' => 'Fue despachado con ' . $ordenDespacho->codigo,
                                'fecha_estado' => $fechaRegistro,
                                'registrado_por' => $usuario,
                                'fecha_registro' => $fechaRegistro
                            ]);
                    } else {
                        //si no existe, crea
                        DB::table('almacen.orden_despacho_obs')
                            ->insert([
                                'id_od' => $ordenDespacho->id_od,
                                'accion' => $id_estado_envio,
                                'fecha_estado' => $fechaRegistro,
                                'observacion' => 'Fue despachado con ' . $ordenDespacho->codigo,
                                'registrado_por' => $usuario,
                                'fecha_registro' => $fechaRegistro
                            ]);
                    }
                }
            }
            if ($request->envio == 'envio') {

                if ($request->id_oportunidad !== null) {
                    $cuadro = CuadroCosto::where('id_oportunidad', $request->id_oportunidad)->first();
                    $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
                }

                $this->enviarOrdenDespacho($request, $oportunidad, $requerimiento, $ordenDespacho);

                if ($request->archivos) {
                    foreach ($request->archivos as $key => $value) {
                        if ($value != null) {
                            $fechaHoy = new Carbon();
                            $sufijo = $fechaHoy->format('YmdHis');
                            $file = $value->getClientOriginalName();
                            $codigo = $request->codigo;
                            $extension = pathinfo($file, PATHINFO_EXTENSION);
                            $id_requerimiento = $request->id_requerimiento;
                            $newNameFile = $codigo . '_' . $key . $id_requerimiento . $sufijo . '.' . $extension;
                            Storage::disk('archivos')->put("logistica/despacho/" . $newNameFile, File::get($value));

                            $adjuntos_notificaciones = new AdjuntosDespacho();
                            $adjuntos_notificaciones->archivo = $newNameFile;
                            $adjuntos_notificaciones->estado = 1;
                            $adjuntos_notificaciones->fecha_registro = date('Y-m-d H:i:s');
                            $adjuntos_notificaciones->id_requerimiento = $request->id_requerimiento;
                            $adjuntos_notificaciones->id_oportunidad = $request->id_oportunidad;
                            $adjuntos_notificaciones->save();
                        }
                    }
                }
            }

            DB::commit();
            return response()->json(
                array(
                    'tipo' => 'success',
                    'mensaje' => ($request->envio == 'envio'
                        ? ($ordenDespacho !== null ? 'Se envió la orden con código ' . $ordenDespacho->codigo : 'Se envió sólo el correo')
                        : 'Se generó la orden de despacho'), 200
                )
            );
        } catch (\PDOException $e) {
            DB::rollBack();

            // return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage()), 200);
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function verDatosContacto(Request $request)
    {
        try {
            DB::beginTransaction();
            // $contacto = null;
            $entidad = null;
            $listaContactos = [];

            if ($request->id_requerimiento !== '0') {
                $requerimiento = DB::table('almacen.alm_req')
                    ->select('alm_req.id_contacto', 'alm_req.enviar_contacto', 'alm_req.correo_licencia', 'adm_contri.id_contribuyente')
                    ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
                    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
                    ->where('id_requerimiento', $request->id_requerimiento)
                    ->first();


                if ($requerimiento !== null) {
                    $listaContactos = DB::table('contabilidad.adm_ctb_contac')
                        ->leftjoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'adm_ctb_contac.ubigeo')
                        ->leftjoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
                        ->leftjoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
                        ->select(
                            'adm_ctb_contac.*',
                            'ubi_dis.descripcion as distrito',
                            'ubi_prov.descripcion as provincia',
                            'ubi_dpto.descripcion as departamento'
                        )
                        ->where([
                            ['adm_ctb_contac.id_contribuyente', '=', $requerimiento->id_contribuyente],
                            ['adm_ctb_contac.estado', '!=', 7]
                        ])
                        ->orderBy('nombre')
                        ->get();
                }
            }

            if ($request->id_entidad !== '0') {
                $entidad = DB::table('mgcp_acuerdo_marco.entidades')
                    ->where('id', $request->id_entidad)
                    ->first();
            }

            DB::commit();
            return response()->json([
                'entidad' => $entidad,
                'contacto' => ($requerimiento !== null ? $requerimiento : ''),
                'lista' => $listaContactos,
                'tipo' => 'success'
            ], 200);
            // return response()->json(array('tipo' => 'success', 'mensaje' => 'Se envió la orden con código ' . $ordenDespacho->codigo), 200);

        } catch (\PDOException $e) {
            DB::rollBack();

            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage()), 200);
        }
    }

    public function listarContactos($id_contribuyente)
    {
        try {
            $listaContactos = ContactoContribuyente::leftjoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'adm_ctb_contac.ubigeo')
                ->leftjoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
                ->leftjoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
                ->where([
                    ['adm_ctb_contac.id_contribuyente', '=', $id_contribuyente],
                    ['adm_ctb_contac.estado', '!=', 7]
                ])
                ->select(
                    'adm_ctb_contac.*',
                    'ubi_dis.descripcion as distrito',
                    'ubi_prov.descripcion as provincia',
                    'ubi_dpto.descripcion as departamento'
                )
                ->orderBy('nombre')
                ->get();

            return response()->json(array('lista' => $listaContactos, 'tipo' => 'success'), 200);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema. Por favor intente de nuevo', 'error' => $e->getMessage()), 200);
        }
    }

    public function mostrarContacto($id_contacto)
    {
        $contacto = DB::table('contabilidad.adm_ctb_contac')
            ->select(
                'adm_ctb_contac.*',
                DB::raw("(ubi_dis.descripcion) || ' - ' || (ubi_prov.descripcion) || ' - ' || (ubi_dpto.descripcion) AS name_ubigeo")
            )
            ->where('adm_ctb_contac.id_datos_contacto', $id_contacto)
            ->leftJoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'adm_ctb_contac.ubigeo')
            ->leftJoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->first();

        return response()->json($contacto);
    }

    public function anularContacto($id_contacto)
    {
        $contacto = DB::table('contabilidad.adm_ctb_contac')
            ->where('adm_ctb_contac.id_datos_contacto', $id_contacto)
            ->update(['estado' => 7]);

        return response()->json($contacto);
    }

    public function actualizaDatosContacto(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_contacto = null;
            $texto = '';

            if ($request->id_contacto !== '' && $request->id_contacto !== null) {
                $id_contacto = $request->id_contacto;
                $texto = 'actualizado';

                DB::table('contabilidad.adm_ctb_contac')
                    ->where('id_datos_contacto', $request->id_contacto)
                    ->update([
                        'nombre' => strtoupper(trim($request->nombre)),
                        'telefono' => trim($request->telefono),
                        'email' => trim($request->email),
                        'cargo' => strtoupper(trim($request->cargo)),
                        'direccion' => strtoupper(trim($request->direccion)),
                        'horario' => strtoupper(trim($request->horario)),
                        'ubigeo' => $request->ubigeo
                    ]);
            } else {
                $texto = 'guardado';
                $id_contacto = DB::table('contabilidad.adm_ctb_contac')
                    ->insertGetId(
                        [
                            'id_contribuyente' => $request->id_contribuyente_contacto,
                            'nombre' => strtoupper(trim($request->nombre)),
                            'telefono' => trim($request->telefono),
                            'email' => trim($request->email),
                            'cargo' => strtoupper(trim($request->cargo)),
                            'direccion' => strtoupper(trim($request->direccion)),
                            'horario' => strtoupper(trim($request->horario)),
                            'ubigeo' => $request->ubigeo,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => 1
                        ],
                        'id_datos_contacto'
                    );
            }

            if ($request->origen == 'despacho') {
                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $request->id_requerimiento)
                    ->update(['id_contacto' => $id_contacto]);
            }

            $contacto = DB::table('contabilidad.adm_ctb_contac')
                ->select(
                    'adm_ctb_contac.*',
                    'ubi_dis.descripcion as distrito',
                    'ubi_prov.descripcion as provincia',
                    'ubi_dpto.descripcion as departamento'
                )
                ->leftjoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'adm_ctb_contac.ubigeo')
                ->leftjoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
                ->leftjoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
                ->where('id_datos_contacto', $id_contacto)->first();

            DB::commit();
            return response()->json(
                array(
                    'tipo' => 'success',
                    'mensaje' => 'Se ha ' . $texto . ' el contacto.',
                    'id_contacto' => $id_contacto,
                    'contacto' => $contacto,
                ),
                200
            );
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(
                array(
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un problema al enviar el contacto. Por favor intente de nuevo.',
                    'error' => $e->getMessage()
                ),
                200
            );
        }
    }

    public function seleccionarContacto($id_contacto, $id_requerimiento)
    {
        DB::table('almacen.alm_req')
            ->where('id_requerimiento', $id_requerimiento)
            ->update(['id_contacto' => $id_contacto]);

        return response()->json('ok');
    }

    public function enviarDatosContacto(Request $request)
    {
        try {
            DB::beginTransaction();

            $requerimiento = Requerimiento::find($request->id_requerimiento);
            $cuadro = CuadroCosto::find($requerimiento->id_cc);
            $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
            // $ordenView = $oportunidad->ordenCompraPropia;

            DB::table('almacen.alm_req')
                ->where('id_requerimiento', $request->id_requerimiento)
                ->update([
                    'enviar_contacto' => true,
                    'correo_licencia' => $request->correo_licencia
                ]);

            $correos = [];
            if (config('app.debug')) {
                $correos[] = config('global.correoDebug1');
                $idUsuarios[] = Auth::user()->id_usuario;
            } else {
                $idUsuarios = Usuario::getAllIdUsuariosPorRol(26);
                $correos[] = Usuario::withTrashed()->find($requerimiento->id_usuario)->email;
                foreach ($idUsuarios as $id) {
                    $correos[] = Usuario::withTrashed()->find($id)->email;
                }
            }

            // Mail::to($correos)->send(new EmailContactoDespacho($oportunidad, $request->mensaje));
            NotificacionHelper::notificarContactoDespacho($request->mensaje, $idUsuarios);

            DB::commit();
            return response()->json(
                array(
                    'tipo' => 'success',
                    'mensaje' => 'Se envió los datos de contacto correctamente',
                ),
                200
            );
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(
                array(
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un problema al enviar el contacto. Por favor intente de nuevo',
                    'error' => $e->getMessage()
                ),
                200
            );
        }
    }

    public function guardarTransportista(Request $request)
    {
        try {
            DB::beginTransaction();
            $array = [];

            $contribuyente = DB::table('contabilidad.adm_contri')
                ->where('nro_documento', trim($request->nro_documento))
                ->first();

            if ($contribuyente !== null) {
                $array = array(
                    'tipo' => 'warning',
                    'mensaje' => 'Ya existe el RUC ingresado.',
                );
            } else {
                $id_contribuyente = DB::table('contabilidad.adm_contri')
                    ->insertGetId(
                        [
                            'nro_documento' => trim($request->nro_documento),
                            'razon_social' => strtoupper(trim($request->razon_social)),
                            'telefono' => trim($request->telefono),
                            'direccion_fiscal' => trim($request->direccion_fiscal),
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => 1,
                            'transportista' => true
                        ],
                        'id_contribuyente'
                    );

                DB::table('contabilidad.transportistas')
                    ->insert([
                        'id_contribuyente' => $id_contribuyente
                    ]);

                $array = array(
                    'tipo' => 'success',
                    'mensaje' => 'Se guardó el transportista correctamente',
                );
            }
            DB::commit();
            return response()->json($array);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(
                array(
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un problema. Por favor intente de nuevo',
                    'error' => $e->getMessage()
                )
            );
        }
    }

    public function actualizarOrdenDespachoExterno(Request $request)
    {
        $update = DB::table('almacen.orden_despacho')
            ->where('id_od', $request->id_od)
            ->update([
                'telefono' => trim($request->telefono_cliente),
                'persona_contacto' => trim($request->persona_contacto),
                'ubigeo_destino' => $request->ubigeo,
                'direccion_destino' => trim($request->direccion_destino),
                'correo_cliente' => trim($request->correo_cliente),
            ]);
        return response()->json($update);
    }

    public function enviarFacturacion(Request $request)
    {
        $update = DB::table('almacen.alm_req')
            ->where('id_requerimiento', $request->id_requerimiento)
            ->update([
                'enviar_facturacion' => true,
                'fecha_facturacion' => $request->fecha_facturacion,
                'obs_facturacion' => $request->obs_facturacion
            ]);

        return response()->json($update);
    }

    public function priorizar(Request $request)
    {
        try {
            DB::beginTransaction();
            $despachos = json_decode($request->despachos_externos);

            foreach ($despachos as $det) {
                DB::table('almacen.orden_despacho')
                    ->where('id_od', $det->id_od)
                    ->update([
                        'fecha_despacho' => $request->fecha_despacho,
                        'estado' => 25 //priorizado
                    ]);

                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $det->id_requerimiento)
                    ->update([
                        'enviar_facturacion' => true,
                        'fecha_facturacion' => $request->fecha_facturacion,
                        'obs_facturacion' => $request->comentario
                    ]);
            }
            DB::commit();
            return response()->json('ok');
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(':(');
        }
    }

    public function pruebaTransportista()
    {
        $oc = DB::table('almacen.alm_req')
            ->select(
                'oc_propias_view.id',
                'oc_propias_view.tipo',
                'oc_directas.id_despacho as id_despacho_directa',
                'oc_propias.id_despacho as id_despacho_propia',
                DB::raw("(SELECT SUM(orden_despacho_obs.gasto_extra) FROM almacen.orden_despacho_obs
                inner join almacen.orden_despacho on
                (orden_despacho_obs.id_od = orden_despacho.id_od)
                where   orden_despacho.id_requerimiento = alm_req.id_requerimiento
                        and orden_despacho.aplica_cambios = false
                        and orden_despacho.estado != 7) AS gasto_extra")
            )
            // ->join('almacen.orden_despacho', 'orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento')
            ->join('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->join('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->join('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_directas', 'oc_directas.id', '=', 'oc_propias_view.id')
            ->leftJoin('mgcp_acuerdo_marco.oc_propias', 'oc_propias.id', '=', 'oc_propias_view.id')
            ->where('alm_req.id_requerimiento', 1130)
            ->first();
        return response()->json($oc);
    }
    public function despachoTransportista(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $fecha_registro = date('Y-m-d H:i:s');
            $id_estado_envio = 2; //transportandose (ag transp. lima)

            $fechaRegistroFlete = null;
            $actualOD = OrdenDespacho::find($request->id_od);
            if (($actualOD->importe_flete == null && isset($request->importe_flete)) || ($actualOD->importe_flete != null && $request->fecha_registro_flete == null)) {
                $fechaRegistroFlete = new Carbon();
            }

            $data = DB::table('almacen.orden_despacho')
                ->where('id_od', $request->id_od)
                ->update([
                    'id_transportista' => $request->tr_id_transportista,
                    'serie' => $request->serie,
                    'numero' => $request->numero,
                    'fecha_transportista' => $request->fecha_transportista,
                    'fecha_despacho_real' => $request->fecha_despacho_real,
                    'codigo_envio' => $request->codigo_envio,
                    'importe_flete' => $request->importe_flete,
                    'serie_guia_venta' => $request->serie_guia_venta,
                    'numero_guia_venta' => $request->numero_guia_venta,
                    'id_estado_envio' => $id_estado_envio,
                    'fecha_actualizacion_od' => new Carbon(),
                    'fecha_registro_flete' => $fechaRegistroFlete ?? null,
                    // 'propia'=>((isset($request->transporte_propio)&&$request->transporte_propio=='on')?true:false),
                    'credito' => ((isset($request->credito) && $request->credito == 'on') ? true : false),
                ]);

            if ($request->fecha_despacho_real !== null || $request->tr_id_transportista !== null) {
                $oc = DB::table('almacen.alm_req')
                    ->select(
                        'oc_propias_view.id',
                        'oc_propias_view.tipo',
                        'oc_directas.id_despacho as id_despacho_directa',
                        'oc_propias.id_despacho as id_despacho_propia',
                        DB::raw("(SELECT SUM(orden_despacho_obs.gasto_extra) FROM almacen.orden_despacho_obs
                        inner join almacen.orden_despacho on
                        (orden_despacho_obs.id_od = orden_despacho.id_od)
                        where   orden_despacho.id_requerimiento = alm_req.id_requerimiento
                                and orden_despacho.aplica_cambios = false
                                and orden_despacho.estado != 7) AS gasto_extra")
                    )
                    // ->join('almacen.orden_despacho', 'orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento')
                    ->join('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                    ->join('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                    ->join('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
                    ->leftJoin('mgcp_ordenes_compra.oc_directas', 'oc_directas.id', '=', 'oc_propias_view.id')
                    ->leftJoin('mgcp_acuerdo_marco.oc_propias', 'oc_propias.id', '=', 'oc_propias_view.id')
                    ->where('alm_req.id_requerimiento', $request->con_id_requerimiento)
                    ->first();
                $id_despacho = null;
                //si existe una oc
                if ($oc !== null) {
                    //tiene un despacho
                    $id_despacho = $oc->id_despacho_directa !== null ? $oc->id_despacho_directa
                        : ($oc->id_despacho_propia !== null ? $oc->id_despacho_propia : null);

                    //si ya existe un despacho
                    if ($id_despacho !== null) {

                        DB::table('mgcp_ordenes_compra.despachos')
                            ->where('id', $id_despacho)
                            ->update([
                                'id_transportista' => $request->tr_id_transportista,
                                'flete_real' => (($request->importe_flete !== null ? $request->importe_flete : 0) + ($oc->gasto_extra !== null ? $oc->gasto_extra : 0)),
                                'fecha_salida' => $request->fecha_despacho_real,
                            ]);
                    } else {
                        $id_despacho = DB::table('mgcp_ordenes_compra.despachos')
                            ->insertGetId([
                                'id_transportista' => $request->tr_id_transportista,
                                'flete_real' => (($request->importe_flete !== null ? $request->importe_flete : 0) + ($oc->gasto_extra !== null ? $oc->gasto_extra : 0)),
                                'fecha_salida' => $request->fecha_despacho_real,
                                'id_usuario' => $id_usuario,
                                'fecha_registro' => new Carbon(),
                            ], 'id');
                    }

                    if ($oc->tipo == 'am') {
                        DB::table('mgcp_acuerdo_marco.oc_propias')
                            ->where('oc_propias.id', $oc->id)
                            ->update([
                                'despachada' => true,
                                'id_despacho' => $id_despacho
                            ]);
                    } else {
                        DB::table('mgcp_ordenes_compra.oc_directas')
                            ->where('oc_directas.id', $oc->id)
                            ->update([
                                'despachada' => true,
                                'id_despacho' => $id_despacho
                            ]);
                    }
                }
            }

            if (!empty($request->serie) && !empty($request->numero)) {
                //si se ingreso serie y numero de la guia se agrega el nuevo estado envio
                $obs = DB::table('almacen.orden_despacho_obs')
                    ->where([
                        ['id_od', '=', $request->id_od],
                        ['accion', '=', $id_estado_envio]
                    ])
                    ->first();

                if ($obs !== null) {
                    //si ya existe este estado lo actualiza
                    DB::table('almacen.orden_despacho_obs')
                        ->where('id_obs', $obs->id_obs)
                        ->update([
                            'observacion' => 'Guía N° ' . $request->serie . '-' . $request->numero,
                            'fecha_estado' => $request->fecha_transportista,
                            'registrado_por' => $id_usuario,
                            'fecha_registro' => $fecha_registro
                        ]);
                } else {
                    //si no existe este estado lo crea
                    DB::table('almacen.orden_despacho_obs')
                        ->insert([
                            'id_od' => $request->id_od,
                            'accion' => $id_estado_envio,
                            'fecha_estado' => $request->fecha_transportista,
                            'observacion' => 'Guía N° ' . $request->serie . '-' . $request->numero,
                            'registrado_por' => $id_usuario,
                            'fecha_registro' => $fecha_registro
                        ]);
                }

                //Agrega accion en requerimiento
                if ($request->con_id_requerimiento !== null) {
                    DB::table('almacen.alm_req_obs')
                        ->insert([
                            'id_requerimiento' => $request->con_id_requerimiento,
                            'accion' => 'TRANSPORTANDOSE',
                            'descripcion' => 'Se agrego los Datos del transportista. ' . $request->serie . '-' . $request->numero,
                            'id_usuario' => $id_usuario,
                            'fecha_registro' => $fecha_registro
                        ]);
                }
            }

            DB::commit();
            return response()->json($data);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }
    /*
    public function migrarDespachos()
    {
        try {
            DB::beginTransaction();

            $requerimientos = DB::table('almacen.alm_req')
                ->select(
                    'alm_req.id_requerimiento',
                    'alm_req.id_sede',
                    'alm_req.id_cliente',
                    'alm_req.id_almacen',
                    'alm_req.tiene_transformacion',
                    'oc_propias_view.fecha_salida',
                    'oc_propias_view.fecha_llegada',
                    'oc_propias_view.flete_real',
                    'oc_propias_view.id_transportista'
                )
                ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
                // ->where([['alm_req.id_requerimiento', '<', 80]])
                ->get();

            $ods = [];

            foreach ($requerimientos as $req) {

                $ordenDespacho = OrdenDespacho::where([
                    ['id_requerimiento', '=', $req->id_requerimiento],
                    ['aplica_cambios', '=', false],
                    ['estado', '!=', 7]
                ])->first();

                if ($ordenDespacho == null && ($req->fecha_salida !== null || $req->id_transportista !== null)) {

                    $usuario = Auth::user()->id_usuario;
                    $fechaRegistro = new Carbon();

                    // $req = Requerimiento::where('id_requerimiento', $req->id_requerimiento)->first();
                    $ordenDespacho = new OrdenDespacho();
                    $ordenDespacho->id_sede = $req->id_sede;
                    $ordenDespacho->id_requerimiento = $req->id_requerimiento;
                    $ordenDespacho->id_cliente = $req->id_cliente;
                    $ordenDespacho->id_almacen = $req->id_almacen;
                    $ordenDespacho->aplica_cambios = false;
                    $ordenDespacho->registrado_por = $usuario;
                    $ordenDespacho->fecha_despacho = $req->fecha_salida;
                    $ordenDespacho->fecha_despacho_real = $req->fecha_salida;
                    $ordenDespacho->fecha_entregada = $req->fecha_llegada;
                    $ordenDespacho->id_transportista = $req->id_transportista;
                    $ordenDespacho->importe_flete = $req->flete_real;
                    $ordenDespacho->fecha_registro = $fechaRegistro;
                    $ordenDespacho->estado = 23;
                    $ordenDespacho->id_estado_envio = 8;
                    $ordenDespacho->codigo = OrdenDespacho::ODnextId($req->id_almacen, false, 0);
                    $ordenDespacho->save();

                    // $ordenDespacho->save();

                    //Agrega accion en requerimiento
                    DB::table('almacen.alm_req_obs')
                        ->insert([
                            'id_requerimiento' => $req->id_requerimiento,
                            'accion' => 'DESPACHO EXTERNO',
                            'descripcion' => 'Se generó la Orden de Despacho Externa',
                            'id_usuario' => 64, //ricardo
                            'fecha_registro' => $fechaRegistro
                        ]);

                    $detalle = DB::table('almacen.alm_det_req')
                        ->where([
                            ['id_requerimiento', '=', $req->id_requerimiento],
                            ['tiene_transformacion', '=', $req->tiene_transformacion],
                            ['estado', '!=', 7]
                        ])
                        ->get();

                    foreach ($detalle as $d) {
                        DB::table('almacen.orden_despacho_det')
                            ->insert([
                                'id_od' => $ordenDespacho->id_od,
                                'id_detalle_requerimiento' => $d->id_detalle_requerimiento,
                                'cantidad' => $d->cantidad,
                                'transformado' => $d->tiene_transformacion,
                                'estado' => 1,
                                'fecha_registro' => $fechaRegistro
                            ]);

                        DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento', $d->id_detalle_requerimiento)
                            ->update(['estado' => 23]); //despacho externo
                    }

                    array_push($ods, $ordenDespacho);

                    DB::table('almacen.alm_req')
                        ->where('id_requerimiento', $req->id_requerimiento)
                        ->update(['estado' => 23]); //despacho externo

                    DB::table('almacen.orden_despacho_obs')
                        ->insert([
                            'id_od' => $ordenDespacho->id_od,
                            'accion' => 1,
                            'observacion' => 'Fue despachado con ' . $ordenDespacho->codigo,
                            'registrado_por' => $usuario,
                            'fecha_registro' => $fechaRegistro
                        ]);

                    DB::table('almacen.orden_despacho_obs')
                        ->insert([
                            'id_od' => $ordenDespacho->id_od,
                            'accion' => 8,
                            'observacion' => 'Migrado desde MGCP',
                            'registrado_por' => 64,
                            'fecha_registro' => $fechaRegistro
                        ]);
                }
            }
            DB::commit();
            return response()->json($ods);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }
*/

    public function migrarDespachos()
    {
        try {
            DB::beginTransaction();

            $despachos = DB::table('almacen.orden_despacho')
                ->select(
                    'orden_despacho.id_od',
                    'orden_despacho.id_transportista',
                    'orden_despacho.importe_flete',
                    'orden_despacho.fecha_despacho_real',
                    'orden_despacho.fecha_entregada',
                    DB::raw("(SELECT SUM(gasto_extra) FROM almacen.orden_despacho_obs WHERE
                            orden_despacho_obs.id_od = orden_despacho.id_od
                            and orden_despacho.estado != 7) AS suma_gasto_extra"),
                    'alm_req.id_requerimiento',
                    'alm_req.tiene_transformacion',
                    'oc_propias_view.fecha_salida',
                    'oc_propias_view.fecha_llegada',
                    'oc_propias_view.flete_real',
                    'oc_propias_view.id_transportista',
                    'oc_propias_view.tipo',
                    'oc_propias_view.id as id_orden',
                    'oc_propias_view.nro_orden',
                    'oc_directas.id_despacho as id_despacho_directa',
                    'oc_propias.id_despacho as id_despacho_propia'
                )
                ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'orden_despacho.id_requerimiento')
                ->join('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
                ->leftJoin('mgcp_ordenes_compra.oc_directas', 'oc_directas.id', '=', 'oc_propias_view.id')
                ->leftJoin('mgcp_acuerdo_marco.oc_propias', 'oc_propias.id', '=', 'oc_propias_view.id')
                ->where([
                    ['orden_despacho.aplica_cambios', '=', false],
                    ['orden_despacho.estado', '!=', 7]
                ])
                ->get();

            $rsptas = [];
            $id_usuario = Auth::user()->id_usuario;
            $update = 0;
            $save = 0;

            foreach ($despachos as $req) {
                //tiene un despacho
                $id_despacho = $req->id_despacho_directa !== null ? $req->id_despacho_directa
                    : ($req->id_despacho_propia !== null ? $req->id_despacho_propia : null);

                $flete_real = (floatval($req->importe_flete) + floatval($req->suma_gasto_extra));

                //si ya existe un despacho
                if ($id_despacho !== null) {
                    DB::table('mgcp_ordenes_compra.despachos')
                        ->where('id', $id_despacho)
                        ->update([
                            'id_transportista' => $req->id_transportista,
                            'flete_real' => $flete_real,
                            'fecha_llegada' => $req->fecha_entregada,
                            'fecha_salida' => $req->fecha_despacho_real
                        ]);
                    $update++;
                    array_push($rsptas, [
                        'tipo' => 'update',
                        'id_despacho' => $id_despacho,
                        'mensaje' => 'nro_orden: ' . $req->nro_orden . ' id_transportista = ' . $req->id_transportista . ' flete real = ' . $flete_real
                    ]);
                } else {
                    $id_despacho = DB::table('mgcp_ordenes_compra.despachos')
                        ->insertGetId([
                            'id_transportista' => $req->id_transportista,
                            'flete_real' => $flete_real,
                            'fecha_llegada' => $req->fecha_entregada,
                            'fecha_salida' => $req->fecha_despacho_real,
                            'id_usuario' => $id_usuario,
                            'fecha_registro' => new Carbon(),
                        ], 'id');
                    $save++;
                    array_push($rsptas, [
                        'tipo' => 'save',
                        'id_despacho' => $id_despacho,
                        'mensaje' => 'nro_orden: ' . $req->nro_orden . ' id_transportista = ' . $req->id_transportista . ' flete real = ' . $flete_real
                    ]);
                }

                if ($req->tipo == 'am') {
                    DB::table('mgcp_acuerdo_marco.oc_propias')
                        ->where('oc_propias.id', $req->id_orden)
                        ->update([
                            'despachada' => true,
                            'id_despacho' => $id_despacho
                        ]);
                } else {
                    DB::table('mgcp_ordenes_compra.oc_directas')
                        ->where('oc_directas.id', $req->id_orden)
                        ->update([
                            'despachada' => true,
                            'id_despacho' => $id_despacho
                        ]);
                }
            }
            DB::commit();
            return response()->json(['updates' => $update, 'saves' => $save, 'rsptas' => $rsptas]);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function actualizarOcFisica(Request $request)
    {
        try {
            DB::beginTransaction();

            if ($request->tipo == 'am') {
                DB::table('mgcp_acuerdo_marco.oc_propias')
                    ->where('oc_propias.id', $request->id)
                    ->update(['orden_compra' => $request->oc_fisica]);
            } else if ($request->tipo == 'directa') {
                DB::table('mgcp_ordenes_compra.oc_directas')
                    ->where('oc_directas.id', $request->id)
                    ->update(['orden_compra' => $request->oc_fisica]);
            }
            DB::commit();

            return array('tipo' => 'success', 'mensaje' => 'Se actualizó correctamente la OC física de la ' . $request->nro_orden);
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage());
        }
    }


    public function actualizarSiaf(Request $request)
    {
        try {
            DB::beginTransaction();

            if ($request->tipo == 'am') {
                DB::table('mgcp_acuerdo_marco.oc_propias')
                    ->where('oc_propias.id', $request->id)
                    ->update(['siaf' => $request->siaf]);
            } else if ($request->tipo == 'directa') {
                DB::table('mgcp_ordenes_compra.oc_directas')
                    ->where('oc_directas.id', $request->id)
                    ->update(['siaf' => $request->siaf]);
            }
            DB::commit();

            return array('tipo' => 'success', 'mensaje' => 'Se actualizó el SIAF correctamente la ' . $request->nro_orden);
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage());
        }
    }
    public function adjuntosDespacho(Request $request)
    {
        $success = false;
        $adjuntos_despacho = AdjuntosDespacho::where('estado', 1)->where('id_oportunidad', $request->id_oportunidad)->where('id_requerimiento', $request->id_requerimiento)->get();
        if (sizeof($adjuntos_despacho) > 0) {
            $success = true;
        }
        return response()->json(['success' => $success, 'data' => $adjuntos_despacho]);
    }
    public function prueba($id)
    {
        $data = DB::table('almacen.alm_req')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'oc_propias_view.fecha_entrega',
                'alm_req.tiene_transformacion',
                'alm_req.direccion_entrega',
                'alm_req.id_ubigeo_entrega',
                'alm_req.id_almacen',
                'alm_req.id_sede as sede_requerimiento',
                'alm_req.telefono',
                'alm_req.email',
                'alm_req.id_cliente',
                'alm_req.id_prioridad',
                'alm_req.id_contacto',
                'alm_req.enviar_contacto',
                'alm_req.estado',
                'alm_req.estado_despacho',
                'alm_tp_req.descripcion as tipo_requerimiento_descripcion',
                'sis_usua.nombre_corto as responsable',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'alm_almacen.descripcion as almacen_descripcion',
                'sede_req.descripcion as sede_descripcion_req',
                'adm_contri.id_contribuyente',
                'adm_contri.nro_documento as cliente_ruc',
                'adm_contri.razon_social as cliente_razon_social',
                'orden_despacho.id_od',
                'orden_despacho.fecha_despacho',
                'orden_despacho.nro_orden as numero_orden',
                'orden_despacho.persona_contacto',
                'orden_despacho.direccion_destino',
                'orden_despacho.correo_cliente',
                'orden_despacho.telefono as telefono_od',
                'orden_despacho.ubigeo_destino',
                'orden_despacho.codigo as codigo_od',
                'orden_despacho.estado as estado_od',
                'orden_despacho.serie as serie_tra',
                'orden_despacho.numero as numero_tra',
                'orden_despacho.serie_guia_venta as serie_guia',
                'orden_despacho.numero_guia_venta as numero_guia',
                'orden_despacho.fecha_transportista',
                'orden_despacho.codigo_envio',
                'orden_despacho.credito',
                'orden_despacho.importe_flete',
                'orden_despacho.id_transportista',
                'orden_despacho.plazo_excedido',
                'orden_despacho.fecha_entregada',
                'orden_despacho.fecha_despacho_real',
                'despachoInterno.id_od as id_despacho_interno',
                'despachoInterno.codigo as codigo_despacho_interno',
                'despachoInterno.estado as estado_di',
                'estado_envio.descripcion as estado_envio',
                'transportista.razon_social as transportista_razon_social',
                'guia_ven.serie',
                'guia_ven.numero',
                DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho_obs where
                            orden_despacho_obs.id_od = orden_despacho.id_od
                            and orden_despacho.estado != 7) AS count_estados_envios"),
                DB::raw("(SELECT SUM(orden_despacho_obs.gasto_extra) FROM almacen.orden_despacho_obs where
                            orden_despacho_obs.id_od = orden_despacho.id_od
                            and orden_despacho.estado != 7) AS gasto_extra"),
                DB::raw("(SELECT orden_despacho_obs.adjunto FROM almacen.orden_despacho_obs where
                            orden_despacho_obs.id_od = orden_despacho.id_od
                            and (orden_despacho_obs.accion = 8 or orden_despacho_obs.accion = 7 or orden_despacho_obs.accion = 6)
                            and orden_despacho.estado != 7
                            order by id_obs desc limit 1) AS adjunto"),
                'oc_propias_view.nro_orden',
                'oportunidades.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo',
                'oc_propias_view.id_oportunidad',
                'oc_propias_view.id_entidad',
                'oc_propias_view.estado_oc',
                'oc_propias_view.moneda_oc',
                'oc_propias_view.monto_total',
                'oc_propias_view.fecha_publicacion',
                'oc_propias_view.estado_aprobacion_cuadro',
                'oc_propias_view.siaf',
                'oc_propias_view.orden_compra',
                'oc_propias_view.occ',
                'oc_propias_view.tiene_comentarios',
                'oc_propias_view.nombre_entidad',
                'oc_propias_view.nombre_largo_responsable',
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req where
                            alm_det_req.id_requerimiento = alm_req.id_requerimiento
                            and alm_det_req.estado != 7
                            and alm_det_req.id_producto is null) AS productos_no_mapeados"),
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req where
                            alm_det_req.id_requerimiento = alm_req.id_requerimiento
                            and alm_det_req.estado != 7
                            and alm_det_req.id_tipo_item = 1) AS count_productos"),
            )
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'oportunidades.id')
            ->leftJoin('almacen.alm_tp_req', 'alm_tp_req.id_tipo_requerimiento', '=', 'alm_req.id_tipo_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado_despacho')
            ->leftJoin('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function ($join) {
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.aplica_cambios', '=', false);
                $join->where('orden_despacho.estado', '!=', 7);
            })
            ->leftJoin('almacen.orden_despacho as despachoInterno', function ($join) {
                $join->on('despachoInterno.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('despachoInterno.aplica_cambios', '=', true);
                $join->where('despachoInterno.estado', '!=', 7);
            })
            ->leftJoin('contabilidad.adm_contri as transportista', 'transportista.id_contribuyente', '=', 'orden_despacho.id_transportista')
            ->leftJoin('administracion.adm_estado_doc as est_od', 'est_od.id_estado_doc', '=', 'orden_despacho.estado')
            ->leftJoin('almacen.estado_envio', 'estado_envio.id_estado', '=', 'orden_despacho.id_estado_envio')
            ->leftJoin('almacen.guia_ven', 'guia_ven.id_od', '=', 'orden_despacho.id_od')
            ->where([
                ['alm_req.estado', '!=', 7]
            ])
            ->where('alm_req.codigo', $id)->get();
        return $data;
    }
}
