<div class="modal fade" tabindex="-1" role="dialog" id="modal-partidaCICreate" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header" >
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Crear Partida </h3>
                    <label id="titulo"></label>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Código</h5>
                        <input type="text" name="codigo_ci" class="form-control right" readOnly/>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-12">
                        <h5>Ingrese una descripción</h5>
                        <input type="text" name="descripcion_partida_ci" class="form-control input-sm"/>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-12">
                        <input class="oculto" name="id_ci_detalle">
                        <input class="oculto" name="cod_compo_ci">
                        <input class="oculto" name="id_ci">
                        <h5>Ingrese o seleccione un A.C.U.</h5>
                        <div style="width: 100%; display:flex;">
                            <div style="width:90%; display:flex;">
                                <input class="oculto" name="id_cu_ci">
                                <input type="text" name="cod_acu_ci" class="form-control input-sm" readOnly style="width:70px;"/>
                                <input type="text" name="des_acu_ci" class="form-control input-sm" 
                                    onkeydown="change_descripcion_ci();" 
                                    onKeyPress="change_descripcion_ci();" 
                                    onpaste="change_descripcion_ci();"/>
                            </div>
                            <div style="width:10%;">
                                <span class="input-group-addon input-sm " style="cursor:pointer;" 
                                    onClick="acuPartidaModal('ci');">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Cantidad</h5>
                        <input type="number" name="cantidad_ci" onChange="calculaPrecioTotalCI();" class="form-control right"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Precio Unitario</h5>
                        <input type="number" name="precio_unitario_ci" onChange="calculaPrecioTotalCI();" class="form-control right" />
                    </div>
                    <div class="col-md-4">
                        <h5>Unid. Medida</h5>
                        <div style="display:flex;">
                            <select class="form-control" style="font-size:12px;" name="unid_medida_ci">
                                <option value="0" selected>Elija una opción</option>
                                @foreach ($unidades as $unid)
                                    <option value="{{$unid->id_unidad_medida}}">{{$unid->descripcion}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-primary" title="Agregar Unidad de Medida" onClick="agregar_unidad('ci');">
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
                        <input type="number" name="participacion" onChange="calculaPrecioTotalCI();" class="form-control right"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Tiempo</h5>
                        <input type="number" name="tiempo" onChange="calculaPrecioTotalCI();" class="form-control right"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Veces</h5>
                        <input type="number" name="veces" onChange="calculaPrecioTotalCI();" class="form-control right"/>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Precio Total</h5>
                        <input type="number" name="precio_total_ci" class="form-control right" readOnly/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" id="btnGuardarPartidaCI" onClick="guardar_partida_ci();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
