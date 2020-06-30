@extends('layout.main')
@include('layout.menu_config')
@section('option')
    @include('layout.option')
@endsection
@section('cabecera')
    Documentos
@endsection

@section('content')
<div class="page-main" type="documento">
    <legend><h2>Documentos</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listarDocumentos">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="5">id</th>
                            <th width="60">Estado Documento</th>
                            <th width="10">Color</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
        <form id="form-documento" type="register" form="formulario">
                <input type="hidden" name="id_documento" primary="ids">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Estado Documento</h5>
                        <input type="text" class="form-control activation" name="estado_documento" disabled="true" placeholder="Estado de documento">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Color</h5>
                        <input type="text" class="form-control activation" name="color" disabled="true" placeholder="Color de estado">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
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
    <script src="{{('/js/configuracion/flujo_aprobacion/documentos.js')}}"></script>
@endsection