<div class="modal fade" tabindex="-1" role="dialog" id="modal-adjuntar-archivos-detalle-requerimiento">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Archivos adjuntos de item</h3>
            </div>
            <div class="modal-body">
                <div class="row" id="group-action-upload-file">
                    <div class="col-md-12">
                        <input type="file" class="filestyle" data-input="false" name="nombre_archivo" onchange="requerimientoView.agregarAdjuntoItem(event);" />
                    </div>

                </div>
                <br>
                <table class="mytable table table-striped table-condensed table-bordered" id="listaArchivos">
                    <thead>
                        <tr>
                            <th>DESCRIPCION</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody id="body_archivos_item"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
</div>

