<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CategoriaAdjunto extends Model
{
    protected $table = 'logistica.categoria_adjunto';
    protected $primaryKey = 'id_categoria_adjunto';
    public $timestamps = false;

}
