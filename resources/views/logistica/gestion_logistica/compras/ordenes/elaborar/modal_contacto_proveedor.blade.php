<div class="modal fade" tabindex="-1" role="dialog" id="modal-contacto-proveedor">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de contactos de Proveedor</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaContactosProveedor">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Telefono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="select_id_contacto" style="display: none;"></label>
                <label id="select_nombre_contacto" style="display: none;"></label>
                <label id="select_cargo_contacto" style="display: none;"></label>
                <label id="select_telefono_contacto" style="display: none;"></label>
                <label id="select_email_contacto" style="display: none;"></label>
                <label id="select_direccion_contacto" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectContactoProveedor();">Aceptar</button>
            </div>
        </div>
    </div>
</div>