<?php

namespace App\Models\Administracion;
use Debugbar;

use Illuminate\Database\Eloquent\Model;

class Operacion extends Model
{
    protected $table = 'administracion.adm_operacion';
    protected $primaryKey = 'id_operacion';
    public $timestamps = false;

    public static function getOperacion($IdTipoDocumento,$idTipoRequerimientoCompra, $idGrupo, $idDivision, $idPrioridad, $idMoneda, $montoTotal, $idTipoRequerimientoPago)
    {

         
        $montoTotalDolares=0;
        $montoTotalSoles=0;
        if($idMoneda ==1){ // soles convertir a dolares
            $montoTotalDolares = floatval($montoTotal)/3.7; // TODO llamar a función para obtener el tipo de cambio
            $montoTotalSoles=$montoTotal;
        }elseif($idMoneda ==2){
            $montoTotalDolares=$montoTotal;
            $montoTotalSoles = floatval($montoTotal)*3.7;
        }
        
        $totalOperaciones = Operacion::where("estado","!=",7)->get();
        $operacionesCoincidenciaTipoDocumentoGrupo=[];
        foreach ($totalOperaciones as $k => $o) {
            if($o->id_tp_documento ==$IdTipoDocumento && $o->id_grupo ==$idGrupo){
                $operacionesCoincidenciaTipoDocumentoGrupo[]= $o;
            }
        }

        $operacionesCoincidenciaPorTipoRequerimiento=[];
        if(count($operacionesCoincidenciaTipoDocumentoGrupo)!=1){
            foreach ($operacionesCoincidenciaTipoDocumentoGrupo as $k => $o) {
                if($o->tipo_requerimiento_id !=null){
                    if($o->tipo_requerimiento_id ==$idTipoRequerimientoCompra){
                        $operacionesCoincidenciaPorTipoRequerimiento[]= $o;
                    }
                }elseif($o->tipo_requerimiento_pago_id !=null){
                    if($o->tipo_requerimiento_pago_id ==$idTipoRequerimientoPago){
                        $operacionesCoincidenciaPorTipoRequerimiento[]= $o;
                    }

                }
            }
        }else{
            //$operacionesCoincidenciaTipoDocumentoGrupo solo tiene un valor
            return $operacionesCoincidenciaTipoDocumentoGrupo;
        }
        // return $operacionesCoincidenciaPorTipoRequerimiento ;

        $operacionesCoincidenciaPorDivision=[];
        if(count($operacionesCoincidenciaPorTipoRequerimiento) !=1){
            foreach ($operacionesCoincidenciaPorTipoRequerimiento as $k => $o) {
                if($o->division_id ==$idDivision){
                    $operacionesCoincidenciaPorDivision[]= $o;
                }
            }
        }else{
            return $operacionesCoincidenciaPorTipoRequerimiento;
        }


        $operacionesCoincidenciaPorMonedaMonto=[];
        $elMontoEsMayor='NO';
        $elMontoEsMenor='NO';
        $elMontoEsIgual='NO';
        $elMontoEsEntre='NO';
        if(count($operacionesCoincidenciaPorDivision) !=1){
            foreach ($operacionesCoincidenciaPorDivision as $k => $o) {
                if($o->monto_mayor >0 ){ // tiene monto definido
                    $elMontoEsMayor = $o->id_moneda ==1?($montoTotalSoles > $o->monto_mayor?'SI':'NO'):($o->id_moneda ==2?($montoTotalDolares > $o->monto_mayor?'SI':'NO'):'NO');
                    $operacionPropuestaPorMonto1[]= $o;
                }
                if($o->monto_menor >0 ){ // tiene monto definido
                    $elMontoEsMenor = $o->id_moneda ==1?($montoTotalSoles < $o->monto_menor?'SI':'NO'):($o->id_moneda ==2?($montoTotalDolares < $o->monto_menor?'SI':'NO'):'NO');
                    $operacionPropuestaPorMonto2[]= $o;

                }
                if($o->monto_igual >0){ // tiene monto definido
                    $elMontoEsIgual = $o->id_moneda ==1?($montoTotalSoles == $o->monto_igual?'SI':'NO'):($o->id_moneda ==2?($montoTotalDolares == $o->monto_igual?'SI':'NO'):'NO');
                    $operacionPropuestaPorMonto3[]= $o;
                }
                if($o->monto_igual >0 && $o->monto_menor >0){ // tiene monto definido
                    $elMontoEsEntre = $o->id_moneda ==1?((($montoTotalSoles > $o->monto_mayor && $montoTotalSoles < $o->monto_menor ))?'SI':'NO'):($o->id_moneda ==2?(($montoTotalSoles > $o->monto_mayor && $montoTotalSoles < $o->monto_menor )?'SI':'NO'):'NO');
                    $operacionPropuestaPorMonto4[]= $o;

                }
            }

            if($elMontoEsEntre =='SI'){
                return $operacionPropuestaPorMonto4;
            }else{
                if($elMontoEsMayor == 'SI'){
                    return $operacionPropuestaPorMonto1;
                }elseif($elMontoEsMenor == 'SI'){
                    return $operacionPropuestaPorMonto2;
                }elseif($elMontoEsIgual == 'SI'){
                    return $operacionPropuestaPorMonto3;
                }
            }
        }else{
            return $operacionesCoincidenciaPorDivision;
        }


    }
}
