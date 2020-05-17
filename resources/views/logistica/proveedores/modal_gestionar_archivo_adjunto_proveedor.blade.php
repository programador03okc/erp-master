<div class="modal fade" tabindex="-1" role="dialog" id="modal-gestionar-archivo-adjunto-proveedor">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="modal-gestionar-archivo-adjunto-proveedor-title"></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="file" class="custom-file-input"  onchange="agregarAdjuntoProveedor(event)" />
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-8">
                        <div name="text-status" class="text-animate"></div>
                    </div>
                    <div class="col-md-4">
                        <label style="display: none;" id="id_archivo_adjunto_proveedor"></label>
                        <div id="btnAction_archivo_adjunto"></div>
                    </div>
                </div>
                

            </div>
        </div>
    </div>
</div>

