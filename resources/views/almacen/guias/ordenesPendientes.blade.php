@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Pendientes de Ingreso
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Movimientos</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="ordenesPendientes">
            <div class="col-md-12" id="tab-ordenes" style="padding-left:0px;padding-right:0px;">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a type="#pendientes">Ordenes Pendientes de Ingreso</a></li>
                    <li class=""><a type="#transformaciones">Transformaciones Pendientes de Ingreso</a></li>
                    <li class=""><a type="#ingresadas">Ingresos Procesados</a></li>
                </ul>
                <div class="content-tabs">
                    <section id="pendientes">
                        <form id="form-pendientes" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="display: flex;">
                                        <!-- @if(Auth::user()->tieneAccion(83))
                                        <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Seleccione varias ordenes para ingresar la Guía de Compra" onClick="open_guia_create_seleccionadas();">Ingresar Guía</button>
                                        @endif -->
                                    </div>
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ordenesPendientes" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th width="3%"></th>
                                                <th width="10%">Orden SoftLink</th>
                                                <th width="10%">Cod.Orden</th>
                                                <th width="20%">Proveedor</th>
                                                <th width="12%">Fecha Emisión</th>
                                                <!-- <th width="15%">Días para que llegue</th> -->
                                                <th width="8%">Sede Orden</th>
                                                <th width="8%">Creado por</th>
                                                <th width="5%">Estado</th>
                                                <th width="6%"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="transformaciones" hidden>
                        <form id="form-transformaciones" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaTransformaciones" style="width:100px;">
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

                            <!-- <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default" id="btnExportarExcel" title="Exportar a Excel" onclick="listaOrdenView.tipoVistaPorCabecera();"><i class="fas fa-file-excel"></i> Exportar a Excel</button>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="row">
                                <div class="col-md-12">

                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaIngresosAlmacen">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th></th>
                                                <th>Fecha Ingreso</th>
                                                <th>Guía Compra</th>
                                                <th>R.U.C.</th>
                                                <th>Razon Social</th>
                                                <th>Ingreso</th>
                                                <th>Operación</th>
                                                <th>Almacén</th>
                                                <th>Responsable</th>
                                                <th>Ordenes</th>
                                                <th>OC SoftLink</th>
                                                <th>Facturas</th>
                                                <th>Requerimientos</th>
                                                <th width="140px"></th>
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
    </div>
</div>


@include('almacen.documentos.doc_com_create')
@include('almacen.guias.ordenDetalle')
@include('almacen.guias.movimientoDetalle')
@include('almacen.guias.guia_com_create')
@include('almacen.guias.guia_com_series')
@include('almacen.guias.guia_com_obs')
@include('almacen.guias.guia_com_cambio')
@include('almacen.documentos.doc_com_ver')
@include('almacen.guias.ordenesGuias')
@include('almacen.guias.guia_com_ver')
@include('almacen.producto.productoModal')
@include('tesoreria.facturacion.archivos_oc_mgcp')

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<!-- <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->

<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>

<script src="{{ asset('js/almacen/guia/ordenesPendientes.js?v=2')}}"></script>
<script src="{{ asset('js/almacen/guia/transformacionesPendientes.js')}}"></script>
<script src="{{ asset('js/almacen/guia/ingresosProcesados.js')}}"></script>
<script src="{{ asset('js/almacen/guia/ordenes_ver_detalle.js')}}"></script>
<script src="{{ asset('js/almacen/guia/movimientoDetalle.js?v=2')}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_create.js')}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_cambio.js')}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_det_series.js')}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_det_series_edit.js')}}"></script>
<script src="{{ asset('js/almacen/documentos/doc_com_create.js')}}"></script>
<script src="{{ asset('js/almacen/documentos/doc_com_ver.js')}}"></script>
<script src="{{ asset('js/almacen/transferencias/transferenciaCreate.js')}}"></script>
<script src="{{ asset('js/almacen/producto/productoModal.js')}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        iniciar('{{Auth::user()->tieneAccion(83)}}');
    });
</script>
@endsection