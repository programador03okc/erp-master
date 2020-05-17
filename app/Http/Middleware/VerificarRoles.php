<?php

namespace App\Http\Middleware;

use Closure;

class VerificarRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
    	$error = true;
    	foreach ($roles as $role){
			if ($request->user()->hasRole($role)) {
				//return redirect('tesoreria');
				$error = false;
				break;
			}
		}
    	if($error){
			abort(403, "No tienes autorización para ingresar.");
		}


        return $next($request);
    }
}
