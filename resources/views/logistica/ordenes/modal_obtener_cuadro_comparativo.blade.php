<div class="modal fade" tabindex="-1" role="dialog" id="modal-obtener-cuadro-comparativo">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Obtener Cuadro Comparativo</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="ListaBuenasPro">
                    <thead>
                        <tr>
                            <th hidden>id_valorizacion</th>
                            <th hidden>id_cotizacion</th>
                            <th>Cuadro Comparativo</th>
                            <th>Cotización</th>
                            <th>Proveedor</th>
                             <th>Requerimiento</th>
                            <th>Empresa</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="idValorizacionCotizacion" style="display: none;"></label>
                <label id="idCotizacion" style="display: none;"></label>
 
                <button class="btn btn-sm btn-success" onClick="selectBuenaPro();">Aceptar</button>
            </div>
        </div>
    </div>
</div>