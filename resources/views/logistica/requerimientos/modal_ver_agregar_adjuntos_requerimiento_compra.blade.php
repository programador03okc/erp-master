<div class="modal fade" tabindex="-1" role="dialog" id="modal-ver-agregar-adjuntos-requerimiento-compra" style="overflow-y: scroll;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Archivos adjuntos de requerimiento logístico</h3>
            </div>
            <div class="modal-body">
                <form id="form_ver_agregar_adjuntos_requerimiento_compra">
                    <input type="text" class="oculto" name="id_requerimiento" />
                    <fieldset class="group-table">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntar nuevo</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="file" multiple="multiple" class="filestyle handleChangeAgregarAdjuntoRequerimientoCompraCabecera" name="nombre_archivo" placeholder="Seleccionar archivo" data-buttonName="btn-primary" data-buttonText="Seleccionar archivo" data-size="sm" data-iconName="fa fa-folder-open" />
                                <br>
                            </div>
                        </div>
                    </fieldset>
                    <br>
                    <fieldset class="group-table">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos de la cabecera requerimiento</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="adjuntosCabecera" class="mytable table table-condensed table-bordered table-okc-view">
                                    <tbody id="body_archivos_requerimiento_compra_cabecera"></tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    <br>
                    <fieldset class="group-table">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos Items requerimiento</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="adjuntosDetalle" class="mytable table table-condensed table-bordered table-okc-view">
                                    <tbody id="body_archivos_requerimiento_compra_detalle"></tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    <br>
                    <fieldset class="group-table">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos de pago</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="adjuntosDePagos" class="mytable table table-condensed table-bordered table-okc-view">
                                    <tbody id="body_archivos_pagos"></tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success handleClickGuardarAdjuntosAdicionales">Guardar</button>
                <button class="btn btn-sm btn-default" class="close" data-dismiss="modal">Cerrar</button>

            </div>
        </div>
    </div>
</div>