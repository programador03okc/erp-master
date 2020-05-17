<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    //
    protected $table = 'almacen.alm_almacen';

    protected $primaryKey = 'id_almacen';

    public $timestamps = false;

   /* protected $fillable = [
        'id_categoria',
        'id_tipo_producto',
        'descripcion',
        'estado',
        'fecha_registro'
    ];*/
    protected $guarded = ['id_almacen'];

    public function sede(){
        return $this->belongsTo('App\Models\Tesoreria\Sede','id_sede','id_sede');
    }
}
