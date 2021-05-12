<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-requerimientos-pendientes">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Filtros</h3>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="formFiltroListaRequerimientosPendientes">
                    <div class="row">
                        <div class="col-md-12">
                            <small>Seleccione los filtros que desee aplicar y haga clic en aceptar para ejecutar los filtros marcados con check</small>
                        </div>
                    </div>
                    <br>
                    <div class="container-filter" style="margin: 0 auto;">
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Empresa">
                                    <input type="checkbox" name="chkEmpresa" onclick="requerimientoPendienteView.chkEmpresa(event)">&nbsp; Empresa
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="empresa" onChange="requerimientoPendienteView.handleChangeFilterReqByEmpresa(event);" readOnly>
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
                                    <input type="checkbox" name="chkSede" onclick="requerimientoPendienteView.chkSede(event);">&nbsp; Sede
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="sede" readOnly>
                                    <option>Lima</option>
                                    <option>Ilo</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </form> 
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="requerimientoPendienteView.aplicarFiltros();">Aplicar</button>
            </div>
        </div>
    </div>
</div>