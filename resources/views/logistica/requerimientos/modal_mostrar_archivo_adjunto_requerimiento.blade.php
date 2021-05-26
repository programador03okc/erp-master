<div class="modal fade" tabindex="-1" role="dialog" id="modal-mostrar-archivos-adjuntos-requerimiento">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Archivos adjuntos</h3>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="group-table">
                            <div class="row">
                                <div class="col-md-12">

                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs nav-justified" role="tablist">
                                        <li role="presentation" class="active"><a href="#otrosArchivos" aria-controls="otrosArchivos" role="tab" data-toggle="tab">Otros archivos</a></li>
                                        <li role="presentation"><a href="#ordenes" aria-controls="ordenes" role="tab" data-toggle="tab">Ordenes</a></li>
                                        <li role="presentation"><a href="#comprobanteBancario" aria-controls="comprobanteBancario" role="tab" data-toggle="tab">Comprobante bancario</a></li>
                                        <li role="presentation"><a href="#comprobanteContable" aria-controls="comprobanteContable" role="tab" data-toggle="tab">Comprobante contable</a></li>
                                        <li role="presentation">
                                            <div style="text-align: end; padding-right: 15px;">
                                                <button class="btn btn-xs btn-success" onclick="adjuntoRequerimientoModal(event);" id="btnAgregarAdjuntoReq" data-toggle="tooltip" data-placement="bottom" title="Agregar Adjunto"><i class="fas fa-plus"></i>Adjuntos</button>
                                            </div>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="otrosArchivos">
                                            <br>
                                            <table class="table table-bordered table-condensed table-hover no-footer" id="tablaAdjuntoOtrosArchivos" width="100%">
                                                <thead style=" background: #f4f4f4; ">
                                                    <tr>
                                                        <th width="200">DESCRIPCION</th>
                                                        <th width="60">FECHA</th>
                                                        <th width="70">ACCIÓN</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body_detalle_requerimiento">
                                                    <tr id="default_tr">
                                                        <td></td>
                                                        <td colspan="3"> No hay datos registrados</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="ordenes">
                                            <br>
                                            <table class="table table-bordered table-condensed table-hover no-footer" id="tablaAdjuntoOrdenes" width="100%">
                                                <thead style=" background: #f4f4f4; ">
                                                    <tr>
                                                        <th width="200">DESCRIPCION</th>
                                                        <th width="60">FECHA</th>
                                                        <th width="70">ACCIÓN</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body_detalle_requerimiento">
                                                    <tr id="default_tr">
                                                        <td></td>
                                                        <td colspan="3"> No hay datos registrados</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="comprobanteBancario">
                                            <br>
                                            <table class="table table-bordered table-condensed table-hover no-footer" id="tablaAdjuntoComprobanteBancario" width="100%">
                                                <thead style=" background: #f4f4f4; ">
                                                    <tr>
                                                        <th width="200">DESCRIPCION</th>
                                                        <th width="60">FECHA</th>
                                                        <th width="70">ACCIÓN</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body_detalle_requerimiento">
                                                    <tr id="default_tr">
                                                        <td></td>
                                                        <td colspan="3"> No hay datos registrados</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="comprobanteContable">
                                            <br>
                                            <table class="table table-bordered table-condensed table-hover no-footer" id="tablaAdjuntoComprobanteContable" width="100%">
                                                <thead style=" background: #f4f4f4; ">
                                                    <tr>
                                                        <th width="200">DESCRIPCION</th>
                                                        <th width="60">FECHA</th>
                                                        <th width="70">ACCIÓN</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body_detalle_requerimiento">
                                                    <tr id="default_tr">
                                                        <td></td>
                                                        <td colspan="3"> No hay datos registrados</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>