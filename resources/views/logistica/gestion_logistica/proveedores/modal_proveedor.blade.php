<div class="modal fade" tabindex="-1" role="dialog" id="modal-proveedor" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-proveedor" method="post" type="register">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Agregar Proveedor</h3>
                </div>
                <div class="modal-body">
                    <div>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-justified" role="tablist">
                            <li role="presentation" class="active"><a href="#principal" aria-controls="principal" role="tab" data-toggle="tab">Datos principales</a></li>
                            <li role="presentation"><a href="#establecimiento" aria-controls="establecimiento" role="tab" data-toggle="tab">Establecimientos</a></li>
                            <li role="presentation"><a href="#contacto" aria-controls="contacto" role="tab" data-toggle="tab">Contacto</a></li>
                            <li role="presentation"><a href="#cuentas_bancarias" aria-controls="cuentas_bancarias" role="tab" data-toggle="tab">Cuentas bancarias</a></li>
                            <li role="presentation"><a href="#observaciones" aria-controls="observaciones" role="tab" data-toggle="tab">Observaciones</a></li>
                            <li role="presentation" class="oculto"><a href="#adjuntos" aria-controls="adjuntos" role="tab" data-toggle="tab">Adjuntos</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="principal">
                                <fieldset class="group-table">
                                    <div class="row">
                                    <input type="text" class="oculto" name="contribuyenteEncontrado" value="false">
                                    <input type="text" class="oculto" name="idContribuyente">
                                    <input type="text" class="oculto" name="idProveedor">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h5>Tipo contribuyente</h5>
                                                <select class="form-control activation handleChangeTipoContribuyente" name="tipoContribuyente">
                                                    @foreach ($tipoContribuyentes as $tipo)
                                                        @if($tipo->id_tipo_contribuyente ==7)
                                                        <option value="{{$tipo->id_tipo_contribuyente}}" selected>{{$tipo->descripcion}}</option>
                                                        @else
                                                        <option value="{{$tipo->id_tipo_contribuyente}}">{{$tipo->descripcion}}</option>
                                                        @endif
                                                    @endforeach                                              
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h5>Tipo documento</h5>
                                                <select class="form-control activation handleChangeTipoDocumentoIdentidad" name="tipoDocumentoIdentidad">
                                                    @foreach ($tipoDocumentos as $tipo)
                                                        @if($tipo->id_doc_identidad ==2)
                                                        <option value="{{$tipo->id_doc_identidad}}" selected>{{$tipo->descripcion}}</option>
                                                        @else
                                                        <option value="{{$tipo->id_doc_identidad}}">{{$tipo->descripcion}}</option>
                                                        @endif
                                                    @endforeach    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h5>Nro documento</h5>
                                                <input type="text" class="form-control activation handleKeyUpNroDocumento handleFocusoutNroDocumento"  name="nroDocumento">
                                                <span class="text-info oculto" name="info-obtener-contribuyente"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h5>Razón social</h5>
                                                <input type="text" class="form-control activation handleKeyUpRazonSocial" name="razonSocial">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <h5>Dirección</h5>
                                                <input type="text" class="form-control activation" name="direccion">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h5>Pais</h5>
                                                <select class="form-control activation handleChangePais" name="pais">
                                                    @foreach ($paises as $pais)
                                                        @if($pais->id_pais ==170)
                                                        <option value="{{$pais->id_pais}}" selected>{{$pais->descripcion}}</option>
                                                        @else
                                                        <option value="{{$pais->id_pais}}">{{$pais->descripcion}}</option>
                                                        @endif
                                                    @endforeach 
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h5>Ubigeo</h5>
                                                <div style="display:flex;">
                                                    <input type="text" class="oculto" name="ubigeoProveedor">
                                                    <input type="text" class="form-control" name="descripcionUbigeoProveedor" readOnly>
                                                    <button type="button" title="Seleccionar Ubigeo" class="btn-primary handleClickUbigeoSoloNacional handleClickOpenModalUbigeoProveedor" onClick="ubigeoModal();"><i class="far fa-compass"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h5>Teléfono</h5>
                                                <input type="text" class="form-control activation handleKeyUpTelefono" name="telefono">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h5>Celular</h5>
                                                <input type="text" class="form-control activation handleKeyUpCelular" name="celular">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h5>Email</h5>
                                                <input type="email" class="form-control activation" name="email">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="establecimiento">
                                <fieldset class="group-table">
                                    <button type="button" id="btnNuevoEstablecimiento" class="btn btn-sm btn-primary pull-right handleClickNuevoEstablecimiento" style="margin-left:5px;"><i class="fas fa-new"></i>Agregar establecimiento</button>
                                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaEstablecimientoProveedor" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width:20%">Dirección</th>
                                                <th class="text-center" style="width:8%">Ubigeo</th>
                                                <th class="text-center" style="width:8%">Horario atención</th>
                                                <th class="text-center" style="width:8%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodylistaEstablecimientoProveedor"></tbody>
                                    </table>
                                </fieldset>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="contacto">
                                <fieldset class="group-table">
                                    <button type="button" id="btnNuevaContactoProveedor" class="btn btn-sm btn-primary pull-right handleClickNuevoContactoProveedor" style="margin-left:5px;"><i class="fas fa-new"></i>Agregar contacto</button>
                                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaContactoProveedor" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width:20%">Nombre</th>
                                                <th class="text-center" style="width:8%">Cargo</th>
                                                <th class="text-center" style="width:8%">Telefono</th>
                                                <th class="text-center" style="width:10%">Email</th>
                                                <th class="text-center" style="width:10%">Dirección</th>
                                                <th class="text-center" style="width:8%">Ubigeo</th>
                                                <th class="text-center" style="width:8%">Horario atención</th>
                                                <th class="text-center" style="width:8%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodylistaContactoProveedor"></tbody>
                                    </table>
                                </fieldset>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="cuentas_bancarias">
                                <fieldset class="group-table">
                                    <button type="button" id="btnNuevaCuentaBancariaProveedor" class="btn btn-sm btn-primary pull-right handleClickNuevoCuentaBancariaProveedor" style="margin-left:5px;"><i class="fas fa-new"></i>Agregar cuenta</button>
                                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaCuentaBancariasProveedor" width="100%">
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
                                                <th class="text-center" style="width:8%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodylistaCuentasBancariasProveedor"></tbody>
                                    </table>
                                </fieldset>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="observaciones">
                                <fieldset class="group-table">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5>Observación:</h5>
                                                <textarea class="form-control activation" name="observacion" cols="100" rows="100" style="height:50px;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="adjuntos">
                                <fieldset class="group-table">
                                    <button type="button" id="btnNuevaAdjuntoProveedor" class="btn btn-sm btn-primary pull-right handleClickNuevoAdjuntoProveedor" style="margin-left:5px;"><i class="fas fa-new"></i>Agregar adjunto</button>
                                        <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaAdjuntosProveedor" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width:30%">Descripción</th>
                                                    <th class="text-center" style="width:20%">Adjunto</th>
                                                    <th class="text-center" style="width:5%">Fecha registro</th>
                                                    <th class="text-center" style="width:8%">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodylistaAdjuntosProveedor"></tbody>
                                        </table>
                                </fieldset>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
                    <button type="button" class="btn btn-sm btn-success handleClickGuardarProveedor" id="btnGuardarProveedor" >Guardar</button>
                    <button type="button" class="btn btn-sm btn-success handleClickActualizarProveedor oculto" id="btnActualizarProveedor" >Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>