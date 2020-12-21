<div class="modal fade" id="tituloCreate" tabindex="-1" role="dialog" aria-labelledby="tituloCreateLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 0px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="tituloCreateLabel" style="margin-bottom: 7px;">
                    Detalle del Título</h3>
                <label>Padre </label>: <span id="cod_padre_titu"></span> - <span id="descripcion_padre_titu"></span>
            </div>
            <form id="form-tituloCreate">
                <div class="modal-body">
                    <div class="row">
                        <input style="display: none" name="id_presup"/> 
                        <input style="display: none" name="id_titulo"/> 
                        <input style="display: none" name="cod_padre"/>
                        <div class="col-md-4">
                            <h5>Código</h5>
                            <input type="text" name="codigo" class="form-control" readOnly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Descripción</h5>
                            <input type="text" name="descripcion" class="form-control" 
                                placeholder="Ingrese la descripción de la título..." required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit-tituloCreate" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>
