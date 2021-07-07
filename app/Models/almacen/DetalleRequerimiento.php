<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class DetalleRequerimiento extends Model
{
    protected $table = 'almacen.alm_det_req';
    protected $primaryKey = 'id_detalle_requerimiento';
    public $timestamps = false;

    public function getPartNumberAttribute(){
        return $this->attributes['part_number'] ?? '';
    }
}

