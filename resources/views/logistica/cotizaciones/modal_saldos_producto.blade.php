<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-saldos_producto">
    <div class="modal-dialog" style="width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Saldos del Producto
                <label id="des_producto"></label></h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaSaldos">
                    <thead>
                        <tr>
                            <th></th>
                            {{-- <th>Código</th>
                            <th>Descripcion</th> --}}
                            <th>Almacén</th>
                            <th>Posición</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            {{-- <div class="modal-footer">
                <label id="id_solicitud" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectSolicitud();">Aceptar</button>
            </div> --}}
        </div>
    </div>
</div>
