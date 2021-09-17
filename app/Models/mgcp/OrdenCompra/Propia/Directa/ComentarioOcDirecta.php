<?php

namespace App\Models\mgcp\OrdenCompra\Propia\Directa;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioOcDirecta extends Model {

    use HasFactory;
    protected $table = 'mgcp_ordenes_compra.oc_directas_comentarios';
    public $timestamps = false;

    public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function ordenCompra() {
        return $this->belongsTo(OrdenCompraDirecta::class, 'id_oc');
    }
    
    public function getFechaAttribute() {
        return date_format(date_create($this->attributes['fecha']), 'd-m-Y g:i A');
    }
}
