<?php

namespace App\Http\Controllers\Migraciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\AlmacenImport;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class MigracionAlmacenSoftLinkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('Migraciones/migrar-softlink');
    }

    public function importar(Request $request)
    {
        try {
            $type = $request->tipo;
            $file = $request->file('archivo');
            $text = '';

            $import = new AlmacenImport($type);
            Excel::import($import, $file);

            switch ($type) {
                case 1:
                    $text = ' almacenes nuevos';
                break;
                case 2:
                    $text = ' categorías nuevas';
                break;
                case 3:
                    $text = ' sub categorías nuevas';
                break;
                case 4:
                    $text = ' unidades de medida nuevas';
                break;
                case 5:
                    $text = ' productos nuevos';
                break;
                case 6:
                    $text = ' series de productos cargados';
                break;
                case 7:
                    $text = ' saldos de productos cargados';
                break;
            }

            $response = 'ok';
            $alert = 'success';
            $msj = 'Se ha importado '.$import->getRowCount().$text;
            $error = '';
        } catch (Exception $ex) {
            $response = 'error';
            $alert = 'danger';
            $msj ='Hubo un problema al importar. Por favor intente de nuevo';
            $error = $ex;
        }
        return response()->json(array('response' => $response, 'alert' => $alert, 'message' => $msj, 'error' => $error), 200);
    }
}
