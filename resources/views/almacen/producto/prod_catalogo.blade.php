@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
    Catálogo de Productos
@endsection

@section('content')
<div class="page-main" type="producto">
    <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;padding-top: 10px;">
        <div class="row">
            <div class="col-md-12">
                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaProductoCatalogo">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Tipo</th>
                            <th>Categoría</th>
                            <th>Subcatgoría</th>
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

    <script src="{{ asset('js/almacen/producto/prod_catalogo.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection
