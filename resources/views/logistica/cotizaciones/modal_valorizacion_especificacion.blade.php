<div class="modal fade" tabindex="-1" role="dialog" id="modal-valorizacion-especificacion">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Valorizar</h3>
            </div>
            <div class="modal-body">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#tabValorizar" aria-controls="tabValorizar" role="tab" data-toggle="tab">Item</a></li>
                        <li role="presentation"><a href="#tabEspecificacion" aria-controls="tabEspecificacion" role="tab" data-toggle="tab">Especificación</a></li>
                        <li role="presentation"><a href="#tabAdjuntos" aria-controls="tabAdjuntos" role="tab" data-toggle="tab">Adjuntos</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="tabPanel">
                        <div role="tabpanel" class="tab-pane active" id="tabValorizar"> <!--tab1 -->
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="container-fluid">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <form class="form-horizontal" id="form-valorizacion-item">
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Unidad Medida</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group-btn">
                                                                <select id="unidad_medida_valorizacion" name="unidad_medida_valorizacion" class="form-control input-sm activation" onchange="onChangeInputValorizacion();">
                                                                    <option value="">Elija una opción</option>
                                                                    @foreach ($unidades_medida as $unidad_medida)
                                                                    <option value="{{$unidad_medida->id_unidad_medida}}">{{ $unidad_medida->descripcion}}</option>
                                                                    @endforeach

                                                                </select> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputPassword3" class="col-sm-3 control-label">Cantidad</label>
                                                        <div class="col-sm-9">
                                                            <input type="number" min="1" class="form-control input-sm" id="cantidad_valorizacion" name="cantidad_valorizacion" onchange="onChangeInputValorizacion();" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Precio Unitario</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <div class="input-group-addon">S/.</div>
                                                                <input type="text" class="form-control input-sm" id="precio_valorizacion" name="precio_valorizacion" onchange="onChangeInputValorizacion();" >
                                                                <div class="input-group-addon">IGV</div>
                                                                <select class="form-control input-sm input-sm" id="igv" name="igv" onchange="onChangeInputValorizacion();">
                                                                    <option value="SI">SI</option>
                                                                    <option value="NO" selected >NO</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">IGV</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <div class="input-group-addon">S/.</div>
                                                                <input type="text" class="form-control input-sm" id="monto_igv" name="monto_igv" readonly >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Descuento</label>
                                                        <div class="col-sm-9">
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><input type="radio"  id="check_option_porcentaje" name="defaultExampleRadios" onchange='handleCheckChange(this);'> %</div>
                                                            <input type="text" class="form-control input-sm" id="porcentaje_descuento_valorizacion" name="porcentaje_descuento_valorizacion" onchange="onChangeInputValorizacion();" value="0">
                                                       
                                                            <div class="input-group-addon"><input type="radio" id="check_option_monto"  name="defaultExampleRadios" onchange='handleCheckChange(this);'> Monto</div>
                                                            <input type="text" class="form-control input-sm" id="monto_descuento_valorizacion" name="monto_descuento_valorizacion" onchange="onChangeInputValorizacion();" value="0">
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Monto Neto (SIN IGV)</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <div class="input-group-addon">S/.</div>
                                                                <input type="text" class="form-control input-sm" id="monto_neto" name="monto_neto" readonly >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Sub-total</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <div class="input-group-addon">S/.</div>
                                                                <input type="text" class="form-control input-sm" id="subtotal_valorizacion" name="subtotal_valorizacion" readonly >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Flete</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <div class="input-group-addon">S/.</div>
                                                                <input type="text" class="form-control input-sm" id="flete_valorizacion" name="flete_valorizacion" onchange="onChangeInputValorizacion();" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Sub-total + Flete</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <div class="input-group-addon">S/.</div>
                                                                <input type="text" class="form-control input-sm" id="subtotal_con_flete" name="subtotal_con_flete" onchange="onChangeInputValorizacion();" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--/tab1 -->
                        <div role="tabpanel" class="tab-pane" id="tabEspecificacion"><!--tab2 -->
                        <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="container-fluid">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <form class="form-horizontal" id="form-valorizacion-especificacion" form="formulario" >
                                                    
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Garantía</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <input type="text" pattern="[0-9]+" class="form-control input-sm" id="garantia" name="garantia">
                                                                <div class="input-group-addon">Meses</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Plazo Entrega</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <input type="text" pattern="[0-9]+" class="form-control input-sm" id="plazo_entrega" name="plazo_entrega">
                                                                <div class="input-group-addon">Días</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Lugar Entrega</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="lugar_entrega" name="lugar_entrega">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputEmail3" class="col-sm-3 control-label">Detalle Adicional</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control input-sm" rows="1" id="detalle_adicional" name="detalle_adicional"></textarea>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--/tab2 -->
                        <div role="tabpanel" class="tab-pane" id="tabAdjuntos"><!--tab3 -->
                        <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-body">
                                            <input type="hidden" id="id_valorizacion_cotizacion">
                                            <input type="hidden" id="id_detalle_requerimiento">
                                            <h4>Requerimiento</h4>
                                            <table class="mytable table table-striped table-condensed table-bordered" id="listaArchivos">
                                                <thead>
                                                    <tr>
                                                        <th class="hidden"></th>
                                                        <th class="hidden"></th>
                                                        <th>#</th>
                                                        <th>DESCRIPCION</th>
                                                        <th>
                                                            
                                                        <!-- <i class="fas fa-plus-square icon-tabla green boton" 
                                                            data-toggle="tooltip" data-placement="bottom" 
                                                            title="Agregar Archivo" onClick="agregarAdjunto(event);"></i> -->
                                                        </th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <h4>Proveedor</h4>
                                            <div class="col-md-12">
                                                <div class="input-group-okc">
                                                    <input type="file" class="custom-file-input" onchange="agregarAdjuntoProveedor(event)" />
                                                    <div class="input-group-append">
                                                        <button
                                                            type="button"
                                                            class="btn btn-info"
                                                            onClick="guardarAdjuntosProveedor();"
                                                            ><i class="fas fa-file-upload"></i> Subir Archivo
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="mytable table table-striped table-condensed table-bordered" id="listaArchivosProveedor">
                                                <thead>
                                                    <tr>
                                                        <th class="hidden"></th>
                                                        <th class="hidden"></th>
                                                        <th>#</th>
                                                        <th>DESCRIPCION</th>
                                                        <th></th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--/ tab3 -->

                    </div> <!--  /tab-content -->
                </div>
            </div>

            <div class="modal-footer">
            <input type="hidden" id="id_cotizacion">
            <input type="hidden" id="id_valorizacion_cotizacion">

                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-primary btn-sm" title="Guardar" name="btnGuardarValorizarCotizacion" onClick="guardarValorizarCotizacion();" >
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>