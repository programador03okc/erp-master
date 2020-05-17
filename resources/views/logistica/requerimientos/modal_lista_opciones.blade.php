 
<div class="modal fade" tabindex="-1" role="dialog" id="modal-lista_opciones">
    <div class="modal-dialog" style="width: 84%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Opciones</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaOpcionCom">
                    <thead>
                        <tr>
                                    <td class="hidden"></td>
                                    <td>Código</td>
                                    <td>Descripción</td>
                                    <td>Fecha</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_op_com"></label>
                <button class="btn btn-sm btn-success" onClick="selectProyecto();">Aceptar</button>
            </div>
        </div>
    </div>
</div>