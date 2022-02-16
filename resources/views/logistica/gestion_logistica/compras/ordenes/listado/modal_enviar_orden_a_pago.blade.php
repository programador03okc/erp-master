<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-enviar-orden-a-pago" style="overflow-y: scroll;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form-enviar_orden_a_pago">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Enviar a pago <span class="text-primary" id="codigo_orden"></span></h3>
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
                                    <input type="text" class="form-control handleKeyUpBuscarDestinatarioPorNombre handleFocusInputNombreDestinatario handleFocusOutInputNombreDestinatario" name="nombre_destinatario" placeholder="Nombre destinatario" >
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
                                    <button type="button" class="btn btn-sm btn-flat btn-primary"  title="Agregar cuenta bancaria" onClick="modalNuevaCuentaDestinatario();">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-flat btn-default handleClickInfoAdicionalCuentaSeleccionada">
                                        <i class="fas fa-question-circle"></i>
                                    </button>

                                </div>
                            </div>
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
                    <button type="button" class="btn btn-sm btn-success handleClickEnviarOrdenAPago">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>