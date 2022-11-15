<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-enviar-solicitud-pago" style="overflow-y: scroll;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form-enviar_solicitud_pago">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Enviar a pago <span class="text-primary" id="codigo_orden"></span> <span class="text-danger" id="condicion_de_envio_pago"></span></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="oculto" name="id_orden_compra" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Prioridad *</h5>
                            <div style="display:flex;">
                                <select class="form-control" name="id_prioridad">
                                    @foreach ($prioridades as $prioridad)
                                    <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <h5>Tipo Destinatario *</h5>
                                <div style="display:flex;">
                                    <select class="form-control activation handleCheckStatusValue handleChangeTipoDestinatario" name="id_tipo_destinatario">
                                        @foreach ($tiposDestinatario as $tipo)
                                        <option value="{{$tipo->id_requerimiento_pago_tipo_destinatario}}">{{$tipo->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Destinatario *</h5>
                                <div style="display:flex;">
                                    <input class="oculto" name="id_persona">
                                    <input class="oculto" name="id_contribuyente">
                                    <input class="oculto" name="id_proveedor">
                                    <input type="text" class="form-control" name="tipo_documento_identidad" placeholder="Tipo" style="width:25%;" disabled>
                                    <input type="text" class="form-control handleBlurBuscarDestinatarioPorNumeroDocumento" name="nro_documento" placeholder="Nro documento" style="width: 75%">
                                    <input type="text" class="form-control handleKeyUpBuscarDestinatarioPorNombre handleFocusInputNombreDestinatario handleFocusOutInputNombreDestinatario" name="nombre_destinatario" placeholder="Nombre destinatario">
                                    <button type="button" class="btn btn-sm btn-flat btn-primary" id="btnAgregarNuevoDestiantario" onClick="modalNuevoDestinatario();" disabled>
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <div class="form-group">
                                    <div class="box box-solid box-default oculto" id="resultadoDestinatario" style="position: absolute; z-index: 999; overflow:scroll; height:20vh; box-shadow: rgb(9 30 66 / 25%) 0px 4px 8px, rgb(9 30 66 / 31%) 0px 0px 1px;">
                                        <div class="box-body">
                                            <ul class="nav nav-pills" role="tablist">
                                                <li>
                                                    <h5>Resultados encontrados: <span class="badge" id="cantidadDestinatariosEncontrados">0</span></h5>
                                                </li>
                                            </ul>
                                            <table class="table table-striped table-hover" id="listaDestinatariosEncontrados"></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Cuenta bancaria *</h5>
                                <div style="display:flex;">
                                    <input class="oculto" name="id_cuenta_persona">
                                    <input class="oculto" name="id_cuenta_contribuyente">
                                    <select class="form-control activation handleCheckStatusValue handleChangeCuenta" name="id_cuenta">
                                    </select>
                                    <button type="button" class="btn btn-sm btn-flat btn-primary" title="Agregar cuenta bancaria" onClick="modalNuevaCuentaDestinatario();">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-flat btn-default handleClickInfoAdicionalCuentaSeleccionada">
                                        <i class="fas fa-question-circle"></i>
                                    </button>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h5>Monto total Orden:</h5>
                                <div class="input-group">
                                    <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda"></div>
                                    <input type="text" class="form-control" name="monto_total_orden" data-monto-total-orden="" placeholder="Monto total orden" readOnly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h5>Monto a pagar:</h5>
                                <div class="input-group">
                                    <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda"></div>
                                    <input type="text" class="form-control" name="monto_a_pagar" placeholder="Monto a pagar">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <h5>Pago en cuotas:</h5>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <label for=""> <input type="checkbox" class="handleCkeckPagoCuotas" name="pagoEnCuotasCheckbox"></label>
                                    </span>
                                    <select class="form-control handleChangeNumeroDeCuotas" name="numero_de_cuotas" placeholder="N° cuotas" disabled>
                                        <option value="2">2 cuotas</option>
                                        <option value="3">3 cuotas</option>
                                        <option value="4">4 cuotas</option>
                                        <option value="5">5 cuotas</option>
                                        <option value="6">6 cuotas</option>
                                        <option value="7">7 cuotas</option>
                                        <option value="8">8 cuotas</option>
                                        <option value="9">9 cuotas</option>
                                        <option value="10">10 cuotas</option>
                                        <option value="11">11 cuotas</option>
                                        <option value="12">12 cuotas</option>
                                        <option value="13">13 cuotas</option>
                                        <option value="14">15 cuotas</option>
                                        <option value="15">15 cuotas</option>
                                        <option value="16">16 cuotas</option>
                                        <option value="17">17 cuotas</option>
                                        <option value="18">18 cuotas</option>
                                        <option value="19">19 cuotas</option>
                                        <option value="20">20 cuotas</option>
                                        <option value="21">21 cuotas</option>
                                        <option value="22">22 cuotas</option>
                                        <option value="23">23 cuotas</option>
                                        <option value="24">24 cuotas</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Adjuntar:</h5>
                                <input type="file" multiple="multiple" class="filestyle handleChangeAgregarAdjuntoRequerimientoCompraCabecera" name="nombre_archivo" placeholder="Seleccionar" data-buttonName="btn-primary" data-buttonText="Seleccionar archivo" data-size="sm" data-iconName="fa fa-folder-open" accept="application/pdf,image/*" />
                                <div style="display:flex; justify-content: space-between;">
                                    <h6>Máximo 1 archivos de seleccion y con un máximo de 100MB por subida.</h6>
                                    <h6>Carga actual: <span class="label label-default" id="tamaño_total_archivos_para_subir">0MB</span></h6>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                        <fieldset class="group-table" style="margin-bottom: 25px">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos para subir</strong></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="adjuntosCabecera" class="mytable table table-condensed table-bordered table-okc-view">
                                    <tbody id="body_archivos_requerimiento_compra_cabecera"></tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                        </div>

                        <div class="col-md-12"  id="group-adjuntosLogisticosRegistrados" hidden>
                            <fieldset class="group-table">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos logísticos registrados</strong></h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="adjuntosDetalle" class="mytable table table-condensed table-bordered table-okc-view" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Archivo</th>
                                                    <th>Fecha emisión</th>
                                                    <th>Nro comprobante</th>
                                                    <th>Tipo</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_adjuntos_logisticos">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="col-md-12" id="group-historialEnviosAPagoLogistica" hidden>
                            <fieldset class="group-table">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Historial de envios a pagos en cuotas</strong></h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="historialEnviosAPagoLogistica" class="mytable table table-condensed table-bordered table-okc-view" style="width: 100%;">
                                            <thead>
                                                <th>N° cuota</th>
                                                <th>Monto</th>
                                                <th>Observación</th>
                                                <th>Fecha registro</th>
                                                <th>Adjuntos</th>                                                
                                            </tr></thead>
                                            <tbody id="body_historial_de_envios_a_pago_en_cuotas">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Comentario:</h5>
                                <textarea class="form-control activation handleCheckStatusValue" name="comentario" placeholder="Comentario (opcional)" cols="100" rows="100" style="height:50px;"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-default" class="close" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-sm btn-success handleClickEnviarSolicitudDePago">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>