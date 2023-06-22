@extends('layout.main')
@include('layout.menu_cas')

@section('cabecera')
Orden Devolución
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> Servicios CAS</a></li>
    <li>Movimientos</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
@if (in_array(Auth::user()->id_usuario,[1,3,27,17,77,93,64,8,71,76,141]))
    @include('almacen.devoluciones.devolucionContenido')
@else
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger pulse" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error de Accesos:</span>
            Solicite los accesos
        </div>
    </div>
</div>
@endif


@include('almacen.devoluciones.devolucionModal')
@include('almacen.customizacion.productoCatalogoModal')
@include('almacen.devoluciones.contribuyenteModal')
@include('almacen.devoluciones.salidasModal')
@include('cas.incidencias.incidenciaModal')
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

<script src="{{ asset('js/almacen/devolucion/devolucion.js')}}?v={{filemtime(public_path('js/almacen/devolucion/devolucion.js'))}}"></script>
<script src="{{ asset('js/almacen/devolucion/devolucionModal.js')}}?v={{filemtime(public_path('js/almacen/devolucion/devolucionModal.js'))}}"></script>
<script src="{{ asset('js/almacen/devolucion/salidasDevolucion.js')}}?v={{filemtime(public_path('js/almacen/devolucion/salidasDevolucion.js'))}}"></script>
<script src="{{ asset('js/almacen/devolucion/incidenciasDevolucion.js')}}?v={{filemtime(public_path('js/almacen/devolucion/incidenciasDevolucion.js'))}}"></script>
<script src="{{ asset('js/almacen/devolucion/productosDevolucion.js')}}?v={{filemtime(public_path('js/almacen/devolucion/productosDevolucion.js'))}}"></script>
<script src="{{ asset('js/almacen/devolucion/salidasModal.js')}}?v={{filemtime(public_path('js/almacen/devolucion/salidasModal.js'))}}"></script>
<script src="{{ asset('js/cas/incidencias/incidenciaModal.js')}}?v={{filemtime(public_path('js/cas/incidencias/incidenciaModal.js'))}}"></script>
<script src="{{ asset('js/almacen/devolucion/contribuyenteModal.js')}}?v={{filemtime(public_path('js/almacen/devolucion/contribuyenteModal.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/productoCatalogoModal.js')}}?v={{filemtime(public_path('js/almacen/customizacion/productoCatalogoModal.js'))}}"></script>

<script>
    console.log('{{Auth::user()->id_usuario}}');
    $(document).ready(function() {
        seleccionarMenu(window.location);
        usuarioSession = '{{Auth::user()->id_usuario}}';
        usuarioNombreSession = '{{Auth::user()->nombre_corto}}';
    });
</script>
@endsection
