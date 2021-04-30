<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function __construct(){
        // session_start();
    }

    function view_saldos(){
        $almacenes = $this->mostrar_almacenes_cbo();
        return view('almacen/reportes/saldos', compact('almacenes'));
    }

    function view_kardex_series(){
        return view('almacen/reportes/kardex_series');
    }

    public function mostrar_almacenes_cbo(){
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen','alm_almacen.codigo','alm_almacen.descripcion')
            ->where([['alm_almacen.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }

    public function listar_saldos($almacen)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.*','alm_prod.codigo','alm_prod.descripcion',
            // 'alm_ubi_posicion.codigo as cod_posicion','alm_prod.codigo_anexo',
            'alm_und_medida.abreviatura','alm_prod.part_number','sis_moneda.simbolo',
            // 'alm_cat_prod.descripcion as des_categoria',
            // 'alm_subcat.descripcion as des_subcategoria','alm_clasif.descripcion as des_clasificacion',
            // 'alm_prod_antiguo.cod_antiguo','alm_item.id_item',
            'alm_prod.id_moneda','alm_prod.id_unidad_medida',
            DB::raw("(SELECT SUM(alm_det_req.cantidad) FROM almacen.alm_det_req 
            WHERE ( alm_det_req.estado=19 or alm_det_req.estado=28 or alm_det_req.estado=27
                    or alm_det_req.estado=22)
            AND alm_det_req.id_producto=alm_prod_ubi.id_producto 
            AND alm_det_req.id_almacen_reserva=alm_prod_ubi.id_almacen ) as cantidad_reserva"),
            'alm_almacen.descripcion as almacen_descripcion')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            // ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            // ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_prod_ubi.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_ubi.id_producto')
            // ->join('almacen.alm_item','alm_item.id_producto','=','alm_prod.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_prod.id_moneda')
            // ->leftjoin('almacen.alm_clasif','alm_clasif.id_clasificacion','=','alm_prod.id_clasif')
            // ->leftjoin('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
            // ->leftjoin('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
            // ->leftjoin('almacen.alm_prod_antiguo','alm_prod_antiguo.id_producto','=','alm_prod.id_producto')
            // ->leftjoin('almacen.alm_det_req','alm_det_req.id_producto','=','alm_prod.id_producto')
            ->where([['alm_prod_ubi.estado','=',1],['alm_prod_ubi.id_almacen','=',$almacen]])
            ->get();
        
        $nueva_data = [];
        $fecha = date('Y-m-d');
        $tipo_cambio_compra = $this->tipo_cambio_compra($fecha);

        foreach($data as $d){
            // $saldos = $this->saldo_producto($almacen, $d->id_producto, $fecha);
            // $costo = ($saldos['saldo'] !== 0 ? ($saldos['valorizacion'] / $saldos['saldo']) : 0);

            if ($d->stock !== '0'){

                $soles = 0;
                $dolares = 0;
    
                if ($d->id_moneda == 1){
                    $dolares = $d->valorizacion * $tipo_cambio_compra;
                    $soles = $d->valorizacion;
                } 
                else if ($d->id_moneda == 2){
                    $dolares = $d->valorizacion;
                    $soles = $d->valorizacion / $tipo_cambio_compra;
                }
                else {
                    $soles = $d->valorizacion;
                    $dolares = $d->valorizacion * $tipo_cambio_compra;
                }
                $nuevo = [
                    'id_prod_ubi'=> $d->id_prod_ubi,
                    // 'id_item'=> $d->id_item,
                    'id_producto'=> $d->id_producto,
                    'id_almacen'=> $d->id_almacen,
                    'codigo'=> $d->codigo,
                    // 'codigo_anexo'=> $d->codigo_anexo,
                    'part_number'=> $d->part_number,
                    // 'cod_antiguo'=> $d->cod_antiguo,
                    'descripcion'=> $d->descripcion,
                    'abreviatura'=> $d->abreviatura,
                    'id_unidad_medida'=> $d->id_unidad_medida,
                    'stock'=> $d->stock,
                    'simbolo'=> $d->simbolo,
                    'id_moneda'=> $d->id_moneda,
                    'soles'=> round($soles,4,PHP_ROUND_HALF_UP),
                    'dolares'=> round($dolares,4,PHP_ROUND_HALF_UP),
                    'costo_promedio'=> round($d->costo_promedio,4,PHP_ROUND_HALF_UP),
                    'cantidad_reserva'=> $d->cantidad_reserva,
                    'almacen_descripcion'=> $d->almacen_descripcion,
                    // 'cod_posicion'=> $d->cod_posicion,
                    // 'des_clasificacion'=> $d->des_clasificacion,
                    // 'des_categoria'=> $d->des_categoria,
                    // 'des_subcategoria'=> $d->des_subcategoria,
                ];
                array_push($nueva_data,$nuevo);
            }
        }
        // return response()->json($nueva_data);
        $output['data'] = $nueva_data;
        return response()->json($output);
    }

    public function listar_saldos_todo()
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.*','alm_prod.codigo','alm_prod.descripcion',//'alm_ubi_posicion.codigo as cod_posicion',
            'alm_und_medida.abreviatura','alm_prod.part_number','sis_moneda.simbolo',
            // 'alm_cat_prod.descripcion as des_categoria','alm_prod.codigo_anexo',
            // 'alm_subcat.descripcion as des_subcategoria','alm_clasif.descripcion as des_clasificacion',
            // 'alm_prod_antiguo.cod_antiguo',
            'alm_prod.id_moneda','alm_prod.id_unidad_medida',
            DB::raw("(SELECT SUM(alm_det_req.stock_comprometido) FROM almacen.alm_det_req 
            WHERE ( alm_det_req.estado=19 or alm_det_req.estado=28 or alm_det_req.estado=27
                    or alm_det_req.estado=22)
            AND alm_det_req.id_producto=alm_prod_ubi.id_producto 
            AND alm_det_req.id_almacen_reserva=alm_prod_ubi.id_almacen ) as cantidad_reserva"),
            'alm_almacen.descripcion as almacen_descripcion')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            // ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            // ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_prod_ubi.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_ubi.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_prod.id_moneda')
            // ->leftjoin('almacen.alm_clasif','alm_clasif.id_clasificacion','=','alm_prod.id_clasif')
            // ->leftjoin('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
            // ->leftjoin('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_prod.id_categoria')
            // ->leftjoin('almacen.alm_prod_antiguo','alm_prod_antiguo.id_producto','=','alm_prod.id_producto')
            // ->leftjoin('almacen.alm_det_req','alm_det_req.id_producto','=','alm_prod.id_producto')
            ->where([['alm_prod_ubi.estado','=',1]])
            ->get();
        
        $nueva_data = [];
        // $fecha = date('Y-m-d');
        // $tipo_cambio_compra = $this->tipo_cambio_compra($fecha);

        foreach($data as $d){
            // $saldos = $this->saldo_producto($almacen, $d->id_producto, $fecha);
            // $costo = ($saldos['saldo'] !== 0 ? ($saldos['valorizacion'] / $saldos['saldo']) : 0);

            // $soles = 0;
            // $dolares = 0;

            // if ($d->id_moneda == 1){
            //     $dolares = $d->valorizacion * $tipo_cambio_compra;
            //     $soles = $d->valorizacion;
            // } 
            // else if ($d->id_moneda == 2){
            //     $dolares = $d->valorizacion;
            //     $soles = $d->valorizacion / $tipo_cambio_compra;
            // }
            // else {
            //     $soles = $d->valorizacion;
            //     $dolares = $d->valorizacion * $tipo_cambio_compra;
            // }
            if ($d->stock !== '0'){
                $nuevo = [
                    'id_prod_ubi'=> $d->id_prod_ubi,
                    'id_producto'=> $d->id_producto,
                    'id_almacen'=> $d->id_almacen,
                    'codigo'=> $d->codigo,
                    // 'codigo_anexo'=> $d->codigo_anexo,
                    'part_number'=> $d->part_number,
                    // 'cod_antiguo'=> $d->cod_antiguo,
                    'descripcion'=> $d->descripcion,
                    'abreviatura'=> $d->abreviatura,
                    'id_unidad_medida'=> $d->id_unidad_medida,
                    'stock'=> $d->stock,
                    // 'simbolo'=> $d->simbolo,
                    // 'id_moneda'=> $d->id_moneda,
                    // 'soles'=> round($soles,4,PHP_ROUND_HALF_UP),
                    // 'dolares'=> round($dolares,4,PHP_ROUND_HALF_UP),
                    // 'costo_promedio'=> round($d->costo_promedio,4,PHP_ROUND_HALF_UP),
                    'cantidad_reserva'=> $d->cantidad_reserva,
                    'almacen_descripcion'=> $d->almacen_descripcion,
                    // 'cod_posicion'=> $d->cod_posicion,
                    // 'des_clasificacion'=> $d->des_clasificacion,
                    // 'des_categoria'=> $d->des_categoria,
                    // 'des_subcategoria'=> $d->des_subcategoria,
                ];
                array_push($nueva_data,$nuevo);
            }
        }
        // return response()->json($nueva_data);
        $output['data'] = $nueva_data;
        return response()->json($output);
    }

    public function verRequerimientosReservados($id,$almacen){
        $detalles = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_req.codigo','alm_req.concepto','sis_usua.nombre_corto',
            'alm_almacen.descripcion as almacen_descripcion')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_det_req.id_almacen_reserva')
            ->where([['alm_det_req.id_producto','=',$id],
                     ['alm_det_req.id_almacen_reserva','=',$almacen]])
            ->whereIn('alm_det_req.estado',[19,27,28])
            ->get();
        return response()->json($detalles);
    }

    public function tipo_cambio_compra($fecha){
        $data = DB::table('contabilidad.cont_tp_cambio')
        ->where('cont_tp_cambio.fecha','<=',$fecha)
        ->orderBy('fecha','desc')
        // ->take(1)->get();
        ->first();
        return $data->compra;
    }

    public function listar_serie_productos($serie, $descripcion, $codigo, $part_number){
        $hasWhere = [];
        if ($serie !== 'null'){
            $hasWhere[] = ['alm_prod_serie.serie','like','%'.$serie.'%'];
        }
        if ($descripcion !== 'null'){
            $hasWhere[] = ['alm_prod.descripcion','like','%'.strtoupper($descripcion).'%'];
        }
        if ($codigo !== 'null'){
            $hasWhere[] = ['alm_prod.codigo','like','%'.$codigo.'%'];
        }
        if ($part_number !== 'null'){
            $hasWhere[] = ['alm_prod.part_number','like','%'.$part_number.'%'];
        }
        $data = DB::table('almacen.alm_prod_serie')
        ->select('alm_prod_serie.id_prod_serie','alm_prod_serie.id_prod','alm_prod_serie.serie',
        'alm_prod.descripcion','alm_prod.codigo','alm_prod.part_number')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_serie.id_prod')
        ->where([['alm_prod_serie.estado','=',1],
                 ['alm_prod.estado','=',1]])
        ->where($hasWhere)
        ->distinct()
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_kardex_serie($serie,$id_prod){
        
        $data = DB::table('almacen.alm_prod_serie')
        ->select('alm_prod_serie.*',
        'guia_com.fecha_emision as fecha_guia_com',
        'guia_ven.fecha_emision as fecha_guia_ven',
        'contri_cliente.razon_social as razon_social_cliente',
        'contri_prove.razon_social as razon_social_prove',
        'alm_com.descripcion as almacen_compra',
        'alm_ven.descripcion as almacen_venta',
        'ope_com.descripcion as operacion_compra',
        'ope_ven.descripcion as operacion_venta',
        'responsable_com.nombre_corto as responsable_compra',
        'responsable_ven.nombre_corto as responsable_venta',
        'ingreso.codigo as ingreso_codigo',
        'salida.codigo as salida_codigo',
        DB::raw("(tp_doc_com.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com"),
        DB::raw("(tp_doc_ven.abreviatura) || '-' || (guia_ven.serie) || '-' || (guia_ven.numero) as guia_ven"),
        DB::raw("(cont_tp_doc.abreviatura) || '-' || (doc_com.serie) || '-' || (doc_com.numero) as doc_com"))

        ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','alm_prod_serie.id_guia_ven_det')
        ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
        ->leftjoin('contabilidad.adm_contri as contri_cliente','contri_cliente.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('almacen.tp_doc_almacen as tp_doc_ven','tp_doc_ven.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
        ->leftjoin('almacen.alm_almacen as alm_ven','alm_ven.id_almacen','=','guia_ven.id_almacen')
        ->leftjoin('almacen.tp_ope as ope_ven','ope_ven.id_operacion','=','guia_ven.id_operacion')
        ->leftjoin('configuracion.sis_usua as responsable_ven','responsable_ven.id_usuario','=','guia_ven.usuario')
        ->leftjoin('almacen.mov_alm_det as det_salida','det_salida.id_guia_ven_det','=','alm_prod_serie.id_guia_ven_det')
        ->leftjoin('almacen.mov_alm as salida','salida.id_mov_alm','=','det_salida.id_mov_alm')

        ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','alm_prod_serie.id_guia_com_det')
        ->leftjoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
        ->leftjoin('almacen.tp_ope as ope_com','ope_com.id_operacion','=','guia_com.id_operacion')
        ->leftjoin('configuracion.sis_usua as responsable_com','responsable_com.id_usuario','=','guia_com.usuario')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri as contri_prove','contri_prove.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftjoin('almacen.tp_doc_almacen as tp_doc_com','tp_doc_com.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
        ->leftjoin('almacen.alm_almacen as alm_com','alm_com.id_almacen','=','guia_com.id_almacen')
        ->leftjoin('almacen.doc_com_det','doc_com_det.id_guia_com_det','=','alm_prod_serie.id_guia_com_det')
        ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','doc_com_det.id_doc')
        ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
        ->leftjoin('almacen.mov_alm_det as det_ingreso','det_ingreso.id_guia_com_det','=','alm_prod_serie.id_guia_com_det')
        ->leftjoin('almacen.mov_alm as ingreso','ingreso.id_mov_alm','=','det_ingreso.id_mov_alm')

        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_serie.id_prod')

        ->where([['alm_prod_serie.serie','=',$serie],
                 ['alm_prod_serie.id_prod','=',$id_prod],
                 ['alm_prod.estado','=',1]])
        ->orderBy('guia_com.fecha_emision')
        ->get();
        
        return response()->json($data);
    }

}
