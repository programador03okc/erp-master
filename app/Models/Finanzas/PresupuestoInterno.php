<?php

namespace App\Models\Finanzas;

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
}
