<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PresupuestoInternoDetalleHistorial extends Model
{
    //
    use SoftDeletes;
    protected $table = 'finanzas.presupuesto_interno_detalle_historial';
    protected $fillable = [
        'partida, descripcion', 'id_padre', 'id_tipo_presupuesto',
        'id_presupuesto_interno', 'id_grupo', 'id_area', 'fecha_registro', 'estado',
        'monto', 'id_hijo', 'registro', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre',
        'porcentaje_gobierno', 'porcentaje_privado', 'porcentaje_comicion',
        'porcentaje_penalidad', 'porcentaje_costo', 'enero_aux', 'febrero_aux',
        'marzo_aux', 'abril_aux', 'mayo_aux', 'junio_aux', 'julio_aux', 'agosto_aux',
        'setiembre_aux', 'octubre_aux', 'noviembre_aux', 'diciembre_aux', 'saldo_anual'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $timestamps = false;
}
