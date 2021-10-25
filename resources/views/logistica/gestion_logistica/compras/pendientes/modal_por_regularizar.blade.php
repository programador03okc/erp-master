<!-- modal obs -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-por-regularizar">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <form id="form-por-regularizar">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Items por regularlizar </h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id_doc_aprob">
                            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaItemsPorRegularizar" style="margin-bottom: 0px; width:100%;">
                                    <thead>
                                        <tr style="background: grey;">
                                            <th style="width: 10%; text-align:center;">Código</th>
                                            <th style="width: 10%; text-align:center;">Part number</th>
                                            <th style="width: 40%; text-align:center;">Descripción</th>
                                            <th style="width: 10%; text-align:center;">Cantidad</th>
                                            <th style="width: 10%; text-align:center;">Precio U.</th>
                                            <th style="width: 5%; text-align:center;">Ordenes C.</th>
                                            <th style="width: 5%; text-align:center;">Reservada</th>
                                            <th style="width: 5%; text-align:center;">Ingresos Almacén</th>
                                            <th style="width: 8%; text-align:center;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodylistaItemsPorRegularizar"></tbody>
                                </table>
                         </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                        <!-- <button type="button" class="btn btn-sm btn-success handleClickRegistrarObservaciónRequerimientoLogistica">Registrar Respuesta</button> -->
                        <button type="button" class="btn btn-sm btn-primary" aria-label="close" data-dismiss="modal">Cerrar</button>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>