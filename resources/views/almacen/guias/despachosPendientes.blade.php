@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Pendientes de Salida
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
  <li>Movimientos</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="despachosPendientes">
    <div class="col-md-12" id="tab-ordenes"  style="padding-left:0px;padding-right:0px;">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Despachos Pendientes</a></li>
            <li class=""><a type="#salidas">Salidas de Almacén</a></li>
        </ul>
        <div class="content-tabs">
            <section id="pendientes" >
                <form id="form-pendientes" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="despachosPendientes" style="width:100px;">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Tipo</th>
                                        <th>Fecha Despacho</th>
                                        <th>Hora Despacho</th>
                                        <th>Codigo</th>
                                        <th>Cliente</th>
                                        <th>Requerimiento</th>
                                        <th>Concepto</th>
                                        <th>Almacén</th>
                                        <!-- <th>Ubigeo</th>
                                        <th>Dirección Destino</th> -->
                                        <th>Fecha Entrega</th>
                                        <th>Registrado por</th>
                                        <th width="90px">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="salidas" hidden>
                <form id="form-salidas" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="despachosEntregados">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Fecha Salida</th>
                                        <th>Cod.Orden</th>
                                        <th>Cliente</th>
                                        <th>Req.</th>
                                        <th>Concepto</th>
                                        <th>Almacén</th>
                                        <th>Guia</th>
                                        <th>Responsable</th>
                                        <th width="120px"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@include('almacen.guias.guia_ven_create')
@include('almacen.distribucion.despachoDetalle')
@include('almacen.guias.guia_ven_obs')
@include('almacen.guias.guia_ven_cambio')
@include('almacen.guias.guia_com_series')
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

    <script src="{{ asset('js/almacen/guia/despachosPendientes.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_ven_create.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/despachoDetalle.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_com_det_series.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_ven_cambio.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        iniciar('{{Auth::user()->tieneAccion(85)}}');
    });
    </script>
@endsection