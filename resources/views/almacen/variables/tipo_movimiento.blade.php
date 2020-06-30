@extends('layout.main')
@include('layout.menu_almacen')

@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Documentos de Prorrateo
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('content')
<div class="page-main" type="tipoMov">
    <legend><h2>Tipos de Operación en Almacén</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaTipoMov">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Cod</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-tipo_movimiento" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="text" class="oculto"  name="id_operacion">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Tipo</h5>
                        <select class="form-control activation" name="tipo" disabled="true">
                            <option value="0" selected>Elija una opción</option>
                            <option value="1">Ingreso</option>
                            <option value="2">Salida</option>
                            <option value="3">Ingreso/Salida</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Cod.Sunat</h5>
                        <input type="text" class="form-control activation"  name="cod_sunat" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                    <h5>Estado</h5>
                    <select class="form-control activation" name="estado" readonly>
                        <option value="1" selected>Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
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
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{('/js/almacen/variables/tipo_movimiento.js')}}"></script>
@endsection
