<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-aprobar_orden">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <form id="form-aprobacion_orden">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Aprobación de Orden</h3>
                 </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="oculto" name="id_orden_compra"/>
                            <input type="text" class="oculto" name="codigo_orden"/>

                            <h4>Seguro que desea aprobar la orden <span id="codigo_orden"></span> ?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                     <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                    <button type="button" onclick="cancelarModalAprobarOrden();" class="btn btn-sm btn-danger">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>