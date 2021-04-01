<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-detalle-fuente">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Agregar Detalle Fuente</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Nombre Detalle Fuente</h5>
                        <div style="display:flex">
                            <input class="form-control" type="text" name="nombre_detalle_fuente" id="nombre_detalle_fuente">
                            <button type="button" class="btn-primary" title="Agregar" name="bnt-agregar-detalle-fuente" onclick="agregarDetalleFuente();">
                            Agregar
                            </button>
                        </div>
                    </div>

                </div>
                <br>
                <table class="mytable table table-striped table-condensed table-bordered" id="listaDetalleFuente">
                    <thead>
                        <tr>
                            <th class="hidden"></th>
                            <th class="hidden"></th>
                            <th>#</th>
                            <th>DESCRIPCION</th>
                            <th>ACCIÓN</th>
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_fuente"></label>
                <button type="button" class="btn-success" title="Guardar" name="btnGuardarDetalleFuente" onclick="guardar_detalle_fuente();">Guardar</button>
            </div>
        </div>
    </div>
</div>

