<div class="modal fade" tabindex="-1" role="dialog" id="modal-despacho">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Editar</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control input-sm" id="id_valorizacion_cotizacion" name="id_valorizacion_cotizacion" />

                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#tabDescripcionAdicional" aria-controls="tabDescripcionAdicional" role="tab" data-toggle="tab">Descripción Adicional</a></li>
                        <li role="presentation" class=""><a href="#tabDespacho" aria-controls="tabDespacho" role="tab" data-toggle="tab">Despacho</a></li>
                      </ul>
                    

                    
                    <!-- Tab panes -->
                    <div class="tab-content" id="tabPanel">
                    <div role="tabpanel" class="tab-pane active" id="tabDescripcionAdicional"> <!--tab1 -->
                    
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="container-fluid">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <form class="form-horizontal" id="form-descripcion_adicional">
                                                    <div class="form-group">
                                                        <label for="inputPassword3" class="col-sm-4 control-label">Descripción en ítem Requerimiento</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control input-sm" id="descripcion_adicional_item_requerimiento" name="descripcion_adicional_item_requerimiento" disabled />
                                                        </div>
                                                    </div>                                                   
                                                    <div class="form-group">
                                                        <label for="inputPassword3" class="col-sm-4 control-label">Descripción adicional en ítem de Orden (Opcional)</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control input-sm" id="descripcion_adicional_item_orden" name="descripcion_adicional_item_orden" />
                                                        </div>
                                                    </div>                                                   
                                                    <div class="row">
                                                        <div class="col-md-12 text-right">
                                                            <input type="submit" class="btn btn-success btn-sm btn-flat" value="Guardar">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--/tab1 -->

                        <div role="tabpanel" class="tab-pane" id="tabDespacho"> <!--tab2 -->
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="container-fluid">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <form class="form-horizontal" id="form-despacho">
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-4 control-label">Personal Autorizado</label>
                                                        <div class="col-sm-8">
                                                            <div class="input-group-btn">
                                                            <select class="form-control" name="personal_autorizado">
                                                                    <option value="">Elija una opción</option>
                                                                     @foreach ($responsables as $responsable)
                                                                        <option value="{{$responsable->id_responsable}}">{{$responsable->nombre_responsable}}</option>
                                                                    @endforeach
                                                                </select> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputPassword3" class="col-sm-4 control-label">Despacho (Orden)</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control input-sm" id="lugar_despacho_orden" name="lugar_despacho_orden" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputPassword3" class="col-sm-4 control-label">Despacho (Valorización)</label>
                                                        <div class="col-sm-8">
                                                             <input type="text" class="form-control input-sm" id="lugar_despacho_valorizacion" name="lugar_despacho_valorizacion" readonly />
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputPassword3" class="col-sm-4 control-label">Despacho (Requerimiento)</label>
                                                        <div class="col-sm-8">
                                                             <input type="text" class="form-control input-sm" id="lugar_entrega_requerimiento" name="lugar_entrega_requerimiento" readonly />
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12 text-right">
                                                            <input type="submit" class="btn btn-success btn-sm btn-flat" value="Guardar">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--/tab1 -->
                    </div> <!--  /tab-content -->
                </div>
            </div>
        </div>
    </div>
</div>