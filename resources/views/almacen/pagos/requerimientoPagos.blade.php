@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="requerimientoPagos">
    <legend class="mylegend">
        <h2 id="titulo">Confirmación de Pagos</h2>
    </legend>
    <div class="col-md-12" id="tab-reqPendientes">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Requerimientos Pendientes</a></li>
            <li class=""><a type="#confirmados">Requerimientos Confirmados</a></li>
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
                                        <th>Tipo</th>
                                        <th>Codigo</th>
                                        <th>Concepto</th>
                                        <th>Fecha Req.</th>
                                        <th>Ubigeo Entrega</th>
                                        <th>Dirección Entrega</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th width="90px">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="confirmados" hidden>
                <form id="form-confirmados" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="requerimientosConfirmados">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Tipo</th>
                                        <th>Codigo</th>
                                        <th>Concepto</th>
                                        <th>Fecha Req.</th>
                                        <th>Ubigeo Entrega</th>
                                        <th>Dirección Entrega</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th>Confirmación</th>
                                        <th>Observación</th>
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
@include('almacen.distribucion.requerimientoObs')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/pagos/requerimientoPagos.js')}}"></script>
<script src="{{('/js/almacen/distribucion/requerimientoDetalle.js')}}"></script>
@include('layout.fin_html')