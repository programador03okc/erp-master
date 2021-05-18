<div class="modal fade" tabindex="-1" role="dialog" id="modal-od_transformacion" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 1000px;">
        <div class="modal-content">
            <form id="form-od_transformacion">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Instrucciones para la Transformación</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_detalle_requerimiento"/>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Item Base</label>
                            <h5 name="part_no"></h5><h5 name="descripcion"></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Item Transformado</label>
                            <h5 name="part_no_producto_transformado"></h5><h5 name="descripcion_producto_transformado"></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Opciones Adicionales</label>
                            <div name="adicionales"></div>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Ingresos y Salidas para Transformación</label>
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleTransformacion">
                                <thead>
                                    <tr>
                                        <th>Ingresa</th>
                                        <th>Sale</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    <input type="submit" id="submit_od_transformacion" class="btn btn-success" value="Guardar"/>
                </div> -->
            </form>
        </div>
    </div>
</div>