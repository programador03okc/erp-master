@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
Requerimientos pendientes
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Compras</li>
    <li class="active">Requerimientos pendientes</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_compras" id="lista_compras">
    <div class="row">
        <div class="col-md-12">
            <div>
                <!-- Nav tabs -->
                @if (in_array(218,$array_accesos) || in_array(219,$array_accesos) )
                <ul class="nav nav-tabs" role="tablist">
                    @if (in_array(218,$array_accesos))
                        <li role="presentation" class="handleClickTabRequerimientosPendientes active"><a href="#requerimientos_pendientes" aria-controls="requerimientos_pendientes" role="tab" data-toggle="tab">Requerimientos pendientes</a></li>
                    @endif
                    @if (in_array(219,$array_accesos))
                    <li role="presentation" class="handleClickTabRequerimientosAtendidos"><a href="#requerimientos_atendidos"  aria-controls="requerimientos_atendidos" role="tab" data-toggle="tab" >Requerimientos atendidos</a></li>
                    @endif
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    @if (in_array(218,$array_accesos))
                    <div role="tabpanel" class="tab-pane active" id="requerimientos_pendientes">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="form-requerimientosPendientes" type="register">
                                            <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" id="listaRequerimientosPendientes">
                                                <thead>
                                                    <tr>
                                                        <th hidden>Id</th>
                                                        <th style="text-align:center;">Selec.</th>
                                                        <th style="text-align:center;">Prio.</th>
                                                        <th style="text-align:center;">Empresa - Sede</th>
                                                        <th style="text-align:center; width:10%;">Código</th>
                                                        <th style="text-align:center;">Fecha creación</th>
                                                        <th style="text-align:center;">Fecha limite</th>
                                                        <th style="text-align:center;">Concepto</th>
                                                        <th style="text-align:center;">Tipo Req.</th>
                                                        <th style="text-align:center;">División</th>
                                                        <th style="text-align:center;">Solicitado por</th>
                                                        <th style="text-align:center;">Req. creado por</th>
                                                        <th style="text-align:center;">Observación</th>
                                                        <th style="text-align:center;">Estado</th>
                                                        <th style="text-align:center;width:7%;">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-12 right">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if (in_array(219,$array_accesos))
                    <div role="tabpanel" class="tab-pane" id="requerimientos_atendidos">
                        <div class="panel panel-default">
                            <div class="panel-body">
                            <div class="row">
                                    <div class="col-md-12">
                                        <div id="form-requerimientosAtendidos">
                                            <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" id="listaRequerimientosAtendidos">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;">Empresa - Sede</th>
                                                        <th style="text-align:center; width:10%;">Código</th>
                                                        <th style="text-align:center;">Fecha creación</th>
                                                        <th style="text-align:center;">Fecha limite</th>
                                                        <th style="text-align:center;">Concepto</th>
                                                        <th style="text-align:center;">Tipo Req.</th>
                                                        <th style="text-align:center;">División</th>
                                                        <th style="text-align:center;">Solicitado por</th>
                                                        <th style="text-align:center;">Req. creado por</th>
                                                        <th style="text-align:center;">Estado</th>
                                                        <th style="text-align:center;width:7%;">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-12 right">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger pulse" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error de Accesos:</span>
                            Solicite los accesos
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('logistica.gestion_logistica.compras.pendientes.modal_por_regularizar')
@include('logistica.requerimientos.mapeo.mapeoItemsRequerimiento')
@include('logistica.requerimientos.mapeo.mapeoAsignarProducto')
@include('logistica.gestion_logistica.compras.pendientes.modal_opciones_para_regularizar_item')
@include('logistica.requerimientos.modal_ver_agregar_adjuntos_requerimiento_compra')
@include('logistica.gestion_logistica.compras.pendientes.modal_adjuntos_detalle_requerimiento')
@include('logistica.gestion_logistica.compras.pendientes.modal_observar_requerimiento_logistica')
@include('logistica.gestion_logistica.compras.pendientes.modal_filtro_requerimientos_pendientes')
@include('logistica.gestion_logistica.compras.pendientes.modal_filtro_requerimientos_atendidos')
@include('logistica.gestion_logistica.compras.pendientes.modal_ver_cuadro_costos')
@include('logistica.gestion_logistica.compras.pendientes.modal_agregar_items_requerimiento')
@include('logistica.gestion_logistica.compras.pendientes.modal_atender_con_almacen')
@include('logistica.gestion_logistica.compras.pendientes.modal_agregar_items_para_compra')
@include('logistica.gestion_logistica.compras.pendientes.modal_orden_requerimiento')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('logistica.gestion_logistica.compras.pendientes.ordenesModal')
@include('logistica.requerimientos.modal_vincular_item_requerimiento')
@include('logistica.gestion_logistica.compras.pendientes.modal_nueva_reserva')
@include('logistica.gestion_logistica.compras.pendientes.modal_historial_reserva')
@include('logistica.gestion_logistica.compras.pendientes.modal_ver_orden_de_requerimiento')

@include('logistica.gestion_logistica.compras.pendientes.modal_gestionar_estado_requerimiento')


@endsection

@section('scripts')
<script src="{{ asset('js/util.js')}}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/datetime-moment.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>

<script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>

<script src="{{('/js/logistica/orden/RequerimientoPendienteModel.js')}}?v={{filemtime(public_path('/js/logistica/orden/RequerimientoPendienteModel.js'))}}"></script>
<script src="{{('/js/logistica/orden/RequerimientoPendienteView.js')}}?v={{filemtime(public_path('/js/logistica/orden/RequerimientoPendienteView.js'))}}"></script>
<script src="{{('/js/logistica/orden/RequerimientoPendienteController.js')}}?v={{filemtime(public_path('/js/logistica/orden/RequerimientoPendienteController.js'))}}"></script>
<script src="{{('/js/logistica/orden/Regularizar.js')}}?v={{filemtime(public_path('/js/logistica/orden/Regularizar.js'))}}"></script>

<script src="{{ asset('js/logistica/mapeo/mapeoItemsRequerimiento.js')}}?v={{filemtime(public_path('js/logistica/mapeo/mapeoItemsRequerimiento.js'))}}"></script>
<script src="{{ asset('js/logistica/mapeo/mapeoAsignarProducto.js')}}?v={{filemtime(public_path('js/logistica/mapeo/mapeoAsignarProducto.js'))}}"></script>
<script src="{{ asset('js/logistica/requerimiento/verTodoAdjuntosYAdicionalesRequerimientoCompra.js')}}?v={{filemtime(public_path('js/logistica/requerimiento/verTodoAdjuntosYAdicionalesRequerimientoCompra.js'))}}"></script>


<script>
    var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function() {

        $.fn.dataTable.moment('DD-MM-YYYY HH:mm');
        $.fn.dataTable.moment('DD-MM-YYYY');

        seleccionarMenu(window.location);

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let tab = $(e.target).attr("href") // activated tab
            // console.log('tab: '+tab);

            if (tab=='#seleccionar'){
                listarProductosSugeridos($('[name=part_number]').val(), decodeURIComponent( $('[name=descripcion]').val()), 0);
                listarProductosCatalogo();
            }
        });

        const requerimientoPendienteModel = new RequerimientoPendienteModel();
        const requerimientoPendienteController = new RequerimientoPendienteCtrl(requerimientoPendienteModel);
        const requerimientoPendienteView = new RequerimientoPendienteView(requerimientoPendienteController);

        requerimientoPendienteView.renderRequerimientoPendienteList('SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO');
        requerimientoPendienteView.initializeEventHandler();

    });
</script>

@endsection
