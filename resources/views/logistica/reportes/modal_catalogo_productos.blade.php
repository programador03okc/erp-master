<div class="modal fade" tabindex="-1" role="dialog" id="modal-catalogo-productos">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Productos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaProductos">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>CODIGO</th>
                            <th>DESCRIPCION</th>
                            <th width="120">UNIDAD</th>
                            <th width="100">STOCK</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_item"></label>
                <label style="display: none;" id="codigo"></label>
                <label style="display: none;" id="descripcion"></label>
                <label style="display: none;" id="id_producto"></label>
                <button class="btn btn-sm btn-success" onClick="selectProducto();">Aceptar</button>
            </div>
        </div>
    </div>
</div>