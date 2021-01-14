<!-- modal obs -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-sustento">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <form id="form-obs-sustento">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Sustento</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="tablaListaObservacionesPorSustentar" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Observación</th>
                                        <th>Observado por</th>
                                        <th width="120">Sustento (opcional)</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" onClick="GrabarSustentoRequerimiento();">Guardar Sustento y Requerimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>