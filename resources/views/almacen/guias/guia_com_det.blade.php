<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_com_det">
    <div class="modal-dialog" style="width:70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Seleccione los Items</h3>
            </div>
            <div class="modal-body">
                <input type="text" class="oculto" name="id_prorrateo">
                <input type="text" class="oculto" name="importe_prorrateo">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaGuiaDetalle">
                    <thead>
                        <tr>
                            <td></td>
                            <td width="10%">Código</td>
                            <td width="50%">Descripción</td>
                            <td width="10%">Cantidad</td>
                            <td width="5%">Unidad</td>
                            <td width="10%">Unitario</td>
                            <td width="10%">Total</td>
                            <td width="10%">Adicional</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_det" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="guardar_prorrateo_detalle();">Agregar</button>
            </div>
        </div>
    </div>
</div>