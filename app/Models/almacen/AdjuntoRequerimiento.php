<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Model;

class AdjuntoRequerimiento extends Model
{
    protected $table = 'almacen.alm_req_adjuntos';
    protected $primaryKey = 'id_adjunto';
    public $timestamps = false;


    public function categoriaAdjunto()
    {
        return $this->belongsTo('App\Models\Almacen\CategoriaAdjunto', 'categoria_adjunto_id','id_categoria_adjunto');
    }
}
