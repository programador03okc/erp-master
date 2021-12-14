@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Gestión de Despachos Internos
@endsection

@section('estilos')
{{-- <link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}"> --}}
{{-- <link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"> --}}
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Distribución</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="listaOrdenesDespacho">
    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <form id="formFiltrosDespachoInterno" method="POST" target="_blank" action="{{route('almacen.movimientos.pendientes-ingreso.ordenesPendientesExcel')}}">
                    @csrf()
                    <input type="hidden" name="select_mostrar" value="0">
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" id="requerimientosEnProceso">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th></th>
                                    <th width="8%">Cod.Req.</th>
                                    <th>Fecha Despacho</th>
                                    <th>Orden Elec.</th>
                                    <th>Cod.CP</th>
                                    <th>Cliente/Entidad</th>
                                    <th>Generado por</th>
                                    <th>Sede Req.</th>
                                    {{-- <th>Estado</th> --}}
                                    <th>O.T.</th>
                                    <th>D.I.</th>
                                    {{-- <th>Estado O.T.</th> --}}
                                    <th>Estado D.I.</th>
                                    <th width="30px">Acción</th>
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
@include('tesoreria.facturacion.archivos_oc_mgcp')

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') }}"></script>

<script src="{{ asset('js/almacen/distribucion/ordenesDespachoInterno.js')}}"></script>
<script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js')}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        listarRequerimientosPendientes();
    });
</script>
@endsection