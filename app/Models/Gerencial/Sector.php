<?php

namespace App\models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    //
    protected $table = 'cobranza.sector';
    protected $primaryKey = 'id_sector';
    public $timestamps = false;
}
