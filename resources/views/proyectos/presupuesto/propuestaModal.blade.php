<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-propuesta">
    <div class="modal-dialog" style="width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Propuestas Cliente</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" 
                    id="listaPropuestas">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Fecha Emision</th>
                            {{-- <th>Duración</th>
                            <th>Und</th> --}}
                            {{-- <th hidden>id_moneda</th> --}}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            {{-- <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="selectpropuesta();">Aceptar</button>
            </div> --}}
        </div>
    </div>
</div>
