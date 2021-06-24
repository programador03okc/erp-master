<?php

namespace App\Http\Controllers\Proyectos\Opciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Proyectos\Catalogos\GenericoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PartidasController extends Controller
{
    
    public function guardar_partida_cd(Request $request)
    {
        $rspta = DB::table('proyectos.proy_cd_partida')
            ->insertGetId([
                'id_cd' => $request->id_cd,
                'id_cu_partida' => $request->id_cu_partida,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'id_sistema' => $request->sis,
                'cod_compo' => $request->comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_partida'
            );
        $this->suma_partidas_cd($request->comp, $request->id_cd);
        return response()->json($rspta);
    }
    public function guardar_partida_ci(Request $request)
    {
        $data = DB::table('proyectos.proy_ci_detalle')
            ->insertGetId([
                'id_ci' => $request->id_ci,
                'id_cu_partida' => $request->id_cu_partida,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'unid_medida' => ($request->unid_medida !== null ? $request->unid_medida : 0),
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_ci_detalle'
            );
        $this->suma_partidas_ci($request->comp, $request->id_ci);

        return response()->json($data);
    }
    public function guardar_partida_gg(Request $request)
    {
        $data = DB::table('proyectos.proy_gg_detalle')
            ->insertGetId([
                'id_gg' => $request->id_gg,
                'id_cu_partida' => $request->id_cu_partida,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_gg_detalle'
            );
        $this->suma_partidas_gg($request->comp, $request->id_gg);

        return response()->json($data);
    }
    public function update_partida_cd(Request $request){

        $data = DB::table('proyectos.proy_cd_partida')
            ->where('id_partida', $request->id_partida)
            ->update([
                // 'id_cu' => $request->id_cu,
                // 'descripcion' => $request->descripcion,
                // 'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->importe_unitario,
                'importe_parcial' => $request->importe_parcial,
                'id_sistema' => $request->id_sistema
                ]);
        $this->suma_partidas_cd($request->comp, $request->id_cd);
        return response()->json($data);
    }

    public function update_partida_ci(Request $request){

        $data = DB::table('proyectos.proy_ci_detalle')
            ->where('id_ci_detalle', $request->id_ci_detalle)
            ->update([
                // 'id_cu_partida' => $request->id_cu_partida,
                'descripcion' => strtoupper($request->descripcion),
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp
                ]);

        $this->suma_partidas_ci($request->comp, $request->id_ci);

        return response()->json($data);
    }
    public function update_partida_gg(Request $request){

        $data = DB::table('proyectos.proy_gg_detalle')
            ->where('id_gg_detalle', $request->id_gg_detalle)
            ->update([
                // 'id_cu_detalle' => $request->id_cu_detalle,
                'descripcion' => strtoupper($request->descripcion),
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp
                ]);

        $this->suma_partidas_gg($request->comp, $request->id_gg);

        return response()->json($data);
    }
    public function anular_partida_cd(Request $request){

        $data = DB::table('proyectos.proy_cd_partida')
            ->where('proy_cd_partida.id_partida', $request->id_partida)
            ->update(['estado' => 7]);

        $this->suma_partidas_cd($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_partida_ci(Request $request){

        $data = DB::table('proyectos.proy_ci_detalle')
            ->where('proy_ci_detalle.id_ci_detalle', $request->id_ci_detalle)
            ->update(['estado' => 7]);

        $this->suma_partidas_ci($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_partida_gg(Request $request){

        $data = DB::table('proyectos.proy_gg_detalle')
            ->where('proy_gg_detalle.id_gg_detalle', $request->id_gg_detalle)
            ->update(['estado' => 7]);

        $this->suma_partidas_gg($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }

    
    public function suma_partidas_cd($cod_padre, $id_cd)
    {
        $this->suma_padres_cd($cod_padre, $id_cd);
        $update = $this->actualiza_totales($id_cd);
        return $update;
    }

    public function suma_partidas_ci($cod_padre, $id_ci)
    {
        $this->suma_padres_ci($cod_padre, $id_ci);
        $update = $this->actualiza_totales($id_ci);
        return $update;
    }
    
    public function suma_partidas_gg($padre, $id_gg)
    {
        $this->suma_padres_gg($padre, $id_gg);
        $update = $this->actualiza_totales($id_gg);
        return $update;
    }

    
    public function suma_padres_cd($cod_padre, $id_cd)
    {
        $part = DB::table('proyectos.proy_cd_partida')
        ->select(DB::raw('SUM(proy_cd_partida.importe_parcial) as suma_partidas'))
        ->where([['proy_cd_partida.cod_compo', '=', $cod_padre],
                ['proy_cd_partida.id_cd', '=', $id_cd],
                ['proy_cd_partida.estado', '!=', 7]])
        ->first();

        //Actualiza totales de los padres
        $update = DB::table('proyectos.proy_cd_compo')
            ->where([['proy_cd_compo.codigo','=',$cod_padre],
                    ['proy_cd_compo.id_cd', '=', $id_cd]])
            ->update(['total_comp'=>$part->suma_partidas]);

        //Obtiene el abuelo
        $abuelo = DB::table('proyectos.proy_cd_compo')
            ->select('cod_padre')//<-abuelo
            ->where([['codigo','=',$cod_padre],
                    ['id_cd','=',$id_cd],
                    ['estado','=',1]])
            ->first();

        //copia el padre
        $actualizar_padre = (isset($abuelo) ? $abuelo->cod_padre : null);
        
        while ($actualizar_padre !== null){
            //Suma los totales del abuelo
            $sum = DB::table('proyectos.proy_cd_compo')
            ->select(DB::raw('SUM(proy_cd_compo.total_comp) as suma'))
            ->where([['cod_padre',$actualizar_padre],
                    ['id_cd','=',$id_cd],
                    ['estado','=',1]])
            ->first();

            $data = DB::table('proyectos.proy_cd_compo')
            ->where([['codigo',$actualizar_padre],
                    ['id_cd','=',$id_cd],
                    ['estado','=',1]])
            ->update(['total_comp'=>$sum->suma]);

            //busca bisabuelo
            $bisabuelo = DB::table('proyectos.proy_cd_compo')
            ->select('cod_padre')//<-bisabuelo
            ->where([['codigo','=',$actualizar_padre],
                    ['id_cd','=',$id_cd],
                    ['estado','=',1]])
            ->first();
            //copia el bisabuelo
            $actualizar_padre = (isset($bisabuelo) ? $bisabuelo->cod_padre : null);
        }
    }

    public function suma_padres_ci($cod_padre, $id_ci)
    {
        $part = DB::table('proyectos.proy_ci_detalle')
        ->select(DB::raw('SUM(proy_ci_detalle.importe_parcial) as suma_partidas'))
        ->where([['proy_ci_detalle.cod_compo', '=', $cod_padre],
                ['proy_ci_detalle.id_ci', '=', $id_ci],
                ['proy_ci_detalle.estado', '!=', 7]])
        ->first();
        //Actualiza totales de los padres
        $update = DB::table('proyectos.proy_ci_compo')
            ->where([['proy_ci_compo.codigo','=',$cod_padre],
                    ['proy_ci_compo.id_ci', '=', $id_ci]])
            ->update(['total_comp'=>$part->suma_partidas]);
        //Obtiene el abuelo
        $abuelo = DB::table('proyectos.proy_ci_compo')
            ->select('cod_padre')//<-abuelo
            ->where([['codigo','=',$cod_padre],
                    ['id_ci','=',$id_ci],
                    ['estado','=',1]])
            ->first();

        //copia el padre
        $actualizar_padre = (isset($abuelo) ? $abuelo->cod_padre : null);

        while ($actualizar_padre !== null){
            //Suma los totales del abuelo
            $sum = DB::table('proyectos.proy_ci_compo')
            ->select(DB::raw('SUM(proy_ci_compo.total_comp) as suma'))
            ->where([['cod_padre',$actualizar_padre],
                    ['id_ci','=',$id_ci],
                    ['estado','=',1]])
            ->first();
    
            $data = DB::table('proyectos.proy_ci_compo')
            ->where([['codigo',$actualizar_padre],
                    ['id_ci','=',$id_ci],
                    ['estado','=',1]])
            ->update(['total_comp'=>$sum->suma]);
            
            //busca bisabuelo
            $bisabuelo = DB::table('proyectos.proy_ci_compo')
            ->select('cod_padre')//<-bisabuelo
            ->where([['codigo','=',$actualizar_padre],
                    ['id_ci','=',$id_ci],
                    ['estado','=',1]])
            ->first();
            //copia el bisabuelo
            $actualizar_padre = (isset($bisabuelo) ? $bisabuelo->cod_padre : null);
        }
    }


    public function suma_padres_gg($padre, $id_gg)
    {
        $part = DB::table('proyectos.proy_gg_detalle')
            ->select(DB::raw('SUM(proy_gg_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_gg_detalle.cod_compo', '=', $padre],
                    ['proy_gg_detalle.id_gg', '=', $id_gg],
                    ['proy_gg_detalle.estado', '!=', 7]])
            ->first();
        
        $update = DB::table('proyectos.proy_gg_compo')
            ->where([['proy_gg_compo.codigo','=',$padre],
                    ['proy_gg_compo.id_gg', '=', $id_gg]])
            ->update(['total_comp'=>$part->suma_partidas]);

        //Obtiene el abuelo
        $abuelo = DB::table('proyectos.proy_gg_compo')
        ->select('cod_padre')//<-abuelo
        ->where([['codigo','=',$padre],
                ['id_gg','=',$id_gg],
                ['estado','=',1]])
        ->first();

        //copia el padre
        $actualizar_padre = (isset($abuelo) ? $abuelo->cod_padre : null);

        while ($actualizar_padre !== null){
            //Suma los totales del abuelo
            $sum = DB::table('proyectos.proy_gg_compo')
            ->select(DB::raw('SUM(proy_gg_compo.total_comp) as suma'))
            ->where([['cod_padre',$actualizar_padre],
                    ['id_gg','=',$id_gg],
                    ['estado','=',1]])
            ->first();
    
            $data = DB::table('proyectos.proy_gg_compo')
            ->where([['codigo',$actualizar_padre],
                    ['id_gg','=',$id_gg],
                    ['estado','=',1]])
            ->update(['total_comp'=>$sum->suma]);
            
            //busca bisabuelo
            $bisabuelo = DB::table('proyectos.proy_gg_compo')
            ->select('cod_padre')//<-bisabuelo
            ->where([['codigo','=',$actualizar_padre],
                    ['id_gg','=',$id_gg],
                    ['estado','=',1]])
            ->first();
            //copia el bisabuelo
            $actualizar_padre = (isset($bisabuelo) ? $bisabuelo->cod_padre : null);
        }
    }

    
    public function actualiza_totales($id_pres)
    {
        $part_cd_todo = DB::table('proyectos.proy_cd_partida')
            ->select(DB::raw('SUM(proy_cd_partida.importe_parcial) as suma_partidas'))
            ->where([['proy_cd_partida.id_cd', '=', $id_pres],
                    ['proy_cd_partida.estado', '=', 1]])
            ->first();

        $part_ci_todo = DB::table('proyectos.proy_ci_detalle')
            ->select(DB::raw('SUM(proy_ci_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_ci_detalle.id_ci', '=', $id_pres],
                    ['proy_ci_detalle.estado', '=', 1]])
            ->first();

        $part_gg_todo = DB::table('proyectos.proy_gg_detalle')
            ->select(DB::raw('SUM(proy_gg_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_gg_detalle.id_gg', '=', $id_pres],
                    ['proy_gg_detalle.estado', '=', 1]])
            ->first();

        $total_cd = $part_cd_todo->suma_partidas;
        $total_ci = $part_ci_todo->suma_partidas;
        $total_gg = $part_gg_todo->suma_partidas;
            
        $imp = DB::table('proyectos.proy_presup_importe')
            ->where([['id_presupuesto','=',$id_pres]])
            ->first();
                   
        if (isset($imp)){
            if ($total_ci == 0){
                $total_ci = $total_cd * $imp->porcentaje_ci;
            }
            if ($total_gg == 0){
                $total_gg = $total_cd * $imp->porcentaje_gg;
            }

            if ($imp->porcentaje_igv > 0){
                $porcentaje_igv = $imp->porcentaje_igv;
            } 
            else {
                $igv = DB::table('contabilidad.cont_impuesto')
                ->where('codigo','IGV')
                ->orderBy('fecha_inicio','desc')
                ->first();
                $porcentaje_igv = $igv->porcentaje;
            }
        
            $subtotal = $total_cd + $total_ci + $total_gg;
            $total_uti = (($subtotal / (1 - ($imp->porcentaje_utilidad / 100))) - $subtotal);
            $total_igv = ($porcentaje_igv / 100) * ($subtotal + $total_uti);
            $total_pres = $subtotal + $total_uti + $total_igv;

            $pres = DB::table('proyectos.proy_presup_importe')
            ->where([['id_presupuesto','=',$id_pres]])
            ->update([
                'total_costo_directo' => $total_cd, 
                'total_ci' => $total_ci,
                'total_gg' => $total_gg,
                'sub_total' => $subtotal, 
                'total_utilidad' => $total_uti, 
                'porcentaje_igv' => $porcentaje_igv, 
                'total_igv' => $total_igv, 
                'total_presupuestado' => $total_pres,
            ]);
        }
        return response()->json($pres);
    }

    public function subir_partida_cd($id_partida){
        $cid = DB::table('proyectos.proy_cd_partida')
        ->where('id_partida',$id_partida)
        ->first();
        $codigo = $cid->codigo;
        //obtiene ultimo numero y resta -1
        $nuevo = intval(substr($cid->codigo,-2,2)) - 1;
        $update = 0;

        if ($nuevo > 0){
            //obtiene el codigo
            $padre = substr($cid->codigo,0,strlen($cid->codigo)-2);
            $nuevo_codigo = $padre.GenericoController::leftZero(2,$nuevo);
            
            //obtener el anterior y restarle una posicion
            $ant = DB::table('proyectos.proy_cd_partida')
            ->where([['id_cd','=',$cid->id_cd],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();

            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$ant->id_partida)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            $anterior = intval(substr($cid->codigo,-5,2));
            //resta ultimo numero
            $nuevo = $anterior - 1;
            //obtiene el codigo
            $nuevo_codigo = substr($cid->codigo,0,strlen($cid->codigo)-5);
            $nue_padre = $nuevo_codigo.GenericoController::leftZero(2,$nuevo);
            $padre_anterior = substr($cid->codigo,0,strlen($cid->codigo)-3);
            //obtiene el ultimo hijo del nuevo padre
            $titulo = DB::table('proyectos.proy_cd_compo')
            ->where([['codigo','like',$nue_padre.'%'],['estado','=',1],['id_cd','=',$cid->id_cd]])
            ->orderBy('codigo','desc')
            ->first();

            if (isset($titulo)){
                //obtener el anterior y sumarle una posicion
                $count = DB::table('proyectos.proy_cd_partida')
                ->where([['cod_compo','=',$titulo->codigo],['estado','=',1],['id_cd','=',$cid->id_cd]])
                ->count();
                //genera nuevo codigo
                $cod = $titulo->codigo.'.'.GenericoController::leftZero(2,($count+1));
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $cod,
                          'cod_compo' => $titulo->codigo]);
                // actualiza hijos del padre anterior
                $hijos = DB::table('proyectos.proy_cd_partida')
                ->where([['cod_compo','=',$padre_anterior],['estado','=',1],['id_cd','=',$cid->id_cd]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 0;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.GenericoController::leftZero(2,($i));
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_cd_partida')
                    ->where('id_partida',$h->id_partida)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
            }
        }
        return response()->json($update);
    }

    public function bajar_partida_cd($id_partida){
        $cid = DB::table('proyectos.proy_cd_partida')
        ->where('id_partida',$id_partida)
        ->first();
        //codigo actual
        $codigo = $cid->codigo;
        //obtiene ultimo numero 
        $ultimo = intval(substr($cid->codigo,-2,2));
        $update = 0;
        $padre = substr($cid->codigo,0,strlen($cid->codigo)-3);
        //cuenta los hijos
        $count = DB::table('proyectos.proy_cd_partida')
            ->where([['cod_compo','=',$padre],['estado','=',1],['id_cd','=',$cid->id_cd]])
            ->count();
        //si el codigo actual es menor que la cantidad de partidas
        if ($ultimo < $count){
            //suma uno al numero
            $nuevo = $ultimo + 1;
            //genera el nuevo codigo
            $nuevo_codigo = $padre.'.'.GenericoController::leftZero(2,$nuevo);
            //obtener el anterior
            $ant = DB::table('proyectos.proy_cd_partida')
            ->where([['id_cd','=',$cid->id_cd],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();
            //verifica si existe el anterior
            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$ant->id_partida)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            //obtiene padre actual
            $padre_actual = intval(substr($cid->codigo,-5,2));
            //suma al padre
            $nue = $padre_actual + 1;
            //obtiene el codigo
            $nuevo_padre = substr($cid->codigo,0,strlen($cid->codigo)-5).GenericoController::leftZero(2,$nue);

            $count_nuevo_padre = DB::table('proyectos.proy_cd_compo')
            ->where([['codigo','=',$nuevo_padre],['estado','=',1],['id_cd','=',$cid->id_cd]])
            ->count();

            if ($count_nuevo_padre > 0){
                //genera nuevo codigo hijo
                $nuevo_codigo = $nuevo_padre.'.01';
                //actualiza los hijos del nuevo padre
                $hijos = DB::table('proyectos.proy_cd_partida')
                ->where([['cod_compo','=',$nuevo_padre],['estado','=',1],['id_cd','=',$cid->id_cd]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 1;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.GenericoController::leftZero(2,$i);
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_cd_partida')
                    ->where('id_partida',$h->id_partida)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$id_partida)
                ->update(['codigo' => $nuevo_codigo,
                          'cod_compo' => $nuevo_padre]);
            }
            else {
                //obtiene abuelo actual
                $abuelo_actual = intval(substr($cid->codigo,-8,2));
                //suma al abuelo
                $nue_abu = $abuelo_actual + 1;
                //obtiene el codigo
                $nuevo_abuelo = substr($cid->codigo,0,strlen($cid->codigo)-8).GenericoController::leftZero(2,$nue_abu);

                $count_nuevo_abuelo = DB::table('proyectos.proy_cd_compo')
                ->where([['codigo','=',$nuevo_abuelo],['estado','=',1],['id_cd','=',$cid->id_cd]])
                ->count();

                if ($count_nuevo_abuelo > 0){
                    //genera nuevo codigo hijo
                    $nuevo_codigo = $nuevo_abuelo.'.'.GenericoController::leftZero(2,1);
                    //actualiza los hijos del nuevo padre
                    $hijos = DB::table('proyectos.proy_cd_partida')
                    ->where([['cod_compo','=',$nuevo_abuelo],['estado','=',1],['id_cd','=',$cid->id_cd]])
                    ->orderBy('codigo','asc')
                    ->get();
                    
                    $i = 1;
                    foreach($hijos as $h){
                        $i++;
                        $c = substr($h->codigo,0,strlen($h->codigo)-3);
                        $nuevo_hijo = $c.'.'.GenericoController::leftZero(2,$i);
                        //actualiza nuevo codigo
                        DB::table('proyectos.proy_cd_partida')
                        ->where('id_partida',$h->id_partida)
                        ->update(['codigo'=>$nuevo_hijo]);
                    }
                    // actualiza el codigo actual
                    $update = DB::table('proyectos.proy_cd_partida')
                    ->where('id_partida',$id_partida)
                    ->update(['codigo' => $nuevo_codigo,
                              'cod_compo' => $nuevo_abuelo]);
                }
            }
        }
        return response()->json($update);
    }

    public function subir_partida_ci($id_ci_detalle){
        $cid = DB::table('proyectos.proy_ci_detalle')
        ->where('id_ci_detalle',$id_ci_detalle)
        ->first();
        $codigo = $cid->codigo;
        //obtiene ultimo numero y resta -1
        $nuevo = intval(substr($cid->codigo,-2,2)) - 1;
        $update = 0;

        if ($nuevo > 0){
            //obtiene el codigo
            $padre = substr($cid->codigo,0,strlen($cid->codigo)-2);
            $nuevo_codigo = $padre.GenericoController::leftZero(2,$nuevo);
            
            //obtener el anterior y restarle una posicion
            $ant = DB::table('proyectos.proy_ci_detalle')
            ->where([['id_ci','=',$cid->id_ci],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();

            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$ant->id_ci_detalle)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$id_ci_detalle)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            $anterior = intval(substr($cid->codigo,-5,2));
            //resta ultimo numero
            $nuevo = $anterior - 1;
            //obtiene el codigo
            $nuevo_codigo = substr($cid->codigo,0,strlen($cid->codigo)-5);
            $nue_padre = $nuevo_codigo.GenericoController::leftZero(2,$nuevo);
            $padre_anterior = substr($cid->codigo,0,strlen($cid->codigo)-3);
            //obtiene el ultimo hijo del nuevo padre
            $titulo = DB::table('proyectos.proy_ci_compo')
            ->where([['codigo','like',$nue_padre.'%'],['estado','=',1],['id_ci','=',$cid->id_ci]])
            ->orderBy('codigo','desc')
            ->first();

            if (isset($titulo)){
                //obtener el anterior y sumarle una posicion
                $count = DB::table('proyectos.proy_ci_detalle')
                ->where([['cod_compo','=',$titulo->codigo],['estado','=',1],['id_ci','=',$cid->id_ci]])
                ->count();
                //genera nuevo codigo
                $cod = $titulo->codigo.'.'.GenericoController::leftZero(2,($count+1));
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$id_ci_detalle)
                ->update(['codigo' => $cod,
                            'cod_compo' => $titulo->codigo]);
                // actualiza hijos del padre anterior
                $hijos = DB::table('proyectos.proy_ci_detalle')
                ->where([['cod_compo','=',$padre_anterior],['estado','=',1],['id_ci','=',$cid->id_ci]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 0;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.GenericoController::leftZero(2,($i));
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_ci_detalle')
                    ->where('id_ci_detalle',$h->id_ci_detalle)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
            }
        }
        return response()->json($update);
    }

    public function bajar_partida_ci($id_ci_detalle){
        $cid = DB::table('proyectos.proy_ci_detalle')
        ->where('id_ci_detalle',$id_ci_detalle)
        ->first();
        //codigo actual
        $codigo = $cid->codigo;
        //obtiene ultimo numero 
        $ultimo = intval(substr($cid->codigo,-2,2));
        $update = 0;
        $padre = substr($cid->codigo,0,strlen($cid->codigo)-3);
        //cuenta los hijos
        $count = DB::table('proyectos.proy_ci_detalle')
            ->where([['cod_compo','=',$padre],['estado','=',1],['id_ci','=',$cid->id_ci]])
            ->count();
        //si el codigo actual es menor que la cantidad de partidas
        if ($ultimo < $count){
            //suma uno al numero
            $nuevo = $ultimo + 1;
            //genera el nuevo codigo
            $nuevo_codigo = $padre.'.'.GenericoController::leftZero(2,$nuevo);
            //obtener el anterior
            $ant = DB::table('proyectos.proy_ci_detalle')
            ->where([['id_ci','=',$cid->id_ci],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();
            //verifica si existe el anterior
            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$ant->id_ci_detalle)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$id_ci_detalle)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            //obtiene padre actual
            $padre_actual = intval(substr($cid->codigo,-5,2));
            //suma al padre
            $nue = $padre_actual + 1;
            //obtiene el codigo
            $nuevo_padre = substr($cid->codigo,0,strlen($cid->codigo)-5).GenericoController::leftZero(2,$nue);

            $count_nuevo_padre = DB::table('proyectos.proy_ci_compo')
            ->where([['codigo','=',$nuevo_padre],['estado','=',1],['id_ci','=',$cid->id_ci]])
            ->count();

            if ($count_nuevo_padre > 0){
                //genera nuevo codigo hijo
                $nuevo_codigo = $nuevo_padre.'.01';
                //actualiza los hijos del nuevo padre
                $hijos = DB::table('proyectos.proy_ci_detalle')
                ->where([['cod_compo','=',$nuevo_padre],['estado','=',1],['id_ci','=',$cid->id_ci]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 1;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.GenericoController::leftZero(2,$i);
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_ci_detalle')
                    ->where('id_ci_detalle',$h->id_ci_detalle)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$id_ci_detalle)
                ->update(['codigo' => $nuevo_codigo,
                          'cod_compo' => $nuevo_padre]);
            }
            else {
                //obtiene abuelo actual
                $abuelo_actual = intval(substr($cid->codigo,-8,2));
                //suma al abuelo
                $nue_abu = $abuelo_actual + 1;
                //obtiene el codigo
                $nuevo_abuelo = substr($cid->codigo,0,strlen($cid->codigo)-8).GenericoController::leftZero(2,$nue_abu);

                $count_nuevo_abuelo = DB::table('proyectos.proy_ci_compo')
                ->where([['codigo','=',$nuevo_abuelo],['estado','=',1],['id_ci','=',$cid->id_ci]])
                ->count();

                if ($count_nuevo_abuelo > 0){
                    //genera nuevo codigo hijo
                    $nuevo_codigo = $nuevo_abuelo.'.'.GenericoController::leftZero(2,1);
                    //actualiza los hijos del nuevo padre
                    $hijos = DB::table('proyectos.proy_ci_detalle')
                    ->where([['cod_compo','=',$nuevo_abuelo],['estado','=',1],['id_ci','=',$cid->id_ci]])
                    ->orderBy('codigo','asc')
                    ->get();
                    
                    $i = 1;
                    foreach($hijos as $h){
                        $i++;
                        $c = substr($h->codigo,0,strlen($h->codigo)-3);
                        $nuevo_hijo = $c.'.'.GenericoController::leftZero(2,$i);
                        //actualiza nuevo codigo
                        DB::table('proyectos.proy_ci_detalle')
                        ->where('id_ci_detalle',$h->id_ci_detalle)
                        ->update(['codigo'=>$nuevo_hijo]);
                    }
                    // actualiza el codigo actual
                    $update = DB::table('proyectos.proy_ci_detalle')
                    ->where('id_ci_detalle',$id_ci_detalle)
                    ->update(['codigo' => $nuevo_codigo,
                              'cod_compo' => $nuevo_abuelo]);
                }
            }
        }
        return response()->json($update);
    }

    public function subir_partida_gg($id_gg_detalle){
        $cid = DB::table('proyectos.proy_gg_detalle')
        ->where('id_gg_detalle',$id_gg_detalle)
        ->first();
        $codigo = $cid->codigo;
        //obtiene ultimo numero y resta -1
        $nuevo = intval(substr($cid->codigo,-2,2)) - 1;
        $update = 0;

        if ($nuevo > 0){
            //obtiene el codigo
            $padre = substr($cid->codigo,0,strlen($cid->codigo)-2);
            $nuevo_codigo = $padre.GenericoController::leftZero(2,$nuevo);
            
            //obtener el anterior y sumarle una posicion
            $ant = DB::table('proyectos.proy_gg_detalle')
            ->where([['id_gg','=',$cid->id_gg],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();

            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$ant->id_gg_detalle)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$id_gg_detalle)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            $anterior = intval(substr($cid->codigo,-5,2));
            //resta ultimo numero
            $nuevo = $anterior - 1;
            //obtiene el codigo
            $nuevo_codigo = substr($cid->codigo,0,strlen($cid->codigo)-5);
            $nue_padre = $nuevo_codigo.GenericoController::leftZero(2,$nuevo);
            $padre_anterior = substr($cid->codigo,0,strlen($cid->codigo)-3);
            //obtiene el ultimo hijo del nuevo padre
            $titulo = DB::table('proyectos.proy_gg_compo')
            ->where([['codigo','like',$nue_padre.'%'],['estado','=',1],['id_gg','=',$cid->id_gg]])
            ->orderBy('codigo','desc')
            ->first();
            
            if (isset($titulo)){
                //obtener el anterior y sumarle una posicion
                $count = DB::table('proyectos.proy_gg_detalle')
                ->where([['cod_compo','=',$titulo->codigo],['estado','=',1],['id_gg','=',$cid->id_gg]])
                ->count();
                
                $cod = $titulo->codigo.'.'.GenericoController::leftZero(2,($count+1));
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$id_gg_detalle)
                ->update(['codigo' => $cod,
                          'cod_compo' => $titulo->codigo]);
                // actualiza hijos del padre anterior
                $hijos = DB::table('proyectos.proy_gg_detalle')
                ->where([['cod_compo','=',$padre_anterior],['estado','=',1],['id_gg','=',$cid->id_gg]])
                ->orderBy('codigo','asc')
                ->get();

                $i = 0;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.GenericoController::leftZero(2,($i));
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_gg_detalle')
                    ->where('id_gg_detalle',$h->id_gg_detalle)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
            }
        }
        return response()->json($update);
    }

    public function bajar_partida_gg($id_gg_detalle){
        $cid = DB::table('proyectos.proy_gg_detalle')
        ->where('id_gg_detalle',$id_gg_detalle)
        ->first();
        //codigo actual
        $codigo = $cid->codigo;
        //obtiene ultimo numero 
        $ultimo = intval(substr($cid->codigo,-2,2));
        $update = 0;
        $padre = substr($cid->codigo,0,strlen($cid->codigo)-3);
        //cuenta los hijos
        $count = DB::table('proyectos.proy_gg_detalle')
            ->where([['cod_compo','=',$padre],['estado','=',1],['id_gg','=',$cid->id_gg]])
            ->count();
        //si el codigo actual es menor que la cantidad de partidas
        if ($ultimo < $count){
            //suma uno al numero
            $nuevo = $ultimo + 1;
            //genera el nuevo codigo
            $nuevo_codigo = $padre.'.'.GenericoController::leftZero(2,$nuevo);
            //obtener el anterior
            $ant = DB::table('proyectos.proy_gg_detalle')
            ->where([['id_gg','=',$cid->id_gg],
                     ['codigo','=',strval($nuevo_codigo)],
                     ['estado','=',1]])
            ->first();
            //verifica si existe el anterior
            if (isset($ant)){
                //actualiza el anterior
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$ant->id_gg_detalle)
                ->update(['codigo' => $codigo]);
                //actualiza el codigo actual
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$id_gg_detalle)
                ->update(['codigo' => $nuevo_codigo]);
            }
        } 
        else {
            //obtiene padre actual
            $padre_actual = intval(substr($cid->codigo,-5,2));
            //suma al padre
            $nue = $padre_actual + 1;
            //obtiene el codigo
            $nuevo_padre = substr($cid->codigo,0,strlen($cid->codigo)-5).GenericoController::leftZero(2,$nue);

            $count_nuevo_padre = DB::table('proyectos.proy_gg_compo')
            ->where([['codigo','=',$nuevo_padre],['estado','=',1],['id_gg','=',$cid->id_gg]])
            ->count();
            
            if ($count_nuevo_padre > 0){
                //genera nuevo codigo hijo
                $nuevo_codigo = $nuevo_padre.'.01';
                // actualiza los hijos del nuevo padre
                $hijos = DB::table('proyectos.proy_gg_detalle')
                ->where([['cod_compo','=',$nuevo_padre],['estado','=',1],['id_gg','=',$cid->id_gg]])
                ->orderBy('codigo','asc')
                ->get();
                
                $i = 1;
                foreach($hijos as $h){
                    $i++;
                    $c = substr($h->codigo,0,strlen($h->codigo)-3);
                    $nuevo_hijo = $c.'.'.GenericoController::leftZero(2,$i);
                    //actualiza nuevo codigo
                    DB::table('proyectos.proy_gg_detalle')
                    ->where('id_gg_detalle',$h->id_gg_detalle)
                    ->update(['codigo'=>$nuevo_hijo]);
                }
                // actualiza el codigo actual
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$id_gg_detalle)
                ->update(['codigo' => $nuevo_codigo,
                          'cod_compo' => $nuevo_padre]);
            }
            else {
                //obtiene abuelo actual
                $abuelo_actual = intval(substr($cid->codigo,-8,2));
                //suma al abuelo
                $nue_abu = $abuelo_actual + 1;
                //obtiene el codigo
                $nuevo_abuelo = substr($cid->codigo,0,strlen($cid->codigo)-8).GenericoController::leftZero(2,$nue_abu);

                $count_nuevo_abuelo = DB::table('proyectos.proy_gg_compo')
                ->where([['codigo','=',$nuevo_abuelo],['estado','=',1],['id_gg','=',$cid->id_gg]])
                ->count();

                if ($count_nuevo_abuelo > 0){
                    //genera nuevo codigo hijo
                    $nuevo_codigo = $nuevo_abuelo.'.01';
                    //actualiza los hijos del nuevo padre
                    $hijos = DB::table('proyectos.proy_gg_detalle')
                    ->where([['cod_compo','=',$nuevo_abuelo],['estado','=',1],['id_gg','=',$cid->id_gg]])
                    ->orderBy('codigo','asc')
                    ->get();
                    
                    $i = 1;
                    foreach($hijos as $h){
                        $i++;
                        $c = substr($h->codigo,0,strlen($h->codigo)-3);
                        $nuevo_hijo = $c.'.'.GenericoController::leftZero(2,$i);
                        //actualiza nuevo codigo
                        DB::table('proyectos.proy_gg_detalle')
                        ->where('id_gg_detalle',$h->id_gg_detalle)
                        ->update(['codigo'=>$nuevo_hijo]);
                    }
                    // actualiza el codigo actual
                    $update = DB::table('proyectos.proy_gg_detalle')
                    ->where('id_gg_detalle',$id_gg_detalle)
                    ->update(['codigo' => $nuevo_codigo,
                              'cod_compo' => $nuevo_abuelo]);
                }
            }
        }
        return response()->json($update);
    }

    public function listar_obs_cd($id_partida){
        $obs = DB::table('proyectos.proy_obs')
            ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
            ->where([['proy_obs.id_cd_partida','=', $id_partida],
                     ['proy_obs.estado','=',1]])
            ->orderBy('proy_obs.fecha_registro')
            ->get();
        $html = '';
        $i = 1;
        // <td><a href="abrir_adjunto_partida/'.$o->archivo_adjunto.'">'.$o->archivo_adjunto.'</a></td>
        foreach($obs as $o){
            $ruta = '/proyectos/presupuestos/partidas_adjunto/'.$o->archivo_adjunto;
            $file = asset('files').$ruta;
            $html .= '
            <tr id="obs-'.$o->id_obs.'">
                <td>'.$i.'</td>
                <td>'.$o->descripcion.'</td>
                <td>'.$o->nombre_usuario.'</td>
                <td>'.$o->fecha_registro.'</td>
                <td><a href="'.$file.'" target="_blank">'.$o->archivo_adjunto.'</a></td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular" onClick="anular_obs('.$o->id_obs.');"></i>
                </td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }
    public function listar_obs_ci($id_partida){
        $obs = DB::table('proyectos.proy_obs')
            ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
            ->where([['proy_obs.id_ci_detalle','=', $id_partida],
                     ['proy_obs.estado','=',1]])
            ->orderBy('proy_obs.fecha_registro')
            ->get();
        $html = '';
        $i = 1;
        foreach($obs as $o){
            $html .= '
            <tr id="obs-'.$o->id_obs.'">
                <td>'.$i.'</td>
                <td>'.$o->descripcion.'</td>
                <td>'.$o->nombre_usuario.'</td>
                <td>'.$o->fecha_registro.'</td>
                <td><a href="abrir_adjunto_partida/'.$o->archivo_adjunto.'">'.$o->archivo_adjunto.'</a></td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular" onClick="anular_obs('.$o->id_obs.');"></i>
                </td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }
    public function listar_obs_gg($id_partida){
        $obs = DB::table('proyectos.proy_obs')
            ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
            ->where([['proy_obs.id_gg_detalle','=', $id_partida],
                     ['proy_obs.estado','=',1]])
            ->orderBy('proy_obs.fecha_registro')
            ->get();
        $html = '';
        $i = 1;
        foreach($obs as $o){
            $html .= '
            <tr id="obs-'.$o->id_obs.'">
                <td>'.$i.'</td>
                <td>'.$o->descripcion.'</td>
                <td>'.$o->nombre_usuario.'</td>
                <td>'.$o->fecha_registro.'</td>
                <td><a href="abrir_adjunto_partida/'.$o->archivo_adjunto.'">'.$o->archivo_adjunto.'</a></td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular" onClick="anular_obs('.$o->id_obs.');"></i>
                </td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }

    public function guardar_obs_partida(Request $request){
        $id_usuario = Auth::user()->id_usuario;
        $id_obs = DB::table('proyectos.proy_obs')->insertGetId(
                [
                'id_cd_partida'=>$request->id_cd_partida,
                'id_ci_detalle'=>$request->id_ci_detalle,
                'id_gg_detalle'=>$request->id_gg_detalle,
                'descripcion'=>$request->observacion,
                'usuario'=>$id_usuario,
                'estado'=>1,
                'fecha_registro'=>date('Y-m-d H:i:s'),
                ],
                'id_obs'
            );
        //obtenemos el campo file definido en el formulario
        $file = $request->file('adjunto');
        if (isset($file)){
            //obtenemos el nombre del archivo
            // $file = Input::file('upfile')->getClientOriginalName();
            // $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

            $nombre = $id_obs.'.'.$extension;
            //indicamos que queremos guardar un nuevo archivo en el disco local
            File::delete(public_path('proyectos/presupuestos/partidas_adjunto/'.$nombre));
            Storage::disk('archivos')->put('proyectos/presupuestos/partidas_adjunto/'.$nombre,File::get($file));
            
            $update = DB::table('proyectos.proy_obs')
                ->where('id_obs', $id_obs)
                ->update(['archivo_adjunto' => $nombre]); 
        } else {
            $nombre = null;
        }
        return response()->json($id_obs);
    }
    /*
    public function abrir_adjunto_partida($file_name){
        $file_path = public_path('files/proyectos/presupuestos/partidas_adjunto/'.$file_name);
        // $result = File::exists('files/proyectos/contratos/'.$file_name);
        if (file_exists($file_path)){
            return response()->download($file_path);
        } else {
            return response()->json("No existe dicho archivo!");
        }
    }*/
    public function anular_obs_partida($id_obs){
        $data = DB::table('proyectos.proy_obs')->where('id_obs',$id_obs)
            ->update([ 'estado'=> 2 ]);
        return response()->json($data);
    }

}
