 
<div class="modal fade" tabindex="-1" role="dialog" id="modal-cuadro_costos_comercial">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Cuadro de Costos</h3>
            </div>
            <div class="modal-body">  
            <table class="mytable table table-striped table-condensed table-bordered" id="listaCuadroCostos">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Fecha Entrega</th>
                            <th>Codigo</th>
                            <th>Oportunidad</th>
                            <th>Probabilidad</th>
                            <th>Fecha Limite</th>
                            <th>Moneda</th>
                            <th>Importe</th>
                            <th>Tipo Negocio</th>
                            <th>Contacto</th>
                            <th>Fecha Creado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="codigo"></label>
                <label style="display: none;" id="descripcion"></label>
                <button class="btn btn-sm btn-success" onClick="selectCodigoCC();">Aceptar</button>
            </div>
        </div>
    </div>
</div>