<div class="modal fade" tabindex="-1" role="dialog" id="modal-transportista" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 400px;">
        <div class="modal-content">
            <form id="form-transportista">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Nuevo Transportista</h3>
                </div>
                <div class="modal-body">
                    {{-- <input type="text" class="oculto" name="id_contribuyente"/> --}}
                    <fieldset class="group-table" id="fieldsetNuevoTransportista">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>RUC *</h5>
                                <input type="text" name="nro_documento" class="form-control limpiar" placeholder="Ingrese el ruc"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Razon social *</h5>
                                <input type="text" name="razon_social" class="form-control limpiar" placeholder="Ingrese la razon social"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Teléfono</h5>
                                <input type="text" name="telefono" class="form-control limpiar" placeholder="Ingrese el teléfono"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Dirección</h5>
                                <input type="text" name="direccion_fiscal" class="form-control limpiar" placeholder="Ingrese la dirección fiscal"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>* Campos obligatorios</h5>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" onClick="cerrarTransportista();" value="Cerrar"/>
                    <input type="submit" id="submit_nuevo_transportista" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>