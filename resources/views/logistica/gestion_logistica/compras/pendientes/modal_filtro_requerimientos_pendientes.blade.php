<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-requerimientos-pendientes">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="font-weight:bold;">Filtros</h3>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="formFiltroListaRequerimientosPendientes">
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
                                    <label title="Empresa">
                                        Empresa
                                    </label> 
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm handleChangeFiltroEmpresa" name="empresa" >
                                        <option value="SIN_FILTRO">-----------------</option>
                                        @foreach ($empresas as $emp)
                                        <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label title="Sede">
                                        Sede
                                    </label> 
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm" name="sede" >
                                        <option value="SIN_FILTRO">-----------------</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label title="Sede">
                                        Fecha registro
                                    </label> 
                                </div>
                                <div class="col-md-8">
                                    <div style="display:flex;">
                                        <input type="date" class="form-control input-sm" name="fechaRegistroDesde" >
                                        <input type="date" class="form-control input-sm" name="fechaRegistroHasta" >
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <h5 style="display:flex;justify-content: space-between; font-weight:bold;">Nivel Item </h5> 
                        <fieldset class="group-table">
                            <div class="row">
                                <div class="col-md-4">
                                    <label title="Empresa">
                                        Reserva almacén
                                    </label> 
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm" name="reserva" >
                                        <option value="SIN_FILTRO">-----------------</option>
                                        <option value="SIN_RESERVA">Sin reservas</option>
                                        <option value="CON_RESERVA">Con reservas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label title="Empresa">
                                        Orden
                                    </label> 
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm" name="orden" >
                                        <option value="SIN_FILTRO">-----------------</option>
                                        <option value="SIN_ORDEN">Sin orden</option>
                                        <option value="CON_ORDEN">Con orden</option>
                                    </select>
                                </div>
                            </div>


                        </fieldset>


                    </div>
                </div> 
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default handleClickLimpiarFiltroRequerimientosPendientes">Limpiar filtros</button>
                <button class="btn btn-sm btn-success handleClickAplicarFiltroRequerimientosPendientes">Aplicar</button>
            </div>
        </div>
    </div>
</div>