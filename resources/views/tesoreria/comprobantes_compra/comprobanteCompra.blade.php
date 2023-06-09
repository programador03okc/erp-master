@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Crear Comprobante Compra
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> Tesorería</a></li>
    <li>Comprobantes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="comprobanteCompra">

    <div class="box">
        <form id="form-comprobanteCompra">
            <div class="box-header with-border">

                <h3 class="box-title">Comprobante N° <span class="badge badge-secondary" id="codigo">0000-0000000</span></h3>
                <div class="box-tools pull-right">
                    
                    <button type="button" class="btn btn-sm btn-warning nueva-comprobante" data-toggle="tooltip" data-placement="bottom"
                        title="Nuevo Comprobante Compra">
                        <i class="fas fa-copy"></i> Nuevo
                    </button>
                    
                    <input id="submit_comprobante" class="btn btn-sm btn-success guardar-comprobante" type="submit" style="display: none;"
                        data-toggle="tooltip" data-placement="bottom" title="Actualizar comprobante" value="Guardar">
                    
                    <button type="button" class="btn btn-sm btn-primary edit-comprobante"
                        data-toggle="tooltip" data-placement="bottom" title="Editar comprobante">
                        <i class="fas fa-pencil-alt"></i> Editar
                    </button>
                    
                    <button type="button" class="btn btn-sm btn-danger anular-comprobante" data-toggle="tooltip" data-placement="bottom"
                        title="Anular comprobante" onClick="anularComprobante();">
                        <i class="fas fa-trash"></i> Anular
                    </button>
                    
                    <button type="button" class="btn btn-sm btn-info buscar-customizacion" data-toggle="tooltip" data-placement="bottom"
                        title="Buscar historial de registros" onClick="comprobantesModal();">
                        <i class="fas fa-search"></i> Buscar</button>
                    
                    <button type="button" class="btn btn-sm btn-secondary cancelar" data-toggle="tooltip" data-placement="bottom"
                        title="Cancelar" style="display: none;">
                            Cancelar</button>
{{--                     
                    <button type="button" class="btn btn-sm btn-success procesar-comprobante" data-toggle="tooltip" data-placement="bottom"
                        title="Procesar comprobante" onClick="procesarComprobante();">
                        <i class="fas fa-share"></i> Procesar
                    </button> --}}
                    
                </div>
            </div>
            <div class="box-body">

                <div class="row" style="padding-left: 10px;padding-right: 10px;margin-bottom: 0px;">
                    <div class="col-md-12">
                        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                        <input type="hidden" name="id_doc_com" primary="ids">

                        <div class="row">
                            <div class="col-md-4">
                                <label class="col-sm-4 control-label">Tipo de Documento: </label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single" name="id_tp_doc">
                                        <option value="0">Elija una opción</option>
                                        @foreach ($tp_doc as $tp)
                                            <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-sm-4 control-label">Serie-Número: </label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="serie_doc" placeholder="F001" required>
                                        <span class="input-group-addon">-</span>
                                        <input type="text" class="form-control" name="numero_doc" onBlur="ceros_numero_doc();" required placeholder="000000">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-sm-4 control-label">Empresa-Sede: </label>
                                <div class="col-sm-8">
                                <select class="form-control js-example-basic-single" name="id_sede">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($sedes as $sede)
                                        <option value="{{$sede->id_sede}}">{{$sede->descripcion}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="padding-left: 10px;padding-right: 10px;margin-top: 0px;">
                    <div class="col-md-4">
                        <label class="col-sm-4 control-label">Responsable: </label>
                        <div class="col-sm-8">
                            <select class="form-control js-example-basic-single edition limpiarComprobante"
                                name="id_usuario" required>
                                <option value="">Elija una opción</option>
                                @foreach ($usuarios as $usuario)
                                <option value="{{$usuario->id_usuario}}">{{$usuario->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="col-sm-5 control-label">Fecha del documento: </label>
                        <div class="col-sm-7">
                            <input type="date" class="form-control edition limpiarComprobante" name="fecha_proceso"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Registrado por:</label>
                        <span id="nombre_registrado_por" class="limpiarTexto"></span>
                    </div>
                </div>
                <div class="row" style="padding-left: 10px;padding-right: 10px;margin-top: 0px;">
                    <div class="col-md-4">
                        <label class="col-sm-4 control-label">Moneda: </label>
                        <div class="col-sm-8">
                            <select class="form-control js-example-basic-single edition limpiarComprobante"
                                name="id_moneda" required>
                                <option value="">Elija una opción</option>
                                @foreach ($monedas as $moneda)
                                <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="col-sm-5 control-label">Tipo de cambio: </label>
                        <div class="col-sm-7">
                            <input type="number" class="form-control edition limpiarComprobante" name="tipo_cambio" step="0.0001"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Fecha registro:</label>
                        <span id="fecha_registro" class="limpiarTexto"></span>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info" style="margin-bottom: 0px;">
                        <div class="panel-heading"><strong>Productos</strong></div>
                        <table id="listaMateriasPrimas" class="table">
                            <thead>
                                <tr style="background: lightskyblue;">
                                    <th>Código</th>
                                    <th>Part Number</th>
                                    <th width='30%'>Descripción</th>
                                    <th>Cant.</th>
                                    <th>Unid.</th>
                                    <th>Cos.Prom</th>
                                    <th>Unit.</th>
                                    <th>Total</th>
                                    <th width='8%' style="padding:0px;">
                                        <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante edition"
                                        id="addProductoBase" data-toggle="tooltip" data-placement="bottom"
                                        title="Agregar Producto" onClick="agregarProductoBase();"></i>
                                        <i class="fas fa-sync-alt icon-tabla boton add-new-sobrante edition"
                                        id="addProductoBase" data-toggle="tooltip" data-placement="bottom"
                                        title="Obtener el costo promedio actual" onClick="actualizarCostosBase();"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <table id="totalSobrantesTransformados" width="100%">
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default" style="margin-bottom: 0px;">
                        <div class="panel-heading"><strong>Servicios Directos</strong></div>
                        <table id="listaServiciosDirectos" class="table">
                            <thead>
                                <tr style="background: lightgray;">
                                    <th>Descripción</th>
                                    <th width='15%'>Total</th>
                                    <th style="padding:0px;">
                                        <i class="fas fa-plus-square icon-tabla green boton add-new-servicio edition"
                                        id="addServicio" data-toggle="tooltip" data-placement="bottom" title="Agregar Servicio"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-warning" style="margin-bottom: 0px;">
                        <div class="panel-heading"><strong>Costos Indirectos</strong></div>
                        <table id="listaCostosIndirectos" class="table">
                            <thead>
                                <tr style="background: navajowhite;">
                                    <!-- <th width='5%'>Nro</th> -->
                                    <th>Código Item</th>
                                    <th>Tasa(%)</th>
                                    <th>Parámetro</th>
                                    <th>Unit.</th>
                                    <th>Total</th>
                                    <th style="padding:0px;">
                                        <i class="fas fa-plus-square icon-tabla green boton add-new-indirecto edition"
                                        id="addCostoIndirecto" data-toggle="tooltip" data-placement="bottom" title="Agregar Indirecto"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>

@include('almacen.customizacion.transformacionModal')
@include('almacen.customizacion.transformacionProcesar')
@include('almacen.customizacion.productoCatalogoModal')
@include('almacen.transferencias.productosAlmacenModal')
@include('almacen.guias.guia_com_series')
@include('almacen.guias.guia_ven_series')
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
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>

<script src="{{ asset('js/almacen/customizacion/customizacion.js')}}?v={{filemtime(public_path('js/almacen/customizacion/customizacion.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transformacionModal.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transformacionModal.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/productoCatalogoModal.js')}}?v={{filemtime(public_path('js/almacen/customizacion/productoCatalogoModal.js'))}}"></script>
<script src="{{ asset('js/almacen/transferencias/productosAlmacenModal.js')}}?v={{filemtime(public_path('js/almacen/transferencias/productosAlmacenModal.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/customizacionDetalleBase.js')}}?v={{filemtime(public_path('js/almacen/customizacion/customizacionDetalleBase.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/customizacionDetalleSobrante.js')}}?v={{filemtime(public_path('js/almacen/customizacion/customizacionDetalleSobrante.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/customizacionDetalleTransformado.js')}}?v={{filemtime(public_path('js/almacen/customizacion/customizacionDetalleTransformado.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_com_det_series.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_com_det_series.js'))}}"></script>
<script src="{{ asset('js/almacen/guia/guia_ven_series.js')}}?v={{filemtime(public_path('js/almacen/guia/guia_ven_series.js'))}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        usuarioSession = '{{Auth::user()->id_usuario}}';
        usuarioNombreSession = '{{Auth::user()->nombre_corto}}';
    });
</script>
@endsection
