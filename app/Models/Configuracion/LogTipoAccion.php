<?php

namespace App\Models\Configuracion;
use Illuminate\Database\Eloquent\Model;

class LogTipoAccion extends Model
{
    protected $table = 'configuracion.log_tipo_acciones';
    protected $fillable = ['descripcion'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
