@extends('layout.main')
@include('layout.menu_almacen')

{{-- @if(Auth::user()->tieneAccion(133)) --}}
    @section('option')
        @include('layout.option')
    @endsection
{{-- @elseif(Auth::user()->tieneAccion(132)) --}}
    @section('option')
        @include('layout.option_historial')
    @endsection
{{-- @endif --}}

@section('cabecera')
    Tipos de Documentos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
  <li>Variables</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="tipo_doc">
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view"
                    id="listaTiposDocsAlmacen">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Cod</th>
                            <th>Descripción</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-tipo_doc" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Codigo</h5>
                        <input type="text" class="oculto" name="usuario">
                        <input type="text" class="form-control activation" readonly name="id_tp_doc_almacen" primary="ids">
                    </div>
                    <div class="col-md-6">
                        <h5>Codigo Sunat</h5>
                        <select class="form-control activation js-example-basic-single" name="id_tp_doc" disabled="true" >
                            <option value="0">Elija una opción</option>
                            @foreach ($tp_doc as $tp)
                                <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Tipo</h5>
                        <select class="form-control activation" name="tipo" readonly>
                            <option value="1" selected>Ingreso</option>
                            <option value="2">Salida</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <h5>Abreviatura</h5>
                        <input type="text" class="form-control activation" name="abreviatura">
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

    <script src="{{ asset('js/almacen/variables/tipo_doc_almacen.js')}}"></script>
    <script>
        var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection
