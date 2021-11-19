<?php

namespace App\Models\mgcp\CuadroCosto;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialPrecio extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_cuadro_costos.historial_precios';
    public $timestamps = false;
    protected $appends = ['fecha_format'];

    public function setFechaAttribute()
    {
        $this->attributes['fecha'] = date('Y-m-d H:i:s');
    }

    public function getFechaFormatAttribute()
    {
        return date_format(date_create($this->fecha), 'd-m-Y H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_responsable');
    }
}
