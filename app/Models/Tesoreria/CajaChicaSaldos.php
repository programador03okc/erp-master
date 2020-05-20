<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class CajaChicaSaldos extends Model
{
    //
    protected $table = 'finanzas.cajachica_saldos';

    //protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'cajachica_movimiento_id',
        'fecha',
        'inicial',
        'ingreso',
        'egreso',
        'saldo'
    ];

    //  protected $hidden = ['id_sucursal'];

    //protected $guarded = ['id'];


    public function cajachica_movimiento(){
        return $this->belongsTo(CajaChicaMovimiento::class,'cajachica_movimiento_id');
    }
}
