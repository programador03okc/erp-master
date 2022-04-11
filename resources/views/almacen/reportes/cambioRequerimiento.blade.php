<div class="modal fade" tabindex="-1" role="dialog" id="modal-cambio_requerimiento" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:40%;">
        <div class="modal-content">
            <form id="form-cambio_requerimiento">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Cambiar el Almacén de Atención <label id="codigo_req"></label></h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Almacén de atención</h5>
                            <select class="form-control js-example-basic-single" name="id_almacen" required>
                                <option value="">Elija una opción</option>
                                @foreach ($almacenes as $tp)
                                <option value="{{$tp->id_almacen}}">{{$tp->codigo}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_cambio_almacen" class="btn btn-success" value="Actualizar" />
                    <!-- <button class="btn btn-sm btn-success" onClick="generar_transferencia();">Generar Transferencia</button> -->
                </div>
            </form>
        </div>
    </div>
</div>