@extends('layout.main')
@include('layout.menu_proyectos')

@section('cabecera')
Gestión Nombres de A.C.U.
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
  <li>Catálogos</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="cu">
    <div class="row">
        <div class="col-md-12">
        <div style="text-align: right;">
            <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear un Nombre ACU" 
                onClick="open_acu_create();">Crear Nombre ACU</button>
        </div>
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaCu">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Categoría</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th width="130px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.acu.acuCreate')
@include('proyectos.presupuesto.verPartidaCu')
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

<script src="{{ asset('/js/proyectos/acus/cu.js') }}"></script>
<script src="{{ asset('/js/proyectos/acus/acuCreate.js') }}"></script>
@endsection