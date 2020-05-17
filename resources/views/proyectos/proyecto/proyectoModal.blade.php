<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-proyecto">
    <div class="modal-dialog" style="width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Proyectos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" 
                    id="listaProyecto">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Cliente</th>
                            <th>Mnd</th>
                            <th>Importe</th>
                            {{-- <th>Pres.Ejec.</th> --}}
                            <th hidden></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
