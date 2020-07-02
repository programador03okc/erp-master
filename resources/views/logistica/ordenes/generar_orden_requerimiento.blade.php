@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Generar Orden por Requerimiento
@endsection

@section('content')
<div class="page-main" type="orden-requerimiento">

    <legend class="mylegend">
        <h2>Generar Orden</h2>
    </legend>

        <div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#requerimientosPendientes" aria-controls="requerimientosPendientes" role="tab" data-toggle="tab">Requerimientos Pendientes</a></li>
                        <li role="presentation" class=""><a href="#requerimientosAtendidos" onClick="vista_extendida(); updateTableRequerimientoAtendidos();" aria-controls="requerimientosAtendidos" role="tab" data-toggle="tab">Requerimientos Atentidos</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="requerimientosPendientes">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaRequerimientosPendientes">
                                            <thead>
                                                <tr>
                                                <th hidden></th>
                                                <th width="50">Código</th>
                                                <th width="250">Concepto</th>
                                                <th width="100">Sede</th>
                                                <th width="50">Fecha Registro</th>
                                                <th width="50">ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="requerimientosAtendidos">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaRequerimientosAtendidos">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 </div>
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
    });
    </script>
@endsection