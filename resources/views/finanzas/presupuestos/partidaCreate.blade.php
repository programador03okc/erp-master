<div class="modal fade" id="partidaCreate" tabindex="-1" role="dialog" aria-labelledby="partidaCreateLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 0px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="partidaCreateLabel" style="margin-bottom: 7px;">
                    Detalle de la Partida</h3>
                <label>Padre </label>: <span id="cod_padre"></span> - <span id="descripcion_padre"></span>
            </div>
            <form id="form-partidaCreate">
                <div class="modal-body">
                    <div class="row">
                        <input style="display: none" name="id_presup"/> 
                        <input style="display: none" name="id_partida"/> 
                        <input style="display: none" name="cod_padre"/>
                        <div class="col-md-4">
                            <h5>Código</h5>
                            <input type="text" name="codigo" class="form-control" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Descripción</h5>
                            <input type="text" name="descripcion" class="form-control" 
                                placeholder="Ingrese la descripción de la partida..." required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Total</h5>
                            <input type="number" name="importe_total" class="form-control" step="0.01" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit-partidaCreate" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>
