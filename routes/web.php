<?php

use App\Exports\CatalogoProductoExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

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

Route::get('recueperar-clave', 'RecuperarClaveController@recuperarClave')->name('recuperar.clave');
Route::post('enviar-correo', 'RecuperarClaveController@enviarCorreo')->name('enviar.correo');
Route::get('recueperar-clave/ingresar-nueva-clave', 'RecuperarClaveController@ingresarNuevaClave')->name('recuperar.clave.ingresar');
Route::post('buscar-codigo', 'RecuperarClaveController@buscarCodigo')->name('buscar.codigo');
Route::post('guardar-cambio-clave', 'RecuperarClaveController@guardarCambioClave')->name('guardar.cambio.clave');
Route::get('clave', 'LoginController@actualizarContraseña')->name('actualizar');
Route::post('modificar-clave', 'LoginController@modificarClave')->name('modificarClave');

//Route::post('iniciar_sesion', 'LoginController@iniciar_sesion');
Route::get('cargar_usuarios/{user}', 'LoginController@mostrar_roles');
//Route::get('logout', 'LoginController@cerrar_sesion');
Route::get('mostrar-version-actual', 'ConfiguracionController@mostrarVersionActual')->name('mostrar-version-actual');
Route::get('socket_setting/{option}', 'ConfiguracionController@socket_setting');

Route::group(['as' => 'api-consulta.', 'prefix' => 'api-consulta'], function () {
	Route::get('tipo_cambio_masivo/{desde}/{hasta}', 'ApiController@tipoCambioMasivo')->name('tipo_cambio_masivo');
	Route::get('tipo_cambio_actual', 'ApiController@tipoCambioActual')->name('tipo_cambio_actual');
});

/**
 * Rutas para Testing
 */
Route::get('test-descripcion-adicional', 'TestController@testDescripcionAdicionalOrden')->name('test-descripcion-adicional');

Auth::routes();

Route::group(['middleware' => ['auth']], function () {

	Route::get('duplicar-requerimiento-pago-y-actualizar-codigo/{idRequerimientoPago}/{idEstado}', 'Tesoreria\RequerimientoPagoController@duplicarRequerimientoPagoYActualizarCodigo');

	Route::group(['as' => 'mgcp.', 'prefix' => 'mgcp'], function () {
		Route::name('cuadro-costos.')->prefix('cuadro-costos')->middleware('auth')->group(function () {
			Route::get('detalles/{id?}', 'CuadroCostoController@detalles')->name('detalles');
		});
	});

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
				Route::get('listar_insumo_precios/{id}', 'Proyectos\Catalogos\InsumoController@listar_insumo_precios');

				// Route::post('guardar_precio', 'ProyectosController@guardar_precio');
				Route::post('guardar_acu', 'Proyectos\Catalogos\AcuController@guardar_acu');
				Route::post('actualizar_acu', 'Proyectos\Catalogos\AcuController@update_acu');
				Route::get('anular_acu/{id}', 'Proyectos\Catalogos\AcuController@anular_acu');
				Route::get('valida_acu_editar/{id}', 'Proyectos\Catalogos\AcuController@valida_acu_editar');
				// Route::get('insumos/{id}/{cu}', 'Proyectos\Catalogos\AcuController@insumos');
				Route::get('partida_insumos_precio/{id}/{ins}', 'Proyectos\Catalogos\AcuController@partida_insumos_precio');
				Route::post('guardar_insumo', 'Proyectos\Catalogos\InsumoController@guardar_insumo');
				Route::get('listar_insumos', 'Proyectos\Catalogos\InsumoController@listar_insumos');

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
				Route::get('mostrar_acu/{id}', 'Proyectos\Catalogos\AcuController@mostrar_acu');

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
				Route::get('mostrar_presint/{id}', 'Proyectos\Opciones\PresupuestoInternoController@mostrar_presint');
				Route::post('guardar_preseje', 'ProyectosController@guardar_preseje');
				Route::post('update_preseje', 'ProyectosController@update_preseje');
				Route::get('anular_presint/{id}', 'ProyectosController@anular_presint');

				Route::get('generar_estructura/{id}/{tp}', 'ProyectosController@generar_estructura');
				Route::get('listar_presupuesto_proyecto/{id}', 'Proyectos\Opciones\PresupuestoInternoController@listar_presupuesto_proyecto');
				Route::get('anular_estructura/{id}', 'ProyectosController@anular_estructura');
				Route::get('totales/{id}', 'ProyectosController@totales');
				Route::get('download_presupuesto/{id}', 'ProyectosController@download_presupuesto');
				Route::get('generar_preseje/{id}', 'ProyectosController@generar_preseje');
				Route::get('actualiza_moneda/{id}', 'ProyectosController@actualiza_moneda');
				Route::get('mostrar_presupuestos/{id}', 'Proyectos\Opciones\PresupuestoInternoController@mostrar_presupuestos');
				Route::get('listar_presupuestos_copia/{tp}/{id}', 'ProyectosController@listar_presupuestos_copia');
				Route::get('generar_partidas_presupuesto/{id}/{ida}', 'ProyectosController@generar_partidas_presupuesto');
				Route::get('listar_proyectos', 'ProyectosController@listar_proyectos');

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
				Route::get('crear_titulos_ci/{id}', 'ProyectosController@crear_titulos_ci');
				Route::get('crear_titulos_gg/{id}', 'ProyectosController@crear_titulos_gg');

				Route::post('add_unid_med', 'Proyectos\Catalogos\InsumoController@add_unid_med');
				Route::post('update_unitario_partida_cd', 'ProyectosController@update_unitario_partida_cd');
				Route::get('listar_acus_sin_presup', 'ProyectosController@listar_acus_sin_presup');

				Route::get('mostrar_acu/{id}', 'Proyectos\Catalogos\AcuController@mostrar_acu');
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
				Route::get('nuevo_cronograma/{id}', 'Proyectos\Opciones\CronogramaInternoController@nuevo_cronograma');
				Route::get('listar_acus_cronograma/{id}', 'ProyectosController@listar_acus_cronograma'); // ! no existe método
				Route::get('listar_pres_crono/{tc}/{tp}', 'Proyectos\Opciones\CronogramaInternoController@listar_pres_crono');
				Route::get('listar_pres_cronoval/{tc}/{tp}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@listar_pres_cronoval');
				Route::post('guardar_crono', 'Proyectos\Opciones\CronogramaInternoController@guardar_crono');
				Route::get('anular_crono/{id}', 'Proyectos\Opciones\CronogramaInternoController@anular_crono');
				Route::get('ver_gant/{id}', 'Proyectos\Opciones\CronogramaInternoController@ver_gant');
				Route::get('listar_cronograma/{id}', 'Proyectos\Opciones\CronogramaInternoController@listar_cronograma');
				Route::get('mostrar_acu/{id}', 'Proyectos\Catalogos\AcuController@mostrar_acu');
				Route::get('listar_obs_cd/{id}', 'Proyectos\Opciones\PartidasController@listar_obs_cd');
			});

			Route::group(['as' => 'cronogramas-valorizados-ejecucion.', 'prefix' => 'cronogramas-valorizados-ejecucion'], function () {
				//Cronograma Valorizado Ejecucion
				Route::get('index', 'ProyectosController@view_cronovaleje')->name('index');
				Route::get('nuevo_crono_valorizado/{id}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@nuevo_crono_valorizado');
				Route::get('mostrar_crono_valorizado/{id}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@mostrar_crono_valorizado');
				Route::get('download_cronoval/{id}/{nro}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@download_cronoval');
				Route::post('guardar_cronoval_presupuesto', 'Proyectos\Opciones\CronogramaValorizadoInternoController@guardar_cronoval_presupuesto');
				Route::get('anular_cronoval/{id}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@anular_cronoval');
				Route::get('listar_pres_cronoval/{tc}/{tp}', 'Proyectos\Opciones\CronogramaValorizadoInternoController@listar_pres_cronoval');
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

			Route::group(['as' => 'cuadro-gastos.', 'prefix' => 'cuadro-gastos'], function () {
				//Opciones y Relaciones
				Route::get('index', 'ProyectosController@view_cuadro_gastos')->name('index');
				Route::get('listar', 'ProyectosController@listar_cuadro_gastos');
				Route::post('cuadroGastosExcel', 'Finanzas\Presupuesto\PresupuestoController@cuadroGastosExcel')->name('cuadroGastosExcel');
				Route::get('mostrarGastosPorPresupuesto/{id}', 'Finanzas\Presupuesto\PresupuestoController@mostrarGastosPorPresupuesto')->name('mostrar-gastos-presupuesto');

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
	});

	Route::group(['as' => 'necesidades.', 'prefix' => 'necesidades'], function () {
		Route::get('index', 'NecesidadesController@view_main_necesidades')->name('index');
		Route::group(['as' => 'requerimiento.', 'prefix' => 'requerimiento'], function () {

			Route::group(['as' => 'elaboracion.', 'prefix' => 'elaboracion'], function () {

				// Route::get('calcular-saldo', 'Logistica\RequerimientoController@calcularSaldo');
                // -----
				Route::get('index', 'Logistica\RequerimientoController@index')->name('index');
				Route::get('mostrar/{idRequerimiento?}', 'Logistica\RequerimientoController@mostrar')->name('mostrar');
				// Route::get('index/{idRequerimiento?}', 'Logistica\RequerimientoController@index')->name('index');

				Route::get('tipo-cambio-compra/{fecha}', 'Almacen\Reporte\SaldosController@tipo_cambio_compra');
				Route::get('lista-divisiones', 'Logistica\RequerimientoController@listaDivisiones');
				Route::get('mostrar-partidas/{idGrupo?}/{idProyecto?}', 'Finanzas\Presupuesto\PresupuestoController@mostrarPresupuestos')->name('mostrar-partidas');
				Route::get('mostrar-centro-costos', 'Finanzas\CentroCosto\CentroCostoController@mostrarCentroCostosSegunGrupoUsuario')->name('mostrar-centro-costos');
				Route::post('guardar-requerimiento', 'Logistica\RequerimientoController@guardarRequerimiento')->name('guardar-requerimiento');
				Route::post('actualizar-requerimiento', 'Logistica\RequerimientoController@actualizarRequerimiento')->name('actualizar-requerimiento');
				Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
				Route::get('mostrar-requerimiento/{id?}/{codigo?}', 'Logistica\RequerimientoController@mostrarRequerimiento')->name('mostrar-requerimiento');
				Route::post('elaborados', 'Logistica\RequerimientoController@listarRequerimientosElaborados')->name('elaborados');
				Route::get('imprimir-requerimiento-pdf/{id}/{codigo}', 'Logistica\RequerimientoController@generar_requerimiento_pdf');
				Route::put('anular-requerimiento/{id_requerimiento?}', 'Logistica\RequerimientoController@anularRequerimiento')->name('anular-requerimiento');

				Route::get('mostrar-categoria-adjunto', 'Logistica\RequerimientoController@mostrarCategoriaAdjunto')->name('mostrar-categoria-adjunto');
				Route::get('listar-adjuntos-requerimiento-cabecera/{idRequerimento}', 'Logistica\RequerimientoController@listaAdjuntosRequerimientoCabecera');
				Route::get('listar-adjuntos-requerimiento-detalle/{idRequerimentoDetalle}', 'Logistica\RequerimientoController@listaAdjuntosRequerimientoDetalle');

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
				Route::get('mostrar-archivos-adjuntos-requerimiento/{id_requerimiento?}/{categoria?}', 'Logistica\RequerimientoController@mostrarArchivosAdjuntosRequerimiento');
				Route::get('listar_almacenes', 'Almacen\Ubicacion\AlmacenController@mostrar_almacenes');
				Route::get('mostrar-sede', 'ConfiguracionController@mostrarSede');
				Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
				Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');
				// Route::get('verTrazabilidadRequerimiento/{id}', 'DistribucionController@verTrazabilidadRequerimiento');
				Route::get('getCodigoRequerimiento/{id}', 'LogisticaController@getCodigoRequerimiento');
				Route::get('mostrar-archivos-adjuntos/{id_detalle_requerimiento}', 'Logistica\RequerimientoController@mostrarArchivosAdjuntos');
				Route::post('save_cliente', 'LogisticaController@save_cliente');
				Route::get('listar_saldos/{id}', 'AlmacenController@listar_saldos');
				Route::get('listar_opciones', 'ProyectosController@listar_opciones');
				// Route::get('listar_partidas/{id_grupo?}/{id_op_com?}', 'EquipoController@listar_partidas');
				Route::get('listar-saldos-por-almacen', 'AlmacenController@listar_saldos_por_almacen');
				Route::get('listar-saldos-por-almacen/{id_producto}', 'AlmacenController@listar_saldos_por_almacen_producto');
				Route::get('obtener-promociones/{id_producto}/{id_almacen}', 'LogisticaController@obtener_promociones');
				Route::get('migrar_venta_directa/{id}', 'Migraciones\MigrateRequerimientoSoftLinkController@migrar_venta_directa');
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
				Route::post('lista-cuadro-presupuesto', 'Tesoreria\RequerimientoPagoController@listaCuadroPresupuesto');
				Route::post('listarIncidencias', 'Cas\IncidenciaController@listarIncidencias');

				Route::get('combo-presupuesto-interno/{idGrupo?}/{idArea?}', 'Finanzas\Presupuesto\PresupuestoInternoController@comboPresupuestoInterno');
				Route::get('obtener-detalle-presupuesto-interno/{idPresupuesto?}', 'Finanzas\Presupuesto\PresupuestoInternoController@obtenerDetallePresupuestoInterno');
				Route::get('obtener-lista-proyectos/{idGrupo?}', 'Logistica\RequerimientoController@obtenerListaProyectos');

			});

			Route::group(['as' => 'listado.', 'prefix' => 'listado'], function () {
				Route::get('index', 'Logistica\RequerimientoController@viewLista')->name('index');
				Route::post('elaborados', 'Logistica\RequerimientoController@listarRequerimientosElaborados')->name('elaborados');
				Route::get('ver-flujos/{req?}/{doc?}', 'Logistica\RequerimientoController@flujoAprobacion')->name('ver-flujos');
				Route::get('mostrar-divisiones/{idGrupo?}', 'Logistica\RequerimientoController@mostrarDivisionesDeGrupo')->name('mostrar-divisiones-de-grupo');

				Route::get('requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@mostrarCabeceraRequerimiento')->name('mostrar-cabecera-requerimiento');
				Route::get('detalle-requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@detalleRequerimiento')->name('detalle-requerimientos');

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

				Route::get('mostrarDocumentosByRequerimiento/{id}', 'Logistica\Requerimientos\TrazabilidadRequerimientoController@mostrarDocumentosByRequerimiento');
				Route::get('imprimir_transferencia/{id}', 'Almacen\Movimiento\TransferenciaController@imprimir_transferencia');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\IngresoPdfController@imprimir_ingreso');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidasPendientesController@imprimir_salida');
				Route::get('imprimir_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@imprimir_transformacion');
				Route::get('reporte-requerimientos-bienes-servicios-excel/{meOrAll}/{Empresa}/{Sede}/{Grupo}/{Division}/{FechaDesde}/{FechaHasta}/{Estado}', 'Logistica\RequerimientoController@reporteRequerimientosBienesServiciosExcel');
				Route::get('reporte-items-requerimientos-bienes-servicios-excel/{meOrAll}/{Empresa}/{Sede}/{Grupo}/{Division}/{FechaDesde}/{FechaHasta}/{Estado}', 'Logistica\RequerimientoController@reporteItemsRequerimientosBienesServiciosExcel');
				Route::get('listar-todo-archivos-adjuntos-requerimiento-logistico/{id}', 'Logistica\RequerimientoController@listarTodoArchivoAdjuntoRequerimientoLogistico');
				Route::post('anular-adjunto-requerimiento-logístico-cabecera', 'Logistica\RequerimientoController@anularArchivoAdjuntoRequerimientoLogisticoCabecera');
				Route::post('anular-adjunto-requerimiento-logístico-detalle', 'Logistica\RequerimientoController@anularArchivoAdjuntoRequerimientoLogisticoDetalle');
				Route::get('lista-adjuntos-pago/{idRequerimientoPago}', 'Tesoreria\RegistroPagoController@listarAdjuntosPago');
				Route::get('listar-archivos-adjuntos-pago/{id}', 'Logistica\RequerimientoController@listarArchivoAdjuntoPago');
				Route::get('listar-otros-adjuntos-tesoreria-orden-requerimiento/{id}', 'Logistica\RequerimientoController@listarOtrsAdjuntosTesoreriaOrdenRequerimiento');
				Route::get('listar-adjuntos-logisticos/{id}', 'Logistica\RequerimientoController@listarAdjuntosLogisticos');

				Route::get('listar-categoria-adjunto', 'Logistica\RequerimientoController@mostrarCategoriaAdjunto');
				Route::post('guardar-adjuntos-adicionales-requerimiento-compra', 'Logistica\RequerimientoController@guardarAdjuntosAdicionales');

				Route::get('listar-flujo/{idDocumento}', 'RevisarAprobarController@mostrarTodoFlujoAprobacionDeDocumento');
				// Route::get('detalleRequerimiento/{id}', 'Logistica\RequerimientoController@detalleRequerimiento')->name('detalle-requerimiento');

			});
			Route::group(['as' => 'aprobar.', 'prefix' => 'aprobar'], function () {
				// Route::get('index', 'Logistica\RequerimientoController@viewAprobar')->name('index');
				// Route::post('listado-aprobacion', 'Logistica\RequerimientoController@listadoAprobacion')->name('listado-aprobacion');
				// Route::get('getOperacion/{id1}/{id2}/{id3}/{id4}/{id5}/{id6}/{id7}/{id8}/{id9}', 'Logistica\RequerimientoController@getOperacion');
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
				Route::post('listarProductosSugeridos', 'Almacen\Catalogo\ProductoController@listarProductosSugeridos');
				Route::get('mostrar_prods_sugeridos/{part}/{desc}', 'Almacen\Catalogo\ProductoController@mostrar_prods_sugeridos');
				Route::post('guardar_mapeo_productos', 'Logistica\Requerimientos\MapeoProductosController@guardar_mapeo_productos')->name('guardar-mapeo-productos');
				Route::get('mostrar_categorias_tipo/{id}', 'Almacen\Catalogo\SubCategoriaController@mostrarSubCategoriasPorCategoria');
			});
		});

		Route::group(['as' => 'pago.', 'prefix' => 'pago'], function () {
			Route::group(['as' => 'listado.', 'prefix' => 'listado'], function () {
				Route::get('index', 'Tesoreria\RequerimientoPagoController@viewListaRequerimientoPago')->name('index');
				Route::get('listado-requerimientos-pagos-export-excel/{meOrAll}/{Empresa}/{Sede}/{Grupo}/{Division}/{FechaDesde}/{FechaHasta}/{Estado}', 'Tesoreria\RequerimientoPagoController@listadoRequerimientoPagoExportExcel');
				Route::get('listado-items-requerimientos-pagos-export-excel/{meOrAll}/{Empresa}/{Sede}/{Grupo}/{Division}/{FechaDesde}/{FechaHasta}/{Estado}', 'Tesoreria\RequerimientoPagoController@listadoItemsRequerimientoPagoExportExcel');
				Route::post('lista-requerimiento-pago', 'Tesoreria\RequerimientoPagoController@listarRequerimientoPago')->name('lista-requerimiento-pago');
				Route::get('lista-adjuntos-pago/{idRequerimientoPago}', 'Tesoreria\RegistroPagoController@listarAdjuntosPago');
				// adjuntos de tesoreria
				Route::get('obtener-otros-adjuntos-tesoreria/{id_requerimiento_pago}', 'Tesoreria\RequerimientoPagoController@obtenerOtrosAdjuntosTesoreria');
				// Route::get('detalle-requerimiento-pago/{id?}', 'Tesoreria\RequerimientoPagoController@listarDetalleRequerimientoPago')->name('detalle-requerimiento-pago');
				Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
				Route::get('listar-division-por-grupo/{id?}', 'Logistica\RequerimientoController@listarDivisionPorGrupo')->name('listar-division-por-grupo');
				Route::get('mostrar-partidas/{idGrupo?}/{idProyecto?}', 'Finanzas\Presupuesto\PresupuestoController@mostrarPresupuestos')->name('mostrar-partidas');
				Route::get('mostrar-centro-costos', 'Finanzas\CentroCosto\CentroCostoController@mostrarCentroCostosSegunGrupoUsuario')->name('mostrar-centro-costos');
				Route::post('guardar-requerimiento-pago', 'Tesoreria\RequerimientoPagoController@guardarRequerimientoPago');
				Route::post('lista-cuadro-presupuesto', 'Tesoreria\RequerimientoPagoController@listaCuadroPresupuesto');
				Route::get('mostrar-requerimiento-pago/{idRequerimientoPago}', 'Tesoreria\RequerimientoPagoController@mostrarRequerimientoPago');
				Route::post('actualizar-requerimiento-pago', 'Tesoreria\RequerimientoPagoController@actualizarRequerimientoPago');
				Route::post('anular-requerimiento-pago', 'Tesoreria\RequerimientoPagoController@anularRequerimientoPago');
				Route::get('listar-adjuntos-requerimiento-pago-cabecera/{idRequerimentoPago}', 'Tesoreria\RequerimientoPagoController@listaAdjuntosRequerimientoPagoCabecera');
				Route::get('listar-adjuntos-requerimiento-pago-detalle/{idRequerimentoPagoDetalle}', 'Tesoreria\RequerimientoPagoController@listaAdjuntosRequerimientoPagoDetalle');
				Route::get('listar-categoria-adjunto', 'ContabilidadController@listaTipoDocumentos');
				Route::post('mostrar-proveedores', 'OrdenController@mostrarProveedores');
				Route::get('listar-cuentas-bancarias-proveedor/{idProveedor?}', 'OrdenController@listarCuentasBancariasProveedor')->name('listar-cuentas-bancarias-proveedor');
				Route::post('guardar-cuenta-bancaria-proveedor', 'OrdenController@guardarCuentaBancariaProveedor');
				Route::get('imprimir-requerimiento-pago-pdf/{id}', 'Tesoreria\RequerimientoPagoController@imprimirRequerimientoPagoPdf');
				// Route::post('guardarProveedor', 'Tesoreria\ProveedorController@guardarProveedor');
				Route::post('obtener-destinatario-por-nro-documento', 'Tesoreria\RequerimientoPagoController@obtenerDestinatarioPorNumeroDeDocumento');
				Route::post('obtener-destinatario-por-nombre', 'Tesoreria\RequerimientoPagoController@obtenerDestinatarioPorNombre');
				Route::post('guardar-contribuyente', 'Tesoreria\RequerimientoPagoController@guardarContribuyente');
				Route::post('guardar-persona', 'Tesoreria\RequerimientoPagoController@guardarPersona');
				Route::post('guardar-cuenta-destinatario', 'Tesoreria\RequerimientoPagoController@guardarCuentaDestinatario');
				Route::get('obtener-cuenta-persona/{idPersona}', 'Tesoreria\RequerimientoPagoController@obtenerCuentaPersona');
				Route::get('obtener-cuenta-contribuyente/{idContribuyente}', 'Tesoreria\RequerimientoPagoController@obtenerCuentaContribuyente');
				Route::get('listar-todo-archivos-adjuntos-requerimiento-pago/{id}', 'Tesoreria\RequerimientoPagoController@listarTodoArchivoAdjuntoRequerimientoPago');
				Route::post('guardar-adjuntos-adicionales-requerimiento-pago', 'Tesoreria\RequerimientoPagoController@guardarAdjuntosAdicionales');
				Route::post('anular-adjunto-requerimiento-pago-cabecera', 'Tesoreria\RequerimientoPagoController@anularAdjuntoRequerimientoPagoCabecera');
				Route::post('anular-adjunto-requerimiento-pago-detalle', 'Tesoreria\RequerimientoPagoController@anularAdjuntoRequerimientoPagoDetalle');
				Route::get('listar_trabajadores', 'ProyectosController@listar_trabajadores');

				Route::get('combo-presupuesto-interno/{idGrupo?}/{idArea?}', 'Finanzas\Presupuesto\PresupuestoInternoController@comboPresupuestoInterno');
				Route::get('obtener-detalle-presupuesto-interno/{idPresupuesto?}', 'Finanzas\Presupuesto\PresupuestoInternoController@obtenerDetallePresupuestoInterno');
				Route::get('obtener-lista-proyectos/{idGrupo?}', 'Logistica\RequerimientoController@obtenerListaProyectos');

			});
			// Route::group(['as' => 'revisar_aprobar.', 'prefix' => 'revisar_aprobar'], function () {
			// 	Route::get('index', 'Tesoreria\RequerimientoPagoController@viewRevisarAprobarRequerimientoPago')->name('index');
			// });
		});
		Route::group(['as' => 'revisar-aprobar.', 'prefix' => 'revisar-aprobar'], function () {
			Route::group(['as' => 'listado.', 'prefix' => 'listado'], function () {
				Route::get('index', 'RevisarAprobarController@viewListaRequerimientoPagoPendienteParaAprobacion')->name('index');
				Route::post('documentos-pendientes', 'RevisarAprobarController@mostrarListaDeDocumentosPendientes');
				Route::post('documentos-aprobados', 'RevisarAprobarController@mostrarListaDeDocumentosAprobados');
				Route::get('imprimir-requerimiento-pago-pdf/{id}', 'Tesoreria\RequerimientoPagoController@imprimirRequerimientoPagoPdf');
				Route::post('guardar-respuesta', 'RevisarAprobarController@guardarRespuesta');
				Route::get('mostrar-requerimiento-pago/{idRequerimientoPago}', 'Tesoreria\RequerimientoPagoController@mostrarRequerimientoPago');
				Route::get('listar-categoria-adjunto', 'ContabilidadController@listaTipoDocumentos');
				Route::get('listar-adjuntos-requerimiento-pago-cabecera/{idRequerimentoPago}', 'Tesoreria\RequerimientoPagoController@listaAdjuntosRequerimientoPagoCabecera');
				Route::get('listar-adjuntos-requerimiento-pago-detalle/{idRequerimentoPagoDetalle}', 'Tesoreria\RequerimientoPagoController@listaAdjuntosRequerimientoPagoDetalle');
				Route::get('mostrar-requerimiento/{id?}/{codigo?}', 'Logistica\RequerimientoController@mostrarRequerimiento')->name('mostrar-requerimiento');
				Route::get('test-operacion/{idTipoDocumento}/{idTipoRequerimientoCompra}/{idGrupo}/{idDivision}/{idPrioridad}/{idMoneda}/{montoTotal}/{idTipoRequerimientoPago}/{idRolUsuarioDocList}', 'Logistica\RequerimientoController@getOperacion'); // *solo para probar si retorna data correcta de la operacion que corresponda

			});
		});
		Route::group(['as' => 'ecommerce.', 'prefix' => 'ecommerce'], function () {
			Route::get('index', 'EcommerceController@index')->name('index');
			Route::get('crear', 'EcommerceController@crear')->name('crear');
			Route::post('guardar', 'EcommerceController@guardar')->name('guardar');
			Route::post('buscar-trabajador', 'EcommerceController@buscarTrabajador');
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

			Route::group(['as' => 'compras.', 'prefix' => 'compras'], function () {
				Route::group(['as' => 'pendientes.', 'prefix' => 'pendientes'], function () {
					Route::get('index', 'ComprasPendientesController@viewComprasPendientes')->name('index');
					Route::post('requerimientos-pendientes', 'ComprasPendientesController@listarRequerimientosPendientes')->name('requerimientos-pendientes');
					Route::post('requerimientos-atendidos', 'ComprasPendientesController@listarRequerimientosAtendidos')->name('requerimientos-atendidos');
					Route::get('reporte-requerimientos-atendidos-excel/{Empresa}/{Sede}/{FechaDesde}/{FechaHasta}/{Reserva}/{Orden}', 'ComprasPendientesController@reporteRequerimientosAtendidosExcel');
					Route::get('solicitud-cotizacion-excel/{id}', 'ComprasPendientesController@solicitudCotizacionExcel');

					Route::get('exportar-lista-requerimientos-pendientes-excel', 'ComprasPendientesController@exportListaRequerimientosPendientesExcel');

					// Route::post('lista_items-cuadro-costos-por-requerimiento-pendiente-compra', 'ComprasPendientesController@get_lista_items_cuadro_costos_por_id_requerimiento_pendiente_compra')->name('lista_items-cuadro-costos-por-requerimiento-pendiente-compra');
					// Route::post('tiene-items-para-compra', 'ComprasPendientesController@tieneItemsParaCompra')->name('tiene-items-para-compra');
					Route::post('lista_items-cuadro-costos-por-requerimiento', 'ComprasPendientesController@get_lista_items_cuadro_costos_por_id_requerimiento')->name('lista_items-cuadro-costos-por-requerimiento');
					Route::get('grupo-select-item-para-compra', 'ComprasPendientesController@getGrupoSelectItemParaCompra')->name('grupo-select-item-para-compra');
					Route::post('guardar-reserva-almacen', 'ComprasPendientesController@guardarReservaAlmacen')->name('guardar-reserva-almacen');
					// Route::post('obtener-stock-almacen', 'ComprasPendientesController@obtenerStockAlmacen')->name('obtener-stock-almacen');
					Route::post('anular-reserva-almacen', 'ComprasPendientesController@anularReservaAlmacen');
					Route::post('anular-toda-reserva-detalle-requerimiento', 'ComprasPendientesController@anularTodaReservaAlmacenDetalleRequerimiento');
					Route::post('buscar-item-catalogo', 'ComprasPendientesController@buscarItemCatalogo')->name('buscar-item-catalogo');
					Route::post('guardar-items-detalle-requerimiento', 'ComprasPendientesController@guardarItemsEnDetalleRequerimiento')->name('guardar-items-detalle-requerimiento');
					Route::get('listar-almacenes', 'Almacen\Ubicacion\AlmacenController@mostrar_almacenes')->name('listar-almacenes');
					Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');

					Route::post('guardar-producto', 'AlmacenController@guardar_producto')->name('guardar-producto');

					Route::get('itemsRequerimiento/{id}', 'Logistica\Requerimientos\MapeoProductosController@itemsRequerimiento')->name('items-requerimiento');
					Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
					Route::post('listarProductosSugeridos', 'Almacen\Catalogo\ProductoController@listarProductosSugeridos');
					Route::get('mostrar_prods_sugeridos/{part}/{desc}', 'Almacen\Catalogo\ProductoController@mostrar_prods_sugeridos');
					Route::post('guardar_mapeo_productos', 'Logistica\Requerimientos\MapeoProductosController@guardar_mapeo_productos')->name('guardar-mapeo-productos');
					Route::get('mostrar_categorias_tipo/{id}', 'Almacen\Catalogo\SubCategoriaController@mostrarSubCategoriasPorCategoria');
					Route::get('detalle-requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@detalleRequerimiento')->name('detalle-requerimientos');
					Route::get('detalle-requeriento-para-reserva/{idDetalleRequerimiento?}', 'Logistica\RequerimientoController@detalleRequerimientoParaReserva')->name('detalle-requerimiento-para-reserva');
					Route::get('almacen-requeriento/{idRequerimiento?}', 'Logistica\RequerimientoController@obtenerAlmacenRequerimiento');
					Route::get('historial-reserva-producto/{idDetalleRequerimiento?}', 'Logistica\RequerimientoController@historialReservaProducto')->name('historial-reserva-producto');
					Route::get('todo-detalle-requeriento/{idRequerimiento?}/{transformadosONoTransformados?}', 'Logistica\RequerimientoController@todoDetalleRequerimiento')->name('todo-detalle-requerimiento');
					Route::get('mostrar_tipos_clasificacion/{id}', 'Almacen\Catalogo\CategoriaController@mostrarCategoriasPorClasificacion');
					Route::get('por-regularizar-cabecera/{id}', 'ComprasPendientesController@listarPorRegularizarCabecera');
					Route::get('por-regularizar-detalle/{id}', 'ComprasPendientesController@listarPorRegularizarDetalle');
					Route::post('realizar-remplazo-de-producto-comprometido-en-toda-orden', 'ComprasPendientesController@realizarRemplazoDeProductoEnTodaOrden');
					Route::post('realizar-liberacion-de-producto-comprometido-en-toda-orden', 'ComprasPendientesController@realizarLiberacionDeProductoEnTodaOrden');
					Route::post('realizar-anular-item-en-toda-orden-y-reservas', 'ComprasPendientesController@realizarAnularItemEnTodaOrdenYReservas');
					Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\IngresoPdfController@imprimir_ingreso');
					Route::get('items-orden-items-reserva-por-detalle-requerimiento/{idDetalleRequerimiento}', 'ComprasPendientesController@itemsOrdenItemsReservaPorDetalleRequerimiento');
					Route::get('finalizar-cuadro/{id}', 'OrdenController@finalizarCuadroPresupuesto');
					Route::get('mostrar-archivos-adjuntos-detalle-requerimiento/{id_detalle_requerimiento}', 'Logistica\RequerimientoController@mostrarArchivosAdjuntos');
					Route::get('listar-otros-adjuntos-tesoreria-orden-requerimiento/{id}', 'Logistica\RequerimientoController@listarOtrsAdjuntosTesoreriaOrdenRequerimiento');
					Route::get('listar-adjuntos-logisticos/{id}', 'Logistica\RequerimientoController@listarAdjuntosLogisticos');
					// Route::get('mostrar-todo-adjuntos-requerimiento/{id_requerimiento}', 'Logistica\RequerimientoController@mostrarTodoAdjuntos');
					Route::get('listar-todo-archivos-adjuntos-requerimiento-logistico/{id}', 'Logistica\RequerimientoController@listarTodoArchivoAdjuntoRequerimientoLogistico');
					Route::get('listar-archivos-adjuntos-pago/{id}', 'Logistica\RequerimientoController@listarArchivoAdjuntoPago');
					Route::get('listar-categoria-adjunto', 'Logistica\RequerimientoController@mostrarCategoriaAdjunto');
					Route::post('guardar-adjuntos-adicionales-requerimiento-compra', 'Logistica\RequerimientoController@guardarAdjuntosAdicionales');

					Route::get('almacenes-con-stock-disponible/{idProducto}', 'ComprasPendientesController@mostrarAlmacenesConStockDisponible');
					Route::post('actualizar-tipo-item-detalle-requerimiento', 'ComprasPendientesController@actualizarTipoItemDetalleRequerimiento');
					Route::post('actualizar-ajuste-estado-requerimiento', 'ComprasPendientesController@actualizarAjusteEstadoRequerimiento');

					Route::post('guardar-observacion-logistica', 'ComprasPendientesController@guardarObservacionLogistica');

					Route::get('retornar-requerimiento-atendido-a-lista-pedientes/{id}', 'ComprasPendientesController@retornarRequerimientoAtendidoAListaPendientes')->name('retornar-requerimiento-atendido-a-lista-pedientes');

				});

				Route::group(['as' => 'ordenes.', 'prefix' => 'ordenes'], function () {
					Route::group(['as' => 'elaborar.', 'prefix' => 'elaborar'], function () {
						// Route::get('notificar', 'OrdenController@notificarFinalizacion');// solo testing
						Route::get('index', 'OrdenController@view_crear_orden_requerimiento')->name('index');
						Route::post('requerimiento-detallado', 'OrdenController@ObtenerRequerimientoDetallado')->name('requerimiento-detallado');
						Route::post('detalle-requerimiento-orden', 'OrdenController@get_detalle_requerimiento_orden')->name('detalle-requerimiento-orden');
						Route::post('guardar', 'OrdenController@guardar_orden_por_requerimiento')->name('guardar');
						Route::post('actualizar', 'OrdenController@actualizar_orden_por_requerimiento')->name('actualizar');
						Route::post('mostrar-proveedores', 'OrdenController@mostrarProveedores');
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

						Route::post('listar-historial-ordenes-elaboradas', 'OrdenController@listaHistorialOrdenes');
						Route::get('mostrar-orden/{id_orden?}', 'OrdenController@mostrarOrden');
						Route::post('anular', 'OrdenController@anularOrden')->name('anular');
						Route::get('tipo-cambio-compra/{fecha}', 'Almacen\Reporte\SaldosController@tipo_cambio_compra');
						// Route::post('requerimientos-pendientes', 'ComprasPendientesController@listarRequerimientosPendientes')->name('requerimientos-pendientes');
						Route::get('detalle-requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@detalleRequerimiento')->name('detalle-requerimientos');
						Route::get('requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@requerimiento')->name('requerimientos');
						Route::post('listarRequerimientoLogisticosParaVincularView', 'Logistica\RequerimientoController@listarRequerimientoLogisticosParaVincularView');
						Route::get('listar-cuentas-bancarias-proveedor/{idProveedor?}', 'OrdenController@listarCuentasBancariasProveedor')->name('listar-cuentas-bancarias-proveedor');
						Route::post('guardar-cuenta-bancaria-proveedor', 'OrdenController@guardarCuentaBancariaProveedor');
						Route::get('migrarOrdenCompra/{id}', 'Migraciones\MigrateOrdenSoftLinkController@migrarOrdenCompra');
						Route::get('listarOrdenesPendientesMigrar', 'Migraciones\MigrateOrdenSoftLinkController@listarOrdenesPendientesMigrar');
						Route::get('ordenesPendientesMigrar', 'Migraciones\MigrateOrdenSoftLinkController@ordenesPendientesMigrar');
						Route::get('listarOrdenesSoftlinkNoVinculadas/{cod}/{ini}/{fin}', 'Migraciones\MigrateOrdenSoftLinkController@listarOrdenesSoftlinkNoVinculadas');
						// Route::get('migrarOrdenCompra/{id}', 'Migraciones\MigrateOrdenSoftLinkController@migrarOrdenCompra');
						Route::post('mostrar-catalogo-productos', 'Logistica\RequerimientoController@mostrarCatalogoProductos');
						Route::post('enviar-notificacion-finalizacion-cdp', 'OrdenController@enviarNotificacionFinalizacionCDP');
						Route::post('validar-orden-agil-orden-softlink', 'OrdenController@validarOrdenAgilOrdenSoftlink');
						Route::post('vincular-oc-softlink', 'OrdenController@vincularOcSoftlink');
						Route::get('imprimir_orden_servicio_o_transformacion/{idOportunidad}', 'Almacen\Movimiento\TransformacionController@imprimir_orden_servicio_o_transformacion');
					});
					Route::group(['as' => 'listado.', 'prefix' => 'listado'], function () {
						Route::get('index', 'OrdenController@view_listar_ordenes')->name('index');
						Route::get('listas-categorias-adjunto', 'OrdenController@listarCategoriasAdjuntos');
						Route::post('guardar-adjunto-orden', 'OrdenController@guardarAdjuntoOrden');
						Route::get('listar-archivos-adjuntos-orden/{id_order}', 'OrdenController@listarArchivosOrder');
						Route::get('historial-de-envios-a-pago-en-cuotas/{id_order}', 'OrdenController@ObtenerHistorialDeEnviosAPagoEnCuotas');
						Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
						Route::get('generar-orden-pdf/{id?}', 'OrdenController@generar_orden_por_requerimiento_pdf')->name('generar-orden-por-requerimiento-pdf'); // PDF
						Route::get('facturas/{id_orden}', 'OrdenController@obtenerFacturas');
						//nivel cabecera
						// Route::post('listar-ordenes', 'OrdenController@listarOrdenes');
						Route::post('lista-ordenes-elaboradas', 'OrdenController@listaOrdenesElaboradas');
						Route::get('detalle-orden/{id_orden}', 'OrdenController@detalleOrden');
						Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');
						// Route::get('generar_orden_pdf/{id}', 'OrdenController@generar_orden_pdf'); // PDF
						Route::get('verSession', 'LogisticaController@verSession');
						// Route::get('explorar-orden/{id_orden}', 'LogisticaController@explorar_orden');
						Route::get('exportar-lista-ordenes-elaboradas-nivel-cabecera-excel/{filtro?}', 'OrdenController@exportListaOrdenesNivelCabeceraExcel');
						Route::get('exportar-lista-ordenes-elaboradas-nivel-detalle-excel', 'OrdenController@exportListaOrdenesNivelDetalleExcel');

						// nivel item
						Route::post('lista-items-ordenes-elaboradas', 'OrdenController@listaItemsOrdenesElaboradas');
						// Route::post('listar-detalle-orden', 'OrdenController@listarDetalleOrden')->name('ordenes-en-proceso');
						Route::post('actualizar-estado', 'OrdenController@update_estado_orden')->name('actualizar-estado-orden');
						Route::post('actualizar-estado-detalle', 'OrdenController@update_estado_item_orden')->name('actualizar-estado-detalle-orden');
						Route::post('anular', 'OrdenController@anularOrden')->name('anular');
						Route::get('documentos-vinculados/{id_orden?}', 'OrdenController@documentosVinculadosOrden')->name('documentos-vinculados');
						Route::get('obtener-contribuyente-por-id-proveedor/{id_proveedor?}', 'OrdenController@obtenerContribuyentePorIdProveedor');
						Route::get('obtener-cuenta-contribuyente/{idContribuyente}', 'Tesoreria\RequerimientoPagoController@obtenerCuentaContribuyente');
						Route::get('obtener-cuenta-persona/{idPersona}', 'Tesoreria\RequerimientoPagoController@obtenerCuentaPersona');
						Route::post('guardar-persona', 'Tesoreria\RequerimientoPagoController@guardarPersona');
						Route::post('guardar-cuenta-destinatario', 'Tesoreria\RequerimientoPagoController@guardarCuentaDestinatario');
						Route::post('registrar-solicitud-de-pago', 'OrdenController@registrarSolicitudDePagar');
						Route::get('obtener-contribuyente/{id}', 'OrdenController@obtenerContribuyente');
						Route::get('obtener-persona/{id}', 'OrdenController@obtenerPersona');
						Route::post('obtener-destinatario-por-nro-documento', 'Tesoreria\RequerimientoPagoController@obtenerDestinatarioPorNumeroDeDocumento');
						Route::post('obtener-destinatario-por-nombre', 'Tesoreria\RequerimientoPagoController@obtenerDestinatarioPorNombre');

						Route::get('listar-archivos-adjuntos-pago-requerimiento/{idOrden}', 'OrdenController@listarArchivoAdjuntoPagoRequerimiento');

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
				Route::post('obtener-data-contribuyente-segun-nro-documento', 'Logistica\ProveedoresController@obtenerDataContribuyenteSegunNroDocumento');
			});

			Route::group(['as' => 'reportes.', 'prefix' => 'reportes'], function () {
				Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
				Route::get('ordenes-compra', 'ReporteLogisticaController@viewReporteOrdenesCompra')->name('ordenes-compra');
				Route::get('ordenes-servicio', 'ReporteLogisticaController@viewReporteOrdenesServicio')->name('ordenes-servicio');
				Route::post('lista-ordenes-compra', 'ReporteLogisticaController@listaOrdenesCompra')->name('lista-ordenes-compra');
				Route::post('lista-ordenes-servicio', 'ReporteLogisticaController@listaOrdenesServicio')->name('lista-ordenes-servicio');
				Route::get('transito-ordenes-compra', 'ReporteLogisticaController@viewReporteTransitoOrdenesCompra')->name('transito-ordenes-compra');
				Route::post('lista-transito-ordenes-compra', 'ReporteLogisticaController@listaTransitoOrdenesCompra')->name('lista-transito-ordenes-compra');
				Route::get('reporte-ordenes-compra-excel/{idEmpresa?}/{idSede?}/{fechaDesde?}/{fechaHasta?}', 'OrdenController@reporteOrdenesCompraExcel')->name('reporte-ordenes-compra-excel');
				Route::get('reporte-ordenes-servicio-excel/{idEmpresa?}/{idSede?}/{fechaDesde?}/{fechaHasta?}', 'OrdenController@reporteOrdenesServicioExcel')->name('reporte-ordenes-servicio-excel');
				Route::get('reporte-transito-ordenes-compra-excel/{idEmpresa?}/{idSede?}/{fechaDesde?}/{fechaHasta?}', 'OrdenController@reporteTransitoOrdenesCompraExcel')->name('reporte-transito-ordenes-compra-excel');
				Route::get('compras-locales', 'ReporteLogisticaController@viewReporteComprasLocales')->name('compras-locales');
				Route::post('lista-compras', 'ReporteLogisticaController@listarCompras');
				Route::get('reporte-compras-locales-excel', 'ReporteLogisticaController@reporteCompraLocalesExcel')->name('reporte-compras-locales-excel');
				Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
				Route::get('listar-archivos-adjuntos-pago-requerimiento/{idOrden}', 'OrdenController@listarArchivoAdjuntoPagoRequerimiento');
				Route::get('listar-archivos-adjuntos-orden/{id_order}', 'OrdenController@listarArchivosOrder');
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
					Route::get('mostrar-archivos-adjuntos/{id_detalle_requerimiento}', 'Logistica\RequerimientoController@mostrarArchivosAdjuntos');
					Route::post('guardar-archivos-adjuntos-detalle-requerimiento', 'LogisticaController@guardar_archivos_adjuntos_detalle_requerimiento');
					Route::put('eliminar-archivo-adjunto-detalle-requerimiento/{id_archivo}', 'LogisticaController@eliminar_archivo_adjunto_detalle_requerimiento');
					Route::put('descargar_olicitud_cotizacion_excel/{id_cotizacion}', 'LogisticaController@descargar_olicitud_cotizacion_excel');
					Route::get('archivos_adjuntos_cotizacion/{id_cotizacion}', 'LogisticaController@mostrar_archivos_adjuntos_cotizacion');
				});
			});
		});

		Route::get('getEstadosRequerimientos/{filtro}', 'Logistica\Distribucion\DistribucionController@getEstadosRequerimientos');
		Route::get('listarEstadosRequerimientos/{id}/{filtro}', 'Logistica\Distribucion\DistribucionController@listarEstadosRequerimientos');

		Route::group(['as' => 'distribucion.', 'prefix' => 'distribucion'], function () {
			//PENDIENTE
			// Route::group(['as' => 'despachos.', 'prefix' => 'control-despachos'], function () {
			// 	//Ordenes Despacho
			// 	Route::get('index', 'DistribucionController@view_ordenesDespacho')->name('index');
			// 	Route::get('listarRequerimientosEnProceso', 'DistribucionController@listarRequerimientosEnProceso');
			// 	Route::get('listarRequerimientosEnTransformacion', 'DistribucionController@listarRequerimientosEnTransformacion');
			// 	Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
			// 	Route::get('verDetalleIngreso/{id}', 'DistribucionController@verDetalleIngreso');
			// 	Route::post('guardar_orden_despacho', 'DistribucionController@guardar_orden_despacho');
			// 	Route::get('listarOrdenesDespacho', 'DistribucionController@listarOrdenesDespacho');
			// 	// Route::get('verDetalleDespacho/{id}', 'DistribucionController@verDetalleDespacho');
			// 	Route::post('guardar_grupo_despacho', 'DistribucionController@guardar_grupo_despacho');
			// 	Route::post('despacho_anular_requerimiento', 'DistribucionController@anular_requerimiento');
			// 	Route::get('anular_orden_despacho/{id}/{tp}', 'DistribucionController@anular_orden_despacho');
			// 	Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
			// 	Route::get('mostrarTransportistas', 'DistribucionController@mostrarTransportistas');
			// 	Route::get('listarGruposDespachados', 'DistribucionController@listarGruposDespachados');
			// 	Route::get('listarGruposDespachadosPendientesCargo', 'DistribucionController@listarGruposDespachadosPendientesCargo');
			// 	Route::get('verDetalleGrupoDespacho/{id}', 'DistribucionController@verDetalleGrupoDespacho');
			// 	Route::post('despachoTransportista', 'DistribucionController@despachoTransportista');
			// 	Route::post('despacho_revertir_despacho', 'DistribucionController@despacho_revertir_despacho');
			// 	Route::post('despacho_conforme', 'DistribucionController@despacho_conforme');
			// 	Route::post('despacho_no_conforme', 'DistribucionController@despacho_no_conforme');
			// 	Route::get('imprimir_despacho/{id}', 'DistribucionController@imprimir_despacho');
			// 	Route::get('listarAdjuntosOrdenDespacho/{id}', 'DistribucionController@listarAdjuntosOrdenDespacho');
			// 	Route::post('guardar_od_adjunto', 'DistribucionController@guardar_od_adjunto');
			// 	Route::get('anular_od_adjunto/{id}', 'DistribucionController@anular_od_adjunto');
			// 	Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');
			// 	Route::get('mostrar_clientes', 'Comercial\ClienteController@mostrar_clientes');
			// 	Route::get('listar_personas', 'RecursosHumanosController@mostrar_persona_table');
			// 	Route::get('listarDetalleTransferencias/{id}', 'Almacen\Movimiento\TransferenciaController@listarDetalleTransferencias');
			// 	Route::get('listar_ubigeos', 'AlmacenController@listar_ubigeos');
			// 	Route::post('save_cliente', 'LogisticaController@save_cliente');
			// 	Route::get('listarRequerimientosElaborados', 'DistribucionController@listarRequerimientosElaborados');
			// 	Route::get('listarRequerimientosConfirmados', 'DistribucionController@listarRequerimientosConfirmados');
			// 	Route::get('actualizaCantidadDespachosTabs', 'DistribucionController@actualizaCantidadDespachosTabs');
			// 	Route::get('mostrar_prods', 'AlmacenController@mostrar_prods');
			// 	Route::get('verSeries/{id}', 'DistribucionController@verSeries');
			// 	Route::post('guardar_producto', 'AlmacenController@guardar_producto');
			// 	Route::get('getTimelineOrdenDespacho/{id}', 'DistribucionController@getTimelineOrdenDespacho');
			// 	Route::post('guardarEstadoTimeLine', 'DistribucionController@guardarEstadoTimeLine');
			// 	Route::post('mostrarEstados', 'DistribucionController@mostrarEstados');
			// 	// Route::get('enviarFacturar/{id}', 'Logistica\Distribucion\OrdenesDespachoExternoController@enviarFacturar');
			// 	Route::get('mostrar_transportistas', 'DistribucionController@mostrar_transportistas');
			// 	Route::get('eliminarTrazabilidadEnvio/{id}', 'DistribucionController@eliminarTrazabilidadEnvio');
			// });

			// Route::group(['as' => 'trazabilidad-requerimientos.', 'prefix' => 'trazabilidad-requerimientos'], function () {

			// 	Route::get('index', 'DistribucionController@view_trazabilidad_requerimientos')->name('index');
			// 	Route::post('listarRequerimientosTrazabilidad', 'DistribucionController@listarRequerimientosTrazabilidad');
			// 	Route::get('verTrazabilidadRequerimiento/{id}', 'DistribucionController@verTrazabilidadRequerimiento');
			// 	Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
			// 	Route::get('imprimir_despacho/{id}', 'DistribucionController@imprimir_despacho');
			// 	Route::get('listarAdjuntosOrdenDespacho/{id}', 'DistribucionController@listarAdjuntosOrdenDespacho');
			// 	Route::post('guardar_od_adjunto', 'DistribucionController@guardar_od_adjunto');
			// 	Route::get('anular_od_adjunto/{id}', 'DistribucionController@anular_od_adjunto');
			// });

			Route::group(['as' => 'guias-transportistas.', 'prefix' => 'guias-transportistas'], function () {

				Route::get('index', 'Logistica\Distribucion\DistribucionController@view_guias_transportistas')->name('index');
				Route::get('listarGuiasTransportistas', 'Logistica\Distribucion\DistribucionController@listarGuiasTransportistas');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('imprimir_despacho/{id}', 'Logistica\Distribucion\DistribucionController@imprimir_despacho');
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
				Route::get('verSeries/{id}', 'Logistica\Distribucion\DistribucionController@verSeries');
			});

			Route::group(['as' => 'ordenes-despacho-externo.', 'prefix' => 'ordenes-despacho-externo'], function () {

				Route::get('index', 'Logistica\Distribucion\OrdenesDespachoExternoController@view_ordenes_despacho_externo')->name('index');
				Route::post('listarRequerimientosPendientesDespachoExterno', 'Logistica\Distribucion\OrdenesDespachoExternoController@listarRequerimientosPendientesDespachoExterno');
				Route::get('prueba/{id}', 'Logistica\Distribucion\OrdenesDespachoExternoController@prueba');
				Route::post('priorizar', 'Logistica\Distribucion\OrdenesDespachoExternoController@priorizar');
				Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');
				Route::get('listarDetalleTransferencias/{id}', 'Almacen\Movimiento\TransferenciaController@listarDetalleTransferencias');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('listar_ubigeos', 'AlmacenController@listar_ubigeos');
				Route::post('guardarOrdenDespachoExterno', 'Logistica\Distribucion\OrdenesDespachoExternoController@guardarOrdenDespachoExterno');
				Route::get('adjuntos-despacho', 'Logistica\Distribucion\OrdenesDespachoExternoController@adjuntosDespacho');
				Route::post('generarDespachoInterno', 'Logistica\Distribucion\OrdenesDespachoInternoController@generarDespachoInterno');
				// Route::get('guardarOrdenDespachoExterno/{id}', 'Logistica\Distribucion\OrdenesDespachoExternoController@guardarOrdenDespachoExterno');
				Route::post('actualizarOrdenDespachoExterno', 'Logistica\Distribucion\OrdenesDespachoExternoController@actualizarOrdenDespachoExterno');
				Route::get('anular_orden_despacho/{id}/{tp}', 'Almacen\Movimiento\SalidasPendientesController@anular_orden_despacho');
				Route::post('enviarFacturacion', 'Logistica\Distribucion\OrdenesDespachoExternoController@enviarFacturacion');
				Route::post('despachoTransportista', 'Logistica\Distribucion\OrdenesDespachoExternoController@despachoTransportista');
				Route::get('mostrarTransportistas', 'Logistica\Distribucion\DistribucionController@mostrarTransportistas');
				Route::get('getTimelineOrdenDespacho/{id}', 'Logistica\Distribucion\DistribucionController@getTimelineOrdenDespacho');
				Route::post('guardarEstadoEnvio', 'Logistica\Distribucion\DistribucionController@guardarEstadoEnvio');
				Route::get('eliminarTrazabilidadEnvio/{id}', 'Logistica\Distribucion\DistribucionController@eliminarTrazabilidadEnvio');

				Route::get('mostrarDocumentosByRequerimiento/{id}', 'Logistica\Requerimientos\TrazabilidadRequerimientoController@mostrarDocumentosByRequerimiento');
				Route::get('imprimir_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@imprimir_transformacion');
				Route::get('imprimir_transferencia/{id}', 'Almacen\Movimiento\TransferenciaController@imprimir_transferencia');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\IngresoPdfController@imprimir_ingreso');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidaPdfController@imprimir_salida');

				Route::post('verDatosContacto', 'Logistica\Distribucion\OrdenesDespachoExternoController@verDatosContacto');
				Route::get('listarContactos/{id}', 'Logistica\Distribucion\OrdenesDespachoExternoController@listarContactos');
				Route::post('actualizaDatosContacto', 'Logistica\Distribucion\OrdenesDespachoExternoController@actualizaDatosContacto');
				Route::get('seleccionarContacto/{id}/{req}', 'Logistica\Distribucion\OrdenesDespachoExternoController@seleccionarContacto');
				Route::get('mostrarContacto/{id}', 'Logistica\Distribucion\OrdenesDespachoExternoController@mostrarContacto');
				Route::get('anularContacto/{id}', 'Logistica\Distribucion\OrdenesDespachoExternoController@anularContacto');
				Route::post('enviarDatosContacto', 'Logistica\Distribucion\OrdenesDespachoExternoController@enviarDatosContacto');

				Route::post('guardarTransportista', 'Logistica\Distribucion\OrdenesDespachoExternoController@guardarTransportista');

				Route::post('despachosExternosExcel', 'Logistica\Distribucion\OrdenesDespachoExternoController@despachosExternosExcel')->name('despachosExternosExcel');
				Route::post('listarPorOc', 'mgcp\OrdenCompra\Propia\ComentarioController@listarPorOc')->name('listar-por-oc');
				Route::post('actualizarOcFisica', 'Logistica\Distribucion\OrdenesDespachoExternoController@actualizarOcFisica')->name('actualizarOcFisica');
				Route::post('actualizarSiaf', 'Logistica\Distribucion\OrdenesDespachoExternoController@actualizarSiaf')->name('actualizarSiaf');

				Route::post('anularDespachoInterno', 'Logistica\Distribucion\OrdenesDespachoInternoController@anularDespachoInterno')->name('anularDespachoInterno');

				Route::get('migrarDespachos', 'Logistica\Distribucion\OrdenesDespachoExternoController@migrarDespachos')->name('migrarDespachos');
				Route::get('generarDespachoInternoNroOrden', 'Logistica\Distribucion\OrdenesDespachoInternoController@generarDespachoInternoNroOrden')->name('generarDespachoInternoNroOrden');

				Route::get('usuariosDespacho', 'Logistica\Distribucion\OrdenesDespachoExternoController@usuariosDespacho')->name('prueba');
			});

			Route::group(['as' => 'ordenes-despacho-interno.', 'prefix' => 'ordenes-despacho-interno'], function () {

				Route::get('index', 'Logistica\Distribucion\OrdenesDespachoInternoController@view_ordenes_despacho_interno')->name('index');
				Route::post('listarRequerimientosPendientesDespachoInterno', 'Logistica\Distribucion\OrdenesDespachoInternoController@listarRequerimientosPendientesDespachoInterno');
				Route::post('priorizar', 'Logistica\Distribucion\OrdenesDespachoInternoController@priorizar');
				Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');
				Route::get('listarDetalleTransferencias/{id}', 'Almacen\Movimiento\TransferenciaController@listarDetalleTransferencias');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('listar_ubigeos', 'AlmacenController@listar_ubigeos');
				Route::post('guardarOrdenDespachoExterno', 'Logistica\Distribucion\OrdenesDespachoExternoController@guardarOrdenDespachoExterno');
				Route::get('generarDespachoInterno/{id}', 'Logistica\Distribucion\OrdenesDespachoInternoController@generarDespachoInterno');
				Route::get('anular_orden_despacho/{id}/{tp}', 'Almacen\Movimiento\SalidasPendientesController@anular_orden_despacho');
				Route::post('enviarFacturacion', 'Logistica\Distribucion\OrdenesDespachoExternoController@enviarFacturacion');

				Route::get('mostrarDocumentosByRequerimiento/{id}', 'Logistica\Requerimientos\TrazabilidadRequerimientoController@mostrarDocumentosByRequerimiento');
				Route::get('imprimir_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@imprimir_transformacion');
				Route::get('imprimir_transferencia/{id}', 'Almacen\Movimiento\TransferenciaController@imprimir_transferencia');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\IngresoPdfController@imprimir_ingreso');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidaPdfController@imprimir_salida');

				Route::get('listarDespachosInternos/{fec}', 'Logistica\Distribucion\OrdenesDespachoInternoController@listarDespachosInternos');
				Route::get('subirPrioridad/{id}', 'Logistica\Distribucion\OrdenesDespachoInternoController@subirPrioridad');
				Route::get('bajarPrioridad/{id}', 'Logistica\Distribucion\OrdenesDespachoInternoController@bajarPrioridad');
				Route::get('pasarProgramadasAlDiaSiguiente/{fec}', 'Logistica\Distribucion\OrdenesDespachoInternoController@pasarProgramadasAlDiaSiguiente');
				Route::get('listarPendientesAnteriores/{fec}', 'Logistica\Distribucion\OrdenesDespachoInternoController@listarPendientesAnteriores');
				Route::post('cambiaEstado', 'Logistica\Distribucion\OrdenesDespachoInternoController@cambiaEstado');
			});
		});
	});



	/**Almacén */
	Route::group(['as' => 'almacen.', 'prefix' => 'almacen'], function () {
		#script 1
		Route::get('script-categoria', 'AlmacenController@scripCategoria');
		#script 2
		Route::get('script-actualizar-categoria-softlink', 'AlmacenController@scripActualizarCategoriasSoftlink');

		Route::get('index', 'AlmacenController@view_main_almacen')->name('index');

		Route::get('getEstadosRequerimientos/{filtro}', 'Logistica\Distribucion\DistribucionController@getEstadosRequerimientos');
		Route::get('listarEstadosRequerimientos/{id}/{filtro}', 'Logistica\Distribucion\DistribucionController@listarEstadosRequerimientos');

		Route::group(['as' => 'catalogos.', 'prefix' => 'catalogos'], function () {

			Route::group(['as' => 'clasificaciones.', 'prefix' => 'clasificaciones'], function () {
				//Clasificacion
				Route::get('index', 'Almacen\Catalogo\ClasificacionController@view_clasificacion')->name('index');
				Route::get('listarClasificaciones', 'Almacen\Catalogo\ClasificacionController@listarClasificaciones');
				Route::get('mostrarClasificacion/{id}', 'Almacen\Catalogo\ClasificacionController@mostrarClasificacion');
				Route::post('guardarClasificacion', 'Almacen\Catalogo\ClasificacionController@guardarClasificacion');
				Route::post('actualizarClasificacion', 'Almacen\Catalogo\ClasificacionController@actualizarClasificacion');
				Route::get('anularClasificacion/{id}', 'Almacen\Catalogo\ClasificacionController@anularClasificacion');
				Route::get('revisarClasificacion/{id}', 'Almacen\Catalogo\ClasificacionController@revisarClasificacion');
			});

			Route::group(['as' => 'categorias.', 'prefix' => 'categorias'], function () {
				//Categoria
				Route::get('index', 'Almacen\Catalogo\CategoriaController@view_categoria')->name('index');
				Route::get('listarCategorias', 'Almacen\Catalogo\CategoriaController@listarCategorias');
				Route::get('mostrarCategoria/{id}', 'Almacen\Catalogo\CategoriaController@mostrarCategoria');
				Route::post('guardarCategoria', 'Almacen\Catalogo\CategoriaController@guardarCategoria');
				Route::post('actualizarCategoria', 'Almacen\Catalogo\CategoriaController@actualizarCategoria');
				Route::get('anularCategoria/{id}', 'Almacen\Catalogo\CategoriaController@anularCategoria');
				Route::get('revisarCategoria/{id}', 'Almacen\Catalogo\CategoriaController@revisarCategoria');
			});

			Route::group(['as' => 'sub-categorias.', 'prefix' => 'sub-categorias'], function () {
				//SubCategoria
				Route::get('index', 'Almacen\Catalogo\SubCategoriaController@view_sub_categoria')->name('index');
				Route::get('listar_categorias', 'Almacen\Catalogo\SubCategoriaController@mostrar_categorias');
				Route::get('mostrar_categoria/{id}', 'Almacen\Catalogo\SubCategoriaController@mostrar_categoria');
				Route::post('guardar_categoria', 'Almacen\Catalogo\SubCategoriaController@guardar_categoria');
				Route::post('actualizar_categoria', 'Almacen\Catalogo\SubCategoriaController@update_categoria');
				Route::get('anular_categoria/{id}', 'Almacen\Catalogo\SubCategoriaController@anular_categoria');
				Route::get('revisarCat/{id}', 'Almacen\Catalogo\SubCategoriaController@cat_revisar');

				Route::get('mostrar_tipos_clasificacion/{id}', 'Almacen\Catalogo\CategoriaController@mostrarCategoriasPorClasificacion');
			});

			Route::group(['as' => 'marcas.', 'prefix' => 'marcas'], function () {
				//Marca
				Route::get('index', 'Almacen\Catalogo\MarcaController@viewMarca')->name('index');
				Route::get('listarMarcas', 'Almacen\Catalogo\MarcaController@listarMarcas');
				Route::get('mostrarMarca/{id}', 'Almacen\Catalogo\MarcaController@mostrarMarca');
				Route::post('guardarMarca', 'Almacen\Catalogo\MarcaController@guardarMarca');
				Route::post('actualizarMarca', 'Almacen\Catalogo\MarcaController@actualizarMarca');
				Route::get('anularMarca/{id}', 'Almacen\Catalogo\MarcaController@anularMarca');
				Route::get('revisarMarca/{id}', 'Almacen\Catalogo\MarcaController@revisarMarca');

				//Route::post('guardar-marca', 'Almacen\Catalogo\MarcaController@guardar')->name('guardar-marca');
			});

			Route::group(['as' => 'productos.', 'prefix' => 'productos'], function () {
				//Producto
				Route::get('index', 'Almacen\Catalogo\ProductoController@view_producto')->name('index');
				Route::post('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::get('mostrar_prods_almacen/{id}', 'Almacen\Catalogo\ProductoController@mostrar_prods_almacen');
				Route::get('mostrar_producto/{id}', 'Almacen\Catalogo\ProductoController@mostrar_producto');
				Route::get('mostrarCategoriasPorClasificacion/{id}', 'Almacen\Catalogo\CategoriaController@mostrarCategoriasPorClasificacion');
				Route::get('mostrarSubCategoriasPorCategoria/{id}', 'Almacen\Catalogo\SubCategoriaController@mostrarSubCategoriasPorCategoria');
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

				Route::get('obtenerProductoSoftlink/{id}', 'Migraciones\MigrateProductoSoftlinkController@obtenerProductoSoftlink');
			});

			Route::group(['as' => 'catalogo-productos.', 'prefix' => 'catalogo-productos'], function () {
				Route::get('index', 'Almacen\Catalogo\ProductoController@view_prod_catalogo')->name('index');
				Route::get('listar_productos', 'Almacen\Catalogo\ProductoController@mostrar_productos');
				// Route::post('productosExcel', 'Almacen\Catalogo\ProductoController@productosExcel')->name('productosExcel');
				Route::post('catalogoProductosExcel', function () {
					return Excel::download(new CatalogoProductoExport, 'Catalogo_Productos.xlsx');
				})->name('catalogoProductosExcel');
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
				Route::get('detalleOrden/{id}/{soloProductos}', 'Almacen\Movimiento\OrdenesPendientesController@detalleOrden');
				Route::post('guardar_guia_com_oc', 'Almacen\Movimiento\OrdenesPendientesController@guardar_guia_com_oc');
				Route::get('verGuiasOrden/{id}', 'Almacen\Movimiento\OrdenesPendientesController@verGuiasOrden');
				// Route::post('guardar_guia_transferencia', 'Almacen\Movimiento\OrdenesPendientesController@guardar_guia_transferencia');
				Route::post('anular_ingreso', 'Almacen\Movimiento\OrdenesPendientesController@anular_ingreso');
				Route::get('cargar_almacenes/{id}', 'Almacen\Ubicacion\AlmacenController@cargar_almacenes');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\IngresoPdfController@imprimir_ingreso');

				Route::post('detalleOrdenesSeleccionadas', 'Almacen\Movimiento\OrdenesPendientesController@detalleOrdenesSeleccionadas');
				Route::get('detalleMovimiento/{id}', 'Almacen\Movimiento\OrdenesPendientesController@detalleMovimiento');
				Route::post('listarTransformacionesFinalizadas', 'Almacen\Movimiento\TransformacionController@listarTransformacionesFinalizadas');
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

				Route::post('listarProductosSugeridos', 'Almacen\Catalogo\ProductoController@listarProductosSugeridos');
				Route::get('mostrar_prods_sugeridos/{part}/{desc}', 'Almacen\Catalogo\ProductoController@mostrar_prods_sugeridos');
				Route::get('mostrar_categorias_tipo/{id}', 'Almacen\Catalogo\SubCategoriaController@mostrarSubCategoriasPorCategoria');
				Route::get('mostrar_tipos_clasificacion/{id}', 'Almacen\Catalogo\CategoriaController@mostrarCategoriasPorClasificacion');

				Route::get('sedesPorUsuario', 'Almacen\Movimiento\OrdenesPendientesController@sedesPorUsuario');
				Route::post('actualizarFiltrosPendientes', 'Almacen\Movimiento\OrdenesPendientesController@actualizarFiltrosPendientes');

				Route::post('ordenesPendientesExcel', 'Almacen\Movimiento\OrdenesPendientesController@ordenesPendientesExcel')->name('ordenesPendientesExcel');
				Route::post('ingresosProcesadosExcel', 'Almacen\Movimiento\OrdenesPendientesController@ingresosProcesadosExcel')->name('ingresosProcesadosExcel');
				Route::get('seriesExcel/{id}', 'Almacen\Movimiento\OrdenesPendientesController@seriesExcel');
				Route::post('actualizarIngreso', 'Almacen\Movimiento\OrdenesPendientesController@actualizarIngreso');

				Route::get('sedesPorUsuarioArray', 'Almacen\Movimiento\OrdenesPendientesController@sedesPorUsuarioArray');
				Route::get('getTipoCambioVenta/{fec}', 'Almacen\Movimiento\TransformacionController@getTipoCambioVenta');
				Route::get('pruebaOrdenesPendientesLista', 'Almacen\Movimiento\OrdenesPendientesController@pruebaOrdenesPendientesLista');

				Route::get('listarDevolucionesRevisadas', 'Almacen\Movimiento\DevolucionController@listarDevolucionesRevisadas');
				Route::get('listarDetalleDevolucion/{id}', 'Almacen\Movimiento\DevolucionController@listarDetalleDevolucion');
				Route::get('verFichasTecnicasAdjuntas/{id}', 'Almacen\Movimiento\DevolucionController@verFichasTecnicasAdjuntas')->name('ver-fichas-tecnicas');
			});

			Route::group(['as' => 'pendientes-salida.', 'prefix' => 'pendientes-salida'], function () {
				//Pendientes de Salida
				Route::get('index', 'Almacen\Movimiento\SalidasPendientesController@view_despachosPendientes')->name('index');
				Route::post('listarOrdenesDespachoPendientes', 'Almacen\Movimiento\SalidasPendientesController@listarOrdenesDespachoPendientes');
				Route::post('guardarSalidaGuiaDespacho', 'Almacen\Movimiento\SalidasPendientesController@guardarSalidaGuiaDespacho');
				Route::post('listarSalidasDespacho', 'Almacen\Movimiento\SalidasPendientesController@listarSalidasDespacho');
				Route::post('anular_salida', 'Almacen\Movimiento\SalidasPendientesController@anular_salida');
				Route::post('cambio_serie_numero', 'Almacen\Movimiento\SalidasPendientesController@cambio_serie_numero');
				Route::get('verDetalleDespacho/{id}/{od}/{ac}/{tra}', 'Almacen\Movimiento\SalidasPendientesController@verDetalleDespacho');
				Route::get('marcar_despachado/{id}/{tra}', 'Almacen\Movimiento\SalidasPendientesController@marcar_despachado');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidaPdfController@imprimir_salida');
				// Route::get('anular_orden_despacho/{id}', 'Almacen\Movimiento\SalidasPendientesController@anular_orden_despacho');
				Route::get('listarSeriesGuiaVen/{id}/{alm}', 'Almacen\Movimiento\SalidasPendientesController@listarSeriesGuiaVen');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');

				Route::post('actualizarSalida', 'Almacen\Movimiento\SalidasPendientesController@actualizarSalida')->name('actualizar-salida');
				Route::get('detalleMovimientoSalida/{id}', 'Almacen\Movimiento\SalidasPendientesController@detalleMovimientoSalida');
				Route::get('guia-salida-excel/{idGuia}', 'Almacen\Movimiento\SalidasPendientesController@guiaSalidaExcel');
				Route::get('guia-salida-excel-formato-okc', 'Almacen\Movimiento\GuiaSalidaExcelFormatoOKCController@construirExcel');
				Route::get('guia-salida-excel-formato-svs', 'Almacen\Movimiento\GuiaSalidaExcelFormatoSVSController@construirExcel');

				Route::get('validaStockDisponible/{id}/{alm}', 'Almacen\Movimiento\SalidasPendientesController@validaStockDisponible');

				Route::get('seriesVentaExcel/{id}', 'Almacen\Movimiento\SalidasPendientesController@seriesVentaExcel');
				Route::post('salidasPendientesExcel', 'Almacen\Movimiento\SalidasPendientesController@salidasPendientesExcel')->name('salidasPendientesExcel');
				Route::post('salidasProcesadasExcel', 'Almacen\Movimiento\SalidasPendientesController@salidasProcesadasExcel')->name('salidasProcesadasExcel');

				Route::get('actualizaItemsODE/{id}', 'Almacen\Movimiento\SalidasPendientesController@actualizaItemsODE');
				Route::get('actualizaItemsODI/{id}', 'Almacen\Movimiento\SalidasPendientesController@actualizaItemsODI');
				Route::get('atencion-ver-adjuntos', 'Almacen\Movimiento\SalidasPendientesController@verAdjuntos');
				Route::get('mostrarClientes', 'Almacen\Movimiento\SalidasPendientesController@mostrarClientes')->name('mostrarClientes');
				Route::post('guardarCliente', 'Almacen\Movimiento\SalidasPendientesController@guardarCliente')->name('guardarCliente');

				Route::get('listarDevolucionesSalidas', 'Almacen\Movimiento\DevolucionController@listarDevolucionesSalidas');
				Route::get('verDetalleDevolucion/{id}', 'Almacen\Movimiento\SalidasPendientesController@verDetalleDevolucion');
			});

			Route::group(['as' => 'customizacion.', 'prefix' => 'customizacion'], function () {
				//Pendientes de Salida
				Route::get('index', 'Almacen\Movimiento\CustomizacionController@viewCustomizacion')->name('index');
				Route::post('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::post('listarProductosAlmacen', 'Almacen\Movimiento\SaldoProductoController@listarProductosAlmacen');
				Route::post('guardar_materia', 'Almacen\Movimiento\TransformacionController@guardar_materia');
				Route::post('guardarCustomizacion', 'Almacen\Movimiento\CustomizacionController@guardarCustomizacion');
				Route::post('actualizarCustomizacion', 'Almacen\Movimiento\CustomizacionController@actualizarCustomizacion');
				Route::get('anularCustomizacion/{id}', 'Almacen\Movimiento\CustomizacionController@anularCustomizacion');
				Route::get('listar_transformaciones/{tp}', 'Almacen\Movimiento\TransformacionController@listar_transformaciones');
				Route::get('mostrarCustomizacion/{id}', 'Almacen\Movimiento\CustomizacionController@mostrarCustomizacion');
				Route::get('imprimir_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@imprimir_transformacion');
				Route::post('actualizarCostosBase', 'Almacen\Movimiento\CustomizacionController@actualizarCostosBase');
				Route::get('procesarCustomizacion/{id}', 'Almacen\Movimiento\CustomizacionController@procesarCustomizacion');
				Route::get('obtenerTipoCambio/{fec}/{mon}', 'Almacen\Movimiento\CustomizacionController@obtenerTipoCambio');
				Route::get('listarSeriesGuiaVen/{id}/{alm}', 'Almacen\Movimiento\SalidasPendientesController@listarSeriesGuiaVen');
				Route::get('validarEdicion/{id}', 'Almacen\Movimiento\CustomizacionController@validarEdicion');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\IngresoPdfController@imprimir_ingreso');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidaPdfController@imprimir_salida');
			});

			Route::group(['as' => 'devolucion.', 'prefix' => 'devolucion'], function () {
				//Devoluciones
				Route::get('index', 'Almacen\Movimiento\DevolucionController@viewDevolucion')->name('index');
				Route::post('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::get('listarDevoluciones', 'Almacen\Movimiento\DevolucionController@listarDevoluciones');
				Route::post('mostrarContribuyentes', 'Almacen\Movimiento\DevolucionController@mostrarContribuyentes');
				Route::get('mostrarDevolucion/{id}', 'Almacen\Movimiento\DevolucionController@mostrarDevolucion');
				Route::post('guardarDevolucion', 'Almacen\Movimiento\DevolucionController@guardarDevolucion');
				Route::post('actualizarDevolucion', 'Almacen\Movimiento\DevolucionController@actualizarDevolucion');
				Route::get('validarEdicion/{id}', 'Almacen\Movimiento\DevolucionController@validarEdicion');
				Route::get('anularDevolucion/{id}', 'Almacen\Movimiento\DevolucionController@anularDevolucion');
				Route::get('listarSalidasVenta/{alm}/{id}', 'Almacen\Movimiento\DevolucionController@listarSalidasVenta');
				Route::get('listarIngresos/{alm}/{id}', 'Almacen\Movimiento\DevolucionController@listarIngresos');
				Route::get('obtenerMovimientoDetalle/{id}', 'Almacen\Movimiento\DevolucionController@obtenerMovimientoDetalle');
				Route::get('listarIncidencias', 'Cas\IncidenciaController@listarIncidencias');
			});

			Route::group(['as' => 'prorrateo.', 'prefix' => 'prorrateo'], function () {
				//Pendientes de Salida
				Route::get('index', 'Almacen\Movimiento\ProrrateoCostosController@view_prorrateo_costos')->name('index');
				Route::get('mostrar_prorrateos', 'Almacen\Movimiento\ProrrateoCostosController@mostrar_prorrateos');
				Route::get('mostrar_prorrateo/{id}', 'Almacen\Movimiento\ProrrateoCostosController@mostrar_prorrateo');
				Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
				Route::get('guardar_tipo_prorrateo/{nombre}', 'Almacen\Movimiento\ProrrateoCostosController@guardar_tipo_prorrateo');
				Route::get('obtenerTipoCambio/{fec}/{mon}', 'Almacen\Movimiento\CustomizacionController@obtenerTipoCambio');
				Route::get('listar_guias_compra', 'Almacen\Movimiento\ProrrateoCostosController@listar_guias_compra');
				Route::get('listar_docs_prorrateo/{id}', 'Almacen\Movimiento\ProrrateoCostosController@listar_docs_prorrateo');
				Route::get('listar_guia_detalle/{id}', 'Almacen\Movimiento\ProrrateoCostosController@listar_guia_detalle');
				Route::post('guardarProrrateo', 'Almacen\Movimiento\ProrrateoCostosController@guardarProrrateo');
				Route::post('updateProrrateo', 'Almacen\Movimiento\ProrrateoCostosController@updateProrrateo');
				Route::get('anular_prorrateo/{id}', 'Almacen\Movimiento\ProrrateoCostosController@anular_prorrateo');
				Route::post('guardarProveedor', 'Almacen\Movimiento\ProrrateoCostosController@guardarProveedor');
			});

			Route::group(['as' => 'reservas.', 'prefix' => 'reservas'], function () {
				//Pendientes de Salida
				Route::get('index', 'Almacen\Movimiento\ReservasAlmacenController@viewReservasAlmacen')->name('index');
				Route::post('listarReservasAlmacen', 'Almacen\Movimiento\ReservasAlmacenController@listarReservasAlmacen')->name('listarReservasAlmacen');
				// Route::get('anularReserva/{id}/{id_detalle}', 'Almacen\Movimiento\ReservasAlmacenController@anularReserva');
				Route::post('anularReserva', 'Almacen\Movimiento\ReservasAlmacenController@anularReserva');
				Route::post('actualizarReserva', 'Almacen\Movimiento\ReservasAlmacenController@actualizarReserva');
				Route::get('actualizarReservas', 'Almacen\Movimiento\ReservasAlmacenController@actualizarReservas');
				Route::post('actualizarEstadoReserva', 'Almacen\Movimiento\ReservasAlmacenController@actualizarEstadoReserva');
			});

			Route::group(['as' => 'requerimientos-almacen.', 'prefix' => 'requerimientos-almacen'], function () {
				//Pendientes de Salida
				Route::get('index', 'Almacen\Reporte\ListaRequerimientosAlmacenController@viewRequerimientosAlmacen')->name('index');
				Route::post('listarRequerimientosAlmacen', 'Almacen\Reporte\ListaRequerimientosAlmacenController@listarRequerimientosAlmacen')->name('listarRequerimientosAlmacen');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('listarDetalleTransferencias/{id}', 'Almacen\Movimiento\TransferenciaController@listarDetalleTransferencias');
				Route::post('cambioAlmacen', 'Almacen\Reporte\ListaRequerimientosAlmacenController@cambioAlmacen');
				Route::get('listarDetalleRequerimiento/{id}', 'Almacen\Reporte\ListaRequerimientosAlmacenController@listarDetalleRequerimiento');
				Route::post('anularDespachoInterno', 'Logistica\Distribucion\OrdenesDespachoInternoController@anularDespachoInterno')->name('anularDespachoInterno');
				Route::post('guardar-ajuste-transformacion-requerimiento', 'ComprasPendientesController@guardarAjusteTransformacionRequerimiento')->name('guardar-ajuste-transformacion-requerimiento');
				Route::get('mostrar-requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@requerimiento');
				Route::get('detalle-requerimiento/{idRequerimiento?}', 'Logistica\RequerimientoController@detalleRequerimiento')->name('detalle-requerimientos');

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
			Route::get('enviarComprobanteSoftlink/{id}', 'Migraciones\MigrateFacturasSoftlinkController@enviarComprobanteSoftlink');
			Route::get('documentos_ver/{id}', 'Almacen\Movimiento\OrdenesPendientesController@documentos_ver');
			Route::get('actualizarSedesFaltantes', 'Migraciones\MigrateFacturasSoftlinkController@actualizarSedesFaltantes');
			Route::get('actualizarProveedorComprobantes', 'Migraciones\MigrateFacturasSoftlinkController@actualizarProveedorComprobantes');
			Route::get('migrarComprobantesSoftlink', 'Migraciones\MigrateFacturasSoftlinkController@migrarComprobantesSoftlink');
			Route::get('migrarItemsComprobantesSoftlink', 'Migraciones\MigrateFacturasSoftlinkController@migrarItemsComprobantesSoftlink');

			Route::get('lista-comprobantes-pago-export-excel', 'ComprobanteCompraController@exportListaComprobantesPagos')->name('lista.comprobante.pago.export.excel');
		});

		Route::group(['as' => 'transferencias.', 'prefix' => 'transferencias'], function () {

			Route::group(['as' => 'gestion-transferencias.', 'prefix' => 'gestion-transferencias'], function () {
				//Transferencias
				Route::get('index', 'Almacen\Movimiento\TransferenciaController@view_listar_transferencias')->name('index');
				Route::post('listarRequerimientos', 'Almacen\Movimiento\TransferenciaController@listarRequerimientos');
				Route::get('listarTransferenciaDetalle/{id}', 'Almacen\Movimiento\TransferenciaController@listarTransferenciaDetalle');
				Route::post('guardarIngresoTransferencia', 'Almacen\Movimiento\TransferenciaController@guardarIngresoTransferencia');
				Route::post('guardarSalidaTransferencia', 'Almacen\Movimiento\TransferenciaController@guardarSalidaTransferencia');
				Route::post('anularTransferenciaIngreso', 'Almacen\Movimiento\TransferenciaController@anularTransferenciaIngreso');
				Route::get('ingreso_transferencia/{id}', 'Almacen\Movimiento\TransferenciaController@ingreso_transferencia');
				// Route::get('transferencia_nextId/{id}', 'Almacen\Movimiento\TransferenciaController@transferencia_nextId');
				Route::post('anularTransferenciaSalida', 'Almacen\Movimiento\TransferenciaController@anularTransferenciaSalida');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\IngresoPdfController@imprimir_ingreso');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidaPdfController@imprimir_salida');
				Route::post('listarTransferenciasPorEnviar', 'Almacen\Movimiento\TransferenciaController@listarTransferenciasPorEnviar');
				Route::post('listarTransferenciasPorRecibir', 'Almacen\Movimiento\TransferenciaController@listarTransferenciasPorRecibir');
				Route::post('listarTransferenciasRecibidas', 'Almacen\Movimiento\TransferenciaController@listarTransferenciasRecibidas');
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
				Route::get('listarSeriesGuiaVen/{id}/{alm}', 'Almacen\Movimiento\SalidasPendientesController@listarSeriesGuiaVen');
				Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');
				Route::get('mostrarTransportistas', 'Logistica\Distribucion\DistribucionController@mostrarTransportistas');

				Route::get('autogenerarDocumentosCompra/{id}/{tr}', 'Tesoreria\Facturacion\VentasInternasController@autogenerarDocumentosCompra')->name('autogenerarDocumentosCompra');
				Route::get('verDocumentosAutogenerados/{id}', 'Tesoreria\Facturacion\VentasInternasController@verDocumentosAutogenerados');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('almacenesPorUsuario', 'Almacen\Movimiento\TransferenciaController@almacenesPorUsuario');

				Route::post('listarProductosAlmacen', 'Almacen\Movimiento\SaldoProductoController@listarProductosAlmacen');
				Route::post('nuevaTransferencia', 'Almacen\Movimiento\TransferenciaController@nuevaTransferencia');
				Route::get('pruebaSaldos', 'Almacen\Movimiento\SaldoProductoController@pruebaSaldos');

				Route::get('getAlmacenesPorEmpresa/{id}', 'Almacen\Movimiento\TransferenciaController@getAlmacenesPorEmpresa');
				Route::get('imprimir_transferencia/{id}', 'Almacen\Movimiento\TransferenciaController@imprimir_transferencia');

				Route::post('actualizarCostosVentasInternas', 'Tesoreria\Facturacion\VentasInternasController@actualizarCostosVentasInternas');
				Route::post('actualizarValorizacionesIngresos', 'Tesoreria\Facturacion\VentasInternasController@actualizarValorizacionesIngresos');
			});
		});

		Route::group(['as' => 'reportes.', 'prefix' => 'reportes'], function () {

			Route::group(['as' => 'saldos.', 'prefix' => 'saldos'], function () {

				Route::get('tipo_cambio_compra/{fecha}', 'Almacen\Reporte\SaldosController@tipo_cambio_compra');

				Route::get('index', 'Almacen\Reporte\SaldosController@view_saldos')->name('index');
				Route::post('filtrar', 'Almacen\Reporte\SaldosController@filtrar')->name('filtrar');
				Route::post('listar', 'Almacen\Reporte\SaldosController@listar')->name('listar');
				Route::get('verRequerimientosReservados/{id}/{alm}', 'Almacen\Reporte\SaldosController@verRequerimientosReservados');
				Route::get('exportar', 'Almacen\Reporte\SaldosController@exportar')->name('exportar');
				Route::get('exportarSeries', 'Almacen\Reporte\SaldosController@exportarSeries')->name('exportarSeries');
				Route::get('exportarAntiguedades', 'Almacen\Reporte\SaldosController@exportarAntiguedades')->name('exportarAntiguedades');
				Route::post('exportar-valorizacion', 'Almacen\Reporte\SaldosController@valorizacion')->name('exportar-valorizacion');
				Route::get('actualizarFechasIngresoSoft/{id}', 'Migraciones\MigrateProductoSoftlinkController@actualizarFechasIngresoSoft')->name('actualizarFechasIngresoSoft');
				Route::get('actualizarFechasIngresoAgile/{id}', 'Migraciones\MigrateProductoSoftlinkController@actualizarFechasIngresoAgile')->name('actualizarFechasIngresoSoft');
			});

			Route::group(['as' => 'lista-ingresos.', 'prefix' => 'lista-ingresos'], function () {

				Route::get('index', 'AlmacenController@view_ingresos')->name('index');
				Route::get('listar_ingresos/{empresa}/{sede}/{alm}/{cond}/{fini}/{ffin}/{prov}/{usu}/{mon}/{tra}', 'AlmacenController@listar_ingresos_lista');
				Route::get('update_revisado/{id}/{rev}/{obs}', 'AlmacenController@update_revisado');

				Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
				Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
				Route::get('listar_transportistas_com', 'AlmacenController@listar_transportistas_com');
				Route::get('listar_transportistas_ven', 'AlmacenController@listar_transportistas_ven');

				Route::get('listar-ingresos-excel/{empresa}/{sede}/{alm}/{cond}/{fini}/{ffin}/{prov}/{usu}/{mon}/{tra}', 'AlmacenController@ExportarExcelListaIngresos');
				// reportes con modelos
				Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
				Route::post('listar-ingresos', 'Almacen\Reporte\ListaIngresosController@listarIngresos');
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

				Route::get('listar-salidas-excel/{empresa}/{sede}/{alm}/{cond}/{fini}/{ffin}/{prov}/{usu}/{mon}', 'AlmacenController@ExportarExcelListaSalidas');
				// reportes con modelos
				Route::get('listar-sedes-por-empresa/{id?}', 'Logistica\RequerimientoController@listarSedesPorEmpresa')->name('listar-sedes-por-empresa');
				Route::post('listar-salidas', 'Almacen\Reporte\ListaSalidasController@listarSalidas');
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
				Route::get('exportar_kardex_general/{id}/{fini}/{ffin}', 'Almacen\Reporte\ReportesController@exportarKardex');
			});

			Route::group(['as' => 'kardex-productos.', 'prefix' => 'kardex-productos'], function () {

				Route::get('index', 'AlmacenController@view_kardex_detallado')->name('index');
				Route::get('kardex_producto/{id}/{alm}/{fini}/{ffin}', 'AlmacenController@kardex_producto');
				Route::get('listar_kardex_producto/{id}/{alm}/{fini}/{ffin}', 'AlmacenController@kardex_producto');
				Route::get('kardex_detallado/{id}/{alm}/{fini}/{ffin}', 'AlmacenController@download_kardex_producto');
				Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
				Route::get('datos_producto/{id}', 'Almacen\Reporte\KardexSerieController@datos_producto');
				Route::post('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
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

			Route::group(['as' => 'stock-series.', 'prefix' => 'stock-serie'], function () {

				Route::get('index', 'AlmacenController@view_stock_series')->name('index');
				Route::post('listar_stock_series', 'AlmacenController@listar_stock_series');
				Route::get('prueba_exportar_excel', 'AlmacenController@obtener_data_stock_series');
				Route::get('exportar_excel', 'AlmacenController@exportar_stock_series_excel');
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

			Route::group(['as' => 'tablero-transformaciones.', 'prefix' => 'tablero-transformaciones'], function () {

				Route::get('index', 'Logistica\Distribucion\OrdenesTransformacionController@view_tablero_transformaciones')->name('index');
				Route::get('listarDespachosInternos/{fec}', 'Logistica\Distribucion\OrdenesDespachoInternoController@listarDespachosInternos');
				Route::get('subirPrioridad/{id}', 'Logistica\Distribucion\OrdenesDespachoInternoController@subirPrioridad');
				Route::get('bajarPrioridad/{id}', 'Logistica\Distribucion\OrdenesDespachoInternoController@bajarPrioridad');
				Route::get('pasarProgramadasAlDiaSiguiente/{fec}', 'Logistica\Distribucion\OrdenesDespachoInternoController@pasarProgramadasAlDiaSiguiente');
				Route::get('listarPendientesAnteriores/{fec}', 'Logistica\Distribucion\OrdenesDespachoInternoController@listarPendientesAnteriores');
				Route::get('imprimir_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@imprimir_transformacion');
				Route::post('cambiaEstado', 'Logistica\Distribucion\OrdenesDespachoInternoController@cambiaEstado');
			});

			Route::group(['as' => 'gestion-customizaciones.', 'prefix' => 'gestion-customizaciones'], function () {
				//Transformaciones
				Route::get('index', 'Almacen\Movimiento\TransformacionController@view_listar_transformaciones')->name('index');
				Route::get('listarTransformacionesProcesadas', 'Almacen\Movimiento\TransformacionController@listarTransformacionesProcesadas');
				Route::post('listar_transformaciones_pendientes', 'Almacen\Movimiento\TransformacionController@listar_transformaciones_pendientes');
				Route::post('listarCuadrosCostos', 'Almacen\Movimiento\TransformacionController@listarCuadrosCostos');
				Route::post('generarTransformacion', 'Almacen\Movimiento\TransformacionController@generarTransformacion');
				Route::get('obtenerCuadro/{id}/{tipo}', 'Almacen\Movimiento\TransformacionController@obtenerCuadro');
				Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::get('id_ingreso_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@id_ingreso_transformacion');
				Route::get('id_salida_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@id_salida_transformacion');
				Route::get('imprimir_ingreso/{id}', 'Almacen\Movimiento\IngresoPdfController@imprimir_ingreso');
				Route::get('imprimir_salida/{id}', 'Almacen\Movimiento\SalidaPdfController@imprimir_salida');
				Route::get('imprimir_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@imprimir_transformacion');
				Route::get('recibido_conforme_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@recibido_conforme_transformacion');
				Route::get('no_conforme_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@no_conforme_transformacion');
				Route::get('iniciar_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@iniciar_transformacion');
				Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');
			});

			Route::group(['as' => 'hoja-transformacion.', 'prefix' => 'hoja-transformacion'], function () {
				//Transformaciones
				Route::get('index', 'Almacen\Movimiento\TransformacionController@view_transformacion')->name('index');
				Route::post('guardar_transformacion', 'Almacen\Movimiento\TransformacionController@guardar_transformacion');
				Route::post('update_transformacion', 'Almacen\Movimiento\TransformacionController@update_transformacion');
				Route::get('listar_transformaciones/{tp}', 'Almacen\Movimiento\TransformacionController@listar_transformaciones');
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
				// Route::post('update_materia', 'Almacen\Movimiento\TransformacionController@update_materia');
				// Route::post('update_directo', 'Almacen\Movimiento\TransformacionController@update_directo');
				// Route::post('update_indirecto', 'Almacen\Movimiento\TransformacionController@update_indirecto');
				// Route::post('update_sobrante', 'Almacen\Movimiento\TransformacionController@update_sobrante');
				// Route::post('update_transformado', 'Almacen\Movimiento\TransformacionController@update_transformado');
				Route::get('anular_materia/{id}', 'Almacen\Movimiento\TransformacionController@anular_materia');
				Route::get('anular_directo/{id}', 'Almacen\Movimiento\TransformacionController@anular_directo');
				Route::get('anular_indirecto/{id}', 'Almacen\Movimiento\TransformacionController@anular_indirecto');
				Route::get('anular_sobrante/{id}', 'Almacen\Movimiento\TransformacionController@anular_sobrante');
				Route::get('anular_transformado/{id}', 'Almacen\Movimiento\TransformacionController@anular_transformado');
				Route::get('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::post('guardar_producto', 'Almacen\Catalogo\ProductoController@guardar_producto');
				Route::get('imprimir_transformacion/{id}', 'Almacen\Movimiento\TransformacionController@imprimir_transformacion');
			});
		});

		Route::group(['as' => 'garantias.', 'prefix' => 'garantias'], function () {

			Route::group(['as' => 'incidencias.', 'prefix' => 'incidencias'], function () {

				Route::get('index', 'Cas\IncidenciaController@view_incidencia')->name('index');
				Route::get('listarIncidencias', 'Cas\IncidenciaController@listarIncidencias');
				Route::get('mostrarIncidencia/{id}', 'Cas\IncidenciaController@mostrarIncidencia');
				Route::get('listarSalidasVenta', 'Cas\IncidenciaController@listarSalidasVenta');

				Route::post('verDatosContacto', 'Logistica\Distribucion\OrdenesDespachoExternoController@verDatosContacto');
				Route::get('listarContactos/{id}', 'Logistica\Distribucion\OrdenesDespachoExternoController@listarContactos');
				Route::post('actualizaDatosContacto', 'Logistica\Distribucion\OrdenesDespachoExternoController@actualizaDatosContacto');
				Route::get('seleccionarContacto/{id}/{req}', 'Logistica\Distribucion\OrdenesDespachoExternoController@seleccionarContacto');
				Route::get('mostrarContacto/{id}', 'Logistica\Distribucion\OrdenesDespachoExternoController@mostrarContacto');
				Route::get('anularContacto/{id}', 'Logistica\Distribucion\OrdenesDespachoExternoController@anularContacto');
				Route::get('listar_ubigeos', 'AlmacenController@listar_ubigeos');

				Route::get('listarSeriesProductos/{id}', 'Cas\IncidenciaController@listarSeriesProductos');
				Route::post('guardarIncidencia', 'Cas\IncidenciaController@guardarIncidencia');
				Route::post('actualizarIncidencia', 'Cas\IncidenciaController@actualizarIncidencia');
				Route::get('anularIncidencia/{id}', 'Cas\IncidenciaController@anularIncidencia');

				Route::get('imprimirIncidencia/{id}', 'Cas\IncidenciaController@imprimirIncidencia');
				Route::get('imprimirFichaAtencionBlanco/{id}', 'Cas\IncidenciaController@imprimirFichaAtencionBlanco');

			});

			Route::group(['as' => 'devolucionCas.', 'prefix' => 'devolucionCas'], function () {
				//Devoluciones
				Route::get('index', 'Almacen\Movimiento\DevolucionController@viewDevolucionCas')->name('index');
				Route::post('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
				Route::get('listarDevoluciones', 'Almacen\Movimiento\DevolucionController@listarDevoluciones');
				Route::post('mostrarContribuyentes', 'Almacen\Movimiento\DevolucionController@mostrarContribuyentes');
				Route::get('mostrarDevolucion/{id}', 'Almacen\Movimiento\DevolucionController@mostrarDevolucion');
				Route::post('guardarDevolucion', 'Almacen\Movimiento\DevolucionController@guardarDevolucion');
				Route::post('actualizarDevolucion', 'Almacen\Movimiento\DevolucionController@actualizarDevolucion');
				Route::get('validarEdicion/{id}', 'Almacen\Movimiento\DevolucionController@validarEdicion');
				Route::get('anularDevolucion/{id}', 'Almacen\Movimiento\DevolucionController@anularDevolucion');
				Route::get('listarSalidasVenta/{alm}/{id}', 'Almacen\Movimiento\DevolucionController@listarSalidasVenta');
				Route::get('listarIngresos/{alm}/{id}', 'Almacen\Movimiento\DevolucionController@listarIngresos');
				Route::get('obtenerMovimientoDetalle/{id}', 'Almacen\Movimiento\DevolucionController@obtenerMovimientoDetalle');
				Route::get('listarIncidencias', 'Cas\IncidenciaController@listarIncidencias');
			});

			Route::group(['as' => 'fichas.', 'prefix' => 'fichas'], function () {

				Route::get('index', 'Cas\FichaReporteController@view_ficha_reporte')->name('index');
				Route::post('listarIncidencias', 'Cas\FichaReporteController@listarIncidencias');
				Route::post('guardarFichaReporte', 'Cas\FichaReporteController@guardarFichaReporte');
				Route::post('actualizarFichaReporte', 'Cas\FichaReporteController@actualizarFichaReporte');
				Route::get('anularFichaReporte/{id}', 'Cas\FichaReporteController@anularFichaReporte');
				Route::get('listarFichasReporte/{id}', 'Cas\FichaReporteController@listarFichasReporte');
				Route::post('cerrarIncidencia', 'Cas\FichaReporteController@cerrarIncidencia');
				Route::post('cancelarIncidencia', 'Cas\FichaReporteController@cancelarIncidencia');

				Route::get('verAdjuntosFicha/{id}', 'Cas\FichaReporteController@verAdjuntosFicha')->name('ver-adjuntos-ficha');

				Route::get('imprimirFichaReporte/{id}', 'Cas\FichaReporteController@imprimirFichaReporte');
				Route::get('incidenciasExcel', 'Cas\FichaReporteController@incidenciasExcel')->name('incidenciasExcel');
				Route::get('incidenciasExcelConHistorial', 'Cas\FichaReporteController@incidenciasExcelConHistorial')->name('incidenciasExcelConHistorial');

				Route::get('listarDevoluciones', 'Almacen\Movimiento\DevolucionController@listarDevoluciones');
				Route::post('guardarFichaTecnica', 'Almacen\Movimiento\DevolucionController@guardarFichaTecnica');
				Route::get('verFichasTecnicasAdjuntas/{id}', 'Almacen\Movimiento\DevolucionController@verFichasTecnicasAdjuntas')->name('ver-fichas-tecnicas');
				Route::post('conformidadDevolucion', 'Almacen\Movimiento\DevolucionController@conformidadDevolucion')->name('conformidad-devolucion');
				Route::get('revertirConformidad/{id}', 'Almacen\Movimiento\DevolucionController@revertirConformidad')->name('revertir-devolucion');

                Route::post('clonarIncidencia', 'Cas\FichaReporteController@clonarIncidencia');
			});
            Route::group(['as' => 'marca.', 'prefix' => 'marca'], function () {

                Route::get('inicio', 'Cas\CasMarcaController@inicio')->name('inicio');
                Route::post('listar', 'Cas\CasMarcaController@listar')->name('listar');
                Route::post('guardar', 'Cas\CasMarcaController@guardar')->name('guardar');
                Route::get('editar', 'Cas\CasMarcaController@editar')->name('editar');
                Route::post('actualizar', 'Cas\CasMarcaController@actualizar')->name('actualizar');
                Route::post('eliminar', 'Cas\CasMarcaController@eliminar')->name('eliminar');
			});

            Route::group(['as' => 'modelo.', 'prefix' => 'modelo'], function () {

                Route::get('inicio', 'Cas\CasModeloController@inicio')->name('inicio');
                Route::post('listar', 'Cas\CasModeloController@listar')->name('listar');
                Route::post('guardar', 'Cas\CasModeloController@guardar')->name('guardar');
                Route::get('editar', 'Cas\CasModeloController@editar')->name('editar');
                Route::post('actualizar', 'Cas\CasModeloController@actualizar')->name('actualizar');
                Route::post('eliminar', 'Cas\CasModeloController@eliminar')->name('eliminar');
			});

            Route::group(['as' => 'producto.', 'prefix' => 'producto'], function () {

                Route::get('inicio', 'Cas\CasProductoController@inicio')->name('inicio');
                Route::post('listar', 'Cas\CasProductoController@listar')->name('listar');
                Route::post('guardar', 'Cas\CasProductoController@guardar')->name('guardar');
                Route::get('editar', 'Cas\CasProductoController@editar')->name('editar');
                Route::post('actualizar', 'Cas\CasProductoController@actualizar')->name('actualizar');
                Route::post('eliminar', 'Cas\CasProductoController@eliminar')->name('eliminar');
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

			Route::get('mostrarGastosPorPresupuesto/{id}', 'Finanzas\Presupuesto\PresupuestoController@mostrarGastosPorPresupuesto')->name('mostrar-gastos-presupuesto');
			Route::post('cuadroGastosExcel', 'Finanzas\Presupuesto\PresupuestoController@cuadroGastosExcel')->name('cuadroGastosExcel');

            Route::group(['as' => 'presupuesto-interno.', 'prefix' => 'presupuesto-interno'], function () {
                //Presupuesto interno
                Route::get('lista', 'Finanzas\Presupuesto\PresupuestoInternoController@lista')->name('lista');
                Route::post('lista-presupuesto-interno', 'Finanzas\Presupuesto\PresupuestoInternoController@listaPresupuestoInterno')->name('lista-presupuesto-interno');
                Route::get('crear', 'Finanzas\Presupuesto\PresupuestoInternoController@crear')->name('crear');

                Route::get('presupuesto-interno-detalle', 'Finanzas\Presupuesto\PresupuestoInternoController@presupuestoInternoDetalle')->name('presupuesto-interno-detalle');
                Route::post('guardar', 'Finanzas\Presupuesto\PresupuestoInternoController@guardar')->name('guardar');

                Route::post('editar', 'Finanzas\Presupuesto\PresupuestoInternoController@editar')->name('editar');
                Route::post('editar-presupuesto-aprobado', 'Finanzas\Presupuesto\PresupuestoInternoController@editarPresupuestoAprobado')->name('editar-presupuesto-aprobado');
                Route::post('actualizar', 'Finanzas\Presupuesto\PresupuestoInternoController@actualizar')->name('actualizar');
                Route::post('eliminar', 'Finanzas\Presupuesto\PresupuestoInternoController@eliminar')->name('eliminar');

                Route::get('get-area', 'Finanzas\Presupuesto\PresupuestoInternoController@getArea');
                // exportable de presupiesto interno
                Route::post('get-presupuesto-interno', 'Finanzas\Presupuesto\PresupuestoInternoController@getPresupuestoInterno');

                //exportable de excel total ejecutado
                Route::post('presupuesto-ejecutado-excel', 'Finanzas\Presupuesto\PresupuestoInternoController@presupuestoEjecutadoExcel');

                Route::post('aprobar', 'Finanzas\Presupuesto\PresupuestoInternoController@aprobar');
                Route::post('editar-monto-partida', 'Finanzas\Presupuesto\PresupuestoInternoController@editarMontoPartida');
                // buscar partidas
                Route::post('buscar-partida-combo', 'Finanzas\Presupuesto\PresupuestoInternoController@buscarPartidaCombo');
                // prueba de presupuestos
				Route::get('cierre-mes', 'Finanzas\Presupuesto\PresupuestoInternoController@cierreMes');

                Route::group(['as' => 'script.', 'prefix' => 'script'], function () {
                    Route::get('generar-presupuesto-gastos', 'Finanzas\Presupuesto\ScriptController@generarPresupuestoGastos');
                    Route::get('homologacion-partidas', 'Finanzas\Presupuesto\ScriptController@homologarPartida');
                    Route::get('total-presupuesto/{presup}/{tipo}', 'Finanzas\Presupuesto\ScriptController@totalPresupuesto');
                    Route::get('total-consumido-mes/{presup}/{tipo}/{mes}', 'Finanzas\Presupuesto\ScriptController@totalConsumidoMes');
                    Route::get('total-ejecutado', 'Finanzas\Presupuesto\ScriptController@totalEjecutado');
                    Route::get('regularizar-montos', 'Finanzas\Presupuesto\ScriptController@montosRegular');

                    Route::get('total-presupuesto-anual-niveles/{presupuesto_intero_id}/{tipo}/{nivel}/{tipo_campo}', 'Finanzas\Presupuesto\ScriptController@totalPresupuestoAnualPartidasNiveles');
                });
				Route::get('actualizaEstadoHistorial/{id}/{est}', 'Finanzas\Presupuesto\PresupuestoInternoController@actualizaEstadoHistorial');
            });

            Route::group(['as' => 'normalizar.', 'prefix' => 'normalizar'], function () {
                Route::get('presupuesto', 'Finanzas\Normalizar\NormalizarController@lista')->name('presupuesto');
                Route::get('listar', 'Finanzas\Normalizar\NormalizarController@listar')->name('listar');
                Route::post('listar-requerimientos-pagos', 'Finanzas\Normalizar\NormalizarController@listarRequerimientosPagos')->name('listar-requerimientos-pagos');
                Route::post('listar-ordenes', 'Finanzas\Normalizar\NormalizarController@listarOrdenes')->name('listar-ordenes');
                Route::post('obtener-presupuesto', 'Finanzas\Normalizar\NormalizarController@obtenerPresupuesto')->name('obtener-presupuesto');
                Route::post('vincular-partida', 'Finanzas\Normalizar\NormalizarController@vincularPartida')->name('vincular-partida');

            });
		});

		Route::group(['as' => 'centro-costos.', 'prefix' => 'centro-costos'], function () {
			//Centro de Costos
			Route::get('index', 'Finanzas\CentroCosto\CentroCostoController@index')->name('index');
			Route::get('mostrar-centro-costos', 'Finanzas\CentroCosto\CentroCostoController@mostrarCentroCostos')->name('mostrar-centro-costos');
			Route::post('guardarCentroCosto', 'Finanzas\CentroCosto\CentroCostoController@guardarCentroCosto')->name('guardar-centro-costo');
			Route::post('actualizar-centro-costo', 'Finanzas\CentroCosto\CentroCostoController@actualizarCentroCosto')->name('actualizar-centro-costo');
			Route::get('anular-centro-costo/{id}', 'Finanzas\CentroCosto\CentroCostoController@anularCentroCosto')->name('anular-centro-costo');
		});


		Route::group(['as' => 'reportes.', 'prefix' => 'reportes'], function () {
			Route::group(['as' => 'gastos.', 'prefix' => 'gastos'], function () {
				Route::get('index-requerimiento-logistico', 'Finanzas\Reportes\ReporteGastoController@indexReporteGastoRequerimientoLogistico')->name('index-requerimiento-logistico');
				Route::get('index-requerimiento-pago', 'Finanzas\Reportes\ReporteGastoController@indexReporteGastoRequerimientoPago')->name('index-requerimiento-pago');
				Route::get('index-cdp', 'Finanzas\Reportes\ReporteGastoController@indexReporteGastoCDP')->name('index-cdp');

				Route::post('lista-requerimiento-logistico', 'Finanzas\Reportes\ReporteGastoController@listaGastoDetalleRequerimientoLogistico')->name('lista-requerimiento-logistico');
				Route::post('lista-requerimiento-pago', 'Finanzas\Reportes\ReporteGastoController@listaGastoDetalleRequerimientoPago')->name('lista-requerimiento-pago');
				Route::post('lista-cdp', 'Finanzas\Reportes\ReporteGastoController@listaGastoCDP')->name('lista-cdp');

				Route::get('exportar-requerimiento-logistico-excel', 'Finanzas\Reportes\ReporteGastoController@listaGastoDetalleRequerimientoLogisticoExcel');
				Route::get('exportar-requerimiento-pago-excel', 'Finanzas\Reportes\ReporteGastoController@listaGastoDetalleRequerimienoPagoExcel');
				Route::get('exportar-cdp-excel', 'Finanzas\Reportes\ReporteGastoController@listaGastoCDPExcel');

			});
		});
	});

	/**Tesoreria */
	Route::group(['as' => 'tesoreria.', 'prefix' => 'tesoreria'], function () {

		Route::get('index', 'Tesoreria\RegistroPagoController@view_main_tesoreria')->name('index');

		Route::group(['as' => 'pagos.', 'prefix' => 'pagos'], function () {

			Route::group(['as' => 'procesar-pago.', 'prefix' => 'procesar-pago'], function () {

				Route::get('index', 'Tesoreria\RegistroPagoController@view_pendientes_pago')->name('index');
				Route::post('guardar-adjuntos-tesoreria', 'Tesoreria\RegistroPagoController@guardarAdjuntosTesoreria');
				Route::post('listarComprobantesPagos', 'Tesoreria\RegistroPagoController@listarComprobantesPagos')->name('listar-comprobante-pagos');
				Route::post('listarOrdenesCompra', 'Tesoreria\RegistroPagoController@listarOrdenesCompra')->name('listar-ordenes-compra');
				Route::post('listarRequerimientosPago', 'Tesoreria\RegistroPagoController@listarRequerimientosPago')->name('listar-requerimientos-pago');
				Route::post('procesarPago', 'Tesoreria\RegistroPagoController@procesarPago')->name('procesar-pagos');
				Route::get('listarPagos/{tp}/{id}', 'Tesoreria\RegistroPagoController@listarPagos')->name('listar-pagos');
				Route::get('listarPagosEnCuotas/{tp}/{id}', 'Tesoreria\RegistroPagoController@listarPagosEnCuotas')->name('listar-pagos-en-cuotas');
				// Route::get('pagosComprobante/{id}', 'Tesoreria\RegistroPagoController@pagosComprobante')->name('pagos-comprobante');
				// Route::get('pagosRequerimientos/{id}', 'Tesoreria\RegistroPagoController@pagosRequerimientos')->name('pagos-requerimientos');
				Route::get('cuentasOrigen/{id}', 'Tesoreria\RegistroPagoController@cuentasOrigen')->name('cuentas-origen');
				Route::get('anularPago/{id}', 'Tesoreria\RegistroPagoController@anularPago')->name('anular-pago');
				Route::post('enviarAPago', 'Tesoreria\RegistroPagoController@enviarAPago')->name('enviar-pago');
				Route::post('revertirEnvio', 'Tesoreria\RegistroPagoController@revertirEnvio')->name('revertir-envio');
				Route::get('verAdjuntos/{id}', 'Tesoreria\RegistroPagoController@verAdjuntos')->name('ver-adjuntos');
				Route::get('verAdjuntosRegistroPagoOrden/{id}', 'Tesoreria\RegistroPagoController@verAdjuntosRegistroPagoOrden')->name('ver-adjuntos-registro-pago-orden');
				Route::get('verAdjuntosRequerimientoDeOrden/{id}', 'Tesoreria\RegistroPagoController@verAdjuntosRequerimientoDeOrden')->name('ver-adjuntos-requerimiento-de-orden');
				Route::post('anular-adjunto-requerimiento-pago-tesoreria', 'Tesoreria\RegistroPagoController@anularAdjuntoTesoreria');
				Route::get('listar-archivos-adjuntos-pago/{id}', 'Logistica\RequerimientoController@listarArchivoAdjuntoPago');
				Route::get('lista-adjuntos-pago/{idRequerimientoPago}', 'Tesoreria\RegistroPagoController@listarAdjuntosPago');

				Route::get('verAdjuntosPago/{id}', 'Tesoreria\RegistroPagoController@verAdjuntosPago')->name('ver-adjuntos-pago');
				Route::get('actualizarEstadoPago', 'Tesoreria\RegistroPagoController@actualizarEstadoPago')->name('actualizar-estados-pago');

				Route::get('mostrar-requerimiento-pago/{idRequerimientoPago}', 'Tesoreria\RequerimientoPagoController@mostrarRequerimientoPago');

				Route::get('reistro-pagos-exportar-excel', 'Tesoreria\RegistroPagoController@registroPagosExportarExcel');
				Route::get('ordenes-compra-servicio-exportar-excel', 'Tesoreria\RegistroPagoController@ordenesCompraServicioExportarExcel');
				Route::get('listar-archivos-adjuntos-orden/{id_order}', 'OrdenController@listarArchivosOrder');

                #exportar excel con los fltros aplicados
                Route::post('exportar-requerimientos-pagos', 'Tesoreria\RegistroPagoController@exportarRequerimientosPagos')->name('exportar-requerimientos-pagos');
                #exportar excel con los fltros aplicados
                Route::post('exportar-ordeners-compras-servicios', 'Tesoreria\RegistroPagoController@exportarOrdenesComprasServicios')->name('exportar-ordeners-compras-servicios');
				// lista adjuntos pago
				// Route::get('adjuntos-pago/{id}', 'OrdenController@listarArchivosOrder');
			});

			Route::group(['as' => 'confirmacion-pagos.', 'prefix' => 'confirmacion-pagos'], function () {

				Route::get('index', 'Logistica\Distribucion\DistribucionController@view_confirmacionPago')->name('index');
				Route::post('listarRequerimientosPendientesPagos', 'Logistica\Distribucion\DistribucionController@listarRequerimientosPendientesPagos');
				Route::post('listarRequerimientosConfirmadosPagos', 'Logistica\Distribucion\DistribucionController@listarRequerimientosConfirmadosPagos');
				Route::post('pago_confirmado', 'Logistica\Distribucion\DistribucionController@pago_confirmado');
				Route::post('pago_no_confirmado', 'Logistica\Distribucion\DistribucionController@pago_no_confirmado');
				Route::get('verDetalleRequerimientoDI/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@verDetalleRequerimientoDI');
				Route::get('verRequerimientoAdjuntos/{id}', 'Logistica\Distribucion\DistribucionController@verRequerimientoAdjuntos');
			});
		});

		Route::group(['as' => 'facturacion.', 'prefix' => 'facturacion'], function () {

			Route::get('index', 'Tesoreria\Facturacion\PendientesFacturacionController@view_pendientes_facturacion')->name('index');
			Route::post('listarGuiasVentaPendientes', 'Tesoreria\Facturacion\PendientesFacturacionController@listarGuiasVentaPendientes')->name('listar-guias-pendientes');
			Route::post('listarRequerimientosPendientes', 'Tesoreria\Facturacion\PendientesFacturacionController@listarRequerimientosPendientes')->name('listar-requerimientos-pendientes');
			Route::post('guardar_doc_venta', 'Tesoreria\Facturacion\PendientesFacturacionController@guardar_doc_venta')->name('guardar-doc-venta');
			Route::get('documentos_ver/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@documentos_ver')->name('ver-doc-venta');
			Route::post('anular_doc_ven', 'Tesoreria\Facturacion\PendientesFacturacionController@anular_doc_ven')->name('anular-doc-venta');
			Route::get('obtenerGuiaVenta/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerGuiaVenta')->name('obtener-guia-venta');
			Route::post('obtenerGuiaVentaSeleccionadas', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerGuiaVentaSeleccionadas')->name('obtener-guias-ventas');
			Route::get('obtenerRequerimiento/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerRequerimiento')->name('obtener-requerimiento');
			Route::get('detalleFacturasGuias/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@detalleFacturasGuias')->name('detalle-facturas-guia');
			Route::get('detalleFacturasRequerimientos/{id}', 'Tesoreria\Facturacion\PendientesFacturacionController@detalleFacturasRequerimientos')->name('detalle-facturas-guia');
			Route::post('obtenerArchivosOc', 'Tesoreria\Facturacion\PendientesFacturacionController@obtenerArchivosOc')->name('obtener-archivos-oc');

			Route::get('autogenerarDocumentosCompra/{id}', 'Tesoreria\Facturacion\VentasInternasController@autogenerarDocumentosCompra')->name('autogenerarDocumentosCompra');
			Route::get('listado-ventas-internas-exportar-excel', 'Tesoreria\Facturacion\PendientesFacturacionController@listadoVentasInternasExportarExcel');
			Route::get('listado-ventas-externas-exportar-excel', 'Tesoreria\Facturacion\PendientesFacturacionController@listadoVentasExternasExportarExcel');

			Route::post('guardar-adjuntos-factura', 'Tesoreria\Facturacion\PendientesFacturacionController@guardarAdjuntosFactura');
			Route::get('ver-adjuntos', 'Tesoreria\Facturacion\PendientesFacturacionController@verAdjuntos');
			Route::post('eliminar-adjuntos', 'Tesoreria\Facturacion\PendientesFacturacionController@eliminarAdjuntos');
		});

		Route::group(['as' => 'comprobante-compra.', 'prefix' => 'comprobante-compra'], function () {
			Route::get('index', 'ContabilidadController@view_comprobante_compra');
			Route::get('ordenes_sin_facturar/{id_empresa}/{all_or_id_orden}', 'ContabilidadController@ordenes_sin_facturar');
			Route::post('guardar_comprobante_compra', 'ContabilidadController@guardar_comprobante_compra');
			Route::get('lista_comprobante_compra/{id_sede}/{all_or_id_doc_com}', 'ContabilidadController@lista_comprobante_compra');
		});

		Route::group(['as' => 'documento-compra.', 'prefix' => 'documento-compra'], function () {
			//Documento de compra
			Route::get('index', 'ComprobanteCompraController@view_crear_comprobante_compra')->name('index');
			// Route::post('mostrar_prods', 'Almacen\Catalogo\ProductoController@mostrar_prods');
			// Route::get('listarDevoluciones', 'Almacen\Movimiento\DevolucionController@listarDevoluciones');
		});

		Route::group(['as' => 'tipo-cambio.', 'prefix' => 'tipo-cambio'], function () {
			Route::get('index', 'Tesoreria\TipoCambioController@index')->name('index');
			Route::post('listar', 'Tesoreria\TipoCambioController@listar')->name('listar');
			Route::post('editar', 'Tesoreria\TipoCambioController@editar')->name('editar');
			Route::post('guardar', 'Tesoreria\TipoCambioController@guardar')->name('guardar');
		});

		Route::group(['as' => 'cierre-apertura.', 'prefix' => 'cierre-apertura'], function () {
			Route::get('index', 'Tesoreria\CierreAperturaController@index')->name('index');
			Route::post('listar', 'Tesoreria\CierreAperturaController@listar')->name('listar');
			Route::get('mostrarSedesPorEmpresa/{id}', 'Tesoreria\CierreAperturaController@mostrarSedesPorEmpresa')->name('mostrar-sedes-empresa');
			Route::get('mostrarAlmacenesPorSede/{id}', 'Tesoreria\CierreAperturaController@mostrarAlmacenesPorSede')->name('mostrar-almacenes-sede');
			Route::post('guardar', 'Tesoreria\CierreAperturaController@guardarAccion')->name('guardar');
			Route::post('guardarVarios', 'Tesoreria\CierreAperturaController@guardarVarios')->name('guardarVarios');
			Route::post('guardarCierreAnual', 'Tesoreria\CierreAperturaController@guardarCierreAnual')->name('guardarCierreAnual');
			Route::post('guardarCierreAnualOperativo', 'Tesoreria\CierreAperturaController@guardarCierreAnualOperativo')->name('guardarCierreAnualOperativo');
			Route::get('cargarMeses/{id}', 'Tesoreria\CierreAperturaController@cargarMeses');
			Route::get('listaHistorialAcciones/{id}', 'Tesoreria\CierreAperturaController@listaHistorialAcciones');
			Route::get('consultarPeriodo/{fec}/{id}', 'Tesoreria\CierreAperturaController@consultarPeriodo');
			Route::get('autogenerarPeriodos/{aaaa}', 'Tesoreria\CierreAperturaController@autogenerarPeriodos');
		});
	});

	Route::group(['as' => 'migracion.', 'prefix' => 'migracion'], function () {
		Route::get('index', 'Migraciones\MigracionAlmacenSoftLinkController@index')->name('index');
		Route::get('movimientos', 'Migraciones\MigracionAlmacenSoftLinkController@movimientos')->name('movimientos');
		Route::post('importar', 'Migraciones\MigracionAlmacenSoftLinkController@importar')->name('importar');

		Route::group(['as' => 'softlink.', 'prefix' => 'softlink'], function () {
			Route::get('index', 'Migraciones\MigracionAlmacenSoftLinkController@view_migracion_series')->name('index');
			Route::post('importar', 'Migraciones\MigracionAlmacenSoftLinkController@importarSeries')->name('importar');
			Route::get('exportar', 'Migraciones\MigracionAlmacenSoftLinkController@exportarSeries')->name('exportar');
			Route::get('test', 'Migraciones\MigracionAlmacenSoftLinkController@testSeries')->name('test');
			# actualizar productos al softlink
			Route::get('actualizar-productos', 'Migraciones\MigracionAlmacenSoftLinkController@view_actualizar_productos')->name('actualizar.productos.softlink');

			Route::get('descargar-modelo', 'Migraciones\MigracionAlmacenSoftLinkController@descargarModelo');
			Route::post('enviar-modelo-agil-softlink', 'Migraciones\MigracionAlmacenSoftLinkController@enviarModeloAgilSoftlink')->name('actualizar');
		});
	});

	Route::group(['as' => 'notificaciones.', 'prefix' => 'notificaciones'], function () {
		Route::get('index', 'Notificaciones\NotificacionController@index')->name('index');
		Route::get('ver/{id}', 'Notificaciones\NotificacionController@ver')->name('ver');
		Route::post('eliminar', 'Notificaciones\NotificacionController@eliminar')->name('eliminar');
		Route::post('lista-pendientes', 'Notificaciones\NotificacionController@listaPendientes')->name('lista-pendientes');
		Route::post('cantidad-no-leidas', 'Notificaciones\NotificacionController@cantidadNoLeidas')->name('cantidad-no-leidas');
	});

	Route::get('listarUsu', 'Almacen\Movimiento\TransferenciaController@listarUsu');

	Route::get('migrar_venta_directa/{id}', 'Migraciones\MigrateRequerimientoSoftLinkController@migrar_venta_directa');
	Route::get('migrar_orden_compra/{id}', 'Migraciones\MigrateOrdenSoftLinkController@migrarOrdenCompra');
	Route::get('prue/{id}', 'OrdenesPendientesController@prue');
	Route::get('anular_presup', 'ProyectosController@anular_presup');

	Route::group(['as' => 'configuracion.', 'prefix' => 'configuracion'], function () {

		Route::get('index', 'ConfiguracionController@view_main_configuracion')->name('index');
		Route::get('usuarios', 'ConfiguracionController@view_usuario')->name('listarUsuarios');
		Route::post('validar-documento', 'ConfiguracionController@validarDocumento');
		Route::post('validar-usuario', 'ConfiguracionController@validarUsuario');
		#asignar acceso a los usuarios
		// Route::get('configuracion/usuarios/accesos/{id}', 'ConfiguracionController@usuarioAcceso')->name('accesos');
		// Route::get('usuarios/get/usuario/{id}', 'ConfiguracionController@getUsuario')->name('usuario.accesos');


		Route::post('usuarios/asignar/modulos', 'ConfiguracionController@asiganrModulos');
		#----------------------
		Route::get('listar_usuarios', 'ConfiguracionController@mostrar_usuarios');
		Route::post('cambiar-clave', 'ConfiguracionController@cambiarClave');
		Route::post('guardar_usuarios', 'ConfiguracionController@guardar_usuarios');
		Route::get('listar_trabajadores', 'ProyectosController@listar_trabajadores');
		Route::get('anular_usuario/{id}', 'ConfiguracionController@anular_usuario');
		Route::get('lista-roles-usuario/{id}', 'ConfiguracionController@lista_roles_usuario');
		Route::get('arbol-acceso/{id_rol}', 'ConfiguracionController@arbol_modulos')->name('arbol-acceso');
		Route::put('actualizar-accesos-usuario', 'ConfiguracionController@actualizar_accesos_usuario');

		Route::get('usuarios/asignar', 'ConfiguracionController@usuarioAsignar');

		Route::get('modulos', 'ConfiguracionController@getModulos');

		Route::get('accesos/{id}', 'ConfiguracionController@viewAccesos')->name('accesos');
		// scripts a ejecutar
		Route::get('prueba', 'ConfiguracionController@prueba');
		Route::get('scripts/{var}', 'ConfiguracionController@scripts');
		Route::get('scripts-usuario', 'ConfiguracionController@scriptsAccesos');
		// ----fin de scripts
		Route::group(['as' => 'accesos.', 'prefix' => 'accesos'], function () {
			Route::post('get/modulos', 'ConfiguracionController@getModulosAccion');
			Route::post('guardar-accesos', 'ConfiguracionController@guardarAccesos');
			Route::get('accesos-usuario/{id}', 'ConfiguracionController@accesoUsuario');
		});


		Route::group(['as' => 'usuario.', 'prefix' => 'usuario'], function () {
			Route::get('password-user-decode/{id?}', 'ConfiguracionController@getPasswordUserDecode')->name('password-user-decode');
			Route::get('perfil/{id}', 'ConfiguracionController@getPerfil')->name('get-perfil');
			Route::post('perfil', 'ConfiguracionController@savePerfil')->name('save-perfil');

			// Route::get('usuario/{id}', 'ConfiguracionController@getUsuario')->name('usuario.accesos');
		});
	});

	// gerencial
	Route::group(['as' => 'gerencial.', 'prefix' => 'gerencial'], function () {
		Route::get('index', 'Gerencial\GerencialController@index')->name('index');

		Route::get('prueba', 'Gerencial\Cobranza\RegistroController@prueba')->name('prueba');

		Route::group(['as' => 'cobranza.', 'prefix' => 'cobranza'], function () {
			/**
			 * Módulo cobranzas
			 * */

			Route::get('index', 'Gerencial\Cobranza\CobranzaController@index')->name('index');
			Route::post('listar', 'Gerencial\Cobranza\CobranzaController@listar')->name('listar');
			Route::post('listar-clientes', 'Gerencial\Cobranza\CobranzaController@listarClientes')->name('listar-clientes');
			Route::post('buscar-registro', 'Gerencial\Cobranza\CobranzaController@buscarRegistro')->name('buscar-registro');
			Route::get('seleccionar-registro/{id_requerimiento}', 'Gerencial\Cobranza\CobranzaController@cargarDatosRequerimiento')->name('seleccionar-registro');
			Route::get('obtener-fases/{id}', 'Gerencial\Cobranza\CobranzaController@obtenerFase')->name('obtener-fases');
			Route::post('guardar-fase', 'Gerencial\Cobranza\CobranzaController@guardarFase')->name('guardar-fase');
			Route::post('eliminar-fase', 'Gerencial\Cobranza\CobranzaController@eliminarFase')->name('eliminar-fase');
			Route::get('obtener-observaciones/{id}', 'Gerencial\Cobranza\CobranzaController@obtenerObservaciones')->name('obtener-observaciones');
			Route::post('guardar-observaciones', 'Gerencial\Cobranza\CobranzaController@guardarObservaciones')->name('guardar-observaciones');
			Route::post('eliminar-observacion', 'Gerencial\Cobranza\CobranzaController@eliminarObservaciones')->name('eliminar-observacion');
			Route::post('guardar-registro-cobranza', 'Gerencial\Cobranza\CobranzaController@guardarRegistro')->name('guardar-registro-cobranza');
			Route::post('editar-registro', 'Gerencial\Cobranza\CobranzaController@editarRegistro')->name('editar-registro');
			Route::post('eliminar-registro-cobranza', 'Gerencial\Cobranza\CobranzaController@eliminarRegistro')->name('eliminar-registro-cobranza');
			Route::post('filtros-cobranzas', 'Gerencial\Cobranza\CobranzaController@filtros')->name('filtros-cobranzas');
			Route::post('obtener-penalidades', 'Gerencial\Cobranza\CobranzaController@obtenerPenalidades')->name('obtener-penalidades');
			Route::post('guardar-penalidad', 'Gerencial\Cobranza\CobranzaController@guardarPenalidad')->name('guardar-penalidad');
			Route::post('cambio-estado-penalidad', 'Gerencial\Cobranza\CobranzaController@cambioEstadoPenalidad')->name('cambio-estado-penalidad');
			Route::get('exportar-excel', 'Gerencial\Cobranza\CobranzaController@exportarExcel')->name('exportar-excel');

			/**
			 * Script para recuperar la info de Gerencia e Iniciar en las nuevas tablas
			 */
			Route::group(['as' => 'script.', 'prefix' => 'script'], function () {
				Route::get('script-periodo', 'Gerencial\Cobranza\CobranzaController@scriptPeriodos')->name('script-periodo');
				Route::get('script-fases-inicial', 'Gerencial\Cobranza\CobranzaController@scriptRegistroFase')->name('script-fases-inicial');
				Route::get('script-cobranza-fase', 'Gerencial\Cobranza\CobranzaController@scriptFases')->name('script-cobranza-fase');
                #pasa los contribuyentes a clientes
                Route::get('script-contribuyentes-clientes', 'Gerencial\Cobranza\CobranzaController@scriptContribuyenteCliente')->name('script-contribuyente-cliente');
                #generar codigo para los clientes
                Route::get('script-generar-codigo-clientes', 'Gerencial\Cobranza\CobranzaController@scriptGenerarCodigoCliente')->name('script-generar-codigo-clientes');
                #generar codigo para los clientes
                Route::get('script-generar-codigo-proveedores', 'Gerencial\Cobranza\CobranzaController@scriptGenerarCodigoProveedores')->name('script-generar-codigo-proveedores');
			});


			Route::get('cliente', 'Gerencial\Cobranza\ClienteController@cliente')->name('cliente');
			Route::get('crear-cliente', 'Gerencial\Cobranza\ClienteController@nuevoCliente')->name('nuevo.cliente');
			Route::post('clientes', 'Gerencial\Cobranza\ClienteController@listarCliente')->name('listar.cliente');
			Route::post('clientes/crear', 'Gerencial\Cobranza\ClienteController@crear')->name('clientes.crear');
			Route::post('buscar-cliente-documento', 'Gerencial\Cobranza\ClienteController@buscarClienteDocumento');

			Route::post('clientes/editar', 'Gerencial\Cobranza\ClienteController@editar')->name('clientes.editar');

			Route::get('cliente/ver/{id_contribuyente}', 'Gerencial\Cobranza\ClienteController@ver')->name('clientes.ver');
			Route::post('clientes/actualizar', 'Gerencial\Cobranza\ClienteController@actualizar')->name('clientes.actulizar');
			Route::post('clientes/eliminar', 'Gerencial\Cobranza\ClienteController@eliminar');
            Route::get('get-distrito/{id_provincia}', 'Gerencial\Cobranza\ClienteController@getDistrito')->name('get.distrito');
            Route::get('cliente/get-distrito/{id_provincia}', 'Gerencial\Cobranza\ClienteController@getDistrito');

            Route::get('cliente/{id_contribuyente}', 'Gerencial\Cobranza\ClienteController@editarContribuyente');

            Route::post('cliente/buscar-cliente-documento-editar', 'Gerencial\Cobranza\ClienteController@buscarClienteDocumentoEditar');

			// Route::post('listar-registros', 'Gerencial\Cobranza\RegistroController@listarRegistros');
			// Route::post('listar-clientes', 'Gerencial\Cobranza\RegistroController@listarClientes');
			Route::post('nuevo-cliente', 'Gerencial\Cobranza\RegistroController@nuevoCliente');

			Route::get('provincia/{id_departamento}', 'Gerencial\Cobranza\RegistroController@provincia');
			Route::get('cliente/provincia/{id_departamento}', 'Gerencial\Cobranza\RegistroController@provincia');
			Route::get('distrito/{id_provincia}', 'Gerencial\Cobranza\RegistroController@distrito');
			Route::get('cliente/distrito/{id_provincia}', 'Gerencial\Cobranza\RegistroController@distrito');

			Route::get('get-cliente/{id_cliente}', 'Gerencial\Cobranza\RegistroController@getCliente');
			Route::get('buscar-factura/{factura}', 'Gerencial\Cobranza\RegistroController@getFactura');
			// Route::get('buscar-registro/{input}/{tipo}', 'Gerencial\Cobranza\RegistroController@getRegistro');
			// Route::get('seleccionar-registro/{id_requerimiento}', 'Gerencial\Cobranza\RegistroController@selecconarRequerimiento');
			// registro de cobranza
			// Route::post('guardar-registro-cobranza', 'Gerencial\Cobranza\RegistroController@guardarRegistroCobranza');
			Route::get('actualizar-ven-doc-req', 'Gerencial\Cobranza\RegistroController@actualizarDocVentReq');
			Route::post('editar-cliente', 'Gerencial\Cobranza\RegistroController@editarCliente');
			// Route::group(['as' => 'cliente.', 'prefix' => 'cliente'], function () {
			// });
			// Route::group(['as' => 'registro.', 'prefix' => 'registro'], function () {

			// });
			#script 1
			Route::get('script-cliente-ruc', 'Gerencial\Cobranza\RegistroController@scriptClienteRuc');
			#script 2
			Route::get('script-cliente', 'Gerencial\Cobranza\RegistroController@scriptCliente');
			#script 3
			Route::get('script-empresa', 'Gerencial\Cobranza\RegistroController@scriptEmpresa');
			#script 4
			Route::get('script-fase', 'Gerencial\Cobranza\RegistroController@scriptFase');
			#script 5
			Route::get('script-conbranza', 'Gerencial\Cobranza\RegistroController@scriptCobranza');
			#script 6
			Route::get('script-empresa-unicos', 'Gerencial\Cobranza\RegistroController@scriptEmpresaUnicos');
			#scrip 7
			Route::get('script-match-cobranza-penalidad', 'Gerencial\Cobranza\RegistroController@scriptMatchCobranzaPenalidad');
			#scrip 8
			Route::get('script-match-cobranza-vendedor', 'Gerencial\Cobranza\RegistroController@scriptMatchCobranzaVendedor');
			#scrip 9
			Route::get('script-empresa-actualizacion', 'Gerencial\Cobranza\RegistroController@scriptEmpresaActualizacion');
            #scrip 10
			Route::get('script-vendedor', 'Gerencial\Cobranza\RegistroController@scriptVendedor');
            #scrip 11
			Route::get('script-cliente-unificar', 'Gerencial\Cobranza\RegistroController@scriptClienteUnificar');
            // -----
            #scrip 12 empresas
			Route::get('script-empresa-remplazar-adm-cliente', 'Gerencial\Cobranza\RegistroController@scriptEmpresaRemplazarAdmCliente');
            #scrip 13 revierte el script 12
			Route::get('script-empresa-remplazar-adm-cliente-revertir', 'Gerencial\Cobranza\RegistroController@scriptEmpresaRemplazarAdmClienteRevertir');
            #scrip 14 clientes
			Route::get('script-cliente-remplazar-com-cliente', 'Gerencial\Cobranza\RegistroController@scriptClienteRemplazarComCliente');
            #scrip 15 revierte el script 14
			Route::get('script-cliente-remplazar-com-cliente-revertir', 'Gerencial\Cobranza\RegistroController@scriptClienteRemplazarComClienteRevertir');
            #scrip 16 empresas para los de estado 0
			Route::get('script-empresa-remplazar-adm-cliente-estadp-cero', 'Gerencial\Cobranza\RegistroController@scriptEmpresaRemplazarAdmClienteEstadoCero');
            #scrip 17 clientes
			Route::get('script-cliente-remplazar-com-cliente-estado-cero', 'Gerencial\Cobranza\RegistroController@scriptClienteRemplazarComClienteEstadoCero');
            #scrip 18 clientes en vacio los nuevo generados
			Route::get('script-cliente-nuevos-ingresado', 'Gerencial\Cobranza\RegistroController@scriptClienteNuevosIngresados');
            #scrip 19 clientes en vacio de la vista
            Route::get('script-cliente-vista-null', 'Gerencial\Cobranza\RegistroController@scriptClienteVistaNull');
            Route::get('script-contribuyente-vista-null', 'Gerencial\Cobranza\RegistroController@scriptClienteContribuyenteVistaNull');
            // ------------------
            #script 20 verificar los clientes con los antiguos registros de cobranza
            Route::get('script-cliente-agil-gerencial', 'Gerencial\Cobranza\RegistroController@scriptClienteAgilGerencial');
            #scrip para pasar el registro de cobranzas
            Route::get('script-observaciones-oc', 'Gerencial\Cobranza\RegistroController@scriptObservacionesOC');

			// Route::get('editar-registro/{id}', 'Gerencial\Cobranza\RegistroController@editarRegistro');
			Route::post('modificar-registro', 'Gerencial\Cobranza\RegistroController@modificarRegistro');
			// Route::post('guardar-fase', 'Gerencial\Cobranza\RegistroController@guardarFase');
			// Route::get('obtener-fase/{id}', 'Gerencial\Cobranza\RegistroController@obtenerFase');
			// Route::post('eliminar-fase', 'Gerencial\Cobranza\RegistroController@eliminarFase');
			// Route::post('guardar-penalidad', 'Gerencial\Cobranza\RegistroController@guardarPenalidad');
			// Route::post('obtener-penalidades', 'Gerencial\Cobranza\RegistroController@obtenerPenalidades');
			Route::post('buscar-vendedor', 'Gerencial\Cobranza\RegistroController@buscarVendedor');
			// Route::get('eliminar-registro-cobranza/{id_registro_cobranza}', 'Gerencial\Cobranza\RegistroController@eliminarRegistroCobranza');
			// Route::group(['as' => 'cliente.', 'prefix' => 'cliente'], function () {
			Route::get('buscar-cliente-seleccionado/{id}', 'Gerencial\Cobranza\RegistroController@buscarClienteSeleccionado');
			#exportar excel
			// Route::get('exportar-excel/{request}', 'Gerencial\Cobranza\RegistroController@exportarExcel');
			Route::post('exportar-excel-prueba', 'Gerencial\Cobranza\RegistroController@exportarExcelPrueba');
            // editar penalidad
			Route::get('editar-penalidad/{id}', 'Gerencial\Cobranza\RegistroController@editarPenalidad');
			// Route::post('anular-penalidad', 'Gerencial\Cobranza\RegistroController@anularPenalidad');
			Route::post('eliminar-penalidad', 'Gerencial\Cobranza\RegistroController@eliminarPenalidad');
            // observaciones
			// Route::post('obtener-observaciones', 'Gerencial\Cobranza\RegistroController@obtenerObservaciones');
			// Route::post('guardar-observaciones', 'Gerencial\Cobranza\RegistroController@guardarObservaciones');
			// Route::post('eliminar-observacion', 'Gerencial\Cobranza\RegistroController@eliminarObservaciones');

            Route::get('exportar-excel-power-bi/{request}', 'Gerencial\Cobranza\RegistroController@exportarExcelPowerBI');
			// Route::post('cambio-estado-penalidad', 'Gerencial\Cobranza\RegistroController@cambioEstadoPenalidad');

			// Fondos, Auspicios y Rebates
			Route::group(['as' => 'fondos.', 'prefix' => 'fondos'], function () {
				Route::get('index', 'Gerencial\Cobranza\CobranzaFondoController@index')->name('index');
				Route::post('listar', 'Gerencial\Cobranza\CobranzaFondoController@lista')->name('listar');
				Route::post('guardar', 'Gerencial\Cobranza\CobranzaFondoController@guardar')->name('guardar');
				Route::post('eliminar', 'Gerencial\Cobranza\CobranzaFondoController@eliminar')->name('eliminar');
				Route::post('cargar-cobro', 'Gerencial\Cobranza\CobranzaFondoController@cargarCobro')->name('cargar-cobro');
				Route::post('guardar-cobro', 'Gerencial\Cobranza\CobranzaFondoController@guardarCobro')->name('guardar-cobro');
				Route::get('exportar-excel', 'Gerencial\Cobranza\CobranzaFondoController@exportarExcel')->name('exportar-excel');
			});

			Route::group(['as' => 'devoluciones.', 'prefix' => 'devoluciones'], function () {
				Route::get('index', 'Gerencial\Cobranza\DevolucionPenalidadController@index')->name('index');
				Route::post('listar', 'Gerencial\Cobranza\DevolucionPenalidadController@lista')->name('listar');
				Route::post('guardar', 'Gerencial\Cobranza\DevolucionPenalidadController@guardar')->name('guardar');
				Route::post('guardar-pagador', 'Gerencial\Cobranza\DevolucionPenalidadController@guardarPagador')->name('guardar-pagador');
				Route::post('cargar-cobro-dev', 'Gerencial\Cobranza\DevolucionPenalidadController@cargarCobroDev')->name('cargar-cobro-dev');
				Route::post('eliminar', 'Gerencial\Cobranza\DevolucionPenalidadController@eliminar')->name('eliminar');
				Route::get('exportar-excel', 'Gerencial\Cobranza\DevolucionPenalidadController@exportarExcel')->name('exportar-excel');
			});
		});

		Route::group(['as' => 'test.', 'prefix' => 'test'], function () {
			Route::get('carga-cobranza', 'Gerencial\Cobranza\RegistroController@cargarCobranzaNuevo')->name('carga-cobranza');
			Route::get('carga-orden', 'Gerencial\Cobranza\RegistroController@cargarOrdenNuevo')->name('carga-cobranza');
			Route::get('limpiar-codigo-oc', 'Gerencial\Cobranza\RegistroController@limpiarCodigoOrden')->name('limpiar-codigo-oc');
			Route::get('carga-id-oc', 'Gerencial\Cobranza\RegistroController@cargarOrdenesId')->name('carga-id-oc');
			Route::get('carga-ordenes-faltantes/{tipo}', 'Gerencial\Cobranza\RegistroController@cargarOrdenesFaltantes')->name('carga-ordenes-faltantes');
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

	Route::get('migrarOrdenVenta/{id}', 'Migraciones\MigrateRequerimientoSoftLinkController@migrarOCC');
	Route::get('correlativo', 'Migraciones\MigrateOrdenSoftLinkController@correlativo');

	// Route::get('generarDespachoInternoMgcp/{id}', 'Logistica\Distribucion\OrdenesTransformacionController@generarDespachoInternoMgcp');
	Route::get('soft_tipos_cambio', 'Migraciones\MigrateRequerimientoSoftLinkController@soft_tipos_cambio');
	Route::get('migrarOrdenCompra/{id}', 'Migraciones\MigrateOrdenSoftLinkController@migrarOrdenCompra');
	Route::get('validaNegativosHistoricoKardex/{idp}/{ida}/{an}', 'Almacen\Movimiento\ValidaMovimientosController@validaNegativosHistoricoKardex');



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
	// Route::get('logistica/mostrar_items', 'LogisticaController@mostrar_items');
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
	// Route::get('detalle_cotizacion/{id}', 'LogisticaController@detalle_cotizacion');
	// Route::post('guardar_orden_compra', 'LogisticaController@guardar_orden_compra');
	// Route::post('update_orden_compra', 'LogisticaController@update_orden_compra');
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

    Route::group(['as' => 'scripts.', 'prefix' => 'scripts'], function () {
        Route::get('usuarios', 'ScriptController@usuarios');
        Route::get('empresas', 'ScriptController@empresas');
        Route::get('sedes', 'ScriptController@sedes');
        Route::get('grupos', 'ScriptController@grupos');
    });

});

Route::group(['as' => 'power-bi.', 'prefix' => 'power-bi'], function () {
	Route::get('ventas', function () {
		return view('power-bi/ventas');
	})->name('ventas');
	Route::get('cobranzas', function () {
		return view('power-bi/cobranzas');
	})->name('cobranzas');
	Route::get('inventario', function () {
		return view('power-bi/inventario');
	})->name('inventario');
});
