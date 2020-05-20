<div class="modal fade" tabindex="-1" role="dialog" id="modal-gestionar-cuenta-bancaria">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="modal-gestionar-cuenta-bancaria-title"></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Banco</h5>
                        <select class="form-control group-elemento" name="banco" 
                            style="text-align:center;">
                            <option value="0" disabled>Elija una opción</option>
                            @foreach ($bancos as $bank)
                                <option value="{{$bank->id_banco}}">{{$bank->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <h5>Tipo de Cuenta</h5>
                        <select class="form-control group-elemento" name="tipo_cuenta_banco" 
                            style="text-align:center;">
                            <option value="0" disabled>Elija una opción</option>
                            @foreach ($tipo_cuenta_banco as $tcb)
                                <option value="{{$tcb->id_tipo_cuenta}}">{{$tcb->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <h5>N° Cuenta</h5>
                        <input class="form-control icd-okc" name="nro_cuenta" />
                    </div>
                    <div class="col-md-12">
                        <h5>N° Cuenta Interbancaria</h5>
                        <input class="form-control icd-okc" name="nro_cuenta_interbancaria" />
                    </div>
                </div>
 
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-8">
                        <div name="text-status" class="text-animate"></div>
                    </div>
                    <div class="col-md-4">
                        <label style="display: none;" id="id_cuenta_bancaria"></label>
                        <div id="btnAction_cuentas"></div>
                    </div>
                </div>
                

            </div>
        </div>
    </div>
</div>

