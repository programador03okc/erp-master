<?php

use Illuminate\Http\Request;

// ADMINISTRACIÓN
Route::get('/administracion/empresa', 'AdministracionController@mostrar_empresas');
Route::get('/administracion/empresa/{id}', 'AdministracionController@mostrar_empresa');
Route::post('/administracion/empresa', 'AdministracionController@guardar_empresa');
Route::delete('/administracion/empresa/{id}', 'AdministracionController@eliminar_empresa');
Route::put('/administracion/empresa/{id}', 'AdministracionController@actualizar_empresa');
Route::get('/administracion/area', 'AdministracionController@mostrar_areas');
Route::get('/administracion/area/{id}', 'AdministracionController@mostrar_area');
Route::post('/administracion/area', 'AdministracionController@guardar_area');
Route::delete('/administracion/area/{id}', 'AdministracionController@eliminar_area');
Route::put('/administracion/area/{id}', 'AdministracionController@actualizar_area');
Route::get('/administracion/sede', 'AdministracionController@mostrar_sedes');
Route::get('/administracion/sede/{id}', 'AdministracionController@mostrar_sede');
Route::post('/administracion/sede', 'AdministracionController@guardar_sede');
Route::delete('/administracion/sede/{id}', 'AdministracionController@eliminar_sede');
Route::put('/administracion/sede/{id}', 'AdministracionController@actualizar_sede');

 Route::get('/administracion/grupos', 'AdministracionController@mostrar_grupos');
 Route::get('/administracion/grupo/{id}', 'AdministracionController@mostrar_grupo');
 Route::post('/administracion/grupo', 'AdministracionController@guardar_grupo');
 Route::put('/administracion/grupo/{id}', 'AdministracionController@actualizar_grupo');
 Route::delete('/administracion/grupo/{id}', 'AdministracionController@eliminar_grupo');

 Route::get('/administracion/tipo-documentos', 'AdministracionController@mostrar_tipo_documentos');
 Route::get('/administracion/tipo-documento/{id}', 'AdministracionController@mostrar_tipo_documento');
 Route::post('/administracion/tipo-documento', 'AdministracionController@guardar_tipo_documento');
 Route::put('/administracion/tipo-documento/{id}', 'AdministracionController@actualizar_tipo_documento');
 Route::delete('/administracion/tipo-documento/{id}', 'AdministracionController@eliminar_tipo_documento');

 Route::get('/administracion/vobos', 'AdministracionController@mostrar_vobos');
 Route::get('/administracion/vobo/{id}', 'AdministracionController@mostrar_vobo');
 Route::post('/administracion/vobo', 'AdministracionController@guardar_vobo');
 Route::put('/administracion/vobo/{id}', 'AdministracionController@actualizar_vobo');
 Route::delete('/administracion/vobo/{id}', 'AdministracionController@eliminar_vobo');

 Route::get('/administracion/operacions', 'AdministracionController@mostrar_operacions');
 Route::get('/administracion/operacion/{id}', 'AdministracionController@mostrar_operacion');
 Route::post('/administracion/operacion', 'AdministracionController@guardar_operacion');
 Route::put('/administracion/operacion/{id}', 'AdministracionController@actualizar_operacion');
 Route::delete('/administracion/operacion/{id}', 'AdministracionController@eliminar_operacion');
 Route::get('/administracion/operacion_fill_input', 'AdministracionController@mostrar_operacion_fill_input');
 Route::get('/administracion/operacion_fill_select', 'AdministracionController@mostrar_operacion_fill_select');

 Route::get('/administracion/flujos/{id}', 'AdministracionController@mostrar_flujos');
 Route::get('/administracion/flujo/{id}', 'AdministracionController@mostrar_flujo');
 Route::get('/administracion/rol_fill_select', 'AdministracionController@mostrar_rol_fill_select');
 Route::post('/administracion/flujo', 'AdministracionController@guardar_flujo');
 Route::put('/administracion/flujo/{id}', 'AdministracionController@actualizar_flujo');
 Route::delete('/administracion/flujo/{id}', 'AdministracionController@eliminar_flujo');
