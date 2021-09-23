<div class="modal fade" tabindex="-1" role="dialog" id="modal-ordenes-elaboradas" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Ordenes Elaboradas</h3>
            </div>
            <div class="modal-body"> 
                <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaOrdenesElaboradas">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th style="width: 10%; text-align:center;">Fecha Em.</th>
                            <th style="width: 8%; text-align:center;">Nro.Orden</th>
                            <th style="width: 8%; text-align:center;">RUC</th>
                            <th style="width: 15%; text-align:center;">Proveedor</th>
                            <th style="width: 5%; text-align:center;">Moneda</th>
                            <th style="width: 10%; text-align:center;">Condición</th>
                            <th style="width: 5%; text-align:center;">Plazo Entrega</th>
                            <th style="width: 8%; text-align:center;">Empresa-Sede</th>
                            <th style="width: 10%; text-align:center;">Dirección Destino</th>
                            <th style="width: 10%; text-align:center;">Ubigeo Destino</th>
                            <th style="width: 6%; text-align:center;">Estado</th>
                            <th style="width: 8%; text-align:center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="default_tr">
                            <td colspan="9"> No hay datos registrados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_orden"></label>
                <button class="btn btn-sm btn-success" onClick="selectOrden();">Aceptar</button>
            </div>
        </div>
    </div>
</div>