<?php

namespace App\Models\Presupuestos;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $table = 'finanzas.presup_par';

    protected $primaryKey = 'id_partida';

    public $timestamps = false;
    
    protected $fillable = [
        "id_presup",
        "codigo",
        "id_pardet",
        "importe_base",
        "importe_total",
        "cod_padre",
        "estado",
        "fecha_registro",
        "descripcion"
    ];

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class, 'id_presup');
    }
}
