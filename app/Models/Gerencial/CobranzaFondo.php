<?php

namespace App\Models\Gerencial;

use App\Models\Comercial\Cliente;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CobranzaFondo extends Model
{
    use SoftDeletes;
    
    protected $table = 'cobranza.registros_cobranza_fondos';
    protected $fillable = [
        'tipo_gestion_id', 'tipo_negocio_id', 'fecha_solicitud', 'cliente_id', 'moneda_id', 'importe', 'fecha_inicio', 'fecha_vencimiento', 'periodo_id',
        'forma_pago_id', 'responsable_id', 'detalles', 'pagador', 'claim', 'estado', 'nro_documento', 'observaciones', 'fecha_cobranza', 'usuario_id'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function tipo_gestion()
    {
        return $this->belongsTo(TipoGestion::class);
    }

    public function tipo_negocio()
    {
        return $this->belongsTo(TipoNegocio::class);
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'moneda_id', 'id_moneda');
    }

    public function forma_pago()
    {
        return $this->belongsTo(FormaPago::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'responsable_id', 'id_usuario');
    }
}
