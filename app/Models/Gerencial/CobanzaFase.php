<?php

namespace App\models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class CobanzaFase extends Model
{
    //
    protected $table = 'gerencia_cobranza.cobranza_fase';
    protected $primaryKey = 'id_fase';
    protected $fillable = ['id_cobranza', 'fase', 'fecha', 'estado', 'fecha_registro'];
    public $timestamps = false;

    public function cobranza()
    {
        return $this->belongsTo(Cobranza::class,'id_cobranza', 'id_cobranza');
    }
}
