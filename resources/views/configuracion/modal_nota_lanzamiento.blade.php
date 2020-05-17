<div class="modal fade" tabindex="-1" role="dialog" id="modal-modal_nota_lanzamiento">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="title-nota_lanzamiento">Nota de Lanzamiento</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control icd-okc" name="id_nota_lanzamiento"  />
                <div class="row">
                    <div class="col-md-12">
                        <h5>Versión</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="version" placeholder="Versión" />
                        </div>
                                
                    </div>
                    <div class="col-md-12">
                        <h5>Version Actual?</h5>
                        <select class="form-control" name="version_actual" onchange="">
                            <option value="false" selected >No</option>
                            <option value="true">Si</option>
                        </select>                                    
                    </div>
                    <div class="col-md-12">
                        <h5>Fecha Registro</h5>
                        <input type="date" class="form-control icd-okc" name="fecha_nota_lanzamiento" placeholder="Fecha Registro" />

                     </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" name="btnAgregarNota" onClick="guardarNotaLanzamiento();">Agregar</button>
                <button class="btn btn-sm btn-primary" name="btnActualizarNota" onClick="actualizarNotaLanzamiento();">Actualizar</button>

            </div>
        </div>
    </div>
</div>
