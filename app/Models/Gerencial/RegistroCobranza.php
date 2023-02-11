<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class RegistroCobranza extends Model
{
    //
    protected $table = 'cobranza.registros_cobranzas';
    protected $primaryKey = 'id_registro_cobranza';
    protected $fillable = [
        'id_empresa', 'id_sector', 'id_cliente', 'factura', 'uu_ee', 'fuente_financ', 'ocam', 'siaf', 'fecha_emision', 'fecha_recepcion', 'moneda', 'importe', 'id_estado_doc',
        'id_tipo_tramite', 'vendedor', 'estado', 'fecha_registro', 'id_area', 'id_periodo', 'codigo_empresa', 'categoria', 'cdp', 'oc_fisica', 'plazo_credito', 'id_doc_ven',
        'id_cliente_agil', 'id_cobranza_old', 'id_empresa_old', 'inicio_entrega', 'fecha_entrega', 'id_oc'
    ];
    public $timestamps = false;
}
