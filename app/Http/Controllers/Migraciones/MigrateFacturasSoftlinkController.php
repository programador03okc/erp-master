<?php

namespace App\Http\Controllers\Migraciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tesoreria\TipoCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MigrateFacturasSoftlinkController extends Controller
{
    //Valida el estado de la orden en softlink
    public function enviarComprobanteSoftlink($id_doc_com)
    {
        try {
            DB::beginTransaction();

            $doc = DB::table('almacen.doc_com')
                ->select(
                    'doc_com.id_doc_com',
                    'doc_com.serie',
                    'doc_com.numero',
                    'doc_com.id_tp_doc',
                    'doc_com.fecha_emision',
                    'doc_com.fecha_vcmto',
                    'doc_com.fecha_registro',
                    'doc_com.moneda',
                    'doc_com.credito_dias',
                    'doc_com.id_sede',
                    'doc_com.id_doc_softlink',
                    'doc_com.sub_total',
                    'doc_com.total_igv',
                    'doc_com.total_a_pagar',
                    'doc_com.id_condicion_softlink',
                    'alm_almacen.codigo as codigo_almacen',
                    'adm_contri.nro_documento as ruc',
                    'adm_contri.razon_social',
                    'adm_contri.id_tipo_contribuyente',
                    'sis_identi.cod_softlink as cod_di',
                    'adm_empresa.codigo as codigo_emp',
                    'sis_usua.codvend_softlink',
                )
                ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'doc_com.id_proveedor')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
                ->leftJoin('almacen.alm_almacen', function ($join) {
                    $join->on('alm_almacen.id_sede', '=', 'doc_com.id_sede');
                    $join->where('alm_almacen.id_tipo_almacen', '=', 1);
                    $join->where('alm_almacen.estado', '!=', 7);
                    $join->orderBy('alm_almacen.codigo');
                    $join->limit(1);
                })
                ->leftjoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'doc_com.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'doc_com.usuario')
                ->where('doc_com.id_doc_com', $id_doc_com)
                ->first();

            $detalles = DB::table('almacen.doc_com_det')
                ->select(
                    'doc_com_det.*',
                    'alm_prod.id_producto',
                    'alm_prod.part_number',
                    'alm_prod.descripcion',
                    'alm_und_medida.abreviatura',
                    'alm_cat_prod.id_categoria',
                    'alm_cat_prod.descripcion as categoria',
                    'alm_subcat.id_subcategoria',
                    'alm_subcat.descripcion as subcategoria',
                    'alm_clasif.descripcion as clasificacion',
                    'doc_com.moneda as id_moneda',
                    'alm_prod.series',
                    'alm_prod.notas',
                )
                ->join('almacen.doc_com', 'doc_com.id_doc_com', '=', 'doc_com_det.id_doc')
                ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'doc_com_det.id_item')
                ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
                ->leftjoin('almacen.alm_tp_prod', 'alm_tp_prod.id_tipo_producto', '=', 'alm_cat_prod.id_tipo_producto')
                ->leftjoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_tp_prod.id_clasificacion')
                ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->where([
                    ['doc_com_det.id_doc', '=', $id_doc_com],
                    ['doc_com_det.estado', '!=', 7]
                ])
                ->get();

            $arrayRspta = [];

            if ($doc !== null && count($detalles) > 0) {

                if ($doc->id_tp_doc == 2) { //Factura

                    $empresas_soft = [
                        ['id' => 1, 'nombre' => 'OKC'],
                        ['id' => 2, 'nombre' => 'PYC'],
                        ['id' => 3, 'nombre' => 'SVS'],
                        ['id' => 4, 'nombre' => 'RBDB'],
                        ['id' => 5, 'nombre' => 'JEDR'],
                        ['id' => 6, 'nombre' => 'PTEC']
                    ];
                    // } else if ($doc->id_tp_doc == 3) { //Servicio

                    //     $empresas_soft = [
                    //         ['id' => 1, 'nombre' => 'OKC', 'cod_docu' => 'OS'],
                    //         ['id' => 2, 'nombre' => 'PYC', 'cod_docu' => 'OP'],
                    //         ['id' => 3, 'nombre' => 'SVS', 'cod_docu' => 'OV'],
                    //         ['id' => 4, 'nombre' => 'RBDB', 'cod_docu' => 'OR'],
                    //         ['id' => 5, 'nombre' => 'JEDR', 'cod_docu' => 'OJ'],
                    //         ['id' => 6, 'nombre' => 'PTEC', 'cod_docu' => 'OA']
                    //     ];
                }

                $cod_suc = '';
                $cod_docu = 'FA';

                foreach ($empresas_soft as $emp) {
                    if ($emp['nombre'] == $doc->codigo_emp) {
                        $cod_suc = $emp['id'];
                    }
                }

                //igv por defecto
                $igv = 18.00;
                //persona juridica x defecto
                $doc_tipo = ($doc->id_tipo_contribuyente !== null
                    ? ($doc->id_tipo_contribuyente <= 2 ? 2 : 1)
                    : 1);
                //por defecto ruc
                $cod = ($doc->cod_di !== null ? $doc->cod_di : '06');
                //obtiene o crea el proveedor
                $cod_auxi = (new MigrateOrdenSoftLinkController)->obtenerProveedor($doc->ruc, $doc->razon_social, $doc_tipo, $cod);
                //Calcular IGV
                // if ($doc->incluye_igv) {
                $mon_impto = (floatval($doc->total_a_pagar) * ($igv / 100));
                // } else {
                //     $mon_impto = 0;
                // }

                $fecha = date("Y-m-d", strtotime($doc->fecha_emision));

                //obtiene el tipo de cambio
                // $tp_cambio = DB::connection('soft')->table('tcambio')
                //     ->where([['dfecha', '<=', new Carbon($doc->fecha_emision)]])
                //     ->orderBy('dfecha', 'desc')
                //     ->first();
                $tp_cambio = TipoCambio::where([['moneda', '=', 2], ['fecha', '<=', $doc->fecha_emision]])
                    ->orderBy('fecha', 'DESC')->first();

                //////////////////////////
                $count = DB::connection('soft')->table('movimien')->count();
                //codificar segun criterio x documento
                $mov_id = $this->leftZero(10, (intval($count) + 1));

                $hoy = date('Y-m-d'); //Carbon::now()

                // if ($doc->codvend_softlink == '000055' || $doc->codvend_softlink == '000022') { //si es deza o dorado
                //     $yy = 'P022';
                // } else {
                //obtiene el año a 2 digitos y le aumenta 2 ceros adelante
                // $yy = $this->leftZero(4, intval(date('y', strtotime($hoy))));
                // }
                //obtiene el ultimo registro
                // $ult_mov = DB::connection('soft')->table('movimien')
                //     ->where([
                //         ['num_docu', '>', $yy . '0000000'],
                //         ['num_docu', '<', $yy . '9999999'],
                //         ['cod_suc', '=', $cod_suc],
                //         ['tipo', '=', 1], //ingreso
                //         ['cod_docu', '=', $cod_docu]
                //     ])
                //     ->orderBy('num_docu', 'desc')->first();
                //obtiene el correlativo
                // $num_ult_mov = substr(($ult_mov !== null ? $ult_mov->num_docu : 0), 4);
                //crea el correlativo del documento
                $nro_mov = $this->leftZero(7, (intval($doc->numero)));
                //anida el anio con el numero de documento
                // $num_docu = $yy . $nro_mov;
                $num_docu = $doc->serie . $nro_mov;

                $this->agregarComprobante(
                    $mov_id,
                    $cod_suc,
                    $doc,
                    $cod_docu,
                    $num_docu,
                    $fecha,
                    $cod_auxi,
                    $igv,
                    $mon_impto,
                    $tp_cambio,
                    $id_doc_com
                );

                $i = 0;
                foreach ($detalles as $det) {
                    $cod_prod = null;
                    //Obtiene y/o crea el producto
                    if ($det->id_producto !== null) {
                        $cod_prod = (new MigrateOrdenSoftLinkController)->obtenerProducto($det);
                    } else {
                        $cod_prod = '005675'; //OTROS SERVICIOS - DEFAULT
                    }
                    $this->agregarDetalleComprobante($det, $mov_id, $cod_prod, $cod_docu, $num_docu, $fecha, $igv, $i);
                    // $this->actualizaStockEnTransito($doc, $cod_prod, $det, $cod_suc);
                }
                // $this->agregarAudita($doc, $yy, $nro_mov);

                $soc = DB::connection('soft')->table('movimien')->where('mov_id', $mov_id)->first();
                $sdet = DB::connection('soft')->table('detmov')->where('mov_id', $mov_id)->get();

                $arrayRspta = array(
                    'tipo' => 'success',
                    'mensaje' => 'Se migró correctamente el comprobante ' . $doc->serie . $doc->numero . ' con id ' . $mov_id,
                    'orden_softlink' => $num_docu,
                    'ocSoftlink' => array('cabecera' => $soc, 'detalle' => $sdet),
                    'ocAgile' => array('cabecera' => $doc, 'detalle' => $detalles),
                );
                /////////////////////

            }

            DB::commit();
            return $arrayRspta;
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar el documento. Por favor intente de nuevo', 'error' => $e->getMessage());
        }
    }

    public function agregarComprobante($mov_id, $cod_suc, $doc, $cod_docu, $num_docu, $fecha, $cod_auxi, $igv, $mon_impto, $tp_cambio, $id_doc_com)
    {
        DB::connection('soft')->table('movimien')->insert(
            [
                'mov_id' => $mov_id,
                'tipo' => '1', //Compra 
                'cod_suc' => $cod_suc,
                'cod_alma' => $doc->codigo_almacen,
                'cod_docu' => $cod_docu,
                'num_docu' => $num_docu,
                'fec_docu' => $fecha,
                'fec_entre' => $fecha,
                'fec_vcto' => $fecha,
                'flg_sitpedido' => 0, //
                'cod_pedi' => '',
                'num_pedi' => '',
                'cod_auxi' => $cod_auxi,
                'cod_trans' => '00000',
                'cod_vend' => $doc->codvend_softlink,
                'tip_mone' => $doc->moneda,
                'impto1' => $igv,
                'impto2' => '0.00',
                'mon_bruto' => $doc->total_a_pagar,
                'mon_impto1' => $mon_impto,
                'mon_impto2' => '0.00',
                'mon_gravado' => '0.00',
                'mon_inafec' => '0.00',
                'mon_exonera' => '0.00',
                'mon_gratis' => '0.00',
                'mon_total' => ($doc->total_a_pagar + $mon_impto),
                'sal_docu' => '0.00',
                'tot_cargo' => '0.00',
                'tot_percep' => '0.00',
                'tip_codicion' => $doc->id_condicion_softlink,
                'txt_observa' => '',
                'flg_kardex' => 0,
                'flg_anulado' => 0,
                'flg_referen' => 0,
                'flg_percep' => 0,
                'cod_user' => $doc->codvend_softlink,
                'programa' => '',
                'txt_nota' => '',
                'tip_cambio' => $tp_cambio->venta, //tipo cambio venta
                'tdflags' => 'NSSNNSSNSS',
                'numlet' => '',
                'impdcto' => '0.0000',
                'impanticipos' => '0.0000',
                'registro' => new Carbon(), //date('Y-m-d H:i:s'),
                'tipo_canje' => '0',
                'numcanje' => '',
                'cobrobco' => 0,
                'ctabco' => '',
                'flg_qcont' => 0,
                'fec_anul' => '0000-00-00',
                'audit' => '2',
                'origen' => '',
                'tip_cont' => '',
                'tip_fact' => '',
                'contrato' => '',
                'idcontrato' => '',
                'canje_fact' => 0,
                'aceptado' => 0,
                'reg_conta' => 0,
                'mov_pago' => '',
                'ndocu1' => ($doc->credito_dias !== null ? $doc->credito_dias . ' DIAS' : ''),
                'ndocu2' => '',
                'ndocu3' => '',
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
        //Actualiza la oc softlink eb agile
        DB::table('almacen.doc_com')
            ->where('id_doc_com', $id_doc_com)
            ->update([
                'codigo_softlink' => $num_docu, //($yy . '-' . $nro_mov),
                'id_doc_softlink' => $mov_id
            ]);
    }

    public function agregarDetalleComprobante($det, $mov_id, $cod_prod, $cod_docu, $num_docu, $fecha, $igv, $i)
    {
        //cuenta los registros
        $count_det = DB::connection('soft')->table('detmov')->count();
        //aumenta uno y completa los 10 digitos
        $mov_det_id = $this->leftZero(10, (intval($count_det) + 1));
        //Obtiene y/o crea el producto
        // $cod_prod = $this->obtenerProducto($det);

        DB::connection('soft')->table('detmov')->insert(
            [
                'unico' => $mov_det_id,
                'mov_id' => $mov_id,
                'tipo' => '1', //Compra 
                'cod_docu' => $cod_docu,
                'num_docu' => $num_docu,
                'fec_pedi' => $fecha,
                'cod_auxi' => trim($det->abreviatura),
                'cod_prod' => $cod_prod,
                // 'nom_prod' => $det->descripcion,
                'nom_prod' => ($cod_prod == '005675' ? 'OTROS SERVICIOS - ' . $det->servicio_descripcion : ''),
                'can_pedi' => $det->cantidad,
                'sal_pedi' => $det->cantidad,
                'can_devo' => $i, //numeracion del item 
                'pre_prod' => ($det->precio_unitario !== null ? $det->precio_unitario : 0),
                'dscto_condi' => '0.000',
                'dscto_categ' => '0.000',
                'pre_neto' => ($det->precio_unitario !== null ? ($det->precio_unitario * $det->cantidad) : 0),
                'igv_inclu' => 0,
                'cod_igv' => '',
                'impto1' => $igv,
                'impto2' => '0.00',
                'imp_item' => ($det->precio_unitario !== null ? ($det->precio_unitario * $det->cantidad) : 0),
                'pre_gratis' => '0.0000',
                'descargo' => '*',
                'trecord' => '',
                'cod_model' => '',
                'flg_serie' => ($cod_prod == '005675' ? 0 : ($det->series ? 1 : 0)),
                'series' => '',
                'entrega' => 0,
                'notas' => '',
                'flg_percep' => 0,
                'por_percep' => 0,
                'mon_percep' => 0,
                'ok_stk' => 1,
                'ok_serie' => 1,
                'lStock' => 0,
                'no_calc' => 0,
                'promo' => 0,
                'seriesprod' => '',
                'pre_anexa' => 0,
                'dsctocompra' => 0,
                'cod_prov' => '',
                'costo_unit' => 0,
                'margen' => 0,
                'gasto1' => 0,
                'gasto2' => 0,
                'flg_detrac' => 0,
                'por_detrac' => 0,
                'cod_detrac' => '',
                'mon_detrac' => 0,
                'tipoprecio' => '6'
            ]
        );
        DB::table('almacen.doc_com_det')
            ->where('id_doc_det', $det->id_doc_det)
            ->update(['id_doc_det_softlink' => $mov_det_id]);
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

    public function agregarCondicionesSoftlink()
    {
        $docs = DB::table('almacen.doc_com')
            ->whereNull('id_sede')
            ->get();


        return $docs;
    }
}
