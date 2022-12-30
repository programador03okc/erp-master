<div class="modal fade" tabindex="-1" role="dialog" id="modal-ajustarEstadoReserva" style="overflow-y: scroll;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="form-ajustarEstadoReserva">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Ajustar Estdo de Reserva <label id="codigo_req"></label></h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_reserva">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Estado de reserva</h5>
                            <select class="form-control js-example-basic-single" name="id_estado" required>
                                <option value="">Elija una opción</option>
                                <option value="1">Elaborado</option>
                                <option value="5">Atendido</option>
                                <option value="7">Anulado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_actualizarEstadoReserva" class="btn btn-success" value="Actualizar" />
                </div>
            </form>
        </div>
    </div>
</div>