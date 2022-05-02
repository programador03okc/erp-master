@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Estado de Atención de Requerimientos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
<style>
    #despachosPendientes_filter,
    #despachosEntregados_filter{
        margin-top:10px;
    }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Movimientos</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="requerimientosAlmacen">
            <div class="row" style="padding-top:10px;padding-right:10px;padding-left:10px;">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="mytable table table-condensed table-bordered table-okc-view" 
                            id="requerimientosAlmacen" style="width:100%;">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th>Codigo</th>
                                    <th>Estado</th>
                                    <th>Concepto</th>
                                    <th>Grupo</th>
                                    <th>Almacen</th>
                                    <th>Fecha entrega</th>
                                    <th>Registrado por</th>
                                    <th>Despacho Interno</th>
                                    <th>Despacho Externo</th>
                                    <th>Estado despacho</th>
                                    <th width="7%"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('almacen.transferencias.verTransferenciasPorRequerimiento')
@include('almacen.reportes.cambioRequerimiento')
@include('logistica.gestion_logistica.compras.pendientes.modal_ver_orden_de_requerimiento')
@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>

<script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>

<script src="{{ asset('js/almacen/reporte/requerimientosAlmacen.js') }}?v={{filemtime(public_path('js/almacen/reporte/requerimientosAlmacen.js'))}}"></script>
<script src="{{ asset('js/almacen/reporte/cambioRequerimiento.js') }}?v={{filemtime(public_path('js/almacen/reporte/cambioRequerimiento.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js?') }}?v={{filemtime(public_path('js/almacen/distribucion/verDetalleRequerimiento.js'))}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        listarRequerimientosAlmacen('{{Auth::user()->id_usuario}}');
        // $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
    //     iniciar('{{Auth::user()->tieneAccion(85)}}');
    });
</script>
@endsection