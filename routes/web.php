<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Route::get('admin', function () {
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

	Route::group(['as' => 'proyectos.', 'prefix' => 'proyectos'], function () {
		// Proyetos
		Route::get('getProyectosActivos', 'ProyectosController@getProyectosActivos');

		Route::get('index', function () {
			return view('proyectos/main');
		})->name('index');

		Route::group(['as' => 'variables-entorno.', 'prefix' => 'variables-entorno'], function () {

			Route::group(['as' => 'tipos-insumo.', 'prefix' => 'tipos-insumo'], function () {
				// Tipo de Insumos
				Route::get('index', 'Proyectos\Variables\TipoInsumoController@view_tipo_insumo')->name('index');
				Route::get('listar_tipo_insumos', 'Proyectos\Variables\TipoInsumoController@mostrar_tipos_insumos');
				Route::get('mostrar_tipo_insumo/{id}', 'Proyectos\Variables\TipoInsumoController@mostrar_tp_insumo');
				Route::post('guardar_tipo_insumo', 'Proyectos\Variables\TipoInsumoController@guardar_tp_insumo');
				Route::post('actualizar_tipo_insumo', 'Proyectos\Variables\TipoInsumoController@update_tp_insumo');
				Route::get('anular_tipo_insumo/{id}', 'Proyectos\Variables\TipoInsumoController@anular_tp_insumo');
				Route::get('revisar_tipo_insumo/{id}', 'Proyectos\Variables\TipoInsumoController@buscar_tp_insumo');
			});

			Route::group(['as' => 'sistemas-contrato.', 'prefix' => 'sistemas-contrato'], function () {
				// Sistema de Contrato
				Route::get('index', 'Proyectos\Variables\SistemasContratoController@view_sis_contrato')->name('index');
				Route::get('listar', 'Proyectos\Variables\SistemasContratoController@mostrar_sis_contratos')->name('listar');
				Route::get('mostrar/{id?}', 'Proyectos\Variables\SistemasContratoController@mostrar_sis_contrato')->name('mostrar');
				Route::post('guardar', 'Proyectos\Variables\SistemasContratoController@guardar_sis_contrato')->name('guardar');
				Route::post('actualizar', 'Proyectos\Variables\SistemasContratoController@update_sis_contrato')->name('actualizar');
				Route::get('anular/{id}', 'Proyectos\Variables\SistemasContratoController@anular_sis_contrato')->name('anular');
			});

			Route::group(['as' => 'iu.', 'prefix' => 'iu'], function () {
				// Indices Unificados
				Route::get('index', 'Proyectos\Variables\IuController@view_iu')->name('index');
				Route::get('listar_ius', 'Proyectos\Variables\IuController@mostrar_ius');
				Route::get('mostrar_iu/{id}', 'Proyectos\Variables\IuController@mostrar_iu');
				Route::post('guardar_iu', 'Proyectos\Variables\IuController@guardar_iu');
				Route::post('actualizar_iu', 'Proyectos\Variables\IuController@update_iu');
				Route::get('anular_iu/{id}', 'Proyectos\Variables\IuController@anular_iu');
				Route::get('revisar_iu/{id}', 'Proyectos\Variables\IuController@buscar_iu');
			});

			Route::group(['as' => 'categorias-insumo.', 'prefix' => 'categorias-insumo'], function () {
				// Categoría de Insumos
				Route::get('index', 'Proyectos\Variables\CategoriaInsumoController@view_cat_insumo')->name('index');
				Route::get('listar_cat_insumos', 'Proyectos\Variables\CategoriaInsumoController@listar_cat_insumos');
				Route::get('mostrar_cat_insumo/{id}', 'Proyectos\Variables\CategoriaInsumoController@mostrar_cat_insumo');
				Route::post('guardar_cat_insumo', 'Proyectos\Variables\CategoriaInsumoController@guardar_cat_insumo');
				Route::post('update_cat_insumo', 'Proyectos\Variables\CategoriaInsumoController@update_cat_insumo');
				Route::get('anular_cat_insumo/{id}', 'Proyectos\Variables\CategoriaInsumoController@anular_cat_insumo');
			});

			Route::group(['as' => 'categorias-acu.', 'prefix' => 'categorias-acu'], function () {
				// Categoría de A.C.U
				Route::get('index', 'Proyectos\Variables\CategoriaAcuController@view_cat_acu')->name('index');
				Route::get('listar_cat_acus', 'Proyectos\Variables\CategoriaAcuController@listar_cat_acus');
				Route::get('mostrar_cat_acu/{id}', 'Proyectos\Variables\CategoriaAcuController@mostrar_cat_acu');
				Route::post('guardar_cat_acu', 'Proyectos\Variables\CategoriaAcuController@guardar_cat_acu');
				Route::post('update_cat_acu', 'Proyectos\Variables\CategoriaAcuController@update_cat_acu');
				Route::get('anular_cat_acu/{id}', 'Proyectos\Variables\CategoriaAcuController@anular_cat_acu');
			});
		});

		Route::group(['as' => 'catalogos.', 'prefix' => 'catalogos'], function () {

			Route::group(['as' => 'insumos.', 'prefix' => 'insumos'], function () {
				//Insumos
				Route::get('index', 'Proyectos\Catalogos\InsumoController@view_insumo')->name('index');
				Route::get('listar_insumos', 'Proyectos\Catalogos\InsumoController@listar_insumos');
				Route::get('mostrar_insumo/{id}', 'Proyectos\Catalogos\InsumoController@mostrar_insumo');
				Route::post('guardar_insumo', 'Proyectos\Catalogos\InsumoController@guardar_insumo');
				Route::post('actualizar_insumo', 'Proyectos\Catalogos\InsumoController@update_insumo');
				Route::get('anular_insumo/{id}', 'Proyectos\Catalogos\InsumoController@anular_insumo');
				Route::get('listar_insumo_precios/{id}', 'Proyectos\Catalogos\InsumoController@listar_insumo_precios');
				Route::post('add_unid_med', 'Proyectos\Catalogos\InsumoController@add_unid_med');
			});

			Route::group(['as' => 'nombres-cu.', 'prefix' => 'nombres-cu'], function () {
				//Nombres CU
				Route::get('index', 'Proyectos\Catalogos\NombresAcuController@view_nombres_cu')->name('index');
				Route::get('listar_cus', 'Proyectos\Catalogos\NombresAcuController@listar_nombres_cus');
				Route::post('guardar_cu', 'Proyectos\Catalogos\NombresAcuController@guardar_cu');
				Route::post('update_cu', 'Proyectos\Catalogos\NombresAcuController@update_cu');
				Route::get('anular_cu/{id}', 'Proyectos\Catalogos\NombresAcuController@anular_cu');
				Route::get('listar_partidas_cu/{id}', 'Proyectos\Catalogos\NombresAcuController@listar_partidas_cu');
			});

			Route::group(['as' => 'acus.', 'prefix' => 'acus'], function () {
				//ACUS
				Route::get('index', 'Proyectos\Catalogos\AcuController@view_acu')->name('index');
				Route::get('listar_acus', 'Proyectos\Catalogos\AcuController@listar_acus');
				Route::get('listar_acus_sin_presup', 'Proyectos\Catalogos\AcuController@listar_acus_sin_presup');
				Route::get('mostrar_acu/{id}', 'Proyectos\Catalogos\AcuController@mostrar_acu');
				Route::get('listar_acu_detalle/{id}', 'Proyectos\Catalogos\AcuController@listar_acu_detalle');
				Route::get('listar_insumo_precios/{id}', 'Proyectos\Variables\InsumoController@listar_insumo_precios');

				// Route::post('guardar_precio', 'ProyectosController@guardar_precio');
				Route::post('guardar_acu', 'Proyectos\Catalogos\AcuController@guardar_acu');
				Route::post('actualizar_acu', 'Proyectos\Catalogos\AcuController@update_acu');
				Route::get('anular_acu/{id}', 'Proyectos\Catalogos\AcuController@anular_acu');
				Route::get('valida_acu_editar/{id}', 'Proyectos\Catalogos\AcuController@valida_acu_editar');
				// Route::get('insumos/{id}/{cu}', 'Proyectos\Catalogos\AcuController@insumos');
				Route::get('partida_insumos_precio/{id}/{ins}', 'Proyectos\Catalogos\AcuController@partida_insumos_precio');
				Route::post('guardar_insumo', 'Proyectos\Variables\InsumoController@guardar_insumo');
				Route::get('listar_insumos', 'Proyectos\Variables\InsumoController@listar_insumos');

				Route::post('guardar_cu', 'Proyectos\Catalogos\AcuController@guardar_cu');
				Route::post('update_cu', 'Proyectos\Catalogos\AcuController@update_cu');
				Route::get('listar_cus', 'Proyectos\Catalogos\NombresAcuController@listar_nombres_cus');
				Route::get('mostrar_presupuestos_acu/{id}', 'Proyectos\Catalogos\AcuController@mostrar_presupuestos_acu');
			});
		});

		Route::group(['as' => 'opciones.', 'prefix' => 'opciones'], function () {

			Route::group(['as' => 'opciones.', 'prefix' => 'opciones'], function () {
				//Opciones
				Route::get('index', 'Proyectos\Opciones\OpcionesController@view_opcion')->name('index');
				Route::get('listar_opciones', 'Proyectos\Opciones\OpcionesController@listar_opciones');
				Route::post('guardar_opcion', 'Proyectos\Opciones\OpcionesController@guardar_opcion');
				Route::post('actualizar_opcion', 'Proyectos\Opciones\OpcionesController@update_opcion');
				Route::get('anular_opcion/{id}', 'Proyectos\Opciones\OpcionesController@anular_opcion');
				Route::post('guardar_cliente', 'Comercial\ClienteController@guardar_cliente');
				Route::get('mostrar_clientes', 'Comercial\ClienteController@mostrar_clientes');
			});

			Route::group(['as' => 'presupuestos-internos.', 'prefix' => 'presupuestos-internos'], function () {
				/**Presupuesto Interno */
				Route::get('index', 'Proyectos\Opciones\PresupuestoInternoController@view_presint')->name('index');
				Route::get('mostrar_presint/{id}', 'Proyectos\Opciones\PresupuestoInternoController@mostrar_presint');
				Route::post('guardar_presint', 'Proyectos\Opciones\PresupuestoInternoController@guardar_presint');
				Route::post('update_presint', 'Proyectos\Opciones\PresupuestoInternoController@update_presint');
				Route::get('anular_presint/{id}', 'Proyectos\Opciones\PresupuestoInternoController@anular_presint');

				Route::get('generar_estructura/{id}/{tp}', 'Proyectos\Opciones\PresupuestoInternoController@generar_estructura');
				Route::get('listar_presupuesto_proyecto/{id}', 'Proyectos\Opciones\PresupuestoInternoController@listar_presupuesto_proyecto');
				Route::get('anular_estructura/{id}', 'Proyectos\Opciones\PresupuestoInternoController@anular_estructura');
				Route::get('totales/{id}', 'Proyectos\Opciones\PresupuestoInternoController@totales');
				Route::get('download_presupuesto/{id}', 'Proyectos\Opciones\PresupuestoInternoController@download_presupuesto');
				Route::get('actualiza_moneda/{id}', 'Proyectos\Opciones\PresupuestoInternoController@actualiza_moneda');
				Route::get('mostrar_presupuestos/{id}', 'Proyectos\Opciones\PresupuestoInternoController@mostrar_presupuestos');
				Route::get('listar_presupuestos_copia/{tp}/{id}', 'Proyectos\Opciones\PresupuestoInternoController@listar_presupuestos_copia');
				Route::get('generar_partidas_presupuesto/{id}/{ida}', 'Proyectos\Opciones\PresupuestoInternoController@generar_partidas_presupuesto');

				Route::get('listar_acus_cd/{id}', 'Proyectos\Opciones\ComponentesController@listar_acus_cd');
				Route::get('listar_cd/{id}', 'Proyectos\Opciones\ComponentesController@listar_cd');
				Route::get('listar_ci/{id}', 'Proyectos\Opciones\ComponentesController@listar_ci');
				Route::get('listar_gg/{id}', 'Proyectos\Opciones\ComponentesController@listar_gg');
				Route::post('guardar_componente_cd', 'Proyectos\Opciones\ComponentesController@guardar_componente_cd');
				Route::post('guardar_componente_ci', 'Proyectos\Opciones\ComponentesController@guardar_componente_ci');
				Route::post('guardar_componente_gg', 'Proyectos\Opciones\ComponentesController@guardar_componente_gg');
				Route::post('update_componente_cd', 'Proyectos\Opciones\ComponentesController@update_componente_cd');
				Route::post('update_componente_ci', 'Proyectos\Opciones\ComponentesController@update_componente_ci');
				Route::post('update_componente_gg', 'Proyectos\Opciones\ComponentesController@update_componente_gg');
				Route::post('anular_compo_cd', 'Proyectos\Opciones\ComponentesController@anular_compo_cd');
				Route::post('anular_compo_ci', 'Proyectos\Opciones\ComponentesController@anular_compo_ci');
				Route::post('anular_compo_gg', 'Proyectos\Opciones\ComponentesController@anular_compo_gg');

				Route::post('guardar_partida_cd', 'Proyectos\Opciones\PartidasController@guardar_partida_cd');
				Route::post('guardar_partida_ci', 'Proyectos\Opciones\PartidasController@guardar_partida_ci');
				Route::post('guardar_partida_gg', 'Proyectos\Opciones\PartidasController@guardar_partida_gg');
				Route::post('update_partida_cd', 'Proyectos\Opciones\PartidasController@update_partida_cd');
				Route::post('update_partida_ci', 'Proyectos\Opciones\PartidasController@update_partida_ci');
				Route::post('update_partida_gg', 'Proyectos\Opciones\PartidasController@update_partida_gg');
				Route::post('anular_partida_cd', 'Proyectos\Opciones\PartidasController@anular_partida_cd');
				Route::post('anular_partida_ci', 'Proyectos\Opciones\PartidasController@anular_partida_ci');
				Route::post('anular_partida_gg', 'Proyectos\Opciones\PartidasController@anular_partida_gg');
				Route::get('subir_partida_cd/{id}', 'Proyectos\Opciones\PartidasController@subir_partida_cd');
				Route::get('subir_partida_ci/{id}', 'Proyectos\Opciones\PartidasController@subir_partida_ci');
				Route::get('subir_partida_gg/{id}', 'Proyectos\Opciones\PartidasController@subir_partida_gg');
				Route::get('bajar_partida_cd/{id}', 'Proyectos\Opciones\PartidasController@bajar_partida_cd');
				Route::get('bajar_partida_ci/{id}', 'Proyectos\Opciones\PartidasController@bajar_partida_ci');
				Route::get('bajar_partida_gg/{id}', 'Proyectos\Opciones\PartidasController@bajar_partida_gg');
				Route::get('crear_titulos_ci/{id}', 'Proyectos\Opciones\PartidasController@crear_titulos_ci');
				Route::get('crear_titulos_gg/{id}', 'Proyectos\Opciones\PartidasController@crear_titulos_gg');

				Route::post('add_unid_med', 'Proyectos\Catalogos\InsumoController@add_unid_med');
				Route::post('update_unitario_partida_cd', 'Proyectos\Opciones\PresupuestoInternoController@update_unitario_partida_cd');
				Route::get('listar_acus_sin_presup', 'Proyectos\Catalogos\AcuController@listar_acus_sin_presup');

				Route::get('mostrar_acu/{id}', 'Proyectos\Catalogos\AcuController@mostrar_acu');
				Route::get('partida_insumos_precio/{id}/{ins}', 'Proyectos\Catalogos\AcuController@partida_insumos_precio');
				Route::get('listar_acu_detalle/{id}', 'Proyectos\Catalogos\AcuController@listar_acu_detalle');
				Route::post('guardar_acu', 'Proyectos\Catalogos\AcuController@guardar_acu');
				Route::post('actualizar_acu', 'Proyectos\Catalogos\AcuController@update_acu');

				Route::post('guardar_cu', 'Proyectos\Catalogos\AcuController@guardar_cu');
				Route::post('update_cu', 'Proyectos\Catalogos\AcuController@update_cu');
				// Route::get('listar_cus', 'Proyectos\Catalogos\AcuController@listar_cus');
				Route::get('listar_cus', 'Proyectos\Catalogos\NombresAcuController@listar_nombres_cus');

				Route::get('listar_insumos', 'Proyectos\Catalogos\InsumoController@listar_insumos');
				Route::get('mostrar_insumo/{id}', 'Proyectos\Catalogos\InsumoController@mostrar_insumo');
				Route::post('guardar_insumo', 'Proyectos\Catalogos\InsumoController@guardar_insumo');
				Route::get('listar_insumo_precios/{id}', 'Proyectos\Catalogos\InsumoController@listar_insumo_precios');
				// Route::post('guardar_precio', 'ProyectosController@guardar_precio');
				Route::post('actualizar_insumo', 'Proyectos\Catalogos\InsumoController@update_insumo');
				Route::get('listar_opciones_sin_presint', 'Proyectos\Opciones\OpcionesController@listar_opciones_sin_presint');

				Route::get('listar_obs_cd/{id}', 'Proyectos\Opciones\PartidasController@listar_obs_cd');
				Route::get('listar_obs_ci/{id}', 'Proyectos\Opciones\PartidasController@listar_obs_ci');
				Route::get('listar_obs_gg/{id}', 'Proyectos\Opciones\PartidasController@listar_obs_gg');
				Route::get('anular_obs_partida/{id}', 'Proyectos\Opciones\PartidasController@anular_obs_partida');
				Route::post('guardar_obs_partida', 'Proyectos\Opciones\PartidasController@guardar_obs_partida');
			});

			Route::group(['as' => 'cronogramas-internos.', 'prefix' => 'cronogramas-internos'], function () {
				//Cronograma Interno
				Route::get('index', 'Proyectos\Opciones\CronogramaInternoController@view_cronoint')->name('index');
				Route::get('nuevo_cronograma/{id}', 'Proyectos\Opciones\CronogramaInternoController@nuevo_cronograma');
				Route::get('listar_cronograma/{id}', 'Proyectos\Opciones\CronogramaInternoController@listar_cronograma');
				Route::post('guardar_crono', 'Proyectos\Opciones\CronogramaInternoController@guardar_crono');
				Route::get('anular_crono/{id}', 'Proyectos\Opciones\CronogramaInternoController@anular_crono');
				Route::get('ver_gant/{id}', 'Proyectos\Opciones\CronogramaInternoController@ver_gant');
				Route::get('listar_pres_crono/{tc}/{tp}', 'Proyectos\Opciones\CronogramaInternoController@listar_pres_crono');
				Route::get('actualizar_partidas_cronograma/{id}', 'Proyectos\Opciones\CronogramaInternoController@actualizar_partidas_cronograma');

				Route::get('mostrar_acu/{id}', 'Proyectos\Catalogos\AcuController@mostrar_acu');
				Route::get('listar_obs_cd/{id}', 'Proyectos\Opciones\PartidasController@listar_obs_cd');
				Route::get('listar_obs_ci/{id}', 'Proyectos\Opciones\PartidasController@listar_obs_ci');
				Route::get('listar_obs_gg/{id}', 'Proyectos\Opciones\PartidasController@listar_obs_gg');
				Route::get('anular_obs_partida/{id}', 'Proyectos\Opciones\PartidasController@anular_obs_partida');
				Route::post('guardar_obs_partida', 'Proyectos\Opciones\PartidasController@guardar_obs_partida');
			});

			Route::group(['as' => 'cronogramas-valorizados-internos.', 'prefix' => 'cronogramas-valorizados-internos'], function () {
				//Cronograma Valorizado Interno
				Route::get('index', 'Proyectos\Opciones\CronogramaValorizadoInternoController@view_cronovalint')->name('index');
				Route::get('nuevo_crono_valorizado/{id}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@nuevo_crono_valorizado');
				Route::get('mostrar_crono_valorizado/{id}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@mostrar_crono_valorizado');
				Route::get('download_cronoval/{id}/{nro}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@download_cronoval');
				Route::post('guardar_cronoval_presupuesto', 'Proyectos\Opciones\CronogramaValorizadoInternoController@guardar_cronoval_presupuesto');
				Route::get('anular_cronoval/{id}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@anular_cronoval');
				Route::get('listar_pres_cronoval/{tc}/{tp}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@listar_pres_cronoval');
			});
		});

		Route::group(['as' => 'propuestas.', 'prefix' => 'propuestas'], function () {

			Route::group(['as' => 'propuestas-cliente.', 'prefix' => 'propuestas-cliente'], function () {
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
				Route::get('listar_opciones', 'Proyectos\Opciones\OpcionesController@listar_opciones');

				Route::get('listar_obs_cd/{id}', 'ProyectosController@listar_obs_cd');
				Route::get('listar_obs_ci/{id}', 'ProyectosController@listar_obs_ci');
				Route::get('listar_obs_gg/{id}', 'ProyectosController@listar_obs_gg');
				Route::get('anular_obs_partida/{id}', 'ProyectosController@anular_obs_partida');
				Route::post('guardar_obs_partida', 'ProyectosController@guardar_obs_partida');

				Route::get('listar_par_det', 'ProyectosController@listar_par_det');
			});

			Route::group(['as' => 'cronogramas-cliente.', 'prefix' => 'cronogramas-cliente'], function () {
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

			Route::group(['as' => 'cronogramas-valorizados-cliente.', 'prefix' => 'cronogramas-valorizados-cliente'], function () {
				//Cronograma Valorizado Cliente
				Route::get('index', 'ProyectosController@view_cronovalpro')->name('index');
				Route::get('mostrar_cronoval_propuesta/{id}', 'ProyectosController@mostrar_cronoval_propuesta');
				Route::get('listar_cronoval_propuesta/{id}', 'ProyectosController@listar_cronoval_propuesta');
				Route::post('guardar_cronoval_propuesta', 'ProyectosController@guardar_cronoval_propuesta');
				Route::get('download_cronopro/{id}/{nro}', 'ProyectosController@download_cronopro');
				Route::get('listar_propuesta_cronoval/{id}', 'ProyectosController@listar_propuesta_cronoval');
			});

			Route::group(['as' => 'valorizaciones.', 'prefix' => 'valorizaciones'], function () {
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

		Route::group(['as' => 'ejecucion.', 'prefix' => 'ejecucion'], function () {

			Route::group(['as' => 'proyectos.', 'prefix' => 'proyectos'], function () {
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
				Route::get('listar_opciones', 'Proyectos\Opciones\OpcionesController@listar_opciones');
			});

			Route::group(['as' => 'residentes.', 'prefix' => 'residentes'], function () {
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

			Route::group(['as' => 'presupuestos-ejecucion.', 'prefix' => 'presupuestos-ejecucion'], function () {
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

				Route::post('add_unid_med', 'Proyectos\Catalogos\InsumoController@add_unid_med');
				Route::post('update_unitario_partida_cd', 'ProyectosController@update_unitario_partida_cd');
				Route::get('listar_acus_sin_presup', 'ProyectosController@listar_acus_sin_presup');

				Route::get('mostrar_acu/{id}', 'ProyectosController@mostrar_acu');
				Route::get('partida_insumos_precio/{id}/{ins}', 'ProyectosController@partida_insumos_precio');
				Route::get('listar_acu_detalle/{id}', 'ProyectosController@listar_acu_detalle');
				Route::post('guardar_acu', 'ProyectosController@guardar_acu');
				Route::post('actualizar_acu', 'ProyectosController@update_acu');

				Route::post('guardar_cu', 'ProyectosController@guardar_cu');
				Route::post('update_cu', 'ProyectosController@update_cu');
				Route::get('listar_cus', 'Proyectos\Catalogos\NombresAcuController@listar_nombres_cus');

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

			Route::group(['as' => 'cronogramas-ejecucion.', 'prefix' => 'cronogramas-ejecucion'], function () {

				Route::get('index', 'ProyectosController@view_cronoeje')->name('index');
				Route::get('nuevo_cronograma/{id}', 'ProyectosController@nuevo_cronograma');
				Route::get('listar_acus_cronograma/{id}', 'ProyectosController@listar_acus_cronograma');
				Route::get('listar_pres_crono/{tc}/{tp}', 'ProyectosController@listar_pres_crono');
				Route::get('listar_pres_cronoval/{tc}/{tp}', 'ProyectosController@listar_pres_cronoval');
				Route::post('guardar_crono', 'ProyectosController@guardar_crono');
				Route::get('anular_crono/{id}', 'ProyectosController@anular_crono');
				Route::get('ver_gant/{id}', 'ProyectosController@ver_gant');
				Route::get('listar_cronograma/{id}', 'ProyectosController@listar_cronograma');
				Route::get('mostrar_acu/{id}', 'ProyectosController@mostrar_acu');
				Route::get('listar_obs_cd/{id}', 'ProyectosController@listar_obs_cd');
			});

			Route::group(['as' => 'cronogramas-valorizados-ejecucion.', 'prefix' => 'cronogramas-valorizados-ejecucion'], function () {
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

		Route::group(['as' => 'reportes.', 'prefix' => 'reportes'], function () {

			Route::group(['as' => 'curvas.', 'prefix' => 'curvas'], function () {
				//Curvas S
				Route::get('index', 'ProyectosController@view_curvas')->name('index');
				Route::get('getProgramadoValorizado/{id}/{pre}', 'ProyectosController@getProgramadoValorizado');
				Route::get('listar_propuestas_preseje', 'ProyectosController@listar_propuestas_preseje');
			});

			Route::group(['as' => 'saldos.', 'prefix' => 'saldos'], function () {
				//Saldos
				Route::get('index', 'ProyectosController@view_saldos_pres')->name('index');
				Route::get('listar_saldos_presupuesto/{id}', 'ProyectosController@listar_saldos_presupuesto');
				Route::get('listar_estructuras_preseje', 'ProyectosController@listar_estructuras_preseje');
				Route::get('ver_detalle_partida/{id}', 'ProyectosController@ver_detalle_partida');
			});

			Route::group(['as' => 'opciones-relaciones.', 'prefix' => 'opciones-relaciones'], function () {
				//Opciones y Relaciones
				Route::get('index', 'ProyectosController@view_opciones_todo')->name('index');
				Route::get('listar_opciones_todo', 'ProyectosController@listar_opciones_todo');
			});
		});

		Route::group(['as' => 'configuraciones.', 'prefix' => 'configuraciones'], function () {

			Route::group(['as' => 'estructuras.', 'prefix' => 'estructuras'], function () {
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

	Route::group(['as' => 'administracion.', 'prefix' => 'admin'], function () {
		// administracion
		Route::get('index', 'AdministracionController@view_main_administracion')->name('index');

		Route::group(['as' => 'notificaciones.', 'prefix' => 'notificaciones'], function () {
			Route::get('index', 'AdministracionController@view_notificaciones')->name('index');
			Route::get('no-leidas', 'AdministracionController@listar_notificaciones_no_leidas');
			Route::get('leidas', 'AdministracionController@listar_notificaciones_leidas');
			Route::put('marcar-leida/{id?}', 'AdministracionController@marcar_notificacion_leida');
			Route::put('marcar-no-leida/{id?}', 'AdministracionController@marcar_notificacion_no_leida');
		});
	});



	Route::group(['as' => 'logistica.', 'prefix' => 'logistica'], function () {

		// Logística
		Route::get('index', 'LogisticaController@view_main_logistica')->name('index');

		Route::group(['as' => 'gestion-logistica.', 'prefix' => 'gestion-logistica'], function () {

			Route::group(['as' => 'ocam.', 'prefix' => 'ocam'], function () {
				Route::get('index', 'OCAMController@view_lista_ocams')->name('index');
				Route::group(['as' => 'listado.', 'prefix' => 'listado'], function () {
					Route::get('ordenes-propias/{empresa?}/{year_publicacion?}/{condicion?}', 'OCAMController@lista_ordenes_propias')->name('ordenes-propias');
					Route::get('producto-base-o-transformado/{id_requerimiento?}/{tiene_transformacion?}', 'OCAMController@listaProductosBaseoTransformado')->name('producto-base-o-transformado');
				});
			});

			Route::group(['as' => 'requerimiento.', 'prefix' => 'requerimiento'], function () {

				Route::group(['as' => 'elaboracion.', 'prefix' => 'elaboracion'], function () {
					Route::get('index', 'Logistica\RequerimientoController@index')->name('index');
					Route::get('mostrar/{idRequerimiento?}', 'Logistica\RequerimientoController@mostrar')->name('mostrar');
					// Route::get('index/{idRequerimiento?}', 'Logistica\RequerimientoController@index')->name('index');

					Route::get('tipo-cambio-compra/{fecha}', 'Almacen\Reporte\SaldosController@tipo_cambio_compra');
					Route::get('lista-divisiones', 'Logistica\RequerimientoController@listaDivisiones');
					Route::get('mostrar-partidas/{idGrupo?}/{idProyecto?}', 'Logistica\RequerimientoController@mostrarPartidas')->name('mostrar-partidas');
					Route::get('mostrar-centro-costos', 'Finanzas\CentroCosto\CentroCostoController@mostrarCentroCostosSegunGrupoUsuario')->name('mostrar-centro-costos');
					Route::get('mostrar-categoria-adjunto', 'Logistica\RequerimientoController@mostrarCategoriaAdjunto')->name('mostrar-categoria-adjunto');
					Route::post('guardar-requerimiento', 'Logistica\RequerimientoController@guardarRequerimiento')->name('guardar-requerimiento');
					Route::post('actualizar-requerimiento', 'Logistica\RequerimientoController@actualizarRequerimiento')->name('actualizar-requerimiento');
					Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
					Route::get('mostrar-requerimiento/{id?}/{codigo?}', 'Logistica\RequerimientoController@mostrarRequerimiento')->name('mostrar-requerimiento');
					Route::post('elaborados', 'Logistica\RequerimientoController@listarRequerimientosElaborados')->name('elaborados');
					Route::get('imprimir-requerimiento-pdf/{id}/{codigo}', 'Logistica\RequerimientoController@generar_requerimiento_pdf');
					Route::put('anular-requerimiento/{id_requerimiento?}', 'Logistica\RequerimientoController@anularRequerimiento')->name('anular-requerimiento');

					Route::get('trazabilidad-detalle-requerimiento/{id}', 'Logistica\RequerimientoController@mostrarTrazabilidadDetalleRequerimiento');


					Route::post('guardar', 'LogisticaController@guardar_requerimiento')->name('guardar');
					Route::put('actualizar/{id?}', 'LogisticaController@actualizar_requerimiento')->name('actualizar');
					// Route::get('select-sede-by-empresa/{id?}', 'LogisticaController@select_sede_by_empresa')->name('select-sede-by-empresa');
					// Route::get('select-sede-by-empresa/{id?}', 'LogisticaController@sedesAcceso')->name('select-sede-by-empresa');
					Route::post('copiar-requerimiento/{id?}', 'LogisticaController@copiar_requerimiento')->name('copiar-requerimiento');
					Route::get('telefonos-cliente/{id_persona?}/{id_cliente?}', 'LogisticaController@telefonos_cliente')->name('telefonos-cliente');
					Route::get('direcciones-cliente/{id_persona?}/{id_cliente?}', 'LogisticaController@direcciones_cliente')->name('direcciones-cliente');
					Route::get('cuentas-cliente/{id_persona?}/{id_cliente?}', 'LogisticaController@cuentas_cliente')->name('cuentas-cliente');
					Route::post('guardar-cuentas-cliente', 'LogisticaController@guardar_cuentas_cliente')->name('guardar-cuentas-cliente');
					Route::get('emails-cliente/{id_persona?}/{id_cliente?}', 'LogisticaController@emails_cliente')->name('emails-cliente');
					Route::get('listar_ubigeos', 'AlmacenController@listar_ubigeos');
					Route::get('listar_personas', 'RecursosHumanosController@mostrar_persona_table')->name('listar_personas');
					Route::get('mostrar_clientes', 'Comercial\ClienteController@mostrar_clientes')->name('mostrar_clientes');;
					Route::get('cargar_almacenes/{id_sede}', 'Almacen\Ubicacion\AlmacenController@cargar_almacenes');
					Route::post('guardar-archivos-adjuntos-detalle-requerimiento', 'LogisticaController@guardar_archivos_adjuntos_detalle_requerimiento');
					Route::put('eliminar-archivo-adjunto-detalle-requerimiento/{id_archivo}', 'LogisticaController@eliminar_archivo_adjunto_detalle_requerimiento');
					Route::post('guardar-archivos-adjuntos-requerimiento', 'LogisticaController@guardar_archivos_adjuntos_requerimiento');
					Route::put('eliminar-archivo-adjunto-requerimiento/{id_archivo}', 'LogisticaController@eliminar_archivo_adjunto_requerimiento');
					Route::get('mostrar-archivos-adjuntos-requerimiento/{id_requerimiento?}/{categoria?}', 'LogisticaController@mostrar_archivos_adjuntos_requerimiento');
					Route::get('listar_almacenes', 'Almacen\Ubicacion\AlmacenController@mostrar_almacenes');
					Route::get('mostrar-sede', 'ConfiguracionController@mostrarSede');
					Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
					Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');
					// Route::get('verTrazabilidadRequerimiento/{id}', 'DistribucionController@verTrazabilidadRequerimiento');
					Route::get('getCodigoRequerimiento/{id}', 'LogisticaController@getCodigoRequerimiento');
					Route::get('mostrar-archivos-adjuntos/{id_detalle_requerimiento}', 'LogisticaController@mostrar_archivos_adjuntos');
					Route::post('save_cliente', 'LogisticaController@save_cliente');
					Route::get('listar_saldos/{id}', 'AlmacenController@listar_saldos');
					Route::get('listar_opciones', 'ProyectosController@listar_opciones');
					// Route::get('listar_partidas/{id_grupo?}/{id_op_com?}', 'EquipoController@listar_partidas');
					Route::get('listar-saldos-por-almacen', 'AlmacenController@listar_saldos_por_almacen');
					Route::get('listar-saldos-por-almacen/{id_producto}', 'AlmacenController@listar_saldos_por_almacen_producto');
					Route::get('obtener-promociones/{id_producto}/{id_almacen}', 'LogisticaController@obtener_promociones');
					Route::post('migrar_venta_directa', 'MigrateSoftLinkController@migrar_venta_directa');
					Route::post('guardar-producto', 'AlmacenController@guardar_producto')->name('guardar-producto');

					Route::get('cuadro-costos/{id_cc?}', 'RequerimientoController@cuadro_costos')->name('cuadro-costos');
					Route::get('detalle-cuadro-costos/{id_cc?}', 'RequerimientoController@detalle_cuadro_costos')->name('detalle-cuadro-costos');
					Route::post('obtener-construir-cliente', 'RequerimientoController@obtenerConstruirCliente')->name('obtener-construir-cliente');
					Route::get('proyectos-activos', 'ProyectosController@listar_proyectos_activos')->name('proyectos-activos');
					Route::get('grupo-select-item-para-compra', 'ComprasPendientesController@getGrupoSelectItemParaCompra')->name('grupo-select-item-para-compra');

					Route::get('mostrar-fuente', 'LogisticaController@mostrarFuente')->name('mostrar-fuente');
					Route::post('guardar-fuente', 'LogisticaController@guardarFuente')->name('guardar-fuente');
					Route::post('anular-fuente', 'LogisticaController@anularFuente')->name('anular-fuente');
					Route::post('actualizar-fuente', 'LogisticaController@actualizarFuente')->name('actualizar-fuente');
					Route::post('guardar-detalle-fuente', 'LogisticaController@guardarDetalleFuente')->name('guardar-detalle-fuente');
					Route::get('mostrar-fuente-detalle/{fuente_id?}', 'LogisticaController@mostrarFuenteDetalle')->name('mostrar-fuente-detalle');
					Route::post('anular-detalle-fuente', 'LogisticaController@anularDetalleFuente')->name('anular-detalle-fuente');
					Route::post('actualizar-detalle-fuente', 'LogisticaController@actualizarDetalleFuente')->name('actualizar-detalle-fuente');
					Route::get('buscar-stock-almacenes/{id_item?}', 'RequerimientoController@buscarStockEnAlmacenes')->name('buscar-stock-almacenes');
					Route::get('listar_trabajadores', 'ProyectosController@listar_trabajadores');
				});

				Route::group(['as' => 'listado.', 'prefix' => 'listado'], function () {
					Route::get('index', 'Logistica\RequerimientoController@viewLista')->name('index');
					Route::post('elaborados', 'Logistica\RequerimientoController@listarRequerimientosElaborados')->name('elaborados');
					Route::get('ver-flujos/{req?}/{doc?}', 'Logistica\RequerimientoController@flujoAprobacion')->name('ver-flujos');
					Route::get('mostrar-divisiones/{idGrupo?}', 'Logistica\RequerimientoController@mostrarDivisionesDeGrupo')->name('mostrar-divisiones-de-grupo');

					Route::get('requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@mostrarCabeceraRequerimiento')->name('mostrar-cabecera-requerimiento');
					Route::get('historial-aprobacion/{idRequerimiento?}', 'Logistica\RequerimientoController@mostrarHistorialAprobacion')->name('mostrar-historial-aprobacion');
					Route::get('trazabilidad-detalle-requerimiento/{id}', 'Logistica\RequerimientoController@mostrarTrazabilidadDetalleRequerimiento');

					// Route::get('requerimientoAPago/{id}', 'Logistica\RequerimientoController@requerimientoAPago')->name('requerimiento-a-pago');

					Route::get('mostrar-requerimiento/{id?}/{codigo?}', 'Logistica\RequerimientoController@mostrarRequerimiento')->name('mostrar-requerimiento');
					Route::put('anular-requerimiento/{id_requerimiento?}', 'Logistica\RequerimientoController@anularRequerimiento')->name('anular-requerimiento');
					Route::get('imprimir-requerimiento-pdf/{id}/{codigo}', 'Logistica\RequerimientoController@generar_requerimiento_pdf');

					// Route::get('explorar-requerimiento/{id_requerimiento?}', 'Logistica\RequerimientoController@explorar_requerimiento')->name('explorar-requerimiento');

					// Route::get('listar/{empresa?}/{sede?}/{grupo?}', 'LogisticaController@listar_requerimiento_v2')->name('listar');
					// Route::get('empresa', 'LogisticaController@getIdEmpresa')->name('empresa');
					Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');


					// Route::get('detalleRequerimiento/{id}', 'Logistica\RequerimientoController@detalleRequerimiento')->name('detalle-requerimiento');

				});
				Route::group(['as' => 'aprobar.', 'prefix' => 'aprobar'], function () {
					Route::get('index', 'Logistica\RequerimientoController@viewAprobar')->name('index');
					Route::post('listado-aprobacion', 'Logistica\RequerimientoController@listadoAprobacion')->name('listado-aprobacion');
					Route::get('mostrar-requerimiento/{id?}/{codigo?}', 'Logistica\RequerimientoController@mostrarRequerimiento')->name('mostrar-requerimiento');
					Route::post('guardar-respuesta', 'Logistica\RequerimientoController@guardarRespuesta')->name('guardar-respuesta');


					Route::get('ver-flujos/{req?}/{doc?}', 'Logistica\RequerimientoController@flujoAprobacion')->name('ver-flujos');
					Route::get('explorar-requerimiento/{id_requerimiento?}', 'Logistica\RequerimientoController@explorar_requerimiento')->name('explorar-requerimiento');
					Route::post('aprobar-documento', 'Logistica\RequerimientoController@aprobarDocumento')->name('aprobar-documento');
					Route::post('observar-documento', 'Logistica\RequerimientoController@observarDocumento')->name('observar-documento');
					Route::post('anular-documento', 'Logistica\RequerimientoController@anularDocumento')->name('anular-documento');
					Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
					Route::get('imprimir-requerimiento-pdf/{id}/{codigo}', 'Logistica\RequerimientoController@generar_requerimiento_pdf');
				});
				Route::group(['as' => 'mapeo.', 'prefix' => 'mapeo'], function () {

					Route::get('index', 'Logistica\Requerimientos\MapeoProductosController@view_mapeo_productos')->name('index');
					Route::post('listarRequerimientos', 'Logistica\Requerimientos\MapeoProductosController@listarRequerimientos')->name('listar-requerimiento');
					Route::get('itemsRequerimiento/{id}', 'Logistica\Requerimientos\MapeoProductosController@itemsRequerimiento')->name('items-requerimiento');
					Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
					Route::post('actualizarSugeridos', 'Almacen\Catalogo\ProductoController@actualizarSugeridos');
					Route::get('listarProductosSugeridos', 'Almacen\Catalogo\ProductoController@listarProductosSugeridos');
					Route::get('mostrar_prods_sugeridos/{part}/{desc}', 'Almacen\Catalogo\ProductoController@mostrar_prods_sugeridos');
					Route::post('guardar_mapeo_productos', 'Logistica\Requerimientos\MapeoProductosController@guardar_mapeo_productos')->name('guardar-mapeo-productos');
					Route::get('mostrar_categorias_tipo/{id}', 'Almacen\Catalogo\ProductoController@mostrar_categorias_tipo');
					Route::post('anular_item', 'Logistica\Requerimientos\MapeoProductosController@anular_item')->name('anular-item');
				});
			});

			Route::group(['as' => 'compras.', 'prefix' => 'compras'], function () {
				Route::group(['as' => 'pendientes.', 'prefix' => 'pendientes'], function () {
					Route::get('index', 'ComprasPendientesController@viewComprasPendientes')->name('index');
					Route::get('requerimientos-pendientes/{empresa?}/{sede?}/{fechaDesde?}/{fechaHasta?}/{reserva?}/{orden?}', 'ComprasPendientesController@listarRequerimientosPendientes')->name('requerimientos-pendientes');
					// Route::post('lista_items-cuadro-costos-por-requerimiento-pendiente-compra', 'ComprasPendientesController@get_lista_items_cuadro_costos_por_id_requerimiento_pendiente_compra')->name('lista_items-cuadro-costos-por-requerimiento-pendiente-compra');
					// Route::post('tiene-items-para-compra', 'ComprasPendientesController@tieneItemsParaCompra')->name('tiene-items-para-compra');
					Route::post('lista_items-cuadro-costos-por-requerimiento', 'ComprasPendientesController@get_lista_items_cuadro_costos_por_id_requerimiento')->name('lista_items-cuadro-costos-por-requerimiento');
					Route::get('grupo-select-item-para-compra', 'ComprasPendientesController@getGrupoSelectItemParaCompra')->name('grupo-select-item-para-compra');
					Route::post('guardar-reserva-almacen', 'ComprasPendientesController@guardarReservaAlmacen')->name('guardar-reserva-almacen');
					Route::post('anular-reserva-almacen', 'ComprasPendientesController@anularReservaAlmacen')->name('anular-reserva-almacen');
					Route::post('buscar-item-catalogo', 'ComprasPendientesController@buscarItemCatalogo')->name('buscar-item-catalogo');
					Route::post('guardar-items-detalle-requerimiento', 'ComprasPendientesController@guardarItemsEnDetalleRequerimiento')->name('guardar-items-detalle-requerimiento');
					Route::get('listar-almacenes', 'Almacen\Ubicacion\AlmacenController@mostrar_almacenes')->name('listar-almacenes');
					Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');

					Route::post('guardar-producto', 'AlmacenController@guardar_producto')->name('guardar-producto');

					Route::get('itemsRequerimiento/{id}', 'Logistica\Requerimientos\MapeoProductosController@itemsRequerimiento')->name('items-requerimiento');
					Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
					Route::post('actualizarSugeridos', 'Almacen\Catalogo\ProductoController@actualizarSugeridos');
					Route::get('listarProductosSugeridos', 'Almacen\Catalogo\ProductoController@listarProductosSugeridos');
					Route::get('mostrar_prods_sugeridos/{part}/{desc}', 'Almacen\Catalogo\ProductoController@mostrar_prods_sugeridos');
					Route::post('guardar_mapeo_productos', 'Logistica\Requerimientos\MapeoProductosController@guardar_mapeo_productos')->name('guardar-mapeo-productos');
					Route::post('anular_item', 'Logistica\Requerimientos\MapeoProductosController@anular_item')->name('anular-item');
					Route::get('mostrar_categorias_tipo/{id}', 'Almacen\Catalogo\ProductoController@mostrar_categorias_tipo');
					Route::get('detalle-requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@detalleRequerimiento')->name('detalle-requerimientos');
					Route::get('detalle-requeriento-para-reserva/{idDetalleRequerimiento?}', 'Logistica\RequerimientoController@detalleRequerimientoParaReserva')->name('detalle-requerimiento-para-reserva');
					Route::get('historial-reserva-producto/{idDetalleRequerimiento?}', 'Logistica\RequerimientoController@historialReservaProducto')->name('historial-reserva-producto');
					Route::get('todo-detalle-requeriento/{idRequerimiento?}/{transformadosONoTransformados?}', 'Logistica\RequerimientoController@todoDetalleRequerimiento')->name('todo-detalle-requerimiento');

					Route::get('mostrar_tipos_clasificacion/{id}', 'Almacen\Catalogo\TipoProductoController@mostrar_tipos_clasificacion');
				});

				Route::group(['as' => 'ordenes.', 'prefix' => 'ordenes'], function () {
					Route::group(['as' => 'elaborar.', 'prefix' => 'elaborar'], function () {
						Route::get('index', 'OrdenController@view_crear_orden_requerimiento')->name('index');
						Route::post('requerimiento-detallado', 'OrdenController@ObtenerRequerimientoDetallado')->name('requerimiento-detallado');
						Route::post('detalle-requerimiento-orden', 'OrdenController@get_detalle_requerimiento_orden')->name('detalle-requerimiento-orden');
						Route::post('guardar', 'OrdenController@guardar_orden_por_requerimiento')->name('guardar');
						Route::post('actualizar', 'OrdenController@actualizar_orden_por_requerimiento')->name('actualizar');
						Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
						Route::get('contacto-proveedor/{idProveedor?}', 'OrdenController@obtenerContactoProveedor');
						Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');
						Route::put('actualizar-estado-detalle-requerimiento/{id_detalle_req?}/{estado?}', 'OrdenController@update_estado_detalle_requerimiento')->name('actualizar-estado-detalle-requerimiento');
						Route::post('guardar-producto', 'AlmacenController@guardar_producto')->name('guardar-producto');
						Route::get('listar-almacenes', 'Almacen\Ubicacion\AlmacenController@mostrar_almacenes')->name('listar-almacenes');
						Route::get('listar_ubigeos', 'Almacen\Ubicacion\AlmacenController@listar_ubigeos');
						Route::get('lista_contactos_proveedor/{id_proveedor?}', 'OrdenController@lista_contactos_proveedor');
						Route::get('generar-orden-pdf/{id?}', 'OrdenController@generar_orden_por_requerimiento_pdf')->name('generar-orden-por-requerimiento-pdf'); // PDF
						Route::get('listar_trabajadores', 'ProyectosController@listar_trabajadores');
						Route::post('guardar_contacto', 'OrdenController@guardar_contacto');
						Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');

						Route::get('listar-historial-ordenes-elaboradas', 'OrdenController@listaHistorialOrdenes');
						Route::get('mostrar-orden/{id_orden?}', 'OrdenController@mostrarOrden');
						Route::put('anular/{id_orden?}', 'OrdenController@anularOrden')->name('anular');
						Route::get('tipo-cambio-compra/{fecha}', 'Almacen\Reporte\SaldosController@tipo_cambio_compra');
						Route::get('requerimientos-pendientes/{empresa?}/{sede?}/{fechaDesde?}/{fechaHasta?}/{reserva?}/{orden?}', 'ComprasPendientesController@listarRequerimientosPendientes')->name('requerimientos-pendientes');
						Route::get('detalle-requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@detalleRequerimiento')->name('detalle-requerimientos');
						Route::get('listar-cuentas-bancarias-proveedor/{idProveedor?}', 'OrdenController@listarCuentasBancariasProveedor')->name('listar-cuentas-bancarias-proveedor');
						Route::post('guardar-cuenta-bancaria-proveedor', 'OrdenController@guardarCuentaBancariaProveedor');
					});
					Route::group(['as' => 'listado.', 'prefix' => 'listado'], function () {
						Route::get('index', 'OrdenController@view_listar_ordenes')->name('index');
						Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
						Route::get('generar-orden-pdf/{id?}', 'OrdenController@generar_orden_por_requerimiento_pdf')->name('generar-orden-por-requerimiento-pdf'); // PDF
						Route::get('facturas/{id_orden}', 'OrdenController@obtenerFacturas');
						//nivel cabecera 
						Route::get('listar-ordenes/{tipoOrden?}/{vinculadoPor?}/{empresa?}/{sede?}/{tipoProveedor?}/{enAlmacen?}/{signoOrden?}/{montoOrden?}/{estado?}', 'OrdenController@listarOrdenes');
						Route::get('detalle-orden/{id_orden}', 'OrdenController@detalleOrden');
						Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');
						// Route::get('generar_orden_pdf/{id}', 'OrdenController@generar_orden_pdf'); // PDF
						Route::get('verSession', 'LogisticaController@verSession');
						// Route::get('explorar-orden/{id_orden}', 'LogisticaController@explorar_orden'); 
						Route::get('listar-ordenes-excel', 'OrdenController@exportExcelListaOrdenes')->name('listar-ordenes-excel');

						// nivel item
						Route::get('listar-detalle-orden/{tipoOrden?}/{vinculadoPor?}/{empresa?}/{sede?}/{tipoProveedor?}/{enAlmacen?}/{signoSubtotal?}/{subtotal?}/{estado?}', 'OrdenController@listarDetalleOrden')->name('ordenes-en-proceso');
						Route::get('ver-orden/{id_orden?}', 'OrdenController@ver_orden');
						Route::post('actualizar-estado', 'OrdenController@update_estado_orden')->name('actualizar-estado-orden');
						Route::post('actualizar-estado-detalle', 'OrdenController@update_estado_item_orden')->name('actualizar-estado-detalle-orden');
						Route::put('anular/{id_orden?}', 'OrdenController@anularOrden')->name('anular');
						Route::get('documentos-vinculados/{id_orden?}', 'OrdenController@documentosVinculadosOrden')->name('documentos-vinculados');


						// Route::put('guardar_aprobacion_orden/', 'LogisticaController@guardar_aprobacion_orden'); 
						// Route::post('guardar_pago_orden', 'LogisticaController@guardar_pago_orden');
						// Route::get('eliminar_pago/{id_pago}', 'LogisticaController@eliminar_pago'); 
					});
				});
			});

			Route::group(['as' => 'proveedores.', 'prefix' => 'proveedores'], function () {
				Route::post('guardar', 'Logistica\ProveedoresController@guardar')->name('guardar');
				Route::get('mostrar/{idProveedor?}', 'Logistica\ProveedoresController@mostrar')->name('mostrar');
				Route::post('actualizar', 'Logistica\ProveedoresController@actualizar')->name('actualizar');
				Route::post('anular', 'Logistica\ProveedoresController@anular')->name('anular');
				Route::get('index', 'Logistica\ProveedoresController@index')->name('index');
				Route::post('obtener-data-listado', 'Logistica\ProveedoresController@obtenerDataListado')->name('obtenerDataListado');
				Route::get('listar_ubigeos', 'AlmacenController@listar_ubigeos')->name('listarUbigeos');
			});

			Route::group(['as' => 'cotizacion.', 'prefix' => 'cotizacion'], function () {
				Route::group(['as' => 'gestionar.', 'prefix' => 'gestionar'], function () {
					Route::get('index', 'LogisticaController@view_gestionar_cotizaciones')->name('index');
					Route::get('select-sede-by-empresa/{id?}', 'LogisticaController@select_sede_by_empresa')->name('select-sede-by-empresa');
					Route::get('listaCotizacionesPorGrupo/{id_cotizacion}', 'LogisticaController@listaCotizacionesPorGrupo');
					Route::get('requerimientos_entrante_a_cotizacion/{id_empresa}/{id_sede}', 'CotizacionController@requerimientos_entrante_a_cotizacion');
					Route::get('detalle_requerimiento', 'RequerimientoController@detalle_requerimiento');
					Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');

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
		});

		Route::get('getEstadosRequerimientos/{filtro}', 'DistribucionController@getEstadosRequerimientos');
		Route::get('listarEstadosRequerimientos/{id}/{filtro}', 'DistribucionController@listarEstadosRequerimientos');

		Route::group(['as' => 'distribucion.', 'prefix' => 'distribucion'], function () {
			//PENDIENTE
			Route::group(['as' => 'despachos.', 'prefix' => 'control-despachos'], function () {
				//Ordenes Despacho
				Route::get('index', 'DistribucionController@view_ordenesDespacho')->name('index');
				Route::get('listarRequerimientosEnProceso', 'DistribucionController@listarRequerimientosEnProceso');
				Route::get('listarRequerimientosEnTransformacion', 'DistribucionController@listarRequerimientosEnTransformacion');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('verDetalleIngreso/{id}', 'DistribucionController@verDetalleIngreso');
				Route::post('guardar_orden_despacho', 'DistribucionController@guardar_orden_despacho');
				Route::get('listarOrdenesDespacho', 'DistribucionController@listarOrdenesDespacho');
				Route::get('verDetalleDespacho/{id}', 'DistribucionController@verDetalleDespacho');
				Route::post('guardar_grupo_despacho', 'DistribucionController@guardar_grupo_despacho');
				Route::post('despacho_anular_requerimiento', 'DistribucionController@anular_requerimiento');
				Route::get('anular_orden_despacho/{id}/{tp}', 'DistribucionController@anular_orden_despacho');
				Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
				Route::get('mostrarTransportistas', 'DistribucionController@mostrarTransportistas');
				Route::get('listarGruposDespachados', 'DistribucionController@listarGruposDespachados');
				Route::get('listarGruposDespachadosPendientesCargo', 'DistribucionController@listarGruposDespachadosPendientesCargo');
				Route::get('verDetalleGrupoDespacho/{id}', 'DistribucionController@verDetalleGrupoDespacho');
				Route::post('despacho_transportista', 'DistribucionController@despacho_transportista');
				Route::post('despacho_revertir_despacho', 'DistribucionController@despacho_revertir_despacho');
				Route::post('despacho_conforme', 'DistribucionController@despacho_conforme');
				Route::post('despacho_no_conforme', 'DistribucionController@despacho_no_conforme');
				Route::get('imprimir_despacho/{id}', 'DistribucionController@imprimir_despacho');
				Route::get('listarAdjuntosOrdenDespacho/{id}', 'DistribucionController@listarAdjuntosOrdenDespacho');
				Route::post('guardar_od_adjunto', 'DistribucionController@guardar_od_adjunto');
				Route::get('anular_od_adjunto/{id}', 'DistribucionController@anular_od_adjunto');
				Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');
				Route::get('mostrar_clientes', 'Comercial\ClienteController@mostrar_clientes');
				Route::get('listar_personas', 'RecursosHumanosController@mostrar_persona_table');
				Route::get('listarDetalleTransferencias/{id}', 'Almacen\Movimiento\TransferenciaController@listarDetalleTransferencias');
				Route::get('listar_ubigeos', 'AlmacenController@listar_ubigeos');
				Route::post('save_cliente', 'LogisticaController@save_cliente');
				Route::get('listarRequerimientosElaborados', 'DistribucionController@listarRequerimientosElaborados');
				Route::get('listarRequerimientosConfirmados', 'DistribucionController@listarRequerimientosConfirmados');
				Route::get('actualizaCantidadDespachosTabs', 'DistribucionController@actualizaCantidadDespachosTabs');
				Route::get('mostrar_prods', 'AlmacenController@mostrar_prods');
				Route::get('verSeries/{id}', 'DistribucionController@verSeries');
				Route::post('guardar_producto', 'AlmacenController@guardar_producto');
				Route::get('getTimelineOrdenDespacho/{id}', 'DistribucionController@getTimelineOrdenDespacho');
				Route::post('guardarEstadoTimeLine', 'DistribucionController@guardarEstadoTimeLine');
				Route::post('mostrarEstados', 'DistribucionController@mostrarEstados');
				Route::get('enviarFacturar/{id}', 'DistribucionController@enviarFacturar');
			});

			Route::group(['as' => 'trazabilidad-requerimientos.', 'prefix' => 'trazabilidad-requerimientos'], function () {

				Route::get('index', 'DistribucionController@view_trazabilidad_requerimientos')->name('index');
				Route::post('listarRequerimientosTrazabilidad', 'DistribucionController@listarRequerimientosTrazabilidad');
				Route::get('verTrazabilidadRequerimiento/{id}', 'DistribucionController@verTrazabilidadRequerimiento');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('imprimir_despacho/{id}', 'DistribucionController@imprimir_despacho');
				Route::get('listarAdjuntosOrdenDespacho/{id}', 'DistribucionController@listarAdjuntosOrdenDespacho');
				Route::post('guardar_od_adjunto', 'DistribucionController@guardar_od_adjunto');
				Route::get('anular_od_adjunto/{id}', 'DistribucionController@anular_od_adjunto');
			});

			Route::group(['as' => 'guias-transportistas.', 'prefix' => 'guias-transportistas'], function () {

				Route::get('index', 'DistribucionController@view_guias_transportistas')->name('index');
				Route::get('listarGuiasTransportistas', 'DistribucionController@listarGuiasTransportistas');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('imprimir_despacho/{id}', 'DistribucionController@imprimir_despacho');
			});

			Route::group(['as' => 'ordenes-transformacion.', 'prefix' => 'ordenes-transformacion'], function () {

				Route::get('index', 'Logistica\Distribucion\OrdenesTransformacionController@view_ordenes_transformacion')->name('index');
				Route::get('listarRequerimientosEnProceso', 'Logistica\Distribucion\OrdenesTransformacionController@listarRequerimientosEnProceso');
				Route::get('listarDetalleTransferencias/{id}', 'Almacen\Movimiento\TransferenciaController@listarDetalleTransferencias');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::post('guardarOrdenDespachoInterno', 'Logistica\Distribucion\OrdenesTransformacionController@guardarOrdenDespachoInterno');
				Route::get('verDetalleInstrucciones/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleInstrucciones');
				Route::get('anular_orden_despacho/{id}/{tp}', 'Almacen\Movimiento\SalidasPendientesController@anular_orden_despacho');
				Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');
			});
		});

		// });

		Route::group(['as' => 'notificaciones.', 'prefix' => 'notificaciones'], function () {

			Route::get('index', 'AdministracionController@view_notificaciones')->name('index');
			// Route::get('get_email_usuario_por_rol/{des?}/{sede?}/{emoresa?}', 'LogisticaController@get_email_usuario_por_rol')->name('get_email_usuario_por_rol');
			Route::get('no-leidas', 'AdministracionController@listar_notificaciones_no_leidas');
			Route::get('leidas', 'AdministracionController@listar_notificaciones_leidas');
			Route::put('marcar-leida/{id?}', 'AdministracionController@marcar_notificacion_leida');
			Route::put('marcar-no-leida/{id?}', 'AdministracionController@marcar_notificacion_no_leida');
		});
	});

	/**Almacén */
	Route::group(['as' => 'almacen.', 'prefix' => 'almacen'], function () {

		Route::get('index', 'AlmacenController@view_main_almacen')->name('index');

		Route::get('getEstadosRequerimientos/{filtro}', 'DistribucionController@getEstadosRequerimientos');
		Route::get('listarEstadosRequerimientos/{id}/{filtro}', 'DistribucionController@listarEstadosRequerimientos');

		Route::group(['as' => 'catalogos.', 'prefix' => 'catalogos'], function () {

			Route::group(['as' => 'tipos.', 'prefix' => 'tipos'], function () {
				//Tipo Producto
				Route::get('index', 'Almacen\Catalogo\TipoProductoController@view_tipo')->name('index');
				Route::get('listar_tipos', 'Almacen\Catalogo\TipoProductoController@mostrar_tp_productos');
				Route::get('mostrar_tipo/{id}', 'Almacen\Catalogo\TipoProductoController@mostrar_tp_producto');
				Route::post('guardar_tipo', 'Almacen\Catalogo\TipoProductoController@guardar_tp_producto');
				Route::post('actualizar_tipo', 'Almacen\Catalogo\TipoProductoController@update_tp_producto');
				Route::get('anular_tipo/{id}', 'Almacen\Catalogo\TipoProductoController@anular_tp_producto');
				Route::get('revisarTipo/{id}', 'Almacen\Catalogo\TipoProductoController@tipo_revisar_relacion');
			});

			Route::group(['as' => 'categorias.', 'prefix' => 'categorias'], function () {
				//Categoria
				Route::get('index', 'Almacen\Catalogo\CategoriaController@view_categoria')->name('index');
				Route::get('listar_categorias', 'Almacen\Catalogo\CategoriaController@mostrar_categorias');
				Route::get('mostrar_categoria/{id}', 'Almacen\Catalogo\CategoriaController@mostrar_categoria');
				Route::post('guardar_categoria', 'Almacen\Catalogo\CategoriaController@guardar_categoria');
				Route::post('actualizar_categoria', 'Almacen\Catalogo\CategoriaController@update_categoria');
				Route::get('anular_categoria/{id}', 'Almacen\Catalogo\CategoriaController@anular_categoria');
				Route::get('revisarCat/{id}', 'Almacen\Catalogo\CategoriaController@cat_revisar');
				Route::get('mostrar_tipos_clasificacion/{id}', 'Almacen\Catalogo\TipoProductoController@mostrar_tipos_clasificacion');
			});

			Route::group(['as' => 'sub-categorias.', 'prefix' => 'sub-categorias'], function () {
				//Sub Categoria
				Route::get('index', 'Almacen\Catalogo\SubCategoriaController@view_subcategoria')->name('index');
				Route::get('listar_subcategorias', 'Almacen\Catalogo\SubCategoriaController@mostrar_sub_categorias');
				Route::get('mostrar_subcategoria/{id}', 'Almacen\Catalogo\SubCategoriaController@mostrar_sub_categoria');
				Route::post('guardar_subcategoria', 'Almacen\Catalogo\SubCategoriaController@guardar_sub_categoria');
				Route::post('actualizar_subcategoria', 'Almacen\Catalogo\SubCategoriaController@update_sub_categoria');
				Route::get('anular_subcategoria/{id}', 'Almacen\Catalogo\SubCategoriaController@anular_sub_categoria');
				Route::get('revisarSubCat/{id}', 'Almacen\Catalogo\SubCategoriaController@subcat_revisar');

				Route::post('guardar-marca', 'Almacen\Catalogo\SubCategoriaController@guardar')->name('guardar-marca');
			});

			Route::group(['as' => 'clasificaciones.', 'prefix' => 'clasificaciones'], function () {
				//Clasificacion
				Route::get('index', 'Almacen\Catalogo\ClasificacionController@view_clasificacion')->name('index');
				Route::get('listar_clasificaciones', 'Almacen\Catalogo\ClasificacionController@mostrar_clasificaciones');
				Route::get('mostrar_clasificacion/{id}', 'Almacen\Catalogo\ClasificacionController@mostrar_clasificacion');
				Route::post('guardar_clasificacion', 'Almacen\Catalogo\ClasificacionController@guardar_clasificacion');
				Route::post('actualizar_clasificacion', 'Almacen\Catalogo\ClasificacionController@update_clasificacion');
				Route::get('anular_clasificacion/{id}', 'Almacen\Catalogo\ClasificacionController@anular_clasificacion');
				Route::get('revisarClas/{id}', 'Almacen\Catalogo\ClasificacionController@clas_revisar');
			});

			Route::group(['as' => 'productos.', 'prefix' => 'productos'], function () {
				//Producto
				Route::get('index', 'Almacen\Catalogo\ProductoController@view_producto')->name('index');
				Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::get('mostrar_prods_almacen/{id}', 'Almacen\Catalogo\ProductoController@mostrar_prods_almacen');
				Route::get('mostrar_producto/{id}', 'Almacen\Catalogo\ProductoController@mostrar_producto');
				Route::get('mostrar_categorias_tipo/{id}', 'Almacen\Catalogo\ProductoController@mostrar_categorias_tipo');
				Route::get('mostrar_tipos_clasificacion/{id}', 'Almacen\Catalogo\TipoProductoController@mostrar_tipos_clasificacion');
				Route::post('guardar_producto', 'Almacen\Catalogo\ProductoController@guardar_producto');
				Route::post('actualizar_producto', 'Almacen\Catalogo\ProductoController@update_producto');
				Route::get('anular_producto/{id}', 'Almacen\Catalogo\ProductoController@anular_producto');
				Route::post('guardar_imagen', 'Almacen\Catalogo\ProductoController@guardar_imagen');

				Route::get('listar_promociones/{id}', 'Almacen\Catalogo\ProductoController@listar_promociones');
				Route::post('crear_promocion', 'Almacen\Catalogo\ProductoController@crear_promocion');
				Route::get('anular_promocion/{id}', 'Almacen\Catalogo\ProductoController@anular_promocion');

				Route::get('listar_ubicaciones_producto/{id}', 'Almacen\Catalogo\ProductoController@listar_ubicaciones_producto');
				Route::get('mostrar_ubicacion/{id}', 'Almacen\Catalogo\ProductoController@mostrar_ubicacion');
				Route::post('guardar_ubicacion', 'Almacen\Catalogo\ProductoController@guardar_ubicacion');
				Route::post('actualizar_ubicacion', 'Almacen\Catalogo\ProductoController@update_ubicacion');
				Route::get('anular_ubicacion/{id}', 'Almacen\Catalogo\ProductoController@anular_ubicacion');

				Route::get('listar_series_producto/{id}', 'Almacen\Catalogo\ProductoController@listar_series_producto');
				Route::get('mostrar_serie/{id}', 'Almacen\Catalogo\ProductoController@mostrar_serie');
				Route::post('guardar_serie', 'Almacen\Catalogo\ProductoController@guardar_serie');
				Route::post('actualizar_serie', 'Almacen\Catalogo\ProductoController@update_serie');
				Route::get('anular_serie/{id}', 'Almacen\Catalogo\ProductoController@anular_serie');
			});

			Route::group(['as' => 'catalogo-productos.', 'prefix' => 'catalogo-productos'], function () {
				Route::get('index', 'Almacen\Catalogo\ProductoController@view_prod_catalogo')->name('index');
				Route::get('listar_productos', 'Almacen\Catalogo\ProductoController@mostrar_productos');
			});
		});

		Route::group(['as' => 'ubicaciones.', 'prefix' => 'ubicaciones'], function () {

			Route::group(['as' => 'tipos-almacen.', 'prefix' => 'tipos-almacen'], function () {
				//Tipos Almacen
				Route::get('index', 'Almacen\Ubicacion\TipoAlmacenController@view_tipo_almacen')->name('index');
				Route::get('listar_tipo_almacen', 'Almacen\Ubicacion\TipoAlmacenController@mostrar_tipo_almacen');
				Route::get('cargar_tipo_almacen/{id}', 'Almacen\Ubicacion\TipoAlmacenController@mostrar_tipo_almacenes');
				Route::post('guardar_tipo_almacen', 'Almacen\Ubicacion\TipoAlmacenController@guardar_tipo_almacen');
				Route::post('editar_tipo_almacen', 'Almacen\Ubicacion\TipoAlmacenController@update_tipo_almacen');
				Route::get('anular_tipo_almacen/{id}', 'Almacen\Ubicacion\TipoAlmacenController@anular_tipo_almacen');
			});

			Route::group(['as' => 'almacenes.', 'prefix' => 'almacenes'], function () {
				//Almacen
				Route::get('index', 'Almacen\Ubicacion\AlmacenController@view_almacenes')->name('index');
				Route::get('listar_almacenes', 'Almacen\Ubicacion\AlmacenController@mostrar_almacenes');
				Route::get('mostrar_almacen/{id}', 'Almacen\Ubicacion\AlmacenController@mostrar_almacen');
				Route::post('guardar_almacen', 'Almacen\Ubicacion\AlmacenController@guardar_almacen');
				Route::post('editar_almacen', 'Almacen\Ubicacion\AlmacenController@update_almacen');
				Route::get('anular_almacen/{id}', 'Almacen\Ubicacion\AlmacenController@anular_almacen');
				Route::get('listar_ubigeos', 'Almacen\Ubicacion\AlmacenController@listar_ubigeos');

				Route::get('almacen_posicion/{id}', 'Almacen\Ubicacion\PosicionController@almacen_posicion');
				Route::get('listarUsuarios', 'Almacen\Ubicacion\AlmacenController@listarUsuarios');
				Route::post('guardarAlmacenUsuario', 'Almacen\Ubicacion\AlmacenController@guardarAlmacenUsuario');
				Route::get('listarAlmacenUsuarios/{id}', 'Almacen\Ubicacion\AlmacenController@listarAlmacenUsuarios');
				Route::get('anularAlmacenUsuario/{id}', 'Almacen\Ubicacion\AlmacenController@anularAlmacenUsuario');
			});

			Route::group(['as' => 'posiciones.', 'prefix' => 'posiciones'], function () {
				//Almacen
				Route::get('index', 'Almacen\Ubicacion\PosicionController@view_ubicacion')->name('index');
				Route::get('listar_estantes', 'Almacen\Ubicacion\PosicionController@mostrar_estantes');
				Route::get('listar_estantes_almacen/{id}', 'Almacen\Ubicacion\PosicionController@mostrar_estantes_almacen');
				Route::get('mostrar_estante/{id}', 'Almacen\Ubicacion\PosicionController@mostrar_estante');
				Route::post('guardar_estante', 'Almacen\Ubicacion\PosicionController@guardar_estante');
				Route::post('actualizar_estante', 'Almacen\Ubicacion\PosicionController@update_estante');
				Route::get('anular_estante/{id}', 'Almacen\Ubicacion\PosicionController@anular_estante');
				Route::get('revisar_estante/{id}', 'Almacen\Ubicacion\PosicionController@revisar_estante');
				Route::post('guardar_estantes', 'Almacen\Ubicacion\PosicionController@guardar_estantes');
				Route::get('listar_niveles', 'Almacen\Ubicacion\PosicionController@mostrar_niveles');
				Route::get('listar_niveles_estante/{id}', 'Almacen\Ubicacion\PosicionController@mostrar_niveles_estante');
				Route::get('mostrar_nivel/{id}', 'Almacen\Ubicacion\PosicionController@mostrar_nivel');
				Route::post('guardar_nivel', 'Almacen\Ubicacion\PosicionController@guardar_nivel');
				Route::post('actualizar_nivel', 'Almacen\Ubicacion\PosicionController@update_nivel');
				Route::get('anular_nivel/{id}', 'Almacen\Ubicacion\PosicionController@anular_nivel');
				Route::get('revisar_nivel/{id}', 'Almacen\Ubicacion\PosicionController@revisar_nivel');
				Route::post('guardar_niveles', 'Almacen\Ubicacion\PosicionController@guardar_niveles');
				Route::get('listar_posiciones', 'Almacen\Ubicacion\PosicionController@mostrar_posiciones');
				Route::get('listar_posiciones_nivel/{id}', 'Almacen\Ubicacion\PosicionController@mostrar_posiciones_nivel');
				Route::get('mostrar_posicion/{id}', 'Almacen\Ubicacion\PosicionController@mostrar_posicion');
				Route::post('guardar_posiciones', 'Almacen\Ubicacion\PosicionController@guardar_posiciones');
				Route::get('anular_posicion/{id}', 'Almacen\Ubicacion\PosicionController@anular_posicion');
				Route::get('select_posiciones_almacen/{id}', 'Almacen\Ubicacion\PosicionController@select_posiciones_almacen');
				Route::get('listar_almacenes', 'Almacen\Ubicacion\AlmacenController@mostrar_almacenes');
			});
		});

		Route::group(['as' => 'control-stock.', 'prefix' => 'control-stock'], function () {

			Route::group(['as' => 'importar.', 'prefix' => 'importar'], function () {

				Route::get('index', 'Almacen\StockController@view_importar')->name('index');
			});

			Route::group(['as' => 'toma-inventario.', 'prefix' => 'toma-inventario'], function () {

				Route::get('index', 'Almacen\StockController@view_toma_inventario')->name('index');
			});
		});

		Route::group(['as' => 'movimientos.', 'prefix' => 'movimientos'], function () {

			Route::group(['as' => 'pendientes-ingreso.', 'prefix' => 'pendientes-ingreso'], function () {
				//Pendientes de Ingreso
				Route::get('index', 'Almacen\Movimiento\OrdenesPendientesController@view_ordenesPendientes')->name('index');
				Route::post('listarOrdenesPendientes', 'Almacen\Movimiento\OrdenesPendientesController@listarOrdenesPendientes');
				Route::post('listarIngresos', 'Almacen\Movimiento\OrdenesPendientesController@listarIngresos');
				Route::get('detalleOrden/{id}', 'Almacen\Movimiento\OrdenesPendientesController@detalleOrden');
				Route::post('guardar_guia_com_oc', 'Almacen\Movimiento\OrdenesPendientesController@guardar_guia_com_oc');
				Route::get('verGuiasOrden/{id}', 'Almacen\Movimiento\OrdenesPendientesController@verGuiasOrden');
				// Route::post('guardar_guia_transferencia', 'Almacen\Movimiento\OrdenesPendientesController@guardar_guia_transferencia');
				Route::post('anular_ingreso', 'Almacen\Movimiento\OrdenesPendientesController@anular_ingreso');
				Route::get('cargar_almacenes/{id}', 'Almacen\Ubicacion\AlmacenController@cargar_almacenes');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\OrdenesPendientesController@imprimir_ingreso');

				Route::post('detalleOrdenesSeleccionadas', 'Almacen\Movimiento\OrdenesPendientesController@detalleOrdenesSeleccionadas');
				Route::get('detalleMovimiento/{id}', 'Almacen\Movimiento\OrdenesPendientesController@detalleMovimiento');
				Route::post('listarTransformacionesProcesadas', 'Almacen\Movimiento\TransformacionController@listarTransformacionesProcesadas');
				Route::get('listarDetalleTransformacion/{id}', 'Almacen\Movimiento\TransformacionController@listarDetalleTransformacion');
				// Route::get('transferencia/{id}', 'Almacen\Movimiento\OrdenesPendientesController@transferencia');
				Route::get('obtenerGuia/{id}', 'Almacen\Movimiento\OrdenesPendientesController@obtenerGuia');
				Route::post('guardar_doc_compra', 'Almacen\Movimiento\OrdenesPendientesController@guardar_doc_compra');
				Route::get('documentos_ver/{id}', 'Almacen\Movimiento\OrdenesPendientesController@documentos_ver');

				Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::post('guardar_producto', 'Almacen\Catalogo\ProductoController@guardar_producto')->name('guardar-producto');

				Route::get('mostrar_series/{id}', 'Almacen\Movimiento\OrdenesPendientesController@mostrar_series');
				Route::post('guardar_series', 'Almacen\Movimiento\OrdenesPendientesController@guardar_series')->name('guardar-series');
				Route::post('actualizar_series', 'Almacen\Movimiento\OrdenesPendientesController@actualizar_series')->name('actualizar-series');
				Route::post('cambio_serie_numero', 'Almacen\Movimiento\OrdenesPendientesController@cambio_serie_numero')->name('cambio-series');

				Route::get('verGuiaCompraTransferencia/{id}', 'Almacen\Movimiento\TransferenciaController@verGuiaCompraTransferencia');
				Route::get('transferencia/{id}', 'Almacen\Movimiento\OrdenesPendientesController@transferencia');
				Route::post('obtenerGuiaSeleccionadas', 'Almacen\Movimiento\OrdenesPendientesController@obtenerGuiaSeleccionadas');
				Route::get('anular_doc_com/{id}', 'Almacen\Movimiento\OrdenesPendientesController@anular_doc_com');

				Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');

				Route::post('actualizarSugeridos', 'Almacen\Catalogo\ProductoController@actualizarSugeridos');
				Route::get('listarProductosSugeridos', 'Almacen\Catalogo\ProductoController@listarProductosSugeridos');
				Route::get('mostrar_prods_sugeridos/{part}/{desc}', 'Almacen\Catalogo\ProductoController@mostrar_prods_sugeridos');
				Route::get('mostrar_categorias_tipo/{id}', 'Almacen\Catalogo\ProductoController@mostrar_categorias_tipo');

				Route::get('almacenesPorUsuario', 'Almacen\Movimiento\TransferenciaController@almacenesPorUsuario');
				Route::post('actualizarFiltrosPendientes', 'Almacen\Movimiento\OrdenesPendientesController@actualizarFiltrosPendientes');

				Route::get('ordenesPendientesExcel', 'Almacen\Movimiento\OrdenesPendientesController@ordenesPendientesExcel');
			});

			Route::group(['as' => 'pendientes-salida.', 'prefix' => 'pendientes-salida'], function () {
				//Pendientes de Salida
				Route::get('index', 'Almacen\Movimiento\SalidasPendientesController@view_despachosPendientes')->name('index');
				Route::get('listarOrdenesDespachoPendientes', 'Almacen\Movimiento\SalidasPendientesController@listarOrdenesDespachoPendientes');
				Route::post('guardar_guia_despacho', 'Almacen\Movimiento\SalidasPendientesController@guardar_guia_despacho');
				Route::post('listarSalidasDespacho', 'Almacen\Movimiento\SalidasPendientesController@listarSalidasDespacho');
				Route::post('anular_salida', 'Almacen\Movimiento\SalidasPendientesController@anular_salida');
				Route::post('cambio_serie_numero', 'Almacen\Movimiento\SalidasPendientesController@cambio_serie_numero');
				Route::get('verDetalleDespacho/{id}', 'Almacen\Movimiento\SalidasPendientesController@verDetalleDespacho');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidasPendientesController@imprimir_salida');
				// Route::get('anular_orden_despacho/{id}', 'Almacen\Movimiento\SalidasPendientesController@anular_orden_despacho');
				Route::get('listarSeriesGuiaVen/{id}', 'Almacen\Movimiento\SalidasPendientesController@listarSeriesGuiaVen');
			});

			Route::group(['as' => 'prorrateo.', 'prefix' => 'prorrateo'], function () {
				//Pendientes de Salida
				Route::get('index', 'Almacen\Movimiento\ProrrateoCostosController@view_prorrateo_costos')->name('index');
				Route::get('mostrar_prorrateos', 'Almacen\Movimiento\ProrrateoCostosController@mostrar_prorrateos');
				Route::get('mostrar_prorrateo/{id}', 'Almacen\Movimiento\ProrrateoCostosController@mostrar_prorrateo');
				Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
				Route::get('guardar_tipo_prorrateo/{nombre}', 'Almacen\Movimiento\ProrrateoCostosController@guardar_tipo_prorrateo');
				Route::get('tipo_cambio_promedio/{fecha}/{mnd}', 'Almacen\Movimiento\OrdenesPendientesController@tipo_cambio_promedio');
				Route::get('listar_guias_compra', 'Almacen\Movimiento\ProrrateoCostosController@listar_guias_compra');
				Route::get('listar_docs_prorrateo/{id}', 'Almacen\Movimiento\ProrrateoCostosController@listar_docs_prorrateo');
				Route::get('listar_guia_detalle/{id}', 'Almacen\Movimiento\ProrrateoCostosController@listar_guia_detalle');
				Route::post('guardarProrrateo', 'Almacen\Movimiento\ProrrateoCostosController@guardarProrrateo');
				Route::post('updateProrrateo', 'Almacen\Movimiento\ProrrateoCostosController@updateProrrateo');
			});
		});

		Route::group(['as' => 'comprobantes.', 'prefix' => 'comprobantes'], function () {

			Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
			Route::get('listar_guias_proveedor/{id?}', 'AlmacenController@listar_guias_proveedor');
			Route::get('listar_detalle_guia_compra/{id?}', 'ComprobanteCompraController@listar_detalle_guia_compra');
			Route::get('tipo_cambio_compra/{fecha}', 'AlmacenController@tipo_cambio_compra');
			Route::post('guardar_doc_compra', 'ComprobanteCompraController@guardar_doc_compra');
			// Route::get('listar_guias_prov/{id?}', 'ComprobanteCompraController@listar_guias_prov');
			Route::post('listar_docs_compra', 'ComprobanteCompraController@listar_docs_compra');

			// Route::get('generar_comprobante', 'ComprobanteCompraController@view_genera_comprobante_compra')->name('generar_comprobante');
			// Route::get('listar_doc_guias/{id?}', 'ComprobanteCompraController@listar_doc_guias');
			// Route::get('listar_doc_items/{id?}', 'ComprobanteCompraController@listar_doc_items');
			// Route::post('actualizar_doc_compra', 'ComprobanteCompraController@update_doc_compra');
			// Route::post('update_doc_detalle', 'ComprobanteCompraController@update_doc_detalle');
			// Route::get('anular_doc_detalle/{id?}', 'ComprobanteCompraController@anular_doc_detalle');
			// Route::get('anular_doc_compra/{id?}', 'ComprobanteCompraController@anular_doc_compra');
			// Route::get('mostrar_doc_com/{id?}', 'ComprobanteCompraController@mostrar_doc_com');
			// Route::get('guardar_doc_items_guia/{id?}/{id_doc?}', 'ComprobanteCompraController@guardar_doc_items_guia');
			// Route::get('mostrar_doc_detalle/{id?}', 'ComprobanteCompraController@mostrar_doc_detalle');
			// Route::get('actualiza_totales_doc/{por?}/{id?}/{fec?}', 'ComprobanteCompraController@actualiza_totales_doc'); 
			// Route::get('listar_ordenes_sin_comprobante/{id_proveedor?}', 'ComprobanteCompraController@listar_ordenes_sin_comprobante'); 
			// Route::post('guardar_doc_com_det_orden/{id_doc?}', 'ComprobanteCompraController@guardar_doc_com_det_orden');
			// Route::get('listar_doc_com_orden/{id_doc?}', 'ComprobanteCompraController@listar_doc_com_orden');
			// Route::get('getOrdenByDetOrden/{id_det_orden?}', 'ComprobanteCompraController@getOrdenByDetOrden');
			// Route::get('anular_orden_doc_com/{id_doc_com?}/{id_orden_compra?}', 'ComprobanteCompraController@anular_orden_doc_com');

			Route::get('lista_comprobante_compra', 'ComprobanteCompraController@view_lista_comprobantes_compra')->name('lista_comprobante_compra');
			Route::get('documentoAPago/{id}', 'ComprobanteCompraController@documentoAPago');
			Route::get('documentos_ver/{id}', 'Almacen\Movimiento\OrdenesPendientesController@documentos_ver');
		});

		Route::group(['as' => 'transferencias.', 'prefix' => 'transferencias'], function () {

			Route::group(['as' => 'gestion-transferencias.', 'prefix' => 'gestion-transferencias'], function () {
				//Transferencias
				Route::get('index', 'Almacen\Movimiento\TransferenciaController@view_listar_transferencias')->name('index');
				Route::post('listarRequerimientos', 'Almacen\Movimiento\TransferenciaController@listarRequerimientos');
				Route::get('listarTransferenciasRecibidas/{ori}', 'Almacen\Movimiento\TransferenciaController@listarTransferenciasRecibidas');
				Route::get('listarTransferenciaDetalle/{id}', 'Almacen\Movimiento\TransferenciaController@listarTransferenciaDetalle');
				Route::post('guardarIngresoTransferencia', 'Almacen\Movimiento\TransferenciaController@guardarIngresoTransferencia');
				Route::post('guardarSalidaTransferencia', 'Almacen\Movimiento\TransferenciaController@guardarSalidaTransferencia');
				Route::post('anularTransferenciaIngreso', 'Almacen\Movimiento\TransferenciaController@anularTransferenciaIngreso');
				Route::get('ingreso_transferencia/{id}', 'Almacen\Movimiento\TransferenciaController@ingreso_transferencia');
				// Route::get('transferencia_nextId/{id}', 'Almacen\Movimiento\TransferenciaController@transferencia_nextId');
				Route::post('anularTransferenciaSalida', 'Almacen\Movimiento\TransferenciaController@anularTransferenciaSalida');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\OrdenesPendientesController@imprimir_ingreso');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidasPendientesController@imprimir_salida');
				Route::get('listarTransferenciasPorEnviar/{id}', 'Almacen\Movimiento\TransferenciaController@listarTransferenciasPorEnviar');
				Route::get('listarTransferenciasPorRecibir/{id}', 'Almacen\Movimiento\TransferenciaController@listarTransferenciasPorRecibir');
				// Route::get('cargar_almacenes/{id}', 'Almacen\Ubicacion\AlmacenController@cargar_almacenes');
				Route::post('listarDetalleTransferencia', 'Almacen\Movimiento\TransferenciaController@listarDetalleTransferencia');
				// Route::get('listarDetalleTransferencia/{id}', 'Almacen\Movimiento\TransferenciaController@listarDetalleTransferencia');
				// Route::post('listarDetalleTransferenciasSeleccionadas', 'Almacen\Movimiento\TransferenciaController@listarDetalleTransferenciasSeleccionadas');
				Route::get('listarGuiaTransferenciaDetalle/{id}', 'Almacen\Movimiento\TransferenciaController@listarGuiaTransferenciaDetalle');
				Route::get('listarSeries/{id}', 'Almacen\Movimiento\TransferenciaController@listarSeries');
				Route::get('listarSeriesVen/{id}', 'Almacen\Movimiento\TransferenciaController@listarSeriesVen');
				Route::get('anular_transferencia/{id}', 'Almacen\Movimiento\TransferenciaController@anular_transferencia');
				// Route::get('listar_guias_compra', 'Almacen\Movimiento\TransferenciaController@listar_guias_compra');
				Route::get('transferencia/{id}', 'Almacen\Movimiento\OrdenesPendientesController@transferencia');
				Route::get('verGuiaCompraTransferencia/{id}', 'Almacen\Movimiento\TransferenciaController@verGuiaCompraTransferencia');
				Route::get('verRequerimiento/{id}', 'Almacen\Movimiento\TransferenciaController@verRequerimiento');
				Route::post('generarTransferenciaRequerimiento', 'Almacen\Movimiento\TransferenciaController@generarTransferenciaRequerimiento');
				Route::get('listarSeriesGuiaVen/{id}', 'Almacen\Movimiento\SalidasPendientesController@listarSeriesGuiaVen');
				Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');
				Route::get('mostrarTransportistas', 'DistribucionController@mostrarTransportistas');

				Route::get('autogenerarDocumentosCompra/{id}', 'Tesoreria\Facturacion\VentasInternasController@autogenerarDocumentosCompra')->name('autogenerarDocumentosCompra');
				Route::get('verDocumentosAutogenerados/{id}', 'Tesoreria\Facturacion\VentasInternasController@verDocumentosAutogenerados');
			});
		});

		Route::group(['as' => 'reportes.', 'prefix' => 'reportes'], function () {

			Route::group(['as' => 'saldos.', 'prefix' => 'saldos'], function () {

				Route::get('index', 'Almacen\Reporte\SaldosController@view_saldos')->name('index');
				Route::get('listar_saldos/{id}', 'Almacen\Reporte\SaldosController@listar_saldos');
				Route::get('verRequerimientosReservados/{id}/{alm}', 'Almacen\Reporte\SaldosController@verRequerimientosReservados');
				Route::get('tipo_cambio_compra/{fecha}', 'Almacen\Reporte\SaldosController@tipo_cambio_compra');
			});

			Route::group(['as' => 'lista-ingresos.', 'prefix' => 'lista-ingresos'], function () {

				Route::get('index', 'AlmacenController@view_ingresos')->name('index');
				Route::get('listar_ingresos/{alm}/{cond}/{fini}/{ffin}/{prov}/{usu}/{mon}/{tra}', 'AlmacenController@listar_ingresos_lista');
				Route::get('update_revisado/{id}/{rev}/{obs}', 'AlmacenController@update_revisado');

				Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
				Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
				Route::get('listar_transportistas_com', 'AlmacenController@listar_transportistas_com');
				Route::get('listar_transportistas_ven', 'AlmacenController@listar_transportistas_ven');
			});

			Route::group(['as' => 'lista-salidas.', 'prefix' => 'lista-salidas'], function () {

				Route::get('index', 'AlmacenController@view_salidas')->name('index');
				Route::get('listar_salidas/{alm}/{docs}/{cond}/{fini}/{ffin}/{cli}/{usu}/{mon}/{ref}', 'AlmacenController@listar_salidas');
				Route::get('update_revisado/{id}/{rev}/{obs}', 'AlmacenController@update_revisado');

				Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
				Route::get('mostrar_clientes', 'Comercial\ClienteController@mostrar_clientes');
				Route::get('mostrar_clientes_empresa', 'Comercial\ClienteController@mostrar_clientes_empresa');
				Route::get('listar_transportistas_com', 'AlmacenController@listar_transportistas_com');
				Route::get('listar_transportistas_ven', 'AlmacenController@listar_transportistas_ven');
			});

			Route::group(['as' => 'detalle-ingresos.', 'prefix' => 'detalle-ingresos'], function () {

				Route::get('index', 'AlmacenController@view_busqueda_ingresos')->name('index');
				Route::get('listar_busqueda_ingresos/{alm}/{tp}/{des}/{doc}/{fini}/{ffin}', 'AlmacenController@listar_busqueda_ingresos');
				Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\OrdenesPendientesController@imprimir_ingreso');
				Route::get('imprimir_guia_ingreso/{id}', 'AlmacenController@imprimir_guia_ingreso');
			});

			Route::group(['as' => 'detalle-salidas.', 'prefix' => 'detalle-salidas'], function () {

				Route::get('index', 'AlmacenController@view_busqueda_salidas')->name('index');
				Route::get('listar_busqueda_salidas/{alm}/{tp}/{des}/{doc}/{fini}/{ffin}', 'AlmacenController@listar_busqueda_salidas');
				Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
				Route::get('imprimir_salida/{id}', 'AlmacenController@imprimir_salida');
			});

			Route::group(['as' => 'kardex-general.', 'prefix' => 'kardex-general'], function () {

				Route::get('index', 'AlmacenController@view_kardex_general')->name('index');
				Route::get('kardex_general/{id}/{fini}/{ffin}', 'AlmacenController@kardex_general');
				Route::get('kardex_sunat/{id}/{fini}/{ffin}', 'AlmacenController@download_kardex_sunat');
				// Route::get('kardex_sunatx/{id}', 'AlmacenController@kardex_sunat');

			});

			Route::group(['as' => 'kardex-productos.', 'prefix' => 'kardex-productos'], function () {

				Route::get('index', 'AlmacenController@view_kardex_detallado')->name('index');
				Route::get('kardex_producto/{id}/{alm}/{fini}/{ffin}', 'AlmacenController@kardex_producto');
				Route::get('listar_kardex_producto/{id}/{alm}/{fini}/{ffin}', 'AlmacenController@kardex_producto');
				Route::get('kardex_detallado/{id}/{alm}/{fini}/{ffin}', 'AlmacenController@download_kardex_producto');
				Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
				Route::get('datos_producto/{id}', 'Almacen\Reporte\KardexSerieController@datos_producto');
				Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::get('mostrar_prods_almacen/{id}', 'Almacen\Catalogo\ProductoController@mostrar_prods_almacen');
			});

			Route::group(['as' => 'kardex-series.', 'prefix' => 'kardex-series'], function () {

				Route::get('index', 'Almacen\Reporte\KardexSerieController@view_kardex_series')->name('index');
				Route::get('listar_serie_productos/{serie}/{des}/{cod}/{part}', 'Almacen\Reporte\KardexSerieController@listar_serie_productos');
				Route::get('listar_kardex_serie/{serie}/{id_prod}', 'Almacen\Reporte\KardexSerieController@listar_kardex_serie');
				Route::get('datos_producto/{id}', 'Almacen\Reporte\KardexSerieController@datos_producto');
				Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::get('mostrar_prods_almacen/{id}', 'Almacen\Catalogo\ProductoController@mostrar_prods_almacen');
			});

			Route::group(['as' => 'documentos-prorrateo.', 'prefix' => 'documentos-prorrateo'], function () {

				Route::get('index', 'AlmacenController@view_docs_prorrateo')->name('index');
				Route::get('listar_documentos_prorrateo', 'AlmacenController@listar_documentos_prorrateo');
			});
		});

		Route::group(['as' => 'variables.', 'prefix' => 'variables'], function () {

			Route::group(['as' => 'series-numeros.', 'prefix' => 'series-numeros'], function () {

				Route::get('index', 'AlmacenController@view_serie_numero')->name('index');
				Route::get('listar_series_numeros', 'AlmacenController@listar_series_numeros');
				Route::get('mostrar_serie_numero/{id}', 'AlmacenController@mostrar_serie_numero');
				Route::post('guardar_serie_numero', 'AlmacenController@guardar_serie_numero');
				Route::post('actualizar_serie_numero', 'AlmacenController@update_serie_numero');
				Route::get('anular_serie_numero/{id}', 'AlmacenController@anular_serie_numero');
				Route::get('series_numeros/{desde}/{hasta}/{num}/{serie}', 'AlmacenController@series_numeros');
			});

			Route::group(['as' => 'tipos-movimiento.', 'prefix' => 'tipos-movimiento'], function () {

				Route::get('index', 'AlmacenController@view_tipo_movimiento')->name('index');
				Route::get('listar_tipoMov', 'AlmacenController@mostrar_tipos_mov');
				Route::get('mostrar_tipoMov/{id}', 'AlmacenController@mostrar_tipo_mov');
				Route::post('guardar_tipoMov', 'AlmacenController@guardar_tipo_mov');
				Route::post('actualizar_tipoMov', 'AlmacenController@update_tipo_mov');
				Route::get('anular_tipoMov/{id}', 'AlmacenController@anular_tipo_mov');
			});

			Route::group(['as' => 'tipos-documento.', 'prefix' => 'tipos-documento'], function () {

				Route::get('index', 'AlmacenController@view_tipo_doc_almacen')->name('index');
				Route::get('listar_tp_docs', 'AlmacenController@listar_tp_docs');
				Route::get('mostrar_tp_doc/{id}', 'AlmacenController@mostrar_tp_doc');
				Route::post('guardar_tp_doc', 'AlmacenController@guardar_tp_doc');
				Route::post('update_tp_doc', 'AlmacenController@update_tp_doc');
				Route::get('anular_tp_doc/{id}', 'AlmacenController@anular_tp_doc');
			});

			Route::group(['as' => 'unidades-medida.', 'prefix' => 'unidades-medida'], function () {

				Route::get('index', 'AlmacenController@view_unid_med')->name('index');
				Route::get('listar_unidmed', 'AlmacenController@mostrar_unidades_med');
				Route::get('mostrar_unidmed/{id}', 'AlmacenController@mostrar_unid_med');
				Route::post('guardar_unidmed', 'AlmacenController@guardar_unid_med');
				Route::post('actualizar_unidmed', 'AlmacenController@update_unid_med');
				Route::get('anular_unidmed/{id}', 'AlmacenController@anular_unid_med');
			});
		});
	});

	Route::group(['as' => 'cas.', 'prefix' => 'cas'], function () {

		Route::get('index', 'Almacen\Movimiento\TransformacionController@view_main_cas')->name('index');

		Route::group(['as' => 'customizacion.', 'prefix' => 'customizacion'], function () {

			Route::group(['as' => 'gestion-customizaciones.', 'prefix' => 'gestion-customizaciones'], function () {
				//Transformaciones
				Route::get('index', 'Almacen\Movimiento\TransformacionController@view_listar_transformaciones')->name('index');
				Route::get('listar_todas_transformaciones', 'Almacen\Movimiento\TransformacionController@listar_todas_transformaciones');
				Route::get('listar_transformaciones_pendientes', 'Almacen\Movimiento\TransformacionController@listar_transformaciones_pendientes');
				Route::post('listarCuadrosCostos', 'Almacen\Movimiento\TransformacionController@listarCuadrosCostos');
				Route::post('generarTransformacion', 'Almacen\Movimiento\TransformacionController@generarTransformacion');
				Route::get('obtenerCuadro/{id}/{tipo}', 'Almacen\Movimiento\TransformacionController@obtenerCuadro');
				Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::get('id_ingreso_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@id_ingreso_transformacion');
				Route::get('id_salida_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@id_salida_transformacion');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\OrdenesPendientesController@imprimir_ingreso');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidasPendientesController@imprimir_salida');
				Route::get('imprimir_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@imprimir_transformacion');
				Route::get('recibido_conforme_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@recibido_conforme_transformacion');
				Route::get('no_conforme_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@no_conforme_transformacion');
				Route::get('iniciar_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@iniciar_transformacion');
			});

			Route::group(['as' => 'hoja-transformacion.', 'prefix' => 'hoja-transformacion'], function () {
				//Transformaciones
				Route::get('index', 'Almacen\Movimiento\TransformacionController@view_transformacion')->name('index');
				Route::post('guardar_transformacion', 'Almacen\Movimiento\TransformacionController@guardar_transformacion');
				Route::post('update_transformacion', 'Almacen\Movimiento\TransformacionController@update_transformacion');
				Route::get('listar_transformaciones', 'Almacen\Movimiento\TransformacionController@listar_transformaciones');
				Route::get('mostrar_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@mostrar_transformacion');
				Route::get('anular_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@anular_transformacion');
				Route::get('listar_materias/{id}', 'Almacen\Movimiento\TransformacionController@listar_materias');
				Route::get('listar_directos/{id}', 'Almacen\Movimiento\TransformacionController@listar_directos');
				Route::get('listar_indirectos/{id}', 'Almacen\Movimiento\TransformacionController@listar_indirectos');
				Route::get('listar_sobrantes/{id}', 'Almacen\Movimiento\TransformacionController@listar_sobrantes');
				Route::get('listar_transformados/{id}', 'Almacen\Movimiento\TransformacionController@listar_transformados');
				Route::get('iniciar_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@iniciar_transformacion');
				Route::post('procesar_transformacion', 'Almacen\Movimiento\TransformacionController@procesar_transformacion');
				Route::post('guardar_materia', 'Almacen\Movimiento\TransformacionController@guardar_materia');
				Route::post('guardar_directo', 'Almacen\Movimiento\TransformacionController@guardar_directo');
				Route::post('guardar_indirecto', 'Almacen\Movimiento\TransformacionController@guardar_indirecto');
				Route::post('guardar_sobrante', 'Almacen\Movimiento\TransformacionController@guardar_sobrante');
				Route::post('guardar_transformado', 'Almacen\Movimiento\TransformacionController@guardar_transformado');
				Route::post('update_materia', 'Almacen\Movimiento\TransformacionController@update_materia');
				Route::post('update_directo', 'Almacen\Movimiento\TransformacionController@update_directo');
				Route::post('update_indirecto', 'Almacen\Movimiento\TransformacionController@update_indirecto');
				Route::post('update_sobrante', 'Almacen\Movimiento\TransformacionController@update_sobrante');
				Route::post('update_transformado', 'Almacen\Movimiento\TransformacionController@update_transformado');
				Route::get('id_ingreso_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@id_ingreso_transformacion');
				Route::get('id_salida_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@id_salida_transformacion');
				Route::get('anular_materia/{id}', 'Almacen\Movimiento\TransformacionController@anular_materia');
				Route::get('anular_directo/{id}', 'Almacen\Movimiento\TransformacionController@anular_directo');
				Route::get('anular_indirecto/{id}', 'Almacen\Movimiento\TransformacionController@anular_indirecto');
				Route::get('anular_sobrante/{id}', 'Almacen\Movimiento\TransformacionController@anular_sobrante');
				Route::get('anular_transformado/{id}', 'Almacen\Movimiento\TransformacionController@anular_transformado');
				// Route::get('listar_servicio', 'AlmacenController@mostrar_servicios');
				Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\OrdenesPendientesController@imprimir_ingreso');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidasPendientesController@imprimir_salida');
				Route::post('guardar_producto', 'Almacen\Catalogo\ProductoController@guardar_producto');
				Route::get('imprimir_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@imprimir_transformacion');
			});
		});
	});

	Route::group(['as' => 'finanzas.', 'prefix' => 'finanzas'], function () {
		// Finanzas
		Route::get('index', function () {
			return view('finanzas/main');
		})->name('index');

		Route::group(['as' => 'lista-presupuestos.', 'prefix' => 'lista-presupuestos'], function () {
			// Lista de Presupuestos
			Route::get('index', 'Finanzas\Presupuesto\PresupuestoController@index')->name('index');
			Route::get('actualizarPartidas', 'Finanzas\Presupuesto\PartidaController@actualizarPartidas')->name('actualizar-partidas');
		});

		Route::group(['as' => 'presupuesto.', 'prefix' => 'presupuesto'], function () {
			// Presupuesto
			Route::get('create', 'Finanzas\Presupuesto\PresupuestoController@create')->name('index');
			Route::get('mostrarPartidas/{id}', 'Finanzas\Presupuesto\PresupuestoController@mostrarPartidas')->name('mostrar-partidas');
			Route::get('mostrarRequerimientosDetalle/{id}', 'Finanzas\Presupuesto\PresupuestoController@mostrarRequerimientosDetalle')->name('mostrar-requerimientos-detalle');
			Route::post('guardar-presupuesto', 'Finanzas\Presupuesto\PresupuestoController@store')->name('guardar-presupuesto');
			Route::post('actualizar-presupuesto', 'Finanzas\Presupuesto\PresupuestoController@update')->name('actualizar-presupuesto');

			Route::post('guardar-titulo', 'Finanzas\Presupuesto\TituloController@store')->name('guardar-titulo');
			Route::post('actualizar-titulo', 'Finanzas\Presupuesto\TituloController@update')->name('actualizar-titulo');
			Route::get('anular-titulo/{id}', 'Finanzas\Presupuesto\TituloController@destroy')->name('anular-titulo');

			Route::post('guardar-partida', 'Finanzas\Presupuesto\PartidaController@store')->name('guardar-partida');
			Route::post('actualizar-partida', 'Finanzas\Presupuesto\PartidaController@update')->name('actualizar-partida');
			Route::get('anular-partida/{id}', 'Finanzas\Presupuesto\PartidaController@destroy')->name('anular-partida');
		});

		Route::group(['as' => 'centro-costos.', 'prefix' => 'centro-costos'], function () {
			//Centro de Costos
			Route::get('index', 'Finanzas\CentroCosto\CentroCostoController@index')->name('index');
			Route::get('mostrar-centro-costos', 'Finanzas\CentroCosto\CentroCostoController@mostrarCentroCostos')->name('mostrar-centro-costos');
			Route::post('guardar-centro-costo', 'Finanzas\CentroCosto\CentroCostoController@store')->name('guardar-centro-costo');
			Route::post('actualizar-centro-costo', 'Finanzas\CentroCosto\CentroCostoController@update')->name('actualizar-centro-costo');
			Route::get('anular-centro-costo/{id}', 'Finanzas\CentroCosto\CentroCostoController@destroy')->name('anular-centro-costo');
		});
	});



	/**Tesoreria */
	Route::group(['as' => 'tesoreria.', 'prefix' => 'tesoreria'], function () {

		Route::get('index', 'Tesoreria\RequerimientoPagoController@view_main_tesoreria')->name('index');

		Route::group(['as' => 'pagos.', 'prefix' => 'pagos'], function () {

			Route::group(['as' => 'confirmacion-pagos.', 'prefix' => 'confirmacion-pagos'], function () {

				Route::get('index', 'DistribucionController@view_confirmacionPago')->name('index');
				Route::post('listarRequerimientosPendientesPagos', 'DistribucionController@listarRequerimientosPendientesPagos');
				Route::post('listarRequerimientosConfirmadosPagos', 'DistribucionController@listarRequerimientosConfirmadosPagos');
				Route::post('pago_confirmado', 'DistribucionController@pago_confirmado');
				Route::post('pago_no_confirmado', 'DistribucionController@pago_no_confirmado');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('verRequerimientoAdjuntos/{id}', 'DistribucionController@verRequerimientoAdjuntos');
			});

			Route::group(['as' => 'procesar-pago.', 'prefix' => 'procesar-pago'], function () {

				Route::get('index', 'Tesoreria\RequerimientoPagoController@view_pendientes_pago')->name('index');
				Route::post('listarComprobantesPagos', 'Tesoreria\RequerimientoPagoController@listarComprobantesPagos')->name('listar-comprobante-pagos');
				Route::post('listarOrdenesCompra', 'Tesoreria\RequerimientoPagoController@listarOrdenesCompra')->name('listar-ordenes-compra');
				Route::post('procesarPago', 'Tesoreria\RequerimientoPagoController@procesarPago')->name('procesar-pagos');
				Route::get('pagosComprobante/{id}', 'Tesoreria\RequerimientoPagoController@pagosComprobante')->name('pagos-comprobante');
				Route::get('pagosOrdenes/{id}', 'Tesoreria\RequerimientoPagoController@pagosOrdenes')->name('pagos-ordenes');
				// Route::post('listarRequerimientosPagos', 'Tesoreria\RequerimientoPagoController@listarRequerimientosPagos')->name('listar-requerimiento-pagos');
				// Route::get('detalleRequerimiento/{id}', 'Logistica\RequerimientoController@detalleRequerimiento')->name('detalle-requerimiento');
			});
		});

		Route::group(['as' => 'facturacion.', 'prefix' => 'facturacion'], function () {

			Route::get('index', 'Tesoreria\Facturacion\PendientesFacturacionController@view_pendientes_facturacion')->name('index');
			Route::post('listarGuiasVentaPendientes', 'Tesoreria\Facturacion\PendientesFacturacionController@listarGuiasVentaPendientes')->name('listar-guias-pendientes');
			Route::post('listarRequerimientosPendientes', 'Tesoreria\Facturacion\PendientesFacturacionController@listarRequerimientosPendientes')->name('listar-requerimientos-pendientes');
			Route::post('guardar_doc_venta', 'Tesoreria\Facturacion\PendientesFacturacionController@guardar_doc_venta')->name('guardar-doc-venta');
			Route::get('documentos_ver/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@documentos_ver')->name('ver-doc-venta');
			Route::get('anular_doc_ven/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@anular_doc_ven')->name('anular-doc-venta');
			Route::get('obtenerGuiaVenta/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerGuiaVenta')->name('obtener-guia-venta');
			Route::post('obtenerGuiaVentaSeleccionadas', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerGuiaVentaSeleccionadas')->name('obtener-guias-ventas');
			Route::get('obtenerRequerimiento/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerRequerimiento')->name('obtener-requerimiento');
			Route::get('detalleFacturasGuias/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@detalleFacturasGuias')->name('detalle-facturas-guia');
			Route::get('detalleFacturasRequerimientos/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@detalleFacturasRequerimientos')->name('detalle-facturas-guia');
			Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');

			Route::get('autogenerarDocumentosCompra/{id}', 'Tesoreria\Facturacion\VentasInternasController@autogenerarDocumentosCompra')->name('autogenerarDocumentosCompra');
		});
	});


	Route::get('listarUsu', 'Almacen\Movimiento\TransferenciaController@listarUsu');

	Route::get('migrar_venta_directa/{id}', 'MigrateSoftLinkController@migrar_venta_directa');
	Route::get('prue/{id}', 'OrdenesPendientesController@prue');
	Route::get('prueba', 'MigrateSoftLinkController@prueba');
	Route::get('anular_presup', 'ProyectosController@anular_presup');

	Route::group(['as' => 'configuracion.', 'prefix' => 'configuracion'], function () {

		Route::get('index', 'ConfiguracionController@view_main_configuracion')->name('index');
		Route::get('usuarios', 'ConfiguracionController@view_usuario');
		Route::get('listar_usuarios', 'ConfiguracionController@mostrar_usuarios');
		Route::post('guardar_usuarios', 'ConfiguracionController@guardar_usuarios');
		Route::get('listar_trabajadores', 'ProyectosController@listar_trabajadores');
		Route::get('anular_usuario/{id}', 'ConfiguracionController@anular_usuario');
		Route::get('lista-roles-usuario/{id}', 'ConfiguracionController@lista_roles_usuario');
		Route::get('arbol-acceso/{id_rol}', 'ConfiguracionController@arbol_modulos')->name('arbol-acceso');
		Route::put('actualizar-accesos-usuario', 'ConfiguracionController@actualizar_accesos_usuario');

		Route::group(['as' => 'usuario.', 'prefix' => 'usuario'], function () {
			Route::get('password-user-decode/{id?}', 'ConfiguracionController@getPasswordUserDecode')->name('password-user-decode');
			Route::get('perfil/{id}', 'ConfiguracionController@getPerfil')->name('get-perfil');
			Route::post('perfil', 'ConfiguracionController@savePerfil')->name('save-perfil');
		});
	});

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
	/*
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
		Route::get('llenar_data', 'TesoreriaController@llenarDataInicial');
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
*/

	Route::get('pruebaR', 'Almacen\Movimiento\OrdenesPendientesController@pruebaR');
	Route::get('documentos_ver/{id}', 'OrdenesPendientesController@documentos_ver');

	Route::get('transformacion_nextId/{fec}/{id}', 'CustomizacionController@transformacion_nextId');
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



	// Route::get('requerimientosPendientes', 'DistribucionController@view_requerimientosPendientes');


	Route::get('grupoDespachos', 'DistribucionController@view_grupoDespachos');


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
	// Route::get('generar_orden', 'LogisticaController@view_generar_orden');
	Route::get('detalle_cotizacion/{id}', 'LogisticaController@detalle_cotizacion');
	Route::post('guardar_orden_compra', 'LogisticaController@guardar_orden_compra');
	Route::post('update_orden_compra', 'LogisticaController@update_orden_compra');
	// Route::get('anular_orden_compra/{id}', 'LogisticaController@anular_orden_compra');
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


	Route::get('cta_contable', 'ContabilidadController@view_cta_contable');
	Route::get('mostrar_cta_contables', 'ContabilidadController@mostrar_cuentas_contables');
	Route::get('comprobante_compra', 'ContabilidadController@view_comprobante_compra');
	Route::get('ordenes_sin_facturar/{id_empresa}/{all_or_id_orden}', 'ContabilidadController@ordenes_sin_facturar');
	Route::post('guardar_comprobante_compra', 'ContabilidadController@guardar_comprobante_compra');
	Route::get('lista_comprobante_compra/{id_sede}/{all_or_id_doc_com}', 'ContabilidadController@lista_comprobante_compra');

	// APIs de Terceros
	Route::get('consulta_sunat/{nro_documento?}', 'HynoTechController@consulta_sunat');

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

	// Route::post('notification', 'SocketController@notification');
	// Route::get('notificaciones_sin_leer', 'SocketController@notificaciones_sin_leer');

});
