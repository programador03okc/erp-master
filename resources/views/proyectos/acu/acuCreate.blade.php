<div class="modal fade" tabindex="-1" role="dialog" id="modal-acu_create" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width:700px;">
        <div class="modal-content">
            <form id="form-acu_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                        aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Crear Nombre del Costo Unitario</h3>
                </div>
                <div class="modal-body">
                    <input class="oculto" name="id_cu">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Categoría</h5>
                            <select class="form-control activation" name="id_categoria">
                                @foreach ($categorias as $cat)
                                    <option value="{{$cat->id_categoria}}">{{$cat->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Descripción</h5>
                            <input type="text" name="cu_descripcion" class="form-control input-sm"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Especificaciones Técnicas</h5>
                            <textarea name="observacion" class="form-control" rows="4" cols="50"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success" data-toggle="tooltip" 
                        data-placement="bottom" title="Guardar" value="Guardar"/>
                    {{-- <button class="btn btn-sm btn-success" onClick="guardar_acu();">Guardar</button> --}}
                </div>
            </form>
        </div>
    </div>  
</div>
