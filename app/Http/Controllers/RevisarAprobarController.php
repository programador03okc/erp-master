<?php

namespace App\Http\Controllers;

use App\Models\Administracion\Aprobacion;
use App\Models\Administracion\Division;
use App\Models\Administracion\DivisionArea;
use App\Models\Administracion\Documento;
use App\Models\Administracion\Flujo;
use App\Models\Administracion\Operacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;


class RevisarAprobarController extends Controller{

    function viewListaRequerimientoPagoPendienteParaAprobacion(){
        $gruposUsuario = Auth::user()->getAllGrupo();

        return view('necesidades/revisar_aprobar/lista',compact('gruposUsuario'));
    }

    
    public function mostrarListaDeDocumentosPendientes(Request $request)
    // public function mostrarListaDeDocumentosPendientes()
    {

        $idUsuarioAprobante = Auth::user()->id_usuario;
        $allGrupo = Auth::user()->getAllGrupo();
        $idGrupoList = [];
        foreach ($allGrupo as $grupo) {
            $idGrupoList[] = $grupo->id_grupo; // lista de id_rol del usuario en sesion
        }

        $allRol = Auth::user()->getAllRol();
        $idRolUsuarioList = [];
        foreach ($allRol as  $rol) {
            $idRolUsuarioList[] = $rol->id_rol;
        }

        $divisiones = DivisionArea::mostrar();
        $idDivisionList = [];
        foreach ($divisiones as $value) {
            $idDivisionList[] = $value->id_division; //lista de id del total de divisiones 
        }

        $divisionUsuarioNroOrdenUno = Division::mostrarDivisionUsuarioNroOrdenUno();
        $idDivisionUsuarioList = [];
        foreach ($divisionUsuarioNroOrdenUno as $value) {
            $idDivisionUsuarioList[] = $value->id_division; //lista de id_division al que pertenece el usuario 
        }


        // $idEmpresa = $request->idEmpresa;
        // $idSede = $request->idSede;
        // $idGrupo = $request->idGrupo;
        // $idPrioridad = $request->idPrioridad;
  
        
        $documentoTipoRequerimientoBienesYServicios =Documento::join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'adm_documentos_aprob.id_doc')
        ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'adm_documentos_aprob.id_tp_documento')
        ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
        ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contrib_empresa', 'adm_empresa.id_contribuyente', '=', 'contrib_empresa.id_contribuyente')
        ->leftJoin('administracion.division', 'division.id_division', '=', 'alm_req.division_id')
        ->leftJoin('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'alm_req.id_grupo')
        ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
        ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
        ->select(
            'adm_documentos_aprob.*',
            'alm_tp_req.descripcion AS tipo_requerimiento',
            'adm_tp_docum.descripcion as tipo_documento_descripcion',
            'alm_req.*',
            'alm_req.estado as id_estado',
            'sis_moneda.simbolo as moneda_simbolo',
            'sis_moneda.descripcion as moneda_descripcion',
            'adm_prioridad.descripcion as prioridad_descripcion',
            'contrib_empresa.razon_social as empresa_razon_social',
            'sis_sede.codigo as sede_descripcion',
            'sis_grupo.descripcion as grupo_descripcion',
            'division.descripcion as division_descripcion',
            'sis_usua.nombre_corto as usuario_nombre_corto',
            'adm_estado_doc.estado_doc as estado_descripcion',
            'adm_estado_doc.bootstrap_color',
            DB::raw("(SELECT SUM(alm_det_req.cantidad * alm_det_req.precio_unitario) 
            FROM almacen.alm_det_req 
            WHERE   alm_det_req.id_requerimiento = alm_req.id_requerimiento AND
            alm_det_req.estado != 7) AS monto_total")
        )
        ->where([['adm_documentos_aprob.id_tp_documento',1]]) //documento => requerimiento de B/S
        ->whereIn('alm_req.estado',[1,12]) // elaborado, pendiente aprobación
        // ->when((intval($idEmpresa) > 0), function ($query)  use ($idEmpresa) {
        //     return $query->whereRaw('requerimiento_pago.id_empresa = ' . $idEmpresa);
        // })
        // ->when((intval($idSede) > 0), function ($query)  use ($idSede) {
        //     return $query->whereRaw('requerimiento_pago.id_sede = ' . $idSede);
        // })
        // ->when((intval($idGrupo) > 0), function ($query)  use ($idGrupo) {
        //     return $query->whereRaw('requerimiento_pago.id_grupo = ' . $idGrupo);
        // })
        // ->when((intval($idPrioridad) > 0), function ($query)  use ($idPrioridad) {
        //     return $query->whereRaw('requerimiento_pago.id_prioridad = ' . $idPrioridad);
        // })
        ->get();
        
        $documentoTipoRequerimientoPago =Documento::join('tesoreria.requerimiento_pago', 'requerimiento_pago.id_requerimiento_pago', '=', 'adm_documentos_aprob.id_doc')
        ->leftJoin('tesoreria.requerimiento_pago_tipo', 'requerimiento_pago.id_requerimiento_pago_tipo', '=', 'requerimiento_pago_tipo.id_requerimiento_pago_tipo')
        ->leftJoin('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'adm_documentos_aprob.id_tp_documento')
        ->leftJoin('administracion.adm_prioridad', 'requerimiento_pago.id_prioridad', '=', 'adm_prioridad.id_prioridad')
        ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'requerimiento_pago.id_sede')
        ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
        ->leftJoin('contabilidad.adm_contri as contrib_empresa', 'adm_empresa.id_contribuyente', '=', 'contrib_empresa.id_contribuyente')
        ->leftJoin('administracion.division', 'division.id_division', '=', 'requerimiento_pago.id_division')
        ->leftJoin('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'requerimiento_pago.id_grupo')
        ->leftJoin('configuracion.sis_usua', 'requerimiento_pago.id_usuario', '=', 'sis_usua.id_usuario')
        ->leftJoin('configuracion.sis_moneda', 'requerimiento_pago.id_moneda', '=', 'sis_moneda.id_moneda')
        ->leftJoin('tesoreria.requerimiento_pago_estado', 'requerimiento_pago.id_estado', '=', 'requerimiento_pago_estado.id_requerimiento_pago_estado')
        ->select(
            'adm_documentos_aprob.*',
            'requerimiento_pago_tipo.descripcion AS tipo_requerimiento',
            'adm_tp_docum.descripcion as tipo_documento_descripcion',
            'requerimiento_pago.*',
            'sis_moneda.descripcion as moneda_descripcion',
            'sis_moneda.simbolo as moneda_simbolo',
            'adm_prioridad.descripcion as prioridad_descripcion',
            'contrib_empresa.razon_social as empresa_razon_social',
            'sis_sede.codigo as sede_descripcion',
            'sis_grupo.descripcion as grupo_descripcion',
            'division.descripcion as division_descripcion',
            'sis_usua.nombre_corto as usuario_nombre_corto',
            'requerimiento_pago_estado.descripcion as estado_descripcion',
            'requerimiento_pago_estado.bootstrap_color',
        )
        ->where([['adm_documentos_aprob.id_tp_documento',11]]) // documento => requerimiento de pago
        ->whereIn('requerimiento_pago.id_estado',[1,4]) // elaborado, pendiente aprobación
        // ->when((intval($idEmpresa) > 0), function ($query)  use ($idEmpresa) {
        //     return $query->whereRaw('alm_req.id_empresa = ' . $idEmpresa);
        // })
        // ->when((intval($idSede) > 0), function ($query)  use ($idSede) {
        //     return $query->whereRaw('alm_req.id_sede = ' . $idSede);
        // })
        // ->when((intval($idGrupo) > 0), function ($query)  use ($idGrupo) {
        //     return $query->whereRaw('alm_req.id_grupo = ' . $idGrupo);
        // })
        // ->when((intval($idPrioridad) > 0), function ($query)  use ($idPrioridad) {
        //     return $query->whereRaw('alm_req.id_prioridad = ' . $idPrioridad);
        // })
        ->get();
        

        $documentosEnUnaLista = $documentoTipoRequerimientoBienesYServicios->merge($documentoTipoRequerimientoPago);

        
        $todosLosDocumentos = array_reverse(array_sort($documentosEnUnaLista, function ($value) {
            return $value['adm_documentos_aprob.id_doc_aprob'];
        }));
        
        
    
        $payload = [];
        $mensaje=[];

        $pendiente_aprobacion = [];

        foreach ($todosLosDocumentos as $element) {
            if (in_array($element->id_grupo, $idGrupoList) == true) {
                $idDocumento = $element->id_doc_aprob;
                $tipoDocumento = $element->id_tp_documento;
                $idGrupo = $element->id_grupo;
                $idTipoRequerimiento = $element->id_tp_documento == 1 ? $element->id_tipo_requerimiento:0;
                $idPrioridad = $element->id_prioridad;
                $estado = $element->estado !=null ?$element->estado:$element->id_estado;
                $idDivision = $element->division_id !=null ?$element->division_id:$element->id_division;                
                $operaciones = Operacion::getOperacion($tipoDocumento, $idTipoRequerimiento, $idGrupo, $idDivision, $idPrioridad);
                
                if($operaciones ==[]){
                    $mensaje[]= "El requerimiento ".$element->codigo." no coincide con una operación valida, es omitido en la lista. Parametros para obtener operacion: tipoDocumento= ".$tipoDocumento.", tipoRequerimiento= ".$idTipoRequerimiento.",Grupo= ".$idGrupo.", Division= ".$idDivision.", Prioridad= ".$idPrioridad;
                }else{
                    $flujoTotal = Flujo::getIdFlujo($operaciones[0]->id_operacion)['data'];
                    $tamañoFlujo = $flujoTotal ? count($flujoTotal) : 0;
                    $voboList = Aprobacion::getVoBo($idDocumento); // todas las vobo del documento
                    $cantidadAprobacionesRealizadas = Aprobacion::getCantidadAprobacionesRealizadas($idDocumento);
                    $ultimoVoBo = Aprobacion::getUltimoVoBo($idDocumento);
                    $nextFlujo = [];
                    $nextIdRolAprobante = 0;
                    $nextIdFlujo = 0;
                    $nextIdOperacion = 0;
                    $nextNroOrden = 0;
                    $aprobacionFinalOPendiente = '';
                    $cantidadConSiguienteAprobacion=false;
                    $tieneRolConSiguienteAprobacion='';
    
                    if ($cantidadAprobacionesRealizadas > 0) {
    
                        // si existe data => evaluar si tiene aprobacion / Rechazado / observado.
                        if (in_array($ultimoVoBo->id_vobo, [1, 5])) { // revisado o aprobado
                            // next flujo y rol aprobante
                            $ultimoIdFlujo = $ultimoVoBo->id_flujo;
    
                            foreach ($flujoTotal as $key => $flujo) {
                                if ($flujo->id_flujo == $ultimoIdFlujo) {
                                    $nroOrdenUltimoFlujo = $flujo->orden;
                                    if ($nroOrdenUltimoFlujo != $tamañoFlujo) { // get next id_flujo
                                        foreach ($flujoTotal as $key => $flujo) {
                                            if ($flujo->estado == 1) {
                                                if ($flujo->orden == $nroOrdenUltimoFlujo + 1) {
                                                    $nextFlujo = $flujo;
                                                    $nextIdFlujo = $flujo->id_flujo;
                                                    $nextIdOperacion = $flujo->id_operacion;
                                                    $nextIdRolAprobante = $flujo->id_rol;
                                                    $aprobacionFinalOPendiente = $flujo->orden == $tamañoFlujo ? 'APROBACION_FINAL' : 'PENDIENTE'; // NEXT NRO ORDEN == TAMAÑO FLUJO?
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($ultimoVoBo->id_vobo == 3 && $ultimoVoBo->id_sustentacion != null) { //observado con sustentacion
                            foreach ($flujoTotal as $flujo) {
                                if ($flujo->orden == 1) {
                                    // Debugbar::info($flujo);
                                    $nextFlujo = $flujo;
                                    $nextNroOrden = $flujo->orden;
                                    $nextIdOperacion = $flujo->id_operacion;
                                    $nextIdFlujo = $flujo->id_flujo;
                                    $nextIdRolAprobante = $flujo->id_rol;
                                    $aprobacionFinalOPendiente = $flujo->orden == $tamañoFlujo ? 'APROBACION_FINAL' : 'PENDIENTE'; // NEXT NRO ORDEN == TAMAÑO FLUJO?
    
                                }
                            }
                        }
                    } else { //  no tiene aprobaciones, entonces es la PRIMERA APROBACIÓN de este req.
                        // tiene observación?
    
                        //obtener rol del flujo de aprobacion con orden #1 y comprar con el rol del usuario en sesion
                        foreach ($flujoTotal as $flujo) {
                            if ($flujo->orden == 1) {
                                // Debugbar::info($flujo);
                                $nextFlujo = $flujo;
                                $nextNroOrden = $flujo->orden;
                                $nextIdOperacion = $flujo->id_operacion;
                                $nextIdFlujo = $flujo->id_flujo;
                                $nextIdRolAprobante = $flujo->id_rol;
                                $aprobacionFinalOPendiente = $flujo->orden == $tamañoFlujo ? 'APROBACION_FINAL' : 'PENDIENTE'; // NEXT NRO ORDEN == TAMAÑO FLUJO?
    
                            }
                        }
                    }
                    $numeroOrdenSiguienteAprobacion=0;
                    foreach ($flujoTotal as $flujo) {
                        if ($flujo->id_operacion == $nextIdOperacion) {
                            if($flujo->orden == (intval($nextNroOrden)+1)){ // si existe una siguiente aprobacion (nro orden + 1 ) 
                                if(in_array($flujo->id_rol, $idRolUsuarioList) == true){
                                    $cantidadConSiguienteAprobacion=true;
                                    $numeroOrdenSiguienteAprobacion= $flujo->orden;
                                }
                                
                            }
                            
                        }
                    }
    
                    if($cantidadConSiguienteAprobacion ==true){
                        $tieneRolConSiguienteAprobacion=true;
                    }else{
                        $tieneRolConSiguienteAprobacion=false;    
                    }
                    
                    $llenarCargaUtil=false;
                    if ((in_array($nextIdRolAprobante, $idRolUsuarioList)) == true) {
                        if ($nextNroOrden == 1) {
                            // fitlar por division
                            if (in_array($idDivision, $idDivisionUsuarioList) == true) {
                                $llenarCargaUtil=true;
                            }
                        } else {
                            $llenarCargaUtil=true;
                        }
                        
                        if($llenarCargaUtil){
                            $element->setAttribute('id_flujo',$nextIdFlujo);
                            $element->setAttribute('id_usuario_aprobante',$idUsuarioAprobante);
                            $element->setAttribute('id_rol_aprobante',$nextIdRolAprobante);
                            $element->setAttribute('aprobacion_final_o_pendiente',$aprobacionFinalOPendiente);
                            $element->setAttribute('id_doc_aprob',$idDocumento);
                            $element->setAttribute('id_operacion',$nextIdOperacion);
                            $element->setAttribute('tiene_rol_con_siguiente_aprobacion',$tieneRolConSiguienteAprobacion);
                            $element->setAttribute('cantidad_aprobados_total_flujo',($cantidadAprobacionesRealizadas) . '/' . ($tamañoFlujo));
                            $element->setAttribute('aprobaciones',$voboList);
                            $element->setAttribute('pendiente_aprobacion',$pendiente_aprobacion);
                            $payload[] = $element;
                        }
                    }
                }


            }
        }


        $output = ['data' => $payload, 'mensaje'=>$mensaje];
        return $output;

    }
}
