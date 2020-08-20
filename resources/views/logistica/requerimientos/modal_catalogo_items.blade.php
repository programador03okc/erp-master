<div class="modal fade" tabindex="-1" role="dialog" id="modal-catalogo-items">
    <div class="modal-dialog" style="width: 84%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-catalogo-items" onClick="$('#modal-catalogo-items').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de items</h3>
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                <button class="btn btn-sm btn-primary" onclick="crearProducto();">Crear Producto</button>
                </div>
            </div>
                <table class="mytable table table-striped table-condensed table-bordered" id="listaItems">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>CODIGO</th>
                            <th>PART NUMBER</th>
                            <th>CATEGORÍA</th>
                            <th>SUBCATEGORÍA</th>
                            <th>DESCRIPCION</th>
                            <th width="120">UNIDAD</th>
                            <th>id_unidad_medida</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_item"></label> 
                <label style="display: none;" id="codigo"></label>
                <label style="display: none;" id="part_number"></label>
                <label style="display: none;" id="descripcion"></label>
                <label style="display: none;" id="id_producto"></label>
                <label style="display: none;" id="id_servicio"></label>
                <label style="display: none;" id="id_equipo"></label>
                <label style="display: none;" id="id_unidad_medida"></label>
                <label style="display: none;" id="unidad_medida"></label>
                <label style="display: none;" id="categoria"></label>
                <label style="display: none;" id="subcategoria"></label>
                <button class="btn btn-sm btn-success" onClick="selectItem();">Aceptar</button>
            </div>
        </div>
    </div>
</div>

