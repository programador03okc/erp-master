<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ActualizaSaldoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ubi = DB::table('almacen.alm_prod_ubi')
                ->where('estado',1)
                ->get();
                
        foreach($ubi as $u){
            //Traer stockActual
            $saldo = $this->saldo_actual_almacen($u->id_producto, $u->id_almacen);
            $valor = $this->valorizacion_almacen($u->id_producto, $u->id_almacen);
            $cprom = ($saldo > 0 ? $valor/$saldo : 0);

            DB::table('almacen.alm_prod_ubi')
                    ->where('id_prod_ubi',$u->id_prod_ubi)
                    ->update([  'stock' => $saldo,
                                'valorizacion' => $valor,
                                'costo_promedio' => $cprom
                        ]);
        }
    }

    public static function saldo_actual_almacen($id_producto, $id_almacen){
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as ingresos"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm.id_almacen','=',$id_almacen],
                     ['mov_alm.id_tp_mov','<=',1],//ingreso o carga inicial
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as salidas"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm.id_almacen','=',$id_almacen],
                     ['mov_alm.id_tp_mov','=',2],//salida
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $saldo = 0;
        if ($ing->ingresos !== null) $saldo += $ing->ingresos;
        if ($sal->salidas !== null) $saldo -= $sal->salidas;

        return $saldo;
    }

    public static function valorizacion_almacen($id_producto, $id_almacen){
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as ingresos"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm.id_almacen','=',$id_almacen],
                     ['mov_alm.id_tp_mov','<=',1],//ingreso o carga inicial
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as salidas"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm.id_almacen','=',$id_almacen],
                     ['mov_alm.id_tp_mov','=',2],//salida
                     ['mov_alm_det.estado','=',1]])
            ->first();
        
        $valorizacion = 0;
        if ($ing->ingresos !== null) $valorizacion += $ing->ingresos;
        if ($sal->salidas !== null) $valorizacion -= $sal->salidas;

        return $valorizacion;
    }

}
