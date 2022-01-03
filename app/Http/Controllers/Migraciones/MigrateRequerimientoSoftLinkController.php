<?php

namespace App\Http\Controllers\Migraciones;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class MigrateRequerimientoSoftLinkController extends Controller
{
    public function soft_tipos_cambio()
    {
        DB::connection('soft')->table('tcambio')
            ->insert([
                'dfecha' => '2021-10-18',
                'cambio' => '3.237',
                'cambio2' => '3.237',
                'cambio3' => '3.237'
            ]);
        $data = DB::connection('soft')->table('tcambio')
            ->get();
        return $data;
    }

    public function migrarOCC($id_requerimiento)
    {
        try {
            DB::beginTransaction();

            $req = DB::table('almacen.alm_req')
                ->select(
                    'alm_req.fecha_requerimiento',
                    'alm_req.fecha_entrega',
                    'alm_req.direccion_entrega',
                    'alm_req.telefono',
                    'alm_req.email',
                    'alm_req.id_moneda',
                    'alm_req.observacion',
                    'alm_req.id_occ_softlink',
                    'rrhh_perso.nro_documento as dni',
                    DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
                    'adm_contri.nro_documento as ruc',
                    'adm_contri.razon_social',
                    'sis_identi.cod_softlink as cod_di',
                    'adm_empresa.codigo as codigo_emp',
                    'alm_almacen.codigo as codigo_almacen',
                    'sis_usua.codvend_softlink',
                    DB::raw("(SELECT SUM(alm_det_req.precio_unitario * alm_det_req.cantidad) FROM almacen.alm_det_req 
                      WHERE alm_det_req.estado <> 7 
                      AND alm_det_req.id_requerimiento = alm_req.id_requerimiento) 
                      as total_precio"),
                    'oc_propias_view.nro_orden',
                    'oportunidades.codigo_oportunidad'
                )
                ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
                ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
                ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
                ->leftjoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'alm_req.id_persona')
                ->leftjoin('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'alm_req.id_cliente')
                ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
                ->leftjoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')->leftjoin('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'alm_req.id_almacen')
                ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'alm_req.id_empresa')
                ->leftjoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
                ->where([['alm_req.id_requerimiento', '=', $id_requerimiento]])
                ->first();

            $detalles = DB::table('almacen.alm_det_req')
                ->select(
                    'alm_det_req.*',
                    'alm_prod.part_number as part_number_prod',
                    'alm_prod.descripcion as descripcion_prod',
                    'alm_und_medida.abreviatura',
                    'alm_cat_prod.descripcion as categoria',
                    'alm_subcat.descripcion as subcategoria',
                    'alm_clasif.descripcion as clasificacion',
                    'alm_cat_prod.id_categoria',
                    'alm_subcat.id_subcategoria',
                    'alm_prod.id_moneda',
                    'alm_prod.series',
                    'alm_prod.notas'
                )
                ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
                ->leftjoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
                ->leftjoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                ->leftjoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_prod.id_clasif')
                ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->where([
                    ['alm_det_req.id_requerimiento', '=', $id_requerimiento],
                    ['alm_det_req.entrega_cliente', '=', true]
                ])
                ->get();

            $arrayRspta = [];

            if ($req !== null && count($detalles) > 0) {

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
                    if ($emp['nombre'] == $req->codigo_emp) {
                        $cod_suc = $emp['id'];
                    }
                }
                //igv por defecto
                $igv = 18.00;
                //Registro del cliente
                $nro_documento = ($req->ruc !== null ? $req->ruc : ($req->dni !== null ? $req->dni : ''));
                $razon_social = ($req->razon_social !== null ? $req->razon_social : $req->nombre_persona);
                //persona juridica o natural
                $doc_tipo = ($req->razon_social !== null ? 1 : 2);
                $cod = ($req->ruc !== null ? '06' : $req->cod_di);
                //obtiene o crea cliente
                $cod_auxi = $this->obtenerCliente($nro_documento, $razon_social, $doc_tipo, $cod);

                //calcula IGV
                $mon_impto = (floatval($req->total_precio) * ($igv / 100));
                //obtiene el tipo de cambio
                $tp_cambio = DB::connection('soft')->table('tcambio')
                    ->where([['dfecha', '<=', new Carbon($req->fecha_requerimiento)]])
                    ->orderBy('dfecha', 'desc')
                    ->first();

                if ($req->id_occ_softlink !== null) {
                    //obtiene oc softlink
                    $occ_softlink = DB::connection('soft')->table('movimien')->where('mov_id', $req->id_occ_softlink)->first();

                    if ($occ_softlink !== null) {
                        //pregunta si fue referenciado
                        $fac_referen = DB::connection('soft')->table('movimien')
                            ->where([
                                ['cod_pedi', '=', $occ_softlink->cod_docu],
                                ['num_pedi', '=', $occ_softlink->num_docu],
                                ['cod_suc', '=', $occ_softlink->cod_suc],
                                ['flg_anulado', '=', 0]
                            ])
                            ->first();
                        if ($fac_referen !== null) {
                            $arrayRspta = array(
                                'tipo' => 'warning',
                                'mensaje' => 'Ésta OCC ya fue referenciada en Softlink.',
                                'occSoftlink' => array('cabecera' => $occ_softlink),
                                'reqAgile' => array('cabecera' => $req),
                            );
                        }
                        //pregunta si fue anulada en softlink
                        else if ($occ_softlink->flg_anulado == 1) {
                            $arrayRspta = array(
                                'tipo' => 'warning',
                                'mensaje' => 'Ésta OCC ya fue anulada en Softlink.',
                                'ocSoftlink' => array('cabecera' => $occ_softlink),
                                'ocAgile' => array('cabecera' => $req),
                            );
                        } else {
                            //actualiza orden
                            DB::connection('soft')->table('movimien')
                                ->where('mov_id', $occ_softlink->mov_id)
                                ->update(
                                    [
                                        'cod_suc' => $cod_suc,
                                        'cod_alma' => $req->codigo_almacen,
                                        'fec_docu' => $req->fecha_requerimiento,
                                        'fec_entre' => $req->fecha_entrega,
                                        'fec_vcto' => $req->fecha_requerimiento,
                                        'cod_auxi' => $cod_auxi,
                                        'cod_vend' => $req->codvend_softlink,
                                        'tip_mone' => $req->id_moneda,
                                        // 'tip_codicion' => $oc->id_condicion_softlink,
                                        'impto1' => $igv,
                                        'mon_bruto' => $req->total_precio,
                                        'mon_impto1' => $mon_impto,
                                        'mon_total' => ($req->total_precio + $mon_impto),
                                        'txt_observa' => ($req->observacion !== null ? $req->observacion : ''),
                                        'cod_user' => $req->codvend_softlink,
                                        'tip_cambio' => $tp_cambio->cambio3, //tipo cambio venta
                                        'ndocu1' => ($req->nro_orden !== null ? $req->nro_orden : ''),
                                        'ndocu2' => ($req->direccion_entrega !== null ? $req->direccion_entrega : ''),
                                        'ndocu3' => ($req->codigo_oportunidad !== null ? $req->codigo_oportunidad : ''),
                                    ]
                                );

                            $i = 0;
                            foreach ($detalles as $det) {
                                $i++;
                                //Obtiene y/o crea el producto
                                if ($det->id_producto !== null) {
                                    $cod_prod = $this->obtenerProducto($det);
                                } else {
                                    $cod_prod = '005675'; //OTROS SERVICIOS - DEFAULT
                                }

                                if ($det->id_occ_det_softlink !== null) {
                                    //actualiza el detalle
                                    DB::connection('soft')->table('detmov')
                                        ->where('unico', $det->id_occ_det_softlink)
                                        ->update([
                                            'fec_pedi' => $req->fecha_requerimiento,
                                            'cod_auxi' => ($cod_prod == '005675' ? 'SERV.' : trim($det->abreviatura)),
                                            'cod_prod' => $cod_prod,
                                            'nom_prod' => ($cod_prod == '005675' ? 'OTROS SERVICIOS - ' . $det->descripcion : $det->descripcion_prod),
                                            'can_pedi' => $det->cantidad,
                                            'sal_pedi' => $det->cantidad,
                                            'can_devo' => $i, //numeracion del item 
                                            'pre_prod' => ($det->precio_unitario !== null ? $det->precio_unitario : 0),
                                            'pre_neto' => ($det->precio_unitario !== null ? ($det->precio_unitario * $det->cantidad) : 0),
                                            'impto1' => $igv,
                                            'imp_item' => ($det->precio_unitario !== null ? ($det->precio_unitario * $det->cantidad) : 0),
                                            'flg_serie' => ($cod_prod == '005675' ? 0 : ($det->series ? 1 : 0)),
                                        ]);
                                } else {
                                    $this->agregarDetalleOCC($det, $occ_softlink->mov_id, $cod_prod, $occ_softlink->num_docu, $req->fecha_requerimiento, $igv, $i);
                                }
                            }
                            $arrayRspta = array(
                                'tipo' => 'success',
                                'mensaje' => 'Se actualizó ésta OCC en softlink. Con Nro. ' . $occ_softlink->num_docu . ' con id ' . $occ_softlink->mov_id,
                                'occ_softlink' => $occ_softlink->num_docu,
                                'ocSoftlink' => array('cabecera' => $occ_softlink),
                                'ocAgile' => array('cabecera' => $req),
                            );
                        }
                    } else {
                        $arrayRspta = array(
                            'tipo' => 'warning',
                            'mensaje' => 'No existe dicho id en Softlink. Id: ' . $req->id_occ_softlink,
                            'occSoftlink' => array('cabecera' => $occ_softlink),
                            'reqAgile' => array('cabecera' => $req),
                        );
                    }
                } else {
                    $count = DB::connection('soft')->table('movimien')->count();
                    //codificar segun criterio x documento
                    $mov_id = $this->leftZero(10, (intval($count) + 1));
                    $hoy = date('Y-m-d');
                    //obtiene el año a 2 digitos y le aumenta 2 ceros adelante
                    $yy = $this->leftZero(4, intval(date('y', strtotime($hoy))));
                    //obtiene el ultimo registro
                    $ult_mov = null;

                    $ult_mov = DB::connection('soft')->table('movimien')
                        ->where([
                            ['num_docu', '>', $yy . '0000000'],
                            ['num_docu', '<', $yy . '9999999'],
                            ['cod_suc', '=', $cod_suc],
                            ['tipo', '=', 2], //venta
                            ['cod_docu', '=', 'NP']
                        ])
                        ->orderBy('num_docu', 'desc')->first();

                    //obtiene el correlativo
                    $num_ult_mov = substr($ult_mov->num_docu, 4);
                    //crea el correlativo del documento
                    $nro_mov = $this->leftZero(7, (intval($num_ult_mov) + 1));
                    //anida el anio con el numero de documento
                    $num_docu = $yy . $nro_mov;
                    $this->agregarCabeceraOCC($mov_id, $cod_suc, $req, $num_docu, $cod_auxi, $igv, $mon_impto, $tp_cambio, $id_requerimiento);

                    $i = 0;
                    foreach ($detalles as $det) {
                        $cod_prod = null;
                        //Obtiene y/o crea el producto
                        if ($det->id_producto !== null) {
                            $cod_prod = $this->obtenerProducto($det);
                        } else {
                            $cod_prod = '005675'; //OTROS SERVICIOS - DEFAULT
                        }
                        $this->agregarDetalleOCC($det, $mov_id, $cod_prod, $num_docu, $req->fecha_requerimiento, $igv, $i);
                        // $this->actualizaStockEnTransito($oc, $cod_prod, $det, $cod_suc);
                    }
                    $socc = DB::connection('soft')->table('movimien')->where('mov_id', $mov_id)->first();
                    $sdet = DB::connection('soft')->table('detmov')->where('mov_id', $mov_id)->get();

                    $arrayRspta = array(
                        'tipo' => 'success',
                        'mensaje' => 'Se migró correctamente la OCC Nro. ' . $num_docu . ' con id ' . $mov_id,
                        'occ_softlink' => $num_docu, //($yy . '-' . $nro_mov),
                        'occSoftlink' => array('cabecera' => $socc, 'detalle' => $sdet),
                        'reqAgile' => array('cabecera' => $req, 'detalle' => $detalles),
                    );
                }
            } else {
                $arrayRspta = array(
                    'tipo' => 'warning',
                    'mensaje' => 'No existe el requerimiento seleccionado o no tiene items. Id: ' . $id_requerimiento
                );
            }

            DB::commit();
            return $arrayRspta;
        } catch (\PDOException $e) {
            DB::rollBack($e);
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar el requerimiento. Por favor intente de nuevo', 'error' => $e->getMessage());
        }
    }

    public function agregarCabeceraOCC($mov_id, $cod_suc, $req, $num_docu, $cod_auxi, $igv, $mon_impto, $tp_cambio, $id_requerimiento)
    {

        DB::connection('soft')->table('movimien')->insert(
            [
                'mov_id' => $mov_id,
                'tipo' => '2', //Venta 
                'cod_suc' => $cod_suc,
                'cod_alma' => $req->codigo_almacen,
                'cod_docu' => 'NP', //OCC
                'num_docu' => $num_docu,
                'fec_docu' => $req->fecha_requerimiento,
                'fec_entre' => $req->fecha_entrega,
                'fec_vcto' => $req->fecha_requerimiento,
                'flg_sitpedido' => 0,
                'cod_pedi' => '',
                'num_pedi' => '',
                'cod_auxi' => $cod_auxi,
                'cod_trans' => '00000',
                'cod_vend' => $req->codvend_softlink,
                'tip_mone' => $req->id_moneda,
                'impto1' => $igv,
                'impto2' => '0.00',
                'mon_bruto' => $req->total_precio,
                'mon_impto1' => $mon_impto,
                'mon_impto2' => '0.00',
                'mon_gravado' => '0.00',
                'mon_inafec' => '0.00',
                'mon_exonera' => '0.00',
                'mon_gratis' => '0.00',
                'mon_total' => ($req->total_precio + $mon_impto),
                'sal_docu' => '0.00',
                'tot_cargo' => '0.00',
                'tot_percep' => '0.00',
                'tip_codicion' => '02', //revisar mgcp
                'txt_observa' => ($req->observacion !== null ? $req->observacion : ''),
                'flg_kardex' => 0,
                'flg_anulado' => 0,
                'flg_referen' => 0,
                'flg_percep' => 0,
                'cod_user' => $req->codvend_softlink,
                'programa' => '',
                'txt_nota' => '',
                'tip_cambio' => $tp_cambio->cambio3, //Revisar
                'tdflags' => 'NSSNNSSNSN',
                'numlet' => '',
                'impdcto' => '0.0000',
                'impanticipos' => '0.0000',
                'registro' => new Carbon(),
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
                'ndocu1' => ($req->nro_orden !== null ? $req->nro_orden : ''),
                'ndocu2' => ($req->direccion_entrega !== null ? $req->direccion_entrega : ''),
                'ndocu3' => ($req->codigo_oportunidad !== null ? $req->codigo_oportunidad : ''),
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
        DB::table('almacen.alm_req')
            ->where('id_requerimiento', $id_requerimiento)
            ->update([
                'nro_occ_softlink' => $num_docu,
                'id_occ_softlink' => $mov_id
            ]);
    }
    public function agregarDetalleOCC($det, $mov_id, $cod_prod, $num_docu, $fecha_requerimiento, $igv, $i)
    {
        //cuenta los registros
        $count_det = DB::connection('soft')->table('detmov')->count();
        //aumenta uno y completa los 10 digitos
        $mov_det_id = $this->leftZero(10, (intval($count_det) + 1));

        DB::connection('soft')->table('detmov')->insert(
            [
                'unico' => $mov_det_id,
                'mov_id' => $mov_id,
                'tipo' => '2', //Ventas 
                'cod_docu' => 'NP',
                'num_docu' => $num_docu,
                'fec_pedi' => $fecha_requerimiento,
                'cod_auxi' => trim($det->abreviatura),
                'cod_prod' => $cod_prod,
                'nom_prod' => ($cod_prod == '005675' ? 'OTROS SERVICIOS - ' . $det->descripcion_adicional : $det->descripcion),
                'can_pedi' => $det->cantidad,
                'sal_pedi' => $det->cantidad,
                'can_devo' => $i, //numeracion del item 
                'pre_prod' => ($det->precio_unitario !== null ? $det->precio_unitario : 0),
                'dscto_condi' => '0.000',
                'dscto_categ' => '0.000',
                'pre_neto' => ($det->precio_unitario !== null ? ($det->precio_unitario * $det->cantidad) : 0),
                'igv_inclu' => '0',
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
                'entrega' => '0',
                'notas' => '',
                'flg_percep' => 0,
                'por_percep' => 0,
                'mon_percep' => 0,
                'ok_stk' => 1,
                'ok_serie' => 1,
                'lStock' => '0',
                'no_calc' => '0',
                'promo' => 0,
                'seriesprod' => '',
                'pre_anexa' => '0.0000',
                'dsctocompra' => '0.000',
                'cod_prov' => '',
                'costo_unit' => '0.000000',
                'margen' => '0.00',
                'gasto1' => '0.00',
                'gasto2' => '0.00',
                'flg_detrac' => 0,
                'por_detrac' => 0,
                'cod_detrac' => '',
                'mon_detrac' => 0,
                'tipoprecio' => 6 //'8'
            ]
        );
        DB::table('almacen.alm_det_req')
            ->where('id_detalle_requerimiento', $det->id_detalle_requerimiento)
            ->update(['id_occ_det_softlink' => $mov_det_id]);
    }
    public function obtenerCliente($nro_documento, $razon_social, $doc_tipo, $cod)
    {
        $cliente = null;
        $cod_auxi = null;

        if ($nro_documento !== null && $nro_documento !== '') {
            $cliente = DB::connection('soft')->table('auxiliar')
                ->select('cod_auxi')
                ->where([
                    ['ruc_auxi', '=', $nro_documento],
                    ['tip_auxi', '=', 'C']
                ])
                ->first();
        } else {
            $cliente = DB::connection('soft')->table('auxiliar')
                ->select('cod_auxi')
                ->where([
                    ['nom_auxi', '=', $razon_social],
                    ['tip_auxi', '=', 'C']
                ])
                ->first();
        }

        if ($cliente == null) {
            //obtiene el codigo mayor
            $mayor = DB::connection('soft')->table('auxiliar')
                ->select('cod_auxi')
                ->where([
                    ['cod_auxi', '!=', 'TRANSF'],
                    // ['tip_auxi', '=', 'C']
                ])
                ->orderBy('cod_auxi', 'desc')
                ->first();
            //le aumenta 1 al codigo mayor
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
                    'cod_di' => $cod,
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
            $cod_auxi = $cliente->cod_auxi;
        }
        return $cod_auxi;
    }

    public function obtenerProducto($det)
    {
        //Verifica si esxiste el producto
        $prod = null;
        if (!empty($det->id_producto)) {
            $prod = DB::connection('soft')->table('sopprod')
                ->select('cod_prod')
                ->join('sopsub2', 'sopsub2.cod_sub2', '=', 'sopprod.cod_subc')
                ->where([
                    ['sopprod.cod_espe', '=', trim($det->part_number_prod)],
                    ['sopsub2.nom_sub2', '=', $det->subcategoria]
                ])
                ->first();
        } else if ($det->descripcion_prod !== null && $det->descripcion_prod !== '') {
            $prod = DB::connection('soft')->table('sopprod')
                ->select('cod_prod')
                ->join('sopsub2', 'sopsub2.cod_sub2', '=', 'sopprod.cod_subc')
                ->where([
                    ['nom_prod', '=', trim($det->descripcion_prod)],
                    ['sopsub2.nom_sub2', '=', $det->subcategoria]
                ])
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

            $cod_clasi = $this->obtenerClasificacion($det->clasificacion);

            $cod_cate = $this->obtenerCategoria($det->categoria, $det->id_categoria);

            $cod_subc = $this->obtenerSubCategoria($det->subcategoria, $det->id_subcategoria);

            $cod_unid = $this->obtenerUnidadMedida($det->abreviatura);

            DB::connection('soft')->table('sopprod')->insert(
                [
                    'cod_prod' => $cod_prod,
                    'cod_clasi' => $cod_clasi,
                    'cod_cate' => $cod_cate,
                    'cod_subc' => $cod_subc,
                    'cod_prov' => '',
                    'cod_espe' => trim($det->part_number_prod),
                    'cod_sunat' => '',
                    'nom_prod' => trim($det->descripcion_prod),
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
                    'tip_moneda' => ($det->id_moneda !== null ? $det->id_moneda : 1),
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
                    'por_dcto' => '0.00',
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
}
