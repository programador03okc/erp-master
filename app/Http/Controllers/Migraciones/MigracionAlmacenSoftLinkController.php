<?php

namespace App\Http\Controllers\Migraciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\AlmacenImport;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
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

    public function movimientos()
    {
        $main = array();
        $almacenes = DB::table('almacen.alm_almacen')->where('estado', 1)->get();

        foreach ($almacenes as $key) {
            $detail = array();
            $codigo = 'INI-'.$key->codigo.'-22-00';

            //Guardar Movimientos
            $movimiento = DB::table('almacen.mov_alm')->insertGetId([
                'id_almacen'    => $key->id_almacen,
                'id_tp_mov'     => 0,
                'codigo'        => $codigo,
                'fecha_emision' => new Carbon(),
                'usuario'       => 1,
                'estado'        => 1,
                'fecha_registro'=> new Carbon(),
                'revisado'      => 0,
                'id_operacion'  => 16,
            ], 'id_mov_alm');

            $main = ['id_almacen' => $key->id_almacen, 'cod_almacen' => $key->codigo, 'codigo' => $codigo];
            $saldos = DB::table('almacen.alm_prod_ubi')->where('id_almacen', $key->id_almacen)->where('estado', 1)->get();

            foreach ($saldos as $row) {
                // Guardar detalles del movimientos
                $detalle_mov = DB::table('almacen.mov_alm_det')->insertGetId([
                    'id_mov_alm'    => $movimiento,
                    'id_producto'   => $row->id_producto,
                    'cantidad'      => $row->stock,
                    'valorizacion'  => $row->valorizacion,
                    'usuario'       => 1,
                    'estado'        => 1,
                    'fecha_registro'=> new Carbon(),
                ], 'id_mov_alm_det');

                $detail[] = ['id_mov_alm' => $movimiento, 'id_producto' => $row->id_producto, 'cantidad' => $row->stock, 'valorizacion' => $row->valorizacion];
            }

            $data[] = ['almacen' => $main, 'saldos' => $detail];
        }
        return response()->json($data, 200);
    }
}
