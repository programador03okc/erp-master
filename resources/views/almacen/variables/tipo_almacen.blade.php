@extends('layout.main')
@include('layout.menu_almacen')

{{-- @if(Auth::user()->tieneAccion(72)) --}}
    @section('option')
        @include('layout.option')
    @endsection
{{-- @elseif(Auth::user()->tieneAccion(73)) --}}
    @section('option')
        @include('layout.option_historial')
    @endsection
{{-- @endif --}}

@section('cabecera')
    Tipos de Almacén
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
  <li>Ubicaciones</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="tipo_almacen">
    <!-- <div class="thumbnail" > -->
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
    <!-- </div> -->
</div>
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->

    <script src="{{ asset('js/almacen/ubicacion/tipo_almacen.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection
