<?php

namespace App\Http\Controllers\Almacen\Catalogo;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    function view_producto()
    {
        $tipos = AlmacenController::mostrar_tipos_cbo();
        $clasificaciones = AlmacenController::mostrar_clasificaciones_cbo();
        $subcategorias = AlmacenController::mostrar_subcategorias_cbo();
        $categorias = AlmacenController::mostrar_categorias_cbo();
        $unidades = AlmacenController::mostrar_unidades_cbo();
        // $posiciones = $this->mostrar_posiciones_cbo();
        // $ubicaciones = $this->mostrar_ubicaciones_cbo();
        $monedas = AlmacenController::mostrar_moneda_cbo();
        return view('almacen/producto/producto', compact('tipos', 'categorias', 'clasificaciones', 'subcategorias', 'unidades', 'monedas'));
    }

    function view_prod_catalogo()
    {
        return view('almacen/producto/prod_catalogo');
    }

    public function mostrar_prods()
    {
        $prod = DB::table('almacen.alm_prod')
            ->select(
                'alm_prod.id_producto',
                'alm_prod.codigo',
                'alm_prod.cod_softlink',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'alm_prod.series',
                'alm_prod.estado',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'alm_subcat.descripcion as marca',
                'alm_und_medida.abreviatura'
            )
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_prod.estado');
        return datatables($prod)->toJson();
    }

    ////sugeridos
    public function actualizarSugeridos(Request $request)
    {
        if ($request->part_number != null) {
            $request->session()->put('productFilter_partnumber', $request->part_number);
        } else {
            $request->session()->forget('productFilter_partnumber');
        }

        if ($request->descripcion != null) {
            $request->session()->put('productFilter_descripcion', $request->descripcion);
        } else {
            $request->session()->forget('productFilter_descripcion');
        }

        return response()->json(array('response' => 'ok'), 200);
    }

    public function listarProductosSugeridos()
    {
        $data = DB::table('almacen.alm_prod')
            ->select(
                'alm_prod.id_producto',
                'alm_prod.codigo',
                'alm_prod.cod_softlink',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'alm_prod.series',
                'alm_und_medida.abreviatura',
                'alm_subcat.descripcion as marca'
            )
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida');

        if (session()->has('productFilter_partnumber')) {
            $data = $data->where('alm_prod.part_number', session()->get('productFilter_partnumber'))->get();
        } else if (session()->has('productFilter_descripcion')) {
            $data = $data->where('alm_prod.descripcion', session()->get('productFilter_descripcion'))->get();
        }
        // $output['data'] = $data;
        return response()->json($data);
    }

    public function mostrar_prods_sugeridos($part_number, $descripcion)
    {
        if ($part_number !== '') {
            $prod = DB::table('almacen.alm_prod')
                ->select(
                    'alm_prod.id_producto',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'alm_prod.part_number',
                    'alm_subcat.descripcion as marca'
                )
                ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                ->where('alm_prod.part_number', $part_number)
                ->get();
        } else if ($descripcion !== '') {
            $prod = DB::table('almacen.alm_prod')
                ->select(
                    'alm_prod.id_producto',
                    'alm_prod.codigo',
                    'alm_prod.descripcion',
                    'alm_prod.part_number',
                    'alm_subcat.descripcion as marca'
                )
                ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                ->where('alm_prod.descripcion', $descripcion)
                ->get();
        }
        $output['data'] = $prod;
        return response()->json($output);
    }

    public function mostrar_prods_almacen($id_almacen)
    {
        $prod = DB::table('almacen.alm_prod_ubi')
            ->select(
                'alm_prod_ubi.*',
                'alm_prod.codigo',
                'alm_prod.descripcion',
                'alm_prod.part_number',
                'alm_prod.id_unidad_medida',
                'alm_prod_antiguo.cod_antiguo',
                'alm_prod_ubi.stock',
                'alm_ubi_posicion.codigo as cod_posicion'
            )
            ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_prod_ubi.id_producto')
            ->leftjoin('almacen.alm_prod_antiguo', 'alm_prod_antiguo.id_producto', '=', 'alm_prod_ubi.id_producto')
            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([
                ['alm_almacen.id_almacen', '=', $id_almacen],
                ['alm_prod_ubi.stock', '>', 0]
            ])
            ->get();
        $size = $prod->count();
        $output['data'] = $prod;
        return response()->json($output);
    }

    public function mostrar_producto($id)
    {
        $producto = DB::table('almacen.alm_prod')
            ->select(
                'alm_prod.*',
                'alm_subcat.descripcion as subcat_descripcion',
                'alm_cat_prod.descripcion as cat_descripcion',
                'alm_tp_prod.descripcion as tipo_descripcion',
                'alm_tp_prod.id_tipo_producto',
                'alm_tp_prod.id_clasificacion',
                'sis_usua.nombre_corto',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
            )
            ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->leftjoin('almacen.alm_tp_prod', 'alm_tp_prod.id_tipo_producto', '=', 'alm_cat_prod.id_tipo_producto')
            ->leftjoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_tp_prod.id_clasificacion')
            ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_prod.id_usuario')
            ->leftjoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_prod.estado')
            ->where([['alm_prod.id_producto', '=', $id]])
            ->get();

        $antiguos = DB::table('almacen.alm_prod_antiguo')
            ->where([['alm_prod_antiguo.id_producto', '=', $id]])
            ->orderBy('cod_antiguo')->get();

        $data = ["producto" => $producto, "antiguos" => $antiguos];
        return response()->json($data);
    }

    public function mostrar_categorias_tipo($id_tipo)
    {
        $data = DB::table('almacen.alm_cat_prod')
            ->where([['estado', '=', 1], ['id_tipo_producto', '=', $id_tipo]])
            ->orderBy('descripcion')
            ->get();
        return response()->json($data);
    }

    public function guardar_producto(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id_usuario;
        $msj = '';
        $des = strtoupper(trim($request->descripcion));
        $pn = trim($request->part_number);

        if ($pn !== null && $pn !== '') {
            $count = DB::table('almacen.alm_prod')
                ->where([['part_number', '=', $pn], ['estado', '=', 1]])
                ->count();
        } else if ($des !== null && $des !== '') {
            $count = DB::table('almacen.alm_prod')
                ->where([['descripcion', '=', $des], ['estado', '=', 1]])
                ->count();
        }

        if ($count == 0) {
            $id_producto = DB::table('almacen.alm_prod')->insertGetId(
                [
                    'part_number' => $pn,
                    'id_clasif' => $request->id_clasif,
                    'id_subcategoria' => $request->id_subcategoria,
                    'id_categoria' => $request->id_categoria,
                    'descripcion' => $des,
                    'id_unidad_medida' => ($request->id_unidad_medida !== 0 ? $request->id_unidad_medida : null),
                    'id_unid_equi' => (($request->id_unid_equi !== 0 && $request->id_unid_equi !== null) ? $request->id_unid_equi : null),
                    'cant_pres' => ($request->cant_pres !== null ? $request->cant_pres : null),
                    'series' => ($request->series == null || $request->series !== '1') ? false : true,
                    'afecto_igv' => ($request->afecto_igv == null || $request->afecto_igv !== '1') ? false : true,
                    'id_moneda' => ($request->id_moneda !== null ? $request->id_moneda : null),
                    'notas' => ($request->notas !== null ? $request->notas : ''),
                    'id_usuario' => $id_usuario,
                    'sunat_unsps' => $request->sunat_unsps,
                    'codigo_compuesto' => $request->codigo_compuesto,
                    'peso' => $request->peso,
                    'largo' => $request->largo,
                    'ancho' => $request->ancho,
                    'alto' => $request->alto,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                'id_producto'
            );

            $codigo = AlmacenController::leftZero(7, $id_producto);

            DB::table('almacen.alm_prod')
                ->where('id_producto', $id_producto)
                ->update(['codigo' => $codigo]);

            // $id_item = DB::table('almacen.alm_item')->insertGetId(
            //     [   'id_producto' => $id_producto,
            //         'codigo' => $codigo,
            //         'fecha_registro' => $fecha
            //     ],  'id_item');

            $producto = DB::table('almacen.alm_prod')
                ->select('alm_prod.*', 'alm_und_medida.abreviatura', 'alm_cat_prod.descripcion as categoria', 'alm_subcat.descripcion as subcategoria')
                ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
                ->join('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                ->where('id_producto', $id_producto)->first();

            return response()->json(['msj' => $msj, 'id_item' => 0, 'id_producto' => $id_producto, 'producto' => $producto]);
        } else {
            $msj = 'No es posible guardar. Ya existe un producto con dicha descripción y/o Part Number.';

            return response()->json(['msj' => $msj]);
        }
    }

    public function update_producto(Request $request)
    {
        $msj = '';
        $des = strtoupper($request->descripcion);

        $count = DB::table('almacen.alm_prod')
            ->where([['descripcion', '=', $des], ['part_number', '=', $request->part_number], ['estado', '=', 1]])
            ->count();

        $id_item = 0;
        $id_producto = $request->id_producto;

        if ($count <= 1) {
            $data = DB::table('almacen.alm_prod')
                ->where('id_producto', $id_producto)
                ->update([
                    'part_number' => $request->part_number,
                    'id_subcategoria' => $request->id_subcategoria,
                    'id_categoria' => $request->id_categoria,
                    'id_clasif' => $request->id_clasif,
                    'descripcion' => $des,
                    'id_unidad_medida' => $request->id_unidad_medida,
                    'id_unid_equi' => $request->id_unid_equi,
                    'cant_pres' => $request->cant_pres,
                    'series' => ($request->series == '1' ? true : false),
                    'afecto_igv' => ($request->afecto_igv == '1' ? true : false),
                    'id_moneda' => $request->id_moneda,
                    'notas' => $request->notas,
                    'sunat_unsps' => $request->sunat_unsps,
                    'codigo_compuesto' => $request->codigo_compuesto,
                    'peso' => $request->peso,
                    'largo' => $request->largo,
                    'ancho' => $request->ancho,
                    'alto' => $request->alto,
                ]);

            $id_item = DB::table('almacen.alm_item')
                ->select('alm_item.id_item')
                ->where('id_producto', $id_producto)
                ->first();
        } else {
            $msj = 'No es posible actualizar. Ya existe un producto con la misma descripción y/o Part Number.';
        }
        return response()->json(['msj' => $msj, 'id_item' => $id_item, 'id_producto' => $id_producto]);
    }

    public function anular_producto($id)
    {
        try {
            DB::beginTransaction();

            $relacionados = DB::table('almacen.alm_prod')
                ->leftjoin('almacen.alm_det_req', 'alm_det_req.id_producto', '=', 'alm_prod.id_producto')
                ->leftjoin('almacen.mov_alm_det', 'mov_alm_det.id_producto', '=', 'alm_prod.id_producto')
                ->leftjoin('almacen.transfor_sobrante', 'transfor_sobrante.id_producto', '=', 'alm_prod.id_producto')
                ->where([
                    ['alm_prod.id_producto', '=', $id],
                    ['alm_det_req.estado', '!=', 7]
                ])
                ->count();

            if ($relacionados > 0) {
                $arrayRspta = array(
                    'tipo' => 'warning',
                    'mensaje' => 'El producto ya fue relacionado con otros documentos! No es posible dar de baja',
                );
            } else {
                DB::table('almacen.alm_prod')
                    ->where('id_producto', $id)
                    ->update(['estado' => 7]);

                $arrayRspta = array(
                    'tipo' => 'success',
                    'mensaje' => 'El producto fue dado de baja.',
                );
            }

            DB::commit();
            return response()->json($arrayRspta, 200);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al anular la orden. Por favor intente de nuevo', 'error' => $e->getMessage()));
        }
    }


    public function guardar_imagen(Request $request)
    {
        $update = false;
        $namefile = "";
        if ($request->codigo !== "" && $request->codigo !== null) {
            $nfile = $request->file('imagen');

            if (isset($nfile)) {

                $extension = pathinfo($nfile->getClientOriginalName(), PATHINFO_EXTENSION);
                $namefile = $request->codigo . '.' . $extension;

                File::delete(public_path('almacen/productos/' . $namefile));
                Storage::disk('archivos')->put('almacen/productos/' . $namefile, File::get($nfile));

                $update = DB::table('almacen.alm_prod')
                    ->where('id_producto', $request->id_producto)
                    ->update(['imagen' => $namefile]);
            }
        }

        if ($update) {
            $status = 1;
        } else {
            $status = 0;
        }
        $array = array("status" => $status, "imagen" => $namefile);
        return response()->json($array);
    }

    //Promociones

    public function listar_promociones($id_producto)
    {
        $data = DB::table('almacen.alm_prod_prom')
            ->select(
                'alm_prod_prom.*',
                'sis_usua.nombre_corto',
                DB::raw("(cat_prod.descripcion) || ' ' || (subcat_prod.descripcion) || ' ' || (prod.descripcion) AS descripcion_producto"),
                DB::raw("(cat_prod_prom.descripcion) || ' ' || (subcat_prod_prom.descripcion) || ' ' || (prod_prom.descripcion) AS descripcion_producto_promocion")
            )
            ->join('almacen.alm_prod as prod', 'prod.id_producto', '=', 'alm_prod_prom.id_producto')
            ->join('almacen.alm_cat_prod as cat_prod', 'cat_prod.id_categoria', '=', 'prod.id_categoria')
            ->join('almacen.alm_subcat as subcat_prod', 'subcat_prod.id_subcategoria', '=', 'prod.id_subcategoria')
            ->join('almacen.alm_prod as prod_prom', 'prod_prom.id_producto', '=', 'alm_prod_prom.id_producto_promocion')
            ->join('almacen.alm_cat_prod as cat_prod_prom', 'cat_prod_prom.id_categoria', '=', 'prod_prom.id_categoria')
            ->join('almacen.alm_subcat as subcat_prod_prom', 'subcat_prod_prom.id_subcategoria', '=', 'prod_prom.id_subcategoria')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_prod_prom.usuario_registro')
            ->where([
                ['alm_prod_prom.id_producto', '=', $id_producto],
                ['alm_prod_prom.estado', '!=', 7]
            ])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function crear_promocion(Request $request)
    {
        $id_usuario = Auth::user()->id_usuario;
        $id = DB::table('almacen.alm_prod_prom')
            ->insertGetId(
                [
                    'id_producto' => $request->id_producto,
                    'id_producto_promocion' => $request->id_producto_promocion,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s'),
                    'usuario_registro' => $id_usuario,
                ],
                'id_promocion'
            );
        return response()->json($id);
    }

    public function anular_promocion($id)
    {
        $update = DB::table('almacen.alm_prod_prom')
            ->where('id_promocion', $id)
            ->update(['estado' => 7]);
        return response()->json($update);
    }

    /** Producto Ubicacion */
    public function listar_ubicaciones_producto($id)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select(
                'alm_prod_ubi.*', //'alm_almacen.codigo',
                'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_posicion.codigo as cod_posicion'
            )
            ->leftjoin('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_ubi.id_almacen')
            ->where([['alm_prod_ubi.id_producto', '=', $id], ['alm_prod_ubi.estado', '!=', 7]])
            ->orderBy('id_almacen')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_ubicacion($id)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.*', 'alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_ubi_posicion', 'alm_ubi_posicion.id_posicion', '=', 'alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel', 'alm_ubi_nivel.id_nivel', '=', 'alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante', 'alm_ubi_estante.id_estante', '=', 'alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.id_prod_ubi', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_ubicacion(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_almacen = DB::table('almacen.alm_prod_ubi')->insertGetId(
            [
                'id_producto' => $request->id_producto,
                'id_posicion' => $request->id_posicion,
                'stock' => $request->stock,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
            'id_prod_ubi'
        );
        return response()->json($id_almacen);
    }

    public function update_ubicacion(Request $request)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->where('id_prod_ubi', $request->id_prod_ubi)
            ->update([
                'id_posicion' => $request->id_posicion,
                'stock' => $request->stock
            ]);
        return response()->json($data);
    }

    public function anular_ubicacion(Request $request, $id)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->where([['alm_prod_ubi.id_prod_ubi', '=', $id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    /**ProductoUbicacion Series */
    public function listar_series_producto($id)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->select(
                'alm_prod_serie.*',
                'alm_almacen.descripcion as alm_descripcion',
                DB::raw("('GR') || '-' || (guia_com.serie) || '-' || (guia_com.numero) as guia_com"),
                DB::raw("('GR') || '-' || (guia_ven.serie) || '-' || (guia_ven.numero) as guia_ven")
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_prod_serie.id_almacen')
            ->join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', '=', 'alm_prod_serie.id_guia_com_det')
            ->join('almacen.guia_com', 'guia_com.id_guia', '=', 'guia_com_det.id_guia_com')
            ->leftjoin('almacen.guia_ven_det', 'guia_ven_det.id_guia_ven_det', '=', 'alm_prod_serie.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven', 'guia_ven.id_guia_ven', '=', 'guia_ven_det.id_guia_ven')
            ->where([['alm_prod_serie.id_prod', '=', $id]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_serie($id)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->select('alm_prod_serie.*')
            ->where([['alm_prod_serie.id_prod_serie', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_serie(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_almacen = DB::table('almacen.alm_prod_serie')->insertGetId(
            [
                'id_prod' => $request->id_prod,
                'id_almacen' => $request->id_almacen,
                'serie' => $request->serie,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
            'id_prod_serie'
        );
        return response()->json($id_almacen);
    }

    public function update_serie(Request $request)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->where('id_prod_serie', $request->id_prod_serie)
            ->update([
                'id_prod' => $request->id_prod,
                'serie' => $request->serie
            ]);
        return response()->json($data);
    }

    public function anular_serie(Request $request, $id)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->where([['alm_prod_serie.id_prod_serie', '=', $id]])
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    //Catalogo Producto
    public function mostrar_productos()
    {
        $data = DB::table('almacen.alm_prod')
            ->select(
                'alm_prod.id_producto',
                'alm_prod.part_number',
                'alm_prod.codigo',
                'alm_prod.cod_softlink',
                'alm_prod.descripcion',
                'alm_und_medida.abreviatura',
                'alm_subcat.cod_softlink as cod_sub_cat',
                'alm_subcat.descripcion as subcat_descripcion',
                'alm_cat_prod.cod_softlink as cod_cat',
                'alm_cat_prod.descripcion as cat_descripcion',
                'alm_tp_prod.id_tipo_producto',
                'alm_tp_prod.descripcion as tipo_descripcion',
                'alm_clasif.id_clasificacion',
                'alm_clasif.descripcion as clasif_descripcion'
            )
            ->join('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
            ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
            ->join('almacen.alm_tp_prod', 'alm_tp_prod.id_tipo_producto', '=', 'alm_cat_prod.id_tipo_producto')
            ->join('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_tp_prod.id_clasificacion')
            ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
}
