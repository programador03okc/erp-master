<div class="modal fade" tabindex="-1" role="dialog" id="modal-modalKardexSerie" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1100px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Kardex por Serie</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Serie:</label>
                                        <label id="serie"></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Código:</label>
                                        <label id="codigo"></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Part-Number:</label>
                                        <label id="part_number"></label>
                                    </div>
                                </div> 
                            </div>
                            
                            <div class="panel-body">
                                <p id="descripcion"></p>
                            </div>
                            <table id="listaMovimientosSerie" class="table">
                                <thead>
                                    <tr style="background-color: lightblue;">
                                        <th>Tp</th>
                                        <th>Movimiento</th>
                                        <th>Almacén</th>
                                        <th>Fecha Emisión</th>
                                        <th>Guía</th>
                                        <th>Comprobante</th>
                                        <th>Cliente/Proveedor</th>
                                        <th>Tipo Operación</th>
                                        <th>Responsable</th>
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
</div>
