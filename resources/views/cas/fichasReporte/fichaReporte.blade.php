@extends('layout.main')
@include('layout.menu_cas')

@section('cabecera')
Gestión de incidencias
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
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="incidencia">
            {{-- <div class="box">
                <div class="box-header with-border">

                    <h3 class="box-title">Lista de incidencias</h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body"> --}}
            <div class="col-md-12" id="tab-incidencias" style="padding-top:10px;padding-bottom:10px;">

                <ul class="nav nav-tabs" id="myTabIncidencias">
                    <li class="active"><a data-toggle="tab" href="#incidencias">Lista de Incidencias</span></a></li>
                    <li class=""><a data-toggle="tab" href="#devoluciones">Lista de Devoluciones</a></li>
                </ul>
                <div class="tab-content">
                    <div id="incidencias" class="tab-pane fade in active">
                        
                        <div class="row" style="padding-top:10px;">
                            <div class="col-md-12">
                                <form id="formFiltrosIncidencias" method="POST" target="_blank"
                                action="{{route('cas.garantias.fichas.incidenciasExcel')}}">
                                    @csrf()
                                </form>
                                <table class="mytable table table-condensed table-bordered table-okc-view"
                                    id="listaIncidencias" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th>Código</th>
                                            <th>Estado</th>
                                            <th>Empresa</th>
                                            <th>Cliente</th>
                                            <th>Nro Orden</th>
                                            <th>Factura</th>
                                            <th>Nombre contacto</th>
                                            {{-- <th>Telf. contacto</th>
                                            <th>Cargo contacto</th>
                                            <th>Dirección</th>
                                            <th>Horario</th> --}}
                                            <th>Fecha reporte</th>
                                            <th>Responsable</th>
                                            <th>Falla</th>
                                            <th width="70px">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="devoluciones" class="tab-pane fade ">
                        
                        <div class="row" style="padding-top:10px;">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaDevoluciones">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th width="8%">Código</th>
                                            <th width="20%">Estado</th>
                                            <th width="20%">Fecha registro</th>
                                            <th width="30%">Concepto</th>
                                            <th width="30%">Fichas Técnicas</th>
                                            <th width="10%">Elaborado Por</th>
                                            <th width="10%">Revisado Por</th>
                                            <th width="6%">Acción</th>
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

@include('cas.fichasReporte.fichaReporteCreate')
@include('almacen.devoluciones.fichaTecnicaCreate')
@include('cas.fichasReporte.cierreIncidencia')
@include('cas.fichasReporte.cancelarIncidencia')
@include('cas.fichasReporte.verDatosContacto')
@include('cas.fichasReporte.verAdjuntosFicha')
@include('almacen.devoluciones.verFichasTecnicasAdjuntas')

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>

<!-- <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>

<script src="{{ asset('js/cas/fichasReporte/fichaReporte.js')}}?v={{filemtime(public_path('js/cas/fichasReporte/fichaReporte.js'))}}"></script>
<script src="{{ asset('js/cas/fichasReporte/fichaReporteCreate.js')}}?v={{filemtime(public_path('js/cas/fichasReporte/fichaReporteCreate.js'))}}"></script>
<script src="{{ asset('js/cas/fichasReporte/cierreIncidencia.js')}}?v={{filemtime(public_path('js/cas/fichasReporte/cierreIncidencia.js'))}}"></script>
<script src="{{ asset('js/cas/fichasReporte/cancelarIncidencia.js')}}?v={{filemtime(public_path('js/cas/fichasReporte/cancelarIncidencia.js'))}}"></script>
<script src="{{ asset('js/cas/fichasReporte/verDetalleReportes.js')}}?v={{filemtime(public_path('js/cas/fichasReporte/verDetalleReportes.js'))}}"></script>
<script src="{{ asset('js/cas/fichasReporte/gestionDevoluciones.js')}}?v={{filemtime(public_path('js/cas/fichasReporte/gestionDevoluciones.js'))}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        vista_extendida();
        listarIncidencias();
        listarDevoluciones();
    });
</script>
@endsection
