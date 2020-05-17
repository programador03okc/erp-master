<div class="modal fade" tabindex="-1" role="dialog" id="modal-doc_compra">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Comprobantes de Compra</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaDocsCompra">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Proveedor</th>
                            <th>Serie-Número</th>
                            <th>Fecha Emisión</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_doc_com" style="display: none;"></label>
                {{-- <label id="mid_doc_prov" style="display: none;"></label> --}}
                <button class="btn btn-sm btn-success" onClick="selectDocCompra();">Aceptar</button>
            </div>
        </div>
    </div>
</div>