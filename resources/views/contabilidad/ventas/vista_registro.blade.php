@extends('layout.main')
@include('layout.menu_contabilidad')

@section('option')
@endsection

@section('cabecera')
    Registro de Ventas
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Contabilidad</a></li>
    <li>Ventas</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_ventas">
    <fieldset class="group-table">   
        <form id="form-listaVentas" type="register">
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="row" id="group-vincular-documento" hidden="">
                        <div class="col-md-4">
                            <h5>Vincular Documento</h5>
                            <div class="input-group input-group-sm">
                                <input type="hidden" name="id_documento_vinculado" id="id_documento_vinculado" value="">
                                <input type="hidden" name="nro_documento_vinculado" id="nro_documento_vinculado" value="">
                                <input type="text" class="form-control input-sm ui-autocomplete-input" name="documento_vinculado" id="documento_vinculado" placeholder="Vincular Documento" onkeyup="javascript:this.value = this.value.toUpperCase();" autocomplete="off">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-flat" title="Quitar Documento" type="button" onclick="clearDocument();" id="btnClear" disabled="disabled">
                                        <span class="fa fa-trash"></span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Empresa</h5>
                            <select class="form-control input-sm" name="empresa" id="empresa" required="">
                                <option value="" disabled="" selected="">Elija una opción</option><option value="1">OK COMPUTER E.I.R.L.</option><option value="3">SMART VALUE SOLUTIONS S.R.L.</option><option value="4">RICHARD DORADO BACA</option><option value="5">JONATHAN DEZA RUGEL</option><option value="6">PROYECTEC E.I.R.L</option><option value="7">PROTECNOLOGIA E.I.R.L.</option>									</select>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>Fecha</h5>
                                    <input type="date" class="form-control input-sm" name="fecha" id="fecha" required="" style="text-align: center; padding: 2px;">                                      
                                </div>
                                <div class="col-md-4">
                                    <h5>Tipo Documento</h5>
                                    <select class="form-control input-sm" name="tipo_documento" id="tipo_documento" onchange="onchangeTipoDocumento(event);" required="">
                                        <option value="1">Factura</option><option value="2">Boleta</option><option value="3">Nota de Crédito</option>                                            </select>                                        
                                </div>
                                <div class="col-md-4">
                                    <h5>N° Documento</h5>
                                    <input type="text" class="form-control input-sm text-center" name="nro_documento" id="nro_documento">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <h5>Cliente <i style="font-size: 12px;">( Nombre de la empresa )</i></h5>
                            <input type="hidden" name="id_cliente" id="id_cliente" value="">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control input-sm ui-autocomplete-input" name="cliente" id="cliente" placeholder="Razón social" onkeypress="javascript:this.value = this.value.toUpperCase();" readonly="" autocomplete="off">
                                <span class="input-group-btn">
                                    <!-- <button class="btn btn-default btn-flat" title="Buscar Cliente" type="button" id="search_customer"
                                    onclick="searchCustomer();">
                                        <span class="fa fa-search"></span>
                                    </button> -->
                                    <!-- <button class="btn btn-primary btn-flat" title="Actualizar" type="button" id="new_customer"
                                    onclick="newCustomer();">
                                        <span class="fa fa-save"></span>
                                    </button> -->
                                    <button class="btn btn-default btn-flat" title="Busca" type="button" id="search_customer" onclick="ModalSearchCustomer();">
                                        <span class="fa fa-search"></span>
                                    </button>
                                    <!-- <button class="btn btn-success btn-flat" title="Agregar Nuevo" type="button" id="add_new_customer"
                                    onclick="ModalAddNewCustomer();">
                                        <span class="fa fa-plus"></span>
                                    </button> -->
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>RUC/DNI</h5>
                            <input type="text" class="form-control input-sm text-center" name="ruc_dni_cliente" id="ruc_dni_cliente" readonly="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Código Producto</h5>
                            <input type="text" class="form-control input-sm text-center" name="codigo_producto" id="codigo_producto">    
                        </div>
                        <div class="col-md-3">
                            <h5>Cantidad</h5>
                            <input type="text" class="form-control input-sm text-center" name="cantidad" id="cantidad">
                        </div>
                        <div class="col-md-3">
                            <h5>Unidad Medida</h5>
                            <select class="form-control input-sm" name="unidad_medida" id="unidad_medida" required="">
                                <option value="1">Unidad</option><option value="2">Mensual</option><option value="3">Milimetros</option><option value="4">Mano de Obra</option><option value="5">Kilogramos</option><option value="6">Metros Cubicos</option><option value="7">Bolsas</option><option value="8">Gbl</option><option value="9">Horas Maquina</option><option value="10">Galon</option><option value="11">Pieza</option><option value="12">Pies Cuadrado</option><option value="13">Dia</option><option value="14">Par</option><option value="15">Punto</option><option value="16">Ración</option><option value="17">Est</option><option value="18">Rollo</option><option value="19">Bol</option><option value="20">Juego</option><option value="21">Una</option><option value="22">Kit</option><option value="23">Hoja</option><option value="24">Mill</option><option value="25">Metros</option><option value="26">Metros Cuadrados</option><option value="27">Litros</option><option value="28">Cono</option><option value="29">Doc</option><option value="30">Saco</option><option value="31">Ciento</option><option value="33">Caja</option><option value="34">Horas Hombre</option><option value="35">PAQUETE</option><option value="32">Servicio</option>                                    </select>   
                        </div>
                        <div class="col-md-3">
                            <h5>Comprob. emitida por</h5>
                            <select class="form-control input-sm" name="autor_factura_emitida" id="autor_factura_emitida" required="">
                                <option value="" disabled="" selected="">Elija una opción</option><option value="2">Jonathan Medina</option><option value="3">Juan Mamani</option><option value="4">Norliz Yucra</option><option value="5">Celia Mamani</option><option value="6">Juan Carlos Espinoza</option><option value="7">Jose Paredes</option><option value="8">Rosmery Ventura</option><option value="12">Miguel Gomez</option><option value="13">Efrain Medina</option><option value="14">Juan Pablo Maquera</option><option value="15">Miguel Inado</option><option value="16">Henry Lozano</option><option value="17">Cinthya Ramirez</option><option value="19">Dayanna Fernandez</option><option value="18">Geraldine Capcha</option><option value="20">Lourdes Mendoza</option><option value="1">Edgar Alvarez</option><option value="11">Diana Mayhua</option><option value="21">Ricardo Visbal</option><option value="9">Raul Salinas</option><option value="10">Danitza Suarez</option><option value="22">Rosa Huanca</option>                                    </select>   
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Moneda</h5>
                            <select class="form-control input-sm" name="moneda" id="moneda" required="">
                                <option value="1">Soles</option><option value="2">Dolares</option>                                    </select>   
                        </div>
                        <div class="col-md-3">
                            <h5>Importe</h5>
                            <input type="text" class="form-control input-sm text-center" name="importe" id="importe">
                        </div>
                        <div class="col-md-3">
                            <h5>N° OCC</h5>
                            <input type="text" class="form-control input-sm text-center" name="nro_occ" id="nro_occ">
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha Emisión OCC</h5>
                            <input type="date" class="form-control input-sm" name="fecha_emision_occ" id="fecha_emision_occ" required="" style="text-align: center; padding: 2px;">  
                            </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Comp. Ingresado al Soft por</h5>
                            <select class="form-control input-sm" name="autor_comprobante_ingresado_softlink" id="autor_comprobante_ingresado_softlink" required="">
                                <option value="" disabled="" selected="">Elija una opción</option><option value="2">Jonathan Medina</option><option value="3">Juan Mamani</option><option value="4">Norliz Yucra</option><option value="5">Celia Mamani</option><option value="6">Juan Carlos Espinoza</option><option value="7">Jose Paredes</option><option value="8">Rosmery Ventura</option><option value="12">Miguel Gomez</option><option value="13">Efrain Medina</option><option value="14">Juan Pablo Maquera</option><option value="15">Miguel Inado</option><option value="16">Henry Lozano</option><option value="17">Cinthya Ramirez</option><option value="19">Dayanna Fernandez</option><option value="18">Geraldine Capcha</option><option value="20">Lourdes Mendoza</option><option value="1">Edgar Alvarez</option><option value="11">Diana Mayhua</option><option value="21">Ricardo Visbal</option><option value="9">Raul Salinas</option><option value="10">Danitza Suarez</option><option value="22">Rosa Huanca</option>                                    </select>   
                        </div>
                        <div class="col-md-5">
                            <h5>Vendedor <i style="font-size: 12px;">( Nombre de Vendedor )</i></h5>
                            <input type="hidden" name="id_vendedor" id="id_vendedor" value="">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control input-sm ui-autocomplete-input" name="vendedor" id="vendedor" placeholder="Nombre de Vendedor" onkeyup="javascript:this.value = this.value.toUpperCase();" autocomplete="off">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary btn-flat" type="button" id="new_seller" onclick="newSeller();">
                                        <span class="fa fa-save"></span>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Seguimiento</h5>
                            <input type="text" class="form-control input-sm text-center" name="seguimiento" id="seguimiento">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Observación</h5>
                            <textarea class="form-control input-sm" name="observacion" id="observacion" rows="6" style="height: 47px;"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Unidad Negocio</h5>
                            <select class="form-control input-sm" name="unidad_negocio" id="unidad_negocio" required="">
                                <option value="" disabled="" selected="">Elija una opción</option><option value="1">Comercial</option><option value="2">Proyectos</option><option value="3">Administración</option>                                    </select>   
                        </div>
                        <div class="col-md-4">
                            <h5>Sector</h5>
                            <select class="form-control input-sm" name="sector" id="sector" required="">
                                <option value="" disabled="" selected="">Elija una opción</option><option value="1">PUBLICO</option><option value="2">PRIVADO</option>                                    </select>   
                        </div>
                        <div class="col-md-4">
                            <h5>División</h5>
                            <select class="form-control input-sm" name="division" id="division" required="">
                                <option value="" disabled="" selected="">Elija una opción</option><option value="1">Equipos Informáticos</option><option value="2">Antipandemicos</option><option value="3">Servicios</option>                                    </select>   
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Código Centro de Costos</h5>
                            <input type="hidden" id="id_centro_costo" name="id_centro_costo" value="">
                                <div class="input-group">
                                    <input type="text" name="codigo_centro_costo" id="codigo_centro_costo" class="form-control input-sm" readonly="">
                                    <span class="input-group-addon" onclick="buscarCostos();" title="Buscar Centro Costo">
                                        <i class="glyphicon glyphicon-search"></i>
                                    </span>
                                </div>
                        </div>
                        <div class="col-md-4">
                            <h5>Periodo</h5>
                            <select name="periodo" id="periodo" class="form-control input-sm"><option value="1">2019</option><option value="2">2020</option><option value="3" selected="">2021</option></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <input type="submit" id="submit_guardar_venta" class="btn btn-success" value="Guardar">
                </div>
            </div>
        </form>
    </fieldset>
</div>



<!-- 1re include para evitar error al cargar modal -->
<!-- @include('logistica.requerimientos.modal_justificar_generar_requerimiento') -->
 <!--  includes -->
 

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
 

    <script>

$(document).ready(function(){
        seleccionarMenu(window.location);

    });

    </script>
@endsection