<div class="modal fade" tabindex="-1" role="dialog" id="modal-salidasVenta">
    <div class="modal-dialog"  style="width:65%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de salidas por venta</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaSalidasVenta">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Guía venta</th>
                            <th>Cliente</th>
                            <th>Cod. Req.</th>
                            <th>Concepto</th>
                            <th>Fecha salida</th>
                            <th hidden>IdReq</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 12px;"></tbody>
                </table>
            </div>
            {{-- <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="selectTransformacion();">Aceptar</button>
            </div> --}}
        </div>
    </div>
</div>