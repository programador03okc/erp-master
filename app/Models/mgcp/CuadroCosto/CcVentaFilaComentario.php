<?php

namespace App\Models\mgcp\CuadroCosto;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CcVentaFilaComentario extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.cc_venta_fila_comentarios';
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function getFechaAttribute($date)
    {
        return Carbon::parse($date)->format('d-m-Y');
    }
    /*public function setPvuOcAttribute($value)
    {
        $this->attributes['pvu_oc'] = str_replace(',', '', $value);
    }
    
    public function setCantAttribute($value)
    {
        $this->attributes['cant'] = str_replace(',', '', $value);
    }
    
    public function setFleteOcAttribute($value)
    {
        $this->attributes['flete_oc'] = str_replace(',', '', $value);
    }
    
    public function amProveedor() {
        return $this->hasOne('App\mgcp\CuadroCosto\CcAmProveedor','id', 'proveedor_seleccionado');
    }*/
}
