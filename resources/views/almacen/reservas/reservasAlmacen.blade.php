@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Reservas de almacén
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
<style>
    #despachosPendientes_filter,
    #despachosEntregados_filter{
        margin-top:10px;
    }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Movimientos</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

@if (in_array(155,$array_accesos) || in_array(156,$array_accesos) || in_array(154,$array_accesos))
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="reservasAlmacen">
            <div class="row" style="padding-top:10px;padding-right:10px;padding-left:10px;">
                <div class="col-md-12">
                    @if (Auth::user()->id_usuario == 3)
                    <button id="btn_actualizar_reservas" class="btn btn-default" onClick="actualizarReservas();">Actualizar Reservas</button>
                    @endif
                    <div class="table-responsive">
                        <table class="mytable table table-condensed table-bordered table-okc-view"
                            id="reservasAlmacen" style="width:100%;">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th>Reserva</th>
                                    <th>Requerimiento</th>
                                    <th>Código</th>
                                    <th>Part Number</th>
                                    <th>Descripción del producto</th>
                                    <th>Almacén de reserva</th>
                                    <th>Stock comprom.</th>
                                    <th>Guía compra</th>
                                    <th>Transf.</th>
                                    <th>Item Trans.</th>
                                    <th>Item Base</th>
                                    <th>Registrado por</th>
                                    <th>Fecha de registro</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger pulse" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            Solicite los accesos
        </div>
    </div>
</div>
@endif
@include('almacen.reservas.editarReserva')
@include('almacen.reservas.ajustarEstadoReserva')
@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>

<script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>

<script src="{{ asset('js/almacen/reservas/reservasAlmacen.js') }}?v={{filemtime(public_path('js/almacen/reservas/reservasAlmacen.js'))}}"></script>

<script>
    var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function() {
        seleccionarMenu(window.location);
        listarReservasAlmacen('{{Auth::user()->id_usuario}}');
        // $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
    //     iniciar('{{Auth::user()->tieneAccion(85)}}');
    });
</script>
@endsection
