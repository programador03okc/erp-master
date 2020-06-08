@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="ordenesDespacho">
    <legend class="mylegend">
        <h2 id="titulo">Ordenes de Despacho</h2>
    </legend>
    <div class="col-md-12" id="tab-ordenesPendientes">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Despachos Pendientes</a></li>
            <li class=""><a type="#despachados">Despachos Programados</a></li>
        </ul>
        <div class="content-tabs">
            <section id="pendientes" >
                <form id="form-pendientes" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <form id="frm-example" action="/path/to/your/script" method="POST"> -->
                                <table class="mytable table table-condensed table-bordered table-okc-view" 
                                    id="ordenesDespacho">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th></th>
                                            <th>Codigo</th>
                                            <th>Cliente</th>
                                            <!-- <th>OC Propia</th> -->
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
                                title="Crear Grupo de Ordenes" onClick="crear_grupo_orden_despacho();">Generar G.O.D.</button>
                                <!-- <p><button>Submit</button></p> -->
                            <!-- </form> -->
                        </div>
                    </div>
                </form>
            </section>
            <section id="despachados" hidden>
                <form id="form-despachados" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="requerimientosDespachados">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Codigo</th>
                                        <th>Concepto</th>
                                        <th>Fecha Req.</th>
                                        <th>Observación</th>
                                        <th>Grupo</th>
                                        <th>Responsable</th>
                                        <th width="100px">Acción</th>
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
@include('almacen.distribucion.despachoDetalle')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/distribucion/ordenesDespacho.js')}}"></script>
@include('layout.fin_html')