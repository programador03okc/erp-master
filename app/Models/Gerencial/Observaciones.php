<?php

namespace App\Models\Gerencial;

use App\Models\Configuracion\SisUsua;
use Illuminate\Database\Eloquent\Model;

class Observaciones extends Model
{
    //
    protected $table = 'cobranza.cobranzas_observaciones';
    protected $fillable = ['cobranza_id', 'usuario_id', 'descripcion', 'oc_id', 'estado', 'created_at'];
    protected $primaryKey = 'id';
    // protected $hidden = ['updated_at', 'deleted_at'];
    // public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(SisUsua::class, 'usuario_id', 'id_usuario');
    }
}
