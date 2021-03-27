@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
    Lista Requerimientos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
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
                    <li role="presentation" class="active"><a href="#requerimientosElaborados" onClick="listar_requerimientos_elaborados('OK COMPUTER');" aria-controls="requerimientosElaborados" role="tab" data-toggle="tab">Requerimientos Elaborados</a></li>
                    <li role="presentation" class=""><a href="#requerimientosPendientesAprobacion" onClick="vista_extendida(); listar_requerimientos_pendientes_aprobar();" aria-controls="requerimientosPendientesAprobacion" role="tab" data-toggle="tab">Requerimientos Pendientes de Aprobación</a></li>
                    <li role="presentation" class=""><a href="#requerimientosAprobados" onClick="vista_extendida(); listar_requerimientos_aprobados();" aria-controls="requerimientosAprobados" role="tab" data-toggle="tab">Requerimientos Aprobados</a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="requerimientosElaborados">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form id="form-requerimientosElaborados" type="register">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h5>Empresa</h5>
                                            <div style="display:flex;">
                                                <select class="form-control" id="id_empresa_select" onChange="handleChangeFilterEmpresaListReqByEmpresa(event);">
                                                    <option value="0" >Elija una opción</option>
                                                    @foreach ($empresas as $emp)
                                                        <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h5>Sede</h5>
                                            <div style="display:flex;">
                                            <select class="form-control" id="id_sede_select" onChange="handleChangeFilterSedeListReqByEmpresa(event);" disabled>
                                                    <option value="0" >Todas</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h5>Grupo</h5>
                                            <div style="display:flex;">
                                            <select class="form-control" id="id_grupo_select" onChange="handleChangeFilterGrupoListReqByEmpresa(event);" disabled>
                                                    <option value="0" >Todas</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h5>Prioridad</h5>
                                            <div style="display:flex;">
                                            <select class="form-control" id="id_prioridad_select" onChange="handleChangeFilterPrioridad(event);" >
                                                <option value="0" >Todas</option>
                                                @foreach ($prioridades as $prioridad)
                                                    <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                                                @endforeach
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
                                                        <th></th>
                                                        <th>CODIGO</th>
                                                        <th width="150">CONCEPTO</th>
                                                        <th>FECHA</th>
                                                        <th>TIPO</th>
                                                        <th width="120">EMPRESA</th>
                                                        <th>GRUPO / PROYECTO</th>
                                                        <th>CREADO POR</th>
                                                        <th width="70">ESTADO</th>
                                                        <th width="120">ACCIÓN</th>
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaReqPendienteAprobacion" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="10"></th>
                                                        <th></th>
                                                        <th>CODIGO</th>
                                                        <th width="150">CONCEPTO</th>
                                                        <th>FECHA</th>
                                                        <th>TIPO REQ.</th>
                                                        <th>TIPO CLIENTE</th>
                                                        <th width="120">EMPRESA</th>
                                                        <th>GRUPO / PROYECTO</th>
                                                        <th>CREADO POR</th>
                                                        <th width="70">ESTADO</th>
                                                        <th width="50">Aprob/Total</th>
                                                        <th width="120">ACCIÓN</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="requerimientosAprobados">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form id="form-requerimientosAprobados" type="register">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view" 
                                                id="ListaRequerimientosAprobados" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th hidden></th>
                                                        <th>Tipo</th>
                                                        <th>Código</th>
                                                        <th>Emp-Sede</th>
                                                        <th>Fecha Req.</th>
                                                        <th>Concepto</th>
                                                        <th>Observación</th>
                                                        <th>Responsable</th>
                                                        <th>Monto</th>
                                                        <th>Estado</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

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
@include('logistica.requerimientos.modal_justificar_generar_requerimiento')
@include('logistica.requerimientos.modal_adjuntar_archivos_detalle_requerimiento') 
<!--  includes -->
@include('logistica.requerimientos.aprobacion.modal_anular')
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
    <script src="{{asset('js/logistica/listar_requerimiento_pendientes_aprobacion.js')}}"></script>
    <script src="{{asset('js/logistica/requerimiento/listar_requerimientos_aprobados.js')}}"></script>

    <script>

        var roles = {!! json_encode($roles) !!};
        var grupos = {!! json_encode($grupos) !!};
        // console.log(roles);
        grupos.forEach(element => {
            if(element.id_grupo ==2){ // comercial
                document.querySelector("div[type='lista_requerimiento'] ul").children[0].setAttribute("class",'active')
                document.querySelector("div[type='lista_requerimiento'] div[class='tab-content']").children[1].setAttribute('class','tab-pane')
                document.querySelector("div[type='lista_requerimiento'] div[class='tab-content']").children[0].setAttribute("class",'tab-pane active')

            }else{
                document.querySelector("div[type='lista_requerimiento'] ul").children[0].children[0].setAttribute("class",'hidden')
                document.querySelector("div[type='lista_requerimiento'] ul").children[1].setAttribute("class",'active')
                document.querySelector("div[type='lista_requerimiento'] div[class='tab-content']").children[0].setAttribute('class','tab-pane')
                document.querySelector("div[type='lista_requerimiento'] div[class='tab-content']").children[1].setAttribute("class",'tab-pane active')
            }
        });

    $(document).ready(function(){
        seleccionarMenu(window.location);
        inicializarRutasListado(
            "{{route('logistica.gestion-logistica.requerimiento.listado.elaborados')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.empresa')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.select-sede-by-empresa')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.select-grupo-by-sede')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.ver-flujos')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.explorar-requerimiento')}}"
            );
        
        inicializarRutasPendienteAprobacion(
            "{{route('logistica.gestion-logistica.requerimiento.listado.pendientes-aprobacion')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.aprobar-documento')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.observar-documento')}}",
            "{{route('logistica.gestion-logistica.requerimiento.listado.anular-documento')}}"
            // "{{route('logistica.gestion-logistica.requerimiento.listado.empresa')}}",
            // "{{route('logistica.gestion-logistica.requerimiento.listado.select-sede-by-empresa')}}",
            // "{{route('logistica.gestion-logistica.requerimiento.listado.select-grupo-by-sede')}}",
            // "{{route('logistica.gestion-logistica.requerimiento.listado.ver-flujos')}}",
            // "{{route('logistica.gestion-logistica.requerimiento.listado.explorar-requerimiento')}}"
            );

            listarRequerimientosAprobados();
     });
    </script>
@endsection