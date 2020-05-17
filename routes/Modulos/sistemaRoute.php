<?php

use Illuminate\Http\Request;


Route::get('/sistema/mostrar_archivos_adjuntos/{id_detalle_requerimiento}', 'SistemaController@mostrar_archivos_adjuntos');
Route::post('/sistema/uploadfile', 'SistemaController@uploadfile');
Route::get('/sistema/downloadfile/{ruta}/{archivo}', 'SistemaController@downloadfile');
Route::put('/sistema/actualizar_status_file', 'SistemaController@actualizar_status_file');