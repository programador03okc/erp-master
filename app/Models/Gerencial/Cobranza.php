<?php

namespace App\models\Gerencial;

use App\models\Gerencial\Empresa;
use Illuminate\Database\Eloquent\Model;

class Cobranza extends Model
{
    //
    protected $table = 'gerencial.cobranza';
    protected $primaryKey = 'id_cobranza';
    protected $fillable = [
        'id_empresa', 'id_sector', 'id_cliente', 'factura', 'uu_ee', 'fuente_financ', 'oc', 'siaf', 'fecha_emision', 'fecha_recepcion', 'moneda',
        'importe', 'id_estado_doc', 'id_tipo_tramite', 'vendedor', 'estado', 'fecha_registro', 'id_area', 'id_periodo', 'ocam', 'codigo_empresa',
        'categoria', 'cdp', 'plazo_credito', 'id_venta'
    ];
    public $timestamps = false;

    public function cobranzaFase()
    {
        return $this->hasMany(CobanzaFase::class, 'id_cobranza', 'id_cobranza');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
    public function areaResponsable()
    {
        return $this->belongsTo(AreaResponsable::class, 'id_area', 'id_area');
    }
    public function estadoDocumento()
    {
        return $this->belongsTo(EstadoDocumento::class, 'id_estado_doc', 'id_estado_doc');
    }
}
