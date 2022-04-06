<div class="modal fade" tabindex="-1" role="dialog" id="modal-nueva-reserva" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-nueva-reserva" onClick="$('#modal-nueva-reserva').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Nueva Reserva en almacén <span id="codigoRequerimiento"></span> (<span id="almacenRequerimiento"></span>)</h3>
            </div>
            <div class="modal-body">
            <form id="form-nueva-reserva" type="register" form="formulario"> 
                <input type="hidden" name="idProducto">
                <input type="hidden" name="idRequerimiento">
                <input type="hidden" name="idDetalleRequerimiento">
                <input type="hidden" name="idUnidadMedida">
                <div class="row">
                    <div class="col-md-4">
                        <h4 style="display:flex;justify-content: space-between;">Producto</h4>
                        <fieldset class="group-table" style="padding-top: 20px;">
                        <div class="row">
                            <div class="col-md-12">
                                <span>Part number: </span>
                                <label id="partNumber"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span>Descripción: </span>
                                <label id="descripcion"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span>Cantidad solicitada: </span>
                                <label id="cantidad"></label> <label id="unidadMedida"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span>Cantidad acumulada en Ordenes: </span>
                                <label id="cantidadEnOrdenes"></label>
                            </div>
                        </div>
                        </fieldset>
                    </div>
                    <div class="col-md-8">
                        <h4 style="display:flex;justify-content: space-between;">Almacenes con stock disponible</h4>
                        <fieldset class="group-table" style="padding-top: 20px;">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaAlmacenesConStockDeProducto" wdith="100%">
                                    <thead>
                                        <tr style="background: grey;">
                                            <th width="80%" style="text-align:center;">Almacén</th>
                                            <th width="10%" style="text-align:center;">Saldo</th>
                                            <th width="10%" style="text-align:center;">Stock comprometido</th>
                                            <th width="10%" style="text-align:center;">Stock disponible</th>
                                            <th width="10%" style="text-align:center;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyListaAlmacenesConStockDeProducto"></tbody>
                                </table>
                            </div>
                        </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4 style="display:flex;justify-content: space-between;">Reservar</h4>
                        <fieldset class="group-table" style="padding-top: 20px;">

                        <div class="group-inline">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail2">Cantidad Reservar</label>
                                    <input type="number" min="0" class="form-control" name="cantidadReserva" autofocus>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="exampleInputName2">Almacén seleccionado</label>
                                    <input type="hidden" name="almacenReserva">
                                    <input type="text" class="form-control" name="nombreAlmacenReserva" readonly>

                                    <!-- <select class="form-control activation handleChangeObtenerStockAlmacen" name="almacenReserva">
                                        <option value="0">Seleccione un Almacén</option>
                                        @foreach ($almacenes as $almacen)
                                        <option value="{{$almacen->id_almacen}}" >{{$almacen->descripcion}}</option>
                                        @endforeach
                                    </select> -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 25px;">
                                <button class="btn btn-sm btn-success handleClickAgregarReserva" type="button" id="btnAgregarReserva">
                                        <i class="fas fa-plus"></i> Agregar y Guardar
                                    </button>
                                </div>
                            </div>

                            <!-- <div class="col-md-12 oculto" id="contenedor-info-stock">
                                <ul class="list-inline">
                                    <li style="font-weight: bold; color:#356ed5;">Stock total en almacén: <span id="info-stock-almacen"></span></li>
                                    <li style="font-weight: bold; color:#d535c1;">Reservas activas: <span id="info-reservas-activas"></span</li>
                                    <li style="font-weight: bold; color:#00a65a;">Saldo disponible: <span id="info-saldo-disponible"></span</li>
                                </ul>
                            </div> -->
                        </div>



                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaConReserva" style="margin-bottom: 0px; width:100%;">
                                    <thead>
                                        <tr style="background: grey;">
                                            <th style="width: 10%; text-align:center;">Código reserva</th>
                                            <th style="width: 15%; text-align:center;">Almacén</th>
                                            <th style="width: 5%; text-align:center;">Cantidad reservada</th>
                                            <th style="width: 15%; text-align:center;">Reservado por</th>
                                            <th style="width: 8%; text-align:center;">Estado</th>
                                            <th style="width: 8%; text-align:center;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyListaConReserva"></tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-right"><strong>Cantidad total reserva:</strong></td>
                                            <td class="text-center"><label name="totalReservado">0</label></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row" hidden>
                    <div class="col-md-12">
                        <h4 style="display:flex;justify-content: space-between;">Resumen</h4>
                        <fieldset class="group-table" style="padding-top: 20px;">
                        <div class="row">
                            <div class="col-md-3">
                                <span>Total cantidad atendido con orden: </span>
                                <label id="totalCantidadAtendidoConOrden"></label>
                            </div>
                            <div class="col-md-3">
                                <span>Total cantidad con reserva: </span>
                                <label id="totalCantidadConReserva"></label>
                            </div>
                            <div class="col-md-3">
                                <span>Total: </span>
                                <label id="total"></label>
                            </div>
                        </div>
                        </fieldset>
                    </div>
                </div>

            </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 btn-group right" role="group" style="margin-bottom: 5px;">
                    <span>
                        <button class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

