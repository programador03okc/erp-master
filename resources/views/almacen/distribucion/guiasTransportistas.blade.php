@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Trazabilidad de Envío
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
  <li>Distribución</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="guiasTransportistas">
    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" 
                            id="listaGuiasTransportistas">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th>OCAM</th>
                                    <th>Cod.Req.</th>
                                    <th>OD</th>
                                    <th>Fecha Despacho</th>
                                    <th>Fecha Max Entrega</th>
                                    <th>Cliente</th>
                                    <th>Guía</th>
                                    <th>Transportista</th>
                                    <th>Fecha</th>
                                    <th>Codigo Envío</th>
                                    <th>Importe</th>
                                    <th>Extras</th>
                                    <th>Crédito</th>
                                    <th>Estado</th>
                                    <th>Entrega a Tiempo</th>
                                    <th>Obs</th>
                                    <th width="35px"></th>
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
    <!-- <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/almacen/distribucion/guiasTransportistas.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js')}}"></script>

    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection
