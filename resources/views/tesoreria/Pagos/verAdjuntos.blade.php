<div class="modal fade" tabindex="-1" role="dialog" id="modal-verAdjuntos" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Ver Adjuntos - <label name="codigo_requerimiento_pago"></label></h3>
                </div>
            </div>
            <form action="" data-form="guardar-adjuntos" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento_pago" />
                    <fieldset class="group-table" id="fieldsetDatosProveedor">
                        <legend style="border-bottom: 0px solid #e5e5e5;width: 40% !important"><h5>Adjuntos de requerimiento</h5></legend>
                        <div class="row">
                            <div class="col-md-12">
                                {{-- <fieldset class="group-table" id="fieldsetDatosProveedor"> --}}
                                    <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos de la cabecera</strong></h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table id="adjuntosCabecera" class="mytable table table-condensed table-bordered table-okc-view" >
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                {{-- </fieldset> --}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos en el detalle</strong></h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="adjuntosDetalle" class="mytable table table-condensed table-bordered table-okc-view" >
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <br>
                    <br>
                    <fieldset class="group-table">
                        <legend style="border-bottom: 0px solid #e5e5e5;width: 30%;"><h5>Adjuntos de tesoreria</h5></legend>
                        <div class="row">
                            <div class="col-md-12" style="margin-bottom: 15px;">
                                <div class="form-group">
                                    <input type="hidden" name="codigo_requerimiento">
                                    <input type="hidden" name="id_requerimiento_pago">
                                    <h5 style="display:flex;justify-content: space-between;"><strong>Adjunto multiple de tesoreria</strong></h5>

                                    <input type="file" multiple="multiple" class="filestyle" name="adjuntos[]" multiple data-action="adjuntos" data-buttonName="btn-primary" data-buttonText="Seleccionar archivo"  data-iconName="fa fa-folder-open" required/>
                                    <div style="display:flex; justify-content: space-between;">
                                        <h6>Máximo de 2MB por subida.</h6>
                                        <h6>Carga actual: <span class="label label-default" id="peso-estimado">0MB</span></h6>
                                    </div>
                                </div>
                                <table id="" class="table text-center" >
                                    <tbody data-action="table-body"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Otros adjuntos de tesoreria</strong></h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="" class="table" >
                                            <tbody data-table="adjuntos-pagos"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success guardar-adjuntos"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
