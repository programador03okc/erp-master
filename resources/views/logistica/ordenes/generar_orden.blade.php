@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="orden">
    
    <form id="form-orden" type="register" form="formulario">
    <legend class="mylegend">
        
        <h2>Generar Orden</h2>
        
        <ol class="breadcrumb">
            <li>
                <input class="oculto" name="id_grupo_cotizacion"/>
                <input class="oculto" name="id_cotizacion"/>
                <button type="button" class="btn btn-info activation" onClick="obtenerRequerimientoModal();" >
                    <i class="fas fa-file-alt"></i> 
                    Obtener Requerimiento</button>
                <button type="button" class="btn btn-warning activation" onClick="obtenerCuadroComparativoModal();" >
                    <i class="fas fa-file-invoice"></i> 
                    Obtener Cuadro Comparativo</button>
            </li>
            <li>
                <button type="button" class="btn btn-danger" data-toggle="tooltip" 
                data-placement="bottom" title="Imprimir Orden de Compra" 
                onClick="imprimir_orden();">
                <i class="fas fa-print"></i>  Imprimir </button>
            </li>
        </ol>
    </legend>
        <input type="hidden" name="id_orden_compra" primary="ids">
        <div class="row">
        
            <div class="card-body" id="group-requerimiento_seleccionado" style="background: #dbe4ec; height: 85px; position: relative; box-shadow: 0 2px 8px 0 rgba(0,0,0,0.2);" hidden>
                <input class="oculto" name="id_requerimiento"/>
                <div class="col-md-2">
                    <h5>Código Requerimiento</h5>
                    <input class="form-control" name="codigo_requerimiento" type="text" placeholder="" readonly>
                </div>
                <div class="col-md-6">
                    <h5>Concepto</h5>
                    <input class="form-control" name="concepto_requerimiento" type="text" placeholder="" readonly>
                </div>
                <div class="col-md-2">
                    <h5>Sede</h5>
                    <input class="form-control" name="sede_requerimiento" type="text" placeholder="" readonly>
                </div>
                <div class="col-md-2">
                    <h5>Fecha Requerimiento</h5>
                    <input class="form-control" name="fecha_requerimiento" type="text" placeholder="" readonly>
                </div>
            </div>
    
            <div class="col-md-2"  id="group-tipo_orden">
                <h5>Tipo</h5>
                <select class="form-control activation js-example-basic-single" 
                    name="id_tipo_doc" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($tp_documento as $tp)
                            <option value="{{$tp->id_tp_documento}}">{{$tp->descripcion}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"  id="group-codigo_orden">
                <h5>Código Orden</h5>
                <input class="form-control" id="codigo" name="codigo_orden" type="text" placeholder="OC/OS-{sede}-{añoo}-####" readonly>
            </div>
            <div class="col-md-2"  id="group-codigo_orden_externo" hidden>
                <h5>Código Orden (SoftLink)</h5>
                <input class="form-control" name="codigo_orden_externo" type="text" placeholder="">
            </div>
            <div class="col-md-2" id="group-sede" hidden>
                <h5>Sede</h5>
                    <select name="sede" class="form-control activation"  required>
                        @foreach ($sedes as $sede)
                            <option value="{{$sede->id_sede}}">{{ $sede->descripcion}}</option>
                        @endforeach                    
                    </select>
            </div>
            <div class="col-md-2" id="group-fecha_orden">
                <h5>Fecha</h5>
                <input class="form-control" id="fecha" type="text" placeholder="DD/MM/AA" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4" id="group-proveedor">
                <h5>Proveedor</h5>
                <div style="display:flex;">
                    <input class="oculto" name="id_proveedor"/>
                    <input class="oculto" name="id_contrib"/>
                    <input type="text" class="form-control" name="razon_social" disabled
                        aria-describedby="basic-addon1" required>
                    <button type="button" class="group-text" id="basic-addon1" onClick="proveedorModal();">
                        <i class="fa fa-search"></i>
                    </button> 
                    <button type="button" class="btn-primary activation" title="Agregar Proveedor" onClick="agregar_proveedor();"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="col-md-4"  id="group-condicion">
                <h5>Condición</h5>
                <div style="display:flex;">
                    <select class="form-control group-elemento activation" name="id_condicion" onchange="handlechangeCondicion(event);"
                        style="width:120px;text-align:center;" disabled="true">
                        @foreach ($condiciones as $cond)
                            <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                        @endforeach
                    </select>
                    <input type="number" name="plazo_dias" class="form-control activation group-elemento" style="text-align:right;" disabled/>
                    <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled/>
                </div>
            </div>
            <div class="col-md-2"  id="group-plazo_entrega">
                <h5>Plazo Entrega</h5>
                <div style="display:flex;">
                    <input type="number" name="plazo_entrega" class="form-control activation group-elemento" style="text-align:right;" disabled/>
                    <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled/>
                </div>
            </div>
            <div class="col-md-2"  id="group-moneda">
                <h5>Moneda</h5>
                <select class="form-control activation js-example-basic-single" 
                    name="id_moneda" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($tp_moneda as $tpm)
                        <option value="{{$tpm->id_moneda}}">{{$tpm->descripcion}} ( {{$tpm->simbolo}} )</option>
                    @endforeach
                </select>
            </div>
        </div>

   
        <div class="row">
            <div class="col-md-3"  id="group-tipo_documento">
                <h5>Tipo de Documento</h5>
                <select class="form-control activation js-example-basic-single" 
                    name="id_tp_documento" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($tp_doc as $tp)
                        <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                    @endforeach
                </select>
            </div>
            <div id="group-cuentas">
                <div class="col-md-3">
                    <h5>N° Cuenta Principal</h5>
                    <input class="oculto" name="nro_cuenta_principal"/>
                    <div style="display:flex;">
                        <select class="form-control activation" name="id_cta_principal"></select>
                        <button type="button" class="btn-primary activation" title="Agregar Cuenta Banco" onClick="agregar_cta_banco(1,1);">
                            <i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>N° Cuenta Alternativa</h5>
                    <input class="oculto" name="nro_cuenta_alternativa"/>
                    <select class="form-control activation" name="id_cta_alternativa"></select>
                </div>
                <div class="col-md-3">
                    <h5>N° Cuenta Detracción</h5>
                    <div style="display:flex;">
                        <input class="oculto" name="nro_cuenta_detraccion"/>
                        <select class="form-control activation" name="id_cta_detraccion"></select>
                        <button type="button" class="btn-primary activation" title="Agregar Cuenta Detracción" onClick="agregar_cta_banco(2,4);">
                            <i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaDetalleOrden" width="100%">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="80">COD. ITEM</th>
                            <th width="200">PRODUCTO</th>
                            <th width="30">UNIDAD</th>
                            <th width="50">CANTIDAD</th>
                            {{-- <th width="100">GARANTÍA</th> --}}
                            <th width="50">PRECIO</th>
                            <th width="50">DESCUENTO</th>
                            <th width="50">TOTAL</th>
                            <th width="20">DESC. ADICIONAL</th>
                            <th width="20">DESPACHO</th>
                            <th width="20">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- <tr>
                            <td></td>
                            <td colspan="7"> No hay datos registrados</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="7"></td>
                            <th scope="row">Descuento</th>
                            <td><input type="text" class="form-control icd-okc" name="Descuento" placeholder="S/.0"></td>
                        </tr>
                        <tr>
                            <td colspan="7"></td>
                            <th scope="row">Monto Neto</th>
                            <td><input type="text" class="form-control icd-okc" name="monto_neto" placeholder="S/.0"></td>
                        </tr>
                        <tr>
                            <td colspan="7"></td>
                            <th scope="row">Monto con IGV</th>
                            <td><input type="text" class="form-control icd-okc" name="monto_con_igv" placeholder="S/.0"></td>
                        </tr>
                        <tr>
                            <td colspan="7"></td>
                            <th scope="row">Monto Sub-Total</th>
                            <td><input type="text" class="form-control icd-okc" name="monto_sub_total" placeholder="S/.0"></td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6" id="group-responsable">
                <input type="text" name="cod_estado" hidden/>
                <h5 id="estado">Estado: <label></label></h5>
                <div class="row">   
                    <div class="col-md-9">   
                        <h5>Contacto Responsable</h5>
                        <select class="form-control activation js-example-basic-single" name="contacto_responsable">
                            <option value="0">Elija una opción</option>
                            @foreach ($contactos as $contacto)
                                <option value="{{$contacto->id_datos_contacto}}">{{$contacto->nombre}} - {{$contacto->cargo}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6" id="group-totales">
                <table class="tabla-totales" width="100%">
                    <tbody>
                        <tr>
                            <td width="50%">SubTotal</td>
                            <td width="20%"></td>
                            <td><input type="number" class="importe" name="monto_subtotal" readOnly value="0"/></td>
                        </tr>
                        {{-- <tr>
                            <td>Descuentos</td>
                            <td>
                                <input type="number" class="porcen activation" name="porcen_descuento" readOnly value="0"/>
                                <label>%</label>
                            </td>
                            <td><input type="number" class="importe" name="total_descuento" readOnly value="0"/></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td></td>
                            <td><input type="number" class="importe" name="total" readOnly value="0"/></td>
                        </tr> --}}
                        <tr>
                            <td>IGV</td>
                            <td>
                                <input type="number" class="porcen activation" name="igv_porcentaje" readOnly value="0"/>
                                <label>%</label>
                            </td>
                            <td><input type="number" class="importe" name="monto_igv" readOnly value="0"/></td>
                        </tr>
                        {{-- <tr>
                            <td>Otros Cargos</td>
                            <td>
                            </td>
                            <td><input type="number" class="importe" name="otros" readOnly value="0"/></td>
                        </tr> --}}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>Importe Total</strong></td>
                            <td></td>
                            <td><input type="number" class="importe" name="monto_total" readOnly value="0"/></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
    </form>
</div>
@include('logistica.ordenes.modal_despacho')
@include('logistica.ordenes.modal_actualizar_item')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('logistica.ordenes.modal_obtener_requerimiento')
@include('logistica.ordenes.modal_obtener_cuadro_comparativo')
@include('logistica.ordenes.ordenesModal')
@include('logistica.ordenes.add_cta_banco')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/logistica/generar_orden.js')}}"></script>
<script src="{{('/js/logistica/proveedorModal.js')}}"></script>
<script src="{{('/js/logistica/add_proveedor.js')}}"></script>
<script src="{{('/js/logistica/ordenesModal.js')}}"></script>
<script src="{{('/js/logistica/add_cta_banco.js')}}"></script>
<script src="{{('/js/logistica/orden_requerimiento.js')}}"></script>
@include('layout.fin_html')