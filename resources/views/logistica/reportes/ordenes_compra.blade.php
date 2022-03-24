@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
Reportes de ordenes compra
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Reportes</li>
    <li class="active">Ordenes de compra</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="reporte_ordenes_compra">
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" id="listaOrdenesCompra">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Req.</th>
                            <th style="text-align:center;">Cuadro costos</th>
                            <th style="text-align:center;">Orden compra</th>
                            <th style="text-align:center;">Cod. Softlink</th>
                            <th style="text-align:center;">Empresa - Sede</th>
                            <th style="text-align:center;">Estado</th>
                            <th style="text-align:center;">Fecha vencimiento CC</th>
                            <th style="text-align:center;">Estado aprobación CC</th>
                            <th style="text-align:center;">Fecha aprobación CC</th>
                            <th style="text-align:center;">Días de atención CC</th>
                            <th style="text-align:center;">Condición</th>
                            <th style="text-align:center;">Fecha generación OC</th>
                            <th style="text-align:center;">Días de entrega</th>
                            <th style="text-align:center;">Condición 2</th>
                            <th style="text-align:center;">Fecha entrega</th>
                            <th style="text-align:center;">Observacion</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
    </div>
</div>




@include('logistica.reportes.modal_filtro_reporte_ordenes_compra')

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
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/datetime-moment.js') }}"></script>
<script src="{{('/js/logistica/reportes/ordenesCompra.js')}}?v={{filemtime(public_path('/js/logistica/reportes/ordenesCompra.js'))}}"></script>



<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        const ordenesCompra = new OrdenesCompra();
        ordenesCompra.mostrar();
        ordenesCompra.initializeEventHandler();
    });
</script>

@endsection