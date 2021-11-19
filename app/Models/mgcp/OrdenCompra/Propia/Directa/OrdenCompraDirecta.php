<?php

namespace App\Models\mgcp\OrdenCompra\Propia\Directa;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraDirecta extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_ordenes_compra.oc_directas';
    //public $timestamps = false;

    public function setFechaPublicacionAttribute($valor)
    {
        $this->attributes['fecha_publicacion'] = Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }

    public function setFechaEntregaAttribute($valor)
    {
        $this->attributes['fecha_entrega'] = Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }

    public function setOccAttribute($valor)
    {
        $this->attributes['occ'] = mb_strtoupper($valor);
    }

    public function setMontoTotalAttribute($valor)
    {
        $this->attributes['monto_total'] = str_replace(',','',$valor);
    }

    public function setLugarEntregaAttribute($valor)
    {
        $this->attributes['lugar_entrega'] = mb_strtoupper($valor);
    }

    public function setFechaGuiaAttribute($valor)
    {
        $this->attributes['fecha_guia'] = $valor == null ? null : Carbon::createFromFormat('d-m-Y', $valor)->toDateString();
    }

    /*public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function ordenCompra() {
        return $this->belongsTo(OrdenCompraPropia::class, 'id_oc');
    }
    
    public function getFechaAttribute() {
        return date_format(date_create($this->attributes['fecha']), 'd-m-Y g:i A');
    }*/
}
