<div class="modal fade" tabindex="-1" role="dialog" id="modal-ver-item_valorizacion">
    <div class="modal-dialog" style="width: 84%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Item Valorización</h3> 
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                <table class="mytable table table-hover table-condensed table-bordered table-okc-view dataTable no-footer" id="listaItemValorizacion">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Unidad</th>
                                <th>Cantidad</th>
                                <th>Precio Referencial</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>  
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <!-- <label style="display: none;" id="id_requerimiento"></label> -->
                <!-- <button class="btn btn-sm btn-success" onClick="selectRequerimiento();">Aceptar</button> -->
            </div>
        </div>
    </div>
</div>
@include('logistica.requerimientos.sections_tracking.modal_valorizacion')
