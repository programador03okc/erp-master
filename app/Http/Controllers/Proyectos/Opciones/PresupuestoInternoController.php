<?php

namespace App\Http\Controllers\Proyectos\Opciones;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Proyectos\Catalogos\GenericoController;
use App\Http\Controllers\Proyectos\Variables\CategoriaAcuController;
use App\Http\Controllers\Proyectos\Variables\IuController;
use App\Http\Controllers\Proyectos\Variables\TipoInsumoController;
use App\Http\Controllers\ProyectosController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresupuestoInternoController extends Controller
{
    function view_presint(){
        $monedas = GenericoController::mostrar_monedas_cbo();
        $sistemas = GenericoController::mostrar_sis_contrato_cbo();
        $unidades = AlmacenController::mostrar_unidades_cbo();
        $tipos = TipoInsumoController::mostrar_tipos_insumos_cbo();
        $ius = IuController::mostrar_ius_cbo();
        $categorias = CategoriaAcuController::select_categorias_acus();
        return view('proyectos/presupuesto/presint', compact('monedas','sistemas','unidades','tipos','ius','categorias'));
    }

    
    public function mostrar_presint($id)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_proyecto.descripcion as descripcion_proy',
                     'proy_op_com.descripcion', 'proy_presup_importe.total_costo_directo', 
                     'proy_presup_importe.total_ci', 'proy_presup_importe.porcentaje_ci', 
                     'proy_presup_importe.total_gg', 'proy_presup_importe.porcentaje_gg', 
                     'proy_presup_importe.sub_total', 'proy_presup_importe.porcentaje_utilidad', 
                     'proy_presup_importe.total_utilidad', 'proy_presup_importe.porcentaje_igv', 
                     'proy_presup_importe.total_igv', 'proy_presup_importe.total_presupuestado',
                     'sis_moneda.simbolo','adm_contri.razon_social','adm_estado_doc.estado_doc as des_estado')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->leftjoin('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_presup.id_proyecto')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','proy_presup.estado')
                ->where([['proy_presup.id_presupuesto', '=', $id]])
                ->first();
        
        return response()->json($data);
    }
    
    public function nextPresupuesto($tipo,$id_emp,$fecha)
    {
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));//yyyy
        $anio = date('y',strtotime($fecha));//yy
        $result = '';

        $tp = DB::table('proyectos.proy_tp_pres')
        ->select('codigo')
        ->where('id_tp_pres', $tipo)
        ->first();

        $emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', $id_emp)
        ->first();

        $data = DB::table('proyectos.proy_presup')
                ->where([['id_tp_presupuesto','=',$tipo],
                        ['id_empresa','=',$id_emp],
                        ['estado','!=',7]])
                // ->whereMonth('fecha_emision', '=', $mes)
                ->whereYear('fecha_emision', '=', $yyyy)
                ->count();

        $number = GenericoController::leftZero(3,$data+1);
        $result = $tp->codigo."-".$emp->codigo."-".$anio."-".$number;

        return $result;
    }

    public function guardar_presint(Request $request){
        $op_com = DB::table('proyectos.proy_op_com')
        ->where('id_op_com',$request->id_op_com)
        ->first();
        $msj = '';
        $id_pres = 0;

        if (isset($op_com)){
            $cod = $this->nextPresupuesto(
                $request->id_tp_presupuesto,
                $op_com->id_empresa,
                $request->fecha_emision
            );
            $fecha = date('Y-m-d H:i:s');
            $id_usuario = Auth::user()->id_usuario;

            $version = DB::table('proyectos.proy_presup')
            ->where([['id_tp_presupuesto','=',1],['id_op_com','=',$request->id_op_com],
                    ['estado','!=',7]])->count();

            $id_pres = DB::table('proyectos.proy_presup')->insertGetId(
                [
                    // 'id_proyecto' => $request->id_proyecto,
                    'fecha_emision' => $request->fecha_emision,
                    'moneda' => $request->moneda,
                    'id_tp_presupuesto' => $request->id_tp_presupuesto,
                    'elaborado_por' => $id_usuario,
                    'cronograma' => false,
                    'cronoval' => false,
                    'tipo_cambio' => $request->tipo_cambio,
                    'id_op_com' => $request->id_op_com,
                    'observacion' => $request->observacion,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                    'codigo' => $cod,
                    'id_empresa' => $op_com->id_empresa,
                    'version' => ($version + 1)
                ],
                    'id_presupuesto'
            );
    
            $pres_imp = DB::table('proyectos.proy_presup_importe')->insert(
                [
                    'id_presupuesto' => $id_pres,
                    'total_costo_directo' => 0,
                    'total_ci' => 0,
                    'porcentaje_ci' => 0,
                    'total_gg' => 0,
                    'porcentaje_gg' => 0,
                    'sub_total' => 0,
                    'porcentaje_utilidad' => 0,
                    'total_utilidad' => 0,
                    'porcentaje_igv' => 0,//jalar igv actual
                    'total_igv' => 0,
                    'total_presupuestado' => 0
                ]
            );
            if ($id_pres > 0 && $pres_imp > 0){
                $msj = 'Se guardo exitosamente.';
            }
        } else {
            $msj = 'No existe la Opción Comercial relacionada!.';
        }
        return response()->json(['msj'=>$msj,'id_pres'=>$id_pres]);
    }

    
    public function update_presint(Request $request){

        $version = DB::table('proyectos.proy_presup')
        ->where([['id_tp_presupuesto','=',1],['id_op_com','=',$request->id_op_com],
                ['estado','!=',7],['id_presupuesto','!=',$request->id_presupuesto]])
                ->count();

        $data = DB::table('proyectos.proy_presup')
            ->where('id_presupuesto',$request->id_presupuesto)
            ->update([
                'fecha_emision' => $request->fecha_emision,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'id_op_com' => $request->id_op_com,
                'observacion' => $request->observacion,
                'version' => ($version + 1)
            ]);
            
        $imp = DB::table('proyectos.proy_presup_importe')
            ->where('id_presupuesto',$request->id_presupuesto)
            ->update([
                    'total_costo_directo' => $request->total_costo_directo,
                    'total_ci' => $request->total_ci,
                    'porcentaje_ci' => $request->porcentaje_ci,
                    'total_gg' => $request->total_gg,
                    'porcentaje_gg' => $request->porcentaje_gg,
                    'sub_total' => $request->sub_total,
                    'porcentaje_utilidad' => $request->porcentaje_utilidad,
                    'total_utilidad' => $request->total_utilidad,
                    'porcentaje_igv' => $request->porcentaje_igv,
                    'total_igv' => $request->total_igv,
                    'total_presupuestado' => $request->total_presupuestado,
                ]
            );
        $msj = ($data !== null ? 'Se actualizó exitosamente.' : '');
        return response()->json(['msj'=>$msj,'id_pres'=>$request->id_presupuesto]);
    }

    public function anular_presint($id){
        $presup = DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$id)
        ->first();
        $msj = '';
        $update = 0;
        $anula = false;

        if ($presup->cronograma == false && $presup->cronoval == false && isset($presup)){
            if ($presup->id_presup !== null){
                $partidas = DB::table('finanzas.presup_par')
                ->where('id_presup',$presup->id_presup)
                ->get();
                $tiene_req = false;
                foreach($partidas as $par){
                    $req = DB::table('almacen.alm_det_req')
                    ->where([['partida','=',$par->id_partida],
                            ['estado','!=',7]])
                    ->count();
                    if ($req > 0){
                        $tiene_req = true;
                        break;
                    }
                }
                if ($tiene_req){
                    $msj = 'No se pudo anular!. El presupuesto esta relacionado con Requerimientos.';
                } else {
                    $anula = true;
                }
            } else {
                $anula = true;
            }

            if ($anula){
                $update = DB::table('proyectos.proy_presup')
                ->where('id_presupuesto',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_cd_compo')
                ->where('id_cd',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_cd_partida')
                ->where('id_cd',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_ci_compo')
                ->where('id_ci',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_gg_compo')
                ->where('id_gg',$id)
                ->update(['estado'=>7]);

                DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg',$id)
                ->update(['estado'=>7]);

                DB::table('finanzas.presup_par')
                ->where('id_presup',$presup->id_presup)
                ->update(['estado'=>7]);

                $msj = 'Se anuló con éxito!';
            }
        }
        return response()->json(['msj'=>$msj,'update'=>$update]);
    }
    
    
    public function generar_estructura($id_presupuesto, $tipo){

        try {
            DB::beginTransaction();

            $presup = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*','proy_op_com.descripcion',
            'proy_op_com.cantidad','proy_op_com.unid_program')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->where('id_presupuesto',$id_presupuesto)
            ->first();

            $id_grupo = 3;
            $codigo = (new ProyectosController)->nextCodigoPresupuesto($id_grupo, $presup->fecha_emision, $tipo);


            //Inserta Nuevo Presupuesto
            $id_presup = DB::table('finanzas.presup')
            ->insertGetId([
                'id_empresa' => $presup->id_empresa,
                'id_grupo' => $id_grupo,//Grupo: Proyectos
                'fecha_emision' => $presup->fecha_emision,
                'codigo' => $codigo,
                'descripcion' => $presup->descripcion,
                'moneda' => $presup->moneda,
                'responsable' => $presup->elaborado_por,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'tp_presup' => $tipo,
            ],
                'id_presup'
            );
        
            $base = DB::table('finanzas.presup')
            ->where([['tp_presup','=',1],['estado','=',1]])
            ->orderBy('fecha_emision','desc')
            ->first();
        
            $titulos = DB::table('finanzas.presup_titu')
            ->where([['id_presup','=',$base->id_presup],['estado','=',1]])
            ->get();

            //Inserta los titulos
            foreach($titulos as $titu){
                $data = DB::table('finanzas.presup_titu')
                ->insertGetId([
                    'id_presup' => $id_presup,
                    'codigo' => $titu->codigo,
                    'descripcion' => strtoupper($titu->descripcion),
                    'cod_padre' => $titu->cod_padre,
                    'total' => 0,
                    'fecha_registro' => date('Y-m-d H:i:s'),
                    'estado' => 1
                ],
                    'id_titulo'
                );
            }

            $partidas = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup_pardet.descripcion')
            ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->where([['presup_par.id_presup','=',$base->id_presup],['presup_par.estado','=',1]])
            ->get();

            // $nuevas_partidas = [];
            $cd = (new ProyectosController)->solo_cd($id_presupuesto);
            $ci = DB::table('proyectos.proy_ci_compo')
                ->where([['id_ci', '=', $id_presupuesto],
                        ['estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
            $gg = DB::table('proyectos.proy_gg_compo')
                ->where([['id_gg', '=', $id_presupuesto],
                        ['estado', '=', 1]])
                ->orderBy('codigo')
                ->get();

            foreach($partidas as $par){
                $rel_tipo = substr($par->relacionado, 0, 2);
                $relacionado = substr($par->relacionado, 2, (strlen($par->relacionado)-2));
                $agrega = false;

                if ($rel_tipo == 'CD'){
                    foreach($cd as $c){
                        if ($relacionado === $c["codigo"]){
                            $agrega = true;
                            DB::table('finanzas.presup_par')
                            ->insertGetId([
                                'id_presup' => $id_presup,
                                'codigo' => $par->codigo,
                                'id_pardet' => $par->id_pardet,
                                'cod_padre' => $par->cod_padre,
                                'relacionado' => '',
                                'importe_base' => 0,
                                'importe_total' => $c["suma"],
                                'fecha_registro' => date('Y-m-d H:i:s'),
                                'estado' => 1
                            ],
                                'id_partida'
                            );
                        }
                    }
                }
                else if ($rel_tipo == 'CI'){
                    foreach($ci as $i){
                        if ($relacionado === $i->codigo){
                            $agrega = true;
                            DB::table('finanzas.presup_par')
                            ->insertGetId([
                                'id_presup' => $id_presup,
                                'codigo' => $par->codigo,
                                'id_pardet' => $par->id_pardet,
                                'cod_padre' => $par->cod_padre,
                                'relacionado' => '',
                                'importe_base' => 0,
                                'importe_total' => $i->total_comp,
                                'fecha_registro' => date('Y-m-d H:i:s'),
                                'estado' => 1
                            ],
                                'id_partida'
                            );
                        }
                    }
                }
                else if ($rel_tipo == 'GG'){
                    foreach($gg as $g){
                        if ($relacionado === $g->codigo){
                            $agrega = true;
                            DB::table('finanzas.presup_par')
                            ->insertGetId([
                                'id_presup' => $id_presup,
                                'codigo' => $par->codigo,
                                'id_pardet' => $par->id_pardet,
                                'cod_padre' => $par->cod_padre,
                                'relacionado' => '',
                                'importe_base' => 0,
                                'importe_total' => $g->total_comp,
                                'fecha_registro' => date('Y-m-d H:i:s'),
                                'estado' => 1
                            ],
                                'id_partida'
                            );
                        }
                    }
                }

                if ($agrega == false){
                    DB::table('finanzas.presup_par')
                        ->insertGetId([
                            'id_presup' => $id_presup,
                            'codigo' => $par->codigo,
                            'id_pardet' => $par->id_pardet,
                            'cod_padre' => $par->cod_padre,
                            'relacionado' => '',
                            'importe_base' => 0,
                            'importe_total' => 0,
                            'fecha_registro' => date('Y-m-d H:i:s'),
                            'estado' => 1
                        ],
                            'id_partida'
                        );
                }
            }
            DB::table('proyectos.proy_presup')
            ->where('id_presupuesto',$id_presupuesto)
            ->update(['estado'=>8, 'id_presup'=>$id_presup]);//Emitido

            (new ProyectosController)->suma_titulos($id_presup);
            // $html = $this->html_presupuesto_proyecto($id_presup,'imprimir_padres');

            // return json_encode(['id_presup'=>$id_presup,'html'=>$html]);
            DB::commit();
            return json_encode($id_presup);
            
        } catch (\PDOException $e) {
            DB::rollBack();
        }
    }

    
    public function listar_presupuesto_proyecto($id)
    {
        $html = $this->html_presupuesto_proyecto($id,'imprimir_padres');
        return json_encode($html);
    }

    public function html_presupuesto_proyecto($id, $var)
    {
        $partidas = DB::table('finanzas.presup_par')
            ->select('presup_par.*','presup_pardet.descripcion')
            ->join('finanzas.presup_pardet','presup_pardet.id_pardet','=','presup_par.id_pardet')
            ->where([['presup_par.id_presup', '=', $id],
                     ['presup_par.estado', '=', 1]])
            ->orderBy('presup_par.codigo')
            ->get()
            ->toArray();
            
        $titulos = DB::table('finanzas.presup_titu')
            ->select('presup_titu.*')
            ->where([['presup_titu.id_presup', '=', $id],
                     ['presup_titu.estado', '=', 1]])
            ->orderBy('presup_titu.codigo')
            ->get();
    
        $html = '';

        foreach ($titulos as $titu){
            // $total = 0;
            $codigo = "'".$titu->codigo."'";
            $html .= '
            <tr id="ti-'.$titu->id_titulo.'" class="green success" >
                <td><strong>'.$titu->codigo.'</strong></td>
                <td><strong>'.$titu->descripcion.'</strong></td>
                <td class="right"><strong>'.number_format($titu->total,3,'.',',').'</strong></td>';
                if ($var == 'imprimir_padres'){
                    $html .='<td hidden>'.$titu->cod_padre.'</td>';
                }
                if ($var == 'boton_saldos'){
                    $html .='<td></td>';
                }
            $html .='</tr>';

            foreach($partidas as $par){
                if ($titu->codigo == $par->cod_padre){
                    // $total += $par->importe_total;
                    $html .= '
                    <tr id="par-'.$par->id_partida.'">
                        <td id="pd-'.(isset($par->id_pardet) ? $par->id_pardet : '').'">'.$par->codigo.'</td>
                        <td>'.$par->descripcion.'</td>
                        <td class="right">'.number_format($par->importe_total,3,'.',',').'</td>';

                        if ($var == 'imprimir_padres'){
                            $html.='<td hidden>'.$par->cod_padre.'</td>';
                        }

                        if ($var == 'boton_saldos'){
                            //cuenta las relaciones con requerimiento
                            $count_req = DB::table('almacen.alm_det_req')
                            ->where([['alm_det_req.partida','=',$par->id_partida],['alm_det_req.estado','!=',7]])
                            ->count();
                            //si count es mayor a 0 color warning
                            if ($count_req > 0){
                                $html .= '<td>
                                <i class="fas fa-list-alt btn-warning visible boton" data-toggle="tooltip" data-placement="bottom" 
                                title="Ver Detalle Consumido" onClick="ver_detalle_partida('.$par->id_partida.','."'".$par->codigo.' '.$par->descripcion."'".','.$par->importe_total.');"></i>
                                </td>';
                            } else {
                                $html .= '<td></td>';
                            }
                            
                        }
                    $html .='</tr>';
                }
            }
        }
        return $html;
    }

    
    public function anular_estructura($id_pres)
    {
        $pres = DB::table('proyectos.proy_presup')
        ->where('id_presupuesto',$id_pres)
        ->first();
        $update = 0;

        if (isset($pres->id_presup)){
            $partidas = DB::table('finanzas.presup_par')
            ->where('id_presup',$pres->id_presup)
            ->get();
            $tiene_req = false;
            $r = 0;
            
            foreach($partidas as $par){
                $req = DB::table('almacen.alm_det_req')
                ->where([['partida','=',strval($par->id_partida)],
                         ['estado','!=',7]])
                ->count();
                
                if ($req > 0){
                    $tiene_req = true;
                    $r++;
                    break;
                }
            }
            //el presupuesto tiene partidas?
            if ($tiene_req == false){
                //Anula presup
                $update = DB::table('finanzas.presup')
                ->where('id_presup',$pres->id_presup)
                ->update(['estado' => 7]);
                //Anula titulos 
                $update = DB::table('finanzas.presup_titu')
                ->where('id_presup',$pres->id_presup)
                ->update(['estado' => 7]);
                //Anula partidas
                $update = DB::table('finanzas.presup_par')
                ->where('id_presup',$pres->id_presup)
                ->update(['estado' => 7]);
                //Quita la relacion con el presupuesto
                $update = DB::table('proyectos.proy_presup')
                ->where('id_presupuesto',$id_pres)
                ->update(['id_presup' => null,'estado'=>1]);
            }
        }

        return response()->json($update);
    }

    public function totales($id_pres){
        $data = DB::table('proyectos.proy_presup_importe')
        ->select('proy_presup_importe.total_costo_directo',
        'proy_presup_importe.total_ci','proy_presup_importe.porcentaje_ci',
        'proy_presup_importe.total_gg','proy_presup_importe.porcentaje_gg',
        'proy_presup_importe.sub_total','proy_presup_importe.porcentaje_utilidad',
        'proy_presup_importe.total_utilidad','proy_presup_importe.porcentaje_igv',
        'proy_presup_importe.total_igv','proy_presup_importe.total_presupuestado')
        ->where('id_presupuesto',$id_pres)
        ->first();
        return response()->json($data);
    }

    
    public function download_presupuesto($id){
        $detalle = $this->html_presupuesto_proyecto($id,'');
        $data = '
        <html>
            <head>
            <style type="text/css">
                *{ 
                    font-family: Calibri;
                }
                body{
                    background-color: #fff;
                    font-family: "DejaVu Sans";
                    font-size: 12px;
                    box-sizing: border-box;
                }
            </style>
            </head>
            <body>
                <table border="0" width="100%">
                    <thead>
                        <tr><th colSpan="3" style="alignment:center;">PRESUPUESTO INTERNO</th></tr>
                    </thead>
                </table>
                </br>
                <table id="detalle" border="0" width="100%">
                    <thead>
                        <tr style="background: silver;">
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>'.$detalle.'</tbody>
                </table>
            </body>
        </html>
        ';
        // return $data;  //class="table table-condensed table-bordered table-hover sortable" 
        return view('proyectos/reportes/presupuesto_excel', compact('data'));
    }

    public function mostrar_presupuestos($tp)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_op_com.descripcion', 'proy_presup_importe.sub_total',
                     'proy_presup_importe.total_presupuestado','sis_moneda.simbolo','adm_contri.razon_social')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.estado', '!=', 7],['proy_presup.id_tp_presupuesto', '=', $tp]])
                ->orderBy('proy_presup.id_presupuesto')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    
    public function listar_presupuestos_copia($tp,$menos_id)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_op_com.descripcion', 'proy_presup_importe.total_presupuestado',
                     'sis_moneda.simbolo','adm_contri.razon_social')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.estado', '!=', 7],
                        ['proy_presup.id_tp_presupuesto', '=', $tp],
                        ['proy_presup.id_presupuesto','!=',$menos_id]])
                ->orderBy('proy_presup.id_presupuesto')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    
    //Generar un presupuesto en base a otro presupuesto 
    //(ID del presupuesto que voy a copiar, # del tipo de presupuesto que voy a generar, ID del presupuesto actual)
    public function generar_partidas_presupuesto($id_presupuesto, $id_presupuesto_actual)
    {
        $fecha_emision = date('Y-m-d');
        $fecha_hora = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id_usuario;
        // $id_presupuesto = 0;

        $presint_cd_com = DB::table('proyectos.proy_cd_compo')
            ->where([['id_cd','=',$id_presupuesto],
                    ['estado','!=',7]])
                    ->get();
        $presint_ci_com = DB::table('proyectos.proy_ci_compo')
            ->where([['id_ci','=',$id_presupuesto],
                    ['estado','!=',7]])
                    ->get();
        $presint_gg_com = DB::table('proyectos.proy_gg_compo')
            ->where([['id_gg','=',$id_presupuesto],
                    ['estado','!=',7]])
                    ->get();

        if (isset($presint_cd_com)){
            foreach($presint_cd_com as $com)
            {
                DB::table('proyectos.proy_cd_compo')->insertGetId([
                    'id_cd' => $id_presupuesto_actual,
                    'codigo' => $com->codigo,
                    'descripcion' => $com->descripcion,
                    'cod_padre' => $com->cod_padre,
                    'total_comp' => $com->total_comp,
                    'porcen_utilidad' => $com->porcen_utilidad,
                    'importe_utilidad' => $com->importe_utilidad,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_cd_compo'
                );
            }
        }
        if (isset($presint_ci_com)){
            foreach($presint_ci_com as $com)
            {
                DB::table('proyectos.proy_ci_compo')->insertGetId([
                    'id_ci' => $id_presupuesto_actual,
                    'codigo' => $com->codigo,
                    'descripcion' => $com->descripcion,
                    'cod_padre' => $com->cod_padre,
                    'total_comp' => $com->total_comp,
                    'porcen_utilidad' => $com->porcen_utilidad,
                    'importe_utilidad' => $com->importe_utilidad,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_ci_compo'
                );
            }
        }
        if (isset($presint_gg_com)){
            foreach($presint_gg_com as $com)
            {
                DB::table('proyectos.proy_gg_compo')->insertGetId([
                    'id_gg' => $id_presupuesto_actual,
                    'codigo' => $com->codigo,
                    'descripcion' => $com->descripcion,
                    'cod_padre' => $com->cod_padre,
                    'total_comp' => $com->total_comp,
                    'porcen_utilidad' => $com->porcen_utilidad,
                    'importe_utilidad' => $com->importe_utilidad,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_gg_compo'
                );
            }
        }
        $presint_cd_par = DB::table('proyectos.proy_cd_partida')
            ->where([['id_cd','=',$id_presupuesto],
                    ['estado','!=',7]])
            ->get();
        $presint_ci_par = DB::table('proyectos.proy_ci_detalle')
            ->where([['id_ci','=',$id_presupuesto],
                    ['estado','!=',7]])
            ->get();
        $presint_gg_par = DB::table('proyectos.proy_gg_detalle')
            ->where([['id_gg','=',$id_presupuesto],
                    ['estado','!=',7]])
            ->get();

        if (isset($presint_cd_par)){
            foreach($presint_cd_par as $par)
            {
                $cu = DB::table('proyectos.proy_cu_partida')
                ->where('id_cu_partida',$par->id_cu_partida)
                ->first();
                //Crear cu_partida
                $id_cu_partida = DB::table('proyectos.proy_cu_partida')->insertGetId([
                        'id_cu' => $cu->id_cu,
                        'rendimiento' => $cu->rendimiento,
                        'unid_medida' => $cu->unid_medida,
                        'total' => $cu->total,
                        'estado' => 1,
                        'fecha_registro' => $fecha_hora,
                        'usuario_registro' => $id_usuario
                    ],
                        'id_cu_partida'
                );

                $cu_det = DB::table('proyectos.proy_cu_detalle')
                ->where([['id_cu_partida','=',$par->id_cu_partida],['estado','!=',7]])
                ->get();
                //Crea los cu detalles
                foreach($cu_det as $det)
                {
                    DB::table('proyectos.proy_cu_detalle')->insertGetId([
                            'id_cu_partida' => $id_cu_partida,
                            'id_insumo' => $det->id_insumo,
                            'unid_medida' => $det->unid_medida,
                            'cantidad' => $det->cantidad,
                            'cuadrilla' => $det->cuadrilla,
                            'precio_unit' => $det->precio_unit,
                            'precio_total' => $det->precio_total,
                            // 'id_precio' => $det->id_precio,
                            'estado' => 1,
                            'fecha_registro' => $fecha_hora
                        ],
                            'id_cu_detalle'
                    );
                }
                //Crea la partida
                DB::table('proyectos.proy_cd_partida')->insertGetId([
                        'id_cd' => $id_presupuesto_actual,
                        'id_cu_partida' => $id_cu_partida,
                        'codigo' => $par->codigo,
                        'descripcion' => $par->descripcion,
                        'unid_medida' => $par->unid_medida,
                        'cantidad' => $par->cantidad,
                        'importe_unitario' => $par->importe_unitario,
                        'importe_parcial' => $par->importe_parcial,
                        'id_sistema' => $par->id_sistema,
                        'cod_compo' => $par->cod_compo,
                        'fecha_registro' => $fecha_hora,
                        'estado' => 1
                    ],
                        'id_partida'
                    );
            }
        }
        if (isset($presint_ci_par)){
            foreach($presint_ci_par as $par)
            {
                DB::table('proyectos.proy_ci_detalle')->insertGetId([
                    'id_ci' => $id_presupuesto_actual,
                    'id_cu_partida' => $par->id_cu_partida,
                    'codigo' => $par->codigo,
                    'descripcion' => $par->descripcion,
                    'unid_medida' => $par->unid_medida,
                    'cantidad' => $par->cantidad,
                    'importe_unitario' => $par->importe_unitario,
                    'importe_parcial' => $par->importe_parcial,
                    'participacion' => $par->participacion,
                    'tiempo' => $par->tiempo,
                    'veces' => $par->veces,
                    'cod_compo' => $par->cod_compo,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_ci_detalle'
                );
            }
        }
        if (isset($presint_gg_par)){
            foreach($presint_gg_par as $par)
            {
                DB::table('proyectos.proy_gg_detalle')->insertGetId([
                    'id_gg' => $id_presupuesto_actual,
                    'id_cu_partida' => $par->id_cu_partida,
                    'codigo' => $par->codigo,
                    'descripcion' => $par->descripcion,
                    'unid_medida' => $par->unid_medida,
                    'cantidad' => $par->cantidad,
                    'importe_unitario' => $par->importe_unitario,
                    'importe_parcial' => $par->importe_parcial,
                    'participacion' => $par->participacion,
                    'tiempo' => $par->tiempo,
                    'veces' => $par->veces,
                    'cod_compo' => $par->cod_compo,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_gg_detalle'
                );
            }
        }

        $this->actualiza_totales($id_presupuesto_actual);
        
        return response()->json($id_presupuesto_actual);
    }

    
    public function actualiza_moneda($id_pres)
    {
        $pres = DB::table('proyectos.proy_presup')
            ->where('id_presupuesto',$id_pres)
            ->first();

        $partidas = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*','proy_cu_partida.total as precio_cu')
            ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->where([['proy_cd_partida.id_cd', '=', $id_pres],
                    ['proy_cd_partida.estado', '=', 1]])
                    ->get();

        $ci_detalle = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_ci_detalle.*')
            ->where([['proy_ci_detalle.id_ci', '=', $id_pres],
                    ['proy_ci_detalle.estado', '=', 1]])
                    ->get();

        $gg_detalle = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_gg_detalle.*')
            ->where([['proy_gg_detalle.id_gg', '=', $id_pres],
                    ['proy_gg_detalle.estado', '=', 1]])
                    ->get();

        if (isset($partidas)){
            foreach($partidas as $p){
                if ($pres->moneda == 1){
                    $unitario = $p->precio_cu;
                } else {
                    $unitario = $p->precio_cu * $pres->tipo_cambio;
                }
                $parcial = $unitario * $p->cantidad;
    
                $update = DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$p->id_partida)
                ->update(['importe_unitario'=>$unitario,
                        'importe_parcial'=>$parcial]);
            }
        }

        if (isset($ci_detalle)){
            foreach($ci_detalle as $det){
                if ($pres->moneda == 1){
                    $parcial = $det->importe_unitario * $det->cantidad;
                } else {
                    $parcial = $det->importe_unitario * $det->cantidad * $pres->tipo_cambio;
                }
                $update = DB::table('proyectos.proy_ci_detalle')
                ->where('id_ci_detalle',$det->id_ci_detalle)
                ->update(['importe_parcial'=>$parcial]);

                if (isset($det->cod_compo) && $det->cod_compo !== null){
                    $this->suma_padres_ci($det->cod_compo, $id_pres);
                }
            }
        }

        if (isset($gg_detalle)){
            foreach($gg_detalle as $det){
                if ($pres->moneda == 1){
                    $parcial = $det->importe_unitario * $det->cantidad;
                } else {
                    $parcial = $det->importe_unitario * $det->cantidad * $pres->tipo_cambio;
                }
                $update = DB::table('proyectos.proy_gg_detalle')
                ->where('id_gg_detalle',$det->id_gg_detalle)
                ->update(['importe_parcial'=>$parcial]);

                if (isset($det->cod_compo) && $det->cod_compo !== null){
                    $this->suma_padres_ci($det->cod_compo, $id_pres);
                }
            }        
        }
        $this->actualiza_totales($id_pres);

        return response()->json($update);
    }

    
    //actualiza unitario de la partida
    public function update_unitario_partida_cd(Request $request)
    {
        $cd_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_cu_detalle.*')
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_presup.id_presupuesto')
        ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu_partida','=','proy_cu_partida.id_cu_partida')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_presup.id_presupuesto','=',$request->id_presupuesto],
                    ['proy_cd_partida.estado','=',1],
                    ['proy_cu_detalle.id_insumo','=',$request->id_insumo],
                    ['proy_cu_detalle.estado','=',1]])
            ->get();

        $lista_cu_partida = [];
        $data = 0;

        foreach($cd_insumos as $ins)
        {
            $total = $ins->cantidad * $request->unitario;
            $data = DB::table('proyectos.proy_cu_detalle')
            ->where('proy_cu_detalle.id_cu_detalle',$ins->id_cu_detalle)
            ->update([
                'precio_unit'=>$request->unitario,
                'precio_total'=>$total,
            ]);
            if (!in_array($ins->id_cu_partida, $lista_cu_partida)){
                array_push($lista_cu_partida, $ins->id_cu_partida);
            }
        }

        $tp_mo = 1;//Tipo de Insumo = Mano de Obra
        $cat_aproximados = 1;//Categoria = Aproximados

        for ($i=0; $i<count($lista_cu_partida); $i++)
        {
            //suma totales de mano de obra
            $mo = DB::table('proyectos.proy_cu_detalle')
            ->select(DB::raw('sum(proy_cu_detalle.precio_total) as suma_mo'))
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->where([['proy_cu_detalle.id_cu_partida','=',$lista_cu_partida[$i]],
                    ['proy_insumo.tp_insumo','=',$tp_mo],
                    ['proy_cu_detalle.estado','!=',7]])
            ->first();

            //detalles tipo mo
            $dets = DB::table('proyectos.proy_cu_detalle')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_insumo_cat','proy_insumo_cat.id_categoria','=','proy_insumo.id_categoria')
            ->where([['proy_cu_detalle.id_cu_partida','=',$lista_cu_partida[$i]],
                    ['proy_insumo_cat.id_categoria','=',$cat_aproximados],
                    ['proy_cu_detalle.estado','!=',7]])
            ->get();

            foreach($dets as $d)
            {
                //actualiza precios de insumos de mo
                $total = ($d->cantidad * $mo->suma_mo)/100;
                DB::table('proyectos.proy_cu_detalle')
                ->where('id_cu_detalle',$d->id_cu_detalle)
                ->update(['precio_unit'=>$mo->suma_mo,
                          'precio_total'=>$total
                ]);
            }

            //suma total del acu
            $cu = DB::table('proyectos.proy_cu_detalle')
            ->select(DB::raw('sum(proy_cu_detalle.precio_total) as suma_total'))
            ->where([['proy_cu_detalle.id_cu_partida','=',$lista_cu_partida[$i]],
                    ['proy_cu_detalle.estado','!=',7]])
            ->first();

            $data = DB::table('proyectos.proy_cu_partida')
            ->where('id_cu_partida',$lista_cu_partida[$i])
            ->update(['total'=>$cu->suma_total]);

            $partida = DB::table('proyectos.proy_cd_partida')
            ->where([['id_cu_partida','=',$lista_cu_partida[$i]],
                    ['id_cd','=',$request->id_presupuesto],
                    ['estado','!=',7]])
                    ->first();

            if (isset($partida)){
                $parcial = $cu->suma_total * $partida->cantidad;

                DB::table('proyectos.proy_cd_partida')
                ->where('id_partida',$partida->id_partida)
                ->update(['importe_unitario'=>$cu->suma_total,
                          'importe_parcial'=> $parcial
                ]);
            }
        }
        $this->actualiza_totales($request->id_presupuesto);

        $totales = DB::table('proyectos.proy_presup_importe')
        ->where('id_presupuesto',$request->id_presupuesto)
        ->first();

        return response()->json(['data'=>$data,'totales'=>$totales]);
    }


}
