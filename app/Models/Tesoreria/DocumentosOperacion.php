<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class DocumentosOperacion extends Model
{
    //
    protected $table = 'finanzas.sis_documentos_operacion';

    public $timestamps = false;

   protected $fillable = [
        'codigo',
        'descripcion',
        'moneda_id',
    ];


    public function cajachica(){
        return $this->hasOne('App\Models\Tesoreria\CajaChicaMovimiento','id');
    }
}
