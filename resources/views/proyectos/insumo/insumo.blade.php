@extends('layout.main')
@include('layout.menu_proyectos')

@section('cabecera')
Lista de Insumos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
  <li>Catálogos</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="insumo">
    <div class="row">
        <div class="col-md-12">
        <div style="text-align: right;">
            <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear un Insumo Base" 
                onClick="open_insumo_create();">Crear Insumo</button>
        </div>
            <table class="mytable table table-condensed table-bordered table-okc-view" 
            id="listaInsumo">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Categoría</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Und</th>
                        <th>Ult.Precio</th>
                        <th>Flete</th>
                        <th>Peso</th>
                        <th>IU</th>
                        <th width='120px'>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            
        </div>
    </div>
</div>
@include('proyectos.insumo.insumoCreate')
@include('proyectos.variables.add_unid_med')
@include('proyectos.insumo.insumoPrecioModal')
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

<script src="{{ asset('js/proyectos/insumos/insumo.js')}}"></script>
<script src="{{ asset('js/proyectos/insumos/insumoCreate.js')}}"></script>
<script src="{{ asset('js/proyectos/variables/add_unid_med.js')}}"></script>
<script src="{{ asset('js/proyectos/insumos/insumoPrecioModal.js')}}"></script>
<script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection