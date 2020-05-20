@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body_sin_option')
<div class="page-main" type="valorizaciones">
    <legend>
        <h2>Valorizaciones</h2>
    </legend>

    <form id="form-gestionar_valorizacion" type="register" form="formulario">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#createNewValorizacion" aria-controls="createNewValorizacion" role="tab" data-toggle="tab" title="proceso para valorizar cotización">Valorizar Cotización</a></li>
                        <li role="presentation" class=""><a href="#cotiListValorizadas" onClick="refreshListaCotizacionesValorizadas();" aria-controls="cotiListValorizadas" role="tab" data-toggle="tab" title="lista de cotizaciones habilitadas para generar cuadro comparativo">Lista de Cotizaciones Valorizadas</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="createNewValorizacion">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div>
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-pills nav-justified" role="tablist" id="menu_tab_valorizar_cotizacion">
                                                <li role="presentation" class="active"><a href="#cotizacionesEnviadas" aria-controls="cotizacionesEnviadas" role="tab" data-toggle="tab">1. Selección de Cotizaciones Enviadas</a></li>
                                                <li role="presentation" class="disabled"><a href="#valorizar" aria-controls="valorizar" role="tab" data-toggle="tab">2. Valorizar</a></li>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content" id="contenido_tab_proceso_valorizar">
                                                <div role="tabpanel" class="tab-pane active" id="cotizacionesEnviadas">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <h5>Buscar y Seleccionar Cotizacion(s)</h5>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <h5>Empresa</h5>
                                                                    <div style="display:flex;">
                                                                    <select class="form-control" id="id_empresa_select_coti" onChange="handleChangeFilterCotiByEmpresa(event);">
                                                                            <option value="0" disabled>Elija una opción</option>
                                                                            @foreach ($empresas as $emp)
                                                                                <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <form id="form-valorizaciones" type="register" form="formulario">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaGrupoCotizacionesEnviadas" width="100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th hidden width="20"></th>
                                                                                <th width="20">#</th>
                                                                                <th width="120">COD. COTIZACIÓN</th>
                                                                                <th width="120">COD. GRUPACIÓN</th>
                                                                                <th width="100">EMPRESA</th>
                                                                                <th width="120">REQUERIMIENTOS</th>
                                                                                <th width="100">PROVEEDOR</th>
                                                                                <th width="100">FECHA REGISTRO</th>
                                                                                <th width="100">ESTADO</th>
                                                                                <th width="100">CANT. ITEM / CANT.VALORIZADOS</th>
                                                                                <th width="10">ACCIÓN</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </form>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="valorizar">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                        <form id="form-valorizaciones" type="register" form="formulario">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <h5>Cotizaciones por Valorizar</h5>
                                                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listaGrupoCotizacionesRelacionadas" width="100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th hidden width="20"></th>
                                                                                <th width="20">#</th>
                                                                                <th width="120">COD. COTIZACIÓN</th>
                                                                                <th width="120">COD. GRUPACIÓN</th>
                                                                                <th width="100">EMPRESA</th>
                                                                                <th width="120">REQUERIMIENTOS</th>
                                                                                <th width="100">PROVEEDOR</th>
                                                                                <th width="100">FECHA REGISTRO</th>
                                                                                <th width="100">ESTADO</th>
                                                                                <th width="10">ACCIÓN</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        </tbody>
                                                                    </table>
                                                                    
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 right">
                                                                <button class="btn btn-default" role="button"   id="btnBackFirstTab" onClick="backToFirstStep(event);">
                                                                        Atras <i class="fas fa-arrow-circle-left"></i>
                                                                </button>

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
                        <div role="tabpanel" class="tab-pane" id="cotiListValorizadas">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                    id="listaCotizacionesEnviadasValorizadas">
                                        <thead>
                                            <tr>
                                                <th hidden width="20"></th>
                                                <th width="20">#</th>
                                                <th width="120">COD. COTIZACIÓN</th>
                                                <th width="120">COD. GRUPACIÓN</th>
                                                <th width="100">EMPRESA</th>
                                                <th width="120">REQUERIMIENTOS</th>
                                                <th width="100">PROVEEDOR</th>
                                                <th width="100">FECHA REGISTRO</th>
                                                <th width="100">ESTADO</th>
                                                <th width="20">CANT. ITEM / CANT.VALORIZADOS</th>
                                                <th width="100">ACCIÓN</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
 
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>

<!-- @include('logistica.cotizaciones.modal_comparar_variables_proveedor')
@include('logistica.cotizaciones.modal_ultimas_compras')
@include('logistica.cotizaciones.modal_comparative_board_enabled_to_value')
@include('logistica.cotizaciones.modal_buena_pro')
@include('logistica.cotizaciones.modal_historial_cuadro_comparativo')
@include('logistica.cotizaciones.modal_valorizar_cotizacion')
@include('logistica.cotizaciones.modal_valorizacion_especificacion') -->

@include('layout.footer')
@include('layout.scripts')
<script src="{{ asset('/js/logistica/valorizacion/index.js')}}"></script>
<!-- <script src="{{ asset('/js/logistica/cotizacionModal.js')}}"></script> -->
@include('layout.fin_html')