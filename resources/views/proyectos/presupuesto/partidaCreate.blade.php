<div class="modal fade" tabindex="-1" role="dialog" id="modal-partidaCreate" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Partida</label></h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Código</h5>
                        <input type="text" name="codigo_ci" class="form-control right" readOnly/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input class="oculto" name="id_presup">
                        <input class="oculto" name="cod_padre">
                        <input class="oculto" name="id_partida">
                        <h5>Ingrese la Descripción</h5>
                        <div style="width: 100%; display:flex;">
                            <div style="width:90%; display:flex;">
                                <input class="oculto" name="id_pardet">
                                <input type="text" name="descripcion" class="form-control input-sm"/>
                            </div>
                            <div style="width:10%;">
                                <span class="input-group-addon input-sm " style="cursor:pointer;" 
                                    onClick="pardetModal();">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Metrado</h5>
                        <input type="number" name="metrado" onChange="calculaTotal();" class="form-control right"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Precio Unitario</h5>
                        <input type="number" name="precio_unitario" onChange="calculaTotal();" class="form-control right" />
                    </div>
                    <div class="col-md-4">
                        <h5>Unid. Medida</h5>
                        <select class="form-control" style="font-size:12px;" name="unidad_medida">
                            <option value="0" selected>Elija una opción</option>
                            @foreach ($unidades as $unid)
                                <option value="{{$unid->id_unidad_medida}}">{{$unid->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Importe Base</h5>
                        <input type="number" name="importe_base" class="form-control right" readOnly/>
                    </div>
                    <div class="col-md-4">
                        <h5>Importe Total</h5>
                        <input type="number" name="importe_total" class="form-control right" readOnly/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_partida();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
