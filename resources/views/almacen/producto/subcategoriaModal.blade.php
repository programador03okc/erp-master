<div class="modal fade" tabindex="-1" role="dialog" id="modal-subcategoria">
    <div class="modal-dialog" style="width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de SubCategorias</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaSubCategoria">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Código</th>
                            {{-- <th>Tipo</th>
                            <th>Categoria</th> --}}
                            <th>SubCategoría</th>
                            {{-- <th>Estado</th> --}}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_subcat" style="display: none;"></label>
                <label id="tp_des" style="display: none;"></label>
                <label id="cat_des" style="display: none;"></label>
                <label id="subcat_des" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectSubCategoria();">Aceptar</button>
            </div>
        </div>
    </div>
</div>