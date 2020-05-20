<div class="modal fade" tabindex="-1" role="dialog" id="modal-gestionar_grupo_criterio">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Gestionar Grupo Criterio</h3>
            </div>
            <div class="modal-body">

            <div class="row">
                <div class="col-md-6">
                    <fieldset class="group-table">
                        <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listarGrupoCriterio">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Descripción</th>
                                    <th width="10">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
                    <div class="col-md-6">
                        <form>
                        <div class="row">
                            <div class="col-md-12 btn-group btn-group-sm">
                            <button type="button" class="btn btn-sm btn-default" name="btnGuardarGrupoCriterio" onClick="guardarGrupoCriterio()">Guardar</button>
                            <button type="button" class="btn btn-sm btn-default" name="btnNuevosGrupoCriterio" onClick="nuevoGrupoCriterio()">Nuevo</button>
                            <button type="button" class="btn btn-sm btn-default" name="btnEditarGrupoCriterio" onClick="editarGrupoCriterio()">Editar</button>
                            </div>
                        </div>
                        <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="hidden" class="form-control" name="id_grupo_criterio_" >
                                        <input type="text" class="form-control activation" name="descripcion_grupo_criterio_" placeholder="Descripción" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                <label>Estado</label>
                                <select class="form-control activation" name="estado_grupo_criterio_" onchange="" disabled >
                                    <option value="0" selected >Elija una opción</option>
                                    <option value="1">Activado</option>
                                    <option value="7">Anulado</option>
                                </select>         
                                </div>
                            </div>

                        </form>
                    
                </div>
            </div>


            </div>
        </div>
    </div>
</div>