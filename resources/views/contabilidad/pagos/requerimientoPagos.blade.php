@extends('layout.main')
@include('layout.menu_contabilidad')

@section('cabecera')
Procesar Pago de Requerimientos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('contabilidad.index')}}"><i class="fas fa-tachometer-alt"></i> Contabilidad</a></li>
  <li>Pagos</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="requerimientoPagos">

    <div class="box box-solid">
        <div class="box-body">
            
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
            
        </div>
    </div>
</div>

    
@include('contabilidad.pagos.procesarPago')
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

    <script src="{{ asset('js/contabilidad/pagos/requerimientoPagos.js')}}"></script>
    <!-- <script src="{{ asset('js/almacen/distribucion/requerimientoDetalle.js')}}"></script> -->
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);

        let requerimientoPago=new RequerimientoPago('{{Auth::user()->tieneAccion(78)}}');

        // $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        //     let tab = $(e.target).attr("href") // activated tab
        //     if (tab=='#pendientes')
        //     {
        //         $('#requerimientosPendientes').DataTable().ajax.reload();
        //     }
        //     else
        //     {
        //         $('#requerimientosConfirmados').DataTable().ajax.reload();
        //     }
        //  });
    });
    </script>
@endsection