<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-equi_sol">
    <div class="modal-dialog" style="width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Solicitudes de Equipos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaSolicitudes">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Código</th>
                            <th>Fecha Solicitud</th>
                            <th>Area</th>
                            <th>Solicitado por</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_solicitud" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectSolicitud();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
