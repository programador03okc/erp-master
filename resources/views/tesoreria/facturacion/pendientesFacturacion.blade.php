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
                    <li class="active"><a data-toggle="tab" href="#guias">Guías de Venta</a></li>
                    <!-- <li class=""><a data-toggle="tab" href="#ordenes">Ordenes de compra</a></li> -->
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
                                                <th>Serie</th>
                                                <th>Número</th>
                                                <th>Fecha Emisión</th>
                                                <!-- <th>Tipo Operación</th> -->
                                                <th>Sede Guía</th>
                                                <th>RUC</th>
                                                <th>Entidad/Cliente</th>
                                                <th>Responsable</th>
                                                <th>Cod.Req.</th>
                                                <th>OCAM</th>
                                                <th>C.P.</th>
                                                <th>Monto de O/C</th>
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

    });
</script>
@endsection