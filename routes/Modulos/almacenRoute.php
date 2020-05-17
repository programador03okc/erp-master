<?php

use Illuminate\Http\Request;

Route::get('/almacen/productos', 'AlmacenController@mostrar_productos');
Route::get('/almacen/producto/{id}', 'AlmacenController@mostrar_producto');
Route::post('/almacen/producto', 'AlmacenController@guardar_producto');
Route::put('/almacen/producto/{id}', 'AlmacenController@update_producto');
Route::delete('/almacen/producto/{id}', 'AlmacenController@delete_producto');
Route::put('/almacen/producto/anular/{id}', 'AlmacenController@anular_producto');
Route::get('/almacen/next_correlativo_prod/{id}', 'AlmacenController@next_correlativo_prod');

Route::get('/almacen/unid_medida', 'AlmacenController@mostrar_unidades');
Route::get('/almacen/clasificacion', 'AlmacenController@mostrar_clasificaciones');
Route::get('/almacen/sedes', 'AlmacenController@mostrar_sedes');

Route::get('/almacen/tp_productos', 'AlmacenController@mostrar_tp_productos');
Route::get('/almacen/tp_producto/{id}', 'AlmacenController@mostrar_tp_producto');
Route::post('/almacen/tp_producto', 'AlmacenController@guardar_tp_producto');
Route::put('/almacen/tp_producto/{id}', 'AlmacenController@update_tp_producto');
Route::put('/almacen/tp_producto_anular/{id}', 'AlmacenController@anular_tp_producto');

Route::get('/almacen/categorias', 'AlmacenController@mostrar_categorias');
Route::get('/almacen/categoria/{id}', 'AlmacenController@mostrar_categoria');
Route::post('/almacen/categoria', 'AlmacenController@guardar_categoria');
Route::put('/almacen/categoria/{id}', 'AlmacenController@update_categoria');
Route::put('/almacen/categoria_anular/{id}', 'AlmacenController@anular_categoria');

Route::get('/almacen/subcategorias', 'AlmacenController@mostrar_sub_categorias');
Route::get('/almacen/subcategoria/{id}', 'AlmacenController@mostrar_sub_categoria');
Route::post('/almacen/subcategoria', 'AlmacenController@guardar_sub_categoria');
Route::put('/almacen/subcategoria/{id}', 'AlmacenController@update_sub_categoria');
Route::put('/almacen/subcategoria_anular/{id}', 'AlmacenController@anular_sub_categoria');

Route::get('/almacen/tp_servicios/', 'AlmacenController@mostrar_tp_servicios');
Route::get('/almacen/tp_servicio/{id}', 'AlmacenController@mostrar_tp_servicio');
Route::post('/almacen/tp_servicio', 'AlmacenController@guardar_tp_servicio');
Route::put('/almacen/tp_servicio/{id}', 'AlmacenController@update_tp_servicio');
Route::put('/almacen/tp_servicio_anular/{id}', 'AlmacenController@update_tp_servicio_anular');

Route::get('/almacen/cat_servicios/', 'AlmacenController@mostrar_cat_servicios');
Route::get('/almacen/cat_servicio/{id}', 'AlmacenController@mostrar_cat_servicio');
Route::post('/almacen/cat_servicio', 'AlmacenController@guardar_cat_servicio');
Route::put('/almacen/cat_servicio/{id}', 'AlmacenController@update_cat_servicio');
Route::put('/almacen/cat_servicio_anular/{id}', 'AlmacenController@update_cat_servicio_anular');

Route::get('/almacen/servicios/', 'AlmacenController@mostrar_servicios');
Route::get('/almacen/servicio/{id}', 'AlmacenController@mostrar_servicio');
Route::post('/almacen/servicio', 'AlmacenController@guardar_servicio');
Route::put('/almacen/servicio/{id}', 'AlmacenController@update_servicio');
Route::put('/almacen/servicio_anular/{id}', 'AlmacenController@update_servicio_anular');
Route::get('/almacen/next_correlativo_ser/{id}', 'AlmacenController@next_correlativo_ser');

Route::get('/almacen/almacenes/', 'AlmacenController@mostrar_almacenes');
Route::get('/almacen/almacen/{id}', 'AlmacenController@mostrar_almacen');
Route::post('/almacen/almacen', 'AlmacenController@guardar_almacen');
Route::put('/almacen/almacen/{id}', 'AlmacenController@update_almacen');
Route::put('/almacen/almacen_anular/{id}', 'AlmacenController@anular_almacen');
