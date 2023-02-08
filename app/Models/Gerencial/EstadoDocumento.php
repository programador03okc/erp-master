<?php

namespace App\models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class EstadoDocumento extends Model
{
    //
    protected $table = 'cobranza.estado_doc';
    protected $primaryKey = 'id_estado_doc';
    public $timestamps = false;

    public function cobranza()
    {
        return $this->belongsTo(RegistroCobranza::class, 'id_estado_doc', 'id_estado_doc');
    }
}
