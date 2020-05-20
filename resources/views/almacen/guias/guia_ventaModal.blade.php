<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_venta">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Guías de Remisión por Venta</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaGuiasVenta">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th with="150px">Empresa</th>
                            <th>Serie-Número</th>
                            <th>Fecha Emisión</th>
                            <th>Tp Operación</th>
                            <th>Estado</th>
                            <th hidden></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_guia_ven" style="display: none;"></label>
                <label id="mid_guia_alm" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectGuiaVenta();">Aceptar</button>
            </div>
        </div>
    </div>
</div>