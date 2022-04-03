<div class="modal fade" tabindex="-1" role="dialog" id="modal-movAlmDetalle" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1200px;">
        <div class="modal-content">
            <form id="form-ingreso">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Ingreso a almacén <label class="lbl-codigo" title="Abrir Ingreso" onClick="abrirIngreso()" id="cabecera"></label>
                        - Guía: <span class="red" id="guia_com"></span></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" name="id_guia_com" class="oculto" />
                            <input type="text" name="id_mov_alm" class="oculto" />
                            
                            <fieldset class="group-table" id="fieldsetDatosCabecera">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Datos del documento</strong></h5>
                                        
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Serie-Número</h5>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="ingreso_serie" onBlur="ingreso_ceros_numero('serie');" placeholder="0000" required>
                                            <span class="input-group-addon">-</span>
                                            <input type="text" class="form-control" name="ingreso_numero" onBlur="ingreso_ceros_numero('numero');" placeholder="0000000" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Proveedor</h5>
                                        <input type="text" class="form-control" id="prov_razon_social" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Fecha de Emisión Guía</h5>
                                        <input type="date" class="form-control" name="ingreso_fecha_emision"  required>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Fecha de Ingreso</h5>
                                        <input type="date" class="form-control" name="ingreso_fecha_almacen"  required>
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
                                        <h5>Ordenes Compra</h5>
                                        <input type="text" class="form-control" id="ordenes_compra" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Requerimientos</h5>
                                        <input type="text" class="form-control" id="requerimientos" disabled>
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
                                </div>
                            </fieldset>
                            <br>
                            <fieldset class="group-table" id="fieldsetDatosDetalle">
                                <h5 style="display:flex;justify-content: space-between;"><strong>Items del documento</strong></h5>

                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleMovimientoIngreso" style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>PartNumber</th>
                                            <th>Descripción</th>
                                            <th>Cant.</th>
                                            <th>Unid</th>
                                            <th>Guía Compra</th>
                                            <th>OC/HT/Tr</th>
                                            <th>Requerimiento</th>
                                            <th>Sede Req.</th>
                                            <th width="80px">Series</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
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
                    <input type="submit" id="submit_ingreso" class="btn btn-success" value="Actualizar" />
                </div>
            </form>
        </div>
    </div>
</div>