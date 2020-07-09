@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Confirmación de Pagos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
  <li>Pagos</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="requerimientoPagos">

    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" id="tab-reqPendientes" style="padding-left:0px;padding-right:0px;">

                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a data-toggle="tab" href="#pendientes">Requerimientos Pendientes</a></li>
                    <li class=""><a data-toggle="tab" href="#confirmados">Requerimientos Confirmados</a></li>
                </ul>

                <div class="tab-content">
                    <div id="pendientes" class="tab-pane fade in active">
                        <br>
                        <form id="form-pendientes" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="requerimientosPendientes">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Tipo</th>
                                                <th>Codigo</th>
                                                <th>Concepto</th>
                                                <th>Fecha Req.</th>
                                                <th>Ubigeo Entrega</th>
                                                <th>Dirección Entrega</th>
                                                <th>Responsable</th>
                                                <th>Monto</th>
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
                    <div id="confirmados" class="tab-pane fade">
                        <br>
                        <form id="form-confirmados" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="requerimientosConfirmados">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Tipo</th>
                                                <th>Codigo</th>
                                                <th>Concepto</th>
                                                <th>Fecha Req.</th>
                                                <th>Ubigeo Entrega</th>
                                                <th>Dirección Entrega</th>
                                                <th>Responsable</th>
                                                <th>Estado</th>
                                                <th>Confirmación</th>
                                                <th>Observación</th>
                                                <th width="150px">Acción</th>
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

    
@include('almacen.distribucion.requerimientoDetalle')
@include('almacen.distribucion.requerimientoObs')
@include('almacen.distribucion.verRequerimientoAdjuntos')
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

    <script src="{{ asset('js/almacen/pagos/requerimientoPagos.js')}}"></script>
    <script src="{{ asset('js/almacen/distribucion/requerimientoDetalle.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);

        let requerimientoPago=new RequerimientoPago('{{Auth::user()->tieneAccion(78)}}');

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let tab = $(e.target).attr("href") // activated tab
            if (tab=='#pendientes')
            {
                $('#requerimientosPendientes').DataTable().ajax.reload();
            }
            else
            {
                $('#requerimientosConfirmados').DataTable().ajax.reload();
            }
         });
        //requerimientoPago.listarRequerimientosPendientes();
        //iniciar('{{Auth::user()->tieneAccion(78)}}');
    });
    </script>
@endsection