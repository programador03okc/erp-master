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
			$area = Area::findorFail($autIni->trabajador->roles->first()->pivot->id_area);
			$autenticado = $autIni->toArray();
			$autenticado['id_rol'] = $autIni->trabajador->roles->first()->pivot->id_rol;
			$autenticado['id_rol_concepto'] = $autIni->trabajador->roles->first()->id_rol_concepto;
			$autenticado['rol'] = $autIni->trabajador->roles->first()->descripcion;
			$autenticado['cargo'] = $autIni->cargo;
			$autenticado['nombres'] = $autIni->trabajador->postulante->persona->nombre_completo;
			$autenticado['id_area'] = $autIni->trabajador->roles->first()->pivot->id_area;
			$autenticado['id_grupo'] = $area->id_grupo;
			$autenticado['area'] = $area->descripcion;
		}
		$view->with('auth_user', json_encode($autenticado));
	}
}
