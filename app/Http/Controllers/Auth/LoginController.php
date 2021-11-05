<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Empresa;
use App\Models\Tesoreria\Rol;
use App\Models\Tesoreria\Usuario;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
	/*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	// protected $redirectTo = '/tesoreria';

	public function username()
	{
		return 'usuario';
	}


	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

		$this->middleware('guest')->except('logout');
	}

	protected function validateLogin(Request $request)
	{
		$request->validate([
			$this->username() => 'required|string',
			//'role' => 'required',
			'password' => 'required|string',
		]);
	}

	public function notas_de_lanzamiento()
	{
		$data = DB::table('configuracion.nota_lanzamiento')
			->select('nota_lanzamiento.*', 'detalle_nota_lanzamiento.*')
			->join('configuracion.detalle_nota_lanzamiento', 'detalle_nota_lanzamiento.id_nota_lanzamiento', '=', 'nota_lanzamiento.id_nota_lanzamiento')
			->where([
				['nota_lanzamiento.estado', '=', 1],
				['nota_lanzamiento.version_actual', '=', true]
			])
			->orderBy('detalle_nota_lanzamiento.fecha_detalle_nota_lanzamiento', 'desc')
			->get();
		return $data;
	}


	public function showLoginForm()
	{
		$empresas = Empresa::all();
		$notasLanzamiento = $this->notas_de_lanzamiento();
		return view('login')->with([
			'empresas' => $empresas,
			'notasLanzamiento' => $notasLanzamiento
		]);
	}
	protected function credentials(Request $request)
	{
		$credentials = $request->only($this->username(), 'password');
		return $credentials;
	}


	public function login(Request $request)
	{

		// return ['login'=>$request];
		$this->validateLogin($request);

		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		if ($this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);

			return $this->sendLockoutResponse($request);
		}

		//$usuarioRol = Rol::find($request->get('role'));
		if ($this->attemptLogin($request)) {
			return $this->sendLoginResponse($request);
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);
		return $this->sendFailedLoginResponse($request);
	}


	protected function sendLoginResponse(Request $request)
	{
		$request->session()->regenerate();
		$this->clearLoginAttempts($request);
		// session(['login_empresa' => $request->company]);
		session(['login_rol' => 0]);

		return $this->authenticated($request, $this->guard()->user()) ?:
			response()->json(['success' => true, 'redirectto' => 'modulos']);
	}
}
