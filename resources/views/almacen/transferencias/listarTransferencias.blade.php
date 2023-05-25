@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Transferencias
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Movimientos</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
@if (in_array(123,$array_accesos) || in_array(127,$array_accesos) || in_array(131,$array_accesos)|| in_array(133,$array_accesos))
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="transferencias">
            <div class="col-md-12" id="tab-transferencias" style="padding-top:10px;padding-bottom:10px;">

                <ul class="nav nav-tabs" id="myTabTransferencias">
                    @if (in_array(123,$array_accesos))
                    <li class="active"><a data-toggle="tab" href="#requerimientos">Transferencias sugeridas <span id="nro_pendientes" class="badge badge-info">{{$nro_pendientes}}</span></a></li>
                    @endif
                    @if (in_array(127,$array_accesos))
                    <li class=""><a data-toggle="tab" href="#porEnviar">Transferencias por Enviar <span id="nro_por_enviar" class="badge badge-info">{{$nro_por_enviar}}</span></a></li>
                    @endif
                    @if (in_array(131,$array_accesos))
                    <li class=""><a data-toggle="tab" href="#pendientes">Transferencias por Recibir <span id="nro_por_recibir" class="badge badge-info">{{$nro_por_recibir}}</span></a></li>
                    @endif
                    @if (in_array(133,$array_accesos))
                    <li class=""><a data-toggle="tab" href="#recibidas">Transferencias Recibidas</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    @if (in_array(123,$array_accesos))
                    <div id="requerimientos" class="tab-pane fade in active">

                        <div class="row" style="padding-top:10px;">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaRequerimientos">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th width="10%">Código</th>
                                            <th width="20%">Concepto</th>
                                            <th width="10%">Sede Destino</th>
                                            <th width="25%">Entidad/Cliente</th>
                                            <th width="10%">Responsable</th>
                                            <th width="15%">OCAM</th>
                                            <th width="5%">C.P.</th>
                                            <th width="5%">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    @endif
                    @if (in_array(127,$array_accesos))
                    <div id="porEnviar" class="tab-pane fade ">

                        {{-- <div class="row">
                            <div class="col-md-2"><label>Almacén Origen:</label></div>
                            <div class="col-md-4">
                                <select class="form-control" name="id_almacen_origen_lista" onChange="listarTransferenciasPorEnviar();">
                                    <option value="0" selected>Mostrar Todos</option>
                                    @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}

                        <form id="formFiltrosPorEnviar" method="POST" >
                            @csrf()
                            <input type="hidden" name="id_almacen_origen" value="0">
                        </form>
                        <div class="row" style="padding-top:10px;">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaTransferenciasPorEnviar">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th></th>
                                            <th width="8%">Tipo</th>
                                            <th width="8%">Código</th>
                                            <th width="12%">Almacén Origen</th>
                                            <th width="12%">Almacén Destino</th>
                                            <th width="10%">Fecha</th>
                                            <th width="10%">Codigo Req.</th>
                                            <th width="20%">Concepto</th>
                                            <th width="10%">Elaborado Por</th>
                                            <th width="10%">Guía salida</th>
                                            <th width="6%">Estado</th>
                                            <th width="6%">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                        </div>

                    </div>
                    @endif
                    @if (in_array(131,$array_accesos))
                    <div id="pendientes" class="tab-pane fade ">

                            {{-- <div class="row">
                                <div class="col-md-2"><label>Almacén Destino:</label></div>
                                <div class="col-md-4">
                                    <select class="form-control" name="id_almacen_destino_lista" onChange="listarTransferenciasPorRecibir();">
                                        <option value="0" selected>Mostrar todos</option>
                                        @foreach ($almacenes as $alm)
                                        <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <form id="formFiltrosPorRecibir" method="POST" >
                                @csrf()
                                <input type="hidden" name="id_almacen_destino" value="0">
                            </form>
                            <div class="row" style="padding-top:10px;">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaTransferenciasPorRecibir">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th width="8%">Tipo</th>
                                                <th width="8%">Código</th>
                                                <th width="10%">Guía salida</th>
                                                <th width="8%">Requerimiento</th>
                                                <th width="15%">Almacén Origen</th>
                                                <th width="15%">Almacén Destino</th>
                                                <th width="10%">Responsable Origen</th>
                                                <th width="10%">Responsable Destino</th>
                                                <th width="8%">Estado</th>
                                                <th width="5%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                    </div>
                    @endif
                    @if (in_array(133,$array_accesos))
                    <div id="recibidas" class="tab-pane fade ">

                            {{-- <div class="row">
                                <div class="col-md-2"><label>Almacén Destino:</label></div>
                                <div class="col-md-4">
                                    <select class="form-control" name="id_almacen_dest_recibida" onChange="listarTransferenciasRecibidas();">
                                        <option value="0" selected>Mostrar todos</option>
                                        @foreach ($almacenes as $alm)
                                        <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <form id="formFiltrosRecibidas" method="POST" >
                                @csrf()
                                <input type="hidden" name="id_almacen_destino_recibida" value="0">
                            </form>
                            @if (Auth::user()->id_usuario == 3)
                            <button data-toggle="tooltip" data-placement="bottom" title="Actualizar Ventas Internas"
                                class="btn btn-success btn-sm exportar" style="color:#fff !important;" onClick="exportarVentasInternasActualizadas()">
                                <i class="fas fa-file-excel"></i> Actualizar Ventas Internas
                            </button>
                            <button data-toggle="tooltip" data-placement="bottom" title="Actualizar Ventas Internas"
                                class="btn btn-success btn-sm exportar" style="color:#fff !important;" onClick="exportarValorizacionesIngresos()">
                                <i class="fas fa-file-excel"></i> Actualizar Ingresos Cambio moneda
                            </button>
                            @endif
                            <div class="row" style="padding-top:10px;">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaTransferenciasRecibidas">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th width="5%">Tipo</th>
                                                <th width="8%">Trans.</th>
                                                <th width="8%">Guía salida</th>
                                                <th width="8%">Guía ingreso</th>
                                                <th width="8%">Doc Venta</th>
                                                <th width="8%">Doc Compra</th>
                                                <th width="10%">Almacén Origen</th>
                                                <th width="10%">Almacén Destino</th>
                                                <th width="8%">Estado</th>
                                                <th width="8%">Req.</th>
                                                <th width="25%">Concepto</th>
                                                <th width="5%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger pulse" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            Solicite los accesos
        </div>
    </div>
</div>
@endif

@include('almacen.guias.guia_com_ver')
@include('almacen.transferencias.transferenciaCreate')
@include('almacen.transferencias.transferenciaRecibir')
@include('almacen.transferencias.transferenciaEnviar')
@include('almacen.transferencias.transferenciaDetalle')
@include('almacen.transferencias.ver_series')
@include('almacen.transferencias.transportistaModal')
@include('almacen.transferencias.verDocumentosAutogenerados')
@include('almacen.transferencias.nuevaTransferencia')
@include('almacen.transferencias.productosAlmacenModal')
@include('almacen.guias.guia_com_obs')
@include('almacen.guias.guia_ven_obs')
@include('almacen.guias.guia_ven_series')
@include('tesoreria.facturacion.archivos_oc_mgcp')
@include('logistica.gestion_logistica.compras.pendientes.modal_ver_orden_de_requerimiento')
@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
<!-- <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>

<script>
    // let csrf_token = "{{ csrf_token() }}";
    var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function() {
        seleccionarMenu(window.location);
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        iniciar('{{Auth::user()->tieneAccion(91)}}', '{{Auth::user()->id_usuario}}');
        //listarRequerimientosPendientes();

    });
</script>
<script src="{{ asset('js/almacen/transferencias/listarTransferencias.js')}}?v={{filemtime(public_path('js/almacen/transferencias/listarTransferencias.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/transferenciasRecibidas.js')}}?v={{filemtime(public_path('js/almacen/transferencias/transferenciasRecibidas.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/transferenciaCreate.js')}}?v={{filemtime(public_path('js/almacen/transferencias/transferenciaCreate.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/transferenciaRecibir.js')}}?v={{filemtime(public_path('js/almacen/transferencias/transferenciaRecibir.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/transferenciaEnviar.js')}}?v={{filemtime(public_path('js/almacen/transferencias/transferenciaEnviar.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/transportistaModal.js')}}?v={{filemtime(public_path('js/almacen/transferencias/transportistaModal.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/verDocsAutogenerados.js')}}?v={{filemtime(public_path('js/almacen/transferencias/verDocsAutogenerados.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/nuevaTransferencia.js')}}?v={{filemtime(public_path('js/almacen/transferencias/nuevaTransferencia.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/productosAlmacenModal.js')}}?v={{filemtime(public_path('js/almacen/transferencias/productosAlmacenModal.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js')}}?v={{filemtime(public_path('js/almacen/distribucion/verDetalleRequerimiento.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_ven_series.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_ven_series.js'))}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}?v={{filemtime(public_path('js/tesoreria/facturacion/archivosMgcp.js'))}}"></script>

@endsection
