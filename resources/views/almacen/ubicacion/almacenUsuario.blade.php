<div class="modal fade" tabindex="-1" role="dialog" id="modal-almacen_usuario" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:500px;">
        <div class="modal-content">
            <form id="form-almacen_usuario">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Acceso por Usuario</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_almacen">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Usuario</h5>
                            <div style="display:flex;">
                                <input type="text" class="oculto" name="id_usuario">
                                <input type="text" class="form-control" name="nombre_completo" disabled placeholder="Seleccione un usuario" required>
                                <button type="button" class="form-control btn btn-primary btn-flat" style="width: 40px;" onclick="usuarioModal();">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-6">
                            <input type="checkbox" name="crear_editar" style="margin-right: 10px; margin-left: 7px;" />
                            Crear/Editar
                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" name="ver" style="margin-right: 10px; margin-left: 7px;" />
                            Ver
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit-almacen_usuario" class="btn btn-success" value="Guardar" />
                </div>
            </form>
        </div>
    </div>
</div>