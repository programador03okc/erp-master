<div class="modal fade" tabindex="-1" role="dialog" id="modal-cancelarIncidencia" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width:400px;">
        <div class="modal-content">
            <form id="form-cancelarIncidencia">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Cancelar Incidencia <label id="codigo_incidencia" ></label></h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_incidencia_cancelacion">
                
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-horizontal">
                                <div class="form-group" style="margin-bottom:0px;">
                                    <label class="col-sm-6 control-label" style="text-align: left;">Fecha de cancelación</label>
                                    <div class="col-sm-6">
                                        <input type="date" class="form-control limpiarReporte" name="fecha_cancelacion"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5><strong>Comentario:</strong></h5>
                            <textarea class="form-control limpiarReporte" name="comentarios_cancelacion"></textarea>
                        </div>
                    </div>
                
                </div>
                <div class="modal-footer">
                    {{-- <button id="btn_cerrar" class="btn btn-default" onClick="cerrarFicha();">Cerrar</button> --}}
                    <input type="submit" id="submit_guardar_cancelacion" class="btn btn-success" value="Cancelar incidencia"/>
                </div>
            </form>
        </div>
    </div>
</div>