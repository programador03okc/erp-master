<div class="modal fade" tabindex="-1" role="dialog" id="modal-ordenDespachoEstados" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 450px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Nuevo estado de envío</h3>
                </div>
            </div>
            <form id="form-ordenDespachoEstados" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <fieldset class="group-table" id="fieldsetEstadoEnvio">
                        <div class="row">
                            <input type="text" class="oculto" name="id_od"/>
                            <div class="col-md-6">
                                <h5>Estado *</h5>
                                <div class="input-group-okc">
                                    <select class="form-control js-example-basic-single" name="estado" required>
                                        <option value="">Seleccione una opción</option>
                                        @foreach ($estados as $estado)
                                        <option value="{{$estado->id_estado}}">{{$estado->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Fecha del estado</h5>
                                <input type="date" class="form-control" name="fecha_estado" >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Gasto extra</h5>
                                <div class="input-group">
                                    <span class="input-group-addon" disabled>S/</span>
                                    <input type="number" class="form-control" name="gasto_extra" step="any" placeholder="Gasto extra" >
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <h5>Plazo excedido</h5>
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" name="plazo_excedido" style="margin-top: 13px;"/>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Comentario</h5>
                                <textarea name="observacion" id="observacion" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Buscar Archivo</h5>
                                <input type="file" name="adjunto" id="adjunto" class="filestyle"
                                    data-buttonName="btn-default" data-buttonText="Adjuntar" 
                                    data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>* Campos obligatorios</h5>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_ordenDespachoEstados" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>  
</div>
