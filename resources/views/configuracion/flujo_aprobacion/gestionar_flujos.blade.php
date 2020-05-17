@include('layout.head')
@include('layout.menu_config')
@include('layout.body_sin_option')
<div class="page-main" type="modulo">
    <legend><h2>Gestionar Flujos, prioridades</h2></legend>
    <div class="row">
        <div class="col-md-12">

            <div>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tabListaFlujo" aria-controls="tabListaFlujo" role="tab" data-toggle="tab">Lista de Flujos</a></li>
                <li role="presentation"><a href="#tabListaOperacion" aria-controls="tabListaOperacion" role="tab" data-toggle="tab">Lista de Operaciones</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tabListaFlujo">
                        <fieldset class="group-table">
                            <div class="row">
                                <div class="col-md-3">
                                <h5>Grupo Flujo</h5>
                                <input type="hidden" class="form-control" name="id_flujo">

                                <select class="form-control" onchange="cambiarGrupo(this.value);">
                                    <option value="0" selected disabled>Elija una opción</option>
                                        @foreach ($grupoFlujo as $grupo)
                                            <option value="{{$grupo->id_grupo_flujo}}">{{$grupo->descripcion}}</option>
                                        @endforeach
                                </select>
                                </div>
                            </div>

                            <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listarFlujos" width="100%">
                            <caption>Flujos</caption>

                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Nombre de Flujo</th>
                                        <th>Tipo Documento</th>
                                        <th>Empresa</th>
                                        <th>Sede</th>
                                        <th>Grupo</th>
                                        <th>Area</th>
                                        <th>Rol</th>
                                        <th>Orden de secuencia</th>
                                        <th width="100">Criterio?</th>
                                        <th width="100">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabListaOperacion">
                        <fieldset class="group-table">
                            <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listarOperaciones" width="100%">
                            <caption>Operaciones</caption>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Nombre de Operación</th>
                                        <th>Tipo Documento</th>
                                        <th>Empresa</th>
                                        <th>Sede</th>
                                        <th>Grupo</th>
                                        <th>Area</th>
                                        <th width="100">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>

            </div>



        </div>
    </div>
</div>
@include('layout.footer')
@include('configuracion.flujo_aprobacion.modal_gestionar_flujo')
@include('configuracion.flujo_aprobacion.modal_gestionar_operacion')
@include('configuracion.flujo_aprobacion.modal_gestionar_criterio')
@include('configuracion.flujo_aprobacion.modal_gestionar_grupo_criterio')

@include('layout.scripts')
<script src="{{('/js/configuracion/flujo_aprobacion/gestionarFlujo.js')}}"></script>
<script src="{{('/js/configuracion/flujo_aprobacion/gestionarCriterioPrioridad.js')}}"></script>
<script src="{{('/js/configuracion/flujo_aprobacion/gestionarCriterioMonto.js')}}"></script>
<script src="{{('/js/configuracion/flujo_aprobacion/gestionarCriterio.js')}}"></script>
<script src="{{('/js/configuracion/flujo_aprobacion/gestionarGrupoCriterio.js')}}"></script>
@include('layout.fin_html')