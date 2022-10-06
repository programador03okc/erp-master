@extends('layout.main')
@include('layout.menu_almacen')

@if(Auth::user()->tieneAccion(61))
@section('option')
@include('layout.option')
@endsection
@elseif(Auth::user()->tieneAccion(62))
@section('option')
@include('layout.option_historial')
@endsection
@endif

@section('cabecera')
Categoría
@endsection
@section('estilos')
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes </a></li>
    <li>Catálogo</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="categoria">
    @if (sizeof($array_accesos_botonera)!==0)
        <div class="row">
            <div class="col-md-6">
                <fieldset class="group-table">
                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaCategorias">
                        <thead>
                            <tr>
                                <th hidden>Id</th>
                                <th>Clasificación</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
            </div>
            <div class="col-md-6">
                <form id="form-categoria" type="register" form="formulario">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <input type="text" class="oculto" name="id_tipo_producto" primary="ids">

                    <div class="row">
                        <div class="col-md-8">
                            <h5>Clasificación</h5>
                            <select class="form-control activation" name="id_clasificacion" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($clasificaciones as $clas)
                                <option value="{{$clas->id_clasificacion}}">{{$clas->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>Descripción</h5>
                            <input type="text" class="form-control activation" name="descripcion">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                        </div>
                    </div>
                </form>
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

</div>
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

<script src="{{ asset('js/almacen/producto/categoria.js')}}"></script>
<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
    });
</script>
@endsection
