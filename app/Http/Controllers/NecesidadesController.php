<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NecesidadesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    function view_main_necesidades()
    {
        return view('necesidades/main');
    }

}
