<?php

namespace App\Models\Gerencial;

use App\Models\Administracion\Empresa;
use App\Models\Comercial\Cliente;
use App\models\Gerencial\AreaResponsable;
use App\models\Gerencial\EstadoDocumento;
use App\models\Gerencial\Sector;
use Illuminate\Database\Eloquent\Model;

class RegistroCobranza extends Model
{
    //
    protected $table = 'cobranza.registros_cobranzas';
    protected $primaryKey = 'id_registro_cobranza';
    protected $fillable = [
        'id_empresa', 'id_sector', 'id_cliente', 'factura', 'uu_ee', 'fuente_financ', 'ocam', 'siaf', 'fecha_emision', 'fecha_recepcion', 'moneda', 'importe', 'id_estado_doc', 
        'id_tipo_tramite', 'vendedor', 'estado', 'fecha_registro', 'id_area', 'id_periodo', 'codigo_empresa', 'categoria', 'cdp', 'oc_fisica', 'plazo_credito', 'id_doc_ven', 
        'id_cliente_agil', 'id_cobranza_old', 'id_empresa_old', 'inicio_entrega', 'fecha_entrega', 'id_oc', 'periodo'
    ];
    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_old', 'id_empresa');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'id_sector', 'id_sector');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_agil', 'id_cliente');
    }

    public function estado_doc()
    {
        return $this->belongsTo(EstadoDocumento::class, 'id_estado_doc', 'id_estado_doc');
    }

    public function area()
    {
        return $this->belongsTo(AreaResponsable::class, 'id_area', 'id_area');
    }
}
