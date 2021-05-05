<!-- Modal Centro costos -->
<div class="modal fade" role="dialog" tabindex="-1" id="modal-ver-transformacion">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-ver-transformacion" onClick="$('#modal-ver-transformacion').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Transformación de producto</h3>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial"></div>
                <table id="tableProductoBase" class="table table-condensed table-bordered" style="width: 100%; font-size: small">
                    <caption style="font-size: medium">Producto base:</caption>
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 10%">Nro. parte</th>
                            <th class="text-center" style="width: 50%">Descripción</th>
                            <th class="text-center" style="width: 10%">Unidad</th>
                            <th class="text-center" style="width: 10%">Cantidad</th>
                            <th class="text-center" style="width: 10%">Precio Unitario</th>
                            <th class="text-center" style="width: 10%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table id="tableProductoTransformado" class="table table-condensed table-bordered" style="width: 100%; font-size: small">
                    <caption style="font-size: medium">Producto transformado:</caption>
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 10%">Nro. parte</th>
                            <th class="text-center" style="width: 50%">Descripción</th>
                            <th class="text-center" style="width: 10%">Unidad</th>
                            <th class="text-center" style="width: 10%">Cantidad</th>
                            <th class="text-center" style="width: 10%">Precio Unitario</th>
                            <th class="text-center" style="width: 10%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <label id="indice_item" style="display: none;"></label>
</div>