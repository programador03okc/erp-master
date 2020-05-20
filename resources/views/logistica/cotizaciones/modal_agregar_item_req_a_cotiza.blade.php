<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-item-req-a-cotiza">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Agregar Item de Requerimiento a la Cotización</h3>            
            </div>
            <div class="modal-body">
                <form  id="form-seleccionar_requerimiento">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills nav-justified" role="tablist" id="menu_tab_crear_coti_agregar">
                        <li role="presentation" class="active"><a href="#requerimiento" aria-controls="requerimiento" role="tab" data-toggle="tab">1. Selección de Requerimientos</a></li>
                        <li role="presentation" class="disabled"><a href="#detalle_requerimiento" aria-controls="detalle_requerimiento" role="tab" data-toggle="tab">2. Selección de Items</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="contenido_tab_crear_coti_agregar">
                        <div role="tabpanel" class="tab-pane active" id="requerimiento">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h5>Buscar y Seleccionar Requerimiento(s)</h5>
 
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                    id="listaRequerimientoPendientesAgregar">
                                        <thead>
                                            <tr>
                                                <th hidden>Id</th>
                                                <th>Check</th>
                                                <th>Código</th>
                                                <th>Concepto</th>
                                                <th>Area</th>
                                                <th>Estado</th>
                                                <th>Cotización</th>
                                                <th>Fecha</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-md-12 right">
                                        <button class="btn btn-info" role="button"   id="btnAllowCheckBoxListReqAgregar" onClick="allowCheckBoxListReqAgregar(event);">
                                            Volver a Iniciar <i class="fas fa-undo-alt"></i>
                                        </button>
                                        <button class="btn btn-warning" role="button"   id="btnGotToSecondTabAgregar" onClick="gotToSecondTabAgregar(event);" disabled>
                                            Siguiente <i class="fas fa-chevron-circle-right"></i>
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="detalle_requerimiento">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h5>Seleccionar Items</h5>
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaItemsRequerimientoAgregar" width="100%"> 
                                        <thead>
                                            <tr>
                                                <th hidden>Id</th>
                                                <th width="20">Check</th>
                                                <th width="20">#</th>
                                                <th width="120">COD.REQ.</th>
                                                <th width="120">COD. ITEM</th>
                                                <th width="400">DESCRIPCIÓN</th>
                                                <th width="100">UNIDAD</th>
                                                <th width="100">CANTIDAD</th>
                                                <th width="100">PRECIO REF.</th>
                                                <th width="100">FECHA ENTREGA</th>
                                                <th width="100">LUGAR ENTREGA</th>
                                                <th width="100">ACTUALIZAR CANTIDAD</th>
                                                <th width="200">SALDOS</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>

                                    <div class="row">
                                        <div class="col-md-12 right">
                                        <button class="btn btn-default" role="button"   onClick="gotToSecondToFirstTabAgregar(event);">
                                                Atras <i class="fas fa-arrow-circle-left"></i>
                                        </button>
                                        <button class="btn btn-success" role="button" id='btnAddAllItemReqToCoti' onClick="addAllItemReqToCoti(event);" disabled>
                                            Agregar <i class="fas fa-check"></i>
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>