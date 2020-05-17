<div class="modal fade" tabindex="-1" role="dialog" id="modal-doc_create">
    <div class="modal-dialog">
        <div class="modal-content" style="width:450px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Generar Comprobante de Compra</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Tipo de Documento</h5>
                        <select class="form-control activation js-example-basic-single" name="id_tp_doc">
                            <option value="0">Elija una opción</option>
                            @foreach ($tp_doc as $tp)
                                <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Serie-Número</h5>
                        <div class="input-group">
                            <input type="text" class="form-control activation" 
                                name="serie_doc" placeholder="F001">
                            <span class="input-group-addon">-</span>
                            <input type="text" class="form-control activation" 
                                name="numero_doc" onBlur="ceros_numero_doc();" placeholder="000000">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Fecha de Emisión</h5>
                        <input type="date" class="form-control activation" name="fecha_emision_doc">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Proveedor</h5>
                        <select class="form-control js-example-basic-single" name="id_proveedor">
                            <option value="0">Elija una opción</option>
                            @foreach ($proveedores as $prov)
                                <option value="{{$prov->id_proveedor}}">{{$prov->nro_documento}} - {{$prov->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Motivo</h5>
                        <textarea name="motivo" id="motivo" cols="56" rows="3"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Importe</h5>
                        <div style="display:flex;">
                            <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" readOnly/>
                            <input type="number" name="importe" class="form-control group-elemento" style="text-align: right;" />
                            <select class="form-control group-elemento activation" name="moneda" onChange="moneda();">
                                <option value="0">Elija una opción</option>
                                @foreach ($monedas as $mon)
                                    <option value="{{$mon->id_moneda}}">{{$mon->descripcion}} - {{$mon->simbolo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label id="mid_doc_com" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="guardar_doc_create();">Guardar</button>
            </div>
        </div>
    </div>
</div>