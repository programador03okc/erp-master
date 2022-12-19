<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-adjuntar-orden" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <form id="form-adjunto-orden" enctype="multipart/form-data">
                <input type="hidden" name="id_orden" value="">
                <input type="hidden" name="codigo_orden" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Adjuntar orden - <span class="codigo"></span></h3>
                 </div>
                <div class="modal-body">
                    <fieldset class="group-table" style="margin-bottom: 25px">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Adjuntar archivo</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="file" multiple class="filestyle handleChangeAgregarAdjuntoRequerimientoCompraCabecera" name="nombre_archivo[]" placeholder="Seleccionar archivo" data-buttonName="btn-primary" data-buttonText="Seleccionar archivo" data-size="sm" data-iconName="fa fa-folder-open" accept="application/pdf,image/*" />
                                <div style="display:flex; justify-content: space-between;">
                                    <h6>Máximo 1 archivos de seleccion y con un máximo de 100MB por subida.</h6>
                                    <h6>Carga actual: <span class="label label-default" id="tamaño_total_archivos_para_subir">0MB</span></h6>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="group-table" style="margin-bottom: 25px">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos logisticos</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="adjuntosCabecera" class="mytable table table-condensed table-bordered table-okc-view">
                                    <tbody id="body_archivos_requerimiento_compra_cabecera"></tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="group-table">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos logísticos</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="adjuntosDetalle" class="mytable table table-condensed table-bordered table-okc-view">
                                    <thead>
                                        <th>Archivo</th>
                                        <th>Fecha emisión</th>
                                        <th>Nro comprobante</th>
                                        <th>Tipo</th>
                                        <th>Acción</th>
                                    </thead>
                                    <tbody id="body_adjuntos_logisticos"></tbody>
<<<<<<< HEAD
=======
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="group-table">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos Pago</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="adjuntosPago" class="mytable table table-condensed table-bordered table-okc-view">

                                    <tbody id="body_adjuntos_pago"></tbody>
>>>>>>> develop
                                </table>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                     <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                    <button type="button" class="btn btn-sm btn-danger "data-dismiss="modal" type>Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
