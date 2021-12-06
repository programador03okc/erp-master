<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CondicionSoftlink extends Model
{
    protected $table = 'logistica.condicion_softlink';
    protected $primaryKey = 'id_condicion_softlink';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public static function mostrar()
    {
        $data = CondicionSoftlink::orderBy('condicion_softlink.dias', 'asc') ->get();
        return $data;
    }
}