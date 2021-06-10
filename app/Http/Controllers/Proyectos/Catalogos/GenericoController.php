<?php

namespace App\Http\Controllers\Proyectos\Catalogos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GenericoController extends Controller
{
    public static function mostrar_modalidad_cbo(){
        $data = DB::table('proyectos.proy_modalidad')
            ->select('proy_modalidad.id_modalidad','proy_modalidad.descripcion')
            ->where([['proy_modalidad.estado', '=', 1]])
            ->get();
        return $data;
    }
    public static function mostrar_unid_program_cbo(){
        $data = DB::table('proyectos.proy_unid_program')
            ->select('proy_unid_program.id_unid_program','proy_unid_program.descripcion')
            ->where([['proy_unid_program.estado', '=', 1]])
            ->get();
        return $data;
    }
    public static function mostrar_tipos_cbo()
    {
        $data = DB::table('proyectos.proy_tp_proyecto')
            ->select('proy_tp_proyecto.id_tp_proyecto','proy_tp_proyecto.descripcion')
            ->where([['proy_tp_proyecto.estado', '=', 1]])
            ->get();
            return $data;
    }
    public static function mostrar_tipo_contrato_cbo(){
        $data = DB::table('proyectos.proy_tp_contrato')
        ->select('proy_tp_contrato.id_tp_contrato','proy_tp_contrato.descripcion')
        ->where([['proy_tp_contrato.estado','=',1]])
            ->get();
        return $data;
    }
    public static function mostrar_empresas_cbo(){
        $data = DB::table('administracion.adm_empresa')
        ->select('adm_empresa.id_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')
        ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_empresa.id_empresa', 'asc')->get();
        return $data;
    }
    public static function sis_identidad_cbo(){
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }
    public static function tp_contribuyente_cbo(){
        $data = DB::table('contabilidad.adm_tp_contri')
            ->select('adm_tp_contri.id_tipo_contribuyente', 'adm_tp_contri.descripcion')
            ->where('adm_tp_contri.estado', '=', 1)
            ->orderBy('adm_tp_contri.descripcion', 'asc')->get();
        return $data;
    }
    
    public static function mostrar_monedas_cbo()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda','sis_moneda.simbolo','sis_moneda.descripcion')
            ->where([['sis_moneda.estado', '=', 1]])
            ->orderBy('sis_moneda.id_moneda')
            ->get();
        return $data;
    }

    public static function mostrar_sis_contrato_cbo(){
        $data = DB::table('proyectos.proy_sis_contrato')
            ->select('proy_sis_contrato.id_sis_contrato','proy_sis_contrato.descripcion')
            ->where([['proy_sis_contrato.estado', '=', 1]])
            ->get();
        return $data;
    }

    public static function leftZero($lenght, $number){
        $nLen = strlen($number);
        $zeros = '';
        for($i=0; $i<($lenght-$nLen); $i++){
            $zeros = $zeros.'0';
        }
        return $zeros.$number;
    }
}
