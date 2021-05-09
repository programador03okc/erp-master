@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
Compras pendientes
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Compras</li>
    <li class="active">Pendientes</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_compras_pendientes">
    <div class="row">
        <div class="col-md-12">
            <div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#requerimientos_pendientes" aria-controls="requerimientos_pendientes" role="tab" data-toggle="tab">Requerimientos pendientes</a></li>
                    <li role="presentation" class=""><a href="#buena_pros_pendientes"  aria-controls="buena_pros_pendientes" role="tab" data-toggle="tab">Buena pro's pendientes</a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="requerimientos_pendientes">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form id="form-requerimientosPendientes" type="register">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5>Empresa</h5>
                                                    <div style="display:flex;">
                                                        <select class="form-control" id="id_empresa_select_req" onChange="requerimientoPendienteView.handleChangeFilterReqByEmpresa(event);">
                                                            <option value=null>Todas las Empresas</option>
                                                            @foreach ($empresas as $emp)
                                                            <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>Sede</h5>
                                                    <div style="display:flex;">
                                                        <select class="form-control" id="id_sede_select_req" onChange="requerimientoPendienteView.handleChangeFilterReqBySede(event);" disabled>
                                                            <option value="0">Elija una opción</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>&nbsp;</h5>
                                                    <input type="checkbox" id="incluir_sede" onchange="requerimientoPendienteView.handleChangeIncluirSede(event)" /> Inlcuir Sede
                                                </div>
                                            </div>
                                            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaRequerimientosPendientes">
                                                <thead>
                                                    <tr>
                                                        <th hidden>Id</th>
                                                        <th>Selec.</th>
                                                        <th>Código</th>
                                                        <th>Concepto</th>
                                                        <th>Fecha creación</th>
                                                        <th>Tipo Req.</th>
                                                        <th>Proveedor/Entidad</th>
                                                        <th>Empresa - Sede</th>
                                                        <th>Autor</th>

                                                        <th>Estado</th>
                                                        <th width="130px">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-12 right">
                                                    <button class="btn btn-warning" type="button" id="btnCrearOrdenCompra" onClick="crearOrdenCompra();" disabled>
                                                        Crear Orden <i class="fas fa-file-invoice"></i>
                                                    </button>

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="buena_pros_pendientes">
                        <div class="panel panel-default">
                            <div class="panel-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('logistica.gestion_logistica.compras.pendientes.modal_filtro_requerimientos_pendientes')
@include('logistica.gestion_logistica.compras.pendientes.modal_ver_cuadro_costos')
@include('logistica.gestion_logistica.compras.pendientes.modal_agregar_items_requerimiento')
@include('logistica.gestion_logistica.compras.pendientes.modal_atender_con_almacen')
@include('logistica.requerimientos.modal_catalogo_items')
@include('logistica.requerimientos.modal_crear_nuevo_producto')
@include('logistica.gestion_logistica.compras.pendientes.modal_agregar_items_para_compra')
@include('logistica.gestion_logistica.compras.pendientes.modal_orden_requerimiento')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('logistica.gestion_logistica.compras.pendientes.ordenesModal')
@include('logistica.requerimientos.modal_vincular_item_requerimiento')

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
 
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{('/js/logistica/orden/RequerimientoPendienteModel.js')}}"></script>
<script src="{{('/js/logistica/orden/RequerimientoPendienteView.js')}}"></script>
<script src="{{('/js/logistica/orden/RequerimientoPendienteController.js')}}"></script>
<script src="{{('/js/logistica/CustomTabla.js')}}"></script>
 
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);

    });
</script>

@endsection