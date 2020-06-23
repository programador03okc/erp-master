@extends('layout.head')
@include('layout.menu_proyectos')

@section('cabecera')
Opciones Comerciales
@endsection

@section('content')
<div class="page-main" type="opcion">
    <legend class="mylegend">
        <h2>Gestión de Opciones Comerciales</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear Opción" 
                onClick="open_opcion_create();">Crear Opción</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
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

    <script src="{{('/js/proyectos/opcion/opcion.js')}}"></script>
    <script src="{{('/js/logistica/clienteModal.js')}}"></script>
    <script src="{{('/js/proyectos/variables/add_cliente.js')}}"></script>
@endsection