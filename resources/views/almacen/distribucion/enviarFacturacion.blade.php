<div class="modal fade" tabindex="-1" role="dialog" id="modal-enviarFacturacion" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 600px;">
        <div class="modal-content">
            <form id="form-enviarFacturacion">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Enviar a Facturación <span id="cod_req"></span></h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Fecha de Facturación</h5>
                            <input type="date" class="form-control activation" name="fecha_facturacion" value="<?=date('Y-m-d');?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Comentario sobre la Facturación</h5>
                            <textarea name="obs_facturacion" id="obs_facturacion" class="form-control" rows="3" ></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-success" id="btnEnviarFacturacion" onClick="enviarFacturacion();" >Guardar</button>
                    <!-- <input type="submit" id="submit_orden_despacho" class="btn btn-success" value="Guardar"/> -->
                </div>
            </form>
        </div>
    </div>
</div>