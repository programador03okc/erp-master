<div class="modal fade" tabindex="-1" role="dialog" id="modal-nivel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de niveles</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaNivelM">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Almacén</th>
                            <th>Estante</th>
                            <th>Nivel</th>
                            <th>Estado</th>
                            <th hidden>Id Est</th>
                            <th hidden>Id Alm</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_nivel" style="display: none;"></label>
                <label id="mid_almacen_nivel" style="display: none;"></label>
                <label id="mid_estante_nivel" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectNivel();">Aceptar</button>
            </div>
        </div>
    </div>
</div>