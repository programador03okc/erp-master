@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
Reportes de ordenes servicio
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Reportes</li>
    <li class="active">Ordenes de servicio</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="reporte_ordenes_servicio">
    @if (in_array(275,$array_accesos))
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" id="listaOrdenesServicio">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Req.</th>
                            <th style="text-align:center;">Orden compra</th>
                            <th style="text-align:center;">Cod. Softlink</th>
                            <th style="text-align:center;">Empresa - Sede</th>
                            <th style="text-align:center;">Estado</th>
                            <th style="text-align:center;">Fecha generación OS</th>
                            <th style="text-align:center;">Fecha entrega servicio</th>
                            <th style="text-align:center;">Observacion</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
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
<script src="{{('/js/logistica/reportes/ordenesServicio.js')}}?v={{filemtime(public_path('/js/logistica/reportes/ordenesServicio.js'))}}"></script>



<script>
    var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function() {
        seleccionarMenu(window.location);
        const ordenesServicio = new OrdenesServicio();
        ordenesServicio.mostrar();
        ordenesServicio.initializeEventHandler();
    });
</script>

@endsection
