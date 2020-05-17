<div class="modal fade" tabindex="-1" role="dialog" id="modal-cronovalproImportes" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                {{-- <h3 class="modal-title">Actualizar Importes</h3> --}}
                <label id="partida"></label>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input class="oculto" name="id_partida">
                        <input class="oculto" name="importe_total">
                        <table id="importes">
                            <thead>
                                <tr>
                                    <th style="text-align:center;">Periodo</th>
                                    <th style="text-align:center;">%</th>
                                    <th style="text-align:center;">Importe</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="copiarPeriodos();">Actualizar Importes</button>
            </div>
        </div>
    </div>  
</div>
