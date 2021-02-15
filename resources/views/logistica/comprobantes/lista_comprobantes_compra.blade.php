@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Lista de Comprobantes de Compra
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
    <li>Compras</li>
    <li>comprobantes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')
<div class="page-main" type="lista_comprobantes_compra">

    <div class="row">
            <div class="col-md-12">

 
                <div class="row">
                    <div class="col-sm-12">
                        <!-- <caption>Requerimientos: Registrados | Aprobados</caption> -->
                        <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaComprobantesCompra" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Serie</th>
                                    <th>Número</th>
                                    <th>Tipo Doc.</th>
                                    <th>Fecha Emisión</th>
                                    <th>Condición</th>
                                    <th>Proveedor</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Moneda</th>
                                    <th>Total a Pagar</th>
                                    <th>Estado</th>
                                    <th width="150">ACCIÓN</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

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

    <script src="{{('/js/logistica/comprobantes/listado_doc_compra.js')}}"></script>
    <script src="{{('/js/logistica/comprobantes/doc_compra.js')}}"></script>
 

@endsection