<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoNegocio extends Model
{
    use SoftDeletes;
    
    protected $table = 'cobranza.rc_tipo_negocio';
    protected $fillable = ['descripcion'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
