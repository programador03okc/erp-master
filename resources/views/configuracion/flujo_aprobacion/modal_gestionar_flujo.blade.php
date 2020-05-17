<div class="modal fade" tabindex="-1" role="dialog" id="modal-gestionar_flujo">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Gestionar Flujo</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form-gestionar_flujo" type="register" form="formulario">
                            <input type="hidden" name="id_flujo" >
                            <div class="row">
                                <div class="col-md-9" id="nombre_flujo">
                                    <h5>Nombre de Flujo</h5>
                                    <input type="text"   class="form-control" name="nombre_flujo" placeholder="Descripcion del modulo">
                                </div>
                                <div class="col-md-3">
                                    <h5>Grupo Flujo</h5>
                                    <select class="form-control" name="grupo_flujo" onchange="">
                                        <option value="0" selected >Elija una opción</option>

                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Rol</h5>
                                    <select class="form-control" name="rol" onchange="">
                                        <option value="0" selected >Elija una opción</option>

                                    </select>                                    
                                </div>
                                <div class="col-md-3">
                                    <h5>Orden</h5>
                                    <input type="number" min="1" class="form-control" name="orden" placeholder="Descripcion del modulo">
                                </div>
                                <div class="col-md-3">
                                    <h5>Estado Flujo</h5>
                                    <select class="form-control" name="flujo_estado" onchange="">
                                        <option value="0" selected >Elija una opción</option>
                                        <option value="1">Activado</option>
                                        <option value="7">Desativado</option>
                                    </select>                                        
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">Operación</div>
                                            <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5>Operación</h5>
                                                    <select class="form-control" name="operacion" onchange="OnchangeOperacion(event)">
                                                        <option value="0" selected>Elija una opción</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <table
                                                        class="mytable table table-hover table-condensed table-bordered table-result-form"
                                                        id="listaOperacion"
                                                    >
                                                        <caption>
                                                            Operación
                                                        </caption>

                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>Empresa</th>
                                                                <th>Sede</th>
                                                                <th>Grupo</th>
                                                                <th>Area</th>
                                                                <th>Tipo Documento</th>
                                                                <th>Estado</th>
                                                                <th>Acción</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            </div>
                                                <div class="text-center">
                                                    <button class="btn btn-sm btn-success" name="btnActualizarFlujo" onClick="actualizarFlujo(event);" disabled>Actualizar</button>
                                                </div>
                                            <br>

                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>



            </div>
 
        </div>
    </div>
</div>