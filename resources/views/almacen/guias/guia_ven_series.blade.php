<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_ven_series">
    <div class="modal-dialog" style="width:50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Series: <label id="descripcion"></label></h3>
            </div>
            <div class="modal-body">
                <input type="text" class="oculto" name="cant_items"/>
                <input type="text" class="oculto" name="id_producto"/>
                <input type="text" class="oculto" name="id_guia_ven_det"/>
                <input type="text" class="oculto" name="anulados"/>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="width: 100%; display:flex; font-size:12px;">
                                    <div style="width:88%;">
                                        <input name="serie_prod" class="form-control" type="text" style="height:30px;"
                                            onKeyPress="handleKeyPress(event);">
                                    </div>
                                    <div style="width:12%;">
                                        <button type="button" class="btn btn-warning" id="basic-addon2" 
                                            style="padding:0px;height:34px;width:98%;height:30px;font-size:12px;" onClick="seriesModal();"
                                            data-toggle="tooltip" data-placement="right" title="Agregar Serie">
                                            <i class="fas fa-search"></i>
                                            Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="mytable table table-striped table-condensed table-bordered" 
                            id="listaSeries">
                            <thead>
                                <tr>
                                    <td hidden></td>
                                    <td width="5%">#</td>
                                    <td width="60%">Serie</td>
                                    <td>Doc.Ingreso</td>
                                    <td>Anular</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label id="mid_barra" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="guardar_series();">Guardar</button>
            </div>
        </div>
    </div>
</div>