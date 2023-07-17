<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-enviar-solicitud-pago" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
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
                            <input type="text" class="oculto" name="id_moneda" />
                            <input type="text" class="oculto" name="simbolo_moneda" />
                        </div>
                    </div>

                    <fieldset class="group-table" style="margin-bottom: 25px; border-color: #337ab7;">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Datos de generales</strong></h5>
                        <div class="row">

                            <div class="col-md-2">
                                <h5>Prioridad *</h5>
                                <div style="display:flex;">
                                    <select class="form-control" name="id_prioridad">
                                        @foreach ($prioridades as $prioridad)
                                        <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
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

                            <div class="col-md-8">
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

                            <div class="col-md-4">
                                <div class="form-group">
                                    <h5>Cuenta bancaria *</h5>
                                    <div style="display:flex;">
                                        <input class="oculto" name="id_cuenta_persona">
                                        <input class="oculto" name="id_cuenta_contribuyente">
                                        <select class="form-control activation handleCheckStatusValue handleChangeCuenta" name="id_cuenta" required>
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <h5>Monto total Orden:</h5>
                                    <div class="input-group">
                                        <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda"></div>
                                        <input type="text" class="form-control" name="monto_total_orden" data-monto-total-orden="" placeholder="Monto total orden" readOnly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                                            <option value="1">Personalizado</option>
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
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div class="panel panel-primary" style="padding: 5px;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h5>Tipo Impuesto</h5>
                                                <div style="display:flex;">

                                                    <select class="form-control" name="tipo_impuesto" placeholder="Tipo impuesto">
                                                        <option value="0">No aplica</option>
                                                        <option value="1">Detracción</option>
                                                        <option value="2">Renta</option>
                                                    </select>
                                                    <button type="button" class="btn btn-sm btn-flat btn-default handleClickInfoAdicionalTipoImpuesto">
                                                        <i class="fas fa-question-circle"></i>
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </fieldset>

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-table" style="margin-bottom: 25px; border-color: #337ab7;">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos para subir</strong></h5>
                                <div class="row">
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
                                        <table id="adjuntosCabecera" class="mytable table table-condensed table-bordered table-okc-view">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%;">Nombre archivo</th>
                                                    <th>Fecha emisión</th>
                                                    <th>Número y serie</th>
                                                    <th>Categoría adjunto</th>
                                                    <th>Monto total</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_archivos_requerimiento_compra_cabecera"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12" id="group-adjuntosLogisticosRegistrados" hidden>
                            <fieldset class="group-table" style="margin-bottom: 25px; border-color: #337ab7;">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos logísticos registrados</strong></h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="adjuntosDetalle" class="mytable table table-condensed table-bordered table-okc-view" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Archivo</th>
                                                    <th>Fecha emisión</th>
                                                    <th>Número y serie</th>
                                                    <th>Categoría adjunto</th>
                                                    <th>Moto total</th>
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
                    </div>

                    <div class="row">
                        <div class="col-md-12" id="group-historialEnviosAPagoLogistica" hidden>
                            <fieldset class="group-table" style="margin-bottom: 25px; border-color: #337ab7;">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Historial de envios a pagos en cuotas</strong></h5>
                                <div class="row">
                                    <div class="col-md-12" style="height: 170px; min-height: 170px; overflow: auto; resize: vertical;">
                                        <table id="historialEnviosAPagoLogistica" class="mytable table table-condensed table-bordered table-okc-view" style="width: 100%;">
                                            <thead style="position: sticky; top:0;">
                                                <th>N° cuota</th>
                                                <th>Monto</th>
                                                <th>Observación</th>
                                                <th>Fecha registro</th>
                                                <th>Adjuntos</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_historial_de_envios_a_pago_en_cuotas">
                                            </tbody>
                                            <tfoot style="position: sticky; margin-top: 0; bottom: 0;">
                                                <tr class="bg-info">
                                                    <td class="text-right"><strong>Total:</strong></td>
                                                    <td class="text-center"><strong><span name='sumaMontoTotalPagado'></span></strong></td>
                                                    <td class="text-center" colspan="5"><span class="text-danger text-uppercase" name='estadoHistorialEnvioAPagoLogistica'></span></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
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
