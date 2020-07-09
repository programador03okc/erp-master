@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Saldos Actuales
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
  <li>Reportes</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="saldos">
    <div class="row">
        <!-- <div class="col-md-3">
            <h5>Saldo al:</h5>
            <input type="date" class="form-control" name="fecha">
        </div> -->
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
    </div>
    <div class="row">
        <div class="col-md-12">
            <input type="checkbox" name="todos_almacenes" style="margin-right: 10px; margin-left: 7px;"/> Todos los almacenes
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaSaldos">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Código</th>
                        <th>Cód.Anexo</th>
                        <th>Part Number</th>
                        <th>Descripción</th>
                        <th>Und</th>
                        <th>Stock</th>
                        <th>Reserva</th>
                        <th>Detalle</th>
                        <th>Mnd</th>
                        <th>Soles</th>
                        <th>Dolar</th>
                        <th>Costo Promedio</th>
                        <th>Ubicación</th>
                        <th>Clasificación</th>
                        <th>Categoría</th>
                        <th>SubCategoría</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('almacen.verRequerimientoEstado')
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

    <script src="{{ asset('js/almacen/reporte/saldos.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection