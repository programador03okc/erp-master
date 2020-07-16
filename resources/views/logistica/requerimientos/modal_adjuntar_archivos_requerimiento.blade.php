<div class="modal fade" tabindex="-1" role="dialog" id="modal-adjuntar-archivos-requerimiento">
    <div class="modal-dialog" style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Archivos Adjuntos al Requerimiento</h3>
            </div>
            <div class="modal-body">
                <div class="row" id="section_upload_files">
                    <div class="col-md-12">
                        <div class="input-group-okc">
                            <input type="file" name="nombre_archivo" class="custom-file-input" 
                            onchange="agregarAdjuntoRequerimiento(event); return false;"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-info"
                    onClick="guardarAdjuntosRequerimiento();"
                    ><i class="fas fa-file-upload"></i> Subir Archivo
                </button>
            </div>
        </div>
    </div>
</div>

