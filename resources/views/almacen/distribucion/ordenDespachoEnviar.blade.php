<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_enviar" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 600px;">
        <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Enviar orden de despacho <label id="codigo_cdp"></label></h3>
                </div>
                <div class="modal-body">
                    <form id="form-orden_despacho_enviar" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id_requerimiento">
                        <input type="hidden" name="id_oportunidad">
                        <input type="hidden" name="envio">
                        <input type="hidden" name="codigo">

                        <div class="form-group">
                            <h5>Mensaje para la orden</h5>
                            <textarea class="form-control" rows="12" name="mensaje" style="height: 250px;"></textarea>
                        </div>
                        <div class="form-group">
                            <h5>Adjuntar archivos (los archivos de la O/C ya se incluyen con la orden de despacho)</h5>
                            <input type="file" name="archivos[]" multiple="true" class="form-control">
                            {{-- <input type="file" name="archivos[]" multiple="true" class="form-control filestyle"
                            data-buttonName="btn-primary" data-buttonText="Seleccionar archivo" data-size="sm"
                            data-iconName="fa fa-folder-open"/> --}}
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submit_orden_despacho_enviar" class="btn btn-success">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>
