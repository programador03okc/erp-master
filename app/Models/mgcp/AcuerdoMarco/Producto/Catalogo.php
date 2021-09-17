<?php

namespace App\Models\mgcp\AcuerdoMarco\Producto;

use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Catalogo extends Model
{
    protected $table = 'mgcp_acuerdo_marco.catalogos';
    public $timestamps = false;

    public static function obtenerCatalogosPorEmpresa($idEmpresa)
    {
        return DB::select("SELECT catalogos.* FROM mgcp_acuerdo_marco.catalogos
        INNER JOIN mgcp_acuerdo_marco.acuerdo_marco ON acuerdo_marco.id=catalogos.id_acuerdo_marco
        INNER JOIN mgcp_acuerdo_marco.empresas_acuerdos ON empresas_acuerdos.id_acuerdo_marco=acuerdo_marco.id
        WHERE acuerdo_marco.activo=true AND id_empresa=?
        ORDER BY catalogos.id", [$idEmpresa]);
    }

    public function acuerdoMarco()
    {
        return $this->belongsTo(AcuerdoMarco::class,'id_acuerdo_marco');
    }
}
