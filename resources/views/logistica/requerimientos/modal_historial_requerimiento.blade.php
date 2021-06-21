<div class="modal fade" tabindex="-1" role="dialog" id="modal-historial-requerimiento">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Historial de requerimientos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaRequerimiento">
                    <thead>
                        <tr>
                            <th class="text-center">Prio.</th>
                            <th class="text-center">Código</th>
                            <th class="text-center" style="width:20%">Concepto</th>
                            <th class="text-center">Fecha entrega</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center" style="width:10%">Empresa</th>
                            <th class="text-center">Grupo</th>
                            <th class="text-center">División</th>
                            <th class="text-center" style="width:8%">Estado</th>
                            <th class="text-center" style="width:8%">Creado</th>
                            <th class="text-center" style="width:5%">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="default_tr">
                            <td colspan="7"> No hay datos registrados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_requerimiento"></label>
                <button class="btn btn-sm btn-success" onClick="selectRequerimiento();">Aceptar</button>
            </div>
        </div>
    </div>
</div>