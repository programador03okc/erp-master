<div class="modal fade" tabindex="-1" role="dialog" id="modal-verAdjuntos" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Ver Adjuntos - <label name="codigo_requerimiento_pago"></label></h3>
                </div>
            </div>
            
            <div class="modal-body">
                <input type="text" class="oculto" name="id_requerimiento_pago" />

                <fieldset class="group-table" id="fieldsetDatosProveedor">
                    <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos de la cabecera</strong></h5>
                    <div class="row">
                        <div class="col-md-12">
                            <table id="adjuntosCabecera" class="mytable table table-condensed table-bordered table-okc-view" >
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>
                <br>

                <fieldset class="group-table" id="fieldsetDatosProveedor">
                    <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos en el detalle</strong></h5>
                    <div class="row">
                        <div class="col-md-12">
                            <table id="adjuntosDetalle" class="mytable table table-condensed table-bordered table-okc-view" >
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>
                <br>
            </div>
            <div class="modal-footer">
                <input type="submit" id="submit_verAdjuntos" class="btn btn-success" value="Registrar pago" />
            </div>
            
        </div>
    </div>
</div>