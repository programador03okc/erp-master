<div class="modal fade" tabindex="-1" role="dialog" id="modal-transformacion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Transformaciones</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaTransformaciones">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Código</th>
                            <th>Oportunidad</th>
                            <th>Requerimiento</th>
                            <th>Guía Remisión</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 11px;"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_transformacion" style="display: none;"></label>
                <label id="codigo" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectTransformacion();">Aceptar</button>
            </div>
        </div>
    </div>
</div>