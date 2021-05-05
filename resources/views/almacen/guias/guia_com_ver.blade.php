<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_com_ver">
    <div class="modal-dialog" style="width:1200px;">
        <div class="modal-content" >
            <form id="form-guia_com_ver">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Guía de Compra - <label class="subtitulo_blue">(Vista previa de la Transferencia)</label></h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_guia_com">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Serie-Número</h5>
                            <label name="serie_numero"></label>
                        </div>
                        <div class="col-md-4">
                            <h5>Fecha de Emisión</h5>
                            <label name="fecha_emision"></label>
                        </div>
                        <div class="col-md-4">
                            <h5>Fecha de Ingreso</h5>
                            <label name="fecha_almacen"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén Origen</h5>
                            <label name="almacen"></label>
                        </div>
                        <div class="col-md-4">
                            <h5>Tipo de Operación</h5>
                            <label name="operacion"></label>
                        </div>
                        <div class="col-md-4">
                            <h5>Clasif. de los Bienes y Servicios</h5>
                            <label name="clasificacion"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                id="detalleGuiaCompra"  style="margin-top:10px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>OC/HT</th>
                                        <th>Req.</th>
                                        <th>Sede Req. (Destino)</th>
                                        <th>Código</th>
                                        <th>PartNumber</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unid</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_guia_transferencia" class="btn btn-success" value="Generar Transferencia"/>
                </div>
            </form>
        </div>
    </div>
</div>