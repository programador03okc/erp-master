<div class="modal fade" tabindex="-1" role="dialog" id="modal-detalle-requerimiento">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-detalle-requerimiento" onClick="$('#modal-detalle-requerimiento').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalle Requerimiento</h3>
            </div>
            <div class="modal-body">
                <form id="form-detalle-requerimiento" type="register" form="formulario">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Item</h5>
                                <div style="display:flex;">
                                    <input hidden="true" type="text" name="estado">
                                    <input hidden="true" type="text" name="id_item">
                                    <input hidden="true" type="text" name="id_producto">
                                    <input hidden="true" type="text" name="id_servicio">
                                    <input hidden="true" type="text" name="id_equipo">
                                    <input hidden="true" type="text" name="id_tipo_item">
                                    <input hidden="true" type="text" name="id_detalle_requerimiento">
                                    <input hidden="true" type="text" name="categoria">
                                    <input hidden="true" type="text" name="subcategoria">
                                    <input hidden="true" type="text" name="id_almacen_reserva">
                                    <input hidden="true" type="text" name="almacen_descripcion">
                                    <input type="text" name="codigo_item" class="form-control group-elemento input-sm" style="width:150px;text-align:center;" readonly="">
                                    <input type="text" name="part_number" class="form-control group-elemento input-sm" style="width:150px;text-align:center;" readonly="">
                                    <div class="input-group-okc">
                                    <input type="text" class="form-control  input-sm" name="descripcion_item" placeholder="" aria-describedby="basic-addon4" 
                                        onkeydown="handleKeyDown(event);" 
                                        onKeyPress="handleKeyPress(event);"
                                        onpaste="handlePaste(event);"
                                        readOnly>
                                    <div class="input-group-append">
                                        <button type="button" class="input-group-text" id="basic-addon9" onClick="catalogoItemsModal();">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    </div>                            
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Unidad de Medida</h5>
                                <select name="unidad_medida_item" class="form-control input-sm activation" >
                                        <option value="">Elija una opción</option>
                                    @foreach ($unidades_medida as $unidad_medida)
                                        <option value="{{$unidad_medida->id_unidad_medida}}">{{ $unidad_medida->descripcion}}</option>
                                    @endforeach

                                </select>                        
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Cantidad</h5>
                                <input type="number" min="1" class="form-control input-sm activation" name="cantidad_item" step="1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group-okc">
                                <div class="form-group">
                                    <h5>Precio Unitario <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Precio Unitario Incluye IGV"></i></h5>
                                    <div style="display:flex;">
                                        <input type="number" class="form-control input-sm activation" name="precio_ref_item" step="any" width="200px">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-sm btn-default disabled" id="btnVerUltimasCompras" name="btnVerUltimasCompras" title="Ver ultimas Compras" onClick="verUltimasCompras(event);" disabled>
                                            <i class="fab fa-searchengin"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
      
                            </div>
                        </div>
                        <div class="col-md-2" hidden>
                            <div class="form-group">
                                <h5>Moneda</h5>
                                <select class="form-control input-sm activation" name="tipo_moneda">
                                @foreach ($monedas as $moneda)
                                    <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
        
                        <div class="col-md-3" id="input-group-fecha_entrega">
                            <div class="form-group">
                                <h5>Fecha Máx. Entrega</h5>
                                <input type="date" class="form-control input-sm activation" name="fecha_entrega_item" step="any" min={{ date('Y-m-d H:i:s') }} value={{ date('Y-m-d H:i:s') }}>
                            </div>
                        </div>
                        <div class="col-md-3" id="input-group-lugar_entrega" hidden>
                            <div class="form-group">
                                <h5>Lugar de Entrega</h5>
                                <input type="text" class="form-control input-sm activation" name="lugar_entrega_item" step="any">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="input-group-partida" hidden>
                            <div class="form-group"> 
                                <h5>Partida</h5>
                                <div style="display:flex;">
                                    <input type="hidden" name="id_partida">
                                    <input type="text" name="cod_partida" class="form-control group-elemento input-sm" style="width:200px;text-align:center;" readonly="">
                                    <div class="input-group-okc">
                                        <input type="text" class="form-control  input-sm" name="des_partida" placeholder="" aria-describedby="basic-addon8" disabled>
                                        <div class="input-group-append">
                                            <button type="button" class="input-group-text" id="basic-addon8" onClick="partidasModal();">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            <div class="row">
                <div class="col-md-8">
                        <div name="text-status" class="text-animate"></div>
                </div>
                <div class="col-md-4">
                    <label style="display: none;" id="id_requerimiento"></label>
                    <label><h5><span class="label label-warning" id="obs_det"></span></h5></label>
                    <button class="btn btn-sm btn-primary" name="btn-agregar-item" onClick="agregarItem();">Agregar</button>
                    <button class="btn btn-sm btn-success" name="btn-aceptar-cambio" onClick="aceptarCambiosItem();">Aceptar</button>
                </div>
            </div>
            <div class="row" id="promocion_activa" hidden>
                <div class="col-md-6 text-left">
                <div class="alert alert-info alert-dismissible fade in" role="alert"> 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true" onClick="quitarPromocionAvtiva();">×</span></button> 
                    <i class="fas fa-gift"></i> <strong>promoción:</strong>
                    <ul id="productos_con_promocion">
                    </ul>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

@include('logistica.cotizaciones.modal_ultimas_compras')


