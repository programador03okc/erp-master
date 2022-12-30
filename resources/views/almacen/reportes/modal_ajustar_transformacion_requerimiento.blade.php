<div class="modal fade" tabindex="-1" role="dialog" id="modal_ajustar_transformacion_requerimiento" style="overflow-y: scroll;">
    <div class="modal-dialog modal-md" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal_ajustar_transformacion_requerimiento" onClick="$('#modal_ajustar_transformacion_requerimiento').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="display: flex;justify-content: space-between;">
                    <div>Ajustar transformación de requerimiento</div>
                </h3>
            </div>
            <div class="modal-body">
                <form id="form-ajustar-transformacion-requerimiento" type="register" form="formulario">
                    <input type="hidden" name="idRequerimiento">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="display: flex; align-items: baseline;"><input type="checkbox" class="pull-left handleCheckTransformacion" name="transformacionCabecera"> <i class="fas" id="iconoTransformacion" style="color:red;"></i> <span style="font-weight: bold; font-size:2rem;" id="codigoRequerimiento"></span> <span id="textoTieneONoTransformacion"></span>  </div> 
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div style="display: flex; justify-content: space-between;">
                            </div>
                            <fieldset class="group-table" style="padding-top: 20px; overflow-x: hidden;height: 60vh;">
                                <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view dataTable no-footer" id="tablaListaItemsParaAjusteTransformacion" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Part number</th>
                                            <th>Cód. Prod.</th>
                                            <th>Cód. Soft.</th>
                                            <th style="width: 280px;">Descripción</th>
                                            <th>Estado</th>
                                            <th>Transformación?</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12  right" role="group" style="margin-bottom: 5px;">
                    <button class="btn btn-sm btn-success handleClickActualizarAjusteTransformacionRequerimiento" type="button">Guardar</button>
                    <button class="btn btn-sm btn-default" class="close" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>