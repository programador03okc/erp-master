@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Gestión de Despachos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/stepperHorizontal.css')}}">
<link rel="stylesheet" href="{{ asset('css/stepper.css')}}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Distribución</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="listaOrdenesDespachoExterno">
    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">
                @if (Auth::user()->id_usuario == 3)
                <button id="btn_cerrar" class="btn btn-default" onClick="migrarDespachos();">Migrar</button>
                @endif
                <form id="formFiltrosDespachoExterno" method="POST" target="_blank" action="{{route('logistica.distribucion.ordenes-despacho-externo.despachosExternosExcel')}}">
                    @csrf()
                    <input type="hidden" name="select_mostrar" value="0">
                </form>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="mytable table table-condensed table-bordered table-hover table-striped table-okc-view" 
                                id="requerimientosEnProceso" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        {{-- <th></th> --}}
                                        <th>Cod.Req.</th>
                                        <th>Tipo Req.</th>
                                        <th>Fecha Fin Entrega</th>
                                        <th>Nro O/C</th>
                                        {{-- <th>Estado O/C</th> --}}
                                        <th>Monto total</th>
                                        <th>OC.fís / SIAF</th>
                                        <th>OCC</th>
                                        <th>Cod.CDP</th>
                                        <th width="30%">Cliente/Entidad</th>
                                        <th>Generado por</th>
                                        {{-- <th>Sede Req.</th> --}}
                                        <th>Fecha Despacho Real</th>
                                        <th>Flete</th>
                                        <th>Gasto Adic.</th>
                                        <th>Fecha Entrega</th>
                                        <th>Adj. Cargos.</th>
                                        <th>Estado despacho</th>
                                        <th width="10%">Acción</th>
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
@include('almacen.distribucion.ordenDespachoContacto')
@include('almacen.distribucion.ordenDespachoTransportista')
@include('almacen.distribucion.enviarFacturacion')
@include('almacen.distribucion.ordenDespachoEnviar')
@include('almacen.distribucion.agregarContacto')
@include('almacen.distribucion.contactoEnviar')
@include('almacen.distribucion.ordenDespachoEstados')
@include('almacen.distribucion.comentarios_oc_mgcp')
@include('almacen.distribucion.ordenDespachoProgramar')
@include('almacen.distribucion.priorizarDespachoExterno')
@include('tesoreria.facturacion.archivos_oc_mgcp')
@include('publico.ubigeoModal')
@include('almacen.transferencias.transportistaModal')
@include('almacen.distribucion.agregarTransportista')
{{-- @include('logistica.requerimientos.trazabilidad.modal_trazabilidad') --}}

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

<script src="{{ asset('js/almacen/distribucion/ordenesDespachoExterno.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/ordenesDespachoExterno.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoContacto.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/ordenDespachoContacto.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoEnviar.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/ordenDespachoEnviar.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/verDetalleRequerimiento.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoEstado.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/ordenDespachoEstado.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoTransportista.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/ordenDespachoTransportista.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/contacto.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/contacto.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/contactoEnviar.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/contactoEnviar.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/agregarTransportista.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/agregarTransportista.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoProgramar.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/ordenDespachoProgramar.js'))}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js?')}}?v={{filemtime(public_path('js/tesoreria/facturacion/archivosMgcp.js'))}}"></script>
{{-- <script src="{{ asset('js/almacen/distribucion/priorizarDespachoExterno.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/priorizarDespachoExterno.js'))}}"></script> --}}
{{-- <script src="{{ asset('js/logistica/requerimiento/trazabilidad.js')}}"></script> --}}

<script src="{{ asset('js/publico/ubigeoModal.js?')}}?v={{filemtime(public_path('js/publico/ubigeoModal.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/transportistaModal.js?')}}?v={{filemtime(public_path('js/almacen/transferencias/transportistaModal.js'))}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        
        let usuario = '{{Auth::user()->nombre_corto}}';
        console.log(usuario);
        listarRequerimientosPendientes(usuario);

        $('input.date-picker').datepicker({
            language: "es",
            orientation: "bottom auto",
            format: 'dd-mm-yyyy',
            autoclose: true
        });
    });
</script>
@endsection