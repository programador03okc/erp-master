@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@include('layout.option')
@endsection

@section('cabecera')
    Categoría de Equipos
@endsection

@section('content')
<div class="page-main" type="equi_cat">
    <legend><h2>Categoría de Equipos</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaEquiCat">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fecha Reg.</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-equi_cat" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input class="oculto" name="id_categoria" primary="ids">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Tipo</h5>
                        <select class="form-control activation" 
                            name="id_tipo" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{$tipo->id_tipo}}">{{$tipo->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Codigo</h5>
                        <input type="text" class="form-control" readonly name="codigo">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion">
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-6">
                    <h5>Estado</h5>
                    <select class="form-control activation" name="estado" readonly>
                        <option value="1" selected>Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-12">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
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
    <script src="{{('/js/equipo/equi_cat.js')}}"></script>
@endsection