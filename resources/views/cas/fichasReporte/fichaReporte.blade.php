@extends('layout.main')
@include('layout.menu_cas')

@section('cabecera')
Gestión de fichas reporte
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> Servicios CAS</a></li>
    <li>Garantías</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="incidencia">

    <div class="box">
        <div class="box-header with-border">

            <h3 class="box-title">Lista de incidencias</h3>
            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                        id="listaIncidencias" style="width:100%;">
                        <thead>
                            <tr>
                                <th hidden></th>
                                <th>Código</th>
                                <th>Empresa</th>
                                <th>Cliente</th>
                                <th>Concepto</th>
                                <th>Guía Venta</th>
                                <th>Nombre contacto</th>
                                <th>Telf. contacto</th>
                                <th>Cargo contacto</th>
                                <th>Dirección</th>
                                <th>Horario</th>
                                <th>Fecha reporte</th>
                                <th>Responsable</th>
                                <th>Falla</th>
                                <th>Estado</th>
                                <th width="70px">Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="box-footer">
            
        </div>
    </div>
</div>

@include('cas.fichasReporte.fichaReporteCreate')

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
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>

<script src="{{ asset('js/cas/fichasReporte/fichaReporte.js')}}?v={{filemtime(public_path('js/cas/fichasReporte/fichaReporte.js'))}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        vista_extendida();
        listarIncidencias();
    });
</script>
@endsection