@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Atención de Salidas
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
        <div class="page-main" type="despachosPendientes">
            <div class="col-md-12" id="tab-despachosPendientes" style="padding-top:10px;padding-bottom:10px;">
                <ul class="nav nav-tabs" id="myTabDespachosPendientes">
                    <li class="active"><a data-toggle="tab" href="#pendientes">Despachos Pendientes <span id="nro_despachos" class="badge badge-info">{{$nro_od_pendientes}}</span></a></li>
                    <li class=""><a data-toggle="tab" href="#salidas">Salidas Procesadas</a></li>
                </ul>
                <div class="tab-content">
                    <div id="pendientes" class="tab-pane fade in active">
                        <form id="formFiltrosSalidasPendientes" method="POST" target="_blank" action="{{route('almacen.movimientos.pendientes-ingreso.ordenesPendientesExcel')}}">
                            @csrf()
                            <input type="hidden" name="select_mostrar_pendientes" value="0">
                        </form>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="despachosPendientes" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th>Despacho</th>
                                            <th>Cod.Req.</th>
                                            <th>Fecha Despacho</th>
                                            <th>Comentario</th>
                                            <th>OCAM</th>
                                            <th>CDP</th>
                                            <th>Cliente</th>
                                            <th>Almacén</th>
                                            <th>Estado</th>
                                            <!-- <th>Ubigeo</th>
                                            <th>Dirección Destino</th> -->
                                            {{-- <th>Fecha Entrega</th>
                                            <th>Registrado por</th> --}}
                                            <th width="90px">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div id="salidas" class="tab-pane fade ">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="despachosEntregados">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th>Orden Despacho</th>
                                            <th>Guia venta</th>
                                            <th>Fecha Salida</th>
                                            <th>Almacén</th>
                                            <th>Salida</th>
                                            <th>Operación</th>
                                            <th>Req.</th>
                                            <th>Cliente</th>
                                            <th>Responsable</th>
                                            <th width="70px"></th>
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
    </div>
</div>
@include('almacen.guias.guia_ven_create')
@include('almacen.distribucion.despachoDetalle')
@include('almacen.guias.guia_ven_obs')
@include('almacen.guias.guia_ven_cambio')
@include('almacen.guias.guia_ven_series')
@include('almacen.guias.salidaAlmacen')
@include('tesoreria.facturacion.archivos_oc_mgcp')
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

<script src="{{ asset('js/almacen/guia/despachosPendientes.js')}}?v={{filemtime(public_path('js/almacen/guia/despachosPendientes.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_ven_create.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_ven_create.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/despachoDetalle.js')}}?v={{filemtime(public_path('js/almacen/distribucion/despachoDetalle.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_ven_cambio.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_ven_cambio.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_ven_series.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_ven_series.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/salidaAlmacen.js')}}?v={{filemtime(public_path('js/almacen/guia/salidaAlmacen.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js')}}?v={{filemtime(public_path('js/almacen/distribucion/verDetalleRequerimiento.js'))}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}?v={{filemtime(public_path('js/tesoreria/facturacion/archivosMgcp.js'))}}"></script>
<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        iniciar('{{Auth::user()->tieneAccion(85)}}');
    });
</script>
@endsection