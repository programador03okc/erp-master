<div class="modal fade" tabindex="-1" role="dialog" id="modal-tracking-requerimiento">
    <div class="modal-dialog" style="width: 84%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Explorar Requerimiento - 
                    <strong>RQ</strong> 
                    <!-- <span class="badge label-primary">EN VALORIZACIÓN</span> -->
                </h3> 
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
                                @include('logistica.requerimientos.sections_tracking.historial_aprobacion')

                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <div class="row">
                                <div class="col-xs-12 col-md-8">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseOne">
                                            <strong>Solicitud de Cotización</strong> <span class="badge" id="cantidad_cotizaciones">0</span>
                                        </a>
                                    </h4>
                                </div>
                                <div class="col-xs-6 col-md-4 text-right"></div>
                            </div>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="panel-body">
                                @include('logistica.requerimientos.sections_tracking.cotizaciones')
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                            <div class="row">
                                <div class="col-xs-12 col-md-8">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseOne">
                                            <strong>Cuadros Comparativos</strong> <span class="badge" id="cantidad_cuadros_comparativos">0</span>
                                        </a>
                                    </h4>
                                </div>
                                <div class="col-xs-6 col-md-4 text-right"></div>
                            </div>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                            <div class="panel-body">
                                @include('logistica.requerimientos.sections_tracking.cuadros_comparativos')
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingFour">
                            <div class="row">
                                <div class="col-xs-12 col-md-8">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseOne">
                                            <strong>Ordenes</strong> <span class="badge" id="cantidad_ordenes">0</span>
                                        </a>
                                    </h4>
                                </div>
                                <div class="col-xs-6 col-md-4 text-right"></div>
                            </div>
                        </div>
                        <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                            <div class="panel-body">
                                @include('logistica.requerimientos.sections_tracking.ordenes')

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
    
</div>

@include('logistica.requerimientos.sections_tracking.modal_detalle_cotizacion')

