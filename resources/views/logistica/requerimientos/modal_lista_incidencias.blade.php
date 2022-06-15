<div class="modal fade" tabindex="-1" role="dialog" id="modal-listaIncidencias">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de incidencias</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaIncidencias">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Cod. Incidencia</th>
                            <th>Cliente</th>
                            <th>Factura</th>
                            <th>Falla</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 12px;"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" class="close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>