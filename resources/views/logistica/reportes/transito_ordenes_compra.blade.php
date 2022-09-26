@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
Reportes de transito de ordenes de compra
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Reportes</li>
    <li class="active">Transito de ordenes de compra</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="reporte_transito_ordenes_compra">
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" id="listaTransitoOrdenesCompra">
                    <thead>
                        <tr>
                            <th style="text-align:center; width:8%;">Cuadro presupuestp</th>
                            <th style="text-align:center; width:10%;">Proveedor</th>
                            <th style="text-align:center; width:8%;">Orden compra</th>
                            <th style="text-align:center; width:8%;">Fecha creación</th>
                            <th style="text-align:center; width:8%;">Empresa - Sede</th>
                            <th style="text-align:center; width:5%;">Monto (inc. IGV)</th>
                            <th style="text-align:center; width:5%;">Estado</th>
                            <th style="text-align:center; width:8%;">ETA</th>
                            <th style="text-align:center; width:5%;">Transformaciones</th>
                            <th style="text-align:center; width:40%;">Cantidad de equipos</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
    </div>
</div>



@include('logistica.reportes.modal_filtro_reporte_transito_ordenes_compra')


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

<script src="{{('/js/logistica/reportes/transitoOrdenesCompra.js')}}?v={{filemtime(public_path('/js/logistica/reportes/transitoOrdenesCompra.js'))}}"></script>


<script>
    var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function() {
        seleccionarMenu(window.location);
        const transitoOrdenesCompra = new TransitoOrdenesCompra();
        transitoOrdenesCompra.mostrar();
        transitoOrdenesCompra.initializeEventHandler();
    });
</script>

@endsection
