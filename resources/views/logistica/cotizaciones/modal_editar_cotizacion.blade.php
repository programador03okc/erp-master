<div class="modal fade" tabindex="-1" role="dialog" id="modal-editar-cotizacion">
    <div class="modal-dialog" style="width:85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="title-editar-cotizacion">Editar</h3> 
            </div>
            <div class="modal-body">
            <div class="panel panel-default">
                <div class="panel-heading">Cotización</div>
                <div class="panel-body">

                    <form id="form-editar-cotizacion">
                        <div class="row">
                            <div class="col-md-5">
                                <h5>Empresa</h5>
                                <div style="display:flex;">
                                    <select class="form-control" name="id_empresa" onChange="onChangeEmpresaModalEditarCotizacion();" disabled>
                                        <option value="0" disabled>Elija una opción</option>
                                        @foreach ($empresas as $emp)
                                            <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <img id="img" class="imagen" style="width:200px">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <h5>Codigo</h5>
                                <div class="input-group-okc">
                                    <input type="hidden" class="form-control" name="id_cotizacion" disabled>
                                    <input type="text" class="form-control" name="codigo_cotizacion" placeholder="Código Cotización" disabled>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <h5>Proveedor</h5>
                                <div style="display:flex;">
                                    <input class="oculto" name="id_proveedor"/>
                                    <input class="oculto" name="id_contrib"/>
                                    <input type="text" class="form-control" name="razon_social" placeholder="Seleccione un proveedor..." 
                                        onChange="change_proveedor();" aria-describedby="basic-addon1" >
                                    <button type="button" class="input-group-text" onClick="proveedorModal();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <button type="button" class="btn-primary" title="Agregar Proveedor" onClick="agregar_proveedor();">
                                        <i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5>Contacto</h5>
                                <div style="display:flex;">
                                    <select class="form-control" name="id_contacto" onChange="onChangeContactoModalEditarCotizacion()"></select>
                                    <button type="button" class="btn-primary" title="Agregar Contacto" onClick="agregar_contacto();">
                                        <i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Agregar Adjuntos a Cotización</h5>
                                <div style="display:flex;">
                                    <input type="file" id="nombre_archivo_coti_editar" class="custom-file-input"  onchange="agregarAdjuntoCotizacion(event); return false;" />
                                    <div class="input-group-append">
                                        <button
                                            id="btnUploadFileCotiEditar"
                                            type="button"
                                            class="btn btn-info hidden"
                                            onClick="guardarAdjuntoCoti();"
                                            
                                            ><i class="fas fa-file-upload"></i> Subir Archivo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title" style="position:relative">
                                    <strong>Items - Requerimientos</strong>
                                    <div class="btn-group" role="group" style="margin-top: -6px; right:0px; position:absolute;">
                                    <button
                                        type="button"
                                        class="btn btn-success btn-sm"
                                        id="btnAgregarItemACotizacion"
                                        title="Agregar Item"
                                        onclick="agregarItemACotizacion();"
                                    >
                                        <i class="fas fa-plus fa-xs"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        id="btnEliminarItemDeCotizacion"
                                        title="Eliminar Item"
                                        onclick="eliminarItemDeCotizacion();"
                                        disabled
                                    >
                                        <i class="fas fa-trash fa-xs"></i></button
                                    >
                                </div>
                            </h4>                    
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaItemsRequerimientoModalEditCoti" width="100%"> 
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th width="20">#</th>
                                            <th width="20">Check</th>
                                            <th width="120">COD.REQ.</th>
                                            <th width="120">COD. ITEM</th>
                                            <th width="400">DESCRIPCIÓN</th>
                                            <th width="100">UNIDAD</th>
                                            <th width="100">CANTIDAD REQ.</th>
                                            <th width="100">CANTIDAD COTI.</th>
                                            <th width="100">PRECIO REF.</th>
                                            <th width="100">FECHA ENTREGA</th>
                                            <th width="100">LUGAR ENTREGA</th>
                                            <th width="200">ACCIÓN</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>                    
                    </div>


                        <br>
                        <p>Todo los Archivos Adjuntos:</p>
                        <div class="container row">
                            <div class="col-md-9">
                                <div class="mailbox-attachment-info" id="attachment-container-editar-cotiza"></div>
                            </div>
                        </div>

                        <br>
                    </form>




                    </div>

                </div>
            </div>


            </div>
        </div>
    </div>
</div>


@include('logistica.cotizaciones.modal_agregar_item_req_a_cotiza')
