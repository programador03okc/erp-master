<div class="modal fade" tabindex="-1" role="dialog" id="modal-editar-usuario">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="title-detalle_nota_lanzamiento">Editar Usuario</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control icd-okc" name="id_detalle_nota_lanzamiento" />
                <div class="row">
                    <div class="col-md-3">
                        <h5>Nombre</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Apellido Paterno</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Apellido Materno</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Nombre Corto</h5>
                        <input type="text" class="form-control icd-okc" name="" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Usuario</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Contraseña</h5>
                        <div style="display:flex;">
                            <input type="password" class="form-control icd-okc" name="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Email</h5>
                        <input type="text" class="form-control icd-okc" name="" />
                    </div>
                    <div class="col-md-3">
                        <h5>Rol</h5>
                        <select class="form-control icd-okc" name="" id="">
                            <option value="">Rol 1</option>
                            <option value="">Rol 2</option>
                            <option value="">Rol 3</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" name="btnActualizarDetalleNota" onClick="actualizarDetalleNotaLanzamiento();">Actualizar</button>

            </div>
        </div>
    </div>
</div>
