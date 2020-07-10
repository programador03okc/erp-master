@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Gestión de Customizaciones
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
  <li>Customización</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="transformaciones">
    <div class="row">
        <div class="col-md-4">
            <h5>Almacén</h5>
            <select class="form-control activation" name="id_almacen">
                <option value="0">Elija una opción</option>
                @foreach ($almacenes as $alm)
                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <h5>Actualizar</h5>
            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                data-placement="bottom" title="Actualizar" 
                onClick="listarTransformaciones();">Actualizar</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaTransformaciones">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Fecha Trans.</th>
                        <th>Código</th>
                        <th>Serie-Número</th>
                        <th>Empresa</th>
                        <th>Almacén</th>
                        <th>Responsable</th>
                        <th>Registrado por</th>
                        <th>Estado</th>
                        <th width="10%">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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

    <script src="{{ asset('js/almacen/customizacion/listar_transformaciones.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection