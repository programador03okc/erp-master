@extends('layout.head')
@include('layout.menu_proyectos')

@section('cabecera')
Todas las Opciones
@endsection

@section('content')
<div class="page-main" type="opciones_todo">
    <legend class="mylegend">
        <h2>Gestión de Todas las Opciones</h2>
        {{-- <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear Proyecto" 
                onClick="open_proyecto_create();">Crear Proyecto</button>
            </li>
        </ol> --}}
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaOpcionesTodo">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Fecha Emisión</th>
                        <th>Pres.Int.</th>
                        <th>Propuesta</th>
                        <th>Proyecto</th>
                        <th>Pres.Eje.</th>
                        <th>SubTotal</th>
                        <th>IGV</th>
                        <th>Total</th>
                        <th>Imp.Req.</th>
                        <th>OC/OS</th>
                        {{-- <th>OC con IGV</th> --}}
                        {{-- <th width="90px">Acción</th> --}}
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{('/js/proyectos/reportes/opciones_todo.js')}}"></script>
    <script src="{{('/js/proyectos/proyectoContrato.js')}}"></script>
    <script src="{{('/js/logistica/clienteModal.js')}}"></script> --}}
@endsection