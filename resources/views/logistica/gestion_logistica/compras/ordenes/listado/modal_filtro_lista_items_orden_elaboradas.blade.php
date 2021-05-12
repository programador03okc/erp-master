<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-lista-items-orden-elaboradas">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Filtros</h3>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="formFiltroListaItemsOrdenElaboradas">
                    <div class="row">
                        <div class="col-md-12">
                            <small>Seleccione los filtros que desee aplicar y haga clic en aceptar para ejecutar los filtros marcados con check</small>
                        </div>
                    </div>
                    <br>
                    <div class="container-filter" style="margin: 0 auto;">
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Tipo orden">
                                    <input type="checkbox" name="chkTipoOrden" onclick="listaOrdenView.chkTipoOrden(event)">&nbsp; Tipo Orden
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="tipoOrden" disabled>
                                    <option value="2">Orden de Compra</option>
                                    <option value="3">Orden de Servicio</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Vinculado por">
                                    <input type="checkbox" name="chkVinculadoPor" onclick="listaOrdenView.chkVinculadoPor(event)" >&nbsp; Vinculado por
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="vinculadoPor" disabled>
                                    <option value="CUALQUIERA">Cualquier documento</option>
                                    <option value="REQUERIMIENTO">Requerimiento</option>
                                    <!-- <option value="CUADRO_COMPARATIVO">Cuadro comparativo</option> -->
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Empresa">
                                    <input type="checkbox" name="chkEmpresa" onclick="listaOrdenView.chkEmpresa(event)" >&nbsp; Empresa
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="empresa" onChange="listaOrdenView.handleChangeFilterReqByEmpresa(event);" disabled>
                                    <option value=null>Todas las Empresas</option>
                                    @foreach ($empresas as $emp)
                                    <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Sede">
                                    <input type="checkbox" name="chkSede" onclick="listaOrdenView.chkSede(event)">&nbsp; Sede
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="sede" disabled>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Tipo Proveedor">
                                    <input type="checkbox" name="chkTipoProveedor" onclick="listaOrdenView.chkTipoProveedor(event)" >&nbsp; Tipo Proveedor
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="tipoProveedor" disabled>
                                    <option value="NACIONAL">Nancional</option>
                                    <option value="EXTRANJERO">Extranjero</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="En almacén">
                                    <input type="checkbox" name="chkEnAlmacen" onclick="listaOrdenView.chkEnAlmacen(event)" disabled>&nbsp; En almacen
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="enAlmacen" disabled>
                                    <option value="false">No</option>
                                    <option value="true" >Si</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Subtotal">
                                    <input type="checkbox" name="chkSubtotal" onclick="listaOrdenView.chkSubtotal(event)" disabled>&nbsp; Subtotal
                                </label> 
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="signoSubtotal" disabled>
                                    <option value="MAYOR"> > </option>
                                    <option value="MAYOR_IGUAL"> >= </option>
                                    <option value="IGUAL"> = </option>
                                    <option value="MENOR"> < </option>
                                    <option value="MENOR_IGUAL"> <= </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="subtotal" placeholder="0.00" disabled>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Estado">
                                    <input type="checkbox" name="chkEstado" onclick="listaOrdenView.chkEstado(event)" disabled>&nbsp; Estado
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="estado" disabled>
                                    <option value=null>Todas los Estados</option>
                                    @foreach ($estados as $estado)
                                    <option value="{{$estado->id_estado}}">{{$estado->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </form> 
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="listaOrdenView.aplicarFiltrosVistaDetalleOrden();">Aplicar</button>
            </div>
        </div>
    </div>
</div>