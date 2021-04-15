<div class="modal fade" tabindex="-1" role="dialog" id="modal-ordenes-elaboradas">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Ordenes Elaboradas</h3>
            </div>
            <div class="modal-body"> 
                <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" id="listaOrdenesElaboradas">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Fecha Em.</th>
                            <th>Nro.Orden</th>
                            <th>RUC</th>
                            <th>Proveedor</th>
                            <th>Moneda</th>
                            <th>Condición</th>
                            <th>Plazo Entrega</th>
                            <th>Empresa-Sede</th>
                            <th>Dirección Destino</th>
                            <th>Ubigeo Destino</th>
                            <th>Estado</th>
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