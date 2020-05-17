<div class="modal fade" tabindex="-1" role="dialog" id="modal-comparative_board_enabled_to_value">
    <div class="modal-dialog" style="width: 70%; max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Cuadro comparativos habilitados para Cotización</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="comparative_board_enabled_to_value_table">
                    <thead>
                        <tr>
                            <th hidden>id_grupo_cotizacion</th>
                            <th>Código Cuadro Comparativo</th>
                            <th>Proveedores</th>
                            <th>Cotizaciones</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_grupo_cotizacion" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectCuadroComp();">Aceptar</button>
            </div>
        </div>
    </div>
</div>