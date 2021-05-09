<div class="modal fade" tabindex="-1" role="dialog" id="modal-ordenes">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Ordenes de Compra</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaOrdenes">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Codigo</th>
                            <th>RUC</th>
                            <th>Razon Social</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_orden_compra" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectOrden();">Aceptar</button>
            </div>
        </div>
    </div>
</div>