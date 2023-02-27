<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormaPago extends Model
{
    use SoftDeletes;
    
    protected $table = 'cobranza.rc_forma_pago';
    protected $fillable = ['descripcion'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
