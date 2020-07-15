<?php

use Illuminate\Http\Request;

//  Route::get('/{path?}', function () {
//      return view('index');
//  })->where('path', '.*');


// Route::get('/', function () {
//     return view('index');
// })->where('path', '.*');

/* Vista */

Route::get('artisan', function () {
    Artisan::call('clear-compiled');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
});

Route::get('/', function () {
	return redirect()->route('modulos');
});

Route::get('config', function () {
	return view('configuracion/main');
});
Route::get('rrhh', function () {
	return view('rrhh/main');
});
Route::get('almacen', function () {
	return view('almacen/main');
});
Route::get('equipo', function () {
	return view('equipo/main');
});
Route::get('proyectos', function () {
	return view('proyectos/main');
});
Route::get('contabilidad', function () {
	return view('contabilidad/main');
});
// Route::get('logistica', function () {
// 	return view('logistica/main');
// });
Route::get('admin', function (){
    return view('administracion/main');
});


//Route::get('/', 'LoginController@index');
Route::get('modulos', 'LoginController@index')->name('modulos');
//Route::post('iniciar_sesion', 'LoginController@iniciar_sesion');
Route::get('cargar_usuarios/{user}', 'LoginController@mostrar_roles');
//Route::get('logout', 'LoginController@cerrar_sesion');
Route::get('mostrar-version-actual', 'ConfiguracionController@mostrarVersionActual')->name('mostrar-version-actual');
Route::get('socket_setting/{option}', 'ConfiguracionController@socket_setting');

Auth::routes();

Route::group(['middleware' => ['auth']], function () {

	Route::group(['as' => 'proyectos.', 'prefix' => 'proyectos'], function(){
		// Proyetos
		Route::get('getProyectosActivos', 'ProyectosController@getProyectosActivos');

		Route::get('index', function () {
			return view('proyectos/main');
		})->name('index');

		Route::group(['as' => 'variables-entorno.', 'prefix' => 'variables-entorno'], function(){
			
			Route::group(['as' => 'tipos-insumo.', 'prefix' => 'tipos-insumo'], function(){
				// Tipo de Insumos
				Route::get('index', 'ProyectosController@view_tipo_insumo')->name('index');
				Route::get('listar_tipo_insumos', 'ProyectosController@mostrar_tipos_insumos');
				Route::get('tipo_insumo', 'ProyectosController@view_tipo_insumo');
				Route::get('mostrar_tipo_insumo/{id}', 'ProyectosController@mostrar_tp_insumo');
				Route::post('guardar_tipo_insumo', 'ProyectosController@guardar_tp_insumo');
				Route::post('actualizar_tipo_insumo', 'ProyectosController@update_tp_insumo');
				Route::get('anular_tipo_insumo/{id}', 'ProyectosController@anular_tp_insumo');
				Route::get('revisar_tipo_insumo/{id}', 'ProyectosController@buscar_tp_insumo');

			});

			Route::group(['as' => 'sistemas-contrato.', 'prefix' => 'sistemas-contrato'], function(){
				// Sistema de Contrato
				Route::get('index', 'ProyectosController@view_sis_contrato')->name('index');
				Route::get('listar', 'ProyectosController@mostrar_sis_contratos')->name('listar');
				Route::get('mostrar/{id?}', 'ProyectosController@mostrar_sis_contrato')->name('mostrar');
				Route::post('guardar', 'ProyectosController@guardar_sis_contrato')->name('guardar');
				Route::post('actualizar', 'ProyectosController@update_sis_contrato')->name('actualizar');
				Route::get('anular/{id}', 'ProyectosController@anular_sis_contrato')->name('anular');
				
			});

			Route::group(['as' => 'iu.', 'prefix' => 'iu'], function(){
				// Indices Unificados
				Route::get('index', 'ProyectosController@view_iu')->name('index');
				Route::get('listar_ius', 'ProyectosController@mostrar_ius');
				Route::get('mostrar_iu/{id}', 'ProyectosController@mostrar_iu');
				Route::post('guardar_iu', 'ProyectosController@guardar_iu');
				Route::post('actualizar_iu', 'ProyectosController@update_iu');
				Route::get('anular_iu/{id}', 'ProyectosController@anular_iu');
				Route::get('revisar_iu/{id}', 'ProyectosController@buscar_iu');

			});

			Route::group(['as' => 'categorias-insumo.', 'prefix' => 'categorias-insumo'], function(){
				// Categoría de Insumos
				Route::get('index', 'ProyectosController@view_cat_insumo')->name('index');
				Route::get('listar_cat_insumos', 'ProyectosController@listar_cat_insumos');
				Route::get('mostrar_cat_insumo/{id}', 'ProyectosController@mostrar_cat_insumo');
				Route::post('guardar_cat_insumo', 'ProyectosController@guardar_cat_insumo');
				Route::post('update_cat_insumo', 'ProyectosController@update_cat_insumo');
				Route::get('anular_cat_insumo/{id}', 'ProyectosController@anular_cat_insumo');

			});

			Route::group(['as' => 'categorias-acu.', 'prefix' => 'categorias-acu'], function(){
				// Categoría de A.C.U
				Route::get('index', 'ProyectosController@view_cat_acu')->name('index');
				Route::get('listar_cat_acus', 'ProyectosController@listar_cat_acus');
				Route::get('mostrar_cat_acu/{id}', 'ProyectosController@mostrar_cat_acu');
				Route::post('guardar_cat_acu', 'ProyectosController@guardar_cat_acu');
				Route::post('update_cat_acu', 'ProyectosController@update_cat_acu');
				Route::get('anular_cat_acu/{id}', 'ProyectosController@anular_cat_acu');

			});
		});

		Route::group(['as' => 'catalogos.', 'prefix' => 'catalogos'], function(){

			Route::group(['as' => 'insumos.', 'prefix' => 'insumos'], function(){
				//Insumos
				Route::get('index', 'ProyectosController@view_insumo')->name('index');
				Route::get('listar_insumos', 'ProyectosController@listar_insumos');
				Route::get('mostrar_insumo/{id}', 'ProyectosController@mostrar_insumo');
				Route::post('guardar_insumo', 'ProyectosController@guardar_insumo');
				Route::post('actualizar_insumo', 'ProyectosController@update_insumo');
				Route::get('anular_insumo/{id}', 'ProyectosController@anular_insumo');
				Route::get('listar_insumo_precios/{id}', 'ProyectosController@listar_insumo_precios');

			});

			Route::group(['as' => 'nombres-cu.', 'prefix' => 'nombres-cu'], function(){
				//Nombres CU
				Route::get('index', 'ProyectosController@view_cu')->name('index');
				Route::get('listar_cus', 'ProyectosController@listar_cus');
				Route::post('guardar_cu', 'ProyectosController@guardar_cu');
				Route::post('update_cu', 'ProyectosController@update_cu');
				Route::get('anular_cu/{id}', 'ProyectosController@anular_cu');
				Route::get('listar_partidas_cu/{id}', 'ProyectosController@listar_partidas_cu');
				
			});

			Route::group(['as' => 'acus.', 'prefix' => 'acus'], function(){
				//ACUS
				Route::get('index', 'ProyectosController@view_acu')->name('index');
				Route::get('listar_acus', 'ProyectosController@listar_acus');
				Route::get('listar_acus_sin_presup', 'ProyectosController@listar_acus_sin_presup');
				Route::get('mostrar_acu/{id}', 'ProyectosController@mostrar_acu');
				Route::get('listar_acu_detalle/{id}', 'ProyectosController@listar_acu_detalle');
				Route::get('listar_insumo_precios/{id}', 'ProyectosController@listar_insumo_precios');
				Route::post('guardar_precio', 'ProyectosController@guardar_precio');
				Route::post('guardar_acu', 'ProyectosController@guardar_acu');
				Route::post('actualizar_acu', 'ProyectosController@update_acu');
				Route::get('anular_acu/{id}', 'ProyectosController@anular_acu');
				Route::get('valida_acu_editar/{id}', 'ProyectosController@valida_acu_editar');
				Route::get('insumos/{id}/{cu}', 'ProyectosController@insumos');
				Route::get('partida_insumos_precio/{id}/{ins}', 'ProyectosController@partida_insumos_precio');

				Route::post('guardar_cu', 'ProyectosController@guardar_cu');
				Route::post('update_cu', 'ProyectosController@update_cu');
				Route::get('listar_cus', 'ProyectosController@listar_cus');
				Route::get('listar_insumos', 'ProyectosController@listar_insumos');
				Route::get('mostrar_presupuestos_acu/{id}', 'ProyectosController@mostrar_presupuestos_acu');

			});

		});

		Route::group(['as' => 'opciones.', 'prefix' => 'opciones'], function(){

			Route::group(['as' => 'opciones.', 'prefix' => 'opciones'], function(){
				//Opciones
				Route::get('index', 'ProyectosController@view_opcion')->name('index');
				Route::get('listar_opciones', 'ProyectosController@listar_opciones');
				Route::post('guardar_opcion', 'ProyectosController@guardar_opcion');
				Route::post('actualizar_opcion', 'ProyectosController@update_opcion');
				Route::get('anular_opcion/{id}', 'ProyectosController@anular_opcion');
				Route::post('guardar_cliente', 'ProyectosController@guardar_cliente');				
				Route::get('mostrar_clientes', 'AlmacenController@mostrar_clientes');

			});

			Route::group(['as' => 'presupuestos-internos.', 'prefix' => 'presupuestos-internos'], function(){
				/**Presupuesto Interno */
				Route::get('index', 'ProyectosController@view_presint')->name('index');
				Route::get('mostrar_presint/{id}', 'ProyectosController@mostrar_presint');
				Route::post('guardar_presint', 'ProyectosController@guardar_presint');
				Route::post('update_presint', 'ProyectosController@update_presint');
				Route::get('anular_presint/{id}', 'ProyectosController@anular_presint');
				Route::get('generar_estructura/{id}/{tp}', 'ProyectosController@generar_estructura');
				Route::get('listar_presupuesto_proyecto/{id}', 'ProyectosController@listar_presupuesto_proyecto');
				Route::get('anular_estructura/{id}', 'ProyectosController@anular_estructura');
				Route::get('totales/{id}', 'ProyectosController@totales');
				Route::get('download_presupuesto/{id}', 'ProyectosController@download_presupuesto');
				Route::get('actualiza_moneda/{id}', 'ProyectosController@actualiza_moneda');
				Route::get('mostrar_presupuestos/{id}', 'ProyectosController@mostrar_presupuestos');
				Route::get('listar_presupuestos_copia/{tp}/{id}', 'ProyectosController@listar_presupuestos_copia');
				Route::get('generar_partidas_presupuesto/{id}/{ida}', 'ProyectosController@generar_partidas_presupuesto');
				
				Route::get('listar_acus_cd/{id}', 'ProyectosController@listar_acus_cd');
				Route::get('listar_cd/{id}', 'ProyectosController@listar_cd');
				Route::get('listar_ci/{id}', 'ProyectosController@listar_ci');
				Route::get('listar_gg/{id}', 'ProyectosController@listar_gg');
				Route::post('guardar_componente_cd', 'ProyectosController@guardar_componente_cd');
				Route::post('guardar_componente_ci', 'ProyectosController@guardar_componente_ci');
				Route::post('guardar_componente_gg', 'ProyectosController@guardar_componente_gg');
				Route::post('update_componente_cd', 'ProyectosController@update_componente_cd');
				Route::post('update_componente_ci', 'ProyectosController@update_componente_ci');
				Route::post('update_componente_gg', 'ProyectosController@update_componente_gg');
				Route::post('anular_compo_cd', 'ProyectosController@anular_compo_cd');
				Route::post('anular_compo_ci', 'ProyectosController@anular_compo_ci');
				Route::post('anular_compo_gg', 'ProyectosController@anular_compo_gg');
				Route::post('guardar_partida_cd', 'ProyectosController@guardar_partida_cd');
				Route::post('guardar_partida_ci', 'ProyectosController@guardar_partida_ci');
				Route::post('guardar_partida_gg', 'ProyectosController@guardar_partida_gg');
				Route::post('update_partida_cd', 'ProyectosController@update_partida_cd');
				Route::post('update_partida_ci', 'ProyectosController@update_partida_ci');
				Route::post('update_partida_gg', 'ProyectosController@update_partida_gg');
				Route::post('anular_partida_cd', 'ProyectosController@anular_partida_cd');
				Route::post('anular_partida_ci', 'ProyectosController@anular_partida_ci');
				Route::post('anular_partida_gg', 'ProyectosController@anular_partida_gg');
				Route::get('subir_partida_cd/{id}', 'ProyectosController@subir_partida_cd');
				Route::get('subir_partida_ci/{id}', 'ProyectosController@subir_partida_ci');
				Route::get('subir_partida_gg/{id}', 'ProyectosController@subir_partida_gg');
				Route::get('bajar_partida_cd/{id}', 'ProyectosController@bajar_partida_cd');
				Route::get('bajar_partida_ci/{id}', 'ProyectosController@bajar_partida_ci');
				Route::get('bajar_partida_gg/{id}', 'ProyectosController@bajar_partida_gg');
				Route::get('crear_titulos_ci/{id}', 'ProyectosController@crear_titulos_ci');
				Route::get('crear_titulos_gg/{id}', 'ProyectosController@crear_titulos_gg');

				Route::post('add_unid_med', 'AlmacenController@add_unid_med');
				Route::post('update_unitario_partida_cd', 'ProyectosController@update_unitario_partida_cd');
				Route::get('listar_acus_sin_presup', 'ProyectosController@listar_acus_sin_presup');
				
				Route::get('mostrar_acu/{id}', 'ProyectosController@mostrar_acu');
				Route::get('partida_insumos_precio/{id}/{ins}', 'ProyectosController@partida_insumos_precio');
				Route::get('listar_acu_detalle/{id}', 'ProyectosController@listar_acu_detalle');
				Route::post('guardar_acu', 'ProyectosController@guardar_acu');
				Route::post('actualizar_acu', 'ProyectosController@update_acu');

				Route::post('guardar_cu', 'ProyectosController@guardar_cu');
				Route::post('update_cu', 'ProyectosController@update_cu');
				Route::get('listar_cus', 'ProyectosController@listar_cus');

				Route::get('listar_insumos', 'ProyectosController@listar_insumos');
				Route::get('mostrar_insumo/{id}', 'ProyectosController@mostrar_insumo');
				Route::post('guardar_insumo', 'ProyectosController@guardar_insumo');
				Route::get('listar_insumo_precios/{id}', 'ProyectosController@listar_insumo_precios');
				Route::post('guardar_precio', 'ProyectosController@guardar_precio');
				Route::post('guardar_insumo', 'ProyectosController@guardar_insumo');
				Route::post('actualizar_insumo', 'ProyectosController@update_insumo');
				Route::get('listar_opciones_sin_presint', 'ProyectosController@listar_opciones_sin_presint');

				Route::get('listar_obs_cd/{id}', 'ProyectosController@listar_obs_cd');
				Route::get('listar_obs_ci/{id}', 'ProyectosController@listar_obs_ci');
				Route::get('listar_obs_gg/{id}', 'ProyectosController@listar_obs_gg');
				Route::get('anular_obs_partida/{id}', 'ProyectosController@anular_obs_partida');
				Route::post('guardar_obs_partida', 'ProyectosController@guardar_obs_partida');
				
			});

			Route::group(['as' => 'cronogramas-internos.', 'prefix' => 'cronogramas-internos'], function(){
				//Cronograma Interno
				Route::get('index', 'ProyectosController@view_cronoint')->name('index');
				Route::get('nuevo_cronograma/{id}', 'ProyectosController@nuevo_cronograma');
				Route::get('listar_cronograma/{id}', 'ProyectosController@listar_cronograma');
				Route::post('guardar_crono', 'ProyectosController@guardar_crono');
				Route::get('anular_crono/{id}', 'ProyectosController@anular_crono');
				Route::get('ver_gant/{id}', 'ProyectosController@ver_gant');
				Route::get('listar_pres_crono/{tc}/{tp}', 'ProyectosController@listar_pres_crono');
				
				Route::get('mostrar_acu/{id}', 'ProyectosController@mostrar_acu');
				Route::get('listar_obs_cd/{id}', 'ProyectosController@listar_obs_cd');
				Route::get('listar_obs_ci/{id}', 'ProyectosController@listar_obs_ci');
				Route::get('listar_obs_gg/{id}', 'ProyectosController@listar_obs_gg');
				Route::get('anular_obs_partida/{id}', 'ProyectosController@anular_obs_partida');
				Route::post('guardar_obs_partida', 'ProyectosController@guardar_obs_partida');
				
			});

			Route::group(['as' => 'cronogramas-valorizados-internos.', 'prefix' => 'cronogramas-valorizados-internos'], function(){
				//Cronograma Valorizado Interno
				Route::get('index', 'ProyectosController@view_cronovalint')->name('index');
				Route::get('nuevo_crono_valorizado/{id}', 'ProyectosController@nuevo_crono_valorizado');
				Route::get('mostrar_crono_valorizado/{id}', 'ProyectosController@mostrar_crono_valorizado');
				Route::get('download_cronoval/{id}/{nro}', 'ProyectosController@download_cronoval');
				Route::post('guardar_cronoval_presupuesto', 'ProyectosController@guardar_cronoval_presupuesto');
				Route::get('anular_cronoval/{id}', 'ProyectosController@anular_cronoval');
				Route::get('listar_pres_cronoval/{tc}/{tp}', 'ProyectosController@listar_pres_cronoval');

			});

		});

		Route::group(['as' => 'propuestas.', 'prefix' => 'propuestas'], function(){

			Route::group(['as' => 'propuestas-cliente.', 'prefix' => 'propuestas-cliente'], function(){
				//Propuesta Cliente				
				Route::get('index', 'ProyectosController@view_propuesta')->name('index');
				Route::get('listar_propuestas', 'ProyectosController@listar_propuestas');
				Route::get('mostrar_propuesta/{id}', 'ProyectosController@mostrar_propuesta');
				Route::get('listar_partidas_propuesta/{id}', 'ProyectosController@listar_partidas_propuesta');
				Route::post('guardar_presup', 'ProyectosController@guardar_presup');
				Route::post('update_presup', 'ProyectosController@update_presup');
				Route::get('anular_propuesta/{id}', 'ProyectosController@anular_propuesta');
				Route::post('guardar_titulo', 'ProyectosController@guardar_titulo');
				Route::post('update_titulo', 'ProyectosController@update_titulo');
				Route::post('anular_titulo', 'ProyectosController@anular_titulo');
				Route::post('guardar_partida', 'ProyectosController@guardar_partida');
				Route::post('update_partida_propuesta', 'ProyectosController@update_partida_propuesta');
				Route::get('anular_partida/{id}', 'ProyectosController@anular_partida');
				Route::get('subir_partida/{id}', 'ProyectosController@subir_partida');
				Route::get('bajar_partida/{id}', 'ProyectosController@bajar_partida');
				Route::get('mostrar_detalle_partida/{id}', 'ProyectosController@mostrar_detalle_partida');
				Route::post('guardar_detalle_partida', 'ProyectosController@guardar_detalle_partida');
				Route::post('update_detalle_partida', 'ProyectosController@update_detalle_partida');

				Route::get('download_propuesta/{id}', 'ProyectosController@download_propuesta');
				Route::get('totales_propuesta/{id}', 'ProyectosController@totales_propuesta');
				Route::get('mostrar_total_presint/{id}', 'ProyectosController@mostrar_total_presint');
				Route::get('copiar_partidas_presint/{id}/{pr}', 'ProyectosController@copiar_partidas_presint');
				Route::get('listar_opciones', 'ProyectosController@listar_opciones');
				
				Route::get('listar_obs_cd/{id}', 'ProyectosController@listar_obs_cd');
				Route::get('listar_obs_ci/{id}', 'ProyectosController@listar_obs_ci');
				Route::get('listar_obs_gg/{id}', 'ProyectosController@listar_obs_gg');
				Route::get('anular_obs_partida/{id}', 'ProyectosController@anular_obs_partida');
				Route::post('guardar_obs_partida', 'ProyectosController@guardar_obs_partida');

				Route::get('listar_par_det', 'ProyectosController@listar_par_det');

			});
			
			Route::group(['as' => 'cronogramas-cliente.', 'prefix' => 'cronogramas-cliente'], function(){
				//Cronograma Cliente
				Route::get('index', 'ProyectosController@view_cronopro')->name('index');
				Route::get('listar_crono_propuesta/{id}', 'ProyectosController@listar_crono_propuesta');
				Route::get('listar_cronograma_propuesta/{id}', 'ProyectosController@listar_cronograma_propuesta');
				Route::post('guardar_crono_propuesta', 'ProyectosController@guardar_crono_propuesta');
				Route::get('listar_propuesta_crono/{id}', 'ProyectosController@listar_propuesta_crono');
				Route::get('ver_gant_propuesta/{id}', 'ProyectosController@ver_gant_propuesta');
				Route::get('mostrar_acu/{id}', 'ProyectosController@mostrar_acu');
				
				Route::get('listar_obs_cd/{id}', 'ProyectosController@listar_obs_cd');
				Route::get('listar_obs_ci/{id}', 'ProyectosController@listar_obs_ci');
				Route::get('listar_obs_gg/{id}', 'ProyectosController@listar_obs_gg');
				Route::get('anular_obs_partida/{id}', 'ProyectosController@anular_obs_partida');
				Route::post('guardar_obs_partida', 'ProyectosController@guardar_obs_partida');
				
			});
			
			Route::group(['as' => 'cronogramas-valorizados-cliente.', 'prefix' => 'cronogramas-valorizados-cliente'], function(){
				//Cronograma Valorizado Cliente
				Route::get('index', 'ProyectosController@view_cronovalpro')->name('index');
				Route::get('mostrar_cronoval_propuesta/{id}', 'ProyectosController@mostrar_cronoval_propuesta');
				Route::get('listar_cronoval_propuesta/{id}', 'ProyectosController@listar_cronoval_propuesta');
				Route::post('guardar_cronoval_propuesta', 'ProyectosController@guardar_cronoval_propuesta');
				Route::get('download_cronopro/{id}/{nro}', 'ProyectosController@download_cronopro');
				Route::get('listar_propuesta_cronoval/{id}', 'ProyectosController@listar_propuesta_cronoval');

			});

			Route::group(['as' => 'valorizaciones.', 'prefix' => 'valorizaciones'], function(){
				//Valorizacion
				Route::get('index', 'ProyectosController@view_valorizacion')->name('index');
				Route::get('listar_propuestas_activas', 'ProyectosController@listar_propuestas_activas');
				Route::get('mostrar_valorizacion/{id}', 'ProyectosController@mostrar_valorizacion');
				Route::get('listar_valorizaciones', 'ProyectosController@listar_valorizaciones');
				Route::get('nueva_valorizacion/{id}', 'ProyectosController@nueva_valorizacion');
				Route::post('guardar_valorizacion', 'ProyectosController@guardar_valorizacion');
				Route::post('update_valorizacion', 'ProyectosController@update_valorizacion');
				Route::get('anular_valorizacion', 'ProyectosController@anular_valorizacion');

			});
		});
		
		Route::group(['as' => 'ejecucion.', 'prefix' => 'ejecucion'], function(){

			Route::group(['as' => 'proyectos.', 'prefix' => 'proyectos'], function(){
				//Proyectos
				Route::get('index', 'ProyectosController@view_proyecto')->name('index');
				Route::get('listar_proyectos', 'ProyectosController@listar_proyectos');
				Route::get('mostrar_opcion/{id}', 'ProyectosController@mostrar_opcion');
				Route::get('mostrar_proyecto/{id}', 'ProyectosController@mostrar_proyecto');
				Route::post('guardar_proyecto', 'ProyectosController@guardar_proyecto');
				Route::post('actualizar_proyecto', 'ProyectosController@actualizar_proyecto');
				Route::get('anular_proyecto/{id}', 'ProyectosController@anular_proyecto');
				Route::get('listar_contratos_proy/{id}', 'ProyectosController@listar_contratos_proy');
				Route::post('guardar_contrato', 'ProyectosController@guardar_contrato');
				Route::get('abrir_adjunto/{adjunto}', 'ProyectosController@abrir_adjunto');
				// Route::get('abrir_adjunto_partida/{adjunto}', 'ProyectosController@abrir_adjunto_partida');
				Route::get('anular_contrato/{id}', 'ProyectosController@anular_contrato');
				Route::get('mostrar_presupuestos_acu/{id}', 'ProyectosController@mostrar_presupuestos_acu');
				Route::get('html_presupuestos_acu/{id}', 'ProyectosController@html_presupuestos_acu');

			});
			
			Route::group(['as' => 'residentes.', 'prefix' => 'residentes'], function(){
				//Residentes
				Route::get('index', 'ProyectosController@view_residentes')->name('index');
				Route::get('listar_trabajadores', 'ProyectosController@listar_trabajadores');
				Route::get('listar_residentes', 'ProyectosController@listar_residentes');
				Route::get('listar_proyectos_residente/{id}', 'ProyectosController@listar_proyectos_residente');
				// Route::get('anular_proyecto_residente/{id}', 'ProyectosController@anular_proyecto_residente');
				Route::post('guardar_residente', 'ProyectosController@guardar_residente');
				Route::post('update_residente', 'ProyectosController@update_residente');
				Route::get('anular_residente/{id}', 'ProyectosController@anular_residente');
				Route::get('listar_proyectos', 'ProyectosController@listar_proyectos');
				// Route::get('listar_proyectos_contratos', 'ProyectosController@listar_proyectos_contratos');
			});

			Route::group(['as' => 'presupuestos-ejecucion.', 'prefix' => 'presupuestos-ejecucion'], function(){
				//Presupuesto Ejecución
				Route::get('index', 'ProyectosController@view_preseje')->name('index');
				Route::get('mostrar_presint/{id}', 'ProyectosController@mostrar_presint');
				Route::post('guardar_preseje', 'ProyectosController@guardar_preseje');
				Route::post('update_preseje', 'ProyectosController@update_preseje');
				Route::get('anular_presint/{id}', 'ProyectosController@anular_presint');

				Route::get('generar_estructura/{id}/{tp}', 'ProyectosController@generar_estructura');
				Route::get('listar_presupuesto_proyecto/{id}', 'ProyectosController@listar_presupuesto_proyecto');
				Route::get('anular_estructura/{id}', 'ProyectosController@anular_estructura');
				Route::get('totales/{id}', 'ProyectosController@totales');
				Route::get('download_presupuesto/{id}', 'ProyectosController@download_presupuesto');
				Route::get('generar_preseje/{id}', 'ProyectosController@generar_preseje');
				Route::get('actualiza_moneda/{id}', 'ProyectosController@actualiza_moneda');
				Route::get('mostrar_presupuestos/{id}', 'ProyectosController@mostrar_presupuestos');
				Route::get('listar_presupuestos_copia/{tp}/{id}', 'ProyectosController@listar_presupuestos_copia');
				Route::get('generar_partidas_presupuesto/{id}/{ida}', 'ProyectosController@generar_partidas_presupuesto');
				Route::get('listar_proyectos', 'ProyectosController@listar_proyectos');

				Route::get('listar_acus_cd/{id}', 'ProyectosController@listar_acus_cd');
				Route::get('listar_cd/{id}', 'ProyectosController@listar_cd');
				Route::get('listar_ci/{id}', 'ProyectosController@listar_ci');
				Route::get('listar_gg/{id}', 'ProyectosController@listar_gg');
				Route::post('guardar_componente_cd', 'ProyectosController@guardar_componente_cd');
				Route::post('guardar_componente_ci', 'ProyectosController@guardar_componente_ci');
				Route::post('guardar_componente_gg', 'ProyectosController@guardar_componente_gg');
				Route::post('update_componente_cd', 'ProyectosController@update_componente_cd');
				Route::post('update_componente_ci', 'ProyectosController@update_componente_ci');
				Route::post('update_componente_gg', 'ProyectosController@update_componente_gg');
				Route::post('anular_compo_cd', 'ProyectosController@anular_compo_cd');
				Route::post('anular_compo_ci', 'ProyectosController@anular_compo_ci');
				Route::post('anular_compo_gg', 'ProyectosController@anular_compo_gg');
				Route::post('guardar_partida_cd', 'ProyectosController@guardar_partida_cd');
				Route::post('guardar_partida_ci', 'ProyectosController@guardar_partida_ci');
				Route::post('guardar_partida_gg', 'ProyectosController@guardar_partida_gg');
				Route::post('update_partida_cd', 'ProyectosController@update_partida_cd');
				Route::post('update_partida_ci', 'ProyectosController@update_partida_ci');
				Route::post('update_partida_gg', 'ProyectosController@update_partida_gg');
				Route::post('anular_partida_cd', 'ProyectosController@anular_partida_cd');
				Route::post('anular_partida_ci', 'ProyectosController@anular_partida_ci');
				Route::post('anular_partida_gg', 'ProyectosController@anular_partida_gg');
				Route::get('subir_partida_cd/{id}', 'ProyectosController@subir_partida_cd');
				Route::get('subir_partida_ci/{id}', 'ProyectosController@subir_partida_ci');
				Route::get('subir_partida_gg/{id}', 'ProyectosController@subir_partida_gg');
				Route::get('bajar_partida_cd/{id}', 'ProyectosController@bajar_partida_cd');
				Route::get('bajar_partida_ci/{id}', 'ProyectosController@bajar_partida_ci');
				Route::get('bajar_partida_gg/{id}', 'ProyectosController@bajar_partida_gg');
				Route::get('crear_titulos_ci/{id}', 'ProyectosController@crear_titulos_ci');
				Route::get('crear_titulos_gg/{id}', 'ProyectosController@crear_titulos_gg');

				Route::post('add_unid_med', 'AlmacenController@add_unid_med');
				Route::post('update_unitario_partida_cd', 'ProyectosController@update_unitario_partida_cd');
				Route::get('listar_acus_sin_presup', 'ProyectosController@listar_acus_sin_presup');
				
				Route::get('mostrar_acu/{id}', 'ProyectosController@mostrar_acu');
				Route::get('partida_insumos_precio/{id}/{ins}', 'ProyectosController@partida_insumos_precio');
				Route::get('listar_acu_detalle/{id}', 'ProyectosController@listar_acu_detalle');
				Route::post('guardar_acu', 'ProyectosController@guardar_acu');
				Route::post('actualizar_acu', 'ProyectosController@update_acu');

				Route::post('guardar_cu', 'ProyectosController@guardar_cu');
				Route::post('update_cu', 'ProyectosController@update_cu');
				Route::get('listar_cus', 'ProyectosController@listar_cus');

				Route::get('listar_insumos', 'ProyectosController@listar_insumos');
				Route::get('mostrar_insumo/{id}', 'ProyectosController@mostrar_insumo');
				Route::post('guardar_insumo', 'ProyectosController@guardar_insumo');
				Route::get('listar_insumo_precios/{id}', 'ProyectosController@listar_insumo_precios');
				Route::post('guardar_precio', 'ProyectosController@guardar_precio');
				Route::post('guardar_insumo', 'ProyectosController@guardar_insumo');
				Route::post('actualizar_insumo', 'ProyectosController@update_insumo');
				// Route::get('listar_opciones_sin_preseje', 'ProyectosController@listar_opciones_sin_preseje');

				Route::get('listar_obs_cd/{id}', 'ProyectosController@listar_obs_cd');
				Route::get('listar_obs_ci/{id}', 'ProyectosController@listar_obs_ci');
				Route::get('listar_obs_gg/{id}', 'ProyectosController@listar_obs_gg');
				Route::get('anular_obs_partida/{id}', 'ProyectosController@anular_obs_partida');
				Route::post('guardar_obs_partida', 'ProyectosController@guardar_obs_partida');

			});

			Route::group(['as' => 'cronogramas-ejecucion.', 'prefix' => 'cronogramas-ejecucion'], function(){

				Route::get('index', 'ProyectosController@view_cronoeje')->name('index');
				Route::get('nuevo_cronograma/{id}', 'ProyectosController@nuevo_cronograma');
				Route::get('listar_acus_cronograma/{id}', 'ProyectosController@listar_acus_cronograma');
				Route::get('listar_pres_crono/{tc}/{tp}', 'ProyectosController@listar_pres_crono');
				Route::get('listar_pres_cronoval/{tc}/{tp}', 'ProyectosController@listar_pres_cronoval');
				Route::post('guardar_crono', 'ProyectosController@guardar_crono');
				Route::get('anular_crono/{id}', 'ProyectosController@anular_crono');
				Route::get('ver_gant/{id}', 'ProyectosController@ver_gant');
				Route::get('listar_cronograma/{id}', 'ProyectosController@listar_cronograma');
				
			});

			Route::group(['as' => 'cronogramas-valorizados-ejecucion.', 'prefix' => 'cronogramas-valorizados-ejecucion'], function(){
				//Cronograma Valorizado Ejecucion
				Route::get('index', 'ProyectosController@view_cronovaleje')->name('index');
				Route::get('nuevo_crono_valorizado/{id}', 'ProyectosController@nuevo_crono_valorizado');
				Route::get('mostrar_crono_valorizado/{id}', 'ProyectosController@mostrar_crono_valorizado');
				Route::get('download_cronoval/{id}/{nro}', 'ProyectosController@download_cronoval');
				Route::post('guardar_cronoval_presupuesto', 'ProyectosController@guardar_cronoval_presupuesto');
				Route::get('anular_cronoval/{id}', 'ProyectosController@anular_cronoval');
				Route::get('listar_pres_cronoval/{tc}/{tp}', 'ProyectosController@listar_pres_cronoval');

			});

		});

		Route::group(['as' => 'reportes.', 'prefix' => 'reportes'], function(){

			Route::group(['as' => 'curvas.', 'prefix' => 'curvas'], function(){
				//Curvas S
				Route::get('index', 'ProyectosController@view_curvas')->name('index');
				Route::get('getProgramadoValorizado/{id}/{pre}', 'ProyectosController@getProgramadoValorizado');
				Route::get('listar_propuestas_preseje', 'ProyectosController@listar_propuestas_preseje');
				
			});

			Route::group(['as' => 'saldos.', 'prefix' => 'saldos'], function(){
				//Saldos
				Route::get('index', 'ProyectosController@view_saldos_pres')->name('index');
				Route::get('listar_saldos_presupuesto/{id}', 'ProyectosController@listar_saldos_presupuesto');
				Route::get('listar_estructuras_preseje', 'ProyectosController@listar_estructuras_preseje');
				Route::get('ver_detalle_partida/{id}', 'ProyectosController@ver_detalle_partida');
								
			});

			Route::group(['as' => 'opciones-relaciones.', 'prefix' => 'opciones-relaciones'], function(){
				//Opciones y Relaciones
				Route::get('index', 'ProyectosController@view_opciones_todo')->name('index');
				Route::get('listar_opciones_todo', 'ProyectosController@listar_opciones_todo');
				
			});

		});

		Route::group(['as' => 'configuraciones.', 'prefix' => 'configuraciones'], function(){

			Route::group(['as' => 'estructuras.', 'prefix' => 'estructuras'], function(){
				//Estructura Presupuestos
				Route::get('index', 'ProyectosController@view_presEstructura')->name('index');
				Route::get('listar_pres_estructura', 'ProyectosController@listar_pres_estructura');
				Route::get('mostrar_pres_estructura/{id}', 'ProyectosController@mostrar_pres_estructura');
				Route::post('guardar_pres_estructura', 'ProyectosController@guardar_pres_estructura');
				Route::post('update_pres_estructura', 'ProyectosController@update_pres_estructura');
				Route::get('listar_presupuesto/{id}', 'ProyectosController@listar_presupuesto');
				Route::get('listar_par_det', 'ProyectosController@listar_par_det');
				Route::get('cargar_grupos/{id}', 'ProyectosController@cargar_grupos');
				Route::post('guardar_titulo', 'ProyectosController@guardar_titulo');
				Route::post('update_titulo', 'ProyectosController@update_titulo');
				Route::post('anular_titulo', 'ProyectosController@anular_titulo');
				Route::post('guardar_partida', 'ProyectosController@guardar_partida');
				Route::post('update_partida', 'ProyectosController@update_partida');
				Route::get('anular_partida/{id}', 'ProyectosController@anular_partida');
				

			});
			
		});
		
	});
	
	Route::group(['as' => 'logistica.', 'prefix' => 'logistica'], function(){
		// Logística
		Route::get('index', 'LogisticaController@view_main_logistica')->name('index');

		Route::group(['as' => 'gestion-logistica.', 'prefix' => 'gestion-logistica'], function(){

			Route::group(['as' => 'requerimiento.', 'prefix' => 'requerimiento'], function(){

				Route::group(['as' => 'elaboracion.', 'prefix' => 'elaboracion'], function(){

					Route::get('index', 'LogisticaController@view_gestionar_requerimiento')->name('index');
					Route::get('lista-modal/{option?}', 'LogisticaController@mostrar_requerimientos')->name('lista-modal');
					Route::get('mostrar-requerimiento/{id?}/{codigo?}', 'LogisticaController@mostrar_requerimiento')->name('mostrar-requerimiento');
					Route::post('guardar', 'LogisticaController@guardar_requerimiento')->name('guardar');
					Route::put('actualizar/{id?}', 'LogisticaController@actualizar_requerimiento')->name('actualizar');
					Route::put('anular/{id_requerimiento?}', 'LogisticaController@anular_requerimiento')->name('anular');
					// Route::get('select-sede-by-empresa/{id?}', 'LogisticaController@select_sede_by_empresa')->name('select-sede-by-empresa');
					Route::get('select-sede-by-empresa/{id?}', 'LogisticaController@sedesAcceso')->name('select-sede-by-empresa');
					Route::post('copiar-requerimiento/{id?}', 'LogisticaController@copiar_requerimiento')->name('copiar-requerimiento');
					Route::get('telefonos-cliente/{id_persona?}/{id_cliente?}', 'LogisticaController@telefonos_cliente')->name('telefonos-cliente');
					Route::get('direcciones-cliente/{id_persona?}/{id_cliente?}', 'LogisticaController@direcciones_cliente')->name('direcciones-cliente');
					Route::get('emails-cliente/{id_persona?}/{id_cliente?}', 'LogisticaController@emails_cliente')->name('emails-cliente');
					Route::get('listar_ubigeos', 'AlmacenController@listar_ubigeos');
					Route::get('listar_personas', 'RecursosHumanosController@mostrar_persona_table')->name('listar_personas');
					Route::get('mostrar_clientes', 'AlmacenController@mostrar_clientes')->name('mostrar_clientes');;
					Route::get('cargar_almacenes/{id_sede}', 'AlmacenController@cargar_almacenes');
					Route::post('guardar-archivos-adjuntos-detalle-requerimiento', 'LogisticaController@guardar_archivos_adjuntos_detalle_requerimiento');
					Route::put('eliminar-archivo-adjunto-detalle-requerimiento/{id_archivo}', 'LogisticaController@eliminar_archivo_adjunto_detalle_requerimiento');
					Route::post('guardar-archivos-adjuntos-requerimiento', 'LogisticaController@guardar_archivos_adjuntos_requerimiento');
					Route::put('eliminar-archivo-adjunto-requerimiento/{id_archivo}', 'LogisticaController@eliminar_archivo_adjunto_requerimiento');
					Route::get('mostrar-archivos-adjuntos-requerimiento/{id_requerimiento?}', 'LogisticaController@mostrar_archivos_adjuntos_requerimiento');
					Route::get('listar_almacenes', 'AlmacenController@mostrar_almacenes');
					Route::get('mostrar-sede', 'ConfiguracionController@mostrarSede');
					Route::get('verTrazabilidadRequerimiento/{id}', 'DistribucionController@verTrazabilidadRequerimiento');
					Route::get('getCodigoRequerimiento/{id}', 'LogisticaController@getCodigoRequerimiento');
					Route::get('mostrar-archivos-adjuntos/{id_detalle_requerimiento}', 'LogisticaController@mostrar_archivos_adjuntos');
					Route::post('save_cliente', 'LogisticaController@save_cliente');
					Route::get('listar_saldos/{id}', 'AlmacenController@listar_saldos');
					Route::get('listar_opciones', 'ProyectosController@listar_opciones');
					Route::get('listar_partidas/{id_grupo}/{id_op_com}', 'EquipoController@listar_partidas');


				

				});
				Route::group(['as' => 'listado.', 'prefix' => 'listado'], function(){
					Route::get('index', 'LogisticaController@view_lista_requerimientos')->name('index');
					Route::get('listar/{empresa?}/{sede?}/{grupo?}', 'LogisticaController@listar_requerimiento_v2')->name('listar');
					Route::get('empresa', 'LogisticaController@getIdEmpresa')->name('empresa');
					Route::get('select-sede-by-empresa/{id?}', 'LogisticaController@select_sede_by_empresa')->name('select-sede-by-empresa');
					Route::get('select-grupo-by-sede/{id?}', 'LogisticaController@select_grupo_by_sede')->name('select-grupo-by-sede');
					Route::get('ver-flujos/{req?}/{doc?}', 'LogisticaController@flujo_aprobacion')->name('ver-flujos');
					Route::get('explorar-requerimiento/{id_requerimiento?}', 'LogisticaController@explorar_requerimiento')->name('explorar-requerimiento');
					Route::get('elaborados/{empresa?}/{sede?}/{grupo?}', 'LogisticaController@listar_requerimientos_elaborados')->name('elaborados');
				});
			});
			
			Route::group(['as' => 'cotizacion.', 'prefix' => 'cotizacion'], function(){
				Route::group(['as' => 'gestionar.', 'prefix' => 'gestionar'], function(){
					Route::get('index', 'LogisticaController@view_gestionar_cotizaciones')->name('index');
					Route::get('select-sede-by-empresa/{id?}', 'LogisticaController@select_sede_by_empresa')->name('select-sede-by-empresa');
					Route::get('listaCotizacionesPorGrupo/{id_cotizacion}', 'LogisticaController@listaCotizacionesPorGrupo');
					Route::get('requerimientos_entrante_a_cotizacion_v2/{id_empresa}/{id_sede}', 'LogisticaController@requerimientos_entrante_a_cotizacion_v2');
					Route::get('detalle_requerimiento', 'LogisticaController@detalle_requerimiento');
					Route::post('guardar_cotizacion/{id_gru}', 'LogisticaController@guardar_cotizacion');
					Route::post('agregar-item-cotizacion/{id_cotizacion}', 'LogisticaController@agregar_item_a_cotizacion');
					Route::post('eliminar-item-cotizacion/{id_cotizacion}', 'LogisticaController@eliminar_item_a_cotizacion');
					Route::put('actulizar-empresa-cotizacion', 'LogisticaController@actualizar_empresa_cotizacion');
					Route::put('actulizar-proveedor-cotizacion', 'LogisticaController@actualizar_proveedor_cotizacion');
					Route::put('actulizar-contacto-cotizacion', 'LogisticaController@actualizar_contacto_cotizacion');
					Route::get('mostrar_email_proveedor/{id}', 'LogisticaController@mostrar_email_proveedor');
					// Route::post('update_cotizacion', 'LogisticaController@update_cotizacion');
					// Route::post('duplicate_cotizacion', 'LogisticaController@duplicate_cotizacion');
					Route::post('guardar_contacto', 'LogisticaController@guardar_contacto');
					Route::get('descargar_solicitud_cotizacion_excel/{id}', 'LogisticaController@descargar_solicitud_cotizacion_excel');
					Route::get('anular_cotizacion/{id}', 'LogisticaController@anular_cotizacion');
					Route::get('saldo_por_producto/{id}', 'AlmacenController@saldo_por_producto');
					Route::post('enviar_correo', 'CorreoController@enviar');
					Route::get('estado_archivos_adjuntos_cotizacion/{id_cotizacion}', 'CorreoController@getAttachFileStatus');
					Route::post('guardar-archivos-adjuntos-cotizacion', 'CorreoController@guardar_archivos_adjuntos_cotizacion');
					Route::get('mostrar_grupo_cotizacion/{id}', 'LogisticaController@mostrar_grupo_cotizacion');
					Route::get('mostrar_cotizacion/{id}', 'LogisticaController@mostrar_cotizacion');
					Route::get('get_cotizacion/{id}', 'LogisticaController@get_cotizacion');
					Route::get('mostrar-archivos-adjuntos/{id_detalle_requerimiento}', 'LogisticaController@mostrar_archivos_adjuntos');
					Route::post('guardar-archivos-adjuntos-detalle-requerimiento', 'LogisticaController@guardar_archivos_adjuntos_detalle_requerimiento');
					Route::put('eliminar-archivo-adjunto-detalle-requerimiento/{id_archivo}', 'LogisticaController@eliminar_archivo_adjunto_detalle_requerimiento');
					Route::put('descargar_olicitud_cotizacion_excel/{id_cotizacion}', 'LogisticaController@descargar_olicitud_cotizacion_excel');
					Route::get('archivos_adjuntos_cotizacion/{id_cotizacion}', 'LogisticaController@mostrar_archivos_adjuntos_cotizacion');


					});
				});
			Route::group(['as' => 'orden.', 'prefix' => 'orden'], function(){
				Route::group(['as' => 'por-requerimiento.', 'prefix' => 'por-requerimiento'], function(){
					Route::get('index', 'LogisticaController@view_generar_orden_requerimiento')->name('index');
					// generar oreden por requerimiento
					Route::get('requerimientos-pendientes', 'LogisticaController@listar_requerimientos_pendientes')->name('requerimientos-pendientes'); 
					Route::get('requerimientos-atendidos', 'LogisticaController@listar_requerimientos_atendidos')->name('requerimientos-atendidos'); 
					Route::get('requerimiento-orden/{id?}', 'LogisticaController@get_requerimiento_orden')->name('requerimiento-orden'); 
					Route::post('guardar', 'LogisticaController@guardar_orden_por_requerimiento')->name('guardar');
					Route::put('revertir/{id_orden?}/{id_requerimiento?}', 'LogisticaController@revertir_orden_requerimiento')->name('revertir');
					Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
					Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');

				});
				Route::group(['as' => 'lista-ordenes.', 'prefix' => 'por-requerimiento'], function(){
					Route::get('vista_listar_ordenes', 'LogisticaController@view_listar_ordenes')->name('index');
					Route::get('listar_todas_ordenes', 'LogisticaController@listar_todas_ordenes');
					Route::get('generar_orden_pdf/{id}', 'LogisticaController@generar_orden_pdf'); // PDF
					Route::get('verSession', 'LogisticaController@verSession'); 
					Route::get('explorar-orden/{id_orden}', 'LogisticaController@explorar_orden'); 
					Route::put('guardar_aprobacion_orden/', 'LogisticaController@guardar_aprobacion_orden'); 
					Route::post('guardar_pago_orden', 'LogisticaController@guardar_pago_orden');
					Route::get('eliminar_pago/{id_pago}', 'LogisticaController@eliminar_pago'); 
	
					});
			});

		});

		Route::get('getEstadosRequerimientos', 'DistribucionController@getEstadosRequerimientos');
		Route::get('listarEstadosRequerimientos/{id}', 'DistribucionController@listarEstadosRequerimientos');
		
		Route::group(['as' => 'almacen.', 'prefix' => 'almacen'], function(){
	
			// Route::get('index', 'AlmacenController@view_main_almacen')->name('index');
	
			Route::group(['as' => 'catalogos.', 'prefix' => 'catalogos'], function(){
	
				Route::group(['as' => 'tipos.', 'prefix' => 'tipos'], function(){
					//Tipo Producto
					Route::get('index', 'AlmacenController@view_tipo')->name('index');
					Route::get('listar_tipos', 'AlmacenController@mostrar_tp_productos');
					Route::get('mostrar_tipo/{id}', 'AlmacenController@mostrar_tp_producto');
					Route::post('guardar_tipo', 'AlmacenController@guardar_tp_producto');
					Route::post('actualizar_tipo', 'AlmacenController@update_tp_producto');
					Route::get('anular_tipo/{id}', 'AlmacenController@anular_tp_producto');
					Route::get('revisarTipo/{id}', 'AlmacenController@tipo_revisar_relacion');
					
				});
	
				Route::group(['as' => 'categorias.', 'prefix' => 'categorias'], function(){
					//Categoria
					Route::get('index', 'AlmacenController@view_categoria')->name('index');
					Route::get('listar_categorias', 'AlmacenController@mostrar_categorias');
					Route::get('mostrar_categoria/{id}', 'AlmacenController@mostrar_categoria');
					Route::post('guardar_categoria', 'AlmacenController@guardar_categoria');
					Route::post('actualizar_categoria', 'AlmacenController@update_categoria');
					Route::get('anular_categoria/{id}', 'AlmacenController@anular_categoria');
					Route::get('revisarCat/{id}', 'AlmacenController@cat_revisar');
					
				});
	
				Route::group(['as' => 'sub-categorias.', 'prefix' => 'sub-categorias'], function(){
					//Sub Categoria
					Route::get('index', 'AlmacenController@view_subcategoria')->name('index');
					Route::get('listar_subcategorias', 'AlmacenController@mostrar_sub_categorias');
					Route::get('mostrar_subcategoria/{id}', 'AlmacenController@mostrar_sub_categoria');
					Route::post('guardar_subcategoria', 'AlmacenController@guardar_sub_categoria');
					Route::post('actualizar_subcategoria', 'AlmacenController@update_sub_categoria');
					Route::get('anular_subcategoria/{id}', 'AlmacenController@anular_sub_categoria');
					Route::get('revisarSubCat/{id}', 'AlmacenController@subcat_revisar');
					
				});
	
				Route::group(['as' => 'clasificaciones.', 'prefix' => 'clasificaciones'], function(){
					//Clasificacion
					Route::get('index', 'AlmacenController@view_clasificacion')->name('index');
					Route::get('listar_clasificaciones', 'AlmacenController@mostrar_clasificaciones');
					Route::get('mostrar_clasificacion/{id}', 'AlmacenController@mostrar_clasificacion');
					Route::post('guardar_clasificacion', 'AlmacenController@guardar_clasificacion');
					Route::post('actualizar_clasificacion', 'AlmacenController@update_clasificacion');
					Route::get('anular_clasificacion/{id}', 'AlmacenController@anular_clasificacion');
					Route::get('revisarClas/{id}', 'AlmacenController@clas_revisar');
					
				});
	
				Route::group(['as' => 'productos.', 'prefix' => 'productos'], function(){
					//Producto
					Route::get('index', 'AlmacenController@view_producto')->name('index');
					Route::get('mostrar_prods', 'AlmacenController@mostrar_prods');
					Route::get('mostrar_prods_almacen/{id}', 'AlmacenController@mostrar_prods_almacen');
					Route::get('mostrar_producto/{id}', 'AlmacenController@mostrar_producto');
					Route::post('guardar_producto', 'AlmacenController@guardar_producto');
					Route::post('actualizar_producto', 'AlmacenController@update_producto');
					Route::get('anular_producto/{id}', 'AlmacenController@anular_producto');
					Route::post('guardar_imagen', 'AlmacenController@guardar_imagen');
					
					Route::get('listar_ubicaciones_producto/{id}', 'AlmacenController@mostrar_ubicaciones_producto');
					Route::get('mostrar_ubicacion/{id}', 'AlmacenController@mostrar_ubicacion');
					Route::post('guardar_ubicacion', 'AlmacenController@guardar_ubicacion');
					Route::post('actualizar_ubicacion', 'AlmacenController@update_ubicacion');
					Route::get('anular_ubicacion/{id}', 'AlmacenController@anular_ubicacion');
					
					Route::get('listar_series_producto/{id}', 'AlmacenController@listar_series_producto');
					Route::get('mostrar_serie/{id}', 'AlmacenController@mostrar_serie');
					Route::post('guardar_serie', 'AlmacenController@guardar_serie');
					Route::post('actualizar_serie', 'AlmacenController@update_serie');
					Route::get('anular_serie/{id}', 'AlmacenController@anular_serie');
	
				});
	
				Route::group(['as' => 'catalogo-productos.', 'prefix' => 'catalogo-productos'], function(){
					Route::get('index', 'AlmacenController@view_prod_catalogo')->name('index');
					Route::get('listar_productos', 'AlmacenController@mostrar_productos');
				});
	
			});
	
			Route::group(['as' => 'ubicaciones.', 'prefix' => 'ubicaciones'], function(){
	
				Route::group(['as' => 'tipos-almacen.', 'prefix' => 'tipos-almacen'], function(){
					//Tipos Almacen
					Route::get('index', 'AlmacenController@view_tipo_almacen')->name('index');
					Route::get('listar_tipo_almacen', 'AlmacenController@mostrar_tipo_almacen');
					Route::get('cargar_tipo_almacen/{id}', 'AlmacenController@mostrar_tipo_almacenes');
					Route::post('guardar_tipo_almacen', 'AlmacenController@guardar_tipo_almacen');
					Route::post('editar_tipo_almacen', 'AlmacenController@update_tipo_almacen');
					Route::get('anular_tipo_almacen/{id}', 'AlmacenController@anular_tipo_almacen');
	
				});
	
				Route::group(['as' => 'almacenes.', 'prefix' => 'almacenes'], function(){
					//Almacen
					Route::get('index', 'AlmacenController@view_almacenes')->name('index');
					Route::get('listar_almacenes', 'AlmacenController@mostrar_almacenes');
					Route::get('cargar_almacen/{id}', 'AlmacenController@mostrar_almacen');
					Route::post('guardar_almacen', 'AlmacenController@guardar_almacen');
					Route::post('editar_almacen', 'AlmacenController@update_almacen');
					Route::get('anular_almacen/{id}', 'AlmacenController@anular_almacen');
					Route::get('listar_ubigeos', 'AlmacenController@listar_ubigeos');
	
					Route::get('almacen_posicion/{id}', 'AlmacenController@almacen_posicion');
	
				});
	
				Route::group(['as' => 'posiciones.', 'prefix' => 'posiciones'], function(){
					//Almacen
					Route::get('index', 'AlmacenController@view_ubicacion')->name('index');
					Route::get('listar_estantes', 'AlmacenController@mostrar_estantes');
					Route::get('listar_estantes_almacen/{id}', 'AlmacenController@mostrar_estantes_almacen');
					Route::get('mostrar_estante/{id}', 'AlmacenController@mostrar_estante');
					Route::post('guardar_estante', 'AlmacenController@guardar_estante');
					Route::post('actualizar_estante', 'AlmacenController@update_estante');
					Route::get('anular_estante/{id}', 'AlmacenController@anular_estante');
					Route::get('revisar_estante/{id}', 'AlmacenController@revisar_estante');
					Route::post('guardar_estantes', 'AlmacenController@guardar_estantes');
					Route::get('listar_niveles', 'AlmacenController@mostrar_niveles');
					Route::get('listar_niveles_estante/{id}', 'AlmacenController@mostrar_niveles_estante');
					Route::get('mostrar_nivel/{id}', 'AlmacenController@mostrar_nivel');
					Route::post('guardar_nivel', 'AlmacenController@guardar_nivel');
					Route::post('actualizar_nivel', 'AlmacenController@update_nivel');
					Route::get('anular_nivel/{id}', 'AlmacenController@anular_nivel');
					Route::get('revisar_nivel/{id}', 'AlmacenController@revisar_nivel');
					Route::post('guardar_niveles', 'AlmacenController@guardar_niveles');
					Route::get('listar_posiciones', 'AlmacenController@mostrar_posiciones');
					Route::get('listar_posiciones_nivel/{id}', 'AlmacenController@mostrar_posiciones_nivel');
					Route::get('mostrar_posicion/{id}', 'AlmacenController@mostrar_posicion');
					Route::post('guardar_posiciones', 'AlmacenController@guardar_posiciones');
					Route::get('anular_posicion/{id}', 'AlmacenController@anular_posicion');
					Route::get('select_posiciones_almacen/{id}', 'AlmacenController@select_posiciones_almacen');
	
				});
	
			});
	
			Route::group(['as' => 'pagos.', 'prefix' => 'pagos'], function(){
	
				Route::group(['as' => 'confirmacion-pagos.', 'prefix' => 'confirmacion-pagos'], function(){
	
					Route::get('index', 'DistribucionController@view_requerimientoPagos')->name('index');
					Route::post('listarRequerimientosPendientesPagos', 'DistribucionController@listarRequerimientosPendientesPagos');
					Route::post('listarRequerimientosConfirmadosPagos', 'DistribucionController@listarRequerimientosConfirmadosPagos');
					Route::post('pago_confirmado', 'DistribucionController@pago_confirmado');
					Route::post('pago_no_confirmado', 'DistribucionController@pago_no_confirmado');
					Route::get('verDetalleRequerimiento/{id}', 'DistribucionController@verDetalleRequerimiento');
					Route::get('verRequerimientoAdjuntos/{id}', 'DistribucionController@verRequerimientoAdjuntos');
	
	
				});
	
			});
	
			Route::group(['as' => 'distribucion.', 'prefix' => 'distribucion'], function(){
	
				Route::group(['as' => 'despachos.', 'prefix' => 'despachos'], function(){
					//Ordenes Despacho
					Route::get('index', 'DistribucionController@view_ordenesDespacho')->name('index');
					Route::post('listarRequerimientosPendientes', 'DistribucionController@listarRequerimientosPendientes');
					Route::get('verDetalleRequerimiento/{id}', 'DistribucionController@verDetalleRequerimiento');
					Route::get('verDetalleIngreso/{id}', 'DistribucionController@verDetalleIngreso');
					Route::post('guardar_orden_despacho', 'DistribucionController@guardar_orden_despacho');
					Route::post('listarOrdenesDespacho', 'DistribucionController@listarOrdenesDespacho');
					Route::get('verDetalleDespacho/{id}', 'DistribucionController@verDetalleDespacho');
					Route::post('guardar_grupo_despacho', 'DistribucionController@guardar_grupo_despacho');
					Route::post('despacho_anular_requerimiento', 'DistribucionController@anular_requerimiento');
					Route::get('anular_orden_despacho/{id}', 'DistribucionController@anular_orden_despacho');
					Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
					Route::post('listarGruposDespachados', 'DistribucionController@listarGruposDespachados');
					Route::get('verDetalleGrupoDespacho/{id}', 'DistribucionController@verDetalleGrupoDespacho');
					Route::post('despacho_conforme', 'DistribucionController@despacho_conforme');
					Route::post('despacho_no_conforme', 'DistribucionController@despacho_no_conforme');
					Route::get('imprimir_despacho/{id}', 'DistribucionController@imprimir_despacho');
					Route::get('listarAdjuntosOrdenDespacho/{id}', 'DistribucionController@listarAdjuntosOrdenDespacho');
					Route::post('guardar_od_adjunto', 'DistribucionController@guardar_od_adjunto');
					Route::get('anular_od_adjunto/{id}', 'DistribucionController@anular_od_adjunto');
					Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');
					Route::get('mostrar_clientes', 'AlmacenController@mostrar_clientes');
					Route::get('listar_personas', 'RecursosHumanosController@mostrar_persona_table');

				});
	
				Route::group(['as' => 'trazabilidad-requerimientos.', 'prefix' => 'trazabilidad-requerimientos'], function(){
	
					Route::get('index', 'DistribucionController@view_trazabilidad_requerimientos')->name('index');
					Route::post('listarRequerimientosTrazabilidad', 'DistribucionController@listarRequerimientosTrazabilidad');
					Route::get('verTrazabilidadRequerimiento/{id}', 'DistribucionController@verTrazabilidadRequerimiento');
					
				});
	
			});
	
			Route::group(['as' => 'movimientos.', 'prefix' => 'movimientos'], function(){
	
				Route::group(['as' => 'pendientes-ingreso.', 'prefix' => 'pendientes-ingreso'], function(){
					//Pendientes de Ingreso
					Route::get('index', 'OrdenesPendientesController@view_ordenesPendientes')->name('index');
					Route::post('listarOrdenesPendientes', 'OrdenesPendientesController@listarOrdenesPendientes');
					Route::post('listarOrdenesEntregadas', 'OrdenesPendientesController@listarOrdenesEntregadas');
					Route::get('detalleOrden/{id}', 'OrdenesPendientesController@detalleOrden');
					Route::post('guardar_guia_com_oc', 'OrdenesPendientesController@guardar_guia_com_oc');
					Route::get('verGuiasOrden/{id}', 'OrdenesPendientesController@verGuiasOrden');
					Route::post('guardar_guia_transferencia', 'OrdenesPendientesController@guardar_guia_transferencia');
					Route::post('anular_ingreso', 'OrdenesPendientesController@anular_ingreso');
					Route::get('cargar_almacenes/{id}', 'AlmacenController@cargar_almacenes');
					Route::get('imprimir_ingreso/{id}', 'AlmacenController@imprimir_ingreso');

				});
	
				Route::group(['as' => 'pendientes-salida.', 'prefix' => 'pendientes-salida'], function(){
					//Pendientes de Salida
					Route::get('index', 'DistribucionController@view_despachosPendientes')->name('index');
					Route::post('listarOrdenesDespachoPendientes', 'DistribucionController@listarOrdenesDespachoPendientes');
					Route::post('guardar_guia_despacho', 'DistribucionController@guardar_guia_despacho');
					Route::post('listarSalidasDespacho', 'DistribucionController@listarSalidasDespacho');
					Route::post('anular_salida', 'DistribucionController@anular_salida');
					Route::get('verDetalleDespacho/{id}', 'DistribucionController@verDetalleDespacho');
					Route::get('imprimir_salida/{id}', 'AlmacenController@imprimir_salida');
	
				});
	
				Route::group(['as' => 'guias-compra.', 'prefix' => 'guias-compra'], function(){
					//Guia de Compra
					Route::get('index', 'AlmacenController@view_guia_compra')->name('index');
					Route::get('mostrar_guia_compra/{id}', 'AlmacenController@mostrar_guia_compra');
					Route::post('guardar_guia_compra', 'AlmacenController@guardar_guia_compra');
					Route::post('actualizar_guia_compra', 'AlmacenController@update_guia_compra');
					Route::post('anular_guia_compra', 'AlmacenController@anular_guia_compra');
					Route::get('generar_ingreso/{id}', 'AlmacenController@generar_ingreso');
					Route::get('id_ingreso/{id}', 'AlmacenController@id_ingreso');
					Route::get('imprimir_ingreso/{id}', 'AlmacenController@imprimir_ingreso');
					Route::get('direccion_almacen/{id}', 'AlmacenController@direccion_almacen');
					Route::get('mostrar_guia_detalle/{id}/{pro}', 'AlmacenController@mostrar_guia_detalle');
	
					Route::get('listar_guias_compra', 'AlmacenController@listar_guias_compra');
					Route::get('listar_guias_proveedor/{id}', 'AlmacenController@listar_guias_proveedor');
					//Prorrateo
					Route::post('guardar_prorrateo', 'AlmacenController@guardar_prorrateo');
					Route::post('guardar_prorrateo_detalle', 'AlmacenController@guardar_prorrateo_detalle');
					Route::get('listar_docs_prorrateo/{id}', 'AlmacenController@listar_docs_prorrateo');
					Route::get('listar_guia_detalle_prorrateo/{id}/{total}', 'AlmacenController@listar_guia_detalle_prorrateo');
					Route::get('eliminar_doc_prorrateo/{id}/{id_doc}', 'AlmacenController@eliminar_doc_prorrateo');
					Route::post('update_doc_prorrateo', 'AlmacenController@update_doc_prorrateo');
					Route::get('tipo_cambio_compra/{fecha}', 'AlmacenController@tipo_cambio_compra');
					Route::post('update_guia_detalle_adic', 'AlmacenController@update_guia_detalle_adic');
					Route::get('guardar_tipo_prorrateo/{nombre}', 'AlmacenController@guardar_tipo_prorrateo');
					// Route::get('listar_guia_transportista/{guia}', 'AlmacenController@mostrar_transportistas');
					// Route::get('mostrar_transportista/{id}', 'AlmacenController@mostrar_transportista');
					// Route::post('guardar_transportista', 'AlmacenController@guardar_transportista');
					// Route::post('actualizar_transportista', 'AlmacenController@update_transportista');
					// Route::get('anular_transportista/{id}', 'AlmacenController@anular_transportista');
					Route::get('listar_guia_detalle/{guia}', 'AlmacenController@listar_guia_detalle');
					Route::get('anular_detalle/{id}', 'AlmacenController@anular_detalle');
					Route::post('update_guia_detalle', 'AlmacenController@update_guia_detalle');
					Route::post('guardar_guia_detalle', 'AlmacenController@guardar_guia_detalle');
	
					Route::get('listar_oc_det/{id}/{alm}', 'AlmacenController@listar_oc_det');
					Route::get('guia_ocs/{id}', 'AlmacenController@guia_ocs');
					Route::get('anular_oc/{id}/{guia}', 'AlmacenController@anular_oc');
					Route::post('guardar_detalle_oc', 'AlmacenController@guardar_detalle_oc');
					
					Route::get('listar_series/{id}', 'AlmacenController@listar_series');
					Route::post('guardar_series', 'AlmacenController@guardar_series');
					Route::get('mostrar_prods', 'AlmacenController@mostrar_prods');
					Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
	
					Route::get('mostrar_detalle/{id}', 'AlmacenController@mostrar_detalle');
					Route::get('listar_ocs', 'AlmacenController@listar_ocs');
					Route::get('listar_ordenes/{id}', 'AlmacenController@listar_ordenes');
					Route::get('listar_series_almacen/{prod}/{alm}', 'AlmacenController@listar_series_almacen');
					Route::get('listar_series_guia_ven/{id}', 'AlmacenController@listar_series_guia_ven');
					Route::get('buscar_serie/{serie}', 'AlmacenController@buscar_serie');
					Route::get('listar_ordenes_proveedor/{id}', 'LogisticaController@listar_ordenes_proveedor');
	
				});
	
				Route::group(['as' => 'guias-venta.', 'prefix' => 'guias-venta'], function(){
					//Guia Venta
					Route::get('index', 'AlmacenController@view_guia_venta')->name('index');
					Route::get('mostrar_guia_venta/{id}', 'AlmacenController@mostrar_guia_venta');
					Route::post('guardar_guia_venta', 'AlmacenController@guardar_guia_venta');
					Route::post('actualizar_guia_venta', 'AlmacenController@update_guia_venta');
					Route::post('anular_guia_venta', 'AlmacenController@anular_guia_venta');
					Route::get('generar_salida_guia/{id}', 'AlmacenController@generar_salida_guia');
					Route::get('id_salida/{id}', 'AlmacenController@id_salida');
					Route::get('direccion_almacen/{id}', 'AlmacenController@direccion_almacen');
					Route::get('cargar_almacenes/{id}', 'AlmacenController@cargar_almacenes');
					Route::get('next_serie_numero_guia/{id}/{tp}', 'AlmacenController@next_serie_numero_guia');
	
					Route::get('listar_guias_almacen/{id}', 'AlmacenController@listar_guias_almacen');
					Route::get('listar_req/{id}', 'AlmacenController@listar_req');
					Route::get('listar_doc_ven/{emp}/{cli}', 'AlmacenController@listar_doc_ven');
					Route::get('listar_detalle_doc/{id}/{tp}/{alm}', 'AlmacenController@listar_detalle_doc');
					Route::post('guardar_detalle_ing', 'AlmacenController@guardar_detalle_ing');
					Route::get('listar_guia_ven_det/{id}', 'AlmacenController@listar_guia_ven_det');
					Route::post('guardar_guia_ven_detalle', 'AlmacenController@guardar_guia_ven_detalle');
					Route::post('update_guia_ven_detalle', 'AlmacenController@update_guia_ven_detalle');
					Route::get('anular_guia_ven_detalle/{id}', 'AlmacenController@anular_guia_ven_detalle');
					Route::get('listar_guias_venta', 'AlmacenController@listar_guias_venta');
	
					Route::get('listar_series_guia_ven/{id}', 'AlmacenController@listar_series_guia_ven');
					Route::get('buscar_serie/{serie}', 'AlmacenController@buscar_serie');
					Route::post('update_series', 'AlmacenController@update_series');
					Route::post('guardar_transferencia', 'AlmacenController@guardar_transferencia');
					Route::get('listar_series_almacen/{prod}/{alm}', 'AlmacenController@listar_series_almacen');
					Route::get('cargar_almacenes_contrib/{id}', 'AlmacenController@cargar_almacenes_contrib');
					Route::get('mostrar_prods', 'AlmacenController@mostrar_prods');
					Route::get('mostrar_prods_almacen/{id}', 'AlmacenController@mostrar_prods_almacen');
	
					Route::get('mostrar_clientes', 'AlmacenController@mostrar_clientes');
					Route::get('mostrar_clientes_empresa', 'AlmacenController@mostrar_clientes_empresa');
					Route::post('save_cliente', 'LogisticaController@save_cliente');
					Route::get('imprimir_salida/{id}', 'AlmacenController@imprimir_salida');
					// Route::get('anular_guia/{doc}/{guia}', 'AlmacenController@anular_guia');
					// Route::get('next_correlativo_prod/{subcat}/{clas}', 'AlmacenController@next_correlativo_prod');
					// Route::get('proveedor/{id}', 'AlmacenController@proveedor');
	
				});
				
			});
			
			Route::group(['as' => 'transferencias.', 'prefix' => 'transferencias'], function(){
				
				Route::group(['as' => 'gestion-transferencias.', 'prefix' => 'gestion-transferencias'], function(){
					//Transferencias
					Route::get('index', 'TransferenciaController@view_listar_transferencias')->name('index');
					Route::get('listar_transferencias_pendientes/{ori}', 'TransferenciaController@listar_transferencias_pendientes');
					Route::get('listar_transferencias_recibidas/{ori}', 'TransferenciaController@listar_transferencias_recibidas');
					Route::get('listar_transferencia_detalle/{id}', 'TransferenciaController@listar_transferencia_detalle');
					Route::post('guardar_ingreso_transferencia', 'TransferenciaController@guardar_ingreso_transferencia');
					Route::post('anular_transferencia_ingreso', 'TransferenciaController@anular_transferencia_ingreso');
					Route::get('ingreso_transferencia/{id}', 'TransferenciaController@ingreso_transferencia');
					Route::get('transferencia_nextId/{id}', 'TransferenciaController@transferencia_nextId');
					Route::post('anular_transferencia_salida', 'TransferenciaController@anular_transferencia_salida');
					Route::get('imprimir_ingreso/{id}', 'AlmacenController@imprimir_ingreso');
					
				});
	
			});
	
			Route::group(['as' => 'customizacion.', 'prefix' => 'customizacion'], function(){
	
				Route::group(['as' => 'gestion-customizaciones.', 'prefix' => 'gestion-customizaciones'], function(){
					//Transformaciones
					Route::get('index', 'AlmacenController@view_listar_transformaciones')->name('index');
					Route::get('listar_todas_transformaciones/{id}', 'AlmacenController@listar_todas_transformaciones');
					
				});
	
				Route::group(['as' => 'hoja-transformacion.', 'prefix' => 'hoja-transformacion'], function(){
					//Transformaciones
					Route::get('index', 'AlmacenController@view_transformacion')->name('index');
					Route::post('guardar_transformacion', 'AlmacenController@guardar_transformacion');
					Route::post('update_transformacion', 'AlmacenController@update_transformacion');
					Route::get('listar_transformaciones', 'AlmacenController@listar_transformaciones');
					Route::get('mostrar_transformacion/{id}', 'AlmacenController@mostrar_transformacion');
					Route::get('anular_transformacion/{id}', 'AlmacenController@anular_transformacion');
					Route::get('listar_materias/{id}', 'AlmacenController@listar_materias');
					Route::get('listar_directos/{id}', 'AlmacenController@listar_directos');
					Route::get('listar_indirectos/{id}', 'AlmacenController@listar_indirectos');
					Route::get('listar_sobrantes/{id}', 'AlmacenController@listar_sobrantes');
					Route::get('listar_transformados/{id}', 'AlmacenController@listar_transformados');
					Route::get('procesar_transformacion/{id}', 'AlmacenController@procesar_transformacion');
					Route::post('guardar_materia', 'AlmacenController@guardar_materia');
					Route::post('guardar_directo', 'AlmacenController@guardar_directo');
					Route::post('guardar_indirecto', 'AlmacenController@guardar_indirecto');
					Route::post('guardar_sobrante', 'AlmacenController@guardar_sobrante');
					Route::post('guardar_transformado', 'AlmacenController@guardar_transformado');
					Route::post('update_materia', 'AlmacenController@update_materia');
					Route::post('update_directo', 'AlmacenController@update_directo');
					Route::post('update_indirecto', 'AlmacenController@update_indirecto');
					Route::post('update_sobrante', 'AlmacenController@update_sobrante');
					Route::post('update_transformado', 'AlmacenController@update_transformado');
					Route::get('id_ingreso_transformacion/{id}', 'AlmacenController@id_ingreso_transformacion');
					Route::get('id_salida_transformacion/{id}', 'AlmacenController@id_salida_transformacion');
					Route::get('anular_materia/{id}', 'AlmacenController@anular_materia');
					Route::get('anular_directo/{id}', 'AlmacenController@anular_directo');
					Route::get('anular_indirecto/{id}', 'AlmacenController@anular_indirecto');
					Route::get('anular_sobrante/{id}', 'AlmacenController@anular_sobrante');
					Route::get('anular_transformado/{id}', 'AlmacenController@anular_transformado');
					
				});
	
			});
	
			Route::group(['as' => 'reportes.', 'prefix' => 'reportes'], function(){
	
				Route::group(['as' => 'saldos.', 'prefix' => 'saldos'], function(){
	
					Route::get('index', 'AlmacenController@view_saldos')->name('index');
					Route::get('listar_saldos/{id}', 'AlmacenController@listar_saldos');
					Route::get('listar_saldos_todo', 'AlmacenController@listar_saldos_todo');
					Route::get('verRequerimientosReservados/{id}/{alm}', 'DistribucionController@verRequerimientosReservados');
					Route::get('tipo_cambio_compra/{fecha}', 'AlmacenController@tipo_cambio_compra');
	
				});
	
				Route::group(['as' => 'lista-ingresos.', 'prefix' => 'lista-ingresos'], function(){
	
					Route::get('index', 'AlmacenController@view_ingresos')->name('index');
					Route::get('listar_ingresos/{alm}/{docs}/{cond}/{fini}/{ffin}/{prov}/{usu}/{mon}/{tra}', 'AlmacenController@listar_ingresos');
					Route::get('update_revisado/{id}/{rev}/{obs}', 'AlmacenController@update_revisado');
					
					Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
					Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
					Route::get('listar_transportistas_com', 'AlmacenController@listar_transportistas_com');
					Route::get('listar_transportistas_ven', 'AlmacenController@listar_transportistas_ven');
	
				});
	
				Route::group(['as' => 'lista-salidas.', 'prefix' => 'lista-salidas'], function(){
	
					Route::get('index', 'AlmacenController@view_salidas')->name('index');
					Route::get('listar_salidas/{alm}/{docs}/{cond}/{fini}/{ffin}/{cli}/{usu}/{mon}/{ref}', 'AlmacenController@listar_salidas');
					Route::get('update_revisado/{id}/{rev}/{obs}', 'AlmacenController@update_revisado');
	
					Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
					Route::get('mostrar_clientes', 'AlmacenController@mostrar_clientes');
					Route::get('mostrar_clientes_empresa', 'AlmacenController@mostrar_clientes_empresa');
					Route::get('listar_transportistas_com', 'AlmacenController@listar_transportistas_com');
					Route::get('listar_transportistas_ven', 'AlmacenController@listar_transportistas_ven');
					
				});
	
				Route::group(['as' => 'detalle-ingresos.', 'prefix' => 'detalle-ingresos'], function(){
	
					Route::get('index', 'AlmacenController@view_busqueda_ingresos')->name('index');
					Route::get('listar_busqueda_ingresos/{alm}/{tp}/{des}/{doc}/{fini}/{ffin}', 'AlmacenController@listar_busqueda_ingresos');
					Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
					Route::get('imprimir_ingreso/{id}', 'AlmacenController@imprimir_ingreso');
					Route::get('imprimir_guia_ingreso/{id}', 'AlmacenController@imprimir_guia_ingreso');
					
				});
				
				Route::group(['as' => 'detalle-salidas.', 'prefix' => 'detalle-salidas'], function(){
					
					Route::get('index', 'AlmacenController@view_busqueda_salidas')->name('index');
					Route::get('listar_busqueda_salidas/{alm}/{tp}/{des}/{doc}/{fini}/{ffin}', 'AlmacenController@listar_busqueda_salidas');
					Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
					Route::get('imprimir_salida/{id}', 'AlmacenController@imprimir_salida');
					
				});
	
				Route::group(['as' => 'kardex-general.', 'prefix' => 'kardex-general'], function(){
	
					Route::get('index', 'AlmacenController@view_kardex_general')->name('index');
					Route::get('kardex_general/{id}/{fini}/{ffin}', 'AlmacenController@kardex_general');
					Route::get('kardex_sunat/{id}/{fini}/{ffin}', 'AlmacenController@download_kardex_sunat');
					// Route::get('kardex_sunatx/{id}', 'AlmacenController@kardex_sunat');
									
				});
	
				Route::group(['as' => 'kardex-productos.', 'prefix' => 'kardex-productos'], function(){
	
					Route::get('index', 'AlmacenController@view_kardex_detallado')->name('index');
					Route::get('kardex_producto/{id}/{alm}/{fini}/{ffin}', 'AlmacenController@kardex_producto');
					Route::get('listar_kardex_producto/{id}/{alm}/{fini}/{ffin}', 'AlmacenController@kardex_producto');
					Route::get('kardex_detallado/{id}/{alm}/{fini}/{ffin}', 'AlmacenController@download_kardex_producto');
					Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
					Route::get('datos_producto/{id}', 'AlmacenController@datos_producto');
					Route::get('mostrar_prods', 'AlmacenController@mostrar_prods');
					Route::get('mostrar_prods_almacen/{id}', 'AlmacenController@mostrar_prods_almacen');
	
				});
	
				Route::group(['as' => 'kardex-series.', 'prefix' => 'kardex-series'], function(){
	
					Route::get('index', 'AlmacenController@view_kardex_series')->name('index');
					Route::get('listar_kardex_serie/{serie}/{des}', 'AlmacenController@listar_kardex_serie');
					Route::get('datos_producto/{id}', 'AlmacenController@datos_producto');
					Route::get('mostrar_prods', 'AlmacenController@mostrar_prods');
					Route::get('mostrar_prods_almacen/{id}', 'AlmacenController@mostrar_prods_almacen');
					
				});
	
				Route::group(['as' => 'documentos-prorrateo.', 'prefix' => 'documentos-prorrateo'], function(){
	
					Route::get('index', 'AlmacenController@view_docs_prorrateo')->name('index');
					Route::get('listar_documentos_prorrateo', 'AlmacenController@listar_documentos_prorrateo');
								
				});
	
			});
	
			Route::group(['as' => 'variables.', 'prefix' => 'variables'], function(){
	
				Route::group(['as' => 'series-numeros.', 'prefix' => 'series-numeros'], function(){
	
					Route::get('index', 'AlmacenController@view_serie_numero')->name('index');
					Route::get('listar_series_numeros', 'AlmacenController@listar_series_numeros');
					Route::get('mostrar_serie_numero/{id}', 'AlmacenController@mostrar_serie_numero');
					Route::post('guardar_serie_numero', 'AlmacenController@guardar_serie_numero');
					Route::post('actualizar_serie_numero', 'AlmacenController@update_serie_numero');
					Route::get('anular_serie_numero/{id}', 'AlmacenController@anular_serie_numero');
					Route::get('series_numeros/{desde}/{hasta}/{num}/{serie}', 'AlmacenController@series_numeros');
												
				});
	
				Route::group(['as' => 'tipos-movimiento.', 'prefix' => 'tipos-movimiento'], function(){
	
					Route::get('index', 'AlmacenController@view_tipo_movimiento')->name('index');
					Route::get('listar_tipoMov', 'AlmacenController@mostrar_tipos_mov');
					Route::get('mostrar_tipoMov/{id}', 'AlmacenController@mostrar_tipo_mov');
					Route::post('guardar_tipoMov', 'AlmacenController@guardar_tipo_mov');
					Route::post('actualizar_tipoMov', 'AlmacenController@update_tipo_mov');
					Route::get('anular_tipoMov/{id}', 'AlmacenController@anular_tipo_mov');
																
				});
	
				Route::group(['as' => 'tipos-documento.', 'prefix' => 'tipos-documento'], function(){
	
					Route::get('index', 'AlmacenController@view_tipo_doc_almacen')->name('index');
					Route::get('listar_tp_docs', 'AlmacenController@listar_tp_docs');
					Route::get('mostrar_tp_doc/{id}', 'AlmacenController@mostrar_tp_doc');
					Route::post('guardar_tp_doc', 'AlmacenController@guardar_tp_doc');
					Route::post('update_tp_doc', 'AlmacenController@update_tp_doc');
					Route::get('anular_tp_doc/{id}', 'AlmacenController@anular_tp_doc');
					
				});
	
				Route::group(['as' => 'unidades-medida.', 'prefix' => 'unidades-medida'], function(){
	
					Route::get('index', 'AlmacenController@view_unid_med')->name('index');
					Route::get('listar_unidmed', 'AlmacenController@mostrar_unidades_med');
					Route::get('mostrar_unidmed/{id}', 'AlmacenController@mostrar_unid_med');
					Route::post('guardar_unidmed', 'AlmacenController@guardar_unid_med');
					Route::post('actualizar_unidmed', 'AlmacenController@update_unid_med');
					Route::get('anular_unidmed/{id}', 'AlmacenController@anular_unid_med');
					
				});
	
			});
	
		});
		
	
	});


	Route::get('anular_presup', 'ProyectosController@anular_presup');


	Route::get('config', function () {
		return view('configuracion/main');
	});
	Route::get('rrhh', function () {
		return view('rrhh/main');
	});
	// Route::get('almacen', function () {
	// 	return view('almacen/main');
	// });
	Route::get('equipo', function () {
		return view('equipo/main');
	});
	Route::get('proyectos', function () {
		return view('proyectos/main');
	});
	Route::get('contabilidad', function () {
		return view('contabilidad/main');
	});
	// Route::get('logistica', function () {
	// 	return view('logistica/main');
	// });
	Route::get('logistica', 'LogisticaController@view_main_logistica');
	



//Route::get('login', 'Tesoreria\LoginController@showLoginForm')->name('login');

	Route::group(['middleware' => ['roles:1,2,3,15,22,7,38'], 'prefix' => 'tesoreria', 'as' => 'tesoreria.'], function () {

		$roles['programador'] = [7, 38 //Programador
		];
		$roles['req_sol'] = [22, 38, //Asistnte Administrativo
			7, //Programador
		];
		$roles['gerente_general'] = [1,  //Gerente General
		];
		$roles['gerente'] = [1,  //Gerente General
			2,  //Gerente Administrativo
			3,  //Gerente Comercial
			15,  //Gerente Proyectos
		];
		$roles['pagos'] = [22, 38 //Asistnte Administrativo
		];
		$roles['asis_ger_general'] = [22, 38 //Asistnte Administrativo
		];

		$entrar['solicitud'] = array_merge($roles['programador'], $roles['req_sol'], $roles['gerente']);
		$entrar['pagos'] = array_merge($roles['programador'], $roles['asis_ger_general'], $roles['pagos'], $roles['gerente']);

		View::share('entrar', $entrar);
		View::share('roles', $roles);
		View::share('rolesSeccion', $entrar);


		//Route::get('login', 'Tesoreria\LoginController@showLoginForm')->name('login');

		Route::get('', 'TesoreriaController@index')->name('index');

		Route::group(['prefix' => 'solicitud', 'as' => 'solicitud.'], function () use ($roles, $entrar) {
			Route::get('tipo/{id_tipo}', 'Tesoreria\SolicitudController@index')->name('tipo')->middleware('roles:' . implode(',', $entrar['solicitud']));
			Route::post('state', 'Tesoreria\SolicitudController@cambiarEstadoAjax')->name('update.state');
		});

		Route::group(['middleware' => ['roles:' . implode(',', $entrar['pagos'])], 'prefix' => 'planillapagos', 'as' => 'planillapagos.'], function () {
			Route::any('ordinario', 'Tesoreria\PlanillaPagosController@index')->name('ordinario');
			Route::any('extraordinario', 'Tesoreria\PlanillaPagosController@index')->name('extraordinario');

			Route::post('state', 'Tesoreria\PlanillaPagosController@cambiarEstadoAjax')->name('update.state');
		});

		Route::resources(['proveedor' => 'Tesoreria\ProveedorController',

			'cajachica' => 'Tesoreria\CajaChicaController', 'cajachica_movimientos' => 'Tesoreria\CajaChicaMovimientosController', 'solicitud' => 'Tesoreria\SolicitudController', 'planillapagos' => 'Tesoreria\PlanillaPagosController', 'tcambio' => 'Tesoreria\TipoCambioController',]);

		Route::group(['prefix' => 'pdf', 'as' => 'pdf.'], function () {
			Route::any('vale_salida/{vale_id}', 'Tesoreria\PdfController@generateValeSalida')->name('vale_salida');
			Route::any('historial/cajachica/{cajachica_id}', 'Tesoreria\PdfController@generarHistorialCajaChica')->name('historial.cajachica');
		});

		/*
		Route::get('crear_tablas', 'TesoreriaController@crearTablas');
		Route::get('crear_uno', 'TesoreriaController@crearUno');
		Route::get('llenar_data', 'TesoreriaController@llenarDataInicial');*/
		Route::get('eliminar_tablas', 'TesoreriaController@eliminarTablas');

		Route::group(['middleware' => ['roles:' . implode(',', $roles['programador'])], 'prefix' => 'administracion', 'as' => 'administracion.'], function () {
			Route::get('sol_tipos', 'Tesoreria\SolicitudesTiposController@index')->name('solicitudes_tipos.index');
			Route::post('guardar', 'Tesoreria\SolicitudesTiposController@store')->name('solicitudes_tipos.store');
		});

		Route::group(['middleware' => ['roles:' . implode(',', $roles['programador'])], 'prefix' => 'configuraciones', 'as' => 'configuraciones.'], function () {
			Route::get('/', 'Tesoreria\ConfiguracionesController@index')->name('index');
		});
	});
	Route::group(['prefix' => 'ajax', 'as' => 'ajax.'], function () {
		Route::any('data/{tipo}/{identificador}', 'Tesoreria\AjaxController@getDataPersonaContribuyente')->name('data.persona_contribuyente');

		Route::any('proveedores', 'Tesoreria\AjaxController@getProveedores')->name('proveedores');

		Route::any('cajaschicas', 'Tesoreria\AjaxController@getCajasChicas')->name('cajaschicas');
		Route::any('cajachica/{cajachica_id}/movimientos', 'Tesoreria\AjaxController@getCajaChicaMovimientos')->name('cajachica.movimientos');
		Route::get('cajachica/{cajachica_id}/saldos', 'Tesoreria\AjaxController@getSaldoCajaChica')->name('cajachica.saldos');

		Route::get('almacenes/{empresa?}/{sede?}', 'Tesoreria\AjaxController@ajaxListaAlmacenes')->name('almacenes');
		Route::get('t_cambio/{moneda_id}/{fecha?}', 'Tesoreria\AjaxController@ajaxTipoCambio')->name('t_cambio');

		Route::get('solicitudes_subtipos/{tipo_id}', 'Tesoreria\AjaxController@getSolicitudesSubTipos')->name('sol_subtipos');
		Route::get('solicitudes', 'Tesoreria\AjaxController@getSolicitudes')->name('solicitudes');

		Route::get('planillapagos', 'Tesoreria\AjaxController@getPlanillaPagos')->name('planillapagos');

		Route::get('sedes/{empresa_id}', 'Tesoreria\AjaxController@getSedes')->name('sedes');
		Route::get('areas/{sede_id}', 'Tesoreria\AjaxController@getGruposAreas')->name('areas');

		Route::get('solicitudes_subtipos/{tipo_id}', 'Tesoreria\AjaxController@getSolicitudesSubTipos')->name('sol_subtipos');
		Route::get('solicitudes', 'Tesoreria\AjaxController@getSolicitudes')->name('solicitudes');

		
		Route::get('presupuesto/{area_id}', 'Tesoreria\AjaxController@getPresupuesto')->name('presupuesto');
	});


/* Configuración */
Route::post('update_password', 'ConfiguracionController@cambiar_clave');
Route::get('modulo', 'ConfiguracionController@view_modulos');
Route::get('listar_modulo', 'ConfiguracionController@mostrar_modulo_table');
Route::get('cargar_modulo/{id}', 'ConfiguracionController@mostrar_modulo_id');
Route::post('guardar_modulo', 'ConfiguracionController@guardar_modulo');
Route::post('editar_modulo', 'ConfiguracionController@actualizar_modulo');
Route::get('anular_modulo/{id}', 'ConfiguracionController@anular_modulo');
Route::get('cargar_modulos', 'ConfiguracionController@mostrar_modulos_combo');
Route::get('notas-lanzamiento', 'ConfiguracionController@view_notas_lanzamiento')->name('notas-lanzamiento');
Route::get('correo_coorporativo', 'ConfiguracionController@view_correo_coorporativo');
Route::get('mostrar_correo_coorporativo/{id}', 'ConfiguracionController@mostrar_correo_coorporativo');
Route::put('actualizar_correo_coorporativo', 'ConfiguracionController@actualizar_correo_coorporativo');
Route::post('guardar_correo_coorporativo', 'ConfiguracionController@guardar_correo_coorporativo');
Route::delete('anular_correo_coorporativo/{id}', 'ConfiguracionController@anular_correo_coorporativo');
Route::get('configuracion_socket', 'ConfiguracionController@view_configuracion_socket');
Route::put('actualizar_configuracion_socket', 'ConfiguracionController@actualizar_configuracion_socket');
Route::post('guardar_configuracion_socket', 'ConfiguracionController@guardar_configuracion_socket');
Route::delete('anular_configuracion_socket/{id}', 'ConfiguracionController@anular_configuracion_socket');


Route::get('mostrar_nota_lanzamiento/{id}', 'ConfiguracionController@mostrar_nota_lanzamiento');
Route::get('listar_detalle_notas_lanzamiento/{id}', 'ConfiguracionController@mostrar_detalle_notas_lanzamiento_table');
Route::put('actualizar_nota_lanzamiento', 'ConfiguracionController@updateNotaLanzamiento');
Route::get('mostrar_notas_lanzamiento_select', 'ConfiguracionController@mostrar_notas_lanzamiento_select');
Route::post('guardar_nota_lanzamiento', 'ConfiguracionController@guardarNotaLanzamiento');
Route::put('eliminar_nota_lanzamiento/{id_nota}', 'ConfiguracionController@eliminarNotaLanzamiento');
Route::get('mostrar_detalle_nota_lanzamiento/{id}', 'ConfiguracionController@mostrar_detalle_nota_lanzamiento');
Route::put('actualizar_detalle_nota_lanzamiento', 'ConfiguracionController@updateDetalleNotaLanzamiento');
Route::put('eliminar_detalle_nota_lanzamiento/{id_detalle_nota}', 'ConfiguracionController@eliminarDetalleNotaLanzamiento');



Route::get('aplicaciones', 'ConfiguracionController@view_aplicaciones');
Route::get('cargar_submodulos/{id}', 'ConfiguracionController@mostrar_submodulo_id');
Route::get('listar_aplicaciones', 'ConfiguracionController@mostrar_aplicaciones_table');
Route::get('cargar_aplicaciones/{id}', 'ConfiguracionController@mostrar_aplicaciones_id');
Route::post('guardar_aplicaciones', 'ConfiguracionController@guardar_aplicaciones');
Route::post('editar_aplicaciones', 'ConfiguracionController@actualizar_aplicaciones');
Route::get('anular_aplicaciones/{id}', 'ConfiguracionController@anular_aplicaciones');

Route::get('usuarios', 'ConfiguracionController@view_usuario');
Route::get('listar_usuarios', 'ConfiguracionController@mostrar_usuarios_table');
Route::post('guardar_usuarios', 'ConfiguracionController@guardar_usuarios');
// Route::post('editar_usuarios', 'ConfiguracionController@actualizar_usuarios');
Route::get('anular_usuarios/{id}', 'ConfiguracionController@anular_usuarios');
Route::get('cargar_aplicaciones_mod/{id}/{user}', 'ConfiguracionController@mostrar_aplicaciones_modulo');

Route::get('cargar_departamento', 'ConfiguracionController@select_departamento');
Route::get('cargar_provincia/{id}', 'ConfiguracionController@select_prov_dep');
Route::get('cargar_distrito/{id}', 'ConfiguracionController@select_dist_prov');
Route::get('cargar_estructura_org/{id}', 'ConfiguracionController@cargar_estructura_org'); /////// modal area

Route::get('traer_ubigeo/{id}', 'ConfiguracionController@traer_ubigeo');

Route::post('guardar_accesos', 'ConfiguracionController@guardar_accesos');
Route::post('editar_accesos', 'ConfiguracionController@actualizar_accesos');
Route::get('cargar_roles_usuario/{id}', 'ConfiguracionController@buscar_roles_usuario');


Route::get('gestionar-flujos', 'ConfiguracionController@view_gestionar_flujos');
Route::get('documentos', 'ConfiguracionController@view_docuemtos');
Route::get('listar-documentos', 'ConfiguracionController@mostrar_documento_table');
Route::get('cargar-documento/{id}', 'ConfiguracionController@mostrar_documento_id');
Route::post('guardar-documento', 'ConfiguracionController@guardar_documento');
Route::post('actualizar-documento', 'ConfiguracionController@actualizar_documento');
Route::get('anular-documento/{id}', 'ConfiguracionController@anular_documento');
Route::get('historial-aprobaciones', 'ConfiguracionController@view_historial_aprobaciones');
Route::get('listar-historial-aprobacion', 'ConfiguracionController@mostrar_historial_aprobacion');
// Route::get('mostrar-flujos', 'ConfiguracionController@mostrar_flujos');
Route::get('mostrar-flujos/{id_grupo_flujo}/{id_flujo}', 'ConfiguracionController@mostrar_flujos');
Route::put('actualizar_flujo', 'ConfiguracionController@updateFlujo');
Route::get('mostrar_grupo_flujo', 'ConfiguracionController@grupoFlujo');
Route::get('mostrar_roles_concepto', 'ConfiguracionController@rolesConcepto');
Route::get('mostrar_operacion', 'ConfiguracionController@operacion');
Route::get('mostrar_operacion/{id}', 'ConfiguracionController@operacionSelected');
Route::put('actualizar_operacion', 'ConfiguracionController@updateOperacion');
Route::get('mostrar-operaciones/{id_operacion}', 'ConfiguracionController@mostrar_operaciones');
Route::put('anular-flujo/{id_flujo}', 'ConfiguracionController@revokeFlujo');
Route::put('anular-operacion/{id_operacion}', 'ConfiguracionController@revokeOperacion');

Route::get('mostrar-tipo-documento', 'ConfiguracionController@mostrarTipoDocumento');
Route::get('mostrar-empresa', 'ConfiguracionController@mostrarEmpresa');
Route::get('mostrar-sede', 'ConfiguracionController@mostrarSede');
Route::get('mostrar-grupo', 'ConfiguracionController@mostrarGrupo');
Route::get('mostrar-area', 'ConfiguracionController@mostrarArea');

Route::get('mostrar-criterio-monto/{id_criterio_monto}', 'ConfiguracionController@mostrarCriterioMonto');
Route::get('mostrar-operador', 'ConfiguracionController@mostrarOperador');
Route::put('actualizar-criterio_monto', 'ConfiguracionController@updateCriterioMonto');
Route::post('guardar-criterio_monto', 'ConfiguracionController@saveCriterioMonto');
Route::get('mostrar-criterio-prioridad/{id_criterio_prioridad}', 'ConfiguracionController@mostrarCriterioPrioridad');
Route::put('actualizar-criterio_prioridad', 'ConfiguracionController@updateCriterioPrioridad');
Route::post('guardar-criterio_prioridad', 'ConfiguracionController@saveCriterioPrioridad');
Route::get('mostrar-grupo_criterio/{id_grupo_criterio}', 'ConfiguracionController@mostrarGrupoCriterio');
Route::get('mostrar-grupo-criterio-by-id_flujo/{id_flujo}', 'ConfiguracionController@mostrarGrupoCriterioByIdFlujo');
Route::get('mostrar-criterio/{id_flujo}/{id_grupo_criterio}', 'ConfiguracionController@mostrarCriterio');
Route::post('asignar_criterio', 'ConfiguracionController@saveAignarCriterio');
Route::put('asignar_criterio', 'ConfiguracionController@updateAsignarCriterio');
Route::post('grupo_criterio', 'ConfiguracionController@saveGrupoCriterio');
Route::put('grupo_criterio', 'ConfiguracionController@updateGrupoCriterio');


/* Recursos Humanos */
Route::get('rrhh', 'RecursosHumanosController@view_main');
Route::get('persona', 'RecursosHumanosController@view_persona');
Route::get('postulante', 'RecursosHumanosController@view_postulante');
Route::get('trabajador', 'RecursosHumanosController@view_trabajador');
Route::get('cargo', 'RecursosHumanosController@view_cargo');

Route::get('periodo', 'RecursosHumanosController@view_periodo');
Route::get('tareo', 'RecursosHumanosController@view_tareo');
Route::get('asistencia', 'RecursosHumanosController@view_asistencia');
Route::get('planilla', 'RecursosHumanosController@view_planilla');
Route::get('horario', 'RecursosHumanosController@view_horario');
Route::get('tolerancia', 'RecursosHumanosController@view_tolerancia');
Route::get('est_civil', 'RecursosHumanosController@view_est_civil');
Route::get('cond_derecho_hab', 'RecursosHumanosController@view_cond_derecho_hab');
Route::get('niv_estudios', 'RecursosHumanosController@view_niv_estudio');
Route::get('carreras', 'RecursosHumanosController@view_carrera');
Route::get('tipo_trabajador', 'RecursosHumanosController@view_tipo_trabajador');
Route::get('tipo_contrato', 'RecursosHumanosController@view_tipo_contrato');
Route::get('modalidad', 'RecursosHumanosController@view_modalidad');
Route::get('concepto_rol', 'RecursosHumanosController@view_concepto_rol');
Route::get('cat_ocupacional', 'RecursosHumanosController@view_cat_ocupacional');
Route::get('tipo_planilla', 'RecursosHumanosController@view_tipo_planilla');
Route::get('tipo_merito', 'RecursosHumanosController@view_tipo_merito');
Route::get('tipo_demerito', 'RecursosHumanosController@view_tipo_demerito');
Route::get('tipo_bonificacion', 'RecursosHumanosController@view_tipo_bonificacion');
Route::get('tipo_descuento', 'RecursosHumanosController@view_tipo_descuento');
Route::get('tipo_retencion', 'RecursosHumanosController@view_tipo_retencion');
Route::get('tipo_aportes', 'RecursosHumanosController@view_tipo_aportes');
Route::get('derecho_hab', 'RecursosHumanosController@view_derecho_hab');
Route::get('pension', 'RecursosHumanosController@view_pension');
Route::get('merito', 'RecursosHumanosController@view_merito');
Route::get('demerito', 'RecursosHumanosController@view_sancion');
Route::get('salidas', 'RecursosHumanosController@view_salidas');
Route::get('prestamos', 'RecursosHumanosController@view_prestamo');
Route::get('vacaciones', 'RecursosHumanosController@view_vacaciones');
Route::get('licencia', 'RecursosHumanosController@view_licencia');
Route::get('horas_ext', 'RecursosHumanosController@view_horas_ext');
Route::get('cese', 'RecursosHumanosController@view_cese');

Route::get('bonificacion', 'RecursosHumanosController@view_bonificacion');
Route::get('descuento', 'RecursosHumanosController@view_descuento');
Route::get('retencion', 'RecursosHumanosController@view_retencion');
Route::get('aportacion', 'RecursosHumanosController@view_aportacion');
Route::get('reintegro', 'RecursosHumanosController@view_reintegro');

Route::get('buscar_trabajador_id/{id}/{emp}/{sede}', 'RecursosHumanosController@buscar_trabajador_id');

Route::post('cargar_csv', 'RecursosHumanosController@cargar_horario_reloj');
Route::get('cargar_data_diaria/{empre}/{sede}/{tipo}/{fecha}', 'RecursosHumanosController@cargar_horario_diario');
Route::post('grabar_asistencia', 'RecursosHumanosController@grabar_asistencia_diaria');
Route::post('grabar_asistencia_final', 'RecursosHumanosController@grabar_asistencia_final');
Route::get('cargar_asistencia/{empre}/{sede}/{tipo}/{ini}/{fin}', 'RecursosHumanosController@cargar_asistencia');
Route::get('mostrar_permiso_asistencia/{id}/{fecha}', 'RecursosHumanosController@permisos_asistencia');
Route::get('reporte_tardanzas/{from}/{to}/{empresa}/{sede}', 'RecursosHumanosController@reporte_tardanza');
Route::get('cargar_data_remun/{emp}/{plani}/{mes}/{anio}/{type}/{empleado}/{grupal}', 'RecursosHumanosController@cargar_remuneraciones'); // SOLO PRUEBAS
Route::get('cargar_data_spcc/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@remuneracion_spcc'); // SOLO SPCC
Route::get('procesar_planilla/{emp}/{plani}/{mes}', 'RecursosHumanosController@procesar_planilla'); ///// PLANILLA
Route::get('generar_planilla_pdf/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@generar_planilla_pdf');
Route::get('generar_planilla_spcc_pdf/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@generar_planilla_spcc_pdf');

Route::get('reporte_planilla_xls/{emp}/{plani}/{mes}/{anio}/{filter}/{grupal}', 'RecursosHumanosController@reporte_planilla_xls');
Route::get('reporte_planilla_spcc_xls/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@reporte_planilla_spcc_xls'); // SOLO SPCC
Route::get('reporte_planilla_grupal_xls/{plani}/{mes}/{anio}/{grupo}', 'RecursosHumanosController@reporte_planilla_grupal_xls');//nueva planilla
Route::get('generar_vacaciones/{id}', 'RecursosHumanosController@generar_vacaciones_pdf');
Route::get('reporte_planilla_trabajador_pdf/{emp}/{plani}/{mes}/{anio}/{trab}', 'RecursosHumanosController@reporte_planilla_trabajador_xls');
Route::get('generar_pdf_trabajdor/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@generar_pdf_trabajdor'); // correo rrhh
Route::get('reporte_gastos/{plani}/{mes}/{anio}', 'RecursosHumanosController@reporte_gastos');

Route::get('listar_personas', 'RecursosHumanosController@mostrar_persona_table');
Route::get('cargar_persona/{id}', 'RecursosHumanosController@mostrar_persona_id');
Route::post('guardar_persona', 'RecursosHumanosController@guardar_persona');
Route::post('editar_persona', 'RecursosHumanosController@actualizar_persona');
Route::get('anular_persona/{id}', 'RecursosHumanosController@anular_persona');
Route::get('digitos_documento/{id}', 'RecursosHumanosController@mostrar_longitud_doc');

Route::get('listar_postulantes', 'RecursosHumanosController@mostrar_postulante_table');
Route::get('cargar_postulante/{id}', 'RecursosHumanosController@mostrar_postulante_id');
Route::get('cargar_postulante_dni/{dni}', 'RecursosHumanosController@mostrar_postulante_dni');
Route::post('guardar_informacion_postulante', 'RecursosHumanosController@guardar_informacion_postulante');
Route::post('editar_informacion_postulante', 'RecursosHumanosController@actualizar_informacion_postulante');
Route::get('listar_formacion_acad/{id}', 'RecursosHumanosController@mostrar_formacion_acad');
Route::post('guardar_formacion_academica', 'RecursosHumanosController@guardar_formacion_academica');
Route::post('editar_formacion_academica', 'RecursosHumanosController@actualizar_formacion_academica'); //FALTA ANULAR
Route::get('cargar_formacion_click/{id}', 'RecursosHumanosController@mostrar_formacion_click');
Route::get('listar_experiencia_lab/{id}', 'RecursosHumanosController@mostrar_experiencia_lab');
Route::post('guardar_experiencia_laboral', 'RecursosHumanosController@guardar_experiencia_laboral');
Route::post('editar_experiencia_laboral', 'RecursosHumanosController@actualizar_experiencia_laboral'); //FALTA ANULAR
Route::get('cargar_experiencia_click/{id}', 'RecursosHumanosController@mostrar_experiencia_click');
Route::get('listar_datos_extras/{id}', 'RecursosHumanosController@mostrar_datos_extras');
Route::post('guardar_datos_extras', 'RecursosHumanosController@guardar_dextra_postulante'); //FALTA ANULAR
Route::get('listar_observaciones/{id}', 'RecursosHumanosController@mostrar_observaciones');
Route::post('guardar_observacion', 'RecursosHumanosController@guardar_observacion_postulante');
Route::post('editar_observacion', 'RecursosHumanosController@actualizar_observacion_postulante'); //FALTA ANULAR
Route::get('cargar_observacion_click/{id}', 'RecursosHumanosController@mostrar_observacion_click');

Route::get('listar_trabajador', 'RecursosHumanosController@mostrar_trabajador_table');
Route::get('cargar_trabajador/{id}', 'RecursosHumanosController@mostrar_trabajador_id');
Route::get('cargar_trabajador_dni/{dni}', 'RecursosHumanosController@mostrar_trabajador_dni');
Route::post('guardar_alta_trabajador', 'RecursosHumanosController@guardar_alta_trabajador');
Route::post('editar_alta_trabajador', 'RecursosHumanosController@actualizar_alta_trabajador'); //FALTA ANULAR
Route::get('listar_contrato_trab/{id}', 'RecursosHumanosController@mostrar_contrato_trab');
Route::post('guardar_contrato_trabajador', 'RecursosHumanosController@guardar_contrato_trabajador');
Route::post('editar_contrato_trabajador', 'RecursosHumanosController@actualizar_contrato_trabajador'); //FALTA ANULAR
Route::get('cargar_contrato_click/{id}', 'RecursosHumanosController@mostrar_contrato_click');
Route::get('listar_rol_trab/{id}', 'RecursosHumanosController@mostrar_rol_trab');
Route::post('guardar_rol_trabajador', 'RecursosHumanosController@guardar_rol_trabajador');
Route::post('editar_rol_trabajador', 'RecursosHumanosController@actualizar_rol_trabajador'); //FALTA ANULAR
Route::get('actualizar_cierre_rol/{id}/{fecha}', 'RecursosHumanosController@actualizar_cierre_rol');
Route::get('cargar_rol_click/{id}', 'RecursosHumanosController@mostrar_rol_click');
Route::get('listar_cuentas_trab/{id}', 'RecursosHumanosController@mostrar_cuentas_trab');
Route::post('guardar_cuentas_trabajador', 'RecursosHumanosController@guardar_cuentas_trabajador');
Route::post('editar_cuentas_trabajador', 'RecursosHumanosController@actualizar_cuentas_trabajador'); //FALTA ANULAR
Route::get('cargar_cuenta_click/{id}', 'RecursosHumanosController@mostrar_cuenta_click');

Route::get('mostrar_combos_emp/{id}', 'RecursosHumanosController@buscar_sede');
Route::get('mostrar_grupo_sede/{id}', 'RecursosHumanosController@buscar_grupo');
Route::get('mostrar_area_grupo/{id}', 'RecursosHumanosController@buscar_area');

Route::get('listar_cargo', 'RecursosHumanosController@mostrar_cargo_table');
Route::get('cargar_cargo/{id}', 'RecursosHumanosController@mostrar_cargo_id');
Route::post('guardar_cargo', 'RecursosHumanosController@guardar_cargo');
Route::post('editar_cargo', 'RecursosHumanosController@actualizar_cargo');
Route::get('anular_cargo/{id}', 'RecursosHumanosController@anular_cargo');

Route::get('cargar_trabajador_dni_esc/{dni}', 'RecursosHumanosController@buscar_trab_dni');
Route::get('cargar_persona_dni_esc/{dni}', 'RecursosHumanosController@buscar_persona_dni');

Route::get('listar_merito/{id}', 'RecursosHumanosController@mostrar_merito_table');
Route::get('cargar_merito/{id}', 'RecursosHumanosController@mostrar_merito_id');
Route::post('guardar_merito', 'RecursosHumanosController@guardar_merito');
Route::post('editar_merito', 'RecursosHumanosController@actualizar_merito');
Route::get('anular_merito/{id}', 'RecursosHumanosController@anular_merito');

Route::get('listar_sancion/{id}', 'RecursosHumanosController@mostrar_sancion_table');
Route::get('listar_sancion', 'RecursosHumanosController@mostrar_sancion_table');
Route::get('cargar_sancion/{id}', 'RecursosHumanosController@mostrar_sancion_id');
Route::post('guardar_sancion', 'RecursosHumanosController@guardar_sancion');
Route::post('editar_sancion', 'RecursosHumanosController@actualizar_sancion');
Route::get('anular_sancion/{id}', 'RecursosHumanosController@anular_sancion');

Route::get('listar_derecho_hab/{id}', 'RecursosHumanosController@mostrar_derechohabiente_table');
Route::get('cargar_derecho_hab/{id}', 'RecursosHumanosController@mostrar_derechohabiente_id');
Route::post('guardar_derecho_hab', 'RecursosHumanosController@guardar_derecho_habiente');
Route::post('editar_derecho_hab', 'RecursosHumanosController@actualizar_derecho_habiente');
Route::get('anular_derecho_hab/{id}', 'RecursosHumanosController@anular_derecho_habiente');

Route::get('listar_salidas/{id}', 'RecursosHumanosController@mostrar_salidas_table');
Route::get('cargar_salidas/{id}', 'RecursosHumanosController@mostrar_salidas_id');
Route::post('guardar_salidas', 'RecursosHumanosController@guardar_salidas');
Route::post('editar_salidas', 'RecursosHumanosController@actualizar_salidas');
Route::get('anular_salidas/{id}', 'RecursosHumanosController@anular_salidas');

Route::get('listar_prestamo/{id}', 'RecursosHumanosController@mostrar_prestamo_table');
Route::get('cargar_prestamo/{id}', 'RecursosHumanosController@mostrar_prestamo_id');
Route::post('guardar_prestamo', 'RecursosHumanosController@guardar_prestamo');
Route::post('editar_prestamo', 'RecursosHumanosController@actualizar_prestamo');
Route::get('anular_prestamo/{id}', 'RecursosHumanosController@anular_prestamo');

Route::get('listar_vacaciones/{id}', 'RecursosHumanosController@mostrar_vacaciones_table');
Route::get('cargar_vacaciones/{id}', 'RecursosHumanosController@mostrar_vacaciones_id');
Route::post('guardar_vacaciones', 'RecursosHumanosController@guardar_vacaciones');
Route::post('editar_vacaciones', 'RecursosHumanosController@actualizar_vacaciones');
Route::get('anular_vacaciones/{id}', 'RecursosHumanosController@anular_vacaciones');

Route::get('listar_licencia/{id}', 'RecursosHumanosController@mostrar_licencia_table');
Route::get('cargar_licencia/{id}', 'RecursosHumanosController@mostrar_licencia_id');
Route::post('guardar_licencia', 'RecursosHumanosController@guardar_licencia');
Route::post('editar_licencia', 'RecursosHumanosController@actualizar_licencia');
Route::get('anular_licencia/{id}', 'RecursosHumanosController@anular_licencia');

Route::get('listar_horas_ext/{id}', 'RecursosHumanosController@mostrar_horas_ext_table');
Route::get('cargar_horas_ext/{id}', 'RecursosHumanosController@mostrar_horas_ext_id');
Route::post('guardar_horas_ext', 'RecursosHumanosController@guardar_horas_ext');
Route::post('editar_horas_ext', 'RecursosHumanosController@actualizar_horas_ext');
Route::get('anular_horas_ext/{id}', 'RecursosHumanosController@anular_horas_ext');

Route::post('guardar_cese', 'RecursosHumanosController@guardar_cese');

Route::get('listar_periodo', 'RecursosHumanosController@mostrar_periodo_table');
Route::get('cargar_periodo/{id}', 'RecursosHumanosController@mostrar_periodo_id');
Route::post('guardar_periodo', 'RecursosHumanosController@guardar_periodo');
Route::post('editar_periodo', 'RecursosHumanosController@actualizar_periodo');
Route::get('anular_periodo/{id}', 'RecursosHumanosController@anular_periodo');

Route::get('listar_horarios', 'RecursosHumanosController@mostrar_horarios_table');
Route::get('cargar_horario/{id}', 'RecursosHumanosController@mostrar_horario_id');
Route::post('guardar_horario', 'RecursosHumanosController@guardar_horario');
Route::post('editar_horario', 'RecursosHumanosController@actualizar_horario');
Route::get('anular_horario/{id}', 'RecursosHumanosController@anular_horario');

Route::get('listar_tolerancias', 'RecursosHumanosController@mostrar_tolerancia_table');
Route::get('cargar_tolerancia/{id}', 'RecursosHumanosController@mostrar_tolerancia_id');
Route::post('guardar_tolerancia', 'RecursosHumanosController@guardar_tolerancia');
Route::post('editar_tolerancia', 'RecursosHumanosController@actualizar_tolerancia');
Route::get('anular_tolerancia/{id}', 'RecursosHumanosController@anular_tolerancia');

Route::get('listar_estado_civil', 'RecursosHumanosController@mostrar_estado_civil_table');
Route::get('cargar_est_civil/{id}', 'RecursosHumanosController@mostrar_est_civil_id');
Route::post('guardar_est_civil', 'RecursosHumanosController@guardar_estado_civil');
Route::post('editar_est_civil', 'RecursosHumanosController@actualizar_estado_civil');
Route::get('anular_est_civil/{id}', 'RecursosHumanosController@anular_estado_civil');

Route::get('listar_condi_derecho_hab', 'RecursosHumanosController@mostrar_condiciondh_table');
Route::get('cargar_cond_derecho_hab/{id}', 'RecursosHumanosController@mostrar_condiciondh_id');
Route::post('guardar_cond_derecho_hab', 'RecursosHumanosController@guardar_condicion_dh');
Route::post('editar_cond_derecho_hab', 'RecursosHumanosController@actualizar_condicion_dh');
Route::get('anular_cond_derecho_hab/{id}', 'RecursosHumanosController@anular_condicion_dh');

Route::get('listar_nivel_estudio', 'RecursosHumanosController@mostrar_nivel_estudio_table');
Route::get('cargar_nivel_estudio/{id}', 'RecursosHumanosController@mostrar_nivel_estudios_id');
Route::post('guardar_nivel_estudio', 'RecursosHumanosController@guardar_nivel_estudio');
Route::post('editar_nivel_estudio', 'RecursosHumanosController@actualizar_nivel_estudio');
Route::get('anular_nivel_estudio/{id}', 'RecursosHumanosController@anular_nivel_estudio');

Route::get('listar_carrera', 'RecursosHumanosController@mostrar_carrera_table');
Route::get('cargar_carrera/{id}', 'RecursosHumanosController@mostrar_carrera_id');
Route::post('guardar_carrera', 'RecursosHumanosController@guardar_carrera');
Route::post('editar_carrera', 'RecursosHumanosController@actualizar_carrera');
Route::get('anular_carrera/{id}', 'RecursosHumanosController@anular_carrera');

Route::get('listar_tipo_trabajador', 'RecursosHumanosController@mostrar_tipo_trabajador_table');
Route::get('cargar_tipo_trabajador/{id}', 'RecursosHumanosController@mostrar_tipo_trabajador_id');
Route::post('guardar_tipo_trabajador', 'RecursosHumanosController@guardar_tipo_trabajador');
Route::post('editar_tipo_trabajador', 'RecursosHumanosController@actualizar_tipo_trabajador');
Route::get('anular_tipo_trabajador/{id}', 'RecursosHumanosController@anular_tipo_trabajador');

Route::get('listar_tipo_contrato', 'RecursosHumanosController@mostrar_tipo_contrato_table');
Route::get('cargar_tipo_contrato/{id}', 'RecursosHumanosController@mostrar_tipo_contrato_id');
Route::post('guardar_tipo_contrato', 'RecursosHumanosController@guardar_tipo_contrato');
Route::post('editar_tipo_contrato', 'RecursosHumanosController@actualizar_tipo_contrato');
Route::get('anular_tipo_contrato/{id}', 'RecursosHumanosController@anular_tipo_contrato');

Route::get('listar_modalidad', 'RecursosHumanosController@mostrar_modalidad_table');
Route::get('cargar_modalidad/{id}', 'RecursosHumanosController@mostrar_modalidad_id');
Route::post('guardar_modalidad', 'RecursosHumanosController@guardar_modalidad');
Route::post('editar_modalidad', 'RecursosHumanosController@actualizar_modalidad');
Route::get('anular_modalidad/{id}', 'RecursosHumanosController@anular_modalidad');

Route::get('listar_concepto_rol', 'RecursosHumanosController@mostrar_concepto_rol_table');
Route::get('cargar_concepto_rol/{id}', 'RecursosHumanosController@mostrar_concepto_rol_id');
Route::post('guardar_concepto_rol', 'RecursosHumanosController@guardar_concepto_rol');
Route::post('editar_concepto_rol', 'RecursosHumanosController@actualizar_concepto_rol');
Route::get('anular_concepto_rol/{id}', 'RecursosHumanosController@anular_concepto_rol');

Route::get('listar_categoria_ocupacional', 'RecursosHumanosController@mostrar_categoria_ocupacional');
Route::get('cargar_categoria_ocupacional/{id}', 'RecursosHumanosController@mostrar_categoria_ocupacional_id');
Route::post('guardar_categoria_ocupacional', 'RecursosHumanosController@guardar_categoria_ocupacional');
Route::post('editar_categoria_ocupacional', 'RecursosHumanosController@actualizar_categoria_ocupacional');
Route::get('anular_categoria_ocupacional/{id}', 'RecursosHumanosController@anular_categoria_ocupacional');

Route::get('listar_tipo_planilla', 'RecursosHumanosController@mostrar_tipo_planilla_table');
Route::get('cargar_tipo_planilla/{id}', 'RecursosHumanosController@mostrar_tipo_planilla_id');
Route::post('guardar_tipo_planilla', 'RecursosHumanosController@guardar_tipo_planilla');
Route::post('editar_tipo_planilla', 'RecursosHumanosController@actualizar_tipo_planilla');
Route::get('anular_tipo_planilla/{id}', 'RecursosHumanosController@anular_tipo_planilla');

Route::get('listar_tipo_merito', 'RecursosHumanosController@mostrar_tipo_merito_table');
Route::get('cargar_tipo_merito/{id}', 'RecursosHumanosController@mostrar_tipo_merito_id');
Route::post('guardar_tipo_merito', 'RecursosHumanosController@guardar_tipo_merito');
Route::post('editar_tipo_merito', 'RecursosHumanosController@actualizar_tipo_merito');
Route::get('anular_tipo_merito/{id}', 'RecursosHumanosController@anular_tipo_merito');

Route::get('listar_tipo_demerito', 'RecursosHumanosController@mostrar_tipo_demerito_table');
Route::get('cargar_tipo_demerito/{id}', 'RecursosHumanosController@mostrar_tipo_demerito_id');
Route::post('guardar_tipo_demerito', 'RecursosHumanosController@guardar_tipo_demerito');
Route::post('editar_tipo_demerito', 'RecursosHumanosController@actualizar_tipo_demerito');
Route::get('anular_tipo_demerito/{id}', 'RecursosHumanosController@anular_tipo_demerito');

Route::get('listar_tipo_bonificacion', 'RecursosHumanosController@mostrar_tipo_bonificacion_table');
Route::get('cargar_tipo_bonificacion/{id}', 'RecursosHumanosController@mostrar_tipo_bonificacion_id');
Route::post('guardar_tipo_bonificacion', 'RecursosHumanosController@guardar_tipo_bonificacion');
Route::post('editar_tipo_bonificacion', 'RecursosHumanosController@actualizar_tipo_bonificacion');
Route::get('anular_tipo_bonificacion/{id}', 'RecursosHumanosController@anular_tipo_bonificacion');

Route::get('listar_tipo_descuento', 'RecursosHumanosController@mostrar_tipo_descuento_table');
Route::get('cargar_tipo_descuento/{id}', 'RecursosHumanosController@mostrar_tipo_descuento_id');
Route::post('guardar_tipo_descuento', 'RecursosHumanosController@guardar_tipo_descuento');
Route::post('editar_tipo_descuento', 'RecursosHumanosController@actualizar_tipo_descuento');
Route::get('anular_tipo_descuento/{id}', 'RecursosHumanosController@anular_tipo_descuento');

Route::get('listar_tipo_retencion', 'RecursosHumanosController@mostrar_tipo_retencion_table');
Route::get('cargar_tipo_retencion/{id}', 'RecursosHumanosController@mostrar_tipo_retencion_id');
Route::post('guardar_tipo_retencion', 'RecursosHumanosController@guardar_tipo_retencion');
Route::post('editar_tipo_retencion', 'RecursosHumanosController@actualizar_tipo_retencion');
Route::get('anular_tipo_retencion/{id}', 'RecursosHumanosController@anular_tipo_retencion');

Route::get('listar_tipo_aporte', 'RecursosHumanosController@mostrar_tipo_aporte_table');
Route::get('cargar_tipo_aporte/{id}', 'RecursosHumanosController@mostrar_tipo_aporte_id');
Route::post('guardar_tipo_aporte', 'RecursosHumanosController@guardar_tipo_aporte');
Route::post('editar_tipo_aporte', 'RecursosHumanosController@actualizar_tipo_aporte');
Route::get('anular_tipo_aporte/{id}', 'RecursosHumanosController@anular_tipo_aporte');

Route::get('listar_pension', 'RecursosHumanosController@mostrar_pension_table');
Route::get('cargar_pension/{id}', 'RecursosHumanosController@mostrar_pension_id');
Route::post('guardar_pension', 'RecursosHumanosController@guardar_pension');
Route::post('editar_pension', 'RecursosHumanosController@actualizar_pension');
Route::get('anular_pension/{id}', 'RecursosHumanosController@anular_pension');

Route::get('cargar_regimen/{id}', 'RecursosHumanosController@cargar_regimen');

Route::get('listar_bonificacion/{id}', 'RecursosHumanosController@mostrar_bonificacion_table');
Route::get('cargar_bonificacion/{id}', 'RecursosHumanosController@mostrar_bonificacion_id');
Route::post('guardar_bonificacion', 'RecursosHumanosController@guardar_bonificacion');
Route::post('editar_bonificacion', 'RecursosHumanosController@actualizar_bonificacion');
Route::get('anular_bonificacion/{id}', 'RecursosHumanosController@anular_bonificacion');

Route::get('listar_descuento/{id}', 'RecursosHumanosController@mostrar_descuento_table');
Route::get('cargar_descuento/{id}', 'RecursosHumanosController@mostrar_descuento_id');
Route::post('guardar_descuento', 'RecursosHumanosController@guardar_descuento');
Route::post('editar_descuento', 'RecursosHumanosController@actualizar_descuento');
Route::get('anular_descuento/{id}', 'RecursosHumanosController@anular_descuento');

Route::get('listar_retencion/{id}', 'RecursosHumanosController@mostrar_retencion_table');
Route::get('cargar_retencion/{id}', 'RecursosHumanosController@mostrar_retencion_id');
Route::post('guardar_retencion', 'RecursosHumanosController@guardar_retencion');
Route::post('editar_retencion', 'RecursosHumanosController@actualizar_retencion');
Route::get('anular_retencion/{id}', 'RecursosHumanosController@anular_retencion');

Route::get('listar_aportacion', 'RecursosHumanosController@mostrar_aportacion_table');
Route::get('cargar_aportacion/{id}', 'RecursosHumanosController@mostrar_aportacion_id');
Route::post('guardar_aportacion', 'RecursosHumanosController@guardar_aportacion');
Route::post('editar_aportacion', 'RecursosHumanosController@actualizar_aportacion');
Route::get('anular_aportacion/{id}', 'RecursosHumanosController@anular_aportacion');

Route::get('listar_reintegro/{id}', 'RecursosHumanosController@mostrar_reintegro_table');
Route::get('cargar_reintegro/{id}', 'RecursosHumanosController@mostrar_reintegro_id');
Route::post('guardar_reintegro', 'RecursosHumanosController@guardar_reintegro');
Route::post('editar_reintegro', 'RecursosHumanosController@actualizar_reintegro');
Route::get('anular_reintegro/{id}', 'RecursosHumanosController@anular_reintegro');

// REPORTE RRHH
Route::get('datos_personal', 'RecursosHumanosController@view_cv');
Route::get('busqueda_postulante', 'RecursosHumanosController@view_busq_postu');
Route::get('grupo_trabajador', 'RecursosHumanosController@view_grupo_trab');
Route::get('cumple', 'RecursosHumanosController@view_cumple');
Route::get('datos_generales', 'RecursosHumanosController@view_datos_generales');
Route::get('reporte_afp', 'RecursosHumanosController@view_reporte_afp');

Route::get('buscar_postulantes/{filtro}/{desc}', 'RecursosHumanosController@buscar_postulantes_reporte');
Route::get('buscar_grupo_trabajador/{emp}/{grupo}', 'RecursosHumanosController@grupo_trabajador_reporte');
Route::get('buscar_cumple/{filtro}', 'RecursosHumanosController@onomastico_reporte');
Route::get('cargar_detalle_postulante/{id}', 'RecursosHumanosController@cargar_detalle_postulante');
Route::get('buscar_datos_generales/{tipo}', 'RecursosHumanosController@datos_generales_reporte');
Route::get('buscar_reporte_afp', 'RecursosHumanosController@reporte_afp');

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////


// /* Almacen */

/**Producto */

/* Producto Ubicacion */


/* ProductoUbicacion Serie */


/* Servicios */

Route::get('tipoServ', 'AlmacenController@view_tipo_servicio');
Route::get('listar_tipoServ', 'AlmacenController@mostrar_tp_servicios');
Route::get('mostrar_tipoServ/{id}', 'AlmacenController@mostrar_tp_servicio');
Route::post('guardar_tipoServ', 'AlmacenController@guardar_tp_servicio');
Route::post('actualizar_tipoServ', 'AlmacenController@update_tp_servicio');
Route::get('anular_tipoServ/{id}', 'AlmacenController@anular_tp_servicio');
Route::get('servicio', 'AlmacenController@view_servicio');
Route::get('listar_servicio', 'AlmacenController@mostrar_servicios');
Route::get('mostrar_servicio/{id}', 'AlmacenController@mostrar_servicio');
Route::post('guardar_servicio', 'AlmacenController@guardar_servicio');
Route::post('actualizar_servicio', 'AlmacenController@update_servicio');
Route::get('anular_servicio/{id}', 'AlmacenController@anular_servicio');

/**Guia Compra */




// Route::get('requerimientosPendientes', 'DistribucionController@view_requerimientosPendientes');


Route::get('grupoDespachos', 'DistribucionController@view_grupoDespachos');




/**Guia Venta */


Route::get('listar_occ', 'AlmacenController@listar_occ');
Route::get('listar_kardex', 'AlmacenController@listar_kardex');
Route::get('migrar_docs_compra', 'AlmacenController@migrar_docs_compra');


Route::get('listar_occ', 'LogisticaController@listar_occ');
Route::get('listar_occ_pendientes', 'LogisticaController@listar_occ_pendientes');
Route::get('copiar_items_occ/{id}', 'LogisticaController@copiar_items_occ');
Route::get('copiar_items_occ_doc/{id}/{doc}', 'AlmacenController@copiar_items_occ_doc');
Route::get('next_serie_numero_doc/{id}/{tp}', 'AlmacenController@next_serie_numero_doc');

Route::get('actualiza_totales_doc_ven/{id}', 'AlmacenController@actualiza_totales_doc_ven');



/**Doc Venta */

Route::get('doc_venta', 'AlmacenController@view_doc_venta');
Route::get('listar_docs_venta', 'AlmacenController@listar_docs_venta');
Route::get('mostrar_doc_venta/{id}', 'AlmacenController@mostrar_doc_venta');
Route::post('guardar_doc_venta', 'AlmacenController@guardar_doc_venta');
Route::post('actualizar_doc_venta', 'AlmacenController@update_doc_venta');
Route::get('anular_doc_venta/{id}', 'AlmacenController@anular_doc_venta');
Route::get('listar_guias_emp/{id}', 'AlmacenController@listar_guias_emp');
Route::get('guardar_docven_items_guia/{guia}/{id}', 'AlmacenController@guardar_docven_items_guia');
Route::get('listar_docven_items/{id}', 'AlmacenController@listar_docven_items');
Route::get('listar_docven_guias/{id}', 'AlmacenController@listar_docven_guias');
Route::get('anular_guiaven/{id}/{guia}', 'AlmacenController@anular_guiaven');
Route::post('guardar_docven_detalle', 'AlmacenController@guardar_docven_detalle');
Route::post('update_docven_detalle', 'AlmacenController@update_docven_detalle');

Route::get('saldo_actual/{id}/{ubi}', 'AlmacenController@saldo_actual');
Route::get('costo_promedio/{id}/{ubi}', 'AlmacenController@costo_promedio');
Route::post('tipo_cambio/{fecha}', 'AlmacenController@tipo_cambio');
Route::get('cola_atencion', 'AlmacenController@view_cola_atencion');
Route::get('listar_requerimientos', 'AlmacenController@listar_requerimientos');
Route::get('listar_items_req/{id}', 'AlmacenController@listar_items_req');
Route::post('generar_salida', 'AlmacenController@generar_salida');
Route::post('guardar_guia_ven', 'AlmacenController@guardar_guia_ven');
Route::get('imprimir_salida/{id}', 'AlmacenController@imprimir_salida');
Route::get('req_almacen/{id}', 'AlmacenController@req_almacen');



/**Kardex de Almacen */
Route::get('movimientos_producto/{id}/{prod}', 'AlmacenController@movimientos_producto');
Route::get('verifica_posiciones/{id}', 'AlmacenController@verifica_posiciones');

Route::get('saldo_producto/{id}/{prod}/{fec}', 'AlmacenController@saldo_producto');



/* Proyectos */

Route::get('html_presupuesto_proyecto/{id}', 'ProyectosController@html_presupuesto_proyecto');




// Route::get('mostrar_todo_presint/{id}', 'ProyectosController@mostrar_presupuesto_completo');
Route::get('suma_partidas_ci/{padre}/{id}', 'ProyectosController@suma_partidas_ci');
Route::get('suma_partidas_gg/{padre}/{id}', 'ProyectosController@suma_partidas_gg');
Route::get('actualiza_totales/{id}', 'ProyectosController@actualiza_totales');


Route::get('cd/{id}', 'ProyectosController@cd');








Route::get('listar_acus_cd/{id}', 'ProyectosController@listar_acus_cd');

Route::get('solo_cd/{id}', 'ProyectosController@solo_cd');
Route::get('actualiza_padres/{id}/{padre}', 'ProyectosController@actualiza_padres');

Route::post('add_unid_med', 'AlmacenController@add_unid_med');

/* Maquinaria y Equipos */
Route::get('equi_tipo', 'EquipoController@view_equi_tipo');
Route::get('listar_equi_tipos', 'EquipoController@mostrar_equi_tipos');
Route::get('mostrar_equi_tipo/{id}', 'EquipoController@mostrar_equi_tipo');
Route::post('guardar_equi_tipo', 'EquipoController@guardar_equi_tipo');
Route::post('actualizar_equi_tipo', 'EquipoController@update_equi_tipo');
Route::get('anular_equi_tipo/{id}', 'EquipoController@anular_equi_tipo');
Route::get('equi_cat', 'EquipoController@view_equi_cat');
Route::get('listar_equi_cats', 'EquipoController@mostrar_equi_cats');
Route::get('mostrar_equi_cat/{id}', 'EquipoController@mostrar_equi_cat');
Route::post('guardar_equi_cat', 'EquipoController@guardar_equi_cat');
Route::post('actualizar_equi_cat', 'EquipoController@update_equi_cat');
Route::get('anular_equi_cat/{id}', 'EquipoController@anular_equi_cat');
// Route::get('equipo', 'EquipoController@view_equipo');
Route::get('listar_equipos', 'EquipoController@mostrar_equipos');
Route::get('mostrar_equipo/{id}', 'EquipoController@mostrar_equipo');
Route::post('guardar_equipo', 'EquipoController@guardar_equipo');
Route::post('actualizar_equipo', 'EquipoController@update_equipo');
Route::get('anular_equipo/{id}', 'EquipoController@anular_equipo');
Route::get('equi_catalogo', 'EquipoController@view_equi_catalogo');
Route::get('listar_seguros/{id}', 'EquipoController@listar_seguros');
Route::post('guardar_seguro', 'EquipoController@guardar_seguro');
Route::get('abrir_adjunto_seguro/{id}', 'EquipoController@abrir_adjunto_seguro');
Route::get('anular_seguro/{id}', 'EquipoController@anular_seguro');
Route::get('guardar_tipo_doc/{id}', 'EquipoController@guardar_tipo_doc');
// Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');
Route::get('docs', 'EquipoController@view_docs');
Route::get('listar_docs', 'EquipoController@listar_docs');
Route::get('mtto_realizados', 'EquipoController@view_mtto_realizados');
Route::get('listar_mttos_detalle/{id}/{fini}/{ffin}', 'EquipoController@listar_mttos_detalle');
Route::get('listar_programaciones/{id}', 'EquipoController@listar_programaciones');
Route::post('guardar_programacion', 'EquipoController@guardar_programacion');
Route::get('anular_programacion/{id}', 'EquipoController@anular_programacion');
Route::get('prueba_contri', 'LogisticaController@prueba_contri');
Route::get('download_control_bitacora/{id}/{fini}/{ffin}', 'EquipoController@download_control_bitacora');
// Route::get('imprimir_control_bitacora/{id}', 'EquipoController@imprimir_control_bitacora');

/**Mantenimientos */
Route::get('mtto_pendientes', 'EquipoController@view_mtto_pendientes');
Route::get('listar_mtto_pendientes', 'EquipoController@listar_programaciones_pendientes');
Route::get('listar_mtto_pendientes/{id}', 'EquipoController@listar_mtto_pendientes');
Route::get('listar_todas_programaciones', 'EquipoController@listar_todas_programaciones');
Route::get('mtto', 'EquipoController@view_mtto');
Route::get('listar_mttos', 'EquipoController@listar_mttos');
Route::get('mostrar_mtto/{id}', 'EquipoController@mostrar_mtto');
Route::post('guardar_mtto', 'EquipoController@guardar_mtto');
Route::post('actualizar_mtto', 'EquipoController@update_mtto');
Route::get('anular_mtto/{id}', 'EquipoController@anular_mtto');
Route::post('guardar_mtto_detalle', 'EquipoController@guardar_mtto_detalle');
Route::post('update_mtto_detalle', 'EquipoController@update_mtto_detalle');
Route::get('anular_mtto_detalle/{id}', 'EquipoController@anular_mtto_detalle');
Route::get('listar_mtto_detalle/{id}', 'EquipoController@listar_mtto_detalle');
Route::get('mostrar_mtto_detalle/{id}', 'EquipoController@mostrar_mtto_detalle');
Route::get('tp_combustible', 'EquipoController@view_tp_combustible');
Route::get('listar_tp_combustibles', 'EquipoController@mostrar_tp_combustibles');
Route::get('mostrar_tp_combustible/{id}', 'EquipoController@mostrar_tp_combustible');
Route::post('guardar_tp_combustible', 'EquipoController@guardar_tp_combustible');
Route::post('actualizar_tp_combustible', 'EquipoController@update_tp_combustible');
Route::get('anular_tp_combustible/{id}', 'EquipoController@anular_tp_combustible');
/**Solicitud de Equipos */
Route::get('equi_sol', 'EquipoController@view_equi_sol');
Route::get('presup_ejecucion/{id}', 'ProyectosController@mostrar_presup_ejecucion_contrato');
Route::get('mostrar_solicitudes/{id}/{usu}', 'EquipoController@mostrar_solicitudes');
Route::get('mostrar_solicitud/{id}', 'EquipoController@mostrar_solicitud');
Route::post('guardar_equi_sol', 'EquipoController@guardar_equi_sol');
Route::post('actualizar_equi_sol', 'EquipoController@update_equi_sol');
Route::get('anular_equi_sol/{id}', 'EquipoController@anular_equi_sol');
Route::get('aprob_sol', 'EquipoController@view_aprob_sol');
Route::get('listar_aprob_sol', 'EquipoController@listar_aprob_sol');
Route::post('guardar_aprobacion', 'EquipoController@guardar_aprobacion');
Route::post('guardar_sustento', 'EquipoController@guardar_sustento');
Route::post('solicitud_cambia_estado/{id}/{est}', 'EquipoController@solicitud_cambia_estado');
Route::get('solicitud_flujos/{id}/{sol}', 'EquipoController@solicitud_flujos');
Route::get('sol_todas', 'EquipoController@view_sol_todas');
Route::get('listar_todas_solicitudes', 'EquipoController@listar_todas_solicitudes');
Route::get('imprimir_solicitud/{id}', 'EquipoController@imprimir_solicitud');
Route::get('mostrar_solicitudes_grupo', 'EquipoController@mostrar_solicitudes_grupo');
Route::get('posiciones', 'AlmacenController@posiciones');
Route::get('usuario_aprobacion', 'EquipoController@usuario_aprobacion');
Route::get('download_control_bitacora/{id}/{fini}/{ffin}', 'EquipoController@download_control_bitacora');

/**Asignacion de Equipos */
Route::get('asignacion', 'EquipoController@view_asignacion');
Route::get('listar_solicitudes_aprobadas', 'EquipoController@listar_solicitudes_aprobadas');
Route::get('equipos_disponibles/{id}', 'EquipoController@equipos_disponibles');
Route::post('guardar_asignacion', 'EquipoController@guardar_asignacion');
Route::post('editar_asignacion', 'EquipoController@update_equi_asig');
Route::get('anular_asignacion/{id}', 'EquipoController@anular_equi_asig');
Route::get('control', 'EquipoController@view_control');
Route::get('listar_asignaciones', 'EquipoController@listar_asignaciones');
Route::get('mostrar_asignacion/{id}', 'EquipoController@mostrar_asignacion');
Route::get('listar_controles/{id}', 'EquipoController@listar_controles');
Route::post('guardar_control', 'EquipoController@guardar_control');
Route::post('actualizar_control', 'EquipoController@update_control');
Route::get('anular_control/{id}', 'EquipoController@anular_control');
Route::get('mostrar_control/{id}', 'EquipoController@mostrar_control');
Route::get('getTrabajador/{id}', 'EquipoController@getTrabajador');
Route::get('kilometraje_actual/{id}', 'EquipoController@kilometraje_actual');
Route::get('select_programaciones/{id}', 'EquipoController@select_programaciones');
Route::get('decode5t/{id}', 'EquipoController@decode5t');
	/* Logistica */


	Route::get('get_id_operacion/{id1}/{id2}/{id23}', 'LogisticaController@get_id_operacion');
	Route::get('session-rol-aprob', 'LogisticaController@userSession');
	// Route::get('logistica/reqHasQuotation/{id_ope}', 'LogisticaController@reqHasQuotation');
	Route::get('logistica/cantidad_requerimientos_generados', 'LogisticaController@cantidad_requerimientos_generados');

	// Route::get('logistica/sedes/{empresa_id}','LogisticaController@getSedes')->name('logistica_sedes');
	// Route::get('logistica/areas/{sede_id}','LogisticaController@getGruposAreas')->name('logistica_areas');
	Route::get('logistica/mostrar_items', 'LogisticaController@mostrar_items');
	Route::get('logistica/mostrar_item/{id_item}', 'LogisticaController@mostrar_item');
	Route::get('logistica/get_requerimiento/{id}/{doc}', 'LogisticaController@get_requerimiento');
	Route::get('/mostrar_nombre_grupo/{id}', 'EquipoController@mostrar_nombre_grupo');

	Route::get('logistica/get_historial_aprobacion/{id_req}', 'LogisticaController@get_historial_aprobacion');
	Route::get('logistica/mostrar-adjuntos/{id_requerimiento}', 'LogisticaController@mostrar_adjuntos');
	Route::get('logistica/imprimir-requerimiento-pdf/{id}/{codigo}', 'LogisticaController@generar_requerimiento_pdf');
	Route::get('logistica/requerimiento/lista', 'LogisticaController@view_lista_requerimientos');
	Route::post('logistica/copiar_requerimiento/{id}', 'LogisticaController@copiar_requerimiento');

	Route::get('logistica/cotizacion/cuadro-comparativo', 'LogisticaController@view_cuadro_comparativo');
	Route::get('logistica/cotizacion/valorizacion', 'LogisticaController@view_valoriacion');
	Route::get('logistica/orden/generar', 'LogisticaController@view_generar_orden');
	// Route::get('logistica/get_req_list/{id_empresa}', 'LogisticaController@get_req_list');
	// Route::get('getCriterioMonto/{id}', 'LogisticaController@getCriterioMonto');

	Route::get('logistica/observar_req/{req}/{doc}', 'LogisticaController@observar_requerimiento_vista');
	// Route::get('logistica/get_email/{req}', 'LogisticaController@get_email');
	// Route::post('logistica/observar_detalles', 'LogisticaController@observar_requerimiento_item');
	Route::post('logistica/aprobar_documento', 'LogisticaController@aprobar_requerimiento');
	Route::post('logistica/observar_contenido', 'LogisticaController@observar_requerimiento');
	Route::post('logistica/denegar_documento', 'LogisticaController@denegar_requerimiento');
	Route::get('get_flujo_aprobacion/{req}/{doc}', 'LogisticaController@get_flujo_aprobacion');
	Route::post('logistica/guardar_sustento', 'LogisticaController@guardar_sustento');
	// Route::post('logistica/aceptar_sustento', 'LogisticaController@aceptar_sustento'); 
	// Route::get('verUsuario', 'EquipoController@verUsuario'); 
	
	Route::get('logistica/get_cuadro_costos_comercial', 'LogisticaController@get_cuadro_costos_comercial');

	/**Logistica Cotizaciones */
 	// Route::get('select_responsables', 'LogisticaController@select_responsables');
 	Route::get('get_estado_doc/{nombreEstadoDoc}', 'LogisticaController@get_estado_doc');
	Route::get('logistica/mostrar-archivos-adjuntos-proveedor/{id}', 'LogisticaController@mostrar_archivos_adjuntos_proveedor');
	Route::post('logistica/guardar-archivos-adjuntos-proveedor', 'LogisticaController@guardar_archivos_adjuntos_proveedor');

	// Route::get('gestionar_cotizaciones', 'LogisticaController@view_gestionar_cotizaciones');
	// Route::get('requerimientos_entrante_a_cotizacion', 'LogisticaController@requerimientos_entrante_a_cotizacion');
	

	// Route::get('cotizaciones_por_grupo/{id}', 'LogisticaController@cotizaciones_por_grupo');
	// Route::get('items_cotizaciones_por_grupo/{id}', 'LogisticaController@items_cotizaciones_por_grupo');
	// Route::get('get_items_cotizaciones_por_grupo/{id}', 'LogisticaController@get_items_cotizaciones_por_grupo');
	Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
	Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');
	Route::post('registrar_proveedor', 'LogisticaController@registrar_proveedor');
	Route::put('update_proveedor', 'LogisticaController@update_proveedor');
	Route::post('registrar_establecimiento', 'LogisticaController@registrar_establecimiento');
	Route::put('update_establecimiento', 'LogisticaController@update_establecimiento');
	Route::post('registrar_cuenta_bancaria', 'LogisticaController@registrar_cuenta_bancaria');
	Route::put('update_cuenta_bancaria', 'LogisticaController@update_cuenta_bancaria');
	Route::post('registrar_contacto', 'LogisticaController@registrar_contacto');
	Route::put('update_contacto', 'LogisticaController@update_contacto');
	Route::get('contacto_establecimiento/{id_proveedor}', 'LogisticaController@contacto_establecimiento');
	Route::post('registrar_adjunto_proveedor', 'LogisticaController@registrar_adjunto_proveedor');
	// Route::post('registrar_only_archivo', 'LogisticaController@registrar_only_archivo');
	Route::put('update_adjunto_proveedor', 'LogisticaController@update_adjunto_proveedor');
	// Route::get('logistica/cargar_almacenes/{id_sede}', 'LogisticaController@cargar_almacenes');

	 // logistica proveedores
	Route::get('gestionar_proveedores', 'LogisticaController@view_lista_proveedores');
	Route::get('logistica/listar_proveedores', 'LogisticaController@listar_proveedores');
	Route::get('mostrar_proveedor/{id_proveedor}', 'LogisticaController@mostrar_proveedor');




	// Route::get('form_enviar_correo', 'CorreoController@crear');
	// Route::post('cargar_archivo_correo', 'CorreoController@store');
	// Route::get('generar_cotizacion_excel/{id_cotizacion}', 'CorreoController@generarCotizacionInServer');
	// Route::get('descartar_archivo_adjunto/{id}/{tipo_doc}', 'CorreoController@discardFileInServer');



	// logistica - cuadro comparativo
	Route::get('logistica/detalle_unidad_medida/{id_unidad_medida}', 'LogisticaController@detalle_unidad_medida');
	Route::get('logistica/cuadro_comparativo/mostrar_comparativo/{id1}', 'LogisticaController@mostrar_comparativo');
	Route::put('/logistica/actualizar_valorizacion', 'LogisticaController@update_valorizacion');
	Route::get('logistica/cuadro_comparativos/valorizacion/lista_item/{id_cotizacion}', 'LogisticaController@listaItemValorizar');
	Route::put('logistica/descripcion_adicional_detalle_orden/', 'LogisticaController@update_descripcion_adicional_detalle_orden');
	Route::put('logistica/despacho/', 'LogisticaController@updateDespacho');
	Route::get('logistica/ultimas_compras/{id_item}/{id_detalle_requerimiento}', 'LogisticaController@ultimas_compras');
	Route::put('logistica/actualizar_stock_comprometido', 'LogisticaController@actualizar_stock_comprometido');
	Route::put('logistica/condicion_valorizacion/', 'LogisticaController@condicion_valorizacion');
	Route::get('logistica/comparative_board_enabled_to_value', 'LogisticaController@comparative_board_enabled_to_value');

	Route::get('logistica/get_cuadro_comparativo/{id_grupo}', 'LogisticaController@get_cuadro_comparativo');
	Route::get('logistica/get_cuadro_comparativos', 'LogisticaController@get_cuadro_comparativos');
	Route::get('logistica/cuadro_comparativos/valorizacion/item/{id_detalle_requerimiento}', 'LogisticaController@ItemValorizar');
	Route::get('logistica/cuadro_comparativos', 'LogisticaController@mostrar_cuadro_comparativos');
	Route::get('logistica/cuadro_comparativo/{id}', 'LogisticaController@mostrar_cuadro_comparativo');
	Route::get('logistica/valorizacion/grupo_cotizaciones/{codigo_cotizacion}/{codigo_cuadro_comparativo}/{id_grupo_cotizacion}/{estado_envio}/{id_empresa}/{valorizacion_completa_incompleta}/{id_cotizacion_alone}', 'LogisticaController@grupo_cotizaciones')->where('id', '(.*)');
	// Route::get('logistica/only_valorizaciones/{id_grupo}', 'LogisticaController@only_valorizaciones');
	Route::post('logistica/cuadro_comparativo/guardar_buenas_pro', 'LogisticaController@guardar_buenas_pro');
	Route::put('logistica/cuadro_comparativo/eliminar_buena_pro/{id_valorizacion}', 'LogisticaController@eliminar_buena_pro');
	Route::get('logistica/cuadro_comparativo/exportar_excel/{id_grupo}', 'LogisticaController@solicitud_cuadro_comparativo_excel');
	/**Logistica Ordenes */
	// Route::get('get_ord_list', 'LogisticaController@get_ord_list');
	Route::get('generar_orden', 'LogisticaController@view_generar_orden');
	Route::get('detalle_cotizacion/{id}', 'LogisticaController@detalle_cotizacion');
	Route::post('guardar_orden_compra', 'LogisticaController@guardar_orden_compra');
	Route::post('update_orden_compra', 'LogisticaController@update_orden_compra');
	Route::get('anular_orden_compra/{id}', 'LogisticaController@anular_orden_compra');
	Route::get('mostrar_cuentas_bco/{id}', 'LogisticaController@mostrar_cuentas_bco');
	Route::get('listar_ordenes', 'LogisticaController@listar_ordenes');
	Route::get('listar_ordenes_proveedor/{id}', 'LogisticaController@listar_ordenes_proveedor');
	Route::get('mostrar_orden/{id}', 'LogisticaController@mostrar_orden');
	Route::get('listar_detalle_orden/{id}', 'LogisticaController@listar_detalle_orden');
	Route::post('guardar_cuenta_banco', 'LogisticaController@guardar_cuenta_banco');
	Route::get('mostrar_impuesto/{id}/{fecha}', 'ProyectosController@mostrar_impuesto');
	Route::get('imprimir_orden_pdf/{id}', 'LogisticaController@imprimir_orden_pdf'); // PDF
	Route::put('actualizar_item_sin_codigo/{id_orden}/{id_valorizacion}', 'LogisticaController@actualizar_item_sin_codigo');
	Route::get('data_buenas_pro', 'LogisticaController@data_buenas_pro');
	Route::put('logistica/cuadro_comparativo/eliminar_buena_pro/{id_valorizacion}', 'LogisticaController@eliminar_buena_pro');
	Route::get('get_data_req_by_id_orden/{id_orden}', 'LogisticaController@get_data_req_by_id_orden'); 
	Route::get('getReqOperacionFlujoAprob/{id}/{tipo_id}', 'LogisticaController@getReqOperacionFlujoAprob'); 
	Route::get('get_current_user/', 'LogisticaController@get_current_user'); 


	/** logistica - Comprobante de Compra */

	Route::get('doc_compra', 'LogisticaController@view_doc_compra');
	Route::get('listar_docs_compra', 'LogisticaController@listar_docs_compra');
	Route::get('listar_doc_guias/{id}', 'LogisticaController@listar_doc_guias');
	Route::get('listar_doc_items/{id}', 'LogisticaController@listar_doc_items');
	Route::post('guardar_doc_compra', 'LogisticaController@guardar_doc_compra');
	Route::post('actualizar_doc_compra', 'LogisticaController@update_doc_compra');
	Route::post('update_doc_detalle', 'LogisticaController@update_doc_detalle');
	Route::get('anular_doc_detalle/{id}', 'LogisticaController@anular_doc_detalle');
	Route::get('anular_doc_compra/{id}', 'LogisticaController@anular_doc_compra');
	Route::get('mostrar_doc_com/{id}', 'LogisticaController@mostrar_doc_com');
	Route::get('listar_guias_prov/{id}', 'LogisticaController@listar_guias_prov');
	Route::get('guardar_doc_items_guia/{id}/{id_doc}', 'LogisticaController@guardar_doc_items_guia');
	Route::get('mostrar_doc_detalle/{id}', 'LogisticaController@mostrar_doc_detalle');
	Route::get('actualiza_totales_doc/{por}/{id}/{fec}', 'LogisticaController@actualiza_totales_doc'); 
	Route::get('listar_ordenes_sin_comprobante/{id_proveedor}', 'LogisticaController@listar_ordenes_sin_comprobante'); 
	Route::post('guardar_doc_com_det_orden/{id_doc}', 'LogisticaController@guardar_doc_com_det_orden');
	Route::get('listar_doc_com_orden/{id_doc}', 'LogisticaController@listar_doc_com_orden');
	Route::get('getOrdenByDetOrden/{id_det_orden}', 'LogisticaController@getOrdenByDetOrden');
	Route::get('anular_orden_doc_com/{id_doc_com}/{id_orden_compra}', 'LogisticaController@anular_orden_doc_com');


	/** Logistica - reportes  */
	Route::get('/logistica/reportes/productos_comprados', 'LogisticaController@view_reporte_productos_comprados');
	Route::get('/logistica/productos_comprados', 'LogisticaController@productos_comprados');
	Route::get('/logistica/productos_comprados_excel', 'LogisticaController@productos_comprados_excel');
	
	Route::get('/logistica/reportes/compras_por_proveedor', 'LogisticaController@view_reporte_compras_por_proveedor');
	Route::get('/logistica/compras_por_proveedor', 'LogisticaController@compras_por_proveedor');
	Route::get('/logistica/compras_por_proveedor_excel', 'LogisticaController@compras_por_proveedor_excel');

	Route::get('/logistica/reportes/compras_por_producto', 'LogisticaController@view_reporte_compras_por_producto');
	Route::get('/logistica/reportes/listar_productos', 'LogisticaController@listar_productos');
	Route::get('/logistica/compras_por_producto', 'LogisticaController@compras_por_producto');
	Route::get('/logistica/compras_por_producto_excel', 'LogisticaController@compras_por_producto_excel');
	
	Route::get('/logistica/reportes/mejores_proveedores', 'LogisticaController@view_reporte_mejores_proveedores');
	Route::get('/logistica/mejores_proveedores', 'LogisticaController@mejores_proveedores');
	Route::get('/logistica/mejores_proveedores_excel', 'LogisticaController@mejores_proveedores_excel');
	
	Route::get('/logistica/reportes/proveedores_producto_determinado', 'LogisticaController@view_reporte_proveedores_producto_determinado');
	Route::get('/logistica/proveedores_producto_determinado', 'LogisticaController@proveedores_producto_determinado');
	Route::get('/logistica/proveedores_producto_determinado_excel', 'LogisticaController@proveedores_producto_determinado_excel');
	
	Route::get('/logistica/reportes/frecuencia_compras', 'LogisticaController@view_reporte_frecuencia_compras');
	Route::get('/logistica/frecuencia_compras', 'LogisticaController@frecuencia_compras');
	Route::get('/logistica/frecuencia_compras_excel', 'LogisticaController@frecuencia_compras_excel');

	Route::get('/logistica/reportes/historial_precios', 'LogisticaController@view_reporte_historial_precios');
	Route::get('/logistica/historial_precios', 'LogisticaController@historial_precios');
	Route::get('/logistica/historial_precios_excel', 'LogisticaController@historial_precios_excel');


	/**Contabilidad */
	Route::get('cta_contable', 'ContabilidadController@view_cta_contable');
	Route::get('mostrar_cta_contables', 'ContabilidadController@mostrar_cuentas_contables');
	Route::get('comprobante_compra', 'ContabilidadController@view_comprobante_compra');
	Route::get('ordenes_sin_facturar/{id_empresa}/{all_or_id_orden}', 'ContabilidadController@ordenes_sin_facturar');
	Route::post('guardar_comprobante_compra', 'ContabilidadController@guardar_comprobante_compra');
	Route::get('lista_comprobante_compra/{id_sede}/{all_or_id_doc_com}', 'ContabilidadController@lista_comprobante_compra');


	// APIs de Terceros
	Route::post('consulta_sunat', 'HynoTechController@consulta_sunat');

	// Route::get('hasObsDetReq/{id_req}', 'LogisticaController@hasObsDetReq');
	// // Route::get('get_header_observacion/{id_req}', 'LogisticaController@get_header_observacion');
	// Route::get('get_id_req_by_id_coti/{id_req}', 'LogisticaController@get_id_req_by_id_coti');
	Route::get('get_orden/{id}', 'LogisticaController@get_orden');
	Route::get('count_group_included/{id}', 'LogisticaController@groupIncluded');
	Route::get('last_aprob/{id}', 'LogisticaController@consult_aprob');
	Route::get('list_id_rol_concepto_aprob/{id}', 'LogisticaController@list_id_rol_concepto_aprob');

	//////////////////////
	////ADMINISTRACION
	Route::get('empresas', 'AdministracionController@view_empresa');
	Route::get('sedes', 'AdministracionController@view_sede');
	Route::get('grupos', 'AdministracionController@view_grupo');
	Route::get('areas', 'AdministracionController@view_area');

	Route::get('listar_empresa', 'AdministracionController@mostrar_empresa_table');
	Route::get('cargar_empresa/{id}', 'AdministracionController@mostrar_empresa_id');
	Route::post('guardar_empresa_contri', 'AdministracionController@guardar_empresas');
	Route::post('editar_empresa_contri', 'AdministracionController@actualizar_empresas');

	Route::get('listar_contacto_empresa/{id}', 'AdministracionController@mostrar_contacto_empresa');
	Route::post('guardar_contacto_empresa', 'AdministracionController@guardar_contacto_empresa');
	Route::post('editar_contacto_empresa', 'AdministracionController@actualizar_contacto_empresa'); //FALTA ANULAR

	Route::get('listar_cuentas_empresa/{id}', 'AdministracionController@mostrar_cuentas_empresa');
	Route::post('guardar_cuentas_empresa', 'AdministracionController@guardar_cuentas_empresa');
	Route::post('editar_cuentas_empresa', 'AdministracionController@actualizar_cuentas_empresa'); //FALTA ANULAR

	Route::get('listar_sede', 'AdministracionController@mostrar_sede_table');
	Route::get('buscar_codigo_empresa/{value}/{type}', 'AdministracionController@codigoEmpresa');
	Route::get('cargar_sede/{id}', 'AdministracionController@mostrar_sede_id');
	Route::post('guardar_sede', 'AdministracionController@guardar_sede');
	Route::post('editar_sede', 'AdministracionController@actualizar_sede');
	Route::get('anular_sede/{id}', 'AdministracionController@anular_sede');

	Route::get('listar_grupo', 'AdministracionController@mostrar_grupo_table');
	Route::get('cargar_grupo/{id}', 'AdministracionController@mostrar_grupo_id');
	Route::post('guardar_grupo', 'AdministracionController@guardar_grupo');
	Route::post('editar_grupo', 'AdministracionController@actualizar_grupo');
	Route::get('anular_grupo/{id}', 'AdministracionController@anular_grupo');

	Route::get('listar_area', 'AdministracionController@mostrar_area_table');
	Route::get('cargar_area/{id}', 'AdministracionController@mostrar_area_id');
	Route::post('guardar_area', 'AdministracionController@guardar_area');
	Route::post('editar_area', 'AdministracionController@actualizar_area');
	Route::get('anular_area/{id}', 'AdministracionController@anular_area');

	Route::post('like', 'FrontendController@like');

});