<div class="modal fade" tabindex="-1" role="dialog" id="modal-catalogo-items">
    <div class="modal-dialog" style="width: 84%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-catalogo-items" onClick="$('#modal-catalogo-items').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de productos</h3>
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                <button class="btn btn-sm btn-primary" id="btn-crear-producto" onclick="crearProducto();">Crear Producto</button>
                <span class="text-info" id="text-info-item-vinculado" title="" hidden> Existe un item del CC Viculado <span class="badge label-danger" onClick="eliminarVinculoItemCC();" style="position: absolute;margin-top: -5px;margin-left: 5px; cursor:pointer" title="Eliminar vínculo">×</span></span>

                </div>
            </div>
                <table class="table table-condensed table-bordered table-okc-view" id="listaItems" width="100%">
                    <thead>
                        <tr>
                            <th style="width: 0%"></th>
                            <th style="width: 0%"></th>
                            <th style="width: 0%"></th>
                            <th style="width: 0%"></th>
                            <th style="width: 3%">Código</th>
                            <th style="width: 3%">Part number</th>
                            <th style="width: 5%">Categoría</th>
                            <th style="width: 5%">Subcategoría</th>
                            <th style="width: 30%">Descripción</th>
                            <th style="width: 5%">Unidad</th>
                            <th style="width: 0%">id_unidad_medida</th>
                            <th style="width: 3%"> Acción</th>
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
                <button class="btn btn-sm btn-primary" data-dismiss="modal-catalogo-items" onClick="$('#modal-catalogo-items').modal('hide');">Cerrar</button>
            </div>
        </div>
    </div>
</div>

