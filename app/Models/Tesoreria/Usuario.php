<?php

namespace App\Models\Tesoreria;

use Illuminate\Contracts\Session\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Usuario extends Authenticatable
{
	use Notifiable;
    //
    protected $table = 'configuracion.sis_usua';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

   protected $fillable = [
        'id_trabajador',
        'usuario',
        'clave',
        'estado',
        'fecha_registro',
        'acceso',
    ];

	protected $hidden = [
		'clave',
	];

   protected $appends = ['login_empresa', 'login_rol', 'pertenece_a_empresa'];

    /*protected $hidden = ['clave'];
    protected $guarded = ['id_usuario'];*/

	public function getAuthPassword(){
		//dd($this->clave);
		return $this->clave;
	}

	public function getLoginEmpresaAttribute(){
		return session('login_empresa');
	}


	public function getLoginEmpresaDataAttribute(){
		$empresa = Empresa::find(session('login_empresa'));
		return $empresa;
	}

	public function getLoginRolAttribute(){
		return session('login_rol');
	}

	public function getConceptoLoginRolAttribute(){
		$rol = Rol::with('rol_concepto')->findOrFail($this->login_rol);
		//dd($rol->rol_concepto->descripcion);
		return $rol->rol_concepto->descripcion;
	}

	public function getCargoAttribute(){
		$rol = Rol::with('cargo')->findOrFail($this->login_rol);
		//dd($rol->rol_concepto->descripcion);
		return $rol->cargo->descripcion;
	}

	public function roles(){
		return $this->trabajador->roles();
	}

	public function getPerteneceAEmpresaAttribute(){
		$roles = $this->trabajador->roles;

		//dump($roles->toArray());

		$empresas = [];
		foreach ($roles as $rol){
			//dd($rol);
			$area = Area::findOrFail($rol->pivot->id_area)->grupo->sede->empresa;
			$empresas[] = $area;
		}
		//$empresa = Area::findOrFail($roles)
		//dd($empresas);
		return collect($empresas);
	}


    public function trabajador(){
        return $this->belongsTo('App\Models\Tesoreria\Trabajador','id_trabajador','id_trabajador');
    }


	public function obtenerRoles() {
        $rolesBD = $this->trabajador->roles()->get();
        foreach ($rolesBD as $rol) {
            $roles[] = $rol->id_rol_concepto;
        }
        return $roles;
	}
	











	// SECCION ROLES PERSONALIZADA

	public function authorizeRoles($roles)
	{
		if ($this->hasAnyRole($roles)) {
			return true;
		}
		abort(401, 'Esta acción no está autorizada.');
	}
	public function hasAnyRole($roles)
	{
		if (is_array($roles)) {
			foreach ($roles as $role) {
				//echo $role .'<br>';
				if ($this->hasRole($role)) {
					//dd('rol');
					return true;
				}
			}
		} else {
			if ($this->hasRole($roles)) {
				return true;
			}
		}
		return false;
	}
	public function hasRole($role)
	{
		//DB::enableQueryLog();
		//dd($this->roles()->where('rrhh.rrhh_rol.id_rol_concepto', $role)->first());

		//($this->trabajador->roles()->get()->toArray());
		//$query = DB::getQueryLog();
		//dd($query);
		if ($this->trabajador->roles()->where('rrhh.rrhh_rol.id_rol_concepto', $role)->first()) {
			//dd('a');
			return true;
		}
		//dd('b');
		return false;
	}
}
