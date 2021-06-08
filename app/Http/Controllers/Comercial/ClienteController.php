<?php

namespace App\Http\Controllers\Comercial;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    
    public function mostrar_clientes()
    {
        $data = DB::table('comercial.com_cliente')
        ->select('com_cliente.id_cliente','com_cliente.id_contribuyente',
            'adm_contri.nro_documento','adm_contri.razon_social', 'adm_contri.telefono', 'adm_contri.direccion_fiscal','adm_contri.email')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->where([['com_cliente.estado', '=', 1]])
            ->orderBy('adm_contri.nro_documento')
            ->get();
        $output['data'] = $data;
        return $output;
    }
    
    public function mostrar_clientes_empresa()
    {
        $data = DB::table('comercial.com_cliente')
            ->select('com_cliente.id_cliente','com_cliente.id_contribuyente',
                'adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->join('administracion.adm_empresa','adm_empresa.id_contribuyente','=','adm_contri.id_contribuyente')
            ->where([['com_cliente.estado', '=', 1]])
                ->orderBy('adm_contri.nro_documento')
                ->get();
        $output['data'] = $data;
        return $output;
    }
    
    public function guardar_cliente(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $contri = DB::table('contabilidad.adm_contri')
            ->where('nro_documento',$request->nro_documento)
            ->first();
        $id_cliente = '';
        $id_contribuyente = '';

        if ($contri !== null){
            $id_contribuyente = $contri->id_contribuyente;
        } 
        else {
            $id_contribuyente = DB::table('contabilidad.adm_contri')->insertGetId(
                [
                    'id_tipo_contribuyente' => $request->id_tipo_contribuyente,
                    'id_doc_identidad' => $request->id_doc_identidad,
                    'nro_documento' => $request->nro_documento,
                    'razon_social' => $request->razon_social,
                    'direccion_fiscal' => $request->direccion_fiscal,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_contribuyente'
            );
        }
        $cli = DB::table('comercial.com_cliente')
            ->select('com_cliente.*','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('com_cliente.id_contribuyente',$id_contribuyente)
            ->first();
        
        if ($cli !== null){
            $id_cliente = $cli->id_cliente;
        } 
        else {
            $id_cliente = DB::table('comercial.com_cliente')->insertGetId(
                [
                    'id_contribuyente' => $id_contribuyente,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_cliente'
            );
            $cli = DB::table('comercial.com_cliente')
            ->select('com_cliente.*','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('com_cliente.id_cliente',$id_cliente)
            ->first();
        }
        return response()->json($cli);
    }

    
}
