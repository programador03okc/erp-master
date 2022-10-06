@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Catálogo de Productos
@endsection
@section('estilos')
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection

@section('content')
<div class="page-main" type="producto">

        @if (in_array(37,$array_accesos) || in_array(38,$array_accesos) || in_array(39,$array_accesos) || in_array(40,$array_accesos))
        <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;padding-top: 10px;">
            <div class="row">
                <div class="col-md-12">
                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaProductoCatalogo">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Clasificación</th>
                                <th>Código Softlink</th>
                                <th>Categoría</th>
                                <th>Cód. Cat. Softlink</th>
                                <th>Subcatgoría</th>
                                <th>Cód. SubCat. Softlink</th>
                                <th>Marca</th>
                                <th>Código Agile</th>
                                <th>Part Number</th>
                                <th>Descripción</th>
                                <th>Unid. Med.</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
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
    var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function() {
        seleccionarMenu(window.location);
    });
</script>
@endsection
