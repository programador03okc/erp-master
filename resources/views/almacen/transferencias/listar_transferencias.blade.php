@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Gestión de Transferencias
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
  <li>Transferencias</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="transferencias_pendientes">
    <div class="col-md-12" id="tab-transferencias"  style="padding-left:0px;padding-right:0px;">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Transferencias Pendientes</a></li>
            <li class=""><a type="#recibidas">Transferencias Recibidas</a></li>
        </ul>
        <div class="content-tabs">
            <section id="pendientes" >
                <form id="form-pendientes" type="register">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén Destino</h5>
                            <select class="form-control" name="id_almacen_destino">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}" selected>{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- <div class="col-md-4">
                            <h5>Almacén Destino</h5>
                            <select class="form-control activation" name="id_almacen_des">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div> -->
                        <div class="col-md-4">
                            <h5>Actualizar</h5>
                            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                                data-placement="bottom" title="Actualizar" 
                                onClick="listarTransferenciasPendientes();">Actualizar</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="listaTransferenciasPendientes">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Fecha Trans.</th>
                                        <th>Nro.Trans.</th>
                                        <th>Guía Venta</th>
                                        <th>Guía Compra</th>
                                        <!-- <th>Fecha de Guía</th> -->
                                        <th>Almacén Origen</th>
                                        <th>Almacén Destino</th>
                                        <th>Responsable Origen</th>
                                        <th>Responsable Destino</th>
                                        <!-- <th>Registrado por</th> -->
                                        <th>Estado</th>
                                        <th>OC</th>
                                        <th>Req.</th>
                                        <th>Concepto</th>
                                        <th width="10%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="recibidas" hidden>
                <form id="form-recibidas" type="register">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén Destino</h5>
                            <select class="form-control" name="id_almacen_dest_recibida">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}" selected>{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Actualizar</h5>
                            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                                data-placement="bottom" title="Actualizar" 
                                onClick="listarTransferenciasRecibidas();">Actualizar</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="listaTransferenciasRecibidas">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Fecha Trans.</th>
                                        <th>Nro.Trans.</th>
                                        <th>Guía Venta</th>
                                        <th>Guía Compra</th>
                                        <!-- <th>Fecha de Guía</th> -->
                                        <th>Almacén Origen</th>
                                        <th>Almacén Destino</th>
                                        <th>Responsable Origen</th>
                                        <th>Responsable Destino</th>
                                        <!-- <th>Registrado por</th> -->
                                        <th>Estado</th>
                                        <th>OC</th>
                                        <th>Req.</th>
                                        <th>Concepto</th>
                                        <th width="7%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@include('almacen.transferencias.transferencia_detalle')
@include('almacen.guias.guia_com_obs')
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
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>

    <script src="{{ asset('js/almacen/transferencias/listar_transferencias.js')}}"></script>
    <script src="{{ asset('js/almacen/transferencias/transferencia_detalle.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_venta.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        iniciar('{{Auth::user()->tieneAccion(91)}}');
    });
    </script>
@endsection