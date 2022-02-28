@extends('layout.main')
@include('layout.menu_cas')

@section('cabecera')
Registro de incidencia
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> Servicios CAS</a></li>
    <li>Garantías</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="incidencia">

    <form id="form-incidencia">
    <div class="box">
        <div class="box-header with-border">

            <h3 class="box-title">Incidencia N° <span class="badge badge-secondary" id="codigo_ficha">INC 22-001</span></h3>
            <div class="box-tools pull-right">

                <button type="button" class="btn btn-sm btn-warning nueva-incidencia" data-clipboard-text="Este texto sera copiado"
                    data-toggle="tooltip" data-placement="bottom" title="Nueva Incidencia">
                    <i class="fas fa-copy"></i> Nuevo
                </button>

                <input id="submit_incidencia" class="btn btn-sm btn-success guardar-incidencia" type="submit" style="display: none;"
                    data-toggle="tooltip" data-placement="bottom" title="Actualizar Incidencia" value="Guardar">

                <button type="button" class="btn btn-sm btn-primary edit-incidencia" 
                    data-toggle="tooltip" data-placement="bottom" title="Editar Incidencia">
                    <i class="fas fa-pencil-alt"></i> Editar
                </button>

                <button type="button" class="btn btn-sm btn-danger anular-incidencia" 
                    data-toggle="tooltip" data-placement="bottom" title="Anular Incidencia">
                    <i class="fas fa-trash"></i> Anular
                </button>

                <button type="button" class="btn btn-sm btn-secondary buscar-incidencia" data-toggle="tooltip" data-placement="bottom" 
                    title="Buscar historial de registros" onClick="openFichasModal();">
                    <i class="fas fa-search"></i> Buscar</button>

                {{-- <button type="button" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="bottom" 
                    title="Imprimir ficha" onClick="imprimirFicha();"><i class="fas fa-print"></i> Imprimir</button> --}}

            </div>
        </div>
        <div class="box-body">
            <div class="row" style="margin-bottom:0px">
                <div class="col-md-12">
                    <label style="font-weight: bold;">Seleccione los datos del negocio:</label>
                    <button type="button" class="btn btn-sm btn-secondary edition" data-toggle="tooltip" data-placement="bottom" 
                        title="Buscar historial de registros" onClick="openSalidasVentaModal();">
                         Buscar guias </button>
                </div>
            </div>

            <input type="text" style="display:none;" name="id_incidencia">
            <input type="text" style="display:none;" name="id_mov_alm">
            <input type="text" style="display:none;" name="id_guia_ven">
            <input type="text" style="display:none;" name="id_requerimiento">
            <input type="text" style="display:none;" name="id_contribuyente">
            <input type="text" style="display:none;" name="id_empresa">
            <input type="text" style="display:none;" name="codigo_oportunidad">
            <input type="text" style="display:none;" name="id_contacto">

            <fieldset class="group-table" id="fieldsetDatosNegocio">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:0px;">
                                <label class="col-sm-4 control-label">Guía venta</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static limpiarIncidencia guia_venta"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-4 control-label">Requerimiento</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static limpiarIncidencia codigo_requerimiento"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-4 control-label">Fecha reporte</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control edition limpiarIncidencia" name="fecha_reporte"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-3 control-label">Cliente</label>
                                <div class="col-sm-9">
                                    <div class="form-control-static limpiarIncidencia cliente_razon_social"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-3 control-label">Concepto</label>
                                <div class="col-sm-9">
                                    <div class="form-control-static limpiarIncidencia concepto_requerimiento"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-4 control-label">Sede cliente</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="sede_cliente"/>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px;">
                                <label class="col-sm-4 control-label">Fecha registro</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static limpiarIncidencia fecha_registro"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px;">
                                <label class="col-sm-4 control-label">Responsable</label>
                                <div class="col-sm-8">
                                    {{-- <input type="text" class="form-control edition limpiarIncidencia" name="reponsable"/> --}}
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia" 
                                        name="id_responsable" required>
                                        @foreach ($usuarios as $usuario)
                                        <option value="{{$usuario->id_usuario}}">{{$usuario->nombre_corto}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <br/>
            <div class="row" style="margin-bottom:0px">
                <div class="col-md-12">
                    <label style="font-weight: bold;">Seleccione los datos del contacto:</label>
                    <button type="button" class="btn btn-sm btn-secondary edition" data-toggle="tooltip" data-placement="bottom" 
                        title="Buscar historial de registros" onClick="openContacto();">
                         Contacto </button>
                </div>
            </div>
            
            <fieldset class="group-table" id="fieldsetDatosContacto">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:0px;">
                                <label class="col-sm-4 control-label">Nombre </label>
                                <div class="col-sm-8">
                                    <div class="form-control-static limpiarIncidencia nombre"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-4 control-label">Cargo</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static limpiarIncidencia cargo"></div>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:0px;">
                                <label class="col-sm-4 control-label">Usuario final</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="sede_cliente"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-horizontal">
                            
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-3 control-label">Teléfono</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static limpiarIncidencia telefono"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-3 control-label">Dirección</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static limpiarIncidencia direccion"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-3 control-label">Ubigeo</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static limpiarIncidencia ubigeo"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-5 control-label">Horario</label>
                                <div class="col-sm-7">
                                    <div class="form-control-static limpiarIncidencia horario"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-5 control-label">Correo electrónico</label>
                                <div class="col-sm-7">
                                    <div class="form-control-static limpiarIncidencia email"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <br/>
            <div class="row" style="margin-bottom:0px">
                <div class="col-md-12">
                    <label style="font-weight: bold;">Ingrese los datos de producto(s):</label>
                    <button type="button" class="btn btn-sm btn-secondary edition" data-toggle="tooltip" data-placement="bottom" 
                        title="Buscar historial de registros" onClick="openSeriesProductosModal();">
                         Agregar </button>
                </div>
            </div>
            <fieldset class="group-table" id="fieldsetProductos">
                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                    id="seriesProductos" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th>Serie</th>
                            <th>Código</th>
                            <th>PartNumber</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>

            <br/>
            <div class="row" style="margin-bottom:0px">
                <div class="col-md-12">
                    <label style="font-weight: bold;">Falla reportada:</label>
                </div>
            </div>
            <fieldset class="group-table" id="fieldsetFallaReportada">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:0px;">
                                <label class="col-sm-4 control-label">Tipo de falla</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia" 
                                        name="tipo_falla" required>
                                        @foreach ($tipoFallas as $falla)
                                        <option value="{{$falla->id_tipo_falla}}">{{$falla->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:0px;">
                                <label class="col-sm-4 control-label">Tipo de servicio</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia" 
                                        name="tipo_falla" required>
                                        @foreach ($tipoServicios as $servicio)
                                        <option value="{{$servicio->id_tipo_servicio}}">{{$servicio->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:0px;">
                                <div class="col-sm-6">
                                <div class="icheckbox_flat-blue">
                                    <label style="display:flex;">
                                        <input type="checkbox" class="flat-red activation" name="equipo_operativo" value="0">
                                    </label>
                                </div> Equipo operativo
                            </div>
                                {{-- <label class="col-sm-6 control-label">Equipo operativo</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="tipo_falla"/>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:0px;">
                                {{-- <label class="col-sm-4 control-label">Ingrese la falla reportada</label> --}}
                                <div class="col-sm-12">
                                    <textarea class="form-control" name="falla_reportada" placeholder="Ingrese la falla reportada"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </fieldset>
        </div>
        <div class="box-footer">
            Área de Cas - Garantías
        </div>
      </div>
    </form>
</div>
@include('cas.incidencias.salidasVentaModal')
@include('cas.incidencias.seriesProductosModal')
@include('almacen.distribucion.ordenDespachoContacto')
@include('almacen.distribucion.agregarContacto')
@include('publico.ubigeoModal')
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
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>

<script src="{{ asset('js/cas/incidencias/salidasVentaModal.js')}}?v={{filemtime(public_path('js/cas/incidencias/salidasVentaModal.js'))}}"></script>
<script src="{{ asset('js/cas/incidencias/seriesProductosModal.js')}}?v={{filemtime(public_path('js/cas/incidencias/seriesProductosModal.js'))}}"></script>
<script src="{{ asset('js/cas/incidencias/incidencia.js')}}?v={{filemtime(public_path('js/cas/incidencias/incidencia.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoContacto.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/ordenDespachoContacto.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/contacto.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/contacto.js'))}}"></script>
<script src="{{ asset('js/publico/ubigeoModal.js?')}}?v={{filemtime(public_path('js/publico/ubigeoModal.js'))}}"></script>

{{-- <script src="{{ asset('js/almacen/customizacion/transformacion.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transformacion.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transformacionModal.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transformacionModal.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_materia.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_materia.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_directo.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_directo.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_indirecto.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_indirecto.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_sobrante.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_sobrante.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_transformado.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_transformado.js'))}}"></script> --}}

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
    });
</script>
@endsection