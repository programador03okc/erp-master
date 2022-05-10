@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Atención de Ingresos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
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
        <div class="page-main" type="ordenesPendientes">
            <div class="col-md-12" id="tab-ordenes" style="padding-top:10px;padding-bottom:10px;">

                <ul class="nav nav-tabs" id="myTabOrdenesPendientes">
                    <li class="active"><a data-toggle="tab" href="#pendientes">Ordenes Pendientes <span id="nro_ordenes" class="badge badge-info">{{$nro_oc_pendientes}}</span></a></li>
                    <li class=""><a data-toggle="tab" href="#transformaciones">Transformaciones Pendientes <span id="nro_transformaciones" class="badge badge-info">{{$nro_ot_pendientes}}</span></a></li>
                    <li class=""><a data-toggle="tab" href="#ingresadas">Ingresos Procesados</a></li>
                </ul>

                <div class="tab-content">
                    <div id="pendientes" class="tab-pane fade in active">
                        <br>
                        <form id="formFiltrosOrdenesPendientes" method="POST" target="_blank" action="{{route('almacen.movimientos.pendientes-ingreso.ordenesPendientesExcel')}}">
                            @csrf()
                            <input type="hidden" name="ordenes_fecha_fin" value="{{$fechaActual->format('d-m-Y')}}"/>
                            <input type="hidden" name="ordenes_fecha_inicio" value="{{$fechaActual->addMonths(-3)->format('d-m-Y')}}"/>
                            <input type="hidden" name="ordenes_id_sede" value="0">
                        </form>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="ordenesPendientes" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th width="3%"></th>
                                            <th width="10%">Orden SoftLink</th>
                                            <th width="10%">Cod.Orden</th>
                                            <th width="20%">Proveedor</th>
                                            <th width="12%">Fecha Emisión</th>
                                            <th width="8%">Sede Orden</th>
                                            <th width="8%">Creado por</th>
                                            <th width="5%">Estado</th>
                                            <th width="6%"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="transformaciones" class="tab-pane fade ">
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaTransformaciones" style="width:100px;">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            {{-- <th>Orden Elec.</th>
                                            <th>Cuadro Costo</th>
                                            <th>Oportunidad</th>
                                            <th>Entidad</th> --}}
                                            <th>Código</th>
                                            <th>Fecha Proceso</th>
                                            <th>Almacén</th>
                                            <th>Transformado por</th>
                                            <th>Observación</th>
                                            <th>Requerimiento</th>
                                            <th>Orden Despacho</th>
                                            {{-- <th>Guía</th> --}}
                                            <th width="80px"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="ingresadas" class="tab-pane fade ">
                        <br>
                        <form id="formFiltrosIngresosProcesados" method="POST" target="_blank" action="{{route('almacen.movimientos.pendientes-ingreso.ingresosProcesadosExcel')}}">
                            @csrf()
                            <input type="hidden" name="ingreso_fecha_fin" value="{{$fechaActual2->format('d-m-Y')}}"/>
                            <input type="hidden" name="ingreso_fecha_inicio" value="{{$fechaActual2->addMonths(-3)->format('d-m-Y')}}"/>
                            <input type="hidden" name="ingreso_id_sede" value="0">
                        </form>

                        <div class="row">
                            <div class="col-md-12">

                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaIngresosAlmacen">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th></th>
                                            <th>Fecha Ingreso</th>
                                            <th>Ingreso</th>
                                            <th>Guía Compra</th>
                                            <th>Proveedor</th>
                                            <th>Operación</th>
                                            <th width="70px">Almacén</th>
                                            <th>Responsable</th>
                                            <th>Transferencia</th>
                                            <th>Ordenes</th>
                                            <th>OC SoftLink</th>
                                            <th>Facturas</th>
                                            <th>Requerim.</th>
                                            <th width="100px"></th>
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


@include('almacen.documentos.doc_com_create')
@include('almacen.guias.ordenDetalle')
@include('almacen.guias.movimientoDetalle')
@include('almacen.guias.guia_com_create')
@include('almacen.guias.guia_com_series')
@include('almacen.guias.guia_com_obs')
@include('almacen.guias.guia_com_cambio')
@include('almacen.documentos.doc_com_ver')
@include('almacen.guias.ordenesGuias')
@include('almacen.guias.guia_com_ver')
@include('tesoreria.facturacion.archivos_oc_mgcp')
@include('logistica.requerimientos.mapeo.mapeoAsignarProducto')

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

<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') }}"></script>

<script src="{{ asset('js/almacen/guia/ordenesPendientes.js?')}}?v={{filemtime(public_path('js/almacen/guia/ordenesPendientes.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/transformacionesPendientes.js?')}}?v={{filemtime(public_path('js/almacen/guia/transformacionesPendientes.js'))}}"></script>

<script src="{{ asset('js/almacen/guia/ingresosProcesados.js')}}?v={{filemtime(public_path('js/almacen/guia/ingresosProcesados.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/ordenes_ver_detalle.js')}}?v={{filemtime(public_path('js/almacen/guia/ordenes_ver_detalle.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/movimientoDetalle.js')}}?v={{filemtime(public_path('js/almacen/guia/movimientoDetalle.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_create.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_com_create.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_cambio.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_com_cambio.js'))}}"></script>

<script src="{{ asset('js/almacen/guia/guia_com_det_series.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_com_det_series.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_det_series_edit.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_com_det_series_edit.js'))}}"></script>

<script src="{{ asset('js/almacen/documentos/doc_com_create.js')}}?v={{filemtime(public_path('js/almacen/documentos/doc_com_create.js'))}}"></script>
<script src="{{ asset('js/almacen/documentos/doc_com_ver.js')}}?v={{filemtime(public_path('js/almacen/documentos/doc_com_ver.js'))}}"></script>

{{-- <script src="{{ asset('js/almacen/transferencias/transferenciaCreate.js')}}"></script> --}}
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}?v={{filemtime(public_path('js/tesoreria/facturacion/archivosMgcp.js'))}}"></script>
<script src="{{ asset('js/logistica/mapeo/mapeoAsignarProducto.js')}}?v={{filemtime(public_path('js/logistica/mapeo/mapeoAsignarProducto.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_transformacion_mapeo.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_transformacion_mapeo.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_create_transformacion.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_com_create_transformacion.js'))}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        iniciar('{{Auth::user()->tieneAccion(83)}}');
        // listarAlmacenes();
    });
</script>
@endsection