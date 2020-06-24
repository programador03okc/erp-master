@extends('layout.head')
@include('layout.menu_config')
@section('option')
    @include('layout.option')
@endsection
@section('cabecera')
    Historial de Aprobaciones
@endsection

@section('content')
<div class="page-main" type="modulo">
    <legend><h2>Historial de Aprobaciones</h2></legend>
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listaHistorialAprobación">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="30">Flujo</th>
                            <th>Documento</th>
                            <th>VoBo</th>
                            <th>Detalle</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Area</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
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
    <script src="{{('/js/configuracion/flujo_aprobacion/historialAprobaciones.js')}}"></script>
@endsection