<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden_despacho_contacto" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 800px;">
        <div class="modal-content">
            <form id="form-orden_despacho_contacto">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Datos de Contacto <label id="codigo_req" ></label> <span class="label limpiar" id="enviado"></span></h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <input type="text" class="oculto" name="id_contribuyente"/>
                    <input type="text" class="oculto" name="id_entidad"/>
                    <input type="text" class="oculto" name="id_contacto_od"/>
                    
                    <fieldset class="group-table" id="fieldsetDetallesEntidad">
                        
                        <h5 style="display:flex;justify-content: space-between;"><strong>Datos de la Entidad / Cliente</strong>
                            {{-- <div>
                                <span class="label limpiar" id="enviado"></span>
                            </div> --}}
                        </h5>
                        <div class="row">
                            <div class="col-sm-6">
                                {{-- <fieldset style="margin-bottom: 10px;"> --}}
                                <div class="form-horizontal">
                                    <div class="form-group" style="margin-bottom:0px;">
                                        <label class="col-sm-3 control-label">DNI/RUC</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static limpiar ruc"></div>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-bottom:0px">
                                        <label class="col-sm-3 control-label">Nombre</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static limpiar nombre"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                    <div class="form-horizontal">
                                        
                                        <div class="form-group" style="margin-bottom:0px">
                                            <label class="col-sm-3 control-label">Dirección</label>
                                            <div class="col-sm-8">
                                                <div class="form-control-static limpiar direccion"></div>
                                            </div>
                                        </div>
                                        <div class="form-group" style="margin-bottom:0px">
                                            <label class="col-sm-3 control-label">Ubigeo</label>
                                            <div class="col-sm-8">
                                                <div class="form-control-static limpiar ubigeo"></div>
                                            </div>
                                        </div>
                                    </div>
                                {{-- </fieldset> --}}
                            </div>
                            
                        </div>
                    </fieldset>
                    <br>
                    <fieldset class="group-table" id="fieldsetListaContactos">
                        <div class="row">
                            <div class="col-md-12">
                                <h5><strong>Contactos</strong></h5>
                                <table class="mytable table table-condensed table-bordered table-hover table-striped table-okc-view" 
                                    id="listaContactos">
                                    <thead>
                                        <tr>
                                            <th style="width:5%">Sel.</th>
                                            <th>Nombre</th>
                                            <th style="width:10%">Teléfono</th>
                                            <th style="width:15%">Cargo</th>
                                            <th style="width:10%">Correo</th>
                                            <th style="width:15%">Dirección</th>
                                            <th style="width:10%">Horario</th>
                                            <th style="width:10%">
                                                <button type="button" class="addContacto btn btn-success btn-flat btn-xs boton" 
                                                    data-toggle="tooltip" data-placement="bottom" onClick="agregarContacto();" title="Agregar contacto">
                                                    <i class="fas fa-plus"></i></button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    <br>
                    <fieldset class="group-table" id="fieldsetCorreoLicencia">
                        <div class="row">
                            <div class="col-md-12">
                                <h5><strong>Correo para licencias</strong></h5>
                                <input type="text" class="form-control limpiar" name="correo_licencia">
                            </div>
                        </div>
                    </fieldset>
                </div>
            </form>
            <div class="modal-footer">
                <button id="btn_cerrar" class="btn btn-default" onClick="cerrarContacto();">Cerrar</button>
                <button id="btn_enviar_correo" class="btn btn-success" onClick="abrirVistaPreviaCorreo();">Enviar contacto</button>
                {{-- <input type="submit" id="submit_enviar_correo" class="btn btn-success" value="Enviar correo"/> --}}
            </div>
        </div>
    </div>
</div>