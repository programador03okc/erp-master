<div class="modal fade" tabindex="-1" role="dialog" id="modal-procesarPago" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Procesar Pago - <label name="cod_serie_numero"></label></h3>
                </div>
            </div>
            <form id="form-procesarPago" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_oc" />
                    <input type="text" class="oculto" name="id_doc_com" />
                    <input type="text" class="oculto" name="codigo" />
                    <input type="text" class="oculto" name="total" />
                    <div class="row">
                        <div class="col-md-3">
                            <span>Proveedor: </span>
                        </div>
                        <div class="col-md-9">
                            <label name="razon_social"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <span>Cta. Bancaria: </span>
                        </div>
                        <div class="col-md-9">
                            <label name="cta_bancaria"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Fecha del Pago</h5>
                            <input type="date" class="form-control" name="fecha_pago" value="<?= date('Y-m-d'); ?>" required />
                        </div>
                        <div class="col-md-5">
                            <h5>Total a pagar</h5>
                            <div style="display:flex;">
                                <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" readOnly />
                                <input type="number" class="form-control right" name="total_pago" step="0.01" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Motivo</h5>
                            <textarea name="observacion" id="observacion" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Adjuntar Archivo</h5>
                            <input type="file" name="adjunto" id="adjunto" class="filestyle" data-buttonName="btn-warning" data-buttonText="Adjuntar" data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_procesarPago" class="btn btn-success" value="Guardar" />
                </div>
            </form>
        </div>
    </div>
</div>