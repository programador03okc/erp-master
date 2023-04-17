<div class="modal fade" tabindex="-1" role="dialog" id="modal-factura-requerimiento-pago" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <form id="form-factura-requerimiento-pago" method="post" type="register">
                <input type="hidden" name="id_requerimiento_pago" primary="ids">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="modal-title">
                        <div style="display:flex; justify-content: space-between;">
                            <div><span id="titulo-modal">Vincular Items de requerimiento de pago </span> <span class="text-primary" style="font-weight: bold;" name="codigo"></span> con Factura<span> </div>
                            <label style="font-size: 1.4rem; margin-right: 10px; "><span name="fecha_registro"></span></label>

                        </div>
                    </h3>

                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-table">
                                <div class="btn-group" role="group" aria-label="...">
                                </div>
                                <div class="box box-widget">
                                    <div class="box-body">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <h5>Tipo de Documento</h5>
                                                <select class="form-control js-example-basic-single" name="id_tp_doc" readOnly>
                                                    @foreach($tipoDocumentos as $tipoDocumento)
                                                    <option value="{{$tipoDocumento->id_tp_doc}}">{{$tipoDocumento->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <h5>Serie-Número</h5>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="serie_doc" placeholder="F001" readOnly>
                                                    <span class="input-group-addon">-</span>
                                                    <input type="text" class="form-control" name="numero_doc" onblur="ceros_numero_doc();" placeholder="000000" readOnly>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <h5>Fecha de Emisión</h5>
                                                <input type="date" class="form-control" name="fecha_emision_doc" readOnly>
                                            </div>

                                            <div class="col-md-3">
                                                <h5>Empresa-Sede</h5>
                                                <select class="form-control js-example-basic-single" name="id_sede" readOnly>
                                                    <option value="0">Elija una opción</option>
                                                    @foreach ($empresas as $empresa)
                                                    <option value="{{$empresa->id_empresa}}">{{ $empresa->razon_social}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <h5>Proveedor</h5>
                                                <input type="text" style="display:none;" name="id_proveedor">
                                                <input type="text" style="display:none;" name="id_cta_principal">
                                                <input type="text" class="form-control" name="proveedor_razon_social" value="(No requerido para este medio)" readonly="">
                                            </div>

                                            <div class="col-md-3">
                                                <h5>Importe Total</h5>
                                                <div style="display:flex;">
                                                    <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" readonly="">
                                                    <input type="text" name="importe" class="form-control group-elemento" style="text-align: right;" readonly="">
                                                    <select class="form-control group-elemento" name="moneda" onchange="changeMoneda();" readOnly>
                                                        <option value="0">Elija una opción</option>
                                                        <option value="1" data-sim="S/">Soles</option>
                                                        <option value="2" data-sim="$">Dólares</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <h5>Condición de compra</h5>
                                                <div style="display:flex;">
                                                    <select class="form-control group-elemento" name="id_condicion"  readOnly>
                                                        <option value="0">Elija una opción</option>
                                                        <option value="1" selected>Contado cash</option>
                                                        <option value="2">Crédito</option>
                                                    </select>
                                                </div>
                                            </div>


                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-condensed table-bordered" id="ListaDetalleRequerimientoPagoYFactura" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 1%">Selec.</th>
                                                                <th style="width: 10%">Partida</th>
                                                                <th style="width: 10%">C.Costo</th>
                                                                <th>Descripción de item</th>
                                                                <th style="width: 10%">Unidad</th>
                                                                <th style="width: 6%">Cantidad</th>
                                                                <th style="width: 8%">Precio Unit.<span name="simboloMoneda">S/</span></th>
                                                                <th style="width: 6%">Subtotal</th>
                                                                <th style="width: 10%">Motivo</th>
                                                                <th style="width: 10%">Facturas Vinculadas</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody ></tbody>
                                                        <tfoot>
                                                            <!-- <tr>
                                                        <td colspan="6" class="text-right"><strong>Total:</strong></td>
                                                        <td class="text-right"><span name="simboloMoneda">S/</span><label name="total"> 0.00</label></td>
                                                        <td></td>
                                                    </tr> -->
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </fieldset>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" class="close" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-sm btn-success handleClickConfirmarCrearFactura" id="btnConfirmarCrearFactura" data-toggle="tooltip" data-placement="bottom" title="Confirmar crear factura">Confirmar crear factura</button>

                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="modal-info-adicional-cuenta-seleccionada" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Información de cuenta</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>