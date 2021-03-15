@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Hoja de Transformación
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
  <li>Transformación</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="transformacion">
    <!-- <div class="row"> -->
    <form id="form-transformacion" type="register"  form="formulario">
        <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;">
            
            <div class="row"  style="padding-left: 10px;padding-right: 10px;margin-bottom: 0px;">
                <div class="col-md-12">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <input type="hidden" name="id_transformacion" primary="ids">
                    
                    <div class="row">
                        <div class="col-md-1">
                            <h5>Código</h5>
                            <span id="codigo"></span>
                        </div>
                        <div class="col-md-2">
                            <h5>Almacén</h5>
                            <span id="almacen_descripcion"></span>
                        </div>
                        <div class="col-md-2">
                            <h5>OCAM</h5>
                            <span id="orden_am"></span>
                        </div>
                        <div class="col-md-2">
                            <h5>Cuadro Costos</h5>
                            <span id="codigo_oportunidad"></span>
                        </div>
                        <div class="col-md-2">
                            <h5>Orden Despacho</h5>
                            <span id="codigo_od"></span>
                        </div>
                        <div class="col-md-1">
                            <h5>Requerimiento</h5>
                            <span id="codigo_req"></span>
                        </div>
                        <div class="col-md-2">
                            <h5>Guía Remisión</h5>
                            <span id="serie-numero"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="padding-left: 10px;padding-right: 10px;margin-top: 0px;">
                <div class="col-md-1">
                    <h5>Estado</h5>
                    <input name="id_estado" style="display:none"/>
                    <span id="estado_doc"></span>
                </div>
                <div class="col-md-2">
                    <h5>Fecha Inicio</h5>
                    <span id="fecha_inicio"></span>
                </div>
                <div class="col-md-2">
                    <h5>Fecha Proceso</h5>
                    <span id="fecha_transformacion"></span>
                </div>
                <div class="col-md-2">
                    <h5>Responsable</h5>
                    <span id="nombre_responsable"></span>
                </div>
                <div class="col-md-2">
                    <h5>Observación</h5>
                    <span id="observacion"></span>
                </div>
                <div class="col-md-3" style="padding-left: 10px;padding-right: 0px;padding-top: 15px;">
                    <!-- <div class="col-md-10" style="text-align:right;"> -->
                        <button type="button" class="btn btn-info btn-sm" onClick="imprimirTransformacion();" 
                            data-toggle="tooltip" data-placement="bottom" title="Imprimir Transformación">
                            <i class="fas fa-print"></i> </button>
                        <button type="button" class="btn btn-primary btn-sm" onClick="openIniciar();" 
                            data-toggle="tooltip" data-placement="bottom" title="Iniciar Transformación">
                            Iniciar <i class="fas fa-step-forward"></i> </button>
                        <button type="button" class="btn btn-success btn-sm" onClick="openProcesar();" 
                            data-toggle="tooltip" data-placement="bottom" title="Procesar Transformación">
                            Procesar <i class="fas fa-step-forward"></i> </button>
                        <!-- <button type="button" class="btn btn-warning btn-sm" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Salida de Almacén" 
                            onClick="abrir_salida();">S</button>
                        <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Ingreso a Almacén" 
                            onClick="abrir_ingreso();">I</button> -->
                    <!-- </div> -->
                </div>
            </div>
            <div class="row" style="padding-left: 10px;padding-right: 10px;margin-top: 0px;">
                <div class="col-md-12">
                    <h5>Instrucciones Generales:</h5>
                    <input name="id_estado" style="display:none"/>
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
                            <th style="background:Plum;">PartNumber</th>
                            <th style="background:Plum;">Descripción</th>
                            <th style="background:Plum;">Cantidad</th>
                            <th style="background:Plum;">Comentario</th>
                            <th>
                                <i class="fas fa-plus-square icon-tabla green boton" 
                                    id="addMateriaPrima" data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Producto" onClick="openProductoMateriaModal();"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-3">
            <table id="totales_transformacion" class="table table-condensed table-small" style="margin-bottom: 0px;" width="100%">
                <tbody>
                    <tr>
                        <td width="60%" style="text-align: right;">Total Materias Primas</td>
                        <td width="10%"></td>
                        <td style="text-align: right;"><label name="total_materias">0.00</label></td>
                        
                    </tr>
                    <tr>
                        <td style="text-align: right;">Total Servicios Directos</td>
                        <td width="10%"></td>
                        <td style="text-align: right;"><label name="total_directos">0.00</label></td>
                        
                    </tr>
                    <tr>
                        <td style="text-align: right;"><strong>Costo Primo</strong></td>
                        <td width="10%"></td>
                        <td style="text-align: right;"><label name="costo_primo">0.00</label></td>
                        
                    </tr>
                    <tr>
                        <td style="text-align: right;">Total Costos Indirectos</td>
                        <td width="10%"></td>
                        <td style="text-align: right;"><label name="total_indirectos">0.00</label></td>
                        
                    </tr>
                    <tr>
                        <td style="text-align: right;">Total Sobrantes</td>
                        <td width="10%"></td>
                        <td style="text-align: right;"><label name="total_sobrantes">0.00</label></td>
                        
                    </tr>
                    <tr>
                        <td style="text-align: right;"><strong>Costo de Transformación</strong></td>
                        <td width="10%"></td>
                        <td style="text-align: right;"><label name="costo_transformacion">0.00</label></td>
                        
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-1"></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success" style="margin-bottom: 0px;" >
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
                            <th width='8%' style="padding:0px;">
                                <i class="fas fa-plus-square icon-tabla green boton add-new-transformado" 
                                    id="addTransformado" data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Producto" onClick="openProductoTransformadoModal();"></i>
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
            <div class="panel panel-danger" style="margin-bottom: 0px;" >
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
                                <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante" 
                                    id="addSobrante" data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Producto" onClick="openProductoSobranteModal();"></i>
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
            <div class="panel panel-default" style="margin-bottom: 0px;" >
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
                                <i class="fas fa-plus-square icon-tabla green boton add-new-servicio" 
                                    id="addServicio" data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Servicio" ></i>
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
            <div class="panel panel-warning" style="margin-bottom: 0px;" >
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
                                <i class="fas fa-plus-square icon-tabla green boton add-new-indirecto" 
                                    id="addCostoIndirecto" data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Indirecto"></i>
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
@include('almacen.producto.productoCreate')
@include('logistica.servicioModal')
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
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{('/js/almacen/customizacion/transformacion.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transformacionModal.js')}}"></script>
    <script src="{{('/js/almacen/producto/productoModal.js')}}"></script>
    <script src="{{('/js/almacen/producto/productoCreate.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_materia.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_directo.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_indirecto.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_sobrante.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_transformado.js')}}"></script>
    <script src="{{('/js/logistica/servicioModal.js')}}"></script>
@endsection