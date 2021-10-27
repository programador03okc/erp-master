<!-- modal obs -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-opciones-para-regularizar-item">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <form id="form-opciones-para-regularizar-item">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Opciones para regularizar</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Producto <small>(fuente: requerimiento)</small></h4> 
                            <fieldset class="group-table">
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <span>Precio u.: </span>
                                        <label id="precioUnitario"></label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Ordenes <span class="label label-warning" id="cantidadDeIngresos"></span></h4>
                            <fieldset class="group-table" style="padding-top: 20px;">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaOrdenesDeItem" style="margin-bottom: 0px; width:100%;">
                                    <thead>
                                        <tr style="background: grey;">
                                            <th style="width: 10%; text-align:center;">Cód. orden</th>
                                            <th style="width: 10%; text-align:center;">Código</th>
                                            <th style="width: 10%; text-align:center;">Part number</th>
                                            <th style="width: 40%; text-align:left;">Descripción</th>
                                            <th style="width: 10%; text-align:center;">Cantidad</th>
                                            <th style="width: 10%; text-align:center;">Unidad m.</th>
                                            <th style="width: 10%; text-align:center;">Precio u.</th>
                                            <th style="width: 10%; text-align:center;">Documento Vinculado</th>
                                            <th style="width: 8%; text-align:center;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodylistaOrdenesDeItem"></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <h4>Reservas</h4>
                            <fieldset class="group-table" style="padding-top: 20px;">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaReservasDeItem" style="margin-bottom: 0px; width:100%;">
                                    <thead>
                                        <tr style="background: grey;">
                                            <th style="width: 10%; text-align:center;">Cód. reserva</th>
                                            <th style="width: 10%; text-align:center;">Código</th>
                                            <th style="width: 10%; text-align:center;">Part number</th>
                                            <th style="width: 40%; text-align:left;">Descripción</th>
                                            <th style="width: 10%; text-align:center;">Cantidad reservada</th>
                                            <th style="width: 10%; text-align:center;">Almacén</th>
                                            <th style="width: 10%; text-align:center;">Documento Vinculado</th>
                                            <th style="width: 8%; text-align:center;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodylistaReservasDeItem"></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-primary" aria-label="close" data-dismiss="modal">Cerrar</button>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>