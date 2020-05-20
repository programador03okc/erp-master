<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    //
    protected $table = 'administracion.adm_grupo';

    protected $primaryKey = 'id_grupo';

    public $timestamps = false;

   /* protected $fillable = [
        'id_categoria',
        'id_tipo_producto',
        'descripcion',
        'estado',
        'fecha_registro'
    ];*/
    protected $guarded = ['id_grupo'];


    public function areas(){
        return $this->hasMany('App\Models\Tesoreria\Area','id_grupo', 'id_grupo');
    }

    public function presupuestos(){
        return $this->hasMany(Presupuesto::class,'id_grupo', 'id_grupo');
    }

    public function sede(){
        return $this->belongsTo('App\Models\Tesoreria\Sede','id_sede','id_sede');
    }


}
