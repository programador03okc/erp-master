@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Devolución
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
    <li>Movimientos</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="devolucion">

    <div class="box">
        <form id="form-devolucion">
            <div class="box-header with-border">

                <h3 class="box-title">Devolución N° <span class="badge badge-secondary" id="codigo">CU 00-000</span></h3>
                <div class="box-tools pull-right">

                    <button type="button" class="btn btn-sm btn-warning nueva-devolucion" data-toggle="tooltip" data-placement="bottom" 
                        title="Nueva Customización">
                        <i class="fas fa-copy"></i> Nuevo
                    </button>

                    <input id="submit_devolucion" class="btn btn-sm btn-success guardar-devolucion" type="submit" style="display: none;"
                        data-toggle="tooltip" data-placement="bottom" title="Actualizar devolucion" value="Guardar">

                    <button type="button" class="btn btn-sm btn-primary edit-devolucion" 
                        data-toggle="tooltip" data-placement="bottom" title="Editar devolucion">
                        <i class="fas fa-pencil-alt"></i> Editar
                    </button>

                    <button type="button" class="btn btn-sm btn-danger anular-devolucion" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular devolucion" onClick="anularDevolucion();">
                        <i class="fas fa-trash"></i> Anular
                    </button>

                    <button type="button" class="btn btn-sm btn-info buscar-devolucion" data-toggle="tooltip" data-placement="bottom" 
                        title="Buscar historial de registros" onClick="abrirDevolucionModal();">
                        <i class="fas fa-search"></i> Buscar</button>

                    <button type="button" class="btn btn-sm btn-secondary cancelar" data-toggle="tooltip" data-placement="bottom" 
                        title="Cancelar" style="display: none;">
                            Cancelar</button>
                            
                    {{-- <button type="button" class="btn btn-sm btn-success procesar-devolucion" data-toggle="tooltip" data-placement="bottom" 
                        title="Procesar devolucion" onClick="procesardevolucion();">
                        <i class="fas fa-share"></i> Procesar
                    </button> --}}
                </div>
            </div>
            <div class="box-body">
            
                <div class="row" style="padding-left: 10px;padding-right: 10px;margin-bottom: 0px;">
                    <div class="col-md-12">
                        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                        <input type="hidden" name="id_devolucion" primary="ids">

                        <div class="row">
                            <div class="col-md-4">
                                <label class="col-sm-4 control-label">Almacén: </label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiardevolucion" 
                                        name="id_almacen" required>
                                        <option value="">Elija una opción</option>
                                        @foreach ($almacenes as $almacen)
                                        <option value="{{$almacen->id_almacen}}">{{$almacen->codigo}} - {{$almacen->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="col-sm-2 control-label">Comentario: </label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control edition limpiardevolucion" 
                                        name="observacion" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="padding-left: 10px;padding-right: 10px;margin-top: 0px;">
                    {{-- <div class="col-md-4">
                        <label class="col-sm-4 control-label">Moneda: </label>
                        <div class="col-sm-8">
                            <select class="form-control js-example-basic-single edition limpiardevolucion" 
                                name="id_moneda" required>
                                <option value="">Elija una opción</option>
                                @foreach ($monedas as $moneda)
                                <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                    <div class="col-md-4">
                        <label>Registrado por:</label>
                        <span id="nombre_registrado_por" class="limpiarTexto"></span>
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
                        <table id="listaProductosDevolucion" class="table">
                            <thead>
                                <tr style="background: lightskyblue;">
                                    <th>Código</th>
                                    <th width='15%'>Part Number</th>
                                    <th width='50%'>Descripción</th>
                                    <th width='10%'>Cant.</th>
                                    <th>Unid.</th>
                                    <th width='8%' style="padding:0px;">
                                        <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante edition" 
                                        id="addProducto" data-toggle="tooltip" data-placement="bottom" 
                                        title="Agregar Producto" onClick="abrirProductos();"></i>
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
                    <div class="panel panel-default" style="margin-bottom: 0px;">
                        <div class="panel-heading"><strong>Ventas</strong></div>
                        <table id="listaVentas" class="table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Part Number</th>
                                    <th width='40%'>Descripción</th>
                                    <th>Cant.</th>
                                    <th>Unid.</th>
                                    <th>Unit.</th>
                                    <th>Total</th>
                                    <th width='8%' style="padding:0px;">
                                        <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante edition" 
                                        id="addSobrante" data-toggle="tooltip" data-placement="bottom" 
                                        title="Agregar Producto" onClick="agregarProductoSobrante();"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default" style="margin-bottom: 0px;">
                        <div class="panel-heading"><strong>Incidencias</strong></div>
                        <table id="listaIncidencias" class="table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Part Number</th>
                                    <th width='40%'>Descripción</th>
                                    <th>Cant.</th>
                                    <th>Unid.</th>
                                    <th>Unit.</th>
                                    <th>Total</th>
                                    <th width='8%' style="padding:0px;">
                                        <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante edition" 
                                        id="addSobrante" data-toggle="tooltip" data-placement="bottom" 
                                        title="Agregar Producto" onClick="agregarProductoTransformado();"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
        </div>
    </div>
</div>
@include('almacen.devoluciones.devolucionModal')
@include('almacen.customizacion.productoCatalogoModal')
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

<script src="{{ asset('js/almacen/devolucion/devolucion.js')}}?v={{filemtime(public_path('js/almacen/devolucion/devolucion.js'))}}"></script>
<script src="{{ asset('js/almacen/devolucion/devolucionModal.js')}}?v={{filemtime(public_path('js/almacen/devolucion/devolucionModal.js'))}}"></script>
<script src="{{ asset('js/almacen/devolucion/productosDevolucion.js')}}?v={{filemtime(public_path('js/almacen/devolucion/productosDevolucion.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/productoCatalogoModal.js')}}?v={{filemtime(public_path('js/almacen/customizacion/productoCatalogoModal.js'))}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        usuarioSession = '{{Auth::user()->id_usuario}}';
        usuarioNombreSession = '{{Auth::user()->nombre_corto}}';
    });
</script>
@endsection