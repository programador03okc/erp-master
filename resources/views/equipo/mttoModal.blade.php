<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-mtto">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Mantenimientos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" 
                    id="listaMttos">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Código</th>
                            <th>Fecha Mtto</th>
                            <th>Equipo</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_mtto" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectMtto();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
