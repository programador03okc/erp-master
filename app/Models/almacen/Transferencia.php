<?php

namespace App\Models\Almacen;

use App\Models\almacen\TransferenciaDetalle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    protected $table = 'almacen.trans';
    protected $primaryKey = 'id_transferencia';
    public $timestamps = false;
    protected $appends = ['requerimientos'];

    public function getRequerimientosAttribute()
    {
        $requerimientos = TransferenciaDetalle::join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', 'trans_detalle.id_requerimiento_detalle')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', 'alm_det_req.id_requerimiento')
            ->where('trans_detalle.id_transferencia', $this->attributes['id_transferencia'])
            ->select(['alm_req.codigo'])->distinct()->get();

        $resultado = [];
        foreach ($requerimientos as $req) {
            array_push($resultado, $req->codigo);
        }
        return implode(', ', $resultado);
    }
}
