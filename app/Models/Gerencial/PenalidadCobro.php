<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenalidadCobro extends Model
{
    use SoftDeletes;
    
    protected $table = 'cobranza.penalidad_cobro';
    protected $fillable = ['id_penalidad', 'id_registro_cobranza', 'importe', 'fecha_cobro', 'motivo', 'estado', 'gestion', 'pagador'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function penalidad()
    {
        return $this->belongsTo(Penalidad::class, 'id_penalidad', 'id_penalidad');
    }

    public function cobranza()
    {
        return $this->belongsTo(RegistroCobranza::class, 'id_registro_cobranza', 'id_registro_cobranza');
    }
}
