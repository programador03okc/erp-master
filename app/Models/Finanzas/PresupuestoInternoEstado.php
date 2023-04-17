<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PresupuestoInternoEstado extends Model
{
    //
    use SoftDeletes;
    protected $table = 'finanzas.presupuesto_interno_estado';
    protected $fillable = [
        'descripcion'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
