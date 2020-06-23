@extends('layout.head')
@include('layout.menu_logistica')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Nuevo Requerimiento
@endsection

@section('estilos')
@endsection

@section('content')
<div class="page-main" type="requerimiento">
    <legend>
        <div class="row row-no-gutters">
            <div class="col-xs-12 col-md-5"><h2>Gestionar Requerimiento</h2></div>
            <div class="col-xs-6 col-md-4">
                <div class="form-inline">
                    <div class="form-group" style="display:flex;">
                        <h5 >Tipo de Requerimiento: </h5> 
                        <select class="form-control input-sm activation" name="tipo_requerimiento" onChange="changeOptTipoReqSelect(event);">
                            @foreach ($tipo_requerimiento as $tipo)
                                <option value="{{$tipo->id_tipo_requerimiento}}">{{$tipo->descripcion}}</option>
                            @endforeach                
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-md-3" style="display:flex;">
                <h5>Estado de Documento: <span class="label" id="estado_doc"> </span></h5>
                <button type="button" name="btn-imprimir-requerimento-pdf" class="btn btn-danger btn-sm" onclick="ImprimirRequerimientoPdf()" disabled><i class="fas fa-file-pdf"></i> Imprimir</button>

            </div>
        </div>

 
    </legend>
    <form id="form-requerimiento" type="register" form="formulario">
        
        <input type="hidden" name="id_usuario_session">
        <input type="hidden" name="id_usuario_req">
        <input type="hidden" name="id_estado_doc">
        <input type="hidden" name="id_requerimiento" primary="ids">
        <input type="hidden" name="cantidad_aprobaciones">        
        <div class="row">
            <div class="col-md-2">
                <h5>Buscar Requerimiento</h5>
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="codigo" placeholder="Código">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon1" onClick="get_requerimiento_por_codigo();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <h5>Concepto/Motivo</h5>
                <input type="text" class="form-control activation" name="concepto">
            </div>

            <div class="col-md-3" id="input-group-rol-usuario">
                <h5>Roles del Usuario</h5>
                <div class="input-group-okc">
                    <select class="form-control input-sm activation" name="rol_usuario">
                    @foreach ($roles as $rol)
                        <option value="{{$rol->id_rol_aprobacion}}">{{$rol->rol_concepto.' - '.$rol->nombre_area}}</option>
                    @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2"  id="input-group-fecha">
                <h5>Fecha</h5>
                <input type="date" class="form-control activation" name="fecha_requerimiento" disabled="true" min={{ date('Y-m-d H:i:s') }} value={{ date('Y-m-d H:i:s') }}>
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
            <div class="col-md-2" id="input-group-moneda">
                <h5>Moneda</h5>
                <select class="form-control activation" name="moneda" disabled="true">
                @foreach ($monedas as $moneda)
                    <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                @endforeach
                </select>
            </div>
            <!-- <div class="form-group row"> -->
            <div class="col-md-4" id="input-group-empresa">
                <h5>Empresa</h5>
                <select name="empresa" id="empresa" class="form-control activation" onChange="changeOptEmpresaSelect(event)"
                    required>
                    <option value="">Elija una opción</option>
                    @foreach ($empresas as $empresa)
                        <option value="{{$empresa->id_empresa}}">{{ $empresa->contribuyente->razon_social}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2" id="input-group-sede" >
                <h5>Sede</h5>
                    <select name="sede" name="sede" class="form-control activation" onChange="changeOptUbigeo(event)"
                        required>
                        <option value="">Elija una opción</option>
                    </select>
            </div>
            <div class="col-md-2 form-inline" id="input-group-tipo-cliente" >
                <h5>Tipo Cliente</h5>
                <div class="input-group-okc">
                        <select name="tipo_cliente" onChange="changeTipoCliente(event);"
                        class="form-control activation" style="width:100px" required>
                        <!-- <option value="0">Elija una opción</option> -->
                        <option value="1" default>Persona Natural</option>
                        <option value="2">Persona Juridica</option>
                    </select>
                </div>
            </div>
            <div class="col-md-5 form-inline" id="input-group-cliente" >
                <h5>Cliente</h5>
                <div class="input-group-okc">
                    <input type="text" class="oculto" name="id_cliente" >
                    <input type="text" class="form-control" name="cliente_ruc" style="display: none;">
                    <input type="text" class="form-control" name="cliente_razon_social" style="display: none;">

                    <input type="text" class="oculto" name="id_persona" >
                    <input type="text" class="form-control activation" name="dni_persona" >
                    <input type="text" class="form-control activation" name="nombre_persona" >

                    <div class="input-group-append">        
                        <button type="button" title="Seleccionar Cliente" name="btnCliente" 
                        onClick="openCliente();"
                        class="input-group-text" ><i class="fas fa-user-tie"></i></button>
                    </div>
                    <div class="input-group-append">        
                        <button type="button" title="Agregar Cliente" name="btnAddCliente" 
                        onClick="agregar_cliente();"
                        class="input-group-text" ><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-3" id="input-group-telefono-cliente" >
                <h5>Teléfono Cliente</h5>
                <input type="text" class="form-control activation" name="telefono_cliente"  disabled>
            </div>
            <div class="col-md-4" id="input-group-direccion-entrega" >
                <h5>Dirección Entrega</h5>
                <input type="text" class="form-control activation" name="direccion_entrega"  disabled>
            </div>
            <div class="col-md-2" id="input-group-ubigeo-entrega" >
                <h5>Ubigeo Entrega</h5>
                <div class="input-group-okc">
                    <input type="text" class="oculto" name="ubigeo" >
                    <input type="text" class="form-control" name="name_ubigeo" readOnly>
                    <div class="input-group-append">
                        <button type="button" title="Seleccionar Ubigeo" class="input-group-text" onClick="ubigeoModal();" ><i class="far fa-compass"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-4" id="input-group-almacen">
                <h5>Almacén que solicita</h5>
                <select class="form-control activation " name="id_almacen">
                    <option value="0">Elija una opción</option>
                </select>
            </div>
            <div class="col-md-2" id="input-group-area">
                <h5>Area</h5>
                <input type="hidden" class="form-control" name="id_grupo">
                <input type="hidden" class="form-control" name="id_area">
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="nombre_area" disabled="true">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" onclick="modal_area();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6" id="input-group-proyecto" hidden>
                <h5>Proyecto</h5>
                <div style="display:flex;">
                    <input hidden="true" type="text" name="id_op_com" class="activation">
                    <input type="text" name="codigo_opcion" class="form-control group-elemento" style="width:130px; text-align:center;" readonly>
                    <div class="input-group-okc">
                        <input type="text" class="form-control" name="nombre_opcion" placeholder="" aria-describedby="basic-addon4" disabled="true">
                        <div class="input-group-append">
                            <button type="button" class="input-group-text" id="btnOpenModalProyecto" onClick="open_opcion_modal();">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>                            
                </div>
            </div>

            <div id="input-group-comercial" hidden>
                <div class="col-md-2" >
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
            <div class="col-md-4" id="input-group-observacion">
                <h5>Observación:</h5>
                <textarea class="form-control activation" name="observacion" cols="10" rows="20" disabled></textarea>
            </div>
        </div>
  

  
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="group-importes"><legend><h6>Detalle de Requerimiento</h6></legend>
                <table class="table-group" id="ListaDetalleRequerimiento" width="100%">
                    <thead>
                        <tr>
                            <th class="invisible">#</th>
                            <th>CODIGO</th>
                            <th>DESCRIPCION</th>
                            <th width="60">UNIDAD</th>
                            <th width="70">CANTIDAD</th>
                            <th width="70">PRECIO REF.</th>
                            <th width="100">FECHA ENTREGA</th>
                            <th width="100">LUGAR ENTREGA</th>
                            <th width="120">
                                <center><button class="btn btn-xs btn-success activation" onClick="detalleRequerimientoModal(event);" id="btn-add"
                                    data-toggle="tooltip" data-placement="bottom"  title="Agregar Detalle" disabled><i class="fas fa-plus"></i>
                                </button></center>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="body_detalle_requerimiento">
                        <tr id="default_tr">
                            <td></td>
                            <td colspan="7"> No hay datos registrados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row" id="observaciones_requerimiento"></div> 
        <div class="row" id="observaciones_item_requerimiento"></div> 
    </form>
</div>
@include('logistica.requerimientos.modal_cuadro_costos_comercial')
@include('logistica.requerimientos.modal_copiar_documento')
@include('logistica.requerimientos.modal_adjuntar_archivos_requerimiento')
@include('logistica.requerimientos.modal_historial_requerimiento')
@include('logistica.requerimientos.modal_detalle_requerimiento')
@include('logistica.requerimientos.modal_empresa_area')
@include('proyectos.opcion.opcionModal')
@include('logistica.requerimientos.aprobacion.modal_sustento')
@include('logistica.cotizaciones.clienteModal')
@include('logistica.cotizaciones.add_cliente')
@include('publico.personaModal')
@include('publico.ubigeoModal')
@include('almacen.producto.saldosModal')
@include('almacen.verRequerimientoEstado')
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
    <script src="{{ asset('/js/logistica/requerimiento.js') }}"></script>
    <script src="{{ asset('/js/publico/modal_area.js')}}"></script>
    <script src="{{ asset('/js/proyectos/opcion/opcionModal.js')}}"></script>
    <script src="{{ asset('/js/publico/ubigeoModal.js')}}"></script>
    <script src="{{ asset('/js/publico/personaModal.js')}}"></script>
    <script src="{{ asset('/js/publico/hiddenElement.js')}}"></script>
    <script src="{{ asset('/js/logistica/clienteModal.js')}}"></script>
    <script src="{{ asset('/js/logistica/add_cliente.js')}}"></script>
    <script src="{{ asset('/js/almacen/producto/saldosModal.js')}}"></script>
@endsection