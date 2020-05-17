<div class="modal fade" tabindex="-1" role="dialog" id="modal-actualizar-item-sin-codigo">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Actualizar Item</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Código</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="codigo_item" placeholder="Código de Item" disabled />
                            <button type="button" class="btn-primary activation" title="Ingresar Código de Catálogo" onClick="ingresarCodigoCatalogoItems();">
                            <i class="fas fa-pen"></i></button>
                        </div>
                                
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción de ítem</h5>
                        <textarea class="form-control icd-okc" rows="2" name="descripcion_item" style="height:50px" disabled></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_detalle_orden"></label>
                <label style="display: none;" id="id_val_cot"></label>
                <label style="display: none;" id="new_id_item"></label>
                <button class="btn btn-sm btn-primary" onClick="actualizarCodigoItem();">Actualizar</button>
            </div>
        </div>
    </div>
</div>

@include('logistica.requerimientos.modal_catalogo_items')
