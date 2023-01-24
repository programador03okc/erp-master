
<div class="modal fade" id="modal-cierre-apertura" tabindex="-1" role="dialog" aria-labelledby="modal-data">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="cierre-apertura" method="POST">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="ca_id_periodo">
                <input type="hidden" name="ca_id_estado">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="titleCierreApertura"></h3>
                </div>
                <div class="modal-body">
                    <fieldset class="group-table" id="fieldsetDatosGenerales">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Año</h5>
                                <input type="text" name="ca_anio" class="form-control text-center " readonly>
                            </div>
                            <div class="col-md-4">
                                <h5>Mes</h5>
                                <input type="text" name="ca_mes" class="form-control text-center " readonly>
                            </div>
                            <div class="col-md-4">
                                <h5>Acción</h5>
                                <input type="text" name="ca_estado" class="form-control text-center " readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Almacén</h5>
                                <input type="text" name="ca_almacen" class="form-control text-center " readonly> 
                            </div>
                        </div>
                    </fieldset>
                    <br>
                    <fieldset class="group-table" id="fieldsetComentario2">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Comentario</h5>
                                <textarea class="form-control" name="ca_comentario" style="height: 100px;"></textarea>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row">
                        <div class="col-md-12">
                            <h5 style="font-size: 14px;">* Campos obligatorios</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success shadow-none">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>