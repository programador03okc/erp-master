<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Model;

class PresupuestoInternoDetalle extends Model
{
    //
    protected $table = 'finanzas.presupuesto_interno_detalle';
    protected $primaryKey = 'id_presupuesto_interno_detalle';
    public $timestamps = false;

    //1
    public function getFloatEneroAttribute()
    {
      return floatval(str_replace(",", "", $this->enero));
    }
    //2
    public function getFloatFebreroAttribute()
    {
      return floatval(str_replace(",", "", $this->febrero));
    }
    //3
    public function getFloatMarzoAttribute()
    {
      return floatval(str_replace(",", "", $this->marzo));
    }
    //4
    public function getFloatAbrilAttribute()
    {
      return floatval(str_replace(",", "", $this->abril));
    }
    //5
    public function getFloatMayoAttribute()
    {
      return floatval(str_replace(",", "", $this->mayo));
    }
    //6
    public function getFloatJunioAttribute()
    {
      return floatval(str_replace(",", "", $this->junio));
    }
    //7
    public function getFloatJulioAttribute()
    {
      return floatval(str_replace(",", "", $this->julio));
    }
    //8
    public function getFloatAgostoAttribute()
    {
      return floatval(str_replace(",", "", $this->agosto));
    }
    //9
    public function getFloatSetiembreAttribute()
    {
      return floatval(str_replace(",", "", $this->setiembre));
    }
    //10
    public function getFloatOctubreAttribute()
    {
      return floatval(str_replace(",", "", $this->octubre));
    }
    //11
    public function getFloatNoviembreAttribute()
    {
      return floatval(str_replace(",", "", $this->noviembre));
    }
    //12
    public function getFloatDiciembreAttribute()
    {
      return floatval(str_replace(",", "", $this->diciembre));
    }

    // -----aux----
    //1
    public function getFloatEneroAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->enero_aux));
    }
    //2
    public function getFloatFebreroAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->febrero_aux));
    }
    //3
    public function getFloatMarzoAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->marzo_aux));
    }
    //4
    public function getFloatAbrilAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->abril_aux));
    }
    //5
    public function getFloatMayoAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->mayo_aux));
    }
    //6
    public function getFloatJunioAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->junio_aux));
    }
    //7
    public function getFloatJulioAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->julio_aux));
    }
    //8
    public function getFloatAgostoAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->agosto_aux));
    }
    //9
    public function getFloatSetiembreAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->setiembre_aux));
    }
    //10
    public function getFloatOctubreAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->octubre_aux));
    }
    //11
    public function getFloatNoviembreAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->noviembre_aux));
    }
    //12
    public function getFloatDiciembreAuxAttribute()
    {
      return floatval(str_replace(",", "", $this->diciembre_aux));
    }
}
