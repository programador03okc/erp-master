@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
    Gestión de Cotizaciones
@endsection

@section('content')
<div class="page-main" type="cotizacion">
    <legend>
        <h2>Gestionar Cotizaciones</h2>
    </legend>
    <form id="form-cotizacion" type="register" form="formulario">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#createNewCoti" aria-controls="createNewCoti" role="tab" data-toggle="tab">Crear Nueva Cotización</a></li>
                        <li role="presentation" class=""><a href="#cotiListByGroup" onClick="vista_extendida();" aria-controls="cotiListByGroup" role="tab" data-toggle="tab">Lista de Cotizaciones</a></li>
                    </ul>
                    

                    <!-- Tab panes -->
 

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="createNewCoti">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div>
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-pills nav-justified" role="tablist" id="menu_tab_crear_coti">
                                                <li role="presentation" class="active"><a href="#requerimiento" aria-controls="requerimiento" role="tab" data-toggle="tab">1. Selección de Requerimientos</a></li>
                                                <li role="presentation" class="disabled"><a href="#detalle_requerimiento" aria-controls="detalle_requerimiento" role="tab" data-toggle="tab">2. Selección de Items</a></li>
                                                <li role="presentation" class="disabled"><a href="#crear_coti" aria-controls="crear_coti" role="tab" data-toggle="tab">3. Generar Cotización</a></li>
                                                <!-- <li role="presentation" class="disabled"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">4. Envio</a></li> -->
                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content" id="contenido_tab_crear_coti">
                                                <div role="tabpanel" class="tab-pane active" id="requerimiento">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <h5>Buscar y Seleccionar Requerimiento(s)</h5>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <h5>Empresa</h5>
                                                                    <div style="display:flex;">
                                                                    <select class="form-control" id="id_empresa_select_req" onChange="handleChangeFilterReqByEmpresa(event);">
                                                                            <option value="0" disabled>Elija una opción</option>
                                                                            @foreach ($empresas as $emp)
                                                                                <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <h5>Sede</h5>
                                                                    <div style="display:flex;">
                                                                    <select class="form-control" id="id_sede_select_req" onChange="handleChangeFilterReqBySede(event);" disabled>
                                                                            <option value="0">Elija una opción</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <h5>&nbsp;</h5>
                                                                        <input type="checkbox" id="incluir_sede" onchange="handleChangeIncluirSede(event)" /> Inlcuir Sede
                                                                </div>
                                                            </div>
                                                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                                            id="listaRequerimientoPendientes">
                                                                <thead>
                                                                    <tr>
                                                                        <th hidden>Id</th>
                                                                        <th>Check</th>
                                                                        <th>Código</th>
                                                                        <th>Concepto</th>
                                                                        <th>Area</th>
                                                                        <th>Estado</th>
                                                                        <th>Cotización</th>
                                                                        <th>Fecha</th>
                                                                        <th>Acción</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                            <div class="row">
                                                                <div class="col-md-12 right">
                                                                <button class="btn btn-info" role="button"   id="btnAllowCheckBoxListReq" onClick="allowCheckBoxListReq(event);">
                                                                    Volver a Iniciar <i class="fas fa-undo-alt"></i>
                                                                </button>
                                                                <button class="btn btn-warning" role="button"   id="btnGotToSecondTab" onClick="gotToSecondTab(event);" disabled>
                                                                    Siguiente <i class="fas fa-chevron-circle-right"></i>
                                                                </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="detalle_requerimiento">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <h5>Seleccionar Items</h5>
                                                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                                                id="listaItemsRequerimiento" width="100%"> 
                                                                <thead>
                                                                    <tr>
                                                                        <th hidden>Id</th>
                                                                        <th width="20">Check</th>
                                                                        <th width="20">#</th>
                                                                        <th width="120">COD.REQ.</th>
                                                                        <th width="120">COD. ITEM</th>
                                                                        <th width="400">DESCRIPCIÓN</th>
                                                                        <th width="100">UNIDAD</th>
                                                                        <th width="100">CANTIDAD</th>
                                                                        <th width="100">PRECIO REF.</th>
                                                                        <th width="100">FECHA ENTREGA</th>
                                                                        <th width="100">LUGAR ENTREGA</th>
                                                                        <th width="100">ACTUALIZAR CANTIDAD</th>
                                                                        <th width="200">SALDOS</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>

                                                            <div class="row">
                                                                <div class="col-md-12 right">
                                                                <button class="btn btn-default" role="button"   onClick="gotToSecondToFirstTab(event);">
                                                                        Atras <i class="fas fa-arrow-circle-left"></i>
                                                                </button>
                                                                <button class="btn btn-warning" role="button"   id="btnGotToThirdTab" onClick="gotToThirdTab(event);" disabled>
                                                                    Siguiente <i class="fas fa-chevron-circle-right"></i>
                                                                </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="crear_coti">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <h5>Crear Cotización</h5>
 
                                                            <input class="oculto" name="id_cotizacion"/>
                                                            <input type="hidden" name="id_grupo_cotizacion" value="0" primary="ids">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                        <div class="col-md-3">
                                                                            <h5>Código Cuadro Comparativo</h5>
                                                                            <div style="display:flex;">
                                                                                <input type="text" class="form-control" name="codigo_cuadro_comparativo" placeholder="CC-#####" readOnly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-9 text-right">
                                                                            <h5>&nbsp;</h5>
                                                                            <button class="btn btn-success" id="exampleInputName3" type="button" onClick="nuevaSolicitudCotizacion();">
                                                                                <i class="fas fa-plus"></i> Crear Solicitud de Cotización 
                                                                            </button>
                                                                        </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <table class="mytable table table-striped table-condensed table-bordered table-sm" id="">
                                                                        <thead>
                                                                            <tr>
                                                                                <th width='30px'>#</th>
                                                                                <th>Cotización</th>
                                                                                <th>Proveedor</th>                                                                        
                                                                                <th>Req. Vinculados</th>
                                                                                <th>Estado</th>
                                                                                <th width='150px'>Acción</th>                                                                        
                                                                            </tr>    
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td colspan="6" class="text-center">No existe cotizaciones ingresadas</td>
                                                                            </tr>
                                                                            <!-- <tr>
                                                                                <td>1</td>
                                                                                <td>C0001</td>
                                                                                <td>Holitas</td>
                                                                                <td>RE011</td>
                                                                                <td>Enviado?</td>
                                                                                <td>
                                                                                <center>
                                                                                <div class="btn-group" role="group" style="margin-bottom: 5px;">
                                                                                    <button type="button" class="btn btn-sm btn-log btn-primary" title="Ver detalle" onclick="viewFlujo(3, 21);"><i class="fas fa-eye fa-xs"></i></button>
                                                                                    <button type="button" class="btn btn-sm btn-log btn-warning" title="Editar cotización" onclick="tracking_requerimiento(3);"><i class="fas fa-edit fa-xs"></i></button>
                                                                                    <button type="button" class="btn btn-sm btn-log btn-info" title="Enviar cotización" onclick="tracking_requerimiento(3);"><i class="far fa-paper-plane"></i></button>
                                                                                    <button type="button" class="btn btn-sm btn-log btn-danger" title="Eliminar cotización" onclick="viewFlujo(3, 21);"><i class="fas fa-trash fa-xs"></i></button>
                                                                                </div>
                                                                                </center>
                                                                                </td>
                                                                            </tr> -->
                                                                        </tbody>
                                                                    </table>
                                                                </div>   
                                                            </div>   
                                                            
                                                            <div class="row">
                                                                <div class="col-md-12 right">
                                                                <button class="btn btn-default" role="button"   onClick="gotToThirdToSecondTab(event);">
                                                                        Atras <i class="fas fa-arrow-circle-left"></i>
                                                                </button>
                                                                <button class="btn btn-info" role="button" id="btnOpenModalEnviarCoti"  onClick="openModalEnviarCoti(event);" disabled>
                                                                        Enviar Cotización <i class="far fa-envelope"></i>
                                                                </button>
                                                                <button class="btn btn-default" role="button"   id="btnResetProcessCreateCoti" onClick="resetProcessCreateCoti(event);" disabled>
                                                                    Reiniciar & Volver a Paso 1 <i class="fas fa-redo-alt"></i></i>
                                                                </button>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div role="tabpanel" class="tab-pane" id="settings">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <h5>Envio</h5>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                </div>
                                                                <div class="col-md-12 right">
                                                                <button class="btn btn-default" role="button"   onClick="gotToFourthToThirdTab();">
                                                                        Atras <i class="fas fa-arrow-circle-left"></i>
                                                                </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- <div role="tabpanel" class="tab-pane" id="cotiListByGroup">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                    id="listaCotizacionesPorGrupo">
                                        <thead>
                                            <tr>
                                            <th hidden></th>
                                            <th hidden></th>
                                            <th hidden></th>
                                            <th hidden></th>
                                            <th hidden></th>
                                            <th width="60">CUADRO COMP.</th>
                                            <th width="60">RUC</th>
                                            <th width="250">PROVEEDOR</th>
                                            <th width="100">CORREO CONTACTO</th>
                                            <th width="100">COTIZACIÓN</th>
                                            <th width="100">REQUERIMIENTO</th>
                                            <th width="100">ESTADO</th>
                                            <th width="150">EMPRESA</th>
                                            <th width="100">FECHA REGISTRO</th>
                                            <th width="100">ACCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div> -->
 
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@include('logistica.cotizaciones.modal_nueva_solicitud_cotizacion')
@include('logistica.cotizaciones.modal_duplicar_cotizacion')
@include('logistica.cotizaciones.modal_ver_cotizacion')
@include('logistica.cotizaciones.modal_editar_cotizacion')
@include('logistica.requerimientos.modal_adjuntar_archivos_detalle_requerimiento')
@include('logistica.cotizaciones.modal_envio_cotizacion')
@include('logistica.cotizaciones.modal_adjuntos_cotizacion')
@include('logistica.cotizaciones.cotizacionModal')
@include('logistica.cotizaciones.cotizacion_proveedor')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('logistica.cotizaciones.add_contacto')
@include('logistica.cotizaciones.modal_saldos_producto')
@include('logistica.cotizaciones.modal_det_req_a_cotizar')
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{('/js/logistica/gestionar_cotizaciones.js')}}"></script>
    <script src="{{('/js/logistica/proveedorModal.js')}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}"></script>
    <script src="{{('/js/logistica/add_contacto.js')}}"></script>
    <script src="{{('/js/publico/consulta_sunat.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        inicializar(
            "{{route('logistica.gestion-logistica.cotizacion.gestionar.select-sede-by-empresa')}}"
            );
    });
    </script>
@endsection