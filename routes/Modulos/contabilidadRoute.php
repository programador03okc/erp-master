
<?php

use Illuminate\Http\Request;


// CONTABILIDAD
Route::get('/contabilidad/doc_idendidad', 'ContabilidadController@mostrar_documentos');
Route::get('/contabilidad/doc_idendidad/{id}', 'ContabilidadController@mostrar_doc_idendidad');
Route::post('/contabilidad/doc_idendidad', 'ContabilidadController@guardar_documento');
Route::put('/contabilidad/doc_idendidad/{id}', 'ContabilidadController@actualizar_documento');
Route::delete('/contabilidad/doc_idendidad/{id}', 'ContabilidadController@eliminar_documento');
// Route::get('/contabilidad/tipo-contribuyente', 'ContabilidadController@mostrar_tipo_contribuyentes');
// Route::get('/contabilidad/tipo-contribuyente/{id}', 'ContabilidadController@mostrar_tipo_contribuyente');
// Route::post('/contabilidad/tipo-contribuyente', 'ContabilidadController@guardar_tipo_contribuyente');
// Route::delete('/contabilidad/tipo-contribuyente/{id}', 'ContabilidadController@eliminar_tipo_contribuyente');
// Route::put('/contabilidad/tipo-contribuyente/{id}', 'ContabilidadController@actualizar_tipo_contribuyente');
// Route::get('/contabilidad/contribuyente-data-contribuyente-rubro/{id}', 'ContabilidadController@mostrar_contribuyente_data_contribuyente_rubro');

// ContriModal
Route::get('/contabilidad/contribuyente', 'ContabilidadController@mostrar_contribuyentes');
Route::get('/contabilidad/contribuyente/{id}', 'ContabilidadController@mostrar_contribuyente');
//contabilidad - formulario contribuyente
Route::get('/contabilidad/contribuyente_fill_input', 'ContabilidadController@fill_input_contribuyentes');
Route::get('/contabilidad/contribuyente-data-contribuyente/{id}', 'ContabilidadController@mostrar_contribuyente_data_contribuyente');
Route::post('/contabilidad/contribuyente', 'ContabilidadController@guardar_contribuyente');
Route::put('/contabilidad/contribuyente/{id}', 'ContabilidadController@actualizar_contribuyente');
Route::delete('/contabilidad/contribuyente/{id}', 'ContabilidadController@eliminar_contribuyente');
//contabilidad - formulario contacto
Route::get('/contabilidad/empresas_fill_input', 'ContabilidadController@fill_input_empresas');
Route::get('/contabilidad/contribuyente-data-contribuyente-contacto/{id}', 'ContabilidadController@mostrar_contribuyente_contactos_contribuyente');
Route::post('/contabilidad/contribuyente-contacto', 'ContabilidadController@guardar_contribuyente_contacto');
Route::put('/contabilidad/contribuyente-contacto/{id}', 'ContabilidadController@actualizar_contribuyente_contacto');
Route::delete('/contabilidad/contribuyente-contacto/{id}', 'ContabilidadController@eliminar_contribuyente_contacto');
//contabilidad - formulario cuenta
Route::get('/contabilidad/cuenta_fill_input', 'ContabilidadController@fill_input_cuenta');
Route::get('/contabilidad/contribuyente-data-contribuyente-cuenta/{id}', 'ContabilidadController@mostrar_contribuyente_data_contribuyente_cuenta');
Route::post('/contabilidad/cuenta-contribuyente', 'ContabilidadController@guardar_cuenta_contribuyente');
Route::put('/contabilidad/cuenta-contribuyente/{id}', 'ContabilidadController@actualizar_cuenta_contribuyente');
Route::delete('/contabilidad/cuenta-contribuyente/{id}', 'ContabilidadController@eliminar_cuenta_contribuyente');



//contabilidad - tipo cuenta
Route::get('/contabilidad/tipo-cuenta', 'ContabilidadController@mostrar_tipo_cuentas');
Route::get('/contabilidad/tipo-cuenta/{id}', 'ContabilidadController@mostrar_tipo_cuenta');
Route::post('/contabilidad/tipo-cuenta', 'ContabilidadController@guardar_tipo_cuenta');
Route::put('/contabilidad/tipo-cuenta/{id}', 'ContabilidadController@actualizar_tipo_cuenta');
Route::delete('/contabilidad/tipo-cuenta/{id}', 'ContabilidadController@eliminar_tipo_cuenta');

//  Route::get('/contabilidad/cuenta-contribuyente/{id}', 'ContabilidadController@mostrar_cuenta_contribuyente');
// Route::get('/contabilidad/contribuyente-contacto', 'ContabilidadController@mostrar_contribuyente_contactos');
// Route::get('/contabilidad/contribuyente-contacto/{id}', 'ContabilidadController@mostrar_contribuyente_contacto');

// Route::get('/contabilidad/cuenta-contribuyente', 'ContabilidadController@mostrar_cuenta_contribuyentes');

