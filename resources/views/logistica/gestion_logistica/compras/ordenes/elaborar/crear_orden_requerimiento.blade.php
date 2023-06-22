@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@include('layout.option')
@endsection

@section('cabecera')
Orden de compra / servicio
@endsection

@section('estilos')
    <link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
    <style>
        .mt-4 {
            margin-top: 35px;
        }
        .mb-0 {
            margin-bottom: 0; 
        }
        .label-check {
            font-weight: normal;
            font-size: 15px;
            cursor: pointer;
        }
    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Órdenes</li>
    <li class="active">Orden  de compra / servicio</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="crear-orden-requerimiento">
    <form id="form-crear-orden-requerimiento" type="register" form="formulario">
        <input type="hidden" name="id_orden" primary="ids">
        <input type="hidden" name="tipo_cambio_compra">
        <input class="oculto" name="monto_subtotal">
        <input class="oculto" name="monto_igv">
        <input class="oculto" name="monto_total">

        <div class="row">
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">General &nbsp;
                    <div>
                        <span style="color:blue;" name="codigo_orden_interno"></span> &nbsp; <span style="font-size:small;" name="estado_orden"></span>
                    </div>
                    <div>
                        @if (in_array(284,$array_accesos))
                           <button type="button" name="btn-imprimir-orden-pdf" class="btn btn-info btn-sm handleClickImprimirOrdenPdf" title="Imprimir orden en .pdf" disabled><i class="fas fa-print"></i> Imprimir</button>
                        @endif
                        @if (in_array(285,$array_accesos))
                        <button type="button" name="btn-migrar-orden-softlink" class="btn btn-warning btn-sm handleClickMigrarOrdenASoftlink" title="Migrar orden a softlink" disabled><i class="fad fa-clone"></i>  Migrar Orden a soflink</button>
                        @endif
                        <button type="button" name="btn-enviar-email-finalizacion-cuadro-presupuesto" class="btn btn-default btn-sm handleClickEstadoCuadroPresupuesto oculto" id="btn-enviar-email-finalizacion-cuadro-presupuesto" style="background-color: #9b659b; color:#fff;" title="Enviar email finalización CDP"><i class="fas fa-info-circle"></i> Estado CDP</button>
                        @if (in_array(286,$array_accesos))
                        <button type="button" name="btn-relacionar-a-oc-softlink" id="btn-relacionar-a-oc-softlink" class="btn btn-success btn-sm" title="Relacionar a OC de Softlink" onclick="listarOcSoftlink(event);" disabled><i class="fas fa-object-group"></i> Relacionar a OC de softlink</button>
                        @endif

                    </div>
                </h4>
                <fieldset class="group-table">
                    <div class="row">
                        <div class="col-md-2" id="group-tipo_orden">
                            <h5>Tipo de orden</h5>
                            <select class="form-control handleChangeTipoOrden activation" name="id_tp_documento">
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_documento as $tp)
                                @if($tp->descripcion == 'Orden de Compra')
                                <option value="{{$tp->id_tp_documento}}" selected>{{$tp->descripcion}}</option>
                                @else
                                @if((!in_array(Auth::user()->id_usuario,[17,27,3,1,77]) && $tp->id_tp_documento == 13))
                                @else
                                <option value="{{$tp->id_tp_documento}}"  >{{$tp->descripcion}}</option>
                                @endif
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1" id="group-fecha_orden">
                            <h5>Moneda</h5>
                            <select class="form-control activation handleChangeMoneda" name="id_moneda">
                                @foreach ($tp_moneda as $tpm)
                                <option value="{{$tpm->id_moneda}}" data-simbolo-moneda="{{$tpm->simbolo}}">{{$tpm->descripcion}} ( {{$tpm->simbolo}} )</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2" id="group-codigo_orden">
                            <h5>Código orden softlink</h5>
                            <input class="form-control activation" name="codigo_orden" type="text" placeholder="" readonly>
                        </div>
                        
                        <div class="col-md-1" id="group-periodo_orden">
                            <h5>Periodo</h5>
                            <select class="form-control activation handleChangePeriodo" name="id_periodo">
                                <option value="" disabled>Elija una opción</option>
                                @foreach ($periodos as $periodo)
                                    <option value="{{$periodo->id_periodo}}">{{$periodo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2" id="group-fecha_emision_orden">
                            <h5>Fecha emisión</h5>
                            <div style="display:flex">
                                <input class="form-control  handleChangeFechaEmision activation" name="fecha_emision" type="datetime-local" value={{ date('Y-m-d\TH:i') }}>
                                <button type="button" class="group-text handleClickFechaHoy">
                                    HOY
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2" id="group-datos_para_despacho-sede">
                            <h5>Empresa - Sede</h5>
                            <select class="form-control activation handleChangeSede" name="id_sede">
                                @foreach ($sedes as $sede)
                                <option value="{{$sede->id_sede}}" data-id-empresa="{{$sede->id_empresa}}" data-direccion="{{$sede->direccion}}" data-id-ubigeo="{{$sede->id_ubigeo}}" data-ubigeo-descripcion="{{$sede->ubigeo_descripcion}}">{{$sede->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <h5>&nbsp;</h5>
                        <div class="col-md-2" id="group-datos_para_despacho-logo_empresa">
                            <img id="logo_empresa" src="/images/img-wide.png" alt="" style="height:56px;!important;width:100%;!important;margin-top:-20px;">
                        </div>

                        <!-- <div class="col-md-2 oculto" id="group-migrar-oc-softlink">
                            <h5>&nbsp;</h5>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="migrar_oc_softlink" checked>
                                    Migrar orden a softlink
                                </label>
                            </div>
                        </div> -->

                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4>Datos del proveedor</h4>
                <fieldset class="group-table">
                    <div class="row">
                        <div class="col-md-4" id="group-nombre_proveedor">
                            <h5>Proveedor</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_proveedor">
                                <input class="oculto" name="id_contrib">
                                <input type="text" class="form-control" name="razon_social" disabled>
                                <button type="button" class="group-text" onClick="proveedorModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                                <!-- <button type="button" class="btn-primary" title="Agregar Proveedor" onClick="agregar_proveedor();"><i class="fas fa-plus"></i></button> -->
                            </div>
                        </div>
                        <div class="col-md-3" id="group-direccion_proveedor">
                            <h5>Dirección de proveedor</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="direccion_proveedor" readOnly>
                            </div>
                        </div>

                        <div class="col-md-3" id="group-ubigeo_proveedor" hidden>
                            <h5>Ubigeo de proveedor</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="ubigeo_proveedor">
                                <input type="text" class="form-control" name="ubigeo_proveedor_descripcion" readOnly>
                                <!-- <button type="button" title="Seleccionar Ubigeo" class="btn-primary" onclick="ubigeoModal();"><i class="far fa-compass"></i></button> -->
                            </div>
                        </div>

                        <div class="col-md-3" id="group-contacto_proveedor_nombre">
                            <h5>Nombre de contacto</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_contacto_proveedor">
                                <input type="text" class="form-control" name="contacto_proveedor_nombre" readOnly>
                                <button type="button" class="group-text" onClick="contactoModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                                <!-- <button type="button" class="btn-primary" title="Agregar Contacto" onClick="agregar_contacto();"><i class="fas fa-plus"></i></button> -->
                            </div>
                        </div>

                        <div class="col-md-2" id="group-contacto_proveedor_telefono">
                            <h5>Telefono de contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="contacto_proveedor_telefono" readOnly>
                            </div>
                        </div>

                        <div class="col-md-4" id="group-cuenta_bancaria_proveedor">
                            <h5>Cuenta bancaria</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_cuenta_principal_proveedor">
                                <input type="text" class="form-control" name="moneda_cuenta_principal_proveedor" style="width:50px;" readOnly>
                                <input type="text" class="form-control" name="nro_cuenta_principal_proveedor" readOnly>
                                <button type="button" class="group-text" onClick="cuentasBancariasModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                                <button type="button" class="btn-primary" title="Agregar cuenta bancaria" onClick="agregar_cuenta_proveedor();"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>

                        <div class="col-md-6" id="group-rubro_proveedor">
                            <h5>Rubro</h5>
                            <select class="selectpicker" title="Elija una opción" data-width="100%" data-container="body" data-live-search="true" name="id_rubro_proveedor">
                                @foreach ($rubros as $rubro)
                                    <option value="{{$rubro->id_rubro}}">{{$rubro->descripcion}}</option>
                                @endforeach
                            </select>
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
                                <select class="form-control activation handleChangeCondicion" name="id_condicion_softlink" style="width:100%; text-align:center;">
                                    @foreach ($condiciones_softlink as $cond)
                                    <option value="{{$cond->id_condicion_softlink}}" data-dias="{{$cond->dias}}">{{$cond->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="display:none;">
                                <select class="form-control group-elemento activation" name="id_condicion" style="width:100%; text-align:center;">
                                    @foreach ($condiciones as $cond)
                                    <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="plazo_dias" min="0" class="form-control group-elemento invisible" style="text-align:right; width:80px;">
                                <input type="text" value="días" name="text_dias" class="form-control group-elemento invisible" style="width:40px;text-align:center;" readOnly />
                            </div>
                        </div>

                        <div class="col-md-2" id="group-condicion_compra-plazo_entrega">
                            <h5>Plazo entrega</h5>
                            <div style="display:flex;">
                                <input type="number" name="plazo_entrega" min="0" class="form-control group-elemento activation" style="text-align:right;">
                                <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" readOnly>
                            </div>
                        </div>

                        <div class="col-md-2" id="group-condicion_compra-cdc_req">
                            <h5>Req.</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_cc">
                                <input type="text" name="cdc_req" class="form-control group-elemento" readOnly>
                            </div>
                        </div>
                        <div class="col-md-2" id="group-condicion_compra-ejecutivo_responsable" hidden>
                            <h5>Ejecutivo responsable</h5>
                            <div style="display:flex;">
                                <input type="text" name="ejecutivo_responsable" class="form-control group-elemento" readOnly>
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
                                <input type="text" name="direccion_destino" class="form-control group-elemento activation">
                            </div>
                        </div>
                        <div class="col-md-3" id="group-datos_para_despacho-ubigeo_entrega">
                            <h5>Ubigeo entrega</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_ubigeo_destino" />
                                <input type="text" name="ubigeo_destino" class="form-control group-elemento" readOnly>
                                <button type="button" class="group-text" onClick="ubigeoModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-3" id="group-datos_para_despacho-personal_autorizado">
                            <h5>Personal autorizado #1</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="personal_autorizado_1" />
                                <input type="text" name="nombre_persona_autorizado_1" class="form-control group-elemento" readOnly>
                                <button type="button" class="group-text" onClick="trabajadoresModal(1);">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3" id="group-datos_para_despacho-personal_autorizado">
                            <h5>Personal autorizado #2</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="personal_autorizado_2" />
                                <input type="text" name="nombre_persona_autorizado_2" class="form-control group-elemento" readOnly>
                                <button type="button" class="group-text" onClick="trabajadoresModal(2);">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="form-group mt-4 mb-0">
                                <label class="label-check"><input type="checkbox" class="minimal" id="compra_local" name="compra_local"> Compras locales</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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

        <div class="group-table">
            <div class="row">
                <div class="col-sm-12">
                    <fieldset class="group-importes">
                        <legend>
                            <h6>Items de requerimiento</h6>
                        </legend>
                        <div class="btn-group" role="group" aria-label="...">
                        @if((in_array(Auth::user()->id_usuario,[3,17,27,1,77])))
                            <button type="button" class="btn btn-xs btn-success activation handleClickCatalogoProductosModal" id="btnAgregarProducto" data-toggle="tooltip" data-placement="bottom" title="Agregar producto"><i class="fas fa-plus"></i> Productos</button>
                        @endif
                            <button type="button" class="btn btn-xs btn-info activation handleClickCatalogoProductosObsequioModal" id="btnAgregarProductoObsequio" data-toggle="tooltip" data-placement="bottom" title="Agregar producto para obsequio"><i class="fas fa-plus"></i> Productos para obsequio</button>
                            <button type="button" class="btn btn-xs btn-primary activation handleClickAgregarServicio" id="btnAgregarServicio" data-toggle="tooltip" data-placement="bottom" title="Agregar servicio"><i class="fas fa-plus"></i> Servicio</button>
                            <button type="button" class="btn btn-xs btn-default activation handleClickVincularRequerimientoAOrdenModalOLD" onClick="openVincularRequerimientoConOrden();" id="btnAgregarVinculoRequerimiento" data-toggle="tooltip" data-placement="bottom" title="Agregar items de otro requerimiento" disabled><i class="fas fa-plus"></i> Vincular otro requerimiento
                            </button>
                        </div>
                        <table class="table table-striped table-condensed table-bordered" id="listaDetalleOrden" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 5%">Req.</th>
                                    <th style="width: 5%">Cod. producto</th>
                                    <th style="width: 5%">Cod. softlink</th>
                                    <th style="width: 5%">Part number</th>
                                    <th>Descripción del producto/servicio</th>
                                    <th style="width: 8%">Unid. Med.</th>
                                    <th style="width: 5%">Cantidad solicitada</th>
                                    <th style="width: 5%">Cantidad Reservada</th>
                                    <th style="width: 5%">Cantidad atendida por orden</th>
                                    <th style="width: 8%">Cantidad a comprar</th>
                                    <th style="width: 10%">Precio Unitario</th>
                                    <th style="width: 6%">Total</th>
                                    <th style="width: 5%">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="body_detalle_orden"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="11" class="text-right"><strong>Monto neto:</strong></td>
                                    <td class="text-right"><span name="simboloMoneda">S/</span><label name="montoNeto"> 0.00</label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="text-right">
                                        <input class="activation handleClickIncluyeIGV" type="checkbox" name="incluye_igv" checked> <strong>Incluye IGV</strong>
                                    </td>
                                    <td class="text-right"><span name="simboloMoneda">S/</span><label name="igv"> 0.00</label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="text-right">
                                        <input class="activation handleClickIncluyeICBPER" type="checkbox" name="incluye_icbper"> <strong>Incluye ICBPER</strong>
                                    </td>
                                    <td class="text-right"><span name="simboloMoneda">S/</span><label name="icbper"> 0.00</label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="text-right"><strong>Monto total:</strong></td>
                                    <td class="text-right"><span name="simboloMoneda">S/</span><label name="montoTotal"> 0.00</label></td>
                                    <td></td>
                                </tr>

                            </tfoot>
                        </table>
                    </fieldset>
                </div>
            </div>
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

<div class="hidden" id="divOculto">
    <select id="selectUnidadMedida">
        @foreach ($unidades_medida as $unidad)
        <option value="{{$unidad->id_unidad_medida}}" {{$unidad->id_unidad_medida=='1' ? 'selected' : ''}}>{{$unidad->abreviatura}}</option>
        @endforeach
    </select>
</div>
<div class="hidden">
    <h5>Empresa - Sede</h5>
    <select name="selectEmpresa">
        @foreach ($empresas as $empresa)
        @if($empresa->id_empresa ==1)
        <option value="{{$empresa->id_empresa}}" data-codigo-empresa="{{$empresa->codigo}}" selected>{{$empresa->razon_social}}</option>
        @else
        <option value="{{$empresa->id_empresa}}" data-codigo-empresa="{{$empresa->codigo}}">{{$empresa->razon_social}}</option>
        @endif
        @endforeach
    </select>
</div>
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_lista_oc_softlink')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_estado_cuadro_presupuesto')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.vincularRequerimientoConOrdenModal')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.listaItemsRequerimientoParaVincularModal')
@include('logistica.gestion_logistica.compras.pendientes.modal_ver_orden_de_requerimiento')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_catalogo_items')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_ordenes_elaboradas')
@include('logistica.gestion_logistica.proveedores.modal_cuentas_bancarias_proveedor')
@include('logistica.gestion_logistica.proveedores.modal_agregar_cuenta_bancaria_proveedor')
@include('logistica.gestion_logistica.proveedores.modal_lista_proveedores')
@include('logistica.cotizaciones.add_proveedor')
@include('publico.ubigeoModal')
@include('logistica.gestion_logistica.proveedores.modal_contacto_proveedor')
@include('logistica.gestion_logistica.compras.ordenes.elaborar.modal_trabajadores')

@include('logistica.gestion_logistica.compras.pendientes.modal_ver_cuadro_costos')
@include('logistica.requerimientos.modal_vincular_item_requerimiento') <!--revisar uso -->
@endsection

@section('scripts')
    <script src="{{ asset('js/util.js')}}"></script>
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            seleccionarMenu(window.location);
            $(".sidebar-mini").addClass("sidebar-collapse");
            $('input[type="checkbox"].minimal').iCheck({ checkboxClass: 'icheckbox_minimal-blue' });
        });
    </script>

    <script src="{{('/js/logistica/proveedores/listaProveedoresModal.js')}}?v={{filemtime(public_path('/js/logistica/proveedores/listaProveedoresModal.js'))}}"></script>
    <script src="{{('/js/logistica/proveedores/cuentasBancariasProveedor.js')}}?v={{filemtime(public_path('/js/logistica/proveedores/cuentasBancariasProveedor.js'))}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}?v={{filemtime(public_path('/js/logistica/add_proveedor.js'))}}"></script>
    <script src="{{ asset('js/publico/ubigeoModal.js')}}?v={{filemtime(public_path('js/publico/ubigeoModal.js'))}}"></script>
    <script src="{{('/js/logistica/proveedores/proveedorContactoModal.js')}}?v={{filemtime(public_path('/js/logistica/proveedores/proveedorContactoModal.js'))}}"></script>
    <script src="{{('/js/logistica/orden/trabajadorModal.js')}}?v={{filemtime(public_path('/js/logistica/orden/trabajadorModal.js'))}}"></script>
    <script src="{{ asset('js/publico/consulta_sunat.js')}}?v={{filemtime(public_path('js/publico/consulta_sunat.js'))}}"></script>
    <script src="{{('/js/logistica/orden/orden.js')}}?v={{filemtime(public_path('/js/logistica/orden/orden.js'))}}"></script>
    <script src="{{('/js/logistica/orden/relacionarOcSoftlink.js')}}?v={{filemtime(public_path('/js/logistica/orden/relacionarOcSoftlink.js'))}}"></script>
    <script src="{{('/js/logistica/orden/vincularRequerimientoConOrdenModal.js')}}?v={{filemtime(public_path('/js/logistica/orden/vincularRequerimientoConOrdenModal.js'))}}"></script>
@endsection
