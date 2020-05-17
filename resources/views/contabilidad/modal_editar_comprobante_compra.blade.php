<div class="modal fade" tabindex="-1" role="dialog" id="modal-editar_comprobante_compra">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Editar</h3>
            </div>
            <div class="modal-body">
                <form id="form_editar_comprobante_compra">
                    <div class="row">
                        <input class="oculto" name="id_doc_com"/>
                        <input class="oculto" name="id_usuario_session"/>
                        <input class="oculto" name="id_orden"/>
                        <input class="oculto" name="id_sede"/>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-1">
                                    <h5>Serie</h5>
                                    <input type="text" class="form-control" name="serie" placeholder="" value="001">
                                </div>
                                <div class="col-md-2">
                                    <h5>Número</h5>
                                    <input type="text" class="form-control" name="numero" placeholder="" disabled>
                                </div>
                                <div class="col-md-7">
                                    <h5>Proveedor</h5>
                                    <input class="oculto" name="id_proveedor"/>
                                    <input type="text" class="form-control" name="proveedor" placeholder="" >
                                </div>
                                <div class="col-md-2">
                                    <h5>Fecha Emisión</h5>
                                    <input type="date" class="form-control" name="fecha_emision" placeholder="" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                    <div class="col-md-2">
                                        <h5>Fecha Vencimiento</h5>
                                        <input type="date" class="form-control" name="fecha_vencimiento" placeholder="" >
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Condición</h5>
                                        <div style="display:flex;">
                                            <select class="form-control group-elemento" name="id_condicion" onchange="handlechangeCondicion(event);" style="width:120px;text-align:center;">
                                                <option value="1">CONTADO CA</option>
                                                <option value="2">CREDITO</option>
                                            </select>
                                            <input type="number" name="plazo_dias" class="form-control group-elemento" style="width:60px; text-align:right;">
                                            <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <h5>Moneda</h5>
                                        <select class="form-control" name="id_moneda">
                                        @foreach ($monedas as $moneda)
                                            <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <h5>Tipo Cambio</h5>
                                        <input type="text" class="form-control" name="tipo_cambio" placeholder="" >
                                    </div>
                                <div class="col-md-1">
                                    <h5>% Desc.</h5>
                                    <input type="text" class="form-control" name="porcentaje_descuento" placeholder="" onInput="inputPorcentajeDescKeyPress(event);" >
                                </div>
                                <div class="col-md-2">
                                    <h5>Monto Descuento</h5>
                                    <input type="text" class="form-control" name="monto_descuento" placeholder="" onInput="inputMontoDescKeyPress(event);">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">

                                <div class="col-md-2">
                                    <h5>SubTotal</h5>
                                    <input type="text" class="form-control" name="monto_subtotal" placeholder="" >
                                </div>
                                <div class="col-md-2">
                                    <h5>IGV</h5>
                                    <input type="text" class="form-control" name="monto_igv" placeholder="" >
                                </div>
                                <div class="col-md-2">
                                    <h5>Total</h5>
                                    <input type="text" class="form-control" name="monto_total" placeholder="" >
                                    <input type="hidden" class="form-control" name="monto_total_fijo" placeholder="" >
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                     <button type="button" onclick="actualizarComprobanteCompra();" class="btn btn-sm btn-success">Actualizar</button>
            </div>
        </div>
    </div>
</div>