<?php

use Illuminate\Http\Request;
 


// Route::get('/logistica/requerimientos', 'LogisticaController@mostrar_requerimientos');
Route::get('/logistica/requerimiento_fill_input', 'LogisticaController@requerimiento_fill_input');

Route::get('/logistica/requerimiento/{id}/{codigo}', 'LogisticaController@mostrar_requerimiento')->where('id', '(.*)');
Route::get('/logistica/detalles-requerimiento/{id}', 'LogisticaController@mostrar_detalles_requerimiento');
Route::post('/logistica/requerimiento', 'LogisticaController@guardar_requerimiento');
Route::put('/logistica/requerimiento/{id}', 'LogisticaController@update_requerimiento');
Route::delete('/logistica/requerimiento/{id}', 'LogisticaController@delete_requerimiento');
Route::put('/logistica/requerimiento/anular/{id}', 'LogisticaController@anular_requerimiento');
Route::get('/logistica/requerimiento_detalle/{id}', 'LogisticaController@mostrar_detalle');
Route::get('/logistica/generar_requerimiento_pdf/{id}/{codigo}', 'LogisticaController@generar_requerimiento_pdf')->where('id', '(.*)'); // PDF

Route::get('/logistica/clientes', 'LogisticaController@mostrar_clientes');
Route::get('/logistica/cliente/{id}', 'LogisticaController@mostrar_cliente');

Route::get('/logistica/mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
Route::get('/logistica/mostrar_empresas', 'LogisticaController@mostrar_empresas');


 Route::get('/logistica/mostrar_bien_items', 'LogisticaController@mostrar_bien_items');
Route::get('/logistica/mostrar_producto/{id_producto}', 'LogisticaController@mostrar_producto');
Route::get('/logistica/mostrar_servicios', 'LogisticaController@mostrar_servicios');
Route::get('/logistica/mostrar_servicio/{id}', 'LogisticaController@mostrar_servicio');


Route::get('/logistica/documentos-pendientes/{id_usuario}/{id_rol}', 'LogisticaController@mostrar_documentos_pendientes');
Route::get('/logistica/documentos-aprobados/{id_usuario}/{id_rol}', 'LogisticaController@mostrar_documentos_aprobados');
Route::get('/logistica/documentos-observados/{id_usuario}/{id_rol}', 'LogisticaController@mostrar_documentos_observados');
Route::get('/logistica/documentos-denegados/{id_usuario}/{id_rol}', 'LogisticaController@mostrar_documentos_denegados');

Route::post('/logistica/aprobacion-documento-accion-aprobar', 'LogisticaController@aprobacion_documento_accion_aprobar');
Route::post('/logistica/aprobacion-documento-accion-denegar', 'LogisticaController@aprobacion_documento_accion_denegar');
Route::post('/logistica/aprobacion-documento-accion-observar', 'LogisticaController@aprobacion_documento_accion_observar');

Route::get('/logistica/cotizacion_fill_input', 'LogisticaController@cotizacion_fill_input');
Route::get('/logistica/mostrar_cotizaciones', 'LogisticaController@mostrar_cotizaciones');
Route::get('/logistica/cotizaciones_por_grupo_cotizacion/{id_grupo_cotizacion}', 'LogisticaController@cotizaciones_por_grupo_cotizacion');

Route::get('/logistica/cotizaciones/generadas', 'LogisticaController@cotizaciones_generadas');
Route::get('/cotizacion/mostrar_cotizacion/{id_cotizacion}', 'LogisticaController@mostrar_cotizacion');
Route::get('/cotizacion/mostrar_cotizacion_valorizacion_especificacion/{id_cotizacion}', 'LogisticaController@mostrar_cotizacion_valorizacion_especificacion');
Route::put('/cotizacion/guardar_valorizacion_proveedor/{id_cotizacion}', 'LogisticaController@guardar_valorizacion_proveedor');
// Route::get('/cotizacion/mostrar_relacion_cotizacion_item_proveedor/{num_req}/{id}', 'LogisticaController@mostrar_relacion_cotizacion_item_proveedor');
Route::get('/cotizacion/mostrar_grupo_cotizacion/{codigo_requerimiento}', 'LogisticaController@mostrar_grupo_cotizacion');
Route::get('/cotizacion/especificacion_compra_fill_input/{id_especificacion_compra}', 'LogisticaController@especificacion_compra_fill_input');
Route::put('/cotizacion/remover_cotizacion/{id_cotizacion}', 'LogisticaController@remover_cotizacion');
Route::post('/cotizacion/guardar_cotizacion', 'LogisticaController@guardar_cotizacion');
Route::delete('/cotizacion/eliminar_cotizacion/{id_grupo_cotiza}', 'LogisticaController@eliminar_cotizacion');
Route::put('/cotizacion/actualizar_cotizacion/{id_grupo_cotizacion}', 'LogisticaController@actualizar_cotizacion');
Route::post('/cotizacion/guardar_valorizacion_item', 'LogisticaController@guardar_valorizacion_item');
Route::put('/cotizacion/actualizar_valorizacion_item', 'LogisticaController@actualizar_valorizacion_item');
Route::put('/cotizacion/actualizar_empresa_cotizacion', 'LogisticaController@actualizar_empresa_cotizacion');
Route::put('/cotizacion/actualizar_especificacion_compra', 'LogisticaController@actualizar_especificacion_compra');
Route::get('/cotizacion/mostrar_archivos_adjuntos/{id_cotizacion}', 'LogisticaController@mostrar_archivos_adjuntos');

 Route::get('/logistica/item_cotizacion/{codigo_cotizacion}', 'LogisticaController@item_cotizacion');
// Route::get('/logistica/mostrar_cotizacion_cuadro_comparativo/{id_cotizacion}', 'LogisticaController@mostrar_cotizacion_cuadro_comparativo')->where('id', '(.*)');
Route::put('/logistica/guardar_buena_pro', 'LogisticaController@guardar_buena_pro');
Route::get('/logistica/reportepdf', 'LogisticaController@reportepdf');
Route::get('/logistica/mostrar_cuadro_comparativo/{codigo}', 'LogisticaController@mostrar_cuadro_comparativo');
Route::get('/logistica/generar_cotizacion/{id_cotizacion}', 'LogisticaController@generar_cotizacion_pdf'); // PDF

Route::get('/logistica/mostrar_orden_por_cotizacion/{id_cotizacion}', 'LogisticaController@mostrar_orden_por_cotizacion');
Route::get('/logistica/imprimir_orden_pdf/{id_cotizacion}', 'LogisticaController@imprimir_orden_pdf'); // PDF
Route::get('/logistica/generar_orden_pdf/{id_cotizacion}', 'LogisticaController@generar_orden_pdf'); // PDF

Route::get('/cuadro_comparativo/grupo_cotizaciones/{codigo_cotizacion}/{codigo_requerimiento}', 'LogisticaController@grupo_cotizaciones')->where('id', '(.*)');  


Route::get('/logistica/listar_partidas/{id_grupo}', 'LogisticaController@listar_partidas');  


Route::get('/logistica/solicitud_cotizacion_excel/{id_cotizacion}', 'LogisticaController@solicitud_cotizacion_excel');  
Route::get('/logistica/cuadro_comparativo_excel/{id}', 'LogisticaController@cuadro_comparativo_excel');  




// Route::get('/logistica/sendEmailReminder', 'LogisticaController@sendEmailReminder'); 
