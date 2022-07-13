<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificacion extends Model
{
    use SoftDeletes;

    protected $table = 'configuracion.notificaciones';
    protected $fillable = ['id_usuario', 'mensaje', 'fecha', 'url', 'leido'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
