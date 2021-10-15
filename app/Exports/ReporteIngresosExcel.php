<?php

namespace App\Exports;

use App\Models\Administracion\Sede;
use App\Models\Almacen\MovimientoDetalle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteIngresosExcel implements FromView
{


    public function __construct(string $idEmpresa,string $idSede,string $almacenes,string $condiciones,string $fini,string $ffin, string $prov, string $id_usuario, string $moneda, string $tra)
    {
        $this->idEmpresa = $idEmpresa;
        $this->idsede = $idSede;
        $this->almacenes = $almacenes;
        $this->condiciones = $condiciones;
        $this->fecha_inicio = $fini;
        $this->fecha_fin = $ffin;
        $this->id_usuario = $id_usuario;
        $this->id_proveedor = $prov;
        $this->moneda = $moneda;
        $this->transportista = $tra;
    }

    public function view(): View{
        $idEmpresa= $this->idEmpresa;
        $idSede = $this->idsede;
        $alm_array = explode(',', $this->almacenes);
        $con_array = explode(',', $this->condiciones);
        $fecha_inicio = $this->fecha_inicio;
        $fecha_fin = $this->fecha_fin;
        $id_usuario = $this->id_usuario;
        $id_proveedor = $this->id_proveedor;
        $moneda = $this->moneda;
        $transportista = $this->transportista;


        $data = DB::table('almacen.mov_alm')
            ->select(
                'mov_alm.*',
                'sis_moneda.simbolo',
                'doc_com.total',
                'doc_com.fecha_vcmto',
                'doc_com.total_igv',
                'doc_com.total_a_pagar',
                'cont_tp_doc.abreviatura',
                'doc_com.credito_dias',
                'log_cdn_pago.descripcion as des_condicion',
                'doc_com.fecha_emision as fecha_doc',
                'alm_almacen.descripcion as des_almacen',
                'doc_com.tipo_cambio',
                'doc_com.moneda',
                'doc_com.id_sede',
                DB::raw("(doc_com.serie) || '-' || (doc_com.numero) as doc"),
                DB::raw("(guia_com.serie) || '-' || (guia_com.numero) as guia"),
                'guia_com.fecha_emision as fecha_guia',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'tp_ope.descripcion as des_operacion',
                'sis_usua.nombre_corto as nombre_trabajador'
            )
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'mov_alm.id_almacen')
            ->leftjoin('almacen.guia_com', 'guia_com.id_guia', '=', 'mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen', 'tp_doc_almacen.id_tp_doc_almacen', '=', 'guia_com.id_tp_doc_almacen')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('almacen.tp_ope', 'tp_ope.id_operacion', '=', 'mov_alm.id_operacion')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'mov_alm.usuario')
            // ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            // ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            // ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->leftjoin('almacen.doc_com', 'doc_com.id_doc_com', '=', 'mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'doc_com.id_tp_doc')
            ->leftjoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
            ->leftjoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')

            ->when(($idEmpresa > 0), function ($query) use($idEmpresa) {
                $sedes= Sede::where('id_empresa',$idEmpresa)->get();
                $idSedeList=[];
                foreach($sedes as $sede){
                    $idSedeList[]=$sede->id_sede;
                }
                return $query->whereIn('alm_almacen.id_sede', $idSedeList);
            })
            ->when(($idSede > 0), function ($query) use($idSede) {
                return $query->where('alm_almacen.id_sede',$idSede);
            })

            ->when((($fecha_inicio != 'SIN_FILTRO') and ($fecha_fin == 'SIN_FILTRO')), function ($query) use($fecha_inicio) {
                return $query->where('mov_alm.fecha_emision' ,'>=',$fecha_inicio); 
            })
            ->when((($fecha_inicio == 'SIN_FILTRO') and ($fecha_fin != 'SIN_FILTRO')), function ($query) use($fecha_fin) {
                return $query->where('mov_alm.fecha_emision' ,'<=',$fecha_fin); 
            })
            ->when((($fecha_inicio != 'SIN_FILTRO') and ($fecha_fin != 'SIN_FILTRO')), function ($query) use($fecha_inicio,$fecha_fin) {
                return $query->whereBetween('mov_alm.fecha_emision' ,[$fecha_inicio,$fecha_fin]); 
            })

            ->when((count($alm_array) > 0), function ($query) use($alm_array) {
                return $query->whereIn('mov_alm.id_almacen',$alm_array);
            })
            ->when((count($con_array) > 0), function ($query) use($con_array) {
                return $query->whereIn('mov_alm.id_operacion',$con_array);
            })
            ->when(($id_proveedor !=null && $id_proveedor > 0), function ($query) use($id_proveedor) {
                return $query->where('guia_com.id_proveedor',$id_proveedor);
            })
            ->when(($id_usuario !=null && $id_usuario > 0), function ($query) use($id_usuario) {
                return $query->where('guia_com.usuario',$id_usuario);
            })
            ->when(($moneda == 1 || $moneda == 2), function ($query) use($moneda) {
                return $query->where('doc_com.moneda',$moneda);
            })
            ->when(($transportista !=null && $transportista > 0), function ($query) use($transportista) {
                return $query->where('guia_com.transportista',$transportista);
            })
 

            // ->whereIn('mov_alm.id_almacen', $alm_array)
            // ->whereIn('guia_com.id_tp_doc_almacen',$doc_array)
            // ->whereIn('doc_com.id_tp_doc',$docs)
            // ->whereIn('mov_alm.id_operacion', $con_array)
            // ->whereBetween('mov_alm.fecha_emision', [$fecha_inicio, $fecha_fin])
            ->where([['mov_alm.estado', '!=', 7]])
            // ->where($hasWhere)
            ->get();

        $nueva_data = [];

        foreach ($data as $d) {
            // $ocs = DB::table('almacen.guia_com_oc')
            // ->select('log_ord_compra.codigo')
            // ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
            // ->where('id_guia_com',$d->id_guia_com)
            // ->get();
            $ordenes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
                ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_orden', 'guia_com_det.id_oc_det')
                ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', 'log_det_ord_compra.id_orden_compra')
                ->where('mov_alm_det.id_mov_alm', $d->id_mov_alm)
                ->select(['log_ord_compra.codigo'])->distinct()->get();

            $ordenes_array = [];
            foreach ($ordenes as $oc) {
                array_push($ordenes_array, $oc->codigo);
            }

            $comprobantes = MovimientoDetalle::join('almacen.guia_com_det', 'guia_com_det.id_guia_com_det', 'mov_alm_det.id_guia_com_det')
                ->join('almacen.doc_com_det', 'doc_com_det.id_guia_com_det', 'guia_com_det.id_guia_com_det')
                ->join('almacen.doc_com', 'doc_com.id_doc_com', 'doc_com_det.id_doc')
                ->join('logistica.log_prove', 'log_prove.id_proveedor', 'doc_com.id_proveedor')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', 'log_prove.id_contribuyente')
                ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'doc_com.moneda')
                ->join('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'doc_com.id_condicion')
                ->where([
                    ['mov_alm_det.id_mov_alm', '=', $d->id_mov_alm],
                    ['mov_alm_det.estado', '!=', 7],
                    ['guia_com_det.estado', '!=', 7],
                    ['doc_com_det.estado', '!=', 7]
                ])
                ->select([
                    'doc_com.serie', 'doc_com.numero', 'doc_com.fecha_emision', 'sis_moneda.simbolo', 'doc_com.moneda',
                    'adm_contri.nro_documento', 'adm_contri.razon_social', 'log_cdn_pago.descripcion as des_condicion',
                    'doc_com.credito_dias', 'doc_com.sub_total', 'doc_com.total_igv', 'doc_com.total_a_pagar'
                ])
                ->distinct()->get();

            $comprobantes_array = [];
            $doc_fecha_emision_array = [];
            $ruc = '';
            $razon_social = '';
            $simbolo = '';
            $moneda = '';
            $total = '';
            $total_igv = '';
            $total_a_pagar = '';
            $condicion = '';
            $credito_dias = '';

            foreach ($comprobantes as $doc) {
                array_push($comprobantes_array, $doc->serie . '-' . $doc->numero);
                array_push($doc_fecha_emision_array, $doc->fecha_emision);
                $ruc = ($doc->nro_documento !== null ? $doc->nro_documento : '');
                $razon_social = ($doc->razon_social !== null ? $doc->razon_social : '');
                $simbolo = ($doc->simbolo !== null ? $doc->simbolo : '');
                $moneda = ($doc->moneda !== null ? $doc->moneda : '');
                $total = ($doc->sub_total !== null ? $doc->sub_total : '');
                $total_igv = ($doc->total_igv !== null ? $doc->total_igv : '');
                $total_a_pagar = ($doc->total_a_pagar !== null ? $doc->total_a_pagar : '');
                $condicion = ($doc->des_condicion !== null ? $doc->des_condicion : '');
                $credito_dias = ($doc->credito_dias !== null ? $doc->credito_dias : '');
            }

            $nueva_data[] = [
                'revisado' => ($d->revisado==0?('No Revisado'):($d->revisado ==1?('Revisado'):($d->revisado==2?'Observado':''))),
                'fecha_emision' => $d->fecha_emision,
                'codigo' => $d->codigo,
                'fecha_guia' => $d->fecha_guia,
                'guia' => $d->guia,
                'fecha_doc' => implode(', ', $doc_fecha_emision_array),
                'documentos' => implode(', ', $comprobantes_array),
                'nro_documento' => $ruc,
                'razon_social' => $razon_social,
                'ordenes' => implode(', ', $ordenes_array),
                'simbolo' => $simbolo,
                'total' => $total,
                'total_igv' => $total_igv,
                'total_a_pagar' => $total_a_pagar,
                'des_condicion' => $condicion . ($credito_dias !== '' ? ' ' . $credito_dias . ' días' : ''),
                'des_operacion' => $d->des_operacion,
                'nombre_trabajador' => $d->nombre_trabajador,
                'des_almacen' => $d->des_almacen,
                'fecha_registro' => $d->fecha_registro
            ];
         }
     
        return view('almacen.reportes.view_ingresos_export', [
            'ingresos' => $nueva_data
        ]);
    }

}
