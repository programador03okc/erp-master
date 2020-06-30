@extends('layout.main')
@include('layout.menu_almacen')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Tipos de Almacén
@endsection

@section('content')
<div class="page-main" type="tipo_almacen">
    <legend><h2>Tipos de Almacén</h2></legend>
    {{-- <div class="container-okc"> --}}
        <div class="row">
            <div class="col-md-7">
                <fieldset class="group-table">
                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaTipoAlmacen">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
            </div>
            <div class="col-md-5">
                <form id="form-tipo_almacen" type="register" form="formulario">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <input type="hidden" name="id_tipo_almacen" primary="ids">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Descripción</h5>
                            <input type="text" class="form-control activation" name="descripcion" disabled="true">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    {{-- </div> --}}
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

    <script src="{{('/js/almacen/ubicacion/tipo_almacen.js')}}"></script>
@endsection