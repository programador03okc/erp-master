<!-- modal obs -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-por-regularizar">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <form id="form-por-regularizar">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Por regularlizar</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" style="position:relative;">
                            <h4>Comparativo cuadro presupuesto / requerimiento <span class="label label-warning" id="cantidadItemsPorRegularizar"></span></h4>
                            <fieldset class="group-table" style="height:55vh; overflow:auto; margin-top:20px;">
                                <input type="hidden" name="idRequerimiento">
                                <table class="mytable table table-condensed table-striped  table-hover table-bordered table-okc-view" id="listaItemsPorRegularizar" style="margin-bottom: 0px; width:100%;">
                                    <thead>
                                        <tr style="background: grey;">
                                            <th style="text-align:center; border-right: dashed; border-right-color: #ccc;" colspan="4">Cuadro Presupuesto <span id="codigo_cuadro_presupuesto"></span></th>
                                            <th style="text-align:center;" colspan="4">Requerimiento <span id="codigo_requerimiento"></span></th>
                                            <th style="text-align:center;" rowspan="2">Cod. Ordenes</th>
                                            <th style="text-align:center;" rowspan="2">Cod. Reservas</th>
                                            <th style="text-align:center;" rowspan="2">Acción</th>
                                        </tr>
                                        <tr style="background: grey;">
                                            <th style="width: 5%; text-align:center;">Part number</th>
                                            <th style="width: 20%; text-align:center;">Descripción</th>
                                            <th style="width: 10%; text-align:center;">Cantidad</th>
                                            <th style="width: 10%; text-align:center; border-right: dashed; border-right-color: #ccc;">Precio U.</th>
                                            <th style="width: 5%; text-align:center;">Part number</th>
                                            <th style="width: 20%; text-align:center;">Descripción</th>
                                            <th style="width: 10%; text-align:center;">Cantidad</th>
                                            <th style="width: 10%; text-align:center;">Precio U.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodylistaItemsPorRegularizar"></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-success handleClickFinalizarRegularizacion" >Finalizar regularización</button>
                            <button type="button" class="btn btn-sm btn-primary" aria-label="close" data-dismiss="modal">Cerrar</button>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>