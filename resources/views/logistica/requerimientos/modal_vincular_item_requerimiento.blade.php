<div class="modal fade" tabindex="-1" role="dialog" id="modal-vincular-item-requerimiento" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-vincular-item-requerimiento" onClick="$('#modal-vincular-item-requerimiento').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Vincular Nuevo Items a Requerimiento</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <label>Lista de Requerimientos</label>
                        <table class="mytable table table-condensed table-bordered table-okc-view" id="listaRequerimientosVinculados" style="margin-bottom: 0px; width:100%;">
                            <thead>
                                <tr style="background: grey;">
                                    <th>#</th>
                                    <th>CODIGO</th>
                                    <th>CONCEPTO</th>
                                    <th>TIPO</th>
                                    <th>PROVEEDOR/CLIENTE</th>
                                    <th>EMPRESA</th>
                                    <th>SEDE</th>
                                    <th>USUARIO</th>
                                    <th>FECHA REQ</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <label>Lista de Items Nuevos a Vincular</label>
                        <table class="mytable table table-condensed table-bordered table-okc-view" id="listaItemsRequerimientosVinculados" style="margin-bottom: 0px; width:100%;">
                            <thead>
                            <tr style="background: steelblue;">
                                    <th></th>
                                    <th>#</th>
                                    <th>CODIGO</th>
                                    <th>PART NUMBER</th>
                                    <th>DESCRIPCION</th>
                                    <th>UNIDAD</th>
                                    <th>CANTIDAD A COMPRAR</th>
                                    <th>PRECIO</th>
                                    <th>REQUERIMIENTO</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_item"></label> 
                <label style="display: none;" id="codigo"></label>
                <label style="display: none;" id="part_number"></label>
                <label style="display: none;" id="descripcion"></label>
                <label style="display: none;" id="id_producto"></label>
                <label style="display: none;" id="id_servicio"></label>
                <label style="display: none;" id="id_equipo"></label>
                <button type="button" class="btn btn-sm btn-success" onClick="margeObjArrayToDetalleReqSelected();">Aceptar</button>
            </div>
        </div>
    </div>
</div>

