<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-incidenciaProducto">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Producto</h3>
            </div>
            <div class="modal-body">
                <input type="text" style="display:none;" name="id_incidencia_producto">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Producto</h5>
                        <input type="text" class="form-control" name="producto">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Marca</h5>
                        <input type="text" class="form-control" name="marca">
                    </div>
                    <div class="col-md-6">
                        <h5>Modelo</h5>
                        <input type="text" class="form-control" name="modelo">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Serie</h5>
                        <input type="text" class="form-control" name="serie">
                    </div>
                    <div class="col-md-6">
                        <h5>Tipo</h5>
                        <select class="form-control js-example-basic-single" name="id_tipo" required>
                            <option value="0">Elija una opción</option>
                            @foreach ($tiposProducto as $tp)
                            <option value="{{$tp->id_tipo}}">{{$tp->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="agregarProductoIncidencia();">Agregar</button>
            </div>
        </div>
    </div>
</div>