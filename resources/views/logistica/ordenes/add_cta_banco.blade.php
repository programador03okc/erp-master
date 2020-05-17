<div class="modal fade" tabindex="-1" role="dialog" id="modal-cta_banco" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 300px;">
        <div class="modal-content">
            <form id="form-cta_banco" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                    aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title">Nuevo Cuenta Banco</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input class="oculto" name="id_cuenta_contribuyente">
                            <input class="oculto" name="id_contribuyente">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Banco</h5>
                                    <select class="form-control" name="id_banco" required>
                                        <option value="0" disabled>Elija una opción</option>
                                        @foreach ($bancos as $tp)
                                            <option value="{{$tp->id_banco}}">{{$tp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Tipo de Cuenta</h5>
                                    <select class="form-control" name="id_tipo_cuenta" required>
                                        <option value="0" disabled>Elija una opción</option>
                                        @foreach ($cuentas as $tp)
                                            <option value="{{$tp->id_tipo_cuenta}}">{{$tp->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Número de Cuenta</h5>
                                    <input type="text" class="form-control" name="nro_cuenta" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Número de Cuenta Interbancaria</h5>
                                    <input type="text" class="form-control" name="nro_cuenta_interbancaria" required/>
                                </div>
                            </div>
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
