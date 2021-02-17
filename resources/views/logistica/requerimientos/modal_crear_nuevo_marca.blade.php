<div class="modal fade" tabindex="-1" role="dialog" id="modal-crear-nueva-marca" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 300px;">
        <div class="modal-content">
            <form id="form-crear-nueva-marca">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-crear-nueva-marca" onClick="$('#modal-crear-nueva-marca').modal('hide');"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Crear Marca</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Categoría</h5>
                            <select class="form-control activation js-example-basic-single" name="id_categoria" required>
                                <!-- <option value="0">Elija una opción</option> -->
                                @foreach ($categorias as $cat)
                                    <option value="{{$cat->id_categoria}}">{{$cat->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <h5>Nombre de Marca</h5>
                            <input class="form-control" type="text" name="nombre_marca" id="nombre_marca">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-success" title="Guardar" name="btnSaveMarca" onclick="guardar_nueva_marca();">Guardar</button>

                </div>
            </form>
        </div>
    </div>
</div>