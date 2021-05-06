@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Panel de Control de Despachos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
  <li>Distribución</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="ordenesDespacho">
            <!-- <div class="box box-solid">
                <div class="box-body">
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td>Priorización de Despachos</td>
                                <td><i class="fas fa-flag red"></i> De 0 a 2 días del Despacho</td>
                                <td><i class="fas fa-flag orange"></i> De 3 a 5 días del Despacho</td>
                                <td><i class="fas fa-flag green"></i> De 6 a más días del Despacho</td>
                                <td><span class="label label-primary"><i class="fas fa-warehouse"></i> Stock </span></td>
                                <td><span class="label label-success"><i class="fas fa-shopping-cart"></i> Compra</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> -->
            
            <div class="col-md-12" id="tab-reqPendientes" style="padding-left:0px;padding-right:0px;">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a type="#elaborados">Requerimientos Pendientes <span id="selaborados" class="badge badge-info"></span></a></li>
                    <li class=""><a type="#confirmados">En Compras <span id="sconfirmados" class="badge badge-info"></span></a></li>
                    <li class=""><a type="#pendientes">En Proceso <span id="spendientes" class="badge badge-info"></span></a></li>
                    <li class=""><a type="#transformados">En Transformación <span id="stransformados" class="badge badge-info"></span></a></li>
                    <li class=""><a type="#despachos">Por Despachar <span id="sdespachos" class="badge badge-info"></span></a></li>
                    <li class=""><a type="#sinTransporte">Pendientes de Transporte <span id="ssinTransporte" class="badge badge-info"></span></a></li>
                    <li class=""><a type="#retornoCargo">Pendientes de Retorno de Cargo <span id="sretornoCargo" class="badge badge-info"></span></a></li>
                </ul>
                <div class="content-tabs">
                    <section id="elaborados" >
                        <form id="form-elaborados" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="requerimientosElaborados">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Orden Elec.</th>
                                                <th>Cod.CC</th>
                                                <th>Monto </th>
                                                <th>Entidad</th>
                                                <th>Sede Req.</th>
                                                <th>Cod.Req.</th>
                                                <th>Concepto</th>
                                                <th>Fecha Req.</th>
                                                <!-- <th>Ubigeo Entrega</th>
                                                <th>Dirección Entrega</th> -->
                                                <th>Corporativo</th>
                                                <th>Generado por</th>
                                                <th>Estado</th>
                                                <th>Motivo</th>
                                                <th width="90px">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="confirmados" hidden>
                        <form id="form-confirmados" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="requerimientosConfirmados">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Orden Elec.</th>
                                                <th>Cod.CC</th>
                                                <th>Monto</th>
                                                <th>Entidad</th>
                                                <th>Sede Req.</th>
                                                <th>Cod.Req.</th>
                                                <th>Concepto</th>
                                                <th>Fecha Req.</th>
                                                <th>Ubigeo Entrega</th>
                                                <th>Dirección Entrega</th>
                                                <th>Corporativo</th>
                                                <th>Generado por</th>
                                                <th>Estado</th>
                                                <th>Motivo</th>
                                                <th width="90px">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="pendientes" hidden>
                        <form id="form-pendientes" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="requerimientosEnProceso">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Orden Elec.</th>
                                                <th>Cod.CC</th>
                                                <th>Monto</th>
                                                <th>Entidad</th>
                                                <th>Fecha Entrega</th>
                                                <th>Cod.Req.</th>
                                                <th>Fecha Req.</th>
                                                <th>Corporativo</th>
                                                <th>Generado por</th>
                                                <th>Estado</th>
                                                <th>Transf.</th>
                                                <th>O.Despacho</th>
                                                <th>Motivo</th>
                                                <th width="90px">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="transformados" hidden>
                        <form id="form-transformados" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="requerimientosEnTransformacion">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Orden Elec.</th>
                                                <th>Cod.CC</th>
                                                <th>Monto</th>
                                                <th>Entidad</th>
                                                <th>Fecha Entrega</th>
                                                <th>Cod.Req.</th>
                                                <th>Fecha Req.</th>
                                                <th>Corporativo</th>
                                                <th>Generado por</th>
                                                <th>Estado</th>
                                                <th>Transf.</th>
                                                <th>O.Despacho</th>
                                                <th>Motivo</th>
                                                <th width="90px">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="despachos" hidden>
                        <form id="form-despachos" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="ordenesDespacho">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th></th>
                                                <th>Orden Elec.</th>
                                                <th>Cod.CC</th>
                                                <th>Monto</th>
                                                <th>Entidad</th>
                                                <th>Cod.Req.</th>
                                                <th>Cliente</th>
                                                <th>Requerimiento</th>
                                                <th>Concepto</th>
                                                <!-- <th>Almacén</th>
                                                <th>Ubigeo</th>
                                                <th>Dirección Destino</th> -->
                                                <th>Fecha Despacho</th>
                                                <th>Fecha Entrega</th>
                                                <th>Corporativo</th>
                                                <th>Generado por</th>
                                                <th>Estado</th>
                                                <th>Motivo</th>
                                                <th width="70px">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    @if(Auth::user()->tieneAccion(80))
                                    <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" 
                                    title="Crear Despacho" onClick="crear_grupo_orden_despacho();">Generar Despacho</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="sinTransporte" hidden>
                        <form id="form-sinTransporte" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="gruposDespachados">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Orden Elec.</th>
                                                <th>Cod.CC</th>
                                                <th>Monto</th>
                                                <th>Entidad</th>
                                                <!-- <th>Despacho</th> -->
                                                <th>Orden Despacho</th>
                                                <th>Cod.Req.</th>
                                                <!-- <th>Cliente</th> -->
                                                <th>Concepto</th>
                                                <!-- <th>Almacén</th> -->
                                                <!-- <th>Ubigeo</th> -->
                                                <!-- <th>Dirección</th> -->
                                                <th>Corporativo</th>
                                                <th>Fecha Despacho</th>
                                                <th>Despachador</th>
                                                <th>Tipo Entrega</th>
                                                <!-- <th>Confirmación</th> -->
                                                <th>Estado</th>
                                                <th>Motivo</th>
                                                <th width="100px">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="retornoCargo" hidden>
                        <form id="form-retornoCargo" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="pendientesRetornoCargo">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Orden Elec.</th>
                                                <th>Cod.CC</th>
                                                <th>Monto</th>
                                                <th>Entidad</th>
                                                <!-- <th>Despacho</th> -->
                                                <th>Orden Despacho</th>
                                                <th>Cod.Req.</th>
                                                <!-- <th>Cliente</th> -->
                                                <th>Concepto</th>
                                                <!-- <th>Almacén</th> -->
                                                <!-- <th>Ubigeo</th> -->
                                                <!-- <th>Dirección</th> -->
                                                <th>Corporativo</th>
                                                <th>Fecha Despacho</th>
                                                <th>Despachador</th>
                                                <th>Tipo Entrega</th>
                                                <th>Estado</th>
                                                <th>Motivo</th>
                                                <th width="100px">Acción</th>
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
    </div>
</div>
@include('almacen.distribucion.requerimientoDetalle')
@include('almacen.distribucion.transferenciasDetalle')
@include('almacen.distribucion.ordenDespachoCreate')
@include('almacen.distribucion.grupoDespachoCreate')
@include('almacen.distribucion.despachoDetalle')
@include('almacen.distribucion.grupoDespachoDetalle')
@include('almacen.distribucion.ordenDespachoTransportista')
@include('almacen.distribucion.ordenDespachoObs')
@include('almacen.distribucion.requerimientoObs')
@include('almacen.distribucion.ordenDespachoAdjuntos')
@include('almacen.distribucion.ordenDespachoEstados')
@include('almacen.distribucion.ordenDespachoTransformacion')
@include('almacen.producto.productoModal')
@include('almacen.producto.productoCreate')
@include('almacen.transferencias.ver_series')

@include('logistica.cotizaciones.clienteModal')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_cliente')
@include('logistica.cotizaciones.add_proveedor')
@include('publico.personaModal')
@include('publico.ubigeoModal')
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
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/almacen/distribucion/ordenesDespacho.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/ordenDespachoCreate.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/grupoDespachoCreate.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/ordenDespachoAdjuntos.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/despachoDetalle.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/requerimientoDetalle.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/requerimientoObs.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/requerimientoVerDetalle.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/productoModal.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/productoCreate.js')}}"></script>
    <script src="{{ asset('js/logistica/clienteModal.js')}}"></script>
    <script src="{{ asset('js/logistica/proveedorModal.js')}}"></script>
    <script src="{{ asset('js/logistica/add_proveedor.js')}}"></script>
    <script src="{{ asset('js/logistica/add_cliente.js')}}"></script>
    <script src="{{ asset('js/publico/hiddenElement.js')}}"></script>
    <script src="{{ asset('js/publico/ubigeoModal.js')}}"></script>
    <script src="{{ asset('js/publico/personaModal.js')}}"></script>

    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        iniciar('{{Auth::user()->tieneAccion(80)}}');
    });
    </script>
@endsection
