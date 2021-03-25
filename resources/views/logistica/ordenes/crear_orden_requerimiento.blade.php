@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Crear Orden por Requerimiento
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
    <li>Ordenes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="crear-orden-requerimiento">
    <form id="form-crear-orden-requerimiento" type="register" form="formulario">

        <div class="row">
            <div class="col-md-12">
                <h4>General</h4>
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-3"  id="group-tipo_orden">
                            <h5>Tipo de Orden</h5>
                            <select class="form-control" name="id_tipo_doc" >
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
                            <select class="form-control " name="id_moneda" >
                                @foreach ($tp_moneda as $tpm)
                                    <option value="{{$tpm->id_moneda}}" data-simbolo-moneda="{{$tpm->simbolo}}" >{{$tpm->descripcion}} ( {{$tpm->simbolo}} )</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2" id="group-codigo_orden" >
                            <h5>Código Orden Softlink</h5>
                            <input class="form-control" name="codigo_orden" type="text" placeholder="">
                        </div>
                        <div class="col-md-2" id="group-fecha_emision_orden" >
                            <h5>Fecha Emisión</h5>
                            <input class="form-control" name="fecha_emision" type="date" min={{ date('Y-m-d H:i:s') }} value={{ date('Y-m-d H:i:s') }} >
                        </div>

                        <div class="col-md-3" id="group-datos_para_despacho-logo_empresa">
                                <img id="logo_empresa" src="/images/img-default.jpg" alt=""  style=" height:90px; !important">
                        </div>

                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4>Datos del Proveedor</h4>
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
                            <h5>Dirección de Proveedor</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="direccion_proveedor" readOnly>
                            </div>
                        </div>

                        <div class="col-md-3" id="group-ubigeo_proveedor">
                            <h5>Ubigeo de Proveedor</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="ubigeo_proveedor">
                                <input type="text" class="form-control" name="ubigeo_proveedor_descripcion" readOnly >
                                    <!-- <button type="button" title="Seleccionar Ubigeo" class="btn-primary" onclick="ubigeoModal();"><i class="far fa-compass"></i></button> -->
                            </div>
                        </div>

                        <div class="col-md-3" id="group-contacto_proveedor_nombre">
                            <h5>Nombre de Contacto</h5>
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
                            <h5>Telefono de Contacto</h5>
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
                <h4>Condición de Compra </h4>
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-3" id="group-condicion_compra-forma_pago">
                            <h5>Forma de Pago</h5>
                            <div style="display:flex;">
                                <select class="form-control group-elemento" name="id_condicion" onchange="handlechangeCondicion(event);"
                                    style="width:100%; text-align:center;" >
                                    @foreach ($condiciones as $cond)
                                        <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="plazo_dias"  class="form-control group-elemento invisible" style="text-align:right; width:80px;" >
                                <input type="text" value="días" name="text_dias" class="form-control group-elemento invisible" style="width:40px;text-align:center;" readOnly/>
                            </div>
                        </div>

                        <div class="col-md-2" id="group-condicion_compra-plazo_entrega">
                            <h5>Plazo Entrega</h5>
                            <div style="display:flex;">
                                <input type="number" name="plazo_entrega" class="form-control group-elemento" style="text-align:right;" >
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
                            <h5>Ejecutivo Responsable</h5>
                            <div style="display:flex;">
                                <input type="text" name="ejecutivo_responsable" class="form-control group-elemento" readOnly >
                            </div>
                        </div>
                        <div class="col-md-3" id="group-tipo_documento">
                            <h5>Tipo de Documento</h5>
                            <select class="form-control " name="id_tp_documento">
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
                <h4>Datos para el Despacho </h4>
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-6" id="group-datos_para_despacho-direccion_destino">
                            <h5>Dirección Entrega</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_sede">
                                <input type="text" name="direccion_destino" class="form-control group-elemento" >
                            </div>
                        </div>
                        <div class="col-md-3" id="group-datos_para_despacho-ubigeo_entrega">
                            <h5>Ubigeo Entrega</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_ubigeo_destino"/>
                                <input type="text" name="ubigeo_destino" class="form-control group-elemento" readOnly >
                                <button type="button" class="group-text" onClick="ubigeoModal();">
                                    <i class="fa fa-search"></i>
                                </button> 
                            </div>
                        </div>

                        <div class="col-md-3" id="group-datos_para_despacho-personal_autorizado">
                            <h5>Personal Autorizado</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_trabajador"/>
                                <input type="text" name="nombre_persona_autorizado" class="form-control group-elemento" readOnly >
                                <button type="button" class="group-text" onClick="trabajadoresModal();">
                                    <i class="fa fa-search"></i>
                                </button> 
                            </div>
                        </div>
                    
                    </div>
                </fieldset>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-md-12">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaDetalleOrden" style="margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>REQ.</th>
                            <th>COD. ITEM</th>
                            <th>PRODUCTO</th>
                            <th>UNIDAD</th>
                            <th>CANTIDAD</th>
                            <th>PRECIO</th>
                            <th>STOCK COMPROMETIDO</th>
                            <th>CANTIDAD A COMPRAR</th>
                            <th>TOTAL</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <dl class="dl-horizontal">
                                <dt>Total:</dt>
                                <dd class="text-center"><var name=total></var></dd>
                            </dl>
                        </div>
                    </div>
                <!-- <p class="c"><strong>Total: </strong> <var name="total"></var></p> -->
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-12 text-right">
                <input type="submit" id="submit_orden_requerimiento" class="btn btn-success" value="Guardar">
            </div>
        </div>

        <div class="form-inline">
            <div class="checkbox" id="check-guarda_en_requerimiento" style="display:none">
                <label>
                    <input type="checkbox" name="guardarEnRequerimiento"> Guardar nuevos items en requerimiento?
                </label>
            </div> 
        </div>

    
    </form>
</div>
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('publico.ubigeoModal')
@include('logistica.ordenes.modal_contacto_proveedor')
@include('logistica.ordenes.modal_trabajadores')
@include('logistica.ordenes.agregar_contacto_proveedor')

@include('logistica.ordenes.modal_ver_cuadro_costos')
@include('logistica.ordenes.modal_documentos_vinculados')
@include('logistica.requerimientos.modal_vincular_item_requerimiento')
@include('logistica.ordenes.modal_confirmar_eliminar_item')
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
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <!-- <script src="{{('/js/logistica/generar_orden.js')}}"></script> -->
    <script src="{{('/js/logistica/proveedorModal.js')}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}"></script>
    <script src="{{ asset('js/publico/ubigeoModal.js')}}"></script>
    <script src="{{('/js/logistica/orden/proveedorContactoModal.js')}}"></script>
    <script src="{{('/js/logistica/orden/trabajadorModal.js')}}"></script>
    <script src="{{('/js/logistica/orden/agregarContacto.js')}}"></script>

 
    <script src="{{('/js/logistica/orden/crear_orden_requerimiento.js')}}"></script>
    <!-- <script src="{{('/js/logistica/crear_nuevo_producto.js')}}"></script> -->
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script>
    $(document).ready(function(){
        inicializarModalOrdenRequerimiento(
            "{{route('logistica.gestion-logistica.orden.por-requerimiento.detalle-requerimiento-orden')}}",
            "{{route('logistica.gestion-logistica.orden.por-requerimiento.guardar')}}"

        );
    });
    </script>
@endsection