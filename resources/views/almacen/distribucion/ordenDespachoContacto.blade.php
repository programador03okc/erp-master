<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_create" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 600px;">
        <div class="modal-content">
            <form id="form-orden_despacho">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Datos de Contacto - <label id="codigo_req" ></label>
                        </h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <input type="text" class="oculto" name="id_contacto"/>
                    <input type="text" class="oculto" name="id_contribuyente"/>
                    
                    {{-- <h4  style="display:flex;justify-content: space-between;">Priorización de Despacho</h4>
                    <fieldset class="group-table">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5>Fecha Facturación</h5>
                                        <input type="text" class="form-control date-picker" style="font-size:16px;background-color:#d2fafa;" name="fecha_facturacion">
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Fecha Despacho</h5>
                                        <input type="text" class="form-control date-picker" style="font-size:16px;background-color:#d2fafa;" name="fecha_despacho">
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Hora Despacho</h5>
                                        <input type="time" class="form-control" style="font-size:16px;background-color:#d2fafa;" name="hora_despacho">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                    <label style="font-size: 15px;">Código del Requerimiento:  </label> 
                                    <span id="codigo_req"  style="font-size: 15px;"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label style="font-size: 15px;">Concepto del Requerimiento:  </label> 
                                        <span id="concepto"  style="font-size: 12px;"></span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Comentario sobre la Facturación</h5>
                                <input type="text" class="form-control" style="background-color:#d2fafa;" name="obs_facturacion">
                            </div>
                        </div>
                    </fieldset> --}}
                    {{-- <h4  style="display:flex;justify-content: space-between;">Datos del Contacto</h4>
                    <fieldset class="group-table"> --}}
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <h5>Almacén</h5>
                                <input type="text" class="oculto" name="id_almacen" >
                                <input type="text" class="form-control" name="almacen_descripcion" readOnly>
                            </div>
                            <div class="col-md-6">
                                <h5>Cliente / Entidad</h5>
                                <div style="display:flex;"> 
                                    <input type="text" class="oculto" name="id_cliente" >
                                    <input type="text" class="form-control" name="cliente_ruc" style="display: none; width: 130px;" readOnly>
                                    <input type="text" class="form-control" name="cliente_razon_social" style="display: none;" readOnly>

                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Nombre Completo *</h5>
                                <input type="text" class="form-control" name="nombre" >
                            </div>
                            <div class="col-md-6">
                                <h5>Teléfono *</h5>
                                <input type="text" class="form-control" name="telefono" >
                            </div>
                        </div>
                        {{-- <div class="col-md-2">
                            <div class="form-group ">
                                <div class="radio">
                                    <label>
                                    <input type="radio" name="optionsRadios" id="Boleta" value="Boleta" checked="">
                                    Boleta
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                    <input type="radio" name="optionsRadios" id="Factura" value="Factura">
                                    Factura
                                    </label>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Dirección</h5>
                                <input type="text" class="form-control" name="direccion" >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Ubigeo Destino</h5>
                                <div style="display:flex;">
                                    <input class="oculto" name="ubigeo"/>
                                    <input type="text" class="form-control" name="name_ubigeo" readOnly>
                                    <button type="button" class="input-group-text btn-primary" id="basic-addon1" onClick="ubigeoModal();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Correo electrónico</h5>
                                <input type="text" class="form-control" name="email" >
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Horario de atención</h5>
                                <input type="text" class="form-control" name="horario" >
                            </div>
                            <div class="col-md-6">
                                <h5>Cargo</h5>
                                <input type="text" class="form-control" name="cargo" >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>* Campos obligatorios</h5>
                            </div>
                        </div>
                            {{-- <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Comentarios</h5>
                                        <textarea class="form-control" name="contenido" id="contenido" cols="73" rows="5"></textarea>
                                    </div>
                                </div>
                            </div> --}}
                    {{-- </fieldset> --}}
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_orden_despacho" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>