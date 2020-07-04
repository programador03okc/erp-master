<div class="modal fade" tabindex="-1" role="dialog" id="modal-despacho_obs" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 700px;">
        <div class="modal-content">
            <form id="form-despacho_obs">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title" id="codigo_odg"></h3>
                    </div>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="obs_id_od_grupo_detalle">
                    <input type="text" class="oculto" name="obs_id_od">
                    <input type="text" class="oculto" name="obs_id_requerimiento">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Observaciones</h5>
                            <textarea name="obs_confirmacion" id="obs_confirmacion" cols="95" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" id="btnDespachoObs" onClick="despacho_no_conforme();" >Guardar</button>
                    <!-- <input type="submit" id="submit_orden_despacho" class="btn btn-success" value="Guardar"/> -->
                </div>
            </form>
        </div>
    </div>
</div>