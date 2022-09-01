<?php

namespace App\models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class AreaResponsable extends Model
{
    //
    protected $table = 'gerencia_cobranza.area_responsable';
    protected $primaryKey = 'id_area';
    public $timestamps = false;
    public function cobranza()
    {
        return $this->belongsTo(Cobranza::class, 'id_area', 'id_area');
    }
}
