<?php

namespace App\Http\Controllers\Migraciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
                    'alm_prod.part_number',
                    'alm_prod.descripcion',
                    'alm_und_medida.abreviatura',
                    'alm_cat_prod.id_categoria',
                    'alm_cat_prod.descripcion as categoria',
                    'alm_subcat.id_subcategoria',
                    'alm_subcat.descripcion as subcategoria',
                    'alm_clasif.descripcion as clasificacion',
                    'doc_com.moneda',
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
                $cod_docu = '';

                foreach ($empresas_soft as $emp) {
                    if ($emp['nombre'] == $doc->codigo_emp) {
                        $cod_suc = $emp['id'];
                        $cod_docu = 'FA';
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
                $tp_cambio = DB::connection('soft')->table('tcambio')
                    ->where([['dfecha', '<=', new Carbon($doc->fecha_emision)]])
                    ->orderBy('dfecha', 'desc')
                    ->first();

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
                // $nro_mov = $this->leftZero(7, (intval($num_ult_mov) + 1));
                //anida el anio con el numero de documento
                // $num_docu = $yy . $nro_mov;
                $num_docu = $doc->serie . $doc->numero;

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
                // foreach ($detalles as $det) {
                //     $cod_prod = null;
                //     //Obtiene y/o crea el producto
                //     if ($det->id_producto !== null) {
                //         $cod_prod = $this->obtenerProducto($det);
                //     } else {
                //         $cod_prod = '005675'; //OTROS SERVICIOS - DEFAULT
                //     }
                //     $this->agregarDetalleOrden($det, $mov_id, $cod_prod, $cod_docu, $num_docu, $fecha, $igv, $i);
                //     $this->actualizaStockEnTransito($doc, $cod_prod, $det, $cod_suc);
                // }
                // $this->agregarAudita($doc, $yy, $nro_mov);

                $soc = DB::connection('soft')->table('movimien')->where('mov_id', $mov_id)->first();
                $sdet = DB::connection('soft')->table('detmov')->where('mov_id', $mov_id)->get();

                $arrayRspta = array(
                    'tipo' => 'success',
                    'mensaje' => 'Se migró correctamente la OC Nro. ' . $num_docu . ' con id ' . $mov_id,
                    'orden_softlink' => $num_docu, //($yy . '-' . $nro_mov),
                    'ocSoftlink' => array('cabecera' => $soc, 'detalle' => $sdet),
                    'ocAgile' => array('cabecera' => $doc, 'detalle' => $detalles),
                );
                /////////////////////

            }

            DB::commit();
            return $arrayRspta;
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage());
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
                'txt_observa' => '', //($doc->observacion !== null ? $doc->observacion : ''),
                'flg_kardex' => 0,
                'flg_anulado' => 0,
                'flg_referen' => 0,
                'flg_percep' => 0,
                'cod_user' => $doc->codvend_softlink,
                'programa' => '',
                'txt_nota' => '',
                'tip_cambio' => $tp_cambio->cambio3, //tipo cambio venta
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
                'flg_logis' => 1,
                'cod_recep' => '',
                'flg_aprueba' => 0,
                'fec_aprueba' => '0000-00-00 00:00:00.000000',
                'flg_limite' => 0,
                'fecpago' => '0000-00-00',
                'imp_comi' => '0.00',
                'ptosbonus' => '0',
                'canjepedtran' => 0,
                'cod_clasi' => 1, //mercaderias
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
