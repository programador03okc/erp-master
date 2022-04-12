<div class="modal fade" tabindex="-1" role="dialog" id="modal-editarReserva" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:40%;">
        <div class="modal-content">
            <form id="form-editarReserva">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Editar Reserva <label id="codigo_req"></label></h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_reserva">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Almacén de reserva</h5>
                            <select class="form-control js-example-basic-single" name="id_almacen_reserva" required>
                                <option value="">Elija una opción</option>
                                @foreach ($almacenes as $tp)
                                <option value="{{$tp->id_almacen}}">{{$tp->codigo}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Stock comprometido</h5>
                            <input type="number" class="form-control" name="stock_comprometido"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_editarReserva" class="btn btn-success" value="Actualizar" />
                </div>
            </form>
        </div>
    </div>
</div>