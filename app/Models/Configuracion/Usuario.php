<?php

namespace App\Models\Configuracion;

use App\Models\Administracion\Empresa;
use App\Models\Tesoreria\Area;
use App\Models\Tesoreria\Rol;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Usuario extends Authenticatable
{
	use Notifiable, SoftDeletes;
	//
	protected $table = 'configuracion.sis_usua';
	protected $primaryKey = 'id_usuario';

	public $timestamps = false;

	protected $hidden = [
		'clave',
	];

	protected $appends = ['login_empresa', 'login_rol', 'pertenece_a_empresa'];

	public function getAuthPassword()
	{
		return $this->clave;
	}

	public function trabajador()
	{
		return $this->belongsTo('App\Models\rrhh\Trabajador', 'id_trabajador')->withDefault();
	}

	public function tieneAccion($id)
	{

		return Acceso::where([['id_usuario', $this->id_usuario],['id_accion', $id],['sis_acceso.estado', 1]])->first() != null;

		// return Acceso::join('configuracion.sis_rol', 'sis_acceso.id_rol', '=', 'sis_rol.id_rol')
		// 	->join('configuracion.sis_accion_rol', 'sis_accion_rol.id_rol', '=', 'sis_rol.id_rol')
		// 	->where('id_usuario', $this->id_usuario)->where('sis_accion_rol.id_accion', $id)->first() != null;
	}

	public function tieneAplicacion($id)
	{
		return Acceso::join('configuracion.sis_accion', 'sis_accion.id_accion', '=', 'sis_acceso.id_accion')
			->where([['sis_acceso.id_usuario', $this->id_usuario],['sis_acceso.estado', 1]])->where('sis_accion.id_aplicacion', $id)->first() != null;

		// return Acceso::join('configuracion.sis_rol', 'sis_acceso.id_rol', '=', 'sis_rol.id_rol')
		// 	->join('configuracion.sis_accion_rol', 'sis_accion_rol.id_rol', '=', 'sis_rol.id_rol')
		// 	->join('configuracion.sis_accion', 'sis_accion.id_accion', '=', 'sis_accion_rol.id_accion')
		// 	->where('id_usuario', $this->id_usuario)->where('sis_accion.id_aplicacion', $id)->first() != null;
	}

	public function tieneSubModulo($id)
	{
		return Acceso::join('configuracion.sis_accion', 'sis_accion.id_accion', '=', 'sis_acceso.id_accion')
			->join('configuracion.sis_aplicacion', 'sis_aplicacion.id_aplicacion', '=', 'sis_accion.id_aplicacion')
			->where([['sis_acceso.id_usuario', $this->id_usuario],['sis_acceso.estado', 1]])->where('sis_aplicacion.id_sub_modulo', $id)->first() != null;


		// return Acceso::join('configuracion.sis_rol', 'sis_acceso.id_rol', '=', 'sis_rol.id_rol')
		// ->join('configuracion.sis_accion_rol', 'sis_accion_rol.id_rol', '=', 'sis_rol.id_rol')
		// ->join('configuracion.sis_accion', 'sis_accion.id_accion', '=', 'sis_accion_rol.id_accion')
		// ->join('configuracion.sis_aplicacion', 'sis_aplicacion.id_aplicacion', '=', 'sis_accion.id_aplicacion')
		// ->where('id_usuario', $this->id_usuario)->where('sis_aplicacion.id_sub_modulo', $id)->first() != null;
	}

	public function tieneSubModuloPadre($id)
	{
		return Acceso::join('configuracion.sis_accion', 'sis_accion.id_accion', '=', 'sis_acceso.id_accion')
			->join('configuracion.sis_aplicacion', 'sis_aplicacion.id_aplicacion', '=', 'sis_accion.id_aplicacion')
			->join('configuracion.sis_modulo', 'sis_modulo.id_modulo', '=', 'sis_aplicacion.id_sub_modulo')
			->where([['sis_acceso.id_usuario', $this->id_usuario],['sis_acceso.estado', 1]])->where('sis_modulo.id_padre', $id)->first() != null;

		// return Acceso::join('configuracion.sis_rol', 'sis_acceso.id_rol', '=', 'sis_rol.id_rol')
		// ->join('configuracion.sis_accion_rol', 'sis_accion_rol.id_rol', '=', 'sis_rol.id_rol')
		// ->join('configuracion.sis_accion', 'sis_accion.id_accion', '=', 'sis_accion_rol.id_accion')
		// ->join('configuracion.sis_aplicacion', 'sis_aplicacion.id_aplicacion', '=', 'sis_accion.id_aplicacion')
		// ->join('configuracion.sis_modulo', 'sis_modulo.id_modulo', '=', 'sis_aplicacion.id_sub_modulo')
		// ->where('id_usuario', $this->id_usuario)->where('sis_modulo.id_padre', $id)->first() != null;
	}

	public function sedesAcceso()
	{
		$sedes = DB::table('configuracion.sis_usua_sede')
			->select('sis_sede.*')
			->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'sis_usua_sede.id_sede')
			->where('sis_usua_sede.id_usuario', $this->id_usuario)
			->get();
		return $sedes;
	}

	public function getRolesText()
	{
		$roles = DB::table('configuracion.usuario_rol')
			->select('sis_rol.*')
			->join('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'usuario_rol.id_rol')
			->where([['usuario_rol.id_usuario', $this->id_usuario],['usuario_rol.estado',1]])
			->get();
		$texto = '';
		foreach ($roles as $s) {
			if ($texto == '') {
				$texto .= $s->descripcion;
			} else {
				$texto .= ', ' . $s->descripcion;
			}
		}
		return $texto;

		// $roles = DB::table('configuracion.sis_acceso')
		// 	->select('sis_rol.*')
		// 	->join('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'sis_acceso.id_rol')
		// 	->where('sis_acceso.id_usuario', $this->id_usuario)
		// 	->get();
		// $texto = '';
		// foreach ($roles as $s) {
		// 	if ($texto == '') {
		// 		$texto .= $s->descripcion;
		// 	} else {
		// 		$texto .= ', ' . $s->descripcion;
		// 	}
		// }
		// return $texto;
	}

	public function getGrupo()
	{
		$grupo = DB::table('configuracion.usuario_grupo')
			->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'usuario_grupo.id_grupo')
			->where('usuario_grupo.id_usuario', $this->id_usuario)
			->select('sis_grupo.*')->first();
		return $grupo;
		// $grupo = Acceso::join('configuracion.sis_rol', 'sis_acceso.id_rol', '=', 'sis_rol.id_rol')
		// 	->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'sis_rol.id_grupo')
		// 	->where('id_usuario', $this->id_usuario)
		// 	->select('sis_grupo.*')->first();
		// return $grupo;
	}
	public function getAllGrupo()
	{
		$grupos = DB::table('configuracion.usuario_grupo')
			->join('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'usuario_grupo.id_grupo')
			->where('usuario_grupo.id_usuario', $this->id_usuario)
			->select('sis_grupo.*')
			->distinct('id_grupo')->get();
		// $grupo = Acceso::join('configuracion.sis_rol', 'sis_acceso.id_rol', '=', 'sis_rol.id_rol')
		// 	->leftJoin('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'sis_rol.id_grupo')
		// 	->where('id_usuario', $this->id_usuario)
		// 	->select('sis_grupo.*')
		// 	->distinct('id_grupo')->get();
		return $grupos;
	}

	public function getAllRol()
	{
		$roles = DB::table('configuracion.usuario_rol')
			->select('usuario_rol.*', 'sis_rol.descripcion')
			->join('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'usuario_rol.id_rol')
			->where('usuario_rol.id_usuario', $this->id_usuario)
			->where('usuario_rol.estado',1)
			->get();
		// $roles = DB::table('configuracion.sis_acceso')
		// 	->select('sis_acceso.*', 'sis_rol.descripcion')
		// 	->join('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'sis_acceso.id_rol')
		// 	->where('sis_acceso.id_usuario', $this->id_usuario)
		// 	->get();
		return $roles;
	}

	public function getAllRolUser($id)
	{
		$roles = DB::table('configuracion.usuario_rol')
			->select('usuario_rol.*', 'sis_rol.descripcion')
			->join('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'usuario_rol.id_rol')
			->where('usuario_rol.id_usuario', $id)
			->get();
		// $roles = DB::table('configuracion.sis_acceso')
		// 	->select('sis_acceso.*', 'sis_rol.descripcion')
		// 	->join('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'sis_acceso.id_rol')
		// 	->where('sis_acceso.id_usuario', $id)
		// 	->get();
		return $roles;
	}
	static public function getAllIdUsuariosPorRol($idRol)
	{
		$idUsuarioList = [];
		$accesos = DB::table('configuracion.usuario_rol')
			->select('usuario_rol.*')
			->where([['usuario_rol.id_rol', $idRol], ['estado', 1]])
			->get();

		foreach ($accesos as $value) {
			$idUsuarioList[] = $value->id_usuario;
		}
		return $idUsuarioList;

		// $idUsuarioList = [];
		// $accesos = DB::table('configuracion.sis_acceso')
		// 	->select('sis_acceso.*')
		// 	->where([['sis_acceso.id_rol', $idRol], ['estado', 1]])
		// 	->get();

		// foreach ($accesos as $value) {
		// 	$idUsuarioList[] = $value->id_usuario;
		// }
		// return $idUsuarioList;
	}














	public function getLoginEmpresaAttribute()
	{
		return session('login_empresa');
	}


	public function getLoginEmpresaDataAttribute()
	{
		$empresa = Empresa::find(session('login_empresa'));
		return $empresa;
	}

	public function getLoginRolAttribute()
	{
		return session('login_rol');
	}

	public function getConceptoLoginRolAttribute()
	{
		$rol = Rol::with('rol_concepto')->findOrFail($this->login_rol);
		//dd($rol->rol_concepto->descripcion);
		return $rol->rol_concepto->descripcion;
	}

	public function getCargoAttribute()
	{
		$rol = Rol::find(2); //Rol::with('cargo')->findOrFail($this->login_rol);
		//dd($rol->rol_concepto->descripcion);
		return $rol->cargo->descripcion;
	}

	public function roles()
	{
		return $this->trabajador->roles();
	}

	public function getPerteneceAEmpresaAttribute()
	{
		//$roles = $this->trabajador->roles;

		//dump($roles->toArray());

		$empresas = [];
		/*foreach ($roles as $rol){
			//dd($rol);
			$area = Area::findOrFail($rol->pivot->id_area)->grupo->sede->empresa;
			$empresas[] = $area;
		}*/
		//$empresa = Area::findOrFail($roles)
		//dd($empresas);
		return collect($empresas);
	}

	/*public static function tieneRol($id)
    {
        return true;
    }*/






	public function obtenerRoles()
	{
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
