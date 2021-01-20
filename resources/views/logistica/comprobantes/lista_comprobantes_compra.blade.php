@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Lista de Comprobantes de Compra
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
    <li>Compras</li>
    <li>comprobantes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')
<div class="page-main" type="lista_comprobantes_compra">

    <div class="row">
            <div class="col-md-12">
            

            </div>
    </div>
</div>
@include('logistica.comprobantes.doc_compraModal')
@include('logistica.comprobantes.orden_compraModal')
@include('logistica.comprobantes.detalle_ordenModal')
@include('logistica.comprobantes.doc_com_detalle')
@include('almacen.guias.guia_compraModal')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>

    <script src="{{('/js/logistica/comprobantes/doc_compra.js')}}"></script>
    <script src="{{('/js/logistica/comprobantes/doc_compraModal.js')}}"></script>
    <script src="{{('/js/logistica/comprobantes/orden_compraModal.js')}}"></script>
    <script src="{{('/js/logistica/comprobantes/doc_com_detalle.js')}}"></script>
    <script src="{{('/js/almacen/guia/guia_compraModal.js')}}"></script>
    <script src="{{('/js/logistica/proveedorModal.js')}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}"></script>

@endsection