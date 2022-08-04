<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'configuracion.notificaciones';
    protected $primaryKey = 'id';
    protected $fillable = ['id_usuario', 'mensaje', 'fecha', 'url', 'leido'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
