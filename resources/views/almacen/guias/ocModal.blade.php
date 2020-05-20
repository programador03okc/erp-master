<div class="modal fade" tabindex="-1" role="dialog" id="modal-oc">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Ordenes de Compra</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" 
                id="listaOC">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Nro.OC</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Monto Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_oc" style="display: none;"></label>
                {{-- <label id="mid_guia_prov" style="display: none;"></label> --}}
                <button class="btn btn-sm btn-success" onClick="selectOC();">Aceptar</button>
            </div>
        </div>
    </div>
</div>