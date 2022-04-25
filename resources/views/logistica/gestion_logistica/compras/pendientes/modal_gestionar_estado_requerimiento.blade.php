<div class="modal fade" tabindex="-1" role="dialog" id="modal-gestionar-estado-requerimiento" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-gestionar-estado-requerimiento" onClick="$('#modal-gestionar-estado-requerimiento').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="display: flex;justify-content: space-between;">
                    <div>Gestionar estado requerimiento <span id="codigoRequerimiento"></span></div>
                    <div style="font-size: 2rem;">
                        <span>Estado actual: <span class="label label-default" id="estadoActualRequerimiento"></span></span>
                        <span>Estado virtual: <span class="label label-info" id="estadoVirtualRequerimiento"></span></span>
                    </div>
                </h3>
            </div>
            <div class="modal-body">
                <form id="form-gestionar-estado-requerimiento" type="register" form="formulario">
                    <input type="hidden" name="idRequerimiento">
                    <input type="hidden" name="idNuevoEstado">

                    <div class="row">
                        <div class="col-md-12">
                        <p>En este formulario puede ajustar la cantidad de cada item de requerimiento con pendientes por atención, puede anular cierta cantidad, que no supere la sumatoría de la cantidad solicitada más la cantidad atendida( atención por orden o por reserva). Si la cantidad a anular es todo lo pendiente por atender se marcará como anulado</p>
                            <div style="display: flex; justify-content: space-between;">
                                <h4>Ajuste de cantidades solicitadas por item</h4>
                                <button class="btn btn-sm btn-primary handleClickAutoAjustarCantidad" type="button" id="btnAutoAjustarCantidad"><i class="fas fa-magic"></i>Ajustar a estado atención total</button>
                            </div>
                            <fieldset class="group-table" style="padding-top: 20px;">
                                <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view dataTable no-footer" id="listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Part number</th>
                                            <th>Cód. Prod.</th>
                                            <th>Cód. Soft.</th>
                                            <th style="width: 280px;">Descripción</th>
                                            <th>Unidad</th>
                                            <th>Cantidad original</th>
                                            <th>Cantidad para anular</th>
                                            <th>Razones de anulación</th>
                                            <th>Reserva almacén</th>
                                            <th>Atención orden</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada"></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12  right" role="group" style="margin-bottom: 5px;">
                    <button class="btn btn-sm btn-success handleClickActualizarGestionEstadoRequerimiento" type="button" id="btnActualizarGestionEstadoRequerimiento">Guardar</button>
                    <button class="btn btn-sm btn-default" class="close" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>