<div class="modal fade" tabindex="-1" role="dialog" id="modal-trabajadores" style="overflow-y: scroll;">
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
                            <th style="width:5%;">Nro Documento</th>
                            <th style="width:80%;">Nombre</th>
                            <th style="width:10%;">Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <label style="display: none;" id="numero_persona_autorizada"></label>

            </div>
        </div>
    </div>
</div>