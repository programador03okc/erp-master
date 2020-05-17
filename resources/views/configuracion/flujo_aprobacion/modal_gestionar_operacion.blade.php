<div class="modal fade" tabindex="-1" role="dialog" id="modal-gestionar_operacion">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Gestionar Operación</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
            
                    <form id="form-modal-gestionar_operacion" type="register" form="formulario">
                        <input type="hidden" name="id_operacion_" >
                        <div class="row">
                            <div class="col-md-9">
                                <h5>Nombre de Operación</h5>
                                <input type="text"   class="form-control" name="operacion_descripcion_" placeholder="Descripción">
                            </div>
                            <div class="col-md-3">
                                <h5>Tipo documento</h5>
                                <select class="form-control" name="tipo_documento_" onchange="">
                                    <option value="0" selected >Elija una opción</option>
                
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <h5>Empresa</h5>
                                <select class="form-control" name="empresa_" onchange="">
                                    <option value="0" selected >Elija una opción</option>
                                  
                                </select>
                            </div>
                            <div class="col-md-3">
                                <h5>Sede</h5>
                                <select class="form-control" name="sede_" onchange="">
                                    <option value="0" selected >Elija una opción</option>
                              
                                </select>
                            </div>
                            <div class="col-md-3">
                                <h5>Grupo</h5>
                                <select class="form-control" name="grupo_" onchange="">
                                    <option value="0" selected >Elija una opción</option>
                 
                                </select>
                            </div>
                            <div class="col-md-3">
                                <h5>Area</h5>
                                <select class="form-control" name="area_" onchange="">
                                    <option value="0" selected >Elija una opción</option>
        
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <h5>Estado Operación</h5>
                                <select class="form-control" name="operacion_estado_" onchange="">
                                    <option value="0" selected >Elija una opción</option>
                                    <option value="1">Activado</option>
                                    <option value="7">Anulado</option>
                                </select>                                        
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-sm btn-success" name="btnActualizarOperacion_" onClick="actualizarOperacion_(event);" disabled>Actualizar</button>
                        </div>
                    </form>

                    </div>
                </div>



            </div>
 
        </div>
    </div>
</div>