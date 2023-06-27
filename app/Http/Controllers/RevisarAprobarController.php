<?php

namespace App\Http\Controllers;

use App\Helpers\Finanzas\PresupuestoInternoHistorialHelper;
use App\Helpers\NotificacionHelper;
use App\Http\Controllers\Finanzas\Presupuesto\PresupuestoInternoController;
use App\Mail\EmailNotificarUsuarioPropietarioDeDocumento;
use App\Models\Administracion\Aprobacion;
use App\Models\Administracion\Division;
use App\Models\Administracion\Documento;
use App\Models\Administracion\DocumentosAprobadosView;
use App\Models\Administracion\DocumentosView;
use App\Models\Administracion\Flujo;
use App\Models\Administracion\Operacion;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Producto;
use App\Models\Almacen\Requerimiento;
use App\Models\Almacen\Reserva;
use App\Models\Almacen\Trazabilidad;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Configuracion\Usuario;
use App\Models\Configuracion\UsuarioDivision;
use App\models\Configuracion\UsuarioRol;
use App\Models\Tesoreria\RequerimientoPago;
use App\Models\Tesoreria\RequerimientoPagoDetalle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Debugbar;
use Exception;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class RevisarAprobarController extends Controller{

    function viewListaRequerimientoPagoPendienteParaAprobacion(){
        $gruposUsuario = Auth::user()->getAllGrupo();
        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('necesidades/revisar_aprobar/lista',compact('gruposUsuario','array_accesos'));
    }


    public static function getOperacionSinConsiderarRol($IdTipoDocumento,$idTipoRequerimientoCompra, $idGrupo, $idDivision, $idPrioridad, $idMoneda, $montoTotal, $idTipoRequerimientoPago)
    {
        
        // return [$IdTipoDocumento,$idTipoRequerimientoCompra, $idGrupo, $idDivision, $idPrioridad, $idMoneda, $montoTotal, $idTipoRequerimientoPago];
        $montoTotalDolares=0;
        $montoTotalSoles=0;
        if($idMoneda ==1){ // soles convertir a dolares
            $montoTotalDolares = floatval($montoTotal)/3.7; // TODO llamar a función para obtener el tipo de cambio
            $montoTotalSoles=$montoTotal;
        }elseif($idMoneda ==2){
            $montoTotalDolares=$montoTotal;
            $montoTotalSoles = floatval($montoTotal)*3.7;
        }
        // para residente de obra
  

        $totalOperaciones = Operacion::where("estado","!=",7)->get();
        // para residente de obra


        // $totalOperaciones = Operacion::where("estado","!=",7)->get();
        $operacionesCoincidenciaTipoDocumentoGrupo=[];
        foreach ($totalOperaciones as $k => $o) {
            if($o->id_tp_documento ==$IdTipoDocumento && $o->id_grupo ==$idGrupo){
                $operacionesCoincidenciaTipoDocumentoGrupo[]= $o;
            }
        }

        $operacionesCoincidenciaPorTipoRequerimiento=[];
        if(count($operacionesCoincidenciaTipoDocumentoGrupo)!=1){
            foreach ($operacionesCoincidenciaTipoDocumentoGrupo as $k => $o) {
                if($o->tipo_requerimiento_id !=null){
                    if($o->tipo_requerimiento_id ==$idTipoRequerimientoCompra){
                        $operacionesCoincidenciaPorTipoRequerimiento[]= $o;
                    }
                }elseif($o->tipo_requerimiento_pago_id !=null){
                    if($o->tipo_requerimiento_pago_id ==$idTipoRequerimientoPago){
                        $operacionesCoincidenciaPorTipoRequerimiento[]= $o;
                    }

                }
            }
        }else{
            //$operacionesCoincidenciaTipoDocumentoGrupo solo tiene un valor
            return $operacionesCoincidenciaTipoDocumentoGrupo;
        }

        $operacionesCoincidenciaPorDivision=[];
        if(count($operacionesCoincidenciaPorTipoRequerimiento) !=1){
            foreach ($operacionesCoincidenciaPorTipoRequerimiento as $k => $o) {
                if($o->division_id ==$idDivision){
                    $operacionesCoincidenciaPorDivision[]= $o;
                }
            }
        }else{
            return $operacionesCoincidenciaPorTipoRequerimiento;
        }
 

        $operacionesCoincidenciaPorMonedaMonto=[];
        $elMontoEsMayor='NO';
        $elMontoEsMenor='NO';
        $elMontoEsIgual='NO';
        $elMontoEsEntre='NO';
        if(count($operacionesCoincidenciaPorDivision) !=1){
            foreach ($operacionesCoincidenciaPorDivision as $k => $o) {
                if($o->monto_mayor >0 ){ // tiene monto definido
                    $elMontoEsMayor = $o->id_moneda ==1?($montoTotalSoles > $o->monto_mayor?'SI':'NO'):($o->id_moneda ==2?($montoTotalDolares > $o->monto_mayor?'SI':'NO'):'NO');
                    $operacionPropuestaPorMonto1[]= $o;
                }
                if($o->monto_menor >0 ){ // tiene monto definido
                    $elMontoEsMenor = $o->id_moneda ==1?($montoTotalSoles < $o->monto_menor?'SI':'NO'):($o->id_moneda ==2?($montoTotalDolares < $o->monto_menor?'SI':'NO'):'NO');
                    $operacionPropuestaPorMonto2[]= $o;

                }
                if($o->monto_igual >0){ // tiene monto definido
                    $elMontoEsIgual = $o->id_moneda ==1?($montoTotalSoles == $o->monto_igual?'SI':'NO'):($o->id_moneda ==2?($montoTotalDolares == $o->monto_igual?'SI':'NO'):'NO');
                    $operacionPropuestaPorMonto3[]= $o;
                }
                if($o->monto_igual >0 && $o->monto_menor >0){ // tiene monto definido
                    $elMontoEsEntre = $o->id_moneda ==1?((($montoTotalSoles > $o->monto_mayor && $montoTotalSoles < $o->monto_menor ))?'SI':'NO'):($o->id_moneda ==2?(($montoTotalSoles > $o->monto_mayor && $montoTotalSoles < $o->monto_menor )?'SI':'NO'):'NO');
                    $operacionPropuestaPorMonto4[]= $o;

                }
            }

            if($elMontoEsEntre =='SI'){
                return $operacionPropuestaPorMonto4;
            }else{
                if($elMontoEsMayor == 'SI'){
                    return $operacionPropuestaPorMonto1;
                }elseif($elMontoEsMenor == 'SI'){
                    return $operacionPropuestaPorMonto2;
                }elseif($elMontoEsIgual == 'SI'){
                    return $operacionPropuestaPorMonto3;
                }else{
                    return $operacionesCoincidenciaPorDivision;

                }
            }
            
        }else{
            return $operacionesCoincidenciaPorDivision;
        }
    }

    public function mostrarTodoFlujoAprobacionDeDocumento($idDocumento){

            $documento = DocumentosView::find($idDocumento);
            if(!isset($documento)){
                return [];
            }
            $tipoDocumento = $documento->id_tp_documento;
            $idGrupo = $documento->id_grupo;

            if($documento->id_tp_documento ==1 ){
                $idTipoRequerimiento = $documento->id_tipo_requerimiento;
                if($idTipoRequerimiento ==1 ){ // los requerimiento tipo MPC no tiene flujo en Agil
                    return [];
                }
                $idTipoRequerimientoPago=null;
            }elseif($documento->id_tp_documento ==2){
                $idTipoRequerimiento=null;
                $idTipoRequerimientoPago= $documento->id_tipo_requerimiento;
            }
            $idPrioridad = $documento->id_prioridad;
            $idMoneda = $documento->id_moneda;
            $idDivision = $documento->id_division;

            if($idDivision==null || $tipoDocumento ==null){
                return []; // si no existe división 
            }
            $montoTotal= 0;
            $obtenerMontoTotal = $this->obtenerMontoTotalDocumento($tipoDocumento,$idDocumento);
            if($obtenerMontoTotal['estado']=='success'){
                $montoTotal=$obtenerMontoTotal['monto'];
            }
            
            $operaciones = $idDivision>0? $this->getOperacionSinConsiderarRol($tipoDocumento, $idTipoRequerimiento, $idGrupo, $idDivision, $idPrioridad, $idMoneda, $montoTotal, $idTipoRequerimientoPago):[];
            // return $operaciones;
            if(isset($operaciones)){
                $flujo = Flujo::with('rol')->where([['id_operacion',$operaciones[0]->id_operacion],['estado',1]])->get();

                foreach ($flujo as $keyF => $valueF) {
                    $nombreCortoUsuario=[];
                    $usuarioRol= UsuarioRol::with('sisUSua')->where([['id_rol',$valueF->id_rol],['estado','!=',7]])->get();
                    foreach ($usuarioRol as $keyUr => $valueUr) {
                        $nombreCortoUsuario[] = $valueUr->sisUSua->nombre_corto;
                    }
                    $flujo[$keyF]['nombre_usuarios'] = $nombreCortoUsuario;
                
                }
                return $flujo;
            }else{
                return [];
            }


            
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

        $divisiones = Division::mostrar();
        $idDivisionList = [];
        foreach ($divisiones as $value) {
            $idDivisionList[] = $value->id_division; //lista de id del total de divisiones
        }

        // $divisionUsuarioNroOrdenUno = Division::mostrarDivisionUsuarioNroOrdenUno();
        // $idDivisionUsuarioList = [];
        // foreach ($divisionUsuarioNroOrdenUno as $value) {
        //     $idDivisionUsuarioList[] = $value->id_division;
        // }
        $idDivisionUsuarioList = [];

        $usuarioDivisionAcceso = UsuarioDivision::mostrarDivisionUsuarioAcceso();
        foreach ($usuarioDivisionAcceso as $value) {
            $idDivisionUsuarioList[] = $value->id_division;
        }
        // Debugbar::info($idDivisionUsuarioList);


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
            'adm_estado_doc.bootstrap_color'
            // DB::raw("(SELECT SUM(alm_det_req.cantidad * alm_det_req.precio_unitario)
            // FROM almacen.alm_det_req
            // WHERE   alm_det_req.id_requerimiento = alm_req.id_requerimiento AND
            // alm_det_req.estado != 7) AS monto_total")
        )
        ->where([['adm_documentos_aprob.id_tp_documento',1],['flg_compras',0]]) //documento => requerimiento de B/S
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


                $idRolUsuarioDocList=[];
                $allRolUsuarioDocList = Auth::user()->getAllRolUser($element->id_usuario);
                foreach ($allRolUsuarioDocList as $allroldoc) {
                    $idRolUsuarioDocList[]=$allroldoc->id_rol;
                }

                $idTipoRequerimiento = $element->id_tipo_requerimiento > 0?$element->id_tipo_requerimiento:null;
                $idPrioridad = $element->id_prioridad;
                $idMoneda = $element->id_moneda;
                $estado = $element->estado !=null ?$element->estado:$element->id_estado;
                $idDivision = $element->division_id !=null ?$element->division_id:$element->id_division;
                $idTipoRequerimientoPago= $element->id_requerimiento_pago_tipo >0 ?$element->id_requerimiento_pago_tipo:null;
                $montoTotal= 0;
                $obtenerMontoTotal = $this->obtenerMontoTotalDocumento($tipoDocumento,$idDocumento);
                if($obtenerMontoTotal['estado']=='success'){
                    $montoTotal=$obtenerMontoTotal['monto'];
                }

                $operaciones = Operacion::getOperacion($tipoDocumento, $idTipoRequerimiento, $idGrupo, $idDivision, $idPrioridad, $idMoneda, $montoTotal, $idTipoRequerimientoPago,$idRolUsuarioDocList);
                // Debugbar::info($operaciones);
                // Debugbar::info($tipoDocumento, $idTipoRequerimiento, $idGrupo, $idDivision, $idPrioridad, $idMoneda, $montoTotal, $idTipoRequerimientoPago,$idRolUsuarioDocList);
                if(count($operaciones)>1){
                    $mensaje[]= "Se detecto que los criterios del requerimiento dan como resultado multibles operaciones :".$operaciones;

                }

                if($operaciones ==[]){
                    $mensaje[]= "El requerimiento ".$element->codigo." no coincide con una operación valida, es omitido en la lista. Parametros para obtener operacion: tipoDocumento= ".$tipoDocumento.", tipoRequerimientoCompra= ".$idTipoRequerimiento.",Grupo= ".$idGrupo.", Division= ".$idDivision.", Prioridad= ".$idPrioridad.", Moneda=".$idMoneda.", Monto=".$montoTotal.", TipoRequerimientoPago=".$idTipoRequerimientoPago;
                }else{
                    $flujoTotal = Flujo::getIdFlujo($operaciones[0]->id_operacion)['data'];
                    $tamañoFlujo=0;
                    foreach ($flujoTotal as $key => $f) {
                        if($f->orden ==($key+1)){
                            $tamañoFlujo++;
                        }
                    }
                    // $tamañoFlujo = $flujoTotal ? count($flujoTotal) : 0;
                    $voboList = Aprobacion::getVoBo($idDocumento); // todas las vobo del documento
                    $cantidadAprobacionesRealizadas = Aprobacion::getCantidadAprobacionesRealizadas($idDocumento);
                    $ultimoVoBo = Aprobacion::getUltimoVoBo($idDocumento);
                    $nextFlujo = [];
                    $nextIdRolAprobanteList = [];
                    $aprobarSinImportarOrden = false;
                    $idRolAprobanteEnCualquierOrdenList=[];
                    $nextIdFlujo = 0;
                    $nextIdOperacion = 0;
                    $nextNroOrden = 0;
                    $aprobacionFinalOPendiente = '';
                    $cantidadConSiguienteAprobacion=false;
                    $tieneRolConSiguienteAprobacion='';

                    foreach ($flujoTotal as $flujo) { //obtener rol con privilegio de aprobar sin respetar orden

                        if($flujo->aprobar_sin_respetar_orden ==true || $flujo->aprobar_sin_respetar_orden >0){
                            $idRolAprobanteEnCualquierOrdenList[]= $flujo->id_rol;
                        }
                    }
                // Debugbar::info($flujo->aprobar_sin_respetar_orden);

                    if(count(array_intersect($idRolAprobanteEnCualquierOrdenList,$idRolUsuarioList))>0){
                        $aprobarSinImportarOrden = true;
                    }


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
                                                    $nextIdRolAprobanteList[] = $flujo->id_rol;
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
                                    $nextFlujo = $flujo;
                                    $nextNroOrden = $flujo->orden;
                                    $nextIdOperacion = $flujo->id_operacion;
                                    $nextIdFlujo = $flujo->id_flujo;
                                    $nextIdRolAprobanteList[] = $flujo->id_rol;
                                    $aprobacionFinalOPendiente = $flujo->orden == $tamañoFlujo ? 'APROBACION_FINAL' : 'PENDIENTE'; // NEXT NRO ORDEN == TAMAÑO FLUJO?

                                }
                            }
                        }
                    } else { //  no tiene aprobaciones, entonces es la PRIMERA APROBACIÓN de este req.
                        // tiene observación?

                        //obtener rol del flujo de aprobacion con orden #1 y comprar con el rol del usuario en sesion
                        foreach ($flujoTotal as $flujo) {
                            if ($flujo->orden == 1) {
                                $nextFlujo = $flujo;
                                $nextNroOrden = $flujo->orden;
                                $nextIdOperacion = $flujo->id_operacion;
                                $nextIdFlujo = $flujo->id_flujo;
                                $nextIdRolAprobanteList[] = $flujo->id_rol;
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

                    $idRolAprobanteIntersectSelected=0;
                    $idRolAprobanteIntersectArray = array_intersect($nextIdRolAprobanteList, $idRolUsuarioList);
                    foreach ($idRolAprobanteIntersectArray as $key => $value) {
                        $idRolAprobanteIntersectSelected=$value;
                    }
                    if($idRolAprobanteIntersectSelected==0){ // en caso sea el usuario que puede aprobar en cualquier orden
                        $idRolAprobanteCualquierOrdenIntersectArray = array_intersect($idRolAprobanteEnCualquierOrdenList, $idRolUsuarioList);
                        foreach ($idRolAprobanteCualquierOrdenIntersectArray as $key => $value) {
                            $idRolAprobanteIntersectSelected=$value;
                    }
                    }


                    // Debugbar::info($idRolUsuarioList);
                    // Debugbar::info(array_intersect($idRolAprobanteEnCualquierOrdenList, $idRolUsuarioList));

                    if ( in_array(6,$idRolUsuarioList) || ((count(array_intersect($nextIdRolAprobanteList, $idRolUsuarioList))) > 0) == true || (count(array_intersect($idRolAprobanteEnCualquierOrdenList, $idRolUsuarioList))) > 0) {


                            $element->setAttribute('id_flujo',$nextIdFlujo);
                            $element->setAttribute('id_usuario_aprobante',$idUsuarioAprobante);
                            $element->setAttribute('id_rol_aprobante',$idRolAprobanteIntersectSelected);
                            $element->setAttribute('aprobacion_final_o_pendiente',$aprobarSinImportarOrden =='true'?'APROBACION_FINAL':$aprobacionFinalOPendiente);
                            $element->setAttribute('id_doc_aprob',$idDocumento);
                            $element->setAttribute('id_operacion',$nextIdOperacion);
                            $element->setAttribute('tiene_rol_con_siguiente_aprobacion',$tieneRolConSiguienteAprobacion);
                            $element->setAttribute('cantidad_aprobados_total_flujo',($cantidadAprobacionesRealizadas) . '/' . ($tamañoFlujo));
                            $element->setAttribute('aprobaciones',$voboList);
                            $element->setAttribute('pendiente_aprobacion',$pendiente_aprobacion);
                            $element->setAttribute('aprobar_sin_importar_orden',$aprobarSinImportarOrden);

                            if(!(in_array(36,$idRolUsuarioDocList) && (in_array(21,$idRolUsuarioList) || in_array(22,$idRolUsuarioList) ))){ //filtro residente no mostrar a jefes de planificacion y jefe de ejecición de proyectos
                                $payload[] = $element;
                            }
                            }

                    }
                            }

                }


        $output = ['data' => $payload, 'mensaje'=>$mensaje];
        return $output;

    }

    public function mostrarListaDeDocumentosAprobados(Request $request){

        $allRol = Auth::user()->getAllRol();
        $idRolUsuarioList = [];
        foreach ($allRol as  $rol) {
            $idRolUsuarioList[] = $rol->id_rol;
        }
        $documentos = DocumentosView::whereNotIn('id_estado',[1,7])->whereIn('ultimo_rol_aprobador',$idRolUsuarioList);
        
        return DataTables::of($documentos)->toJson();
    }

    public function obtenerRelacionadoAIdDocumento($tipoDocumento,$idDocumento){
        $result = [];

        $documento = Documento::where([['id_doc_aprob',$idDocumento],['id_tp_documento',$tipoDocumento]])->first();

        if( !empty($documento)){
            $result =[
                'id'=> $documento->id_doc,
                'mensaje'=>'Id encontrado',
                'estado'=>'success'
        ];

        }else{
            $result =[
            'id'=>0,
            'mensaje'=>'No se encontro un id que haga referencia al id documento',
            'estado'=>'error'
        ];

        }

        return $result;

    }

    public function obtenerMontoTotalDocumento($tipoDocumento, $idDocumento){

        $montoTotal =0;

        if($tipoDocumento ==1){
            $obtenerId = $this->obtenerRelacionadoAIdDocumento($tipoDocumento,$idDocumento);
            if($obtenerId['estado']=='success'){
                $requerimiento = Requerimiento::find($obtenerId['id']);
                $detalle = $requerimiento->detalle;
                $montoTotal = 0;
                foreach ($detalle as $item) {
                    $montoTotal += $item->cantidad * $item->precio_unitario;
                }
            }
        }elseif($tipoDocumento ==11){

            $obtenerId = $this->obtenerRelacionadoAIdDocumento($tipoDocumento,$idDocumento);
            if($obtenerId['estado']=='success'){
                $requerimientoPago = RequerimientoPago::find($obtenerId['id']);
                $detalle = $requerimientoPago->detalle;
                $montoTotal = 0;
                foreach ($detalle as $item) {
                    $montoTotal += $item->cantidad * $item->precio_unitario;
                }
            }
        }
        return ['monto'=>$montoTotal, 'estado'=>$obtenerId['estado'], 'mensaje'=>$obtenerId['mensaje']];
    }

    public function actualizarEstadoRequerimiento($accion,$requerimiento,$aprobacionFinalOPendiente){
        switch ($accion) {
            case '1':
                if ($aprobacionFinalOPendiente == 'APROBACION_FINAL') {
                    $requerimiento->estado = 2;
                }
                break;
            case '2':
                $requerimiento->estado = 7;
                $detalleRequerimiento = DetalleRequerimiento::where("id_requerimiento", $requerimiento->id_requerimiento)->get();
                foreach ($detalleRequerimiento as $detalle) {
                    $detalle->estado = 7;
                    $detalle->save();
                }
                break;
            case '3':

                if($requerimiento->estado !=3){
                    $requerimiento->estado_anterior = $requerimiento->estado;
                }
                $requerimiento->estado = 3;


                break;
            case '5':
                $requerimiento->estado = 12;
                break;
        }
        $requerimiento->save();
        return $requerimiento;
    }
    public function actualizarEstadoRequerimientoPago($accion,$requerimientoPago,$aprobacionFinalOPendiente){
        switch ($accion) {
            case '1':
                if ($aprobacionFinalOPendiente == 'APROBACION_FINAL') {
                    $requerimientoPago->id_estado = 2;
                }
                break;
            case '2':
                $requerimientoPago->id_estado = 7;
                $detalleRequerimientoPago = RequerimientoPagoDetalle::where("id_requerimiento_pago", $requerimientoPago->id_requerimiento_pago)->get();
                foreach ($detalleRequerimientoPago as $detalle) {
                    $detalle->id_estado = 7;
                    $detalle->save();
                }
                
                // (new PresupuestoInternoController)->afectarPresupuestoInterno('suma','requerimiento de pago',$requerimientoPago->id_requerimiento_pago,$detalleRequerimientoPago);

                break;
            case '3':
                $requerimientoPago->id_estado = 3;
                if($requerimientoPago->id_estado !=3){
                    $requerimientoPago->estado_anterior = $requerimientoPago->id_estado;
                }
                break;
            case '5':
                $requerimientoPago->id_estado = 4;
                break;
        }
        $requerimientoPago->save();
    }

    public function registrarTrazabilidad($idRequerimiento,$aprobacionFinalOPendiente, $idUsuario, $nombreCompletoUsuarioRevisaAprueba, $accion){
        $trazabilidad = new Trazabilidad();
        $trazabilidad->id_requerimiento = $idRequerimiento;
        $trazabilidad->id_usuario = $idUsuario;
        switch ($accion) {
            case '1':
                if ($aprobacionFinalOPendiente == 'APROBACION_FINAL') {
                    $trazabilidad->accion = 'APROBADO';
                    $trazabilidad->descripcion = 'Aprobado por ';
                }
                break;
            case '2':
                $trazabilidad->accion = 'RECHAZADO';
                $trazabilidad->descripcion = 'Rechazado por ';
                break;
            case '3':
                $trazabilidad->accion = 'OBSERVADO';
                $trazabilidad->descripcion = 'Observado por ';

                break;
            case '5':
                $trazabilidad->accion = 'REVISADO';
                $trazabilidad->descripcion = 'Revisado por ';

                break;
        }
        $trazabilidad->descripcion .=  $nombreCompletoUsuarioRevisaAprueba ?? '';
        $trazabilidad->fecha_registro = new Carbon();
        $trazabilidad->save();

        return $trazabilidad;

    }

    private function enviarNotificacionPorAprobacion($requerimiento,$comentario,$nombreCompletoUsuarioPropietarioDelDocumento,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$trazabilidad){
        $titulo = 'El requerimiento ' . $requerimiento->codigo . ' fue '.$trazabilidad->accion;
        $mensaje = 'El requerimiento ' . $requerimiento->codigo . ' fue '.$trazabilidad->accion.'. Información adicional del requerimiento:' .
            '<ul>' .
            '<li> Concepto/Motivo: ' . $requerimiento->concepto . '</li>' .
            '<li> Tipo de requerimiento: ' . $requerimiento->tipo->descripcion . '</li>' .
            '<li> División: ' . $requerimiento->division->descripcion . '</li>' .
            '<li> Fecha limite de entrega: ' . $requerimiento->fecha_entrega . '</li>' .
            '<li> Monto Total: ' . $requerimiento->moneda->simbolo . number_format($montoTotal, 2) . '</li>' .
            '<li> Creado por: ' . ($nombreCompletoUsuarioPropietarioDelDocumento ?? '') . '</li>' .
            '<li> '.$trazabilidad->descripcion.': ' . ($nombreCompletoUsuarioRevisaAprueba ?? '') . '</li>' .
            (!empty($comentario) ? ('<li> Comentario: ' . $comentario . '</li>') : '') .
            '</ul>' .
            '<p> *Este correo es generado de manera automática, por favor no responder.</p>
        <br> Saludos <br> Módulo de Logística <br> SYSTEM AGILE';

        $seNotificaraporEmail = false;
            $correoUsuarioList = [];
        $correoUsuarioList[] = Usuario::find($requerimiento->id_usuario)->email; // notificar a usuario
        $usuariosList = Usuario::getAllIdUsuariosPorRol(4); // notificar al usuario  con rol = 'logistico compras'

        // Debugbar::info($usuariosList);
        if (count($usuariosList) > 0) {
            if (config('app.debug')) {
                $correoUsuarioList[] = config('global.correoDebug1');
            }else{
                foreach ($usuariosList as $idUsuario) {
                    $correoUsuarioList[] = Usuario::find($idUsuario)->email;
                }
            }

            if (count($correoUsuarioList) > 0) {
                // $destinatarios[]= 'programador03@okcomputer.com.pe';
                $destinatarios = $correoUsuarioList;
                $seNotificaraporEmail = true;



                $payload = [
                    'id_empresa' => $requerimiento->id_empresa,
                    'email_destinatario' => $destinatarios,
                    'titulo' => $titulo,
                    'mensaje' => $mensaje
                ];

                // Debugbar::info($payload);

                if (count($destinatarios) > 0) {
                    NotificacionHelper::enviarEmail($payload);

                }
            }
        }
    }

    public function registrarRespuesta($accion, $idFlujo, $idDocumento, $idTipoDocumento, $idRequerimiento, $idRequerimientoPago, $idUsuario,$comentario, $idRolAprobante){
        $aprobacion = new Aprobacion();
        $aprobacion->id_flujo = $idFlujo;
        $aprobacion->id_doc_aprob = $idDocumento;
        $aprobacion->id_usuario = $idUsuario;
        $aprobacion->id_vobo = $accion;
        $aprobacion->fecha_vobo = new Carbon();
        $aprobacion->detalle_observacion = $comentario;
        $aprobacion->id_rol = $idRolAprobante;
        $aprobacion->tiene_sustento = false;
        $aprobacion->save();

        $mensaje='';
        if($accion ==1){
            $mensaje='Documento aprobado';

            if($idTipoDocumento == 1){
            PresupuestoInternoHistorialHelper::registrarEstadoGastoAprobadoDeRequerimiento($idRequerimiento,$idTipoDocumento);
            }else if($idTipoDocumento == 11){
                PresupuestoInternoHistorialHelper::registrarEstadoGastoAprobadoDeRequerimiento($idRequerimientoPago,$idTipoDocumento);
            }

        }elseif($accion ==2){
            $mensaje='Documento rechazado';
            $this->limpiarMapeoDeDocumento($idDocumento);

            // actualizar a true el campo de "documento_anulado" en la tabla historial_presupuesto_interno_saldo para determinar que registros ya no se atenderán
            if($idTipoDocumento == 11){
                PresupuestoInternoHistorialHelper::actualizarRegistroPorDocumentoAnuladoEnHistorialSaldo(null,null,$idRequerimientoPago);
            }elseif($idTipoDocumento == 1){
                PresupuestoInternoHistorialHelper::actualizarRegistroPorDocumentoAnuladoEnHistorialSaldo($idRequerimiento,null,null);
            }

        }elseif($accion ==3){
            $mensaje='Documento observado';
            $this->limpiarMapeoDeDocumento($idDocumento);

        }

        return ['data'=>$aprobacion,'mensaje'=>$mensaje];
    }

    public function limpiarMapeoDeDocumento($idDocAprob){
        $documento = Documento::where([['id_doc_aprob',$idDocAprob]])->first();
        if($documento->id_tp_documento==1){
            $detalle = DetalleRequerimiento::where('id_requerimiento',$documento->id_doc)->get();
            foreach ($detalle as $value) {
                $det = DetalleRequerimiento::find($value->id_detalle_requerimiento);

                $cantidadDetalleConProducto = DetalleRequerimiento::where([['id_producto',$det->id_producto],['id_requerimiento','!=',$det->id_requerimiento]])->count();
                $cantiadReservaConProd = Reserva::where('id_producto',$det->id_producto)->count();
                if($cantidadDetalleConProducto==0 && $cantiadReservaConProd==0){
                    $prod=Producto::find($det->id_producto);
                    $prod->estado=7;
                    $prod->save();
                }


                $det->id_producto= null;
                $det->save();
            }
        }
    }

    public function guardarRespuesta(Request $request){
        DB::beginTransaction();
        try {
            // $accion = $request->accion;
            // $sustento = $request->sustento;
            // $idTipoDocumento = $request->idTipoDocumento;
            // $tipoDocumento = $request->tipoDocumento;
            // $idDocumento = $request->idDocumento;
            // $idRequerimiento = $request->idRequerimiento;
            // $idRequerimientoPago = $request->idRequerimientoPago;
            // idUsuarioPropietarioDocumento = $request->idUsuarioPropietarioDocumento;
            // idUsuarioAprobante = $request->idUsuarioAprobante;
            // $idRolAprobante = $request->idRolAprobante;
            // $idFlujo = $request->idFlujo;
            // $aprobacionFinalOPendiente = $request->aprobacionFinalOPendiente;
            // tieneRolConSiguienteAprobacion = $request->tieneRolConSiguienteAprobacion;
            // idOperacion = $request->idOperacion;
            // $aprobarSinImportarOrden = $request->aprobarSinImportarOrden;
            $nombreCompletoUsuarioRevisaAprueba = Usuario::withTrashed()->find($request->idUsuarioAprobante)->nombre_corto;
            $nombreAccion='';
            if ($request->accion == 1) {
                $nombreAccion='Aprobado';
            }
            if ($request->accion == 2) {
                $nombreAccion='Rechazado';
            }
            if ($request->accion == 3) {
                $nombreAccion='Observado';
            }
            if ($request->aprobacionFinalOPendiente == 'PENDIENTE') {
                if ($request->accion == 1) {
                    $request->accion = 5; // Revisado
                    $nombreAccion='Pendiente Aprobación';
                }
            }
            // agregar vobo (1= aprobado, 2= rechazado, 3=observado, 5=Revisado)
            $aprobacion= $this->registrarRespuesta($request->accion, $request->idFlujo, $request->idDocumento, $request->idTipoDocumento, $request->idRequerimiento, $request->idRequerimientoPago, $request->idUsuarioAprobante,$request->sustento, $request->idRolAprobante);


            $montoTotal= 0;

            $obtenerMontoTotal = $this->obtenerMontoTotalDocumento($request->idTipoDocumento,$request->idDocumento);

            if($obtenerMontoTotal['estado']=='success'){
                $montoTotal=$obtenerMontoTotal['monto'];

            }else{
                return response()->json(['id_aprobacion' => 0, 'notificacion_por_emial' => false, 'mensaje' => 'Hubo un problema al guardar la respuesta. Mensaje de error:'.$obtenerMontoTotal['mensaje']]);

            }

            // $nombreCompletoUsuarioPropietarioDelDocumento = Usuario::find($request->idUsuarioPropietarioDocumento)->nombre_corto;
            //  ======= inicio tipo requerimiento b/s =======
            $requerimiento=[];
            $requerimientoPago=[];
            if($request->idTipoDocumento ==1){
                if($request->idRequerimiento > 0){
                    $requerimiento = Requerimiento::find($request->idRequerimiento);
                }else{
                    $obtenerId = $this->obtenerRelacionadoAIdDocumento($request->idTipoDocumento,$request->idDocumento);

                    if($obtenerId['estado']=='success'){
                        $requerimiento = Requerimiento::find($obtenerId['id']);
                    }else{
                        return response()->json(['id_aprobacion' => 0, 'notificacion_por_emial' => false, 'mensaje' => 'Hubo un problema al guardar la respuesta. Mensaje de error:'.$obtenerMontoTotal['mensaje']]);
                    }
                }

                $requerimientoConEstadoActualizado= $this->actualizarEstadoRequerimiento($request->accion,$requerimiento,$request->aprobacionFinalOPendiente);
                $trazabilidad= $this->registrarTrazabilidad($request->idRequerimiento,$request->aprobacionFinalOPendiente,$request->idUsuarioAprobante, $nombreCompletoUsuarioRevisaAprueba, $request->accion);

                // $this->enviarNotificacionPorAprobacion($requerimiento,$request->sustento,$nombreCompletoUsuarioPropietarioDelDocumento,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$trazabilidad);

            }
            //  ======= fin tipo requerimiento b/s =======

            //  ======= inicio tipo requerimiento pago =======
            if($request->idTipoDocumento ==11){
                if($request->idRequerimientoPago > 0){
                    $requerimientoPago = RequerimientoPago::find($request->idRequerimientoPago);
                }else{
                    $obtenerId = $this->obtenerRelacionadoAIdDocumento($request->idTipoDocumento,$request->idDocumento);
                    if($obtenerId['estado']=='success'){
                        $requerimientoPago = RequerimientoPago::find($obtenerId['id']);
                    }else{
                        return response()->json(['id_aprobacion' => 0, 'notificacion_por_emial' => false, 'mensaje' => 'Hubo un problema al guardar la respuesta. Mensaje de error:'.$obtenerMontoTotal['mensaje']]);
                    }
                }

                
                $this->actualizarEstadoRequerimientoPago($request->accion,$requerimientoPago,$request->aprobacionFinalOPendiente);
                // $trazabilidad= $this->registrarTrazabilidad($request->idRequerimiento,$request->aprobacionFinalOPendiente,$request->idUsuarioAprobante, $nombreCompletoUsuarioRevisaAprueba, $request->accion);

                // $this->enviarNotificacionPorAprobacion($requerimientoPago,$request->sustento,$nombreCompletoUsuarioPropietarioDelDocumento,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$trazabilidad);


            }
            //  ======= fin tipo requerimiento pago =======


            $accionNext=0;
            $aprobacionFinalOPendiente='';

            if(($request->tieneRolConSiguienteAprobacion) === true || ($request->tieneRolConSiguienteAprobacion) === 'true'){ // si existe un siguiente flujo de aprobacion con el mismo rol
 
                if($request->accion==1 || $request->accion ==5){ // si accion es revisar/aprobar, buscar siguientes aprobaciones con mismo rol de usuario para auto aprobación

                    $allRol = Auth::user()->getAllRol();
                    $idRolUsuarioList = [];
                    foreach ($allRol as  $rol) {
                        $idRolUsuarioList[] = $rol->id_rol;
                    }

                    $flujoTotal = Flujo::getIdFlujo($request->idOperacion)['data'];
                    $tamañoFlujo = $flujoTotal ? count($flujoTotal) : 0;

                    $ordenActual=0;
                    foreach ($flujoTotal as $flujo) {
                        if($flujo->id_flujo == $request->idFlujo){
                            $ordenActual=$flujo->orden;
                        }
                    }

                    if($ordenActual>0){
                        $i=1;
                        foreach ($flujoTotal as $flujo) {
                            if($i<=$tamañoFlujo){
                                if($flujo->orden == (intval($ordenActual)+$i)){
                                    if(in_array($flujo->id_rol, $idRolUsuarioList) == true){
                                        // guardar aprobación
                                        if($flujo->orden ==$tamañoFlujo ){
                                            $accionNext =1;
                                            $aprobacionFinalOPendiente='APROBACION_FINAL';
                                            $nombreAccion='Aprobado';

                                        }else{
                                            $accionNext =5;
                                            $aprobacionFinalOPendiente='PENDIENTE';
                                            $nombreAccion='Pendiente Aprobación';

                                        }
                                        $aprobacion= $this->registrarRespuesta($accionNext, $flujo->id_flujo, $request->idDocumento, $request->idTipoDocumento,$request->idRequerimiento, $request->idRequerimientoPago,$request->idUsuarioAprobante,$request->sustento, $flujo->id_rol);

                                        if($request->idTipoDocumento ==1){ //documento de tipo: requerimiento b/s
                                            $trazabilidad= $this->registrarTrazabilidad($request->idRequerimiento,$aprobacionFinalOPendiente,$request->idUsuarioAprobante, $nombreCompletoUsuarioRevisaAprueba, $accionNext);
                                            $this->actualizarEstadoRequerimiento($accionNext,$requerimiento,$aprobacionFinalOPendiente);
                                            // $this->enviarNotificacionPorAprobacion($requerimiento,$request->sustento,$nombreCompletoUsuarioPropietarioDelDocumento,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$trazabilidad);

                                        }elseif($request->idTipoDocumento ==11){//documento de tipo: requerimiento pago
                                            // $trazabilidad= $this->registrarTrazabilidad($request->idRequerimiento,$aprobacionFinalOPendiente,$request->idUsuarioAprobante, $nombreCompletoUsuarioRevisaAprueba, $accionNext);
                                            $this->actualizarEstadoRequerimientoPago($accionNext,$requerimientoPago,$aprobacionFinalOPendiente);

                                            // $this->enviarNotificacionPorAprobacion($requerimiento,$request->sustento,$nombreCompletoUsuarioPropietarioDelDocumento,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$trazabilidad);
                                        }
                                    }
                                    $i++;
                                }
                            }

                        }
                    }
                }
            }
            if ($request->accion > 0) {
 
                // $seNotificaraporEmail = true;
                // TO-DO NOTIFICAR AL USUARIO QUE SU REQUERIMIENTO FUE APROBADO
                // $correoDestinatario = [];
                $idUsuarioDestinatario=[];
                $codigoRequerimiento='';
                $documentoInternoId='';
                $documentoId='';
                if (config('app.debug')) {
                    // $correoDestinatario[] = config('global.correoDebug1');
                    if($request->idTipoDocumento ==1){ //documento de tipo: requerimiento b/s
                        $idUsuarioDestinatario[] = Auth::user()->id_usuario;
                        $codigoRequerimiento = $requerimiento->codigo??'';
                        $documentoInternoId= 1;
                        $documentoId=$requerimiento->id_requerimiento;


                    }elseif($request->idTipoDocumento ==11){//documento de tipo: requerimiento pago
                        $idUsuarioDestinatario[] = Auth::user()->id_usuario;
                        $codigoRequerimiento = $requerimientoPago->codigo??'';
                        $documentoInternoId= 11;
                        $documentoId=$requerimientoPago->id_requerimiento_pago;

                    }

                } else {
                    if($request->idTipoDocumento ==1){ //documento de tipo: requerimiento b/s
                        // $correoDestinatario[] = Usuario::withTrashed()->find($requerimiento->id_usuario)->email;
                        $idUsuarioDestinatario[] = $requerimiento->id_usuario;
                        $codigoRequerimiento = $requerimiento->codigo??'';
                        $documentoInternoId= 1;
                        $documentoId=$requerimiento->id_requerimiento;

                    }elseif($request->idTipoDocumento ==11){//documento de tipo: requerimiento pago
                        // $correoDestinatario[] = Usuario::withTrashed()->find($requerimientoPago->id_usuario)->email;
                        $idUsuarioDestinatario[] = $requerimientoPago->id_usuario;
                        $codigoRequerimiento = $requerimientoPago->codigo??'';
                        $documentoInternoId= 11;
                        $documentoId=$requerimientoPago->id_requerimiento_pago;

                    }

                }

                // Debugbar::info($codigoRequerimiento);

                $mensajeNotificacion = $codigoRequerimiento.' '.$nombreAccion.' por '.$nombreCompletoUsuarioRevisaAprueba.($request->sustento !=null?(', observación: '.$request->sustento):'');
                NotificacionHelper::notificacionRequerimiento($idUsuarioDestinatario,$mensajeNotificacion,$documentoInternoId,$documentoId);
                
                if($nombreAccion == 'Aprobado'){
                    NotificacionHelper::notificacionRequerimiento([78,75,122,5],$mensajeNotificacion,$documentoInternoId,$documentoId);
                }
                // if($request->idTipoDocumento ==1){ //documento de tipo: requerimiento b/s
                    // Mail::to($correoDestinatario)->send(new EmailNotificarUsuarioPropietarioDeDocumento($request->idTipoDocumento,$requerimiento,$request->sustento,$nombreCompletoUsuarioPropietarioDelDocumento,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$nombreAccion));
                // }elseif($request->idTipoDocumento ==11){ //documento de tipo: requerimiento pago
                    // Mail::to($correoDestinatario)->send(new EmailNotificarUsuarioPropietarioDeDocumento($request->idTipoDocumento,$requerimientoPago,$request->sustento,$nombreCompletoUsuarioPropietarioDelDocumento,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$nombreAccion));

                // }

            }

            // $seNotificaraporEmail = true;
            DB::commit();
            return response()->json(['id_aprobacion' => $aprobacion['data']->id_aprobacion, 'mensaje'=>$aprobacion['mensaje']]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_aprobacion' => 0, 'notificacion_por_emial' => false, 'mensaje' => 'Hubo un problema al guardar la respuesta. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }
}
