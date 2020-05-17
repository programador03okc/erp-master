<div class="modal fade" tabindex="-1" role="dialog" id="modal-categoria">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Categorias</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaCategoria">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id" style="display: none;"></label>
                <label id="tipo" style="display: none;"></label>
                <label id="cat_des" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectCategoria();">Aceptar</button>
            </div>
        </div>
    </div>
</div>