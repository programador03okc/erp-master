<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-pardet">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Detalles de Partidas</h3>
            </div>
            <div class="modal-body">
                <input type="text" class="oculto" name="cod_padre">
                <input type="text" class="oculto" name="codigo">
                <table class="mytable table table-striped table-condensed table-bordered" 
                    id="listaParDet">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_pardet" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectParDet();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
