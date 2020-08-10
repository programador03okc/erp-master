<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="modal-promocion-item">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">El Producto tiene Promoción <i class="fas fa-gift"></i></h3>
            </div>
            <div class="modal-body bg-warning">
                <div class="row">
                    <div class="col-md-12">
                        <p>El producto seleccionado <strong id="producto_descripcion"></strong> tiene las siguientes promociones:</p>
                        <ul id="productos_con_promocion">
                        </ul>
                    </div>
                </div>	
            </div>
            <div class="modal-footer bg-warning">
            <label style="display: none;" id="id_item"></label>
            <label style="display: none;" id="id_producto"></label>
                <strong>¿Desea agregar las promociones?</strong>
                <button class="btn btn-sm btn-success" onClick="agregarPromociones()">Agregar</button>
                <button class="btn btn-sm btn-danger" onClick="omitirPromocion()">Omitir</button>
            </div>
        </div>
    </div>
</div>