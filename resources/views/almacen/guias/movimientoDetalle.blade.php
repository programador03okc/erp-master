<div class="modal fade" tabindex="-1" role="dialog" id="modal-movAlmDetalle" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Ingreso a almacén Guía <span id="guia_com"></span></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" name="id_guia_com_detalle" class="oculto" />
                        <input type="text" name="id_mov_alm" class="oculto" />
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <label class="lbl-codigo" title="Abrir Ingreso" onClick="abrirIngreso()" id="cabecera"></label>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Serie-Número</h5>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="ingreso_serie" placeholder="0000" required>
                                            <span class="input-group-addon">-</span>
                                            <input type="text" class="form-control" name="ingreso_numero" placeholder="0000000" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Proveedor</h5>
                                        <input type="text" class="form-control" id="prov_razon_social" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Fecha de Emisión Guía</h5>
                                        <input type="date" class="form-control" name="fecha_emision"  required>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Fecha de Ingreso</h5>
                                        <input type="date" class="form-control" name="fecha_almacen"  required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Almacén</h5>
                                        <input type="text" class="form-control" id="almacen_descripcion" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Tipo de Operación</h5>
                                        <input type="text" class="form-control" id="operacion_descripcion" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Ordenes Compra</h5>
                                        <input type="text" class="form-control" id="ordenes_compra" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Requerimientos</h5>
                                        <input type="text" class="form-control" id="requerimientos" disabled>
                                    </div>
                                </div>
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleMovimiento" style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>PartNumber</th>
                                            <th>Descripción</th>
                                            <th>Cant.</th>
                                            <th>Unid</th>
                                            <th>Guía Compra</th>
                                            <th>OC/HT/Tr</th>
                                            <th>Requerimiento</th>
                                            <th>Sede Req.</th>
                                            <th width="80px">Series</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Registrado por:</label>
                                        <span id="responsable_nombre"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Ordenes SoftLink:</label>
                                        <span id="ordenes_soft_link"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>