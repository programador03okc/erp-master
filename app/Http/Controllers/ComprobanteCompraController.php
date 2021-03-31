<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

// use Mail;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use Dompdf\Dompdf;
use PDF;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\Logistica\Empresa;
use App\Models\Tesoreria\Usuario;
use App\Models\Tesoreria\Grupo;
use DataTables;
use Debugbar;

date_default_timezone_set('America/Lima');

class ComprobanteCompraController extends Controller
{

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

    public function mostrar_guia_clas_cbo()
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

    public function mostrar_tp_doc_cbo()
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

    public function mostrar_impuestos_cbo(){
        $data = DB::table('contabilidad.cont_impuesto')
            ->select('cont_impuesto.id_impuesto','cont_impuesto.descripcion',
            'cont_impuesto.porcentaje')
            ->where('cont_impuesto.estado', '=', 1)
            ->get();
        return $data;
    }

    public function select_usuarios(){
        $data = DB::table('configuracion.sis_usua')
            ->select('sis_usua.id_usuario','sis_usua.nombre_corto')
            ->where([['sis_usua.estado', '=', 1],['sis_usua.nombre_corto', '<>', null]])
            ->get();
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

    function get_igv(){

    $igv = DB::table('contabilidad.cont_impuesto')
        ->where('codigo','IGV')->first();
        return $igv;
    }

    function view_genera_comprobante_compra(){
        $proveedores = $this->mostrar_proveedores_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $condiciones = $this->mostrar_condiciones_cbo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $moneda = $this->mostrar_moneda_cbo();
        $detracciones = $this->mostrar_detracciones_cbo();
        $impuestos = $this->mostrar_impuestos_cbo();
        $usuarios = $this->select_usuarios();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = $this->sis_identidad_cbo();
        $igv = $this->get_igv();
        return view('logistica/comprobantes/generar_comprobante_compra', compact('igv','proveedores','clasificaciones','condiciones','tp_doc','moneda','detracciones','impuestos','usuarios','tp_contribuyente','sis_identidad'));
    }

    // public function getListaComprobantesCompra(){
    //     $data = DB::table('almacen.doc_com')
    //     ->select(
    //         'doc_com.*',
    //         'adm_contri.razon_social',
    //         'adm_estado_doc.estado_doc',
    //         'adm_estado_doc.bootstrap_color',
    //         'sis_moneda.descripcion as moneda',
    //         'log_cdn_pago.descripcion AS condicion_pago',
    //         'cont_tp_doc.descripcion as tipo_documento'
    //         )
    //     ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
    //     ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
    //     ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_com.estado')
    //     ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
    //     ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
    //     ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')

    //     ->where('doc_com.estado','!=',7)
    //     ->get();
    //     return $data;
    // }

    // public function listar_docs_compra(){
    //     $data = $this->getListaComprobantesCompra();
    //     $output['data'] = $data;
    //     return response()->json($output);
    // }

    public function listar_docs_compra(){
        $data = DB::table('almacen.doc_com')
        ->select(
            'doc_com.*',
            'adm_contri.razon_social',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            'sis_moneda.simbolo',
            'log_cdn_pago.descripcion AS condicion_pago',
            'cont_tp_doc.descripcion as tipo_documento'
            )
        ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_com.estado')
        ->leftJoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
        ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
        ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
        ->where('doc_com.estado','!=',7);

        return datatables($data)->toJson();
    }

    public function listar_doc_guias($id_doc){
        $guias = DB::table('almacen.doc_com_guia')
        ->select('doc_com_guia.*',DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as nro_guia"),
        'guia_com.fecha_emision as fecha_guia','tp_ope.descripcion as des_operacion',
        'adm_contri.razon_social')
        ->join('almacen.guia_com','guia_com.id_guia','=','doc_com_guia.id_guia_com')
        ->join('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where([['doc_com_guia.id_doc_com','=',$id_doc],
                ['doc_com_guia.estado','=',1]])
        ->get();
        $html ='';
        foreach($guias as $guia){
            $html .= '
            <tr id="doc-'.$guia->id_doc_com_guia.'">
                <td>'.$guia->guia.'</td>
                <td>'.$guia->fecha_guia.'</td>
                <td>'.$guia->razon_social.'</td>
                <td>'.$guia->des_operacion.'</td>
                <td><i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular Guia" onClick="anular_guia('.$guia->id_guia_com.','.$guia->id_doc_com_guia.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }

    public function anular_orden_doc_com($id_doc_com,$id_orden_compra)
    {
        $data=0;
        $ordenes = DB::table('logistica.log_det_ord_compra')
            ->select('log_det_ord_compra.id_detalle_orden')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
            ->where([
                ['log_det_ord_compra.id_orden_compra','=',$id_orden_compra]
                ])
            ->get()->toArray();

        foreach($ordenes as $orden){
            $data= DB::table('almacen.doc_com_det')
            ->where('id_detalle_orden', $orden->id_detalle_orden)
            ->update([ 'estado' => 7 ]);
        }

        return response()->json($data);
    }

    // public function listar_doc_items($id_doc){
    //     $detalle = DB::table('almacen.doc_com_det')
    //         ->select('doc_com_det.*','alm_prod.codigo','alm_prod.descripcion',
    //         DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as nro_guia"),
    //         'alm_und_medida.abreviatura')
    //         ->join('almacen.alm_item','alm_item.id_item','=','doc_com_det.id_item')
    //         ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
    //         ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
    //         ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
    //         ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
    //         ->where([['doc_com_det.id_doc','=',$id_doc],
    //                 ['doc_com_det.estado','=',1]])
    //         ->get();
    //     $html = '';
    //     foreach($detalle as $det){
    //         $html .= '
    //         <tr id="det-'.$det->id_doc_det.'">
    //             <td>'.$det->guia.'</td>
    //             <td>'.$det->codigo.'</td>
    //             <td>'.$det->descripcion.'</td>
    //             <td><input type="number" class="input-data right" name="cantidad" 
    //                 value="'.$det->cantidad.'" onChange="calcula_total('.$det->id_doc_det.');" 
    //                 disabled="true"/>
    //             </td>
    //             <td>'.$det->abreviatura.'</td>
    //             <td><input type="number" class="input-data right" name="precio_unitario" 
    //                 value="'.$det->precio_unitario.'" onChange="calcula_total('.$det->id_doc_det.');" 
    //                 disabled="true"/>
    //             </td>
    //             <td><input type="number" class="input-data right" name="porcen_dscto" 
    //                 value="'.$det->porcen_dscto.'" onChange="calcula_dscto('.$det->id_doc_det.');" 
    //                 disabled="true"/>
    //             </td>
    //             <td><input type="number" class="input-data right" name="total_dscto" 
    //                 value="'.$det->total_dscto.'" onChange="calcula_total('.$det->id_doc_det.');" 
    //                 disabled="true"/>
    //             </td>
    //             <td><input type="number" class="input-data right" name="precio_total" 
    //                 value="'.$det->precio_total.'" disabled="true"/>
    //             </td>
    //             <td style="display:flex;">
    //                 <i class="fas fa-pen-square icon-tabla blue boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$det->id_doc_det.');"></i>
    //                 <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$det->id_doc_det.');"></i>
    //             </td>
    //         </tr>';
    //     }
    //     return json_encode($html);
    // }

    public function guardar_doc_compra(Request $request)
    {
        $doc_com= $request->doc_com;
        $doc_com_detalle= $request->doc_com_detalle;
        $guia_remision= $request->guia_remision;
        $usuario = Auth::user();
        $fecha = date('Y-m-d H:i:s');
        $id_doc = DB::table('almacen.doc_com')->insertGetId(
            [
                'serie' => $doc_com['serie'],
                'numero' => $doc_com['numero'],
                'id_tp_doc' => $doc_com['id_tp_doc'],
                'id_proveedor' => $doc_com['id_proveedor'],
                'fecha_emision' => $doc_com['fecha_emision'],
                'fecha_vcmto' => $doc_com['fecha_vcmto'],
                'id_condicion' => $doc_com['id_condicion'],
                'credito_dias' => $doc_com['credito_dias'],
                'moneda' => $doc_com['moneda'],
                'tipo_cambio' => $doc_com['tipo_cambio'],
                'sub_total' => $doc_com['sub_total'],
                'total_descuento' => $doc_com['total_dscto'],
                'porcen_descuento' => $doc_com['porcen_dscto'],
                'total' => $doc_com['total'],
                'total_igv' => $doc_com['total_igv'],
                'total_ant_igv' => $doc_com['total_ant_igv'],
                'porcen_igv' => $doc_com['porcen_igv'],
                'porcen_anticipo' => $doc_com['porcen_anticipo'],
                'total_otros' => $doc_com['total_otros'],
                'total_a_pagar' => $doc_com['total_a_pagar'],
                'usuario' => $doc_com['usuario'],
                'registrado_por' => $usuario->id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_doc_com'
            );

            $count_doc_com_detalle = count($doc_com_detalle);
            if ($count_doc_com_detalle > 0) {
                for ($i = 0; $i < $count_doc_com_detalle; $i++) {
                    $id_doc_det = DB::table('almacen.doc_com_det')->insertGetId(
                        [
                            'id_doc' => $id_doc,
                            'id_item' => $doc_com_detalle[$i]['id_item'],
                            'cantidad' => $doc_com_detalle[$i]['cantidad'],
                            'id_unid_med' => $doc_com_detalle[$i]['id_unid_med'],
                            'precio_unitario' => $doc_com_detalle[$i]['precio_unitario'],
                            'sub_total' => $doc_com_detalle[$i]['sub_total'],
                            'porcen_dscto' => $doc_com_detalle[$i]['porcen_dscto'],
                            'total_dscto' => $doc_com_detalle[$i]['total_dscto'],
                            'precio_total' => $doc_com_detalle[$i]['total'],
                            'id_guia_com_det' => $doc_com_detalle[$i]['id'],
                            'estado' => 1,
                            'fecha_registro' => $fecha,
                            'obs' => null
                        ],
                        'id_doc_det'
                    );

    
                }
            }
            $count_guia_remision = count($guia_remision);
            if ($count_guia_remision > 0) {
                for ($i = 0; $i < $count_guia_remision; $i++) {
 

                    $id_doc_com_guia = DB::table('almacen.doc_com_guia')->insertGetId(
                        [
                            'id_doc_com' => $id_doc,
                            'id_guia_com' => $guia_remision[$i]['id_guia'],
                            'estado' => 1,
                            'fecha_registro' => $fecha
                        ],
                        'id_doc_com_guia'
                    );
                }
            }

        return response()->json(["id_doc"=>$id_doc]);

    }

    public function update_doc_compra(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user();
        $doc_com= $request->doc_com;
        $doc_com_detalle= $request->doc_com_detalle;
        $guia_remision= $request->guia_remision;

        DB::table('almacen.doc_com')
            ->where('id_doc_com',$doc_com['id_doc_com'])
            ->update([
                'serie' => $doc_com['serie'],
                'numero' => $doc_com['numero'],
                'id_tp_doc' => $doc_com['id_tp_doc'],
                'id_proveedor' => $doc_com['id_proveedor'],
                'fecha_emision' => $doc_com['fecha_emision'],
                'fecha_vcmto' => $doc_com['fecha_vcmto'],
                'id_condicion' => $doc_com['id_condicion'],
                'credito_dias' => $doc_com['credito_dias'],
                'moneda' => $doc_com['moneda'],
                'tipo_cambio' => $doc_com['tipo_cambio'],
                'sub_total' => $doc_com['sub_total'],
                'total_descuento' => $doc_com['total_dscto'],
                'porcen_descuento' => $doc_com['porcen_dscto'],
                'total' => $doc_com['total'],
                'total_igv' => $doc_com['total_igv'],
                'total_ant_igv' => $doc_com['total_ant_igv'],
                'porcen_igv' => $doc_com['porcen_igv'],
                'porcen_anticipo' => $doc_com['porcen_anticipo'],
                'total_otros' => $doc_com['total_otros'],
                'total_a_pagar' => $doc_com['total_a_pagar'],
                'usuario' => $doc_com['usuario'],
                'registrado_por' => $usuario->id_usuario,
            ]);

            $count_doc_com_detalle = count($doc_com_detalle);
            $doc_com_detalle_incluida =[];
            if ($count_doc_com_detalle > 0) {
                for ($i = 0; $i < $count_doc_com_detalle; $i++) {
                    if($doc_com_detalle[$i]['id_doc_det'] >0){ //update or delete
                        if($doc_com_detalle[$i]['estado'] ==7){ // delete?
                            $delete_doc_det = DB::table('almacen.doc_com_det')
                            ->where('id_doc_det', $doc_com_detalle[$i]['id_doc_det'])
                            ->update(['estado' => 7]);
                        }else{ // update
                            $update_doc_det = DB::table('almacen.doc_com_det')
                            ->where('id_doc_det', $doc_com_detalle[$i]['id_doc_det'])
                            ->update(
                                [
                                    'id_doc' => $doc_com['id_doc_com'],
                                    'id_item' => $doc_com_detalle[$i]['id_item'],
                                    'cantidad' => $doc_com_detalle[$i]['cantidad'],
                                    'id_unid_med' => $doc_com_detalle[$i]['id_unid_med'],
                                    'precio_unitario' => $doc_com_detalle[$i]['precio_unitario'],
                                    'sub_total' => $doc_com_detalle[$i]['sub_total'],
                                    'porcen_dscto' => $doc_com_detalle[$i]['porcen_dscto'],
                                    'total_dscto' => $doc_com_detalle[$i]['total_dscto'],
                                    'precio_total' => $doc_com_detalle[$i]['total'],
                                    'id_guia_com_det' => $doc_com_detalle[$i]['id'],
                                    'estado' => 1,
                                    'fecha_registro' => $fecha,
                                    'obs' => null
                                ]);
                        }

                    }else{ //insert
                        if($doc_com_detalle[$i]['estado'] ==null){ 
                            $id_doc_det = DB::table('almacen.doc_com_det')->insertGetId(
                                [
                                    'id_doc' => $doc_com['id_doc_com'],
                                    'id_item' => $doc_com_detalle[$i]['id_item'],
                                    'cantidad' => $doc_com_detalle[$i]['cantidad'],
                                    'id_unid_med' => $doc_com_detalle[$i]['id_unid_med'],
                                    'precio_unitario' => $doc_com_detalle[$i]['precio_unitario'],
                                    'sub_total' => $doc_com_detalle[$i]['sub_total'],
                                    'porcen_dscto' => $doc_com_detalle[$i]['porcen_dscto'],
                                    'total_dscto' => $doc_com_detalle[$i]['total_dscto'],
                                    'precio_total' => $doc_com_detalle[$i]['total'],
                                    'id_guia_com_det' => $doc_com_detalle[$i]['id'],
                                    'estado' => 1,
                                    'fecha_registro' => $fecha,
                                    'obs' => null
                                ],
                                'id_doc_det'
                            );
                        }

                    }
            }
        }

        $count_guia_remision = count($guia_remision);
        if ($count_guia_remision > 0) {
            for ($i = 0; $i < $count_guia_remision; $i++) {
                if($guia_remision[$i]['estado'] == 7 ) { // delete
                    // registrar nueva guia
                    $delete_id_doc_com_guia = DB::table('almacen.doc_com_det')
                    ->where('id_doc_com_guia', $guia_remision[$i]['id_doc_com_guia'])
                    ->update(['estado' => 7]);

                }else{ // insert
                    if($guia_remision[$i]['estado'] == null ) {
                        $id_doc_com_guia = DB::table('almacen.doc_com_guia')->insertGetId(
                            [
                                'id_doc_com' => $doc_com['id_doc_com'],
                                'id_guia_com' => $guia_remision[$i]['id_guia'],
                                'estado' => 1,
                                'fecha_registro' => $fecha
                            ],
                            'id_doc_com_guia'
                        );
                    }
                }
            }
        }

            return response()->json(["id_doc"=>$doc_com['id_doc_com']]);
        }

    public function update_doc_detalle(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_com_det')
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

    public function anular_doc_detalle($id_doc_det)
    {
        $data = DB::table('almacen.doc_com_det')
            ->where('id_doc_det', $id_doc_det)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    
    public function anular_doc_compra($id)
    {
        $guias = DB::table('almacen.doc_com_guia')
        ->join('almacen.guia_com','guia_com.id_guia','=','doc_com_guia.id_guia_com')
        ->where([['doc_com_guia.id_doc_com','=', $id],
                ['doc_com_guia.estado','=',1],
                ['guia_com.estado','!=',7]])
                ->count();

        $rspta = '';
        if ($guias > 0){
            $rspta .= 'El documento esta relacionado con Guias Activas.';
        }

        $prorrateo = DB::table('almacen.guia_com_prorrateo')
        ->where([['id_doc_com','=',$id]])->count();

        if ($prorrateo > 0){
            $rspta .= 'El documento esta como Documento de Prorrateo.';
        }

        if ($guias == 0 && $prorrateo == 0){
            DB::table('almacen.doc_com')->where('id_doc_com', $id)
                ->update([ 'estado' => 7 ]);
            DB::table('almacen.doc_com_det')->where('id_doc', $id)
                ->update([ 'estado' => 7 ]);
            DB::table('almacen.doc_com_guia')->where('id_doc_com', $id)
                ->update([ 'estado' => 7 ]);
        }
        return response()->json($rspta);
    }


    public function mostrar_doc_com($id){
        $doc = DB::table('almacen.doc_com')
            ->select('doc_com.*','adm_estado_doc.estado_doc','sis_usua.nombre_corto','sis_moneda.simbolo',
            'adm_contri.nro_documento','adm_contri.razon_social')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_com.estado')
            ->leftJoin('configuracion.sis_usua','sis_usua.id_usuario','=','doc_com.registrado_por')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where('doc_com.id_doc_com',$id)
            ->first();

        $doc_det = DB::table('almacen.doc_com_det')
        ->select('doc_com_det.*',
                'guia_com.id_guia',
                DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as nro_guia"),
                'alm_prod.codigo',
                'alm_prod.part_number',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura'
        )
        ->join('almacen.doc_com','doc_com.id_doc_com','=','doc_com_det.id_doc')
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','doc_com_det.id_item')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
        ->where('doc_com_det.id_doc',$id)
        ->get();
        
        $guias = DB::table('almacen.doc_com_det')
        ->select('guia_com.id_guia','tp_ope.descripcion as tipo_operacion',
        DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as nro_guia"),
        'adm_contri.razon_social','guia_com.fecha_emision')
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
        ->where([['doc_com_det.estado','!=',7],
                ['doc_com_det.id_doc','=',$id]])
        ->distinct()
        ->get();

        if (count($guias)>0){
            $ordenes = DB::table('almacen.doc_com_det')
            ->select('log_ord_compra.id_orden_compra','log_ord_compra.codigo',
            'adm_contri.razon_social','log_ord_compra.fecha')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
            ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['doc_com_det.estado','!=',7],
                    ['doc_com_det.id_doc','=',$id]])
            ->distinct()
            ->get();
        } else {
            $ordenes = [];
        }
         
        return response()->json(['doc'=>$doc,'detalle'=>$doc_det,'guias'=>$guias,'ordenes'=>$ordenes]);
    }


 

    // public function listar_guias_prov($id_proveedor){
    //     $data = DB::table('almacen.guia_com')
    //         ->select('guia_com.*',DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as nro_guia"),
    //         'adm_contri.razon_social','adm_estado_doc.estado_doc')
    //         ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
    //         ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
    //         ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
    //         ->leftjoin('almacen.doc_com_guia','doc_com_guia.id_guia_com','=','guia_com.id_guia')
    //         ->where([['guia_com.id_proveedor','=',$id_proveedor],
    //                  ['doc_com_guia.id_guia_com','=',null]])
    //         // ->orWhere('doc_com_guia.estado',2)
    //         ->get();
    //     return response()->json($data);
    // }

    public function guardar_doc_items_guia($id_guia, $id_doc){
        $fecha = date('Y-m-d H:i:s');
        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','log_valorizacion_cotizacion.precio_cotizado as precio')//jalar el precio de la oc o cotizacion
            ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([['guia_com_det.id_guia_com','=',$id_guia],
                    ['guia_com_det.estado','=',1 ]])
            ->get();
        $nuevo_detalle = [];
        $cant = 0;
    
        foreach ($detalle as $det){
            $exist = false;
            foreach ($nuevo_detalle as $nue => $value){
                if ($det->id_producto == $value['id_producto'] && $det->id_guia_com == $value['id_guia_com']){
                    $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
                    $nuevo_detalle[$nue]['unitario'] = floatval($value['unitario']) + floatval($det->unitario);
                    $nuevo_detalle[$nue]['total'] = floatval($value['total']) + floatval($det->total);
                    $exist = true;
                }
            }
            if ($exist === false){
                $nuevo = [
                    'id_guia_com_det' => $det->id_guia_com_det,
                    'id_guia_com' => $det->id_guia_com,
                    'id_producto' => $det->id_producto,
                    'id_unid_med' => $det->id_unid_med,
                    'cantidad' => floatval($det->cantidad),
                    'unitario' => floatval($det->precio),
                    'total' => (floatval($det->cantidad) * floatval($det->precio))
                    ];
                array_push($nuevo_detalle, $nuevo);
            }
        }
        foreach($nuevo_detalle as $det){
            $item = DB::table('almacen.alm_item')
                ->where('id_producto',$det['id_producto'])
                ->first();

            $id_det = DB::table('almacen.doc_com_det')->insert(
                [
                    'id_doc'=>$id_doc,
                    'id_item'=>$item->id_item,
                    'cantidad'=>$det['cantidad'],
                    'id_unid_med'=>$det['id_unid_med'],
                    'precio_unitario'=>$det['unitario'],
                    'sub_total'=>$det['total'],
                    'porcen_dscto'=>0,
                    'total_dscto'=>0,
                    'precio_total'=>$det['total'],
                    'id_guia_com_det'=>$det['id_guia_com_det'],
                    'estado'=>1,
                    'fecha_registro'=>$fecha
                ]);
        }
        $guia = DB::table('almacen.doc_com_guia')->insert(
            [
                'id_doc_com'=>$id_doc,
                'id_guia_com'=>$id_guia,
                'estado'=>1,
                'fecha_registro'=>$fecha
            ]);
        $ingreso = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_com',$id_guia)
            ->first();

        if (isset($ingreso->id_mov_alm)){
            DB::table('almacen.mov_alm')
                ->where('id_mov_alm',$ingreso->id_mov_alm)
                ->update(['id_doc_com'=>$id_doc]);
        }

        return response()->json($guia);
    }

    public function guardar_doc_com_det_orden(Request $request, $id_doc){
        $status =0;
        $fecha = date('Y-m-d H:i:s');
        $header_id_orden_compra = $request->header['id_orden_compra'];
        $header_codigo_orden = $request->header['codigo_orden'];
        $header_id_proveedor = $request->header['id_proveedor'];

        foreach($request->detalle_orden as $data){
            $doc_com_det = DB::table('almacen.doc_com_det')->insertGetId(
                [
                    'id_doc'  => $id_doc,
                    'id_item'  => $data['id_item'],
                    'cantidad'  => $data['cantidad_cotizada'],
                    'id_unid_med'  => $data['id_unidad_medida'],
                    'precio_unitario'  => $data['precio_cotizado'],
                    'sub_total'  => $data['subtotal'],
                    'porcen_dscto'  => $data['porcentaje_descuento'],
                    'total_dscto'  => $data['monto_descuento'],
                    'precio_total'  => $data['subtotal'],
                    // 'id_guia_com_det'  => $data[''],
                    'estado'  => 1,
                    'fecha_registro'  => $fecha,
                    // 'obs'  => '',
                    'id_detalle_orden'  => $data['id_detalle_requerimiento']
                ],
                'id_doc_det'
            );
        }
        if($doc_com_det > 0){
            $status = 200;
        }

        return response()->json($status);

    }

    public function listar_doc_com_orden($id_doc){
        $status=0;
        $doc_com_doc_com_det=[];
        $ordenes=[];
        $doc_com = DB::table('almacen.doc_com')
        ->select(
            'doc_com.*'
        )
        ->where([
            ['doc_com.id_doc_com','=',$id_doc],
            ['doc_com.estado','=',1]
            ])
        ->get();

        $doc_com_det = DB::table('almacen.doc_com_det')
        ->select('doc_com_det.*','alm_prod.codigo','alm_prod.descripcion',
        DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as nro_guia"),
        'alm_und_medida.abreviatura',
        'log_ord_compra.codigo as codigo_orden'
        )
        ->join('almacen.alm_item','alm_item.id_item','=','doc_com_det.id_item')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
        ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','doc_com_det.id_detalle_orden')
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->where([['doc_com_det.id_doc','=',$id_doc],['doc_com_det.estado','=',1]])
        ->get();

        foreach($doc_com as $data){
            $doc_com_doc_com_det[]=[
                'id_doc_com'=>$data->id_doc_com,
                'serie'=>$data->serie,
                'numero'=>$data->numero,
                'id_tp_doc'=>$data->id_tp_doc,
                'id_proveedor'=>$data->id_proveedor,
                'fecha_emision'=>$data->fecha_emision,
                'fecha_vcmto'=>$data->fecha_vcmto,
                'id_condicion'=>$data->id_condicion,
                'moneda'=>$data->moneda,
                'tipo_cambio'=>$data->tipo_cambio,
                'sub_total'=>$data->sub_total,
                'total_descuento'=>$data->total_descuento,
                'porcen_descuento'=>$data->porcen_dscto,
                'total'=>$data->total,
                'total_igv'=>$data->total_igv,
                'total_ant_igv'=>$data->total_ant_igv,
                'total_a_pagar'=>$data->total_a_pagar,
                'usuario'=>$data->usuario,
                'estado'=>$data->estado,
                'fecha_registro'=>$data->fecha_registro,
                'credito_dias'=>$data->credito_dias,
                'porcen_igv'=>$data->porcen_igv,
                'porcen_anticipo'=>$data->porcen_anticipo,
                'total_otros'=>$data->total_otros,
                'registrado_por'=>$data->registrado_por,
                'id_sede'=>$data->id_sede,
                'doc_com_det'=>$doc_com_det
            ];
        }

        //listar y almacenar en una array todo los id_detalle_orden para obtener la cabecera de la orden
        $id_det_orden_list=[];
        foreach($doc_com_det as $data){
            if($data->id_detalle_orden != null){
                $id_det_orden_list[]= $data->id_detalle_orden;
            }
        }
        if(count($id_det_orden_list) > 0 ){
            $ordenes=$this->getOrdenByDetOrden($id_det_orden_list);
        }

        if(count($ordenes)> 0){
            $status = 200;
        }

        $output=[
                'doc_com_doc_com_det'=>$doc_com_doc_com_det,
                'ordenes'=>$ordenes,
                'status'=>$status
        ];
        return response()->json($output);
    }

    
   public function getOrdenByDetOrden($id_det_orden_list){
    $ord = DB::table('logistica.log_det_ord_compra')
    ->select(
        'log_ord_compra.id_orden_compra',
        'log_ord_compra.codigo',
        'log_ord_compra.fecha',
        'log_ord_compra.id_proveedor',
        'log_ord_compra.id_sede',
        'adm_contri.id_contribuyente',
        'adm_contri.razon_social',
        'adm_contri.nro_documento',
        'adm_tp_docum.descripcion AS tipo_documento'

    )
    ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
    ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    ->Join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')


    ->whereIn('log_det_ord_compra.id_detalle_orden',[$id_det_orden_list])
    ->get();
    return $ord;
}

public function mostrar_doc_detalle($id_doc_det){
    $data = DB::table('almacen.doc_com_det')
        ->select('doc_com_det.*','alm_prod.codigo','alm_prod.descripcion',
        DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as nro_guia"),
        'alm_und_medida.abreviatura')
        ->join('almacen.alm_item','alm_item.id_item','=','doc_com_det.id_item')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
        ->where([['doc_com_det.id_doc_det','=',$id_doc_det]])
        ->first();
    return response()->json($data);
}

public function actualiza_totales_doc($por_dscto, $id_doc, $fecha_emision){
    $detalle = DB::table('almacen.doc_com_det')
    ->select(DB::raw('sum(doc_com_det.precio_total) as sub_total'))
    ->where([['id_doc','=',$id_doc],['estado','=',1]])
    ->first();

    //obtiene IGV
    $impuesto = DB::table('contabilidad.cont_impuesto')
        ->where([['codigo','=','IGV'],['fecha_inicio','<',$fecha_emision]])
        ->orderBy('fecha_inicio','desc')
        ->first();

    $dscto = $por_dscto * $detalle->sub_total / 100;
    $total = $detalle->sub_total - $dscto;
    $igv = $impuesto->porcentaje * $total / 100;

    //actualiza totales
    $data = DB::table('almacen.doc_com')->where('id_doc_com',$id_doc)
    ->update([
        'sub_total'=>$detalle->sub_total,
        'total_descuento'=>$dscto,
        'porcen_descuento'=>$porcen_dscto,
        'total'=>$total,
        'total_igv'=>$igv,
        'total_ant_igv'=>0,
        'porcen_igv' => $impuesto->porcentaje,
        'porcen_anticipo' => 0,
        'total_otros' => 0,
        'total_a_pagar'=>($total + $igv)
    ]);
    return response()->json($data);
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

public function listar_ordenes_sin_comprobante($id_proveedor){
    $estado_elaborado= $this->get_estado_doc('Elaborado');

    $data = DB::table('logistica.log_ord_compra')
    ->select(
        'log_ord_compra.*',
        'adm_estado_doc.estado_doc as des_estado',
        'adm_contri.id_contribuyente',
        'adm_contri.razon_social',
        'adm_contri.nro_documento'
    )
    ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_ord_compra.estado')
    ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
    ->where([
            ['log_ord_compra.estado', '=', $estado_elaborado],
            ['log_ord_compra.id_proveedor','=',$id_proveedor],
            ['log_ord_compra.id_tp_documento','=',2]])
    ->orderBy('log_ord_compra.fecha','desc')
    ->get();
$output['data'] = $data;
return response()->json($output);
}

public function listar_detalle_guia_compra($id_guia)
{
    $cabecera = DB::table('almacen.guia_com')
    ->select('guia_com.*','tp_ope.descripcion as tipo_operacion','adm_contri.razon_social')
    ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
    ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
    ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
    ->where([['guia_com.id_guia','=',$id_guia],['guia_com.estado','!=',7]])
    ->get();

    $detalle = DB::table('almacen.guia_com_det')
    ->select('guia_com_det.*',
    'guia_com.id_guia',
    DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as nro_guia"),

    'alm_item.id_item',
    'alm_prod.codigo',
    'alm_prod.descripcion',
    'alm_und_medida.descripcion as unidad_medida'
    )//cambiar a precio_sin_igv
    ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
    ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
    ->leftjoin('almacen.alm_item','alm_item.id_producto','=','alm_prod.id_producto')
    ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')

    // ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
    // ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
    ->where([['guia_com_det.id_guia_com','=',$id_guia],
            ['guia_com_det.estado','=',1]])->get()->toArray();
    $output= ['guia'=>$cabecera, 'guia_detalle'=>$detalle];

    return response()->json($output);
}


function view_lista_comprobantes_compra(){
    $proveedores = $this->mostrar_proveedores_cbo();
    $clasificaciones = $this->mostrar_guia_clas_cbo();
    $condiciones = $this->mostrar_condiciones_cbo();
    $tp_doc = $this->mostrar_tp_doc_cbo();
    $moneda = $this->mostrar_moneda_cbo();
    $detracciones = $this->mostrar_detracciones_cbo();
    $impuestos = $this->mostrar_impuestos_cbo();
    $usuarios = $this->select_usuarios();
    $tp_contribuyente = $this->tp_contribuyente_cbo();
    $sis_identidad = $this->sis_identidad_cbo();
    return view('logistica/comprobantes/lista_comprobantes_compra', compact('proveedores','clasificaciones','condiciones','tp_doc','moneda','detracciones','impuestos','usuarios','tp_contribuyente','sis_identidad'));
}

    public function documentoAPago($id)
    {
        $doc = DB::table('almacen.doc_com')
        ->where('id_doc_com',$id)
        ->update(['estado'=>8]);

        return response()->json($doc);
    }

}