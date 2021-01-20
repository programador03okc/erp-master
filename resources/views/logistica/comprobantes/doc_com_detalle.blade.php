<div class="modal fade" tabindex="-1" role="dialog" id="modal-doc_compra_detalle" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Detalle del Comprobante</h3>
                </div>
            </div>
            <form id="form-doc_compra_detalle"  enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Código</h5>
                            <input class="oculto" name="id_doc_det">
                            <input type="text" name="codigo" class="form-control" disabled="true">
                        </div>
                        <div class="col-md-6">
                            <h5>Descripción</h5>
                            <input type="text" name="descripcion" class="form-control" disabled="true">
                        </div>
                        <div class="col-md-3">
                            <h5>Guía Compra</h5>
                            <input type="text" name="guia" class="form-control" disabled="true">
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-3">
                            <h5>Cantidad</h5>
                            <div style="display:flex;">
                                <input type="number" name="cantidad_d" class="form-control right" onChange="actualiza_total();">
                                <input type="text" name="abreviatura" class="form-control" disabled="true"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Precio Unitario </h5>
                            <input type="number" name="precio_unitario_d" class="form-control right" onChange="actualiza_total();"/>
                        </div>
                        <div class="col-md-3">
                            <h5>Descuento</h5>
                            <div style="display:flex;">
                                <input type="number" name="porcen_dscto_d" class="form-control right" onChange="actualiza_dscto();" style="width:60px;"/>
                                <span style="padding: 6px 4px;border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;">%</span>
                                <input type="number" name="total_dscto_d" class="form-control right" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>SubTotal</h5>
                            <input type="number" name="precio_total_d" class="form-control right">
                        </div>                    
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            <h5>Detracción </h5>
                            <select class="form-control" name="id_detraccion" required onChange="actualiza_detraccion();">
                                <option value="0" selected>Elija una opción</option>
                                @foreach ($detracciones as $det)
                                    <option value="{{$det->id_detra_det}}">{{$det->descripcion}} - {{$det->porcentaje}}%</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Total Detracción</h5>
                            <input type="number" name="total_detraccion" class="form-control right">
                        </div>                    
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            <h5>Impuesto de Ley </h5>
                            <select class="form-control" name="id_detraccion" required onChange="actualiza_detraccion();">
                                <option value="0" selected>Elija una opción</option>
                                @foreach ($impuestos as $det)
                                    <option value="{{$det->id_impuesto}}">{{$det->descripcion}} - {{$det->porcentaje}}%</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Total Impuesto</h5>
                            <input type="number" name="total_igv" class="form-control right">
                        </div>                    
                    </div> --}}
                    <div class="row">
                        <div class="col-md-12">
                            <table class="tabla-totales" width="100%">
                                <tbody>
                                    <tr>
                                        <td width="20%">SubTotal</td>
                                        <td width="60%" colSpan="2">
                                            <div style="display:flex;">
                                                <input type="number" name="cantidad_d" class="form-control right" onChange="actualiza_total();">
                                                <input type="text" name="abreviatura" class="form-control" disabled="true" style="width:70px;"/>
                                                <input type="number" name="precio_unitario_d" class="form-control right" onChange="actualiza_total();"/>
                                            </div>
                                        </td>
                                        <td><input type="number" class="importe" name="sub_total" disabled="true" value="0"/></td>
                                    </tr>
                                    <tr>
                                        <td>IGV</td>
                                        <td style="width:300px;"></td>
                                        <td>
                                            <input type="number" class="porcen activation" name="porcen_igv" disabled="true" value="0"/>
                                            <span style="padding: 6px 4px;">%</span>
                                        </td>
                                        <td><input type="number" class="importe" name="total_igv" disabled="true" value="0"/></td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td style="width:300px;"></td>
                                        <td></td>
                                        <td><input type="number" class="importe" name="total" disabled="true" value="0"/></td>
                                    </tr>
                                    <tr>
                                        <td>Detracción</td>
                                        <td colSpan="2">
                                            <div style="display:flex;">
                                                <select class="select" name="id_detraccion" required onChange="actualiza_detraccion();">
                                                    <option value="0" selected>Elija una opción</option>
                                                    @foreach ($detracciones as $det)
                                                        <option value="{{$det->id_detra_det}}">{{$det->descripcion}} - {{$det->porcentaje}}%</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" class="porcen activation" name="porcen_detra" disabled="true" value="0"/>
                                                <span style="padding: 6px 4px;">%</span>
                                            </div>
                                        </td>
                                        <td><input type="number" class="importe" name="total_detraccion" disabled="true" value="0"/></td>
                                    </tr>
                                    <tr>
                                        <td>Descuentos</td>
                                        <td style="width:300px;"></td>
                                        <td>
                                            <input type="number" class="porcen" name="porcen_dscto_d" onChange="actualiza_dscto();" value="0"/>
                                            <span style="padding: 6px 4px;">%</span>
                                        </td>
                                        <td><input type="number" class="importe" name="total_dscto_d" disabled="true" value="0"/></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colSpan="2"><strong>Importe Total</strong></td>
                                        <td></td>
                                        <td><input type="number" class="importe" name="precio_total_d" disabled="true" value="0"/></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_control();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
