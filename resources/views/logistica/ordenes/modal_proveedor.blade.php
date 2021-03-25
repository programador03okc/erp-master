<div class="modal fade" tabindex="-1" role="dialog" id="modal-proveedor">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Proveedores</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaProveedor">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th hidden></th>
                            <th>RUC</th>
                            <th>Razon Social</th>
                            <th>Telefono</th>
                            <th>Dirección</th>
                            <th>ubigeo</th>
                            <th>ubigeo descripcion</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_proveedor" style="display: none;"></label>
                <label id="id_contribuyente" style="display: none;"></label>
                <label id="ruc" style="display: none;"></label>
                <label id="select_razon_social" style="display: none;"></label>
                <label id="select_direccion_fiscal" style="display: none;"></label>
                <label id="select_telefono" style="display: none;"></label>
                <label id="select_ubigeo_descripcion" style="display: none;"></label>
                <label id="select_ubigeo" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectProveedor();">Aceptar</button>
            </div>
        </div>
    </div>
</div>