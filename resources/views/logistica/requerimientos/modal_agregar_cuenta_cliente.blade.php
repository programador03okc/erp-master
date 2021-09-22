<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-cuenta-cliente" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Agregar Cuenta a Cliente: <span id="razon_social"></span></h3>
            </div>
            <div class="modal-body">
                <div class="row" >
                    <div  id="input-group-cuenta-cliente-juridica" required>
                    <input type="text" name="id_cliente" class="oculto" />
                        <div class="col-md-4">
                            <h5>Banco</h5>
                            <select class="form-control activation" name="banco">
                                @foreach ($bancos as $banco)
                                    <option value="{{$banco->id_banco}}">{{$banco->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Tipo de Cuenta</h5>
                            <select class="form-control activation" name="tipo_cuenta">
                                @foreach ($tipos_cuenta as $tipo)
                                    <option value="{{$tipo->id_tipo_cuenta}}">{{$tipo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Moneda</h5>
                            <select class="form-control activation" name="moneda">
                                @foreach ($monedas as $moneda)
                                    <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <h5>Nro Cuenta</h5>
                            <input type="text" name="nro_cuenta" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h5>Nro Cuenta Interbancaria</h5>
                        <input type="text" name="cci" class="form-control" required/>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                    <input type="button" class="btn btn-success boton" onClick="guardarCuentaCliente();" value="Guardar"/>
            </div>
        </div>
    </div>
</div>