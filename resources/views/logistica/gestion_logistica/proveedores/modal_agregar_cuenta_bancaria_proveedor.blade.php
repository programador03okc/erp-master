<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-cuenta-bancaria-proveedor" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 400px;">
        <div class="modal-content">
            <form id="form-agregar-cuenta-bancaria-proveedor" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Agregar cuenta bancaria</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="form-control-static"><strong>Proveedor:</strong> <span id="razon_social_proveedor"></span></p>
                        </div>
    
                        <div class="col-md-12">
                            <input class="oculto" name="id_proveedor">
                            <h5>Banco</h5>
                            <select class="form-control group-elemento" name="banco" 
                                style="text-align:center;">
                                <option value="0" disabled>Elija una opción</option>
                                @foreach ($bancos as $banco)
                                    <option value="{{$banco->id_banco}}">{{$banco->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <h5>Tipo de Cuenta</h5>
                            <select class="form-control group-elemento" name="tipo_cuenta_banco" 
                                style="text-align:center;">
                                <option value="0" disabled>Elija una opción</option>
                                @foreach ($tipo_cuenta as $tipo)
                                    <option value="{{$tipo->id_tipo_cuenta}}">{{$tipo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <h5>Moneda</h5>
                            <select class="form-control group-elemento" name="moneda" 
                                style="text-align:center;">
                                @foreach ($monedas as $moneda)
                                    <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <h5>N° Cuenta</h5>
                            <input type="text" class="form-control icd-okc" name="nro_cuenta" />
                        </div>
                        <div class="col-md-12">
                            <h5>N° Cuenta Interbancaria</h5>
                            <input type="text" class="form-control icd-okc" name="nro_cuenta_interbancaria" />
                        </div>
                        <div class="col-md-12">
                            <h5>SWIFT</h5>
                            <input type="text" class="form-control icd-okc" name="swift" />
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success boton" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>

