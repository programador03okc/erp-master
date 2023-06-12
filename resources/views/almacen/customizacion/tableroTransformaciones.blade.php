@extends('layout.main')
@include('layout.menu_cas')

@section('cabecera')
Gestión de transformaciones
@endsection

@section('estilos')
{{-- <link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}"> --}}
{{-- <link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"> --}}
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Servicios CAS</a></li>
    <li>Transformaciones</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="tableroTransformaciones">
    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <div class="row">
                    <div class="col-md-3">
                        <label style="text-align: right;margin-left: 20px;margin-top: 7px;margin-right: 10px;">Fecha de programación: </label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="fecha_programacion" onchange="listarDespachosInternos();"/>
                    </div>
                    <div class="col-md-2">
                    <div style="display:flex;">
                        <button class="btn btn-default btn-flat" onClick="listarDespachosInternos()"><i class="fas fa-sync-alt"></i> Actualizar</button>
                        <button class="btn btn-default btn-flat" onClick="pasarProgramadasAlDiaSiguiente()"><i class="fas fa-undo-alt"></i> Pasar programadas para mañana</button>
                        <button class="btn btn-default btn-flat" onClick="listarPendientesAnteriores()"><i class="fas fa-tasks"></i> Pendientes anteriores</button>
                    </div>
                    </div>
                </div>


                <div class="row">

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header" style="margin-bottom: 15px;">
                                <div class="small-box bg-aqua" style="padding: 5px;text-align: center;">
                                    Programadas
                                </div>
                            </div>
                            <div class="card-body" id="listaProgramados"></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header" style="margin-bottom: 15px;">
                                <div class="small-box bg-blue" style="padding: 5px;text-align: center;">
                                    Pendientes
                                </div>
                            </div>
                            <div class="card-body" id="listaPendientes">
                                {{-- <div class="small-box bg-blue">
                                    <div class="inner">
                                        <h5>OKC2110040 - BANCO DE LA NACION</h5>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        <i class="fa fa-arrow-circle-left"></i>
                                        <i class="fa fa-arrow-circle-right"></i>
                                    </a>
                                </div> --}}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header" style="margin-bottom: 15px;">
                                <div class="small-box bg-orange" style="padding: 5px;text-align: center;">
                                    Proceso
                                </div>
                            </div>
                            <div class="card-body" id="listaProceso"></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header" style="margin-bottom: 15px;">
                                <div class="small-box bg-green" style="padding: 5px;text-align: center;">
                                    Finalizadas
                                </div>
                            </div>
                            <div class="card-body" id="listaFinalizadas"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include('tesoreria.facturacion.archivos_oc_mgcp')
@include('almacen.distribucion.transformacionesPendientesModal')

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') }}"></script>

<script src="{{ asset('js/almacen/distribucion/ordenesDespachoInterno.js')}}?v={{filemtime(public_path('js/almacen/distribucion/ordenesDespachoInterno.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/verDetalleRequerimiento.js')}}?v={{filemtime(public_path('js/almacen/distribucion/verDetalleRequerimiento.js'))}}"></script>
<script src="{{ asset('js/tesoreria/facturacion/archivosMgcp.js')}}?v={{filemtime(public_path('js/tesoreria/facturacion/archivosMgcp.js'))}}"></script>

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        vista_extendida();
        // $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        $('#fecha_programacion').val(fecha_actual());
        listarDespachosInternos();
    });
</script>
@endsection
