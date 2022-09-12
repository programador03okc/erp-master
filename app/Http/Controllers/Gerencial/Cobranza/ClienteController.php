<?php

namespace App\Http\Controllers\Gerencial\Cobranza;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClienteController extends Controller
{
    //
    public function cliente()
    {
        # code...
        return view('gerencial/cobranza/cliente');
    }
}
