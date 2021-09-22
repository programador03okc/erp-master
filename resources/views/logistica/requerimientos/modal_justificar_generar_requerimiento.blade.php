<div class="modal fade" tabindex="-1" role="dialog" id="modal-justificar-generar-requerimiento" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-justificar-generar-requerimiento" onClick="$('#modal-justificar-generar-requerimiento').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Justificar Acción
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Motivo de Generar Requerimiento con Cuadro Costos con Estado: Aprobación Pendiente</h5>
                        <textarea class="form-control" name="motivo_generar_requerimiento" id="motivo_generar_requerimiento" cols="100%" rows="5vh"></textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <label style="display: none;" name="id_cc"></label>
                <button class="btn btn-sm btn-success" onClick="guardarJustificacionGenerarRequerimiento();">Guardar</button>
            </div>
        </div>
    </div>
</div>
