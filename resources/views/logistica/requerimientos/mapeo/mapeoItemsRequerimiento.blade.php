<div class="modal fade" tabindex="-1" role="dialog" id="modal-mapeoItemsRequerimiento"  style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:1000px;">
        <div class="modal-content" >
            <form id="form-mapeoItemsRequerimiento">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Mapeo de productos - <label id="cod_requerimiento"></label></h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                id="detalleItemsRequerimiento"  style="margin-top:10px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Código</th>
                                        <th>PartNumber</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unid</th>
                                        <th width="5%">Asignar</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
                    <input type="submit" id="submit_mapeoItemsRequerimiento" class="btn btn-sm btn-success" value="Guardar"/>

                    <!-- <label id="mid_doc_com" style="display: none;"></label>
                    <button class="btn btn-sm btn-success" onClick="guardar_mapeoItemsRequerimiento();">Guardar</button> -->
                </div>
            </form>
        </div>
    </div>
</div>