<div class="modal fade" tabindex="-1" role="dialog" id="modal-partidaGGCreate" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Partida</h3>
                <label id="titulo_gg"></label>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Código</h5>
                        <input type="text" name="codigo_gg" class="form-control right" readOnly/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input class="oculto" name="id_gg_detalle">
                        <input class="oculto" name="cod_compo_gg">
                        <input class="oculto" name="id_gg">
                        <h5>Ingrese o seleccione un A.C.U.</h5>
                        <div style="width: 100%; display:flex;">
                            <div style="width:90%; display:flex;">
                                <input class="oculto" name="id_cu_gg">
                                <input type="text" name="cod_acu_gg" class="form-control input-sm" readOnly style="width:70px;"/>
                                <input type="text" name="des_acu_gg" class="form-control input-sm" id="des_acu_gg"
                                    onkeydown="change_descripcion_gg();" 
                                    onKeyPress="change_descripcion_gg();" 
                                    onpaste="change_descripcion_gg();" />
                            </div>
                            <div style="width:10%;">
                                <span class="input-group-addon input-sm " style="cursor:pointer;" 
                                    onClick="acuPartidaModal('gg');">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Cantidad</h5>
                        <input type="number" name="cantidad_gg" onChange="calculaPrecioTotalGG();" class="form-control right"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Precio Unitario</h5>
                        <input type="number" name="precio_unitario_gg" onChange="calculaPrecioTotalGG();" class="form-control right"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Unid. Medida</h5>
                        <div style="display:flex;">
                            <select class="form-control" style="font-size:12px;" name="unid_medida_gg">
                                <option value="0" selected>Elija una opción</option>
                                @foreach ($unidades as $unid)
                                    <option value="{{$unid->id_unidad_medida}}">{{$unid->descripcion}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-primary" title="Agregar Unidad de Medida" onClick="agregar_unidad('gg');">
                            <strong>+</strong></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        {{-- <h5>Sist. de Contrato</h5>
                        <select class="form-control group-elemento activation" name="id_sistema" >
                            <option value="0">Elija una opción</option>
                            @foreach ($sistemas as $sis)
                                <option value="{{$sis->id_sis_contrato}}">{{$sis->descripcion}}</option>
                            @endforeach
                        </select> --}}
                        <h5>Participación</h5>
                        <input type="number" name="participacion_gg" onChange="calculaPrecioTotalGG();" class="form-control right"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Tiempo</h5>
                        <input type="number" name="tiempo_gg" onChange="calculaPrecioTotalGG();" class="form-control right"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Veces</h5>
                        <input type="number" name="veces_gg" onChange="calculaPrecioTotalGG();" class="form-control right"/>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Precio Total</h5>
                        <input type="number" name="precio_total_gg" class="form-control right" readOnly/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" id="btnGuardarPartidaGG" onClick="guardar_partida_gg();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
