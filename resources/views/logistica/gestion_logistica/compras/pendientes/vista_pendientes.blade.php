@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
Requerimientos pendientes
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Compras</li>
    <li class="active">Requerimientos pendientes</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_compras_pendientes">
    <div class="row">
        <div class="col-md-12">
            <div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#requerimientos_pendientes" aria-controls="requerimientos_pendientes" role="tab" data-toggle="tab">Requerimientos pendientes</a></li>
                    <!-- <li role="presentation" class=""><a href="#buena_pros_pendientes"  aria-controls="buena_pros_pendientes" role="tab" data-toggle="tab" >Buena pro's pendientes</a></li> -->
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="requerimientos_pendientes">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form id="form-requerimientosPendientes" type="register">
                                            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaRequerimientosPendientes">
                                                <thead>
                                                    <tr>
                                                        <th hidden>Id</th>
                                                        <th style="text-align:center;">Selec.</th>
                                                        <th style="text-align:center;">Empresa - Sede</th>
                                                        <th style="text-align:center;">Código</th>
                                                        <th style="text-align:center;">Fecha creación</th>
                                                        <th style="text-align:center;">Fecha limite</th>
                                                        <th style="text-align:center;">Concepto</th>
                                                        <th style="text-align:center;">Tipo Req.</th>
                                                        <th style="text-align:center;">División</th>
                                                        <th style="text-align:center;">Solicitado por</th>
                                                        <th style="text-align:center;">Estado</th>
                                                        <th style="text-align:center;">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-12 right">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="buena_pros_pendientes">
                        <div class="panel panel-default">
                            <div class="panel-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('logistica.requerimientos.mapeo.mapeoItemsRequerimiento')
@include('logistica.requerimientos.mapeo.mapeoAsignarProducto')

@include('logistica.gestion_logistica.compras.pendientes.modal_observar_requerimiento_logistica')
@include('logistica.gestion_logistica.compras.pendientes.modal_filtro_requerimientos_pendientes')
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

<script src="{{('/js/logistica/orden/RequerimientoPendienteModel.js')}}?v={{filemtime(public_path('/js/logistica/orden/RequerimientoPendienteModel.js'))}}"></script>
<script src="{{('/js/logistica/orden/RequerimientoPendienteView.js')}}?v={{filemtime(public_path('/js/logistica/orden/RequerimientoPendienteView.js'))}}"></script>
<script src="{{('/js/logistica/orden/RequerimientoPendienteController.js')}}?v={{filemtime(public_path('/js/logistica/orden/RequerimientoPendienteController.js'))}}"></script>

<script src="{{ asset('js/logistica/mapeo/mapeoItemsRequerimiento.js')}}?v={{filemtime(public_path('js/logistica/mapeo/mapeoItemsRequerimiento.js'))}}"></script>
<script src="{{ asset('js/logistica/mapeo/mapeoAsignarProducto.js')}}?v={{filemtime(public_path('js/logistica/mapeo/mapeoAsignarProducto.js'))}}"></script>


<script>

    $(document).ready(function() {

        $.fn.dataTable.moment('DD-MM-YYYY HH:mm');
        $.fn.dataTable.moment('DD-MM-YYYY');

        seleccionarMenu(window.location);

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let tab = $(e.target).attr("href") // activated tab
            // console.log('tab: '+tab);

            if (tab=='#seleccionar'){
                $('#productosSugeridos').DataTable().ajax.reload();
                $('#productosCatalogo').DataTable().ajax.reload();
            }
        });

        const requerimientoPendienteModel = new RequerimientoPendienteModel();
        const requerimientoPendienteController = new RequerimientoPendienteCtrl(requerimientoPendienteModel);
        const requerimientoPendienteView = new RequerimientoPendienteView(requerimientoPendienteController);

        requerimientoPendienteView.renderRequerimientoPendienteList('SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO');
        requerimientoPendienteView.initializeEventHandler();

    });
</script>

@endsection