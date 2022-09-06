<?php

namespace App\models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class TableConfiguracionModulo extends Model
{
    //
    protected $table = 'configuracion.table_configuracion_modulo';
    protected $primaryKey = 'id_modulo';
    public $timestamps = false;

    public function accesos()
    {
        return $this->belongsTo(Accesos::class, 'id_modulo', 'id_modulo');
    }
}
