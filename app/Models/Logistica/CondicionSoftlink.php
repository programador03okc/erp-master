<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CondicionSoftlink extends Model
{
    protected $table = 'logistica.condicion_softlink';
    protected $primaryKey = 'id_condicion_softlink';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = DB::table('logistica.condicion_softlink')->orderBy('condicion_softlink.dias', 'asc') ->get();
        return $data;
    }
}