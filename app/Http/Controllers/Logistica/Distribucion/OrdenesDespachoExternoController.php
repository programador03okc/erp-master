<?php

namespace App\Http\Controllers\Logistica\Distribucion;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenesDespachoExternoController extends Controller
{
    public function __construct()
    {
        // session_start();
    }

    function view_ordenes_despacho_externo()
    {
        return view('almacen/distribucion/ordenesDespachoExterno');
    }

    public function listarRequerimientosPendientesDespachoExterno()
    {
        $data = DB::table('almacen.alm_req')
            ->select(
                'alm_req.*',
                'sis_usua.nombre_corto as responsable',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                DB::raw("(ubi_dis.descripcion) || ' - ' || (ubi_prov.descripcion) || ' - ' || (ubi_dpto.descripcion) AS ubigeo_descripcion"),
                'alm_almacen.descripcion as almacen_descripcion',
                'alm_req.id_sede as sede_requerimiento',
                'sede_req.descripcion as sede_descripcion_req',
                'orden_despacho.id_od',
                'orden_despacho.codigo as codigo_od',
                'orden_despacho.estado as estado_od',
                'adm_contri.nro_documento as cliente_ruc',
                'adm_contri.razon_social as cliente_razon_social',
                DB::raw("(SELECT COUNT(*) FROM almacen.trans where
                        trans.id_requerimiento = alm_req.id_requerimiento
                        and trans.estado != 7) AS count_transferencia"),
                'oc_propias_view.nro_orden',
                'oc_propias_view.codigo_oportunidad',
                'oc_propias_view.id as id_oc_propia',
                'oc_propias_view.tipo',
                DB::raw("(SELECT COUNT(*) FROM almacen.alm_det_req where
                        alm_det_req.id_requerimiento = alm_req.id_requerimiento
                        and alm_det_req.estado != 7
                        and alm_det_req.id_producto is null) AS productos_no_mapeados")
            )
            // ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_reserva.id_detalle_requerimiento')
            // ->join('almacen.alm_req', function ($join) {
            //     $join->on('alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento');
            // $join->on('alm_req.id_almacen', '=', 'alm_reserva.id_almacen_reserva');
            // $join->whereNotNull('alm_reserva.id_almacen_reserva');
            // })
            ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')
            ->leftJoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'alm_req.id_ubigeo_entrega')
            ->leftJoin('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->leftJoin('administracion.sis_sede as sede_req', 'sede_req.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
            ->leftJoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftJoin('almacen.orden_despacho', function ($join) {
                $join->on('orden_despacho.id_requerimiento', '=', 'alm_req.id_requerimiento');
                $join->where('orden_despacho.aplica_cambios', '=', false);
                $join->where('orden_despacho.estado', '!=', 7);
            })
            ->where([
                ['alm_req.estado', '!=', 7],
                // ['alm_det_req.estado', '!=', 7],
                // ['alm_reserva.estado', '!=', 7],
                // ['alm_reserva.estado', '!=', 5],
                // ['alm_reserva.stock_comprometido', '>', 0]
            ]);
        // ->whereIn('alm_req.estado',[])
        // ->distinct();
        return datatables($data)->toJson();
    }

    public static function ODnextId($fecha_despacho, $id_almacen, $aplica_cambios, $id)
    {
        $yyyy = date('Y', strtotime($fecha_despacho));
        $yy = date('y', strtotime($fecha_despacho));

        $cantidad = DB::table('almacen.orden_despacho')
            ->whereYear('fecha_despacho', '=', $yyyy)
            ->where([
                ['id_almacen', '=', $id_almacen],
                ['aplica_cambios', '=', $aplica_cambios],
                ['estado', '!=', 7],
                ['id_od', '<=', $id],
            ])
            ->get()->count();

        $val = AlmacenController::leftZero(4, $cantidad);
        $nextId = "OD" . ($aplica_cambios ? "I-" : "E-") . $id_almacen . "-" . $yy . $val;
        return $nextId;
    }

    public function guardarOrdenDespachoExterno(Request $request)
    {

        try {
            DB::beginTransaction();

            $tiene_transformacion = ($request->tiene_transformacion == 'si' ? true : false);

            $usuario = Auth::user()->id_usuario;

            $id_od = DB::table('almacen.orden_despacho')
                ->insertGetId(
                    [
                        'id_sede' => $request->id_sede,
                        'id_requerimiento' => $request->id_requerimiento,
                        'id_cliente' => $request->id_cliente,
                        'id_persona' => ($request->id_persona > 0 ? $request->id_persona : null),
                        'id_almacen' => $request->id_almacen,
                        'telefono' => trim($request->telefono_cliente),
                        'codigo' => '-',
                        'persona_contacto' => trim($request->persona_contacto),
                        'ubigeo_destino' => $request->ubigeo,
                        'direccion_destino' => trim($request->direccion_destino),
                        'correo_cliente' => trim($request->correo_cliente),
                        'fecha_despacho' => date('Y-m-d'),
                        'hora_despacho' => date('H:i:s'),
                        'fecha_entrega' => $request->fecha_entrega,
                        'aplica_cambios' => false,
                        'registrado_por' => $usuario,
                        'tipo_entrega' => $request->tipo_entrega,
                        'fecha_registro' => date('Y-m-d H:i:s'),
                        'documento' => $request->documento,
                        'estado' => 1,
                        'tipo_cliente' => $request->tipo_cliente
                    ],
                    'id_od'
                );

            if ($request->id_requerimiento !== null) {
                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $request->id_requerimiento)
                    ->update(['enviar_facturacion' => true]);
            }

            //Si es Despacho Externo

            //Agrega accion en requerimiento
            DB::table('almacen.alm_req_obs')
                ->insert([
                    'id_requerimiento' => $request->id_requerimiento,
                    'accion' => 'DESPACHO EXTERNO',
                    'descripcion' => 'Se generó la Orden de Despacho Externa',
                    'id_usuario' => $usuario,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ]);

            // $data = json_decode($request->detalle_requerimiento);
            $detalle = DB::table('almacen.alm_det_req')
                ->where([
                    ['id_requerimiento', '=', $request->id_requerimiento],
                    ['tiene_transformacion', '=', $tiene_transformacion],
                    ['estado', '!=', 7]
                ])
                ->get();

            foreach ($detalle as $d) {
                // $descripcion = ($d->producto_descripcion !== null ? $d->producto_descripcion : $d->descripcion_adicional);
                // if ($tiene_transformacion) {
                //     if ($d->tiene_transformacion) {

                DB::table('almacen.orden_despacho_det')
                    ->insert([
                        'id_od' => $id_od,
                        'id_producto' => $d->id_producto,
                        'id_detalle_requerimiento' => $d->id_detalle_requerimiento,
                        'cantidad' => $d->cantidad,
                        'transformado' => $tiene_transformacion,
                        'estado' => 1,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ]);

                DB::table('almacen.alm_det_req')
                    ->where('id_detalle_requerimiento', $d->id_detalle_requerimiento)
                    ->update(['estado' => 29]); //por despachar
                // }
                // } else {
                //     DB::table('almacen.orden_despacho_det')
                //         ->insert([
                //             'id_od' => $id_od,
                //             'id_producto' => $d->id_producto,
                //             'id_detalle_requerimiento' => $d->id_detalle_requerimiento,
                //             'cantidad' => $d->cantidad,
                //             'transformado' => false,
                //             'estado' => 1,
                //             'fecha_registro' => date('Y-m-d H:i:s')
                //         ]);

                //     DB::table('almacen.alm_det_req')
                //         ->where('id_detalle_requerimiento', $d->id_detalle_requerimiento)
                //         ->update(['estado' => 29]); //por despachar
                // }
            }
            // }
            DB::table('almacen.alm_req')
                ->where('id_requerimiento', $request->id_requerimiento)
                ->update(['estado' => 29]); //por despachar


            /*
            $req = DB::table('almacen.alm_req')
                ->select(
                    'alm_req.*',
                    'oc_propias.id as id_oc_propia',
                    'oc_propias.url_oc_fisica',
                    'entidades.nombre',
                    'adm_contri.razon_social',
                    'oportunidades.codigo_oportunidad',
                    'adm_empresa.codigo as codigo_empresa',
                    'oc_propias.orden_am',
                    'adm_empresa.id_empresa'
                )
                ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->leftjoin('mgcp_acuerdo_marco.oc_propias', 'oc_propias.id_oportunidad', '=', 'oportunidades.id')
                ->leftjoin('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'oportunidades.id_entidad')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->where('id_requerimiento', $request->id_requerimiento)
                ->first();
            
            if ($req->id_tipo_requerimiento == 1) {

                $asunto_facturacion = $req->orden_am . ' | ' . $req->nombre . ' | ' . $req->codigo_oportunidad . ' | ' . $req->codigo_empresa;
                $contenido_facturacion = '
                    Favor de generar documentación: <br>- ' . ($request->documento == 'Factura' ? $request->documento . '<br>- Guía<br>- Certificado de Garantía<br>- CCI<br>' : '<br>') . ' 
                    <br>Requerimiento ' . $req->codigo . '
                    <br>Entidad: ' . $req->nombre . '
                    <br>Empresa: ' . $req->razon_social . '
                    <br>' . $request->contenido . '<br>
            <br>' . ($req->id_oc_propia !== null
                    ? ('Ver Orden Física: ' . $req->url_oc_fisica . ' 
            <br>Ver Orden Electrónica: https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=' . $req->id_oc_propia . '&ImprimirCompleto=1') : '') . '
            <br><br>
            Saludos,<br>
            Módulo de Despachos<br>
            SYSTEM AGILE';

                $msj = '';
                $email_destinatario[] = 'programador01@okcomputer.com.pe';
                // $email_destinatario[] = 'administracionventas@okcomputer.com.pe';
                // $email_destinatario[] = 'asistente.contable.lima@okcomputer.com.pe';
                // $email_destinatario[] = 'asistente.contable@okcomputer.com.pe';
                // $email_destinatario[] = 'administracionventas@okcomputer.com.pe';
                // $email_destinatario[] = 'asistente.almacenlima1@okcomputer.com.pe';
                // $email_destinatario[] = 'asistente.almacenlima2@okcomputer.com.pe';
                // $email_destinatario[] = 'asistente.almacenlima@okcomputer.com.pe';
                // $email_destinatario[] = 'logistica.lima@okcomputer.com.pe';
                // $email_destinatario[] = 'soporte.lima@okcomputer.com.pe';
                // $email_destinatario[] = 'contadorgeneral@okcomputer.com.pe';
                // $email_destinatario[] = 'infraestructura@okcomputer.com.pe';
                // $email_destinatario[] = 'lenovo@okcomputer.com.pe';
                // $email_destinatario[] = 'logistica@okcomputer.com.pe';
                // $email_destinatario[] = 'dapaza@okcomputer.com.pe';
                // $email_destinatario[] = 'asistente.logistica@okcomputer.com.pe';
                $payload = [
                    'id_empresa' => $req->id_empresa,
                    'email_destinatario' => $email_destinatario,
                    'titulo' => $asunto_facturacion,
                    'mensaje' => $contenido_facturacion
                ];

                $smpt_setting = [
                    'smtp_server' => 'smtp.gmail.com',
                    // 'smtp_server'=>'outlook.office365.com',
                    'port' => 587,
                    'encryption' => 'tls',
                    'email' => 'webmaster@okcomputer.com.pe',
                    'password' => 'MgcpPeru2020*'
                    // 'email'=>'programador01@okcomputer.com.pe',
                    // 'password'=>'Dafne0988eli@'
                    // 'email'=>'administracionventas@okcomputer.com.pe',
                    // 'password'=>'Logistica1505'
                ];

                if (count($email_destinatario) > 0) {
                    $estado_envio = (new CorreoController)->enviar_correo_despacho($payload, $smpt_setting);
                }
            } else {
                $msj = 'Se guardó existosamente la Orden de Despacho';
            }*/
            $msj = 'Se guardó existosamente la Orden de Despacho';
            DB::commit();

            $codigo = OrdenesDespachoExternoController::ODnextId(date('Y-m-d'), $request->id_almacen, false, $id_od);

            if ($codigo !== null) {
                DB::table('almacen.orden_despacho')
                    ->where('id_od', $id_od)
                    ->update(['codigo' => $codigo]);
            }

            return response()->json($msj);
        } catch (\PDOException $e) {
            DB::rollBack();
        }
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
}
