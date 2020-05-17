<div class="modal fade" tabindex="-1" role="dialog" id="modal-series">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Series en almacén</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaSeriesAlmacen">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Serie</th>
                            <th>Doc.Ingreso</th>
                            <th>Fecha Emisión</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_guia_com" style="display: none;"></label>
                <label id="mid_guia_prov" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectGuiaCompra();">Aceptar</button>
            </div>
        </div>
    </div>
</div>