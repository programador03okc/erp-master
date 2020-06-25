@extends('layout.head')
@include('layout.menu_logistica')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Lista de Ordenes
@endsection

@section('content')
<div class="page-main" type="listar_ordenes">
    <legend class="mylegend">
        <h2>Listado de Ordenes</h2>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaOrdenes">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Fecha Em.</th>
                        <th>Nro.Orden</th>
                        <th>Ruc</th>
                        <th>Proveedor</th>
                        <th>Mnd</th>
                        <th>SubTotal</th>
                        <th>Igv</th>
                        <th>Total</th>
                        <th>Condición</th>
                        <th>Plazo Entrega</th>
                        <th>Cta.Prin.</th>
                        <th>Cta.Alte.</th>
                        <th>Cta.Detr.</th>
                        <th>Cod.Cuadro</th>
                        <th>Estado</th>
                        <th>Detalle Pago</th>
                        <th>Archivo Adjunto</th>
                        <th width='500px'>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-info-grupo">
    <div class="modal-dialog" style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Info</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-10">
                        <table class="table table-bordered table-condensed">
                        <tr>
                            <th>Orden</th>
                            <td id="info-numero_orden"></td>
                        </tr>
                        <tr>
                            <th>Requerimiento</th>
                            <td id="info-numero_requerimiento"></td>

                        </tr>
                        <tr>
                            <th>Grupo</th>
                            <td id="info-nombre_grupo"></td>

                        </tr>
                            <th>Area</th>
                            <td id="info-nombre_area"></td>
                        </tr>
                        
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <label style="display: none;" id="id_detalle_orden"></label>
                <label style="display: none;" id="id_val_cot"></label>
                <label style="display: none;" id="new_id_item"></label> -->
                <!-- <button class="btn btn-sm btn-primary" onClick="actualizarCodigoItem();">Actualizar</button> -->
            </div>
        </div>
    </div>
</div>

@include('logistica.ordenes.modal_explorar_orden')
@include('logistica.ordenes.modal_aprobar_orden')
@include('logistica.ordenes.registrar_pago')
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
    <script src="{{('/js/logistica/listar_ordenes.js')}}"></script>
@endsection