<div class="modal fade" tabindex="-1" role="dialog" id="modal-transferencia">
    <div class="modal-dialog">
        <div class="modal-content" style="width:600px;">
            <form id="form-transferencia">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Transferencia entre Almacenes <label id="codigo_transferencia">T-OKC-19-001</label></h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_guia_ven"/>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Fecha de Transferencia</h5>
                            <input type="date" class="form-control" name="fecha_transferencia" value="<?=date('Y-m-d');?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Almacén Origen</h5>
                            <select class="form-control" name="id_almacen_origen" onChange="revisar_almacen('origen');" readOnly>
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h5>Almacén Destino</h5>
                            <select class="form-control js-example-basic-single" name="id_almacen_destino" onChange="revisar_almacen('destino');">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Responsable Origen:</h5>
                            <select class="form-control" name="responsable_origen" readOnly>
                                <option value="0">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h5>Responsable Destino:</h5>
                            <select class="form-control js-example-basic-single" name="responsable_destino">
                                <option value="0">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>