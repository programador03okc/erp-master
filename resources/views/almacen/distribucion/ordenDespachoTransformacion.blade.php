<div class="modal fade" tabindex="-1" role="dialog" id="modal-od_transformacion" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1000px;">
        <div class="modal-content">
            <form id="form-od_transformacion">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Instrucciones para la Transformación</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_detalle_requerimiento" />
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Producto Base</h4>
                            <!-- <h5 name="part_no"></h5>
                            <h5 name="descripcion"></h5> -->
                            <table class="mytable table table-condensed table-bordered " width="100%" id="productoBase">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="15%">Part Number</th>
                                        <th class="text-center" width="15%">Marca</th>
                                        <th>Descripción del producto</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Producto Transformado</h4>
                            <!-- <h5 name="part_no_producto_transformado"></h5>
                            <h5 name="descripcion_producto_transformado"></h5> -->
                            <table class="mytable table table-condensed table-bordered " width="100%" id="productoTransformado">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="15%">Part Number</th>
                                        <th class="text-center" width="15%">Marca</th>
                                        <th>Descripción del producto</th>
                                        <th>Comentario</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td>Opciones Adicionales</td>
                                        <td colspan="3">
                                            <div name="adicionales"></div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Ingresos y Salidas para Transformación</h4>
                            <table class="mytable table table-condensed table-bordered " width="100%" id="detalleTransformacion">
                                <thead>
                                    <tr>
                                        <th>Ingresa</th>
                                        <th>Sale</th>
                                        <th>Comentario</th>
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