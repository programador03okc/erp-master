<div class="modal fade" tabindex="-1" role="dialog" id="modal-ver_acu" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Análisis de Costos Unitarios</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table>
                            <tbody>
                                <tr>
                                    <th width="100px">Descripción:</th>
                                    <td><h5 id="cod_acu"></h5></td>
                                    <td colSpan="5"><h5 id="descripcion"></h5></td>
                                </tr>
                                <tr>
                                    <th>Rendimiento:</th>
                                    <td width="50px"><h5 id="rendimiento"></h5></td>
                                    <td width="100px">/día</td>
                                    <th width="100px">Unid. Medida:</th>
                                    <td width="100px"><h5 id="unid_medida"></h5></td>
                                    <th>Cantidad de la Partida:</th>
                                    <td><h5 id="cant_partida_cd"></h5></td>
                                </tr>
                                <tr>
                                    <th>Observaciones:</th>
                                    <td colSpan="6"><h5 id="observacion"></h5></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                            id="VerAcuInsumos"  style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th hidden>N°</th>
                                    <th>Código</th>
                                    <th width="40%">Insumo</th>
                                    <th>Tipo</th>
                                    <th>UniMed</th>
                                    <th width="70">Cuadrilla</th>
                                    <th width="70">Cantidad</th>
                                    <th>Unitario</th>
                                    <th width="100">Total</th>
                                    <th width="140">Total Partida</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>
