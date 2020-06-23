@extends('layout.head')
@include('layout.menu_almacen')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Almacenes
@endsection

@section('content')
<div class="page-main" type="almacenes">
    <legend><h2>Almacenes</h2></legend>
    <div class="row">
        <div class="col-md-7">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaAlmacen">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Sede</th>
                            <th>Cód.</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            {{-- <th>Estado</th> --}}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-5">
            <form id="form-almacenes" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_almacen" primary="ids">
                <div class="row">
                    <div class="col-md-8">
                        <h5>Sede</h5>
                        <select class="form-control activation" name="id_sede" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($sedes as $sede)
                                <option value="{{$sede->id_sede}}">{{$sede->razon_social}} - {{$sede->codigo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Código</h5>
                        <input type="number" class="form-control activation" name="codigo" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Dirección</h5>
                        <input type="text" class="form-control activation" name="ubicacion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Ubigeo</h5>
                        <div class="input-group-okc">
                            <input type="text" class="oculto" name="ubigeo">
                            <input type="text" class="form-control" name="name_ubigeo" readonly placeholder="Seleccione un ubigeo">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text activation" onclick="ubigeoModal();">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        {{-- <input type="text" class="form-control activation" name="ubigeo" disabled="true"> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <h5>Tipo de Almacén</h5>
                        <select class="form-control activation" name="id_tipo_almacen" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{$tipo->id_tipo_almacen}}">{{$tipo->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('publico.ubigeoModal')
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

    <script src="{{('/js/almacen/ubicacion/almacenes.js')}}"></script>
    <script src="{{('/js/publico/ubigeoModal.js')}}"></script>
@endsection
