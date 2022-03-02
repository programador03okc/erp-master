<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-incidencia">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de incidencias</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaIncidencias">
                    <thead>
                        <tr>
                            <td hidden></td>
                            <td>Código</td>
                            <td>Cliente</td>
                            <td>Fecha reporte</td>
                            <td>Responsable</td>
                            <td>Estado</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="selectIncidencia();">Aceptar</button>
            </div>
        </div>
    </div>
</div>