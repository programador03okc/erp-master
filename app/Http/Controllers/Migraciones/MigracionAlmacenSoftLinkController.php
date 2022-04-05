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
            $mode = $request->modelo;
            $file = $request->file('archivo');
            $text_new = '';
            $text_upt = '';

            $import = new AlmacenImport($type, $mode);
            Excel::import($import, $file);

            switch ($type) {
                case 1:
                    $text_new = ' almacenes nuevos';
                    $text_upt = ' almacenes actualizados';
                break;
                case 2:
                    $text_new = ' categorías nuevas';
                    $text_upt = ' categorías actualizadas';
                break;
                case 3:
                    $text_new = ' sub categorías nuevas';
                    $text_upt = ' sub categorías actualizadas';
                break;
                case 4:
                    $text_new = ' unidades de medida nuevas';
                    $text_upt = ' unidades de medida actualizadas';
                break;
                case 5:
                    $text_new = ' productos nuevos';
                    $text_upt = ' productos actualizados';
                break;
                case 6:
                    $text_new = ' series de productos cargados';
                    $text_upt = ' series de productos actualizados';
                break;
                case 7:
                    $text_new = ' saldos de productos cargados';
                    $text_upt = ' saldos de productos actualizados';
                break;
            }

            $response = 'ok';
            $alert = 'success';
            $msj = 'Se ha importado '.$import->getRowCount(1).$text_new.' y '.$import->getRowCount(2).$text_upt;
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
