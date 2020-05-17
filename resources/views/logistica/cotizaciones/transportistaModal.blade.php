<div class="modal fade" tabindex="-1" role="dialog" id="modal-transportista">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Transportistas</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaTransportistas">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th hidden></th>
                            <th>RUC</th>
                            <th>Razon Social</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_proveedor_tra" style="display: none;"></label>
                <label id="id_contribuyente_tra" style="display: none;"></label>
                <label id="razon_social_tra" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectTransportista();">Aceptar</button>
            </div>
        </div>
    </div>
</div>