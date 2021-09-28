<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-requerimientos-elaborados">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="font-weight:bold;">Filtros</h3>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="formFiltroListaRequerimientosElaborados">
                    <div class="row">
                        <div class="col-md-12">
                            <small>Seleccione alguna de las opciones con valor que desee y luego haga clic en aplicar.</small>
                        </div>
                    </div>
                    <div class="container-filter" style="margin: 0 auto;">

                        <h5 style="display:flex;justify-content: space-between;  font-weight:bold;">Nivel cabecera</h5>
                        <fieldset class="group-table">
                            <div class="row">
                                <div class="col-md-4">
                                        <label>
                                            <input type="checkbox" name="chkElaborado" style="margin-right: 3px;"> Elaborados por
                                        </label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosElaborados" name="elaborado" readOnly>
                                        <option value="SIN_FILTRO">-----------------</option>
                                        <option value="ALL">Todos</option>
                                        <option value="ME">Por mi</option>
                                        <option value="REVISADO_APROBADO">Con revisados / aprobados por mi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                        <label>
                                            <input type="checkbox" name="chkEmpresa" style="margin-right: 3px;"> Empresa
                                        </label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeFiltroEmpresa handleChangeUpdateValorFiltroRequerimientosElaborados" name="empresa" readOnly>
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
                                        <input type="checkbox" name="chkSede" style="margin-right: 3px;" > Sede
                                    </label> 
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosElaborados" name="sede" readOnly>
                                        <option value="SIN_FILTRO">-----------------</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" name="chkGrupo" style="margin-right: 3px;" > Grupo
                                    </label> 
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeFiltroGrupo handleChangeUpdateValorFiltroRequerimientosElaborados" name="grupo" readOnly>
                                        <option value="SIN_FILTRO">-----------------</option>
                                        @foreach ($grupos as $grupo)
                                        <option value="{{$grupo->id_grupo}}">{{$grupo->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" name="chkDivision" style="margin-right: 3px;" > División
                                    </label> 
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosElaborados" name="division" readOnly>
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
                                        <input type="date" class="form-control input-sm handleBlurUpdateValorFiltroRequerimientosElaborados" name="fechaRegistroDesde" readOnly>
                                        <input type="date" class="form-control input-sm handleBlurUpdateValorFiltroRequerimientosElaborados" name="fechaRegistroHasta" readOnly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" name="chkEstado" style="margin-right: 3px;" > Estado
                                    </label> 
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosElaborados" name="estado" readOnly>
                                        <option value="SIN_FILTRO">-----------------</option>
                                        @foreach ($estados as $estado)
                                        <option value="{{$estado->id_estado_doc}}">{{$estado->estado_doc}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
</div>