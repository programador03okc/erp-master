<?php

namespace App\Http\Controllers\Proyectos\Opciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresupuestoInternoController extends Controller
{
    function view_presint(){
        $monedas = $this->mostrar_monedas_cbo();
        $sistemas = $this->mostrar_sis_contrato_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        $tipos = $this->mostrar_tipos_insumos_cbo();
        $ius = $this->mostrar_ius_cbo();
        $categorias = $this->select_categorias_acus();
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
    
}
