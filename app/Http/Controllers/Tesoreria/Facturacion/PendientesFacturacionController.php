<?php

namespace App\Http\Controllers\Tesoreria\Facturacion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PendientesFacturacionController extends Controller
{
    function view_pendientes_facturacion()
    {
        return view('tesoreria/facturacion/pendientesFacturacion');
    }

    public function listarGuiasVentaPendientes(){
        $data = DB::table('almacen.guia_ven')
        ->select('guia_ven.*','adm_contri.nro_documento','adm_contri.razon_social',
        'sis_usua.nombre_corto','adm_estado_doc.estado_doc','adm_estado_doc.bootstrap_color',
        DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_persona"),
        'alm_almacen.descripcion as almacen_descripcion')
        ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->leftjoin('rrhh.rrhh_perso','rrhh_perso.id_persona','=','guia_ven.id_persona')
        ->leftjoin('almacen.alm_almacen','alm_almacen.id_almacen','=','guia_ven.id_almacen')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','guia_ven.registrado_por')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
        ->where('guia_ven.estado',1)
        ->get();
        $output['data'] = $data;
        return response()->json($output);
        // return datatables($data)->toJson();
    }
}
