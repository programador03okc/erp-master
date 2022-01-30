<div class="modal fade" tabindex="-1" role="dialog" id="modal-adjuntar-archivos-requerimiento-pago-detalle" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Archivos adjuntos</h3>
                <span id="descripcion"></span>
            </div>
            <div class="modal-body">

                <div class="row" id="group-action-upload-file">
                    <div class="col-md-12">
                        <input type="file" multiple="multiple" class="filestyle handleChangeAgregarAdjuntoDetalle"  name="nombre_archivo" placeholder="Seleccionar archivo" data-buttonName="btn-primary" data-buttonText="Seleccionar archivo" data-size="sm" data-iconName="fa fa-folder-open" />
                    </div>
                </div>
                <br>
                <table class="table table-striped table-condensed table-bordered" id="listaArchivosRequerimientoPagoDetalle" width="100%">
                    <thead>
                        <tr>
                            <th width="200">Descripción</th>
                            <th width="70">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="body_archivos_requerimiento_pago_detalle">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" class="close" data-dismiss="modal" >Cerrar</button>

            </div>
        </div>
    </div>
</div>