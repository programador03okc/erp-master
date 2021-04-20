@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Búsqueda sensitiva de Series
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
  <li>Reportes</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="kardex_series">

    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <div class="row">
                    <div class="col-md-2">
                        <h5>Serie</h5>
                        <input type="text" class="form-control" name="serie" placeholder="Ingrese un Nro. de Serie..."/>
                    </div>
                    <div class="col-md-2">
                        <h5>Código</h5>
                        <input type="text" class="form-control" name="codigo" placeholder="Ingrese un Código..."/>
                    </div>
                    <div class="col-md-2">
                        <h5>Part-Number</h5>
                        <input type="text" class="form-control" name="part_number" placeholder="Ingrese un Part Number..."/>
                    </div>
                    <div class="col-md-6">
                        <h5>Descripción</h5>
                        <div class="input-group-okc">
                            <input class="oculto" name="id_producto"/>
                            <input type="text" class="form-control" placeholder="Ingrese la descripción de un producto..." 
                                aria-describedby="basic-addon2" name="descripcion"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Kardex" 
                            onClick="listarKardexSeries();">Actualizar Kardex</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" 
                            id="listaKardexSeries">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th>Serie</th>
                                    <th>Código</th>
                                    <th>Part Number</th>
                                    <th width="70%">Descripción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
</div>
@include('almacen.reportes.modalKardexSerie')
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
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection