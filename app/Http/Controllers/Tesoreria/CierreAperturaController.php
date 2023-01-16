<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CierreAperturaController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index()
	{
		return view('tesoreria.cierre_apertura.lista');
	}

	public function listar()
    {
        $data = DB::table('administracion.adm_periodo');

        return DataTables::of($data)
        ->addColumn('accion', function ($data) { 
			return 
            '<div class="btn-group" role="group">
                <button type="button" class="btn btn-xs btn-primary" onclick="editar('.$data->id_periodo.');"><span class="fas fa-edit"></span></button>
            </div>';
        })->rawColumns(['accion'])->make(true);
    }

}
