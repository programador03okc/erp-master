<div class="modal fade" tabindex="-1" role="dialog" id="modal-cotizacion_proveedor" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Cotización
                        <h5 id="codigo_cotizacion" style="padding:12px;margin:0px;"></h5>
                    </h3>
                </div>
            </div>
            <div class="modal-body">
                <form id="cotizacion_proveedor" method="post">
                    <div class="row">
                        <input class="oculto" name="id_cotizacion"/>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Empresa</h5>
                                    <div style="display:flex;">
                                        <select class="form-control activation" name="id_empresa" onChange="cargar_imagen();" required>
                                            <option value="0" disabled>Elija una opción</option>
                                            @foreach ($empresas as $emp)
                                                <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <img id="img" class="imagen">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Proveedor</h5>
                                    <div style="display:flex;">
                                        <input class="oculto" name="id_proveedor"/>
                                        <input class="oculto" name="id_contrib"/>
                                        <input type="text" class="form-control" name="razon_social" disabled placeholder="Seleccione un proveedor..." 
                                            onChange="change_proveedor();" aria-describedby="basic-addon1" required>
                                        <button type="button" class="input-group-text" id="basic-addon1" onClick="proveedorModal();">
                                            <i class="fa fa-search"></i>
                                        </button>
                                        <button type="button" class="btn-primary" title="Agregar Proveedor" onClick="agregar_proveedor();">
                                            <i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Email - Proveedor</h5>
                                    <div style="display:flex;">
                                        <select class="form-control activation" name="id_contacto" required></select>
                                        <button type="button" class="btn-primary" title="Agregar Contacto" onClick="agregar_contacto();">
                                            <i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" onClick="action_cotizacion('UPDATE');">Actualizar</button>
                <button class="btn btn-sm btn-success" onClick="action_cotizacion('DUPLICATE');">Agregar</button>
            </div>
        </div>
    </div>  
</div>
