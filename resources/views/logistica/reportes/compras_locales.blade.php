@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
Reportes de compras locales
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Reportes</li>
    <li class="active">Compras locales</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="reporte_compras_locales">
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" id="listaComprasLocales">
                    <thead>
                        <tr>
                            <th style="text-align:center; width:10%;">Bien comprado/ servicio contratado</th>
                            <th style="text-align:center; width:10%;">Razón Social del Proveedor</th>
                            <th style="text-align:center; width:5%;">RUC del Proveedor</th>
                            <th style="text-align:center; width:10%;">Domicilio Fiscal/Principal</th>
                            <th style="text-align:center; width:10%;">Provincia</th>
                            <th style="text-align:center; width:5%;">Fecha de presentación del comprobante de pago.</th>
                            <th style="text-align:center; width:5%;">Fecha de cancelación del comprobante de pago</th>
                            <th style="text-align:center; width:5%;">Tiempo de cancelación(# días)</th>
                            <th style="text-align:center; width:10%;">Moneda</th>
                            <th style="text-align:center; width:10%;">Monto Soles inc IGV</th>
                            <th style="text-align:center; width:10%;">Monto Dólares inc IGV</th>
                            <th style="text-align:center; width:10%;">Tipo de Comprobante de Pago</th>
                            <th style="text-align:center; width:10%;">N° Comprobante de Pago</th>
                            <th style="text-align:center; width:10%;">Empresa - sede</th>
                            <th style="text-align:center; width:10%;">Grupo</th>
                     
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

<script src="{{('/js/logistica/reportes/comprasLocales.js')}}?v={{filemtime(public_path('/js/logistica/reportes/comprasLocales.js'))}}"></script>


<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        const comprasLocales = new ComprasLocales();
        comprasLocales.mostrar();
        comprasLocales.initializeEventHandler();
    });
</script>

@endsection