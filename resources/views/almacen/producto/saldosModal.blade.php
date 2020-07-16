<div class="modal fade" tabindex="-1" role="dialog" id="modal-saldos">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Saldos de Almacén</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaSaldos">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Código</th>
                            <th>Part Number</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>SubCategoría</th>
                            <th>Stock</th>
                            <th>Reserva</th>
                            <!-- <th>Detalle</th> -->
                            <th hidden>unid.medida</th>
                            <th hidden>id_item</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_item" style="display: none;"></label>
                <label id="saldo_id_producto" style="display: none;"></label>
                <label id="saldo_codigo_item" style="display: none;"></label>
                <label id="part_number" style="display: none;"></label>
                <label id="saldo_descripcion_item" style="display: none;"></label>
                <label id="categoria" style="display: none;"></label>
                <label id="subcategoria" style="display: none;"></label>
                <label id="saldo_cantidad_item" style="display: none;"></label>
                <label id="saldo_unidad_medida_item" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectValue();">Aceptar</button>
            </div>
        </div>
    </div>
</div>