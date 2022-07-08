<div class="modal fade" tabindex="-1" role="dialog" id="modal-listaItemsRequerimientoParaVincular" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Items habilitados para vincular - <span id="codigoRequerimiento"></span></h3>
            </div>
            <div class="modal-body">
                <div class="box box-widget">
                    <div class="box-body">
                        <input type="hidden" id="idRequerimiento">
                        <button type="button" class="btn btn-success btn-sm" id="btnAgregarItemADetalleOrden" onclick="agregarItemADetalleOrden(this);" data-toggle="tooltip" data-placement="bottom" title="Agregar Item(s) seleccionados" disabled><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar item(s) </button>
                        <div class="table-responsive">
                            <table class="table table-condensed table-bordered table-okc-view" id="listaItemsRequerimientoParaVincular" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%; text-align:center;"><input type="checkbox" class="handleCheckSeleccionarTodoItemParaVincular" id="checkSeleccionarTodos"> Todos</th>
                                        <th style="width: 5%; text-align:center;">Part number</th>
                                        <th style="width: 5%; text-align:center;">Cód. producto</th>
                                        <th style="width: 5%; text-align:center;">Cód. softlink</th>
                                        <th style="width: 40%; text-align:center;">Descripción</th>
                                        <th style="width: 8%; text-align:center;">Unidad medida</th>
                                        <th style="width: 5%; text-align:center;">Cantidad.</th>
                                        <th style="width: 8%; text-align:center;">Precio unitario</th>
                                        <th style="width: 5%; text-align:center;">Reserva almacén</th>
                                        <th style="width: 5%; text-align:center;">Reserva Orden</th>
                                        <th style="width: 5%; text-align:center;">Estado</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" class="close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>