<div class="modal fade" tabindex="-1" role="dialog" id="modal-todo-adjuntos" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Archivos adjuntos  <span id="codigo-requerimiento"></span></h3> 
               
            </div>
            <div class="modal-body">

            <div class="row">
                <div class="col-md-12">
                <fieldset class="group-table">
                <h5 style="font-weight: bold;">A nivel de cabecera</h5>
                <table class="table table-hover table-striped table-condensed table-bordered table-okc-view" id="listaAdjuntosRequerimiento" width="100%">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Fecha registro</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="body_adjuntos_requerimiento"></tbody>
                </table>
            </fieldset>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                <fieldset class="group-table">
                <h5 style="font-weight: bold;">A nivel de item's</h5>
                <table class="table table-hover table-striped table-condensed table-bordered table-okc-view" id="listaTodoAdjuntosDetalleRequerimiento" width="100%">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Part number</th>
                            <th>Descripción</th>
                            <th>Archivo</th>
                            <th>Fecha registro</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="body_adjuntos_detalle_requerimiento"></tbody>
                </table>
            </fieldset>
                </div>
            </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
</div>