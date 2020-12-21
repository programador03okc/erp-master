<?php

namespace App\Models\Presupuestos;

use Illuminate\Database\Eloquent\Model;

class Titulo extends Model
{
    protected $table = 'finanzas.presup_titu';

    protected $primaryKey = 'id_titulo';

    public $timestamps = false;
    
    protected $fillable = [
        "id_presup",
        "codigo",
        "descripcion",
        "cod_padre",
        "total",
        "estado",
        "fecha_registro"
    ];

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class, 'id_presup');
    }
}
