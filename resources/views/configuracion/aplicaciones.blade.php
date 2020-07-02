@extends('layout.main')
@include('layout.menu_config')
@section('option')
    @include('layout.option')
@endsection
@section('cabecera')
    Aplicaciones del Sistema
@endsection
@section('content')
<div class="page-main" type="aplicaciones">
    <legend><h2>Aplicaciones del Sistema</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listaAplicaciones">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Sub modulo</th>
                            <th>Descripción</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-tipo_aporte" type="register" form="formulario">
                <input type="hidden" name="id_aplicacion" primary="ids">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Módulo</h5>
                        <select class="form-control activation" name="modulo" disabled="true" onchange="cambiarModulo(this.value);">
                            <option value="0" selected disabled>Elija una opción</option>
                            @foreach ($modulos as $modulos)
                                <option value="{{$modulos->id_modulo}}">{{$modulos->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <h5>Sub Módulo</h5>
                        <select class="form-control activation" name="sub_modulo" id="sub_modulo" disabled="true"></select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Descripcion de la aplicacion">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <h5>Link</h5>
                        <input type="text" class="form-control activation" name="ruta" disabled="true" placeholder="Link (ruta de la aplicacion)">
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
    <script src="{{('/js/configuracion/aplicaciones.js')}}"></script>
@endsection
