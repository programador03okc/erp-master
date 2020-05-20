<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-asignaciones">
    <div class="modal-dialog" style="width: 1100px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Asignaciones</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaAsignaciones">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Código</th>
                            <th>Area</th>
                            <th>Equipo</th>
                            <th>Concepto</th>
                            <th>Solicitante</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_asignacion" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectAsignacion();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
