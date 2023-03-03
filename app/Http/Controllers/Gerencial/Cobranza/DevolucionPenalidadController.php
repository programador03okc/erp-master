<?php
namespace App\Http\Controllers\Gerencial\Cobranza;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Gerencial\PenalidadCobro;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class DevolucionPenalidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('gerencial.cobranza.devolucion', get_defined_vars());
    }

    public function lista()
    {
        $data = PenalidadCobro::all();
        return DataTables::of($data)
        ->addColumn('empresa', function ($data) { return $data->cobranza->empresa->codigo; })
        ->addColumn('ocam', function ($data) { return $data->cobranza->ocam; })
        ->addColumn('cliente', function ($data) { return $data['cliente']['contribuyente']['razon_social']; })
        ->addColumn('factura', function ($data) { return $data->cobranza->factura; })
        ->addColumn('oc_fisica', function ($data) { return $data->cobranza->oc_fisica; })
        ->addColumn('siaf', function ($data) { return $data->cobranza->siaf; })
        ->addColumn('moneda', function($data) { return ($data->cobranza->moneda == 1) ? 'S/' : 'USD'; })
        ->addColumn('accion', function ($data) {
            $button = '';
            if ($data->estado == 'PENDIENTE') {
                $button .=
                    '<button type="button" class="btn btn-success btn-xs cobrar" data-id="'.$data->id.'">
                        <span class="fas fa-check"></span>
                    </button>';
            }
            return $button;
        })
        ->rawColumns(['accion'])
        ->make(true);
    }
}