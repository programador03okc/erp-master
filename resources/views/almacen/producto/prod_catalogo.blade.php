@extends('layout.head')
@include('layout.menu_almacen')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Catálogo de Productos
@endsection

@section('content')
<div class="page-main" type="producto">
    <legend class="mylegend">
        <h2>Catálogo de Productos</h2>
        <ol class="breadcrumb">
            <li><label id="tipo_descripcion"> </li>
            <li><label id="cat_descripcion"></li>
            <li><label id="subcat_descripcion"></li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaProductoCatalogo">
                <thead>
                    <tr>
                        <th></th>
                        <th>Cod</th>
                        <th>Tipo</th>
                        <th>Cod</th>
                        <th>Categoría</th>
                        <th>Cod</th>
                        <th>Subcatgoría</th>
                        <th>Cod</th>
                        <th>Clasificación</th>
                        <th>Código</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
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
    <script src="{{('/js/almacen/producto/prod_catalogo.js')}}"></script>
@endsection
