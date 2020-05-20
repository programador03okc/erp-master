<div class="modal fade" tabindex="-1" role="dialog" id="modal-modal_detalle_nota_lanzamiento">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="title-detalle_nota_lanzamiento">Detalle Nota de Lanzamiento</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control icd-okc" name="id_detalle_nota_lanzamiento" />
                <div class="row">
                    <div class="col-md-12">
                        <h5>Titutlo</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="titulo" placeholder="Títutlo" />
                        </div>
                                
                    </div>
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <textarea class="form-control icd-okc" name="descripcion" placeholder="Descripción" rows="3"></textarea>

                    </div>
                    <div class="col-md-12">
                        <h5>Fecha Registro</h5>
                        <input type="date" class="form-control icd-okc" name="fecha_detalle_nota_lanzamiento" placeholder="Fecha Registro" />

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" name="btnAgregarDetalleNota" onClick="guardarDetalleNotaLanzamiento();">Agregar</button>
                <button class="btn btn-sm btn-primary" name="btnActualizarDetalleNota" onClick="actualizarDetalleNotaLanzamiento();">Actualizar</button>

            </div>
        </div>
    </div>
</div>
