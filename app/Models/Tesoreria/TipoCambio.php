<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class TipoCambio extends Model
{
    //
    protected $table = 'contabilidad.cont_tp_cambio';

    protected $primaryKey = 'id_tp_cambio';

    public $timestamps = false;

   protected $fillable = [
        'fecha',
        'moneda',
        'compra',
        'venta',
        'estado',
    ];
    protected $guarded = ['id_tp_cambio'];

}
