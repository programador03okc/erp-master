@extends('layout.main')
@include('layout.menu_tesoreria')

@section('cabecera')
Procesar Pagos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('tesoreria.index')}}"><i class="fas fa-tachometer-alt"></i> Tesorería</a></li>
  <li>Pagos</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="requerimientoPagos">

    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">
                
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a data-toggle="tab" href="#comprobantes">Comprobantes</a></li>
                    <li class=""><a data-toggle="tab" href="#ordenes">Ordenes de compra</a></li>
                </ul>

                <div class="tab-content">

                    <div id="comprobantes" class="tab-pane fade in active">
                        <br>
                        <form id="form-comprobantes" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaComprobantes">
                                        <thead>
                                            <tr>
                                                <th hidden>#</th>
                                                <th>Tipo Doc.</th>
                                                <th>Serie</th>
                                                <th>Número</th>
                                                <th>Proveedor</th>
                                                <th>Fecha Emisión</th>
                                                <th>Condición</th>
                                                <th>Fecha Vencimiento</th>
                                                <th>Mnd</th>
                                                <th>Total</th>
                                                <th>Fecha Pago</th>
                                                <th>Motivo</th>
                                                <th>Procesado por</th>
                                                <th>Total pago</th>
                                                <th>Adjunto</th>
                                                <th>Estado</th>
                                                <th style="width:90px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="ordenes" class="tab-pane fade ">
                        <br>
                        <form id="form-ordenes" type="register">

                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaOrdenes">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Emp-Sede</th>
                                                <th>Codigo</th>
                                                <th>Codigo SoftLink</th>
                                                <th>Proveedor</th>
                                                <th>Fecha</th>
                                                <th>Condición Pago</th>
                                                <th>Mnd</th>
                                                <th>Total</th>
                                                <th>Fecha Pago</th>
                                                <th>Motivo</th>
                                                <th>Procesado por</th>
                                                <th>Total pago</th>
                                                <th>Adjunto</th>
                                                <th>Estado</th>
                                                <th style="width:120px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

@include('tesoreria.pagos.procesarPago')
    
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

    <script src="{{ asset('js/tesoreria/pagos/pendientesPago.js')}}"></script>
    <!-- <script src="{{ asset('js/almacen/distribucion/requerimientoDetalle.js')}}"></script> -->
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        vista_extendida();

        let requerimientoPago=new RequerimientoPago('{{Auth::user()->tieneAccion(78)}}');

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let tab = $(e.target).attr("href") // activated tab

            if (tab=='#ordenes'){
                $('#listaOrdenes').DataTable().ajax.reload();
            }
            else if (tab=='#comprobantes'){
                $('#listaComprobantes').DataTable().ajax.reload();
            }
         });
    });
    </script>
@endsection