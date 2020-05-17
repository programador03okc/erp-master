<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-asignacion_equipos">
    <div class="modal-dialog" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Equipos Disponibles</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" 
                            id="listaEquipos">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Fechas de Uso</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label id="id_equi" style="display: none;"></label>
                <label id="cod_equi" style="display: none;"></label>
                <label id="des_equi" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectEquipo();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
