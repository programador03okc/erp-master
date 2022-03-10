<div class="modal fade" tabindex="-1" role="dialog" id="modal-procesarPago" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Registro del pago - <label name="cod_serie_numero"></label></h3>
                </div>
            </div>
            <form id="form-procesarPago" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento_pago" />
                    <input type="text" class="oculto" name="id_oc" />
                    <input type="text" class="oculto" name="id_doc_com" />
                    <input type="text" class="oculto" name="codigo" />
                    <input type="text" class="oculto" name="total" />

                    <fieldset class="group-table" id="fieldsetDatosProveedor">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Datos del documento</strong></h5>
                        <div class="row">
                            <div class="col-md-2">
                                <span style="font-size: 14px;">Empresa: </span>
                            </div>
                            <div class="col-md-3">
                                <label style="font-size: 14px;" name="empresa_razon_social"></label>
                            </div>
                            <div class="col-md-2">
                                <span style="font-size: 14px;" name="titulo_motivo">Motivo: </span>
                            </div>
                            <div class="col-md-5">
                                <label style="font-size: 14px;" name="motivo"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <span style="font-size: 14px;">Monto total: </span>
                            </div>
                            <div class="col-md-3">
                                <label style="font-size: 14px;" name="monto_total"></label>
                            </div>
                            <div class="col-md-2">
                                <span style="font-size: 14px;">Total pagado: </span>
                            </div>
                            <div class="col-md-5">
                                <label style="font-size: 14px;" name="total_pagado"></label>
                            </div>
                        </div>
                    </fieldset>
                    <br>

                    <fieldset class="group-table" id="fieldsetDatosProveedor">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Datos del destinatario</strong></h5>
                        <div class="row">
                            <div class="col-md-2">
                                <span style="font-size: 14px;">Nro. documento: </span>
                            </div>
                            <div class="col-md-3">
                                <label style="font-size: 14px;" name="nro_documento"></label>
                            </div>
                            <div class="col-md-2">
                                <span style="font-size: 14px;">Destinatario: </span>
                            </div>
                            <div class="col-md-5">
                                <label style="font-size: 14px;" name="razon_social"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <span style="font-size: 14px;">Tipo de cuenta: </span>
                            </div>
                            <div class="col-md-3">
                                <label style="font-size: 14px;" name="tp_cta_bancaria"></label>
                            </div>
                            <div class="col-md-2">
                                <span style="font-size: 14px;">Banco: </span>
                            </div>
                            <div class="col-md-5">
                                <label style="font-size: 14px;" name="banco"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <span style="font-size: 14px;">Cuenta bancaria: </span>
                            </div>
                            <div class="col-md-3">
                                <label style="font-size: 14px;" name="cta_bancaria"></label>
                            </div>
                            <div class="col-md-2">
                                <span style="font-size: 14px;">Cuenta CCI: </span>
                            </div>
                            <div class="col-md-5">
                                <label style="font-size: 14px;" name="cta_cci"></label>
                            </div>
                        </div>
                    </fieldset>
                    <br>
                    <fieldset class="group-table" id="fieldsetDatosPago">
                        <h5 style="display:flex;justify-content: space-between;"><strong>Datos del pago</strong></h5>
                        <div class="row">
                            <div class="col-md-3">
                                <h5>Fecha del Pago</h5>
                                <input type="date" class="form-control" name="fecha_pago" value="<?= date('Y-m-d'); ?>" required />
                            </div>
                            <div class="col-md-6">
                                <h5>Cuenta origen</h5>
                                <div style="display:flex;">
                                    <select class="form-control js-example-basic-single" name="id_empresa" id="id_empresa" 
                                        onchange="listarCuentasOrigen()" required>
                                        <option value="">Elija una opción</option>
                                        @foreach ($empresas as $empresa)
                                        <option value="{{$empresa->id_empresa}}">{{$empresa->razon_social}}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-control js-example-basic-single" name="id_cuenta_origen" id="id_cuenta_origen" required>
                                    </select>
                                    {{-- <input type="text" class="form-control" name="cuenta_origen"  /> --}}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h5>Total a pagar</h5>
                                <div style="display:flex;">
                                    <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" readOnly />
                                    <input type="number" class="form-control right celestito" name="total_pago" step="0.01" required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Motivo</h5>
                                <textarea name="observacion" id="observacion" class="form-control" style="height: 60px;" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Adjuntar Archivo</h5>
                                {{-- <input type="file" name="adjunto" id="adjunto" class="filestyle" data-buttonName="btn-primary" data-buttonText="Adjuntar" data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false"> --}}
                                <input type="file" name="archivos[]" multiple="true" class="form-control">
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_procesarPago" class="btn btn-success" value="Registrar pago" />
                </div>
            </form>
        </div>
    </div>
</div>