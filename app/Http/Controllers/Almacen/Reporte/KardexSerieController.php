<?php

namespace App\Http\Controllers\Almacen\Reporte;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class KardexSerieController extends Controller
{
    function view_kardex_series()
    {
        return view('almacen/reportes/kardex_series');
    }

    public function listar_serie_productos($serie, $descripcion, $codigo, $part_number)
    {
        $hasWhere = [];
        if ($serie !== 'null') {
            $hasWhere[] = ['alm_prod_serie.serie', 'like', '%' . $serie . '%'];
        }
        if ($descripcion !== 'null') {
            $hasWhere[] = ['alm_prod.descripcion', 'like', '%' . strtoupper($descripcion) . '%'];
        }
        if ($codigo !== 'null') {
            $hasWhere[] = ['alm_prod.codigo', 'like', '%' . $codigo . '%'];
        }
        if ($part_number !== 'null') {
            $hasWhere[] = ['alm_prod.part_number', 'like', '%' . $part_number . '%'];
        }
        $data = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.id_prod_serie',
                'alm_prod_serie.id_prod',
                'alm_prod_serie.serie',
                'alm_almacen.descripcion as almacen_descripcion',
                'alm_prod.descripcion',
                'alm_prod.codigo',
                'alm_prod.part_number'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_serie.id_prod')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_serie.id_almacen')
            ->where([
                ['alm_prod_serie.estado', '=', 1],
                // ['alm_prod.estado', '=', 1]
            ])
            ->where($hasWhere)
            ->distinct()
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_kardex_serie($serie, $id_prod)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.*',
                'guia_com.fecha_emision as fecha_guia_com',
                'guia_ven.fecha_emision as fecha_guia_ven',
                'contri_cliente.razon_social as razon_social_cliente',
                'contri_prove.razon_social as razon_social_prove',
                'alm_com.descripcion as almacen_compra',
                'ope_com.descripcion as operacion_compra',
                'alm_ven.descripcion as almacen_venta',
                'ope_ven.descripcion as operacion_venta',
                'responsable_com.nombre_corto as responsable_compra',
                'responsable_ven.nombre_corto as responsable_venta',
                'ingreso.codigo as ingreso_codigo',
                'salida.codigo as salida_codigo',

                'alm_base.descripcion as almacen_customizacion',
                'ope_cus.descripcion as operacion_customizacion',
                'ingreso_cus.codigo as ingreso_codigo_customizacion',
                'transformacion.codigo as codigo_customizacion',
                'ingreso_cus.fecha_emision as fecha_ingreso_customizacion',
                'mov_det_base.id_mov_alm_det as id_mov_alm_det_base',

                'alm_sobrante.descripcion as almacen_sobrante',
                'ope_sobrante.descripcion as operacion_sobrante',
                'ingreso_sob.codigo as ingreso_codigo_sobrante',
                'custom_sobrante.codigo as codigo_sobrante',
                'ingreso_sob.fecha_emision as fecha_ingreso_sobrante',
                'mov_det_sobrante.id_mov_alm_det as id_mov_alm_det_sobrante',

                'alm_transformado.descripcion as almacen_transformado',
                'ope_transformado.descripcion as operacion_transformado',
                'ingreso_transformado.codigo as ingreso_codigo_transformado',
                'custom_transformado.codigo as codigo_transformado',
                'ingreso_transformado.fecha_emision as fecha_ingreso_transformado',
                'mov_det_transformado.id_mov_alm_det as id_mov_alm_det_transformado',

                DB::raw("(tp_doc_com.abreviatura) || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com"),
                DB::raw("(tp_doc_ven.abreviatura) || '-' || (guia_ven.serie) || '-' || (guia_ven.numero) as guia_ven"),
                DB::raw("(cont_tp_doc.abreviatura) || '-' || (doc_com.serie) || '-' || (doc_com.numero) as doc_com")
            )
            ->leftjoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'alm_prod_serie.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri as contri_cliente', 'contri_cliente.id_contribuyente', '=', 'com_cliente.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen as tp_doc_ven', 'tp_doc_ven.id_tp_doc_almacen', '=', 'guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.alm_almacen as alm_ven', 'alm_ven.id_almacen', '=', 'guia_ven.id_almacen')
            ->leftjoin('almacen.tp_ope as ope_ven', 'ope_ven.id_operacion', '=', 'guia_ven.id_operacion')
            ->leftjoin('configuracion.sis_usua as responsable_ven', 'responsable_ven.id_usuario', '=', 'guia_ven.usuario')
            // ->leftjoin('almacen.mov_alm_det as det_salida', 'det_salida.id_guia_ven_det', '=', 'alm_prod_serie.id_guia_ven_det')
            ->leftJoin('almacen.mov_alm_det as det_salida', function ($join) {
                $join->on('det_salida.id_guia_ven_det', '=', 'alm_prod_serie.id_guia_ven_det');
                $join->where('det_salida.estado', '!=', 7);
            })
            ->leftjoin('almacen.mov_alm as salida', 'salida.id_mov_alm', '=', 'det_salida.id_mov_alm')

            ->leftjoin('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.tp_ope as ope_com', 'ope_com.id_operacion', '=', 'guia_com.id_operacion')
            ->leftjoin('configuracion.sis_usua as responsable_com', 'responsable_com.id_usuario', '=', 'guia_com.usuario')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri as contri_prove', 'contri_prove.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen as tp_doc_com', 'tp_doc_com.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.alm_almacen as alm_com', 'alm_com.id_almacen', '=', 'guia_com.id_almacen')
            ->leftjoin('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det')
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->leftJoin('almacen.mov_alm_det as det_ingreso', function ($join) {
                $join->on('det_ingreso.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det');
                $join->where('det_ingreso.estado', '!=', 7);
            })
            ->leftjoin('almacen.mov_alm as ingreso', 'ingreso.id_mov_alm', '=', 'det_ingreso.id_mov_alm')
            //item base
            ->leftjoin('almacen.transfor_materia', 'transfor_materia.id_materia', '=', 'alm_prod_serie.id_base')
            ->leftjoin('almacen.mov_alm_det as mov_det_base', function ($join) {
                $join->on('mov_det_base.id_materia', '=', 'transfor_materia.id_materia');
                $join->where('mov_det_base.estado', '!=', 7);
            })
            ->leftjoin('almacen.mov_alm as ingreso_cus', 'ingreso_cus.id_mov_alm', '=', 'mov_det_base.id_mov_alm')
            ->leftjoin('almacen.transformacion', 'transformacion.id_transformacion', '=', 'transfor_materia.id_transformacion')
            ->leftjoin('almacen.alm_almacen as alm_base', 'alm_base.id_almacen', '=', 'transformacion.id_almacen')
            ->leftjoin('almacen.tp_ope as ope_cus', 'ope_cus.id_operacion', '=', 'ingreso_cus.id_operacion')
            //item sobrante
            ->leftjoin('almacen.transfor_sobrante', 'transfor_sobrante.id_sobrante', '=', 'alm_prod_serie.id_sobrante')
            ->leftjoin('almacen.mov_alm_det as mov_det_sobrante', function ($join) {
                $join->on('mov_det_sobrante.id_sobrante', '=', 'transfor_sobrante.id_sobrante');
                $join->where('mov_det_sobrante.estado', '!=', 7);
            })
            ->leftjoin('almacen.mov_alm as ingreso_sob', 'ingreso_sob.id_mov_alm', '=', 'mov_det_sobrante.id_mov_alm')
            ->leftjoin('almacen.transformacion as custom_sobrante', 'custom_sobrante.id_transformacion', '=', 'transfor_sobrante.id_transformacion')
            ->leftjoin('almacen.alm_almacen as alm_sobrante', 'alm_sobrante.id_almacen', '=', 'custom_sobrante.id_almacen')
            ->leftjoin('almacen.tp_ope as ope_sobrante', 'ope_sobrante.id_operacion', '=', 'ingreso_sob.id_operacion')
            //item transformado
            ->leftjoin('almacen.transfor_transformado', 'transfor_transformado.id_transformado', '=', 'alm_prod_serie.id_transformado')
            ->leftjoin('almacen.mov_alm_det as mov_det_transformado', function ($join) {
                $join->on('mov_det_transformado.id_transformado', '=', 'transfor_transformado.id_transformado');
                $join->where('mov_det_transformado.estado', '!=', 7);
            })
            ->leftjoin('almacen.mov_alm as ingreso_transformado', 'ingreso_transformado.id_mov_alm', '=', 'mov_det_transformado.id_mov_alm')
            ->leftjoin('almacen.transformacion as custom_transformado', 'custom_transformado.id_transformacion', '=', 'transfor_transformado.id_transformacion')
            ->leftjoin('almacen.alm_almacen as alm_transformado', 'alm_transformado.id_almacen', '=', 'custom_transformado.id_almacen')
            ->leftjoin('almacen.tp_ope as ope_transformado', 'ope_transformado.id_operacion', '=', 'ingreso_transformado.id_operacion')

            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_serie.id_prod')

            ->where([
                ['alm_prod_serie.serie', '=', $serie],
                ['alm_prod_serie.id_prod', '=', $id_prod],
                ['alm_prod_serie.estado', '=', 1],
            ])
            ->orderBy('alm_prod_serie.fecha_registro')
            ->get();

        return response()->json($data);
    }

    public function datos_producto($id_producto)
    {
        $producto = DB::table('almacen.alm_prod')
            ->select(
                'alm_prod.*',
                'alm_und_medida.abreviatura',
                'alm_subcat.descripcion as des_subcategoria',
                'alm_cat_prod.descripcion as des_categoria',
                'alm_tp_prod.descripcion as des_tipo',
                'alm_tp_prod.id_tipo_producto',
                'alm_ubi_posicion.codigo as cod_posicion',
                'alm_clasif.descripcion as des_clasificacion'
            )
            ->join('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->join('almacen.alm_tp_prod', 'alm_tp_prod.id_tipo_producto', '=', 'alm_cat_prod.id_tipo_producto')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->join('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_tp_prod.id_clasificacion')
            ->leftjoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_producto', '=', 'alm_prod.id_producto')
            ->leftjoin('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
            ->where('alm_prod.id_producto', $id_producto)
            ->first();

        $html = '
            <tr>
                <th width="80px">Código</th>
                <td>' . $producto->codigo . '</td>
                <th width="80px">Descripción</th>
                <td>' . $producto->descripcion . '</td>
                <th width="80px">Unid.Med.</th>
                <td>' . $producto->abreviatura . '</td>
            </tr>
            <tr>
                <th>Tipo</th>
                <td width="23%">' . $producto->des_tipo . '</td>
                <th>Categoría</th>
                <td>' . $producto->des_categoria . '</td>
                <th>Sub-Categoría</th>
                <td>' . $producto->des_subcategoria . '</td>
            </tr>
            <tr>
                <th>Clasificación</th>
                <td>' . $producto->des_clasificacion . '</td>
                <th>Part number</th>
                <td>' . $producto->part_number . '</td>
                <th>Ubicación</th>
                <td>' . $producto->cod_posicion . '</td>
            </tr>
            ';
        return json_encode($html);
    }
}
