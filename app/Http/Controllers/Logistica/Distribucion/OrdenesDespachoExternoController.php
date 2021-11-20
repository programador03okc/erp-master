<?php

namespace App\Http\Controllers\Logistica\Distribucion;

use App\Exports\DespachosExternosExport;
use App\Helpers\mgcp\OrdenCompraAmHelper;
use App\Helpers\mgcp\OrdenCompraDirectaHelper;
use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\EmailContactoDespacho;
use App\Mail\EmailOrdenDespacho;
use App\Models\Almacen\Requerimiento;
use App\Models\Configuracion\Usuario;
use App\Models\Contabilidad\ContactoContribuyente;
use App\Models\Distribucion\OrdenDespacho;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\Oportunidad\Oportunidad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OrdenesDespachoExternoController extends Controller
{
    public function __construct()
    {
        // session_start();
    }

    function view_ordenes_despacho_externo()
    {
        $estados = DB::table('almacen.estado_envio')
            ->where([
                ['id_estado', '>=', 3],
                ['id_estado', '<=', 8]
            ])
            ->get();
        return view('almacen/distribucion/ordenesDespachoExterno', compact('estados'));
    }

    public function listarDespachosExternos(Request $request)
    {
        $data = Requerimiento::select(
            'alm_req.id_requerimiento',
            'alm_req.codigo',
            'alm_req.concepto',
            'alm_req.fecha_entrega',
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
            'sis_usua.nombre_corto as responsable',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            // DB::raw("(ubi_dis.descripcion) || ' - ' || (ubi_prov.descripcion) || ' - ' || (ubi_dpto.descripcion) AS ubigeo_descripcion"),
            'alm_almacen.descripcion as almacen_descripcion',
            'sede_req.descripcion as sede_descripcion_req',
            'adm_contri.id_contribuyente',
            'adm_contri.nro_documento as cliente_ruc',
            'adm_contri.razon_social as cliente_razon_social',
            'orden_despacho.id_od',
            'orden_despacho.fecha_despacho',
            'orden_despacho.persona_contacto',
            'orden_despacho.direccion_destino',
            'orden_despacho.correo_cliente',
            'orden_despacho.telefono as telefono_od',
            'orden_despacho.ubigeo_destino',
            'orden_despacho.codigo as codigo_od',
            'orden_despacho.estado as estado_od',
            'orden_despacho.serie as serie_tra',
            'orden_despacho.numero as numero_tra',
            'orden_despacho.fecha_transportista',
            'orden_despacho.codigo_envio',
            'orden_despacho.credito',
            'orden_despacho.importe_flete',
            'orden_despacho.id_transportista',
            'orden_despacho.plazo_excedido',
            'orden_despacho.fecha_entregada',
            'orden_despacho.fecha_despacho_real',
            'est_od.estado_doc as estado_od',
            'estado_envio.descripcion as estado_envio',
            'est_od.bootstrap_color as estado_bootstrap_od',
            'transportista.razon_social as transportista_razon_social',
            'guia_ven.serie',
            'guia_ven.numero',
            // DB::raw("(od_dis.descripcion) || ' - ' || (od_prov.descripcion) || ' - ' || (od_dpto.descripcion) AS od_ubigeo_descripcion"),
            DB::raw("(SELECT COUNT(*) FROM almacen.orden_despacho_obs where
                        orden_despacho_obs.id_od = orden_despacho.id_od
                        and orden_despacho.estado != 7) AS count_estados_envios"),
            DB::raw("(SELECT SUM(orden_despacho_obs.gasto_extra) FROM almacen.orden_despacho_obs where
                        orden_despacho_obs.id_od = orden_despacho.id_od
                        and orden_despacho.estado != 7) AS gasto_extra"),
            // 'oc_propias_view.codigo_oportunidad',
            'oc_propias_view.nro_orden',
            'oportunidades.codigo_oportunidad',
            'oportunidades.id as id_oportunidad',
            'oc_propias_view.id as id_oc_propia',
            'oc_propias_view.tipo',
            'oc_propias_view.id_entidad',
            'oc_propias_view.estado_oc',
            'oc_propias_view.fecha_publicacion',
            'oc_propias_view.estado_aprobacion_cuadro',
            'oc_propias_view.siaf',
            'oc_propias_view.occ',
            'oc_propias_view.tiene_comentarios',
            DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req where
                        alm_det_req.id_requerimiento = alm_req.id_requerimiento
                        and alm_det_req.estado != 7
                        and alm_det_req.id_producto is null) AS productos_no_mapeados")
        )
            ->join('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            // ->leftJoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'alm_req.id_ubigeo_entrega')
            // ->leftJoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            // ->leftJoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->leftJoin('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function ($join) {
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.aplica_cambios', '=', false);
                $join->where('orden_despacho.estado', '!=', 7);
            })
            ->leftJoin('contabilidad.adm_contri as transportista', 'transportista.id_contribuyente', '=', 'orden_despacho.id_transportista')
            // ->leftJoin('configuracion.ubi_dis as od_dis', 'od_dis.id_dis', '=', 'orden_despacho.ubigeo_destino')
            // ->leftJoin('configuracion.ubi_prov as od_prov', 'od_prov.id_prov', '=', 'od_dis.id_prov')
            // ->leftJoin('configuracion.ubi_dpto as od_dpto', 'od_dpto.id_dpto', '=', 'od_prov.id_dpto')
            ->leftJoin('administracion.adm_estado_doc as est_od', 'est_od.id_estado_doc', '=', 'orden_despacho.estado')
            ->leftJoin('almacen.estado_envio', 'estado_envio.id_estado', '=', 'orden_despacho.id_estado_envio')
            ->leftJoin('almacen.guia_ven', 'guia_ven.id_od', '=', 'orden_despacho.id_od')
            ->where([
                ['alm_req.estado', '!=', 7],
                // ['orden_despacho.estado', '!=', 7],
                // ['alm_det_req.estado', '!=', 7],
                // ['alm_reserva.estado', '!=', 7],
                // ['alm_reserva.estado', '!=', 5],
                // ['alm_reserva.stock_comprometido', '>', 0]
            ]);
        if ($request->select_mostrar == 1) {
            // $data->whereNotNull('orden_despacho.fecha_despacho');
            $data->where('orden_despacho.estado', 25);
        } else if ($request->select_mostrar == 2) {
            $data->where('orden_despacho.estado', 25);
            $data->whereDate('orden_despacho.fecha_despacho', (new Carbon())->format('Y-m-d'));
        }
        return $data;
        // return datatables($data)->toJson();
    }

    public function listarRequerimientosPendientesDespachoExterno(Request $request)
    {
        $query = $this->listarDespachosExternos($request);
        return datatables($query)->toJson();
    }

    public function despachosExternosExcel(Request $request)
    {
        $data = $this->listarDespachosExternos($request);
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
    private function enviarOrdenDespacho(Request $request)
    {
        $requerimiento = Requerimiento::find($request->id_requerimiento);
        $cuadro = CuadroCosto::find($requerimiento->id_cc);
        $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
        $ordenView = $oportunidad->ordenCompraPropia;
        $archivosOc = [];
        //Obtencion de archivos en carpeta temporal
        if ($ordenView != null) {
            if ($ordenView->tipo == 'am') {
                $archivosOc = OrdenCompraAmHelper::descargarArchivos($ordenView->id);
            } else {
                $archivosOc = OrdenCompraDirectaHelper::copiarArchivos($ordenView->id);
            }
        }
        //Guardar archivos subidos
        if ($request->hasFile('archivos')) {
            $archivos = $request->file('archivos');
            foreach ($archivos as $archivo) {
                Storage::putFileAs('mgcp/ordenes-compra/temporal/', $archivo, $archivo->getClientOriginalName());
                $archivosOc[] = storage_path('app/mgcp/ordenes-compra/temporal/') . $archivo->getClientOriginalName();
            }
        }
        $correos = [];
        if (config('app.debug')) {
            $correos[] = config('global.correoDebug1');
        } else {
            $idUsuarios = Usuario::getAllIdUsuariosPorRol(25);
            foreach ($idUsuarios as $id) {
                $correos[] = Usuario::find($id)->email;
            }
        }

        Mail::to($correos)->send(new EmailOrdenDespacho($oportunidad, $request->mensaje, $archivosOc));

        foreach ($archivosOc as $archivo) {
            unlink($archivo);
        }
    }

    public function guardarOrdenDespachoExterno(Request $request)
    {
        try {
            DB::beginTransaction();

            $ordenDespacho = OrdenDespacho::where([
                ['id_requerimiento', '=', $request->id_requerimiento],
                ['aplica_cambios', '=', false],
                ['estado', '!=', 7]
            ])->first();

            if ($ordenDespacho == null) {

                $usuario = Auth::user()->id_usuario;
                $fechaRegistro = new Carbon(); //date('Y-m-d H:i:s');
                $id_estado_envio = 1; //despacho elaborado

                $req = Requerimiento::where('id_requerimiento', $request->id_requerimiento)->first();
                $ordenDespacho = new OrdenDespacho();
                $ordenDespacho->id_sede = $req->id_sede;
                $ordenDespacho->id_requerimiento = $req->id_requerimiento;
                $ordenDespacho->id_cliente = $req->id_cliente;
                $ordenDespacho->id_persona = $req->id_persona;
                $ordenDespacho->id_almacen = $req->id_almacen;
                $ordenDespacho->aplica_cambios = false;
                $ordenDespacho->registrado_por = $usuario;
                $ordenDespacho->fecha_despacho = $fechaRegistro;
                $ordenDespacho->fecha_registro = $fechaRegistro;
                $ordenDespacho->estado = 1;
                $ordenDespacho->id_estado_envio = $id_estado_envio;
                $ordenDespacho->save();
                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                    ->insert([
                        'id_requerimiento' => $request->id_requerimiento,
                        'accion' => 'DESPACHO EXTERNO',
                        'descripcion' => 'Se generó la Orden de Despacho Externa',
                        'id_usuario' => $usuario,
                        'fecha_registro' => $fechaRegistro
                    ]);

                $detalle = DB::table('almacen.alm_det_req')
                    ->where([
                        ['id_requerimiento', '=', $request->id_requerimiento],
                        ['tiene_transformacion', '=', $req->tiene_transformacion],
                        ['estado', '!=', 7]
                    ])
                    ->get();

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
                    ->where('id_requerimiento', $request->id_requerimiento)
                    ->update(['estado' => 23]); //despacho externo

                $ordenDespacho->codigo = OrdenDespacho::ODnextId($req->id_almacen, false, $ordenDespacho->id_od);
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
                            'registrado_por' => $usuario,
                            'fecha_registro' => $fechaRegistro
                        ]);
                } else {
                    //si no existe, crea
                    DB::table('almacen.orden_despacho_obs')
                        ->insert([
                            'id_od' => $ordenDespacho->id_od,
                            'accion' => $id_estado_envio,
                            'observacion' => 'Fue despachado con ' . $ordenDespacho->codigo,
                            'registrado_por' => $usuario,
                            'fecha_registro' => $fechaRegistro
                        ]);
                }
                /*if ($codigo !== null) {
                    DB::table('almacen.orden_despacho')
                        ->where('id_od', $ordenDespacho->id_od)
                        ->update(['codigo' => $codigo]);
                }*/
                //$msj = 'Se guardó existosamente la Orden de Despacho';
            } /*else {
                $msj = '';
            }*/
            $this->enviarOrdenDespacho($request);

            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se envió la orden con código ' . $ordenDespacho->codigo), 200);
            //return response()->json($msj);
        } catch (\PDOException $e) {
            DB::rollBack();
            //return response()->json('Algo salió mal');
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage()), 200);
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
                        ->where([
                            ['id_contribuyente', '=', $requerimiento->id_contribuyente],
                            ['estado', '!=', 7]
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
            $listaContactos = ContactoContribuyente::where([
                ['id_contribuyente', '=', $id_contribuyente],
                ['estado', '!=', 7]
            ])
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

            DB::table('almacen.alm_req')
                ->where('id_requerimiento', $request->id_requerimiento)
                ->update(['id_contacto' => $id_contacto]);

            DB::commit();
            return response()->json(
                array(
                    'tipo' => 'success',
                    'mensaje' => 'Se ha ' . $texto . ' el contacto.',
                    'id_contacto' => $id_contacto
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
            } else {
                $idUsuarios = Usuario::getAllIdUsuariosPorRol(25);
                foreach ($idUsuarios as $id) {
                    $correos[] = Usuario::find($id)->email;
                }
            }

            Mail::to($correos)->send(new EmailContactoDespacho($oportunidad, $request->mensaje));

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
                    ->where('id_od', $det)
                    ->update([
                        'fecha_despacho' => $request->fecha_despacho,
                        'estado' => 25 //priorizado
                    ]);
            }
            DB::commit();
            return response()->json('ok');
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(':(');
        }
    }

    public function despachoTransportista(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $fecha_registro = date('Y-m-d H:i:s');
            $id_estado_envio = 2; //transportandose (ag transp. lima)

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
                    'id_estado_envio' => $id_estado_envio,
                    // 'propia'=>((isset($request->transporte_propio)&&$request->transporte_propio=='on')?true:false),
                    'credito' => ((isset($request->credito) && $request->credito == 'on') ? true : false),
                ]);

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
                            'registrado_por' => $id_usuario,
                            'fecha_registro' => $fecha_registro
                        ]);
                } else {
                    //si no existe este estado lo crea
                    DB::table('almacen.orden_despacho_obs')
                        ->insert([
                            'id_od' => $request->id_od,
                            'accion' => $id_estado_envio,
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
}
