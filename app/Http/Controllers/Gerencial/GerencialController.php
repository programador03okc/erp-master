<?php

namespace App\Http\Controllers\Gerencial;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GerencialController extends Controller
{
    //
    public function index()
    {
        return view('gerencial/main');
    }
}
