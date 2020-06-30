@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Confirmación de Pagos
@endsection

@section('content')
<div class="page-main" type="requerimientoPagos">
    <legend class="mylegend">
        <h2 id="titulo">Confirmación de Pagos</h2>
    </legend>
    <div class="col-md-12" id="tab-reqPendientes">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Requerimientos Pendientes</a></li>
            <li class=""><a type="#confirmados">Requerimientos Confirmados</a></li>
        </ul>
        <div class="content-tabs">
            <section id="pendientes" >
                <form id="form-pendientes" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="requerimientosPendientes">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Tipo</th>
                                        <th>Codigo</th>
                                        <th>Concepto</th>
                                        <th>Fecha Req.</th>
                                        <th>Ubigeo Entrega</th>
                                        <th>Dirección Entrega</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
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
                                        <th>Tipo</th>
                                        <th>Codigo</th>
                                        <th>Concepto</th>
                                        <th>Fecha Req.</th>
                                        <th>Ubigeo Entrega</th>
                                        <th>Dirección Entrega</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th>Confirmación</th>
                                        <th>Observación</th>
                                        <th width="70px">Acción</th>
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
@include('almacen.distribucion.requerimientoDetalle')
@include('almacen.distribucion.requerimientoObs')
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

    <script src="{{('/js/almacen/pagos/requerimientoPagos.js')}}"></script>
    <script src="{{('/js/almacen/distribucion/requerimientoDetalle.js')}}"></script>
@endsection