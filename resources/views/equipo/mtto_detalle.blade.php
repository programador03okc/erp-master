<div class="modal fade" tabindex="-1" role="dialog" id="modal-mtto_detalle" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Detalle del Mantenimiento</h3>
                </div>
            </div>
            <form id="form-mtto_detalle"  enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <input type="text" name="id_mtto_det" class="oculto">
                    <input type="text" name="id_mtto_padre" class="oculto">
                    <input type="text" name="id_programacion" class="oculto">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Tipo de Mantenimiento</h5>
                            <select name="tp_mantenimiento" class="form-control">
                                <option value="0" disabled>Elija una opción</option>
                                <option value="1">Mtto. Preventivo</option>
                                <option value="2">Mtto. Correctivo</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <h5>Mantenimiento</h5>
                            <select name="id_programacion" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Especificación del Mantenimiento</h5>
                            <input type="text" name="descripcion" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Cantidad</h5>
                            <input type="number" name="cantidad" onChange="calcula_total();" step="0.01" class="form-control right" required>
                        </div>
                        <div class="col-md-4">
                            <h5>Precio Unitario</h5>
                            <input type="number" name="precio_unitario" onChange="calcula_total();" step="0.01" class="form-control right" required>
                        </div>
                        <div class="col-md-4">
                            <h5>Precio Total</h5>
                            <input type="number" name="precio_total" class="form-control right"  step="0.01" readOnly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Seleccione una partida</h5>
                            <div style="width: 100%; display:flex;">
                                <div style="width:90%; display:flex;">
                                    <input class="oculto" name="id_partida">
                                    <input type="text" name="cod_partida" class="form-control input-sm" readOnly style="width:180px;"/>
                                    <input type="text" name="des_partida" class="form-control input-sm" readOnly/>
                                </div>
                                <div style="width:10%;">
                                    <span class="input-group-addon input-sm " style="cursor:pointer;" 
                                        onClick="partidasModal();">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Observaciones</h5>
                            <textarea name="resultado" cols="92" rows="3"></textarea>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-12">
                            <h5>Adjuntar CheckList</h5>
                            <input type="file" name="adjunto" id="adjunto" class="filestyle"
                                data-buttonName="btn-primary" data-buttonText="Adjuntar"
                                data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
                        </div>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success boton" value="Guardar"/>
                    {{-- <button class="btn btn-sm btn-success" onClick="guardar_asignacion();">Guardar</button> --}}
                </div>
            </form>
        </div>
    </div>  
</div>
