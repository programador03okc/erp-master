@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="ordenesPendientes">
    <legend class="mylegend">
        <h2 id="titulo">Ordenes Pendientes de Ingreso</h2>
    </legend>
    <div class="col-md-12" id="tab-ordenes">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Ordenes Pendientes</a></li>
            <li class=""><a type="#ingresadas">Ordenes Ingresadas Almacén</a></li>
        </ul>
        <div class="content-tabs">
            <section id="pendientes" >
                <form id="form-pendientes" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="ordenesPendientes">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Cod.Orden</th>
                                        <th>R.U.C.</th>
                                        <th>Razon Social</th>
                                        <th>Fecha Emisión</th>
                                        <th>Condición</th>
                                        <th>Responsable</th>
                                        <th>Mnd</th>
                                        <th>SubTotal</th>
                                        <th>IGV</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="ingresadas" hidden>
                <form id="form-ingresadas" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="ordenesEntregadas">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Cod.Orden</th>
                                        <th>R.U.C.</th>
                                        <th>Razon Social</th>
                                        <th>Fecha Emisión</th>
                                        <th>Condición</th>
                                        <th>Responsable</th>
                                        <th>Mnd</th>
                                        <th>SubTotal</th>
                                        <th>IGV</th>
                                        <th>Total</th>
                                        <th></th>
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
@include('almacen.guias.ordenDetalle')
@include('almacen.guias.ordenesGuias')
@include('almacen.guias.guia_com_create')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/ordenesPendientes.js')}}"></script>
@include('layout.fin_html')