@extends('layout.main')
@include('layout.menu_cas')

{{-- @section('option')
@include('layout.option')
@endsection --}}

@section('cabecera')
Orden de Transformación
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> Servicios CAS</a></li>
    <li>Transformación</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="transformacion">

    <div class="row">
        <div class="col-md-12" class="thumbnail">
            <h4 style="display:flex;justify-content: space-between;">
                <div>
                    <span id="codigo"></span>
                    <span id="estado_doc"></span>
                </div>
                <div style="display:flex;padding-right: 0px;">
                    <button type="button" class="btn btn-default btn-sm btn-flat" onClick="transformacionModal('OT');" data-toggle="tooltip" data-placement="bottom" title="Ver Historial de Ordenes de Transformación">
                        <i class="fas fa-search"></i> Buscar</button>
                    <button type="button" class="btn btn-info btn-sm btn-flat" onClick="imprimirTransformacion();" data-toggle="tooltip" data-placement="bottom" title="Imprimir Transformación">
                        <i class="fas fa-print"></i> Imprimir</button>
                    <button type="button" class="btn btn-warning btn-sm btn-flat" onClick="openIniciar();" data-toggle="tooltip" data-placement="bottom" title="Iniciar Transformación">
                        Iniciar <i class="fas fa-step-forward"></i> </button>
                    <button type="button" class="btn btn-success btn-sm btn-flat" onClick="openProcesar();" data-toggle="tooltip" data-placement="bottom" title="Procesar Transformación">
                        Finalizar <i class="fas fa-step-forward"></i> </button>
                </div>
            </h4>
        </div>
    </div>

    <form id="form-transformacion" type="register" form="formulario">
        <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;">

            <div class="row" style="padding-left: 10px;padding-right: 10px;margin-bottom: 0px;">
                <div class="col-md-12">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <input type="hidden" name="id_transformacion" primary="ids">

                    <div class="row">
                        <!-- <div class="col-md-1">
                            <h5><label>Código</label></h5>
                            <span id="codigo"></span>
                        </div> -->
                        <div class="col-md-2">
                            <label>Almacén: </label>
                            <span id="almacen_descripcion"></span>
                        </div>
                        <div class="col-md-2">
                            <label>OCAM:</label>
                            <span id="orden_am"></span>
                        </div>
                        <div class="col-md-2">
                            <label>Cuadro Costos:</label>
                            <span id="codigo_oportunidad"></span>
                        </div>
                        <div class="col-md-2">
                            <label>Orden Despacho:</label>
                            <span id="codigo_od"></span>
                        </div>
                        <div class="col-md-2">
                            <label>Requerimiento:</label>
                            <span id="codigo_req"></span>
                        </div>
                        <div class="col-md-2">
                            <label>Guía Remisión:</label>
                            <span id="serie-numero"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="padding-left: 10px;padding-right: 10px;margin-top: 0px;">
                <!-- <div class="col-md-1">
                    <h5><label>Estado</label></h5>
                    <span id="estado_doc"></span>
                </div> -->
                <div class="col-md-2">
                    <label>Fecha Inicio:</label>
                    <span id="fecha_inicio"></span>
                </div>
                <div class="col-md-2">
                    <label>Fecha Proceso:</label>
                    <span id="fecha_transformacion"></span>
                </div>
                <div class="col-md-2">
                    <label>Responsable:</label>
                    <span id="nombre_responsable"></span>
                </div>
                <div class="col-md-2">
                    <label>Observación:</label>
                    <span id="observacion"></span>
                </div>
                <!-- <div class="col-md-3" style="display:flex;padding-left: 10px;padding-right: 0px;padding-top: 15px;">
                    <button type="button" class="btn btn-info btn-sm btn-flat" onClick="imprimirTransformacion();" data-toggle="tooltip" data-placement="bottom" title="Imprimir Transformación">
                        <i class="fas fa-print"></i> </button>
                    <button type="button" class="btn btn-warning btn-sm btn-flat" onClick="openIniciar();" data-toggle="tooltip" data-placement="bottom" title="Iniciar Transformación">
                        Iniciar <i class="fas fa-step-forward"></i> </button>
                    <button type="button" class="btn btn-success btn-sm btn-flat" onClick="openProcesar();" data-toggle="tooltip" data-placement="bottom" title="Procesar Transformación">
                        Procesar <i class="fas fa-step-forward"></i> </button>
                </div> -->
            </div>
            <div class="row" style="padding-left: 10px;padding-right: 10px;margin-top: 0px;">
                <div class="col-md-12">
                    <label>Instrucciones Generales:</label>
                    <input name="id_estado" style="display:none" />
                    <label id="descripcion_sobrantes"></label>
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info" style="margin-bottom: 0px;">
                <div class="panel-heading"><strong>Productos Base</strong></div>
                <table id="listaMateriasPrimas" class="table">
                    <thead>
                        <tr style="background: lightskyblue;">
                            <th>Código</th>
                            <th>Part Number</th>
                            <th>Descripción</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>Unit.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table id="totales_transformacion" class="table table-condensed table-small" style="margin-bottom: 0px;" width="100%">
                    <tbody>
                        <tr>
                            <td width="90%" style="text-align: right;">Total Materias Primas</td>
                            <td width="5%"></td>
                            <td style="text-align: right;"><label name="total_materias">0.00</label></td>
                            <td width="5%"></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;">Total Servicios Directos</td>
                            <td width="5%"></td>
                            <td style="text-align: right;"><label name="total_directos">0.00</label></td>
                            <td width="5%"></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><strong>Costo Primo</strong></td>
                            <td width="5%"></td>
                            <td style="text-align: right;"><label name="costo_primo">0.00</label></td>
                            <td width="5%"></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;">Total Costos Indirectos</td>
                            <td width="5%"></td>
                            <td style="text-align: right;"><label name="total_indirectos">0.00</label></td>
                            <td width="5%"></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;">Total Sobrantes</td>
                            <td width="5%"></td>
                            <td style="text-align: right;"><label name="total_sobrantes">0.00</label></td>
                            <td width="5%"></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><strong>Costo de Transformación</strong></td>
                            <td width="5%"></td>
                            <td style="text-align: right;"><label name="costo_transformacion">0.00</label></td>
                            <td width="5%"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-3">
            
        </div>
        <div class="col-md-1"></div>
    </div> -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-danger" style="margin-bottom: 0px;">
                <div class="panel-heading"><strong>Productos Sobrantes</strong></div>
                <table id="listaSobrantes" class="table">
                    <thead>
                        <tr style="background: lightcoral;">
                            <th>Código</th>
                            <th>Part Number</th>
                            <th width='40%'>Descripción</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>Unit.</th>
                            <th>Total</th>
                            <th width='8%' style="padding:0px;">
                                <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante" id="addSobrante" data-toggle="tooltip" data-placement="bottom" title="Agregar Producto" onClick="agregar_producto_sobrante();"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success" style="margin-bottom: 0px;">
                <div class="panel-heading"><strong>Productos Transformados</strong></div>
                <table id="listaProductoTransformado" class="table">
                    <thead>
                        <tr style="background: palegreen;">
                            <th>Código</th>
                            <th>Part Number</th>
                            <th width='40%'>Descripción</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>Unit.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default" style="margin-bottom: 0px;">
                <div class="panel-heading"><strong>Servicios Directos</strong></div>
                <table id="listaServiciosDirectos" class="table">
                    <thead>
                        <tr style="background: lightgray;">
                            <!-- <th width='10%'>Part Number</th> -->
                            <th>Descripción</th>
                            <!-- <th width='10%'>Cant.</th>
                            <th>Unit.</th> -->
                            <th width='15%'>Total</th>
                            <th style="padding:0px;">
                                <i class="fas fa-plus-square icon-tabla green boton add-new-servicio" id="addServicio" data-toggle="tooltip" data-placement="bottom" title="Agregar Servicio"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-warning" style="margin-bottom: 0px;">
                <div class="panel-heading"><strong>Costos Indirectos</strong></div>
                <table id="listaCostosIndirectos" class="table">
                    <thead>
                        <tr style="background: navajowhite;">
                            <!-- <th width='5%'>Nro</th> -->
                            <th>Código Item</th>
                            <th>Tasa(%)</th>
                            <th>Parámetro</th>
                            <th>Unit.</th>
                            <th>Total</th>
                            <th style="padding:0px;">
                                <i class="fas fa-plus-square icon-tabla green boton add-new-indirecto" id="addCostoIndirecto" data-toggle="tooltip" data-placement="bottom" title="Agregar Indirecto"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('almacen.customizacion.transformacionModal')
@include('almacen.customizacion.transformacionProcesar')
@include('almacen.producto.productoModal')
{{-- @include('almacen.producto.productoCreate') --}}
@include('logistica.servicioModal')
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
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>

<script src="{{ asset('js/almacen/customizacion/transformacion.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transformacion.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transformacionModal.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transformacionModal.js'))}}"></script>
<script src="{{ asset('js/almacen/producto/productoModal.js')}}?v={{filemtime(public_path('js/almacen/producto/productoModal.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_materia.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_materia.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_directo.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_directo.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_indirecto.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_indirecto.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_sobrante.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_sobrante.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_transformado.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_transformado.js'))}}"></script>
{{-- <script src="{{('/js/almacen/customizacion/transformacion.js')}}"></script> --}}
{{-- <script src="{{('/js/almacen/customizacion/transformacionModal.js')}}"></script>
<script src="{{('/js/almacen/producto/productoModal.js')}}"></script> --}}
{{-- <script src="{{('/js/almacen/customizacion/transfor_materia.js')}}"></script> --}}
{{-- <script src="{{('/js/almacen/customizacion/transfor_directo.js')}}"></script> --}}
{{-- <script src="{{('/js/almacen/customizacion/transfor_indirecto.js')}}"></script> --}}
{{-- <script src="{{('/js/almacen/customizacion/transfor_sobrante.js')}}"></script> --}}
{{-- <script src="{{('/js/almacen/customizacion/transfor_transformado.js')}}"></script> --}}

{{-- <script src="{{('/js/almacen/producto/productoCreate.js')}}"></script>
<script src="{{('/js/logistica/servicioModal.js')}}"></script> --}}

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
    });
</script>
@endsection