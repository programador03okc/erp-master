<div class="modal fade" tabindex="-1" role="dialog" id="modal-cierreIncidencia" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 400px;">
        <div class="modal-content">
            <form id="form-cierreIncidencia">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Cierre de la Incidencia <label id="codigo_incidencia" ></label></h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_incidencia_cierre">
                    
                    {{-- <fieldset class="group-table" id="fieldsetDetallesEntidad"> --}}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-horizontal">
                                    <div class="form-group" style="margin-bottom:0px;">
                                        <h5 class="col-sm-6 control-label" style="text-align: left;">Fecha de cierre:</h5>
                                        <div class="col-sm-6">
                                            <input type="date" class="form-control limpiarReporte" name="fecha_cierre"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-horizontal">
                                    <div class="form-group" style="margin-bottom:0px">
                                        <h5 class="col-sm-6 control-label" style="text-align: left;">
                                            Costo del servicio contratado:</h5>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control edition limpiarReporte" name="importe_gastado"
                                            style="margin-top: 7px;"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-horizontal">
                                    <div class="form-group" style="margin-bottom:0px;">
                                        <h5 class="col-sm-6 control-label" style="text-align: left;padding-bottom:5px;">Parte reemplazada:</h5>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control limpiarReporte" name="parte_reemplazada"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Comentario:</h5>
                                <textarea class="form-control limpiarReporte" name="comentarios_cierre"></textarea>
                            </div>
                        </div>
                    {{-- </fieldset> style="display:flex;justify-content: space-between;"--}}
                    
                </div>
                <div class="modal-footer">
                    {{-- <button id="btn_cerrar" class="btn btn-default" onClick="cerrarFicha();">Cerrar</button> --}}
                    <input type="submit" id="submit_guardar_cierre" class="btn btn-success" value="Cerrar incidencia"/>
                </div>
            </form>
        </div>
    </div>
</div>