<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('api/logistica/gestion-logistica/requerimiento/elaboracion/guardar', 'LogisticaController@guardar_requerimiento');
Route::post('api/tesoreria/guardar-tipo-cambio', 'Tesoreria\TipoCambioController@store');
Route::get('api/getTipoCambio', 'Tesoreria\TipoCambioController@getTipoCambio');
Route::get('get-cambio', 'ApiController@get');
Route::get('movimiento', 'SoftlinkController@movimiento');

Route::get('pruebas', function(Request $request){
    return 'acceso';
});













