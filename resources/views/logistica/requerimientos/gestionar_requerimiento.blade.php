@extends('layout.main')
@include('layout.menu_logistica')

@if(Auth::user()->tieneAccion(102))
@section('option')
    @include('layout.option')
@endsection
@elseif(Auth::user()->tieneAccion(103))
@section('option')
        @include('layout.option_historial')
    @endsection
@endif

@section('cabecera')
    Elaborar Requerimiento
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Requerimientos</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="requerimiento">
    <form id="form-requerimiento" type="register" form="formulario">
        <input type="hidden" name="id_usuario_session">
        <input type="hidden" name="id_usuario_req">
        <input type="hidden" name="id_estado_doc">
        <input type="hidden" name="id_requerimiento" primary="ids">
        <input type="hidden" name="cantidad_aprobaciones">        
        <input type="hidden" name="confirmacion_pago">
        <input type="hidden" name="fecha_creacion_cc">
        <input type="hidden" name="id_cc">
        <input type="hidden" name="tipo_cuadro">
        <input type="hidden" name="tiene_transformacion" value=false>
        <input type="hidden" name="justificacion_generar_requerimiento">
        <input type="hidden" name="id_grupo">
        <input type="hidden" name="estado">

        <div class="row">
                <!-- <div class="col-md-6" id="group-barra-secundaria">
                    <div class="input-group pull-left" style="display:flex;">
                            &nbsp;
                            <button type="button" name="btn-migrar-requerimiento" class="btn btn-success btn-sm" 
                                data-toggle="tooltip" data-placement="bottom" title="Migrar Requerimiento a Softlink"
                                onclick="migrarRequerimiento()" disabled><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div> -->
 
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">General 
                    <div>
                        <span class="label" id="estado_doc">&nbsp;</span>
                        <span style="color:blue;" name="codigo"></span>
                        <button type="button" name="btn-imprimir-requerimento-pdf" class="btn btn-info btn-sm" onclick="ImprimirRequerimientoPdf()" disabled><i class="fas fa-print"></i></button>
                    </div>
                </h4> 
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-2" id="group-tipo_requerimiento">
                            <h5 >Tipo de requerimiento:</h5> 
                            <select class="form-control input-sm activation" name="tipo_requerimiento" onChange="changeOptTipoReqSelect(event);">
                                <option value="">Elija una opción</option>
                                @foreach ($tipo_requerimiento as $tipo)
                                    <option value="{{$tipo->id_tipo_requerimiento}}">{{$tipo->descripcion}}</option>
                                @endforeach                
                            </select>
                        </div>

                        <div class="col-md-2"  id="input-group-fecha" hidden>
                            <h5>Fecha Creación</h5>
                            <input type="date" class="form-control" name="fecha_requerimiento" disabled="true" min={{ date('Y-m-d H:i:s') }} value={{ date('Y-m-d H:i:s') }}>
                        </div>

                        <div class="col-md-2" id="input-group-moneda">
                            <h5>Moneda</h5>
                            <select class="form-control activation" name="moneda" onChange="changeMonedaSelect(event)" disabled="true">
                            @foreach ($monedas as $moneda)
                                <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                            @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <h5>Concepto/Motivo</h5>
                            <input type="text" class="form-control activation" name="concepto">
                        </div>

                        <div class="col-md-2">
                            <h5>Periodo</h5>
                            <select class="form-control activation" name="periodo" disabled="true">
                                @foreach ($periodos as $periodo)
                                    <option value="{{$periodo->id_periodo}}">{{$periodo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                                <h5>Prioridad</h5>
                                <select class="form-control activation" name="prioridad" disabled="true">
                                    @foreach ($prioridades as $prioridad)
                                        <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                                    @endforeach
                                </select>
                        </div>

                        <div class="col-md-2" id="input-group-rol-usuario" hidden>
                            <h5>Roles del usuario</h5>
                            <div class="input-group-okc">
                                <select class="form-control input-sm activation" name="rol_usuario">
                                @foreach ($roles as $rol)
                                    <option value="{{$rol->id_rol}}">{{$rol->rol_concepto}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2" id="input-group-empresa">
                            <h5>Empresa</h5>
                            <select name="empresa" id="empresa" class="form-control activation" onChange="changeOptEmpresaSelect(event)" required>
                                <option value="">Elija una opción</option>
                                @foreach ($empresas as $empresa)
                                    <option value="{{$empresa->id_empresa}}">{{ $empresa->contribuyente->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2" id="input-group-sede" >
                            <h5>Sede</h5>
                            <select id="sede" name="sede" class="form-control activation" onChange="changeOptUbigeo(event)" required>
                                <option value="">Elija una opción</option>
                            </select>
                        </div>

                        <div class="col-md-2" id="input-group-fecha_entrega">
                            <div class="form-group">
                                <h5>Fecha límite entrega</h5>
                                <input type="date" class="form-control input-sm activation" name="fecha_entrega">
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-para_stock_almacen">
                            <div class="form-group">
                                <h5>&nbsp;</h5>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="activation" name="para_stock_almacen"> Stock para Almacén
                                    </label>
                                    </div>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-aprobante" >
                            <h5>Aprobante</h5>
                            <select  name="rol_aprobante" class="form-control activation">
                                @if(count($aprobantes)>0)
                                    <option value="">Elija una opción</option>
                                    @foreach ($aprobantes as $aprobante)
                                        <option value="{{$aprobante->id_rol}}">{{$aprobante->nombre}}</option>
                                    @endforeach
                                @else
                                <option value="">Ninguno para seleccionar</option>
                                @endif
                            </select>
                        </div>
                        <div id="input-group-fuente">
                            <div class="col-md-2">
                                <h5>Fuente</h5>
                                <div style="display:flex">
                                    <select class="form-control activation " name="fuente_id" onChange="selectFuente(event);">
                                            <option value="0">Elija una opción</option>
                                        @foreach ($fuentes as $fuente)
                                            <option value="{{$fuente->id_fuente}}">{{$fuente->descripcion}}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn-primary" title="Agregar Fuente" name="bnt-agregar-fuente" onclick="agregarFuenteModal();">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2" id="input-group-fuente_det">
                                <h5>Detalle fuente</h5>
                                <div style="display:flex">
                                    <select class="form-control activation " name="fuente_det_id">
                                    </select>
                                    <button type="button" class="btn-primary" title="Agregar Detalle Fuente" name="bnt-agregar-detalle-fuente" onclick="agregarDetalleFuenteModal();">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

 
                        <div class="col-md-2" id="input-group-monto">
                            <h5>Monto total</h5>
                            <div class="input-group-okc">
                                <div class="input-group-addon" id="montoMoneda" style="width: auto;">S/.</div>
                                <input type="text" class="form-control activation" name="monto" readOnly>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>



        <div class="row" hidden>
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">Almacén</h4> 
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-4" id="input-group-almacen" hidden>
                            <h5>Almacén que solicita</h5>
                            <select class="form-control activation " name="id_almacen">
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>


        <div class="row" id="input-group-proyecto">
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">Proyecto</h4> 
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Nombre</h5>
                            <div style="display:flex;">
                                <input type="hidden" class="form-control" name="descripcion_grupo">
                                <input type="text" name="codigo_proyecto" class="form-control group-elemento" style="width:130px; text-align:center;" readonly>
                                <div class="input-group-okc">
                                    <select class="form-control activation" name="id_proyecto" onChange="selectedProyecto(event);">
                                        <option value="0">Seleccione un Proyecto</option>
                                        @foreach ($proyectos_activos as $proyecto)
                                            <option value="{{$proyecto->id_proyecto}}" data-codigo="{{$proyecto->codigo}}">{{$proyecto->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>                            
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="row" id="seccion-cliente">
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">Cliente</h4> 
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-2 form-inline" id="input-group-tipo-cliente" >
                            <h5>Tipo cliente</h5>
                            <div class="input-group-okc">
                                    <select name="tipo_cliente" onChange="changeTipoCliente(event);"
                                    class="form-control activation" style="width:100px" required>
                                    <option value="0">Elija una opción</option>
                                    <option value="1" default>Persona Natural</option>
                                    <option value="2">Persona Juridica</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="input-group-cliente" >
                            <h5>Cliente</h5>
                            <div style="display:flex;">
                                <input type="text" class="oculto" name="id_cliente" >
                                <input type="text" class="form-control activation" name="cliente_ruc"  style="width: 120px; display: none;">
                                <input type="text" class="form-control activation" name="cliente_razon_social" style="display: none;">

                                <input type="text" class="oculto" name="id_persona" >
                                <input type="text" class="form-control activation" name="dni_persona" style="width: 120px;">
                                <input type="text" class="form-control activation" name="nombre_persona" >

                                <!-- <div class="input-group-append">         -->
                                    <button type="button" class="btn-primary" title="Seleccionar Cliente" name="btnCliente" 
                                    onClick="openCliente();"  ><i class="fas fa-user-tie"></i></button>
                                <!-- </div> 
                                <div class="input-group-append"> class="input-group-text         -->
                                    <button type="button" class="btn-success" title="Agregar Cliente" name="btnAddCliente" 
                                    onClick="agregar_cliente();"><i class="fas fa-plus"></i></button>
                                <!-- </div> -->
                            </div>
                        </div>

                        <div class="col-md-2" id="input-group-ubigeo-entrega" >
                            <h5>Ubigeo entrega</h5>
                            <div style="display:flex;">
                                <input type="text" class="oculto" name="ubigeo" >
                                <input type="text" class="form-control" name="name_ubigeo" readOnly>
                                <button type="button" title="Seleccionar Ubigeo" class="btn-primary" onClick="ubigeoModal();" ><i class="far fa-compass"></i></button>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-telefono-cliente" >
                            <h5>Teléfono cliente</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control activation" name="telefono_cliente" onkeypress="return isNumberKey(event)"  disabled>
                                    <button type="button" class="btn-primary" title="Buscar Teléfonos" name="btnSearchPhone"  onClick="telefonosClienteModal();">
                                        <i class="fas fa-address-book"></i>
                                    </button>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-email-cliente" >
                            <h5>Correo cliente</h5>
                            <div style="display:flex;">
                                <input type="email" class="form-control activation" name="email_cliente"  disabled>
                                    <button type="button" class="btn-primary" title="Buscar Teléfonos" name="btnSearchPhone"  onClick="emailClienteModal();">
                                        <i class="fas fa-address-book"></i>
                                    </button>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-direccion-entrega" >
                            <h5>Dirección cliente</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control activation" name="direccion_entrega"  disabled>
                                <button type="button" class="btn-primary" title="Buscar Dirección" name="btnSearchAddress" onClick="direccionesClienteModal();">
                                    <i class="fas fa-location-arrow"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </fieldset>
            </div>
        </div>


        <div class="row" hidden>
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">Cuentas bancarias </h4> 
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-2" id="input-group-cuenta">
                            <h5>Cuenta</h5>
                            <div style="display:flex;">
                                <input type="text" class="oculto" name="id_cuenta" >
                                <select class="form-control activation" name="banco" readOnly>
                                    @foreach ($bancos as $banco)
                                        <option value="{{$banco->id_banco}}">{{$banco->razon_social}}</option>
                                    @endforeach
                                </select>
                                <select class="form-control activation" name="tipo_cuenta" readOnly>
                                    @foreach ($tipos_cuenta as $tipo)
                                        <option value="{{$tipo->id_tipo_cuenta}}">{{$tipo->descripcion}}</option>
                                    @endforeach
                                </select>
                                <input type="text" class="form-control activation" name="nro_cuenta" placeholder="Nro Cuenta" readOnly>
                                <input type="text" class="form-control activation" name="cci" placeholder="CCI" readOnly>
                                <button type="button" class="btn-primary" title="Buscar Cuenta" name="btnSearchAccount" onClick="cuentaClienteModal();">
                                    <i class="fas fa-piggy-bank"></i>
                                </button>
                                <button type="button" class="btn-success" title="Agregar Cuenta" name="btnAddAccount" onClick="agregarCuentaClienteModal();">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="row" id="seccion-contacto-cliente">
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">Contacto cliente</h4> 
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-4" id="input-group-nombre-contacto" >
                            <h5>Nombre contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="nombre_contacto"  disabled>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-cargo-contacto" >
                            <h5>Cargo contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="cargo_contacto"  disabled>
                            </div>
                        </div>
                        <div class="col-md-4" id="input-group-email-contacto" >
                            <h5>Email contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="email_contacto"  disabled>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-telefono-contacto" >
                            <h5>Teléfono contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="telefono_contacto"  disabled>
                            </div>
                        </div>
                        <div class="col-md-4" id="input-group-direccion-contacto" >
                            <h5>Dirección entrega</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="direccion_contacto"  disabled>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-horario-contacto" >
                            <h5>Horario atención</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="horario_contacto"  disabled>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>


        <div class="row" id="input-group-comercial" hidden>
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">Comercial</h4> 
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Comercial</h5>
                            <select class="form-control activation" name="tpOptCom" disabled="true" onChange="changeOptComercialSelect();">
                                <option value="1">Orden C. Cliente</option>
                                <option value="2">Cuadro Costos</option>
                                <option value="3">Gastos Operativos</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h5 id="title-option-comercial">Código</h5>
                            <div style="display:flex;">
                                <input hidden="true" type="text" name="idOtpCom" class="activation">
                                <input type="text" name="codigo_occ" class="form-control group-elemento" style="width:130px; text-align:center;" readonly>
                                <div class="input-group-okc">
                                    <input type="text" class="form-control" name="occ" placeholder="" aria-describedby="basic-addon4" disabled="true">
                                    <div class="input-group-append">
                                        <button type="button" class="input-group-text" onClick="mostrar_cuadro_costos_modal();">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>                            
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">Opcional</h4> 
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-md-12" id="input-group-observacion">
                            <h5>Observación:</h5>
                            <textarea class="form-control activation" name="observacion" cols="100" rows="100" style="height:50px;" disabled></textarea>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

    <br>
    <fieldset class="group-table">   
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="group-importes"><legend><h6>Item's de Requerimiento</h6></legend>
                <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" id="ListaDetalleRequerimiento" width="100%">
                    <thead>
                        <tr>
                            <th class="invisible">#</th>
                            <th width="70">CODIGO</th>
                            <th width="70">PART NUMBER</th>
                            <th width="200">DESCRIPCION</th>
                            <th width="60">UNIDAD</th>
                            <th width="70">CANTIDAD</th>
                            <th width="70">PRECIO U.</th>
                            <th width="70">MONEDA</th>
                            <th width="70">SUBTOTAL</th>
                            <th width="70">PARTIDA</th>
                            <th width="70">C.Costos</th>
                            <th width="70">Atendido Por</th>
                            <th width="140">
                                <center>
                                <button type="button" class="btn btn-xs btn-success activation" onClick="catalogoItemsModal();" id="btn-add-producto"
                                data-toggle="tooltip" data-placement="bottom"  title="Agregar Detalle" disabled><i class="fas fa-plus"></i> Producto
                                </button>
                                <button type="button" class="btn btn-xs btn-success activation" onClick="agregarServicio();" id="btn-add-servicio"
                                data-toggle="tooltip" data-placement="bottom"  title="Agregar Detalle" disabled><i class="fas fa-plus"></i> Servicio
                            </button>
                            </center>
                        </th>
                    </tr>
                </thead>
                    <tbody id="body_detalle_requerimiento">
                        <tr id="default_tr">
                            <td></td>
                            <td colspan="11"> No hay datos registrados</td>
                        </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-2">
                        <table class="table table-condensed table-small" style="border: none;margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                <td width="60%" style="text-align: right; font-weight: bold;">Monto Total:</td>
                                <td width="10%"></td>
                                <td style="font-weight: bold;"><label name="total"> <span name="simbolo_moneda">S/.</span> 0.00</label></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <br>
    <fieldset class="group-table" id="group-detalle-items-transformados" hidden>
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="group-importes" ><legend style="background: #968a30;"><h6 name='titulo_tabla_detalle_items_transfomados'>Detalles Items Transformados</h6></legend>
                <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" id="ListaDetalleItemstransformado" width="100%" style="width: 100%;background: #968a30;">
                    <thead>
                        <tr>
                            <th>Part No.</th>
                            <th>Descripción</th>
                            <th>Cant.</th>
                            <th>Comentario</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>   
    <br>
    <fieldset class="group-table" id="group-detalle-cuadro-costos" hidden>
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="group-importes" ><legend style="background: #5d4d6d;"><h6 name='titulo_tabla_detalle_cc'>Detalles de cuadro de Costos</h6></legend>
                <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" id="ListaDetalleCuadroCostos" width="100%" style="width: 100%;background: #f8f3f9;">
                    <thead>
                        <tr>
                            <th>Part No.</th>
                            <th>Descripción</th>
                            <th>P.V.U. O/C (sinIGV) S/</th>
                            <th>Flete O/C (sinIGV) S/</th>
                            <th>Cant.</th>
                            <th>Garant. meses</th>
                            <th>Proveedor seleccionado</th>
                            <th>Creado Por</th>
                            <th>Fecha Creación</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>   

    <fieldset class="group-table"> 
    <div class="row">
        <div class="col-md-4">
                <fieldset class="group-importes"><legend><h6>Archivos Adjuntos</h6></legend>
                <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" id="listaArchivosAdjuntosRequerimiento" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ARCHIVO</th>
                            <th width="120">
                                <center><button class="btn btn-xs btn-success" onClick="adjuntoRequerimientoModal(event);" id="btnAgregarAdjuntoReq"
                                    data-toggle="tooltip" data-placement="bottom"  title="Agregar Adjunto"><i class="fas fa-plus"></i>
                                </button></center>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="body_adjuntos_requerimiento">
                        <tr id="default_tr">
                            <td colspan="4"> No hay datos registrados</td>
                        </tr>
                    </tbody>
                </table>
        </div>
        <div class="col-md-8">
                <fieldset class="group-importes"><legend><h6>Trazabilidad de Requerimiento</h6></legend>
                <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" id="listaTrazabilidadRequerimiento" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ACCIÓN</th>
                            <th>OBSERVACIÓN</th>
                            <th>USUARIO</th>
                            <th>FECHA</th>
                        </tr>
                    </thead>
                    <tbody id="body_lista_trazabilidad_requerimiento">
                        <tr id="default_tr">
                            <td colspan="5"> No hay datos registrados</td>
                        </tr>
                    </tbody>
                </table>
        </div>
    </div>
    </fieldset>  
    <br>
    <div class="row" id="observaciones_requerimiento"></div> 

    <div class="row">
            <div class="col-md-12 text-right">
                <button type="submit" class="btn-okc" id="btnGuardar"><i class="fas fa-save fa-lg"></i> Guardar</button>
            </div>
        </div>
        <br>
        
    </form>

</div>
<!-- @include('logistica.requerimientos.modal_buscar_stock_almacenes') -->
@include('logistica.requerimientos.aprobacion.modal_sustento')
@include('logistica.requerimientos.modal_agregar_fuente')
@include('logistica.requerimientos.modal_agregar_detalle_fuente')
@include('logistica.requerimientos.modal_almacen_reserva')
@include('logistica.requerimientos.modal_seleccionar_crear_proveedor')
@include('publico.personaModal')
@include('logistica.cotizaciones.clienteModal')
@include('logistica.cotizaciones.add_cliente')
@include('publico.ubigeoModal')
@include('logistica.requerimientos.modal_agregar_cuenta_cliente')
@include('logistica.requerimientos.modal_cuentas_cliente')
@include('logistica.requerimientos.modal_direcciones_cliente')
@include('logistica.requerimientos.modal_email_cliente')
@include('logistica.requerimientos.modal_telefonos_cliente')
@include('logistica.requerimientos.modal_cuadro_costos_comercial')
@include('logistica.requerimientos.modal_copiar_documento')
@include('logistica.requerimientos.modal_adjuntar_archivos_requerimiento')
@include('logistica.requerimientos.modal_adjuntar_archivos_detalle_requerimiento')
@include('logistica.requerimientos.modal_historial_requerimiento')
@include('logistica.requerimientos.modal_catalogo_items')
@include('logistica.requerimientos.modal_crear_nuevo_producto')
@include('logistica.requerimientos.modal_crear_nuevo_marca')
@include('almacen.producto.saldosModal')
@include('logistica.requerimientos.modal_empresa_area')
@include('logistica.requerimientos.modal_partidas')
@include('logistica.requerimientos.modal_centro_costos')
@include('logistica.requerimientos.modal_detalle_requerimiento')
@include('almacen.verRequerimientoEstado')
@include('logistica.requerimientos.modal_promocion_item')


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
    <!-- <script src="{{ asset('js/logistica/requerimiento/modal_buscar_stock_almacenes.js') }}"></script> -->
    <script src="{{ asset('js/logistica/requerimiento/cuadro_costos.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/historial.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/trazabilidad.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/editar.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/cancelar.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/anular.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/guardar.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/adjuntos.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/duplicar_requerimiento.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/historial.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/modal_detalle_requerimiento.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/mostrar.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/tipo_formulario.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/cabecera_detalle.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/inicializar.js') }}"></script>
    <script src="{{ asset('js/logistica/requerimiento/modal_almacen_reserva.js')}}"></script>
    <script src="{{ asset('js/logistica/requerimiento/modal_seleccionar_crear_proveedor.js')}}"></script>
    <script src="{{ asset('js/logistica/requerimiento/public.js') }}"></script>
    <script src="{{ asset('js/logistica/adjuntar_archivos_req.js') }}"></script>
    <script src="{{ asset('js/publico/modal_area.js')}}"></script>
    <!-- <script src="{{ asset('js/proyectos/opcion/opcionModal.js')}}"></script> -->
    <script src="{{ asset('js/publico/ubigeoModal.js')}}"></script>
    <script src="{{ asset('js/publico/personaModal.js')}}"></script>
    <script src="{{ asset('js/publico/hiddenElement.js')}}"></script>
    <script src="{{ asset('js/logistica/clienteModal.js')}}"></script>
    <script src="{{ asset('js/logistica/add_cliente.js')}}"></script>
    <script src="{{ asset('js/logistica/crear_nuevo_producto.js')}}"></script>
    <script src="{{ asset('js/logistica/crear_nueva_marca.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/saldosModal.js')}}"></script>
    <script src="{{ asset('js/publico/consulta_sunat.js')}}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script>
        var grupos = {!! json_encode($grupos) !!};
        var id_grupo_usuario_sesion_list= [];
        grupos.forEach(element => {
            id_grupo_usuario_sesion_list.push(element.id_grupo);
        });
        
        autoSelectTipoRequerimientoPorDefecto();
        // grupos.forEach(element => {
        //     if(element.id_grupo ==3){ // proyectos
        //         cambiarTipoFormulario(4)
        //     }else if(element.id_grupo ==2){ // comercial
        //         cambiarTipoFormulario(5)
                
        //     }else if(element.id_grupo ==1){ //administración
        //         cambiarTipoFormulario(6)
        //     }
        // });
    $(document).ready(function(){
        seleccionarMenu(window.location);
        var descripcion_grupo='{{Auth::user()->getGrupo()->descripcion}}';
        var id_grupo='{{Auth::user()->getGrupo()->id_grupo}}';
        document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value = id_grupo;

        // controlInput(id_grupo,descripcion_grupo);
        inicializar(
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.lista-modal')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.mostrar-requerimiento')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.guardar')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.actualizar')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.anular')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.select-sede-by-empresa')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.copiar-requerimiento')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.telefonos-cliente')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.direcciones-cliente')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.emails-cliente')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.siguiente-codigo-requerimiento')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.cuentas-cliente')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.guardar-cuentas-cliente')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.cuadro-costos')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.detalle-cuadro-costos')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.obtener-construir-cliente')}}",
            "{{route('logistica.gestion-logistica.requerimiento.elaboracion.grupo-select-item-para-compra')}}"

            );
    });
    </script>
@endsection