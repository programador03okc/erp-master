<div class="modal fade" tabindex="-1" role="dialog" id="modal-requerimiento-pago" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <form id="form-requerimiento-pago" method="post" type="register">
                <input type="hidden" name="id_requerimiento_pago" primary="ids">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="modal-title">Requerimiento de pago</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-table">
                                <div class="row">
                                    <input type="text" class="oculto" name="idProveedor">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <h5>Código:</h5>
                                            <input type="text" class="form-control activation handleCheckStatusValue" name="codigo" readonly>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <h5>Concepto:</h5>
                                            <input type="text" class="form-control activation handleCheckStatusValue"  placeholder="Concepto/motivo" name="concepto">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Fecha</h5>
                                            <input type="date" class="form-control activation handleCheckStatusValue" name="fecha_registro" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <h5>Periodo</h5>
                                            <select class="form-control activation handleCheckStatusValue" name="periodo">
                                                @foreach ($periodos as $periodo)
                                                <option value="{{$periodo->id_periodo}}">{{$periodo->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <h5>Moneda</h5>
                                            <select class="form-control activation handleCheckStatusValue handleChangeUpdateMoneda" name="moneda">
                                                @foreach ($monedas as $moneda)
                                                <option data-simbolo="{{$moneda->simbolo}}" value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <h5>Prioridad</h5>
                                            <select class="form-control activation handleCheckStatusValue" name="prioridad">
                                                @foreach ($prioridades as $prioridad)
                                                <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Comentario</h5>
                                            <textarea class="form-control activation handleCheckStatusValue" name="comentario" placeholder="Comentario/observación (opcional)" cols="100" rows="100" style="height:50px;"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Empresa</h5>
                                            <select class="form-control activation handleCheckStatusValue handleChangeOptEmpresa" name="empresa">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($empresas as $empresa)
                                                <option value="{{$empresa->id_empresa}}">{{ $empresa->razon_social}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Sede</h5>
                                            <select class="form-control activation handleCheckStatusValue" name="sede" disabled>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Grupo</h5>
                                            <select class="form-control activation handleCheckStatusValue handleChangeOptGrupo" name="grupo" disabled>
                                                <option value="0">Elija una opción</option>
                                                @foreach ($grupos as $grupo)
                                                <option value="{{$grupo->id_grupo}}">{{ $grupo->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>División</h5>
                                            <select class="form-control activation handleCheckStatusValue" name="division" disabled>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 oculto" id="contenedor-proyecto">
                                        <div class="form-group">
                                            <h5>Proyecto</h5>
                                            <select class="form-control activation handleCheckStatusValue" name="proyecto">
                                            <option value="0">Seleccione un Proyecto</option>
                                            @foreach ($proyectos_activos as $proyecto)
                                                <option value="{{$proyecto->id_proyecto}}" data-id-centro-costo="{{$proyecto->id_centro_costo}}" data-codigo-centro-costo="{{$proyecto->codigo_centro_costo}}" data-descripcion-centro-costo="{{$proyecto->descripcion_centro_costo}}" data-codigo="{{$proyecto->codigo}}">{{$proyecto->descripcion}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 oculto" id="contenedor-cdp">
                                        <div class="form-group">
                                            <h5>CDP</h5>
                                            <div style="display:flex;">
                                                <input type="text" class="form-control oculto" name="id_cc">
                                                <input type="text" class="form-control" name="codigo_oportunidad" readonly>

                                                <button type="button" class="btn-primary handleClickModalListaCuadroDePresupuesto" title="Buscar cuadro de presupuesto" placeholder="Código CDP" name="btnSearchCDP"">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Tipo cuenta</h5>
                                            <div style="display:flex;">
                                                <select class="form-control activation handleCheckStatusValue" name="tipo_cuenta">
                                                    <option value="bcp">BCP</option>
                                                    <option value="cci">CCI</option>
                                                </select>
                                                <input type="text" class="form-control activation handleCheckStatusValue" placeholder="Nro de cuenta" name="nro_cuenta">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Documento</h5>
                                            <div style="display:flex;">
                                                <select class="form-control activation handleCheckStatusValue" name="tipo_documento_idendidad">
                                                    <option value="dni">DNI</option>
                                                    <option value="ruc">RUC</option>
                                                </select>
                                                <input type="text" class="form-control activation handleCheckStatusValue" placeholder="Nro de documento" name="nro_documento_idendidad">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <h5>Monto Total:</h5>
                                            <div style="display:flex;">
                                                <div class="input-group-addon" name="montoMoneda" style="width: auto;">S/.</div>
                                                <input type="text" class="form-control oculto" name="monto_total">
                                                <input type="text" class="form-control activation handleCheckStatusValue" name="monto_total_read_only" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-table">
                                <div class="btn-group" role="group" aria-label="...">
                                    <!-- <button type="button" class="btn btn-xs btn-success activation handleCheckStatusValue handleClickAgregarProducto" id="btnAddProducto" data-toggle="tooltip" data-placement="bottom" title="Agregar Producto"><i class="fas fa-plus"></i> Producto
                                    </button> -->
                                    <button type="button" class="btn btn-xs btn-primary activation handleCheckStatusValue handleClickAgregarServicio" id="btnAddServicio" data-toggle="tooltip" data-placement="bottom" title="Agregar Servicio"><i class="fas fa-plus"></i> Servicio
                                    </button>
                                </div>
                                <table class="table table-striped table-condensed table-bordered" id="ListaDetalleRequerimientoPago" width="100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%">Partida</th>
                                            <th style="width: 10%">C.Costo</th>
                                            <th style="width: 10%">Part number</th>
                                            <th>Descripción de item</th>
                                            <th style="width: 10%">Unidad</th>
                                            <th style="width: 6%">Cantidad</th>
                                            <th style="width: 8%">Precio Unit.<span name="simboloMoneda">S/</span> <em>(Sin IGV)</em></th>
                                            <th style="width: 6%">Subtotal</th>
                                            <th style="width: 7%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_detalle_requerimiento_pago">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7" class="text-right"><strong>Total:</strong></td>
                                            <td class="text-right"><span name="simboloMoneda">S/</span><label name="total"> 0.00</label></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </fieldset>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" class="close" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-sm btn-success handleClickGuardarRequerimientoPago" id="btnGuardarRequerimientoPago" disabled>Guardar</button>
                    <button type="button" class="btn btn-sm btn-success handleClickRequerimientoPago oculto" id="btnActualizarRequerimientoPago" disabled>Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>