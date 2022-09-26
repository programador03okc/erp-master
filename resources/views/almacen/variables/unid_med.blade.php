@extends('layout.main')
@include('layout.menu_almacen')

{{-- @if(Auth::user()->tieneAccion(135)) --}}
    @section('option')
        @include('layout.option')
    @endsection
{{-- @elseif(Auth::user()->tieneAccion(134)) --}}
    @section('option')
        @include('layout.option_historial')
    @endsection
{{-- @endif --}}

@section('cabecera')
    Unidades de Medida
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
  <li>Variables</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="unidmed">
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view"
                id="listaUnidMed">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Abreviatura</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-unidmed" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Id</h5>
                        <input type="text" class="form-control" readOnly name="id_unidad_medida" primary="ids">
                    </div>
                    <div class="col-md-4">
                        <h5>Abreviatura</h5>
                        <input type="text" class="form-control activation"  name="abreviatura" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                    <h5>Estado</h5>
                    <select class="form-control activation" name="estado" readonly>
                        <option value="1" selected>Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-12">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                    </div>
                </div> --}}
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

    <script src="{{ asset('js/almacen/variables/unid_med.js')}}"></script>
    <script>
        var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection
