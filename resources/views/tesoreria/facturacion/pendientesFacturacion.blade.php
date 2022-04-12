@extends('layout.main')
@include('layout.menu_tesoreria')

@section('cabecera')
Facturación
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('tesoreria.index')}}"><i class="fas fa-tachometer-alt"></i> Tesorería</a></li>
    <li>Comprobantes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="pendientesFacturacion">

    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a data-toggle="tab" href="#guias">Ventas Internas</a></li>
                    <li class=""><a data-toggle="tab" href="#requerimientos">Ventas Externas</a></li>
                    <li class=""><a data-toggle="tab" href="#individuales">Comprobantes Individuales</a></li>
                </ul>

                <div class="tab-content">

                    <div id="guias" class="tab-pane fade in active">
                        <br>
                        <form id="form-guias" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- <div style="display: flex;justify-content: flex-end;">
                                        <button type="button" class="btn btn-success btn-flat" data-toggle="tooltip" data-placement="bottom" title="Seleccione varias Guias para ingresar Factura" onClick="open_doc_ven_create_guias_seleccionadas();">
                                            Ingresar Factura</button>
                                    </div> -->
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaGuias">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th></th>
                                                <th>Guía</th>
                                                <th>Fecha Guía</th>
                                                <th>Sede Guía</th>
                                                <th>Entidad/Cliente</th>
                                                <th>Responsable</th>
                                                <th>Cod.Trans.</th>
                                                <th style="width:8%;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>

                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="requerimientos" class="tab-pane fade ">
                        <br>
                        <form id="form-requerimientos" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaRequerimientos">
                                        <thead>
                                            <tr>
                                                <th hidden>#</th>
                                                <th>Fecha Facturación Solicitada</th>
                                                <th>Obs Facturación</th>
                                                <th>Código</th>
                                                <th>Concepto</th>
                                                <th>Sede Req</th>
                                                <th>Entidad/Cliente</th>
                                                <th>Responsable</th>
                                                <th>OCAM</th>
                                                <th>C.P.</th>
                                                <th style="width:8%;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="Individuales" class="tab-pane fade ">
                        <br>
                        <form id="form-Individuales" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaIndividuales">
                                        <thead>
                                            <tr>
                                                <th hidden>#</th>
                                                <th>Empresa</th>
                                                <th>Tipo de Doc.</th>
                                                <th>Serie-Numero</th>
                                                <th>Fecha emisión</th>
                                                <th>Fecha vcto</th>
                                                <th>Mnd</th>
                                                <th>Monto</th>
                                                <th>Entidad/Cliente</th>
                                                <th>Responsable</th>
                                                <th>Estado</th>
                                                <th style="width:8%;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

@include('tesoreria.facturacion.doc_ven_create')
@include('tesoreria.facturacion.doc_ven_ver')
@include('tesoreria.facturacion.archivos_oc_mgcp')

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<!--<script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>

<script src="{{ asset('js/tesoreria/facturacion/pendientesFacturacion.js')}}?v={{filemtime(public_path('js/tesoreria/facturacion/pendientesFacturacion.js'))}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/facturacionGuia.js')}}?v={{filemtime(public_path('js/tesoreria/facturacion/facturacionGuia.js'))}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/facturacionRequerimiento.js')}}?v={{filemtime(public_path('js/tesoreria/facturacion/facturacionRequerimiento.js'))}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}?v={{filemtime(public_path('js/tesoreria/facturacion/archivosMgcp.js'))}}"></script>
<script src="{{ asset('js/almacen/documentos/doc_ven_create.js')}}?v={{filemtime(public_path('js/almacen/documentos/doc_ven_create.js'))}}"></script>
<script src="{{ asset('js/almacen/documentos/doc_ven_ver.js')}}?v={{filemtime(public_path('js/almacen/documentos/doc_ven_ver.js'))}}"></script>
<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        vista_extendida();
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        // let facturacion = new Facturacion('{{Auth::user()->tieneAccion(78)}}');
        let facturacion = new Facturacion();
        facturacion.listarGuias();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            let tab = $(e.target).attr("href");

            if (tab == '#guias') {
                // $('#listaGuias').DataTable().ajax.reload();
                facturacion.listarGuias();
            } else if (tab == '#requerimientos') {
                // $('#listaRequerimientos').DataTable().ajax.reload();
                facturacion.listarRequerimientos();
            }
        });

    });
</script>
@endsection