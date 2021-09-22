<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-lista-ordenes-elaboradas" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Filtros</h3>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="formFiltroListaOrdenesElaboradas">
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
                                    <input type="checkbox" class="handleCheckTipoOrden" name="chkTipoOrden" >&nbsp; Tipo Orden
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
                                    <input type="checkbox" class="handleCheckVinculadoPor" name="chkVinculadoPor" >&nbsp; Vinculado por
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
                                    <input type="checkbox" class="handleCheckEmpresa" name="chkEmpresa">&nbsp; Empresa
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm handleChangeFilterReqByEmpresa" name="empresa" disabled>
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
                                    <input type="checkbox" class="handleCheckSede" name="chkSede" >&nbsp; Sede
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
                                    <input type="checkbox" class="handleCheckTipoProveedor" name="chkTipoProveedor" >&nbsp; Tipo Proveedor
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
                                    <input type="checkbox" class="handleCheckEnAlmacen" name="chkEnAlmacen" >&nbsp; En almacen
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
                                <label title="Monto de orden">
                                    <input type="checkbox" class="handleCheckMontoOrden" name="chkMontoOrden" >&nbsp; Monto
                                </label> 
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="signoTotalOrden" disabled>
                                    <option value="MAYOR"> > </option>
                                    <option value="MAYOR_IGUAL"> >= </option>
                                    <option value="IGUAL"> = </option>
                                    <option value="MENOR"> < </option>
                                    <option value="MENOR_IGUAL"> <= </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="montoTotalOrden" placeholder="0.00" disabled>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Estado">
                                    <input type="checkbox" class="handleCheckEstado" name="chkEstado" >&nbsp; Estado
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
                <button class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
                <button class="btn btn-sm btn-success handleClickAplicarFiltrosVistaCabeceraOrden">Aplicar</button>
            </div>
        </div>
    </div>
</div>