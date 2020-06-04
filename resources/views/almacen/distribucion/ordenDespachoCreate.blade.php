<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_create" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 1000px;">
        <div class="modal-content">
            <form id="form-orden_despacho">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Generar Orden de Despacho</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_orden_despacho">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Cliente</h5>
                            <input type="text" class="form-control activation" name="cliente_razon_social">
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Despacho</h5>
                            <input type="date" class="form-control activation" name="fecha_despacho" value="<?=date('Y-m-d');?>">
                        </div>
                        <div class="col-md-3">
                            <h5>Última Fecha de Entrega</h5>
                            <input type="date" class="form-control activation" name="fecha_entrega" value="<?=date('Y-m-d');?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Dirección Destino</h5>
                            <input type="text" class="form-control activation" name="direccion_destino">
                        </div>
                        <div class="col-md-3">
                            <h5>Ubigeo Destino</h5>
                            <input type="text" class="form-control activation" name="ubigeo_destino">
                        </div>
                        <div class="col-md-3">
                            <h5>Tipo de Entrega</h5>
                            <select class="form-control activation js-example-basic-single" name="tipo_entrega">
                                <option value="MISMA CIUDAD">MISMA CIUDAD</option>
                                <option value="OTRAS CIUDADES">OTRAS CIUDADES</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5></h5>
                            <input type="checkbox" name="aplica_cambios" style="margin-right: 10px; margin-left: 7px;"/> Aplica Cambios
                        </div>
                    </div>
                </div>
                <div id="detalleItemsReq">
                    <div class="modal-header" style="display:flex;">
                        <h4 class="modal-title green"><i class="fas fa-arrow-circle-right green"></i> Ingresa: </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="checkbox" style="margin-right: 10px; margin-left: 7px;"/> Seleccione todos los items
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                    id="detalleRequerimientoOD"  style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th width="40%">Descripción</th>
                                            <th>Cantidad</th>
                                            <th>Unid</th>
                                            <th>Lugar de Entrega</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                            <!-- <th>Total</th> -->
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-header" style="display:flex;">
                        <h4 class="modal-title red"><i class="fas fa-arrow-circle-left red"></i> Sale: </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <textarea name="sale" id="sale" cols="137" rows="5"></textarea>
                                <!-- <input type="checkbox" style="margin-right: 10px; margin-left: 7px;"/> Seleccione todos los items -->
                                <!-- <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                    id="detalleRequerimientoOD"  style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th width="40%">Descripción</th>
                                            <th>Cantidad</th>
                                            <th>Unid</th>
                                            <th>Lugar de Entrega</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_orden_despacho" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>