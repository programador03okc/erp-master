@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Pendientes de Ingreso
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Movimientos</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="ordenesPendientes">
            <div class="col-md-12" id="tab-ordenes" style="padding-left:0px;padding-right:0px;">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a type="#pendientes">Ordenes Pendientes de Ingreso</a></li>
                    <li class=""><a type="#transformaciones">Transformaciones Pendientes de Ingreso</a></li>
                    <li class=""><a type="#ingresadas">Ingresos Procesados</a></li>
                </ul>
                <div class="content-tabs">
                    <section id="pendientes">
                        <form id="form-pendientes" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ordenesPendientes" style="width:100px;">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th></th>
                                                <th>Orden SoftLink</th>
                                                <th>Cod.Orden</th>
                                                <th>Creado por</th>
                                                <th>Días para que llegue</th>
                                                <th>Sede Orden</th>
                                                <th>Proveedor</th>
                                                <th>Fecha Emisión</th>
                                                <th>Responsable</th>
                                                <th>Estado</th>
                                                <th width="80px"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                    @if(Auth::user()->tieneAccion(83))
                                    <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" 
                                    title="Ingresar Guía de Compra" onClick="open_guia_create_seleccionadas();">Ingresar Guía</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="transformaciones" hidden>
                        <form id="form-transformaciones" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaTransformaciones" style="width:100px;">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Orden Elec.</th>
                                                <th>Cuadro Costo</th>
                                                <th>Oportunidad</th>
                                                <th>Entidad</th>
                                                <th>Código</th>
                                                <th>Fecha Transf</th>
                                                <th>Almacén</th>
                                                <th>Responsable</th>
                                                <th>Orden Despacho</th>
                                                <th>Requerimiento</th>
                                                <th>Guía</th>
                                                <th>Observación</th>
                                                <th width="80px"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="ingresadas" hidden>
                        <form id="form-ingresadas" type="register">

                            <!-- <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default" id="btnExportarExcel" title="Exportar a Excel" onclick="listaOrdenView.tipoVistaPorCabecera();"><i class="fas fa-file-excel"></i> Exportar a Excel</button>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                                
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaIngresosAlmacen">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th></th>
                                                <th>Fecha Ingreso</th>
                                                <th>Guía Compra</th>
                                                <th>R.U.C.</th>
                                                <th>Razon Social</th>
                                                <th>Ingreso</th>
                                                <th>Operación</th>
                                                <th>Almacén</th>
                                                <th>Responsable</th>
                                                <th>Ordenes</th>
                                                <th>OC SoftLink</th>
                                                <th>Facturas</th>
                                                <th width="140px"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                    <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" 
                                    title="Ingresar Factura/Boleta" onClick="open_doc_create_seleccionadas();">Ingresar Comprobante</button>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>


@include('almacen.documentos.doc_com_create')
@include('almacen.guias.ordenDetalle')
@include('almacen.guias.movimientoDetalle')
@include('almacen.guias.guia_com_create')
@include('almacen.guias.guia_com_series')
@include('almacen.guias.guia_com_obs')
@include('almacen.guias.guia_com_cambio')
@include('almacen.documentos.doc_com_ver')
@include('almacen.guias.ordenesGuias')
@include('almacen.guias.guia_com_ver')
@include('almacen.producto.productoModal')

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>

<script src="{{ asset('js/almacen/guia/ordenesPendientes.js')}}"></script>
<script src="{{ asset('js/almacen/guia/ordenes_ver_detalle.js')}}"></script>
<script src="{{ asset('js/almacen/guia/movimientoDetalle.js')}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_create.js')}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_cambio.js')}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_det_series.js')}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_det_series_edit.js')}}"></script>
<script src="{{ asset('js/almacen/documentos/doc_com_create.js')}}"></script>
<script src="{{ asset('js/almacen/documentos/doc_com_ver.js')}}"></script>
<script src="{{ asset('js/almacen/transferencias/transferenciaCreate.js')}}"></script>
<script src="{{ asset('js/almacen/producto/productoModal.js')}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        iniciar('{{Auth::user()->tieneAccion(83)}}');

    });
</script>
@endsection