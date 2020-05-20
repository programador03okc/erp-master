<div class="modal fade" tabindex="-1" role="dialog" id="modal-opcion_create" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Opción Comercial</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Codigo</h5>
                        <input class="oculto" name="id_op_com"  >
                        <input type="text"  name="codigo" placeholder="OP-00-000" class="form-control activation" readOnly>
                    </div>
                    <div class="col-md-9">
                        <h5>Descripción</h5>
                        <input type="text"  name="descripcion" class="form-control activation">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Tipo</h5>
                        <select class="form-control activation" name="tp_proyecto">
                            <option value="0">Elija una opción</option>
                            @foreach ($tipos as $tp)
                                <option value="{{$tp->id_tp_proyecto}}">{{$tp->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Empresa</h5>
                        <select class="form-control activation" name="id_empresa">
                            <option value="0">Elija una opción</option>
                            @foreach ($empresas as $emp)
                                <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <h5>Cliente</h5>
                        <div style="display:flex;">
                            <input class="oculto" name="id_cliente"/>
                            <input class="oculto" name="id_contrib"/>
                            <input type="text" class="form-control activation" name="cliente_razon_social" placeholder="Seleccione un cliente..." 
                                aria-describedby="basic-addon1" disabled>
                            <button type="button" class="input-group-text activation btn-primary" id="basic-addon1" onClick="clienteModal();">
                                <i class="fa fa-search"></i>
                            </button>
                            <button type="button" class="input-group-text activation btn-success" id="basic-addon1" onClick="agregar_cliente();">
                                <strong>+</strong>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Fecha Emisión</h5>
                        <input type="date" name="fecha_emision" class="form-control" value="<?=date('Y-m-d');?>"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Plazo de Ejecución</h5>
                        <div style="display:flex;">
                            <input type="number" name="cantidad" class="form-control group-elemento activation" style="width:90px;text-align:right;"/>
                            <select class="form-control group-elemento activation" name="unid_program">
                                <option value="0" selected>Elija una opción</option>
                                @foreach ($unid_program as $unid)
                                    <option value="{{$unid->id_unid_program}}">{{$unid->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h5>Modalidad</h5>
                        <select class="form-control activation" name="modalidad">
                            <option value="0" selected>Elija una opción</option>
                            @foreach ($modalidades as $mod)
                                <option value="{{$mod->id_modalidad}}">{{$mod->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" id="btnGuardarOpcion" onClick="guardar_opcion();" >Guardar</button>
            </div>
        </div>
    </div>  
</div>
