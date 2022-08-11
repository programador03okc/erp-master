<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\administracion\empresa;
use App\Models\administracion\area;
use App\Models\administracion\sede;

date_default_timezone_set('America/Lima');


 

class AdministracionController extends Controller{

    function view_main_administracion(){
        return view('administracion/main');
    }

    function view_empresa(){
        $pais = $this->select_pais();
        $tp_contri = $this->select_tipo_contribuyente();
        $banco = $this->select_banco();
        $tpcta = $this->select_tipo_cuenta();
        return view('administracion/empresa', compact('pais', 'tp_contri', 'banco', 'tpcta'));
    }
    function view_sede(){
        $emp = $this->select_empresa();
        return view('administracion/sede', compact('emp'));
    }
    function view_grupo(){
        $emp = $this->select_empresa();
        return view('administracion/grupo', compact('emp'));
    }
    function view_area(){
        $emp = $this->select_empresa();
        return view('administracion/area', compact('emp'));
    }

    public function select_pais(){
        $data = DB::table('configuracion.sis_pais')->select('id_pais', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_empresa(){
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('adm_empresa.id_empresa', 'adm_contri.razon_social')->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')->get();
        return $data;
    }
    public function select_sede(){
            $data = DB::table('administracion.sis_sede')->select('id_sede', 'descripcion')->where('estado', '=', 1)
                ->orderBy('descripcion', 'asc')->get();
            return $data;
        }
    public function select_tipo_contribuyente(){
        $data = DB::table('contabilidad.adm_tp_contri')->select('id_tipo_contribuyente', 'descripcion')->where('estado', '=', 1)
        ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_banco(){
        $data = DB::table('contabilidad.cont_banco')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
                ->select('cont_banco.id_banco', 'adm_contri.razon_social AS descripcion')
                ->orderBy('adm_contri.razon_social', 'asc')->get();
        return $data;
    }
    public function select_tipo_cuenta(){
        $data = DB::table('contabilidad.adm_tp_cta')->select('id_tipo_cuenta', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }

    public function mostrar_empresa_table(){
        $sql = DB::table('administracion.adm_empresa')
                    ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
                    ->select('adm_empresa.*', 'adm_contri.razon_social', 'adm_contri.nro_documento', 'adm_contri.direccion_fiscal', 'adm_contri.telefono')
                    ->where('adm_empresa.estado', 1)->orderBy('adm_contri.razon_social', 'ASC')->get();
        $output['data'] = $sql;
        return response()->json($output);
    }

    public function mostrar_empresa_id($id){
        $sql = DB::table('administracion.adm_empresa')
                    ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
                    ->select('adm_empresa.id_empresa', 'adm_empresa.codigo', 'adm_contri.*')
                    ->where('adm_empresa.id_contribuyente', $id)->get();
        return response()->json($sql);
    }

    public function mostrar_contacto_empresa($id){
        $html = '';
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->join('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('adm_ctb_contac.*')
            ->where('adm_ctb_contac.id_contribuyente', $id)->get();

        if ($data->count() > 0){
            foreach ($data as $row){
                $html .=
                '<tr>
                    <td>'.$row->id_datos_contacto.'</td>
                    <td>'.$row->nombre.'</td>
                    <td>'.$row->cargo.'</td>
                    <td>'.$row->email.'</td>
                    <td>'.$row->telefono.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="4"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }
    public function mostrar_cuentas_empresa($id){
        $html = '';
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->join('contabilidad.adm_cta_contri', 'adm_cta_contri.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->select('adm_cta_contri.*')
            ->where('adm_cta_contri.id_contribuyente', $id)->get();

        if ($data->count() > 0){
            foreach ($data as $row){
                $banco = $this->nombreBanco($row->id_banco);
                $tpcta = $this->tipoCuenta($row->id_tipo_cuenta);
                $html .=
                '<tr>
                    <td>'.$row->id_cuenta_contribuyente.'</td>
                    <td>'.$banco.'</td>
                    <td>'.$tpcta.'</td>
                    <td>'.$row->nro_cuenta.'</td>
                </tr>';
            }
        }else{
            $html.= '<tr><td></td><td colspan="3"> No hay datos registrados</td></tr>';
        }
        return response()->json($html);
    }

    public function guardar_empresas(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $sql = DB::table('contabilidad.adm_contri')
                ->where('adm_contri.nro_documento', '=', $request->nro_documento)->get();

        if ($sql->count() > 0){
            $values = 'exist';
        }else{
            $id = DB::table('contabilidad.adm_contri')->insertGetId(
                [
                    'id_tipo_contribuyente' => $request->id_tipo_contribuyente,
                    'id_doc_identidad'      => $request->id_doc_identidad,
                    'nro_documento'         => $request->nro_documento,
                    'razon_social'          => strtoupper($request->razon_social),
                    'telefono'              => $request->telefono,
                    'celular'               => $request->celular,
                    'direccion_fiscal'      => strtoupper($request->direccion_fiscal),
                    'ubigeo'                => $request->ubigeo,
                    'id_pais'               => $request->id_pais,
                    'estado'                => 1,
                    'fecha_registro'        => $fecha_registro
                ],
                'id_contribuyente'
            );

            if ($id > 0){
                $new_hour = date('Y-m-d H:i:s');
                $empre = DB::table('administracion.adm_empresa')->insertGetId(
                    [
                        'id_contribuyente'  => $request->id_contribuyente,
                        'codigo'            => strtoupper($request->codigo),
                        'estado'            => 1,
                        'fecha_registro'    => $new_hour
                    ],
                    'id_empresa'
                );
                if ($empre > 0){
                    $values = $empre;
                }else{
                    $values = 'null_empre';
                }
            }else{
                $values = 'null';
            }
        }
        return response()->json($id);
    }
    public function actualizar_empresas(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');

        $data = DB::table('administracion.adm_empresa')->where('id_empresa', $request->id_empresa)
        ->update(['codigo'    => strtoupper($request->codigo)]);

        if ($data > 0){
            $data_cont = DB::table('contabilidad.adm_contri')->where('id_contribuyente', $request->id_contribuyente)
            ->update([
                'id_tipo_contribuyente' => $request->id_tipo_contribuyente,
                'id_doc_identidad'      => $request->id_doc_identidad,
                'nro_documento'         => $request->nro_documento,
                'razon_social'          => strtoupper($request->razon_social),
                'telefono'              => $request->telefono,
                'celular'               => $request->celular,
                'direccion_fiscal'      => strtoupper($request->direccion_fiscal),
                'ubigeo'                => $request->ubigeo,
                'id_pais'               => $request->id_pais,
                'estado'                => 1,
                'fecha_registro'        => $fecha_registro
            ]);
    
            if ($data_cont > 0){
                $val = $request->id_contribuyente;
            }else{
                $val = 0;
            }
        }else{
            $val = -1;
        }
        return response()->json($val);
    }

    public function nombreBanco($id){
        $sql = DB::table('contabilidad.cont_banco')
                    ->join('contabilidad.adm_contri', 'cont_banco.id_contribuyente', '=', 'adm_contri.id_contribuyente')
                    ->select('adm_contri.razon_social')
                    ->where('cont_banco.id_banco', $id)->get();
        
        $val = ($sql->count() > 0) ? $sql->first()->razon_social : '';
        return $val;
    }
    public function tipoCuenta($id){
        $sql = DB::table('contabilidad.adm_cta_contri')
                    ->join('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
                    ->select('adm_tp_cta.descripcion')
                    ->where('adm_cta_contri.id_tipo_cuenta', $id)->get();
        
        $val = ($sql->count() > 0) ? $sql->first()->descripcion : '';
        return $val;
    }
    public function codigoEmpresa($value, $type){
        $sql = DB::table('administracion.adm_empresa')->select('adm_empresa.codigo')->where('id_empresa', $value)->get();
        $val = ($sql->count() > 0) ? $sql->first()->codigo : '----';
        if ($type == 'return'){
            return response()->json($val);
        }else if ($type == 'echo'){
            return $val;
        }
    }

    /* SEDES */
    public function mostrar_sede_table(){
        $sql = DB::table('administracion.sis_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
                    ->select('sis_sede.*', 'adm_contri.razon_social')
                    ->where('sis_sede.estado', 1)->orderBy('sis_sede.descripcion', 'ASC')->get();
        $output['data'] = $sql;
        return response()->json($output);
    }
    public function mostrar_sede_id($id){
        $sql = DB::table('administracion.sis_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('sis_sede.*', 'adm_contri.razon_social', 'adm_empresa.codigo AS abrev')
            ->where('sis_sede.id_sede', $id)->get();
        return response()->json($sql);
    }
    public function guardar_sede(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $codigo = $this->codigoEmpresa($request->empresa, 'echo');
        $nombre = $codigo.'-'.$request->descripcion;

        $sql = DB::table('administracion.sis_sede')->where([['id_empresa', $request->empresa], ['descripcion', $nombre]])->get();

        if ($sql->count() > 0){
            $values = 'exist';
        }else{
            $id = DB::table('administracion.sis_sede')->insertGetId(
                [
                    'id_empresa'        => $request->empresa,
                    'codigo'            => $request->abt,
                    'descripcion'       => $nombre,
                    'direccion'         => strtoupper($request->direccion),
                    'estado'            => 1,
                    'fecha_registro'    => $fecha_registro
                ],
                'id_sede'
            );

            if ($id > 0){
                $values = 'ok';
            }else{
                $values = 'null';
            }
        }
        return response()->json($values);
    }
    public function actualizar_sede(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $codigo = $this->codigoEmpresa($request->empresa, 'echo');
        $nombre = $codigo.'-'.$request->descripcion;

        $sql = DB::table('administracion.sis_sede')
        ->where([['id_empresa', $request->empresa], ['descripcion', $nombre], ['id_sede', '!=', $request->id_sede]])->get();

        if ($sql->count() > 0){
            $values = 'exist';
        }else{
            $data_cont = DB::table('administracion.sis_sede')->where('id_sede', $request->id_sede)
            ->update([
                'id_empresa'        => $request->empresa,
                'codigo'            => $request->abt,
                'descripcion'       => $nombre,
                'direccion'         => strtoupper($request->direccion),
            ]);
    
            if ($data_cont > 0){
                $values = 'ok';
            }else{
                $values = 'null';
            }
        }
        return response()->json($values);
    }
    public function anular_sede($id){
        $data = DB::table('administracion.sis_sede')->where('id_sede', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* GRUPOS */
    public function mostrar_grupo_table(){
        $sql = DB::table('administracion.adm_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                    ->select('adm_grupo.*', 'sis_sede.descripcion AS sede', 'adm_contri.razon_social AS empresa')
                    ->where('adm_grupo.estado', 1)->orderBy('adm_grupo.descripcion', 'ASC')->get();
        $output['data'] = $sql;
        return response()->json($output);
    }
    public function mostrar_grupo_id($id){
        $sql = DB::table('administracion.adm_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->select('adm_grupo.*', 'adm_empresa.id_empresa')
                    ->where('adm_grupo.id_grupo', $id)->get();
        return response()->json($sql);
    }
    public function guardar_grupo(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $nombre = $request->descripcion;

        $sql = DB::table('administracion.adm_grupo')->where([['id_sede', $request->sede], ['descripcion', $nombre]])->get();

        if ($sql->count() > 0){
            $values = 'exist';
        }else{
            $id = DB::table('administracion.adm_grupo')->insertGetId(
                [
                    'descripcion'       => $nombre,
                    'estado'            => 1,
                    'fecha_registro'    => $fecha_registro,
                    'codigo'            => $request->codigo,
                    'id_sede'           => $request->sede
                ],
                'id_grupo'
            );

            if ($id > 0){
                $values = 'ok';
            }else{
                $values = 'null';
            }
        }
        return response()->json($values);
    }
    public function actualizar_grupo(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $nombre = $request->descripcion;

        $sql = DB::table('administracion.adm_grupo')
        ->where([['id_sede', $request->sede], ['descripcion', $nombre], ['id_grupo', '!=', $request->id_grupo]])->get();

        if ($sql->count() > 0){
            $values = 'exist';
        }else{
            $data_cont = DB::table('administracion.adm_grupo')->where('id_grupo', $request->id_grupo)
            ->update([
                'descripcion'       => $nombre,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro,
                'codigo'            => $request->codigo,
                'id_sede'           => $request->sede
            ]);
    
            if ($data_cont > 0){
                $values = 'ok';
            }else{
                $values = 'null';
            }
        }
        return response()->json($values);
    }
    public function anular_grupo($id){
        $data = DB::table('administracion.adm_grupo')->where('id_grupo', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    /* AREAS */
    public function mostrar_area_table(){
        $sql = DB::table('administracion.adm_area')
                    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                    ->select('adm_area.*', 'adm_grupo.descripcion AS grupo', 'sis_sede.descripcion AS sede', 'adm_contri.razon_social AS empresa')
                    ->where('adm_area.estado', 1)->orderBy('adm_area.descripcion', 'ASC')->get();
        $output['data'] = $sql;
        return response()->json($output);
    }
    public function mostrar_area_id($id){
        $sql = DB::table('administracion.adm_area')
                    ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'adm_area.id_grupo')
                    ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
                    ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
                    ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                    ->select('adm_area.*', 'sis_sede.id_sede', 'adm_empresa.id_empresa')
                    ->where('adm_area.id_area', $id)->get();
        return response()->json($sql);
    }
    public function guardar_area(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $nombre = $request->descripcion;

        $sql = DB::table('administracion.adm_area')->where([['id_grupo', $request->grupo], ['descripcion', $nombre]])->get();

        if ($sql->count() > 0){
            $values = 'exist';
        }else{
            $id = DB::table('administracion.adm_area')->insertGetId(
                [
                    'codigo'            => $request->codigo,
                    'descripcion'       => $nombre,
                    'estado'            => 1,
                    'fecha_registro'    => $fecha_registro,
                    'id_grupo'          => $request->grupo
                ],
                'id_area'
            );

            if ($id > 0){
                $values = 'ok';
            }else{
                $values = 'null';
            }
        }
        return response()->json($values);
    }
    public function actualizar_area(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $nombre = $request->descripcion;

        $sql = DB::table('administracion.adm_area')
        ->where([['id_grupo', $request->grupo], ['descripcion', $nombre], ['id_area', '!=', $request->id_area]])->get();

        if ($sql->count() > 0){
            $values = 'exist';
        }else{
            $data_cont = DB::table('administracion.adm_area')->where('id_area', $request->id_area)
            ->update([
                'codigo'            => $request->codigo,
                'descripcion'       => $nombre,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro,
                'id_grupo'          => $request->grupo
            ]);
    
            if ($data_cont > 0){
                $values = 'ok';
            }else{
                $values = 'null';
            }
        }
        return response()->json($values);
    }
    public function anular_area($id){
        $data = DB::table('administracion.adm_area')->where('id_area', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

}
