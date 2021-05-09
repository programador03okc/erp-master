<div class="modal fade" tabindex="-1" role="dialog" id="modal-registrar_pago" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 400px;">
        <div class="modal-content">
            <form id="form-registrar_pago" enctype="multipart/form-data" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                    aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title">Registrar Pago <label class="subtitulo_red" id='codigo_orden'></label></h3>
                        
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input class="oculto" name="id_pago">
                            <input class="oculto" name="id_orden_compra">
                            <input class="oculto" name="codigo_orden">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Detalle del Pago</h5>
                                    <textarea name="detalle_pago" class="form-control" rows="4" cols="30" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Archivo Adjunto</h5>
                                    <input type="file" name="archivo_adjunto" id="archivo_adjunto" class="filestyle"
                                        data-buttonName="btn-primary" data-buttonText="Seleccionar archivo"
                                        data-size="sm" data-iconName="fa fa-folder-open" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success boton" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>  
</div>
