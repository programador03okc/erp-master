<div class="modal fade" tabindex="-1" role="dialog" id="modal-od_transformacion" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 500px;">
        <div class="modal-content">
            <form id="form-od_transformacion">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Instrucciones para la Transformación</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_detalle_requerimiento"/>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Part Number</h5>
                            <input type="text" class="form-control" name="part_number_transformado">
                        </div>
                        <div class="col-md-6">
                            <h5>Cantidad</h5>
                            <input type="number" class="form-control" name="cantidad_transformado">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Descripción</h5>
                            <textarea name="descripcion_transformado" cols="65" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Comentario</h5>
                            <textarea name="comentario_transformado" cols="65" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_od_transformacion" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>