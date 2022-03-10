@extends('layout.main')
@include('layout.menu_tesoreria')

@section('cabecera')
Registro de pagos
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
                    <li class="active"><a data-toggle="tab" href="#requerimientos">Requerimiento de pagos</a></li>
                    <li class=""><a data-toggle="tab" href="#ordenes">Ordenes Compra/Servicio</a></li>
                    {{-- <li class=""><a data-toggle="tab" href="#comprobantes">Comprobantes</a></li> --}}
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
                                                <th hidden>#</th>
                                                <th>Prio.</th>
                                                <th>Empresa</th>
                                                <th>Código</th>
                                                <th>Grupo</th>
                                                <th>Concepto</th>
                                                <th>Nro. Doc.</th>
                                                <th>Destinatario</th>
                                                <th>Fecha Emisión</th>
                                                <th>Mnd</th>
                                                <th>Total</th>
                                                <th>Saldo</th>
                                                <th>Estado</th>
                                                <th style="width:80px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 14px;"></tbody>
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
                                                <th>Prio.</th>
                                                <th>Cod.Req.</th>
                                                <th>Empresa</th>
                                                <th>Codigo</th>
                                                {{-- <th>Codigo SoftLink</th> --}}
                                                <th>Nro. Doc.</th>
                                                <th>Razon social del proveedor</th>
                                                <th>Fecha emisión</th>
                                                <th>Forma de Pago</th>
                                                {{-- <th>Cta. Bancaria</th> --}}
                                                <th>Mnd</th>
                                                <th>Total</th>
                                                <th>Saldo</th>
                                                <th>Estado</th>
                                                <th style="width:80px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 14px;"></tbody>
                                    </table>
                                </div>
                            </div>

                        </form>
                    </div>

                    {{-- <div id="comprobantes" class="tab-pane fade ">
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
                                                <th>Razon social del proveedor</th>
                                                <th>Fecha Emisión</th>
                                                <th>Condición</th>
                                                <th>Fecha Vencimiento</th>
                                                <th>Cta. Bancaria</th>
                                                <th>Mnd</th>
                                                <th>Total</th>
                                                <th>Saldo</th>
                                                <th>Estado</th>
                                                <th style="width:80px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div> --}}

                </div>

            </div>
        </div>
    </div>
</div>

@include('tesoreria.pagos.procesarPago')
@include('tesoreria.pagos.verAdjuntos')
@include('tesoreria.pagos.verAdjuntosPago')
@include('tesoreria.requerimiento_pago.modal_vista_rapida_requerimiento_pago')

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
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>

    <script src="{{ asset('js/tesoreria/pagos/pendientesPago.js')}}"></script>
    <script src="{{ asset('js/tesoreria/pagos/procesarPago.js')}}"></script>
    {{-- <script src="{{ asset('js/tesoreria/requerimientoPago/ListarRequerimientoPagoView.js')}}?v={{filemtime(public_path('js/Tesoreria/requerimientoPago/ListarRequerimientoPagoView.js'))}}"></script> --}}

    <script src="{{ asset('js/tesoreria/pagos/modalVistaRapidaRequerimiento.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        vista_extendida();

        let requerimientoPago=new RequerimientoPago('{{Auth::user()->tieneAccion(137)}}','{{Auth::user()->tieneAccion(138)}}','{{Auth::user()->tieneAccion(139)}}');
        // let requerimientoPago=new RequerimientoPago('1','1','1');

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let tab = $(e.target).attr("href") // activated tab

            if (tab=='#ordenes'){
                $('#listaOrdenes').DataTable().ajax.reload();
            }
            else if (tab=='#comprobantes'){
                $('#listaComprobantes').DataTable().ajax.reload();
            }
            else if (tab=='#requerimientos'){
                $('#listaRequerimientos').DataTable().ajax.reload();
            }
         });
    });
    </script>
@endsection