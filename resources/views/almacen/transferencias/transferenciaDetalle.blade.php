<div class="modal fade" tabindex="-1" role="dialog" id="modal-transferenciaDetalle">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalle de la Transferencia <label class="subtitulo_red" id="codigo_transferencia"></label> - Guía: <label id="nro_guia" class="subtitulo_blue"></label></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Almacén Origen</h5>
                        <input type="text" class="form-control" name="det_almacen_origen" readOnly/>
                    </div>
                    <div class="col-md-4">
                        <h5>Almacén Destino</h5>
                        <input type="text" class="form-control" name="det_almacen_destino" readOnly/>
                    </div>
                </div>
                <br/>
                <table class="mytable table table-striped table-condensed table-bordered table-okc-view" 
                    id="listaTransferenciaDetalle">
                    <thead>
                        <tr>
                            <td width="3%">Nro</td>
                            <td width="10%">Código</td>
                            <td>PartNumber</td>
                            <td>Descripción</td>
                            <td>Cantidad</td>
                            <td>Und</td>
                            <td>Guía Compra</td>
                            <td>Estado</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>