<?php

namespace App\models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class CobranzaFase extends Model
{
    //
    protected $table = 'cobranza.cobranza_fase';
    protected $primaryKey = 'id_fase';
    protected $fillable = ['id_cobranza', 'fase', 'fecha', 'estado', 'fecha_registro', 'id_registro_cobranza'];
    public $timestamps = false;

    public function cobranza()
    {
        return $this->belongsTo(RegistroCobranza::class,'id_cobranza', 'id_cobranza');
    }
}
