<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-equipo">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Equipos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" 
                    id="listaEquipos">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Código</th>
                            <th>Descripción</th>
                            {{-- <th hidden>unid</th> --}}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_equipo" style="display: none;"></label>
                <label id="unid_med" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectEquipo();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
