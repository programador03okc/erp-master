<div class="modal fade" tabindex="-1" role="dialog" id="modal-adjuntar-archivos-requerimiento">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Archivos Adjuntos</h3>
            </div>
            <div class="modal-body">
                <div class="row" id="section_upload_files">
                    <div class="col-md-12">
                        <div class="input-group-okc">
                            <input type="file" id="nombre_archivo" class="custom-file-input" onchange="agregarAdjunto(event); return false;" />
                            <div class="input-group-append">
                                <button
                                    type="button"
                                    class="btn btn-info"
                                    onClick="guardarAdjuntos();"
                                    ><i class="fas fa-file-upload"></i> Subir Archivo
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <table class="mytable table table-striped table-condensed table-bordered" id="listaArchivos">
                    <thead>
                        <tr>
                            <th class="hidden"></th>
                            <th class="hidden"></th>
                            <th>#</th>
                            <th>DESCRIPCION</th>
                            <th>
                                
                            <!-- <i class="fas fa-plus-square icon-tabla green boton" 
                                data-toggle="tooltip" data-placement="bottom" 
                                title="Agregar Archivo" onClick="agregarAdjunto(event);"></i> -->
                            </th>
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_archivo_adjunto"></label>
                <label style="display: none;" id="id_requerimiento"></label>
                <label style="display: none;" id="id_detalle_requerimiento"></label>
                <!-- <button class="btn btn-sm btn-success" onClick="guardarAdjuntos();">Aceptar</button> -->
            </div>
        </div>
    </div>
</div>

