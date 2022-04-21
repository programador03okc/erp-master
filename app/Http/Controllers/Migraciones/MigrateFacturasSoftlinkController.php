<?php

namespace App\Http\Controllers\Migraciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MigrateFacturasSoftlinkController extends Controller
{
    //Valida el estado de la orden en softlink
    public function envioFacturasSoftlink($id_doc_com)
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
                    'doc_com.id_moneda',
                    'doc_com.id_sede',
                    'doc_com.id_softlink',
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
                    $join->on('alm_almacen.id_sede', '=', 'log_ord_compra.id_sede');
                    $join->where('alm_almacen.estado', '!=', 7);
                    $join->orderBy('alm_almacen.codigo');
                    $join->limit(1);
                })
                ->leftjoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
                ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'log_ord_compra.id_sede')
                ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
                ->where('id_doc_com', $id_doc_com)
                ->first();



            DB::commit();
            return;
        } catch (\PDOException $e) {
            DB::rollBack();
            return array('tipo' => 'error', 'mensaje' => 'Hubo un problema al enviar la orden. Por favor intente de nuevo', 'error' => $e->getMessage());
        }
    }
}
