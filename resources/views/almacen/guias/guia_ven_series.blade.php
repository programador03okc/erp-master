<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_ven_series" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Seleccione las Series</h3>
            </div>
            <div class="modal-body">
                <input type="text" class="oculto" name="id_od_detalle"/>
                <input type="text" class="oculto" name="id_trans_detalle"/>
                <input type="text" class="oculto" name="id_producto"/>
                <input type="text" class="oculto" name="cant_items"/>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-striped table-condensed table-bordered table-okc-view" 
                            id="listaSeriesVen" style="margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td width="5%">#</td>
                                    <td width="60%">Serie</td>
                                    <td>Guía Compra</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="row">
                            <div class="col-md-12" style="margin-left: 5px;">
                                <input type="checkbox" name="seleccionar_todos" style="margin-right: 10px;"/> Seleccionar todos
                            </div>
                        </div>
                        
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