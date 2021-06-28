<div class="modal fade" tabindex="-1" role="dialog" id="modal-requerimiento">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalle del requerimiento</h3>
            </div>
            <div class="modal-body">
                <fieldset class="group-importes">
                    <legend>Datos generales</legend>
                    <table class="table" border="0" id="tablaDatosGenerales">
                        <tbody>
                            <tr>
                                <td style="width:5%; font-weight:bold; text-align:right;">Código</td>
                                <td id="codigo" style="width:10%;"></td>
                                <td style="width:5%; font-weight:bold; text-align:right;">Motivo</td>
                                <td id="concepto" style="width:auto;" colspan="2"></td>
                                <td></td>
                                <td style="width:5%; font-weight:bold; text-align:right;">Empresa</td>
                                <td id="razon_social_empresa" style="width:20%;"></td>
                            </tr>
                            <tr>
                                <td style="width:5%; font-weight:bold; text-align:right;">División</td>
                                <td id="division" style="width:10%;"></td>
                                <td style="width:5%; font-weight:bold; text-align:right;">Prioridad</td>
                                <td id="prioridad" style="width:10%;"></td>
                                <td style="width:14%; font-weight:bold; text-align:right;">Fecha Entrega</td>
                                <td id="fecha_entrega" style="width:10%;"></td>
                                <td style="width:5%; font-weight:bold; text-align:right;">Solicitado por</td>
                                <td id="solicitado_por" style="width:15%;"></td>
                                <!--Elmer Figueroa Arce -->
                            </tr>
                            <tr>
                                <td style="width:5%; font-weight:bold; text-align:right;">Periodo</td>
                                <td id="periodo" style="width:5%;"></td>
                                <td style="width:10%; font-weight:bold; text-align:right;">Creado por</td>
                                <td id="creado_por" style="width:18%;"></td>
                                <td style="width:10%; font-weight:bold; text-align:right;">Archivos adjuntos</td>
                                <td>    
                                    <a title="Ver archivos adjuntos de requerimiento" style="cursor:pointer;" onClick="aprobarRequerimiento.verAdjuntosRequerimiento();" >
                                    Ver adjuntos: <span name="cantidadAdjuntosRequerimiento">0</span>
                                    </a>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="width:5%; font-weight:bold; text-align:right;">Observación</td>
                                <td id="observacion" style="width:95%;" colspan="7"></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>

                <br>
                <fieldset class="group-importes">
                    <legend>
                        Items de requerimiento
                    </legend>
                    <table class="table table-striped table-condensed table-bordered" id="listaDetalleRequerimientoModal">
                        <thead>
                            <tr>
                                <th style="width: 2%">#</th>
                                <th style="width: 10%">Partida</th>
                                <th style="width: 10%">C.Costo</th>
                                <th style="width: 5%">Part number</th>
                                <th style="width: 30%">Descripción de item</th>
                                <th style="width: 5%">Unidad</th>
                                <th style="width: 5%">Cantidad</th>
                                <th style="width: 5%">Precio U. <span name="simboloMoneda">S/</span></th>
                                <th style="width: 5%">Subtotal</th>
                                <th style="width: 20%">Motivo</th>
                                <th style="width: 2%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="body_item_requerimiento">
                        </tbody>
                    </table>

                </fieldset>

                <fieldset class="group-importes">
                    <legend style="background:#b3a705;">Historial de revisiones</legend>
                    <br>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Revisado por</th>
                                <th>Acción</th>
                                <th>Comentario</th>
                                <th>Fecha revisión</th>
                            </tr>
                        </thead>
                        <tbody id="body_historial_revision"></tbody>
                    </table>
                </fieldset>

                <fieldset class="group-importes">
                    <legend>Revisar</legend>
                    <br>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-5 control-label">Acción a realizar</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="accion">
                                    <option value="APROBAR">Aprobar Requerimiento</option>
                                    <option value="RECHAZAR">Rechazar Requerimiento</option>
                                    <option value="OBSERVAR">Observar Requerimiento</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-5 control-label">Comentarios</label>
                            <div class="col-sm-4">
                                <textarea class="form-control" id="comentario"></textarea>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onClick="aprobarRequerimiento.registrarRespuesta();" >Registrar respuesta</button>
                <button class="btn btn-danger" class="close" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>