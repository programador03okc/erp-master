<div class="modal fade" tabindex="-1" role="dialog" id="modal-Almacen">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Almacenes</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaAlmacen">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Sede</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_almacen" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectAlmacen();">Aceptar</button>
            </div>
        </div>
    </div>
</div>