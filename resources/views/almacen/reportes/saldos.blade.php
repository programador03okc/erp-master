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
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Reportes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="saldos">

    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <div class="row">
                    <div class="col-md-6">
                        <h5>Almacén</h5>
                        <div style="display:flex;">
                            <select class="form-control js-example-basic-single" name="almacen">
                                <option value="0" selected>Todos los almacenes</option>
                                @foreach ($almacenes as $alm)
                                <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Descargar Saldos" onClick="mostrarSaldos();">Buscar</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" id="listaSaldos">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th>Código</th>
                                    <th>Part Number</th>
                                    <th width="40%">Descripción</th>
                                    <th>Und</th>
                                    <th>Stock Actual</th>
                                    <th>Valorización</th>
                                    <th>Costo promedio</th>
                                    <th>Reserva</th>
                                    <th>Disponible</th>
                                    <th width="15%">Almacén</th>
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
@include('almacen.reportes.verRequerimientoReservas')
@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<!-- <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>

<script src="{{ asset('js/almacen/reporte/saldos.js')}}"></script>
<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
    });
</script>
@endsection