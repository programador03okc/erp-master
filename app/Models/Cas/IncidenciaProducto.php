<?php

namespace App\Models\Cas;

use App\Models\Almacen\Producto;
use Illuminate\Database\Eloquent\Model;

class IncidenciaProducto extends Model
{
    protected $table = 'cas.incidencia_producto';
    public $timestamps = false;
    protected $primaryKey = 'id_incidencia_producto';

    public function incidencia()
    {
        return $this->belongsTo(Incidencia::class, 'id_incidencia');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_usuario');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado');
    }
}
