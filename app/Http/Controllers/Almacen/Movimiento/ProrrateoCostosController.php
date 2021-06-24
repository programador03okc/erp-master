<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProrrateoCostosController extends Controller
{
    function view_prorrateo_costos(){
        $tp_prorrateo = $this->select_tp_prorrateo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $monedas = AlmacenController::mostrar_moneda_cbo();
        $sis_identidad = AlmacenController::sis_identidad_cbo();

        return view('almacen/prorrateo/doc_prorrateo', compact('tp_prorrateo','tp_doc','monedas','sis_identidad'));
    }

    public function select_tp_prorrateo(){
        $data = DB::table('almacen.tp_prorrateo')
            ->select('tp_prorrateo.id_tp_prorrateo', 'tp_prorrateo.descripcion')
            ->where('tp_prorrateo.estado', '=', 1)
            ->orderBy('tp_prorrateo.id_tp_prorrateo', 'asc')->get();
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

    public function listar_guias_compra()
    {
        $data = DB::table('almacen.guia_com')
        ->select('guia_com.*','adm_contri.razon_social','tp_ope.descripcion as operacion',
        'alm_almacen.descripcion as almacen_descripcion','mov_alm.codigo')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_com.id_almacen')
        ->leftjoin('almacen.mov_alm','mov_alm.id_guia_com','=','guia_com.id_guia')
            ->where([['guia_com.estado','!=',7]])
            ->orderBy('fecha_emision','desc')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
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

    public function listar_guia_detalle($id){
        $data = DB::table('almacen.guia_com_det')
        ->select('guia_com_det.*','alm_prod.codigo','alm_prod.part_number','alm_prod.descripcion',
        'alm_und_medida.abreviatura','guia_com.serie','guia_com.numero','mov_alm_det.valorizacion')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('almacen.mov_alm_det','mov_alm_det.id_guia_com_det','=','guia_com_det.id_guia_com_det')
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['guia_com_det.id_guia_com', '=', $id],
                ['guia_com_det.estado','=',1]])
        ->get();
        return response()->json($data);
    }

    public function guardarProrrateo(Request $request){

        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;

            $id_prorrateo = DB::table('almacen.guia_com_prorrateo')->insertGetId(
                [
                    'estado' => 1,
                    'registrado_por' => $id_usuario,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                    'id_prorrateo'
                );

            $documentos = json_decode($request->documentos);

            foreach ($documentos as $det) {
                
                $id_doc = DB::table('almacen.doc_com')->insertGetId(
                    [
                        'serie' => $det->serie,
                        'numero' => $det->numero,
                        'id_tp_doc' => $det->id_tp_documento,
                        'id_proveedor' => $det->id_proveedor,
                        'moneda' => $det->id_moneda,
                        'fecha_emision' => $det->fecha_emision,
                        'tipo_cambio' => $det->tipo_cambio,
                        'sub_total' => $det->total,
                        'total_descuento' => 0,
                        'total' => $det->total,
                        'total_igv' => 0,
                        'total_a_pagar' => $det->total,
                        'usuario' => $id_usuario,
                        'registrado_por' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                        'id_doc_com'
                    );

                $data = DB::table('almacen.guia_com_prorrateo_doc')->insertGetId(
                    [
                        'id_prorrateo' => $id_prorrateo,
                        'id_tp_prorrateo' => $det->id_tp_prorrateo,
                        'id_doc_com' => $id_doc,
                        'importe_aplicado' => $det->importe_aplicado,
                        'estado' => 1,
                        'registrado_por' => $id_usuario,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                        'id_prorrateo_doc'
                    );
            }

            $detalles = json_decode($request->guias_detalle);

            foreach ($detalles as $det) {
                $id_prorrateo_det = DB::table('almacen.guia_com_prorrateo_det')->insertGetId(
                    [
                        'id_prorrateo' => $id_prorrateo,
                        'id_guia_com_det' => $det->id_guia_com_det,
                        'importe' => $det->adicional,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                        'id_prorrateo_det'
                    );
            }
            
            DB::commit();
            return response()->json($id_prorrateo);
            
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
        }

    }

    public function mostrar_prorrateos(){
        $prorrateos = DB::table('almacen.guia_com_prorrateo')
            ->select('guia_com_prorrateo.*','sis_usua.nombre_corto')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','guia_com_prorrateo.registrado_por')
            ->where('guia_com_prorrateo.estado',1)
            ->get();
        $data['data'] = $prorrateos;
        return response()->json($data);
    }

    public function mostrar_prorrateo($id_prorrateo){
        $documentos = DB::table('almacen.guia_com_prorrateo_doc')
        ->select('guia_com_prorrateo_doc.*','tp_prorrateo.descripcion','doc_com.serie','doc_com.numero',
        'doc_com.fecha_emision','doc_com.moneda','sis_moneda.simbolo','doc_com.total_a_pagar','doc_com.tipo_cambio',
        'doc_com.id_proveedor','adm_contri.razon_social','doc_com.id_tp_doc')
        ->join('almacen.doc_com','doc_com.id_doc_com','=','guia_com_prorrateo_doc.id_doc_com')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
        ->join('almacen.tp_prorrateo','tp_prorrateo.id_tp_prorrateo','=','guia_com_prorrateo_doc.id_tp_prorrateo')
        ->where('guia_com_prorrateo_doc.estado',1)
        ->get();

        $detalles = DB::table('almacen.guia_com_prorrateo_det')
        ->select('guia_com_prorrateo_det.*','guia_com.serie','guia_com.numero','alm_prod.codigo',
        'alm_prod.part_number','alm_prod.descripcion','alm_und_medida.abreviatura',
        'mov_alm_det.valorizacion','guia_com_det.cantidad')
        ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','guia_com_prorrateo_det.id_guia_com_det')
        ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->join('almacen.mov_alm_det','mov_alm_det.id_guia_com_det','=','guia_com_det.id_guia_com_det')
        ->get();

        return response()->json(['documentos'=>$documentos,'detalles'=>$detalles]);
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

}
