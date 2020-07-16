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
                            <th rowspan="2"  hidden>Id</th>
                            <th rowspan="2" >Código</th>
                            <th rowspan="2" >Part Number</th>
                            <th rowspan="2" >Descripción</th>
                            <th rowspan="2" >Categoría</th>
                            <th rowspan="2" >SubCategoría</th>
                            <th colspan="2">Almacén Central OKC - Ilo</th>
                            <th colspan="2">Almacén Central OKC - Lima</th>
                            <th rowspan="2" >unid.medida</th>
                            <th rowspan="2" >id_item</th>
                        </tr>
                        <tr>
                            <td>Stock</td>
                            <td>Reserva</td>
                            <td>Stock</td>
                            <td>Reserva</td>
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
                <!-- <button class="btn btn-sm btn-success" onClick="selectValue();">Aceptar</button> -->
            </div>
        </div>
    </div>
</div>