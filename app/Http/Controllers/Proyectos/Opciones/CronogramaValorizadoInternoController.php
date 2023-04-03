<?php

namespace App\Http\Controllers\Proyectos\Opciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Proyectos\Catalogos\GenericoController;
use DateTime;
use Illuminate\Support\Facades\DB;

class CronogramaValorizadoInternoController extends Controller
{
    function view_cronovalint(){
        $unid_program = GenericoController::mostrar_unid_program_cbo();
        return view('proyectos/cronograma/cronovalint', compact('unid_program'));
    }

    
    public function nuevo_crono_valorizado($id_presupuesto)
    {
        $part_cd = DB::table('proyectos.proy_cd_pcronog')
            ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cu.id_cu',
            'proy_cd_partida.id_cu_partida','proy_cd_partida.cod_compo','proy_cd_partida.codigo',
            'proy_cd_partida.descripcion','proy_cd_partida.cantidad','proy_cd_partida.importe_parcial',
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
                'partidas' => $partidas
            ];
            array_push($lista, $nuevo_comp);
            $partidas = [];
        }

        foreach($part_cd as $partida){
            if ($partida->tipo !== 'cd'){
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
                array_push($lista, $partida);
            } 
        }
        $total = DB::table('proyectos.proy_presup_importe')
        ->select('proy_presup_importe.*','sis_moneda.simbolo')
        ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_presup_importe.id_presupuesto')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
        ->where('proy_presup_importe.id_presupuesto',$id_presupuesto)->first();

        return response()->json([ 'lista'=>$lista, 'fecha_inicio'=>$fini, 'fecha_fin'=>$ffin, 'moneda'=>$total->simbolo,
        'total_ci'=>$total->total_ci, 'total_gg'=>$total->total_gg,'sub_total'=>$total->sub_total ]);
    }

    public function mostrar_crono_valorizado($id_presupuesto)
    {
        $partidas = DB::table('proyectos.proy_cd_pcronog')
            ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cd_partida.cod_compo',
            'proy_cd_partida.codigo','proy_cd_partida.descripcion','proy_cd_partida.cantidad',
            'proy_cd_partida.importe_parcial','alm_und_medida.abreviatura')
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

        $titulos = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id_presupuesto],
                     ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();
        
        $lista = [];
        $list_par = [];
        $fini = null;
        $ffin = null;
        
        foreach($titulos as $ti){
            foreach($partidas as $par){
                if ($ti->codigo == $par->cod_compo){
                    if ($ffin == null){
                        $ffin = $par->fecha_fin;
                    } else {
                        if ($ffin < $par->fecha_fin){
                            $ffin = $par->fecha_fin;
                        }
                    }
                    if ($fini == null){
                        $fini = $par->fecha_inicio;
                    } else {
                        if ($fini > $par->fecha_inicio){
                            $fini = $par->fecha_inicio;
                        }
                    }
                    $periodos = DB::table('proyectos.proy_cd_pcronoval')
                    ->where([['id_pcronog','=',$par->id_pcronog],['estado','=',1]])
                    ->get();

                    $nuevo_par = [
                        // 'id_pcronoval' => $par->id_pcronoval,
                        'id_pcronog' => $par->id_pcronog,
                        'codigo' => $par->codigo,
                        'descripcion' => $par->descripcion,
                        'dias' => $par->dias,
                        'importe_parcial' => $par->importe_parcial,
                        'periodos' => (isset($periodos) ? $periodos : [])
                    ];

                    array_push($list_par, $nuevo_par);
                }
            }
            $nuevo_comp = [
                'id_cd_compo' => $ti->id_cd_compo,
                'codigo' => $ti->codigo,
                'descripcion' => $ti->descripcion,
                'cod_padre' => $ti->cod_padre,
                'partidas' => $list_par
            ];
            array_push($lista, $nuevo_comp);
            $list_par = [];
        }
    
        $total = DB::table('proyectos.proy_presup_importe')
        ->select('proy_presup_importe.*','proy_presup.crono_cantidad','proy_presup.crono_unid_program','sis_moneda.simbolo')
        ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_presup_importe.id_presupuesto')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
        ->where('proy_presup_importe.id_presupuesto',$id_presupuesto)->first();

        foreach($partidas as $par){
            if ($par->id_partida == null){
                if ($ffin == null){
                    $ffin = $par->fecha_fin;
                } else {
                    if ($ffin < $par->fecha_fin){
                        $ffin = $par->fecha_fin;
                    }
                }
                if ($fini == null){
                    $fini = $par->fecha_inicio;
                } else {
                    if ($fini > $par->fecha_inicio){
                        $fini = $par->fecha_inicio;
                    }
                }
                $periodos = DB::table('proyectos.proy_cd_pcronoval')
                ->where([['id_pcronog','=',$par->id_pcronog],['estado','=',1]])
                ->get();

                $nuevo_par = [
                    // 'id_pcronoval' => $par->id_pcronoval,
                    'id_pcronog' => $par->id_pcronog,
                    'codigo' => ($par->tipo == 'ci' ? 'CI' : 'GG'),
                    'descripcion' => ($par->tipo == 'ci' ? 'COSTOS INDIRECTOS' : 'GASTOS GENERALES'),
                    'dias' => $par->dias,
                    'importe_parcial' => ($par->tipo == 'ci' ? $total->total_ci : $total->total_gg),
                    'periodos' => (isset($periodos) ? $periodos : [])
                ];
                array_push($lista, $nuevo_par);
            } 
        }

        return response()->json([ 'lista'=>$lista, 'fecha_inicio'=>$fini, 'fecha_fin'=>$ffin, 
        'total'=>$total->sub_total, 'moneda'=>$total->simbolo, 'cantidad'=>$total->crono_cantidad, 
        'unid_program'=>$total->crono_unid_program ]);
    }

    
    public function download_cronoval($id_presupuesto, $nro_dias){

        $part_cd = DB::table('proyectos.proy_cd_pcronog')
            ->select('proy_cd_pcronog.*','proy_presup.fecha_emision','proy_cu.id_cu',
            'proy_cd_partida.id_cu_partida','proy_cd_partida.cod_compo','proy_cd_partida.codigo',
            'proy_cd_partida.descripcion','proy_cd_partida.cantidad','proy_cd_partida.importe_parcial',
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

        $lista = [];
        $partidas = [];
        $fini_crono = null;
        $ffin_crono = null;

        foreach($compo_cd as $comp){
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    if ($ffin_crono == null){
                        $ffin_crono = $partida->fecha_fin;
                    } else {
                        if ($ffin_crono < $partida->fecha_fin){
                            $ffin_crono = $partida->fecha_fin;
                        }
                    }
                    if ($fini_crono == null){
                        $fini_crono = $partida->fecha_inicio;
                    } else {
                        if ($fini_crono > $partida->fecha_inicio){
                            $fini_crono = $partida->fecha_inicio;
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
                'partidas' => $partidas
            ];
            $partidas = [];
            array_push($lista, $nuevo_comp);
        }

        foreach($part_cd as $partida){
            if ($partida->tipo !== 'cd'){
                array_push($lista, $partida);
            } 
        }
        $total = DB::table('proyectos.proy_presup_importe')->where('id_presupuesto',$id_presupuesto)->first();

        $fecha1 = new DateTime($fini_crono);
        $fecha2 = new DateTime($ffin_crono);
        $duracion_total = $fecha1->diff($fecha2);
        $array_periodo = [];
        
        if ($duracion_total->days > $nro_dias){
            $length = 0;
            if ($nro_dias > 0){
                $length = ($duracion_total->days / $nro_dias);
            }
            $periodo=[];
            $suma_rango = 0;
            $i=0;
            $fini = $fini_crono;
            $ffin = date('Y-m-d');

            for ($i=1;$i<=$length;$i++) {
                $suma_rango += $nro_dias;
                // $duracion = round(($partida->cantidad / $partida->rendimiento),2,PHP_ROUND_HALF_UP);
                $ffin = date("Y-m-d",strtotime($fini."+ ".round($nro_dias,0,PHP_ROUND_HALF_UP)." days"));

                // $ffin = strtotime( '+'+$nro_dias+' day' , strtotime( $fini ) );
                // $ffin = date ( 'Y-m-d' , $ffin );
                $periodo = [
                    'nro' => $i,
                    'nro_dias'=> $nro_dias,
                    'dias'=> $suma_rango,
                    'fecha_inicio'=> $fini,
                    'fecha_fin'=> $ffin
                ];
                array_push($array_periodo, $periodo);
                $fini = $ffin;
            }
            $dif = $duracion_total->days - $suma_rango;

            if ($dif > 0){
                $suma_rango += $dif;
                $ffin = date("Y-m-d",strtotime($fini."+ ".round($nro_dias,0,PHP_ROUND_HALF_UP)." days"));
                // $ffin = strtotime( '+'+$dif+' day' , strtotime( $fini ) );
                // $ffin = date ( 'Y-m-d' , $ffin );
                $periodo = [
                    'nro'=> $i,
                    'nro_dias'=> $dif,
                    'dias'=> $suma_rango,
                    'fecha_inicio'=> $fini,
                    'fecha_fin'=> $ffin
                ];
                array_push($array_periodo, $periodo);
            }
        }

        return ['array_periodo'=>$array_periodo];

    }

    public function guardar_cronoval_presupuesto(Request $request)
    {
        $ids = explode(',',$request->id_pcronoval);
        $par = explode(',',$request->id_pcronog);
        $per = explode(',',$request->periodo);
        $por = explode(',',$request->porcentaje);
        $imp = explode(',',$request->importe);
        $count = count($ids);
        $data = 0;

        for ($i=0; $i<$count; $i++){
            $id = $ids[$i];
            $pa = $par[$i];
            $pe = $per[$i];
            $po = $por[$i];
            $im = $imp[$i];

            if ($request->modo === 'new'){
                $data = DB::table('proyectos.proy_cd_pcronoval')
                ->insert([
                    'id_pcronog'=>$pa,
                    'id_presupuesto'=>$request->id_presupuesto,
                    'periodo'=>$pe,
                    'porcentaje'=>$po,
                    'importe'=>$im,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1
                ]);
            } 
            else {
                $data = DB::table('proyectos.proy_cd_pcronoval')
                ->where('id_pcronoval',$id)
                ->update([
                    'periodo'=>$pe,
                    'porcentaje'=>$po,
                    'importe'=>$im,
                ]);
            }
        }

        if ($request->modo === 'new'){
            $nro = explode(',',$request->pnro);
            $ndias = explode(',',$request->pnro_dias);
            $dias = explode(',',$request->pdias);
            $ini = explode(',',$request->pfini);
            $fin = explode(',',$request->pffin);
            $tot = explode(',',$request->ptotal);
            $cnt = count($nro);
    
            for ($j=0; $j<$cnt; $j++){
                $nr = $nro[$j];
                $nd = $ndias[$j];
                $di = $dias[$j];
                $in = $ini[$j];
                $fi = $fin[$j];
                $to = $tot[$j];

                DB::table('proyectos.proy_presup_periodos')
                ->insert([
                    'id_presupuesto'=>$request->id_presupuesto,
                    'numero'=>$nr,
                    'nro_dias'=>$nd,
                    'dias_acum'=>$di,
                    'fecha_inicio'=>$in,
                    'fecha_fin'=>$fi,
                    'total'=>$to,
                    'fecha_registro'=>date('Y-m-d H:i:s'),
                    'estado'=>1
                ]);
            }
        }

        DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$request->id_presupuesto)
        ->update([ 'cronoval'=>true,
                   'crono_cantidad'=>$request->cantidad,
                   'crono_unid_program'=>$request->unid_program ]);

        return response()->json($data);
    }

    public function anular_cronoval($id_presupuesto)
    {
        $anula = DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$id_presupuesto)
        ->update([  'cronoval'=>false,
                    'crono_cantidad'=>null,
                    'crono_unid_program'=>null
        ]);
        $anula = DB::table('proyectos.proy_presup_periodos')
        ->where('id_presupuesto',$id_presupuesto)
        ->update([ 'estado' => 7 ]);

        $anula = DB::table('proyectos.proy_cd_pcronoval')
        ->where('id_presupuesto',$id_presupuesto)
        ->update([ 'estado' => 7 ]);

        return response()->json($anula);
    }

    
    public function listar_pres_cronoval($tiene_crono, $tp_presup)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_op_com.descripcion', 'proy_presup_importe.sub_total',
                     'sis_moneda.simbolo')//,'adm_contri.razon_social'
            // ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            // ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            // ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->where([['proy_presup.estado', '!=', 7],
                ['proy_presup.id_tp_presupuesto', '=', $tp_presup],
                ['proy_presup.cronograma', '=', true],
                ['proy_presup.cronoval', '=', ($tiene_crono == 0 ? false : true)]])
                ->orderBy('proy_presup.fecha_emision','desc')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    
}
