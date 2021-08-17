@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Gestión de Transferencias
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Transferencias</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="transferencias">
            <div class="col-md-12" id="tab-transferencias" style="padding-left:0px;padding-right:0px;">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a type="#requerimientos">Requerimientos Pendientes</a></li>
                    <li class=""><a type="#porEnviar">Transferencias Pendientes de Enviar</a></li>
                    <li class=""><a type="#pendientes">Transferencias Pendientes de Recibir</a></li>
                    <li class=""><a type="#recibidas">Transferencias Recibidas</a></li>
                </ul>
                <div class="content-tabs">
                    <section id="requerimientos">
                        <form id="form-requerimientos" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaRequerimientos">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th width="10%">Código</th>
                                                <th width="20%">Concepto</th>
                                                <th width="10%">Sede Req</th>
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
                        </form>
                    </section>
                    <section id="porEnviar" hidden>
                        <form id="form-porEnviar" type="register">
                            <div class="row">
                                <div class="col-md-2"><label>Almacén Origen:</label></div>
                                <div class="col-md-4">
                                    <!-- <label>Almacén Origen:</label> -->
                                    <select class="form-control" name="id_almacen_origen_lista" onChange="listarTransferenciasPorEnviar();">
                                        <option value="0" selected>Mostrar Todos</option>
                                        @foreach ($almacenes as $alm)
                                        <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="display: flex;justify-content: flex-end;">
                                        @if(Auth::user()->tieneAccion(91))
                                        <button type="button" class="btn btn-success btn-flat" data-toggle="tooltip" data-placement="bottom" title="Crear Guía con varias transferencias" onClick="openGuiaTransferenciaCreate();">
                                            Generar Guía</button>
                                        @endif
                                    </div>
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaTransferenciasPorEnviar">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th></th>
                                                <th width="10%">Código</th>
                                                <!-- <th width="10%">Fecha Registro</th> -->
                                                <th width="15%">Almacén Origen</th>
                                                <th width="15%">Almacén Destino</th>
                                                <th width="10%">Codigo Req.</th>
                                                <th width="25%">Concepto</th>
                                                <th width="10%">Sede Req.</th>
                                                <th width="10%">Elaborado Por</th>
                                                <th width="6%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>

                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="pendientes" hidden>
                        <form id="form-pendientes" type="register">
                            <div class="row">
                                <div class="col-md-2"><label>Almacén Destino:</label></div>
                                <div class="col-md-4">
                                    <select class="form-control" name="id_almacen_destino_lista" onChange="listarTransferenciasPorRecibir();">
                                        <option value="0" selected>Mostrar todos</option>
                                        @foreach ($almacenes as $alm)
                                        <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaTransferenciasPorRecibir">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th width="10%">Fecha Guía</th>
                                                <th width="10%">Guía Venta</th>
                                                <th width="20%">Almacén Origen</th>
                                                <th width="20%">Almacén Destino</th>
                                                <th width="14%">Responsable Origen</th>
                                                <th width="14%">Responsable Destino</th>
                                                <th width="8%">Estado</th>
                                                <th width="8%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="recibidas" hidden>
                        <form id="form-recibidas" type="register">
                            <div class="row">
                                <div class="col-md-2"><label>Almacén Destino:</label></div>
                                <div class="col-md-4">
                                    <select class="form-control" name="id_almacen_dest_recibida" onChange="listarTransferenciasRecibidas();">
                                        <option value="0" selected>Mostrar todos</option>
                                        @foreach ($almacenes as $alm)
                                        <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaTransferenciasRecibidas">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Fecha Trans.</th>
                                                <th>Nro.Trans.</th>
                                                <th>Guía Venta</th>
                                                <th>Guía Compra</th>
                                                <th>Almacén Origen</th>
                                                <th>Almacén Destino</th>
                                                <th>Responsable Origen</th>
                                                <th>Responsable Destino</th>
                                                <th>Estado</th>
                                                <th>Req.</th>
                                                <th>Concepto</th>
                                                <th width="7%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@include('almacen.guias.guia_com_ver')
@include('almacen.transferencias.transferenciaCreate')
@include('almacen.transferencias.transferenciaRecibir')
@include('almacen.transferencias.transferenciaEnviar')
@include('almacen.transferencias.transferenciaDetalle')
@include('almacen.transferencias.ver_series')
@include('almacen.transferencias.transportistaModal')
@include('almacen.guias.guia_com_obs')
@include('almacen.guias.guia_ven_obs')
@include('almacen.guias.guia_ven_series')
@include('tesoreria.facturacion.archivos_oc_mgcp')

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
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>

<script src="{{ asset('js/almacen/transferencias/listarTransferencias.js')}}"></script>
<script src="{{ asset('js/almacen/transferencias/transferenciaCreate.js')}}"></script>
<script src="{{ asset('js/almacen/transferencias/transferenciaRecibir.js')}}"></script>
<script src="{{ asset('js/almacen/transferencias/transferenciaEnviar.js')}}"></script>
<script src="{{ asset('js/almacen/transferencias/transportistaModal.js')}}"></script>
<!-- <script src="{{ asset('js/logistica/requerimiento/historial.js')}}"></script> -->
<!-- <script src="{{ asset('js/almacen/guia/guia_com_det_series.js')}}"></script> -->
<script src="{{ asset('js/almacen/guia/guia_ven_series.js')}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        iniciar('{{Auth::user()->tieneAccion(91)}}', '{{Auth::user()->id_usuario}}');
        inicializar(
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.elaborados')}}"
        );
    });
</script>
@endsection