@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Generar Orden por Requerimiento
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
                        <li role="presentation" class=""><a href="#requerimientosAtendidos" onClick="vista_extendida();" aria-controls="requerimientosAtendidos" role="tab" data-toggle="tab">Compras Atentidas</a></li>
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
                                            id="listaRequerimientosPendientes">
                                                <thead>
                                                    <tr>
                                                    <th hidden>Id</th>
                                                    <th>Check</th>
                                                    <th>Código</th>
                                                    <th>Concepto</th>
                                                    <th>Tipo Req.</th>
                                                    <th>Tipo Cliente</th>
                                                    <th>Proveedor/Entidad</th>
                                                    <th>Empresa - Sede</th>
                                                    <th>Autor</th>
                                                    <th>Estado</th>
                                                    <th>Fecha</th>
                                                    <th>Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-12 right">
                                                    <button class="btn btn-warning" type="button" id="btnCrearOrdenCompra" onClick="openModalCrearOrdenCompra();" disabled>
                                                        Crear Orden <i class="fas fa-file-invoice"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="requerimientosAtendidos">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <form id="form-requerimientosAtendidos" type="register">
                                        <table class="mytable table table-condensed table-bordered table-okc-view" id="listaRequerimientosAtendidos">
                                            <thead>
                                                <tr>
                                                <th hidden></th>
                                                <th width="50">Código Requerimiento</th>
                                                <th width="250">Concepto</th>
                                                <th width="50">Fecha Requerimiento</th>
                                                <th width="50">Código Orden Softlink</th>
                                                <th width="100">Sede</th>
                                                <th width="50">Fecha Orden</th>
                                                <th width="20">ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 </div>
@include('logistica.ordenes.modal_detalle_orden_atendido')
@include('logistica.ordenes.modal_orden_requerimiento')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('logistica.ordenes.ordenesModal')
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
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{('/js/logistica/generar_orden.js')}}"></script>
    <script src="{{('/js/logistica/proveedorModal.js')}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}"></script>
    <script src="{{('/js/logistica/orden_requerimiento.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        inicializar(
            "{{route('logistica.gestion-logistica.orden.por-requerimiento.requerimientos-pendientes')}}",
            "{{route('logistica.gestion-logistica.orden.por-requerimiento.requerimientos-atendidos')}}",
            "{{route('logistica.gestion-logistica.orden.por-requerimiento.requerimiento-orden')}}",
            "{{route('logistica.gestion-logistica.orden.por-requerimiento.guardar')}}",
            "{{route('logistica.gestion-logistica.orden.por-requerimiento.revertir')}}"
            );
            tieneAccion('{{Auth::user()->tieneAccion(114)}}','{{Auth::user()->tieneAccion(115)}}');
    });
    </script>
@endsection