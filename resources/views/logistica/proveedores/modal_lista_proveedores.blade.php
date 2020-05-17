<div class="modal fade" tabindex="-1" role="dialog" id="modal-lista-proveedores">
    <div class="modal-dialog" style="width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista Proveedores</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" id="ListaProveedores" width="100%">
                            <thead>
                                <tr>
                                    <th width="10"></th>
                                    <th width="190">RAZON SOCIAL</th>
                                    <th width="90">DOCUMENTO</th>
                                    <th>TIPO CONTRIBUYENTE</th>
                                    <th>DIRECCIÓN</th>
                                    <th>TELEFONO</th>
                                    <th>ESTADO RUC</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
 
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_proveedor"></label>
                <button class="btn btn-sm btn-success" onClick="selectValue();">Aceptar</button>
            </div>
        </div>
    </div>
</div>

