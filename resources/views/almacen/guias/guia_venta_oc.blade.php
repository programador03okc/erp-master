<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_detalle_ing">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Seleccione los Items</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" 
                id="listaDetalleIng">
                    <thead>
                        <tr>
                            <td width="5%"></td>
                            <td width="10%">Guía Nro.</td>
                            <td width="10%">Ing Nro.</td>
                            <td width="10%">Código</td>
                            <td width="30%">Descripción</td>
                            <td width="15%">Posición</td>
                            <td width="5%">Cantidad</td>
                            <td width="5%">Unidad</td>
                            <td width="10%">Unitario</td>
                            <td width="10%">Total</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_det" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="guardar_detalle_ing();">Guardar</button>
            </div>
        </div>
    </div>
</div>