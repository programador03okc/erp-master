<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_confirmacion" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 700px;">
        <div class="modal-content">
            <form id="form-orden_despacho_confirmacion">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Confirmación de Entrega del Despacho</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_od"/>
                    <input type="text" class="oculto" name="con_id_requerimiento">
                    <input type="text" class="oculto" name="id_od_grupo_detalle">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Guías Adicionales</h5>
                            <textarea name="guias_adicionales" id="guias_adicionales" cols="105" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Importe Total (S/)</h5>
                            <input type="number" class="form-control" name="importe_total" step="any" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_od_confirmacion" class="btn btn-success" value="Confirmar"/>
                </div>
            </form>
        </div>
    </div>
</div>