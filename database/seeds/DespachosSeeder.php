<?php

use App\Models\Almacen\Requerimiento;
use App\Models\Distribucion\OrdenDespacho;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DespachosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $requerimientos = DB::table('almacen.alm_req')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.id_sede',
                'alm_req.id_cliente',
                'alm_req.id_almacen',
                'alm_req.tiene_transformacion',
                'oc_propias_view.fecha_salida',
                'oc_propias_view.fecha_llegada',
                'oc_propias_view.flete_real',
                'oc_propias_view.transportista'
            )
            ->join('mgcp_cuadro_costos.cc', 'cc.id', '=', 'alm_req.id_cc')
            ->leftjoin('mgcp_oportunidades.oportunidades', 'oportunidades.id', '=', 'cc.id_oportunidad')
            ->leftJoin('mgcp_ordenes_compra.oc_propias_view', 'oc_propias_view.id_oportunidad', '=', 'cc.id_oportunidad')
            ->where('alm_req.id_requerimiento')
            ->get();

        foreach ($requerimientos as $req) {

            $ordenDespacho = OrdenDespacho::where([
                ['id_requerimiento', '=', $req->id_requerimiento],
                ['aplica_cambios', '=', false],
                ['estado', '!=', 7]
            ])->first();

            if ($ordenDespacho == null) {

                $usuario = Auth::user()->id_usuario;
                $fechaRegistro = new Carbon(); //date('Y-m-d H:i:s');
                $id_estado_envio = 1; //despacho elaborado

                $req = Requerimiento::where('id_requerimiento', $req->id_requerimiento)->first();
                $ordenDespacho = new OrdenDespacho();
                $ordenDespacho->id_sede = $req->id_sede;
                $ordenDespacho->id_requerimiento = $req->id_requerimiento;
                $ordenDespacho->id_cliente = $req->id_cliente;
                $ordenDespacho->id_almacen = $req->id_almacen;
                $ordenDespacho->codigo = OrdenDespacho::ODnextId($req->id_almacen, false, $ordenDespacho->id_od);
                $ordenDespacho->aplica_cambios = false;
                $ordenDespacho->registrado_por = $usuario;
                $ordenDespacho->fecha_despacho = $fechaRegistro;
                $ordenDespacho->fecha_registro = $fechaRegistro;
                $ordenDespacho->estado = 1;
                $ordenDespacho->id_estado_envio = $id_estado_envio;
                $ordenDespacho->save();
                //Agrega accion en requerimiento
                DB::table('almacen.alm_req_obs')
                    ->insert([
                        'id_requerimiento' => $req->id_requerimiento,
                        'accion' => 'DESPACHO EXTERNO',
                        'descripcion' => 'Se generó la Orden de Despacho Externa',
                        'id_usuario' => $usuario,
                        'fecha_registro' => $fechaRegistro
                    ]);

                $detalle = DB::table('almacen.alm_det_req')
                    ->where([
                        ['id_requerimiento', '=', $req->id_requerimiento],
                        ['tiene_transformacion', '=', $req->tiene_transformacion],
                        ['estado', '!=', 7]
                    ])
                    ->get();

                foreach ($detalle as $d) {
                    DB::table('almacen.orden_despacho_det')
                        ->insert([
                            'id_od' => $ordenDespacho->id_od,
                            'id_detalle_requerimiento' => $d->id_detalle_requerimiento,
                            'cantidad' => $d->cantidad,
                            'transformado' => $d->tiene_transformacion,
                            'estado' => 1,
                            'fecha_registro' => $fechaRegistro
                        ]);

                    DB::table('almacen.alm_det_req')
                        ->where('id_detalle_requerimiento', $d->id_detalle_requerimiento)
                        ->update(['estado' => 23]); //despacho externo
                }

                $ordenDespacho->codigo = OrdenDespacho::ODnextId($req->id_almacen, false, $ordenDespacho->id_od);
                $ordenDespacho->save();

                DB::table('almacen.alm_req')
                    ->where('id_requerimiento', $req->id_requerimiento)
                    ->update(['estado' => 23]); //despacho externo

                DB::table('almacen.orden_despacho_obs')
                    ->insert([
                        'id_od' => $ordenDespacho->id_od,
                        'accion' => 1,
                        'observacion' => 'Fue despachado con ' . $ordenDespacho->codigo,
                        'registrado_por' => $usuario,
                        'fecha_registro' => $fechaRegistro
                    ]);

                DB::table('almacen.orden_despacho_obs')
                    ->insert([
                        'id_od' => $ordenDespacho->id_od,
                        'accion' => 8,
                        'observacion' => 'Migrado desde MGCP',
                        'registrado_por' => 64,
                        'fecha_registro' => $fechaRegistro
                    ]);
            }
        }
    }
}
