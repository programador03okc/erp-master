@extends('layout.head')
@include('layout.menu_almacen')

@section('cabecera')
Kardex General
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('content')
<div class="page-main" type="kardex_general">
    <legend class="mylegend">
        <h2>Kardex General</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Kardex Sunat" 
                    onClick="downloadKardexSunat();">Kardex Sunat</button>
                <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Ingrese los filtros" 
                    onClick="open_filtros();">Filtros</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="kardexGeneral">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Ubicación</th>
                        <th>Und</th>
                        <th>Ing.</th>
                        <th>Sal.</th>
                        <th>Saldo</th>
                        <th>Ing.</th>
                        <th>Sal.</th>
                        <th>Valoriz.</th>
                        <th>Op</th>
                        <th>Movimiento</th>
                        <th>Guía Remisión</th>
                        <th>Documento</th>
                        <th>Orden Compra</th>
                        <th>Requerimiento</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('almacen.reportes.kardex_filtro')
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
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>

    <script src="{{('/js/almacen/reporte/kardex_general.js')}}"></script>
@endsection