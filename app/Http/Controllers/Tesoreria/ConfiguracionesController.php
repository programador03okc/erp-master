<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfiguracionesController extends Controller
{
    public function index(){
    	return view('tesoreria.configuraciones.index');
	}
}
