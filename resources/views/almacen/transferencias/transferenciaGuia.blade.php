<div class="modal fade" tabindex="-1" role="dialog" id="modal-transferenciaGuia">
    <div class="modal-dialog">
        <div class="modal-content" style="width:600px;">
            <form id="form-transferenciaGuia">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Transferencia entre Almacenes </h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_guia_ven"/>
                    <input type="text" class="oculto" name="id_sede">
                    <input type="text" class="oculto" name="id_mov_alm">
                    <input type="text" class="oculto" name="id_guia_com">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Serie-Número</h5>
                            <input type="text" class="oculto" name="id_serie_numero">
                            <div class="input-group">
                                <input type="text" class="form-control activation" name="trans_serie"
                                    placeholder="0000" onBlur="ceros_numero_trans('serie');">
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" name="trans_numero"
                                    placeholder="0000000" onBlur="ceros_numero_trans('numero');"> 
                                    <!-- onBlur="ceros_numero('numero');"> -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Fecha Almacén</h5>
                            <input type="date" class="form-control" name="fecha_almacen" value="<?=date('Y-m-d');?>">
                        </div>
                        <!-- <div class="col-md-6">
                            <h5>Fecha de Transferencia</h5>
                            <input type="date" class="form-control" name="fecha_transferencia" value="<?=date('Y-m-d');?>">
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Almacén Origen</h5>
                            <select class="form-control" name="id_almacen_origen" readOnly>
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h5>Almacén Destino</h5>
                            <select class="form-control" name="id_almacen_destino">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <!-- <div class="col-md-6">
                            <h5>Responsable Origen:</h5>
                            <select class="form-control" name="responsable_origen" >
                                <option value="0">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div> -->
                        <div class="col-md-6">
                            <h5>Responsable Destino:</h5>
                            <!-- js-example-basic-single -->
                            <select class="form-control " name="responsable_destino">
                                <option value="0">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_transferencia" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>