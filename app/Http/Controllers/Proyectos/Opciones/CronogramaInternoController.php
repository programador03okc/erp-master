<?php

namespace App\Http\Controllers\Proyectos\Opciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Proyectos\Catalogos\GenericoController;
use Illuminate\Support\Facades\DB;

class CronogramaInternoController extends Controller
{
    function view_cronoint(){
        $unid_program = GenericoController::mostrar_unid_program_cbo();
        return view('proyectos/cronograma/cronoint', compact('unid_program'));
    }
    
    public function nuevo_cronograma($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*','proy_presup.fecha_emision',
            'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_partida.id_cd', '=', $id_presupuesto],
                     ['proy_cd_partida.estado', '!=', 7]])
            ->orderBy('proy_cd_partida.codigo')
            ->get()
            ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                    ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();

        $tp_pred = DB::table('proyectos.proy_tp_predecesora')->where('estado',1)->get();
        $lista = [];
        $partidas = [];
        $fecha_emision = null;
        $i = 1;

        foreach($compo_cd as $comp){
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $fecha_emision = $partida->fecha_emision;
                    $duracion = round(($partida->cantidad / $partida->rendimiento),2,PHP_ROUND_HALF_UP);
                    $fecha_fin = date("Y-m-d",strtotime($partida->fecha_emision."+ ".round($duracion,0,PHP_ROUND_HALF_UP)." days"));
                    $nuevo = [
                        'id_partida' => $partida->id_partida,
                        'id_cu_partida' => $partida->id_cu_partida,
                        'id_presupuesto' => $partida->id_cd,
                        'tipo' => 'cd',
                        'nro_orden' => $i,
                        'dias' => $duracion,
                        'fecha_inicio' => $partida->fecha_emision,
                        'fecha_fin' => $fecha_fin,
                        'tp_predecesora' => 1,
                        'predecesora' => "",
                        'dias_pos' => 0,
                        'codigo' => $partida->codigo,
                        'descripcion' => $partida->descripcion,
                        'cod_compo' => $partida->cod_compo,
                        'cantidad' => $partida->cantidad,
                        'abreviatura' => $partida->abreviatura,
                        'rendimiento' => $partida->rendimiento
                    ];
                    array_push($partidas, $nuevo);
                    $i++;
                }
            }
            $nuevo_comp = [
                'id_cd_compo' => $comp->id_cd_compo,
                'codigo' => $comp->codigo,
                'descripcion' => $comp->descripcion,
                'cod_padre' => $comp->cod_padre,
                'partidas' => $partidas
            ];
            $partidas = [];
            array_push($lista, $nuevo_comp);
        }

        $ci = [
            'id_partida' => null,
            'id_cu_partida' => null,
            'id_presupuesto' => $id_presupuesto,
            'tipo' => 'ci',
            'nro_orden' => $i,
            'dias' => 0,
            'fecha_inicio' => $fecha_emision,
            'fecha_fin' => $fecha_emision,
            'tp_predecesora' => 1,
            'predecesora' => "",
            'dias_pos' => 0,
            'codigo' => null,
            'descripcion' => null,
            'cod_compo' => null,
            'cantidad' => null,
            'abreviatura' => null,
            'rendimiento' => null
        ];
        array_push($lista, $ci);
        $i++;

        $gg = [
            'id_partida' => null,
            'id_cu_partida' => null,
            'id_presupuesto' => $id_presupuesto,
            'tipo' => 'gg',
            'nro_orden' => $i,
            'dias' => 0,
            'fecha_inicio' => $fecha_emision,
            'fecha_fin' => $fecha_emision,
            'tp_predecesora' => 1,
            'predecesora' => "",
            'dias_pos' => 0,
            'codigo' => null,
            'descripcion' => null,
            'cod_compo' => null,
            'cantidad' => null,
            'abreviatura' => null,
            'rendimiento' => null
        ];
        array_push($lista, $gg);

        $presup = DB::table('proyectos.proy_presup')->where('id_presupuesto',$id_presupuesto)->first();

        return response()->json(['lista'=>$lista,'unid_program'=>$presup->unid_program,
        'fecha_inicio_crono'=>$presup->fecha_emision,'tp_pred'=>$tp_pred]);
    }

    public function listar_cronograma($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.proy_cd_pcronog')
            ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cu.id_cu',
            'proy_cd_partida.id_cu_partida','proy_cd_partida.cod_compo',
            'proy_cd_partida.codigo','proy_cd_partida.descripcion','proy_cd_partida.cantidad',
            'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->leftjoin('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
            ->leftjoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_pcronog.id_presupuesto', '=', $id_presupuesto],
                    ['proy_cd_pcronog.estado', '!=', 7]])
            ->orderBy('proy_cd_pcronog.nro_orden')
            ->get()
            ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                    ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();

        $tp_pred = DB::table('proyectos.proy_tp_predecesora')->where('estado',1)->get();
        $lista = [];
        $partidas = [];
        $fini = null;
        $ffin = null;

        foreach($compo_cd as $comp){
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    if ($ffin == null){
                        $ffin = $partida->fecha_fin;
                    } else {
                        if ($ffin < $partida->fecha_fin){
                            $ffin = $partida->fecha_fin;
                        }
                    }
                    if ($fini == null){
                        $fini = $partida->fecha_inicio;
                    } else {
                        if ($fini > $partida->fecha_inicio){
                            $fini = $partida->fecha_inicio;
                        }
                    }
                    array_push($partidas, $partida);
                }
            }
            $nuevo_comp = [
                'id_cd_compo' => $comp->id_cd_compo,
                'codigo' => $comp->codigo,
                'descripcion' => $comp->descripcion,
                'cod_padre' => $comp->cod_padre,
                'fecha_inicio' => $fini,
                'fecha_fin' => $ffin,
                'partidas' => $partidas
            ];
            $partidas = [];
            $fini = null;
            $ffin = null;
            array_push($lista, $nuevo_comp);
        }

        foreach($part_cd as $partida){
            if ($partida->tipo !== 'cd'){
                array_push($lista, $partida);
                // array_push($no_part, $partida);
            }
        }
        // array_push($lista, $no_part);
        $presup = DB::table('proyectos.proy_presup')->where('id_presupuesto',$id_presupuesto)->first();

        return response()->json(['lista'=>$lista,'unid_program'=>$presup->unid_program,
        'fecha_inicio_crono'=>$presup->fecha_inicio_crono,'tp_pred'=>$tp_pred]);
    }

    public function ver_gant($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.proy_cd_pcronog')
        ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cu.id_cu',
        'proy_cd_partida.id_cu_partida','proy_cd_partida.cod_compo',
        'proy_cd_partida.codigo','proy_cd_partida.descripcion','proy_cd_partida.cantidad',
        'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
        ->leftjoin('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
        ->leftjoin('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
        ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
        ->where([['proy_cd_pcronog.id_presupuesto', '=', $id_presupuesto],
                 ['proy_cd_pcronog.estado', '=', 1]])
        ->orderBy('proy_cd_pcronog.nro_orden')
        ->get()
        ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
        ->select('proy_cd_compo.*')
        ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                ['proy_cd_compo.estado', '!=', 7]])
        ->orderBy('proy_cd_compo.codigo')
        ->get();
        
        return response()->json(['partidas'=>$part_cd,'titulos'=>$compo_cd]);
    }

    public function guardar_crono(Request $request){
        $ids = explode(',',$request->id_partida);
        $nro = explode(',',$request->nro_orden);
        $dias = explode(',',$request->dias);
        $fini = explode(',',$request->fini);
        $ffin = explode(',',$request->ffin);
        $tp_pred = explode(',',$request->tp_pred);
        $dias_pos = explode(',',$request->dias_pos);
        $pred = explode(',',$request->predecesora);
        $count = count($ids);
        $id_crono = 0;
        $fecha_inicio_crono = null;

        for ($i=0; $i<$count; $i++){
            if (is_numeric($ids[$i])){
                $id = $ids[$i];
                $tipo = 'cd';
            } else {
                $id = null;
                $tipo = $ids[$i];
            }
            $no = $nro[$i];
            $dia = $dias[$i];
            $ini = $fini[$i];
            $fin = $ffin[$i];
            $tp_pre = $tp_pred[$i];
            $dpos = $dias_pos[$i];
            $pre = $pred[$i];

            if ($fecha_inicio_crono == null){
                $fecha_inicio_crono = $ini;
            } else if ($ini < $fecha_inicio_crono){
                $fecha_inicio_crono = $ini;
            }

            if ($request->modo === 'new'){
                $id_crono = DB::table('proyectos.proy_cd_pcronog')
                ->insert([
                    'id_partida'=>$id,
                    'id_presupuesto'=>$request->id_presupuesto,
                    'tipo'=>$tipo,
                    'nro_orden'=>$no,
                    'fecha_inicio'=>$ini,
                    'fecha_fin'=>$fin,
                    'dias'=> ($dia!=='' ? $dia : 0),
                    'tp_predecesora'=>$tp_pre,
                    'dias_pos'=> ($dpos!=='' ? $dpos : 0),
                    'predecesora'=> ($pre!=='' ? $pre : 0),
                    'fecha_registro'=>date('Y-m-d'),
                    'estado'=>1
                ]);
            } 
            else {
                $crono = DB::table('proyectos.proy_cd_pcronog')
                ->where([['id_partida','=',$id],['tipo','=',$tipo]])
                ->first();

                $id_crono = DB::table('proyectos.proy_cd_pcronog')
                ->where('id_pcronog',$crono->id_pcronog)
                ->update([
                    'fecha_inicio'=>$ini,
                    'fecha_fin'=>$fin,
                    'tp_predecesora'=>$tp_pre,
                    'dias_pos'=>$dpos,
                    'predecesora'=>$pre,
                    'dias'=>$dia
                ]);
            }
        }
        DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$request->id_presupuesto)
        ->update([  'cronograma'=>true,
                    'unid_program'=>$request->unid_program,
                    'fecha_inicio_crono'=>$fecha_inicio_crono
                    ]);

        return response()->json($id_crono);
    }

    public function actualizar_partidas_cronograma($id_presupuesto)
    {
        try {
            DB::beginTransaction();

            $part_cd = DB::table('proyectos.proy_cd_partida')
                ->select('proy_cd_partida.*','proy_presup.fecha_emision','proy_cu_partida.rendimiento')
                ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd_partida.id_cd')
                // ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
                ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
                // ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
                ->where([['proy_cd_partida.id_cd', '=', $id_presupuesto],
                        ['proy_cd_partida.estado', '!=', 7]])
                ->orderBy('proy_cd_partida.codigo')
                ->get()
                ->toArray();

            $i = 1;

            foreach($part_cd as $partida)
            {
                $par_crono = DB::table('proyectos.proy_cd_pcronog')
                    ->select('proy_cd_pcronog.*','proy_cd_partida.cantidad','proy_cu_partida.rendimiento')
                    ->join('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
                    ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
                    ->where([['proy_cd_pcronog.id_partida','=',$partida->id_partida],['proy_cd_pcronog.tipo','=','cd']])
                    ->first();

                if ($par_crono !== null){
                    
                    $fecha_inicio = $par_crono->fecha_inicio;
                    $duracion = round(($par_crono->cantidad / $par_crono->rendimiento),2,PHP_ROUND_HALF_UP);
                    $fecha_fin = date("Y-m-d",strtotime($fecha_inicio."+ ".round($duracion,0,PHP_ROUND_HALF_UP)." days"));

                    DB::table('proyectos.proy_cd_pcronog')
                    ->where('id_pcronog',$par_crono->id_pcronog)
                    ->update([
                        'nro_orden'=>$i,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'tp_predecesora'=>1,
                        'dias_pos'=>0,
                        'predecesora'=>"",
                        'dias'=>$duracion
                    ]);
                }
                else {
                    $fecha_inicio = $partida->fecha_emision;
                    $duracion = round(($partida->cantidad / $partida->rendimiento),2,PHP_ROUND_HALF_UP);
                    $fecha_fin = date("Y-m-d",strtotime($fecha_inicio."+ ".round($duracion,0,PHP_ROUND_HALF_UP)." days"));

                    DB::table('proyectos.proy_cd_pcronog')
                    ->insert([
                        'id_partida'=>$partida->id_partida,
                        'id_presupuesto'=>$id_presupuesto,
                        'tipo'=>'cd',
                        'nro_orden'=>$i,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'dias'=> $duracion,
                        'tp_predecesora'=>1,
                        'dias_pos'=>0,
                        'predecesora'=>"",
                        'fecha_registro'=>date('Y-m-d'),
                        'estado'=>1
                    ]);
                }
                $i++;
            }
            DB::commit();
            return response()->json($id_presupuesto);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    public function anular_crono($id_presupuesto)
    {
        $presup = DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$id_presupuesto)
        ->first();
        $anula = 0;

        if ($presup->cronoval == false)
        {
            $anula = DB::table('proyectos.proy_presup')
            ->where('id_presupuesto',$id_presupuesto)
            ->update([  'cronograma'=>false,
                        'unid_program'=>null,
                        'fecha_inicio_crono'=>null
            ]);
            $anula = DB::table('proyectos.proy_cd_pcronog')
            ->where('id_presupuesto',$id_presupuesto)
            ->update([ 'estado' => 7 ]);
        }

        return response()->json($anula);
    }

    public function listar_pres_crono($tiene_crono, $tp_presup)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_op_com.descripcion', 'proy_presup_importe.sub_total',
                     'sis_moneda.simbolo','adm_contri.razon_social')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.estado', '!=', 7],
                ['proy_presup.id_tp_presupuesto', '=', $tp_presup],
                ['proy_presup.cronograma', '=', ($tiene_crono == 0 ? false : true)]])
                ->orderBy('proy_presup.fecha_emision','desc')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
}
