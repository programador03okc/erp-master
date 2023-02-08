<?php

namespace App\models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class TipoTramite extends Model
{
    //
    protected $table = 'cobranza.tipo_tramite';
    protected $primaryKey = 'id_tipo_tramite';
    public $timestamps = false;
}
