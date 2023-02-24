<?php

namespace App\Models\mgcp\AcuerdoMarco;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraPropias extends Model {

    protected $table = 'mgcp_acuerdo_marco.oc_propias';
    protected $primaryKey = 'id';
    protected $fillable = ['orden_am', 'id_empresa', 'id_entidad', 'estado_oc', 'fecha_estado', 'plazo_dias', 'lugar_entrega',
    'monto_total', 'factura', 'guia', 'fecha_guia', 'orden_compra', 'id_etapa', 'id_corporativo', 'cobrado', 'conformidad',
    'siaf', 'codigo_gasto', 'eliminado', 'id_tipo', 'url_oc_fisica', 'fecha_entrega', 'id_oportunidad', 'paquete',
    'id_alternativo', 'estado_entrega', 'fecha_publicacion', 'id_catalogo', 'occ', 'despachada', 'id_contacto',
    'inicio_entrega', 'id_despacho', 'id_ultimo_usuario'];
    public $timestamps = false;


}
