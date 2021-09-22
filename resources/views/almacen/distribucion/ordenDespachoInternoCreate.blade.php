<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_interno_create" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 80%">
        <div class="modal-content">
            <form id="form-orden_despacho">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Generar Orden de Transformación</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <input type="text" class="oculto" name="id_sede" />
                    <input type="text" class="oculto" name="id_cc" />
                    <input type="text" class="oculto" name="tiene_transformacion" />
                    <input type="date" class="oculto" name="fecha_entrega" />
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén</h5>
                            <input type="text" class="oculto" name="id_almacen">
                            <input type="text" class="form-control" name="almacen_descripcion" readOnly>
                        </div>
                        <div class="col-md-8">
                            <h5>Observación</h5>
                            <input type="text" class="form-control" name="descripcion_sobrantes" />
                        </div>
                    </div>
                </div>
                <div>
                    <!-- <div class="modal-header" style="display:flex;padding-top: 0px;">
                        <h4 class="modal-title blue"><i class="fas fa-arrow-circle-right blue"></i> Instrucciones Generales: </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="descripcion_sobrantes" />
                                <textarea class="form-control" name="descripcion_sobrantes" id="descripcion_sobrantes" rows="2"></textarea>
                            </div>
                        </div>
                    </div> -->

                    <div class="modal-header" style="display:flex;padding-top: 0px;">
                        <h4 class="modal-title green"><i class="fas fa-arrow-alt-circle-down green"></i> Productos base: </h4>
                    </div>

                    <div class="modal-body" style="padding-bottom:0px;">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- <input type="checkbox" name="seleccionar_todos" style="margin-right: 10px; margin-left: 7px;" /> Seleccione todos los items -->
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleRequerimientoOD" style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Código</th>
                                            <th>PartNumber</th>
                                            <th>Descripción</th>
                                            <th>Cant.</th>
                                            <th>Unid</th>
                                            <th>Reservado</th>
                                            <th>Despachado</th>
                                            <th>Cant.Despacho</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="modal-header" style="display:flex;padding-top: 0px;">
                        <h4 class="modal-title red"><i class="fas fa-arrow-alt-circle-up red"></i> Productos finales: </h4>
                    </div>
                    <div class="modal-body" style="padding-top:0px;padding-bottom:0px;">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleSale" style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>PartNumber</th>
                                            <th>Descripción</th>
                                            <th>Cant.</th>
                                            <th>Unid</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <!-- <textarea name="sale" id="sale" cols="137" rows="5"></textarea> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button class="btn btn-sm btn-success" id="submit_orden_despacho" onClick="guardar_orden_despacho();" >Guardar y Enviar <i class="fas fa-paper-plane"></i> </button> -->
                    <!-- &nbsp;<img width="10px" src="{{ asset('images/loading.gif')}}" class="loading invisible"><img> -->
                    <input type="submit" id="submit_orden_despacho" class="btn btn-success" value="Guardar" />
                </div>
            </form>
        </div>
    </div>
</div>