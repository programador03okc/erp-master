<?php

namespace App\Http\Controllers\Migraciones;

use App\Helpers\StringHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MigrateProductoSoftlinkController extends Controller
{
    public function obtenerProductoSoftlink($id_producto)
    {
        try {
            DB::beginTransaction();
            $producto = DB::table('almacen.alm_prod')
                ->select(
                    'alm_prod.*',
                    'alm_und_medida.abreviatura',
                    'alm_cat_prod.descripcion as categoria',
                    'alm_subcat.descripcion as subcategoria',
                    'alm_clasif.descripcion as clasificacion',
                    // 'alm_tp_prod.descripcion as tipo_descripcion',
                    // 'alm_tp_prod.id_tipo_producto',
                    'alm_tp_prod.id_clasificacion',
                    // 'sis_usua.nombre_corto',
                    // 'adm_estado_doc.estado_doc',
                    // 'adm_estado_doc.bootstrap_color',
                )
                ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
                ->leftjoin('almacen.alm_tp_prod', 'alm_tp_prod.id_tipo_producto', '=', 'alm_cat_prod.id_tipo_producto')
                ->leftjoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_tp_prod.id_clasificacion')
                ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                // ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_prod.id_usuario')
                // ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_prod.estado')
                ->where([['alm_prod.id_producto', '=', $id_producto]])
                ->first();
            //Verifica si esxiste el producto
            $prod = null;
            if (!empty($producto->part_number)) { //if ($producto->part_number !== null && $producto->part_number !== '') {
                // return [$producto];exit;
                $prod = DB::connection('soft')->table('sopprod')
                    ->select('cod_prod')
                    ->join('sopsub2', 'sopsub2.cod_sub2', '=', 'sopprod.cod_subc')
                    ->where([
                        ['sopprod.cod_espe', '=', trim($producto->part_number)],
                        ['sopsub2.nom_sub2', '=', $producto->subcategoria]
                    ])
                    ->first();

            } else if ($producto->descripcion !== null && $producto->descripcion !== '') {
                $prod = DB::connection('soft')->table('sopprod')
                    ->select('cod_prod')
                    ->join('sopsub2', 'sopsub2.cod_sub2', '=', 'sopprod.cod_subc')
                    ->where([
                        ['nom_prod', '=', trim($producto->descripcion)],
                        ['sopsub2.nom_sub2', '=', $producto->subcategoria]
                    ])
                    ->first();
            }

            $cod_prod = null;
            //Si existe copia el cod_prod
            if ($prod !== null) {
                $cod_prod = $prod->cod_prod;
                $cod_clasi = $this->obtenerClasificacion($producto->clasificacion);
                $cod_cate = $this->obtenerCategoria($producto->categoria, $producto->id_categoria);
                // return $cod_cate;exit;
                DB::connection('soft')
                ->table('sopprod')
                ->where('cod_prod',$cod_prod)
                ->update(
                    ['cod_clasi' =>$cod_clasi,
                    'cod_cate' =>$cod_cate]
                );
            } //Si no existe, genera el producto
            else {
                //obtiene el sgte codigo
                $ultimo = DB::connection('soft')->table('sopprod')
                    ->select('cod_prod')
                    ->where([['cod_prod', '!=', 'TEXTO']])
                    ->orderBy('cod_prod', 'desc')
                    ->first();

                $cod_prod = StringHelper::leftZero(6, (intval($ultimo->cod_prod) + 1));

                $cod_clasi = $this->obtenerClasificacion($producto->clasificacion);

                $cod_cate = $this->obtenerCategoria($producto->categoria, $producto->id_categoria);

                $cod_subc = $this->obtenerSubCategoria($producto->subcategoria, $producto->id_subcategoria);

                $cod_unid = $this->obtenerUnidadMedida($producto->abreviatura);

                DB::connection('soft')->table('sopprod')->insert(
                    [
                        'cod_prod' => $cod_prod,
                        'cod_clasi' => $cod_clasi,
                        'cod_cate' => $cod_cate,
                        'cod_subc' => $cod_subc,
                        'cod_prov' => '',
                        'cod_espe' => trim($producto->part_number),
                        'cod_sunat' => '',
                        'nom_prod' => trim($producto->descripcion),
                        'cod_unid' => $cod_unid,
                        'nom_unid' => trim($producto->abreviatura),
                        'fac_unid' => 1,
                        'kardoc_costo' => 0,
                        'kardoc_stock' => 0,
                        'kardoc_ultingfec' => '0000-00-00',
                        'kardoc_ultingcan' => 0,
                        'kardoc_unico' => '',
                        'fec_ingre' => date('Y-m-d'),
                        'flg_descargo' => 1,
                        'tip_moneda' => $producto->id_moneda,
                        'flg_serie' => ($producto->series ? 1 : 0), //Revisar
                        'txt_observa' => ($producto->notas !== null ? $producto->notas : ''),
                        'flg_afecto' => 1,
                        'flg_suspen' => 0,
                        'apl_lista' => 3,
                        'foto' => '',
                        'aweb' => '',
                        'bi_c' => '',
                        'impto1_c' => '',
                        'impto2_c' => '',
                        'impto3_c' => '',
                        'dscto_c' => '',
                        'bi_v' => '',
                        'impto1_v' => '',
                        'impto2_v' => '',
                        'impto3_v' => '',
                        'dscto_v' => '',
                        'cta_s_caja' => 0,
                        'cta_d_caja' => '',
                        'cod_ubic' => '',
                        'peso' => 0,
                        'flg_percep' => 0,
                        'por_percep' => 0,
                        'gasto' => 0,
                        'dsctocompra' => 0,
                        'dsctocompra2' => 0,
                        'cod_promo' => '',
                        'can_promo' => 0,
                        'ult_edicion' => date('Y-m-d H:i:s'),
                        'ptosbonus' => 0,
                        'bonus_moneda' => 0,
                        'bonus_importe' => 0,
                        'flg_detrac' => 0,
                        'por_detrac' => 0,
                        'cod_detrac' => '',
                        'mon_detrac' => 0,
                        'largo' => 0,
                        'ancho' => 0,
                        'area' => 0,
                        'aweb' => 0,
                        'id_product' => 0,
                        'width' => 0,
                        'height' => 0,
                        'depth' => 0,
                        'weight' => 0,
                        'costo_adicional' => 0
                    ]
                );

                $sucursales = DB::connection('soft')->table('sucursal')->get();

                foreach ($sucursales as $suc) {
                    $prod = DB::connection('soft')->table('precios')
                        ->where([['cod_prod', '=', $cod_prod], ['cod_suc', '=', $suc->cod_suc]])
                        ->first();

                    if ($prod == null) {
                        DB::connection('soft')->table('precios')->insert(
                            [
                                'cod_prod' => $cod_prod,
                                'cod_suc' => $suc->cod_suc,
                                'en_lista' => 1,
                                'lsupendido' => 0,
                                'fecha_susp' => '0000-00-00',
                                'precio_venta' => 0,
                                'precio_mayor' => 0,
                                'precio_tres' => 0,
                                'precio_cuatro' => 0,
                                'precio_cinco' => 0,
                                'precio_seis' => 0,
                                'precio_costo' => 0,
                                'precio_inver' => 0,
                                'precio_refer' => 0,
                                'porct_1' => 0,
                                'porct_2' => 0,
                                'porct_3' => 0,
                                'porct_4' => 0,
                                'porct_5' => 0,
                                'porct_6' => 0,
                                'costo_ultimo' => 0
                            ]
                        );
                    }
                }

                $almacenes = DB::connection('soft')->table('almacen')->get();

                foreach ($almacenes as $alm) {
                    $stock = DB::connection('soft')->table('stocks')
                        ->where([['cod_suc', '=', $alm->cod_suc], ['cod_alma', '=', $alm->cod_alma], ['cod_prod', '=', $cod_prod]])
                        ->first();

                    if ($stock == null) {
                        DB::connection('soft')->table('stocks')->insert(
                            [
                                'cod_suc' => $alm->cod_suc,
                                'cod_alma' => $alm->cod_alma,
                                'cod_prod' => $cod_prod,
                                'stock_act' => 0,
                                'stock_ing' => 0,
                                'stock_ped' => 0,
                                'stock_min' => 0,
                                'stock_max' => 0,
                                'cod_ubic' => '',
                            ]
                        );
                    }
                }
            }
            DB::table('almacen.alm_prod')
                ->where('id_producto', $producto->id_producto)
                ->update(['cod_softlink' => $cod_prod]);

            DB::commit();
            return response()->json(array('tipo' => 'success', 'codigo_softlink' => $cod_prod, 'mensaje' => 'Se migró correctamente el producto a Softlink con cod: ' . $cod_prod));
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage()));
        }
    }

    public function obtenerClasificacion($clasificacion)
    {
        //verifica si tiene clasificacion
        $clasif = DB::connection('soft')->table('soplinea')
            ->select('cod_line')
            ->where('nom_line', trim($clasificacion))
            ->first();

        $cod_clasi = null;

        if ($clasif !== null) {
            $cod_clasi = $clasif->cod_line;
        } else {
            $ultimo_line = DB::connection('soft')->table('soplinea')
                ->select('cod_line')->orderBy('cod_line', 'desc')->first();

            $cod_clasi = $this->leftZero(2, (intval($ultimo_line->cod_line) + 1));

            DB::connection('soft')->table('soplinea')->insert(
                [
                    'cod_line' => $cod_clasi,
                    'nom_line' => trim($clasificacion),
                    'cod_sunat' => '',
                    'cod_osce' => ''
                ]
            );
        }
        return $cod_clasi;
    }

    public function obtenerCategoria($categoria, $id_categoria)
    {
        //verifica si existe categoria
        $cate = DB::connection('soft')->table('sopsub1')
            ->select('cod_sub1')
            ->where('nom_sub1', trim($categoria))
            ->first();

        $cod_cate = null;

        if ($cate !== null) {
            $cod_cate = $cate->cod_sub1;
        } else {
            $ultima_cate = DB::connection('soft')->table('sopsub1')
                ->select('cod_sub1')->orderBy('cod_sub1', 'desc')->first();

            $cod_cate = $this->leftZero(3, (intval($ultima_cate->cod_sub1) + 1));

            DB::connection('soft')->table('sopsub1')->insert(
                [
                    'cod_sub1' => $cod_cate,
                    'nom_sub1' => trim($categoria),
                    'por_dcto' => 0,
                    'num_corr' => 0
                ]
            );

            DB::table('almacen.alm_cat_prod')
                ->where('id_categoria', $id_categoria)
                ->update(['cod_softlink' => $cod_cate]);
        }
        return $cod_cate;
    }

    public function obtenerSubCategoria($subcategoria, $id_subcategoria)
    {
        //verifica si existe subcategoria
        $subcate = DB::connection('soft')->table('sopsub2')
            ->select('cod_sub2')
            ->where('nom_sub2', trim($subcategoria))
            ->first();

        $cod_subc = null;

        if ($subcate !== null) {
            $cod_subc = $subcate->cod_sub2;
        } else {
            $ultima_subc = DB::connection('soft')->table('sopsub2')
                ->select('cod_sub2')->orderBy('cod_sub2', 'desc')->first();

            $cod_subc = $this->leftZero(3, (intval($ultima_subc->cod_sub2) + 1));

            DB::connection('soft')->table('sopsub2')->insert(
                [
                    'cod_sub2' => $cod_subc,
                    'nom_sub2' => trim($subcategoria),
                    'por_adic' => '0.00',
                    'cod_sub1' => '',
                    'id_manufacturer' => 0
                ]
            );

            DB::table('almacen.alm_subcat')
                ->where('id_subcategoria', $id_subcategoria)
                ->update(['cod_softlink' => $cod_subc]);
        }
        return $cod_subc;
    }

    public function obtenerUnidadMedida($abreviatura)
    {
        //verifica si existe unidad medida
        $unidad = DB::connection('soft')->table('unidades')
            ->select('cod_unid')
            ->where('nom_unid', trim($abreviatura))
            ->first();

        $cod_unid = null;

        if ($unidad !== null) {
            $cod_unid = $unidad->cod_unid;
        } else {
            $count_unid = DB::connection('soft')->table('unidades')->count();

            $cod_unid = $this->leftZero(3, (intval($count_unid) + 1));

            DB::connection('soft')->table('unidades')->insert(
                [
                    'cod_unid' => $cod_unid,
                    'nom_unid' => trim($abreviatura),
                    'fac_unid' => '1'
                ]
            );
        }
        return $cod_unid;
    }
    public function leftZero($lenght, $number)
    {
        $nLen = strlen($number);
        $zeros = '';
        for ($i = 0; $i < ($lenght - $nLen); $i++) {
            $zeros = $zeros . '0';
        }
        return $zeros . $number;
    }

    public function actualizarFechasIngresoSoft($id_almacen)
    {
        $productos = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.serie',
                'alm_prod_serie.id_almacen',
                'alm_prod.id_producto',
                'alm_prod.cod_softlink',
                'alm_almacen.codigo'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_serie.id_prod')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_serie.id_almacen')
            ->whereNull('id_guia_com_det')
            ->whereNull('id_base')
            ->whereNull('alm_prod_serie.fecha_ingreso_soft')
            ->where('alm_prod_serie.estado', 1)
            ->where('alm_prod_serie.id_almacen', $id_almacen)
            ->get();

        foreach ($productos as $p) {
            $prod = DB::connection('soft')->table('series')
                ->select(
                    'series.fecha_ing',
                    'detmov.pre_prod',
                    'movimien.num_docu',
                    'movimien.tip_mone'
                )
                ->join('detmov', 'detmov.unico', '=', 'series.unicodet_i')
                ->join('movimien', 'movimien.mov_id', '=', 'detmov.mov_id')
                ->where('series.serie', strval(trim($p->serie)))
                ->orderBy('series.fecha_ing', 'asc')
                ->first();

            if ($prod !== null) {

                $fec = $prod->fecha_ing;
                $pre = $prod->pre_prod;
                $doc = $prod->num_docu;
                $mon = $prod->tip_mone;

                DB::table('almacen.alm_prod_serie')
                    ->where('serie', $p->serie)
                    ->update([
                        'fecha_ingreso_soft' => $fec,
                        'precio_unitario_soft' => $pre,
                        'doc_ingreso_soft' => $doc,
                        'moneda_soft' => $mon,
                    ]);
            }
        }

        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se actualizó correctamente '));
    }

    public function actualizarFechasIngresoAgile($id_almacen)
    {
        $productos = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.id_prod_serie',
                'alm_prod_serie.serie',
                'alm_prod_serie.id_almacen',
                'alm_prod.id_producto',
                'alm_prod.cod_softlink',
                'alm_almacen.codigo'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_serie.id_prod')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_serie.id_almacen')
            ->whereNull('alm_prod_serie.fecha_ingreso_soft')
            ->where('alm_prod_serie.estado', 1)
            ->where('alm_prod_serie.id_almacen', $id_almacen)
            // ->whereIn('alm_prod_serie.id_prod_serie', [53305, 53887])
            ->get();

        foreach ($productos as $p) {
            $data = DB::table('almacen.alm_prod_serie')
                ->select(
                    'guia_com.fecha_almacen',
                    'guia_com_det.unitario',
                    DB::raw("guia_com.serie || '-' || guia_com.numero as serie_numero"),
                    'log_ord_compra.id_moneda',
                    'alm_prod.id_moneda as id_moneda_producto',

                    'ingreso_transformado.fecha_emision as fecha_transformado',
                    'transformado.id_moneda as id_moneda_transformado',
                    'transformado.codigo as codigo_transformado',
                    'transfor_transformado.valor_unitario as unitario_transformado',

                    'ingreso_sobrante.fecha_emision as fecha_sobrante',
                    'sobrante.id_moneda as id_moneda_sobrante',
                    'sobrante.codigo as codigo_sobrante',
                    'transfor_sobrante.valor_unitario as unitario_sobrante'
                )
                ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det')
                ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
                ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'guia_com_det.id_producto')
                ->leftjoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', '=', 'guia_com_det.id_oc_det')
                ->leftjoin('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')

                ->leftjoin('almacen.mov_alm_det as ingreso_sobrante_det',  'ingreso_sobrante_det.id_sobrante', '=', 'alm_prod_serie.id_sobrante')
                ->leftjoin('almacen.mov_alm as ingreso_sobrante',  'ingreso_sobrante.id_mov_alm', '=', 'ingreso_sobrante_det.id_mov_alm')
                ->leftjoin('almacen.transfor_sobrante',  'transfor_sobrante.id_sobrante', '=', 'alm_prod_serie.id_sobrante')
                ->leftjoin('almacen.transformacion as sobrante', 'sobrante.id_transformacion', '=', 'transfor_sobrante.id_transformacion')

                ->leftjoin('almacen.mov_alm_det as ingreso_transformado_det',  'ingreso_transformado_det.id_transformado', '=', 'alm_prod_serie.id_transformado')
                ->leftjoin('almacen.mov_alm as ingreso_transformado',  'ingreso_transformado.id_mov_alm', '=', 'ingreso_transformado_det.id_mov_alm')
                ->leftjoin('almacen.transfor_transformado',  'transfor_transformado.id_transformado', '=', 'alm_prod_serie.id_transformado')
                ->leftjoin('almacen.transformacion as transformado', 'transformado.id_transformacion', '=', 'transfor_transformado.id_transformacion')

                ->where('alm_prod_serie.serie', $p->serie)
                ->where('alm_prod_serie.id_almacen', $p->id_almacen)
                ->where('alm_prod_serie.estado', 1)
                ->orderBy('alm_prod_serie.fecha_registro', 'asc')
                ->first();

            if ($data !== null) {

                if ($data->serie_numero !== null) {
                    DB::table('almacen.alm_prod_serie')
                        ->where('id_prod_serie', $p->id_prod_serie)
                        ->update([
                            'fecha_ingreso_soft' => $data->fecha_almacen,
                            'precio_unitario_soft' => $data->unitario,
                            'doc_ingreso_soft' => $data->serie_numero,
                            'moneda_soft' => ($data->id_moneda !== null ? $data->id_moneda : $data->id_moneda_producto),
                        ]);
                } else if ($data->codigo_sobrante !== null) {
                    DB::table('almacen.alm_prod_serie')
                        ->where('id_prod_serie', $p->id_prod_serie)
                        ->update([
                            'fecha_ingreso_soft' => $data->fecha_sobrante,
                            'precio_unitario_soft' => $data->unitario_sobrante,
                            'doc_ingreso_soft' => $data->codigo_sobrante,
                            'moneda_soft' => ($data->id_moneda_sobrante !== null ? $data->id_moneda_sobrante : $data->id_moneda_producto),
                        ]);
                } else if ($data->codigo_transformado !== null) {
                    DB::table('almacen.alm_prod_serie')
                        ->where('id_prod_serie', $p->id_prod_serie)
                        ->update([
                            'fecha_ingreso_soft' => $data->fecha_transformado,
                            'precio_unitario_soft' => $data->unitario_transformado,
                            'doc_ingreso_soft' => $data->codigo_transformado,
                            'moneda_soft' => ($data->id_moneda_transformado !== null ? $data->id_moneda_transformado : $data->id_moneda_producto),
                        ]);
                }
            }
        }

        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se actualizó correctamente '));
    }
}
