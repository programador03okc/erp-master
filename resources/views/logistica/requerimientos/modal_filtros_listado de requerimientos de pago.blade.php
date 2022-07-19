<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-requerimientos-elaborados">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="font-weight:bold;">Filtros</h3>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="formFiltroListaRequerimientosElaborados">

                    <div class="form-group">
                        <div class="col-md-12">
                            <small>Seleccione los filtros que desee aplicar y cierre este cuadro para continuar</small>
                        </div>
                    </div>

                    <div class="container-filter">
                        <fieldset class="group-table">
                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Elaborados por">
                                            <input type="checkbox" name="chkElaborado"> Elaborados por
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosElaborados" name="elaborado" readOnly>
                                        <option value="ALL">Todos</option>
                                        <option value="ME">Por mi</option>
                                        <option value="REVISADO_APROBADO">Con revisados / aprobados por mi</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Empresa">
                                            <input type="checkbox" name="chkEmpresa"> Empresa
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleChangeFiltroEmpresa handleChangeUpdateValorFiltroRequerimientosElaborados" name="empresa" readOnly>
                                        @foreach ($empresas as $emp)
                                        <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Sede">
                                            <input type="checkbox" name="chkSede"> Sede
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosElaborados" name="sede" readOnly>
                                    </select>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Grupo">
                                            <input type="checkbox" name="chkGrupo"> Grupo
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleChangeFiltroGrupo handleChangeUpdateValorFiltroRequerimientosElaborados" name="grupo" readOnly>
                                        @foreach ($grupos as $grupo)
                                        <option value="{{$grupo->id_grupo}}">{{$grupo->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="División">
                                            <input type="checkbox" name="chkDivision"> División
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosElaborados" name="division" readOnly>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Fecha de creación">
                                            <input type="checkbox" name="chkFechaRegistro"> Fecha creación
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-4">
                                    <input type="date" name="fechaRegistroDesde" class="form-control handleChangeUpdateValorFiltroRequerimientosElaborados" readOnly>
                                    <small class="help-block">Desde (dd-mm-aaaa)</small>
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" name="fechaRegistroHasta" class="form-control handleChangeUpdateValorFiltroRequerimientosElaborados" readOnly>
                                    <small class="help-block">Hasta (dd-mm-aaaa)</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Estado">
                                            <input type="checkbox" name="chkEstado"> Estado
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosElaborados" name="estado" readOnly>
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
                <button type="button" class="btn btn-sm btn-primary" class="close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

