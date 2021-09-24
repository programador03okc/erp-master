<?php

namespace App\Http\Controllers;

use App\Models\Comercial\CuadroCosto\CcAmFila;
use App\Models\Comercial\CuadroCosto\CuadroCosto;
use App\Models\Configuracion\Departamento;
use App\Models\Configuracion\Distrito;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use Dompdf\Dompdf;
use PDF;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

 
use DataTables;
use Debugbar;

date_default_timezone_set('America/Lima');

class RequerimientoController extends Controller
{

    public function leftZero($lenght, $number)
    {
        $nLen = strlen($number);
        $zeros = '';
        for ($i = 0; $i < ($lenght - $nLen); $i++) {
            $zeros = $zeros . '0';
        }
        return $zeros . $number;
    }
    
    // public function nextCodigoRequerimiento($tipo_requerimiento){
    //     $yy = date('y', strtotime("now"));
    //     $yyyy = date('Y', strtotime("now"));
    //     $documento = 'R';

    //     $num = DB::table('almacen.alm_req')
    //     ->where('id_tipo_requerimiento',$tipo_requerimiento)
    //     ->whereYear('fecha_registro', '=', $yyyy)
    //     ->count();

    //     $identificador='';

    //     switch ($tipo_requerimiento) {
    //         case 1:
    //             # code...
    //             $identificador= 'M';
    //         break;
    //         case 2:
    //             # code...
    //             $identificador= 'E';
    //         break;
    //         case 3:
    //             # code...
    //             $grupos = Auth::user()->getAllGrupo();
    //             foreach($grupos as $grupo){
    //                 $idGrupoList[]=$grupo->id_grupo;
    //             }
    //             if($idGrupoList[0]== 1){
    //                 $identificador= 'A';
    //             }
    //             if($idGrupoList[0]== 2){
    //                 $identificador= 'C';
    //             }
    //             if($idGrupoList[0]== 3){
    //                 $identificador= 'P';
    //             }
    //         break;
            
    //         default:
    //             $identificador= '';
    //             # code...
    //             break;
    //     }

    //     $correlativo = $this->leftZero(4, ($num + 1));
    //     $codigo = "{$documento}{$identificador}{$yy}{$correlativo}";

    //     $output = ['data'=>$codigo];
    //     return $output;

    // }
    

    public function cargar_almacenes($id_sede){
        $data = DB::table('almacen.alm_almacen')
        ->select('alm_almacen.id_almacen','alm_almacen.id_sede','alm_almacen.codigo','alm_almacen.descripcion',
        'sis_sede.descripcion as sede_descripcion','alm_tp_almacen.descripcion as tp_almacen')
        ->leftjoin('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->join('almacen.alm_tp_almacen','alm_tp_almacen.id_tipo_almacen','=','alm_almacen.id_tipo_almacen')
        ->where([['alm_almacen.estado', '=', 1],
        ['alm_almacen.id_sede','=',$id_sede]])
        ->orderBy('codigo')
        ->get();
        return $data;
    }
    
    public function is_true($val, $return_null=false){
        $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        return ( $boolval===null && !$return_null ? false : $boolval );
    }

    public function detalle_requerimiento( Request $request )
    {
        
        $checkList= $request->data;
        $idReqList=[];

        foreach($checkList as $data){
            if($this->is_true($data['stateCheck']) == true){
                $idReqList[]= $data['id_req'];
            }
        }



        // return $idReqList;
            $det = DB::table('almacen.alm_det_req')
            ->select(
                'alm_det_req.*', 
                'alm_req.codigo as cod_req',
                'alm_req.fecha_entrega',
                'alm_und_medida.abreviatura as unidad_medida_detalle_req',
                'alm_almacen.descripcion as descripcion_almacen'
                
                )
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen_reserva')

            ->whereIn('alm_det_req.id_requerimiento', $idReqList)
            ->get();
        
 
      

        $html = '';
        $i = 1;
        $payload=[];
        foreach ($det as $clave => $d) {
            $item = DB::table('almacen.alm_item')
                ->select(
                    'alm_item.*',
                    'alm_prod.id_producto',
                    'alm_prod.codigo as cod_producto',
                    'alm_prod.descripcion as des_producto',
                    'log_servi.codigo as cod_servicio',
                    'log_servi.descripcion as des_servicio',
                    'alm_und_medida.abreviatura as unidad_medida_item'
                )
                ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
                ->where('id_item', $d->id_item)
                ->first();

            if (isset($item)) { // si existe variable
                
                if ($item->id_producto !== null || is_numeric($item->id_producto) == 1) {
                    $sedeReq = DB::table('almacen.alm_req')
                    ->select(
                        'adm_grupo.id_sede'
                    )
                    ->leftjoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
                    ->where('alm_req.id_requerimiento', $d->id_requerimiento)
                    ->first();
                    $almacenes  = $this->cargar_almacenes($sedeReq->id_sede);

                    $payload[]=[
                        'id_requerimiento'=>$d->id_requerimiento,
                        'id_detalle_requerimiento'=>$d->id_detalle_requerimiento,
                        'id_item'=>$d->id_item,
                        'id_tipo_item'=>$d->id_tipo_item,
                        'cod_req' =>$d->cod_req,
                        'descripcion_adicional'=>$d->descripcion_adicional,
                        'lugar_entrega'=>$d->lugar_entrega,
                        'fecha_entrega'=>$d->fecha_entrega?$d->fecha_entrega:null,
                        'id_producto'=>$item->id_producto,
                        'cod_producto' =>$item->cod_producto?$item->cod_producto:$item->cod_servicio,
                        'des_producto' =>$item->des_producto?$item->des_producto:$item->des_servicio,
                        'unidad_medida_detalle_req' =>$d->unidad_medida_detalle_req?$d->unidad_medida_detalle_req:'',
                        'unidad_medida_item' =>$item->unidad_medida_item?$item->unidad_medida_item:'',
                        'cantidad' =>$d->cantidad,
                        'precio_referencial' =>$d->precio_referencial,
                        'descripcion_almacen' =>$d->descripcion_almacen,
                        'stock_comprometido' =>$d->stock_comprometido,
                        'almacen'=> $almacenes
                    ];
                }
            }else{
                $payload[]=[
                    'id_requerimiento'=>$d->id_requerimiento,
                    'id_detalle_requerimiento'=>$d->id_detalle_requerimiento,
                    'id_item'=>0,
                    'id_tipo_item'=>0,
                    'cod_req' =>$d->cod_req,
                    'descripcion_adicional'=>$d->descripcion_adicional,
                    'lugar_entrega'=>$d->lugar_entrega,
                    'fecha_entrega'=>$d->fecha_entrega,
                    'id_producto'=>0,
                    'cod_producto' =>0,
                    'des_producto' =>'',
                    'unidad_medida_detalle_req' =>$d->unidad_medida_detalle_req?$d->unidad_medida_detalle_req:'',
                    'unidad_medida_item' =>'',
                    'cantidad' =>$d->cantidad,
                    'precio_referencial' =>$d->precio_referencial,
                    'descripcion_almacen' =>$d->descripcion_almacen,
                    'stock_comprometido' =>$d->stock_comprometido,
                    'almacen'=> []
                ];
            }


                //     if($type_view =='VIEW_CHECKBOX'){
                //     $html .= '
                //         <tr>
                //             <td>
                //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>
                //                 <input type="checkbox"/>
                //             </td>
                //             <td>' . $d->cod_req . '</td>
                //             <td>-</td>
                //             <td>' . $item->cod_producto . '</td>
                //             <td>' . $item->des_producto . '</td>
                //             <td>' . $item->abreviatura . '</td>
                //             <td>' . $d->cantidad . '</td>
                //             <td>' . $d->precio_referencial . '</td>
                //             <td> <input type="number" min="0" max="'.$d->cantidad.'" value="'.$d->stock_comprometido .'" class="form-control activation stock_comprometido" data-id-det-req="'.$d->id_detalle_requerimiento.'"  data-id-req="'.$d->id_requerimiento.'"name="stock_comprometido[]" disabled></td>
                //             <td>
                //                 <select class="form-control almacen_selected" name="" data-id-det-req="'.$d->id_detalle_requerimiento.'">';
                //                 foreach($almacenes as $al){
                //                     $html.='<option value="'.$al->id_almacen.'">'.$al->descripcion.'</option>';
                //                 }
                //         $html.='</select>
                //             </td>

                //         </tr>
                //     ';
                //     }else{
                //         $html .= '
                //         <tr>
                //             <td>
                //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>';
                //         $html.= $clave;
                //         $html.='
                //             </td>
                //             <td>' . $d->cod_req . '</td>
                //             <td>' . $item->cod_producto . '</td>
                //             <td>' . $item->des_producto . '</td>
                //             <td>' . $item->abreviatura . '</td>
                //             <td>' . $d->cantidad . '</td>
                //             <td>' . $d->precio_referencial . '</td>
                //             <td>' . $d->stock_comprometido . '</td>
                //         </tr>
                //         ';
                //     }
                // } else if ($item->id_servicio !== null || is_numeric($item->id_servicio) == 1) {
                //     if($type_view =='VIEW_CHECKBOX'){
                //     $html .= '
                //         <tr>
                //             <td>
                //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>';
                //                 '<input type="checkbox"/>
                //             </td>
                //             <td>' . $d->cod_req . '</td>
                //             <td>'.$item->codigo.'</td>
                //             <td>' . $item->cod_servicio . '</td>
                //             <td>' . $item->des_servicio . '</td>
                //             <td>serv</td>
                //             <td>' . $d->cantidad . '</td>
                //             <td>' . $d->precio_referencial . '</td>
                //             <td> <input type="number" min="0" max="'.$d->cantidad.'" value="'.$d->stock_comprometido .'" class="form-control activation stock_comprometido" data-id-det-req="'.$d->id_detalle_requerimiento.'"  data-id-req="'.$d->id_requerimiento.'"name="stock_comprometido[]" disabled></td>

                //         </tr>
                //         ';
                //     }else{
                //         $html .= '
                //         <tr>
                //             <td>
                //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>';
                //         $html.= $clave;
                //         $html.= '
                //             </td>
                //             <td>' . $d->cod_req . '</td>
                //             <td>' . $item->cod_servicio . '</td>
                //             <td>' . $item->des_servicio . '</td>
                //             <td>serv</td>
                //             <td>' . $d->cantidad . '</td>
                //             <td>' . $d->precio_referencial . '</td>
                //             <td>' . $d->stock_comprometido . '</td>

                //         </tr>
                //         ';                        
                // ';
                //         ';                        
                //     }
                // }
            // } else { // si no existe | no existe id_item
            //     if($type_view =='VIEW_CHECKBOX'){
            //         $sedeReq = DB::table('almacen.alm_req')
            //         ->select(
            //             'adm_grupo.id_sede'
            //         )
            //         ->leftjoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            //         ->where('alm_req.id_requerimiento', $d->id_requerimiento)
            //         ->first();
            //         $almacenes  = $this->cargar_almacenes($sedeReq->id_sede);
            //     $html .= '
            //         <tr>
            //             <td>
            //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
            //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>
            //                 <input type="checkbox"/>
            //             </td>
            //             <td>' . $d->cod_req . '</td>
            //             <td>-</td>
            //             <td>-</td>
            //             <td>' . $d->descripcion_adicional . '</td>
            //             <td>' . $d->abreviatura . '</td>
            //             <td>' . $d->cantidad . '</td>
            //             <td>' . $d->precio_referencial . '</td>
            //             <td> <input type="number" min="0" max="'.$d->cantidad.'" value="'.$d->stock_comprometido .'" class="form-control activation stock_comprometido" data-id-det-req="'.$d->id_detalle_requerimiento.'"  data-id-req="'.$d->id_requerimiento.'"name="stock_comprometido[]" disabled></td>
            //             <td>
            //                 <select class="form-control almacen_selected" name="" data-id-det-req="'.$d->id_detalle_requerimiento.'">';
            //                 foreach($almacenes as $al){
            //                     $html.='<option value="'.$al->id_almacen.'">'.$al->descripcion.'</option>';
            //                 }
            //         $html.='</select>
            //             </td>

            //         </tr>
            //     ';
            //     }else{
            //         $html .= '
            //         <tr>
            //             <td>
            //                 <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
            //                 <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>';
            //         $html.= $clave;
            //         $html.='</td>
            //             <td>' . $d->cod_req . '</td>
            //             <td>0</td>
            //             <td>' . $d->descripcion_adicional . '</td>
            //             <td>' . $d->abreviatura . '</td>
            //             <td>' . $d->cantidad . '</td>
            //             <td>' . $d->precio_referencial . '</td>
            //             <td>' . $d->stock_comprometido . '</td>

            //         </tr>
            //     '; 
            //     }


        }
        return json_encode($payload);
    }



    function cuadro_costos($id_cc){
        $cc = DB::table('mgcp_acuerdo_marco.oc_propias')
        ->select(
            'cc.id as id_cc',
            'cc.fecha_creacion as fecha_creacion_cc',
            'cc.tipo_cuadro',
            'oc_propias.id as id_orden_propia',
            'oc_propias.orden_am',
            'oc_propias.id_empresa',
            'empresas.empresa',
            'oc_propias.fecha_estado',
            'oc_propias.lugar_entrega',
            'oc_propias.id_entidad',
            'entidades.nombre as nombre_entidad',
            'entidades.ruc as ruc_entidad',
            'entidades.direccion as direccion_entidad',
            'entidades.ubigeo as ubigeo_entidad',
            'entidades.responsable',
            'entidades.telefono',
            'entidades.cargo',
            'entidades.correo',
            'oc_propias.monto_total',
            'oc_propias.url_oc_fisica',
            DB::raw("('https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra='|| (oc_propias.id) ||'&ImprimirCompleto=1') AS url_oc_electronica"),
            'oc_propias.url_oc_fisica',
            'oc_propias.fecha_entrega',
            'oc_propias.id_oportunidad',
            'oportunidades.codigo_oportunidad',
            'oc_propias.estado_entrega',
            'oc_propias.fecha_publicacion',
            'oc_propias.id_contacto',
            'adm_ctb_contac.id_contribuyente as contact_id_contribuyente',
            'entidades_contact.nombre as contact_nombre_entidad',
            'adm_ctb_contac.nombre as contact_nombre',
            'adm_ctb_contac.telefono as contact_telefono',
            'adm_ctb_contac.email as contact_email',
            'adm_ctb_contac.cargo as contact_cargo',
            'adm_ctb_contac.direccion as contact_direccion',
            'adm_ctb_contac.horario as contact_horario',
            // 'oc_propias.occ',
            // 'oc_propias.despachada',
            'acuerdo_marco.descripcion_corta as am',
            'cc.estado_aprobacion as id_estado_aprobacion_cc',
            'estados_aprobacion.estado as estado_aprobacion_cc'

            )
        ->leftJoin('mgcp_acuerdo_marco.empresas', 'empresas.id', '=', 'oc_propias.id_empresa')
        ->leftJoin('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'oc_propias.id_entidad')
        ->leftJoin('mgcp_acuerdo_marco.catalogos', 'catalogos.id', '=', 'oc_propias.id_catalogo')
        ->leftJoin('mgcp_acuerdo_marco.acuerdo_marco', 'acuerdo_marco.id', '=', 'catalogos.id_acuerdo_marco')
        ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id_oportunidad', '=', 'oc_propias.id_oportunidad')
        ->leftJoin('mgcp_cuadro_costos.estados_aprobacion', 'estados_aprobacion.id', '=', 'cc.estado_aprobacion')
        ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
        ->leftJoin('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_datos_contacto', '=', 'oc_propias.id_contacto')
        ->leftJoin('mgcp_acuerdo_marco.entidades as entidades_contact', 'entidades_contact.id', '=', 'adm_ctb_contac.id_contribuyente')
        ->where('cc.id','=',$id_cc)  
        ->get();
        if(count($cc)>0){
            $status=200;
            $msj='Ok';
            $output=['status'=>$status, 'mensaje'=>$msj,'data'=>$cc->first()];
        }else{
            $status=204;
            $msj='no se encontro data';
            $output=['status'=>$status, 'mensaje'=>$msj,'data'=>[]];
        }
        return response()->json($output);

    }

    function get_detalle_cuadro_costos($id_cc){

        $status =0;
        $msj='';

        $cc = CuadroCosto::select(
            'cc.id as id_cc',

            'cc.tipo_cambio',
            'cc.igv',
            'oc_propias.orden_am',
            'cc.tipo_cuadro',
            'cc.id_oportunidad',
            'oportunidades.id_tipo_negocio',
            'cc.estado_aprobacion as id_estado_aprobacion_cc',
            'estados_aprobacion.estado as estado_aprobacion_cc'
            )

        ->leftJoin('mgcp_cuadro_costos.estados_aprobacion', 'estados_aprobacion.id', '=', 'cc.estado_aprobacion')
        ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
        ->leftJoin('mgcp_acuerdo_marco.oc_propias', 'oc_propias.id_oportunidad', '=', 'oportunidades.id')
        ->where('cc.id','=',$id_cc)  
        ->first();
        
        // $tipo_cuadro=0;
        // if(count($cc)>0){
        //     $tipo_cuadro = $cc->first()->tipo_cuadro;
        // }
        
        // donde esta el id_cc si en cc_am_filas o en cc_venta_filas
        //$count_id_cc_in_cc_am_filas = DB::table('mgcp_cuadro_costos.cc_am_filas')->select('cc_am_filas.*')->where('cc_am_filas.id_cc_am','=',$id_cc)->count();
        //$count_id_cc_in_cc_venta_filas = DB::table('mgcp_cuadro_costos.cc_venta_filas')->select('cc_venta_filas.*')->where('cc_venta_filas.id_cc_venta','=',$id_cc)->count();


        // if($tipo_cuadro>0){
           // if($count_id_cc_in_cc_am_filas > 0){ // acuerdo marco

                $det_cc = CcAmFila::select(
                    'cc.tipo_cambio',
                    'cc.igv',
                    'cc_am_filas.id',
                    'cc_am_filas.id as id_cc_am_filas',
                    'cc_am_filas.id_cc_am',
                    'cc_am_filas.part_no',
                    'cc_am_filas.descripcion',
                    'cc_am_filas.cantidad',
                    'cc_am_filas.pvu_oc',
                    'cc_am_filas.flete_oc',
                    'cc_am_filas.proveedor_seleccionado',
                    'proveedores.razon_social as razon_social_proveedor',
                    'proveedores.ruc as ruc_proveedor',
                    'cc_am_filas.garantia',
                    'origenes_costeo.origen as origen_costo',
                    'cc_am_proveedores.precio as costo_unitario_proveedor',
                    'cc_am_proveedores.moneda as moneda_costo_unitario_proveedor',
                    'cc_am_proveedores.plazo as plazo_proveedor',
                    'cc_am_proveedores.flete as flete_proveedor',
                    'fondos_proveedores.descripcion as fondo_proveedor',
                    'cc_am_filas.id_ultimo_usuario as id_autor',
                    'users.name as nombre_autor',
                    'cc_am_filas.fecha_creacion',
                    'cc_am_filas.part_no_producto_transformado',
                    'cc_am_filas.descripcion_producto_transformado',
                    'cc_am_filas.comentario_producto_transformado'
                    )
                ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'cc_am_filas.id_cc_am')
                ->leftJoin('mgcp_cuadro_costos.estados_aprobacion', 'estados_aprobacion.id', '=', 'cc.estado_aprobacion')
                ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->leftJoin('mgcp_oportunidades.tipos_negocio', 'tipos_negocio.id', '=', 'oportunidades.id_tipo_negocio')
                ->leftJoin('mgcp_cuadro_costos.cc_am_proveedores', 'cc_am_proveedores.id', '=', 'cc_am_filas.proveedor_seleccionado')
                ->leftJoin('mgcp_cuadro_costos.proveedores', 'proveedores.id', '=', 'cc_am_proveedores.id_proveedor')
                ->leftJoin('mgcp_cuadro_costos.fondos_proveedores', 'fondos_proveedores.id', '=', 'cc_am_proveedores.id_fondo_proveedor')
                ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'cc_am_filas.id_ultimo_usuario')
                ->leftJoin('mgcp_cuadro_costos.origenes_costeo', 'origenes_costeo.id', '=', 'cc_am_filas.id_origen_costeo')
                // ->leftJoin('mgcp_cuadro_costos.cc_am_proveedores', 'cc_am_proveedores.id_fila', '=', 'cc_am_filas.id')
 
                ->where('cc_am_filas.id_cc_am','=',$id_cc)  
                ->get();
                $status =200;
                $msj='Ok';
            /*}elseif($count_id_cc_in_cc_venta_filas >0){ // venta
                $det_cc = DB::table('mgcp_cuadro_costos.cc_venta_filas')
                ->select(
                    'cc_venta_filas.id',
                    'cc_am_filas.id as id_cc_venta_filas',
                    'cc_venta_filas.id_cc_venta',
                    'cc_venta_filas.part_no',
                    'cc_venta_filas.descripcion',
                    'cc_venta_filas.cantidad',
                    'cc_venta_filas.pvu_oc',
                    'cc_venta_filas.flete_oc',
                    'cc_venta_filas.proveedor_seleccionado',
                    'proveedores.razon_social as razon_social_proveedor',
                    'proveedores.ruc as ruc_proveedor',
                    'cc_venta_filas.garantia',
                    'cc_venta_filas.creado_por as id_autor',
                    'users.name as nombre_autor',
                    'cc_venta_filas.fecha_creacion'
                    )
                ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'cc_venta_filas.id_cc_venta')
                ->leftJoin('mgcp_cuadro_costos.estados_aprobacion', 'estados_aprobacion.id', '=', 'cc.estado_aprobacion')
                ->leftJoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->leftJoin('mgcp_oportunidades.tipos_negocio', 'tipos_negocio.id', '=', 'oportunidades.id_tipo_negocio')
                ->leftJoin('mgcp_cuadro_costos.cc_venta_proveedor', 'cc_venta_proveedor.id', '=', 'cc_venta_filas.proveedor_seleccionado')
                ->leftJoin('mgcp_cuadro_costos.proveedores', 'proveedores.id', '=', 'cc_venta_filas.proveedor_seleccionado')
                ->leftJoin('mgcp_usuarios.users', 'users.id', '=', 'cc_venta_filas.creado_por')
                ->where('cc_venta_filas.id_cc_venta','=',$id_cc)  
                ->get();
                $status =200;
                $msj='Ok';*/
            /*}else{
                $status =204;
                $msj='el tipo de negocio no esta comprendido en la consulta.';
            }*/
        // }
        $output=['status'=>$status, 'mensaje'=>$msj, 'head'=>$cc?$cc:[], 'detalle'=>$det_cc?$det_cc:[]];
        return $output;
        

    }

    function detalle_cuadro_costos($id_cc){
        $output= $this->get_detalle_cuadro_costos($id_cc);
        return response()->json($output);

    }

 


    function getIdProvincia($nombre){
        $data = DB::table('configuracion.ubi_prov')
        ->select('ubi_prov.*')
        ->where([
            ['ubi_prov.descripcion', '=', $nombre]
            ])
        ->first();
        return ($data!==null ? $data->id_prov : 0);
    }

    

    function obtenerConstruirCliente(Request $request){
        $status=0;
        $msj=[];

        $razon_social=$request->razon_social;
        $ruc=$request->ruc;
        $telefono=$request->telefono;
        $direccion=$request->direccion;
        $correo=$request->correo;
        $ubigeo=$request->ubigeo;
        $cliente=[];
        $fechaHoy = date('Y-m-d H:i:s');

        //decodificar ubigeo
        $id_ubigeo_cliente=null;
        $descripcion_ubigeo_cliente=null;
        if(isset($ubigeo) && $ubigeo !=null){
            $IdDis=null;
            $ubigeo_list = array_filter(array_map('trim',explode("/", $ubigeo)));
            if(count($ubigeo_list)==3){
                $IdDis=  Distrito::getIdDistrito($ubigeo_list[0]);
                // $IdProv=  $this->getIdProvincia($ubigeo_list[1]);
                $IdDpto=  Departamento::getIdDepartamento($ubigeo_list[2]);

                if($IdDis==0 || $IdDpto == 0){
                    $IdDis=  Distrito::getIdDistrito($ubigeo_list[2]);
                    // $IdProv=  $this->getIdProvincia($ubigeo_list[1]);
                    $IdDpto=  Departamento::getIdDepartamento($ubigeo_list[0]);
                }
            }
 

            $id_ubigeo_cliente= $IdDis;
            $descripcion_ubigeo_cliente= $ubigeo_list[0].'/'.$ubigeo_list[1].'/'.$ubigeo_list[2];
        }
        //  

        $adm_contri = DB::table('contabilidad.adm_contri')
        ->select(
            'adm_contri.*',
            )
        ->when(($ruc !='null'), function($query) use ($ruc)  {
            return $query->Where('adm_contri.nro_documento','=',$ruc);
        })
        ->when(($razon_social !='null'), function($query) use ($razon_social)  {
            return $query->Where('adm_contri.razon_social','=',$razon_social);
        })
        ->get();
        

        $id_contribuyente=null;
        if(count($adm_contri)>0){
            $id_contribuyente= $adm_contri->first()->id_contribuyente;

            $com_cliente = DB::table('comercial.com_cliente')
            ->select(
                'com_cliente.*'
                )
            ->where([
                ['com_cliente.id_contribuyente','=',$id_contribuyente]
                ])
            ->orderBy('com_cliente.fecha_registro')
            ->get();
            $msj[]='Contribuyente encontrado';

            if(count($com_cliente)>0){

                $cliente =[
                    'id_cliente'=>$com_cliente->first()->id_cliente,
                    'razon_social'=>$adm_contri->first()->razon_social,
                    'ruc'=>$adm_contri->first()->nro_documento,
                    'telefono'=>$adm_contri->first()->telefono,
                    'direccion'=>$adm_contri->first()->direccion_fiscal,
                    'correo'=>$adm_contri->first()->email,
                    'id_ubigeo'=>$id_ubigeo_cliente,
                    'descripcion_ubigeo'=>$descripcion_ubigeo_cliente
                ];

                if($adm_contri->first()->direccion_fiscal == null || $adm_contri->first()->direccion_fiscal ==''){
                    DB::table('contabilidad.adm_contri')
                    ->where('id_contribuyente', $id_contribuyente)
                    ->update(['direccion_fiscal' => $direccion?$direccion:null]);
                    $cliente[0]['direccion']=$direccion;

                    DB::table('contabilidad.adm_contri')
                    ->where('id_contribuyente', $id_contribuyente)
                    ->update(['ubigeo' => $id_ubigeo_cliente?$id_ubigeo_cliente:null]); 
                    $cliente[0]['id_ubigeo']=$id_ubigeo_cliente;
                }
                if($adm_contri->first()->telefono == null || $adm_contri->first()->telefono ==''){
                    DB::table('contabilidad.adm_contri')
                    ->where('id_contribuyente', $id_contribuyente)
                    ->update(['telefono' => $telefono?$telefono:null]); 
                    $cliente[0]['telefono']=$telefono;

                }
                if($adm_contri->first()->email == null || $adm_contri->first()->email ==''){
                    DB::table('contabilidad.adm_contri')
                    ->where('id_contribuyente', $id_contribuyente)
                    ->update(['email' => $correo?$correo:null]); 
                    $cliente[0]['correo']=$correo;

                }

                $msj[]=' Cliente encontrado';
                $status=200;

            }else{ // se encontro contribuyente pero no registrado como cliente => crear cliente
                
                $id_cliente = DB::table('comercial.com_cliente')->insertGetId(
                    [
                        'id_contribuyente' => $id_contribuyente,
                        'codigo' => null,
                        'estado' =>1,
                        'fecha_registro' => $fechaHoy
                    ],
                        'id_cliente'
                    );

                    if($id_cliente>0){
                        $msj[]=' Cliente creado';
                        $cliente =[
                            'id_cliente'=>$id_cliente,
                            'razon_social'=>$adm_contri->first()->razon_social,
                            'ruc'=>$adm_contri->first()->nro_documento,
                            'telefono'=>$adm_contri->first()->telefono,
                            'direccion'=>$adm_contri->first()->direccion_fiscal,
                            'correo'=>$adm_contri->first()->email,
                            'id_ubigeo'=>$id_ubigeo_cliente,
                            'descripcion_ubigeo'=>$descripcion_ubigeo_cliente

                        ];
                        $status=200;
                    }else{
                        $msj[]=' hubo un problema al crear el cliente en base a un contribuyente';
                        $status=204;
                    }
            }

        }else{ // no se encontro el contribuyente, se debe crear contribuyente y cliente
            

            $id_contribuyente = DB::table('contabilidad.adm_contri')->insertGetId(
                [
                    'razon_social' => $razon_social?$razon_social:null,
                    'nro_documento' => $ruc?$ruc:null,
                    'telefono' => $telefono?$telefono:null,
                    'direccion_fiscal' => $direccion?$direccion:null,
                    'email' => $correo?$correo:null,
                    'estado' => 1,
                    'fecha_registro' => $fechaHoy,
                    'transportista' => false
                ],
                    'id_contribuyente'
                );

            $id_cliente = DB::table('comercial.com_cliente')->insertGetId(
                [
                    'id_contribuyente' => $id_contribuyente,
                    'codigo' => null,
                    'estado' =>1,
                    'fecha_registro' => $fechaHoy
                ],
                    'id_cliente'
                );

                $cliente =[
                    'id_cliente'=>$id_cliente,
                    'razon_social'=>$razon_social,
                    'ruc'=>$ruc,
                    'telefono'=>$telefono,
                    'direccion'=>$direccion,
                    'correo'=>$correo,
                    'id_ubigeo'=>$id_ubigeo_cliente,
                    'descripcion_ubigeo'=>$descripcion_ubigeo_cliente

                ];

                if($id_contribuyente >0 && $id_cliente >0){
                    $status=200;
                    $msj[]='Se creo un nuevo cliente';
                }else{
                    $status=204;
                    $msj[]='hubo un problema al crear un nuevo cliente';
                }
        }

        

        

        $output=['status'=>$status, 'mensaje'=>$msj, 'data'=>$cliente];
        return response()->json($output);
    }


    public function buscarStockEnAlmacenes($id_item){
       
    
        $msj = '';
        $status = 0;
        $data = [];

        if ($id_item > 0){
            $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen','alm_almacen.codigo','alm_almacen.descripcion')
            ->where([['alm_almacen.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();

        } else {
            $msj = 'No es posible guardar. Ya existe una subcategoria con dicha descripción';
            $status= 204;
        }
        $output=['status'=>$status,'msj'=>$msj,'data'=>$data];
        return response()->json($output);
    }

}