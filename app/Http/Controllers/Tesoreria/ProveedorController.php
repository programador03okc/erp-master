<?php

namespace App\Http\Controllers\Tesoreria;

use App\Models\Tesoreria\Contribuyente;
use App\Models\Tesoreria\Proveedor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dump($request->toArray());
        //
        /*
		 * let dataSave = {
						    id_tipo: $('#reg_prov_tipo_id').val(),
						    ruc: $('#reg_prov_ruc').val(),
						    razon_social: $('#reg_prov_razon_social').val(),
						    direccion: $('#reg_prov_direccion').val(),
						    telefono: $('#reg_prov_telefono').val(),
						    celular: $('#reg_prov_celular').val(),
						};
		 */
        $guardar = false;
        DB::beginTransaction();
        $proveedor = new Proveedor();
        if ($request->get('id_contribuyente') != '') {
            $proveedor->id_contribuyente = $request->get('id_contribuyente');
            $proveedor->estado = 1;
            $proveedor->fecha_registro = now();
        } else {
            $contribuyente = new Contribuyente();
            $contribuyente->id_tipo_contribuyente = $request->get('id_tipo');
            $contribuyente->id_doc_identidad = 2;
            $contribuyente->nro_documento = $request->get('nro_documento');
            $contribuyente->razon_social = $request->get('razon_social');
            $contribuyente->telefono = $request->get('telefono');
            $contribuyente->celular = $request->get('celular');
            $contribuyente->direccion_fiscal = $request->get('direccion');
            $contribuyente->estado = 1;
            $contribuyente->fecha_registro = now();

            if ($contribuyente->save()) {
                $proveedor->id_contribuyente = $contribuyente->id_contribuyente;
                $proveedor->estado = 1;
                $proveedor->fecha_registro = now();
                $guardar = true;
            } else {
                $responseData = ['error' => true, 'msg' => 'Error en Contribuyente', 'data' => []];
                DB::rollBack();
            }
        }

        //dd($proveedor->toArray());

        if ($proveedor->save() && $guardar) {
            $responseData = ['error' => false, 'msg' => '', 'data' => [
                'id_proveedor' => $proveedor->id_proveedor
            ]];
            DB::commit();
        } else {
            $responseData = ['error' => true, 'msg' => 'Error en Proveedor', 'data' => []];
            DB::rollBack();
        }

        return response()->json($responseData);
    }

    public function guardarProveedor(Request $request)
    {
        try {
            DB::beginTransaction();
            $array = [];

            $contribuyente = DB::table('contabilidad.adm_contri')
                ->where('nro_documento', trim($request->nuevo_nro_documento))
                ->first();

            if ($contribuyente !== null) {
                $proveedor = DB::table('logistica.log_prove')
                    ->where([
                        ['id_contribuyente', '=', $contribuyente->id_contribuyente],
                        ['estado', '=', 1]
                    ])
                    ->first();
                if ($proveedor !== null) {
                    $array = array(
                        'tipo' => 'warning',
                        'mensaje' => 'Ya existe el RUC ingresado.',
                    );
                } else {
                    DB::table('logistica.log_prove')
                        ->insert([
                            'id_contribuyente' => $contribuyente->id_contribuyente,
                            'estado' => 1,
                            'fecha_registro' => date('Y-m-d H:i:s')
                        ]);
                    $array = array(
                        'tipo' => 'success',
                        'mensaje' => 'Ya se encontró dicho contribuyente. Se guardó como proveedor correctamente',
                    );
                }
            } else {
                $id_contribuyente = DB::table('contabilidad.adm_contri')
                    ->insertGetId(
                        [
                            'nro_documento' => trim($request->nuevo_nro_documento),
                            'id_doc_identidad' => $request->id_doc_identidad,
                            'razon_social' => strtoupper(trim($request->nuevo_razon_social)),
                            'telefono' => trim($request->telefono),
                            'direccion_fiscal' => trim($request->direccion_fiscal),
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => 1,
                            'transportista' => false
                        ],
                        'id_contribuyente'
                    );

                DB::table('logistica.log_prove')
                    ->insert([
                        'id_contribuyente' => $id_contribuyente,
                        'estado' => 1,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ]);

                $array = array(
                    'tipo' => 'success',
                    'mensaje' => 'Se guardó el proveedor correctamente',
                );
            }
            DB::commit();
            return response()->json($array);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(
                array(
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un problema. Por favor intente de nuevo',
                    'error' => $e->getMessage()
                )
            );
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
