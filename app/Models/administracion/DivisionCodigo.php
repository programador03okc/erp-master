<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DivisionCodigo extends Model
{
    //
    use SoftDeletes;
    protected $table = 'administracion.division_codigo';
    protected $fillable = [
        'codigo','descripcion','division_id','sede_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
