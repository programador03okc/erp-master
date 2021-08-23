<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-contacto">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-agregar-contacto" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Agregar contacto</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <h5>Nombre</h5>
                                <input type="text" class="form-control activation handleChangeUpdateConcepto" name="nombre">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Cargo</h5>
                                <input type="text" class="form-control activation handleChangeUpdateConcepto" name="cargo">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Telefono</h5>
                                <input type="text" class="form-control activation handleChangeUpdateConcepto" name="telefono">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <h5>Ubigeo</h5>
                                <div style="display:flex;">
                                    <input type="text" class="oculto" name="ubigeo">
                                    <input type="text" class="form-control" name="name_ubigeo" readOnly>
                                    <button type="button" title="Seleccionar Ubigeo" class="btn-primary" onClick="ubigeoModal();"><i class="far fa-compass"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <h5>Dirección</h5>
                                <input type="text" class="form-control activation handleChangeUpdateConcepto" name="direccion">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <h5>Horario</h5>
                                <input type="text" class="form-control activation handleChangeUpdateConcepto" name="horario">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <h5>Email</h5>
                                <input type="text" class="form-control activation handleChangeUpdateConcepto" name="email">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success boton" value="Agregar"/>
                </div>
            </form>
        </div>
    </div>
</div>

