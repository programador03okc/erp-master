<div class="modal fade" tabindex="-1" role="dialog" id="modal-historial-reserva">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-historial-reserva" onClick="$('#modal-historial-reserva').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Historial Reserva <span id="codigoRequerimiento"></span></h3>
            </div>
            <div class="modal-body">
            <div class="row">
                    <div class="col-md-12">
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
                                <span>Cantidad: </span>
                                <label id="cantidad"></label> <label id="unidadMedida"></label>
                            </div>
                        </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4 style="display:flex;justify-content: space-between;">Historial</h4>
                        <fieldset class="group-table" style="padding-top: 20px;">

                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaHistorialReserva" style="margin-bottom: 0px; width:100%;">
                                    <thead>
                                        <tr style="background: grey;">
                                            <th style="width: 10%; text-align:center;">Código reserva</th>
                                            <th style="width: 15%; text-align:center;">Almacén</th>
                                            <th style="width: 5%; text-align:center;">Cantidad reservada</th>
                                            <th style="width: 15%; text-align:center;">Reservado por</th>
                                            <th style="width: 8%; text-align:center;">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyListaHistorialReservaProducto"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

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

