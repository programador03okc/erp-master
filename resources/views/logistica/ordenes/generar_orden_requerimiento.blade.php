@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Pendientes
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
    <li>Ordenes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="orden-requerimiento">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#requerimientosPendientes" aria-controls="requerimientosPendientes" role="tab" data-toggle="tab">Compras Pendientes</a></li>
                        <li role="presentation" class=""><a href="#comprasEnProceso" onClick="vista_extendida();" aria-controls="comprasEnProceso" role="tab" data-toggle="tab">En Proceso</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="requerimientosPendientes">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form id="form-requerimientosPendientes" type="register">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5>Empresa</h5>
                                                    <div style="display:flex;">
                                                    <select class="form-control" id="id_empresa_select_req" onChange="requerimientoPendienteView.handleChangeFilterReqByEmpresa(event);">
                                                            <option value=null>Todas las Empresas</option>
                                                            @foreach ($empresas as $emp)
                                                                <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>Sede</h5>
                                                    <div style="display:flex;">
                                                    <select class="form-control" id="id_sede_select_req" onChange="requerimientoPendienteView.handleChangeFilterReqBySede(event);" disabled>
                                                            <option value="0">Elija una opción</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>&nbsp;</h5>
                                                        <input type="checkbox" id="incluir_sede" onchange="requerimientoPendienteView.handleChangeIncluirSede(event)" /> Inlcuir Sede
                                                </div>
                                            </div>
                                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                            id="listaRequerimientosPendientes">
                                                <thead>
                                                    <tr>
                                                    <th hidden>Id</th>
                                                    <th>Check</th>
                                                    <th>Código</th>
                                                    <th>Concepto</th>
                                                    <th>Fecha creación</th>
                                                    <th>Tipo Req.</th>
                                                    <th>Proveedor/Entidad</th>
                                                    <th>Empresa - Sede</th>
                                                    <th>Autor</th>
                                                    
                                                    <th>Estado</th>
                                                    <th width="130px">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-12 right">
                                                    <button class="btn btn-warning" type="button" id="btnCrearOrdenCompra" onClick="crearOrdenCompra();" disabled>
                                                        Crear Orden <i class="fas fa-file-invoice"></i>
                                                    </button>
 
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="comprasEnProceso">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <form id="form-comprasEnProceso" type="register">
                                        <table class="mytable table table-condensed table-bordered table-okc-view" id="listaComprasEnProceso">
                                            <thead>
                                                <tr>
                                                <th width="10">#</th>
                                                <th width="20">OC</th>
                                                <th width="20">OC Softlink</th>
                                                <th width="20">OCAM</th>
                                                <th width="20">Cliente</th>
                                                <th width="30">Mayorista</th>
                                                <th width="20">Marca</th>
                                                <th width="20">Categoría</th>
                                                <th width="20">Part Number</th>
                                                <th width="50">Descripción</th>
                                                <th width="20">Fecha</th>
                                                <th width="20">Transito</th>
                                                <th width="20">ETA</th>
                                                <th width="30">Sede - Empresa</th>
                                                <th width="20">Estado</th>
                                                <th width="30">Observación</th>
                                                <th width="40">ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </form>
                                    <h6>Leyenda: Estados de Orden:</h6>
                                    <span class="label label-info">Enviado</span>
                                    <span class="label label-primary">Confirmada</span>
                                    <span class="label label-danger">Facturada</span>
                                    <span class="label label-primary">Despachado</span>
                                    <span class="label label-warning">En Transito</span>
                                    <span class="label label-danger">En Almacen Parcial</span>
                                    <span class="label label-primary">En Almacen Total</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 </div>
 @include('logistica.ordenes.modal_ver_cuadro_costos')
@include('logistica.ordenes.modal_documentos_vinculados')
@include('logistica.ordenes.modal_agregar_items_requerimiento')
@include('logistica.ordenes.modal_atender_con_almacen')
@include('logistica.requerimientos.modal_catalogo_items')
@include('logistica.requerimientos.modal_crear_nuevo_producto')
@include('logistica.ordenes.modal_ver_orden')
@include('logistica.ordenes.modal_editar_estado_orden')
@include('logistica.ordenes.modal_editar_estado_detalle_orden')
@include('logistica.ordenes.modal_agregar_items_para_compra')
@include('logistica.ordenes.modal_orden_requerimiento')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('logistica.ordenes.ordenesModal')
@include('logistica.requerimientos.modal_vincular_item_requerimiento')
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{('/js/logistica/orden/RequerimientoPendienteModel.js')}}"></script>
    <script src="{{('/js/logistica/orden/RequerimientoPendienteView.js')}}"></script>
    <script src="{{('/js/logistica/orden/RequerimientoPendienteController.js')}}"></script>
    <script src="{{('/js/logistica/CustomTabla.js')}}"></script>
    <!-- <script src="{{('/js/logistica/proveedorModal.js')}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}"></script>
    <script src="{{('/js/logistica/orden/orden_requerimiento.js')}}"></script>
    <script src="{{('/js/logistica/orden/modal_items_para_compra.js')}}"></script>
    <script src="{{('/js/logistica/crear_nuevo_producto.js')}}"></script> -->
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        // inicializar(
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.requerimientos-pendientes')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.ordenes-en-proceso')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.revertir')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.actualizar-estado-orden')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.actualizar-estado-detalle-orden')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.actualizar-estado-detalle-requerimiento')}}",
        //     "{{route('logistica.gestion-logistica.cotizacion.gestionar.select-sede-by-empresa')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.documentos-vinculados')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.tiene-items-para-compra')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.guardar-items-detalle-requerimiento')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.guardar-atencion-con-almacen')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.generar-orden-por-requerimiento-pdf')}}"

        //     );
        // inicializarModalItemsParaCompra(
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.lista_items-cuadro-costos-por-requerimiento')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.buscar-item-catalogo')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.grupo-select-item-para-compra')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.guardar-items-detalle-requerimiento')}}"
        // );
        // inicializarModalOrdenRequerimiento(
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.detalle-requerimiento-orden')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.guardar')}}"

        // );
            // tieneAccion('{{Auth::user()->tieneAccion(114)}}','{{Auth::user()->tieneAccion(115)}}');
    });
    </script>
@endsection