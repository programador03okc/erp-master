@extends('layout.head')
@include('layout.menu_config')
@section('option')
@endsection
@section('cabecera')
    Notas de Lanzamiento
@endsection
@section('content')
<div class="page-main" type="modulo">
    <legend><h2>Gestión de Notas de Lanzamiento</h2></legend>
    <div class="row">
        <div class="col-md-12">
            <label>NOTAS</label>
            <div class="row">
                <div class="col-md-3">
                        <h5>Nota Lanzamiento</h5>
                        <select class="form-control" name="nota_lanzamiento"  onchange="cambiarNotaLanzamiento(this.value);">
                        </select>
                </div>
                <div class="col-md-9">
                <fieldset class="group-table">
                    <table class="tableNotas table table-hover table-condensed table-bordered table-result-form" id="listarNotasLanzamiento">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="10">Versión</th>
                                <th width="10">Version Actual?</th>
                                <th width="40">Fecha Registro</th>
                                <th width="20">
                                    <center><button class="btn btn-xs btn-success activation" name="btnAgregarNotaLanzamiento" onClick="agregarNotaLanzamiento();" id="btn-add"
                                    data-toggle="tooltip" data-placement="bottom"  title="Agregar Nota Lanzamiento"><i class="fas fa-plus"></i>
                                    </button></center>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
                </div>
            </div>

        </div>
        <div class="col-md-12">
            <label>DETALLE DE NOTA</label>

            <fieldset class="group-table">
                <table class="tableDetalleNotas table table-hover table-condensed table-bordered table-result-form" id="listarDetalleNotasLanzamiento">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="100">Título</th>
                            <th width="200">Descripción</th>
                            <th width="40">Fecha Registro</th>
                            <th width="20">
                                <center><button class="btn btn-xs btn-success activation" name="btnAgregarDetalleNotaLanzamiento" onClick="agregarDetalleNotaLanzamiento();" id="btn-add"
                                    data-toggle="tooltip" data-placement="bottom"  title="Agregar Detalle Nota Lanzamiento"><i class="fas fa-plus"></i>
                                </button></center>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>

    </div>
</div>
@include('configuracion.modal_nota_lanzamiento')
@include('configuracion.modal_detalle_nota_lanzamiento')
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
    <script src="{{('/js/configuracion/notas_lanzamiento.js')}}"></script>
@endsection
