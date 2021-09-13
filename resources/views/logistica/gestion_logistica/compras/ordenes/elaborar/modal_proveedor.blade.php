<div class="modal fade" tabindex="-1" role="dialog" id="modal-proveedores">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Proveedores</h3>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-primary btn-sm handleClickCrearProveedor" title="Agregar Proveedor" ><i class="fas fa-plus"></i> Crear nuevo</button>
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaProveedor">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th hidden></th>
                            <th>RUC</th>
                            <th>Razon social</th>
                            <th>Telefono</th>
                            <th>Dirección</th>
                            <th>Ubigeo</th>
                            <th>Ubigeo descripcion</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>