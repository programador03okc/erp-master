<?php

namespace App\Models\Finanzas;

use App\Models\Administracion\Periodo;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use Illuminate\Database\Eloquent\Model;

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
        $enero      = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->enero));
        $febrero    = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->febrero));
        $marzo      = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->marzo ));
        $abril      = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->abril));
        $mayo       = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->mayo));
        $junio      = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->junio));
        $julio      = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->julio));
        $agosto     = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->agosto));
        $setiembre  = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->setiembre));
        $octubre    = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->octubre));
        $noviembre  = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->noviembre));
        $diciembre  = floatval(str_replace(",", "", $presupuesto_interno_destalle[0]->diciembre));
        $total      = $enero + $febrero + $marzo + $abril + $mayo + $junio + $julio + $agosto + $setiembre + $octubre + $noviembre + $diciembre;
        return $total;
    }
    // es el total en filas a la altura de la partida de todo el año
    public static function calcularTotalPresupuestoFilas($id_presupuesto_interno, $id_tipo_presupuesto)
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

            $enero      = floatval(str_replace(",", "", $value->enero));
            $febrero    = floatval(str_replace(",", "", $value->febrero));
            $marzo      = floatval(str_replace(",", "", $value->marzo ));
            $abril      = floatval(str_replace(",", "", $value->abril));
            $mayo       = floatval(str_replace(",", "", $value->mayo));
            $junio      = floatval(str_replace(",", "", $value->junio));
            $julio      = floatval(str_replace(",", "", $value->julio));
            $agosto     = floatval(str_replace(",", "", $value->agosto));
            $setiembre  = floatval(str_replace(",", "", $value->setiembre));
            $octubre    = floatval(str_replace(",", "", $value->octubre));
            $noviembre  = floatval(str_replace(",", "", $value->noviembre));
            $diciembre  = floatval(str_replace(",", "", $value->diciembre));
            $total      = $enero + $febrero + $marzo + $abril + $mayo + $junio + $julio + $agosto + $setiembre + $octubre + $noviembre + $diciembre;
            array_push($array_nivel_partida,array(
                "partida"=>$value->partida,
                "descripcion"=>$value->descripcion,
                "total"=>round($total, 2),
            ));
        }

        return $array_nivel_partida;
    }

    // calcula el total de un mes en especifico tomandolo como columna
    public static function calcularTotalMensualColumnas($id_presupuesto_interno, $id_tipo_presupuesto, $partida = '01.01.01.01', $mes = 'enero')
    {

        $presupuesto_interno_destalle = PresupuestoInternoDetalle::where('id_presupuesto_interno', $id_presupuesto_interno)->where('id_tipo_presupuesto', $id_tipo_presupuesto)->where('estado', 1)->where('partida', $partida)->orderBy('partida')->first();

        $id_hijo = $presupuesto_interno_destalle->id_hijo;
        $id_padre = $presupuesto_interno_destalle->id_padre;
        $total = 0;
        while ($id_padre !== '0') {
            $total = 0;
            $partidas = PresupuestoInternoDetalle::where('id_presupuesto_interno', $id_presupuesto_interno)->where('id_tipo_presupuesto', $id_tipo_presupuesto)->where('estado', 1)->where('id_padre', $id_padre)->orderBy('partida')->get();

            foreach ($partidas as $key => $value) {
                $columna_mes      = floatval(str_replace(",", "", $value->$mes));
                $total      = $total + $columna_mes;
            }

            $presupuesto_interno_destalle = PresupuestoInternoDetalle::where('id_presupuesto_interno', $id_presupuesto_interno)->where('id_tipo_presupuesto', $id_tipo_presupuesto)->where('estado', 1)->where('id_hijo', $id_padre)->orderBy('partida')->first();
            $presupuesto_interno_destalle->$mes = number_format($total, 2);
            $presupuesto_interno_destalle->save();

            $id_hijo = $presupuesto_interno_destalle->id_hijo;
            $id_padre = $presupuesto_interno_destalle->id_padre;
        }
        return $partidas;
    }

    public static function calcularTotalMensualColumnasPorcentajes($id_presupuesto_interno, $id_tipo_presupuesto, $partida = '01.01.01.01', $mes = 'enero')
    {
        $partida_creada = '';
        $presupuesto_interno_destalle_inicio = PresupuestoInternoDetalle::where('id_presupuesto_interno', $id_presupuesto_interno)->where('id_tipo_presupuesto', $id_tipo_presupuesto)->where('estado', 1)->where('partida', $partida)->first();

        $presupuesto_interno_destalle = array();
        switch ($id_tipo_presupuesto) {
            case 1:
                $partida = explode('.', $partida);
                $partida_creada = '02';
                $partida_numero_final = '';
                $porcentaje_gobierno = 0;
                $porcentaje_privado = 0;
                $porcentaje_comicion = 0;
                $porcentaje_penalidad = 0;

                foreach ($partida as $key => $value) {
                    if ($key !== 0) {
                        $partida_creada = $partida_creada . '.' . $value;
                    }

                    $partida_numero_final = $value;
                }
                $presupuesto_interno_destalle = PresupuestoInternoDetalle::where('id_presupuesto_interno', $id_presupuesto_interno)->where('id_tipo_presupuesto', 2)->where('estado', 1)->where('partida', $partida_creada)->first();

                $porcentaje_gobierno    = $presupuesto_interno_destalle_inicio->porcentaje_gobierno;
                $porcentaje_privado     = $presupuesto_interno_destalle_inicio->porcentaje_privado;
                $porcentaje_comicion    = $presupuesto_interno_destalle_inicio->porcentaje_comicion;
                $porcentaje_penalidad   = $presupuesto_interno_destalle_inicio->porcentaje_penalidad;

                $costo_gobierno = 0;
                $costo_privado = 0;
                $costo_comisiones = 0;
                $costo_penalidades = 0;
                $valor_cabecera = '';

                return $porcentaje_gobierno;
                exit;
                break;

            case 3:
                # code...
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
            ->where([['alm_det_req.estado', '!=', 7], ['presupuesto_interno_detalle.id_tipo_presupuesto', $id_tipo_presupuesto], ['presupuesto_interno_detalle.estado', 1]])
            ->whereYear('presupuesto_interno_detalle.fecha_registro', '=', $yyyy)
            ->select('alm_det_req.id_requerimiento', 'alm_det_req.id_detalle_requerimiento', 'alm_det_req.partida as id_partida', 'alm_det_req.subtotal', 'presupuesto_interno_detalle.partida')
            ->join('finanzas.presupuesto_interno_detalle', 'presupuesto_interno_detalle.id_presupuesto_interno_detalle', '=', 'alm_det_req.partida')
            ->get();

        return $detalleRequerimientoPartidaConsumidaList;
    }
}
