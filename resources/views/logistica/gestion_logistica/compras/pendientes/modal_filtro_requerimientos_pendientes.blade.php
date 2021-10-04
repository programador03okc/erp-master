<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-requerimientos-pendientes">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="font-weight:bold;">Filtros</h3>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="formFiltroListaRequerimientosPendientes">
                    <div class="form-group">
                        <div class="col-md-12">
                            <small>Seleccione los filtros que desee aplicar y cierre este cuadro para continuar</small>
                        </div>
                    </div>
                    <div class="container-filter" style="margin: 0 auto;">

                        <h5 style="display:flex;justify-content: space-between;  font-weight:bold;">Nivel cabecera</h5>
                        <fieldset class="group-table"> 
                        <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Empresa">
                                            <input type="checkbox" name="chkEmpresa"> Empresa
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleChangeFiltroEmpresa handleChangeUpdateValorFiltroRequerimientosPendientes" name="empresa" readOnly>
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
                                    <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosPendientes" name="sede" readOnly>
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
                                    <input type="date" name="fechaRegistroDesde" class="form-control handleBlurUpdateValorFiltroRequerimientosPendientes" readOnly>
                                    <small class="help-block">Desde (dd-mm-aaaa)</small>
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" name="fechaRegistroHasta" class="form-control handleBlurUpdateValorFiltroRequerimientosPendientes" readOnly>
                                    <small class="help-block">Hasta (dd-mm-aaaa)</small>
                                </div>
                            </div>
                        </fieldset>
                        <h5 style="display:flex;justify-content: space-between; font-weight:bold;">Nivel Item </h5> 
                        <fieldset class="group-table">

                        <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Reserva">
                                            <input type="checkbox" name="chkReservaAlmacen"> Reserva
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosPendientes" name="reserva" readOnly>
                                        <option value="SIN_RESERVA">Sin reservas</option>
                                        <option value="CON_RESERVA">Con reservas</option>
                                    </select>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Orden">
                                            <input type="checkbox" name="chkOrden"> Orden
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                <select class="form-control input-sm handleChangeUpdateValorFiltroRequerimientosPendientes" name="orden" readOnly>
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
                <button type="button" class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
</div>