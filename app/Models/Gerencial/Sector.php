<?php

namespace App\models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    //
    protected $table = 'gerencia_cobranza.sector';
    protected $primaryKey = 'id_sector';
    public $timestamps = false;
}
