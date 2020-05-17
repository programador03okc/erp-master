<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-fechas">
    <div class="modal-dialog" style="width: 400px;">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button> --}}
                <h3 class="modal-title">Ingrese un Rango de Fechas</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Fecha Inicio</h5>
                        <input type="date" class="form-control" name="f_fecha_inicio">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Fecha Fin</h5>
                        <input type="date" class="form-control" name="f_fecha_fin">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="enviar_fechas();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
