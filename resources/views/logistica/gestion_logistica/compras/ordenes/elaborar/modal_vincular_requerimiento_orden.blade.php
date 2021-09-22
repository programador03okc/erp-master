<div class="modal fade" tabindex="-1" role="dialog" id="modal-vincular-requerimiento-orden" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-vincular-requerimiento-orden" onClick="$('#modal-vincular-requerimiento-orden').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Requerimientos Pendientes</h3>
            </div>
            <div class="modal-body">
                <table class="table table-condensed table-bordered table-okc-view" id="listaRequerimientosParaVincular" width="100%">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align:center;">Código</th>
                            <th style="width: 40%; text-align:center;">Concepto</th>
                            <th style="width: 5%; text-align:center;">Fecha creación</th>
                            <th style="width: 5%; text-align:center;">Tipo Req.</th>
                            <th style="width: 5%; text-align:center;">Moneda.</th>
                            <th style="width: 8%; text-align:center;">Proveedor/Entidad</th>
                            <th style="width: 8%; text-align:center;">Empresa - Sede</th>
                            <th style="width: 5%; text-align:center;">Autor</th>
                            <th style="width: 5%; text-align:center;">Estado</th>
                            <th style="width: 5%; text-align:center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" data-dismiss="modal-vincular-requerimiento-orden" onClick="$('#modal-vincular-requerimiento-orden').modal('hide');">Cerrar</button>
            </div>
        </div>
    </div>
</div>

