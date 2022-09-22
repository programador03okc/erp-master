@extends('layout.main')
@include('layout.menu_almacen')

{{-- @if(Auth::user()->tieneAccion(129)) --}}
    @section('option')
        @include('layout.option')
    @endsection
{{-- @elseif(Auth::user()->tieneAccion(128)) --}}
    @section('option')
        @include('layout.option_historial')
    @endsection
{{-- @endif --}}

@section('cabecera')
Series-Numeros
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
  <li>Variables</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="serie_numero">
    <div class="row">
        <div class="col-md-7">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-okc-view"
                    id="listaSerieNumero">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Tipo Doc</th>
                            <th>Empresa-Sede</th>
                            <th>Serie</th>
                            <th>Numero</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-5">
            <form id="form-serie_numero" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_serie_numero" primary="ids">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Tipo de Documento</h5>
                        <select class="form-control activation js-example-basic-single" name="id_tp_documento" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($tipos as $tp)
                                <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Empresa-Sede</h5>
                        <select class="form-control activation js-example-basic-single" name="id_sede" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($sedes as $tp)
                                <option value="{{$tp->id_sede}}">{{$tp->razon_social}} - {{$tp->codigo}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Serie-Número</h5>
                        <div class="input-group">
                            <input type="text" class="form-control activation" name="serie"
                                onBlur="ceros_serie('serie');" placeholder="0000" disabled="true">
                            <span class="input-group-addon">-</span>
                            <input type="text" class="form-control activation" name="numero"
                                onBlur="ceros_numero('numero');" placeholder="0000000" disabled="true">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="group-importes"><legend><h6>Crear Números</h6></legend>
                            <div class="input-group">
                                <span class="input-group-addon"> Desde: </span>
                                <input type="number" name="numero_desde" class="form-control activation" disabled="true">
                                <span class="input-group-addon"> Hasta: </span>
                                <input type="number" name="numero_hasta" class="form-control activation" disabled="true">
                            </div>
                        </fieldset>
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
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>

    <script src="{{ asset('js/almacen/variables/serie_numero.js')}}"></script>
    <script>
        var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection
