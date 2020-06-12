<div class="modal fade" tabindex="-1" role="dialog" id="modal-detalle_orden">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Items de Orden</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaDetalleOrden">
                    <thead>
                        <tr>
                            <th hidden>Id Detalle Orden</th>
                            <th>Check</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio</th>
                            <th>subtotal</th>
                            <th>plazo_entrega</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_detalle_orden" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectDetalleOrden();">Aceptar</button>
            </div>
        </div>
    </div>
</div>