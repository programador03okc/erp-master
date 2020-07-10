@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Kardex de Series
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
  <li>Reportes</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="kardex_series">
    <div class="row">
        <div class="col-md-5">
            <h5>Ingrese el Número de Serie</h5>
            <input type="text" class="form-control" name="serie" placeholder="Ingrese un Nro. de Serie..."/>
        </div>
        <div class="col-md-7">
            <h5>Seleccione el producto</h5>
            <div class="input-group-okc">
                <input class="oculto" name="id_producto"/>
                <input type="text" class="form-control" placeholder="Ingrese la descripción de un producto..." 
                    aria-describedby="basic-addon2" name="descripcion"/>
                {{-- <div class="input-group-append">
                    <button type="button" class="input-group-text" id="basic-addon2" onClick="productoModal();">
                        <i class="fa fa-search"></i>
                    </button>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                data-placement="bottom" title="Generar Kardex" 
                onClick="listarKardexSeries();">Actualizar Kardex</button>
            {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Exportar Kardex" 
                onClick="download_kardex_excel();">Excel</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id ="datos_producto" class="table-group">
                <tbody></tbody>
            </table>
        </div>
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaKardexSeries">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Serie</th>
                        <th>Descripción</th>
                        <th>Fec.Ingreso</th>
                        <th>Doc.Ingreso</th>
                        <th>Proveedor</th>
                        <th>Alm.Ingreso</th>
                        <th>Fec.Salida</th>
                        <th>Doc.Salida</th>
                        <th>Cliente</th>
                        <th>Alm.Salida</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('almacen.producto.productoModal')
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

    <script src="{{ asset('js/almacen/reporte/kardex_series.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/productoModal.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection