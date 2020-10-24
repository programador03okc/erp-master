<div class="modal fade" tabindex="-1" role="dialog" id="modal-ver-orden">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Orden de Compra <span id="inputCodigo"></span></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal container">
                            <div class="form-group">
                                <label class="col-sm-1 control-label" style="text-align: left;">Proveedor:</label>
                                <div class="col-sm-3">
                                <p class="form-control-static" id="inputProveedor"></p>
                                </div>
                                <label class="col-sm-1 control-label" style="text-align: left;">Estado:</label>
                                <div class="col-sm-2">
                                <p class="form-control-static" id="inputEstado"></p>
                                </div>
                                <label class="col-sm-1 control-label" style="text-align: left;">Fecha:</label>
                                <div class="col-sm-2">
                                <p class="form-control-static" id="inputFecha"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-1 control-label" style="text-align: left;">Plazo Entrega:</label>
                                <div class="col-sm-3">
                                    <p class="form-control-static" id="inputPlazoEntrega"></p>
                                </div>
                                <label class="col-sm-1 control-label" style="text-align: left;">Moneda:</label>
                                <div class="col-sm-2">
                                    <p class="form-control-static" id="inputMoneda"></p>
                                </div>
                                <label class="col-sm-1 control-label" style="text-align: left;">Condición:</label>
                                <div class="col-sm-2">
                                    <p class="form-control-static" id="inputCondicion"></p>
                                </div>


                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                        <table class="mytable table table-responsive table-bordered table-okc-view" 
                            id="tablaItemOrdenCompra">
                            <thead>
                                <tr>
                                    <th width="20">#</th>
                                    <th width="20">CODIGO.</th>
                                    <th width="30">PART NUMBER</th>
                                    <th width="40">CATEGORÍA</th>
                                    <th width="40">SUBCATEGORÍA</th>
                                    <th width="40">DESCRIPCIÓN</th>
                                    <th width="30">UNIDAD</th>
                                    <th width="30">CANTIDAD</th>
                                    <th width="30">PRECIO</th>
                                    <th width="30">SUBTOTAL</th>
                                    <th width="30">ESTADO</th>
        
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                </div>

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>