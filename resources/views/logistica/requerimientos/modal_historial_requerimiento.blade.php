 
<div class="modal fade" tabindex="-1" role="dialog" id="modal-requerimiento">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Requerimientos</h3>
            </div>
            <div class="modal-body">
                <div class="checkbox">
                    <label>
                    <input type="checkbox" id="checkViewTodos"> Mostrar Todos (eliminados + anulados)
                    </label>
                </div>   
                <table class="mytable table table-striped table-condensed table-bordered table-okc-view" id="listaRequerimiento">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Código</th>
                            <th>Tipo Requerimiento</th>
                            <th>Tipo Cliente</th>
                            <th>Concepto</th>
                            <th>Cliente</th>
                            <th>Usuario</th>
                            <th>Fecha Req.</th>
                            <th>Estado</th>
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