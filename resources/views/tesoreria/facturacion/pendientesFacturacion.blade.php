@extends('layout.main')
@include('layout.menu_tesoreria')

@section('cabecera')
Facturación
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
                </ul>

                <div class="tab-content">

                    <div id="guias" class="tab-pane fade in active">
                        <br>
                        <form id="form-guias" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaGuias">
                                        <thead>
                                            <tr>
                                                <th hidden>#</th>
                                                <th>Factura</th>
                                                <th>Fecha Factura</th>
                                                <th>Guía</th>
                                                <th>Fecha Guía</th>
                                                <th>Sede Guía</th>
                                                <th>Entidad/Cliente</th>
                                                <th>Responsable</th>
                                                <th>Cod.Trans.</th>
                                                <!-- <th>Cod.Req.</th>
                                                <th>OCC</th>
                                                <th>C.P.</th>
                                                <th>Monto de OCC</th> -->
                                                <th style="width:5%;">Acción</th>
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
                                                <th>Factura</th>
                                                <th>Fecha Factura</th>
                                                <th>Código</th>
                                                <th>Concepto</th>
                                                <th>Sede Req</th>
                                                <th>Entidad/Cliente</th>
                                                <th>Responsable</th>
                                                <th>OCAM</th>
                                                <th>C.P.</th>
                                                <!-- <th>Monto de O/C</th> -->
                                                <th style="width:5%;">Acción</th>
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

@include('tesoreria.pagos.procesarPago')
@include('almacen.documentos.doc_ven_create')
@include('almacen.documentos.doc_ven_ver')

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

<script src="{{ asset('js/tesoreria/facturacion/pendientesFacturacion.js')}}"></script>
<script src="{{ asset('js/almacen/documentos/doc_ven_create.js')}}"></script>
<script src="{{ asset('js/almacen/documentos/doc_ven_ver.js')}}"></script>
<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        vista_extendida();

        // let facturacion = new Facturacion('{{Auth::user()->tieneAccion(78)}}');
        let facturacion = new Facturacion();
        facturacion.listarGuias();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            let tab = $(e.target).attr("href");

            if (tab == '#guias') {
                $('#listaGuias').DataTable().ajax.reload();
            } else if (tab == '#requerimientos') {
                facturacion.listarRequerimientos();
                // $('#listaRequerimientos').DataTable().ajax.reload();
            }
        });

    });
</script>
@endsection