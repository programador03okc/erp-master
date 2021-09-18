<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-establecimiento">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="form-agregar-establecimiento" onsubmit="return false;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Agregar establecimiento</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Dirección</h5>
                                <input type="text" class="form-control" name="direccionEstablecimiento">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Ubigeo</h5>
                                <div style="display:flex;">
                                    <input type="text" class="oculto" name="ubigeoEstablecimiento">
                                    <input type="text" class="form-control" name="descripcionUbigeoEstablecimiento" readOnly>
                                    <button type="button" title="Seleccionar Ubigeo" class="btn-primary handleClickOpenModalUbigeoEstablecimiento" onClick="ubigeoModal();"><i class="far fa-compass"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Horario</h5>
                                <input type="text" class="form-control" name="horarioEstablecimiento">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
                    <button type="button" class="btn btn-sm btn-success btnAgregarEstablecimiento handleClickAgregarEstablecimiento" >Agregar</button>
                    <button type="button" class="btn btn-sm btn-success btnActualizarEstablecimiento handleClickActualizarEstablecimiento oculto">Actualizar</button>

                </div>
            </form>
        </div>
    </div>
</div>

