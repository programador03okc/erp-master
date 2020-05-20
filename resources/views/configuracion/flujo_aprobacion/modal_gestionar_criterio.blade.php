<div class="modal fade" tabindex="-1" role="dialog" id="modal-gestionar_criterio">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Gestionar Criterio</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#asignar_monto" aria-controls="asignar_monto" role="tab" data-toggle="tab">Asignar Criterio</a></li>
                            <li role="presentation"><a href="#criterio_monto" aria-controls="criterio_monto" role="tab" data-toggle="tab">Criterio de Monto</a></li>
                            <li role="presentation"><a href="#criterio_prioridad" aria-controls="criterio_prioridad" role="tab" data-toggle="tab">Criterio de Prioridad</a></li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="asignar_monto">
                            <br>
                                <div class="row">
                                    <div class="col-md-3">
                                            <h5>Grupo Criterio</h5>
                                            <div style="display:flex">
                                            <select class="form-control activacion" name="grupo_criterio" onchange="cambiarGrupoCriterio(event);" disabled>
                                            </select>
                                            <button type="button" class="btn btn-sm btn-default" name="btnModalGrupoCriterio" onClick="modalGrupoCriterio();"><i class="far fa-edit"></i></button>
                                            </div>

                                    </div>
                                    <div class="col-md-3">
                                        <h5>Estado</h5>
                                        <select class="form-control activacion" name="estado_grupo_criterio" onchange="" disabled >
                                            <option value="0" selected >Elija una opción</option>
                                            <option value="1">Activado</option>
                                            <option value="7">Anulado</option>
                                        </select>         
                                    </div>

                                </div>
                                <br>
                                <div class="panel panel-default">
                                <div class="panel-heading">Criterio Asignado</div>
                                <input type="hidden" class="form-control" name="id_detalle_grupo_criterios" >

                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12 btn-group btn-group-sm">
                                                <button type="button" class="btn btn-sm btn-default" name="btnGuardarAsignarCriterio" onClick="guardarAsignarCriterio()">Guardar</button>
                                                <button type="button" class="btn btn-sm btn-default" name="btnNuevosAsignarCriterio" onClick="nuevoAsignarCriterio()">Nuevo</button>
                                                <button type="button" class="btn btn-sm btn-default" name="btnEditarAsignarCriterio" onClick="editarAsignarCriterio()">Editar</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h5>Lista de Criterios de Monto</h5>
                                                <select class="form-control activation" name="select_criterio_monto" onchange="" disabled >
                                                    <option value="0" selected >Elija una opción</option>
                                                </select> 
                                            </div>
                                            <div class="col-md-4">
                                                <h5>Lista de Criterios Prioridad</h5>
                                                <select class="form-control activation" name="select_criterio_prioridad" onchange="" disabled>
                                                    <option value="0" selected >Elija una opción</option>
                                                </select> 
                                            </div>
                                            <div class="col-md-4">
                                                <h5>Estado Criterio</h5>
                                                <select class="form-control activation" name="select_estado_detalle_grupo_criterio" onchange="" disabled>
                                                    <option value="0" selected >Elija una opción</option>
                                                    <option value="1">Activado</option>
                                                    <option value="7">Anulado</option>
                                                </select> 
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div role="tabpanel" class="tab-pane" id="criterio_monto">
                                <br>
                                <div class="row">
                                    <div class="col-md-8">
                                        <fieldset class="group-table">
                                        <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listarCriterioMonto">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Descripción</th>
                                                    <th>Operador 1</th>
                                                    <th>Nonto 1</th>
                                                    <th>Operador 2</th>
                                                    <th>Monto 2</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4">
                                        <form>
                                        
                                        <div class="row">
                                            <div class="col-md-12 btn-group btn-group-sm">
                                                <button type="button" class="btn btn-sm btn-default" name="btnGuardarCriterioMonto" onClick="guardarCriterioMonto()">Guardar</button>
                                                <button type="button" class="btn btn-sm btn-default" name="btnNuevosCriterioMonto" onClick="nuevoCriterioMonto()">Nuevo</button>
                                                <button type="button" class="btn btn-sm btn-default" name="btnEditarCriterioMonto" onClick="editarCriterioMonto()">Editar</button>
                                            </div>
                                        </div>
                                        <br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="descripcion_monto">Descripción</label>
                                                        <input type="hidden" class="form-control activation" name="id_criterio_monto">
                                                        <input type="text" class="form-control activation" name="descripcion_monto" placeholder="Descripción" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1" >Operador 1</label>
                                                            <select class="form-control activation" name="operador1" disabled>
                                                            <option value="0" selected >Elija una opción</option>
                                                            </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="monto1">Monto 1</label>
                                                        <input type="text" class="form-control activation" name="monto1" placeholder="Monto 1" disabled>
                                                    </div>  
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1" >Operador 2</label>
                                                            <select class="form-control activation" name="operador2" disabled>
                                                            <option value="0" selected >Elija una opción</option>
                                                            </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="monto1">Monto 2</label>
                                                        <input type="text" class="form-control activation" name="monto2" placeholder="Monto 2" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                <label for="monto1">Estado</label>
                                                <select class="form-control activation" name="estado_criterio" onchange="" disabled >
                                                    <option value="0" selected >Elija una opción</option>
                                                    <option value="1">Activado</option>
                                                    <option value="7">Anulado</option>
                                                </select>         
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="criterio_prioridad">
                            <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <fieldset class="group-table">
                                            <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listarCriterioPrioridad">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Descripción</th>
                                                        <th width="10">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </fieldset>
                                    </div>
                                        <div class="col-md-6">
                                            <form>
                                            <div class="row">
                                                <div class="col-md-12 btn-group btn-group-sm">
                                                <button type="button" class="btn btn-sm btn-default" name="btnGuardarCriterioPrioridad" onClick="guardarCriterioPrioridad()">Guardar</button>
                                                <button type="button" class="btn btn-sm btn-default" name="btnNuevosCriterioPrioridad" onClick="nuevoCriterioPrioridad()">Nuevo</button>
                                                <button type="button" class="btn btn-sm btn-default" name="btnEditarCriterioPrioridad" onClick="editarCriterioPrioridad()">Editar</button>
                                                </div>
                                            </div>
                                            <br>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="descripcion_prioridad">Descripción</label>
                                                            <input type="hidden" class="form-control" name="id_criterio_prioridad" >
                                                            <input type="text" class="form-control activation" name="descripcion_prioridad" placeholder="Descripción" disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                    <label>Estado</label>
                                                    <select class="form-control activation" name="estado_prioridad" onchange="" disabled >
                                                        <option value="0" selected >Elija una opción</option>
                                                        <option value="1">Activado</option>
                                                        <option value="7">Anulado</option>
                                                    </select>         
                                                    </div>
                                                </div>

                                            </form>
                                        
                                    </div>
                                </div>
                            </div>
                            </div>

                        </div>
                    </div>
                </div>



            </div>
            <div class="modal-footer">
                <label id="id_proveedor" style="display: none;"></label>
                <label id="id_contribuyente" style="display: none;"></label>
                <label id="razon_social" style="display: none;"></label>
                <!-- <button class="btn btn-sm btn-success" onClick="selectProveedor();">Aceptar</button> -->
            </div>
        </div>
    </div>
</div>