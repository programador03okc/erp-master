<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-lista-items-orden-elaboradas" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="font-weight:bold;">Filtros</h3>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="formFiltroListaItemsOrdenElaboradas">
                    <div class="row">
                        <div class="col-md-12">
                            <small>Seleccione alguna de las opciones con valor que desee y luego haga clic en aplicar.</small>
                        </div>
                    </div>
                    <div class="container-filter" style="margin: 0 auto;">

                        <h5 style="display:flex;justify-content: space-between;  font-weight:bold;">Nivel detalle</h5>
                        <fieldset class="group-table">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" name="chkEmpresa" style="margin-right: 3px;"> Empresa
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeFiltroEmpresa handleChangeUpdateValorFiltroDetalleOrdenesElaboradas" name="empresa" readOnly>
                                        <option value="SIN_FILTRO">-----------------</option>
                                        @foreach ($empresas as $emp)
                                        <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" name="chkSede" style="margin-right: 3px;"> Sede
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroDetalleOrdenesElaboradas" name="sede" readOnly>
                                        <option value="SIN_FILTRO">-----------------</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" name="chkFechaRegistro" style="margin-right: 3px;"> Fecha creación
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <div style="display:flex;">
                                        <input type="date" class="form-control input-sm handleBlurUpdateValorFiltroDetalleOrdenElaboradas" name="fechaRegistroDesde" readOnly>
                                        <input type="date" class="form-control input-sm handleBlurUpdateValorFiltroDetalleOrdenElaboradas" name="fechaRegistroHasta" readOnly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" name="chkEstado" style="margin-right: 3px;"> Estado
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroDetalleOrdenesElaboradas" name="estado" readOnly>
                                        <option value="SIN_FILTRO">-----------------</option>
                                        @foreach ($estados as $estado)
                                        <option value="{{$estado->id_estado}}">{{$estado->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" class="close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>