<div class="modal fade" tabindex="-1" role="dialog" id="modal-trabajadores">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Trabajadores</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaTrabajadores">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Nro Documento</th>
                            <th>Nombre</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="select_id_trabajador" style="display: none;"></label>
                <label id="select_nombre_trabajador" style="display: none;"></label>
                <label id="select_nro_documento_trabajador" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectTrabajador();">Aceptar</button>
            </div>
        </div>
    </div>
</div>