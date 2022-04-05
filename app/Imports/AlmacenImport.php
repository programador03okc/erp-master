<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class AlmacenImport implements ToCollection, WithHeadingRow
{
    private $numRows = 0;
    private $numRowsStatus = 0;
    public $type, $model;

    public function __construct($type, $model)
    {
        $this->type = $type;
        $this->model = $model;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            switch ($this->type) {
                case 1:
                    $descripcion = trim($row['descripcion']);
                    $this->saveAlmacen($row['cod_alma'], $row['cod_suc'], $row['sede'], $descripcion, $row['direccion'], $this->model);
                break;
                case 2:
                    $descripcion = trim($row['descripcion']);
                    $this->saveCategoria($row['cod_cate'], $descripcion, $this->model);
                break;
                case 3:
                    $descripcion = trim($row['descripcion']);
                    $this->saveSubCategoria($row['cod_subc'], $descripcion, $this->model);
                break;
                case 4:
                    $descripcion = trim($row['descripcion']);
                    $this->saveUnidad($row['cod_uni'], $descripcion, $this->model);
                break;
                case 5:
                    $descripcion = trim($row['nom_prod']);
                    $part_no = trim($row['cod_espe']);
                    $this->saveProducto($row['cod_prod'], $part_no, $descripcion, $row['cod_clasi'], $row['cod_cate'], $row['cod_subc'], $row['cod_unid'], $row['tip_moneda'], $row['flg_serie'], $row['flg_afecto_igv'], $row['txt_observa'], $row['ult_edicion'], $this->model);
                break;
                case 6:
                    $this->saveSerie($row['cod_alma'], $row['cod_prod'], $row['serie']);
                break;
                case 7:
                    $this->saveSaldo($row['cod_alma'], $row['cod_prod'], $row['stock'], $row['costo_promedio'], $row['valorizacion']);
                break;
            }

            // if ($this->type == 1) {
            //     $descripcion = trim($row['descripcion']);
            //     $this->saveAlmacen($row['cod_alma'], $row['cod_suc'], $row['sede'], $descripcion, $row['direccion'], $this->model);
            // } else if ($this->type == 2) {
            //     $descripcion = trim($row['descripcion']);
            //     $this->saveCategoria($row['cod_cate'], $descripcion, $this->model);
            // } else if ($this->type == 3) {
            //     $descripcion = trim($row['descripcion']);
            //     $this->saveSubCategoria($row['cod_subc'], $descripcion, $this->model);
            // } else if ($this->type == 4) {
            //     $descripcion = trim($row['descripcion']);
            //     $this->saveUnidad($row['cod_uni'], $descripcion, $this->model);
            // } else if ($this->type == 5) {
            //     $descripcion = trim($row['nom_prod']);
            //     $part_no = trim($row['cod_espe']);
            //     $this->saveProducto($row['cod_prod'], $part_no, $descripcion, $row['cod_clasi'], $row['cod_cate'], $row['cod_subc'], $row['cod_unid'], $row['tip_moneda'], $row['flg_serie'], $row['flg_afecto_igv'], $row['txt_observa'], $row['ult_edicion'], $this->model);
            // } else if ($this->type == 6) {
            //     $this->saveSerie($row['cod_alma'], $row['cod_prod'], $row['serie']);
            // } else if ($this->type == 7) {
            //     $this->saveSaldo($row['cod_alma'], $row['cod_prod'], $row['stock'], $row['costo_promedio'], $row['valorizacion']);
            // }
        }
    }

    public function getRowCount($form): int
    {
        $total = ($form == 1) ? $this->numRows : $this->numRowsStatus;
        return $total;
    }

    public function saveAlmacen($cod_alma, $cod_suc, $sede, $descripcion, $direccion, $tipo)
    {
        $query = DB::table('almacen.alm_almacen')->where('codigo', $cod_alma)->where('estado', 1);
        if ($query->count() == 0) {
            $cod_empresa = $this->getCodigoEmpresa($cod_suc);

            $query_emp = DB::table('administracion.adm_empresa')->where('codigo', $cod_empresa)->first();
                $id_empresa = $query_emp->id_empresa;
            $query_sede = DB::table('administracion.sis_sede')->where('id_empresa', $id_empresa)->where('codigo', $sede);

            if ($query_sede->count() > 0) {
                $id_sede = $query_sede->first()->id_sede;

                DB::table('almacen.alm_almacen')->insertGetId([
                    'id_sede'           => $id_sede,
                    'descripcion'       => $descripcion,
                    'ubicacion'         => ($direccion != '') ? $direccion : null,
                    'id_tipo_almacen'   => 1,
                    'estado'            => 1,
                    'codigo'            => $cod_alma,
                    'registrado_por'    => 1,
                    'fecha_registro'    => new Carbon()
                ], 'id_almacen');
                
                $this->numRows++;
            }
        } else {
            if ($tipo == 2) {
                $id_almacen = $query->first()->id_almacen;
                DB::table('almacen.alm_almacen')->where('id_almacen', $id_almacen)->update(['estado' => 1]);
                $this->numRowsStatus++;
            }
        }
    }

    public function saveCategoria($cod_cate, $descripcion, $tipo)
    {
        $query = DB::table('almacen.alm_cat_prod')->where('descripcion', $descripcion)->where('cod_softlink', $cod_cate);
        if ($query->count() == 0) {
            DB::table('almacen.alm_cat_prod')->insertGetId([
                'id_tipo_producto'  => 8,
                'cod_softlink'      => $cod_cate,
                'descripcion'       => ($descripcion != '') ? $descripcion : null,
                'estado'            => 1,
                'fecha_registro'    => new Carbon()
            ], 'id_categoria');

            $this->numRows++;
        } else {
            if ($tipo == 2) {
                $estado = $query->first()->estado;
                if ($estado == 7) {
                    $id_categoria = $query->first()->id_categoria;
                    DB::table('almacen.alm_cat_prod')->where('id_categoria', $id_categoria)->update(['estado' => 1]);
                    $this->numRowsStatus++;
                }
            }
        }
    }

    public function saveSubCategoria($cod_subc, $descripcion, $tipo)
    {
        $query = DB::table('almacen.alm_subcat')->where('descripcion', $descripcion)->where('cod_softlink', $cod_subc);
        if ($query->count() == 0) {
            DB::table('almacen.alm_subcat')->insertGetId([
                'cod_softlink'      => $cod_subc,
                'descripcion'       => ($descripcion != '') ? $descripcion : null,
                'estado'            => 1,
                'fecha_registro'    => new Carbon(),
                'registrado_por'    => 1
            ], 'id_subcategoria');

            $this->numRows++;
        } else {
            if ($tipo == 2) {
                $estado = $query->first()->estado;
                if ($estado == 7) {
                    $id_subcategoria = $query->first()->id_subcategoria;
                    DB::table('almacen.alm_subcat')->where('id_subcategoria', $id_subcategoria)->update(['estado' => 1]);
                    $this->numRowsStatus++;
                }
            }
        }
    }

    public function saveUnidad($cod_unid, $descripcion, $tipo)
    {
        $query = DB::table('almacen.alm_und_medida')->where('descripcion', $descripcion)->where('cod_softlink', $cod_unid);
        if ($query->count() == 0) {
            DB::table('almacen.alm_und_medida')->insertGetId([
                'descripcion'   => $descripcion,
                'abreviatura'   => ($descripcion != '') ? $descripcion : null,
                'estado'        => 1,
                'cod_softlink'  => $cod_unid
            ], 'id_unidad_medida');

            $this->numRows++;
        } else {
            if ($tipo == 2) {
                $estado = $query->first()->estado;
                if ($estado == 7) {
                    $id_unidad_medida = $query->first()->id_unidad_medida;
                    DB::table('almacen.alm_und_medida')->where('id_unidad_medida', $id_unidad_medida)->update(['estado' => 1]);
                    $this->numRowsStatus++;
                }
            }
        }
    }

    public function saveProducto($cod_prod, $part_no, $descripcion, $cod_clasi, $cod_cate, $cod_subc, $cod_unid, $tip_moneda, $flg_serie, $flg_afecto_igv,  $txt_observa, $ult_edicion, $tipo)
    {
        $query = DB::table('almacen.alm_prod')->where('descripcion', $descripcion) ->where('cod_softlink', $cod_prod)->orWhere('part_number', $part_no);
        if ($query->count() == 0) {
            $query_cla = DB::table('almacen.alm_clasif')->where('cod_softlink', $cod_clasi)->first();
            $query_cat = DB::table('almacen.alm_cat_prod')->where('cod_softlink', $cod_cate)->first();
            $query_sub = DB::table('almacen.alm_subcat')->where('cod_softlink', $cod_subc)->first();
            $query_und = DB::table('almacen.alm_und_medida')->where('cod_softlink', $cod_unid)->first();

            $id_cla = ($query_cla != '') ? $query_cla->id_clasificacion : null;
            $id_cat = ($query_cat != '') ? $query_cat->id_categoria : null;
            $id_sub = ($query_sub != '') ? $query_sub->id_subcategoria : null;
            $id_und = ($query_und != '') ? $query_und->id_unidad_medida : null;

            $flg_serie = ($flg_serie == 't') ? true : false;
            $flg_afect = ($flg_afecto_igv == 't') ? true : false;

            $producto = DB::table('almacen.alm_prod')->insertGetId([
                'cod_softlink'      => $cod_prod,
                'part_number'       => $part_no,
                'id_subcategoria'   => $id_sub,
                'id_clasif'         => $id_cla,
                'descripcion'       => $descripcion,
                'id_unidad_medida'  => $id_und,
                'series'            => $flg_serie,
                'afecto_igv'        => $flg_afect,
                'estado'            => 1,
                'fecha_registro'    => date('Y-m-d', strtotime($ult_edicion)),
                'id_moneda'         => ($tip_moneda != '') ? $tip_moneda : null,
                'notas'             => ($txt_observa != '') ? $txt_observa : null,
                'id_categoria'      => $id_cat,
                'id_usuario'        => 1,
                'afecta_kardex'     => true,
                'cod_cate'          => $cod_cate,
                'cod_subcate'       => $cod_subc,
                'cod_unid'          => $cod_unid
            ], 'id_producto');

            $code = $this->leftZero(7, $producto);
            DB::table('almacen.alm_prod')->where('id_producto', $producto)->update(['codigo' => $code]);

            $this->numRows++;
        } else {
            if ($tipo == 2) {
                $estado = $query->first()->estado;
                if ($estado == 7) {
                    $id_producto = $query->first()->id_producto;
                    DB::table('almacen.alm_prod')->where('id_producto', $id_producto)->update(['estado' => 1]);
                    $this->numRowsStatus++;
                }
            }
        }
    }
    
    public function saveSerie($cod_alma, $cod_prod, $serie)
    {
        $query_alm = DB::table('almacen.alm_almacen')->where('codigo', $cod_alma)->first();
        $query_pro = DB::table('almacen.alm_prod')->where('cod_softlink', $cod_prod)->first();

        $id_alm = ($query_alm != '') ? $query_alm->id_almacen : null;
        $id_pro = ($query_pro != '') ? $query_pro->id_producto : null;

        DB::table('almacen.alm_prod_serie')->insertGetId([
            'id_prod'           => $id_pro,
            'serie'             => $serie,
            'estado'            => 1,
            'fecha_registro'    => new Carbon(),
            'id_almacen'        => $id_alm
        ], 'id_prod_serie');

        $this->numRows++;
    }

    public function saveSaldo($cod_alma, $cod_prod, $stock, $costo_promedio, $valorizacion)
    {
        $query_alm = DB::table('almacen.alm_almacen')->where('codigo', $cod_alma)->first();
        $query_pro = DB::table('almacen.alm_prod')->where('cod_softlink', $cod_prod)->first();

        $id_alm = ($query_alm != '') ? $query_alm->id_almacen : null;
        $id_pro = ($query_pro != '') ? $query_pro->id_producto : null;

        DB::table('almacen.alm_prod_ubi')->insertGetId([
            'id_producto'       => $id_pro,
            'stock'             => $stock,
            'estado'            => 1,
            'fecha_registro'    => new Carbon(),
            'costo_promedio'    => $costo_promedio,
            'id_almacen'        => $id_alm,
            'valorizacion'      => $valorizacion
        ], 'id_prod_ubi');

        $this->numRows++;
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

    public function getCodigoEmpresa($valor)
    {
        $codigo = '';
        switch ($valor) {
            case 1:
                $codigo = 'OKC';
            break;
            case 2:
                $codigo = 'PYC';
            break;
            case 3:
                $codigo = 'SVS';
            break;
            case 4:
                $codigo = 'RBDB';
            break;
            case 5:
                $codigo = 'JEDR';
            break;
            case 6:
                $codigo = 'PTEC';
            break;
        }
        return $codigo;
    }
}
