<?php

namespace App\Http\Controllers;

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
    
    public function nextCodigoRequerimiento($tipo_requerimiento){
        $yy = date('y', strtotime("now"));
        $yyyy = date('Y', strtotime("now"));
        $documento = 'R';

        $num = DB::table('almacen.alm_req')
        ->where('id_tipo_requerimiento',$tipo_requerimiento)
        ->whereYear('fecha_registro', '=', $yyyy)
        ->count();

        $identificador='';

        switch ($tipo_requerimiento) {
            case 1:
                # code...
                $identificador= 'C';
            break;
            case 2:
                # code...
                $identificador= 'V';
            break;
            case 3:
                # code...
                $identificador= 'PA';
            break;
            
            default:
                $identificador= '';
                # code...
                break;
        }

        $correlativo = $this->leftZero(4, ($num + 1));
        $codigo = "{$documento}{$identificador}{$yy}{$correlativo}";

        $output = ['data'=>$codigo];
        return $output;

    }
    
    public function requerimientos_pendientes_aprobacion(){
        // $id_usuario = Auth::user()->id_usuario;
        // $nombre_corto = Auth::user()->nombre_corto;
        // $rolActual = Auth::user()->rol;
        // $allRol = Auth::user()->getAllRol();
        // $allGrupo = Auth::user()->getAllGrupo();

        // $estado_elaborado =(new LogisticaController)->get_estado_doc('Elaborado');
        $uso_administracion =(new LogisticaController)->get_tipo_cliente('Uso Administración');
        $compra =(new LogisticaController)->get_tipo_requerimiento('Compra');
        $tipo_documento = 1; // Requerimientos

        $requerimientos = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('almacen.tipo_cliente', 'alm_req.tipo_cliente', '=', 'tipo_cliente.id_tipo_cliente')
            ->leftJoin('almacen.alm_almacen', 'alm_req.id_almacen', '=', 'alm_almacen.id_almacen')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
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
                'adm_grupo.descripcion as descripcion_grupo',
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
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                'adm_area.descripcion AS area_descripcion',
                'alm_req.id_op_com',
                'proy_op_com.codigo as codigo_op_com',
                'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.archivo_adjunto',
                'alm_req.fecha_registro',
                'alm_req.id_sede',
                'alm_req.tipo_cliente as id_tipo_cliente',
                'tipo_cliente.descripcion as descripcion_tipo_cliente',
                'alm_req.id_ubigeo_entrega',
                DB::raw("(ubi_dis.descripcion) || ' ' || (ubi_prov.descripcion) || ' ' || (ubi_dpto.descripcion)  AS name_ubigeo"),
                'alm_req.id_almacen',
                'alm_almacen.descripcion as descripcion_almacen',
                'alm_req.monto',
                'alm_req.fecha_entrega'
            )
            ->where([
                ['alm_req.id_tipo_requerimiento','=',$compra], // compra
                ['alm_req.tipo_cliente','=',$uso_administracion] // uso administracion
                // ['alm_req.estado','=',$estado_elaborado] // elaborado
            ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
        ->get();

        
        $payload=[];
        $operacion_selected=0;
        $flujo_list_selected=[];
        $aprobaciones=[];
        $pendiente_aprobacion=[];
        
        
        foreach($requerimientos as $element){
            
            $id_doc_aprobacion_req = $element->id_doc_aprob;
            $id_grupo_req = $element->id_grupo;
            $id_tipo_requerimiento_req = $element->id_tipo_requerimiento;
            $id_prioridad_req = $element->id_prioridad;
            $estado_req = $element->estado;

            $voboList =(new AprobacionController)->getVoBo($id_doc_aprobacion_req); // todas las vobo

            if($voboList['status']== 200){
                foreach($voboList['data'] as $vobo){ 
                    $aprobaciones[]= $vobo; //lista de aprobaciones
                }
            }
            // return $aprobaciones;

            // ##### obteniendo un array de id_flujos de aprobacion ###
            $id_flujo_array=[];
            foreach($aprobaciones as $aprobacion){
                $id_flujo_array[]= $aprobacion->id_flujo;
            }
            // #####
            // return $aprobaciones;

            // ### seleccionar la operacion que corrresponde el req segun grupo, tipo documento , prioridad
            // $prioridadList=['data'=>[],'status'=>400];
            $operaciones =(new AprobacionController)->get_operacion('Requerimiento',$id_grupo_req,$id_prioridad_req);

            foreach($operaciones['data'] as $operacion){
                if($operacion->id_grupo == $id_grupo_req && $operacion->id_tp_documento == $tipo_documento && $operacion->id_prioridad == $id_prioridad_req){ 
                    $operacion_selected = $operacion->id_operacion;
                    // ### si tiene agun criterio 
                    if($operacion->id_grupo_criterios !=null){ // accion si existe algun criterio
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
                    $flujo_list =(new AprobacionController)->getIdFlujo($operacion_selected);
                    // return $id_flujo_array;

                    $pendiente_aprobacion= [];
                    // return $pendiente_aprobacion;
                    //eliminando flujo ya aprobados
                    foreach ($flujo_list['data'] as $key => $object) {
                        // if ($object->id_flujo == 3) {
                            if (!in_array($object->id_flujo,$id_flujo_array)) {
                                // array_splice($pendiente_aprobacion, $key, 1);
                                $pendiente_aprobacion[]=$object;
                                
                        }
                    }
                // return $pendiente_aprobacion;

                    
                }
            }

            // filtar requerimientos para usuario en sesion 
            $allRol = Auth::user()->getAllRol();
        //    return  $allRol;
            $id_rol_list=[];
            foreach($allRol as $rol){
                $id_rol_list[]= $rol->id_rol; // lista de id_rol del usuario en sesion
            }
            if(count($pendiente_aprobacion)>0){
                if(in_array($pendiente_aprobacion[0]->id_rol, $id_rol_list) == true){
                
                    $payload[]=[
                        'id_requerimiento'=>$element->id_requerimiento,
                        'id_doc_aprob'=> $id_doc_aprobacion_req,
                        'id_tipo_requerimiento'=>$element->id_tipo_requerimiento,
                        'tipo_requerimiento'=>$element->tipo_requerimiento,
                        'id_tipo_cliente'=>$element->id_tipo_cliente,
                        'descripcion_tipo_cliente'=>$element->descripcion_tipo_cliente,
                        'id_prioridad'=>$element->id_prioridad,
                        'descripcion_prioridad'=>$element->descripcion_prioridad,
                        'id_periodo'=>$element->id_periodo,
                        'descripcion_periodo'=>$element->descripcion_periodo,
                        'codigo'=>$element->codigo,
                        'concepto'=>$element->concepto,
                        'id_empresa'=>$element->id_empresa,
                        'razon_social_empresa'=>$element->razon_social_empresa,
                        'codigo_sede_empresa'=>$element->codigo_sede_empresa,
                        'logo_empresa'=>$element->logo_empresa,
                        'id_grupo'=>$element->id_grupo,
                        'descripcion_grupo'=>$element->descripcion_grupo,
                        'descripcion_op_com'=>$element->descripcion_op_com,
                        'fecha_requerimiento'=>$element->fecha_requerimiento,
                        'observacion'=>$element->observacion,
                        'name_ubigeo'=>$element->name_ubigeo,
                        'id_moneda'=>$element->id_moneda,
                        'desrcipcion_moneda'=>$element->desrcipcion_moneda,
                        'monto'=>$element->monto,
                        'fecha_entrega'=>$element->fecha_entrega,
                        'id_usuario'=>$element->id_usuario,
                        'id_rol'=>$element->id_rol,
                        'descripcion_rol'=>$element->descripcion_rol,
                        'usuario'=>$element->usuario,
                        'persona'=>$element->persona,
                        'id_almacen'=>$element->id_almacen,
                        'descripcion_almacen'=>$element->descripcion_almacen,
                        'cantidad_aprobados_total_flujo'=> count($aprobaciones).'/'.count($flujo_list['data']),
                        'aprobaciones'=>$aprobaciones,
                        'pendiente_aprobacion'=>$pendiente_aprobacion,
                        'estado'=>$element->estado,
                        'estado_doc'=>$element->estado_doc
                    ];
                }
            }


            
        }
        $output = ['data'=>$payload];
        return $output;

        //  return DataTables::of($output)
        // ->addColumn('flag',function($output){
        //         $flag = $output['flag'];
        //         return $flag;
        // })
        // ->addColumn('status',function($output){
        //         $status = $output['status'];
        //         return $status;
        // })
        // ->addColumn('action',function($output){
        //         $action = $output['action'];
        //         return $action;
        // })
        // ->rawColumns(['flag','status','action'])
        // ->make(true);
    }
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
                        'fecha_entrega'=>$d->fecha_entrega,
                        'id_producto'=>$item->id_producto,
                        'cod_producto' =>$item->cod_producto?$item->cod_producto:$item->cod_servicio,
                        'des_producto' =>$item->des_producto?$item->des_producto:$item->des_servicio,
                        'unidad_medida_detalle_req' =>$d->unidad_medida_detalle_req?$d->unidad_medida_detalle_req:'',
                        'unidad_medida_item' =>$item->unidad_medida_item?$item->unidad_medida_item:'',
                        'cantidad' =>$d->cantidad,
                        'precio_referencial' =>$d->precio_referencial,
                        'descripcion_almacen' =>$d->descripcion_almacen,
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


    function lista_ordenes_propias($id_empresa){
        $oc_propias = DB::table('mgcp_acuerdo_marco.oc_propias')
        ->select(
            'oc_propias.*',
            'empresas.empresa',
            'acuerdo_marco.descripcion_corta as am',
            'entidades.nombre as entidad',
            'cc.id as id_cc',
            'cc.estado_aprobacion as id_estado_aprobacion_cc',
            'estados_aprobacion.estado as estado_aprobacion_cc'
            )
        ->leftJoin('mgcp_acuerdo_marco.empresas', 'empresas.id', '=', 'oc_propias.id_empresa')
        ->leftJoin('mgcp_acuerdo_marco.entidades', 'entidades.id', '=', 'oc_propias.id_entidad')
        ->leftJoin('mgcp_acuerdo_marco.catalogos', 'catalogos.id', '=', 'oc_propias.id_catalogo')
        ->leftJoin('mgcp_acuerdo_marco.acuerdo_marco', 'acuerdo_marco.id', '=', 'catalogos.id_acuerdo_marco')
        ->leftJoin('mgcp_cuadro_costos.cc', 'cc.id_oportunidad', '=', 'oc_propias.id_oportunidad')
        ->leftJoin('mgcp_cuadro_costos.estados_aprobacion', 'estados_aprobacion.id', '=', 'cc.estado_aprobacion')
        ->orderBy('oc_propias.fecha_publicacion', 'desc')
        ->get();

        return datatables($oc_propias)->toJson();

    }
    
}