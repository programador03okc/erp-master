<div class="modal fade" tabindex="-1" role="dialog" id="modal-ver-agregar-adjuntos-requerimiento-pago" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Adjuntos de requerimiento de pago</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <label for="requerimiento_sustentado">
                            <input type="checkbox" name="requerimiento_sustentado" id="requerimiento_sustentado" value="t">
                            Requerimiento sustentado
                        </label>
                    </div>
                </div>
                <form id="form_ver_agregar_adjuntos_requerimiento_pago">
                    <input type="text" class="oculto" name="id_requerimiento_pago" />
                    <input type="text" class="oculto" name="id_moneda" />
                    <input type="text" class="oculto" name="simbolo_moneda" />
                    <input type="text" class="oculto" name="monto_a_pagar" />
                    <fieldset class="group-table">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntar nuevo</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="file" multiple="multiple" class="filestyle handleChangeAgregarAdjuntoRequerimientoPagoCabecera" name="nombre_archivo" placeholder="Seleccionar archivo" data-buttonName="btn-primary" data-buttonText="Seleccionar archivo" data-size="sm" data-iconName="fa fa-folder-open" />
                                <br>
                                <div style="display:flex; justify-content: space-between;">
                                    <h6>Máximo 5 archivos de seleccieon multiple y con un máximo de 100MB por subida.</h6>
                                    <h6>Carga actual: <span class="label label-default" id="tamaño_total_archivos_para_subir">0MB</span></h6>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    {{-- <br> --}}
                    {{-- <fieldset class="group-table"> --}}
                        {{-- <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos del pago</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="adjuntosPago" class="mytable table table-condensed table-bordered table-okc-view">
                                    <thead>
                                        <tr>
                                            <th>Adjunto</th>
                                            <th>Fecha pago</th>
                                            <th>Observación</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_archivos_pago"></tbody>
                                </table>
                            </div>
                        </div> --}}
                    {{-- </fieldset> --}}
                    <br>
                    <fieldset class="group-table">
                        <legend style="border-bottom: 0px solid #e5e5e5;width: 30%;"><h5>Adjuntos de requerimiento</h5></legend>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos de la cabecera</strong></h5>
                                <table id="adjuntosCabecera" class="mytable table table-condensed table-bordered table-okc-view">
                                    <tbody id="body_archivos_requerimiento_pago_cabecera">
                                        <thead>
                                            <th style="width: 30%;">Nombre archivo</th>
                                            <th style="width: 10%;">Fecha emisión</th>
                                            <th style="width: 15%;">Número y serie</th>
                                            <th style="width: 20%;">Categoría adjunto</th>
                                            <th style="width: 20%;">Monto total</th>
                                            <th style="width: 5%;">Acción</th>
                                        </thead>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos en el detalle</strong></h5>
                                <table id="adjuntosDetalle" class="mytable table table-condensed table-bordered table-okc-view">
                                    <tbody id="body_archivos_requerimiento_pago_detalle"></tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    <br>
                    {{-- <fieldset class="group-table">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos en el detalle</strong></h5>
                                <table id="adjuntosDetalle" class="mytable table table-condensed table-bordered table-okc-view">
                                    <tbody id="body_archivos_requerimiento_pago_detalle"></tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    <br> --}}
                    <fieldset class="group-table">
                        <legend style="border-bottom: 0px solid #e5e5e5;width: 25%;"><h5>Adjuntos de tesoreria</h5></legend>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos del pago</strong></h5>
                                <table id="adjuntosPago" class="mytable table table-condensed table-bordered table-okc-view">
                                    <thead>
                                        <tr>
                                            <th>Adjunto</th>
                                            <th>Fecha pago</th>
                                            <th>Observación</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_archivos_pago"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Otros adjuntos de tesorería</strong></h5>
                                <table class="mytable table table-condensed table-bordered table-okc-view">
                                    <thead>
                                        <th>Adjunto</th>
                                        <th>Fecha emisión</th>
                                        <th>Tipo adjunto</th>
                                    </thead>
                                    <tbody data-table="adjuntos-tesoreria">

                                    </tbody>
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
