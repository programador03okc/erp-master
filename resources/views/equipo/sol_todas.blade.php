@extends('layout.head')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Solicitudes de Equipos
@endsection

@section('content')
<div class="page-main" type="sol_todas">
    <legend class="mylegend">
        <h2>Solicitudes de Equipos</h2>
    </legend>
    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view"
            id="listaSolTodas">
                <thead>
                    <tr>
                        <th hidden>Id</th>
                        <th>Código</th>
                        <th>Fecha Solicitud</th>
                        <th>Solicitado por</th>
                        <th>Area</th>
                        <th>Categoria</th>
                        <th>Equipo Asignado</th>
                        <th>Fecha Asignación</th>
                        <th>Estado</th>
                        <th width="50px"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('equipo.aprob_flujos')
@include('equipo.sol_ver')
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
    <script script src="{{('/js/equipo/sol_todas.js')}}"></script>
@endsection