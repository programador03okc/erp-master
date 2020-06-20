@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body_sin_option')
<div class="page-main" type="orden-requerimiento">

    <legend class="mylegend">
        <h2>Generar Orden</h2>
    </legend>

        <div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#requerimientosPendientes" aria-controls="requerimientosPendientes" role="tab" data-toggle="tab">Requerimientos Pendientes</a></li>
                        <li role="presentation" class=""><a href="#requerimientosAtendidos" onClick="vista_extendida();" aria-controls="requerimientosAtendidos" role="tab" data-toggle="tab">Requerimientos Atentidos</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="requerimientosPendientes">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaRequerimientosPendientes">
                                            <thead>
                                                <tr>
                                                <th hidden></th>
                                                <th width="50">Código</th>
                                                <th width="250">Concepto</th>
                                                <th width="100">Sede</th>
                                                <th width="50">Fecha Registro</th>
                                                <th width="50">ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="requerimientosAtendidos">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaRequerimientosAtendidos">
                                            <thead>
                                                <tr>
                                                <th hidden></th>
                                                <th width="50">Código Requerimiento</th>
                                                <th width="250">Concepto</th>
                                                <th width="50">Fecha Requerimiento</th>
                                                <th width="50">Código Orden Softlink</th>
                                                <th width="100">Sede</th>
                                                <th width="50">Fecha Orden</th>
                                                <th width="20">ACCIONES</th>
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
 </div>
@include('logistica.ordenes.modal_orden_requerimiento')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('logistica.ordenes.ordenesModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/logistica/generar_orden.js')}}"></script>
<script src="{{('/js/logistica/proveedorModal.js')}}"></script>
<script src="{{('/js/logistica/add_proveedor.js')}}"></script>
<script src="{{('/js/logistica/orden_requerimiento.js')}}"></script>
@include('layout.fin_html')