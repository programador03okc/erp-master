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
                    <input type="hidden" name="forzarActualizarEstadoRequerimiento" value="NO">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>En este formulario puede ajustar la cantidad solicitada de cada item de requerimiento con pendientes por atención (se considera la cantidad atendida).
                                Considere si la "cantidad solicitada" sea igual a la "cantidad para anulada" y el item NO TIENE atención, el item tedrá un estado "anulado", de lo contrario si TIENE alguna atención el item, el estado será "atendido total".
                                Si desea establecer el estado de item como "atendido total" haga clic en el check (tenga encuenta que si tiene una cantidad anulada y marcado el check, ambos se condierarán en la actualización).</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div style="display: flex; justify-content: space-between;">
                                <h4>Ajuste en items</h4>
                                <!-- <button class="btn btn-sm btn-primary handleClickControlTodoCheckEnAtencionTotal" type="button" id="btnControlTodoCheckEnAtencionTotal"><i class="far fa-square" id="icoEstadoCheck"></i> Todo en atención total</button> -->
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
                                            <th>Cantidad virtual</th>
                                            <th>Razones de ajuste</th>
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