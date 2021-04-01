<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-fuente">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Agregar Fuente</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Nombre de Fuente</h5>
                        <div style="display:flex">
                            <input class="form-control" type="text" name="nombre_fuente" id="nombre_fuente">
                            <button type="button" class="btn-primary" title="Agregar" name="bnt-agregar-fuente" onclick="agregarFuente();">
                            Agregar
                            </button>
                        </div>
                    </div>

                </div>
                <br>
                <table class="mytable table table-striped table-condensed table-bordered" id="listaFuente">
                    <thead>
                        <tr>
                            <th class="hidden"></th>
                            <th class="hidden"></th>
                            <th>#</th>
                            <th>DESCRIPCION</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Second group">
                                    <center><button type="button" class="btn btn-warning btn-xs" name="btnAgregarDetalleFuente" data-toggle="tooltip" title="Agregar Detalle Fuente" onclick="agregarDetalleFuenteModal(event, 0);"><i class="fas fa-cookie-bite"></i></button>
                                            <button type="button" class="btn btn-danger btn-xs" name="btnAnularFuente" data-toggle="tooltip" title="AnularFuente" onclick="anularFuente();"><i class="fas fa-trash-alt"></i></button>
                                        </center>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_fuente"></label>
                <button type="button" class="btn-success" title="Guardar" name="btnGuardaFuente" onclick="guardar_fuente();">Guardar</button>
            </div>
        </div>
    </div>
</div>

