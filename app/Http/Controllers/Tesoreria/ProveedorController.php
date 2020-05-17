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
		if ($request->get('id_contribuyente') != ''){
			$proveedor->id_contribuyente = $request->get('id_contribuyente') ;
			$proveedor->estado = 1;
			$proveedor->fecha_registro = now();
		}
		else{
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

			if ($contribuyente->save()){
				$proveedor->id_contribuyente = $contribuyente->id_contribuyente;
				$proveedor->estado = 1;
				$proveedor->fecha_registro = now();
				$guardar = true;
			}
			else{
				$responseData = ['error' => true, 'msg' => 'Error en Contribuyente', 'data' => []];
				DB::rollBack();
			}
		}

		//dd($proveedor->toArray());

		if ($proveedor->save() && $guardar){
			$responseData = ['error' => false, 'msg' => '', 'data' => [
				'id_proveedor' => $proveedor->id_proveedor
			]];
			DB::commit();
		}
		else{
			$responseData = ['error' => true, 'msg' => 'Error en Proveedor', 'data' => []];
			DB::rollBack();
		}

		return response()->json($responseData);




		/*
		 * 'id_tipo_contribuyente',
        'id_doc_identidad',
        'razon_social',
        'telefono',
        'celular',
        'direccion_fiscal',
        'ubigeo',
        'id_pais',
        'estado',
        'fecha_registro'
		 */

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
