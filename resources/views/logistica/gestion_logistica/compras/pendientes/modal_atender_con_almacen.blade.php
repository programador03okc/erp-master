<div class="modal fade" tabindex="-1" role="dialog" id="modal-atender-con-almacen" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-atender-con-almacen" onClick="$('#modal-atender-con-almacen').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Reserva en almacén <span id="codigo_requerimiento"></span> (<span id="almacen_requerimiento"></span>)</h3>
                
            </div>
            <div class="modal-body">
            <form id="form-reserva-almacen" type="register" form="formulario">
            <input type="hidden" name="id_requerimiento">

                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view dataTable no-footer" id="listaItemsRequerimientoParaAtenderConAlmacen" width="100%">

                            <thead>
                                <tr >
                                    <th>Código</th>
                                    <th>Cód. Softlink</th>
                                    <th>Part number</th>
                                    <th style="width: 280px;">Descripción</th>
                                    <th>Unidad</th>
                                    <th>Cantidad</th>
                                    <th>Moneda</th>
                                    <th>Proveedor</th>
                                    <th>Estado actual</th>
                                    <th>Cantidad reservada</th>
                                    <th>Código reservas</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 btn-group right" role="group" style="margin-bottom: 5px;">
                    <span id='group-inputGuardarAtendidoConAlmacen'>
                        <button class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

