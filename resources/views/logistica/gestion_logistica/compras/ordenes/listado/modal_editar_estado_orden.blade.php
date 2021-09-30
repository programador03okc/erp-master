<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-editar-estado-orden" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <form id="form-editar_estado_orden">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Editar Estado de Orden <span name="codigo_orden"></span></h3>
                 </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="oculto" name="id_orden_compra"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <h5>Estado</h5>
                            <div style="display:flex;">
                                <select class="form-control" name="estado_orden" >
                                    @foreach ($estados as $estado)
                                        <option value="{{$estado->id_estado}}">{{$estado->descripcion}}</option>
                                    @endforeach     
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cancelar</button>
                    <button type="button" class="btn btn-sm btn-success handleClickUpdateEstadoOrdenCompra">Actualizar</button>

                </div>
            </form>
        </div>
    </div>
</div>