<div class="modal fade" tabindex="-1" role="dialog" id="modal-vista-rapida-requerimiento-pago" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalle del requerimiento pago</h3>
            </div>
            <div class="modal-body">
                <div id="botonera-accion"></div>
                <input type="hidden" name="id_requerimiento_pago">
                <input type="hidden" name="id_estado">
                <input type="hidden" name="id_usuario">
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
                                            <td style="width:5%; font-weight:bold; text-align:right;">Grupo/División</td>
                                            <td id="grupo_division" style="width:10%;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Periodo</td>
                                            <td id="periodo" style="width:5%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Tipo Req.</td>
                                            <td id="tipo_requerimiento" style="width:5%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Prioridad</td>
                                            <td id="prioridad" style="width:10%;"></td>
                                            <td style="width:14%; font-weight:bold; text-align:right;">Fecha Registro</td>
                                            <td id="fecha_registro" style="width:10%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Creado por</td>
                                            <td id="creado_por" style="width:10%;"></td>

                                        </tr>
                                        <tr>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Solicitado por</td>
                                            <td id="solicitado_por" style="width:10%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Comentario</td>
                                            <td id="comentario" style="width:5%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Tipo Impuesto</td>
                                            <td id="tipo_impuesto" style="width:5%;"></td>
                                            <td style="width:10%; font-weight:bold; text-align:right;">Archivos adjuntos</td>
                                            <td id='adjuntosRequerimientoPago'>-</td>
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
                                            <td id="proyecto_presupuesto" style="width:5%;" colspan="6"></td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="group-importes">
                    <legend>Datos destinatario de pago</legend>
                    <div class="box box-widget">
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table" border="0" id="tablaDatosDestinatario">
                                    <tbody>
                                        <tr>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Tipo Destinatario</td>
                                            <td id="tipo_destinatario" style="width:5%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Nombre destinatario</td>
                                            <td id="destinatario" style="width:10%;"></td>
                                            <td id="tipo_documento_destinatario" style="width:5%; font-weight:bold; text-align:right;">Doc.</td>
                                            <td id="nro_documento_destinatario" style="width:5%;"></td>
                                            <td style="width:10%; font-weight:bold; text-align:right;">Banco</td>
                                            <td id="banco" style="width:10%;"></td>


                                        </tr>
                                        <tr>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Tipo Cuenta</td>
                                            <td id='tipo_cuenta' style="width:5%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Moneda</td>
                                            <td id="moneda" style="width:5%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">Nro Cuenta</td>
                                            <td id="nro_cuenta" style="width:5%;"></td>
                                            <td style="width:5%; font-weight:bold; text-align:right;">CCI</td>
                                            <td id="nro_cci" style="width:5%;"></td>
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
                        Items de requerimiento de pago
                    </legend>
                    <div class="box box-widget">
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-condensed table-bordered" id="listaDetalleRequerimientoPago">
                                    <thead>
                                        <tr>
                                            <th style="width: 2%">#</th>
                                            <th style="width: 10%">Partida</th>
                                            <th style="width: 10%">C.Costo</th>
                                            <th style="width: 30%">Descripción de item</th>
                                            <th style="width: 5%">Unidad</th>
                                            <th style="width: 5%">Cantidad</th>
                                            <th style="width: 8%">Precio U. <span name="simboloMoneda">S/</span></th>
                                            <th style="width: 8%">Subtotal</th>
                                            <th style="width: 10%">Motivo</th>
                                            <th style="width: 10%">Estado</th>
                                            <th style="width: 2%">Adjuntos</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_requerimiento_pago_detalle">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7" class="text-right"><strong>Monto total:</strong></td>
                                            <td class="text-right"><span name="simbolo_moneda">S/</span><label name="total"> 0.00</label></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </fieldset>

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
                                    <tbody id="body_requerimiento_pago_historial_revision"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </fieldset>

            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" class="close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>