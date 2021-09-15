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
Crear / editar requerimiento
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Requerimientos</li>
    <li class="active">Crear / editar</li>
</ol>
@endsection

<style>
    table thead th {
        vertical-align: middle !important;
        text-align: center !important;
    }
</style>

@section('content')
<div class="page-main" type="requerimiento">
    <form id="form-requerimiento" type="register" enctype="multipart/form-data" form="formulario">
        <input type="hidden" name="id_usuario_session">
        <input type="hidden" name="id_usuario_req">
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


        
        <div class="group-table" id="group-historial-revisiones" hidden>
            <div class="row">
                <div class="col-sm-12">
                    <fieldset class="group-importes">
                        <legend style="background: #b3a705;">
                            <h6>Historial de revisiones</h6>
                        </legend>
                        <table class="table table-bordered" id="listaHistorialRevision">
                            <thead>
                                <tr>
                                <th>Revisado por</th>
                                <th>Acción</th>
                                <th>Comentario</th>
                                <th>Fecha revisión</th>
                                </tr>
                            </thead>
                            <tbody id="body_historial_revision"></tbody>
                        </table>
                </div>
            </div>
</div>

        <div class="row">
            <div class="col-md-12">
                <h4 style="display:flex;justify-content: space-between;">General
                    <div>
                        <span class="label label-default" id="codigo_requerimiento"></span>
                        <small>Tipo cambio($): <span id="tipo_cambio_compra">{{$tipo_cambio}}</span></small> 
                        <span class="label label-default" id="estado_doc"></span>
                        <button type="button" name="btn-imprimir-requerimento-pdf" class="btn btn-info btn-sm handleClickImprimirRequerimientoPdf" title="Imprimir requerimiento en .pdf" disabled><i class="fas fa-print"></i> Imprimir</button>
                        <button type="button" name="btn-adjuntos-requerimiento" class="btn btn-sm btn-warning handleClickAdjuntarArchivoRequerimiento" title="Archivos adjuntos"  disabled><i class="fas fa-file-archive"></i>
                            <span class="badge" name="cantidadAdjuntosRequerimiento" style="position:absolute; right: 74px; border: solid 0.1px;">0</span>
                            Adjuntos
                        </button>
                    </div>
                </h4>
                <fieldset class="group-table">
                    <div class="row">
                        <!-- <div class="col-md-2" id="group-tipo_requerimiento" hidden>
                            <h5>Tipo de requerimiento:</h5>
                            <select class="form-control input-sm activation" name="tipo_requerimiento" onChange="changeOptTipoReqSelect(event);">
                                <option value="">Elija una opción</option>
                                @foreach ($tipo_requerimiento as $tipo)
                                <option value="{{$tipo->id_tipo_requerimiento}}">{{$tipo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div> -->

                        <div class="col-md-2" id="input-group-fecha" hidden>
                            <h5>Fecha Creación</h5>
                            <input type="text" class="form-control" name="fecha_requerimiento" disabled="true" min={{ date('Y-m-d H:i:s') }} value={{ date('Y-m-d H:i:s') }}>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <h5>Concepto/Motivo</h5>
                                <input type="text" class="form-control activation handleChangeUpdateConcepto" name="concepto" >
                            </div>
                        </div>

                        <div class="col-md-2" id="input-group-moneda">
                            <div class="form-group">
                                <h5>Moneda</h5>
                                <select class="form-control activation handleChangeUpdateMoneda" name="moneda" disabled="true">
                                    @foreach ($monedas as $moneda)
                                    <option data-simbolo="{{$moneda->simbolo}}" value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Periodo</h5>
                                <select class="form-control activation" name="periodo" disabled="true">
                                    @foreach ($periodos as $periodo)
                                    <option value="{{$periodo->id_periodo}}">{{$periodo->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Prioridad</h5>
                                <select class="form-control activation" name="prioridad" disabled="true">
                                    @foreach ($prioridades as $prioridad)
                                    <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2" id="input-group-rol-usuario" hidden>
                            <div class="form-group">

                                <h5>Roles del usuario</h5>
                                <div class="input-group-okc">
                                    <select class="form-control input-sm activation" name="rol_usuario">
                                        @foreach ($roles as $rol)
                                        <option value="{{$rol->id_rol}}">{{$rol->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2" id="input-group-empresa">
                            <div class="form-group">
                                <h5>Empresa</h5>
                                <select name="empresa" id="empresa" class="form-control activation handleChangeOptEmpresa handleChangeUpdateEmpresa">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($empresas as $empresa)
                                    <option value="{{$empresa->id_empresa}}">{{ $empresa->razon_social}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2" id="input-group-sede">
                            <div class="form-group">
                                <h5>Sede</h5>
                                <select id="sede" name="sede" class="form-control activation handleChangeOptUbigeo handleChangeUpdateSede">
                                    <option value="0">Elija una opción</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2" id="input-group-fecha_entrega">
                            <div class="form-group">
                                <h5>Fecha límite entrega</h5>
                                <input type="date" class="form-control input-sm activation handleChangeFechaLimite" name="fecha_entrega">
                            </div>
                        </div>

                        <div class="col-md-2" id="input-group-aprobante">
                            <div class="form-group">
                                <h5>División</h5>
                                <select name="division" class="form-control activation">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($divisiones as $division)
                                    @if($division->id_division==4)
                                    <option value="{{$division->id_division}}" selected>{{$division->descripcion}}</option>
                                    @else
                                    <option value="{{$division->id_division}}">{{$division->descripcion}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-tipo-requerimiento">
                            <div class="form-group">
                                <h5>Tipo Requerimiento</h5>
                                <select class="form-control input-sm activation" name="tipo_requerimiento">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($tipo_requerimiento as $tipo)
                                    <option value="{{$tipo->id_tipo_requerimiento}}">{{$tipo->descripcion}}</option>
                                    <!-- <option value="{{$tipo->id_tipo_requerimiento}}" {{$tipo->id_tipo_requerimiento==4 ? 'selected' : ''}}>{{$tipo->descripcion}}</option>                                 -->
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-2" id="input-group-asignar_trabajador">
                            <div class="form-group">
                                <h5>Solicitado por</h5>
                                <div style="display:flex;">
                                    <input class="oculto" name="id_trabajador" value="{{$idTrabajador}}">
                                    <input type="text" name="nombre_trabajador" class="form-control group-elemento" placeholder="Trabajador" value="{{$nombreUsuario}}" readonly="">
                                    <button type="button" class="group-tex btn-primary activation" onclick="listaTrabajadoresModal();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="input-group-fuente">
                            <div class="col-md-2">
                                <div class="form-group">
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
                            </div>
                            <div class="col-md-2" id="input-group-fuente_det">
                                <div class="form-group">
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
                        </div>


                        <div class="col-md-2" id="input-group-monto">
                            <div class="form-group">

                                <h5>Monto total</h5>
                                <div class="input-group-okc">
                                    <div class="input-group-addon" name="montoMoneda" style="width: auto;">S/.</div>
                                    <input type="text" class="form-control activation" name="monto" readOnly>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12" id="input-group-observacion">
                                    <h5>Observación:</h5>
                                    <textarea class="form-control activation" name="observacion" cols="100" rows="100" style="height:50px;" disabled></textarea>
                                </div>
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
                                    <select class="form-control activation" name="id_proyecto" onChange="requerimientoView.changeProyecto(event);">
                                        <option value="0">Seleccione un Proyecto</option>
                                        @foreach ($proyectos_activos as $proyecto)
                                        <option value="{{$proyecto->id_proyecto}}" data-id-centro-costo="{{$proyecto->id_centro_costo}}" data-codigo-centro-costo="{{$proyecto->codigo_centro_costo}}" data-descripcion-centro-costo="{{$proyecto->descripcion_centro_costo}}" data-codigo="{{$proyecto->codigo}}">{{$proyecto->descripcion}}</option>
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
                        <div class="col-md-2 form-inline" id="input-group-tipo-cliente">
                            <h5>Tipo cliente</h5>
                            <div class="input-group-okc">
                                <select name="tipo_cliente" onChange="changeTipoCliente(event);" class="form-control activation" style="width:100px" required>
                                    <option value="0">Elija una opción</option>
                                    <option value="1" default>Persona Natural</option>
                                    <option value="2">Persona Juridica</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="input-group-cliente">
                            <h5>Cliente</h5>
                            <div style="display:flex;">
                                <input type="text" class="oculto" name="id_cliente">
                                <input type="text" class="form-control activation" name="cliente_ruc" style="width: 120px; display: none;">
                                <input type="text" class="form-control activation" name="cliente_razon_social" style="display: none;">

                                <input type="text" class="oculto" name="id_persona">
                                <input type="text" class="form-control activation" name="dni_persona" style="width: 120px;">
                                <input type="text" class="form-control activation" name="nombre_persona">

                                <!-- <div class="input-group-append">         -->
                                <button type="button" class="btn-primary" title="Seleccionar Cliente" name="btnCliente" onClick="openCliente();"><i class="fas fa-user-tie"></i></button>
                                <!-- </div> 
                                <div class="input-group-append"> class="input-group-text         -->
                                <button type="button" class="btn-success" title="Agregar Cliente" name="btnAddCliente" onClick="agregar_cliente();"><i class="fas fa-plus"></i></button>
                                <!-- </div> -->
                            </div>
                        </div>

                        <div class="col-md-2" id="input-group-ubigeo-entrega">
                            <h5>Ubigeo entrega</h5>
                            <div style="display:flex;">
                                <input type="text" class="oculto" name="ubigeo">
                                <input type="text" class="form-control" name="name_ubigeo" readOnly>
                                <button type="button" title="Seleccionar Ubigeo" class="btn-primary" onClick="ubigeoModal();"><i class="far fa-compass"></i></button>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-telefono-cliente">
                            <h5>Teléfono cliente</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control activation" name="telefono_cliente" onkeypress="return isNumberKey(event)" disabled>
                                <button type="button" class="btn-primary" title="Buscar Teléfonos" name="btnSearchPhone" onClick="telefonosClienteModal();">
                                    <i class="fas fa-address-book"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-email-cliente">
                            <h5>Correo cliente</h5>
                            <div style="display:flex;">
                                <input type="email" class="form-control activation" name="email_cliente" disabled>
                                <button type="button" class="btn-primary" title="Buscar Teléfonos" name="btnSearchPhone" onClick="emailClienteModal();">
                                    <i class="fas fa-address-book"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-direccion-entrega">
                            <h5>Dirección cliente</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control activation" name="direccion_entrega" disabled>
                                <button type="button" class="btn-primary" title="Buscar Dirección" name="btnSearchAddress" onClick="direccionesClienteModal();">
                                    <i class="fas fa-location-arrow"></i>
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
                        <div class="col-md-4" id="input-group-nombre-contacto">
                            <h5>Nombre contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="nombre_contacto" disabled>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-cargo-contacto">
                            <h5>Cargo contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="cargo_contacto" disabled>
                            </div>
                        </div>
                        <div class="col-md-4" id="input-group-email-contacto">
                            <h5>Email contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="email_contacto" disabled>
                            </div>
                        </div>
                        <div class="col-md-2" id="input-group-telefono-contacto">
                            <h5>Teléfono contacto</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="telefono_contacto" disabled>
                            </div>
                        </div>
                        <div class="col-md-4" id="input-group-direccion-contacto">
                            <h5>Dirección entrega</h5>
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="direccion_contacto" disabled>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>


        <br>
        <div class="group-table">
            <div class="row">
                <div class="col-sm-12">
                    <fieldset class="group-importes">
                        <legend>
                            <h6>Item's de Requerimiento</h6>
                        </legend>
                        <div class="btn-group" role="group" aria-label="...">
                            <button type="button" class="btn btn-xs btn-success activation" id="btn-add-producto" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled><i class="fas fa-plus"></i> Producto
                            </button> <!--  onClick="catalogoItemsModal();" -->
                            <button type="button" class="btn btn-xs btn-primary activation" id="btn-add-servicio" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled><i class="fas fa-plus"></i> Servicio
                            </button>
                        </div>
                        <table class="table table-striped table-condensed table-bordered" id="ListaDetalleRequerimiento" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 3%">#</th>
                                    <th style="width: 10%">Partida</th>
                                    <th style="width: 10%">C.Costo</th>
                                    <th style="width: 10%">Part number</th>
                                    <th>Descripción de item</th>
                                    <th style="width: 10%">Unidad</th>
                                    <th style="width: 6%">Cantidad</th>
                                    <th style="width: 8%">Precio U.<span name="simboloMoneda">S/</span> <em>(Sin IGV)</em></th>
                                    <th style="width: 6%">Subtotal</th>
                                    <th style="width: 15%">Motivo</th>
                                    <th style="width: 7%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="body_detalle_requerimiento">

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" class="text-right"><strong>Total:</strong></td>
                                    <td class="text-right"><span name="simboloMoneda">S/</span><label name="total"> 0.00</label></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </fieldset>
                </div>
            </div>
        <br>
        <fieldset class="group-table" id="group-detalle-items-transformados" hidden>
            <div class="row">
                <div class="col-sm-12">
                    <fieldset class="group-importes">
                        <legend style="background: #968a30;">
                            <h6 name='titulo_tabla_detalle_items_transfomados'>Detalles Items Transformados</h6>
                        </legend>
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
        <fieldset class="group-table" id="group-detalle-cuadro-costos" hidden>
            <div class="row">
                <div class="col-sm-12">
                    <fieldset class="group-importes">
                        <legend style="background: #5d4d6d;">
                            <h6 name='titulo_tabla_detalle_cc'>Detalles de cuadro de Costos</h6>
                        </legend>
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
    
        <div class="group-table col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <fieldset class="group-importes">
                        <legend style="background: #999;">
                            <h6 name='titulo_tabla_detalle_cc'>Partidas Activas</h6>
                        </legend>
                        <table class="table table-striped table-bordered" id="listaPartidasActivas" width="100%">
                            <thead>
                                <tr>
                                    <th width="10">Codigo</th>
                                    <th width="70">Descripción</th>
                                    <th width="10">Presupuesto Total</th>
                                    <th width="10">Presupuesto Utilizado</th>
                                    <th width="10">Saldo</th>
                                </tr>
                            </thead>
                            <tbody id="body_partidas_activas">
                            </tbody>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button onclick="scrollToTheTopOfDocument()" id="btnVolverArriba" title="Volver Arriba"><i class="fas fa-arrow-circle-up"></i></button>
                <!-- <button type="submit" class="btn-okc" id="btnGuardar"><i class="fas fa-save fa-lg"></i> Guardar</button> -->
            </div>
        </div>
        <br>
        <div class="row" id="observaciones_requerimiento"></div>
    </form>

</div>

<div class="hidden" id="divOculto">
    <select id="selectUnidadMedida">
        @foreach ($unidadesMedida as $unidad)
        <option value="{{$unidad->id_unidad_medida}}" {{$unidad->id_unidad_medida=='1' ? 'selected' : ''}}>{{$unidad->descripcion}}</option>
        @endforeach
    </select>
</div>
<!-- @include('logistica.requerimientos.modal_buscar_stock_almacenes') -->
@include('logistica.requerimientos.modal_loader')
@include('logistica.requerimientos.modal_lista_trabajadores')
@include('logistica.requerimientos.modal_trazabilidad_requerimiento')
@include('logistica.requerimientos.modal_motivo_detalle_requerimiento')
@include('logistica.requerimientos.modal_adjuntar_archivos_requerimiento')
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
<script src="{{ asset('js/util.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
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
<script src="{{ asset('js/logistica/requerimiento/modal_lista_trabajadores.js') }}"></script>
<script src="{{ asset('js/logistica/requerimiento/cuadro_costos.js') }}"></script>
<script src="{{ asset('js/logistica/requerimiento/historial.js') }}"></script>

<script src="{{ asset('js/logistica/requerimiento/scrollToTheTopOfDocument.js') }}"></script>
<script src="{{ asset('js/logistica/requerimiento/duplicar_requerimiento.js') }}"></script>
<script src="{{ asset('js/logistica/requerimiento/historial.js') }}"></script>
<script src="{{ asset('js/logistica/requerimiento/modal_detalle_requerimiento.js') }}"></script>
<script src="{{ asset('js/logistica/requerimiento/mostrar.js') }}"></script>
<script src="{{ asset('js/logistica/requerimiento/tipo_formulario.js') }}"></script>
<script src="{{ asset('js/logistica/requerimiento/cabecera_detalle.js') }}"></script>
<!-- <script src="{{ asset('js/logistica/requerimiento/inicializar.js') }}"></script> -->
<script src="{{ asset('js/logistica/requerimiento/modal_almacen_reserva.js')}}"></script>
<script src="{{ asset('js/logistica/requerimiento/modal_motivo_detalle_requerimiento.js')}}"></script>
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

<script src="{{ asset('js/logistica/requerimiento/ArchivoAdjunto.js?v=3')}}"></script>
<script src="{{ asset('js/logistica/requerimiento/TrazabilidadRequerimientoView.js?v=3')}}"></script>
<script src="{{ asset('js/logistica/requerimiento/RequerimientoView.js?v=6')}}"></script>
<script src="{{ asset('js/logistica/requerimiento/RequerimientoController.js?v=3')}}"></script>
<script src="{{ asset('js/logistica/requerimiento/RequerimientoModel.js?v=3')}}"></script>

<script>
    var grupos = JSON.parse('{!!$grupos!!}');

    var id_grupo_usuario_sesion_list = [];
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
 

    window.onload = function() {

        seleccionarMenu(window.location);
        var descripcion_grupo = '{{Auth::user()->getGrupo()->descripcion}}';
        var id_grupo = '{{Auth::user()->getGrupo()->id_grupo}}';
        document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value = id_grupo;


        // inicializar(

        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.mostrar-requerimiento')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.guardar')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.actualizar')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.anular-requerimiento')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.copiar-requerimiento')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.telefonos-cliente')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.direcciones-cliente')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.emails-cliente')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.cuentas-cliente')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.guardar-cuentas-cliente')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.cuadro-costos')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.detalle-cuadro-costos')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.obtener-construir-cliente')}}",
        //     "{{route('logistica.gestion-logistica.requerimiento.elaboracion.grupo-select-item-para-compra')}}"
        // );
        
        const requerimientoModel = new RequerimientoModel();
        const requerimientoController = new RequerimientoCtrl(requerimientoModel);
        const requerimientoView= new RequerimientoView(requerimientoController);
        requerimientoView.init();
    };
</script>
@endsection