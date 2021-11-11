<div class="modal fade" tabindex="-1" role="dialog" id="modal-contacto_enviar" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 450px;">
        <div class="modal-content">
            
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Enviar contacto <label id="codigo_cdp"></label></h3>
                </div>
                <div class="modal-body">
                    <form id="form-contacto_enviar" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id_requerimiento">
                
                        <div class="form-group">
                            <h5>Mensaje</h5>
                            <textarea class="form-control" rows="12" name="mensaje_contacto" style="height: 260px;"></textarea>
                        </div>
                        {{-- <div class="form-group">
                            <h5>Adjuntar archivos (los archivos de la O/C ya se incluyen con la orden de despacho)</h5>
                            <input type="file" name="archivos[]" multiple="true" class="form-control filestyle" 
                            data-buttonName="btn-primary" data-buttonText="Seleccionar archivo" data-size="sm" 
                            data-iconName="fa fa-folder-open"/>
                        </div> --}}
                    </form>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" id="submit_contacto_enviar" class="btn btn-success">Enviar</button> --}}
                    <button id="btn_enviar_correo_contacto" class="btn btn-success" onClick="enviarDatosContacto();">Enviar correo</button>
                </div>
            </form>
        </div>
    </div>
</div>