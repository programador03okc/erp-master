<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_compra">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Ordenes de Compra</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaOrdenesCompra">
                    <thead>
                        <tr>
                            <th hidden>Id Orden</th>
                            <th>Proveedor</th>
                            <th>Código</th>
                            <th>Fecha Emisión</th>
                            <th>Estado</th>
                            <th hidden>Id Proveedor</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_orden_com" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectOrdenCompra();">Aceptar</button>
            </div>
        </div>
    </div>
</div>