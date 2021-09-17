<?php

namespace App\Models\mgcp\AcuerdoMarco\Producto;

use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class HistorialActualizacion extends Model
{
    protected $table = 'mgcp_acuerdo_marco.producto_historial_actualizaciones';
    public $timestamps = false;
    
    public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    
    public function empresa() {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
    
    public function producto() {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    
    public function getFechaAttribute() {
        //return date_format(date_create($this->attributes['fecha'], timezone_open('America/Lima')), 'd-m-Y H:i');
        return date_format(date_create($this->attributes['fecha']), 'd-m-Y');
    }
}
