<div class="modal fade" tabindex="-1" role="dialog" id="modal-partidaCDCreate" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width:900px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="titulo">Crear Partida</label></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-9">
                        <h5>Seleccione un A.C.U.</h5>
                        <input class="oculto" name="id_partida">
                        <input class="oculto" name="codigo_cd">
                        <input class="oculto" name="cod_compo">
                        <div style="width: 100%; display:flex;">
                            <div style="width:90%; display:flex;">
                                <input class="oculto" name="id_cu_partida_cd">
                                <input class="oculto" name="id_cu_cd">
                                <input type="text" name="cod_cu" class="form-control input-sm" readOnly style="width:70px; height: 31px;"/>
                                <input type="text" name="des_cu" class="form-control input-sm" readOnly style="height: 31px;"/>
                            </div>
                            <div style="width:7%;">
                                <span class="input-group-addon input-sm btn-primary" style="cursor:pointer; height: 31px;" 
                                    onClick="acuPartidaModal('cd');">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <div style="width:7%;">
                                <span class="input-group-addon input-sm btn-success" style="cursor:pointer; height: 31px;" 
                                    onClick="acuParticaCreateModal();">
                                    <i class="fas fa-plus"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Unidad</h5>
                        <input type="text" name="id_unid_medida" hidden/>
                        <input type="text" name="unid_medida" readOnly class="form-control input-sm"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Sistema</h5>
                        <select class="form-control group-elemento" name="id_sistema_cu" >
                            <option value="0">Elija una opción</option>
                            @foreach ($sistemas as $sis)
                                <option value="{{$sis->id_sis_contrato}}">{{$sis->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <h5>Cantidad</h5>
                        <input type="number" name="cantidad_par" class="form-control input-sm" 
                            onChange="calculaPrecioTotalPartida();"/>                        
                    </div>
                    <div class="col-md-3">
                        <h5>Unitario</h5>
                        <input type="number" name="precio_unitario" readOnly class="form-control input-sm" 
                            onChange="calculaPrecioTotalPartida();"/>
                    </div>
                    <div class="col-md-3">
                        <h5>Total</h5>
                        <input type="number" name="precio_total_partida" readOnly class="form-control input-sm" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_partida_cd();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
