<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class Penalidad extends Model
{
    //
    protected $table = 'cobranza.penalidad';
    protected $primaryKey = 'id_penalidad';
    protected $fillable = ['tipo', 'monto', 'documento', 'fecha', 'observacion', 'estado', 'fecha_registro', 'id_registro_cobranza', 'id_oc', 'motivo', 'estado_penalidad'];
    public $timestamps = false;
}
