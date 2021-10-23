<?php

namespace App\Http\Controllers\Almacen\Reporte;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Sede;
use App\Models\Almacen\Movimiento;
use Yajra\DataTables\Facades\DataTables;

class ListaSalidasController extends Controller
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


    public function obtenerDataSalidas($idEmpresa,$idSede,$idAlmacenes,$idCondicioneList,$fechaInicio,$fechaFin,$idUsuario,$idCliente,$idMoneda){
        $data = Movimiento::with(
            [
            'movimiento_detalle',
            'guia_venta',
            'guia_venta.cliente.contribuyente',
            'guia_venta.tipo_documento_almacen',
            'guia_venta.estado',
            'estado',
            'almacen',
            'almacen.tipo_almacen',
            'almacen.sede',
            'almacen.estado',
            'documento_venta.sede',
            'documento_venta.tipo_documento.estado',
            'documento_venta.moneda',
            'documento_venta.condicion_pago',
            'operacion',
            'operacion.estado',
            'usuario'
            ])
            ->when(($idEmpresa > 0), function ($query) use($idEmpresa) {
                $sedes= Sede::where('id_empresa',$idEmpresa)->get();
                $idSedeList=[];
                foreach($sedes as $sede){
                    $idSedeList[]=$sede->id_sede;
                }
                $query->join('almacen.alm_almacen', 'alm_almacen.id_almacen', 'mov_alm.id_almacen');
                return $query->whereIn('alm_almacen.id_sede', $idSedeList);
            })
            ->when(($idSede > 0), function ($query) use($idSede) {
                return $query->where('alm_almacen.id_sede',$idSede);
            })
    
            ->when((($fechaInicio != 'SIN_FILTRO') and ($fechaFin == 'SIN_FILTRO')), function ($query) use($fechaInicio) {
                return $query->where('mov_alm.fecha_emision' ,'>=',$fechaInicio); 
            })
            ->when((($fechaInicio == 'SIN_FILTRO') and ($fechaFin != 'SIN_FILTRO')), function ($query) use($fechaFin) {
                return $query->where('mov_alm.fecha_emision' ,'<=',$fechaFin); 
            })
            ->when((($fechaInicio != 'SIN_FILTRO') and ($fechaFin != 'SIN_FILTRO')), function ($query) use($fechaInicio,$fechaFin) {
                return $query->whereBetween('mov_alm.fecha_emision' ,[$fechaInicio,$fechaFin]); 
            })
            ->when(($idAlmacenes!=null && count($idAlmacenes) > 0), function ($query) use($idAlmacenes) {
                return $query->whereIn('mov_alm.id_almacen',$idAlmacenes);
            })
            ->when(($idCondicioneList!=null && count($idCondicioneList) > 0), function ($query) use($idCondicioneList) {
                return $query->whereIn('mov_alm.id_operacion',$idCondicioneList);
            })
            ->when(($idCliente !=null && $idCliente > 0), function ($query) use($idCliente) {
                return $query->where('guia_com.id_proveedor',$idCliente);
            })
            ->when(($idUsuario !=null && $idUsuario > 0), function ($query) use($idUsuario) {
                return $query->where('guia_com.usuario',$idUsuario);
            })
            ->when(($idMoneda == 1 || $idMoneda == 2), function ($query) use($idMoneda) {
                return $query->where('doc_com.moneda',$idMoneda);
            })        
            ->where([['mov_alm.estado','!=',7]]);
            return $data;
    }  


    public function listarSalidas(Request $request){
        $idEmpresa= $request->idEmpresa;
        $idSede= $request->idSede;
        $idAlmacenes= $request->idAlmacenList;
        $idCondicioneList= $request->idCondicionList;
        $fechaInicio= $request->fechaInicio;
        $fechaFin= $request->fechaFin;
        $idCliente= $request->idCliente;
        $idUsuario= $request->idUsuario;
        $idMoneda= $request->idMoneda;

        $data = $this->obtenerDataSalidas($idEmpresa,$idSede,$idAlmacenes,$idCondicioneList,$fechaInicio,$fechaFin,$idUsuario,$idCliente,$idMoneda);

		return datatables($data)->toJson();

    }
}
