 <div class="modal fade" tabindex="-1" role="dialog" id="modal-tracking_orden">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <form id="form-tracking_orden">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Explorar Orden</h3>
                    <strong>OC/OS</strong> 
                 </div>
                <div class="modal-body">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingOne">
                                <div class="row">
                                    <div class="col-xs-12 col-md-8">
                                        <h4 class="panel-title">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                <strong>Historial de Aprobaciones</strong>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="col-xs-6 col-md-4 text-right"></div>
                                </div>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                <div class="panel-body">
                                    @include('logistica.ordenes.sections_tracking.historial_aprobacion')

                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <div class="row">
                                    <div class="col-xs-12 col-md-8">
                                        <h4 class="panel-title">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseOne">
                                                <strong>Registro de Pago</strong> <span class="badge" id="cantidad_cotizaciones">0</span>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="col-xs-6 col-md-4 text-right"></div>
                                </div>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    @include('logistica.ordenes.sections_tracking.registro_pago')
                                </div>
                            </div>
                        </div>
 
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingThree">
                                <div class="row">
                                    <div class="col-xs-12 col-md-8">
                                        <h4 class="panel-title">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseOne">
                                                <strong>Entrada a almacen</strong> <span class="badge" id="cantidad_item_entrantes">0</span>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="col-xs-6 col-md-4 text-right"></div>
                                </div>
                            </div>
                            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                <div class="panel-body">
                                    @include('logistica.ordenes.sections_tracking.entradas_almacen')
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingFour">
                                <div class="row">
                                    <div class="col-xs-12 col-md-8">
                                        <h4 class="panel-title">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseOne">
                                                <strong>Despachado</strong> <span class="badge" id="cantidad_despachados">0</span>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="col-xs-6 col-md-4 text-right"></div>
                                </div>
                            </div>
                            <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                                <div class="panel-body">
                                    @include('logistica.ordenes.sections_tracking.despachos')
                                </div>
                            </div>
                        </div>
 
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>