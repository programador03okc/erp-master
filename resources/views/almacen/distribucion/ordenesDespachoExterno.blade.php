@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Gestión de Despachos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Distribución</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="listaOrdenesDespachoExterno">
    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" id="requerimientosEnProceso">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th width="8%">Cod.Req.</th>
                                    <th>Fecha Entrega</th>
                                    <th>Orden Elec.</th>
                                    <th>Cod.CP</th>
                                    <th>Cliente/Entidad</th>
                                    <th>Generado por</th>
                                    <th>Sede Req.</th>
                                    <th>Estado</th>
                                    <th>Transf.</th>
                                    <th width="60px">Acción</th>
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
@include('almacen.distribucion.transferenciasDetalle')
@include('almacen.distribucion.ordenDespachoCreate')
@include('tesoreria.facturacion.archivos_oc_mgcp')
@include('publico.ubigeoModal')

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>

<script src="{{ asset('js/almacen/distribucion/ordenesDespachoExterno.js')}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoCreate.js')}}"></script>
<script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js')}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}"></script>
<script src="{{ asset('js/publico/ubigeoModal.js')}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        listarRequerimientosPendientes();
    });
</script>
@endsection