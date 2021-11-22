<div class="modal fade" tabindex="-1" role="dialog" id="modal-priorizarDespachoExterno" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 400px;">
        <div class="modal-content">
            <form id="form-priorizarDespachoExterno">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Priorizar Despacho Externo</h3>
                </div>
                <form id="form-priorizarDespachoExterno">
                    <div class="modal-body">
                        {{-- <input type="text" class="oculto" name="id_contribuyente"/> --}}
                        <fieldset class="group-table" id="fieldsetPriorizarDespachoExterno">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>Fecha despacho</h5>
                                    <input type="date" name="fecha_despacho" class="form-control limpiar" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>Fecha facturación</h5>
                                    <input type="date" name="fecha_facturacion" class="form-control limpiar"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Comentario</h5>
                                    <textarea name="comentario" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-default"  data-dismiss="modal" value="Cerrar"/>
                        {{-- <input type="submit" id="submit_contacto" class="btn btn-success" value="Guardar"/> --}}
                        <input type="submit" id="submit_priorizarDespachoExterno" class="btn btn-success" value="Priorizar"/>
                        {{-- <button class="btn btn-sm btn-success" id="btnEnviarFacturacion" onClick="guardarPriorizado();" >Guardar</button> --}}
                    </div>
                </form>
            </form>
        </div>
    </div>
</div>