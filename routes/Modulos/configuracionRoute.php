<?php

use Illuminate\Http\Request;

Route::get('/config/pais', 'ConfiguracionController@mostrar_paises');
Route::get('/config/pais/{id}', 'ConfiguracionController@mostrar_pais');
Route::post('/config/pais', 'ConfiguracionController@guardar_pais');
Route::delete('/config/pais/{id}', 'ConfiguracionController@eliminar_pais');
Route::put('/config/pais/{id}', 'ConfiguracionController@actualizar_pais');

Route::get('/config/moneda', 'ConfiguracionController@mostrar_monedas');
Route::get('/config/moneda/{id}', 'ConfiguracionController@mostrar_moneda');
Route::post('/config/moneda', 'ConfiguracionController@guardar_moneda');
Route::delete('/config/moneda/{id}', 'ConfiguracionController@eliminar_moneda');
Route::put('/config/moneda/{id}', 'ConfiguracionController@actualizar_moneda');

Route::get('/config/usuarios', 'ConfiguracionController@mostrar_usuarios');
Route::get('/config/usuario/{id}', 'ConfiguracionController@mostrar_usuario');
Route::post('/config/usuario', 'ConfiguracionController@guardar_usuario');
Route::delete('/config/usuario/{id}', 'ConfiguracionController@eliminar_usuario');
Route::put('/config/usuario/{id}', 'ConfiguracionController@actualizar_usuario');
Route::get('/config/rol_fill_select', 'ConfiguracionController@mostrar_rol_fill_select');

Route::get('/config/trabajadores', 'ConfiguracionController@mostrar_trabajadores');
Route::get('/config/trabajador/{id}', 'ConfiguracionController@mostrar_trabajador');

// Route::get('/config/singin', 'ConfiguracionController@sample1');
// Route::get('/config/singin/{id}', 'ConfiguracionController@sample2');
Route::get('/config/departamento', 'ConfiguracionController@mostrar_departamentos');
Route::get('/config/provincia/{id}', 'ConfiguracionController@mostrar_provincias');
Route::get('/config/distrito/{id}', 'ConfiguracionController@mostrar_distritos');