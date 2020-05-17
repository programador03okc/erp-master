<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    //
    protected $table = 'administracion.adm_area';

    protected $primaryKey = 'id_area';

    public $timestamps = false;

   /* protected $fillable = [
        'id_categoria',
        'id_tipo_producto',
        'descripcion',
        'estado',
        'fecha_registro'
    ];*/
    protected $guarded = ['id_area'];

	//protected $appends = ['gerente'];


	public function solicitudes(){
        return $this->hasMany('App\Models\Tesoreria\Solicitud','id', 'id_area');
    }

    public function grupo(){
        return $this->belongsTo('App\Models\Tesoreria\Grupo','id_grupo','id_grupo');
    }

    public function getGerenteAttribute(){
		//dump($this->id_area);
		//dd($this->toArray());
    	$trabajador_rol = Rol::where('id_area', $this->id_area)
			->whereIn('id_rol_concepto', [1,2,3,15])->first();

    	$trabajador = Trabajador::findOrFail($trabajador_rol->id_trabajador);

    	return $trabajador;

	}
}
