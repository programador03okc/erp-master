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
                    <li class="active"><a data-toggle="tab" href="#requerimientos">Requerimientos</a></li>
                    <li class=""><a data-toggle="tab" href="#comprobantes">Comprobantes</a></li>
                </ul>

                <div class="tab-content">

                    <div id="requerimientos" class="tab-pane fade in active">
                        <br>
                        <form id="form-requerimientos" type="register">

                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaRequerimientos">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <!-- <th>Tipo</th> -->
                                                <th>Codigo</th>
                                                <th>Concepto</th>
                                                <th>Fecha Req.</th>
                                                <th>Emp-Sede</th>
                                                <th>Responsable</th>
                                                <th>Monto</th>
                                                <th>Fecha Pago</th>
                                                <th>Motivo</th>
                                                <th>Procesado por</th>
                                                <th>Estado</th>
                                                <th width="90px">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div id="comprobantes" class="tab-pane fade">
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
                                                <th>Moneda</th>
                                                <th>Total a Pagar</th>
                                                <th>Fecha Pago</th>
                                                <th>Motivo</th>
                                                <th>Procesado por</th>
                                                <th>Estado</th>
                                                <th>Acción</th>
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
<!-- @include('almacen.distribucion.requerimientoDetalle')
@include('almacen.distribucion.verRequerimientoAdjuntos') -->
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

    <script src="{{ asset('js/tesoreria/pagos/requerimientoPagos.js')}}"></script>
    <!-- <script src="{{ asset('js/almacen/distribucion/requerimientoDetalle.js')}}"></script> -->
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);

        let requerimientoPago=new RequerimientoPago('{{Auth::user()->tieneAccion(78)}}');

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let tab = $(e.target).attr("href") // activated tab

            if (tab=='#requerimientos'){
                $('#listaRequerimientos').DataTable().ajax.reload();
            }
            else if (tab=='#comprobantes'){
                $('#listaComprobantes').DataTable().ajax.reload();
            }
         });
    });
    </script>
@endsection