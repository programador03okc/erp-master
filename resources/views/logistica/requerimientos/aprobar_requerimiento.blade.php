@extends('layout.main')
@include('layout.menu_necesidades')

@section('option')
@endsection

@section('cabecera')
Revisar y aprobar requerimientos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Necesidades</a></li>
    <li>Requerimientos</li>
    <li class="active">Revisar y aprobar</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="aprobar_requerimiento">
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">

                <form id="form-requerimientosElaborados" type="register">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Empresa</h5>
                            <div style="display:flex;">
                                <select class="form-control handleChangeFilterEmpresaListReqByEmpresa handleChangeFiltroListadoByEmpresa" name="id_empresa_select" >
                                    <option value="0">Todas</option>
                                    @foreach ($empresas as $emp)
                                    <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Sede</h5>
                            <div style="display:flex;">
                                <select class="form-control handleChangeFiltroListadoBySede" name="id_sede_select" >
                                    <option value="0">Todas</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Grupo</h5>
                            <div style="display:flex;">
                                <select class="form-control handleChangeFiltroListadoByGrupo" name="id_grupo_select" >
                                    <option value="0">Todas</option>
                                    @foreach ($grupos as $grupo)
                                    <option value="{{$grupo->id_grupo}}" >{{$grupo->descripcion}}</option>
                                    @endforeach                                
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Prioridad</h5>
                            <div style="display:flex;">
                                <select class="form-control handleChangeFiltroListadoByPrioridad" name="id_prioridad_select">
                                    <option value="0">Todas</option>
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
                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaReqPendienteAprobacion" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Prio.</th>
                                        <th class="text-center" style="width:8%">Código</th>
                                        <th class="text-center" style="width:25%">Concepto</th>
                                        <th class="text-center">Tipo req.</th>
                                        <th class="text-center">Fecha registro</th>
                                        <th class="text-center">Fecha entrega</th>
                                        <th class="text-center" style="width:10%">Empresa</th>
                                        <th class="text-center">División</th>
                                        <th class="text-center">Monto Total</th>
                                        <th class="text-center">Observacion</th>
                                        <th class="text-center">Creado por</th>
                                        <th class="text-center" style="width:8%">Estado</th>
                                        <th class="text-center" style="width:3%">Aprob /<br/>Total</th>
                                        <th class="text-center" style="width:5%">Acción</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </form>
            </fieldset>
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
@include('logistica.requerimientos.modal_requerimiento')
@include('logistica.requerimientos.modal_adjuntar_archivos_requerimiento')
@include('logistica.requerimientos.modal_adjuntar_archivos_detalle_requerimiento')
 

@endsection

@section('scripts')
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('js/util.js')}}"></script>
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/logistica/requerimiento/scrollToTheTopOfDocument.js') }}"></script>

 
<script src="{{ asset('js/logistica/requerimiento/AprobarRequerimientoView.js')}}"></script>
<!-- <script src="{{ asset('js/logistica/requerimiento/RequerimientoView.js')}}"></script> -->
<script src="{{ asset('js/logistica/requerimiento/RequerimientoController.js')}}"></script>
<script src="{{ asset('js/logistica/requerimiento/RequerimientoModel.js')}}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/datetime-moment.js') }}"></script>
<script>

var roles = JSON.parse('{!!$roles!!}');
var grupos = JSON.parse('{!!$gruposUsuario!!}');


    $(document).ready(function() {
        seleccionarMenu(window.location);
        
        $.fn.dataTable.moment('DD-MM-YYYY HH:mm');
        $.fn.dataTable.moment('DD-MM-YYYY');

        const requerimientoModel = new RequerimientoModel();
        const requerimientoController = new RequerimientoCtrl(requerimientoModel);
        const aprobarRequerimientoView= new AprobarRequerimientoView(requerimientoController);

        aprobarRequerimientoView.mostrar();
        aprobarRequerimientoView.addEventToFilterButtons();


    });
</script>
@endsection