<div class="modal fade" tabindex="-1" role="dialog" id="modal-productosAlmacen">
    <div class="modal-dialog"  style="width:60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de productos con saldo <strong><span id="titulo_almacen"></span></strong> </h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaProductoSaldos">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Codigo</th>
                            <th>Codigo softlink</th>
                            <th>Part Number</th>
                            <th width="50%">Descripción del producto</th>
                            <th>Saldo</th>
                            <th>Stock comprometido</th>
                            <th>Disponible</th>
                            <th>Und.</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px;"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>