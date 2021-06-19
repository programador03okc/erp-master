<div class="modal fade" tabindex="-1" role="dialog" id="modal-requerimiento">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalle del requerimiento</h3>
            </div>
            <div class="modal-body">
                <fieldset class="group-importes">
                    <legend>Datos Generales</legend>
                    <table class="table" border="0">
                        <tbody>
                            <tr>
                                <td style="width:5%; font-weight:bold; text-align:right;">Código</td>
                                <td style="width:10%;">RC-210004</td>
                                <td style="width:5%; font-weight:bold; text-align:right;">Motivo</td>
                                <td style="width:auto;" colspan="2">COMPRA DE EQUIPOS PARA STOCK ALMACËN ILO VILLA DEL MAR</td>
                                <td></td>
                                <td style="width:5%; font-weight:bold; text-align:right;">Empresa</td>
                                <td style="width:20%;">OK COMPUTER E.I.R.L. - ILO</td>
                            </tr>
                            <tr>
                                <td style="width:5%; font-weight:bold; text-align:right;">División</td>
                                <td style="width:10%;">UCORP</td>
                                <td style="width:5%; font-weight:bold; text-align:right;">Prioridad</td>
                                <td style="width:10%;">Normal</td>
                                <td style="width:14%; font-weight:bold; text-align:right;">Fecha Entrega</td>
                                <td style="width:10%;">02/06/2021</td>
                                <td style="width:5%; font-weight:bold; text-align:right;">Solicitado por</td>
                                <td style="width:15%;">Stock para almacén</td>
                                <!--Elmer Figueroa Arce -->
                            </tr>
                            <tr>
                                <td style="width:5%; font-weight:bold; text-align:right;">Periodo</td>
                                <td style="width:5%;">2021</td>
                                <td style="width:10%; font-weight:bold; text-align:right;">Creado por</td>
                                <td style="width:18%;">Rosmery Tatiana Ventura Huacho</td>
                                <td style="width:10%; font-weight:bold; text-align:right;">Archivos adjuntos</td>
                                <td></td>
                                <td></td>
                                <td></td>



                            </tr>
                        </tbody>
                    </table>
                </fieldset>

                <br>
                <fieldset class="group-importes">
                    <legend>
                        Items de Requerimiento
                    </legend>
                    <table class="table table-striped table-condensed table-bordered" id="listaDetalleRequerimiento">
                        <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th style="width: 10%">Partida</th>
                                <th style="width: 10%">C.Costo</th>
                                <th style="width: 10%">Part number</th>
                                <th>Descripción de item</th>
                                <th style="width: 10%">Unidad</th>
                                <th style="width: 6%">Cantidad</th>
                                <th style="width: 8%">Precio U. <span name="simboloMoneda">S/</span></th>
                                <th style="width: 6%">Subtotal</th>
                                <th style="width: 15%">Motivo</th>
                                <th style="width: 7%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="body_archivos_item">
                            <tr>
                                <td>1</td>
                                <td>Repuestos</td>
                                <td>Terceros</td>
                                <td>TR405930</td>
                                <td>COMPUTADORA DE ESCRITORIO : PROCESADOR: INTEL CORE I7-10700 2.90 GHZ RAM: 8 GB DDR4 2933 366 MHZ</td>
                                <td>Unidad</td>
                                <td>1</td>
                                <td>S/1400.00</td>
                                <td>S/1400.00</td>
                                <td> Reparación de computadora</td>
                                <td style="text-align: center;"><button type="button" class="btn btn-sm btn-warning" title="Adjunto"><i class="fas fa-file-archive"></i></button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Repuestos</td>
                                <td>Terceros</td>
                                <td>4560i30</td>
                                <td>Procesador M1 </td>
                                <td>Unidad</td>
                                <td>1</td>
                                <td>S/4900.00</td>
                                <td>S/4900.00</td>
                                <td>Cambio de Procesador</td>
                                <td style="text-align: center;"><button type="button" class="btn btn-sm btn-warning" title="Adjunto"><i class="fas fa-file-archive"></i></button></td>
                            </tr>
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
                        <tbody>
                            <tr>
                                <td>Manuel Jesus Rivera Lujan</td>
                                <td style="color:forestgreen;">Aprobado</td>
                                <td>motivo justificado</td>
                                <td>15-06-2021</td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset class="group-importes">
                    <legend>Revisar</legend>
                    <br>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-5 control-label">Acción a realizar</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="" id="acciones">
                                    <option value="">Aprobar Requerimiento</option>
                                    <option value="">Rechazar Requerimiento</option>
                                    <option value="">Observar Requerimiento</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-5 control-label">Comentarios</label>
                            <div class="col-sm-4">
                                <textarea class="form-control" name=""></textarea>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" >Registrar respuesta</button>
                <button class="btn btn-danger" class="close" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>