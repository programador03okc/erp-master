<div class="modal fade" tabindex="-1" role="dialog" id="modal-asignar-accesos">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="title-detalle_nota_lanzamiento">Asignar Accesos</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control icd-okc" name="id_detalle_nota_lanzamiento" />
                <div class="form-inline">
                    <div class="form-group">
                    <label for="exampleInputName2">Lista de Roles</label>
                        <select class="form-control icd-okc" name="roles_usuario" id="roles_usuario" onChange="buildArbolSistema();">
                        </select>
                    </div>
                    <div class="form-group">
                        <!-- <button type="button" class="btn btn-primary">Crear Nuevo Rol</button> -->
                    </div>
                </div>
 
                <br>
                <div class="row">
                    <div class="col-md-12">
                            <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist" id="tab_modulos">
                            <li role="presentation" class="active"><a href="#modulo1" aria-controls="modulo1" role="tab" data-toggle="tab">modulo 1</a></li>
                            <li role="presentation" class=""><a href="#modulo2" onClick="vista_extendida();" aria-controls="modulo2" role="tab" data-toggle="tab">modulo 2</a></li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content" id="tabpanel_modulos">
                            <div role="tabpanel" class="tab-pane active" id="modulo1">
                                    <div class="panel panel-default">
                                        <div class="panel-body" style="overflow: scroll; height: 35vh;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="">
                                                        Option modulo 1 one is this and that&mdash;be sure to include why it's great
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="">
                                                        Option modulo 1 one is this and that&mdash;be sure to include why it's great
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="">
                                                        Option modulo 1 one is this and that&mdash;be sure to include why it's great
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="">
                                                        Option modulo 1 one is this and that&mdash;be sure to include why it's great
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="">
                                                        Option modulo 1 one is this and that&mdash;be sure to include why it's great
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="">
                                                        Option modulo 1 one is this and that&mdash;be sure to include why it's great
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="">
                                                        Option modulo 1 one is this and that&mdash;be sure to include why it's great
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="">
                                                        Option modulo 1 one is this and that&mdash;be sure to include why it's great
                                                    </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="modulo2">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="">
                                                        Option modulo 2one is this and that&mdash;be sure to include why it's great
                                                    </label>
                                                    </div>
                                                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" name="btnActualizarAccesoUsuario" onClick="actualizarAccesoUsuario();">Actualizar</button>

            </div>
        </div>
    </div>
</div>
