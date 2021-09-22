<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-fuente" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 30%;">
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
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-hover table-condensed table-bordered table-okc-view dataTable no-footer" id="listaFuente" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>DESCRIPCION</th>
                                    <th>ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

