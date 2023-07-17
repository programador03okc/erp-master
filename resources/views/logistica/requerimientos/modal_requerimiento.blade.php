<div class="modal fade" tabindex="-1" role="dialog" id="modal-requerimiento" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalle del requerimiento de B/S</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_requerimiento">
                <fieldset class="group-importes">
                    <legend>Datos generales</legend>
                    <div class="box box-widget">
                        <div class="box-body">
                            <div class="table-responsive">
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
                                            <td style="width:5%; font-weight:bold; text-align:right;">Tipo Req.</td>
                                            <td id="tipo_requerimiento" style="width:5%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Periodo</td>
                                            <td id="periodo" style="width:5%;"></td>
                                            <td style="width:10%; font-weight:bold; text-align:right;">Creado por</td>
                                            <td id="creado_por" style="width:18%;"></td>
                                            <td style="width:10%; font-weight:bold; text-align:right;">Archivos adjuntos</td>
                                            <td id='adjuntosRequerimiento'>-</td>
                                            <td></td>
                                        </tr>
                                        <tr class="oculto" id="contenedor_presupuesto_old">
                                            <td style="width:5%; font-weight:bold; text-align:right;">Presupuesto <em>(Proy)</em></td>
                                            <td id="presupuesto_old" style="width:5%;" colspan="7"></td>
                                        </tr>
                                        <tr class="oculto" id="contenedor_presupuesto_interno">
                                            <td style="width:5%; font-weight:bold; text-align:right;">Presupuesto <em>(Interno)</em></td>
                                            <td id="presupuesto_interno" style="width:5%;" colspan="7"></td>
                                        </tr>
                                        <tr class="oculto" id="contenedor_cdp">
                                            <td style="width:5%; font-weight:bold; text-align:right;">CDP</td>
                                            <td id="codigo_cdp" style="width:5%;"></td>
                                        </tr>
                                        <tr class="oculto" id="contenedor_proyecto">
                                            <td style="width:5%; font-weight:bold; text-align:right;">Proyecto</td>
                                            <td id="proyecto_presupuesto" style="width:5%;" colspan="7"></td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Tipo Impuesto</td>
                                            <td id="tipo_impuesto" style="width:10%;" colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Observación</td>
                                            <td id="observacion" style="width:70%;" colspan="5"></td>
                                        </tr>
                                        <tr class="oculto" id="contenedor_incidencia">
                                            <td style="width:5%; font-weight:bold; text-align:right;">Incidencia</td>
                                            <td id="incidencia" style="width:5%;" colspan="7"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <br>
                <fieldset class="group-importes">
                    <legend>
                        Items de requerimiento
                    </legend>
                    <div class="box box-widget">
                        <div class="box-body">
                            <div class="table-responsive">
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
                                            <th style="width: 8%">Precio U. <span name="simboloMoneda">S/</span></th>
                                            <th style="width: 8%">Subtotal</th>
                                            <th style="width: 20%">Motivo</th>
                                            <th style="width: 10%">Estado</th>
                                            <th style="width: 2%">Adjuntos</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_item_requerimiento">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8" class="text-right"><strong>Monto neto:</strong></td>
                                            <td class="text-right"><span name="simbolo_moneda">S/</span><label name="monto_subtotal"> 0.00</label></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="text-right"><strong>IGV 18%:</strong></td>
                                            <td class="text-right"><span name="simbolo_moneda"></span><label name="monto_igv"> 0.00</label></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="text-right"><strong>Monto Total:</strong></td>
                                            <td class="text-right"><span name="simbolo_moneda"></span><label name="monto_total"> 0.00</label></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-md-6">
                        <fieldset class="group-importes">
                            <legend style="background:#b3a705;">Historial de revisiones/aprobaciones</legend>
                            <br>
                            <div class="box box-widget">
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="listaHistorialRevision">
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
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                    <fieldset class="group-importes">
                            <legend style="background:#b3a705;">Flujo de aprobación</legend>
                            <br>
                            <div class="box box-widget">
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="listaFlujoAprobacion">
                                            <thead>
                                                <tr>
                                                    <th>Secuencia <i class="fas fa-question-circle" title="Si la secuancia es repetida, implica que en ese orden caulquiere de esos usuarios puede intervenir." style="cursor:help;"></i></th>
                                                    <th>Rol</th>
                                                    <th>Usuarios</th>
                                                    <th>Aprobación Salta el flujo? <i class="fas fa-question-circle" title="De ser 'SI' implica que el usuario puede aprobar en cualquier momento el documento saltandose el flujo." style="cursor:help;"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_flujo_aprobacion"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <fieldset class="group-importes" id="group-acciones">
                    <legend>Revisar</legend>
                    <br>
                    <div class="form-horizontal">
                        <input type="hidden" name="idRequerimiento">
                        <input type="hidden" name="idDocumento">
                        <input type="hidden" name="idUsuario">
                        <input type="hidden" name="idRolAprobante">
                        <input type="hidden" name="idFlujo">
                        <input type="hidden" name="idOperacion">
                        <input type="hidden" name="aprobacionFinalOPendiente">
                        <input type="hidden" name="tieneRolConSiguienteAprobacion">
                        <div class="form-group">
                            <label class="col-sm-5 control-label">Acción a realizar</label>
                            <div class="col-sm-4">
                                <select class="form-control handleChangeUpdateAccion" id="accion">
                                    <option value="0">Seleccione una acción</option>
                                    <option value="1">Aprobar Requerimiento</option>
                                    <option value="2">Rechazar Requerimiento</option>
                                    <option value="3">Observar Requerimiento</option>
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
                <button class="btn btn-primary" class="close" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-success handleClickRegistrarRespuesta" id="btnRegistrarRespuesta">Registrar respuesta</button>
            </div>
        </div>
    </div>
</div>