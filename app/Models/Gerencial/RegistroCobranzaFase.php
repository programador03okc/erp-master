<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class RegistroCobranzaFase extends Model
{
    protected $table = 'cobranza.registro_cobranza_fases';
    protected $fillable = ['id_registro_cobranza', 'fase', 'fecha'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
