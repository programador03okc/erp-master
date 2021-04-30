<?php

namespace App\Http\ViewComposers;

use App\Models\Tesoreria\Area;
use App\Models\Tesoreria\Empresa;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AuthToViewComposer {

	public function compose(View $view) {
		$autenticado = [];
		if (Auth::check()){
			$autIni = Auth::user();
			$area = 'CAMBIAR';//Area::findorFail($autIni->trabajador->roles->first()->pivot->id_area);
			$autenticado = $autIni->toArray();
			// $autenticado['id_rol'] = 'CAMBIAR';//$autIni->trabajador->roles->first()->pivot->id_rol;
			$autenticado['roles'] = Auth::user()->getAllRol();
			// $autenticado['grupos'] = Auth::user()->getAllGrupo();
			$autenticado['id_rol_concepto'] = 'CAMBIAR';//$autIni->trabajador->roles->first()->id_rol_concepto;
			$autenticado['rol'] = 'CAMBIAR';//$autIni->trabajador->roles->first()->descripcion;
			$autenticado['cargo'] = 'CAMBIAR';//$autIni->cargo;
			$autenticado['nombres'] = 'CAMBIAR';//$autIni->trabajador->postulante->persona->nombre_completo;
			$autenticado['id_area'] = 'CAMBIAR';//$autIni->trabajador->roles->first()->pivot->id_area;
			$autenticado['grupos'] = Auth::user()->getAllGrupo();
			$autenticado['area'] = 'CAMBIAR';//$area->descripcion;
		}
		$view->with('auth_user', json_encode($autenticado));
	}
}
