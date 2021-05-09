<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-items-requerimiento">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-agregar-items-requerimiento" onClick="$('#modal-agregar-items-requerimiento').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Agregar de Items a Requerimientos</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 btn-group right" role="group" style="margin-bottom: 5px;">
                        <span id='group-inputGuardarNuevosItemsEnRequerimiento' hidden>
                            <button class="btn btn-success" type="button" id="btnGuardarNuevoItemsEnRequerimiento" onClick="guardarNuevosItemsEnRequerimiento();">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </span>
                        <span id='group-inputAgregarItem' hidden>
                            <button class="btn btn-primary" type="button" id="btnAgregarNuevoItem" onClick="agregarNuevoItem();">
                                <i class="fas fa-plus"></i> Agregar Nuevo Item
                            </button>
                        </span>
                    </div>
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" id="listaItemsRequerimientoParaAgregarItem" style="margin-bottom: 0px; width:100%;">
                            <thead>
                                <tr style="background: grey;">
                                    <th>#</th>
                                    <th>CODIGO</th>
                                    <th>PART NUMBER</th>
                                    <th>CATEGORIA</th>
                                    <th>SUBCATEGORIA</th>
                                    <th>DESCRIPCION</th>
                                    <th>UNIDAD</th>
                                    <th>CANTIDAD</th>
                                    <th>ESTADO ACTUAL</th>
                                    <th>ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

