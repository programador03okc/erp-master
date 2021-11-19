<?php

namespace App\Models\mgcp\CuadroCosto;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AprobadorTres extends Model
{
    // use HasFactory;
    protected $table = 'mgcp_cuadro_costos.aprobadores_tipo_tres';
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
