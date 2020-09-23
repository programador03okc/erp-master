<div class="modal fade" tabindex="-1" role="dialog" id="modal-transformacion_servicio_create" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 800px;">
        <div class="modal-content">
            <form id="form-transformacion_servicio_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Agregar Servicio</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_cc">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Descripción del servicio</h5>
                            <textarea class="form-control" name="descripcion_cc" id="descripcion_cc" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Importe</h5>
                            <input type="text" class="form-control" name="total"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_transformacion_servicio" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>