<div class="modal fade" tabindex="-1" role="dialog" id="modal-despacho_fecha_programada" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 300px;">
        <div class="modal-content">
            <form id="form-despacho_fecha_programada">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title">Despacho interno</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="req_id_requerimiento">
                    <input type="text" class="oculto" name="req_id_od">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Fecha de programación</h5>
                            <input type="date" class="form-control" name="fecha_despacho"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Comentario</h5>
                            <textarea class="form-control" name="comentario"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" 
                    id="btnDespachoObs" onClick="anularDespachoInterno();" >Quitar despacho</button>
                    <button type="button" class="btn btn-sm btn-success" 
                    id="btnDespachoObs" onClick="generarDespachoInterno();" >Programar despacho</button>
                </div>
            </form>
        </div>
    </div>
</div>