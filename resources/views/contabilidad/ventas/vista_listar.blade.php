@extends('layout.main')
@include('layout.menu_contabilidad')

@section('option')
@endsection

@section('cabecera')
    Lista de Ventas
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Contabilidad</a></li>
    <li>Ventas</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_ventas">
    <fieldset class="group-table">   
        <form id="form-listaVentas" type="register">
            <div class="row">
                <div class="col-md-6">
                    <div style="display: flex;">
                        <button type="button" class="btn btn-default btn-flat" onclick="openFiltroVenta();"><i class="fa fa-filter"></i> Filtros </button>
                        <button type="button" class="btn btn-default btn-flat" onclick="openExportarListaVenta();"><i class="fas fa-file-export"></i> Exportar Lista </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <!-- <caption>Requerimientos: Registrados | Aprobados</caption> -->
                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaReq" width="100%">
                        <thead>
                            <tr>
                                <th>EMP.</th>
                                <th>F. EMI. COMPROB.</th>
                                <th>DOC</th>
                                <th>NRO</th>
                                <th>RAZÓNN SOCIAL/ NOMBRE</th>
                                <th>CÓD. PRODUCTO</th>
                                <th>CANTIDAD</th>
                                <th>AUTOR FACT. EMITIDA</th>
                                <th>MONEDA</th>
                                <th>IMPORTE</th>
                                <th>ACCIÓN</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </form>
    </fieldset> 
</div>



<!-- 1re include para evitar error al cargar modal -->
@include('contabilidad.ventas.modal_filtros_ventas')
@include('contabilidad.ventas.modal_exportar_lista_ventas')
 <!--  includes -->
 

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
 
    <script src="{{ asset('js/contabilidad/ventas/modal_filtros_ventas.js') }}"></script>
    <script src="{{ asset('js/contabilidad/ventas/modal_exportar_lista_ventas.js') }}"></script>

    <script>

$(document).ready(function(){
        seleccionarMenu(window.location);

    });

    </script>
@endsection