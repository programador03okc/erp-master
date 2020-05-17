<div class="modal fade" tabindex="-1" role="dialog" id="modal-gestionar-contacto">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="modal-gestionar-contacto-title"></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Nombre</h5>
                        <input class="form-control icd-okc" name="nombre" />
                    </div>
                    <div class="col-md-12">
                        <h5>Telefono</h5>
                        <input class="form-control icd-okc" name="telefono_contacto" />
                    </div>
                    <div class="col-md-12">
                        <h5>Email</h5>
                        <input class="form-control icd-okc" name="email" />
                    </div>
                    <div class="col-md-12">
                        <h5>Cargo</h5>
                        <input class="form-control icd-okc" name="cargo" />
                    </div>
                    <div class="col-md-12">
                        <h5>Establecimiento</h5>

                        <select class="form-control group-elemento" name="establecimiento_contacto" 
                            style="text-align:center;">
                            <!-- <option value="0" disabled>Elija una opción</option>
                            @foreach ($bancos as $bank)
                                <option value="{{$bank->id_banco}}">{{$bank->razon_social}}</option>
                            @endforeach -->
                        </select>
                    </div>
                </div>
 
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-8">
                        <div name="text-status" class="text-animate"></div>
                    </div>
                    <div class="col-md-4">
                        <label style="display: none;" id="id_datos_contacto"></label>
                        <div id="btnAction_contactos"></div>
                    </div>
                </div>
                

            </div>
        </div>
    </div>
</div>

