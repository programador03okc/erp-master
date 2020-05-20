<?php

use Illuminate\Http\Request;

/* Ruta PDF */
// Route::get('/pdf', function(){
//     $pdf = PDF::loadView('vista_pdf');
//     return $pdf->download('archivo.pdf');
// });

Route::get('/rrhh/traer_personas/{id}', 'ReportesController@traer_personas'); // html
Route::get('/rrhh/traer_permisos/{id}', 'ReportesController@traer_permisos'); // html
Route::get('/rrhh/reporte_personas/{id}', 'ReportesController@generar_personas_pdf'); // PDF
Route::get('/rrhh/reporte_papeleta/{id}', 'ReportesController@generar_permiso_pdf'); // PDF
Route::get('/reporte_personas_excel', 'ReportesController@generar_personas_excel');
// Route::get('/prueba_sesion', 'RecursosHumanosController@prueba_session');
// Route::get('/comprobar_sesion', 'RecursosHumanosController@comprobar_session');