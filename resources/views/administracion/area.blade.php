@extends('layout.main')
@include('layout.menu_admin')
@section('option')
    @include('layout.option')
@endsection
@section('cabecera')
    Areas
@endsection

@section('content')
<div class="page-main" type="area">
    <legend><h2>Areas</h2></legend>
    <div class="row">
        <div class="col-md-12">
            <form id="form-area" type="register" form="formulario">
                <input type="hidden" name="id_area" primary="ids">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Empresa</h5>
                        <select class="form-control activation" name="empresa" disabled="true" onchange="buscarSede(this.value, 'nuevo', 0);">
                            <option value="0" selected disabled>Elija una empresa</option>
                            @foreach ($emp as $emp)
                                <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Sede</h5>
                        <select class="form-control activation" name="sede" disabled="true" onchange="buscarGrupo(this.value, 'nuevo', 0);">
                            <option value="0" selected disabled>Elija una opción</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Grupo</h5>
                        <select class="form-control activation" name="grupo" disabled="true">
                            <option value="0" selected disabled>Elija una opción</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <h5>Código / Descripción</h5>
                        <div class="flexAccion">
                            <input type="text" class="form-control activation" name="codigo" disabled="true" maxlength="4" placeholder="----"
                            style="width: 20%; text-align: center;">
                            <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Nombre del area"
                            style="width: 80%;">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-8">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listaArea">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Empresa</th>
                            <th>Sede</th>
                            <th>Grupo</th>
                            <th>Area</th>
                            <th>Código</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
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
    <script src="{{('/js/administracion/area.js')}}"></script>
@endsection
