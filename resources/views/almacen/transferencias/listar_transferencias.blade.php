@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="transferencias_pendientes">
    <legend class="mylegend">
        <h2>Gestión de Transferencias entre Almacenes</h2>
    </legend>
    <div class="col-md-12" id="tab-transferencias">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#pendientes">Transferencias Pendientes</a></li>
            <li class=""><a type="#recibidas">Transferencias Recibidas</a></li>
        </ul>
        <div class="content-tabs">
            <section id="pendientes" >
                <form id="form-pendientes" type="register">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén Origen</h5>
                            <select class="form-control" name="id_almacen_ori">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- <div class="col-md-4">
                            <h5>Almacén Destino</h5>
                            <select class="form-control activation" name="id_almacen_des">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div> -->
                        <div class="col-md-4">
                            <h5>Actualizar</h5>
                            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                                data-placement="bottom" title="Actualizar" 
                                onClick="listarTransferenciasPendientes();">Actualizar</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="listaTransferenciasPendientes">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Fecha Trans.</th>
                                        <th>Nro.Trans.</th>
                                        <th>Guía Venta</th>
                                        <th>Guía Compra</th>
                                        <!-- <th>Fecha de Guía</th> -->
                                        <th>Almacén Origen</th>
                                        <th>Almacén Destino</th>
                                        <th>Responsable Origen</th>
                                        <th>Responsable Destino</th>
                                        <th>Registrado por</th>
                                        <th>Estado</th>
                                        <th>OC</th>
                                        <th>Req.</th>
                                        <th>Concepto</th>
                                        <th width="10%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="recibidas" hidden>
                <form id="form-recibidas" type="register">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén Origen</h5>
                            <select class="form-control" name="id_almacen_ori_recibida">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Actualizar</h5>
                            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                                data-placement="bottom" title="Actualizar" 
                                onClick="listarTransferenciasRecibidas();">Actualizar</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="listaTransferenciasRecibidas">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Fecha Trans.</th>
                                        <th>Nro.Trans.</th>
                                        <th>Guía Venta</th>
                                        <th>Guía Compra</th>
                                        <!-- <th>Fecha de Guía</th> -->
                                        <th>Almacén Origen</th>
                                        <th>Almacén Destino</th>
                                        <th>Responsable Origen</th>
                                        <th>Responsable Destino</th>
                                        <th>Registrado por</th>
                                        <th>Estado</th>
                                        <th>OC</th>
                                        <th>Req.</th>
                                        <th>Concepto</th>
                                        <th width="7%">Acción</th>
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
@include('almacen.transferencias.transferencia_detalle')
@include('almacen.guias.guia_com_obs')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/transferencias/listar_transferencias.js')}}"></script>
<script src="{{('/js/almacen/transferencias/transferencia_detalle.js')}}"></script>
<script src="{{('/js/almacen/guia/guia_venta.js')}}"></script>
@include('layout.fin_html')