<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_create" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 1000px;">
        <div class="modal-content">
            <form id="form-orden_despacho">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title blue"><strong>Orden de Despacho Externo </strong>
                        </h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <input type="text" class="oculto" name="id_sede"/>
                    <input type="text" class="oculto" name="id_cc"/>
                    <input type="text" class="oculto" name="tiene_transformacion"/>
                    <input type="date" class="oculto" name="fecha_entrega"/>
                    
                    <h4  style="display:flex;justify-content: space-between;">Priorización de Despacho</h4>
                    <fieldset class="group-table">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Fecha Despacho</h5>
                                        <input type="date" class="form-control" name="fecha_despacho">
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Hora Despacho</h5>
                                        <input type="time" class="form-control" name="hora_despacho">
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
                            
                    </fieldset>
                    <h4  style="display:flex;justify-content: space-between;">Datos del Despacho</h4>
                    <fieldset class="group-table">
                        <div class="row">
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

                                    <input type="text" class="oculto" name="id_persona" >
                                    <input type="text" class="form-control" name="dni_persona" style="width: 130px;" readOnly>
                                    <input type="text" class="form-control" name="nombre_persona" readOnly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Persona Contacto</h5>
                                <input type="text" class="form-control" name="persona_contacto" >
                            </div>
                            <div class="col-md-4">
                                <h5>Ubigeo Destino</h5>
                                <div style="display:flex;">
                                    <input class="oculto" name="ubigeo"/>
                                    <input type="text" class="form-control" name="name_ubigeo" readOnly>
                                    <button type="button" class="input-group-text btn-primary" id="basic-addon1" onClick="ubigeoModal();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Dirección Destino</h5>
                                        <input type="text" class="form-control" name="direccion_destino" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5>Correo Cliente</h5>
                                        <input type="text" class="form-control" name="correo_cliente" >
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Teléfono</h5>
                                        <input type="text" class="form-control" name="telefono_cliente" >
                                    </div>
                                    <input class="oculto" name="aplica_cambios_valor"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Agregar Contenido Adicional al Correo</h5>
                                        <textarea class="form-control" name="contenido" id="contenido" cols="73" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_orden_despacho" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>