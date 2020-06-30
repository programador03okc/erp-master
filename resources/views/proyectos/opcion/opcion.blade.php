@extends('layout.main')
@include('layout.menu_proyectos')

@section('cabecera')
Opciones Comerciales
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
  <li>Opción Comercial</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="opcion">
    <div class="row">
        <div class="col-md-12">
            <div style="text-align: right;">
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Crear Opción" 
                    onClick="open_opcion_create();">Crear Opción</button>
            </div>
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaOpcion">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Código</th>
                        <th>Fecha Emisión</th>
                        <th>Descripción</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Duración</th>
                        <th>Unidad</th>
                        <th>Modalidad</th>
                        <th>Elaborado por</th>
                        <th>Estado</th>
                        <th width="50px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.opcion.opcionCreate')
@include('logistica.cotizaciones.clienteModal')
@include('proyectos.variables.add_cliente')
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
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/proyectos/opcion/opcion.js')}}"></script>
    <script src="{{ asset('js/logistica/clienteModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/variables/add_cliente.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection