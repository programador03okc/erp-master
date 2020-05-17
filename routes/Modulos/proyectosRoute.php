<?php

use Illuminate\Http\Request;


Route::get('/proyectos/iu', 'ProyectosController@mostrar_ius');
Route::get('/proyectos/iu/{id}', 'ProyectosController@mostrar_iu');
Route::post('/proyectos/iu', 'ProyectosController@guardar_iu');
Route::put('/proyectos/iu/{id}', 'ProyectosController@update_iu');
Route::delete('/proyectos/iu/{id}', 'ProyectosController@delete_iu');

Route::get('/proyectos/tp_insumo', 'ProyectosController@mostrar_tipos_insumos');
Route::get('/proyectos/tp_insumo/{id}', 'ProyectosController@mostrar_tp_insumo');
Route::post('/proyectos/tp_insumo', 'ProyectosController@guardar_tp_insumo');
Route::put('/proyectos/tp_insumo/{id}', 'ProyectosController@update_tp_insumo');
Route::put('/proyectos/tp_insumo_anular/{id}', 'ProyectosController@anular_tp_insumo');

Route::get('/proyectos/sistemas', 'ProyectosController@mostrar_sis_contratos');
Route::get('/proyectos/sistemas/{id}', 'ProyectosController@mostrar_sis_contrato');
Route::post('/proyectos/sistemas', 'ProyectosController@guardar_sis_contrato');
Route::put('/proyectos/sistemas/{id}', 'ProyectosController@update_sis_contrato');
Route::put('/proyectos/sistemas_anular/{id}', 'ProyectosController@anular_sis_contrato');

Route::get('/proyectos/insumo', 'ProyectosController@mostrar_insumos');
Route::get('/proyectos/insumos_lista', 'ProyectosController@mostrar_insumos_lista');
Route::get('/proyectos/insumo/{id}', 'ProyectosController@mostrar_insumo');
Route::post('/proyectos/insumo', 'ProyectosController@guardar_insumo');
Route::put('/proyectos/insumo/{id}', 'ProyectosController@update_insumo');
Route::delete('/proyectos/insumo/{id}', 'ProyectosController@delete_insumo');
Route::put('/proyectos/insumo/anular/{id}', 'ProyectosController@anular_insumo');
Route::get('/proyectos/insumo/iu/{id}', 'ProyectosController@buscar_iu');
Route::get('/proyectos/insumo/tp_insumo/{id}', 'ProyectosController@buscar_tp_insumo');

Route::get('/proyectos/acu', 'ProyectosController@mostrar_acus');
Route::get('/proyectos/acu/{id}', 'ProyectosController@mostrar_acu');
Route::get('/proyectos/acu_todo/{id}', 'ProyectosController@mostrar_acu_todo');
Route::get('/proyectos/acu_completo/', 'ProyectosController@mostrar_acu_completo');
Route::post('/proyectos/acu', 'ProyectosController@guardar_acu');
Route::put('/proyectos/acu/{id}', 'ProyectosController@update_acu');
Route::put('/proyectos/acu_anular/{id}', 'ProyectosController@anular_acu');
// Route::delete('/proyectos/acu/{id}', 'ProyectosController@delete_acu');

Route::get('/proyectos/acu_exist/', 'ProyectosController@exist_acu_detalle');


Route::get('/proyectos/acu_detalle/{id}', 'ProyectosController@mostrar_acu_detalle');
Route::post('/proyectos/acu_detalle', 'ProyectosController@guardar_acu_detalle');
Route::put('/proyectos/acu_detalle/{id}', 'ProyectosController@update_acu_detalle');
Route::delete('/proyectos/acu_detalle/{id}', 'ProyectosController@delete_acu_detalle');

Route::get('/proyectos/proyectos_pend', 'ProyectosController@mostrar_proyectos_pendientes');
Route::get('/proyectos/proyectos', 'ProyectosController@mostrar_proyectos');
Route::get('/proyectos/proyectos_contratos', 'ProyectosController@mostrar_proyectos_contratos');
Route::get('/proyectos/proy_contratos', 'ProyectosController@mostrar_proy_contratos');
Route::get('/proyectos/proyecto/{id}', 'ProyectosController@mostrar_proyecto');
Route::post('/proyectos/proyecto', 'ProyectosController@guardar_proyecto');
Route::put('/proyectos/proyecto/{id}', 'ProyectosController@update_proyecto');
// Route::delete('/proyectos/proyecto/{id}', 'ProyectosController@delete_proyecto');
Route::put('/proyectos/proyecto/anular/{id}', 'ProyectosController@anular_proyecto');
Route::put('/proyectos/proyecto/cambiar_estado/{id}/{estado}', 'ProyectosController@estado_proyecto');
Route::get('/proyectos/proyecto_pendientes/{emp}/{rol}', 'ProyectosController@mostrar_proyectos_pendientes');
Route::get('/proyectos/proyecto_aprobacion/{id}', 'ProyectosController@aprobacion_completa');
Route::post('/proyectos/aprobacion', 'ProyectosController@guardar_aprobacion');

Route::get('/proyectos/opciones', 'ProyectosController@mostrar_opciones');
Route::get('/proyectos/opcion/{id}', 'ProyectosController@mostrar_opcion');
Route::post('/proyectos/opcion', 'ProyectosController@guardar_opcion');
Route::put('/proyectos/opcion/{id}', 'ProyectosController@update_opcion');
Route::put('/proyectos/opcion_anular/{id}', 'ProyectosController@anular_opcion');

Route::get('/proyectos/lecciones/{id}', 'ProyectosController@mostrar_lecciones');
Route::post('/proyectos/leccion', 'ProyectosController@guardar_leccion');
Route::put('/proyectos/leccion/{id}', 'ProyectosController@update_leccion');

Route::get('/proyectos/presupuestos_cabecera', 'ProyectosController@mostrar_presupuestos_cabecera');
Route::get('/proyectos/presupuesto_cabecera/{id}', 'ProyectosController@mostrar_presupuesto_cabecera');
Route::get('/proyectos/presupuesto_cabecera2/{id}', 'ProyectosController@mostrar_presupuesto_cabecera2');
Route::get('/proyectos/presupuesto_pres_acu/{id}', 'ProyectosController@mostrar_pres_acu');

Route::get('/proyectos/presupuestos/{tp}', 'ProyectosController@mostrar_presupuestos');
Route::get('/proyectos/presupuestos_eje', 'ProyectosController@mostrar_presup_ejecucion');
Route::get('/proyectos/presupuesto/{id}', 'ProyectosController@mostrar_presupuesto');
Route::get('/proyectos/presupuesto_todo/{id}', 'ProyectosController@mostrar_todo_presint');
Route::get('/proyectos/propuesta_todo/{id}', 'ProyectosController@mostrar_todo_propuesta');
Route::get('/proyectos/presupuesto_completo/{id}', 'ProyectosController@mostrar_presupuesto_completo');
Route::get('/proyectos/pres_completo/{id}', 'ProyectosController@mostrar_pres_completo');
Route::post('/proyectos/presupuesto', 'ProyectosController@guardar_presupuesto');
Route::put('/proyectos/presupuesto/{id}', 'ProyectosController@update_presupuesto');
Route::get('/proyectos/componentes_cd/{id}', 'ProyectosController@getComponentesCDByPresupuesto');
Route::get('/proyectos/componentes_ci/{id}', 'ProyectosController@getComponentesCIByPresupuesto');
Route::get('/proyectos/componentes_gg/{id}', 'ProyectosController@getComponentesGGByPresupuesto');
Route::get('/proyectos/partidas_cd/{id}', 'ProyectosController@getPartidasCDByPresupuesto');
Route::get('/proyectos/partidas_presupuesto/{id}', 'ProyectosController@getPartidas_Presupuesto');
Route::get('/proyectos/partidas_ci/{id}', 'ProyectosController@getPartidasCIByPresupuesto');
Route::get('/proyectos/partidas_gg/{id}', 'ProyectosController@getPartidasGGByPresupuesto');
Route::get('/proyectos/presup_acu/{id}', 'ProyectosController@mostrar_presupuestos_acu');
Route::get('/proyectos/partida_obs/{id}/{tp}', 'ProyectosController@obsPartida');
Route::get('/proyectos/lecciones_acu/{id}', 'ProyectosController@mostrar_lecciones_acu');

Route::get('/proyectos/prueba/{id}', 'ProyectosController@mostrar_prueba');

Route::get('/proyectos/presup_sum_insumos/{id}', 'ProyectosController@getSumaTipoInsumos');
Route::get('/proyectos/presup_insumos/{id}', 'ProyectosController@getInsumos');
Route::get('/proyectos/presup_insumos_tp/{id}', 'ProyectosController@getInsumosByTipo');
Route::get('/proyectos/presup_seguimiento_cd/{id}/{cd}', 'ProyectosController@mostrar_seguimiento_cd');
Route::get('/proyectos/presup_seguimiento_ci/{id}/{ci}', 'ProyectosController@mostrar_seguimiento_ci');
Route::get('/proyectos/presup_seguimiento_gg/{id}/{gg}', 'ProyectosController@mostrar_seguimiento_gg');

Route::post('/proyectos/presup_uti', 'ProyectosController@guardar_pres_uti');
Route::get('/proyectos/presup_uti/{id}', 'ProyectosController@mostrar_uti');
Route::put('/proyectos/presup_uti/{id}', 'ProyectosController@update_pres_uti');

Route::post('/proyectos/cronograma', 'ProyectosController@guardar_cronograma');
Route::put('/proyectos/cronograma/{id}', 'ProyectosController@update_cronograma');
Route::get('/proyectos/cronograma_completo/{id}', 'ProyectosController@mostrar_cronograma_completo');
Route::get('/proyectos/next_cronograma/{id}/{emp}/{fecha}', 'ProyectosController@nextCronograma');
Route::get('/proyectos/cronogramas/{tipo}', 'ProyectosController@mostrar_cronogramas');
Route::get('/proyectos/cronograma/{id}', 'ProyectosController@mostrar_cronograma');
Route::get('/proyectos/crono_partida_cd/{id}', 'ProyectosController@getPartidasCDByCronograma');
Route::get('/proyectos/crono_partida_ci/{id}', 'ProyectosController@getPartidasCIByCronograma');
Route::get('/proyectos/crono_partida_gg/{id}', 'ProyectosController@getPartidasGGByCronograma');

Route::post('/proyectos/desembolso', 'ProyectosController@guardar_desembolso');
Route::get('/proyectos/desembolsos', 'ProyectosController@mostrar_desembolsos');

Route::get('/proyectos/pres_valorizacion/{id}', 'ProyectosController@mostrar_pres_valorizacion');
Route::post('/proyectos/valorizacion', 'ProyectosController@guardar_valorizacion');


Route::get('/proyectos/tp_contrato', 'ProyectosController@mostrar_tipos_contrato');
Route::get('/proyectos/tp_proyecto', 'ProyectosController@mostrar_tipos_proyecto');
Route::get('/proyectos/modalidades', 'ProyectosController@mostrar_modalidad');
Route::get('/proyectos/tp_insumo', 'ProyectosController@mostrar_tipos_insumos');
Route::get('/proyectos/tp_presupuesto', 'ProyectosController@mostrar_tp_presupuesto');
Route::get('/proyectos/unid_program', 'ProyectosController@mostrar_unid_program');
Route::get('/proyectos/unid_program/{id}', 'ProyectosController@mostrar_unid_programById');

Route::get('/proyectos/nextPres/{id}/{emp}/{fecha}', 'ProyectosController@nextPresupuesto');

Route::post('/proyectos/residente', 'ProyectosController@guardar_residente');
Route::put('/proyectos/residente/{id}', 'ProyectosController@update_residente');
Route::put('/proyectos/residente_anular/{id}', 'ProyectosController@anular_residente');
Route::get('/proyectos/residentes', 'ProyectosController@mostrar_residentes');
Route::get('/proyectos/residente/{id}', 'ProyectosController@mostrar_residente');

Route::post('/proyectos/portafolio', 'ProyectosController@guardar_portafolio');
Route::put('/proyectos/portafolio/{id}', 'ProyectosController@update_portafolio');
Route::put('/proyectos/portafolio_anular/{id}', 'ProyectosController@anular_portafolio');
Route::get('/proyectos/portafolios', 'ProyectosController@mostrar_portafolios');
Route::get('/proyectos/portafolio/{id}', 'ProyectosController@mostrar_portafolio');

Route::get('/sistema/moneda', 'ProyectosController@mostrar_moneda');
Route::get('/logistica/clientes', 'ProyectosController@mostrar_clientes');
Route::get('/logistica/cliente/{id}', 'ProyectosController@mostrar_cliente');
Route::get('/contabilidad/impuesto/{cod}/{fecha}', 'ProyectosController@mostrar_impuesto');
