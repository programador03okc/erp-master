@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="requerimientosPendientes">
    <legend class="mylegend">
        <h2 id="titulo">Gestión de Despachos</h2>
    </legend>
    <div class="col-md-12" id="tab-reqPendientes">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Requerimientos Pendientes</a></li>
            <li class=""><a type="#despachos">Despachos Pendientes</a></li>
            <li class=""><a type="#despachados">Despachos Realizados</a></li>
        </ul>
        <div class="content-tabs">
            <section id="pendientes" >
                <form id="form-pendientes" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="requerimientosPendientes">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Codigo</th>
                                        <th>Concepto</th>
                                        <th>Fecha Req.</th>
                                        <th>Observación</th>
                                        <th>Grupo</th>
                                        <th>Responsable</th>
                                        <th width="90px">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="despachos" hidden>
                <form id="form-despachos" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="ordenesDespacho">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th></th>
                                        <th>Codigo</th>
                                        <th>Cliente</th>
                                        <th>Requerimiento</th>
                                        <th>Concepto</th>
                                        <th>Ubigeo</th>
                                        <th>Dirección Destino</th>
                                        <th>Fecha Despacho</th>
                                        <th>Fecha Entrega</th>
                                        <th>Registrado por</th>
                                        <th>Estado</th>
                                        <th width="70px">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" 
                            title="Crear Despacho" onClick="crear_grupo_orden_despacho();">Generar Despacho</button>
                        </div>
                    </div>
                </form>
            </section>
            <section id="despachados" hidden>
                <form id="form-despachados" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="gruposDespachados">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Codigo</th>
                                        <th>Fecha Despacho</th>
                                        <th>Responsable</th>
                                        <th>Guía Venta</th>
                                        <th>Estado</th>
                                        <th width="70px">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@include('almacen.distribucion.requerimientoDetalle')
@include('almacen.distribucion.ordenDespachoCreate')
@include('almacen.distribucion.grupoDespachoCreate')
@include('almacen.distribucion.despachoDetalle')
@include('almacen.distribucion.grupoDespachoDetalle')
@include('logistica.cotizaciones.clienteModal')
@include('publico.ubigeoModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/distribucion/ordenesDespacho.js')}}"></script>
<script src="{{('/js/almacen/distribucion/ordenDespachoCreate.js')}}"></script>
<script src="{{('/js/logistica/clienteModal.js')}}"></script>
<script src="{{('/js/publico/ubigeoModal.js')}}"></script>
@include('layout.fin_html')