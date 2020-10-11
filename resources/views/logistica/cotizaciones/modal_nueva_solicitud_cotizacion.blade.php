<div class="modal fade" tabindex="-1" role="dialog" id="modal-nueva-solicitud-cotizacion">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Nueva Solicitud de Cotización</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form-nueva-solicitud-cotizacion" method="post">

                        <div class="row">
                            <div class="col-md-8">
                                <h5>Empresa</h5>
                                <div style="display:flex;">
                                    <select class="form-control" name="id_empresa" onChange="cargar_imagen(); handleChangeFilterCrearCotiByEmpresa(event);">
                                        <option value="0" disabled>Elija una opción</option>
                                        @foreach ($empresas as $emp)
                                            <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5>Sede</h5>
                                <div style="display:flex;">
                                    <select class="form-control" id="id_sede_crear_coti" >
                                        <option value="0" disabled>Elija una opción</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Proveedor</h5>
                                <div style="display:flex;">
                                    <input class="oculto" name="id_proveedor"/>
                                    <input class="oculto" name="id_contrib"/>
                                    <input type="text" class="form-control" name="razon_social" placeholder="Seleccione un proveedor..." 
                                        onChange="change_proveedor();" aria-describedby="basic-addon1" >
                                    <button type="button" class="input-group-text" id="basic-addon11" onClick="proveedorModal();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <button type="button" class="btn-primary" title="Agregar Proveedor" onClick="agregar_proveedor();">
                                        <i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5>Contacto</h5>
                                <div style="display:flex;">
                                    <select class="form-control" name="id_contacto" ></select>
                                    <button type="button" class="btn-primary" title="Agregar Contacto" onClick="agregar_contacto();">
                                        <i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>   
                </div> 
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" onClick="generar_cotizacion();">
                        Generar Solicitud de Cotización <i class="fas fa-save"></i>
                    </button>
            </div>
        </div>
    </div>
</div>