<div class="modal fade" tabindex="-1" role="dialog" id="modal-nueva-persona" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 400px;">
        <div class="modal-content">
            <form id="form-nueva-persona">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Nueva Persona</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_persona" />
                    <fieldset class="group-table">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Tipo Documento *</h5>
                                <select class="form-control js-example-basic-single " name="id_doc_identidad" required>
                                    <option value="">Elija una opción</option>
                                    @foreach ($tipos_documentos as $tipo)
                                    @if($tipo->id_doc_identidad == 1)
                                    <option value="{{$tipo->id_doc_identidad}}" selected>{{$tipo->descripcion}}</option>
                                    @else
                                    @if($tipo->id_doc_identidad == 2)
                                    <option value="{{$tipo->id_doc_identidad}}" disabled>{{$tipo->descripcion}}</option>
                                    @else
                                    <option value="{{$tipo->id_doc_identidad}}">{{$tipo->descripcion}}</option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Nro. documento *</h5>
                                <input type="text" name="nuevo_nro_documento" class="form-control limpiar" placeholder="Ingrese el número de documento" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Nombres *</h5>
                                <input type="text" name="nuevo_nombres" class="form-control limpiar" placeholder="Ingrese los nombres" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Apellido paterno *</h5>
                                <input type="text" name="nuevo_apellido_paterno" class="form-control limpiar" placeholder="Ingrese el apellido paterno" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Apellido materno *</h5>
                                <input type="text" name="nuevo_apellido_materno" class="form-control limpiar" placeholder="Ingrese el apellido materno" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Teléfono</h5>
                                <input type="text" name="telefono" class="form-control limpiar" placeholder="Ingrese el teléfono" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Dirección</h5>
                                <input type="text" name="direccion" class="form-control limpiar" placeholder="Ingrese la dirección" />
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
                    <button type="button" class="btn btn-sm btn-default" class="close" data-dismiss="modal">Cerrar</button>
                    <button type="submit" id="submit_nueva_persona" class="btn btn-sm btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>