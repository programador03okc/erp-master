<div class="modal fade" tabindex="-1" role="dialog" id="modal-adjuntar-archivos-requerimiento">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Archivos adjuntos de requerimiento</h3>
            </div>
            <div class="modal-body">

                <div class="row" id="group-action-upload-file">
                    <div class="col-md-12">
                        <input type="file" name="nombre_archivo" class="custom-file-input handleChangeAgregarAdjuntoRequerimiento" placeholder="Seleccionar archivo" />
                    </div>
                </div>
                <br>
                <table class="table table-striped table-condensed table-bordered" id="listaArchivosRequerimiento" width="100%">
                    <thead>
                        <tr>
                            <th width="200">Descripción</th>
                            <th width="60">Tipo</th>
                            <th width="70">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="body_archivos_requerimiento">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" class="close" data-dismiss="modal" >Cerrar</button>

            </div>
        </div>
    </div>
</div>