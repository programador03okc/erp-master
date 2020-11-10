<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_create" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 1000px;">
        <div class="modal-content">
            <form id="form-orden_despacho">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Generar Orden de Despacho - <strong><span id="name_title"></span></strong></h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <input type="text" class="oculto" name="id_sede"/>
                    <input type="text" class="oculto" name="id_cc"/>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Almacén</h5>
                            <input type="text" class="oculto" name="id_almacen" >
                            <input type="text" class="form-control" name="almacen_descripcion" readOnly>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Despacho</h5>
                            <input type="date" class="form-control" name="fecha_despacho" value="<?=date('Y-m-d');?>">
                        </div>
                        <div class="col-md-3">
                            <h5>Hora de Despacho</h5>
                            <input type="time" class="form-control" name="hora_despacho">
                        </div>
                    </div>
                    <div id="despachoExterno">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Tipo Cliente</h5>
                                <div class="input-group-okc">
                                    <select name="tipo_cliente" onChange="changeTipoCliente(event);"
                                        class="form-control activation" style="width:100px" required>
                                        <option value="1" default>Persona Natural</option>
                                        <option value="2">Persona Juridica</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <h5>Teléfono</h5>
                                <input type="number" class="form-control" name="telefono_cliente">
                            </div>
                            <div class="col-md-6">
                                <h5>Cliente</h5>
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
                            <div class="col-md-6">
                                <h5>Dirección Destino</h5>
                                <input type="text" class="form-control" name="direccion_destino">
                            </div>
                            <div class="col-md-3">
                                <h5>Ubigeo Destino</h5>
                                <div style="display:flex;">
                                    <input class="oculto" name="ubigeo"/>
                                    <input type="text" class="form-control" name="name_ubigeo" readOnly>
                                    <button type="button" class="input-group-text btn-primary" id="basic-addon1" onClick="ubigeoModal();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h5>Última Fecha de Entrega</h5>
                                <input type="date" class="form-control" name="fecha_entrega" value="<?=date('Y-m-d');?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Correo Cliente</h5>
                                <input type="text" class="form-control" name="correo_cliente">
                            </div>
                            <div class="col-md-3">
                                <h5>Tipo de Entrega</h5>
                                <select class="form-control" name="tipo_entrega">
                                    <option value="MISMA CIUDAD">MISMA CIUDAD</option>
                                    <option value="OTRAS CIUDADES">OTRAS CIUDADES</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <h5>Elija el documento que desea emitir:</h5>
                                <div class="form-group">
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
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5></h5>
                            <input class="oculto" name="aplica_cambios_valor"/>
                            <input type="checkbox" name="aplica_cambios" style="margin-right: 10px; margin-left: 7px;"/> Aplica Cambios
                        </div>
                    </div>
                </div>
                <div id="detalleItemsReq">
                    <div class="modal-header" style="display:flex;padding-top: 0px;">
                        <h4 class="modal-title green"><i class="fas fa-arrow-circle-right green"></i> Ingresa: </h4>
                    </div>
                    <div class="modal-body" style="padding-bottom:0px;">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="checkbox" name="seleccionar_todos" style="margin-right: 10px; margin-left: 7px;"/> Seleccione todos los items
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                    id="detalleRequerimientoOD"  style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>PartNumber</th>
                                            <!-- <th>Categoría</th>
                                            <th>SubCategoría</th> -->
                                            <th>Descripción</th>
                                            <!-- <th>Almacén Reserva</th> -->
                                            <th>Cant.</th>
                                            <th>Unid</th>
                                            <th>Ingresado</th>
                                            <th>Despachado</th>
                                            <th>Cant.Despacho</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-header" style="display:flex;padding-top: 0px;">
                        <h4 class="modal-title red"><i class="fas fa-arrow-circle-left red"></i> Sale: </h4>
                    </div>
                    <div class="modal-body" style="padding-top:0px;padding-bottom:0px;">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                    id="detalleSale"  style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>PartNumber</th>
                                            <th>Descripción</th>
                                            <th>Cant.</th>
                                            <th>Unid</th>
                                            <th style="background: white;width: 40px;padding: 0px;">
                                                <i class="fas fa-plus icon-tabla green boton" 
                                                    data-toggle="tooltip" data-placement="bottom" 
                                                    title="Agregar Producto" onClick="productoModal();"></i>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <!-- <textarea name="sale" id="sale" cols="137" rows="5"></textarea> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button class="btn btn-sm btn-success" id="submit_orden_despacho" onClick="guardar_orden_despacho();" >Guardar y Enviar <i class="fas fa-paper-plane"></i> </button> -->
                    <!-- &nbsp;<img width="10px" src="{{ asset('images/loading.gif')}}" class="loading invisible"><img> -->
                    <input type="submit" id="submit_orden_despacho" class="btn btn-success" value="Guardar y Enviar"/>
                </div>
            </form>
        </div>
    </div>
</div>