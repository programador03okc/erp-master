<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-contacto" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 600px;">
        <div class="modal-content">
            <form id="form-contacto">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title"> Contacto </h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_contacto"/>
                    {{-- <input type="text" class="oculto" name="id_requerimiento_contacto"/> --}}
                    <input type="text" class="oculto" name="id_contribuyente_contacto"/>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Nombre Completo *</h5>
                            <input type="text" class="form-control" name="nombre" >
                        </div>
                        <div class="col-md-6">
                            <h5>Teléfono *</h5>
                            <input type="text" class="form-control" name="telefono" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Dirección</h5>
                            <input type="text" class="form-control" name="direccion" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Ubigeo Destino</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="ubigeo"/>
                                <input type="text" class="form-control" name="name_ubigeo" readOnly>
                                <button type="button" class="input-group-text btn-primary" id="basic-addon1" onClick="ubigeoModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Correo electrónico</h5>
                            <input type="text" class="form-control" name="email" >
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Horario de atención</h5>
                            <input type="text" class="form-control" name="horario" >
                        </div>
                        <div class="col-md-6">
                            <h5>Cargo</h5>
                            <input type="text" class="form-control" name="cargo" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>* Campos obligatorios</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_contacto" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>