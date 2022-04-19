<div class="modal fade" tabindex="-1" role="dialog" id="modal-salidaAlmacen" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width:85%;">
        <div class="modal-content">
            <form id="form-salidaAlmacen">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Salida de almacén <span class="blue" title="Abrir Salida" id="codigo_salida"></span>
                        - Guía venta: <span class="red" id="guia_ven"></span></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" name="id_guia_ven" class="oculto" />
                            <input type="text" name="id_mov_alm" class="oculto" />
                            
                            <fieldset class="group-table" id="fieldsetDatosCabecera">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Datos del documento</strong></h5>

                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Serie-Número</h5>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="salida_serie" onBlur="salida_ceros_numero('serie');" placeholder="0000" required>
                                            <span class="input-group-addon">-</span>
                                            <input type="text" class="form-control" name="salida_numero" onBlur="salida_ceros_numero('numero');" placeholder="0000000" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Cliente</h5>
                                        <input type="text" class="form-control" id="cliente_razon_social" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Fecha de Emisión Guía</h5>
                                        <input type="date" class="form-control" name="salida_fecha_emision"  required>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Fecha de Salida</h5>
                                        <input type="date" class="form-control" name="salida_fecha_almacen"  required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Almacén</h5>
                                        <input type="text" class="form-control" id="almacen_descripcion" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Tipo de Operación</h5>
                                        <input type="text" class="form-control" id="operacion_descripcion" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Punto de partida</h5>
                                        <input type="text" class="form-control" name="salida_punto_partida" required>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Punto de llegada</h5>
                                        <input type="text" class="form-control" name="salida_punto_llegada" required>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-md-3">
                                        <label>Registrado por:</label>
                                        <span id="responsable_nombre"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Fecha registro:</label>
                                        <span id="fecha_registro"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Orden de despacho:</label>
                                        <span id="orden_despacho"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Requerimiento:</label>
                                        <span id="requerimientos"></span>
                                    </div>
                                </div>
                            </fieldset>
                            <br>
                            <fieldset class="group-table" id="fieldsetDatosDetalle">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Items del documento</strong></h5>

                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleMovimiento" style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>PartNumber</th>
                                            <th>Descripción</th>
                                            <th>Cant.</th>
                                            <th>Unid</th>
                                            <th width="80px">Series</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Comentario</h5>
                                        <textarea class="form-control" name="salida_comentario"></textarea>
                                    </div>
                                </div>
                            </fieldset>
                            <br>
                            <fieldset class="group-table" id="fieldsetDatosAuditoria">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Datos de Auditoría</strong></h5>

                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Motivo de la actualización:</h5>
                                        <select class="form-control activation js-example-basic-single" name="id_motivo_cambio" required>
                                            <option value="">Elija una opción</option>
                                            @foreach ($motivos_anu as $mot)
                                            <option value="{{$mot->id_motivo}}">{{$mot->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Observación:</h5>
                                        <input type="text" class="form-control" name="observacion"  required>
                                    </div>
                                </div>
                            </fieldset>
                                
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_salidaAlmacen" class="btn btn-success" value="Actualizar" />
                </div>
            </form>
        </div>
    </div>
</div>