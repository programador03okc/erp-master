@extends('layout.head')
@include('layout.menu_almacen')

@section('cabecera')
Documentos de Prorrateo
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('content')
<div class="page-main" type="docs_prorrateo">
    <legend class="mylegend">
        <h2>Reporte de Documentos de Prorrateo </h2>
        <ol class="breadcrumb">
            <li>
                {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Saldos" 
                    onClick="downloadKardexSunat();">Saldos por Almacén</button>
                <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Ingrese los filtros" 
                    onClick="open_filtros();">Filtros</button> --}}
            </li>
        </ol>
    </legend>
    {{-- <div class="row">
        <div class="col-md-3">
            <h5>Saldo al:</h5>
            <input type="date" class="form-control" name="fecha">
        </div>
        <div class="col-md-6">
            <h5>Almacén</h5>
            <div style="display:flex;">
                <select class="form-control js-example-basic-single" name="almacen">
                    @foreach ($almacenes as $alm)
                        <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Saldos" 
                    onClick="listarSaldos();">Buscar</button>
            </div>
        </div>
        <div class="col-md-2">
            <h5>Tipo de Cambio Compra</h5>
            <input type="text" class="form-control" name="tipo_cambio" disabled/>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaDocsProrrateo">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Tipo</th>
                        <th>Guía</th>
                        <th>Documento</th>
                        <th>RUC</th>
                        <th>Razon Social</th>
                        <th>Fecha Emisión</th>
                        <th>Tipo Cambio</th>
                        <th>Mnd</th>
                        <th>SubTotal</th>
                        <th>Descuento</th>
                        <th>Total</th>
                        <th>IGV</th>
                        <th>Total Neto</th>
                        <th hidden>idDoc</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
{{-- @include('almacen.kardex_filtro') --}}
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

    <script src="{{('/js/almacen/reporte/docs_prorrateo.js')}}"></script>
@endsection
