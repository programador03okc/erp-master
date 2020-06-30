@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Pendientes de Ingreso
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('content')
<div class="page-main" type="ordenesPendientes">
    <legend class="mylegend">
        <h2 id="titulo">Pendientes de Ingreso</h2>
    </legend>
    <div class="col-md-12" id="tab-ordenes">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Ordenes Pendientes</a></li>
            <li class=""><a type="#ingresadas">Ordenes Ingresadas Almacén</a></li>
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
                                        <th>Cod.Orden</th>
                                        <th>R.U.C.</th>
                                        <th>Razon Social</th>
                                        <th>Fecha Emisión</th>
                                        <th>Req.</th>
                                        <th>Concepto</th>
                                        <th>Responsable</th>
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
                                        <th>Cod.Orden</th>
                                        <th>Sede Orden</th>
                                        <th>R.U.C.</th>
                                        <th>Razon Social</th>
                                        <th>Req.</th>
                                        <th>Sede Req.</th>
                                        <th>Concepto</th>
                                        <th>Guía Compra</th>
                                        <th>Ingreso</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Responsable</th>
                                        <th>Trans.</th>
                                        <th width="100px"></th>
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
@include('almacen.guias.ordenesGuias')
@include('almacen.guias.guia_com_create')
@include('almacen.guias.guia_com_obs')
@include('almacen.transferencias.transferenciaGuia')
@include('almacen.guias.guia_ven_obs')
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

    <script src="{{('/js/almacen/guia/ordenesPendientes.js')}}"></script>
    <script src="{{('/js/almacen/transferencias/transferenciaGuia.js')}}"></script>
@endsection