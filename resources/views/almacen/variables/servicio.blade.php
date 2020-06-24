@extends('layout.head')
@include('layout.menu_logistica')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Servicio
@endsection

@section('content')
<div class="page-main" type="servicio">
    <legend class="mylegend">
        <h2>Catálogo de Servicios</h2>
        <ol class="breadcrumb">
            <li><label id="tipo_descripcion"> </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaServicio">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-servicio" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_servicio" primary="ids">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Código</h5>
                        <input type="text" class="form-control" readonly name="codigo">
                    </div>
                    <div class="col-md-8">
                        <h5>Tipo de Servicio</h5>
                        <select class="form-control activation js-example-basic-single" name="id_tipo_servicio" disabled="true">
                            <option value="0" selected>Elija una opción</option>
                            @foreach ($tipos as $tp)
                                <option value="{{$tp->id_tipo_servicio}}">{{$tp->descripcion}}</option>
                            @endforeach
                        </select>
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
                        <h5>Cuenta de Detracción</h5>
                        <select class="form-control activation js-example-basic-single" name="id_detra_det" disabled="true">
                            <option value="0" selected>Elija una opción</option>
                            @foreach ($detracciones as $det)
                                <option value="{{$det->id_detra_det}}">{{$det->cod_sunat}} - {{$det->descripcion}} - {{$det->porcentaje}}%</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                    </div>
                </div>                  
            </div>
        </div>
    </form>
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
    <script src="{{('/js/almacen/variables/servicio.js')}}"></script>

@endsection