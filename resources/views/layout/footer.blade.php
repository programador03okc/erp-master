            </section>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-settings">
        <div class="modal-dialog" style="width: 17%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Configuración</h4>
                </div>
                <div class="modal-body">
                    <form id="formSettingsPassword">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Actual contraseña</h5>
                                <input type="password" name="pass_old" class="form-control input-sm" aria-describedby="basic-addon" placeholder="Clave actual" required>
                            </div>
                            <div class="col-md-12">
                                <h5>Nueva contraseña</h5>
                                <input type="password" name="pass_new" class="form-control input-sm" aria-describedby="basic-addon" placeholder="Nueva clave" required>
                            </div>
                            <div class="col-md-12">
                                <h5>Confirmar contraseña</h5>
                                <input type="password" name="pass_renew" class="form-control input-sm" aria-describedby="basic-addon" placeholder="Repita nueva clave" required>
                            </div>
                        </div>
                        <br>
                        <button type="button" class="btn btn-success btn-block btn-sm" onclick="execSetting();"> Guardar </button>
                    </form>
                </div>
            </div>
        </div>
    </div>