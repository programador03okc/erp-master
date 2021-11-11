<?php

namespace App\Http\Controllers\Migraciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MigrateOrdenSoftLinkController extends Controller
{
    public function migrarOrdenCompra($id_orden_compra)
    {
        try {
            DB::beginTransaction();

            $oc = DB::table('logistica.log_ord_compra')
                ->select(
                    'log_ord_compra.codigo',
                    'log_ord_compra.fecha',
                    'log_ord_compra.fecha_registro',
                    'log_ord_compra.id_moneda',
                    'log_ord_compra.id_sede',
                    'alm_almacen.codigo as codigo_almacen',
                    'log_ord_compra.observacion',
                    'adm_contri.nro_documento as ruc',
                    'adm_contri.razon_social',
                    'adm_contri.id_tipo_contribuyente',
                    'sis_identi.cod_softlink as cod_di',
                    'adm_empresa.codigo as codigo_emp',
                    'sis_usua.codvend_softlink',
                    DB::raw("(SELECT SUM(log_det_ord_compra.precio * log_det_ord_compra.cantidad) FROM logistica.log_det_ord_compra 
                      WHERE log_det_ord_compra.estado <> 7 
                      AND log_det_ord_compra.id_orden_compra = log_ord_compra.id_orden_compra) 
                      as total_precio")
                )
                ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
                // ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'log_ord_compra.id_sede')
                ->leftJoin('almacen.alm_almacen', function ($join) {
                    $join->on('alm_almacen.id_sede', '=', 'log_ord_compra.id_sede');
                    $join->where('alm_almacen.estado', '!=', 7);
                    $join->orderBy('alm_almacen.codigo');
                    $join->limit(1);
                })
                ->leftjoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
                ->where('id_orden_compra', $id_orden_compra)
                ->first();

            $msj = '';


            if ($oc !== null) {
                //persona juridica x defecto
                $doc_tipo = ($oc->id_tipo_contribuyente !== null
                    ? ($oc->id_tipo_contribuyente <= 2 ? 2 : 1)
                    : 1);
                //por defecto ruc
                $cod = ($oc->cod_di !== null ? $oc->cod_di : '06');
                //obtiene o crea el proveedor
                $cod_auxi = $this->obtenerProveedor($oc->ruc, $oc->razon_social, $doc_tipo, $cod);

                $empresas_soft = [
                    ['id' => 1, 'nombre' => 'OKC'],
                    ['id' => 2, 'nombre' => 'PYC'],
                    ['id' => 3, 'nombre' => 'SVS'],
                    ['id' => 4, 'nombre' => 'JEDR'],
                    ['id' => 5, 'nombre' => 'RBDB'],
                    ['id' => 6, 'nombre' => 'PTEC']
                ];
                $cod_suc = '';
                foreach ($empresas_soft as $emp) {
                    if ($emp['nombre'] == $oc->codigo_emp) {
                        $cod_suc = $emp['id'];
                    }
                }
                $count = DB::connection('soft')->table('movimien')->count();
                //codificar segun criterio x documento
                $mov_id = '_OC' . $this->leftZero(7, (intval($count) + 1));
                $fecha = date('Y-m-d');
                //obtiene el año a 2 digitos y le aumenta 2 ceros adelante
                $yy = $this->leftZero(4, intval(date('y', strtotime($fecha))));
                //busca segun oc de lima, las oc de ilo inician con P=>P021
                $count_mov = DB::connection('soft')->table('movimien')
                    ->where([['num_docu', 'like', $yy . '%'], ['cod_docu', '=', 'OC']])
                    ->count();
                //crea el correlativo del documento
                $nro_mov = $this->leftZero(7, (intval($count_mov) + 1));
                //anida el numero de documento
                $num_docu = $yy . $nro_mov;

                $mon_impto = (floatval($oc->total_precio) * 0.18);

                // $msj = 'Se migró correctamente. La OC ' . $yy . '-' . $nro_mov . ' con id ' . $mov_id;

                $fecha = date("Y-m-d", strtotime($oc->fecha));
                // return response()->json(['oc' => $oc, 'cod_suc' => $cod_suc, 'cod_auxi' => $cod_auxi]);

                DB::connection('soft')->table('movimien')->insert(
                    [
                        'mov_id' => $mov_id,
                        'tipo' => '1', //Compra 
                        'cod_suc' => $cod_suc,
                        'cod_alma' => $oc->codigo_almacen,
                        'cod_docu' => 'OC',
                        'num_docu' => $num_docu,
                        'fec_docu' => $fecha,
                        'fec_entre' => $fecha,
                        'fec_vcto' => $fecha,
                        'flg_sitpedido' => 0,
                        'cod_pedi' => '',
                        'num_pedi' => '',
                        'cod_auxi' => $cod_auxi,
                        'cod_trans' => '00000',
                        'cod_vend' => $oc->codvend_softlink,
                        'tip_mone' => $oc->id_moneda,
                        'impto1' => '18.00',
                        'impto2' => '0.00',
                        'mon_bruto' => $oc->total_precio,
                        'mon_impto1' => $mon_impto,
                        'mon_impto2' => '0.00',
                        'mon_gravado' => '0.00',
                        'mon_inafec' => '0.00',
                        'mon_exonera' => '0.00',
                        'mon_gratis' => '0.00',
                        'mon_total' => ($oc->total_precio + $mon_impto),
                        'sal_docu' => '0.00',
                        'tot_cargo' => '0.00',
                        'tot_percep' => '0.00',
                        'tip_codicion' => '02', //REvisar la condicion
                        'txt_observa' => ($oc->observacion !== null ? $oc->observacion : ''),
                        'flg_kardex' => 0,
                        'flg_anulado' => 0,
                        'flg_referen' => 0,
                        'flg_percep' => 0,
                        'cod_user' => $oc->codvend_softlink,
                        'programa' => '',
                        'txt_nota' => '',
                        'tip_cambio' => '0.000', //Revisar
                        'tdflags' => 'NSSNNSSNSN',
                        'numlet' => '',
                        'impdcto' => '0.0000',
                        'impanticipos' => '0.0000',
                        'registro' => date('Y-m-d H:i:s'),
                        'tipo_canje' => '0',
                        'numcanje' => '',
                        'cobrobco' => 0,
                        'ctabco' => '',
                        'flg_qcont' => '',
                        'fec_anul' => '0000-00-00',
                        'audit' => '2',
                        'origen' => '',
                        'tip_cont' => '',
                        'tip_fact' => '',
                        'contrato' => '',
                        'idcontrato' => '',
                        'canje_fact' => 0,
                        'aceptado' => 0,
                        'reg_conta' => '0',
                        'mov_pago' => '',
                        'ndocu1' => '',
                        'ndocu2' => '',
                        'ndocu3' => $oc->codigo,
                        'flg_logis' => 0,
                        'cod_recep' => '',
                        'flg_aprueba' => 0,
                        'fec_aprueba' => '0000-00-00 00:00:00.000000',
                        'flg_limite' => 0,
                        'fecpago' => '0000-00-00',
                        'imp_comi' => '0.00',
                        'ptosbonus' => '0',
                        'canjepedtran' => 0,
                        'cod_clasi' => '',
                        'doc_elec' => '',
                        'cod_nota' => '',
                        'hashcpe' => '',
                        'flg_sunat_acep' => 0,
                        'flg_sunat_anul' => 0,
                        'flg_sunat_mail' => 0,
                        'flg_sunat_webs' => 0,
                        'mov_id_baja' => '',
                        'mov_id_resu_bv' => '',
                        'mov_id_resu_ci' => '',
                        'flg_guia_traslado' => 0,
                        'flg_anticipo_doc' => 0,
                        'flg_anticipo_reg' => 0,
                        'doc_anticipo_id' => '',
                        'flg_emi_itinerante' => 0,
                        'placa' => ''
                    ]
                );

                $detalles = DB::table('logistica.log_det_ord_compra')
                    ->select(
                        'log_det_ord_compra.*',
                        'alm_prod.part_number',
                        'alm_prod.descripcion',
                        'alm_und_medida.abreviatura',
                        'alm_cat_prod.descripcion as categoria',
                        'alm_subcat.descripcion as subcategoria',
                        'alm_clasif.descripcion as clasificacion',
                        'log_ord_compra.id_moneda',
                        'alm_prod.series',
                        'alm_prod.notas'
                    )
                    ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
                    ->join('almacen.alm_prod', 'alm_prod.id_producto', '=', 'log_det_ord_compra.id_producto')
                    ->join('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
                    ->join('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                    ->join('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_prod.id_clasif')
                    ->join('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                    ->where('log_det_ord_compra.id_orden_compra', $id_orden_compra)
                    ->get();

                $i = 0;

                foreach ($detalles as $det) {
                    $i++;
                    //cuenta los registros
                    $count_det = DB::connection('soft')->table('detmov')->count();
                    //aumenta uno y completa los 10 digitos
                    $mov_det_id = $this->leftZero(10, (intval($count_det) + 1));
                    //Obtiene y/o crea el producto
                    $cod_prod = $this->obtenerProducto($det);

                    DB::connection('soft')->table('detmov')->insert(
                        [
                            'unico' => $mov_det_id,
                            'mov_id' => $mov_id,
                            'tipo' => '2', //Ventas 
                            'cod_docu' => 'NP',
                            'num_docu' => $num_docu,
                            'fec_pedi' => $fecha,
                            'cod_auxi' => trim($det->abreviatura),
                            'cod_prod' => $cod_prod,
                            'nom_prod' => $det->descripcion,
                            'can_pedi' => $det->cantidad,
                            'sal_pedi' => '0.0000',
                            'can_devo' => $i, //numeracion del item 
                            'pre_prod' => ($det->precio !== null ? $det->precio : 0),
                            'dscto_condi' => '0.000',
                            'dscto_categ' => '0.000',
                            'pre_neto' => ($det->precio !== null ? ($det->precio * $det->cantidad) : 0),
                            'igv_inclu' => '0',
                            'cod_igv' => '',
                            'impto1' => '18.00',
                            'impto2' => '0.00',
                            'imp_item' => ($det->precio !== null ? ($det->precio * $det->cantidad) : 0),
                            'pre_gratis' => '0.0000',
                            'descargo' => '*',
                            'trecord' => '',
                            'cod_model' => '',
                            'flg_serie' => '1',
                            'series' => '',
                            'entrega' => '0',
                            'notas' => '',
                            'flg_percep' => '0',
                            'por_percep' => '0.000',
                            'mon_percep' => '0.000',
                            'ok_stk' => '1',
                            'ok_serie' => ($det->series ? '1' : '0'),
                            'lStock' => '0',
                            'no_calc' => '0',
                            'promo' => '1',
                            'seriesprod' => '',
                            'pre_anexa' => '0.0000',
                            'dsctocompra' => '0.000',
                            'cod_prov' => '',
                            'costo_unit' => '0.000000',
                            'margen' => '0.00',
                            'gasto1' => '0.00',
                            'gasto2' => '0.00',
                            'flg_detrac' => '0',
                            'por_detrac' => '0.000',
                            'cod_detrac' => '',
                            'mon_detrac' => '0.0000',
                            'tipoprecio' => '8'
                        ]
                    );
                }
            }

            DB::commit();
            // return response()->json($msj);
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se migró correctamente. La OC ' . $yy . '-' . $nro_mov . ' con id ' . $mov_id), 200);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage()), 200);
        }
    }

    public function obtenerProducto($det)
    {
        //Verifica si esxiste el producto
        $prod = null;
        if ($det->part_number !== null && $det->part_number !== '') {
            $prod = DB::connection('soft')->table('sopprod')
                ->select('cod_prod')
                ->where('cod_espe', trim($det->part_number))
                ->first();
        } else if ($det->descripcion !== null && $det->descripcion !== '') {
            $prod = DB::connection('soft')->table('sopprod')
                ->select('cod_prod')
                ->where('nom_prod', trim($det->descripcion))
                ->first();
        }
        $cod_prod = null;
        //Si existe copia el cod_prod
        if ($prod !== null) {
            $cod_prod = $prod->cod_prod;
        } //Si no existe, genera el producto
        else {
            //obtiene el sgte codigo
            $ultimo = DB::connection('soft')->table('sopprod')
                ->select('cod_prod')
                ->where([['cod_prod', '!=', 'TEXTO']])
                ->orderBy('cod_prod', 'desc')
                ->first();

            $cod_prod = $this->leftZero(6, (intval($ultimo->cod_prod) + 1));
            //verifica si tiene clasificacion
            $clasif = DB::connection('soft')->table('soplinea')
                ->select('cod_line')
                ->where('nom_line', trim($det->clasificacion))
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
                        'nom_line' => trim($det->clasificacion),
                        'cod_sunat' => '',
                        'cod_osce' => ''
                    ]
                );
            }
            //verifica si existe categoria
            $cate = DB::connection('soft')->table('sopsub1')
                ->select('cod_sub1')
                ->where('nom_sub1', trim($det->categoria))
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
                        'nom_sub1' => trim($det->categoria),
                        'por_dcto' => '0.00',
                        'num_corr' => '0'
                    ]
                );
            }
            //verifica si existe subcategoria
            $subcate = DB::connection('soft')->table('sopsub2')
                ->select('cod_sub2')
                ->where('nom_sub2', trim($det->subcategoria))
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
                        'nom_sub2' => trim($det->subcategoria),
                        'por_adic' => '0.00',
                        'cod_sub1' => '',
                        'id_manufacturer' => '0'
                    ]
                );
            }
            //verifica si existe unidad medida
            $unidad = DB::connection('soft')->table('unidades')
                ->select('cod_unid')
                ->where('nom_unid', trim($det->abreviatura))
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
                        'nom_unid' => trim($det->abreviatura),
                        'fac_unid' => '1'
                    ]
                );
            }

            DB::connection('soft')->table('sopprod')->insert(
                [
                    'cod_prod' => $cod_prod,
                    'cod_clasi' => $cod_clasi,
                    'cod_cate' => $cod_cate,
                    'cod_subc' => $cod_subc,
                    'cod_prov' => '',
                    'cod_espe' => trim($det->part_number),
                    'cod_sunat' => '',
                    'nom_prod' => trim($det->descripcion),
                    'cod_unid' => $cod_unid,
                    'nom_unid' => trim($det->abreviatura),
                    'fac_unid' => '1',
                    'kardoc_costo' => '0.000',
                    'kardoc_stock' => '0.000',
                    'kardoc_ultingfec' => '0000-00-00',
                    'kardoc_ultingcan' => '0.000',
                    'kardoc_unico' => '',
                    'fec_ingre' => date('Y-m-d'),
                    'flg_descargo' => '1',
                    'tip_moneda' => $det->id_moneda,
                    'flg_serie' => ($det->series ? '1' : '0'), //Revisar
                    'txt_observa' => ($det->notas !== null ? $det->notas : ''),
                    'flg_afecto' => '1',
                    'flg_suspen' => '0',
                    'apl_lista' => '3',
                    'foto' => '',
                    'web' => '',
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
                    'cta_s_caja' => '00',
                    'cta_d_caja' => '',
                    'cod_ubic' => '',
                    'peso' => '0.000',
                    'flg_percep' => '0',
                    'por_percep' => '0.000',
                    'gasto' => '0',
                    'dsctocompra' => '0.000',
                    'dsctocompra2' => '0.000',
                    'cod_promo' => '',
                    'can_promo' => '0.000',
                    'ult_edicion' => date('Y-m-d H:i:s'),
                    'ptosbonus' => '0',
                    'bonus_moneda' => '0',
                    'bonus_importe' => '0.00',
                    'flg_detrac' => '0',
                    'por_detrac' => '0.000',
                    'cod_detrac' => '',
                    'mon_detrac' => '0.0000',
                    'largo' => '0.000',
                    'ancho' => '0.000',
                    'area' => '0.000',
                    'aweb' => '0',
                    'id_product' => '0',
                    'width' => '0.000000',
                    'height' => '0.000000',
                    'depth' => '0.000000',
                    'weight' => '0.000000',
                    'costo_adicional' => '0.00'
                ]
            );
        }
        return $cod_prod;
    }

    public function obtenerProveedor($nro_documento, $razon_social, $doc_tipo, $cod_di)
    {
        $proveedor = DB::connection('soft')->table('auxiliar')
            ->select('cod_auxi')
            ->where([
                ['ruc_auxi', '=', $nro_documento],
                ['tip_auxi', '=', 'P']
            ])
            ->first();

        $cod_auxi = null;

        if ($proveedor == null) {

            $mayor = DB::connection('soft')->table('auxiliar')
                ->select('cod_auxi')
                ->where([
                    ['cod_auxi', '!=', 'TRANSF'],
                    ['tip_auxi', '=', 'P']
                ])
                ->orderBy('cod_auxi', 'desc')
                ->first();

            $cod_auxi = $this->leftZero(6, (intval($mayor->cod_auxi) + 1));


            DB::connection('soft')->table('auxiliar')->insert(
                [
                    'tip_auxi' => 'C',
                    'cod_auxi' => $cod_auxi,
                    'nom_auxi' => $razon_social,
                    'nom_contac' => '',
                    'car_contac' => '',
                    'dir_auxi' => '', //($req->direccion_entrega !== null ? $req->direccion_entrega : ''),
                    'dir_entre' => '',
                    'tel_auxi' => '', //($req->telefono !== null ? $req->telefono : ''),
                    'fax_auxi' => '',
                    'doc_tipo' => $doc_tipo,
                    'ruc_auxi' => $nro_documento,
                    'doc_auxi' => '',
                    'est_auxi' => '',
                    'hijos_auxi' => '0',
                    'sexo_auxi' => '',
                    'fnac_auxi' => '0000-00-00',
                    'cod_di' => $cod_di,
                    'cre_moneda' => '0',
                    'max_credi' => '0.0000',
                    'util_credi' => '0.0000',
                    'fec_credi' => '0000-00-00',
                    'nom_aval' => '',
                    'ruc_aval' => '',
                    'dir_aval' => '',
                    'tel_aval' => '',
                    'fax_aval' => '',
                    'doc_aval' => '',
                    'cod_zona' => '000',
                    'tip_clasi' => '00',
                    'cta1' => '',
                    'cta2' => '',
                    'codvend' => '',
                    'condicion' => '',
                    'aux_qcont' => '',
                    'website' => '',
                    'email' => '', //($req->email !== null ? $req->email : ''),
                    'visita' => '',
                    'notas' => '',
                    'notas2' => '',
                    'v_tipo' => '',
                    'v_nombre' => '',
                    'v_numero' => '',
                    'v_interior' => '',
                    'v_zona' => '',
                    'v_distrito' => '',
                    'v_provincia' => '',
                    'v_depart' => '',
                    'cta3' => '',
                    'cta4' => '',
                    'fec_llama' => '0000-00-00',
                    'asunto' => '0',
                    'flg_percep' => '0',
                    'flg_reten' => '',
                    'por_reten' => '0',
                    'flg_baja' => '0',
                    'fec_baja' => '0000-00-00',
                    'dias_cred' => '0',
                    'tipo_auxi' => '0',
                    'ult_edicion' => date('Y-m-d H:i:s'),
                    'ptosbonus' => '0',
                    'canje_bonus' => '0000-00-00',
                    'id_pais' => 'PE',
                    'cta_detrac' => ''
                ]
            );
        } else {
            $cod_auxi = $proveedor->cod_auxi;
        }
        return $cod_auxi;
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
}
