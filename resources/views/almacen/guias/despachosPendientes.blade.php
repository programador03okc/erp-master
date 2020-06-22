@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="despachosPendientes">
    <legend class="mylegend">
        <h2 id="titulo">Ordenes de Despacho Pendientes</h2>
    </legend>
    <div class="col-md-12" id="tab-ordenes">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Despachos Pendientes</a></li>
            <li class=""><a type="#salidas">Salidas de Almacén</a></li>
        </ul>
        <div class="content-tabs">
            <section id="pendientes" >
                <form id="form-pendientes" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="despachosPendientes" style="width:100px;">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Codigo</th>
                                        <th>Cliente</th>
                                        <th>Requerimiento</th>
                                        <th>Concepto</th>
                                        <th>Almacén</th>
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
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="salidas" hidden>
                <form id="form-salidas" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="despachosEntregados">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Cod.Orden</th>
                                        <th>Cliente</th>
                                        <th>Req.</th>
                                        <th>Concepto</th>
                                        <th>Almacén</th>
                                        <th>Guia</th>
                                        <th>Fecha Salida</th>
                                        <th>Responsable</th>
                                        <th width="50px"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@include('almacen.guias.guia_ven_create')
@include('almacen.distribucion.despachoDetalle')
@include('almacen.guias.guia_ven_obs')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/guia/despachosPendientes.js')}}"></script>
<script src="{{('/js/almacen/guia/guia_ven_create.js')}}"></script>
<script src="{{('/js/almacen/distribucion/despachoDetalle.js')}}"></script>
@include('layout.fin_html')