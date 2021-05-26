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
                        <div class="form-group">
                            <label for="categoria_adjunto">Tipo archivo:</label>
                            <select name="categoria_adjunto" id="categoria_adjunto" class="form-control">
                            @foreach ($categoria_adjunto as $categoria)
                                        <option value="{{$categoria->id_categoria_adjunto}}">{{$categoria->descripcion}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" id="group-adjunto-requerimiento">
                            <label for="categoria_adjunto">Cargar archivo:</label>
                            <input type="file" name="nombre_archivo" class="custom-file-input" placeholder="Seleccionar archivo"
                            onchange="agregarAdjuntoRequerimiento(event); return false;"
                            />
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-success"
                    onClick="guardarAdjuntosRequerimiento();"
                    ><i class="fas fa-file-upload"></i> Subir Archivo
                </button>
            </div>
        </div>
    </div>
</div>

