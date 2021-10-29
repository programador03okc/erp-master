<?php

namespace App\Exports;

use App\Http\Controllers\Almacen\Reporte\ListaIngresosController;
use App\Models\Administracion\Sede;
use App\Models\Almacen\MovimientoDetalle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteIngresosExcel implements FromView
{


    public function __construct(string $idEmpresa,string $idSede,string $almacenes,string $condiciones,string $fechaRegistroDesde,string $fechaRegistroHasta, string $prov, string $id_usuario, string $moneda, string $tra)
    {
        $this->idEmpresa = $idEmpresa;
        $this->idsede = $idSede;
        $this->almacenes = $almacenes;
        $this->condiciones = $condiciones;
        $this->fechaRegistroDesde = $fechaRegistroDesde;
        $this->fechaRegistroHasta = $fechaRegistroHasta;
        $this->id_usuario = $id_usuario;
        $this->id_proveedor = $prov;
        $this->moneda = $moneda;
        $this->transportista = $tra;
    }

    public function view(): View{

        $idEmpresa= $this->idEmpresa;
        $idSede = $this->idsede;
        $idAlmacenes = $this->almacenes;
        $fechaInicio = $this->fechaRegistroDesde;
        $fechaFin = $this->fechaRegistroHasta;
        $idUsuario = $this->id_usuario;
        $idProveedor = $this->id_proveedor;
        $idMoneda = $this->moneda;
        $idCondicioneList = $this->condiciones;
 

        $ingresos = (new ListaIngresosController)->obtenerDataListarIngresos($idEmpresa,$idSede,$idAlmacenes,$idCondicioneList,$fechaInicio,$fechaFin,$idProveedor,$idUsuario,$idMoneda)->orderBy('mov_alm.fecha_emision','ASC')->orderBy('guia_com.fecha_emision', 'ASC')->get();
        foreach($ingresos as $d){
            $nueva_data[] = [
                'revisado' => ($d->revisado==0?('No Revisado'):($d->revisado ==1?('Revisado'):($d->revisado==2?'Observado':''))),
                'fecha_emision' => $d->fecha_emision??'',
                'codigo' => $d->codigo??'',
                'fecha_guia' => $d->fecha_guia??'',
                'guia' => $d->guia??'',
                'documentos' => $d->comprobantes['codigo']?implode(',',$d->comprobantes['codigo']):'',
                'nro_documento' => $d->nro_documento??'',
                'razon_social' => $d->razon_social??'',
                'ordenes' => $d->ordenes_compra??'',
                'empresa_sede' => $d->comprobantes['empresa_sede']??'',
                'simbolo' => $d->comprobantes['moneda']??'',
                'total' => $d->comprobantes['montos']?$d->comprobantes['montos']['sub_total']:0,
                'total' => $d->comprobantes['montos']?$d->comprobantes['montos']['sub_total']:0,
                'total_igv' => $d->comprobantes['montos']?$d->comprobantes['montos']['total_igv']:0,
                'total_a_pagar' => $d->comprobantes['montos']?$d->comprobantes['montos']['total_a_pagar']:0,
                'des_condicion' => $d->comprobantes['condicion']??'',
                'des_operacion' => $d->des_operacion??'',
                'nombre_trabajador' => $d->nombre_trabajador??'',
                'des_almacen' => $d->des_almacen??'',
                'fecha_registro' => $d->fecha_registro??''
            ];
        }

        
     
        return view('almacen.reportes.view_ingresos_export', [
            'ingresos' => $nueva_data
        ]);
    }

}
