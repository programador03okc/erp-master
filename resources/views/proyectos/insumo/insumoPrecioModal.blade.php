<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-insumo_precio">
    <div class="modal-dialog" style="width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Historial de Precios (sin IGV)</h3>
            </div>
            <div class="modal-body">
                <input type="text" name="id_insumo" class="oculto"/>
                <table class="mytable table table-striped table-condensed table-bordered" 
                    id="listaInsumoPrecios">
                    <thead>
                        <tr>
                            <th>pre</th>
                            <th>Presupuesto</th>
                            <th>Fecha Emisión</th>
                            <th>Precio</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
