<div class="modal fade" tabindex="-1" role="dialog" id="modal-ver_detalle_partida" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1300px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Ver Detalle de la Partida</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="nombre_partida"></div>
                            <div class="panel-body">
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                    id="VerPartidaInsumo"  style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Cód.Req.</th>
                                            <th>Concepto</th>
                                            <th>Fecha Req.</th>
                                            <th>Producto</th>
                                            <th>Cant.</th>
                                            <th>Mnd</th>
                                            <th>Total Ref.</th>
                                            {{-- <th>Fecha Entrega</th> --}}
                                            <th>Cod.Orden</th>
                                            <th>Fecha Orden</th>
                                            <th>RUC</th>
                                            <th>Proveedor</th>
                                            <th>Mnd</th>
                                            <th>Importe</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>
