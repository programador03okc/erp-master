@extends('layout.main')
@include('layout.menu_proyectos')

@section('cabecera')
Residentes
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
  <li>Ejecución</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="residente">
    <div class="row">
        <div class="col-md-12">
            <div style="text-align: right;">
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Crear Residente" 
                    onClick="open_residente_create('');">Crear Residente</button>
            </div>
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaResidentes">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>DNI</th>
                        <th>Nombres y Apellidos</th>
                        <th>Colegiatura</th>
                        {{-- <th>Especialidad</th> --}}
                        <th>Estado</th>
                        <th width="100px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.residentes.residenteCreate')
@include('proyectos.residentes.trabajadorModal')
@include('proyectos.proyecto.proyectoModal')
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

    <script src="{{ asset('js/proyectos/residentes/residentes.js')}}"></script>
    <script src="{{ asset('js/proyectos/residentes/trabajadorModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/proyecto/proyectoModal.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection