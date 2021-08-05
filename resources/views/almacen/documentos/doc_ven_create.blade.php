<div class="modal fade" tabindex="-1" role="dialog" id="modal-doc_ven_create" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:1200px;">
        <div class="modal-content">
            <form id="form-doc_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Generar Documento de Venta</h3>
                </div>
                <div class="modal-body">
                    <input type="text" style="display:none;" name="id_requerimiento">
                    <input type="text" style="display:none;" name="id_almacen_doc">

                    <div class="row">
                        <div class="col-md-3">
                            <h5>Empresa-Sede</h5>
                            <select class="form-control js-example-basic-single" name="id_sede">
                                <option value="0">Elija una opción</option>
                                @foreach ($sedes as $sede)
                                <option value="{{$sede->id_sede}}">{{$sede->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Tipo de Documento</h5>
                            <select class="form-control js-example-basic-single" name="id_tp_doc">
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_doc as $tp)
                                <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" name="serie_doc" placeholder="F001" required>
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" name="numero_doc" onBlur="ceros_numero_doc();" required placeholder="000000">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control" name="fecha_emision_doc">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>RUC Cliente</h5>
                            <input type="text" style="display:none;" name="id_cliente">
                            <input type="text" style="display:none;" name="id_cta_principal">
                            <input type="text" class="form-control" name="cliente_ruc" readOnly>
                        </div>
                        <div class="col-md-3">
                            <h5>Razon Social Cliente</h5>
                            <input type="text" class="form-control" name="cliente_razon_social" readOnly>
                        </div>
                        <div class="guia col-md-3">
                            <h5>Serie-Número (Guía)</h5>
                            <div class="input-group">
                                <input type="text" style="display:none;" name="id_guia">
                                <input type="text" class="form-control" name="serie_guia" readOnly>
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" name="numero_guia" readOnly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Moneda</h5>
                            <div style="display:flex;">
                                <select class="form-control group-elemento" name="moneda" onChange="changeMoneda();">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($monedas as $mon)
                                    <option value="{{$mon->id_moneda}}" data-sim="{{$mon->simbolo}}">{{$mon->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="ocam col-md-3">
                            <h5>Importe Total segun OCAM</h5>
                            <div style="display:flex;">
                                <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" readOnly />
                                <input type="text" name="importe_oc" class="form-control group-elemento" style="text-align: right;" readOnly />

                            </div>
                        </div>
                        <!-- <div class="col-md-3">
                            <h5>Condición de compra</h5>
                            <div style="display:flex;">
                                <select class="form-control group-elemento" name="id_condicion" style="width:150px;">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($condiciones as $con)
                                        <option value="{{$con->id_condicion_pago}}">{{$con->descripcion}}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="credito_dias" class="form-control group-elemento" style="text-align: right;"/>
                                <input type="text" class="form-control group-elemento" style="width:50px;text-align:center;" value="días" readOnly/>
                            </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleItems" style="margin-top:10px; margin-bottom: 0px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Guía/Req</th>
                                        <th>Código</th>
                                        <th>PartNumber</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unid</th>
                                        <th width="110px">Unitario</th>
                                        <th>Sub Total</th>
                                        <th width="110px">% Dscto</th>
                                        <th width="110px">Dcsto</th>
                                        <th>Total
                                            <!-- <i class="fas fa-plus-circle icon-tabla green boton agregarServicio" data-toggle="tooltip" data-placement="bottom" title="Agregar Servicio" onClick="agregarServicio();"></i> -->
                                        </th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_doc_ven_create" class="btn btn-success" value="Guardar" />
                </div>
            </form>
        </div>
    </div>
</div>