@extends('layout.main')
@include('layout.menu_necesidades')

@section('option')
@endsection

@section('cabecera')
Listado de requerimientos logísticos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('css/stepper.css')}}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Necesidades</a></li>
    <li>Requerimientos logísticos</li>
    <li class="active">Listado</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_requerimiento">
    @if (in_array(33,$array_accesos) || in_array(19,$array_accesos) || in_array(36,$array_accesos) || in_array(35,$array_accesos) || in_array(34,$array_accesos) || in_array(18,$array_accesos))
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">

                <div class="row" hidden>
                    <div class="col-md-2">
                        <h5>Elaborado por</h5>
                        <div style="display:flex;">
                            <select class="form-control" name="mostrar_me_all" onChange="listarRequerimientoView.handleChangeFiltroListado();">
                                <option value="ALL">Todos</option>
                                <option value="ME">Elaborados por mi</option>
                                <option value="REVISADO_APROBADO">Revisado/aprobados por mi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h5>Empresa</h5>
                        <div style="display:flex;">
                            <select class="form-control" name="id_empresa_select" onChange="listarRequerimientoView.handleChangeFilterEmpresaListReqByEmpresa(event); listarRequerimientoView.handleChangeFiltroListado();">
                                <option value="0">Elija una opción</option>
                                @foreach ($empresas as $emp)
                                <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h5>Sede</h5>
                        <div style="display:flex;">
                            <select class="form-control" name="id_sede_select" onChange="listarRequerimientoView.handleChangeFiltroListado();">
                                <option value="0">Todas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h5>Grupo</h5>
                        <div style="display:flex;">
                            <select class="form-control" name="id_grupo_select" onChange="listarRequerimientoView.handleChangeFiltroListado(); listarRequerimientoView.handleChangeGrupo(event);">
                                <option value="0">Todas</option>
                                @foreach ($grupos as $grupo)
                                <option value="{{$grupo->id_grupo}}">{{$grupo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h5>División</h5>
                        <div style="display:flex;">
                            <select class="form-control" name="division_select" onChange="listarRequerimientoView.handleChangeFiltroListado();">
                                <option value="0">Todas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h5>Prioridad</h5>
                        <div style="display:flex;">
                            <select class="form-control" name="id_prioridad_select" onChange="listarRequerimientoView.handleChangeFiltroListado();">
                                <option value="0">Todas</option>
                                @foreach ($prioridades as $prioridad)
                                <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>
                <div class="box box-widget">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaRequerimientosElaborados" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center"></th>
                                        <th class="text-center" style="width:2%">Prio.</th>
                                        <th class="text-center" style="width:10%">Código</th>
                                        <th class="text-center" style="width:20%">Concepto</th>
                                        <th class="text-center" style="width:8%">Fecha registro</th>
                                        <th class="text-center">Fecha entrega</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center" style="width:10%">Empresa - sede</th>
                                        <th class="text-center">Grupo</th>
                                        <th class="text-center">División</th>
                                        <th class="text-center">Proyecto</th>
                                        <th class="text-center">Presupuesto Interno</th>
                                        <th class="text-center">Monto Subtotal</th>
                                        <th class="text-center">Monto Total</th>
                                        <th class="text-center">Creado por</th>
                                        <th class="text-center">Solicitado por</th>
                                        <th class="text-center" style="width:5%;">Estado</th>
                                        <th class="text-center" style="width:15%">Acción</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </fieldset>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span class="sr-only">Error de Accesos:</span>
                Solicite los accesos
            </div>
        </div>
    </div>
    @endif


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


@include('logistica.requerimientos.modal_requerimiento')

@include('logistica.requerimientos.modal_ver_agregar_adjuntos_requerimiento_compra')
@include('logistica.requerimientos.modal_adjuntar_archivos_requerimiento')
@include('logistica.requerimientos.modal_adjuntar_archivos_detalle_requerimiento')
@include('logistica.requerimientos.modal_filtro_requerimientos_elaborados')
@include('logistica.requerimientos.trazabilidad.modal_trazabilidad')

@endsection

@section('scripts')
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('js/util.js')}}"></script>
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>

<!-- <script src="{{ asset('js/logistica/requerimiento/TrazabilidadRequerimientoView.js')}}?v={{filemtime(public_path('js/logistica/requerimiento/TrazabilidadRequerimientoView.js'))}}"></script> -->
<!-- <script src="{{ asset('js/logistica/requerimiento/AprobarRequerimientoView.js')}}"></script> -->
<script src="{{ asset('js/logistica/requerimiento/ListarRequerimientoView.js')}}?v={{filemtime(public_path('js/logistica/requerimiento/ListarRequerimientoView.js'))}}"></script>
<script src="{{ asset('js/logistica/requerimiento/RequerimientoView.js')}}?v={{filemtime(public_path('js/logistica/requerimiento/RequerimientoView.js'))}}"></script>
<script src="{{ asset('js/logistica/requerimiento/RequerimientoController.js')}}?v={{filemtime(public_path('js/logistica/requerimiento/RequerimientoController.js'))}}"></script>
<script src="{{ asset('js/logistica/requerimiento/RequerimientoModel.js')}}?v={{filemtime(public_path('js/logistica/requerimiento/RequerimientoModel.js'))}}"></script>
<script src="{{ asset('js/logistica/requerimiento/trazabilidad.js')}}?v={{filemtime(public_path('js/logistica/requerimiento/trazabilidad.js'))}}"></script>
<script src="{{ asset('js/logistica/requerimiento/verTodoAdjuntosYAdicionalesRequerimientoCompra.js')}}?v={{filemtime(public_path('js/logistica/requerimiento/verTodoAdjuntosYAdicionalesRequerimientoCompra.js'))}}"></script>


<script>
    var roles = JSON.parse('{!!$roles!!}');
    var grupos = JSON.parse('{!!$gruposUsuario!!}');

    var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');

    $(document).ready(function() {
        seleccionarMenu(window.location);
        const requerimientoModel = new RequerimientoModel();
        const requerimientoController = new RequerimientoCtrl(requerimientoModel);
        const listarRequerimientoView = new ListarRequerimientoView(requerimientoController);

        listarRequerimientoView.mostrar('ALL');

        listarRequerimientoView.initializeEventHandler();

    });


    // window.onload = function() {
    //     listarRequerimientoView.mostrar('ALL');
    // };
</script>
@endsection
