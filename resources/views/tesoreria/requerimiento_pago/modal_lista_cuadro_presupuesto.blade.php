<div class="modal fade" role="dialog" id="modal-lista-cuadro-presupuesto" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-lista-cuadro-presupuesto" onClick="$('#modal-lista-cuadro-presupuesto').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Seleccionar cuadro de presupuesto</h3>
            </div>
            <div class="modal-body">
                <div class="box box-widget">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaCuadroPresupuesto" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:10%">Código</th>
                                        <th class="text-center" style="width:30%">Concepto</th>
                                        <th class="text-center" style="width:8%">Fecha registro</th>
                                        <th class="text-center" style="width:8%">Fecha limite</th>
                                        <th class="text-center" style="width:30%">Entidad</th>
                                        <th class="text-center" style="width:8%">Responsable</th>
                                        <th class="text-center" style="width:10%">Estado aprobación</th>
                                        <th class="text-center" style="width:8%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" class="close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
    <label id="indice_item" style="display: none;"></label>
</div>