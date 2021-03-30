<div class="modal fade" tabindex="-1" role="dialog" id="modal-procesarPago" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Procesar Pago</h3>
                </div>
            </div>
            <form id="form-procesarPago" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="text" class="oculto" name="id_requerimiento"/>
                        <input type="text" class="oculto" name="codigo"/>
                        <div class="col-md-6">
                            <h5>Fecha del Pago</h5>
                            <input type="date" class="form-control" name="fecha_pago" value="<?=date('Y-m-d');?>" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Motivo</h5>
                            <textarea name="observacion" id="observacion" cols="70" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Adjuntar Archivo</h5>
                            <input type="file" name="adjunto" id="adjunto" class="filestyle"
                                data-buttonName="btn-warning" data-buttonText="Adjuntar" 
                                data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_procesarPago" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>  
</div>
