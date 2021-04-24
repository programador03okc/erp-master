<div class="modal fade" tabindex="-1" role="dialog" id="modal-atender-con-almacen">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-atender-con-almacen" onClick="$('#modal-atender-con-almacen').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Atender Con Almacén</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 btn-group right" role="group" style="margin-bottom: 5px;">
                        <span id='group-inputGuardarAtendidoConAlmacen'>
                            <button class="btn btn-success" type="button" id="btnGuardarAtendidoConAlmacen" onClick="requerimientoPendienteView.guardarAtendidoConAlmacen();">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </span>
                    </div>
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" id="listaItemsRequerimientoParaAtenderConAlmacen" style="margin-bottom: 0px; width:100%;">
                            <thead>
                                <tr style="background: grey;">
                                    <th>#</th>
                                    <th>CODIGO</th>
                                    <th>PART NUMBER</th>
                                    <th>DESCRIPCION</th>
                                    <th>UNIDAD</th>
                                    <th>CANTIDAD</th>
                                    <th>PROVEEDOR</th>
                                    <th>ESTADO ACTUAL</th>
                                    <th style="background:#586c86;">ALMACÉN</th>
                                    <th style="background:#586c86;">CANTIDAD A ATENDER</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

