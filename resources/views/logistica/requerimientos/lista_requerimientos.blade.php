@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
    Lista Requerimientos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Requerimientos</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_requerimiento">
<div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#requerimientosElaborados" aria-controls="requerimientosElaborados" role="tab" data-toggle="tab">Requerimientos Elaborados</a></li>
                        <li role="presentation" class=""><a href="#requerimientosPendientesAprobacion" onClick="vista_extendida();" aria-controls="requerimientosPendientesAprobacion" role="tab" data-toggle="tab">Requerimientos Pendientes</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="requerimientosElaborados">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form id="form-requerimientosElaborados" type="register">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h5>Empresa</h5>
                                                <div style="display:flex;">
                                                <select class="form-control" id="id_empresa_select" onChange="handleChangeFilterEmpresaListReqByEmpresa(event);">
                                                        <option value="0" disabled>Elija una opción</option>
                                                        @foreach ($empresas as $emp)
                                                            <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h5>Sede</h5>
                                                <div style="display:flex;">
                                                <select class="form-control" id="id_sede_select" onChange="handleChangeFilterSedeListReqByEmpresa(event);" disabled>
                                                        <option value="0" >Todas</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h5>Grupo</h5>
                                                <div style="display:flex;">
                                                <select class="form-control" id="id_grupo_select" onChange="handleChangeFilterGrupoListReqByEmpresa(event);" disabled>
                                                        <option value="0" >Todas</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <!-- <caption>Requerimientos: Registrados | Aprobados</caption> -->
                                                <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaReq" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="10"></th>
                                                            <th>CODIGO</th>
                                                            <th width="250">CONCEPTO</th>
                                                            <th>FECHA</th>
                                                            <th>TIPO</th>
                                                            <th width="120">EMPRESA</th>
                                                            <th>GRUPO / PROYECTO</th>
                                                            <th>CREADO POR</th>
                                                            <th width="70">ESTADO</th>
                                                            <th width="90">ACCIÓN</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="requerimientosPendientesAprobacion">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <form id="form-requerimientosPendientesAprobacion" type="register">

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




</div>

<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-flujo-aprob">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalles del Requerimiento</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                <div class="col-md-12" id="req-detalle"></div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="flujo-detalle"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12" id="flujo-proximo"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 1re include para evitar error al cargar modal -->
@include('logistica.requerimientos.modal_adjuntar_archivos_detalle_requerimiento') 
<!--  includes -->
@include('logistica.requerimientos.aprobacion.modal_obs')
@include('logistica.requerimientos.aprobacion.modal_aprobacion')
@include('logistica.requerimientos.modal_tracking_requerimiento')

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

    <script src="{{asset('js/logistica/listar_requerimiento.js')}}"></script>
    <script src="{{asset('js/logistica/aprobacion/aprobacion.js')}}"></script>

    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        inicializar(
            "{{route('logistica.gestion-logistica.requerimiento.listado.elaborados')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.empresa')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.select-sede-by-empresa')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.select-grupo-by-sede')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.ver-flujos')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.explorar-requerimiento')}}"
            );
     });
    </script>
@endsection