@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Pendientes de Ingreso
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
  <li>Movimientos</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="ordenesPendientes">
    <div class="col-md-12" id="tab-ordenes"  style="padding-left:0px;padding-right:0px;">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Ordenes Pendientes de Llegada</a></li>
            <li class=""><a type="#transformaciones">Transformaciones Pendientes de Ingreso</a></li>
            <li class=""><a type="#ingresadas">Ingresos a Almacén</a></li>
        </ul>
        <div class="content-tabs">
            <section id="pendientes" >
                <form id="form-pendientes" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="ordenesPendientes" style="width:100px;">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th></th>
                                        <th>Orden SoftLink</th>
                                        <th>Cod.Orden</th>
                                        <th>Días para que llegue</th>
                                        <th>Sede Orden</th>
                                        <th>Proveedor</th>
                                        <th>Fecha Emisión</th>
                                        <!-- <th>Req.</th>
                                        <th>Concepto</th>
                                        <th>Fecha Entrega</th> -->
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <!-- <th>Mnd</th>
                                        <th>SubTotal</th>
                                        <th>IGV</th>
                                        <th>Total</th> -->
                                        <th width="80px"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                            @if(Auth::user()->tieneAccion(83))
                                <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" 
                                title="Crear Guía / Ingreso" onClick="open_guia_create_seleccionadas();">Ingresar Guía</button>
                            @endif
                        </div>
                    </div>
                </form>
            </section>
            <section id="transformaciones" hidden>
                <form id="form-transformaciones" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="listaTransformaciones" style="width:100px;">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Orden Elec.</th>
                                        <th>Cuadro Costo</th>
                                        <th>Oportunidad</th>
                                        <th>Entidad</th>
                                        <th>Código</th>
                                        <th>Fecha Transf</th>
                                        <th>Almacén</th>
                                        <th>Responsable</th>
                                        <th>Orden Despacho</th>
                                        <th>Requerimiento</th>
                                        <th>Guía</th>
                                        <th>Observación</th>
                                        <th width="80px"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="ingresadas" hidden>
                <form id="form-ingresadas" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="ordenesEntregadas">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <!-- <th>Cod.Orden</th> -->
                                        <!-- <th>Sede Guía</th> -->
                                        <th>Guía Compra</th>
                                        <th>R.U.C.</th>
                                        <th>Razon Social</th>
                                        <!-- <th>SoftLink</th> -->
                                        <!-- <th>Req.</th> -->
                                        <!-- <th>Sede Req.</th> -->
                                        <!-- <th>Concepto</th> -->
                                        <th>Ingreso</th>
                                        <th>Operación</th>
                                        <th>Almacén</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Responsable</th>
                                        <!-- <th>Trans.</th> -->
                                        <th width="90px"></th>
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
@include('almacen.guias.ordenDetalle')
@include('almacen.guias.movimientoDetalle')
@include('almacen.guias.guia_com_create')
@include('almacen.guias.ordenesGuias')
@include('almacen.guias.guia_com_obs')
@include('almacen.guias.guia_ven_obs')
@include('almacen.guias.guia_com_series')
@include('almacen.producto.productoModal')
@include('almacen.producto.productoCreate')
@include('almacen.documentos.doc_com_create')
@include('almacen.documentos.doc_com_ver')

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
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/almacen/guia/ordenesPendientes.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/ordenes_ver_detalle.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_com_create.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_com_det_series.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_com_det_series_edit.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/productoModal.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/productoCreate.js')}}"></script>
    <script src="{{ asset('js/almacen/documentos/doc_com_create.js')}}"></script>
    <script src="{{ asset('js/almacen/documentos/doc_com_ver.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        iniciar('{{Auth::user()->tieneAccion(83)}}');
    });
    </script>
@endsection