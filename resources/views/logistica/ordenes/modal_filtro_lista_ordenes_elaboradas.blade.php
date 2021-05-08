<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-lista-ordenes-elaboradas">
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
                                <label title="Etapa">
                                    <input type="checkbox" name="chkEtapa">&nbsp; Tipo Orden
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="etapa">
                                    <option>Compra</option>
                                    <option>Servico</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Etapa">
                                    <input type="checkbox" name="chkEtapa">&nbsp; Origen
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="etapa">
                                    <option>Por Requerimiento</option>
                                    <option>Por Cotizaciones</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Etapa">
                                    <input type="checkbox" name="chkEtapa">&nbsp; Empresa
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="etapa">
                                    <option>OKC</option>
                                    <option>PYT</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Etapa">
                                    <input type="checkbox" name="chkEtapa">&nbsp; Sede
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="etapa">
                                    <option>Lima</option>
                                    <option>Ilo</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Etapa">
                                    <input type="checkbox" name="chkEtapa">&nbsp; Tipo Proveedor
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="etapa">
                                    <option>Nancional</option>
                                    <option>Extranjero</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Etapa">
                                    <input type="checkbox" name="chkEtapa">&nbsp; En almacen
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="etapa">
                                    <option>No</option>
                                    <option>Si</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Etapa">
                                    <input type="checkbox" name="chkEtapa">&nbsp; Monto
                                </label> 
                            </div>
                            <div class="col-md-2">
                                <select class="form-control">
                                    <option> > </option>
                                    <option> >= </option>
                                    <option> = </option>
                                    <option> < </option>
                                    <option> <= </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label title="Etapa">
                                    <input type="checkbox" name="chkEstado">&nbsp; Estado
                                </label> 
                            </div>
                            <div class="col-md-8">
                                <select class="form-control input-sm" name="estado">
                                    <option>ENVIADA</option>
                                    <option>CONFIRMADA</option>
                                    <option>FACTURADA</option>
                                    <option>DESPACHADO</option>
                                    <option>EN TRANSITO</option>
                                    <option>ATENCION PARCIAL</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </form> 
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="listaOrdenView.aplicarFiltros();">Aceptar</button>
            </div>
        </div>
    </div>
</div>