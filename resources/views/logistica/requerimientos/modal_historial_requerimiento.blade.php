 
<div class="modal fade" tabindex="-1" role="dialog" id="modal-requerimiento">
    <div class="modal-dialog" style="width: 90%;">
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
            <table class="mytable table table-striped table-condensed table-bordered" id="listaRequerimiento">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Concepto</th>
                            <th>Grupo</th>
                            <th>Área</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Fecha Req.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_requerimiento"></label>
                <button class="btn btn-sm btn-success" onClick="selectRequerimiento();">Aceptar</button>
            </div>
        </div>
    </div>
</div>