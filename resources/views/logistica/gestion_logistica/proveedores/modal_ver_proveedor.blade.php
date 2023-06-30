<div class="modal fade" tabindex="-1" role="dialog" id="modal-ver-proveedor" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-ver-proveedor">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Información de Proveedor - <span id="tituloAdicional"></span></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Nav tabs -->
                        <div class="col-md-3">
                            <ul class="nav nav-pills nav-stacked" role="tablist">
                                <li role="presentation" class="active"><a href="#principalSoloLectura" aria-controls="principalSoloLectura" role="tab" data-toggle="tab">Datos principales</a></li>
                                <li role="presentation"><a href="#establecimientoSoloLectura" aria-controls="establecimientoSoloLectura" role="tab" data-toggle="tab">Establecimientos</a></li>
                                <li role="presentation"><a href="#contactoSoloLectura" aria-controls="contactoSoloLectura" role="tab" data-toggle="tab">Contacto</a></li>
                                <li role="presentation"><a href="#cuentasBancariasSoloLectura" aria-controls="cuentasBancariasSoloLectura" role="tab" data-toggle="tab">Cuentas bancarias</a></li>
                                <li role="presentation"><a href="#observacionesSoloLectura" aria-controls="observacionesSoloLectura" role="tab" data-toggle="tab">Observaciones</a></li>
                                <li role="presentation" class="oculto"><a href="#adjuntosSoloLectura" aria-controls="adjuntosSoloLectura" role="tab" data-toggle="tab">Adjuntos</a></li>
                            </ul>
                        </div>
                        <!-- Tab panes -->
                        <div class="col-md-9">
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="principalSoloLectura">
                                    <fieldset class="group-table">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="">
                                                <dt>Tipo contribuyente</dt>
                                                <dd><p class="form-control-static" name="tipoContribuyente"></p></dd>
                                                <dt>Tipo documento</dt>
                                                <dd><p class="form-control-static" name="tipoDocumentoIdentidad"></p></dd>
                                                <dt>Nro documento</dt>
                                                <dd><p class="form-control-static" name="nroDocumento"></p></dd>
                                                <dt>Razón social</dt>
                                                <dd><p class="form-control-static" name="razonSocial"></p></dd>
                                                <dt>Dirección</dt>
                                                <dd><p class="form-control-static" name="direccion"></p></dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <dl class="">
                                                <dt>País</dt>
                                                <dd><p class="form-control-static" name="pais"></p></dd>
                                                <dt>Ubigeo</dt>
                                                <dd><p class="form-control-static" name="descripcionUbigeoProveedor"></p></dd>
                                                <dt>Teléfono</dt>
                                                <dd><p class="form-control-static" name="telefono"></p></dd>
                                                <dt>Celular</dt>
                                                <dd><p class="form-control-static" name="celular"></p></dd>
                                                <dt>Email</dt>
                                                <dd><p class="form-control-static" name="email"></p></dd>
                                            </dl>
                                        </div>
                                    </div>
                                    </fieldset>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="establecimientoSoloLectura">
                                    <fieldset class="group-table">
                                        <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaEstablecimientoProveedorSoloLectura" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width:10%">Dirección</th>
                                                    <th class="text-center" style="width:8%">Ubigeo</th>
                                                    <th class="text-center" style="width:8%">Horario atención</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodylistaEstablecimientoProveedorSoloLectura"></tbody>
                                        </table>
                                    </fieldset>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="contactoSoloLectura">
                                    <fieldset class="group-table">
                                        <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaContactoProveedorSoloLectura" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width:20%">Nombre</th>
                                                    <th class="text-center" style="width:8%">Cargo</th>
                                                    <th class="text-center" style="width:8%">Telefono</th>
                                                    <th class="text-center" style="width:10%">Email</th>
                                                    <th class="text-center" style="width:10%">Dirección</th>
                                                    <th class="text-center" style="width:8%">Ubigeo</th>
                                                    <th class="text-center" style="width:8%">Horario atención</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodylistaContactoProveedorSoloLectura"></tbody>
                                        </table>
                                    </fieldset>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="cuentasBancariasSoloLectura">
                                    <fieldset class="group-table">
                                        <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaCuentaBancariasProveedorSoloLectura" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width:20%">Banco</th>
                                                    <th class="text-center" style="width:5%">Tipo cuenta</th>
                                                    <th class="text-center" style="width:8%">Moneda</th>
                                                    <th class="text-center" style="width:10%">Nro cuenta</th>
                                                    <th class="text-center" style="width:10%">Nro cuenta interbancaria</th>
                                                    <th class="text-center" style="width:10%">Swift</th>
                                                    <th class="text-center" style="width:10%">Fecha registro</th>
                                                    <th class="text-center" style="width:10%">Fecha actualización</th>
                                                    <th class="text-center" style="width:10%">Usuario</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodylistaCuentasBancariasProveedorSoloLectura"></tbody>
                                        </table>
                                    </fieldset>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="observacionesSoloLectura">
                                    <fieldset class="group-table">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>Observación:</h5>
                                                    <p class="form-control-static" name="observacion"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="adjuntosSoloLectura">
                                    <fieldset class="group-table">
                                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaAdjuntosProveedorSoloLectura" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width:30%">Descripción</th>
                                                        <th class="text-center" style="width:20%">Adjunto</th>
                                                        <th class="text-center" style="width:5%">Fecha registro</th>
                                                        <th class="text-center" style="width:8%">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="bodylistaAdjuntosProveedorSoloLectura"></tbody>
                                            </table>
                                    </fieldset>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>