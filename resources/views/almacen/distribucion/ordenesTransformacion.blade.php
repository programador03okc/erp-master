@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Requerimientos pendientes de Transformación
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
                        <table class="mytable table table-condensed table-bordered table-okc-view" id="requerimientosEnProceso">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th width="8%">Cod.Req.</th>
                                    <th>Fecha Entrega</th>
                                    <th>Orden Elec.</th>
                                    <th>Cod.CP</th>
                                    <!-- <th>Monto</th> -->
                                    <th>Cliente/Entidad</th>
                                    <!-- <th>Fecha Req.</th> -->
                                    <!-- <th>Corporativo</th> -->
                                    <th>Generado por</th>
                                    <th>Sede Req.</th>
                                    <th>Estado</th>
                                    <th>Transf.</th>
                                    <!-- <th>O.Despacho</th> -->
                                    <!-- <th>Motivo</th> -->
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
@include('almacen.distribucion.ordenDespachoInternoCreate')
@include('almacen.distribucion.ordenDespachoTransformacion')
@include('tesoreria.facturacion.archivos_oc_mgcp')
@include('almacen.transferencias.ver_series')

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>

<script src="{{ asset('js/almacen/distribucion/ordenesTransformacion.js')}}"></script>
<script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js')}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoInternoCreate.js')}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoTransformacion.js')}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        listarRequerimientosPendientes();
    });
</script>
@endsection