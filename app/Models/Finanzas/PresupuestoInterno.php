<?php

namespace App\Models\Finanzas;

use App\Helpers\ConfiguracionHelper;
use App\Http\Controllers\Finanzas\Presupuesto\PresupuestoInternoController;
use App\Models\administracion\AdmGrupo;
use App\Models\Administracion\Periodo;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PresupuestoInterno extends Model
{
    //
    protected $table = 'finanzas.presupuesto_interno';
    protected $primaryKey = 'id_presupuesto_interno';
    public $timestamps = false;

    public function detalle()
    {
        return $this->hasMany('App\Models\Finanzas\PresupuestoInternoDetalle', 'id_presupuesto_interno', 'id_presupuesto_interno');
    }
    // el total de todo el año suma las cabeceras
    public static function calcularTotalPresupuestoAnual($id_presupuesto_interno, $id_tipo_presupuesto)
    {
        $presupuesto_interno_detalle=array();
        switch ($id_tipo_presupuesto) {
            case 1:
                $presupuesto_interno_detalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',1)->where('estado', 1)->orderBy('partida')->get();
            break;

            case 2:
                $presupuesto_interno_detalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',2)->where('estado', 1)->orderBy('partida')->get();
            break;
            case 3:
                $presupuesto_interno_detalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',3)->where('estado', 1)->orderBy('partida')->get();
            break;
        }
        $enero      = $presupuesto_interno_detalle[0]->float_enero;
        $febrero    = $presupuesto_interno_detalle[0]->float_febrero;
        $marzo      = $presupuesto_interno_detalle[0]->float_marzo;
        $abril      = $presupuesto_interno_detalle[0]->float_abril;
        $mayo       = $presupuesto_interno_detalle[0]->float_mayo;
        $junio      = $presupuesto_interno_detalle[0]->float_junio;
        $julio      = $presupuesto_interno_detalle[0]->float_julio;
        $agosto     = $presupuesto_interno_detalle[0]->float_agosto;
        $setiembre  = $presupuesto_interno_detalle[0]->float_setiembre;
        $octubre    = $presupuesto_interno_detalle[0]->float_octubre;
        $noviembre  = $presupuesto_interno_detalle[0]->float_noviembre;
        $diciembre  = $presupuesto_interno_detalle[0]->float_diciembre;
        $total      = $enero + $febrero + $marzo + $abril + $mayo + $junio + $julio + $agosto + $setiembre + $octubre + $noviembre + $diciembre;
        return $total;
    }
    // obtener el total en filas de un mes
    public static function obtenerPresupuestoFilasMes($id_presupuesto_interno, $id_tipo_presupuesto ,$mes=0){
        $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)
        ->where('id_tipo_presupuesto',$id_tipo_presupuesto)
        ->where('estado', 1)
        ->orderBy('partida')->get();

        $array_nivel_partida = array();
        $mesLista = ['1'=>'enero','2'=>'febrero','3'=>'marzo','4'=>'abril','5'=>'mayo','6'=>'junio','7'=>'julio','8'=>'agosto','9'=>'setiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];

        if($mes > 0){
            $nombreMes= $mesLista[$mes];
        }else{
            $fechaHoy = date("Y-m-d");
            $mes = intval(date('m', strtotime($fechaHoy)));
            $nombreMes= $mesLista[$mes];

        }
        foreach ($presupuesto_interno_destalle as $key => $value) {
            array_push($array_nivel_partida,array(
                "id"=>$value->id_presupuesto_interno_detalle,
                "partida"=>$value->partida,
                "descripcion"=>$value->descripcion,
                "total"=>floatval(str_replace(",", "", $value[$nombreMes])),
            ));
        }

        return $array_nivel_partida;

    }
    // obtener el total en filas de un mes registro numero 2
    public static function obtenerPresupuestoFilasMesRegistro($id_presupuesto_interno, $id_tipo_presupuesto ,$mes=0){
        $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)
        ->where('id_tipo_presupuesto',$id_tipo_presupuesto)
        ->where('estado', 1)
        ->where('registro', 2)
        ->orderBy('partida')->get();

        $array_nivel_partida = array();
        $mesLista = ['1'=>'enero','2'=>'febrero','3'=>'marzo','4'=>'abril','5'=>'mayo','6'=>'junio','7'=>'julio','8'=>'agosto','9'=>'setiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];

        if($mes > 0){
            $nombreMes= $mesLista[$mes];
        }else{
            $fechaHoy = date("Y-m-d");
            $mes = intval(date('m', strtotime($fechaHoy)));
            $nombreMes= $mesLista[$mes];

        }
        foreach ($presupuesto_interno_destalle as $key => $value) {
            array_push($array_nivel_partida,array(
                "id"=>$value->id_presupuesto_interno_detalle,
                "partida"=>$value->partida,
                "descripcion"=>$value->descripcion,
                "total"=>floatval(str_replace(",", "", $value[$nombreMes])),
            ));
        }

        return $array_nivel_partida;

    }
    // es el total en filas a la altura de la partida de todo el año
    public static function calcularTotalPresupuestoFilas($id_presupuesto_interno, $id_tipo_presupuesto, $tipoCampo = 1)
    {
        $presupuesto_interno_destalle=array();
        switch ($id_tipo_presupuesto) {
            case 1:
                $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',1)->where('estado', 1)->orderBy('partida')->get();
            break;

            case 2:
                $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',2)->where('estado', 1)->orderBy('partida')->get();
            break;
            case 3:
                $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',3)->where('estado', 1)->orderBy('partida')->get();
            break;
        }
        $array_nivel_partida = array();
        foreach ($presupuesto_interno_destalle as $key => $value) {
            $total=0;

            $enero      = ($tipoCampo == 1) ? $value->float_enero : floatval(str_replace(",", "", $value->enero_aux));
            $febrero    = ($tipoCampo == 1) ? $value->float_febrero : floatval(str_replace(",", "", $value->febrero_aux));
            $marzo      = ($tipoCampo == 1) ? $value->float_marzo : floatval(str_replace(",", "", $value->marzo_aux));
            $abril      = ($tipoCampo == 1) ? $value->float_abril : floatval(str_replace(",", "", $value->abril_aux));
            $mayo       = ($tipoCampo == 1) ? $value->float_mayo : floatval(str_replace(",", "", $value->mayo_aux));
            $junio      = ($tipoCampo == 1) ? $value->float_junio : floatval(str_replace(",", "", $value->junio_aux));
            $julio      = ($tipoCampo == 1) ? $value->float_julio : floatval(str_replace(",", "", $value->julio_aux));
            $agosto     = ($tipoCampo == 1) ? $value->float_agosto : floatval(str_replace(",", "", $value->agosto_aux));
            $setiembre  = ($tipoCampo == 1) ? $value->float_setiembre : floatval(str_replace(",", "", $value->setiembre_aux));
            $octubre    = ($tipoCampo == 1) ? $value->float_octubre : floatval(str_replace(",", "", $value->octubre_aux));
            $noviembre  = ($tipoCampo == 1) ? $value->float_noviembre : floatval(str_replace(",", "", $value->noviembre_aux));
            $diciembre  = ($tipoCampo == 1) ? $value->float_diciembre : floatval(str_replace(",", "", $value->diciembre_aux));
            $total      = $enero + $febrero + $marzo + $abril + $mayo + $junio + $julio + $agosto + $setiembre + $octubre + $noviembre + $diciembre;
            array_push($array_nivel_partida,array(
                "partida"=>$value->partida,
                "descripcion"=>$value->descripcion,
                "total"=>round($total, 2),
            ));
        }

        return $array_nivel_partida;
    }

    public static function calcularTotalConsumidoMesFilas($id_presupuesto_interno, $id_tipo_presupuesto, $numeroMes)
    {
        $nombre_mes= (new PresupuestoInternoController)->mes($numeroMes);
        $nombreCampoFijo= 'float_'.$nombre_mes;
        $nombreCampoVariable= 'float_'.$nombre_mes.'_aux';
        $presupuesto_interno_destalle=array();
        switch ($id_tipo_presupuesto) {
            case 1:
                $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',1)->where('estado', 1)->orderBy('partida')->get();
            break;

            case 2:
                $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',2)->where('estado', 1)->orderBy('partida')->get();
            break;
            case 3:
                $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',3)->where('estado', 1)->orderBy('partida')->get();
            break;
        }
        $array_nivel_partida = array();
        foreach ($presupuesto_interno_destalle as $key => $value) {
            $total = $value->$nombreCampoFijo - $value->$nombreCampoVariable;
            array_push($array_nivel_partida,array(
                "partida"=>$value->partida,
                "descripcion"=>$value->descripcion,
                "total"=>round($total, 2),
            ));
        }

        return $array_nivel_partida;
    }
    // calcula el total de un mes en especifico tomandolo como columna
    public static function calcularTotalMensualColumnas($id_presupuesto_interno, $id_tipo_presupuesto, $partida='01.01.01.01',$mes='enero')
    {

        $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',$id_tipo_presupuesto)->where('estado', 1)->where('partida', $partida)->orderBy('partida')->first();

        $id_hijo = $presupuesto_interno_destalle->id_hijo;
        $id_padre = $presupuesto_interno_destalle->id_padre;
        $total = 0;
        // if ('03.01.03.01'===$partida) {
        //     return $presupuesto_interno_destalle;exit;
        // }
        while ($id_padre!=='0') {
            $total = 0;
            $partidas = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',$id_tipo_presupuesto)->where('estado', 1)->where('id_padre', $id_padre)->orderBy('partida')->get();

            foreach ($partidas as $key => $value) {
                $columna_mes      = floatval(str_replace(",", "", $value->$mes));
                $total      = $total + $columna_mes;
            }

            $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',$id_tipo_presupuesto)->where('estado', 1)->where('id_hijo', $id_padre)->orderBy('partida')->first();
            $presupuesto_interno_destalle->$mes = number_format($total, 2);
            $presupuesto_interno_destalle->save();

            $id_hijo = $presupuesto_interno_destalle->id_hijo;
            $id_padre = $presupuesto_interno_destalle->id_padre;
        }
        return $partidas;
    }
    public static function calcularTotalMensualColumnasPorcentajes($id_presupuesto_interno, $id_tipo_presupuesto, $partida='01.01.01.01',$mes='enero')
    {
        // $partida_creada ='';


        $presupuesto_interno_destalle= array();
        switch ($id_tipo_presupuesto) {
            case 1:
                // $partida = explode('.',$partida);
                $partida_array = explode('.',$partida);
                $partida_gobierno='';
                $partida_privado='';

                foreach ($partida_array as $key => $value) {

                    $partida_gobierno = ($key===0?$value:($key===sizeof($partida_array)-1?$partida_gobierno.'.01':$partida_gobierno.'.'.$value));
                    $partida_privado = ($key===0?$value:($key===sizeof($partida_array)-1?$partida_privado.'.02':$partida_privado.'.'.$value));
                }

                $presupuesto_interno_gobierno= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',$id_tipo_presupuesto)->where('estado', 1)->where('partida', $partida_gobierno)->first();

                $presupuesto_interno_privado= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',$id_tipo_presupuesto)->where('estado', 1)->where('partida', $partida_privado)->first();

                $partida_creada = '02';
                $porcentaje_gobierno = 0;
                $porcentaje_privado = 0;
                $porcentaje_comicion = 0;
                $porcentaje_penalidad = 0;

                foreach (explode('.',$partida) as $key => $value) {
                    if ($key!==0) {
                        $partida_creada = $partida_creada.'.'.$value;
                    }
                }


                $porcentaje_gobierno    = $presupuesto_interno_gobierno->porcentaje_gobierno;
                $porcentaje_privado     = $presupuesto_interno_privado->porcentaje_privado;
                $porcentaje_comicion    = $presupuesto_interno_gobierno->porcentaje_comicion;
                $porcentaje_penalidad   = $presupuesto_interno_gobierno->porcentaje_penalidad;

                $costo_gobierno = 0;
                $costo_privado = 0;
                $costo_comisiones = 0;
                $costo_penalidades = 0;

                $presupuesto_interno_destalle_padre= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',1)->where('id_hijo',$presupuesto_interno_gobierno->id_padre)->where('estado', 1)->first();

                $valor_cabecera='';

                $monto_mensual_gobierno_string = $presupuesto_interno_gobierno->$mes;
                $monto_mensual_privado_string = $presupuesto_interno_privado->$mes;

                $monto_mensual_gobierno = '';
                $monto_mensual_privado = '';

                foreach (explode(',',$monto_mensual_gobierno_string) as $key => $value) {
                    $monto_mensual_gobierno = $monto_mensual_gobierno.$value;
                }
                foreach (explode(',',$monto_mensual_privado_string) as $key => $value) {
                    $monto_mensual_privado = $monto_mensual_privado.$value;
                }
                $valor_cabecera = '';
                foreach (explode(',',$presupuesto_interno_destalle_padre->$mes) as $key => $value) {
                    $valor_cabecera = $valor_cabecera.$value;
                }

                $costo_gobierno      = floatval($monto_mensual_gobierno) * (floatval($porcentaje_gobierno)/100);
                $costo_privado       = floatval($monto_mensual_privado) * (floatval($porcentaje_privado)/100);
                $costo_comisiones    = floatval($valor_cabecera) * (floatval($porcentaje_comicion)/100);
                $costo_penalidades   = floatval($valor_cabecera) * (floatval($porcentaje_penalidad)/100);

                $partida_costos_gobierno    = '';
                $partida_costos_privado     = '';
                $partida_costos_comisiones  = '';
                $partida_costos_penalidades = '';

                foreach (explode('.',$partida) as $key => $value) {
                    if ($key===0) {
                        $partida_costos_gobierno = '02';
                        $partida_costos_privado = '02';
                        $partida_costos_comisiones = '02';
                        $partida_costos_penalidades = '02';
                    }else{
                        $partida_costos_gobierno = $partida_costos_gobierno.'.'.$value;
                        $partida_costos_privado = ($key===sizeof(explode('.',$partida))-1?$partida_costos_privado.'.02':$partida_costos_privado.'.'.$value);

                        $partida_costos_comisiones = ($key===sizeof(explode('.',$partida))-1?$partida_costos_comisiones.'.03':$partida_costos_comisiones.'.'.$value);
                        $partida_costos_penalidades = ($key===sizeof(explode('.',$partida))-1?$partida_costos_penalidades.'.04':$partida_costos_penalidades.'.'.$value);
                    }
                }
                // actualizar los montos de acuerdo su porcentaje
                $presupuesto_interno_detalle_costos_gobierno = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',2)->where('partida',$partida_costos_gobierno)->where('estado', 1)->first();
                $presupuesto_interno_detalle_costos_gobierno->$mes = number_format($costo_gobierno, 2);
                $presupuesto_interno_detalle_costos_gobierno->save();

                $presupuesto_interno_detalle_costos_privado = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',2)->where('partida',$partida_costos_privado)->where('estado', 1)->first();
                $presupuesto_interno_detalle_costos_privado->$mes = number_format($costo_privado, 2);
                $presupuesto_interno_detalle_costos_privado->save();

                $presupuesto_interno_detalle_costos_comisiones = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',2)->where('partida',$partida_costos_comisiones)->where('estado', 1)->first();
                $presupuesto_interno_detalle_costos_comisiones->$mes = number_format($costo_comisiones, 2);
                $presupuesto_interno_detalle_costos_comisiones->save();

                $presupuesto_interno_detalle_costos_penalidades = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',2)->where('partida',$partida_costos_penalidades)->where('estado', 1)->first();
                $presupuesto_interno_detalle_costos_penalidades->$mes = number_format($costo_penalidades, 2);
                $presupuesto_interno_detalle_costos_penalidades->save();

                // return [$presupuesto_interno_detalle_costos_gobierno,$presupuesto_interno_detalle_costos_privado,$presupuesto_interno_detalle_costos_comisiones,$presupuesto_interno_detalle_costos_penalidades];exit;
            break;

            case 3:
                $presupuesto_interno_destalle_gastos_hijo = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', $partida)->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();

                $presupuesto_interno_destalle_gastos_padre = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)
                ->where('estado', 1)
                ->where('id_hijo', $presupuesto_interno_destalle_gastos_hijo->id_padre)
                ->where('id_tipo_presupuesto', $id_tipo_presupuesto)
                ->first();

                if ($presupuesto_interno_destalle_gastos_padre->partida.'.01' === '03.01.01.01'||$presupuesto_interno_destalle_gastos_padre->partida.'.02' === '03.01.01.02' ||$presupuesto_interno_destalle_gastos_padre->partida.'.03' === '03.01.01.03'  ) {

                    $presupuesto_interno_01 = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', $presupuesto_interno_destalle_gastos_padre->partida.'.01')->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();
                    $presupuesto_interno_02 = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', $presupuesto_interno_destalle_gastos_padre->partida.'.02')->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();
                    $presupuesto_interno_03 = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', $presupuesto_interno_destalle_gastos_padre->partida.'.03')->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();

                    $total = floatval(str_replace(",", "", $presupuesto_interno_01->$mes)) + floatval(str_replace(",", "", $presupuesto_interno_02->$mes)) + floatval(str_replace(",", "", $presupuesto_interno_03->$mes));


                    $essalud         = round(($total * 0.09), 0);
                    $sctr            = round(($total * 0.0158), 0);
                    $essalud_vida    = round(($total * 0.0127), 0);

                    $servicios       = round(($total * 0.0833), 2);
                    $gratificaciones = round(($total / 6), 2);
                    $vacacione       = round(($total / 12), 2);
                    // return number_format($essalud, 2);exit;

                    $essalud_partida = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', '03.01.02.01')->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();
                    $essalud_partida->$mes = number_format($essalud, 2);
                    $essalud_partida->save();

                    $sctr_partida = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', '03.01.02.02')->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();
                    $sctr_partida->$mes = number_format($sctr, 2);
                    $sctr_partida->save();

                    $essalud_vida_partida = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', '03.01.02.03')->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();
                    $essalud_vida_partida->$mes = number_format($essalud_vida, 2);
                    $essalud_vida_partida->save();

                    $servicios_partida = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', '03.01.03.01')->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();
                    $servicios_partida->$mes = number_format($servicios, 2);
                    $servicios_partida->save();

                    $gratificaciones_partida = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', '03.01.03.02')->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();
                    $gratificaciones_partida->$mes = number_format($gratificaciones, 2);
                    $gratificaciones_partida->save();

                    $vacacione_partida = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('estado', 1)->where('partida', '03.01.03.03')->where('id_tipo_presupuesto', $id_tipo_presupuesto)->first();
                    $vacacione_partida->$mes = number_format($vacacione, 2);
                    $vacacione_partida->save();

                }
            break;
        }

        // $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',$id_tipo_presupuesto)->where('estado', 1)->where('partida', $partida_creada)->first();


        return $id_presupuesto_interno;
    }
    public static function calcularConsumidoPresupuestoFilas($id_presupuesto_interno, $id_tipo_presupuesto)
    {
        $periodoActual = Periodo::where('estado', 1)->orderBy("id_periodo", "desc")->first();
        $yyyy = $periodoActual->descripcion;

        $requerimientoList = Requerimiento::where([['estado', '!=', 7], ['id_presupuesto_interno', '=', $id_presupuesto_interno]])
            ->whereYear('fecha_registro', '=', $yyyy)->get();

        $idRequerimientoList = [];
        foreach ($requerimientoList as $key => $requerimiento) {
            $idRequerimientoList[] = $requerimiento->id_requerimiento;
        }

        $detalleRequerimientoPartidaConsumidaList = DetalleRequerimiento::whereIn('alm_det_req.id_requerimiento', $idRequerimientoList)
            ->where([['alm_det_req.estado', '!=', 7], ['presupuesto_interno_detalle.id_tipo_presupuesto', $id_tipo_presupuesto]
            , ['presupuesto_interno_detalle.estado', 1], ['log_det_ord_compra.estado', '!=',7], ['log_ord_compra.estado', '!=',7]
            ])
            ->whereYear('presupuesto_interno_detalle.fecha_registro', '=', $yyyy)
            ->select('alm_det_req.id_requerimiento', 'alm_det_req.id_detalle_requerimiento',
                    'alm_det_req.partida as id_partida', 'alm_det_req.subtotal',
                    'presupuesto_interno_detalle.partida',
                    'log_det_ord_compra.subtotal as subtotal_orden'
                    )
            ->join('finanzas.presupuesto_interno_detalle', 'presupuesto_interno_detalle.id_presupuesto_interno_detalle', '=', 'alm_det_req.partida')
            ->join('logistica.log_det_ord_compra', 'log_det_ord_compra.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
            ->join('logistica.log_ord_compra', 'log_ord_compra.id_orden_compra', '=', 'log_det_ord_compra.id_orden_compra')
            ->get();

        return $detalleRequerimientoPartidaConsumidaList;
    }

    public static function saldoPartida($id_presupuesto_interno,$numero_mes='01',$nombre_mes,$monto,$partida)
    {
        $monto = floatval(str_replace(",", "", $monto));
        $respuesta=true;

        $nombre_mes= 'float_'.$nombre_mes;
        $presupuesto_interno_destalle_gastos_hijo = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)
        ->where('estado', 1)
        ->where('id_tipo_presupuesto', 3)
        ->where('partida', $partida)
        ->first();
        $monto_detalle = floatval(str_replace(",", "", $presupuesto_interno_destalle_gastos_hijo->$nombre_mes));
        if ($monto > $monto_detalle) {
            $respuesta=false;
        }

        return $respuesta;
    }
    public static function cierreMensual($id_tipo_presupuesto_interno,$numero_mes='01',$nombre_mes,$numero_mes_siguiente,$nombre_mes_siguiente)
    {
        $array_requerimiento_detalle = array();
        $array_id_presupuesto_interno = array();
        $array_id_presupuesto_interno_detalle = array();
        $mes_siguiente =  $nombre_mes_siguiente.'_aux';
        $mes_aux = $nombre_mes.'_aux';
        $array_historia_presupuesto_interno=array();
        $array_id_presupuesto_interno = array();
        $año_actua = date('Y');

        // presupuesto aprobados
        $presupuesto_interno = PresupuestoInterno::where('estado',2)
        ->whereYear('fecha_registro',$año_actua)
        ->get();

        foreach ($presupuesto_interno as $key => $value) {
            if (!in_array($value->id_presupuesto_interno, $array_id_presupuesto_interno)) {
                array_push($array_id_presupuesto_interno,$value->id_presupuesto_interno);
            }
        }
        $array_temporal=array();
        foreach ($array_id_presupuesto_interno as $key => $value) {
            $historial = PresupuestoInternoDetalle::where('id_presupuesto_interno',$value)
            // ->whereMonth('fecha_registro',$numero_mes)
            ->where('estado','!=',7)
            ->where('id_tipo_presupuesto',3)
            ->orderBy('partida', 'asc')
            ->get();

            array_push($array_temporal,$historial);

            if (sizeof($historial)>0) {
                $saldo_partida = 0;
                foreach ($historial as $key_partida => $value_partida) {
                    if ($key_partida!==0 && $value_partida->registro==='2') {

                        $saldo_mes_actual = floatval(str_replace(",", "", $value_partida->$mes_aux));

                        $inicio_mes_siguiente = ($nombre_mes!=='diciembre'?floatval(str_replace(",", "", $value_partida->$nombre_mes_siguiente)):0) ;

                        $saldo_mes_siguiente =  $saldo_mes_actual + $inicio_mes_siguiente;

                        $mes_siguiente = ($nombre_mes!=='diciembre'?$mes_siguiente:'saldo_anual');

                        $presupuesto_interno_detalle= PresupuestoInternoDetalle::find($value_partida->id_presupuesto_interno_detalle);
                        $presupuesto_interno_detalle->$mes_siguiente = $saldo_mes_siguiente;
                        $presupuesto_interno_detalle->save();

                        #historial de saldo
                        $historial = HistorialPresupuestoInternoSaldo::where('id_presupuesto_interno',$presupuesto_interno_detalle->id_presupuesto_interno)
                        ->where('id_partida',$presupuesto_interno_detalle->id_presupuesto_interno_detalle)
                        ->where('descripcion','saldo del mes anterior')->first();
                        if (!$historial) {
                            $historial = new HistorialPresupuestoInternoSaldo();
                        }
                        // $historial = HistorialPresupuestoInternoSaldo::firstOrNew([
                        //     'id_presupuesto_interno' => $presupuesto_interno_detalle->id_presupuesto_interno,
                        //     'id_partida' => $presupuesto_interno_detalle->id_presupuesto_interno_detalle,
                        //     'descripcion' => 'saldo del mes anterior'
                        // ]
                        //     ['id_presupuesto_interno' => 'Flight 10'],
                        //     ['id_partida' => 1, 'arrival_time' => '11:30'],
                        //     ['descripcion' => 1, 'arrival_time' => '11:30']
                        // );
                            $historial->id_presupuesto_interno = $presupuesto_interno_detalle->id_presupuesto_interno;
                            $historial->id_partida = $presupuesto_interno_detalle->id_presupuesto_interno_detalle;
                            $historial->tipo = 'INGRESO';
                            $historial->importe = floatval(str_replace(",", "", $saldo_mes_actual));
                            $historial->mes = $numero_mes_siguiente;
                            $historial->fecha_registro = date('Y-m-d H:i:s');
                            $historial->estado = 3;
                            $historial->descripcion = 'saldo del mes anterior';
                            $historial->operacion = 'S';
                        $historial->save();
                        #-----
                        PresupuestoInterno::calcularColumnaAuxMensual(
                            $presupuesto_interno_detalle->id_presupuesto_interno,
                            $presupuesto_interno_detalle->id_tipo_presupuesto,
                            $presupuesto_interno_detalle->id_presupuesto_interno_detalle,
                            $nombre_mes_siguiente
                        );
                        array_push($array_historia_presupuesto_interno,$presupuesto_interno_detalle);
                        array_push($array_historia_presupuesto_interno,$saldo_mes_siguiente);
                    }
                    // $value_partida->saldo = $saldo_partida;
                }
            }

        }


        return [$mes_siguiente];exit;
    }


    public static function calcularColumnaAuxMensual($id_presupuesto_interno, $id_tipo_presupuesto, $id_partida,$mes)
    {
        // ini_set('max_execution_time', 50000);
        // return ;rexit;
        $mes= $mes.'_aux';
        $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',$id_tipo_presupuesto)->where('estado', 1)->where('id_presupuesto_interno_detalle', $id_partida)->orderBy('partida')->first();

        $id_hijo = $presupuesto_interno_destalle->id_hijo;
        $id_padre = $presupuesto_interno_destalle->id_padre;
        $total = 0;

        // if ('03.01.03.01'===$partida) {
        //     return $presupuesto_interno_destalle;exit;
        // }
        while ($id_padre!=='0' && $id_padre!==0) {
            $total = 0;
            $partidas = PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',$id_tipo_presupuesto)->where('estado', 1)->where('id_padre', $id_padre)->orderBy('partida')->get();

            foreach ($partidas as $key => $value) {
                $float_mes = 'float_'.$mes;
                $columna_mes      = $value->$float_mes;
                $total      = $total + $columna_mes;
            }

            $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',$id_tipo_presupuesto)->where('estado', 1)->where('id_hijo', $id_padre)->orderBy('partida')->first();

            if ($presupuesto_interno_destalle) {

                $presupuesto_interno_destalle->$mes = $total;
                $presupuesto_interno_destalle->save();
                $id_hijo = $presupuesto_interno_destalle->id_hijo;
                $id_padre = $presupuesto_interno_destalle->id_padre;
            }



        }
        // return $partidas;
    }
    public function grupo(): BelongsTo
    {
        return $this->belongsTo(AdmGrupo::class,'id_grupo');
    }
    public static function presupuestoEjecutado($id_presupuesto_interno, $id_tipo_presupuesto)
    {
        $presupuesto_interno_destalle=array();
        switch ($id_tipo_presupuesto) {
            case 1:
                $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',1)->where('estado', 1)->orderBy('partida')->where('registro',2)->get();
            break;

            case 2:
                $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',2)->where('estado', 1)->orderBy('partida')->where('registro',2)->get();
            break;
            case 3:
                $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$id_presupuesto_interno)->where('id_tipo_presupuesto',3)->where('estado', 1)->where('registro',2)->orderBy('partida')->get();
            break;
        }
        $array_nivel_partida = array();
        foreach ($presupuesto_interno_destalle as $key => $value) {
            $total=0;

            $enero      = $value->float_enero_aux;
            $febrero    = $value->float_febrero_aux;
            $marzo      = $value->float_marzo_aux ;
            $abril      = $value->float_abril_aux;
            $mayo       = $value->float_mayo_aux;
            $junio      = $value->float_junio_aux;
            $julio      = $value->float_julio_aux;
            $agosto     = $value->float_agosto_aux;
            $setiembre  = $value->float_setiembre_aux;
            $octubre    = $value->float_octubre_aux;
            $noviembre  = $value->float_noviembre_aux;
            $diciembre  = $value->float_diciembre_aux;
            $total      = $enero + $febrero + $marzo + $abril + $mayo + $junio + $julio + $agosto + $setiembre + $octubre + $noviembre + $diciembre;
            array_push($array_nivel_partida,array(
                "partida"=>$value->partida,
                "descripcion"=>$value->descripcion,
                "total"=>round($total, 2),
            ));
        }
        $total_ejecutado = 0;
        foreach ($array_nivel_partida as $key => $value) {
            $total_ejecutado = $total_ejecutado + $value['total'];
        }
        $presupuesto_total = PresupuestoInterno::calcularTotalPresupuestoAnual($id_presupuesto_interno, $id_tipo_presupuesto);
        $total_ejecutado = $presupuesto_total - $total_ejecutado;
        // $total_ejecutado = $total_ejecutado;
        return $total_ejecutado;

        // return 'ejecutado';
    }
    public static function totalEjecutatoMonto($mes, $id_presupuesto_interno)
    {
        $mes = intval($mes);

        $total = 0;

        for ($i=1; $i <= $mes ; $i++) {
            $saldo = HistorialPresupuestoInternoSaldo::where('id_presupuesto_interno',$id_presupuesto_interno)
            ->where([
                ['mes',ConfiguracionHelper::leftZero(2,$i)],
                // ['tipo','SALIDA'],
                ['estado',3]]
            )
            ->orderBy('id','ASC')
            ->get();

            if (sizeof($saldo)>0) {
                foreach ($saldo as $key => $value) {

                    if ($value->operacion==='R') {
                        $total = $total + floatval($value->importe);
                    }
                }
            }

        }
        return $total;
    }

    #total de presupuesto anual en filas de partidas contemplando el nivel indicado
    public static function totalPartidasAnualFilasNivel($presupuesto_interno_id, $tipo, $nivel, $tipoCampo=1)
    {
        $presupuesto_interno_destalle= PresupuestoInternoDetalle::where('id_presupuesto_interno',$presupuesto_interno_id)->where('id_tipo_presupuesto',$tipo)->where('estado', 1)->orderBy('partida')->get();

        $array_nivel_partida = array();
        foreach ($presupuesto_interno_destalle as $key => $value) {
            $array_partida = explode('.',$value->partida);
            $nivel_array = sizeof($array_partida);
            // return $nivel;exit;
            if ($nivel_array == $nivel) {
                $total=0;

                $enero      = ($tipoCampo == 1) ? $value->float_enero : $value->float_enero_aux;
                $febrero    = ($tipoCampo == 1) ? $value->float_febrero : $value->float_febrero_aux;
                $marzo      = ($tipoCampo == 1) ? $value->float_marzo : $value->float_marzo_aux;
                $abril      = ($tipoCampo == 1) ? $value->float_abril : $value->float_abril_aux;
                $mayo       = ($tipoCampo == 1) ? $value->float_mayo : $value->float_mayo_aux;
                $junio      = ($tipoCampo == 1) ? $value->float_junio : $value->float_junio_aux;
                $julio      = ($tipoCampo == 1) ? $value->float_julio : $value->float_julio_aux;
                $agosto     = ($tipoCampo == 1) ? $value->float_agosto : $value->float_agosto_aux;
                $setiembre  = ($tipoCampo == 1) ? $value->float_setiembre : $value->float_setiembre_aux;
                $octubre    = ($tipoCampo == 1) ? $value->float_octubre : $value->float_octubre_aux;
                $noviembre  = ($tipoCampo == 1) ? $value->float_noviembre : $value->float_noviembre_aux;
                $diciembre  = ($tipoCampo == 1) ? $value->float_diciembre : $value->float_diciembre_aux;
                $total      = $enero + $febrero + $marzo + $abril + $mayo + $junio + $julio + $agosto + $setiembre + $octubre + $noviembre + $diciembre;
                array_push($array_nivel_partida,array(
                    "partida"=>$value->partida,
                    "descripcion"=>$value->descripcion,
                    "total"=>round($total, 2),
                ));
            }

        }

        return $array_nivel_partida;
    }

}
