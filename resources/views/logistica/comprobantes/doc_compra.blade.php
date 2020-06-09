@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="doc_compra">
    <legend class="mylegend">
        <h2>Comprobante de Compra</h2>
        <ol class="breadcrumb">
            <li><label>Fact</label></li>
            <li><label id="serie"></label></li>
            <li><label id="numero"></label>
            {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Generar Ingreso a Almacén" onClick="generar_ingreso();">Generar Ingreso </button>
            <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Generar Factura de Compra" onClick="generar_factura();">Generar Factura </button></li> --}}
        </ol>
    </legend>
    <div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#crearNuevoComprobanteCompra" aria-controls="crearNuevoComprobanteCompra" role="tab" data-toggle="tab">Nuevo Comprobante de Compra</a></li>
                        <li role="presentation" class=""><a href="#listaComprobanteCompra" onClick="vista_extendida();" aria-controls="listaComprobanteCompra" role="tab" data-toggle="tab">Lista de Comprobantes</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="crearNuevoComprobanteCompra">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div>
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-pills nav-justified" role="tablist" id="menu_tab_crear_comprobante">
                                                <li role="presentation" class="active"><a href="#cabeceraComprobante" aria-controls="cabeceraComprobante" role="tab" data-toggle="tab">1. Crear Cabecera</a></li>
                                                <li role="presentation" class="disabled"><a href="#composicionComprobante" aria-controls="composicionComprobante" role="tab" data-toggle="tab">2. Definir Composición </a></li>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content" id="contenido_tab_crea_cabecera">
                                                <div role="tabpanel" class="tab-pane active" id="cabeceraComprobante">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                        <!-- form cabecera -->
                                                            <form id="form-doc_compra" type="register" form="formulario">
                                                                <input type="text" class="oculto" name="id_doc_com" primary="ids">
                                                                {{-- <input type="text" class="oculto" name="id_doc"> --}}
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <h5>Serie-Número</h5>
                                                                            <div class="input-group">
                                                                                <input type="text" class="form-control activation" name="serie" 
                                                                                    placeholder="F001" disabled="true">
                                                                                <span class="input-group-addon">-</span>
                                                                                <input type="text" class="form-control activation" name="numero"
                                                                                    placeholder="000000" disabled="true" onChange="ceros_numero();">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h5>Tipo de Documento</h5>
                                                                            <select class="form-control activation js-example-basic-single" 
                                                                                name="id_tp_doc" disabled="true">
                                                                                <option value="0">Elija una opción</option>
                                                                                @foreach ($tp_doc as $tp)
                                                                                    <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <h5>Fecha de Emisión</h5>
                                                                            <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <h5>Condición</h5>
                                                                            <div style="display:flex;">
                                                                                <select class="form-control group-elemento activation" name="id_condicion" 
                                                                                    onChange="change_dias();" disabled="true">
                                                                                    <option value="0">Elija una opción</option>
                                                                                    @foreach ($condiciones as $cond)
                                                                                        <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <input type="number" name="credito_dias" class="form-control activation group-elemento" style="text-align:right;" disabled/>
                                                                                <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled/>
                                                                                {{-- <span type="text" class="form-control group-elemento" style="width:100px;text-align:center;">días</span> --}}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h5>Proveedor</h5>
                                                                            <div style="display:flex;">
                                                                                <input class="oculto" name="id_proveedor"/>
                                                                                <input class="oculto" name="id_contrib"/>
                                                                                <input type="text" class="form-control" name="prov_razon_social" placeholder="Seleccione un proveedor..." 
                                                                                    aria-describedby="basic-addon1" disabled="true">
                                                                                <button type="button" class="input-group-text activation" id="basic-addon1" onClick="proveedorModal();">
                                                                                    <i class="fa fa-search"></i>
                                                                                </button>
                                                                                <button type="button" class="btn-primary activation" title="Agregar Proveedor" onClick="agregar_proveedor();">
                                                                                    <i class="fas fa-plus"></i></button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <h5>Fecha de Vencimiento</h5>
                                                                            <input type="date" class="form-control activation" name="fecha_vcmto" value="<?=date('Y-m-d');?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <h5>Moneda / Tipo de Cambio</h5>
                                                                            <div style="display:flex;">
                                                                                <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" value="S/" readOnly/>
                                                                                <input type="number" name="tipo_cambio" class="form-control group-elemento" style="text-align: right;"value="3.15248" readOnly/>
                                                                                <select class="form-control group-elemento activation" name="moneda" disabled="true">
                                                                                    <option value="0">Elija una opción</option>
                                                                                    @foreach ($moneda as $mon)
                                                                                        <option value="{{$mon->id_moneda}}">{{$mon->descripcion}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h5>Responsable</h5>
                                                                            <select class="form-control activation js-example-basic-single" 
                                                                                name="usuario" disabled="true">
                                                                                <option value="0">Elija una opción</option>
                                                                                @foreach ($usuarios as $usu)
                                                                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            {{-- <input type="button" class="btn btn-primary" onClick="getTipoCambio();" value="Tipo Cambio"/> --}}
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h5 id="registrado_por">Registrado por: <label></label></h5>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <input type="text" class="oculto" name="cod_estado">
                                                                            <h5 id="estado">Estado: <label></label></h5>
                                                                        </div>
                                                                    </div>
                                                            </form>
                                                        <!-- end form cabecera -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="composicionComprobante">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <h5>Composicion</h5>
                                                            <!-- composicion -->
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                            <fieldset class="group-importes"><legend><h6>Guía(s) de Remisión</h6></legend>
                                                                                <table id="guias" class="table-group" width="100%">
                                                                                    <thead>
                                                                                        {{-- <tr>
                                                                                            <td colSpan='5'>
                                                                                                <div style="width: 100%; display:flex;">
                                                                                                    <div style="width:90%;">
                                                                                                        <select class="form-control js-example-basic-single" name="id_guia">
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div style="width:10%;">
                                                                                                        <button type="button" class="btn btn-success" id="basic-addon2" 
                                                                                                            style="padding:5px;height:29px;font-size:12px;" 
                                                                                                            data-toggle="tooltip" data-placement="bottom" title="Agregar Guía"
                                                                                                            onClick="agrega_guia();">
                                                                                                            Agregar Guía
                                                                                                        </button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr> --}}
                                                                                        <tr>
                                                                                            <th>Guía Nro</th>
                                                                                            <th>Fecha Emisión</th>
                                                                                            <th>Proveedor</th>
                                                                                            <th>Tp Operación</th>
                                                                                            <th width="10%">
                                                                                                <i class="fas fa-plus-square icon-tabla green boton" 
                                                                                                    data-toggle="tooltip" data-placement="bottom" 
                                                                                                    title="Agregar Guía" onClick="guia_compraModal();"></i>
                                                                                            </th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <fieldset class="group-importes"><legend><h6>Orden de Compra</h6></legend>
                                                                                <table id="ordenes" class="table-group" width="100%">
                                                                                    <thead>
                                                                                        {{-- <tr>
                                                                                            <td colSpan='5'>
                                                                                                <div style="width: 100%; display:flex;">
                                                                                                    <div style="width:90%;">
                                                                                                        <select class="form-control js-example-basic-single" name="id_guia">
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div style="width:10%;">
                                                                                                        <button type="button" class="btn btn-success" id="basic-addon2" 
                                                                                                            style="padding:5px;height:29px;font-size:12px;" 
                                                                                                            data-toggle="tooltip" data-placement="bottom" title="Agregar Guía"
                                                                                                            onClick="agrega_guia();">
                                                                                                            Agregar Orden
                                                                                                        </button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr> --}}
                                                                                        <tr>
                                                                                            <th>Código Orden</th>
                                                                                            <th>Fecha Emisión</th>
                                                                                            <th>Proveedor</th>
                                                                                            <th>Tp Operación</th>
                                                                                            <th width="10%">
                                                                                                <i class="fas fa-plus-square icon-tabla green boton" 
                                                                                                    data-toggle="tooltip" data-placement="bottom" 
                                                                                                    title="Agregar Orden" onClick="orden_compraModal();"></i>
                                                                                            </th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <fieldset class="group-importes">
                                                                                <legend><h6>Items del Documento de Compra</h6></legend>
                                                                                <table class="table-group" width="100%" id="listaDetalle">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th width='10%'>Guía Nro.</th>
                                                                                            <th width='10%'>Código</th>
                                                                                            <th width='30%'>Descripción</th>
                                                                                            <th>Cantidad</th>
                                                                                            <th>Unidad</th>
                                                                                            <th>Unitario</th>
                                                                                            <th>%Dscto</th>
                                                                                            <th>Total Dscto</th>
                                                                                            <th>Precio Total</th>
                                                                                            <th width='5%'>Acción</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody></tbody>
                                                                                </table>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <button type="button" class="btn btn-secondary" data-toggle="tooltip" 
                                                                            data-placement="bottom" title="pruebs" 
                                                                            onClick="actualiza_totales();">Actualizar</button>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <table class="tabla-totales" width="100%">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td width="50%">SubTotal</td>
                                                                                        <td width="20%"></td>
                                                                                        <td><label name="simbolo_moneda"></label><input type="number" class="importe" name="sub_total" readOnly value="0"/></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Descuentos</td>
                                                                                        <td>
                                                                                            <input type="number" class="porcen activation" name="porcen_descuento" value="0"/>
                                                                                            <label>%</label>
                                                                                        </td>
                                                                                        <td><label name="simbolo_moneda"></label><input type="number" class="importe" name="total_descuento" readOnly value="0"/></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Total</td>
                                                                                        <td></td>
                                                                                        <td><label name="simbolo_moneda"></label><input type="number" class="importe" name="total" readOnly value="0"/></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>IGV</td>
                                                                                        <td>
                                                                                            <input type="number" class="porcen" name="porcen_igv" readOnly value="0"/>
                                                                                            <label>%</label>
                                                                                        </td>
                                                                                        <td><label name="simbolo_moneda"></label><input type="number" class="importe" name="total_igv" readOnly value="0"/></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                                <tfoot>
                                                                                    <tr>
                                                                                        <td><strong>Importe Total</strong></td>
                                                                                        <td></td>
                                                                                        <td><label name="simbolo_moneda"></label> <input type="number" class="importe" name="total_a_pagar" readOnly value="0"/></td>
                                                                                    </tr>
                                                                                </tfoot>
                                                                            </table>
                                                                        </div>
                                                                    </div>

                                                            <!-- end composicion -->
                                                            
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="listaComprobanteCompra">
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
@include('logistica.comprobantes.doc_compraModal')
@include('logistica.comprobantes.orden_compraModal')
@include('logistica.comprobantes.detalle_ordenModal')
@include('logistica.comprobantes.doc_com_detalle')
@include('almacen.guias.guia_compraModal')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/logistica/comprobantes/doc_compra.js')}}"></script>
<script src="{{('/js/logistica/comprobantes/doc_compraModal.js')}}"></script>
<script src="{{('/js/logistica/comprobantes/orden_compraModal.js')}}"></script>
<script src="{{('/js/logistica/comprobantes/doc_com_detalle.js')}}"></script>
<script src="{{('/js/almacen/guia_compraModal.js')}}"></script>
<script src="{{('/js/logistica/proveedorModal.js')}}"></script>
<script src="{{('/js/logistica/add_proveedor.js')}}"></script>
@include('layout.fin_html')