@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Lista de Ordenes de Despacho
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
  <li>Distribución</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="listaOrdenesDespacho">
    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" 
                            id="listaOrdenesDespacho">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th>Orden Despacho</th>
                                    <th>Fecha</th>
                                    <!-- <th>Hora</th> -->
                                    <th>Cliente</th>
                                    <th>Req.</th>
                                    <th>Concepto</th>
                                    <th>Almacén</th>
                                    <!-- <th>Fecha Desp</th> -->
                                    <th>Registrado por</th>
                                    <th>Estado</th>
                                    <th width="60px"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/almacen/distribucion/listaOrdenesDespacho.js')}}"></script>

    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        listarOrdenesDespacho();
    });
    </script>
@endsection
