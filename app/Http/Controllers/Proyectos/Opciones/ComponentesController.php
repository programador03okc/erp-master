<?php

namespace App\Http\Controllers\Proyectos\Opciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Proyectos\Catalogos\GenericoController;
use Illuminate\Support\Facades\DB;

class ComponentesController extends Controller
{
    
    public function listar_acus_cd($id)
    {
        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*','proy_sis_contrato.descripcion as nombre_sistema',
            'proy_cu_partida.unid_medida as cu_unid_medida',
            'alm_und_medida.abreviatura','proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_cd_partida.id_sistema')
            ->join('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_cd_partida.id_cu_partida')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu_partida.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_cd_partida.id_cd', '=', $id],
                     ['proy_cd_partida.estado', '!=', 7]])
            ->orderBy('proy_cd_partida.codigo')
            ->get()
            ->toArray();
        
        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id],
                    ['proy_cd_compo.estado', '!=', 7]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();
        
        $html = '';
        $total = 0;
        $sistemas = GenericoController::mostrar_sis_contrato_cbo();

        foreach ($compo_cd as $comp){
            $codigo = "'".$comp->codigo."'";
            $desc = "'".$comp->codigo." - ".$comp->descripcion."'";
            $html .= '
            <tr id="com-'.$comp->id_cd_compo.'">
                <td></td>
                <td>'.$comp->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$comp->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right">'.number_format($comp->total_comp,2,".",",").'</td>
                <td></td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Título" onClick="agregar_compo_cd('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agrega_partida_cd('.$codigo.','.$desc.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Título" onClick="editar_compo_cd('.$comp->id_cd_compo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Título" onClick="update_compo_cd('.$comp->id_cd_compo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Título" onClick="anular_compo_cd('.$comp->id_cd_compo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$comp->cod_padre.'</td>
            </tr>';
            
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $total += $partida->importe_parcial;
                    $id_sistema = (isset($partida->id_sistema) ? $partida->id_sistema : 0);
                    $html .= '
                    <tr id="par-'.$partida->id_partida.'">
                        <td>
                            <i class="fas fa-arrow-alt-circle-down" data-toggle="tooltip" data-placement="bottom" title="Bajar Partida" onClick="bajar_partida_cd('.$partida->id_partida.');"></i>
                            <i class="fas fa-arrow-alt-circle-up" data-toggle="tooltip" data-placement="bottom" title="Subir Partida" onClick="subir_partida_cd('.$partida->id_partida.');"></i>
                        </td>
                        <td id="cu-'.$partida->id_cu_partida.'">'.$partida->codigo.'</td>
                        <td id="ccu-'.$partida->cod_acu.'">'.$partida->descripcion.'</td>
                        <td id="abr-'.$partida->cu_unid_medida.'">'.$partida->abreviatura.'</td>
                        <td><input type="number" class="input-data right" name="cantidad" value="'.$partida->cantidad.'" onChange="calcula_total_cd('.$partida->id_partida.');" disabled="true"/></td>
                        <td><input type="number" class="input-data right" style="width:130px;" name="importe_unitario" value="'.round($partida->importe_unitario,6,PHP_ROUND_HALF_UP).'" onChange="calcula_total_cd('.$partida->id_partida.');" disabled="true"/></td>
                        <td><input type="number" class="input-data right" style="width:130px;" name="importe_parcial" value="'.round($partida->importe_parcial,6,PHP_ROUND_HALF_UP).'" onChange="calcula_total_cd('.$partida->id_partida.');" disabled="true"/></td>
                        <td>
                            <select class="input-data" name="id_sistema" disabled="true">
                                <option value="0">Elija una opción</option>';
                                foreach ($sistemas as $row) {
                                    if ($id_sistema == $row->id_sis_contrato){
                                        $html.='<option value="'.$row->id_sis_contrato.'" selected>'.$row->descripcion.'</option>';
                                    } else {
                                        $html.='<option value="'.$row->id_sis_contrato.'">'.$row->descripcion.'</option>';
                                    }
                                }
                            $html.='</select>
                        </td>
                        <td style="display:flex;">
                            <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida_cd('.$partida->id_partida.');"></i>
                            <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_partida_cd('.$partida->id_partida.');"></i>
                            <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida_cd('.$partida->id_partida.');"></i>
                            <i class="fas fa-pen-square icon-tabla green visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar A.C.U." onClick="edit_acu('.$partida->id_cu_partida.','.$partida->id_partida.');"></i>
                            <i class="fas fa-bars icon-tabla purple boton" data-toggle="tooltip" data-placement="bottom" title="Ver A.C.U." onClick="ver_acu_detalle('.$partida->id_cu_partida.','.$partida->cantidad.');"></i>
                            <i class="fas fa-file-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Lecciones Aprendidas" onClick="open_presLeccion('."'"."cd"."'".','.$partida->id_partida.');"></i>
                        </td>
                        <td hidden>'.$partida->cod_compo.'</td>
                    </tr>';
                }
            }
        }
        
        return json_encode(['html'=>$html,'total'=>$total]);
    }

    public function listar_cd($id)
    {
        $data = $this->cd($id);
        return response()->json(['data'=>$data['array'],'total'=>$data['total']]);
    }

    public function cd($id)
    {
        $cd_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.id_insumo','proy_insumo.tp_insumo','proy_insumo.codigo',
        'proy_insumo.descripcion','alm_und_medida.abreviatura','proy_insumo.id_categoria',
        DB::raw('SUM(proy_cd_partida.cantidad * proy_cu_detalle.cantidad) as cantidad'),
        'proy_cu_detalle.precio_unit as precio_unitario',
        DB::raw('SUM(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as importe_parcial'), 
        DB::raw('count(proy_cu_detalle.precio_unit) as count_precio'))
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_presup.id_presupuesto')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->groupBy('proy_insumo.id_insumo','proy_insumo.tp_insumo','proy_insumo.codigo',
            'proy_insumo.descripcion','alm_und_medida.abreviatura','proy_insumo.id_categoria',
            'proy_cu_detalle.precio_unit')
            ->where([['proy_presup.id_presupuesto','=',$id],
                    ['proy_cd_partida.estado','=',1],
                    ['proy_cu_detalle.estado','=',1]])
            ->get();

        $insumos_aproximados = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.id_insumo','proy_insumo.tp_insumo','proy_insumo.codigo',
        'proy_insumo.descripcion','alm_und_medida.abreviatura','proy_insumo.id_categoria',
        // DB::raw('SUM(proy_cd_partida.cantidad * proy_cu_detalle.cantidad) as cantidad'),
        // 'proy_cu_detalle.precio_unit as precio_unitario',
        DB::raw('SUM(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as importe_parcial'), 
        DB::raw('count(proy_cu_detalle.precio_unit) as count_precio'))
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_presup.id_presupuesto')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu_partida','=','proy_cd_partida.id_cu_partida')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->groupBy('proy_insumo.id_insumo','proy_insumo.tp_insumo','proy_insumo.codigo',
            'proy_insumo.descripcion','alm_und_medida.abreviatura','proy_insumo.id_categoria')
            ->where([['proy_presup.id_presupuesto','=',$id],
                    ['proy_cd_partida.estado','=',1],
                    ['proy_cu_detalle.estado','=',1],
                    ['proy_insumo.id_categoria','=',1]])//categoria=aproximados
            ->get();
    
        $tipos = DB::table('proyectos.proy_tp_insumo')
        ->select('proy_tp_insumo.id_tp_insumo','proy_tp_insumo.codigo','proy_tp_insumo.descripcion')
        ->where('estado',1)
            ->get();
            
        $sum = 0;
        $array = [];
        $total = 0;

        foreach($tipos as $tipo){
            $insumos_tipo = [];
            foreach ($cd_insumos as $row){
                if ($tipo->id_tp_insumo == $row->tp_insumo && $row->id_categoria !== 1){//categoria=aproximados
                    $sum += $row->importe_parcial;
                    $insumos_tipo[] = $row;
                }
            }
            foreach ($insumos_aproximados as $row){
                if ($tipo->id_tp_insumo == $row->tp_insumo){
                    $sum += $row->importe_parcial;
                    $insumos_tipo[] = $row;
                }
            }
            if ($sum > 0){
                $nuevo = array( 'id_tp_insumo'=>$tipo->id_tp_insumo, 
                                'codigo'=>$tipo->codigo,
                                'descripcion'=>$tipo->descripcion,
                                'suma'=>round($sum,6,PHP_ROUND_HALF_UP),
                                'insumos'=>$insumos_tipo);
                $array[] = $nuevo;
                $total +=$sum;
                $sum = 0;
            }
        }
        return ['array'=>$array,'total'=>$total];
    }
    
    public function listar_ci($id)
    {
        $part_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_ci_detalle.*','alm_und_medida.abreviatura',
            'proy_cu_partida.rendimiento','proy_cu.codigo as cod_acu')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
            ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_ci_detalle.id_cu_partida')
            ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_ci_detalle.id_ci', '=', $id],
                     ['proy_ci_detalle.estado', '=', 1]])
            ->orderBy('proy_ci_detalle.codigo')
            ->get()
            ->toArray();
            
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
            ->where([['proy_ci_compo.id_ci', '=', $id],
                     ['proy_ci_compo.estado', '=', 1]])
            ->orderBy('proy_ci_compo.codigo')
            ->get();
    
        $componentes_ci = [];
        $array = [];
        $html = '';
        $tipo = "'ci'";

        foreach ($compo_ci as $comp){
            $total = 0;
            $codigo = "'".$comp->codigo."'";
            $desc = "'".$comp->codigo." - ".$comp->descripcion."'";
            $html .= '
            <tr id="comci-'.$comp->id_ci_compo.'">
                <td></td>
                <td>'.$comp->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$comp->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right">'.number_format($comp->total_comp,2,".",",").'</td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Componente" onClick="agregar_compo_ci('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agrega_partida_ci('.$codigo.','.$desc.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Componente" onClick="editar_compo_ci('.$comp->id_ci_compo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Componente" onClick="update_compo_ci('.$comp->id_ci_compo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Componente" onClick="anular_compo_ci('.$comp->id_ci_compo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$comp->cod_padre.'</td>
            </tr>';

            foreach($part_ci as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $total += $partida->importe_parcial;
                    $html .= '
                    <tr id="parci-'.$partida->id_ci_detalle.'">
                        <td>
                            <i class="fas fa-arrow-alt-circle-down" data-toggle="tooltip" data-placement="bottom" title="Bajar Partida" onClick="bajar_partida_ci('.$partida->id_ci_detalle.');"></i>
                            <i class="fas fa-arrow-alt-circle-up" data-toggle="tooltip" data-placement="bottom" title="Subir Partida" onClick="subir_partida_ci('.$partida->id_ci_detalle.');"></i>
                        </td>
                        <td id="cu-'.(isset($partida->id_cu_partida) ? $partida->id_cu_partida : '').'">'.$partida->codigo.'</td>
                        <td id="ccu-'.(isset($partida->cod_acu) ? $partida->cod_acu : '').'">'.$partida->descripcion.'</td>
                        <td id="abr-'.(isset($partida->unid_medida) ? $partida->unid_medida : '').'">'.($partida->abreviatura !== null ? $partida->abreviatura : '').'</td>
                        <td class="right">'.$partida->cantidad.'</td>
                        <td class="right">'.number_format($partida->importe_unitario,2,".",",").'</td>
                        <td class="right">'.(isset($partida->participacion) ? number_format($partida->participacion,2,".",",") : '').'</td>
                        <td class="right">'.(isset($partida->tiempo) ? number_format($partida->tiempo,2,".",",") : '').'</td>
                        <td class="right">'.(isset($partida->veces) ? number_format($partida->veces,2,".",",") : '').'</td>
                        <td class="right">'.number_format($partida->importe_parcial,2,".",",").'</td>
                        <td></td>
                        <td style="display:flex;">
                            <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida_ci('.$partida->id_ci_detalle.');"></i>
                            <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida_ci('.$partida->id_ci_detalle.');"></i>
                            <i class="fas fa-file-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Lecciones Aprendidas" onClick="open_presLeccion('."'".$tipo."'".','.$partida->id_ci_detalle.');"></i>
                        </td>
                        <td hidden>'.$partida->cod_compo.'</td>
                    </tr>';
                }
            }
        }
        return json_encode($html);
    }
    public function listar_gg($id)
    {
        // $ci = DB::table('proyectos.proy_presup')
        //     ->select('proy_gg.id_gg')
        //     ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
        //     ->where([['proy_presup.id_presupuesto', '=', $id]])
        //     ->first();

        $part_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_gg_detalle.*',
            'alm_und_medida.abreviatura','proy_cu.rendimiento','proy_cu.codigo as cod_acu')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
            ->leftjoin('proyectos.proy_cu_partida','proy_cu_partida.id_cu_partida','=','proy_gg_detalle.id_cu_partida')
            ->leftjoin('proyectos.proy_cu','proy_cu.id_cu','=','proy_cu_partida.id_cu')
            ->where([['proy_gg_detalle.id_gg', '=', $id],
                    ['proy_gg_detalle.estado', '=', 1]])
            ->orderBy('proy_gg_detalle.codigo')
            ->get()
            ->toArray();
            
        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
            ->where([['proy_gg_compo.id_gg', '=', $id],
                    ['proy_gg_compo.estado', '=', 1]])
            ->orderBy('proy_gg_compo.codigo')
            ->get();
    
        $componentes_gg = [];
        $array = [];
        $html = '';
        $tipo = "'gg'";

        foreach ($compo_gg as $comp){
            $total = 0;
            $codigo = "'".$comp->codigo."'";
            $desc = "'".$comp->codigo." - ".$comp->descripcion."'";
            $html .= '
            <tr id="comgg-'.$comp->id_gg_compo.'">
                <td></td>
                <td>'.$comp->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$comp->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right">'.number_format($comp->total_comp,2,".",",").'</td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Componente" onClick="agregar_compo_gg('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agrega_partida_gg('.$codigo.','.$desc.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Componente" onClick="editar_compo_gg('.$comp->id_gg_compo.');"></i>
                    <i class="fas fa-save icon-tabla green boton oculto" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Componente" onClick="update_compo_gg('.$comp->id_gg_compo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Componente" onClick="anular_compo_gg('.$comp->id_gg_compo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$comp->cod_padre.'</td>
            </tr>';
            foreach($part_gg as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $total += $partida->importe_parcial;
                    $html .= '
                    <tr id="pargg-'.$partida->id_gg_detalle.'">
                        <td>
                            <i class="fas fa-arrow-alt-circle-down" data-toggle="tooltip" data-placement="bottom" title="Bajar Partida" onClick="bajar_partida_gg('.$partida->id_gg_detalle.');"></i>
                            <i class="fas fa-arrow-alt-circle-up" data-toggle="tooltip" data-placement="bottom" title="Subir Partida" onClick="subir_partida_gg('.$partida->id_gg_detalle.');"></i>
                        </td>
                        <td id="cu-'.(isset($partida->id_cu_partida) ? $partida->id_cu_partida : '').'">'.$partida->codigo.'</td>
                        <td id="ccu-'.(isset($partida->cod_acu) ? $partida->cod_acu : '').'">'.$partida->descripcion.'</td>
                        <td id="abr-'.(isset($partida->unid_medida) ? $partida->unid_medida : '').'">'.$partida->abreviatura.'</td>
                        <td class="right">'.$partida->cantidad.'</td>
                        <td class="right">'.number_format($partida->importe_unitario,2,".",",").'</td>
                        <td class="right">'.(isset($partida->participacion) ? number_format($partida->participacion,2,".",",") : '').'</td>
                        <td class="right">'.(isset($partida->tiempo) ? number_format($partida->tiempo,2,".",",") : '').'</td>
                        <td class="right">'.(isset($partida->veces) ? number_format($partida->veces,2,".",",") : '').'</td>
                        <td class="right">'.number_format($partida->importe_parcial,2,".",",").'</td>
                        <td></td>
                        <td style="display:flex;">
                        <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida_gg('.$partida->id_gg_detalle.');"></i>
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida_gg('.$partida->id_gg_detalle.');"></i>
                        <i class="fas fa-file-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Lecciones Aprendidas" onClick="open_presLeccion('."'".$tipo."'".','.$partida->id_gg_detalle.');"></i>
                        </td>
                        <td hidden>'.$partida->cod_compo.'</td>
                    </tr>';
                }
            }
        }
        return json_encode($html);
    }

    
    public function guardar_componente_cd(Request $request)
    {
        $data = DB::table('proyectos.proy_cd_compo')
            ->insertGetId([
                'id_cd' => $request->id_pres,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'cod_padre' => $request->cod_compo,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_cd_compo'
            );
        return response()->json($data);
    }

    public function guardar_componente_ci(Request $request)
    {
        $data = DB::table('proyectos.proy_ci_compo')
            ->insertGetId([
                'id_ci' => $request->id_pres,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'cod_padre' => $request->cod_compo,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_ci_compo'
            );
        return response()->json($data);
    }
    public function guardar_componente_gg(Request $request)
    {
        $data = DB::table('proyectos.proy_gg_compo')
            ->insertGetId([
                'id_gg' => $request->id_pres,
                'codigo' => $request->codigo,
                'descripcion' => strtoupper($request->descripcion),
                'cod_padre' => $request->cod_compo,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_gg_compo'
            );
        return response()->json($data);
    }
    public function update_componente_cd(Request $request){
        
        $data = DB::table('proyectos.proy_cd_compo')
            ->where('id_cd_compo', $request->id_cd_compo)
            ->update(['descripcion' => strtoupper($request->descripcion)]);

        return response()->json($data);
    }
    public function update_componente_ci(Request $request){
        
        $data = DB::table('proyectos.proy_ci_compo')
            ->where('id_ci_compo', $request->id_ci_compo)
            ->update(['descripcion' => strtoupper($request->descripcion)]);

        return response()->json($data);
    }
    public function update_componente_gg(Request $request){
        
        $data = DB::table('proyectos.proy_gg_compo')
            ->where('id_gg_compo', $request->id_gg_compo)
            ->update(['descripcion' => strtoupper($request->descripcion)]);

        return response()->json($data);
    }
    public function anular_compo_cd(Request $request){

        $data = DB::table('proyectos.proy_cd_compo')
            ->where('proy_cd_compo.id_cd_compo', $request->id_cd_compo)
            ->update(['estado' => 7]);

        $hijos_com = explode(',',$request->hijos_com);
        $count1 = count($hijos_com);

        if (!empty($request->hijos_com)){
            for ($i=0; $i<$count1; $i++){
                DB::table('proyectos.proy_cd_compo')
                ->where('proy_cd_compo.id_cd_compo', $hijos_com[$i])
                ->update(['estado' => 7]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par)){
            for ($i=0; $i<$count2; $i++){
                DB::table('proyectos.proy_cd_partida')
                ->where('proy_cd_partida.id_partida', $hijos_par[$i])
                ->update(['estado' => 7]);
            }
        }

        $this->suma_partidas_cd($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_compo_ci(Request $request){

        $data = DB::table('proyectos.proy_ci_compo')
            ->where('proy_ci_compo.id_ci_compo', $request->id_ci_compo)
            ->update(['estado' => 7]);

        $hijos_com = explode(',',$request->hijos_com);
        $count1 = count($hijos_com);

        if (!empty($request->hijos_com)){
            for ($i=0; $i<$count1; $i++){
                DB::table('proyectos.proy_ci_compo')
                ->where('proy_ci_compo.id_ci_compo', $hijos_com[$i])
                ->update(['estado' => 7]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par)){
            for ($i=0; $i<$count2; $i++){
                DB::table('proyectos.proy_ci_detalle')
                ->where('proy_ci_detalle.id_ci_detalle', $hijos_par[$i])
                ->update(['estado' => 7]);
            }
        }

        $this->suma_partidas_ci($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_compo_gg(Request $request){

        $data = DB::table('proyectos.proy_gg_compo')
            ->where('proy_gg_compo.id_gg_compo', $request->id_gg_compo)
            ->update(['estado' => 7]);

        $hijos_com = explode(',',$request->hijos_com);
        $count1 = count($hijos_com);

        if (!empty($request->hijos_com)){
            for ($i=0; $i<$count1; $i++){
                DB::table('proyectos.proy_gg_compo')
                ->where('proy_gg_compo.id_gg_compo', $hijos_com[$i])
                ->update(['estado' => 7]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par)){
            for ($i=0; $i<$count2; $i++){
                DB::table('proyectos.proy_gg_detalle')
                ->where('proy_gg_detalle.id_gg_detalle', $hijos_par[$i])
                ->update(['estado' => 7]);
            }
        }

        $this->suma_partidas_gg($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }

}
