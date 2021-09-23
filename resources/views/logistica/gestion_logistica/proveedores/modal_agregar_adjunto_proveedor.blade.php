<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-adjunto-proveedor" style="overflow-y: scroll;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="form-agregar-adjunto-proveedor" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Agregar adjunto</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Descripción:</h5>
                                    <textarea class="form-control activation" name="descripcion" cols="100" rows="100" style="height:50px;"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">

                            <div class="row" id="group-action-upload-file">
                                <div class="col-md-12">
                                <h5>Adjunto:</h5>
                                    <input type="file" name="nombre_archivo" class="custom-file-input handleChangeAgregarAdjuntoRequerimiento" placeholder="Seleccionar archivo" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success boton" value="Agregar" />
                </div>
            </form>
        </div>
    </div>
</div>