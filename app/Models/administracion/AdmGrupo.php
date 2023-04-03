<?php

namespace App\Models\administracion;

use App\Models\Finanzas\PresupuestoInterno;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmGrupo extends Model
{
    //
    protected $table = 'administracion.adm_grupo';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;

    public function presupuestoInterno(): HasMany
    {
        return $this->hasMany(PresupuestoInterno::class,'id_grupo');
    }
}
