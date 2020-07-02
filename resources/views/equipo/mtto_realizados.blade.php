@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Mantenimientos Realizados
@endsection

@section('content')
<div class="page-main" type="mtto_realizados">
    <legend class="mylegend">
        <h2>Mantenimientos Realizados</h2>
    </legend>
    <div class="row">
        <div class="col-md-4">
            <h5>Equipo</h5>
            <select class="form-control activation" name="id_equipo">
                <option value="0" >Elija una opción</option>
                @foreach ($equipos as $item)
                    <option value="{{$item->id_equipo}}">{{$item->codigo}} - {{$item->descripcion}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <h5>Fecha Inicio</h5>
            <input type="date" class="form-control" name="fecha_inicio">
        </div>
        <div class="col-md-3">
            <h5>Fecha Fin</h5>
            <input type="date" class="form-control" name="fecha_fin">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                data-placement="bottom" title="Actualizar" 
                onClick="actualizar_reporte();">Actualizar</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-striped table-condensed table-bordered"
             id="listaMttoRealizados">
                <thead>
                    <tr>
                        <th hidden>Id</th>
                        <th></th>
                        <th>Fecha Mtto</th>
                        <th>Proveedor</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Mantenimiento</th>
                        <th>Precio</th>
                        <th>Obs</th>
                        {{-- <th>Estado</th> --}}
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
{{-- @include('equipo.mtto_programacion') --}}
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
    <script src="{{('/js/equipo/mtto_realizados.js')}}"></script>
@endsection