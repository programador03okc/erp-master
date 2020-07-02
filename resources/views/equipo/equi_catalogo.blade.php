@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Catálogo de Equipos
@endsection

@section('content')
<div class="page-main" type="equi_catalogo">
    <legend class="mylegend">
        <h2>Catálogo de Equipos</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear Equipo" 
                onClick="equipo_create();">Crear Equipo</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view"
                id="listaEquiCatalogo">
                <thead>
                    <tr>
                        <th></th>
                        <th>Tipo</th>
                        <th>Categoría</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Propietario</th>
                        <th>Placa</th>
                        <th>Modelo</th>
                        <th>Combustible</th>
                        <th width="15%">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('equipo.equipo_create')
@include('equipo.equi_seguro')
@include('equipo.mtto_programacion')
{{-- @include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor') --}}
@include('equipo.add_tp_combustible')
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
    <script src="{{('/js/equipo/equipo.js')}}"></script>
    <script src="{{('/js/equipo/equi_seguro.js')}}"></script>
    <script src="{{('/js/equipo/mtto_programacion.js')}}"></script>
    <script src="{{('/js/equipo/add_tp_combustible.js')}}"></script>
    {{-- <script src="{{('/js/logistica/proveedorModal.js')}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}"></script> --}}
@endsection