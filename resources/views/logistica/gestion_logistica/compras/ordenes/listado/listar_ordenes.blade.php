@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Listado de Ordenes
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Compras</li>
    <li>Ordenes</li>
    <li class="active">Listado</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="listar_ordenes" id="listar_ordenes">
    <legend class="mylegend">
    </legend>

    <fieldset class="group-table">
    <div class="row">
        <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default handleClickTipoVistaPorCabecera" id="btnTipoVistaPorCabecera" title="Ver tabla a nivel de cabecera"><i class="fas fa-columns"></i> Vista a nivel de Cabecera</button>
                    <button type="button" class="btn btn-default handleClickTipoVistaPorItem" id="btnTipoVistaPorItemPara" title="Ver tabla a nivel de Items"><i class="fas fa-table"></i> Vista a nivel de Item's</button>
                </div>
            </div>
        </div>
        
    </div>

    <div class="row" id="contenedor-tabla-nivel-cabecera">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaOrdenes" style="font-size: 9px;">
                <thead>
                    <tr>
                        <th>Cuadro costos</th>
                        <th>Proveedor</th>
                        <th>Nro.orden</th>
                        <th >Req.</th>
                        <th style="width:5%">Estado</th>
                        <th>Fecha vencimiento</th>
                        <th>Fecha llegada</th>
                        <th>Estado aprobación CC</th>
                        <th>Fecha aprobación CC</th>
                        <th>Fecha Requerimiento</th>
                        <th style="width:20%">Leadtime</th>
                        <th>Empresa / Sede</th>
                        <th>Condición</th>
                        <th>Fecha em.</th>
                        <th style="width:5%">Tiem. Atenc. Log.</th>
                        <th style="width:5%">Tiem. Atenc. Prov.</th>
                        <th>Facturas</th>
                        <th>Monto Presup. CC</th>
                        <th>Monto Orden</th>
                        <th >Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="row" id="contenedor-tabla-nivel-item">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaDetalleOrden" style="font-size: 9px;">
                <thead>
                    <tr>
                    <th >OC</th>
                    <th >Req.</th>
                    <th >OC Softlink</th>
                    <th >OCAM</th>
                    <th >Cliente</th>
                    <th >Proveedor</th>
                    <th >Marca</th>
                    <th >Categoría</th>
                    <th >Part Number</th>
                    <th >Descripción</th>
                    <th >Precio Orden</th>
                    <th >Precio CC</th>
                    <th >Fecha Emisión</th>
                    <th >Plazo Entrega</th>
                    <th style="width:15%">ETA</th>
                    <th >Sede - Empresa</th>
                    <th >Estado</th>
                    <th style="width:20%">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>    
        </div>
    </div>
    
    </fieldset>
</div>



@include('logistica.gestion_logistica.compras.ordenes.listado.modal_filtro_lista_ordenes_elaboradas')
@include('logistica.gestion_logistica.compras.ordenes.listado.modal_filtro_lista_items_orden_elaboradas')
@include('logistica.gestion_logistica.compras.ordenes.listado.modal_aprobar_orden')
@include('logistica.gestion_logistica.compras.ordenes.listado.registrar_pago')

@include('logistica.gestion_logistica.compras.ordenes.listado.modal_ver_orden')
@include('logistica.gestion_logistica.compras.ordenes.listado.modal_editar_estado_orden')
@include('logistica.gestion_logistica.compras.ordenes.listado.modal_editar_estado_detalle_orden')
@include('logistica.gestion_logistica.compras.ordenes.listado.modal_documentos_vinculados')

@endsection

@section('scripts')
    <script src="{{ asset('js/util.js')}}"></script>
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <!-- <script src="{{('/js/logistica/orden/listar_ordenes.js')}}"></script> -->
    <!-- <script src="{{('/js/logistica/orden/orden_ver_detalle.js')}}"></script> -->

    <script src="{{('/js/logistica/orden/listaOrdenView.js')}}"></script>
    <script src="{{('/js/logistica/orden/listaOrdenController.js')}}"></script>
    <script src="{{('/js/logistica/orden/listaOrdenModel.js')}}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script>
        
        window.onload = function () {
        const listaOrdenModel = new ListaOrdenModel();
        const listaOrdenCtrl = new ListaOrdenCtrl(listaOrdenModel);
        const listaOrdenView = new ListaOrdenView(listaOrdenCtrl);

        listaOrdenView.init();
        listaOrdenView.initializeEventHandler();
    };

    </script>

@endsection