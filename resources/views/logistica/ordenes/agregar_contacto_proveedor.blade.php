<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-contacto-proveedor" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 500px;">
        <div class="modal-content">
            <form id="form-agregar-contacto" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                    aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title">Agregar Contacto</h3>
                    </div>
                    <center><span id="razon_social_proveedor"></span></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input class="oculto" name="id_contacto">
                            <input class="oculto" name="id_contribuyente">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Nombre Completo</h5>
                                    <input type="text" class="form-control" name="nombre" required/>
                                </div>
                                <div class="col-md-6">
                                    <h5>Cargo</h5>
                                    <input type="text" class="form-control" name="cargo" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>E-mail</h5>
                                    <input type="text" class="form-control" name="email" required/>
                                </div>
                                <div class="col-md-6">
                                    <h5>Teléfono</h5>
                                    <input type="number" class="form-control" name="telefono" required/>
                                </div>
                                <div class="col-md-12">
                                    <h5>Dirección</h5>
                                    <input type="text" class="form-control" name="direccion" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success boton" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>  
</div>
