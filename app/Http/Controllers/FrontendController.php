<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\AlertEvent;

class FrontendController extends Controller
{
    public function notification(Request $request){
        $params = [
            'title'     => $request->get('title'),
            'message'   => $request->get('message'),
            'id_area'  => $request->get('id_area'),
            'id_rol'  => $request->get('id_rol')
        ];

        event(new AlertEvent($params, 'notification'));
    }
}
