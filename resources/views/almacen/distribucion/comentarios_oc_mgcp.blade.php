<div class="modal fade" tabindex="-1" role="dialog" id="modal-comentarios_oc_mgcp">
    <div class="modal-dialog" style="width:500px;">
        <div class="modal-content">
            <form id="form-comentarios_oc_mgcp">
                <input type="hidden" id="hdnComentariosMgcpData">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Comentarios para <label id="nro_orden"></label></h4>
                </div>
                <div class="modal-body" id="divComentarios">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="listaComentarios" class="table table-condensed table-hover table-stripped" style="font-size:small;">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Comentario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>