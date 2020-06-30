@extends('layout.main')
@include('layout.menu_proyectos')

@section('cabecera')
Proyectos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
  <li>Ejecución</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="proyecto">
    <div class="row">
        <div class="col-md-12">
            <div style="text-align: right;">
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Crear Proyecto" 
                    onClick="open_proyecto_create();">Crear Proyecto</button>
            </div>
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaProyecto">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Modalidad</th>
                        <th>Sis.Contrato</th>
                        <th>Mnd</th>
                        <th>Importe</th>
                        <th>Usuario</th>
                        <th>Duración</th>
                        <th>Estado</th>
                        <th width="110px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.proyecto.proyectoContrato')
@include('proyectos.proyecto.proyectoCreate')
@include('proyectos.opcion.opcionModal')
@include('logistica.cotizaciones.clienteModal')
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

    <script src="{{ asset('js/proyectos/proyecto/proyecto.js')}}"></script>
    <script src="{{ asset('js/proyectos/opcion/opcionModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/proyecto/proyectoContrato.js')}}"></script>
    <script src="{{ asset('js/logistica/clienteModal.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection