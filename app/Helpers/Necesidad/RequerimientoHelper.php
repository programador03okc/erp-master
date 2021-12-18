<?php

namespace App\Helpers\Necesidad;

use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;

class RequerimientoHelper
{
    public function EstaHabilitadoRequerimiento($idDetalleRequerimientoList)
    {
        $idRequerimientoList=[];
        $cantidadRequerimientosEnPausaOPorRegularizar=0;
        $detalleRequerimientoList = DetalleRequerimiento::whereIn('id_detalle_requerimiento',$idDetalleRequerimientoList)->where('estado','!=',7)->get();
        foreach ($detalleRequerimientoList as  $dr) {
            $idRequerimientoList[]=$dr->id_requerimiento;
            
        }
        
        $requerimientoList= Requerimiento::whereIn('id_requerimiento',array_unique($idRequerimientoList))->where('estado','!=',7)->get();

        foreach ($requerimientoList as $r) {
            if($r->estado== 38 || $r->estado==39){ // estado en pausa o estado por regularizar

                $cantidadRequerimientosEnPausaOPorRegularizar++;
            }
        }
        return $cantidadRequerimientosEnPausaOPorRegularizar>0?false:true;

    }
}
