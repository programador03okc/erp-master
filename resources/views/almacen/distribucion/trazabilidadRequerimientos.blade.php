@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Trazabilidad de Requerimientos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
  <li>Distribución</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="trazabilidadRequerimientos">

    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" 
                            id="listaRequerimientosTrazabilidad">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th>Codigo</th>
                                    <th>Concepto</th>
                                    <th>Sede Req.</th>
                                    <th>Cliente</th>
                                    <th>Fecha Req.</th>
                                    <th>Ubigeo Entrega</th>
                                    <th>Dirección Entrega</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                    <th>O.D.</th>
                                    <th>Guía Tra.</th>
                                    <th>Importe</th>
                                    <th width="80px">Ver</th>
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
@include('almacen.distribucion.verTrazabilidadRequerimiento')
@include('almacen.distribucion.requerimientoDetalle')
@include('almacen.distribucion.ordenDespachoAdjuntos')
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
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/almacen/distribucion/trazabilidadRequerimientos.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/requerimientoDetalle.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/ordenDespachoAdjuntos.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js')}}"></script>

    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        listarTrazabilidadRequerimientos();
        vista_extendida();
    });
    </script>
@endsection
