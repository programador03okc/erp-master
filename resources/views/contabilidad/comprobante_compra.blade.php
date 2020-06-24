@extends('layout.head')
@include('layout.menu_contabilidad')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Generar Comprobante de Compra
@endsection

@section('content')
<div class="page-main" type="facturacion_generar_factura">
    <legend><h2>Generar Comprobante de Compra</h2></legend>
        <div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#createNuevaComprobanteCompraTab" aria-controls="createNuevaComprobanteCompraTab" role="tab" data-toggle="tab">Crear Nueva Comprobante de Compra</a></li>
                        <li role="presentation" class=""><a href="#listaComprobanteCompraTab"  aria-controls="listaComprobanteCompraTab" role="tab" data-toggle="tab">Lista de Combrobantes de compra</a></li>
                    </ul>
                    <!-- Tab panes -->

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="createNuevaComprobanteCompraTab">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div>
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-pills nav-justified" role="tablist" id="menu_tab_crear_factura">
                                                <li role="presentation" class="active"><a href="#ordenes" aria-controls="ordenes" role="tab" data-toggle="tab">1. Seleccionar Orden</a></li>
                                                <li role="presentation" class="disabled"><a href="#crear_comprobante_compra" aria-controls="crear_comprobante_compra" role="tab" data-toggle="tab">2. Generar Factura</a></li>
                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content" id="contenido_tab_crear_factura">
                                                <div role="tabpanel" class="tab-pane active" id="requerimiento">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <h5>Buscar y Seleccionar Orden(s)</h5>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <h5>Empresa</h5>
                                                                    <div style="display:flex;">
                                                                    <select class="form-control" id="id_empresa_select_req" onChange="handleChangeFilterReqByEmpresa(event);">
                                                                    <option value="0" disabled>Elija una opción</option>
                                                                        @foreach ($empresas as $emp)
                                                                        @if($emp->razon_social == 'OK COMPUTER EIRL')
                                                                            <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}" selected>{{$emp->razon_social}}</option>
                                                                            @else
                                                                            <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                                                        @endif
                                                                        @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                                            id="ListaOrdenes">
                                                                <thead>
                                                                    <tr>
                                                                        <th hidden>Id</th>
                                                                        <th>Fecha Emisión</th>
                                                                        <th>Código</th>
                                                                        <th>Proveedor</th>
                                                                        <th>Moneda</th>
                                                                        <th>SubTotal</th>
                                                                        <th>IGV</th>
                                                                        <th>Total</th>
                                                                        <th>Condición</th>
                                                                        <th>Plazo Entrega (Dias)</th>
                                                                        <th>Acción</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="crear_comprobante_compra">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">                                                        
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <form id="form_crear_comprobante_compra">
                                                                        <div class="row">
                                                                            <input class="oculto" name="id_usuario_session"/>
                                                                            <input class="oculto" name="id_orden"/>
                                                                            <input class="oculto" name="id_sede"/>
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                    <div class="col-md-1">
                                                                                        <h5>Serie</h5>
                                                                                        <input type="text" class="form-control" name="serie" placeholder="" value="001">
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <h5>Número</h5>
                                                                                        <input type="text" class="form-control" name="numero" placeholder="" disabled>
                                                                                    </div>
                                                                                    <div class="col-md-7">
                                                                                        <h5>Proveedor</h5>
                                                                                        <input class="oculto" name="id_proveedor"/>
                                                                                        <input type="text" class="form-control" name="proveedor" placeholder="" >
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <h5>Fecha Emisión</h5>
                                                                                        <input type="date" class="form-control" name="fecha_emision" placeholder="" >
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                        <div class="col-md-2">
                                                                                            <h5>Fecha Vencimiento</h5>
                                                                                            <input type="date" class="form-control" name="fecha_vencimiento" placeholder="" >
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <h5>Condición</h5>
                                                                                            <div style="display:flex;">
                                                                                                <select class="form-control group-elemento" name="id_condicion" onchange="handlechangeCondicion(event);" style="width:120px;text-align:center;">
                                                                                                    <option value="1">CONTADO CA</option>
                                                                                                    <option value="2">CREDITO</option>
                                                                                                </select>
                                                                                                <input type="number" name="plazo_dias" class="form-control group-elemento" style="width:60px; text-align:right;">
                                                                                                <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled="">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <h5>Moneda</h5>
                                                                                            <select class="form-control" name="id_moneda">
                                                                                            @foreach ($monedas as $moneda)
                                                                                                <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                                                                            @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <h5>Tipo Cambio</h5>
                                                                                            <input type="text" class="form-control" name="tipo_cambio" placeholder="" >
                                                                                        </div>
                                                                                    <div class="col-md-1">
                                                                                        <h5>% Desc.</h5>
                                                                                        <input type="text" class="form-control" name="porcentaje_descuento" placeholder="" onInput="inputPorcentajeDescKeyPress(event);" >
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <h5>Monto Descuento</h5>
                                                                                        <input type="text" class="form-control" name="monto_descuento" placeholder="" onInput="inputMontoDescKeyPress(event);">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="row">

                                                                                    <div class="col-md-2">
                                                                                        <h5>SubTotal</h5>
                                                                                        <input type="text" class="form-control" name="monto_subtotal" placeholder="" >
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <h5>IGV</h5>
                                                                                        <input type="text" class="form-control" name="monto_igv" placeholder="" >
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <h5>Total</h5>
                                                                                        <input type="text" class="form-control" name="monto_total" placeholder="" >
                                                                                        <input type="hidden" class="form-control" name="monto_total_fijo" placeholder="" >
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>

                                                                        <div class="row">
                                                                            <div class="col-md-12 center">
                                                                            <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaDetalleOrden">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th hidden>Id</th>
                                                                                            <th>Código</th>
                                                                                            <th>Descripción</th>
                                                                                            <th>Cantidad</th>
                                                                                            <th>Unidad</th>
                                                                                            <th>Precio</th>
                                                                                            <th>Incluye IGV?</th>
                                                                                            <th>IGV</th>
                                                                                            <th>Monto Sin IGV</th>
                                                                                            <th>SubTotal</th>
                                                                                            <th>Monto Descuento</th>
                                                                                            <th>% Descuento</th>
                                                                                            <th>Total</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </div>                    
                 
                                                                        </div> 
                                                                        <div class="row">                   
                                                                        <div class="col-md-12 right">
                                                                            <button class="btn btn-default" role="button"   onClick="gotToSecondToFirstTab(event);">
                                                                                    Atras <i class="fas fa-arrow-circle-left"></i>
                                                                            </button>
                                                                                <button class="btn btn-success" role="button"   onClick="generar_comprobante_compra(event);">
                                                                                        Generar Comprobante de Compra <i class="fas fa-save"></i>
                                                                                </button>
                                                                            </div>   
                                                                        </div>                    
                                                                </div>   
                                                            </div>   
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="listaComprobanteCompraTab">
                            <div class="panel panel-default">
                                <div class="panel-body" style="position: relative; overflow: auto; width: 100%;">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                    id="listaComprobanteCompra">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>#</th>
                                                <th>Fecha Emisión</th>
                                                <th>CODIGO</th>
                                                <th width="250">Proveedor</th>
                                                <th>Empresa</th>
                                                <th>Sede</th>
                                                <th>Fecha Vencimiento</th>
                                                <th>Condición</th>
                                                <th>Moneda</th>
                                                <th>Tipo Cambio</th>
                                                <th>% Descuento</th>
                                                <th>Monto Descuento</th>
                                                <th>Monto Sub-Total</th>
                                                <th>Monto IGV</th>
                                                <th>Monto Total</th>
                                                <th>Fecha Registro</th>
                                                <th width="90">ACCIÓN</th>
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
        </div>
</div>
@include('contabilidad.modal_editar_comprobante_compra')

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
    <script src="{{('/js/contabilidad/comprobante_compra.js')}}"></script>

@endsection