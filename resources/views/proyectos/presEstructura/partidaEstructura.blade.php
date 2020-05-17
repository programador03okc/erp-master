<div class="modal fade" tabindex="-1" role="dialog" id="modal-partidaEstructura" style="overflow-y:scroll;">
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
                        <input type="text" name="codigo" class="form-control right" readOnly/>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-12">
                        <h5>Ingrese una descripción</h5>
                        <input type="text" name="descripcion_partida" class="form-control input-sm"/>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-12">
                        <input class="oculto" name="id_partida">
                        <input class="oculto" name="cod_padre">
                        <input class="oculto" name="id_presup">
                        <h5>Ingrese o seleccione un A.C.U.</h5>
                        <div style="width: 100%; display:flex;">
                            <div style="width:90%; display:flex;">
                                <input class="oculto" name="id_pardet">
                                {{-- <input type="text" name="codigo" class="form-control input-sm" readOnly style="width:70px;"/> --}}
                                <input type="text" name="des_pardet" class="form-control input-sm" readOnly
                                    onkeydown="change_descripcion();" 
                                    onKeyPress="change_descripcion();" 
                                    onpaste="change_descripcion();"/>
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
                        <h5>Codigo Relacionado</h5>
                        <input type="text" name="relacionado" class="form-control right"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_partida();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
