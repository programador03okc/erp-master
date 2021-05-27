@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Elaborar orden
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Órdenes</li>
    <li class="active">Elaborar</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="crear-orden-requerimiento">
    <form id="form-crear-orden-requerimiento" type="register" form="formulario">
    <input type="hidden" name="id_orden" primary="ids">

        <div class="row">
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">General &nbsp;<span style="color:blue;" name="codigo_orden_interno"></span> </h4> 
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-2"  id="group-tipo_orden">
                            <h5>Tipo de orden</h5>
                            <select class="form-control activation" name="id_tp_documento" >
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_documento as $tp)
                                    @if($tp->descripcion == 'Orden de Compra')
                                            <option value="{{$tp->id_tp_documento}}" selected>{{$tp->descripcion}}</option>
                                    @else
                                            <option value="{{$tp->id_tp_documento}}">{{$tp->descripcion}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2" id="group-fecha_orden">
                            <h5>Moneda</h5>
                            <select class="form-control activation" name="id_moneda" onChange="ordenView.changeMoneda(this);">
                                @foreach ($tp_moneda as $tpm)
                                    <option value="{{$tpm->id_moneda}}" data-simbolo-moneda="{{$tpm->simbolo}}" >{{$tpm->descripcion}} ( {{$tpm->simbolo}} )</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2" id="group-codigo_orden" >
                            <h5>Código orden softlink</h5>
                            <input class="form-control activation" name="codigo_orden" type="text" placeholder="">
                        </div>
                        <div class="col-md-2" id="group-fecha_emision_orden" >
                            <h5>Fecha emisión</h5>
                            <div style="display:flex">
                                <input class="form-control activation" name="fecha_emision" type="datetime-local"  value={{ date('Y-m-d\TH:i') }}>
                                <button type="button" class="group-text" onClick="fechaHoy();">
                                HOY
                                </button> 
                            </div>
                        </div>
                        <div class="col-md-2" id="group-datos_para_despacho-sede">
                            <h5>Empresa - Sede</h5>
                            <select class="form-control activation " name="id_sede" onChange="ordenView.changeSede(this);">
                                @foreach ($sedes as $sede)
                                    <option value="{{$sede->id_sede}}" data-id-empresa="{{$sede->id_empresa}}" data-direccion="{{$sede->direccion}}"  data-id-ubigeo="{{$sede->id_ubigeo}}" data-ubigeo-descripcion="{{$sede->ubigeo_descripcion}}" >{{$sede->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <h5>&nbsp;</h5>
                        <div class="col-md-2" id="group-datos_para_despacho-logo_empresa">
                                <img id="logo_empresa" src="/images/img-wide.png" alt=""  style="height:56px;!important;width:100%;!important;margin-top:-20px;">
                        </div>

                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4>Datos del proveedor</h4>
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-6" id="group-nombre_proveedor">
                            <h5>Proveedor</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_proveedor">
                                <input class="oculto" name="id_contrib">
                                <input type="text" class="form-control" name="razon_social" disabled >
                                <button type="button" class="group-text" onClick="proveedorModal();">
                                    <i class="fa fa-search"></i>
                                </button> 
                                <button type="button" class="btn-primary" title="Agregar Proveedor" onClick="agregar_proveedor();"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6" id="group-direccion_proveedor">
                            <h5>Dirección de proveedor</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="direccion_proveedor" readOnly>
                            </div>
                        </div>

                        <div class="col-md-3" id="group-ubigeo_proveedor">
                            <h5>Ubigeo de proveedor</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="ubigeo_proveedor">
                                <input type="text" class="form-control" name="ubigeo_proveedor_descripcion" readOnly >
                                    <!-- <button type="button" title="Seleccionar Ubigeo" class="btn-primary" onclick="ubigeoModal();"><i class="far fa-compass"></i></button> -->
                            </div>
                        </div>

                        <div class="col-md-3" id="group-contacto_proveedor_nombre">
                            <h5>Nombre de contacto</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_contacto_proveedor">
                                <input type="text" class="form-control" name="contacto_proveedor_nombre" readOnly>
                                <button type="button" class="group-text" id="basic-addon1" onClick="contactoModal();">
                                    <i class="fa fa-search"></i>
                                </button> 
                                <button type="button" class="btn-primary" title="Agregar Contacto" onClick="agregar_contacto();"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>

                        <div class="col-md-3" id="group-contacto_proveedor_telefono">
                            <h5>Telefono de contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="contacto_proveedor_telefono" readOnly >
                            </div>
                        </div>
                    
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4>Condición de compra </h4>
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-3" id="group-condicion_compra-forma_pago">
                            <h5>Forma de pago</h5>
                            <div style="display:flex;">
                                <select class="form-control group-elemento activation" name="id_condicion" onchange="ordenView.handlechangeCondicion(event);"
                                    style="width:100%; text-align:center;" >
                                    @foreach ($condiciones as $cond)
                                        <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="plazo_dias"  min="0" class="form-control group-elemento invisible" style="text-align:right; width:80px;" >
                                <input type="text" value="días" name="text_dias" class="form-control group-elemento invisible" style="width:40px;text-align:center;" readOnly/>
                            </div>
                        </div>

                        <div class="col-md-2" id="group-condicion_compra-plazo_entrega">
                            <h5>Plazo entrega</h5>
                            <div style="display:flex;">
                                <input type="number" name="plazo_entrega" class="form-control group-elemento activation" style="text-align:right;" >
                                <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" readOnly >
                            </div>
                        </div>

                        <div class="col-md-2" id="group-condicion_compra-cdc_req">
                            <h5>CDC / Req.</h5>
                            <div style="display:flex;">
                            <input class="oculto" name="id_cc">
                                <input type="text" name="cdc_req" class="form-control group-elemento" readOnly >
                            </div>
                        </div>
                        <div class="col-md-2" id="group-condicion_compra-ejecutivo_responsable">
                            <h5>Ejecutivo responsable</h5>
                            <div style="display:flex;">
                                <input type="text" name="ejecutivo_responsable" class="form-control group-elemento" readOnly >
                            </div>
                        </div>
                        <div class="col-md-3" id="group-tipo_documento">
                            <h5>Tipo de documento</h5>
                            <select class="form-control activation" name="id_tp_doc">
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_doc as $tp)
                                    @if($tp->descripcion == 'Factura')
                                        <option value="{{$tp->id_tp_doc}}" selected>{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                    @else
                                        <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4>Datos para el despacho </h4>
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-6" id="group-datos_para_despacho-direccion_destino">
                            <h5>Dirección Entrega</h5>
                            <div style="display:flex;">
                                 <input type="text" name="direccion_destino" class="form-control group-elemento activation" >
                            </div>
                        </div>
                        <div class="col-md-3" id="group-datos_para_despacho-ubigeo_entrega">
                            <h5>Ubigeo entrega</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_ubigeo_destino"/>
                                <input type="text" name="ubigeo_destino" class="form-control group-elemento" readOnly >
                                <button type="button" class="group-text" onClick="ubigeoModal();">
                                    <i class="fa fa-search"></i>
                                </button> 
                            </div>
                        </div>

                        <div class="col-md-3" id="group-datos_para_despacho-personal_autorizado">
                            <h5>Personal autorizado</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_trabajador"/>
                                <input type="text" name="nombre_persona_autorizado" class="form-control group-elemento" readOnly >
                                <button type="button" class="group-text" onClick="trabajadoresModal();">
                                    <i class="fa fa-search"></i>
                                </button> 
                            </div>
                        </div>

                        <div class="col-md-12" id="group-datos_para_despacho-observacion">
                            <h5>Observación</h5>
                            <div style="display:flex;">
                                <textarea class="form-control activation" name="observacion" cols="100" rows="100" style="height:50px;" disabled></textarea>
                            </div>
                        </div>
                    
                    </div>
                </fieldset>
            </div>
        </div>
        

        
        <div class="row">
            <div class="col-md-12">
                <fieldset class="group-table">
                    <div class="col-md-12">
                        <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" 
                            id="listaDetalleOrden" style="margin-bottom: 0px;">
                            <thead style="background-color: grey;">
                                <tr>
                            
                                    <th>REQ.</th>
                                    <th>PART NUMBER</th>
                                    <th>PRODUCTO</th>
                                    <th>UNIDAD</th>
                                    <th>CANTIDAD</th>
                                    <th>PRECIO</th>
                                    <th>STOCK COMPROMETIDO</th>
                                    <th>CANTIDAD A COMPRAR</th>
                                    <th>TOTAL</th>
                                    <th>
                                    <center>
                                        <button type="button" class="btn btn-xs btn-success activation" onclick="ordenView.catalogoProductosModal();" id="btn-add-producto" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle"><i class="fas fa-plus"></i> Producto
                                        </button>
                                    </center>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-7"></div>
                                <div class="col-md-3" id="pie-tabla" style="text-align: right;">
                                    <dl class="dl-horizontal">
                                        <dt>Monto neto: &nbsp;<var name="simboloMoneda"></var></dt>
                                        <dd class="text-right"> <var name=montoNeto></var></dd>
                                    </dl>
                                    <dl class="dl-horizontal">
                                        <dt>IGV: &nbsp;<var name="simboloMoneda"></var></dt>
                                        <dd class="text-right"><var name=igv></var></dd>
                                    </dl>
                                    <dl class="dl-horizontal">
                                        <dt>Monto total: &nbsp;<var name="simboloMoneda"></var></dt>
                                        <dd class="text-right"><var name=montoTotal></var></dd>
                                    </dl>
                                </div>
                            </div>
                        <!-- <p class="c"><strong>Total: </strong> <var name="total"></var></p> -->
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <br>


        <div class="form-inline">
            <div class="checkbox" id="check-guarda_en_requerimiento" style="display:none">
                <label>
                    <input type="checkbox" name="guardarEnRequerimiento"> Guardar nuevos items en requerimiento?
                </label>
            </div> 
        </div>

    
    </form>
</div>
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_catalogo_items')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_ordenes_elaboradas')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_proveedor')
@include('logistica.cotizaciones.add_proveedor')
@include('publico.ubigeoModal')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_contacto_proveedor')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_trabajadores')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.agregar_contacto_proveedor')

@include('logistica.gestion_logistica.compras.pendientes.modal_ver_cuadro_costos')
@include('logistica.requerimientos.modal_vincular_item_requerimiento')
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
    <!-- <script src="{{('/js/logistica/generar_orden.js')}}"></script> -->
    <script src="{{('/js/logistica/orden/modal_ordenes_elaboradas.js')}}"></script>
    <script src="{{('/js/logistica/orden/modal_proveedor.js')}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}"></script>
    <script src="{{ asset('js/publico/ubigeoModal.js')}}"></script>
    <script src="{{('/js/logistica/orden/proveedorContactoModal.js')}}"></script>
    <script src="{{('/js/logistica/orden/trabajadorModal.js')}}"></script>
    <script src="{{('/js/logistica/orden/agregarContacto.js')}}"></script>

 
    <!-- <script src="{{('/js/logistica/orden/crear_orden_requerimiento.js')}}"></script> -->
    <script src="{{ asset('js/publico/consulta_sunat.js')}}"></script>
    <script src="{{('/js/logistica/orden/OrdenModel.js')}}"></script>
    <script src="{{('/js/logistica/orden/OrdenView.js')}}"></script>
    <script src="{{('/js/logistica/orden/OrdenController.js')}}"></script>
    <script src="{{('/js/logistica/CustomTabla.js')}}"></script>

    <!-- <script src="{{('/js/logistica/orden/anular_orden.js')}}"></script> -->
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script>

    $(document).ready(function(){
        seleccionarMenu(window.location);
 
    });
    </script>
@endsection