@extends('layout.head')
@include('layout.menu_config')
@section('option')
    @include('layout.option')
@endsection
@section('cabecera')
    Módulos del Sistema
@endsection
@section('content')

<div class="page-main" type="modulo">
    <legend><h2>Módulos del Sistema</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listaModulo">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="60">Código</th>
                            <th>Descripción</th>
                            <th>Link</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-tipo_aporte" type="register" form="formulario">
                <input type="hidden" name="id_modulo" primary="ids">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Tipo</h5>
                        <select class="form-control activation" name="tipo_mod" disabled="true" onchange="cargarModulos(this.value);">
                            <option value="0" selected disabled>Elija una opción</option>
                            <option value="1">Módulo</option>
                            <option value="2">Sub Módulo</option>
                        </select>
                    </div>
                    <div class="col-md-6 oculto" id="mod">
                        <h5>Módulo</h5>
                        <select class="form-control activation" name="padre_mod" disabled="true"></select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Descripcion del modulo">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <h5>Link</h5>
                        <input type="text" class="form-control activation" name="ruta" disabled="true" placeholder="Link (ruta del modulo)">
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
    <script src="{{('/js/configuracion/modulo.js')}}"></script>

@endsection
