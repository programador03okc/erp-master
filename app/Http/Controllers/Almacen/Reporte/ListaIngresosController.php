<?php

namespace App\Http\Controllers\Almacen\Reporte;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Almacen\Movimiento;

class ListaIngresosController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    // public function listarIngresos(Request $request){
    public function listarIngresos(){
        // $idEmpresa= $request->idEmpresa;
        // $idSede= $request->idSede;
        // $idAlmacenes= $request->idAlmacenes;
        // $idCondicioneList= $request->idCondicioneList;
        // $idCondicioneList= $request->idCondicioneList;
        // $fechaInicio= $request->fechaInicio;
        // $fechaFin= $request->fechaFin;
        // $idProveedor= $request->idProveedor;
        // $idUsuario= $request->idUsuario;
        // $idMoneda= $request->idMoneda;

        $data = Movimiento::with(
        'movimiento_detalle',
        'movimiento_detalle.guia_compra_detalle.documento_compra_detalle.documento_compra.proveedor.contribuyente',
        'movimiento_detalle.guia_compra_detalle.documento_compra_detalle.documento_compra.moneda',
        'movimiento_detalle.guia_compra_detalle.documento_compra_detalle.documento_compra.condicion_pago',
        'movimiento_detalle.guia_compra_detalle.orden_detalle.orden',
        'estado',
        'almacen',
        'almacen.tipo_almacen',
        'almacen.sede',
        'almacen.estado',
        'documento_compra',
        'documento_compra.tipo_documento.estado',
        'documento_compra.moneda',
        'documento_compra.condicion_pago',
        'guia_compra.estado',
        'guia_compra.tipo_documento_almacen.estado',
        'guia_compra.proveedor.estadoProveedor',
        'operacion',
        'operacion.estado',
        'usuario'
        )->where('mov_alm.estado','!=',7)
        ->get();
        return $data;
    
    }

}
