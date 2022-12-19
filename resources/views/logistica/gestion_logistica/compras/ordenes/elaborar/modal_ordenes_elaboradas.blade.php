<div class="modal fade" tabindex="-1" role="dialog" id="modal-ordenes-elaboradas" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Ordenes Elaboradas</h3>
            </div>
            <div class="modal-body"> 
                <table class="table table-hover table-condensed table-bordered table-okc-view" id="listaOrdenesElaboradas" width="100%">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th width="10%" style="text-align:center;">Fecha Em.</th>
                            <th width="10%" style="text-align:center;">Nro.Orden</th>
                            <th width="10%" style="text-align:center;">Cod. Softlink</th>
                            <th widt="8%" style="text-align:center;">RUC</th>
                            <th width="20%" style="text-align:center;">Proveedor</th>
                            <th widt="5%" style="text-align:center;">Moneda</th>
                            <th widt="8%" style="text-align:center;">Empresa-Sede</th>
                            <th widt="8%" style="text-align:center;">Estado</th>
                            <th width="10%" style="text-align:center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="default_tr">
                            <td colspan="10"> No hay datos registrados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- <div class="modal-footer">
                <label style="display: none;" id="id_orden"></label>
                <button class="btn btn-sm btn-success" onClick="selectOrden();">Aceptar</button>
            </div> -->
        </div>
    </div>
</div>