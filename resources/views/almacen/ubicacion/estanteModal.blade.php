<div class="modal fade" tabindex="-1" role="dialog" id="modal-estante">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Estantes</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaEstanteM">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Almacén</th>
                            <th>Estante</th>
                            <th>Estado</th>
                            <th hidden>Id Alm</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_estante" style="display: none;"></label>
                <label id="mid_almacen_estante" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectEstante();">Aceptar</button>
            </div>
        </div>
    </div>
</div>