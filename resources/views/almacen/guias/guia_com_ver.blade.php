<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_com_ver">
    <div class="modal-dialog" style="width:900px;">
        <div class="modal-content" >
            <form id="form-guia_com_ver">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Guía de Compra</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_guia_com">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Serie-Número</h5>
                            <label name="serie_numero"></label>
                            <!-- <input type="text" class="form-control" name="serie_numero"> -->
                            <!-- <div class="input-group">
                                <input type="text" class="form-control" 
                                    name="serie" onBlur="ceros_numero('serie');" placeholder="0000" required>
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" 
                                    name="numero" onBlur="ceros_numero('numero');" placeholder="0000000" required>
                            </div> -->
                        </div>
                        <div class="col-md-4">
                            <h5>Fecha de Emisión</h5>
                            <label name="fecha_emision"></label>
                            <!-- <input type="date" class="form-control" name="fecha_emision"> -->
                        </div>
                        <div class="col-md-4">
                            <h5>Fecha de Ingreso</h5>
                            <label name="fecha_almacen"></label>
                            <!-- <input type="date" class="form-control" name="fecha_almacen"> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén Origen</h5>
                            <label name="almacen"></label>
                            <!-- <input type="text" class="form-control" name="almacen"> -->
                        </div>
                        <div class="col-md-4">
                            <h5>Tipo de Operación</h5>
                            <label name="operacion"></label>
                            <!-- <input type="text" class="form-control" name="tp_operacion"> -->
                        </div>
                        <div class="col-md-4">
                            <h5>Clasif. de los Bienes y Servicios</h5>
                            <label name="clasificacion"></label>
                            <!-- <input type="text" class="form-control" name="clasificacion"> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                id="detalleGuiaCompra"  style="margin-top:10px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>O.C.</th>
                                        <th>Req.</th>
                                        <th>Sede Req. (Destino)</th>
                                        <th>Código</th>
                                        <th>PartNumber</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unid</th>
                                        <th>Series</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_guia" class="btn btn-success" value="Generar Transferencia"/>
                    <!-- <button class="btn btn-sm btn-success" onClick="generar_transferencia();">Generar Transferencia</button> -->
                </div>
            </form>
        </div>
    </div>
</div>