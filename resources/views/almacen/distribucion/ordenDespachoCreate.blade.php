<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_create" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 1000px;">
        <div class="modal-content">
            <form id="form-orden_despacho">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Orden de Despacho Externo</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <input type="text" class="oculto" name="id_sede"/>
                    <input type="text" class="oculto" name="id_cc"/>
                    <input type="text" class="oculto" name="tiene_transformacion"/>
                    <input type="date" class="oculto" name="fecha_entrega"/>
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

                                <button type="button" title="Seleccionar Cliente" name="btnCliente" 
                                onClick="openCliente();" class="input-group-text btn-primary" >
                                <i class="fas fa-user-tie"></i></button>

                                <button type="button" class="btn-success" title="Agregar Cliente" name="btnAddCliente" 
                                onClick="agregar_cliente();">
                                <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <h5>Tipo Cliente</h5>
                            <div class="input-group-okc">
                                <select name="tipo_cliente" onChange="changeTipoCliente(event);"
                                    class="form-control" style="width:100px" required>
                                    <option value="1" default>Persona Natural</option>
                                    <option value="2">Persona Juridica</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h5>Persona Contacto</h5>
                            <input type="text" class="form-control" name="contacto_cliente" readOnly>
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
                                <!-- <div class="col-md-3">
                                    <h5>Tipo de Entrega</h5>
                                    <select class="form-control" name="tipo_entrega">
                                        <option value="MISMA CIUDAD">MISMA CIUDAD</option>
                                        <option value="OTRAS CIUDADES">OTRAS CIUDADES</option>
                                    </select>
                                </div> -->
                                <input class="oculto" name="aplica_cambios_valor"/>
                                <input class="oculto" type="checkbox" name="aplica_cambios" id="aplica_cambios" 
                                style="margin-right: 10px; margin-left: 7px;"/> 
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
                    
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_orden_despacho" class="btn btn-success" value="Guardar y Enviar"/>
                </div>
            </form>
        </div>
    </div>
</div>