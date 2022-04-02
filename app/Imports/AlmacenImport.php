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
    public $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if ($this->type == 1) {
                $query = DB::table('almacen.alm_almacen')->where('codigo', $row['cod_alma'])->where('estado', 1)->count();

                if ($query == 0) {
                    $descripcion = trim($row['descripcion']);
                    $cod_empresa = $this->getCodigoEmpresa($row['cod_suc']);

                    $query_emp = DB::table('administracion.adm_empresa')->where('codigo', $cod_empresa)->first();
                        $id_empresa = $query_emp->id_empresa;
                    $query_sede = DB::table('administracion.sis_sede')->where('id_empresa', $id_empresa)->where('codigo', $row['sede']);

                    if ($query_sede->count() > 0) {
                        $id_sede = $query_sede->first()->id_sede;

                        DB::table('almacen.alm_almacen')->insertGetId([
                            'id_sede'           => $id_sede,
                            'descripcion'       => $descripcion,
                            'ubicacion'         => ($row['direccion'] != '') ? $row['direccion'] : null,
                            'id_tipo_almacen'   => 1,
                            'estado'            => 1,
                            'codigo'            => $row['cod_alma'],
                            'registrado_por'    => 1,
                            'fecha_registro'    => new Carbon()
                        ], 'id_almacen');
                        
                        $this->numRows++;
                    }
                }
            } else if ($this->type == 2) {
                $descripcion = trim($row['descripcion']);
                $query = DB::table('almacen.alm_cat_prod')->where('descripcion', $descripcion)->where('cod_softlink', $row['cod_cate'])->where('estado', 1)->count();

                if ($query == 0) {
                    DB::table('almacen.alm_cat_prod')->insertGetId([
                        'id_tipo_producto'  => 1,
                        'cod_softlink'      => $row['cod_cate'],
                        'descripcion'       => ($descripcion != '') ? $descripcion : null,
                        'estado'            => 1,
                        'fecha_registro'    => new Carbon()
                    ], 'id_categoria');

                    $this->numRows++;
                }
            } else if ($this->type == 3) {
                $descripcion = trim($row['descripcion']);
                $query = DB::table('almacen.alm_subcat')->where('descripcion', $descripcion)->where('cod_softlink', $row['cod_subc'])->where('estado', 1)->count();

                if ($query == 0) {
                    DB::table('almacen.alm_subcat')->insertGetId([
                        'cod_softlink'      => $row['cod_subc'],
                        'descripcion'       => ($descripcion != '') ? $descripcion : null,
                        'estado'            => 1,
                        'fecha_registro'    => new Carbon(),
                        'registrado_por'    => 1
                    ], 'id_subcategoria');

                    $this->numRows++;
                }
            }  else if ($this->type == 4) {
                $descripcion = trim($row['descripcion']);
                $query = DB::table('almacen.alm_und_medida')->where('descripcion', $descripcion)->where('cod_softlink', $row['cod_unid'])->where('estado', 1)->count();

                if ($query == 0) {
                    DB::table('almacen.alm_und_medida')->insertGetId([
                        'descripcion'   => $descripcion,
                        'abreviatura'   => ($descripcion != '') ? $descripcion : null,
                        'estado'        => 1,
                        'cod_softlink'  => $row['cod_unid']
                    ], 'id_unidad_medida');

                    $this->numRows++;
                }
            } else if ($this->type == 5) {
                $descripcion = trim($row['nom_prod']);
                $part_no = trim($row['cod_espe']);
                $query = DB::table('almacen.alm_prod')->where('descripcion', $descripcion)
                                ->where('cod_softlink', $row['cod_prod'])->orWhere('part_number', $part_no)->where('estado', 1)->count();

                if ($query == 0) {
                    $query_cla = DB::table('almacen.alm_clasif')->where('cod_softlink', $row['cod_clasi'])->first();
                    $query_cat = DB::table('almacen.alm_cat_prod')->where('cod_softlink', $row['cod_cate'])->first();
                    $query_sub = DB::table('almacen.alm_subcat')->where('cod_softlink', $row['cod_subc'])->first();
                    $query_und = DB::table('almacen.alm_und_medida')->where('cod_softlink', $row['cod_unid'])->first();

                    $id_cla = ($query_cla != '') ? $query_cla->id_clasificacion : null;
                    $id_cat = ($query_cat != '') ? $query_cat->id_categoria : null;
                    $id_sub = ($query_sub != '') ? $query_sub->id_subcategoria : null;
                    $id_und = ($query_und != '') ? $query_und->id_unidad_medida : null;

                    $flg_serie = ($row['flg_serie'] == 't') ? true : false;
                    $flg_afect = ($row['flg_afecto_igv'] == 't') ? true : false;

                    $producto = DB::table('almacen.alm_prod')->insertGetId([
                        'cod_softlink'      => $row['cod_prod'],
                        'part_number'       => $part_no,
                        'id_subcategoria'   => $id_sub,
                        'id_clasif'         => $id_cla,
                        'descripcion'       => $descripcion,
                        'id_unidad_medida'  => $id_und,
                        'series'            => $flg_serie,
                        'afecto_igv'        => $flg_afect,
                        'estado'            => 1,
                        'fecha_registro'    => date('Y-m-d', strtotime($row['ult_edicion'])),
                        'id_moneda'         => ($row['tip_moneda'] != '') ? $row['tip_moneda'] : null,
                        'notas'             => ($row['txt_observa'] != '') ? $row['txt_observa'] : null,
                        'id_categoria'      => $id_cat,
                        'id_usuario'        => 1,
                        'afecta_kardex'     => true,
                        'cod_cate'          => $row['cod_cate'],
                        'cod_subcate'       => $row['cod_subc'],
                        'cod_unid'          => $row['cod_unid']
                    ], 'id_producto');
                    
                    $code = $this->leftZero(7, $producto);
                    DB::table('almacen.alm_prod')->where('id_producto', $producto)->update(['codigo' => $code]);

                    $this->numRows++;
                }
            } else if ($this->type == 6) {
                $query_alm = DB::table('almacen.alm_almacen')->where('codigo', $row['cod_alma'])->first();
                $query_pro = DB::table('almacen.alm_prod')->where('cod_softlink', $row['cod_prod'])->first();

                $id_alm = ($query_alm != '') ? $query_alm->id_almacen : null;
                $id_pro = ($query_pro != '') ? $query_pro->id_producto : null;

                DB::table('almacen.alm_prod_serie')->insertGetId([
                    'id_prod'           => $id_pro,
                    'serie'             => $row['serie'],
                    'estado'            => 1,
                    'fecha_registro'    => new Carbon(),
                    'id_almacen'        => $id_alm
                ], 'id_prod_serie');

                $this->numRows++;
            } else if ($this->type == 7) {
                $query_alm = DB::table('almacen.alm_almacen')->where('codigo', $row['cod_alma'])->first();
                $query_pro = DB::table('almacen.alm_prod')->where('cod_softlink', $row['cod_prod'])->first();

                $id_alm = ($query_alm != '') ? $query_alm->id_almacen : null;
                $id_pro = ($query_pro != '') ? $query_pro->id_producto : null;

                DB::table('almacen.alm_prod_ubi')->insertGetId([
                    'id_producto'       => $id_pro,
                    'stock'             => $row['stock'],
                    'estado'            => 1,
                    'fecha_registro'    => new Carbon(),
                    'costo_promedio'    => $row['costo_promedio'],
                    'id_almacen'        => $id_alm,
                    'valorizacion'      => $row['valorizacion']
                ], 'id_prod_ubi');

                $this->numRows++;
            }
        }
    }

    public function getRowCount(): int
    {
        return $this->numRows;
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
