<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_transportista" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 500px;">
        <div class="modal-content">
            <form id="form-orden_despacho_transportista">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Guía del Transportista</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_od"/>
                    <input type="text" class="oculto" name="con_id_requerimiento">
                    <input type="text" class="oculto" name="id_od_grupo_detalle">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Agencia</h5>
                            <!-- <input type="text" name="agencia" class="form-control" required/> -->
                            <div style="display:flex;">
                                <input class="oculto" name="tr_id_proveedor"/>
                                <input type="text" class="form-control" name="tr_razon_social" placeholder="Seleccione un proveedor..." 
                                    aria-describedby="basic-addon1" disabled="true" required>
                                <button type="button" class="input-group-text activation" id="basic-addon1" onClick="transportistaModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                                <button type="button" class="btn-primary activation" title="Agregar Proveedor" onClick="addProveedorModal();">
                                    <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Guía TR (Serie-Numero)</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                    name="serie" onBlur="ceros_numero('serie');" placeholder="0000" required>
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" 
                                    name="numero" onBlur="ceros_numero('numero');" placeholder="0000000" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Fecha de Guía</h5>
                            <input type="date" name="fecha_transportista" class="form-control" value="<?=date('Y-m-d');?>" required/>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-md-12">
                            <h5>Guías Adicionales</h5>
                            <textarea name="guias_adicionales" id="guias_adicionales" cols="105" rows="5" required></textarea>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Código de Envío</h5>
                            <input type="text" name="codigo_envio" class="form-control"/>
                        </div>
                        <div class="col-md-6">
                            <h5>Monto Flete (S/)</h5>
                            <input type="number" class="form-control" name="importe_flete" step="any" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_od_transportista" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>